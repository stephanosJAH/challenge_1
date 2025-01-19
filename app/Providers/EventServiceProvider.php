<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use App\Events\BookingCreated;
use App\Listeners\SendBookingConfirmation;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        BookingCreated::class => [
            SendBookingConfirmation::class,
        ],
    ];

    public function boot()
    {
        parent::boot();
    }
}