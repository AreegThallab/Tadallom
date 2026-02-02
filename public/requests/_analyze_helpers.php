<?php
require_once __DIR__ . "/../../includes/ai_client.php";

function analyze_and_update_request(PDO $pdo, int $request_id, int $user_id): void {

  // 1) جلب الطلب
  $st = $pdo->prepare("SELECT * FROM requests WHERE id=? AND user_id=? LIMIT 1");
  $st->execute([$request_id, $user_id]);
  $r = $st->fetch(PDO::FETCH_ASSOC);
  if (!$r) throw new Exception("Request not found");

  // ✅ نعتمد تاريخ العلم بالقرار كمرجع للمهلة (الأدق غالباً)
  $base_date = !empty($r["decision_notice_date"]) ? $r["decision_notice_date"] : $r["decision_date"];

  // 2) استدعاء AI
  $payload = [
    "complaint_text" => $r["complaint_text"] ?? "",
    "decision_date" => $r["decision_date"] ?? date("Y-m-d"),
    "grievance_submitted" => true,
    "grievance_date" => $base_date, // مؤقتاً: نفس تاريخ العلم
    "admin_response" => (bool)($r["admin_response"] ?? 0),
    "response_type" => $r["response_type"] ?? "لا يوجد",
    "today" => date("Y-m-d"),
  ];

  $ai = ai_analyze_v2($payload);

  if ($ai["ok"]) {
    $data = $ai["data"];
    $category = $data["inferred"]["category"] ?? "أخرى";
    $risk = $data["analysis"]["risk_level"] ?? "متوسط";
    $report = $data["report"] ?? "";
  } else {
    $category = "أخرى";
    $risk = "متوسط";
    $report = "تعذر الاتصال بخدمة التحليل.";
  }

  // 3) جلب مدة التظلم من grievance_rules (entity_id + category)
  $q = $pdo->prepare("SELECT grievance_days FROM grievance_rules WHERE entity_id=? AND category=? LIMIT 1");
  $q->execute([(int)$r["entity_id"], $category]);
  $row = $q->fetch(PDO::FETCH_ASSOC);
  $days = $row ? (int)$row["grievance_days"] : 60;

  // 4) حساب deadline_date (من base_date)
  $decision = new DateTime($base_date);
  $deadline = (clone $decision)->modify("+{$days} days");
  $deadline_date = $deadline->format("Y-m-d");

  // 5) status
  $today = new DateTime(date("Y-m-d"));
  $status = ($today <= $deadline) ? "بانتظار الرد" : "اكتمل شكليًا";

  // 6) تحديث الطلب
  $up = $pdo->prepare("
    UPDATE requests SET
      category=?,
      grievance_days=?,
      deadline_date=?,
      status=?,
      risk_level=?,
      report_text=?,
      updated_at=NOW()
    WHERE id=? AND user_id=?
    LIMIT 1
  ");
  $up->execute([
    $category,
    $days,
    $deadline_date,
    $status,
    $risk,
    $report,
    $request_id,
    $user_id
  ]);
}
