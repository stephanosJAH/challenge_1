<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBookingRequest extends FormRequest
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
            'tour_id' => 'required|exists:tours,id',
            'hotel_id' => 'required|exists:hotels,id',
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'required|email|max:255',
            'number_of_people' => 'required|integer|min:1',
            'booking_date' => 'required|date',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'tour_id.required' => 'Tour ID is required.',
            'tour_id.exists' => 'Tour ID does not exist.',
            'hotel_id.required' => 'Hotel ID is required.',
            'hotel_id.exists' => 'Hotel ID does not exist.',
            'customer_name.required' => 'Customer name is required.',
            'customer_name.string' => 'Customer name must be a string.',
            'customer_name.max' => 'Customer name must not be greater than 255 characters.',
            'customer_email.required' => 'Customer email is required.',
            'customer_email.email' => 'Customer email must be a valid email address.',
            'customer_email.max' => 'Customer email must not be greater than 255 characters.',
            'number_of_people.required' => 'Number of people is required.',
            'number_of_people.integer' => 'Number of people must be an integer.',
            'number_of_people.min' => 'Number of people must be at least 1.',
            'booking_date.required' => 'Booking date is required.',
            'booking_date.date' => 'Booking date must be a valid date.',
        ];
    }
}
