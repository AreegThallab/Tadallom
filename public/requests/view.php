<?php
require_once __DIR__ . "/../../includes/db.php";
require_once __DIR__ . "/../../includes/helpers.php";

if (session_status() === PHP_SESSION_NONE) session_start();
if (!($_SESSION["is_logged_in"] ?? false)) {
  header("Location: /tazallom_mvp/public/auth/login.php");
  exit;
}

$user_id = (int)($_SESSION["user_id"] ?? 0);
$id = (int)($_GET["id"] ?? 0);
if ($id <= 0) die("طلب غير صالح");

$stmt = $pdo->prepare("SELECT * FROM requests WHERE id=? AND user_id=? LIMIT 1");
$stmt->execute([$id, $user_id]);
$request = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$request) die("الطلب غير موجود");

function h($s){ return htmlspecialchars((string)$s, ENT_QUOTES, "UTF-8"); }

// ===== حساب المتبقي (إذا فيه نهاية مهلة) =====
$days_left = null;
if (!empty($request["deadline_date"])) {
  $today    = new DateTime(date("Y-m-d"));
  $deadline = new DateTime($request["deadline_date"]);
  $diffDays = (int)$today->diff($deadline)->format("%r%a");
  $days_left = $diffDays;
}

$status = $request["status"] ?? "—";
$risk   = $request["risk_level"] ?? "—";

// نص "المتبقي" بشكل أجمل
$leftText = "—";
if ($days_left !== null) {
  if ($days_left > 0) $leftText = $days_left . " يوم";
  elseif ($days_left === 0) $leftText = "اليوم آخر يوم";
  else $leftText = "منتهية";
}
?>
<!doctype html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title><?= h($request["title"] ?? "عرض الطلب") ?> - تظلُّم</title>
  <link rel="stylesheet" href="/tazallom_mvp/public/assets/styles.css" />
</head>
<body>

