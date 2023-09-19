<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CompanyResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
          'id' => $this->id,
          'name' => $this->name,
          'tel' => $this->tel,
          'fax' => $this->fax,
          'email' => $this->email,
          'address' => $this->address,
          'region' => $this->region,
          'activity' => $this->activity,
          'nif' => $this->nif,
          'rc' => $this->rc,
          'ai' => $this->ai,
          'tags' => array_map(
            function ($tag) {
                return $tag['id'];
            },
            $this->tags->toArray()
          ),
          'documents_count' => $this->documents->count()
        ];
    }
}
