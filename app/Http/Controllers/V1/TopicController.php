<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Topic;

class TopicController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $topics = Topic::with('category', 'user')->get();
        try{
            if($topics!=null){
                return response()->json([
                    'result'=>true,
                    'message'=>'topics received successfully',
                    'data'=>$topics
                ],200);
            }else{
                return response()->json([
                    'result'=>false,
                    'message'=>'there is not any topic',
                ],400);
            }
        }catch (\Exception $e){
            return response()->json([
                'result'=>false,
                'message'=> 'An error occurred while indexing topics:' . $e->getMessage()
            ],500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if($request!=null){
            $validatedData = $request->validate([
                'category_id' => 'required|exists:categories,id',
                'title' => 'required|string',
                'content' => 'required|string',
            ]);
            try{
                $topic = Topic::create([
                    'category_id' => $validatedData['category_id'],
                    'user_id' => auth()->id(), // Ensure user is logged in
                    'title' => $validatedData['title'],
                    'content' => $validatedData['content']
                ]);
        
                return response()->json([
                    'result'=>true,
                    'message'=>'topic added successfully',
                    'data'=>$topic
                ],201);
            }catch (\Exception $e){
                return response()->json([
                    'result'=>false,
                    'message'=>'An error occurred while storing topic: ' . $e->getMessage()
                ],500);
            }
        }else{
            return response()->json([
                'result'=>false,
                'message'=>'request is empty',
            ],400);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $topic = Topic::with(['category', 'user', 'comments'])->find($id);
            if($topic!=null){
                $topic->increment('view');
                return response()->json([
                    'result'=>true,
                    'message'=>'topic received successfully',
                    'data'=>$topic
                ],200);
            }else{
                return response()->json([
                    'result'=>false,
                    'message'=>'there is not any topic',
                ],400);
            }
        }catch (\Exception $e){
            return response()->json([
                'result'=>false,
                'message'=> 'An error occurred while showing topic:' . $e->getMessage()
            ],500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        if($request!=null){
            $validatedData = $request->validate([
                'title' => 'required|string',
                'content' => 'required|string',
            ]);
            try{
                $topic = Topic::find($id);
                if($topic){
                    $topic->update($validatedData);
                    return response()->json([
                        'result'=>true,
                        'message'=>'topic updated successfully',
                        'data'=>$topic
                    ],200);
                }else{
                    return response()->json([
                        'result'=>false,
                        'message'=>'topic not found',
                    ],400);
                }
            }catch (\Exception $e){
                return response()->json([
                    'result'=>false,
                    'message'=>'An error occurred while updating topic: ' . $e->getMessage()
                ],500);
            }
        }else{
            return response()->json([
                'result'=>false,
                'message'=>'request is empty',
            ],400);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $topic = Topic::find($id);
            if($topic){
                $topic->delete();
                return response()->json([
                    'result'=>true,
                    'message'=>'topic deleted successfully',
                ],200);
            }else{
                return response()->json([
                    'result'=>false,
                    'message'=>'topic not found',
                ],400);
            }
        }catch (\Exception $e){
            return response()->json([
                'result'=>false,
                'message'=>'An error occurred while deleting topic: ' . $e->getMessage()
            ],500);
        }
    }

    public function getTopicsByCategory($categoryId)
    {
        try {
            $topics = Topic::where('category_id', $categoryId)->with('user')->get();
            if($topics!=null){
                return response()->json([
                    'result'=>true,
                    'message'=>'topics received successfully',
                    'data'=>$topics
                ],200);
            }else{
                return response()->json([
                    'result'=>false,
                    'message'=>'there is not any topic',
                ],400);
            }
        }catch (\Exception $e){
            return response()->json([
                'result'=>false,
                'message'=>'An error occurred while getting topics by category: ' . $e->getMessage()
            ],500);
        }
    }
}
