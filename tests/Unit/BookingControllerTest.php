<?php

use App\Models\Booking;
use App\Enums\BookingStatus;
use App\Models\Tour;
use App\Models\Hotel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Benchmark;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;
use Illuminate\Support\Facades\Mail;

uses(TestCase::class, RefreshDatabase::class);

it('can create a booking', function () {
    $tour = Tour::factory()->create();
    $hotel = Hotel::factory()->create();

    $bookingData = Booking::factory()->make([
        'tour_id' => $tour->id,
        'hotel_id' => $hotel->id,
    ])->toArray();

    $response = $this->postJson('/api/bookings', $bookingData);

    $response->assertStatus(Response::HTTP_CREATED)
        ->assertJsonFragment([
            'tour_id' => $tour->id,
            'hotel_id' => $hotel->id,
            'customer_name' => $bookingData['customer_name'],
            'customer_email' => $bookingData['customer_email'],
        ]);
});

it('fails to create a booking with invalid data', function () {
    $response = $this->postJson('/api/bookings', []);
    $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
        ->assertJsonValidationErrors(['tour_id', 'hotel_id', 'customer_name', 'customer_email', 'number_of_people', 'booking_date']);
});

it('can retrieve all bookings', function () {
    Booking::factory()->count(3)->create();

    $response = $this->getJson('/api/bookings');
    $response->assertStatus(Response::HTTP_OK)
        ->assertJsonCount(3);
});

it('can retrieve all bookings performing ok', function(){
    Booking::factory()->count(1000)->create();

    $benchmark = Benchmark::measure([
        'normal' => fn () => $this->get('/api/bookings')
    ]);

    $this->assertTrue($benchmark['normal'] < 300);
})->repeat(10);

it('can retrieve bookings with filters', function () {
    $booking1 = Booking::factory()->create(['booking_date' => now()->subDays(2)]);
    $booking2 = Booking::factory()->create(['booking_date' => now()->subDays(1)]);
    $booking3 = Booking::factory()->create(['booking_date' => now()]);

    $response = $this->getJson('/api/bookings?start_date=' . now()->subDays(1)->toDateString());
    $response->assertStatus(Response::HTTP_OK)
        ->assertJsonFragment(['id' => $booking2->id])
        ->assertJsonFragment(['id' => $booking3->id])
        ->assertJsonMissing(['id' => $booking1->id]);
});

it('can retrieve a single booking', function () {
    $booking = Booking::factory()->create();

    $response = $this->getJson("/api/bookings/{$booking->id}");
    $response->assertStatus(Response::HTTP_OK)
        ->assertJson([
            'id' => $booking->id,
            'tour_id' => $booking->tour_id,
            'hotel_id' => $booking->hotel_id,
            'customer_name' => $booking->customer_name,
            'customer_email' => $booking->customer_email,
        ]);
});

it('returns 404 for a non-existent booking', function () {
    $response = $this->getJson('/api/bookings/999');
    $response->assertStatus(Response::HTTP_NOT_FOUND);
});

it('can update a booking', function () {
    $booking = Booking::factory()->create();
    $updatedData = Booking::factory()->make()->toArray();

    $response = $this->putJson("/api/bookings/{$booking->id}", $updatedData);
    $response->assertStatus(Response::HTTP_OK)
        ->assertJsonFragment([
            'tour_id' => $updatedData['tour_id'],
            'hotel_id' => $updatedData['hotel_id'],
            'customer_name' => $updatedData['customer_name'],
            'customer_email' => $updatedData['customer_email'],
        ]);
});

it('fails to update a booking with invalid data', function () {
    $booking = Booking::factory()->create();

    $response = $this->putJson("/api/bookings/{$booking->id}", 
        ['tour_id' => '', 'hotel_id' => '', 'customer_name' => '', 'customer_email' => '', 'number_of_people' => '', 'booking_date' => '']);
    $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
        ->assertJsonValidationErrors(['tour_id', 'hotel_id', 'customer_name', 'customer_email', 'number_of_people', 'booking_date']);
});

it('can delete a booking', function () {
    $booking = Booking::factory()->create();

    $response = $this->deleteJson("/api/bookings/{$booking->id}");
    $response->assertStatus(Response::HTTP_NO_CONTENT);

    $this->assertDatabaseMissing('bookings', ['id' => $booking->id]);
});

it('returns 404 when deleting a non-existent booking', function () {
    $response = $this->deleteJson('/api/bookings/999');
    $response->assertStatus(Response::HTTP_NOT_FOUND);
});

it('sends an email when a booking is created', function () {
    Mail::fake();

    $tour = Tour::factory()->create();
    $hotel = Hotel::factory()->create();

    $bookingData = Booking::factory()->make([
        'tour_id' => $tour->id,
        'hotel_id' => $hotel->id,
    ])->toArray();

    $response = $this->postJson('/api/bookings', $bookingData);
    $response->assertStatus(Response::HTTP_CREATED);

    Mail::assertSent(function ($mail) use ($bookingData) {
        return $mail->hasTo($bookingData['customer_email']);
    });
});

