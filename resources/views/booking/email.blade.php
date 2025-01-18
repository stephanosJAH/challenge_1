<!DOCTYPE html>
<html>
<head>
    <title>Booking Confirmation</title>
</head>
<body>
    <h1>Booking Confirmation</h1>
    <p>Dear {{ $clientName }},</p>
    <p>Thank you for booking with us. Here are your booking details:</p>
    <ul>
        <li><strong>Tour Name:</strong> {{ $tourName }}</li>
        <li><strong>Hotel Name:</strong> {{ $hotelName }}</li>
        <li><strong>Booking Date:</strong> {{ $bookingDate }}</li>
        <li><strong>Number of People:</strong> {{ $numberOfPeople }}</li>
    </ul>
    <p>Enjoy your trip!</p>
</body>
</html>