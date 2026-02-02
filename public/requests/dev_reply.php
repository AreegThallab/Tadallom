<?php
require_once __DIR__ . "/../../includes/db.php";
require_once __DIR__ . "/../../includes/helpers.php";
require_once __DIR__ . "/_status_update.php";

if (session_status() === PHP_SESSION_NONE) session_start();
if (!($_SESSION["is_logged_in"] ?? false)) { die("Unauthorized"); }

$user_id = (int)$_SESSION["user_id"];
$id = (int)($_POST["id"] ?? 0);

$response_type = $_POST["response_type"] ?? "لا يوجد";
$response_text = trim($_POST["response_text"] ?? "");

if ($id <= 0) die("Bad request");
if ($response_type === "لا يوجد") die("اختاري موضوعي أو شكلي في وضع الاختبار");

$up = $pdo->prepare("
  UPDATE requests
  SET admin_response=1,
      response_type=?,
      response_text=?,
      response_date=CURDATE()
  WHERE id=? AND user_id=?
");
$up->execute([$response_type, $response_text, $id, $user_id]);

// حدث الحالة مباشرة
update_request_status($pdo, $id, $user_id);

header("Location: /tazallom_mvp/public/requests/view.php?id=".$id);
exit;
