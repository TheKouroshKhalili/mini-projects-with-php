<?php
require __DIR__ . '/vendor/autoload.php';
use App\Support\SecureSession;
use App\Support\Database;

SecureSession::startStrict();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token'], $_SESSION['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die('خطای امنیتی: توکن CSRF نامعتبر است.');
    }
    $content = trim($_POST['note'] ?? '');
    $password = trim($_POST['password'] ?? '');
    if ($content === '' || $password === '') {
        die('مقدار یادداشت و  jرمز عبور الزامی است.');
    }
    $content = base64_encode(trim($_POST['note'] ?? ''));
    $note_key = bin2hex(random_bytes(4));
    $expires_at = date('Y-m-d H:i:s', time() + 86400);
    $created_at = date('Y-m-d H:i:s');
    $pdo = Database::create();
    $stmt = $pdo->prepare('INSERT INTO notes (note_key, content, password, expires_at, created_at) VALUES (?, ?, ?, ?, ?)');
    $stmt->execute([$note_key, $content, $password, $expires_at, $created_at]);
    $share_link = "note.php?id=$note_key";
} else {
    header('Location: index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="fa">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>لینک یادداشت</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 min-h-screen flex items-center justify-center">
    <div class="w-full max-w-md mx-auto p-6 bg-white rounded-xl shadow-md text-center">
        <h2 class="text-xl font-bold mb-4 text-gray-800">لینک یادداشت شما</h2>
        <div class="mb-6">
            <a href="<?= htmlspecialchars(
                $share_link
            ) ?>" class="break-all text-blue-600 bg-blue-50 px-4 py-2 rounded-lg hover:underline">
                <?= htmlspecialchars((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . '/' . $share_link) ?>
            </a>
        </div>
        <a href="index.php" class="inline-block mt-2 text-gray-500 hover:text-blue-600">یادداشت جدید</a>
    </div>
</body>
</html> 