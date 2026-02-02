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

$stmt = $pdo->prepare("
  SELECT id, request_number, title, entity_name, status, risk_level, deadline_date, created_at
  FROM requests
  WHERE user_id = ?
  ORDER BY id DESC
");
$stmt->execute([$user_id]);
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!doctype html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>الطلبات - تظلُّم</title>
  <link rel="stylesheet" href="/tazallom_mvp/public/assets/styles.css" />
</head>
<body>

<?php require_once __DIR__ . "/../dashboard/dashboard_header.php"; ?>

<main class="container page-top">

  <div class="glass card" style="display:flex;justify-content:space-between;align-items:center;gap:12px;flex-wrap:wrap;">
    <div>
      <h2 style="margin:0 0 6px;">الطلبات</h2>
      <p class="p" style="margin:0;">كل طلباتك في مكان واحد.</p>
    </div>
    <a class="btn primary" href="/tazallom_mvp/public/requests/create.php">رفع طلب جديد</a>
  </div>

  <div class="glass card" style="margin-top:18px;">
    <?php if (!$rows): ?>
      <p class="p" style="margin:0;">ما عندك طلبات حالياً. ابدئي بـ “رفع طلب جديد”.</p>
    <?php else: ?>
      <div style="display:grid;gap:12px;">
        <?php foreach($rows as $r): ?>
          <div style="border:1px solid rgba(203,184,154,.14);background:rgba(255,255,255,.02);border-radius:16px;padding:14px;display:flex;justify-content:space-between;gap:12px;flex-wrap:wrap;align-items:center;">
            <div style="min-width:260px;">
              <div style="font-weight:900"><?= h($r["title"]) ?></div>
              <div style="color:rgba(203,184,154,.85);font-size:12px;margin-top:4px;">
                <?= h($r["request_number"]) ?> • <?= h($r["entity_name"]) ?> • <?= h($r["created_at"]) ?>
              </div>
            </div>

            <div style="display:flex;gap:8px;flex-wrap:wrap;align-items:center;">
              <span class="pill" style="font-size:12px;padding:6px 10px;"><?= h($r["status"]) ?></span>
              <span class="pill" style="font-size:12px;padding:6px 10px;opacity:.9;">الخطر: <?= h($r["risk_level"]) ?></span>
              <a class="btn" href="/tazallom_mvp/public/requests/view.php?id=<?= (int)$r["id"] ?>">عرض</a>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </div>

</main>
</body>
</html>
