<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

use App\Models\Tour;
use App\Http\Resources\TourCollection;

use App\Filters\TourFilter;

class TourController extends Controller
{

    public function __construct(
        protected TourFilter $filter
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

            return new TourCollection(
                Tour::where($queryParams)
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
            'price' => 'required|numeric',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $tour = Tour::create($validatedData);
        return response()->json($tour, 201);
    }

    public function show(Tour $tour)
    {
        return response()->json($tour, 200);
    }

    public function update(Request $request, Tour $tour)
    {
        $validatedData = $request->validate([
            'name' => 'sometimes|string|max:255',
            'description' => 'sometimes|string',
            'price' => 'sometimes|numeric',
            'start_date' => 'sometimes|date',
            'end_date' => 'sometimes|date|after_or_equal:start_date',
        ]);

        $tour->update($validatedData);
        return response()->json($tour, 200);
    }

    public function destroy(Tour $tour)
    {
        $tour->delete();
        return response()->json(null, 204);
    }
}
