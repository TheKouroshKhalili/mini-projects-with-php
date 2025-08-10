<?php
require __DIR__ . '/vendor/autoload.php';
use App\Support\SecureSession;
use App\Support\Database;

SecureSession::startStrict();
$pdo = Database::create();
$error = '';
$success = '';
$note_key = $_GET['id'] ?? '';
$reason = '';

if ($note_key === '') {
    $error = 'شناسه یادداشت نامعتبر است.';
} else {
    $stmt = $pdo->prepare('SELECT id FROM notes WHERE note_key = ? LIMIT 1');
    $stmt->execute([$note_key]);
    $note = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$note) {
        $error = 'یادداشت مورد نظر یافت نشد.';
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$error) {
    if (!isset($_POST['csrf_token'], $_SESSION['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $error = 'خطای امنیتی: توکن CSRF نامعتبر است.';
    } else {
        $reason = trim($_POST['reason'] ?? '');
        if ($reason === '') {
            $error = 'لطفاً دلیل گزارش را وارد کنید.';
        } else {
            $stmt = $pdo->prepare('INSERT INTO reports (note_id, reason, created_at) VALUES (?, ?, NOW())');
            if ($stmt->execute([$note['id'], $reason])) {
                $success = 'گزارش شما با موفقیت ثبت شد.';
                $reason = '';
            } else {
                $error = 'خطا در ثبت گزارش. لطفاً دوباره تلاش کنید.';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fa">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>گزارش یادداشت</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 min-h-screen flex items-center justify-center">
    <div class="w-full max-w-lg mx-auto p-6 bg-white rounded-xl shadow-md">
        <h2 class="text-xl font-bold mb-4 text-gray-800 text-center">گزارش یادداشت نامناسب</h2>
        <?php if ($error): ?>
            <div class="bg-red-100 border border-red-300 text-red-700 rounded-lg p-4 mb-4 text-center text-base">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="bg-green-100 border border-green-300 text-green-700 rounded-lg p-4 mb-4 text-center text-base">
                <?= htmlspecialchars($success) ?>
            </div>
        <?php elseif (!$error): ?>
            <form method="post" class="space-y-4">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
                <label for="reason" class="block mb-1 text-right font-medium text-gray-700">دلیل گزارش خود را بنویسید:</label>
                <textarea id="reason" name="reason" required rows="4" class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400 bg-gray-100"><?= htmlspecialchars($reason) ?></textarea>
                <button type="submit" class="w-full py-3 bg-red-600 text-white rounded-lg font-semibold hover:bg-red-700 transition">ارسال گزارش</button>
            </form>
        <?php endif; ?>
    </div>
    <div class="mt-4 text-center">
        <a href="index.php" class="text-gray-500 hover:text-blue-600">بازگشت به خانه</a>
    </div>
</body>
</html> 