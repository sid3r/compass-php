<?php

namespace App\Http\Controllers\Api;

use App\Laravue\Models\Purchase;
use App\Laravue\Models\Sale;
use App\Http\Resources\PurchaseResource;
use App\Http\Resources\SaleResource;
use App\Laravue\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

/**
 * Class RoleController
 *
 * @package App\Http\Controllers\Api
 */

class StatsController extends BaseController
{
  // purchases 12 months
  public function purchases(Request $request) {
    
    $today = Carbon::now();
    $searchParams = $request->all();
    $store = Arr::get($searchParams, 'store', '');

    $purchaseQuery = Purchase::query();
    // format by month -> total
    $purchaseQuery->select('id', 'created_at', \DB::raw('SUM(total) as total, DATE_FORMAT(created_at,"%m-%y") as month'))
      ->groupBy('month')
      ->orderBy('created_at');

    $purchaseQuery->where('created_at' ,'>=',  $today->subYear()->subMonth(5));

    if($store) {
      $purchaseQuery->where('storehouse_id' , $store);
    }
    // user storehouses only if not admin
    $currentUser = Auth::user();
    // dd($currentUser);
    if (!$currentUser->isAdmin()){
      $purchaseQuery->whereIn('storehouse_id', $currentUser->storehouses);
    }

    return PurchaseResource::collection($purchaseQuery->get());
  }

  // sales 12 months
  public function sales(Request $request) {
    $today = Carbon::now();
    $searchParams = $request->all();
    $store = Arr::get($searchParams, 'store', '');

    $saleQuery = Sale::query();
    // format by month -> total
    $saleQuery->select('id', 'created_at', \DB::raw('SUM(total) as total, SUM(profit) as total_profit, DATE_FORMAT(created_at,"%m-%y") as month'))
      ->groupBy('month')
      ->orderBy('created_at');

    $saleQuery->where('created_at' ,'>=',  $today->subYear()->subMonth(5));

    if($store) {
      $saleQuery->where('storehouse_id' , $store);
    }

    // user storehouses only if not admin
    $currentUser = Auth::user();
    if (!$currentUser->isAdmin()){
      $saleQuery->whereIn('storehouse_id', $currentUser->storehouses);
    }

    return SaleResource::collection($saleQuery->get());
  }

  // sales 12 months by storehouse
  public function salesByStorehouse(Request $request) {
    $today = Carbon::now();

    $saleQuery = Sale::query();
    // format by month -> total
    $saleQuery->select('storehouses.name', \DB::raw('SUM(total) as total, SUM(profit) as total_profit, DATE_FORMAT(sales.created_at,"%m-%y") as month'))
      ->join('storehouses', 'storehouse_id', '=', 'storehouses.id')
      ->groupBy(['month', 'storehouses.name'])
      ->orderBy('sales.created_at');

    $saleQuery->where('sales.created_at' ,'>=',  $today->subYear()->subMonth(5));
    
    return SaleResource::collection($saleQuery->get());
  }
}
