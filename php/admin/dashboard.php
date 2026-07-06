<?php
require_once '../config.php';
require_once '../db.php';
require_once '../auth.php';

$auth->requireAuth('admin');

$stats = [
    'total_cars' => $db->query("SELECT COUNT(*) as c FROM cars")->fetchOne()['c'],
    'total_bookings' => $db->query("SELECT COUNT(*) as c FROM bookings")->fetchOne()['c'],
    'revenue' => $db->query("SELECT COALESCE(SUM(total_amount), 0) as c FROM bookings WHERE payment_status = 'Paid'")->fetchOne()['c'],
    'pending_payments' => $db->query("SELECT COUNT(*) as c FROM bookings WHERE payment_status = 'Pending'")->fetchOne()['c'],
    'active_rentals' => $db->query("SELECT COUNT(*) as c FROM bookings WHERE booking_status = 'Active'")->fetchOne()['c'],
    'pending_approvals' => $db->query("SELECT COUNT(*) as c FROM bookings WHERE booking_status = 'Pending'")->fetchOne()['c'],
    'total_users' => $db->query("SELECT COUNT(*) as c FROM users WHERE role = 'user'")->fetchOne()['c'],
];

$recentBookings = $db->query("SELECT b.*, c.name as car_name FROM bookings b JOIN cars c ON b.car_id = c.id ORDER BY b.created_at DESC LIMIT 10")->fetchAll();
$notifications = $db->query("SELECT * FROM notifications ORDER BY created_at DESC LIMIT 20")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - HS CAR RENTAL</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <nav class="navbar navbar-dark bg-dark mb-4">
        <div class="container-fluid">
            <span class="navbar-brand">HS CAR RENTAL - Admin Panel</span>
            <div>
                <a href="../../" class="btn btn-sm btn-outline-light me-2">View Site</a>
                <a href="../logout.php" class="btn btn-sm btn-outline-danger">Logout</a>
            </div>
        </div>
    </nav>
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-2">
                <div class="list-group">
                    <a href="dashboard.php" class="list-group-item list-group-item-action active">Dashboard</a>
                    <a href="cars.php" class="list-group-item list-group-item-action">Cars</a>
                    <a href="bookings.php" class="list-group-item list-group-item-action">Bookings</a>
                    <a href="payments.php" class="list-group-item list-group-item-action">Payments</a>
                    <a href="reviews.php" class="list-group-item list-group-item-action">Reviews</a>
                    <a href="cms.php" class="list-group-item list-group-item-action">CMS</a>
                </div>
            </div>
            <div class="col-md-10">
                <h2 class="mb-4">Dashboard</h2>
                <div class="row g-3 mb-4">
                    <?php foreach ($stats as $key => $value): ?>
                    <div class="col-md-3">
                        <div class="card text-white bg-dark">
                            <div class="card-body">
                                <h5 class="card-title text-capitalize"><?php echo str_replace('_', ' ', $key); ?></h5>
                                <p class="card-text fs-3"><?php echo $key === 'revenue' ? CURRENCY . number_format($value, 2) : $value; ?></p>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>

                <h4>Recent Bookings</h4>
                <table class="table table-bordered table-striped">
                    <thead><tr><th>Booking ID</th><th>Customer</th><th>Car</th><th>Amount</th><th>Status</th></tr></thead>
                    <tbody>
                        <?php foreach ($recentBookings as $b): ?>
                        <tr>
                            <td><?php echo $b['booking_id']; ?></td>
                            <td><?php echo $b['customer_name']; ?></td>
                            <td><?php echo $b['car_name']; ?></td>
                            <td><?php echo CURRENCY . number_format($b['total_amount'], 2); ?></td>
                            <td><?php echo $b['booking_status']; ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>
