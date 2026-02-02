<?php
require_once __DIR__ . "/../../includes/db.php";
require_once __DIR__ . "/../../includes/helpers.php";

if (session_status() === PHP_SESSION_NONE) session_start();

if (!($_SESSION["is_logged_in"] ?? false)) {
  header("Location: /tazallom_mvp/public/auth/login.php");
  exit;
}

$user_id = (int)($_SESSION["user_id"] ?? 0);

function postv($k, $d=""){ return trim($_POST[$k] ?? $d); }
function make_request_number(){
  return "TZ-" . date("Ymd") . "-" . random_int(1000, 9999);
}

$entities = $pdo->query("SELECT id, name FROM government_entities ORDER BY name ASC")
  ->fetchAll(PDO::FETCH_ASSOC);

$errors = [];
$val = [
  "title" => "",
  "entity_id" => "",
  "decision_number" => "",
  "decision_date" => "",
  "decision_notice_date" => "",
  "decision_source" => "",
  "decision_type" => "",
  "complaint_text" => "",
];

if ($_SERVER["REQUEST_METHOD"] === "POST") {

  foreach($val as $k=>$v) $val[$k] = postv($k, $v);

  $title               = $val["title"];
  $entity_id           = (int)$val["entity_id"];
  $decision_number     = $val["decision_number"];
  $decision_date       = $val["decision_date"];
  $decision_notice_date= $val["decision_notice_date"];
  $decision_source     = $val["decision_source"];
  $decision_type       = $val["decision_type"];
  $complaint_text      = $val["complaint_text"];

  // ✅ كل شيء إجباري
  if ($title === "") $errors[] = "رجاءً أدخلي عنوان الطلب.";
  if ($entity_id <= 0) $errors[] = "رجاءً اختاري الجهة الحكومية.";
  if ($decision_number === "") $errors[] = "رجاءً أدخلي رقم القرار.";
  if ($decision_date === "") $errors[] = "رجاءً أدخلي تاريخ القرار.";
  if ($decision_notice_date === "") $errors[] = "رجاءً أدخلي تاريخ العلم بالقرار.";
  if ($decision_source === "") $errors[] = "رجاءً أدخلي مصدر القرار.";
  if ($decision_type === "") $errors[] = "رجاءً أدخلي نوع القرار.";
  if ($complaint_text === "") $errors[] = "رجاءً اكتبي موضوع التظلّم.";

  // ✅ تحقق التواريخ
  if (!$errors) {
    $today = new DateTime("today");
    $dd = DateTime::createFromFormat("Y-m-d", $decision_date);
    $nd = DateTime::createFromFormat("Y-m-d", $decision_notice_date);

    if (!$dd) $errors[] = "تاريخ القرار غير صحيح.";
    if (!$nd) $errors[] = "تاريخ العلم بالقرار غير صحيح.";

    if ($dd && $dd > $today) $errors[] = "تاريخ القرار لا يمكن يكون في المستقبل.";
    if ($nd && $nd > $today) $errors[] = "تاريخ العلم بالقرار لا يمكن يكون في المستقبل.";
    if ($dd && $nd && $nd < $dd) $errors[] = "تاريخ العلم بالقرار لازم يكون بعد أو مساوي لتاريخ القرار.";
  }

  // ✅ جلب اسم الجهة
  $entity = null;
  foreach ($entities as $e) {
    if ((int)$e["id"] === $entity_id) { $entity = $e; break; }
  }
  if (!$entity) $errors[] = "الجهة المختارة غير موجودة.";

  if (!$errors) {
    try {
      $reqNo = make_request_number();

      $ins = $pdo->prepare("
        INSERT INTO requests
          (user_id, request_number, title, complaint_text,
           entity_id, entity_name,
           decision_number, decision_date, decision_notice_date, decision_source, decision_type,
           status, risk_level)
        VALUES
          (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'قيد المراجعة', 'متوسط')
      ");

      $ins->execute([
        $user_id,
        $reqNo,
        $title,
        $complaint_text,
        $entity_id,
        $entity["name"],
        $decision_number,
        $decision_date,
        $decision_notice_date,
        $decision_source,
        $decision_type
      ]);

      $new_id = (int)$pdo->lastInsertId();

      // ✅ نروح لنجاح -> ومنها تحليل تلقائي
      header("Location: /tazallom_mvp/public/requests/success.php?id=".$new_id);
      exit;

    } catch (Throwable $e){
      $errors[] = "صار خطأ أثناء حفظ الطلب. تأكدي من قاعدة البيانات.";
    }
  }
}
?>
<!doctype html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>رفع طلب جديد - تظلُّم</title>
  <link rel="stylesheet" href="/tazallom_mvp/public/assets/styles.css" />
</head>

<body style="min-height:100vh;display:flex;align-items:center;justify-content:center;">
<main style="width:100%;padding:24px;">

  <div style="max-width:980px;margin:0 auto 14px;">
    <a class="btn" href="/tazallom_mvp/public/requests/index.php">← الرجوع للطلبات</a>
  </div>

  <div class="glass card" style="max-width:980px;margin:0 auto;">
    <h2 style="margin:0 0 8px;">رفع طلب جديد</h2>
    <p class="p" style="margin:0 0 18px;">
      ادخل تفاصيل القرار وموضوع التظلُّم  .
    </p>

    <?php if ($errors): ?>
      <div style="background:#3a1b1b;border:1px solid rgba(255,255,255,.08);padding:12px;border-radius:12px;margin-bottom:14px;">
        <?php foreach($errors as $er): ?>
          <div style="color:#ffd2d2;">• <?= htmlspecialchars($er) ?></div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>

    <form method="post" class="form">

      <div class="field">
        <label>عنوان الطلب</label>
        <input name="title" value="<?= htmlspecialchars($val["title"]) ?>" placeholder="مثال: طعن على قرار إداري" required>
      </div>

      <div class="grid" style="grid-template-columns:repeat(2,1fr);gap:12px;">
        <div class="field">
          <label>الجهة الإدارية</label>
          <select name="entity_id" required>
            <option value="">اختر الجهة</option>
            <?php foreach($entities as $e): ?>
              <option value="<?= (int)$e["id"] ?>" <?= ((string)$val["entity_id"] === (string)$e["id"]) ? "selected" : "" ?>>
                <?= htmlspecialchars($e["name"]) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="field">
          <label>نوع القرار</label>
          <input name="decision_type" value="<?= htmlspecialchars($val["decision_type"]) ?>" placeholder="مثال: إنهاء خدمة / مخالفة / خصم ..." required>
        </div>
      </div>

      <div class="grid" style="grid-template-columns:repeat(3,1fr);gap:12px;">
        <div class="field">
          <label>رقم القرار</label>
          <input name="decision_number" value="<?= htmlspecialchars($val["decision_number"]) ?>" placeholder="مثال: 12345" required>
        </div>

        <div class="field">
          <label>تاريخ القرار</label>
          <input type="date" name="decision_date" value="<?= htmlspecialchars($val["decision_date"]) ?>" required>
        </div>

        <div class="field">
          <label>تاريخ العلم بالقرار</label>
          <input type="date" name="decision_notice_date" value="<?= htmlspecialchars($val["decision_notice_date"]) ?>" required>
        </div>
      </div>

      <div class="field">
        <label>مصدر القرار</label>
        <input name="decision_source" value="<?= htmlspecialchars($val["decision_source"]) ?>" placeholder="مثال: إدارة الموارد البشرية" required>
      </div>

      <div class="field">
        <label>موضوع التظلّم</label>
        <textarea name="complaint_text" rows="7" required placeholder="اشرح القرار ولماذا تعترض عليه..."><?= htmlspecialchars($val["complaint_text"]) ?></textarea>
        <small class="help">هذا النص سيتم تحليله لتحديد التصنيف ومدة التظلّم وإصدار التقرير.</small>
      </div>

      <button class="btn primary" type="submit" style="width:100%;">تقديم الطلب</button>
    </form>

  </div>

</main>
</body>
</html>