it('can cancel a booking', function () {
    $booking = Booking::factory()->create();

    $response = $this->getJson("/api/bookings/{$booking->id}/cancel");

    $response->assertStatus(Response::HTTP_OK)
        ->assertJsonFragment(['status' => BookingStatus::Canceled]);

    $this->assertDatabaseHas('bookings', [
        'id' => $booking->id,
        'status' => BookingStatus::Canceled,
    ]);
});

it('can export bookings', function () {
    $response = $this->getJson('/api/bookings/export');
    $response->assertStatus(Response::HTTP_OK)
        ->assertJson(['message' => 'Export job dispatched.']);
});

it('can retrieve bookings filtered to hotel name', function () {
    $hotel = Hotel::factory()->create(['name' => 'Hotel test']);

    $booking1 = Booking::factory()->create(['hotel_id' => $hotel->id]);
    $booking2 = Booking::factory()->create();

    $response = $this->getJson('/api/bookings?hotel_name=Hotel test');

    $response->assertStatus(Response::HTTP_OK)
        ->assertJsonFragment(['id' => $booking1->id])
        ->assertJsonMissing(['id' => $booking2->id]);
});

it('can retrieve bookings filtered to tour name', function () {
    $tour = Tour::factory()->create(['name' => 'Tour test']);

    $booking1 = Booking::factory()->create(['tour_id' => $tour->id]);
    $booking2 = Booking::factory()->create();

    $response = $this->getJson('/api/bookings?tour_name=Tour test');

    $response->assertStatus(Response::HTTP_OK)
        ->assertJsonFragment(['id' => $booking1->id])
        ->assertJsonMissing(['id' => $booking2->id]);
});

it('can retrieve bookings filtered to customer name', function () {
    $booking1 = Booking::factory()->create(['customer_name' => 'Customer test']);
    $booking2 = Booking::factory()->create();

    $response = $this->getJson('/api/bookings?customer_name=Customer test');

    $response->assertStatus(Response::HTTP_OK)
        ->assertJsonFragment(['id' => $booking1->id])
        ->assertJsonMissing(['id' => $booking2->id]);
});

it('can retrieve bookings filtered to date range', function () {
    $booking1 = Booking::factory()->create(['booking_date' => now()->subDays(2)]);
    $booking2 = Booking::factory()->create(['booking_date' => now()->subDays(1)]);
    $booking3 = Booking::factory()->create(['booking_date' => now()]);

    $response = $this->getJson('/api/bookings?start_date=' . now()->subDays(1)->toDateString() . '&end_date=' . now()->toDateString());
    $response->assertStatus(Response::HTTP_OK)
        ->assertJsonFragment(['id' => $booking2->id])
        ->assertJsonFragment(['id' => $booking3->id])
        ->assertJsonMissing(['id' => $booking1->id]);
});

/**
 * Test the indexFilter method.
 */
it('can retrieve bookings filtered without scope, filter [eq] customer name', function () {
    $booking1 = Booking::factory()->create(['customer_name' => 'Customer test 2']);
    $booking2 = Booking::factory()->create(['customer_name' => 'Customer test']);

    $response = $this->getJson('/api/bookings/index-filter?customer_name[eq]=Customer test 2');

    $response->assertStatus(Response::HTTP_OK)
        ->assertJsonFragment(['id' => $booking1->id])
        ->assertJsonMissing(['id' => $booking2->id]);
});

it('can retrieve bookings filtered without scope, filter [like] customer name', function () {
    $booking1 = Booking::factory()->create(['customer_name' => 'Customer findname']);
    $booking2 = Booking::factory()->create(['customer_name' => 'Customer test']);

    $response = $this->getJson('/api/bookings/index-filter?customer_name[like]=findname');

    $response->assertStatus(Response::HTTP_OK)
        ->assertJsonFragment(['id' => $booking1->id])
        ->assertJsonMissing(['id' => $booking2->id]);
});

it('can retrieve bookings filtered without scope, filter [tle] number of people', function () {
    $booking1 = Booking::factory()->create(['number_of_people' => 2]);
    $booking2 = Booking::factory()->create(['number_of_people' => 3]);

    $response = $this->getJson('/api/bookings/index-filter?number_of_people[tle]=2');

    $response->assertStatus(Response::HTTP_OK)
        ->assertJsonFragment(['id' => $booking1->id])
        ->assertJsonMissing(['id' => $booking2->id]);
});

it('can retrieve bookings filtered without scope, filter [gte][lte] booking date', function () {
    $booking1 = Booking::factory()->create(['booking_date' => now()->subDays(2)]);
    $booking2 = Booking::factory()->create(['booking_date' => now()->subDays(1)]);
    $booking3 = Booking::factory()->create(['booking_date' => now()]);

    $response = $this->getJson('/api/bookings/index-filter?booking_date[gte]=' . now()->subDays(2)->toDateString() . '&booking_date[lte]=' . now()->subDays(1)->toDateString());
    $response->assertStatus(Response::HTTP_OK)
        ->assertJsonFragment(['id' => $booking1->id])
        ->assertJsonFragment(['id' => $booking2->id])
        ->assertJsonMissing(['id' => $booking3->id]);
});