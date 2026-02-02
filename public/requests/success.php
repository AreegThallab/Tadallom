<?php
require_once __DIR__ . "/../../includes/helpers.php";

if (session_status() === PHP_SESSION_NONE) session_start();

if (!($_SESSION["is_logged_in"] ?? false)) {
  header("Location: /tazallom_mvp/public/auth/login.php");
  exit;
}

$id = (int)($_GET["id"] ?? 0);
if ($id <= 0) die("طلب غير صالح");
?>
<!doctype html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>تم إرسال الطلب - تظلُّم</title>
  <link rel="stylesheet" href="/tazallom_mvp/public/assets/styles.css" />

  <!-- ✅ تحويل تلقائي إلى analyze.php بعد ثانيتين -->
  <meta http-equiv="refresh" content="2;url=/tazallom_mvp/public/requests/analyze.php?id=<?= (int)$id ?>">
</head>

<body style="min-height:100vh;display:flex;align-items:center;justify-content:center;">
<main style="width:100%;padding:24px;">

  <div class="glass card" style="max-width:720px;margin:0 auto;text-align:center;">
    <h2 style="margin:0 0 10px;">تم إرسال طلبك بنجاح ✅</h2>

    <p class="p" style="margin:0 auto 16px;max-width:520px;">
      جاري تحليل الطلب وإعداد التقرير… سيتم تحويلك تلقائيًا خلال ثانيتين.
    </p>

    <div class="btns" style="justify-content:center;margin-top:14px;">
      <a class="btn" href="/tazallom_mvp/public/dashboard/index.php">الرجوع للداشبوورد</a>
      <a class="btn primary" href="/tazallom_mvp/public/requests/view.php?id=<?= (int)$id ?>">عرض الطلب الآن</a>
    </div>

    <div style="margin-top:16px;color:rgba(203,184,154,.85);font-size:13px;">
      إذا ما تم التحويل تلقائيًا:
      <a class="small-link" href="/tazallom_mvp/public/requests/analyze.php?id=<?= (int)$id ?>">اضغطي هنا</a>
    </div>
  </div>

</main>
</body>
</html>
