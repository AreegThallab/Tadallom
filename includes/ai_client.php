<?php

function ai_analyze_v2(array $payload): array {
  $url = "http://127.0.0.1:8000/analyze_v2";

  $ch = curl_init($url);
  curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_HTTPHEADER => ["Content-Type: application/json; charset=utf-8"],
    CURLOPT_POSTFIELDS => json_encode($payload, JSON_UNESCAPED_UNICODE),
    CURLOPT_CONNECTTIMEOUT => 2,
    CURLOPT_TIMEOUT => 12,
  ]);

  $raw = curl_exec($ch);
  $err = curl_error($ch);
  $code = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
  curl_close($ch);

  if ($raw === false) {
    return ["ok" => false, "error" => "cURL error: $err"];
  }

  $data = json_decode($raw, true);

  if ($code >= 200 && $code < 300 && is_array($data)) {
    return ["ok" => true, "data" => $data];
  }

  return ["ok" => false, "error" => "HTTP $code", "raw" => $raw];
}
