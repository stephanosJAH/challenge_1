<?php

namespace App\Jobs;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class ExportBookingsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct()
    {
        // add constructor logic here
    }

    public function handle()
    {
        $bookings = Booking::all();
        $csvData = $this->generateCsv($bookings);

        Storage::put('exports/bookings.csv', $csvData);
    }

    protected function generateCsv($bookings)
    {
        $csv = "ID,Tour ID,Hotel ID,Customer Name,Customer Email,Number of People,Booking Date,Status\n";

        foreach ($bookings as $booking) {
            $csv .= "{$booking->id},{$booking->tour_id},{$booking->hotel_id},{$booking->customer_name},{$booking->customer_email},{$booking->number_of_people},{$booking->booking_date},{$booking->status}\n";
        }

        return $csv;
    }
}