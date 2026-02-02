<?php
require_once __DIR__ . "/../../includes/db.php";
require_once __DIR__ . "/../../includes/helpers.php";

if (session_status() === PHP_SESSION_NONE) session_start();

/**
 * mode:
 * - register: جاي من تسجيل حساب جديد (pending_user_id موجود)
 * - login: دخول عبر نفاذ (يسمح بالهوية لو المستخدم موجود في DB)
 */
$mode = $_GET["mode"] ?? ($_SESSION["nafath_mode"] ?? "register");
$_SESSION["nafath_mode"] = $mode;

$error = $_SESSION["nafath_login_error"] ?? "";
unset($_SESSION["nafath_login_error"]);

$entered_national_id = $_SESSION["nafath_entered_nid"] ?? "";
unset($_SESSION["nafath_entered_nid"]);

if ($_SERVER["REQUEST_METHOD"] === "POST") {

  if (isset($_POST["cancel"])) {
    // رجوع مناسب حسب الحالة
    if ($mode === "login") {
      header("Location: /tazallom_mvp/public/auth/login.php");
    } else {
      header("Location: /tazallom_mvp/public/auth/register.php");
    }
    exit;
  }

  $entered_national_id = trim($_POST["national_id"] ?? "");

  if ($entered_national_id === "") {
    $error = "الرجاء إدخال رقم الهوية.";
  } elseif (!preg_match('/^[0-9]{10}$/', $entered_national_id)) {
    $error = "رقم الهوية لازم يكون 10 أرقام.";
  } else {

    if ($mode === "register") {
      $pendingId = $_SESSION["pending_user_id"] ?? null;
      if (!$pendingId) {
        $error = "جلسة التسجيل غير موجودة. رجاءً أعيدي التسجيل.";
      } else {
        $st = $pdo->prepare("SELECT national_id FROM users WHERE id=?");
        $st->execute([$pendingId]);
        $u = $st->fetch(PDO::FETCH_ASSOC);

        if (!$u) $error = "تعذر العثور على المستخدم.";
        elseif ($u["national_id"] !== $entered_national_id) $error = "رقم الهوية لا يطابق بيانات التسجيل.";
      }

    } else { // mode === login
      $st = $pdo->prepare("SELECT id, is_verified FROM users WHERE national_id=? LIMIT 1");
      $st->execute([$entered_national_id]);
      $u = $st->fetch(PDO::FETCH_ASSOC);

      if (!$u) $error = "لا يوجد حساب بهذا الرقم. أنشئي حساب أولاً.";
      else {
        $_SESSION["pending_user_id"] = (int)$u["id"]; // نستخدمها لباقي الخطوات
      }
    }

    if (!$error) {
      $code = random_int(10, 99);
      $_SESSION["nafath_code"] = $code;
      $_SESSION["nafath_code_started_at"] = time();
      $_SESSION["nafath_entered_nid"] = $entered_national_id;

      header("Location: /tazallom_mvp/public/nafath/nafath_code.php");
      exit;
    }
  }

  $_SESSION["nafath_login_error"] = $error;
  $_SESSION["nafath_entered_nid"] = $entered_national_id;
  header("Location: /tazallom_mvp/public/nafath/nafath_login.php?mode=" . urlencode($mode));
  exit;
}
?>

<!doctype html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>التحقق عبر نفاذ - تظلُّم</title>
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
    <p class="nafath-sub">مرحباً بك، لإتمام العملية يرجى تسجيل الدخول عبر نفاذ.</p>

    <div class="nafath-card">
      <h2>الدخول عبر تطبيق نفاذ</h2>

      <?php if (!empty($error)): ?>
        <div class="nafath-error"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></div>
      <?php endif; ?>

      <form method="POST">
        <label class="nafath-label" for="national_id">رقم الهوية <span style="color:#e53935">*</span></label>
        <input class="nafath-input" type="text" id="national_id" name="national_id"
               value="<?= htmlspecialchars($entered_national_id, ENT_QUOTES, 'UTF-8') ?>" maxlength="10">

        <button class="nafath-btn" type="submit">تسجيل الدخول</button>

        <div class="nafath-or">أو باستخدام</div>

        <button class="nafath-disabled" type="button" disabled>اسم المستخدم وكلمة المرور</button>

        <button class="nafath-btn secondary" type="submit" name="cancel">إلغاء</button>
      </form>
    </div>

    <div class="nafath-footer-links">
      <span>عن نفاذ</span> · <span>الدعم الفني</span> · <span>سياسة الخصوصية</span> · <span>الشروط والأحكام</span>
    </div>

  </div>
</main>

<?php require_once __DIR__ . "/../../includes/layout_footer.php"; ?>
</body>
</html>
