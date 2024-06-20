<?php

namespace App\Http\Controllers;

use App\Models\Hotel;
use Illuminate\Http\Request;

class HotelController extends Controller
{
    public function index(Request $request)
    {
        $query = Hotel::query();

        if ($request->has('min_rating')) {
            $query->where('rating', '>=', $request->min_rating);
        }

        if ($request->has('max_rating')) {
            $query->where('rating', '<=', $request->max_rating);
        }

        if ($request->has('min_price')) {
            $query->where('price_per_night', '>=', $request->min_price);
        }

        if ($request->has('max_price')) {
            $query->where('price_per_night', '<=', $request->max_price);
        }

        return response()->json($query->get(), 200);
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
