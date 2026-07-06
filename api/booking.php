<?php
require_once '../db.php';
require_once '../auth.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse('error', 'Invalid request method');
}

$data = json_decode(file_get_contents('php://input'), true);
if (!$data) {
    $data = $_POST;
}

// Validate customer details
$required = ['full_name','email','mobile','address','city','state','pincode','pickup_date','pickup_time','return_date','return_time','car_id'];
foreach ($required as $field) {
    if (empty($data[$field])) {
        jsonResponse('error', ucfirst(str_replace('_', ' ', $field)) . ' is required');
    }
}

// Validate KYC persons
$kycPersons = $data['kyc_persons'] ?? [];
if (count($kycPersons) < 2) {
    jsonResponse('error', 'Two KYC persons are mandatory');
}
foreach ($kycPersons as $p) {
    if (!preg_match('/^\d{12}$/', $p['aadhaar']) || !preg_match('/^\d{10}$/', $p['mobile']) || empty($p['name'])) {
        jsonResponse('error', 'Invalid KYC details');
    }
}

// Get car pricing
$car = $db->query("SELECT * FROM cars WHERE id = :id LIMIT 1")
    ->bind(':id', (int)$data['car_id'], PDO::PARAM_INT)
    ->fetchOne();

if (!$car) {
    jsonResponse('error', 'Car not found');
}

// Calculate rental duration and amount
$pickup = new DateTime($data['pickup_date'] . ' ' . $data['pickup_time']);
$return = new DateTime($data['return_date'] . ' ' . $data['return_time']);
if ($return <= $pickup) {
    jsonResponse('error', 'Return time must be after pickup time');
}

$diff = $pickup->diff($return);
$hours = max(1, ($diff->days * 24) + $diff->h);
$days = max(1, ceil($hours / 24));
$rentalAmount = min($hours * $car['price_per_hour'], $days * $car['price_per_day']);
$totalAmount = $rentalAmount + $car['security_deposit'];

$bookingId = 'HS-' . strtoupper(bin2hex(random_bytes(4)));

try {
    $db->conn->beginTransaction();

    $db->query("INSERT INTO bookings (booking_id, car_id, user_id, customer_name, email, mobile, address, city, state, pincode, pickup_date, pickup_time, return_date, return_time, hours, days, rental_amount, security_deposit, total_amount, payment_method) VALUES (:booking_id, :car_id, :user_id, :customer_name, :email, :mobile, :address, :city, :state, :pincode, :pickup_date, :pickup_time, :return_date, :return_time, :hours, :days, :rental_amount, :security_deposit, :total_amount, :payment_method)")
        ->bind(':booking_id', $bookingId)
        ->bind(':car_id', (int)$data['car_id'], PDO::PARAM_INT)
        ->bind(':user_id', $data['user_id'] ?? null, PDO::PARAM_INT)
        ->bind(':customer_name', sanitize($data['full_name']))
        ->bind(':email', sanitize($data['email']))
        ->bind(':mobile', sanitize($data['mobile']))
        ->bind(':address', sanitize($data['address']))
        ->bind(':city', sanitize($data['city']))
        ->bind(':state', sanitize($data['state']))
        ->bind(':pincode', sanitize($data['pincode']))
        ->bind(':pickup_date', $data['pickup_date'])
        ->bind(':pickup_time', $data['pickup_time'])
        ->bind(':return_date', $data['return_date'])
        ->bind(':return_time', $data['return_time'])
        ->bind(':hours', $hours, PDO::PARAM_INT)
        ->bind(':days', $days, PDO::PARAM_INT)
        ->bind(':rental_amount', $rentalAmount)
        ->bind(':security_deposit', $car['security_deposit'])
        ->bind(':total_amount', $totalAmount)
        ->bind(':payment_method', $data['payment_method'] ?? 'upi')
        ->execute();

    $bookingDbId = $db->lastInsertId();

    foreach ($kycPersons as $p) {
        $db->query("INSERT INTO booking_persons (booking_id, person_name, aadhaar_number, mobile_number) VALUES (:booking_id, :name, :aadhaar, :mobile)")
            ->bind(':booking_id', $bookingDbId, PDO::PARAM_INT)
            ->bind(':name', sanitize($p['name']))
            ->bind(':aadhaar', sanitize($p['aadhaar']))
            ->bind(':mobile', sanitize($p['mobile']))
            ->execute();
    }

    $db->query("INSERT INTO notifications (type, title, message) VALUES ('booking', 'New Booking', :message)")
        ->bind(':message', "New booking {$bookingId} by " . sanitize($data['full_name']) . " for amount " . CURRENCY . number_format($totalAmount, 2))
        ->execute();

    $db->conn->commit();

    // Send confirmation email (requires mail server configuration)
    // mail($data['email'], 'Booking Confirmation - HS CAR RENTAL', "Your booking {$bookingId} is confirmed.");

    jsonResponse('success', 'Booking created', ['booking_id' => $bookingId, 'total_amount' => $totalAmount]);
} catch (Exception $e) {
    $db->conn->rollBack();
    jsonResponse('error', 'Booking failed: ' . $e->getMessage());
}
