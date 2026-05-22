<?php
session_start();

// فقط سشن ادمین را پاک کن
unset($_SESSION['admin_id']);
unset($_SESSION['admin_ad']);

// کاربر را به صفحه ورود ادمین برگردان
header("Location: login-admin.php");
exit();
?>