<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Topic;
use App\Models\Comment;

class CommentController extends Controller
{
    /**ph
     * Display a listing of the resource.
     */
    public function index($modelType,$id)
    {
        $modelClasses = [
            'topic'   => \App\Models\Topic::class,
            'article' => \App\Models\Article::class,
        ];
        if (!isset($modelClasses[$modelType])) {
            return response()->json([
                'result' => false,
                'message' => 'Invalid model type'
            ], 400);
        }

        $modelClass = $modelClasses[$modelType];
        $instance = $modelClass::find($id);
        try {
            if(!$instance) {
                return response()->json([
                    'result' => false,
                    'message' => 'Topic not found'
                ], 404);
            }else {
                $comments = $instance->comments()->with('user','replies')->get();
                return response()->json([
                    'result' => true,
                    'data' => $comments
                ], 200);
            }
        } catch (\Exception $e) {
            return response()->json([
                'result' => false,
                'message' => $e->getMessage()
            ], 500);    
        }
       
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request,$modelType,$id)
    {
        $modelClasses = [
            'topic'   => \App\Models\Topic::class,
            'article' => \App\Models\Article::class,
        ];
        if (!isset($modelClasses[$modelType])) {
            return response()->json([
                'result' => false,
                'message' => 'Invalid model type'
            ], 400);
        }

        $modelClass = $modelClasses[$modelType];
        $instance = $modelClass::find($id);
        try {
            if(!$instance) {
                return response()->json([
                    'result' => false,
                    'message' => 'Topic not found'
                ], 404);
            }else {
               $validatedData = $request->validate([
                    'content' => 'required',
                ]);
                $comment = $instance->comments()->create([
                    'user_id' => auth()->id(),
                    'content' => $validatedData['content']
                ]);
                return response()->json([
                    'result' => true,
                    'data' => $comment
                ], 201);
            }
        } catch (\Exception $e) {
            return response()->json([
                'result' => false,
                'message' => $e->getMessage()
            ], 500);    
        }
    }
    
    /**
     * Display the specified resource.
     */

    public function show(string $id)
    {
        $commentData = Comment::find($id);
        try {
            if(!$commentData) {
                return response()->json([
                    'result' => false,
                    'message' => 'Comment not found'
                ], 404);
            }else {
                $comment = $commentData->load('user','replies');
                return response()->json([
                    'result' => true,
                    'data' => $comment
                ], 200);
            }
        } catch (\Exception $e) {
            return response()->json([
                'result' => false,
                'message' => $e->getMessage()
            ], 500);    
        }
    }

  
    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $comment = Comment::find($id);
        try {
            if(!$comment) {
                return response()->json([
                    'result' => false,
                    'message' => 'Comment not found'
                ], 404);
            }else {
                $validatedData = $request->validate([
                    'content' => 'required',
                    'active' => 'boolean'
                ]);
                $comment->update($validatedData);
                return response()->json([
                    'result' => true,
                    'data' => $comment
                ], 200);
            }
        } catch (\Exception $e) {
            return response()->json([
                'result' => false,
                'message' => $e->getMessage()
            ], 500);    
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $comment = Comment::find($id);
        try {
            if(!$comment) {
                return response()->json([
                    'result' => false,
                    'message' => 'Comment not found'
                ], 404);
            }else {
                $comment->delete();
                return response()->json([
                    'result' => true,
                    'message' => 'Comment deleted successfully'
                ], 200);
            }
        } catch (\Exception $e) {
            return response()->json([
                'result' => false,
                'message' => $e->getMessage()
            ], 500);    
        }
    }
}
