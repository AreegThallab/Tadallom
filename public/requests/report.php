<?php
require_once __DIR__ . "/../../includes/db.php";
require_once __DIR__ . "/../../includes/helpers.php";

if (session_status() === PHP_SESSION_NONE) session_start();
if (!($_SESSION["is_logged_in"] ?? false)) {
  header("Location: /tazallom_mvp/public/auth/login.php");
  exit;
}

$user_id = (int)$_SESSION["user_id"];
$id = (int)($_GET["id"] ?? 0);

$stmt = $pdo->prepare("
  SELECT request_number, title, entity_name, decision_number, decision_date, decision_source, decision_type,
         complaint_text, grievance_days, deadline_date, status, risk_level, report_text
  FROM requests
  WHERE id=? AND user_id=?
  LIMIT 1
");
$stmt->execute([$id, $user_id]);
$r = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$r) die("طلب غير موجود.");

$report = trim((string)($r["report_text"] ?? ""));
if ($report === "") {
  // في حال لسه ما انولد تقرير — نحط نسخة بسيطة (محاكاة)
  $report = "تقرير (محاكاة)\n\n".
            "رقم الطلب: ".$r["request_number"]."\n".
            "العنوان: ".$r["title"]."\n".
            "الجهة: ".$r["entity_name"]."\n".
            "الحالة: ".$r["status"]."\n".
            "الخطر: ".$r["risk_level"]."\n";
}
?>
<!doctype html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>تقرير الطلب - <?= htmlspecialchars($r["request_number"]) ?></title>
  <style>
    body{font-family:Tahoma,Arial; direction:rtl; margin:24px; color:#111;}
    .box{max-width:900px;margin:0 auto;}
    h1{margin:0 0 10px;font-size:20px}
    .meta{color:#444; margin-bottom:18px; line-height:1.8}
    pre{white-space:pre-wrap; line-height:1.9; font-size:14px; background:#f6f6f6; padding:14px; border-radius:12px}
    @media print{
      body{margin:0}
      .no-print{display:none}
      pre{background:#fff}
    }
  </style>
</head>
<body>
  <div class="box">
    <div class="no-print" style="display:flex;justify-content:space-between;gap:10px;align-items:center;margin-bottom:16px;">
      <button onclick="window.print()" style="padding:10px 14px;border-radius:10px;border:1px solid #ccc;background:#fff;cursor:pointer;">طباعة / حفظ PDF</button>
      <a href="/tazallom_mvp/public/requests/view.php?id=<?= (int)$id ?>" style="text-decoration:none;color:#111;">رجوع للطلب</a>
    </div>

    <h1>التقرير الإجرائي — <?= htmlspecialchars($r["request_number"]) ?></h1>
    <div class="meta">
      <b>العنوان:</b> <?= htmlspecialchars($r["title"]) ?><br>
      <b>الجهة:</b> <?= htmlspecialchars($r["entity_name"]) ?><br>
      <b>الحالة:</b> <?= htmlspecialchars($r["status"]) ?> — <b>الخطر:</b> <?= htmlspecialchars($r["risk_level"]) ?>
    </div>

    <pre><?= htmlspecialchars($report) ?></pre>
  </div>

  <script>
    // تفتح نافذة الطباعة مباشرة (اختياري)
    // window.print();
  </script>
</body>
</html>
