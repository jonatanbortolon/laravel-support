<?php

namespace App\Http\Controllers;

use App\Models\Attachments as ModelsAttachments;
use App\Models\Comment as ModelsComment;
use App\Models\Request as ModelsRequest;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

use function App\Http\Helpers\currentUser;

class CommentController extends Controller
{
    public function create(Request $request, $id) {
        $request->validate([
            'content' => [
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
            'content.required' => 'A comentário é obrigatória.',
            'attachment.array' => 'Anexos inválidos.',
            'attachment.max' => 'O máxido de anexos é 10.',
            'attachment.*.mimes' => 'Tipo de anexo inválido.',
            'attachment.*.max' => 'Anexo tem que ser menor que 2MB.',
        ]);

        $currentUser = currentUser();

        $request_data = ModelsRequest::findOrFail($id);
        
        if ($currentUser->role === 'client' && ($currentUser->id != $request_data->user_id)) {
            abort(404);
        }

        DB::beginTransaction();
        
        try {
            $new_comment = ModelsComment::create([
                'content' => $request->input('content'),
                'request_id' =>  $id,
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
            
            return redirect()->route('requests.detail', [ 'id' => $id ]);
        } catch (Exception $e) {
            DB::rollback();

            throw $e;
        }
    }
}
