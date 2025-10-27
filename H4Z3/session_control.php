<?php
require_once __DIR__ . '/../settings.php';
require_once __DIR__ . '/functions.php';

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

if ($method === 'POST' && !empty($adminSessionName)) {
    session_name($adminSessionName);
}

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
$store = h4z3_load_session_store();

if ($store === null) {
    if ($method === 'GET') {
        header('Content-Type: application/json');
        http_response_code(503);
        echo json_encode(['success' => false, 'error' => 'Session store unavailable']);
        exit;
    }

    if (!empty($_POST['redirect'])) {
        header('Location: ' . $_POST['redirect']);
        exit;
    }

    header('Content-Type: application/json');
    http_response_code(503);
    echo json_encode(['success' => false, 'error' => 'Session store unavailable']);
    exit;
}

if ($method === 'GET') {
    $sessionId = h4z3_initialize_tracking_session();

    if ($sessionId === null) {
        header('Content-Type: application/json');
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Unable to determine session']);
        exit;
    }

    h4z3_ensure_session_record($store, $sessionId);

    $pendingAction = $store['sessions'][$sessionId]['pending_action'] ?? null;
    $store['sessions'][$sessionId]['pending_action'] = null;
    $store['sessions'][$sessionId]['last_seen'] = h4z3_get_current_timestamp();

    h4z3_write_session_store($store);

    header('Content-Type: application/json');
    echo json_encode([
        'success' => true,
        'pending_action' => $pendingAction,
    ]);
    exit;
}

if ($method === 'POST') {
    if (empty($_SESSION['admin_authenticated'])) {
        header('Content-Type: application/json');
        http_response_code(403);
        echo json_encode(['success' => false, 'error' => 'Admin authentication required']);
        exit;
    }

    $sessionId = $_POST['session_id'] ?? '';
    $pendingAction = $_POST['pending_action'] ?? null;

    if ($sessionId === '') {
        $response = ['success' => false, 'error' => 'Missing session identifier'];
    } elseif (!isset($store['sessions'][$sessionId])) {
        $response = ['success' => false, 'error' => 'Session not found'];
    } else {
        h4z3_ensure_session_record($store, $sessionId);

        if ($pendingAction === '' || $pendingAction === null) {
            $store['sessions'][$sessionId]['pending_action'] = null;
        } else {
            $store['sessions'][$sessionId]['pending_action'] = $pendingAction;
        }

        h4z3_write_session_store($store);

        $response = [
            'success' => true,
            'session_id' => $sessionId,
            'pending_action' => $store['sessions'][$sessionId]['pending_action'],
        ];
    }

    if (!empty($_POST['redirect'])) {
        $redirect = $_POST['redirect'];
        if (!headers_sent()) {
            if (!empty($response['success'])) {
                header('Location: ' . $redirect);
                exit;
            }

            header('Location: ' . $redirect . '?error=' . urlencode($response['error'] ?? 'Unknown error'));
            exit;
        }
    }

    header('Content-Type: application/json');
    if (empty($response['success'])) {
        http_response_code(400);
    }

    echo json_encode($response);
    exit;
}

header('Content-Type: application/json');
http_response_code(405);

echo json_encode(['success' => false, 'error' => 'Method not allowed']);
