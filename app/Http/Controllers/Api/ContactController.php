<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\ContactResource;
use App\Laravue\Models\Contact;
use App\Laravue\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class ContactController extends BaseController
{
    const ITEM_PER_PAGE = 15;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    
    public function index(Request $request)
    {
        $searchParams = $request->all();
        $contactQuery = Contact::query();
        $limit = Arr::get($searchParams, 'limit', static::ITEM_PER_PAGE);
        $keyword = Arr::get($searchParams, 'keyword', '');

        $function = Arr::get($searchParams, 'function', '');

        if (!empty($keyword)) {
          $contactQuery->where('name', 'LIKE', '%' . $keyword . '%');
          $contactQuery->orWhereHas('company', function($query) use ($keyword) {
            return $query->where('name', 'like', '%' . $keyword . '%');
          });
        }

        if (!empty($function)) {
          $contactQuery->where('function', 'LIKE', '%' . $function . '%');
        }

        $contactQuery->orderby('id','DESC');

        return ContactResource::collection($contactQuery->with(['company'])->paginate($limit));
    }

    /**
     * Storing or updating contact
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $params = $request->all();
        // dd($request->all());
        $company_id = null;

        // create company if no "id" and "name" is present
        if(!$params['company']['id'] && $params['company']['name']){
            $company = Company::create([
                'name' => $params['company']['name'],
                'tel' => $params['company']['tel'],
                'fax' => $params['company']['fax'],
                'email' => $params['company']['email'],
                'address' => $params['company']['address'],
                'region' => $params['company']['region'],
                'activity' => $params['company']['activity']
            ]);
            $company_id = $company->id;
        }elseif ($params['company']['id']){
            $company_id = $params['company']['id'];
        }else{
            // no company
            $company_id = '';
        }
        // create
        if($params['mode'] === 'create'){
          $contact = Contact::create([
            'company_id' => $company_id,
            'name' => $params['name'],
            'mobile' => $params['mobile'],
            'email' => $params['email'],
            'function' => $params['function']
          ]);
          return new ContactResource($contact);
        // update contact
        }else{
          $found = Contact::find($params['id']);
          $found->company_id = $company_id;
          $found->name = $params['name'];
          $found->function = $params['function'];
          $found->mobile = $params['mobile'];
          $found->email = $params['email'];
          
          $found->save();
          return new ContactResource($found);
        }          
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Laravue\Models\Contact  $contact
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $found = Contact::find($id);
        $data = array( 
            "contact" => $found,
            "company" => $found->company,
        );

        return response()->json($data, 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Laravue\Models\Contact  $contact
     * @return \Illuminate\Http\Response
     */
    public function destroy(Contact $contact)
    {
      // $contact = Contact::find($request->get('id'));
      try {
        $contact->delete();
      } catch (\Exception $ex) {
        response()->json(['error' => $ex->getMessage()], 403);
      }

      return response()->json(null, 204);
    }
    /** 
     * Get distinct functions
    */

    public function functions()
    {
      $functions = Contact::select('function')->distinct()->get();
      return response()->json($functions, 200);
    }
}
