<?php

namespace App\Http\Controllers\Api;

use App\Laravue\Models\Category;
use App\Http\Resources\CategoryResource;
use App\Laravue\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class CategoryController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {   

        if($request->nest){
          $data = CategoryResource::collection(Category::where('parent_id', 0)->orderBy('order')->with('children')->get());
        }else{
          $data = CategoryResource::collection(Category::orderBy('order')->get());
        }

        return response()->json($data, 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
      $category = Category::create([
        'name' => $request->name,
        'parent_id' => $request->parent_id,
        'order' => $request->order,
        'description' => $request->description,
      ]);

      return response()->json($category, 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Laravue\Models\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function show(Category $category)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Laravue\Models\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Category $category)
    {
      $category->name = $request->name;
      $category->parent_id = $request->parent_id;
      $category->order = $request->order;
      $category->description = $request->description;

      $category->save();
      return response()->json("Category updated", 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Laravue\Models\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function destroy(Category $category)
    {
      $category->delete();
      return response()->json("Category deleted", 200);
    }

    /**
     * Update categories after reorder.
     *
     * @return \Illuminate\Http\Response
     */
    public function reorder(Request $request)
    {
      $items = $request->all();
      // level 0
      foreach($items as $item){
        $found_parent = Category::where('id', $item['id'])->first();
        $found_parent->parent_id = 0;
        $found_parent->save();
        // level 1
        if(count($item['children']) > 0) {
          foreach($item['children'] as $child){
            $found_child = Category::where('id', $child['id'])->first();
            $found_child->parent_id = $found_parent['id'];
            $found_child->save();
            // level 2
            // if(count($child['children']) > 0) {
            //   foreach($child['children'] as $subchild){
            //     $found_sub_child = Category::where('id', $subchild['id'])->first();
            //     $found_sub_child->parent_id = $child['id'];
            //     $found_sub_child->save();
            //   }
            // }
          }
        }
      }
      return response()->json("Order saved", 200);
    }
}
