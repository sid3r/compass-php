<?php

namespace App\Http\Controllers\Api;

use App\Laravue\Models\Storehouse;
use App\Laravue\Models\StorehouseUser;
use App\Http\Resources\StorehouseResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StorehouseController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
      $currentUser = Auth::user();
      if (!$currentUser->isAdmin()){
        return Storehouse::with(['users', 'purchases', 'sales'])->whereIn('id', $currentUser->storehouses)->get();
      } else {
        return Storehouse::with(['users', 'purchases', 'sales'])->get();
      }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
      $store = new Storehouse();
      $store->name = $request->name;
      $store->code = $request->code;
      $store->adress = $request->adress;
      $store->status = $request->status;
      $store->save();

      foreach($request->users_ids as $user){
        StorehouseUser::create([
          'storehouse_id' => $store->id,
          'user_id' => $user
        ]);
      }

      return response()->json('Store created', 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Laravue\Models\Storehouse  $storehouse
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
      $data = Storehouse::where('id', $id)->with(['stocks.purchase.items.product', 'stocks.purchase.company', 'stocks.sale.items', 'users'])->first();
      return $data;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Laravue\Models\Storehouse  $storehouse
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Storehouse $storehouse)
    {
      $storehouse->name = $request->name;
      $storehouse->code = $request->code;
      $storehouse->adress = $request->adress;
      $storehouse->status = $request->status;
      $storehouse->save();

      $storeuser = StorehouseUser::where('storehouse_id', $storehouse->id)->delete();

      foreach($request->users_ids as $user){
        StorehouseUser::create([
          'storehouse_id' => $storehouse->id,
          'user_id' => $user
        ]);
      }

      return response()->json("Storehouse updated", 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Laravue\Models\Storehouse  $storehouse
     * @return \Illuminate\Http\Response
     */
    public function destroy(Storehouse $storehouse)
    {
        //
    }
}
