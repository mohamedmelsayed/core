<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PlanResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $lang=seesion()->get('lang');
        return [
            "name"=>$lang=="en"?$this->name_en:$this->name,
            "desciption"=>$lang=="en"?$this->desciption_en:$this->desciption,
            "pricing"=>$this->pricing,
            "duration"=>$this->duration,
            "icon"=>$this->icon,
            "image"=>$this->image,
        ];
    }
}
