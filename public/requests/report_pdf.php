<?php
require_once __DIR__ . "/../../includes/db.php";
require_once __DIR__ . "/../../includes/helpers.php";

if (session_status() === PHP_SESSION_NONE) session_start();
if (!($_SESSION["is_logged_in"] ?? false)) {
  header("Location: /tazallom_mvp/public/auth/login.php");
  exit;
}

$user_id    = (int)($_SESSION["user_id"] ?? 0);
$request_id = (int)($_GET["id"] ?? 0);
if ($request_id <= 0) die("Missing id");

require_once __DIR__ . "/../../vendor/autoload.php";

use Mpdf\Mpdf;

/* ================= ✅ مهم: توقيت السعودية + منع الكاش ================= */
date_default_timezone_set("Asia/Riyadh");

// منع التخزين المؤقت (عشان التاريخ يتجدد)
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

/* ================= Helpers ================= */
function h($s){ return htmlspecialchars((string)$s, ENT_QUOTES, "UTF-8"); }
function fmtDate($d){
  if (!$d) return "—";
  $ts = strtotime((string)$d);
  return $ts ? date("d/m/Y", $ts) : (string)$d;
}
function mpdf_file_src(string $path): string {
  $real = realpath($path);
  if (!$real || !is_file($real)) return "";
  return "file:///" . str_replace("\\", "/", $real);
}