<main class="container page-top">

  <!-- ✅ Top actions (Spacing مضبوط) -->
  <div class="view-top-actions" style="display:flex;justify-content:space-between;align-items:center;gap:12px;flex-wrap:wrap;margin-bottom:16px;">
    <a class="btn" href="/tazallom_mvp/public/requests/index.php">← رجوع للطلبات</a>

    <!-- (اختياري) زر PDF فقط -->
    <a class="btn primary" href="/tazallom_mvp/public/requests/report_pdf.php?id=<?= (int)$id ?>">
      تحميل PDF
    </a>
  </div>

  <!-- ✅ 1) شريط الحالة -->
  <section class="glass card status-bar">
    <div class="status-left">
      <h1 class="page-title"><?= h($request["title"] ?? "طلب") ?></h1>
      <div class="page-sub">
        رقم الطلب: <b><?= h($request["request_number"] ?? "—") ?></b>
      </div>
    </div>

    <div class="status-pills">
      <div class="stat-pill">
        <span class="pill-label">الحالة</span>
        <b class="pill-value"><?= h($status) ?></b>
      </div>

      <div class="stat-pill">
        <span class="pill-label">الخطر</span>
        <b class="pill-value"><?= h($risk) ?></b>
      </div>

      <div class="stat-pill">
        <span class="pill-label">المتبقي</span>
        <b class="pill-value"><?= h($leftText) ?></b>
      </div>
    </div>
  </section>

  <!-- ✅ 2) كروت المحتوى -->
  <section class="grid-2">

    <!-- ملخص -->
    <div class="glass card summary-card">
      <h3 class="card-title">ملخص الطلب</h3>

      <div class="summary-grid">
        <div class="summary-item">
          <span>الجهة</span>
          <b><?= h($request["entity_name"] ?? "—") ?></b>
        </div>

        <div class="summary-item">
          <span>تصنيف/فئة</span>
          <b><?= h($request["category"] ?? "—") ?></b>
        </div>

        <div class="summary-item">
          <span>نوع القرار</span>
          <b><?= h($request["decision_type"] ?? "—") ?></b>
        </div>

        <div class="summary-item">
          <span>رقم القرار</span>
          <b><?= h($request["decision_number"] ?? "—") ?></b>
        </div>

        <div class="summary-item">
          <span>تاريخ القرار</span>
          <b><?= h($request["decision_date"] ?? "—") ?></b>
        </div>

        <div class="summary-item">
          <span>مدة التظلّم</span>
          <b><?= h($request["grievance_days"] ?? "—") ?> يوم</b>
        </div>

        <div class="summary-item">
          <span>نهاية المهلة</span>
          <b><?= h($request["deadline_date"] ?? "—") ?></b>
        </div>

        <div class="summary-item">
          <span>تاريخ إنشاء الطلب</span>
          <b><?= h($request["created_at"] ?? "—") ?></b>
        </div>
      </div>
    </div>

    <!-- موضوع التظلم -->
    <div class="glass card complaint-card">
      <div class="accordion open">
        <button class="acc-head" type="button" data-acc>
          <span>موضوع التظلّم</span>
          <span class="acc-icon">▾</span>
        </button>

        <div class="acc-body">
          <p class="complaint-text"><?= nl2br(h($request["complaint_text"] ?? "—")) ?></p>
        </div>
      </div>
    </div>

  </section>

  <!-- ✅ 4) التقرير (مسافة أكبر قبل الكارد) -->
  <section class="glass card report-card" style="margin-top:18px;">

    <div class="report-top">
      <h3 class="card-title">التقرير الإجرائي الذكي</h3>

      <!-- ✅ الأزرار يسار -->
      <div class="report-actions">
        <?php if (!empty($request["report_text"])): ?>
          <button class="btn" type="button" data-copy="#report_full">نسخ التقرير</button>
        <?php endif; ?>

        <a class="btn primary" href="/tazallom_mvp/public/requests/report_pdf.php?id=<?= (int)$id ?>">
          تحميل PDF
        </a>
      </div>
    </div>

    <!-- Tabs -->
    <div class="tabs" data-tabs>
      <div class="tab-bar" role="tablist">
        <button class="tab active" type="button" role="tab" data-tab="result">النتيجة</button>
        <button class="tab" type="button" role="tab" data-tab="risk">الخطر</button>
        <button class="tab" type="button" role="tab" data-tab="next">الخطوة الجاية</button>
      </div>

      <div class="tab-panels">
        <div class="panel show" data-panel="result">
          <p class="panel-title">الحالة الإجرائية</p>
          <p class="panel-text"><?= h($status) ?></p>
          <p class="panel-note">
            <?= !empty($request["deadline_date"])
              ? "تاريخ نهاية المهلة: " . h($request["deadline_date"])
              : "—"; ?>
          </p>
        </div>

        <div class="panel" data-panel="risk">
          <p class="panel-title">مستوى الخطر</p>
          <p class="panel-text"><?= h($risk) ?></p>
          <p class="panel-note">
            <?= ($risk !== "منخفض")
              ? "تنبيه: قد يوجد خطر رفض شكلي في حال رفع الدعوى قبل اكتمال المدة."
              : "التوصية: الإجراءات مكتملة شكليًا ويمكن التقدّم بالإجراء."; ?>
          </p>
        </div>

        <div class="panel" data-panel="next">
          <p class="panel-title">ماذا أفعل الآن؟</p>
          <ul class="next-list">
            <li>تابعي رد الجهة من داخل الطلب.</li>
            <li>إذا انتهت المهلة بدون رد، يتحول الطلب إلى “اكتمل شكليًا”.</li>
            <li>حمّلي التقرير واحتفظي به للخطوات القادمة.</li>
          </ul>
        </div>
      </div>
    </div>

    <!-- النص الكامل (للنسخ) -->
    <textarea id="report_full" class="sr-only"><?= h($request["report_text"] ?? "") ?></textarea>

    <?php if (empty($request["report_text"])): ?>
      <div style="margin-top:12px;color:rgba(203,184,154,.85);line-height:1.8;">
        التقرير غير جاهز حالياً. (إذا تبغينه يولّد تلقائيًا بعد الإرسال: خلّي success.php يحوّل لـ analyze.php ثم يرجع هنا)
      </div>
    <?php endif; ?>
  </section>

</main>

<script src="/tazallom_mvp/public/assets/app.js"></script>
</body>
</html>
