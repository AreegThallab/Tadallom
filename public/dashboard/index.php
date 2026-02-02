<?php
require_once __DIR__ . "/../../includes/helpers.php";
require_once __DIR__ . "/../../includes/db.php";

if (session_status() === PHP_SESSION_NONE) session_start();

if (!($_SESSION["is_logged_in"] ?? false)) {
  header("Location: /tazallom_mvp/public/auth/login.php");
  exit;
}

$user_id = (int)($_SESSION["user_id"] ?? 0);

function h($s){ return htmlspecialchars((string)$s, ENT_QUOTES, "UTF-8"); }

/* ===============================
   1) إحصائيات من DB
================================ */

// إجمالي الطلبات
$st = $pdo->prepare("SELECT COUNT(*) FROM requests WHERE user_id=?");
$st->execute([$user_id]);
$active = (int)$st->fetchColumn();

// بانتظار الرد
$st = $pdo->prepare("SELECT COUNT(*) FROM requests WHERE user_id=? AND status='بانتظار الرد'");
$st->execute([$user_id]);
$pending = (int)$st->fetchColumn();

// مكتمل / اكتمل شكلياً
$st = $pdo->prepare("SELECT COUNT(*) FROM requests WHERE user_id=? AND status IN ('مكتمل','اكتمل شكليًا')");
$st->execute([$user_id]);
$done = (int)$st->fetchColumn();