/* ================= Data ================= */
$st = $pdo->prepare("
  SELECT
    r.*,
    u.first_name, u.second_name, u.last_name,
    u.national_id AS user_nid,
    u.dob AS user_dob,
    ge.headquarter AS entity_headquarter
  FROM requests r
  JOIN users u ON u.id = r.user_id
  LEFT JOIN government_entities ge ON ge.id = r.entity_id
  WHERE r.id = ? AND r.user_id = ?
  LIMIT 1
");
$st->execute([$request_id, $user_id]);
$r = $st->fetch(PDO::FETCH_ASSOC);
if (!$r) die("Not found");

/* ================= Fields ================= */
$party1_name = trim(($r["first_name"] ?? "")." ".($r["second_name"] ?? "")." ".($r["last_name"] ?? ""));
$party2_name = $r["entity_name"] ?? "—";

$request_no   = $r["request_number"] ?? (string)$r["id"];
$request_date = fmtDate($r["created_at"] ?? date("Y-m-d"));
$status       = $r["status"] ?? "—";

$party1_nid   = $r["user_nid"] ?? "—";
$party1_dob   = fmtDate($r["user_dob"] ?? null);

$party2_case        = "دعوى إلغاء قرار إداري";
$party2_headquarter = $r["entity_headquarter"] ?? "—";
$party2_role        = "الجهة الإدارية";

$request_type = "تظُّلُم";
$party1_role  = "فرد";

$decision_no    = $r["decision_number"] ?? "—";
$decision_dt    = fmtDate($r["decision_date"] ?? null);
$decision_type  = $r["decision_type"] ?? "—";
$decision_state = "ساري";
$decision_src   = $r["decision_source"] ?? "—";

$subject = trim($r["complaint_text"] ?? "");
$responseText = ((int)($r["admin_response"] ?? 0) === 1 && !empty($r["response_text"]))
  ? $r["response_text"]
  : "لم يتم الرد من الجهة حتى تاريخ إصدار هذا التقرير.";

/* ================= Remaining Days ================= */
$days_left_txt = "—";
if (!empty($r["deadline_date"])) {
  $today = new DateTime(date("Y-m-d"));
  $deadline = new DateTime($r["deadline_date"]);
  $diff = (int)$today->diff($deadline)->format("%r%a");
  if ($diff > 0) $days_left_txt = $diff . " يوم";
  elseif ($diff === 0) $days_left_txt = "اليوم آخر يوم";
  else $days_left_txt = "انتهت المهلة";
}

/* ================= Assets ================= */
$logoSrc  = mpdf_file_src(__DIR__ . "/../assets/logo1.png");
$stampSrc = mpdf_file_src(__DIR__ . "/../assets/stamp.png");

/* ================= mPDF ================= */
$mpdf = new Mpdf([
  "mode" => "utf-8",
  "format" => "A4",
  "margin_left" => 14,
  "margin_right" => 14,
  "margin_top" => 12,
  "margin_bottom" => 12,
  "default_font" => "dejavusans",
  "autoScriptToLang" => true,
  "autoLangToFont" => true,
  "shrink_tables_to_fit" => 1,
]);
$mpdf->SetDirectionality('rtl');

/* ================= HTML ================= */
$logoHtml = $logoSrc
  ? '<div class="logo"><img src="'.h($logoSrc).'" width="300" alt="تظلُّم"></div>'
  : '';

$stampHtml = $stampSrc
  ? '<div class="stamp"><img src="'.h($stampSrc).'" width="300"></div>'
  : '';

// ✅ تاريخ إصدار التقرير اليوم بالتوقيت السعودي
$issued_at = date("d/m/Y");

$html = '
<!doctype html>
<html lang="ar" dir="rtl">
<head>
<meta charset="utf-8">
<style>
  body{
    font-family: dejavusans, sans-serif;
    direction: rtl;
    color:#111;
    font-size: 12pt;
  }
  .logo{ text-align:right; margin-bottom:6px; }
  h1{ text-align:center; margin:0 0 10px; font-size:20pt; font-weight:800; }
  table{ width:100%; border-collapse:collapse; margin:8px 0 10px; }
  th, td{ border:1px solid #333; padding:6px 8px; text-align:center; font-size:11.5pt; }
  .head th{ background:#b18a5a; color:#fff; }
  .sec{ font-size:14pt; font-weight:800; margin:8px 0 6px; text-align:right; }
  .box{ border:2px solid #111; padding:9px 10px; line-height:1.8; margin-bottom:10px; }
.stamp{
  position: fixed;
  left: 14mm;
  bottom: 0mm;          /* يكون فوق الفوتر */
  transform: rotate(-12deg);
  opacity: .95;
}
.foot{
  position: fixed;
  left: 14mm;
  right: 14mm;
  bottom: 0mm;          /* نزّلي/ارفعي الرقم حسب رغبتك */
  font-size: 10pt;
  color:#444;
  text-align:center;
}
</style>
</head>
<body>

'.$logoHtml.'
<h1>تقرير إثبات التظلُّم</h1>

<table>
<tr class="head">
  <th>رقم الطلب</th>
  <th>تاريخ الطلب</th>
  <th>حالة الطلب</th>
  <th>الطرف الاول</th>
  <th>الطرف الثاني</th>
</tr>
<tr>
  <td>'.h($request_no).'</td>
  <td>'.h($request_date).'</td>
  <td>'.h($status).'</td>
  <td>'.h($party1_name).'</td>
  <td>'.h($party2_name).'</td>
</tr>
</table>

<div class="sec">الطرف الاول</div>
<table>
<tr>
  <th>الاسم</th><th>رقم الهوية</th><th>نوع الطلب</th><th>تاريخ الميلاد</th><th>الصفة</th>
</tr>
<tr>
  <td>'.h($party1_name).'</td>
  <td>'.h($party1_nid).'</td>
  <td>'.h($request_type).'</td>
  <td>'.h($party1_dob).'</td>
  <td>'.h($party1_role).'</td>
</tr>
</table>

<div class="sec">الجهة الإدارية</div>
<table>
<tr>
  <th>الاسم</th>
  <th>نوع الدعوى</th>
  <th>المقر الرئيسي</th>
  <th>الصفة</th>
</tr>
<tr>
  <td>'.h($party2_name).'</td>
  <td>'.h($party2_case).'</td>
  <td>'.h($party2_headquarter).'</td>
  <td>'.h($party2_role).'</td>
</tr>
</table>

<div class="sec">محل التظلّم</div>
<table>
<tr>
  <th>رقم القرار</th>
  <th>التاريخ</th>
  <th>نوع القرار</th>
  <th>حالة القرار</th>
  <th>المصدر</th>
  <th>المدة المتبقية</th>
</tr>
<tr>
  <td>'.h($decision_no).'</td>
  <td>'.h($decision_dt).'</td>
  <td>'.h($decision_type).'</td>
  <td>'.h($decision_state).'</td>
  <td>'.h($decision_src).'</td>
  <td>'.h($days_left_txt).'</td>
</tr>
</table>

<div class="sec">موضوع التظلم</div>
<div class="box">'.nl2br(h($subject ?: "—")).'</div>

<div class="sec">نتيجة التظلم</div>
<div class="box">'.nl2br(h($responseText)).'</div>

'.$stampHtml.'

<div class="foot">
  *تم إنشاء هذا التقرير بتاريخ ('.h($issued_at).') آليًا بناءً على بيانات الطلب المسجلة في المنصه
</div>

</body>
</html>
';

$mpdf->WriteHTML($html);
$mpdf->Output("tazallom_report_".$request_no.".pdf", "D");
exit;
