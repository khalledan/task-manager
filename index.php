<?php
session_start();
require_once 'config/database.php';

// إذا المستخدم مش مسجل الدخول، أرسله لصفحة تسجيل الدخول
if (!isset($_SESSION['user_id'])) {
    header('Location: auth/login.php');
    exit;
}

$user_id = $_SESSION['user_id'];

// إضافة مهمة جديدة
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_task'])) {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);

    if (!empty($title)) {
        $stmt = $pdo->prepare("INSERT INTO tasks (user_id, title, description) VALUES (?, ?, ?)");
        $stmt->execute([$user_id, $title, $description]);
    }
}

// حذف مهمة
if (isset($_GET['delete'])) {
    $task_id = $_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM tasks WHERE id = ? AND user_id = ?");
    $stmt->execute([$task_id, $user_id]);
}

// تغيير حالة المهمة
if (isset($_GET['toggle'])) {
    $task_id = $_GET['toggle'];
    $stmt = $pdo->prepare("SELECT status FROM tasks WHERE id = ? AND user_id = ?");
    $stmt->execute([$task_id, $user_id]);
    $task = $stmt->fetch();

    if ($task) {
        $new_status = $task['status'] === 'pending' ? 'completed' : 'pending';
        $stmt = $pdo->prepare("UPDATE tasks SET status = ? WHERE id = ? AND user_id = ?");
        $stmt->execute([$new_status, $task_id, $user_id]);
    }
}

// جلب جميع المهام
$stmt = $pdo->prepare("SELECT * FROM tasks WHERE user_id = ? ORDER BY created_at DESC");
$stmt->execute([$user_id]);
$tasks = $stmt->fetchAll();

// إحصائيات
$total = count($tasks);
$completed = count(array_filter($tasks, fn($t) => $t['status'] === 'completed'));
$pending = $total - $completed;
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>مدير المهام</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 800px; margin: 40px auto; padding: 20px; background: #f5f5f5; }
        h1 { color: #333; }
        .stats { display: flex; gap: 20px; margin-bottom: 30px; }
        .stat { background: white; padding: 15px 25px; border-radius: 8px; text-align: center; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .stat span { display: block; font-size: 28px; font-weight: bold; color: #4CAF50; }
        form { background: white; padding: 20px; border-radius: 8px; margin-bottom: 30px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        input[type="text"], textarea { width: 100%; padding: 10px; margin-bottom: 10px; border: 1px solid #ddd; border-radius: 5px; box-sizing: border-box; }
        button { background: #4CAF50; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; }
        button:hover { background: #45a049; }
        .task { background: white; padding: 15px 20px; border-radius: 8px; margin-bottom: 10px; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .task.completed { opacity: 0.6; text-decoration: line-through; }
        .task-actions a { margin-right: 10px; text-decoration: none; }
        .toggle { color: #2196F3; }
        .delete { color: #f44336; }
        .logout { float: left; color: #999; text-decoration: none; }
        .logout:hover { color: #f44336; }
    </style>
</head>
<body>

    <a href="logout.php" class="logout">خروج</a>
    <h1>مرحباً <?= htmlspecialchars($_SESSION['username']) ?> 👋</h1>

    <div class="stats">
        <div class="stat"><span><?= $total ?></span>إجمالي المهام</div>
        <div class="stat"><span><?= $completed ?></span>منجزة</div>
        <div class="stat"><span><?= $pending ?></span>قيد التنفيذ</div>
    </div>

    <form method="POST">
        <input type="text" name="title" placeholder="عنوان المهمة" required>
        <textarea name="description" placeholder="وصف المهمة (اختياري)" rows="2"></textarea>
        <button type="submit" name="add_task">إضافة مهمة</button>
    </form>

    <?php if (empty($tasks)): ?>
        <p style="text-align:center; color:#999;">لا توجد مهام بعد — أضف أولى مهامك!</p>
    <?php else: ?>
        <?php foreach ($tasks as $task): ?>
            <div class="task <?= $task['status'] === 'completed' ? 'completed' : '' ?>">
                <div>
                    <strong><?= htmlspecialchars($task['title']) ?></strong>
                    <?php if ($task['description']): ?>
                        <p style="margin:5px 0 0; color:#666; font-size:14px;"><?= htmlspecialchars($task['description']) ?></p>
                    <?php endif; ?>
                </div>
                <div class="task-actions">
                    <a href="?toggle=<?= $task['id'] ?>" class="toggle">
                        <?= $task['status'] === 'pending' ? '✅ إنجاز' : '↩️ إلغاء' ?>
                    </a>
                    <a href="?delete=<?= $task['id'] ?>" class="delete" onclick="return confirm('هل أنت متأكد؟')">🗑️ حذف</a>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>

</body>
</html>