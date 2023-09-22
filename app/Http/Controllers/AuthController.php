<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class AuthController extends Controller
{
    public function loginView()
    {
        return Inertia::render('LoginPage');
    }  
    
    public function registerView()
    {
        return Inertia::render('RegisterPage');
    }  
      
    public function loginRequest(Request $request)
    {
        $request->validate([
            'email' => [
                'required',
            ],
            'password' => [
                'required',
            ],
            'remember' => [
                'required',
                'boolean',
            ],
        ],
        [
            'email.required' => 'O e-mail é obrigatório.',
            'password.required' => 'A senha é obrigatória.',
        ]);
   
        $credentials = $request->only('email', 'password');
        $remember = $request->only('remember');

        if (Auth::attempt($credentials, $remember)) {
            return redirect()->route(RouteServiceProvider::HOME);
        }
  
        return redirect()->route(RouteServiceProvider::LOGIN);
    }
      
    public function registerRequest(Request $request)
    {  
        $request->validate([
            'name' => [
                'required',
            ],
            'document' => [
                'required',
                'numeric',
                'unique:users,document',
            ],
            'email' => [
                'required',
                'unique:users,email',
            ],
            'role' => [
                'required',
                Rule::in(['client', 'employee']),
            ],
            'password' => [
                'required',
            ],
        ], 
        [
            'name.required' => 'O nome é obrigatório.',
            'document.required' => 'O CPF é obrigatório.',
            'document.numeric' => 'O CPF é inválido.',
            'document.unique:users,document' => 'CPF em uso.',
            'email.required' => 'O e-mail é obrigatório.',
            'email.unique:users,email' => 'E-mail em uso.',
            'role.required' => 'O cargo é obrigatório.',
            'role.in' => 'Cargo inválido.',
            'password.required' => 'A senha é obrigatória.',
        ]);
           
        $data = $request->only('name', 'document', 'email', 'role', 'password');

        User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'document' => $data['document'],
            'password' => Hash::make($data['password']),
            'role' => $data['role'],
        ]);
         
        return redirect()->route(RouteServiceProvider::LOGIN);
    }
    
    public function logoutRequest() {
        Session::flush();
        Auth::logout();
  
        return redirect()->route(RouteServiceProvider::LOGIN);
    }
}
