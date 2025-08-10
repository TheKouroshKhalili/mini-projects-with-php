<?php
require_once __DIR__ . '/../src/bootstrap.php'; 


 


function getClientIP() {
    $ip = $_SERVER['REMOTE_ADDR'];
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    }
    return $ip;
}

$ip = getClientIP();


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $result = $auth->login($username, $password, $ip);
    if ($result['ok'] === true) {
        $authConfig = require __DIR__ . '/../config/auth.php';
        $cookieName = $authConfig['session_cookie'] ?? 'session_token';
        $ttl = (int)($authConfig['session_ttl'] ?? 1800);
        setcookie($cookieName, $result['token'], time() + $ttl, '/');
        header('Location: dashboard.php');
        exit;
    } else {
        echo $result['message'];
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>login</title>
</head>
<body>
    <form action="" method="post">
    <input type="text" name="username" placeholder="username">
    <input type="text" name="password" placeholder="password">
    <input type="submit">
</form>


 <?php if (is_logged_in()): ?>

        <div class="welcome">
            خوش آمدید <?php echo htmlspecialchars(get_user_data()['username']); ?>!
            <a href="logout.php">خروج</a>
        </div>
    <?php else: ?>

        <div class="login-prompt">
            لطفاً <a href="login.php">وارد شوید</a>
        </div>
    <?php endif; ?>
    
</body>
</html>