// أحدث الطلبات (آخر 6)
$st = $pdo->prepare("
  SELECT id, request_number, title, status, risk_level, deadline_date, created_at
  FROM requests
  WHERE user_id=?
  ORDER BY id DESC
  LIMIT 6
");
$st->execute([$user_id]);
$recent_requests = $st->fetchAll(PDO::FETCH_ASSOC);

/* ===============================
   2) تقويم: مواعيد المهلة (deadline_date)
   نعرض نقاط على الأيام اللي فيها deadlines
================================ */

$year  = (int)date("Y");
$month = (int)date("n");

$first = new DateTime("$year-$month-01");
$daysInMonth = (int)$first->format("t");
$startDow = (int)$first->format("w"); // 0=Sun

// اجلب deadlines داخل الشهر الحالي فقط
$monthStart = (new DateTime("$year-$month-01"))->format("Y-m-d");
$monthEnd   = (new DateTime("$year-$month-$daysInMonth"))->format("Y-m-d");

$st = $pdo->prepare("
  SELECT id, title, deadline_date, status
  FROM requests
  WHERE user_id=? AND deadline_date IS NOT NULL
    AND deadline_date BETWEEN ? AND ?
  ORDER BY deadline_date ASC
");
$st->execute([$user_id, $monthStart, $monthEnd]);
$deadlines = $st->fetchAll(PDO::FETCH_ASSOC);

// خريطة: يوم الشهر => [items...]
$deadlineMap = [];
foreach ($deadlines as $d) {
  $day = (int)date("j", strtotime($d["deadline_date"]));
  if (!isset($deadlineMap[$day])) $deadlineMap[$day] = [];
  $deadlineMap[$day][] = $d;
}

// بناء خلايا التقويم
$cells = [];
for ($i=0; $i<$startDow; $i++) $cells[] = null;
for ($d=1; $d<=$daysInMonth; $d++) $cells[] = $d;
while (count($cells)%7!==0) $cells[] = null;

$weekDays = ["الأحد","الاثنين","الثلاثاء","الأربعاء","الخميس","الجمعة","السبت"];
$todayDay = (int)date("j");
$todayYM  = date("Y-m");

/* ===============================
   helpers للحالة
================================ */
function status_class($s){
  if ($s === "مكتمل" || $s === "اكتمل شكليًا") return "ok";
  if ($s === "بانتظار الرد") return "warn";
  return "mid"; // قيد المراجعة وغيره
}
?>
<!doctype html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>لوحة التحكم - تظلُّم</title>
  <link rel="stylesheet" href="/tazallom_mvp/public/assets/styles.css" />
</head>
<body>

<?php require_once __DIR__ . "/dashboard_header.php"; ?>

<main class="container page-top dashboard">

  <!-- Top bar -->
  <div class="dash-top">
    <div class="dash-title">
      <h1>لوحة التحكم</h1>
      <p>نظرة سريعة على طلباتك ومواعيد المهلة.</p>
    </div>

    <div class="dash-actions">
      <a class="btn primary" href="/tazallom_mvp/public/requests/create.php">رفع طلب جديد</a>
      <a class="btn" href="/tazallom_mvp/public/requests/index.php">الطلبات</a>
    </div>
  </div>

  <!-- KPI cards -->
  <section class="dash-kpis">
    <div class="kpi-card">
      <div class="kpi-label">إجمالي الطلبات</div>
      <div class="kpi-value"><?= (int)$active ?></div>
      <div class="kpi-sub">كل طلباتك</div>
    </div>

    <div class="kpi-card">
      <div class="kpi-label">بانتظار الرد</div>
      <div class="kpi-value"><?= (int)$pending ?></div>
      <div class="kpi-sub">داخل المهلة</div>
    </div>

    <div class="kpi-card">
      <div class="kpi-label">مكتملة</div>
      <div class="kpi-value"><?= (int)$done ?></div>
      <div class="kpi-sub">تم إصدار التقرير</div>
    </div>

    <div class="kpi-card">
      <div class="kpi-label">مواعيد هذا الشهر</div>
      <div class="kpi-value"><?= (int)count($deadlines) ?></div>
      <div class="kpi-sub">نهايات المهلة</div>
    </div>
  </section>

  <?php if ($active === 0): ?>
    <!-- Empty state -->
    <div class="glass card" style="margin-top:18px; text-align:center;">
      <h2 style="margin:0 0 8px;">لا يوجد طلبات</h2>
      <p class="p" style="margin:0 0 14px; max-width:720px;">
        ابدأ برفع أول طلب تظلُّم .
      </p>
      <a class="btn primary" href="/tazallom_mvp/public/requests/create.php">رفع طلب جديد</a>
    </div>
  <?php else: ?>

    <!-- Main grid -->
    <section class="dash-grid">

      <!-- Calendar + deadlines list -->
      <div class="dash-panel">
        <div class="panel-head">
          <h2>المواعيد (نهاية المهلة)</h2>
          <span class="pill-lite"><?= date("F Y") ?></span>
        </div>

        <div class="calendar">
          <div class="cal-head">
            <button class="cal-nav" type="button" disabled>›</button>
            <div class="cal-month"><?= date("F Y") ?></div>
            <button class="cal-nav" type="button" disabled>‹</button>
          </div>

          <div class="cal-week">
            <?php foreach($weekDays as $wd): ?>
              <span><?= h($wd) ?></span>
            <?php endforeach; ?>
          </div>

          <div class="cal-days">
            <?php foreach($cells as $c): ?>
              <?php if ($c === null): ?>
                <div class="day empty"></div>
              <?php else:
                $has = isset($deadlineMap[$c]);
                $isToday = ($c === $todayDay);
                $cls = "day" . ($isToday ? " today" : "") . ($has ? " has" : "");
              ?>
                <div class="<?= $cls ?>">
                  <span class="num"><?= (int)$c ?></span>
                  <?php if ($has): ?>
                    <span class="dot" title="يوجد موعد مهلة"></span>
                  <?php endif; ?>
                </div>
              <?php endif; ?>
            <?php endforeach; ?>
          </div>
        </div>

        <?php if (!$deadlines): ?>
          <div class="dash-note" style="margin-top:12px;">
            لا توجد مواعيد مهلة هذا الشهر.
          </div>
        <?php else: ?>
          <div class="deadlines">
            <?php foreach($deadlines as $d): ?>
              <a class="deadline-item" href="/tazallom_mvp/public/requests/view.php?id=<?= (int)$d["id"] ?>">
                <span class="deadline-date"><?= h($d["deadline_date"]) ?></span>
                <span class="deadline-title"><?= h($d["title"]) ?></span>
                <span class="deadline-status <?= h(status_class($d["status"])) ?>"><?= h($d["status"]) ?></span>
              </a>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>
      </div>

      <!-- Recent requests -->
      <div class="dash-panel">
        <div class="panel-head">
          <h2>الطلبات الأخيرة</h2>
          <a class="small-link" href="/tazallom_mvp/public/requests/index.php">عرض الكل</a>
        </div>

        <div class="req-list">
          <?php foreach($recent_requests as $r): ?>
            <div class="req-item">
              <div class="req-main">
                <div class="req-title"><?= h($r["title"]) ?></div>
                <div class="req-sub">
                  <span class="status <?= h(status_class($r["status"])) ?>"><?= h($r["status"]) ?></span>
                  <span class="muted">
                    <?= h($r["request_number"]) ?>
                    <?php if (!empty($r["deadline_date"])): ?>
                      • المهلة: <?= h($r["deadline_date"]) ?>
                    <?php endif; ?>
                  </span>
                </div>
              </div>
              <a class="btn" href="/tazallom_mvp/public/requests/view.php?id=<?= (int)$r["id"] ?>">عرض</a>
            </div>
          <?php endforeach; ?>
        </div>

        <div class="dash-note">
          <b>تنبيه:</b> لو انتهت المهلة ولم يردّوا، قد يصبح الطلب <span class="accent">“اكتمل شكليًا”</span>.
        </div>
      </div>

    </section>

  <?php endif; ?>

</main>

<?php require_once __DIR__ . "/../../includes/layout_footer.php"; ?>
<script src="/tazallom_mvp/public/assets/app.js"></script>
</body>
</html>
