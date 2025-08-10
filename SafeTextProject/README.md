CLI Cleanup Command

نصب

composer dump-autoload -n

اجرا

php bin/console list
php bin/console app:cleanup-old-notes

پیکربندی

config/database.php

کران لینوکس

- - - - - /usr/bin/php /path/to/mini-Safe-text-project/bin/console app:cleanup-old-notes >/dev/null 2>&1

زمان‌بند ویندوز

Program: php
Arguments: C:\laragon\www\mini-simple-projects\mini-Safe-text-project\bin\console app:cleanup-old-notes
Start in: C:\laragon\www\mini-simple-projects\mini-Safe-text-project

توضیحات

حذف بر اساس created_at < (NOW() - INTERVAL 24 HOUR)
اتصال دیتابیس هنگام اجرا ساخته می‌شود تا نمایش list بدون خطا باشد
