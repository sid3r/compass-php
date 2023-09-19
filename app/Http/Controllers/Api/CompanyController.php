<?php

namespace App\Http\Controllers\Api;

use App\Laravue\Models\Company;
use App\Laravue\Models\CompanyTags;
use App\Laravue\Models\Document;
use App\Laravue\Models\Contact;
use App\Http\Resources\CompanyResource;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class CompanyController extends BaseController
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
      $companyQuery = Company::query();
      $limit = Arr::get($searchParams, 'limit', static::ITEM_PER_PAGE);
      $keyword = Arr::get($searchParams, 'keyword', '');
      $tag = Arr::get($searchParams, 'tag', '');
      $region = Arr::get($searchParams, 'region', '');
      $activity = Arr::get($searchParams, 'activity', '');
      
      if (!empty($keyword)) {
        $companyQuery->where('name', 'LIKE', '%' . $keyword . '%');
      }
      if (!empty($tag)) {
        $companyQuery->whereHas('tags', function($q) use ($tag) { $q->where('name', $tag); });
      }
      if (!empty($region)) {
        $companyQuery->where('region', 'LIKE', '%' . $region . '%');
      }
      if (!empty($activity)) {
        $companyQuery->where('activity', 'LIKE', '%' . $activity . '%');
      }

      $companyQuery->orderby('id','DESC');

      return CompanyResource::collection($companyQuery->paginate($limit));
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
      $company = new Company;

      try{
        $company->name = $params['name'];
        $company->tel = $params['tel'];
        $company->fax = $params['fax'];
        $company->email = $params['email'];
        $company->address = $params['address'];
        $company->region = $params['region'];
        $company->activity = $params['activity'];
        $company->nif = $params['nif'];
        $company->rc = $params['rc'];
        $company->ai = $params['ai'];

        $company->save();

        foreach($params['tags'] as $tagId){
          CompanyTags::create([
            'company_id' => $company->id,
            'tag_id' => $tagId,
          ]);
        }

      } catch (\Exception $ex) {
        response()->json(['error' => $ex->getMessage()], 403);
      }
      
      return new CompanyResource($company);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Laravue\Models\Company  $company
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
      $company = Company::find($id);
      $documents = Document::where('company_id', $company->id)->with('items')->get();
      $contacts = Contact::where('company_id', $company->id)->get();

      $data = array( 
          "company" => new CompanyResource($company),
          "documents" => $documents,
          "contacts" => $contacts,
      );

      return response()->json($data, 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Laravue\Models\Company  $company
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Company  $company)
    {
      $params = $request->all();

      $company->name = $params['name'];
      $company->tel = $params['tel'];
      $company->email = $params['email'];
      $company->fax = $params['fax'];
      $company->address = $params['address'];
      $company->region = $params['region'];
      $company->activity = $params['activity'];
      $company->nif = $params['nif'];
      $company->rc = $params['rc'];
      $company->ai = $params['ai'];

      $company->save();

      // delete from company_tags table where company_id === $company->id
      $current_tags = CompanyTags::where('company_id', $company->id)->delete();

      foreach($params['tags'] as $tagId){
        CompanyTags::create([
          'company_id' => $company->id,
          'tag_id' => $tagId,
        ]);
      }
      
      return response()->json(new CompanyResource($company), 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Laravue\Models\Company  $Company
     * @return \Illuminate\Http\Response
     */
    public function destroy(Company $company)
    {
      try {
        $company->delete();
      } catch (\Exception $ex) {
        response()->json(['error' => $ex->getMessage()], 403);
      }

      return response()->json(null, 204);
    }
    /** 
     * Get distinct regions
    */

    public function regions()
    {
      $regions = Company::select('region')->distinct()->get();
      return response()->json($regions, 200);
    }
    /** 
     * Get distinct activities
    */

    public function activities()
    {
      $activities = Company::select('activity')->distinct()->get();
      return response()->json($activities, 200);
    }
}
