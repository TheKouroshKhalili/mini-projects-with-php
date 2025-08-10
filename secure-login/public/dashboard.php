<?php
require_once __DIR__ . '/../src/bootstrap.php';
$authConfig = require __DIR__ . '/../config/auth.php';
$cookieName = $authConfig['session_cookie'] ?? 'session_token';

if (!isset($_COOKIE[$cookieName]) || !is_logged_in()) {
    header('Location: login.php');
    exit;
}

$user = get_user_data();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>dashboard</title>
</head>
<body>
    <div class="welcome">
        خوش آمدید <?php echo htmlspecialchars($user['username']); ?>!
        <a href="logout.php">خروج</a>
    </div>
</body>
</html>

