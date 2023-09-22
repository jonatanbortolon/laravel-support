<?php

namespace App\Http\Controllers;

use App\Models\Attachments as ModelsAttachments;
use App\Models\Comment as ModelsComment;
use App\Models\Request as ModelsRequest;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

use function App\Http\Helpers\currentUser;

class RequestController extends Controller
{
    public function view(Request $request) {
        $currentUser = currentUser();
        $requests_query = [];

        if ($currentUser->role === 'client') {

            $requests_query = $currentUser->requests()->with('user:id,name');
        } else {
            $requests_query = ModelsRequest::with('user:id,name');
        }

        $requests_query = $requests_query->select(
            'id', 'user_id', 'title', 'state', 'created_at'
        )->orderBy('created_at', 'desc');

        if ($request->has('from') && $request->query('from') != '' && $currentUser->role == 'employee') {
            $requests_query->whereHas('user', function($q) use ($request) {
                $q->where('name', 'ilike', '%'. $request->query('from') . '%');
            });
        }

        if ($request->has('state') && $request->query('state') != '' && $request->query('state') != 'all') {
            $requests_query->where('state', '=', $request->query('state'));
        }

        if ($request->has('date') && $request->query('date') != '') {
            $date = new Carbon($request->query('date'));
            
            $requests_query->whereDate('created_at', '=', $date);
        }

        $count = $requests_query->count();

        $requests_query->paginate(10);

        $requests_query_data = $requests_query->get();

        $requests_query_data = $requests_query_data->map(function ($data) {
            $request = [
                'id' => $data->id,
                'title' => $data->title,
                'date' => $data->created_at,
                'state' => $data->state,
                'from' => $data->user,
            ];

            return $request;
        });

        return Inertia::render('HomePage', [
            'requests' => $requests_query_data,
            'count' => $count,
        ]);
    }

    public function createView() {
        return Inertia::render('CreateRequestPage');
    }

    public function detailView(Request $request, $id) {
        $currentUser = currentUser();
        $request_data = ModelsRequest::select('id', 'user_id', 'title', 'state')->findOrFail($id);
        
        if ($currentUser->role === 'client' && ($currentUser->id != $request_data->user_id)) {
            abort(404);
        }

        $request_data = $request_data->makeHidden(['user_id']);

        $comments = $request_data->comments()
            ->orderBy('created_at', 'asc')
            ->with('user', 'attachments')
            ->get()
            ->map(function ($data) {
                $mapped_data = [
                    'content' => $data->content,
                    'attachments' => $data->attachments ? $data->attachments->map(function ($att_data) {
                        return $att_data->name;
                    }) : [],
                    'date' => $data->created_at,
                    'from' => $data->user->name,
                ];
    
                return $mapped_data;
            });

        return Inertia::render('RequestDetailPage', [
            'request' => $request_data,
            'comments' => $comments,
        ]);
    }

    public function create(Request $request) {
        $request->validate([
            'title' => [
                'required',
            ],
            'description' => [
                'required',
            ],
            "attachments" => [
                "nullable",
                "array",
                "max:10"
            ],
            'attachments.*' => [
                'mimes:csv,txt,xlx,xls,pdf,jpg,jpeg,png,bmp',
                'max:2048'
            ],
        ],
        [
            'title.required' => 'O titulo é obrigatório.',
            'description.required' => 'A descriçao é obrigatória.',
            'attachment.array' => 'Anexos inválidos.',
            'attachment.max' => 'O máxido de anexos é 10.',
            'attachment.*.mimes' => 'Tipo de anexo inválido.',
            'attachment.*.max' => 'Anexo tem que ser menor que 2MB.',
        ]);

        $currentUser = currentUser();

        DB::beginTransaction();
        
        try {
            $new_request = ModelsRequest::create([
                'title' => $request->input('title'),
                'user_id' => $currentUser->id,
            ]);

            $new_comment = ModelsComment::create([
                'content' => $request->input('description'),
                'request_id' =>  $new_request->id,
                'user_id' =>  $currentUser->id,
            ]);
            
            $attachments = [];

            if ($request->file('attachments')){
                foreach($request->file('attachments') as $key => $file)
                {
                    $file_name = uniqid('attachment-').'.'.$file->extension();

                    Storage::disk('local')->put('public/attachments/' . $file_name,  File::get($file));  

                    $attachments[] = new ModelsAttachments([
                        'name' => $file_name
                    ]);
                }
            }
        
            $new_comment->attachments()->saveMany($attachments);

            DB::commit();
            
            return redirect()->route('requests.detail', [ 'id' => $new_request->id ]);
        } catch (Exception $e) {
            DB::rollback();

            throw $e;
        }
    }

    public function update(Request $request, $id) {
        $request->validate([
            'state' => [
                'required',
                Rule::in(['open', 'inprogress', 'closed']),
            ],
        ],
        [
            'state.required' => 'O estado é obrigatório.',
            'state.in' => 'Estado inválido.',
        ]);

        $updated_request = ModelsRequest::find($id);

        if (currentUser()->role === 'client' && (currentUser()->id != $updated_request->user_id)) {
            abort(404);
        }
 
        $updated_request->update([
            'state' => $request->input('state'),
        ]);

        return redirect()->route('requests.detail', [ 'id' => $id ]);
    }
}
