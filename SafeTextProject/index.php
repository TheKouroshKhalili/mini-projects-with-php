<?php
require __DIR__ . '/vendor/autoload.php';
use App\Support\SecureSession;

SecureSession::startStrict();
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>
<!DOCTYPE html>
<html lang="fa">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>safeBox</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 min-h-screen flex items-center justify-center">
    <div class="w-full max-w-md mx-auto p-6 bg-white rounded-xl shadow-md">
        <h1 class="text-2xl font-bold text-center mb-6 text-gray-800">یادداشت محرمانه</h1>
        <form action="confirm.php" method="post" class="space-y-4">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
            <textarea name="note" rows="7" required placeholder="یادداشت خود را اینجا بنویسید..." class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400 resize-none bg-gray-100"></textarea>
            <div>
                <label for="password" class="block mb-1 text-right font-medium text-gray-700">رمز عبور</label>
                <div class="flex gap-2">
                    <input type="text" id="password" name="password" required class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400 bg-gray-100" readonly>
                    <button type="button" id="copyBtn" class="px-3 py-2 bg-gray-200 rounded-lg hover:bg-gray-300 text-sm">کپی</button>
                </div>
                <small class="block mt-1 text-gray-500 text-xs">می‌توانید رمز را تغییر دهید یا از رمز پیشنهادی استفاده کنید.</small>
            </div>
            <button type="submit" class="w-full py-3 bg-blue-600 text-white rounded-lg font-semibold hover:bg-blue-700 transition">ایجاد لینک اشتراک‌گذاری</button>
        </form>
    </div>
    <script>
function generatePassword(length = 12) {
    const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789!@#$%^&*';
    let pass = '';
    for (let i = 0; i < length; i++) {
        pass += chars.charAt(Math.floor(Math.random() * chars.length));
    }
    return pass;
}

const passwordInput = document.getElementById('password');
const copyBtn = document.getElementById('copyBtn');
window.addEventListener('DOMContentLoaded', () => {
    passwordInput.value = generatePassword();
});
copyBtn.addEventListener('click', () => {
    passwordInput.select();
    document.execCommand('copy');
    copyBtn.textContent = 'کپی شد!';
    setTimeout(() => copyBtn.textContent = 'کپی', 1200);
});
passwordInput.readOnly = false;
</script>
</body>
</html> 