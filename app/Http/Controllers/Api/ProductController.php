<?php

namespace App\Http\Controllers\Api;

use App\Laravue\Models\Product;
// use App\Laravue\Models\Category;
use App\Http\Resources\ProductResource;
use App\Laravue\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Arr;

class ProductController extends BaseController
{
    const ITEM_PER_PAGE = 48;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
      $searchParams = $request->all();
      $prodQuery = Product::query();
      
      $limit = Arr::get($searchParams, 'limit', static::ITEM_PER_PAGE);
      $category = Arr::get($searchParams, 'category', '');
      $keyword = Arr::get($searchParams, 'keyword', '');

      if (!empty($category)) {
        $prodQuery->where('category_id', $category);
        $prodQuery->orWhereHas('category', function($q) use ($category) {
          return $q->where('parent_id', $category );
        });
      }

      if (!empty($keyword)) {
        $prodQuery->where('name', 'LIKE', '%' . $keyword . '%');
        // $prodQuery->orWhere('ref', 'LIKE', '%' . $keyword . '%');
      }
      $prodQuery->with(['category']);
      $prodQuery->orderby('id','DESC');

      if($request->nopaginate) {
        $collection = ProductResource::collection($prodQuery->get());
      } else {
        $collection = ProductResource::collection($prodQuery->paginate($limit));
      }

      return $collection;
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

      $params = $request->all();

      // create product
      $product = Product::create([
        'name' => $request->name,
        'category_id' => $request->category_id,
        'bar_code' => $request->bar_code,
        'description' => $request->description,
        'unit_price' => $params['unit_price'],
        'unit_sale_price' => $request->get('unit_sale_price'),
        'discount' => $request->discount,
        'min_qty' => $request->min_qty,
        'image' => $request->image,
      ]);

      return response()->json($product, 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Laravue\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $found = Product::where('id', $id)->with(['category', 'purchases', 'sales'])->first();
        $data = array( 
          "product" => $found
        );
        return response()->json($data, 200);

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Laravue\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Product  $product)
    {
      // $found = Product::where('id', $request->id)->first();

      $product->name = $request->name;
      $product->category_id = $request->category_id;
      $product->bar_code = $request->bar_code;
      $product->description = $request->description;
      $product->unit_price = $request->unit_price;
      $product->unit_sale_price = $request->unit_sale_price;
      $product->discount = $request->discount;
      $product->min_qty = $request->min_qty;
      $product->image = $request->image;

      $product->save();
      return response()->json("Product updated", 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Laravue\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function destroy(Product $product)
    {
      // $contact = Contact::find($request->get('id'));
      try {
        $product->delete();
      } catch (\Exception $ex) {
        response()->json(['error' => $ex->getMessage()], 403);
      }

      return response()->json("Product deleted", 204);
    }

    /**
     * Save image file and return url.
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function upload(Request $request)
    {
      // $path = $request->file->store('uploads');
      $fileName = time().'.'.$request->file->getClientOriginalExtension();
      $request->file->move(public_path('uploads'), $fileName);

      return response()->json($fileName);
    }
}
