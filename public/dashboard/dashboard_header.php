<?php
require_once __DIR__ . "/../../includes/db.php";

if (session_status() === PHP_SESSION_NONE) session_start();

if (!($_SESSION["is_logged_in"] ?? false)) {
  header("Location: /tazallom_mvp/public/index.php");
  exit;
}

$userId = $_SESSION["user_id"];

// جلب اسم المستخدم من الداتابيس
$stmt = $pdo->prepare("SELECT first_name, last_name FROM users WHERE id=? LIMIT 1");
$stmt->execute([$userId]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

$fullName = $user
  ? trim($user["first_name"] . " " . $user["last_name"])
  : "مستخدم";

$avatarLetter = mb_substr($fullName, 0, 1, "UTF-8");
?>

<nav class="landing-nav">
  <div class="container landing-wrap">

    <!-- اللوقو -->
    <a class="landing-brand" href="/tazallom_mvp/public/dashboard/index.php">
      <img src="/tazallom_mvp/public/assets/logo.svg" alt="تظلُّم">
    </a>

    <!-- روابط الداشبورد -->
    <div class="landing-links">
      <a href="/tazallom_mvp/public/dashboard/index.php" class="is-active">الصفحة الرئيسية</a>
      <a href="/tazallom_mvp/public/requests/index.php">الطلبات</a>
      <a href="/tazallom_mvp/public/services/index.php">خدماتنا</a>
      <a href="/tazallom_mvp/public/profile/index.php">الملف الشخصي</a>
    </div>

    <!-- المستخدم -->
    <div class="landing-actions">

      <a class="user-chip" href="/tazallom_mvp/public/profile/index.php">
        <span class="avatar"><?= htmlspecialchars($avatarLetter) ?></span>
        <span class="user-name"><?= htmlspecialchars($fullName) ?></span>
      </a>

      <a class="btn" href="/tazallom_mvp/public">
        تسجيل خروج
      </a>

    </div>

  </div>
</nav>
