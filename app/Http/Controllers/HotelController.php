<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

use App\Models\Hotel;
use App\Http\Resources\HotelCollection;

use App\Filters\HotelFilter;

class HotelController extends Controller
{

    public function __construct(
        protected HotelFilter $filter
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

            return new HotelCollection(
                Hotel::where($queryParams)
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
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'address' => 'required|string',
            'rating' => 'required|integer',
            'price_per_night' => 'required|numeric',
        ]);

        $hotel = Hotel::create($validatedData);
        return response()->json($hotel, 201);
    }

    public function show(Hotel $hotel)
    {
        return response()->json($hotel, 200);
    }

    public function update(Request $request, Hotel $hotel)
    {
        $validatedData = $request->validate([
            'name' => 'sometimes|string|max:255',
            'description' => 'sometimes|string',
            'address' => 'sometimes|string',
            'rating' => 'sometimes|integer',
            'price_per_night' => 'sometimes|numeric',
        ]);

        $hotel->update($validatedData);
        return response()->json($hotel, 200);
    }

    public function destroy(Hotel $hotel)
    {
        $hotel->delete();
        return response()->json(null, 204);
    }
}
