<?php
require_once '../db.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse('error', 'Invalid request method');
}

$data = json_decode(file_get_contents('php://input'), true);
if (!$data) $data = $_POST;

if (empty($data['name']) || empty($data['email']) || empty($data['mobile']) || empty($data['message'])) {
    jsonResponse('error', 'All fields are required');
}

if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
    jsonResponse('error', 'Invalid email');
}

try {
    $db->query("INSERT INTO contact_messages (name, email, mobile, message) VALUES (:name, :email, :mobile, :message)")
        ->bind(':name', sanitize($data['name']))
        ->bind(':email', sanitize($data['email']))
        ->bind(':mobile', sanitize($data['mobile']))
        ->bind(':message', sanitize($data['message']))
        ->execute();

    $db->query("INSERT INTO notifications (type, title, message) VALUES ('contact', 'New Contact Message', :message)")
        ->bind(':message', sanitize($data['name']) . ' sent a contact message')
        ->execute();

    jsonResponse('success', 'Message sent successfully');
} catch (Exception $e) {
    jsonResponse('error', 'Failed to send message');
}
