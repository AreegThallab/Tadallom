<?php
require_once __DIR__ . "/../../includes/helpers.php";
if (session_status() === PHP_SESSION_NONE) session_start();

$code = $_SESSION["nafath_code"] ?? null;
if ($code === null) {
  header("Location: /tazallom_mvp/public/nafath/nafath_login.php");
  exit;
}
?>
<!doctype html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>رمز نفاذ - تظلُّم</title>
  <link rel="stylesheet" href="/tazallom_mvp/public/assets/styles.css" />
</head>
<body>

<?php require_once __DIR__ . "/../../includes/layout_header.php"; ?>

<main class="nafath-page">
  <div class="nafath-box">

    <div class="nafath-logos">
      <img src="/tazallom_mvp/public/assets/logo1.svg" alt="تظلُّم">
      <img src="/tazallom_mvp/public/assets/NafathLogo.png" alt="نفاذ">
    </div>

    <div class="nafath-title">النفاذ الوطني الموحّد</div>
    <p class="nafath-sub">الرجاء فتح تطبيق نفاذ وتأكيد الطلب باختيار الرقم الظاهر</p>

    <div class="nafath-code"><?= htmlspecialchars($code, ENT_QUOTES, 'UTF-8') ?></div>

    <div id="countdown-text" class="nafath-sub">سيتم المتابعة تلقائياً خلال 7 ثوانٍ...</div>

    <form method="POST" action="/tazallom_mvp/public/nafath/nafath_login.php">
      <button class="nafath-btn secondary" type="submit" name="cancel">إلغاء</button>
    </form>

  </div>
</main>

<script>
  let seconds = 7;
  const text = document.getElementById('countdown-text');
  const timer = setInterval(function () {
    seconds--;
    if (seconds > 0) {
      text.textContent = 'سيتم المتابعة تلقائياً خلال ' + seconds + ' ثوانٍ...';
    } else {
      clearInterval(timer);
      window.location.href = '/tazallom_mvp/public/nafath/nafath_loading.php';
    }
  }, 1000);
</script>

<?php require_once __DIR__ . "/../../includes/layout_footer.php"; ?>
</body>
</html>
