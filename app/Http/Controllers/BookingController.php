<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

use App\Models\Booking;
use App\Http\Resources\BookingCollection;
use App\Http\Resources\BookingResource;

use App\Filters\BookingFilter;

class BookingController extends Controller
{

    public function __construct(
        protected BookingFilter $filter
    )
    {
        // add middleware to the controller or other constructor logic
    }

    public function index(Request $request)
    {
        try{
            $queryParams = $this->filter->queryParams($request);
            $sortData = $this->filter->sort($request);
            $paginate = $this->filter->paginate($request);

            return new BookingCollection(
                Booking::where($queryParams)
                    ->orderBy($sortData[0], $sortData[1])
                    ->paginate($paginate)
                    ->appends($request->query()),
                200
            );
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return response()->json(
                ['error' => 'Internal Server Error. Please try again later.'],
                500
            );
        }
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

    /**
     * Export bookings to a CSV file.
     * 
     * @return \Illuminate\Http\Response
     */
    public function export()
    {
        // export bookings to a file
    }


}
