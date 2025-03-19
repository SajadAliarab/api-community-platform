<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Comment;
use App\Models\Reply;


class ReplyController extends Controller
{
     /**
     * Display all replies for a specific comment.
     */
    public function index($commentId)
    {
        if(!Comment::find($commentId)){
            return response()->json(['message' => 'Comment not found'], 404);
        }else{
            try{
                $replies = Reply::where('comment_id', $commentId)->with('user')->latest()->get();
                return response()->json($replies);
            }catch (\Exception $e){
                return response()->json([
                    'result' => false,
                    'message' => $e->getMessage()
                ], 500);    
            }
        }
    }

    /**
     * Store a new reply.
     */
    public function store(Request $request)
    {
        if(!Comment::find($request->comment_id)){
            return response()->json(['message' => 'Comment not found'], 404);
        }else{
         
                try{
                    $request->validate([
                        'comment_id' => 'required|exists:comments,id',
                        'content' => 'required|string',
                    ]);
            
                    $reply = Reply::create([
                        'user_id' => auth()->id(),
                        'comment_id' => $request->comment_id,
                        'content' => $request->content,
                        'like' => 0, // Default value
                        'active' => true,
                    ]);
            
                    return response()->json(['message' => 'Reply added successfully', 'reply' => $reply], 201);
                }catch (\Exception $e){
                    return response()->json([
                        'result' => false,
                        'message' => $e->getMessage()
                    ], 500);
                }
                
            
        
        }
    }

    /**
     * Update an existing reply.
     */
    public function update(Request $request, $id)
    {
        $reply = Reply::find($id);
        if(!$reply){
            return response()->json(['message' => 'Reply not found'], 404);
        }else{
            if($reply->user_id !== auth()->id && !auth()->user()->is_admin){
                return response()->json(['message' => 'Unauthorized'], 403);
            }else{
                try{
                    $request->validate([
                        'content' => 'required|string',
                        'active' => 'boolean',
                    ]);
            
                    $reply->update([
                        'content' => $request->content,
                        'active' => $request->active,
                    ]);
            
                    return response()->json(['message' => 'Reply updated successfully', 'reply' => $reply]);
                }catch (\Exception $e){
                    return response()->json([
                        'result' => false,
                        'message' => $e->getMessage()
                    ], 500);
                }
            }
        }
    }

    /**
     * Delete a reply.
     */
    public function destroy($id)
    {
        $reply = Reply::find($id);
        if(!$reply){
            return response()->json(['message' => 'Reply not found'], 404);
        }else{
            if($reply->user_id !== auth()->id && !auth()->user()->is_admin){
                return response()->json(['message' => 'Unauthorized'], 403);
            }else{
                try{
                    $reply->delete();
                    return response()->json(['message' => 'Reply deleted successfully']);
                }catch (\Exception $e){
                    return response()->json([
                        'result' => false,
                        'message' => $e->getMessage()
                    ], 500);
                }
            }
        }
    }
}
