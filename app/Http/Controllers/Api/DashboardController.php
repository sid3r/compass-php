<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Laravue\Models\Contact;
use App\Laravue\Models\Company;
use App\Laravue\Models\Document;
use App\Laravue\Models\Product;
use App\Laravue\Models\Storehouse;
use App\Laravue\Models\User;

class DashboardController extends BaseController
{
  public function widgets()
  {
    $widgets = array();
    $widgets[] = [
      'title' => 'contacts',
      'route' => 'contacts',
      'icon' => 'contacts',
      'color' => 'green',
      'permission' => 'view menu contacts',
      'count' => Contact::all()->count()
    ];
    $widgets[] = [
      'title' => 'companies',
      'route' => 'companies',
      'icon' => 'company',
      'color' => 'red',
      'permission' => 'view menu companies',
      'count' => Company::all()->count()
    ];
    $widgets[] = [
      'title' => 'documents',
      'route' => 'documents',
      'icon' => 'layers',
      'color' => 'purple',
      'permission' => 'view menu documents',
      'count' => Document::all()->count()
    ];
    $widgets[] = [
      'title' => 'products',
      'route' => 'products',
      'icon' => 'package',
      'color' => 'orange',
      'permission' => 'view menu products',
      'count' => Product::all()->count()
    ];
    $widgets[] = [
      'title' => 'storehouses',
      'route' => 'storehouses',
      'icon' => 'map',
      'color' => 'yellow',
      'permission' => 'view menu storehouses',
      'count' => Storehouse::all()->count()
    ];
    $widgets[] = [
      'title' => 'users',
      'route' => 'users',
      'icon' => 'unlock',
      'color' => 'primary',
      'permission' => 'manage users',
      'count' => User::all()->count()
    ];

    return response()->json($widgets);
  }

  public function recentDocs()
  {
    $docs = Document::where('status', 'pending')->orderBy('date', 'DESC')->with(['company', 'items'])->limit(10)->get();
    return response()->json($docs);
  }
}
