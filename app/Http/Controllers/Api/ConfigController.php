<?php

namespace App\Http\Controllers\Api;

use App\Laravue\Models\Config;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ConfigController extends BaseController
{
  public function save(Request $request) {
    $params = $request->all();
    foreach ($params as $config) {
      $found = Config::find($config['id']);
      $found->value = $config['value'];
      $found->save();
    }
    return response()->json($params, 200);
  }
}
