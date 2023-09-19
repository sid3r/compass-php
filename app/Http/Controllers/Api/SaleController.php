<?php

namespace App\Http\Controllers\Api;

use App\Laravue\Models\Sale;
use App\Laravue\Models\SaleItem;
use App\Laravue\Models\Storehouse;
use App\Http\Resources\SaleResource;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Laravue\JsonResponse;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;

class SaleController extends BaseController
{
  const ITEM_PER_PAGE = 20;
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index(Request $request)
  {
    $searchParams = $request->all();
    $salesQuery = Sale::query();
    $limit = Arr::get($searchParams, 'limit', static::ITEM_PER_PAGE);
    $store = Arr::get($searchParams, 'store', '');
    $from = Arr::get($searchParams, 'from', '');
    $to = Arr::get($searchParams, 'to', '');

    if (!empty($store)) {
      $salesQuery->where('storehouse_id', $store);
    }
    if (!empty($from)) {
      $salesQuery->where('created_at', '>', $from);
    }
    if (!empty($to)) {
      $salesQuery->where('created_at', '<', $to);
    }

    // user storehouses only if not admin
    $currentUser = Auth::user();
    if (!$currentUser->isAdmin()){
      $salesQuery->whereIn('storehouse_id', $currentUser->storehouses);
    }

    $salesQuery->with(['items.product', 'storehouse', 'document', 'company']);
    $salesQuery->orderby('created_at','DESC');
    return SaleResource::collection($salesQuery->paginate($limit));
  }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
      $currentUser = Auth::user();
      // $currentStore = Storehouse::whereIn('id', $currentUser->storehouses)->first();

      $sale = new Sale();
      $sale->storehouse_id = $currentUser->storehouses->first()->id;
      $sale->total = $request->get('total');
      $sale->profit = $request->get('profit');
      $sale->save();

      foreach($request->get('items') as $item){
        SaleItem::create([
          'sale_id' => $sale->id,
          'product_id' => $item['id'],
          'qty' => $item['qty'],
        ]);
      }

      return response()->json($sale, 200);
    }

    public function show($id)
    {
      $purchase = Sale::where('id', $id)->with(['items.product', 'storehouse'])->first();
      return response()->json($purchase, 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Laravue\Models\Sale  $sale
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Sale $sale)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Laravue\Models\Sale  $sale
     * @return \Illuminate\Http\Response
     */
    public function destroy(Sale $sale)
    {
        //
    }
}
