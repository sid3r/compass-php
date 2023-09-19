<?php

namespace App\Http\Controllers\Api;
use Illuminate\Support\Facades\DB;

use App\Laravue\Models\Product;
use App\Laravue\Models\Purchase;
use App\Http\Resources\ProductResource;
use App\Laravue\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Pagination\LengthAwarePaginator;

class StockController extends BaseController
{
  const ITEM_PER_PAGE = 48;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
      $data = DB::select('SELECT P.id as product_id, P.name as product_name, P.min_qty, SH.id as storehouse_id, SH.name as storehouse_name, sum(PuI.qty) as total_purchases , sum(SI.qty) as total_sales
        FROM products as P 
        JOIN sale_items as SI on SI.product_id = P.id
        JOIN sales as S on SI.sale_id = S.id
        JOIN storehouses as SH on SH.id = S.storehouse_id
        JOIN purchase_items as PuI on PuI.product_id = P.id
        JOIN purchases as Pu on PuI.purchase_id = Pu.id
        JOIN storehouses as SH2 on SH2.id = Pu.storehouse_id
        GROUP BY P.id, SH.id');

      $total = count($data);
      $stocks = $this->arrayPaginator($data, $request);

      // FIX pagination data object to array issue
      $items = $stocks->items();
      $decodeFix = $stocks;
      $decodeFix = json_decode($stocks->toJson());
      $decodeFix->data = array_values($items);

      // $searchParams = $request->all();
      // $limit = Arr::get($searchParams, 'limit', static::ITEM_PER_PAGE);

      // return decodedFix
      return response()->json($decodeFix, 200);
    }

    public function arrayPaginator($array, $request)
      {
        $searchParams = $request->all();
        $page = Arr::get($searchParams, 'page', 1);
        $perPage = Arr::get($searchParams, 'limit');
        $offset = ($page * $perPage) - $perPage;

        return new LengthAwarePaginator(array_slice($array, $offset, $perPage, true), count($array), $perPage, $page);
      }

    public function purchasesSummary(){

    }
}
