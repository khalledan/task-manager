<?php
require_once '../config/database.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if (empty($username) || empty($email) || empty($password)) {
        $error = 'يرجى ملء جميع الحقول';
    } else {
        try {
            // التحقق إذا كان المستخدم موجوداً
            $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->execute([$email]);

            if ($stmt->fetch()) {
                $error = 'هذا الإيميل مستخدم مسبقاً';
            } else {
                // حفظ المستخدم الجديد
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
                $stmt->execute([$username, $email, $hashedPassword]);
                $success = 'تم إنشاء الحساب بنجاح، يمكنك تسجيل الدخول الآن';
            }
        } catch (PDOException $e) {
    $error = $e->getMessage();
}
    }

    }

    ?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>إنشاء حساب</title>
</head>
<body dir="rtl">
    <h2>إنشاء حساب جديد</h2>

    <?php if ($error): ?>
        <p style="color: red;"><?= $error ?></p>
    <?php endif; ?>

    <?php if ($success): ?>
        <p style="color: green;"><?= $success ?></p>
    <?php endif; ?>

    <form method="POST">
        <input type="text" name="username" placeholder="اسم المستخدم" required><br><br>
        <input type="email" name="email" placeholder="الإيميل" required><br><br>
        <input type="password" name="password" placeholder="كلمة المرور" required><br><br>
        <button type="submit">إنشاء حساب</button>
    </form>

    <p>عندك حساب؟ <a href="login.php">سجل الدخول</a></p>
</body>
</html>