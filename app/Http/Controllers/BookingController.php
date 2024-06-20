<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    public function index(Request $request)
    {
        $query = Booking::query();

        if ($request->has('start_date')) {
            $query->where('booking_date', '>=', $request->start_date);
        }

        if ($request->has('end_date')) {
            $query->where('booking_date', '<=', $request->end_date);
        }

        $bookings = $query->get();

        foreach ($bookings as $booking) {
            $booking->tour;
            $booking->hotel;
        }

        return response()->json($bookings, 200);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'tour_id' => 'required|exists:tours,id',
            'hotel_id' => 'required|exists:hotels,id',
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'required|email|max:255',
            'number_of_people' => 'required|integer|min:1',
            'booking_date' => 'required|date',
        ]);

        $booking = Booking::create($validatedData);
        return response()->json($booking, 201);
    }

    public function show(Booking $booking)
    {
        return response()->json($booking, 200);
    }

    public function update(Request $request, Booking $booking)
    {
        $validatedData = $request->validate([
            'tour_id' => 'sometimes|exists:tours,id',
            'hotel_id' => 'sometimes|exists:hotels,id',
            'customer_name' => 'sometimes|string|max:255',
            'customer_email' => 'sometimes|email|max:255',
            'number_of_people' => 'sometimes|integer|min:1',
            'booking_date' => 'sometimes|date',
        ]);

        $booking->update($validatedData);
        return response()->json($booking, 200);
    }

    public function destroy(Booking $booking)
    {
        $booking->delete();
        return response()->json(null, 204);
    }
}
