<?php
require_once __DIR__ . "/../../includes/db.php";
require_once __DIR__ . "/../../includes/helpers.php";
if (session_status() === PHP_SESSION_NONE) session_start();

$mode = $_SESSION["nafath_mode"] ?? "register";
$userId = $_SESSION["pending_user_id"] ?? null;

if (!$userId) {
  header("Location: /tazallom_mvp/public/auth/login.php");
  exit;
}

// ✅ اعتبريه نجاح (MVP)
// لو تبين لاحقًا: API نفاذ حقيقي/حالة موافقة
try {
  $pdo->prepare("UPDATE users SET is_verified=1 WHERE id=?")->execute([(int)$userId]);
} catch (Throwable $e) {}

$_SESSION["is_logged_in"] = true;
$_SESSION["user_id"] = (int)$userId;

// تنظيف جلسات نفاذ
unset($_SESSION["nafath_code"], $_SESSION["nafath_code_started_at"], $_SESSION["nafath_entered_nid"]);

if ($mode === "login") {
  // ✅ دخول عبر نفاذ -> للداشبورد مباشرة
  header("Location: /tazallom_mvp/public/dashboard/index.php");
  exit;
}
?>
<!doctype html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>تم بنجاح - تظلُّم</title>
  <link rel="stylesheet" href="/tazallom_mvp/public/assets/styles.css" />
</head>
<body>

<?php require_once __DIR__ . "/../../includes/layout_header.php"; ?>

<main class="nafath-page">
  <div class="nafath-box">
    <div class="nafath-title">تم التسجيل بنجاح ✅</div>
    <p class="nafath-sub">تم توثيق الحساب عبر نفاذ. يمكنك الآن متابعة رحلتك.</p>

    <a class="nafath-btn" href="/tazallom_mvp/public/dashboard/index.php" style="display:inline-block;text-decoration:none;">
الانتقال الى منصة تظلُّم 
   </a>
  </div>
</main>

<?php require_once __DIR__ . "/../../includes/layout_footer.php"; ?>
</body>
</html>
