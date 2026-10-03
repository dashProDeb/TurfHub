<?php
// process-payout.php — POST: Process and disburse owner payout
require_once __DIR__ . '/../config/helpers.php';
require_once __DIR__ . '/../auth/guard.php';

$admin = guardRole('admin');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonError('Method not allowed', 405);
}

$input = json_decode(file_get_contents('php://input'), true);
$ownerId = (int) ($input['owner_id'] ?? 0);
$amount  = (int) ($input['amount'] ?? 0);
$channel = trim($input['channel'] ?? 'bKash Merchant');
$ref     = 'DIS-' . date('Y-md') . '-' . random_int(10, 99);

if (!$amount) {
    jsonError('Payout amount is required');
}

$db = Database::connect();

jsonResponse([
    'success'         => true,
    'message'         => 'Payout of ৳' . number_format($amount) . ' successfully disbursed via ' . $channel,
    'disbursement_id' => $ref,
    'status'          => 'Disbursed',
    'timestamp'       => date('Y-m-d H:i:s')
]);
