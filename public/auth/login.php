<?php
require_once __DIR__ . "/../../includes/db.php";
require_once __DIR__ . "/../../includes/helpers.php";

if (session_status() === PHP_SESSION_NONE) session_start();

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $email = trim($_POST["email"] ?? "");
  $pass  = $_POST["password"] ?? "";

  if ($email === "" || $pass === "") {
    $error = "رجاءً أدخلي البريد الإلكتروني وكلمة المرور.";
  } else {
    $st = $pdo->prepare("SELECT id, password_hash FROM users WHERE email=? LIMIT 1");
    $st->execute([$email]);
    $u = $st->fetch(PDO::FETCH_ASSOC);

    if (!$u || !password_verify($pass, $u["password_hash"])) {
      $error = "بيانات الدخول غير صحيحة.";
    } else {
      $_SESSION["is_logged_in"] = true;
      $_SESSION["user_id"] = (int)$u["id"];
      header("Location: /tazallom_mvp/public/dashboard/index.php");
      exit;
    }
  }
}
?>
<!doctype html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>تسجيل الدخول - تظلُّم</title>
  <link rel="stylesheet" href="/tazallom_mvp/public/assets/styles.css" />
</head>
<body>

<!-- ✅ Corner logo (يرجع للصفحة الرئيسية) -->
<a class="auth-corner" href="/tazallom_mvp/public/index.php" aria-label="العودة للصفحة الرئيسية">
  <img src="/tazallom_mvp/public/assets/logo1.svg" alt="تظلُّم">
</a>

<!-- ✅ Centered layout -->
<main class="auth-center">

  <div class="auth-stack narrow">

    <!-- ✅ Logo plain فوق الكارد (مو داخل كارد) -->
    <div class="auth-logo-plain">
      <img src="/tazallom_mvp/public/assets/logo1.svg" alt="تظلُّم">
    </div>

    <div class="glass card auth-card">
      <h2 class="auth-title">تسجيل الدخول</h2>

      <?php if ($error): ?>
        <div style="background:#3a1b1b;border:1px solid rgba(255,255,255,.08);padding:12px;border-radius:12px;margin-bottom:14px;">
          <div style="color:#ffd2d2;line-height:1.7; text-align:center;">
            <?= htmlspecialchars($error) ?>
          </div>
        </div>
      <?php endif; ?>

      <form method="post" class="form">
        <div class="field">
          <label>البريد الإلكتروني</label>
          <input type="email" name="email" required value="<?= htmlspecialchars($_POST["email"] ?? "") ?>">
        </div>

        <div class="field">
          <label>كلمة المرور</label>
          <input type="password" name="password" required>
        </div>

        <button class="btn primary" type="submit" style="width:100%;">تسجيل دخول</button>

        <a class="btn" style="width:100%; text-align:center; margin-top:12px;"
           href="/tazallom_mvp/public/nafath/nafath_login.php?mode=login">
          تسجيل دخول عبر نفاذ
        </a>

        <a class="small-link" href="/tazallom_mvp/public/auth/register.php">ما عندي حساب؟ إنشاء حساب</a>
      </form>
    </div>

  </div>

</main>

</body>
</html>
