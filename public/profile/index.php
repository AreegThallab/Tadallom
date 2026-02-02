<?php
require_once __DIR__ . "/../../includes/db.php";
require_once __DIR__ . "/../../includes/helpers.php";

if (session_status() === PHP_SESSION_NONE) session_start();

if (!($_SESSION["is_logged_in"] ?? false)) {
  header("Location: /tazallom_mvp/public/auth/login.php");
  exit;
}

$user_id = (int)($_SESSION["user_id"] ?? 0);
function h($s){ return htmlspecialchars((string)$s, ENT_QUOTES, "UTF-8"); }

// ===== جلب بيانات المستخدم =====
$st = $pdo->prepare("
  SELECT first_name, second_name, last_name, national_id, dob, gender, phone, email
  FROM users
  WHERE id = ?
  LIMIT 1
");
$st->execute([$user_id]);
$u = $st->fetch(PDO::FETCH_ASSOC);
if (!$u) { die("المستخدم غير موجود."); }

$fullName  = trim(($u["first_name"] ?? "")." ".($u["second_name"] ?? "")." ".($u["last_name"] ?? ""));
$genderTxt = ($u["gender"] ?? "") === "F" ? "أنثى" : ((($u["gender"] ?? "") === "M") ? "ذكر" : "—");

// فواصل عرض
function v($x){ return ($x === null || $x === "") ? "—" : $x; }
?>
<!doctype html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>الملف الشخصي - تظلُّم</title>
  <link rel="stylesheet" href="/tazallom_mvp/public/assets/styles.css" />
</head>
<body>

<?php require_once __DIR__ . "/../dashboard/dashboard_header.php"; ?>

<main class="container page-top">

  <!-- Header -->
  <div style="display:flex;justify-content:space-between;align-items:end;gap:12px;flex-wrap:wrap;margin-bottom:18px;">
    <div>
      <div style="font-size:28px;font-weight:900;">الملف الشخصي</div>
    </div>



  <!-- Single Card -->
  <section class="glass card" style="max-width:980px;margin:0 auto;">

    <div style="display:flex;justify-content:space-between;align-items:center;gap:12px;flex-wrap:wrap;margin-bottom:14px;">
      <h3 style="margin:0;">معلومات الحساب</h3>

      <div class="btns">
        <!-- زر شكل فقط -->
        <button class="btn" type="button" disabled
          style="opacity:1; cursor:not-allowed;">
          تعديل البيانات 
        </button>

        <a class="btn" href="/tazallom_mvp/public/auth/logout.php">تسجيل خروج</a>
      </div>
    </div>

    <div style="margin-bottom:14px;">
      <div style="font-weight:800;margin-bottom:10px;">البيانات الأساسية</div>

      <div class="summary-grid">
        <div class="summary-item">
          <span>الاسم</span>
          <b><?= h(v($fullName)) ?></b>
        </div>

        <div class="summary-item">
          <span>رقم الهوية</span>
          <b><?= h(v($u["national_id"] ?? "—")) ?></b>
        </div>

        <div class="summary-item">
          <span>تاريخ الميلاد</span>
          <b><?= h(v($u["dob"] ?? "—")) ?></b>
        </div>

        <div class="summary-item">
          <span>الجنس</span>
          <b><?= h($genderTxt) ?></b>
        </div>
      </div>

    </div>

    <div class="hr"></div>

    <div>
      <div style="font-weight:800;margin-bottom:10px;">معلومات التواصل</div>

      <div class="summary-grid">
        <div class="summary-item">
          <span>البريد الإلكتروني</span>
          <b><?= h(v($u["email"] ?? "—")) ?></b>
        </div>

        <div class="summary-item">
          <span>رقم الجوال</span>
          <b><?= h(v($u["phone"] ?? "—")) ?></b>
        </div>
      </div>
    </div>

  </section>

</main>

</body>
</html>
