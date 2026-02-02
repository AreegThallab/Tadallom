<?php
// update request status based on deadline + admin response
function update_request_status(PDO $pdo, int $request_id, int $user_id): void {
  $st = $pdo->prepare("SELECT id, admin_response, response_type, deadline_date, status, risk_level
                       FROM requests
                       WHERE id=? AND user_id=? LIMIT 1");
  $st->execute([$request_id, $user_id]);
  $r = $st->fetch(PDO::FETCH_ASSOC);
  if (!$r) return;

  $admin = (int)$r['admin_response'];
  $respType = $r['response_type'] ?? 'لا يوجد';
  $deadline = $r['deadline_date'];

  $newStatus = $r['status'];
  $newRisk   = $r['risk_level'];

  $today = new DateTime(date("Y-m-d"));

  // لو فيه رد موضوعي => مكتمل فورًا
  if ($admin === 1 && $respType === "موضوعي") {
    $newStatus = "مكتمل";
    $newRisk   = "منخفض";
  } else {
    // لا رد أو رد شكلي => يعتمد على المهلة
    if ($deadline) {
      $dl = DateTime::createFromFormat("Y-m-d", $deadline);
      if ($dl && $today >= $dl) {
        $newStatus = "اكتمل شكليًا";
        $newRisk   = "منخفض";
      } else {
        // قبل انتهاء المهلة
        $newStatus = "بانتظار الرد";
        $newRisk   = "متوسط";
      }
    }
  }

  if ($newStatus !== $r['status'] || $newRisk !== $r['risk_level']) {
    $up = $pdo->prepare("UPDATE requests SET status=?, risk_level=? WHERE id=? AND user_id=?");
    $up->execute([$newStatus, $newRisk, $request_id, $user_id]);
  }
}
