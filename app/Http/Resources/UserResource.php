<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'name'     => $this->name,
            'remake'   => $this->remake,
            'avatar'   => $this->avatar,
            'sex'      => $this->sex,
            'status'   => $this->status,
            'integral' => $this->integral,
        ];
    }
}
