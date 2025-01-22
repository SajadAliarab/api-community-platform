<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;


class CategoryController extends Controller
{
    /* Display a listing of the Category. */

    public function index()
    {
        try {
            $categories = Category::query()->get()->all();
            if($categories!=null){
                return response()->json([
                    'result'=>true,
                    'message'=>'categories received successfully',
                    'data'=>$categories
                ],200);
            }else{
                return response()->json([
                    'result'=>false,
                    'message'=>'there is not any category',
                ],400);
            }
        }catch (\Exception $e){
            return response()->json([
                'result'=>false,
                'message'=> 'An error occurred while indexing categories:' . $e->getMessage()
            ],500);
        }
    }

    /* Store a newly created Category in storage. */

    public function store (Request $request)
    {
        if($request!=null){
            $validatedData = $request->validate([
                'name' => 'required|string',
                'slug' => 'required|string',
                'description' => 'nullable|string',
                'active' => 'required|boolean'
            ]);
            try{
                Category::query()->create($validatedData);
                return response()->json([
                    'result'=>true,
                    'message'=>'category added successfully',
                    'data'=>$validatedData
                ],201);
            }catch (\Exception $e){
                return response()->json([
                    'result'=>false,
                    'message'=>'An error occurred while storing category: ' . $e->getMessage()
                ],500);
            }
        }else{
            return response()->json([
                'result'=>false,
                'message'=>'Request is null'
            ],400);
        }
    }

    /* Display the specified Category. */

    public function show($id)
    {
        try {
            $category = Category::query()->find($id);
            if($category!=null){
                return response()->json([
                    'result'=>true,
                    'message'=>'category received successfully',
                    'data'=>$category
                ],200);
            }else{
                return response()->json([
                    'result'=>false,
                    'message'=>'category not found',
                ],400);
            }
        }catch (\Exception $e){
            return response()->json([
                'result'=>false,
                'message'=> 'An error occurred while showing category:' . $e->getMessage()
            ],500);
        }
    }

    /* Update the specified Category in storage. */

    public function update (Request $request ,string $id)
    {
        try{
            $category = Category::query()->find($id);
            if($category){
                $validatedData = $request->validate([
                    'name' => 'required|string',
                    'slug' => 'required|string',
                    'description' => 'nullable|string',
                    'active' => 'required|boolean'
                ]);
                $category->update($validatedData);
                return response()->json([
                    'result'=>true,
                    'message'=>'category updated successfully',
                    'data'=>$category
                ],200);
            }else{
                return response()->json([
                    'result'=>false,
                    'message'=>'category could not find'
                ],400);
            }
        }catch (\Exception $e){
            return response()->json([
                'result'=>false,
                'message'=>'An error occurred while updating category: ' . $e->getMessage()
            ],500);
        }
    }

    /* Remove the specified Category from storage. */
    
    public function destroy($id)
    {
        try {
            $category = Category::query()->find($id);
            if($category){
                $category->delete();
                return response()->json([
                    'result'=>true,
                    'message'=>'category deleted successfully'
                ],200);
            }else{
                return response()->json([
                    'result'=>false,
                    'message'=>'category not found'
                ],400);
            }
        }catch (\Exception $e){
            return response()->json([
                'result'=>false,
                'message'=>'An error occurred while deleting category: ' . $e->getMessage()
            ],500);
        }
    }
}
