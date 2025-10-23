<?php
require_once __DIR__ . '/functions.php';

header('Content-Type: application/json');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');

$sessionId = h4z3_initialize_tracking_session();
$store = h4z3_load_session_store();

if ($sessionId === null || $store === null) {
    http_response_code(503);
    echo json_encode([
        'status' => 'error',
        'message' => 'Session tracking unavailable.',
    ]);
    exit;
}

h4z3_ensure_session_record($store, $sessionId);

$timestamp = h4z3_get_current_timestamp();
$store['sessions'][$sessionId]['last_seen'] = $timestamp;

if (!h4z3_write_session_store($store)) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'Failed to update presence.',
    ]);
    exit;
}

echo json_encode([
    'status' => 'ok',
    'last_seen' => $timestamp,
]);

