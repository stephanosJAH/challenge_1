<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

use App\Models\Booking;
use App\Enums\BookingStatus;
use App\Events\BookingCreated;
use App\Http\Resources\BookingCollection;
use App\Http\Resources\BookingResource;
use App\Filters\BookingFilter;
use App\http\Requests\GetBookingRequest;
use App\http\Requests\StoreBookingRequest;
use App\Jobs\ExportBookingsJob;

class BookingController extends Controller
{

    public function __construct(
        protected BookingFilter $filter
    )
    {
        // add middleware to the controller or other constructor logic
    }

    /**
     * Index bookings.
     * 
     * NOTA: 
     * Este metodo incluye el uso de otra clase para filtrar los resultados.
     * Esta forma de filtrar los resultados es mas flexible y permite reutilizarlos
     * en otros controladores.
     * Y tambien se puede incluir el uso de scopes para filtrar los resultados.
     * 
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
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
    
    /**
     * Index bookings with scopes.
     * 
     * NOTA: 
     * Este metodo incluye el uso de scopes para filtrar los resultados.
     * 
     * @param \App\Http\Requests\GetBookingRequest $request
     * @return \Illuminate\Http\Response
     */
    public function indexScopes(GetBookingRequest $request)
    {
        try{
            $query = Booking::query();

            if ($request->has('tour_name')) {
                $query->byTourName($request->input('tour_name'));
            }

            if ($request->has('hotel_name')) {
                $query->byHotelName($request->input('hotel_name'));
            }

            if ($request->has('customer_name')) {
                $query->byCustomerName($request->input('customer_name'));
            }

            if ($request->has('start_date') && $request->has('end_date')) {
                $query->whereBetween('booking_date', [
                    $request->input('start_date'),
                    $request->input('end_date')
                ]);
            }
            
            $bookings = $query
                        ->orderBy(
                            $request->input('field_order', 'booking_date'),
                            $request->input('direction_order', 'asc')
                        )
                        ->paginate($request->input('per_page', 10));

            return new BookingCollection($bookings);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return response()->json(
                ['error' => 'Internal Server Error. Please try again later.'],
                500
            );
        }
    }

    public function store(StoreBookingRequest $request)
    {   
        try{
            $booking = Booking::create($request->validated());
            event(new BookingCreated($booking));
            return new BookingResource($booking, 201);
        }catch (\Exception $e) {
            Log::error($e->getMessage());
            return response()->json(
                ['error' => 'Error creating booking. Please try again later.'],
                500
            );
        }
        
    }

    public function show(Booking $booking)
    {
        try{
            return new BookingResource($booking);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return response()->json(
                ['error' => 'Internal Server Error. Please try again later.'],
                500
            );
        }
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

    /**
     * Cancel a booking.
     * 
     * @param \App\Models\Booking $booking
     * @return \Illuminate\Http\Response
     */
    public function cancel(Booking $booking)
    {
        try{
            $booking->update(['status' => BookingStatus::Canceled]);
            return response()->json($booking, 200);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return response()->json(
                ['error' => 'Internal Server Error. Please try again later.'],
                500
            );
        }
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
        ExportBookingsJob::dispatch();
        return response()->json(['message' => 'Export job dispatched.'], 200);
    }
}
