<?php

namespace App\Listeners;

use App\Events\BookingCreated;
use App\Mail\BookingConfirmation;
use Illuminate\Support\Facades\Mail;

class SendBookingConfirmation
{
    public function handle(BookingCreated $event)
    {
        Mail::to($event->booking->customer_email)
            ->send(new BookingConfirmation($event->booking));
    }
}