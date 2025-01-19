<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BookingResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'tour' => $this->tour,
            'hotel' => $this->hotel,
            'customer_name' => $this->customer_name,
            'customer_email' => $this->customer_email,
            'number_of_people' => $this->number_of_people,
            'booking_date' => $this->booking_date,
        ];
    }
}