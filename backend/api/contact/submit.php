<?php
// submit.php — POST: Submit customer inquiry / contact ticket
require_once __DIR__ . '/../config/helpers.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonError('Method not allowed', 405);
}

$input   = json_decode(file_get_contents('php://input'), true);
$name    = trim($input['name'] ?? '');
$email   = trim($input['email'] ?? '');
$phone   = trim($input['phone'] ?? '');
$subject = trim($input['subject'] ?? 'General Inquiry');
$message = trim($input['message'] ?? '');

if (!$name || !$email || !$message) {
    jsonError('Name, email, and message are required');
}

$ticketId = 'TH-TKT-' . date('ymd') . '-' . random_int(100, 999);

jsonResponse([
    'success'   => true,
    'message'   => 'Thank you, ' . htmlspecialchars($name) . '! Your inquiry has been received. Our support team will reach out within 2 hours.',
    'ticket_id' => $ticketId,
    'timestamp' => date('Y-m-d H:i:s')
]);
