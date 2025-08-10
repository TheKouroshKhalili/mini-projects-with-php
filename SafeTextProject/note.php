<?php
require __DIR__ . '/vendor/autoload.php';
use App\Support\SecureSession;
use App\Support\Database;

SecureSession::startStrict();
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$pdo = Database::create();
$note = null;
$error = '';
$show_password_form = false;
$note_content = '';
$note_key = $_GET['id'] ?? '';
function generateSecurePassword(int $length = 50): string { $bytes = openssl_random_pseudo_bytes($length * 2); return substr(str_replace(['/', '+', '='], '', base64_encode($bytes)), 0, $length); }


    $stmt = $pdo->prepare('SELECT * FROM notes WHERE note_key = ? LIMIT 1');
    $stmt->execute([$note_key]);
    $note = $stmt->fetch(PDO::FETCH_ASSOC);
 
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            if (!isset($_POST['csrf_token'], $_SESSION['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
                $error = 'خطای امنیتی: توکن CSRF نامعتبر است.';
                $show_password_form = true;
            } else {
                $input_password = trim($_POST['password'] ?? '');
                if (!$note || $input_password === '' || $input_password !== $note['password']) {
                    $error = 'رمز عبور اشتباه است';
                    $show_password_form = true;
                }elseif ( $note['expires_at'] < date('Y-m-d H:i:s', time())) {
                      $error = 'تایم تموم شده';
                    $show_password_form = true;
                } else {

                    $note_content = htmlspecialchars(base64_decode($note['content']));
                    $show_password_form = false;
                }
            }
        } else {

            $show_password_form = true;
        }
    

?>
<!DOCTYPE html>
<html lang="fa">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>مشاهده یادداشت</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 min-h-screen flex items-center justify-center">
    <div class="w-full max-w-lg mx-auto p-6 bg-white rounded-xl shadow-md">
        <h2 class="text-xl font-bold mb-4 text-gray-800 text-center">یادداشت محرمانه</h2>
        <?php if ($error): ?>
            <div class="bg-red-100 border border-red-300 text-red-700 rounded-lg p-4 mb-4 text-center text-base">
                <?= $error ?>
            </div>
        <?php endif; ?>
        <?php if ($show_password_form): ?>
            <form method="post" class="space-y-4">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
                <label for="password" class="block mb-1 text-right font-medium text-gray-700">رمز عبور یادداشت را وارد کنید:</label>
                <input type="password" id="password" name="password" required class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400 bg-gray-100">
                <button type="submit" class="w-full py-3 bg-blue-600 text-white rounded-lg font-semibold hover:bg-blue-700 transition">مشاهده یادداشت</button>
            </form>
        <?php elseif ($note_content): ?>
            <div class="bg-gray-100 border border-gray-200 rounded-lg p-5 text-gray-700 text-lg leading-relaxed">
                <?= $note_content ?>
            </div>
            <div class="mt-6 text-center">
                <a href="report.php?id=<?= htmlspecialchars($note_key) ?>" class="text-red-500 hover:underline">گزارش یادداشت نامناسب</a>
            </div>
        <?php endif; ?>
    </div>
</body>
</html> 