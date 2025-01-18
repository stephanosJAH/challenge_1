<!DOCTYPE html>
<html>
<head>
    <title>Booking Confirmation</title>
</head>
<body>
    <h1>Booking Confirmation</h1>
    <p>Dear {{ $booking->customer_name }},</p>
    <p>Thank you for booking with us. Here are your booking details:</p>
    <ul>
        <li><strong>Tour Name:</strong> {{ $booking->tour->name }}</li>
        <li><strong>Hotel Name:</strong> {{ $booking->hotel->name }}</li>
        <li><strong>Booking Date:</strong> {{ Carbon\Carbon::parse($booking->booking_date)->format('d-m-Y') }}</li>
        <li><strong>Number of People:</strong> {{ $booking->number_of_people }}</li>
    </ul>
    <p>Enjoy your trip!</p>
</body>
</html>