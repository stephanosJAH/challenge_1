<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Enums\BookingStatus;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'tour_id',
        'hotel_id',
        'customer_name',
        'customer_email',
        'number_of_people',
        'booking_date',
    ];

    protected $casts = [
        'status' => BookingStatus::class,
    ];

    public function tour()
    {
        return $this->belongsTo(Tour::class);
    }

    public function hotel()
    {
        return $this->belongsTo(Hotel::class);
    }

    /**************************************************************************
     * scopes 
     **************************************************************************/

    /**
     * Scope for filtering by tour name
     */
    public function scopeByTourName($query, $tourName)
    {
        return $query->whereHas('tour', function ($query) use ($tourName) {
            $query->where('name', 'like', '%' . $tourName . '%');
        });
    }

    /**
     * Scope for filtering by tour name
     */
    public function scopeByHotelName($query, $hotelName)
    {
        return $query->whereHas('hotel', function ($query) use ($hotelName) {
            $query->where('name', 'like', '%' . $hotelName . '%');
        });
    }

    /**
     * Scope for filtering by customer name
     */
    public function scopeByCustomerName($query, $customerName)
    {
        return $query->where('customer_name', 'like', '%' . $customerName . '%');
    }

    /**
     * Scope for filtering by booking date range
     */
    public function scopeByBookingDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('booking_date', [$startDate, $endDate]);
    }
}
