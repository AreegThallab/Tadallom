<?php
require_once __DIR__ . "/../../includes/db.php";
require_once __DIR__ . "/../../includes/helpers.php";

if (session_status() === PHP_SESSION_NONE) session_start();

$errors = [];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $first  = trim($_POST["first_name"] ?? "");
  $second = trim($_POST["second_name"] ?? "");
  $last   = trim($_POST["last_name"] ?? "");
  $dob    = trim($_POST["dob"] ?? "");
  $nid    = trim($_POST["national_id"] ?? "");
  $gender = trim($_POST["gender"] ?? "");
  $phone  = trim($_POST["phone"] ?? "");
  $email  = trim($_POST["email"] ?? "");
  $pass   = $_POST["password"] ?? "";
  $pass2  = $_POST["password2"] ?? "";

  if ($first==="" || $second==="" || $last==="") $errors[]="رجاءً أدخلي الاسم الأول/الثاني/الأخير.";
  if ($dob==="") $errors[]="رجاءً أدخلي تاريخ الميلاد.";
  if ($nid==="" || !preg_match('/^[0-9]{10}$/', $nid)) $errors[]="رقم الهوية لازم يكون 10 أرقام.";
  if (!in_array($gender, ["M","F"], true)) $errors[]="اختاري الجنس.";
  if ($email==="" || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[]="البريد الإلكتروني غير صحيح.";
  if (strlen($pass) < 8) $errors[]="كلمة المرور لازم 8 أحرف أو أكثر.";
  if ($pass !== $pass2) $errors[]="كلمتا المرور غير متطابقتين.";

  if (!$errors) {
    try {
      $q = $pdo->prepare("SELECT id FROM users WHERE email=? OR national_id=? LIMIT 1");
      $q->execute([$email, $nid]);

      if ($q->fetch()) {
        $errors[]="الإيميل أو رقم الهوية مستخدم مسبقًا.";
      } else {
        $hash = password_hash($pass, PASSWORD_BCRYPT);

        $ins = $pdo->prepare("
          INSERT INTO users (first_name,second_name,last_name,dob,national_id,gender,phone,email,password_hash)
          VALUES (?,?,?,?,?,?,?,?,?)
        ");
        $ins->execute([$first,$second,$last,$dob,$nid,$gender,$phone,$email,$hash]);

        $user_id = (int)$pdo->lastInsertId();
        $_SESSION["pending_user_id"] = $user_id;

        header("Location: /tazallom_mvp/public/nafath/nafath_login.php?mode=register");
        exit;
      }
    } catch (Throwable $e) {
      $errors[]="صار خطأ أثناء التسجيل. جرّبي مرة ثانية.";
    }
  }
}
?>
<!doctype html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>إنشاء حساب - تظلُّم</title>
  <link rel="stylesheet" href="/tazallom_mvp/public/assets/styles.css" />
</head>
<body>

<!-- ✅ Corner logo (يرجع للصفحة الرئيسية) -->
<a class="auth-corner" href="/tazallom_mvp/public/index.php" aria-label="العودة للصفحة الرئيسية">
  <img src="/tazallom_mvp/public/assets/logo1.svg" alt="تظلُّم">
</a>

<!-- ✅ Centered layout -->
<main class="auth-center">

  <div class="auth-stack">

    <!-- ✅ Logo plain فوق الكارد (مو داخل كارد) -->
    <div class="auth-logo-plain">
      <img src="/tazallom_mvp/public/assets/logo1.svg" alt="تظلُّم">
    </div>

    <div class="glass card auth-card">
      <h2 class="auth-title">إنشاء حساب</h2>

      <?php if (!empty($errors)): ?>
        <div style="background:#3a1b1b;border:1px solid rgba(255,255,255,.08);padding:12px;border-radius:12px;margin-bottom:14px;">
          <?php foreach ($errors as $er): ?>
            <div style="color:#ffd2d2;line-height:1.7;">• <?= htmlspecialchars($er) ?></div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>

      <form method="post" class="form">
        <div class="grid" style="grid-template-columns:repeat(3,1fr);gap:12px;">
          <div class="field">
            <label>الاسم الأول</label>
            <input name="first_name" required value="<?= htmlspecialchars($_POST["first_name"] ?? "") ?>">
          </div>
          <div class="field">
            <label>الاسم الثاني</label>
            <input name="second_name" required value="<?= htmlspecialchars($_POST["second_name"] ?? "") ?>">
          </div>
          <div class="field">
            <label>الاسم الأخير</label>
            <input name="last_name" required value="<?= htmlspecialchars($_POST["last_name"] ?? "") ?>">
          </div>
        </div>

        <div class="grid" style="grid-template-columns:repeat(2,1fr);gap:12px;">
          <div class="field">
            <label>تاريخ الميلاد</label>
            <input type="date" name="dob" required value="<?= htmlspecialchars($_POST["dob"] ?? "") ?>">
          </div>

          <div class="field">
            <label>رقم الهوية</label>
            <input name="national_id" inputmode="numeric" maxlength="10" required value="<?= htmlspecialchars($_POST["national_id"] ?? "") ?>">
          </div>
        </div>

        <div class="grid" style="grid-template-columns:repeat(2,1fr);gap:12px;">
          <div class="field">
            <label>الجنس</label>
            <select name="gender" required>
              <option value="">اختاري</option>
              <option value="F" <?= (($_POST["gender"] ?? "")==="F")?"selected":"" ?>>أنثى</option>
              <option value="M" <?= (($_POST["gender"] ?? "")==="M")?"selected":"" ?>>ذكر</option>
            </select>
          </div>

          <div class="field">
            <label>رقم الجوال (اختياري)</label>
            <input name="phone" inputmode="tel" value="<?= htmlspecialchars($_POST["phone"] ?? "") ?>">
          </div>
        </div>

        <div class="grid" style="grid-template-columns:repeat(2,1fr);gap:12px;">
          <div class="field">
            <label>البريد الإلكتروني</label>
            <input type="email" name="email" required value="<?= htmlspecialchars($_POST["email"] ?? "") ?>">
          </div>

          <div class="field">
            <label>كلمة المرور</label>
            <input type="password" name="password" required>
          </div>
        </div>

        <div class="field">
          <label>تأكيد كلمة المرور</label>
          <input type="password" name="password2" required>
        </div>

        <button class="btn primary" type="submit" style="width:100%;">إنشاء حساب والمتابعة لنفاذ</button>

        <a class="small-link" href="/tazallom_mvp/public/auth/login.php">عندي حساب؟ تسجيل دخول</a>
      </form>

    </div>
  </div>

</main>

</body>
</html>
