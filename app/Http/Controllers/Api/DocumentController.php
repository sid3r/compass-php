<?php

namespace App\Http\Controllers\Api;
use Illuminate\Support\Facades\DB;

use App\Http\Resources\DocumentResource;
use App\Laravue\JsonResponse;
use App\Laravue\Models\Document;
use App\Laravue\Models\DocumentItem;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;

class DocumentController extends BaseController
{
    const ITEM_PER_PAGE = 24;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $searchParams = $request->all();
        $docQuery = Document::query();
        
        $limit = Arr::get($searchParams, 'limit', static::ITEM_PER_PAGE);
        $type = Arr::get($searchParams, 'type', '');
        $keyword = Arr::get($searchParams, 'keyword', '');

        if (!empty($type)) {
            $docQuery->where('type', 'LIKE', '%' . $type . '%');
        }

        if (!empty($keyword)) {
            $docQuery->WhereHas('company', function($query) use ($keyword) {
                return $query->where('name', 'like', '%' . $keyword . '%');
           });
        }
        $docQuery->orderby('date','DESC');
        
        return DocumentResource::collection($docQuery->with(['company', 'items', 'user'])->paginate($limit));
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
      $default_status = 'pending';

      // create document
      $doc = Document::create([
        'type' => $params['type'],
        'code' => $request->get('code'),
        'status' => $default_status,
        'company_id' => $params['company_id'],
        'date' => $params['date'],
        'vatrate' => $params['vatrate'],
        'stamprate' => $params['stamprate'],
        'user_id' => Auth::user()->id,
      ]);

      // create items
      foreach($request->get('items') as $item){
        // insert only if designation is present
        if($item['designation']){
          DocumentItem::create([
            'document_id' => $doc->id,
            'designation' => $item['designation'],
            'quantity' => $item['quantity'],
            'unit' => $item['unit'],
            'unit_price' => $item['unit_price'],
            'discount' => $item['discount'],
          ]);
        }
      }

      return response()->json($doc, 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Laravue\Models\Document  $document
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {

        $doc = Document::where('id', $id)->with(['items', 'company', 'user'])->get();
        $data = array( 
          "document" => $doc[0]
        );
        return response()->json($data, 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Laravue\Models\Document  $document
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Document $document)
    {
      $params = $request->all();
      $document->code = $request->get('code');
      $document->company_id = $params['company']['id'];
      $document->date = $request->get('date');
      $document->vatrate = $request->get('vatrate');
      $document->stamprate = $request->get('stamprate');
      $document->items()->delete();
      
      // insert only if description
      foreach($request->get('items') as $item){
        DocumentItem::create([
          'document_id' => $document->id,
          'designation' => $item['designation'],
          'quantity' => $item['quantity'],
          'unit' => $item['unit'],
          'unit_price' => $item['unit_price'],
          'discount' => $item['discount']
        ]);
      }
      $document->save();

      return response()->json("Document updated.", 204);
    }
    

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Laravue\Models\Document  $document
     * @return \Illuminate\Http\Response
     */
    public function destroy(Document $document)
    {
      try {
        $document->delete();
      } catch (\Exception $ex) {
        response()->json(['error' => $ex->getMessage()], 403);
      }

      return response()->json("Document deleted.", 204);
    }

    /**
     * Get latest invoice & estimate codes.
     *
     * @return \Illuminate\Http\Response
     */
    public function latest()
    {
      $estimate = Document::where('type', "estimate")->where('year', date("Y"))
      // ->where('status', '!=' ,"draft")
      ->orderBy('date', 'desc')->first();

      $invoice = Document::where('type', "invoice")->where('year', date("Y"))
        // ->where('status', '!=' ,"draft")
        ->orderBy('date', 'desc')->first();
      
      if ($estimate) {
        $data['estimate'] = $estimate->code;
      } else {
        $data['estimate'] = 0;
      }
      if ($invoice) {
        $data['invoice'] = $invoice->code;
      } else {
        $data['invoice'] = 0;
      }
      return response()->json($data, 200);
    }
    
    /**
     * Set document status to finished
     * @param  \App\Laravue\Models\Document  $document
     * @return \Illuminate\Http\Response
     */
    public function terminate($id)
    {
      $document = Document::find($id);
      $document->status = "finished";
      $document->save();

      return response()->json("Document terminated.", 200);
    }
    
    /**
     * Set document status to cancelled
     * @param  \App\Laravue\Models\Document  $document
     * @return \Illuminate\Http\Response
     */
    public function cancel($id)
    {
      $document = Document::find($id);
      $document->status = "canceled";
      $document->save();

      return response()->json("Document canceled.", 200);
    }
    
    /**
     * Get documents totals by type and status
     * @param  \App\Laravue\Models\Document  $document
     * @return \Illuminate\Http\Response
     */
    public function stats()
    {
      $stats = Document::groupBy('type')
          ->selectRaw('count(*) as total, type, status')
          ->get();

      return response()->json($stats, 200);
    }
}
