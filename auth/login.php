<?php
session_start();
require_once '../config/database.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if (empty($email) || empty($password)) {
        $error = 'يرجى ملء جميع الحقول';
    } else {
        try {
            $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch();

            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                header('Location: ../index.php');
                exit;
            } else {
                $error = 'الإيميل أو كلمة المرور غير صحيحة';
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
    <title>تسجيل الدخول</title>
</head>
<body>
    <h2>تسجيل الدخول</h2>

    <?php if ($error): ?>
        <p style="color: red;"><?= $error ?></p>
    <?php endif; ?>

    <form method="POST">
        <input type="email" name="email" placeholder="الإيميل" required><br><br>
        <input type="password" name="password" placeholder="كلمة المرور" required><br><br>
        <button type="submit">دخول</button>
    </form>

    <p>ما عندك حساب؟ <a href="register.php">سجل الآن</a></p>
</body>
</html>