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
            $categories = Category::whereNull('parent_id')->with('children')->get();
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
                'active' => 'required|boolean',
                'parent_id' => 'nullable|exists:categories,id',
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
            $category = Category::with(['parent', 'children'])->find($id);
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
                   'slug' => 'required|string|max:255|unique:categories,slug,' . $id,
                    'description' => 'nullable|string',
                    'active' => 'required|boolean',
                    'parent_id' => 'nullable|exists:categories,id|not_in:' . $id,
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
            $category = Category::find($id);
            if (!$category) {
                return response()->json([
                    'result' => false,
                    'message' => 'Category not found'
                ], 404);
            }

            // ✅ Check if category has subcategories before deleting
            if ($category->children()->exists()) {
                return response()->json([
                    'result' => false,
                    'message' => 'Cannot delete category with subcategories. Delete subcategories first.'
                ], 400);
            }

            $category->delete();

            return response()->json([
                'result' => true,
                'message' => 'Category deleted successfully'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'result' => false,
                'message' => 'An error occurred while deleting category: ' . $e->getMessage()
            ], 500);
        }
    }

    /* Get subcategories of a category */
    public function getSubcategories($categoryId)
    {
        try {
            $subcategories = Category::where('parent_id', $categoryId)->get();
            if($subcategories->isEmpty()){
                return response()->json([
                    'result' => false,
                    'message' => 'Subcategories not found'
                ], 404);
            }else{

            return response()->json([
                'result' => true,
                'message' => 'Subcategories retrieved successfully',
                'data' => $subcategories
            ], 200);
        }
        
        } catch (\Exception $e) {
            return response()->json([
                'result' => false,
                'message' => 'An error occurred while fetching subcategories: ' . $e->getMessage()
            ], 500);
        }
    }
    public function getActiveCategoriesBySlug($slug)
    {
        try {
            $categories = Category::where('slug', $slug)
                ->where('active', true)
                ->with(['children' => function ($query) {
                $query->where('active', true);
                }])
                ->get();
            if($categories->isEmpty()){
                return response()->json([
                    'result' => false,
                    'message' => 'Categories not found'
                ], 404);
            }else{
            return response()->json([
                'result' => true,
                'message' => 'Categories retrieved successfully',
                'data' => $categories
            ], 200);
        }
        
        } catch (\Exception $e) {
            return response()->json([
                'result' => false,
                'message' => 'An error occurred while fetching categories: ' . $e->getMessage()
            ], 500);
        }
    }
    public function getActiveCategories()
    {
        try {
            $categories = Category::where('active', true)
            ->whereNull('parent_id')
            ->with(['children' => function ($query) {
                $query->where('active', true);
            }])
            ->get();
            if($categories->isEmpty()){
                return response()->json([
                    'result' => false,
                    'message' => 'Active categories not found'
                ], 404);
            }else{
            return response()->json([
                'result' => true,
                'message' => 'Active categories retrieved successfully',
                'data' => $categories
            ], 200);
        }
        
        } catch (\Exception $e) {
            return response()->json([
                'result' => false,
                'message' => 'An error occurred while fetching active categories: ' . $e->getMessage()
            ], 500);
        }
    }
    
}
