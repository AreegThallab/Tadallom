<?php
require_once __DIR__ . "/../../includes/helpers.php";
if (session_status() === PHP_SESSION_NONE) session_start();
?>
<!doctype html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>جاري التحقق - تظلُّم</title>
  <link rel="stylesheet" href="/tazallom_mvp/public/assets/styles.css" />
</head>
<body>

<?php require_once __DIR__ . "/../../includes/layout_header.php"; ?>

<main class="nafath-page">
  <div class="nafath-box" style="max-width:420px;">
    <div class="nafath-loader"></div>
    <div class="nafath-title" style="font-size:16px;">جاري إتمام التحقق...</div>
    <p class="nafath-sub">الرجاء الانتظار، سيتم تحويلك تلقائياً.</p>
  </div>
</main>

<script>
  setTimeout(function () {
    window.location.href = '/tazallom_mvp/public/nafath/nafath_success.php';
  }, 2000);
</script>

<?php require_once __DIR__ . "/../../includes/layout_footer.php"; ?>
</body>
</html>
