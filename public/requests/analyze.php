<?php
require_once __DIR__ . "/../../includes/db.php";
require_once __DIR__ . "/../../includes/helpers.php";

if (session_status() === PHP_SESSION_NONE) session_start();

if (!($_SESSION["is_logged_in"] ?? false)) {
  header("Location: /tazallom_mvp/public/auth/login.php");
  exit;
}

$user_id = (int)($_SESSION["user_id"] ?? 0);
$id      = (int)($_GET["id"] ?? 0);

if ($id <= 0) {
  die("طلب غير صالح.");
}

require_once __DIR__ . "/_analyze_helpers.php";

try {
  // ✅ تحليل + تحديث الطلب
  analyze_and_update_request($pdo, $id, $user_id);

  // ✅ بعد التحليل يروح مباشرة لصفحة العرض
  header("Location: /tazallom_mvp/public/requests/view.php?id=" . $id);
  exit;

} catch (Throwable $e) {
  die("صار خطأ أثناء التحليل: " . htmlspecialchars($e->getMessage(), ENT_QUOTES, "UTF-8"));
}
