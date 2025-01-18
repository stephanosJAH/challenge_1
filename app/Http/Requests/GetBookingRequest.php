<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GetBookingRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'tour_name' => 'sometimes|string|max:255',
            'hotel_name' => 'sometimes|string|max:255',
            'customer_name' => 'sometimes|string|max:255',
            'start_date' => 'sometimes|date',
            'end_date' => 'sometimes|date',
            'per_page' => 'sometimes|integer|min:1',
            'field_order' => 'sometimes|string|in:tour_name,hotel_name,customer_name,booking_date',
            'direction_order' => 'sometimes|string|in:asc,desc',
        ];
    }

}
