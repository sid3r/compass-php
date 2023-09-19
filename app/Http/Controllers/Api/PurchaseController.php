<?php

namespace App\Http\Controllers\Api;

use App\Laravue\Models\Purchase;
use App\Laravue\Models\PurchaseItem;
use App\Laravue\Models\Storehouse;
use App\Http\Resources\PurchaseResource;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Arr;
use App\Laravue\JsonResponse;

class PurchaseController extends BaseController
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
    $purchaseQuery = Purchase::query();
    $limit = Arr::get($searchParams, 'limit', static::ITEM_PER_PAGE);
    $store = Arr::get($searchParams, 'store', '');
    $from = Arr::get($searchParams, 'from', '');
    $to = Arr::get($searchParams, 'to', '');
    
    if (!empty($store)) {
      $purchaseQuery->where('storehouse_id', $store);
    }
    if (!empty($from)) {
      $purchaseQuery->where('created_at', '>', $from);
    }
    if (!empty($to)) {
      $purchaseQuery->where('created_at', '<', $to);
    }

    // user storehouses only if not admin
    $currentUser = Auth::user();
    if (!$currentUser->isAdmin()){
      $purchaseQuery->whereIn('storehouse_id', $currentUser->storehouses);
    }

    $purchaseQuery->with(['items.product', 'storehouse', 'company']);
    $purchaseQuery->orderby('created_at','DESC');
    return PurchaseResource::collection($purchaseQuery->paginate($limit));
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

      $purchase = new Purchase();
      $purchase->storehouse_id = $currentUser->storehouses->first()->id;
      $purchase->total = $request->get('total');
      if ($request->get('company_id')){
        $purchase->company_id = $request->get('company_id');
      } else {
        $purchase->company_id = null;
      }
      $purchase->save();

      foreach($request->get('items') as $item){
        PurchaseItem::create([
          'purchase_id' => $purchase->id,
          'product_id' => $item['id'],
          'qty' => $item['qty'],
        ]);
      }

      return response()->json($purchase, 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Laravue\Models\Purchase  $purchase
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
      $purchase = Purchase::where('id', $id)->with(['items.product', 'storehouse', 'company'])->first();
      return response()->json($purchase, 200);
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Laravue\Models\Purchase  $purchase
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Purchase $purchase)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Laravue\Models\Purchase  $purchase
     * @return \Illuminate\Http\Response
     */
    public function destroy(Purchase $purchase)
    {
        //
    }

    public function summary(){
      $summary = Purchase::groupBy('created_at')
          ->selectRaw('sum(total) as subtotal')
          ->get();
    }
}
