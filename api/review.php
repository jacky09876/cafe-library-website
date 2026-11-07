<?php
require __DIR__.'/config.php'; header('Content-Type: application/json');
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $d = json_decode(file_get_contents('php://input'), true) ?: [];
  $n = trim((string)($d['name'] ?? '')); $t = trim((string)($d['text'] ?? '')); $st = (int)($d['stars'] ?? 0);
  if ($n === '' || $t === '' || $st < 1 || $st > 5) { http_response_code(400); exit(json_encode(['error' => 'Invalid review'])); }
  try { db()->prepare('INSERT INTO reviews (name,stars,text) VALUES (?,?,?)')->execute([mb_substr($n, 0, 40), $st, mb_substr($t, 0, 500)]); }
  catch (Throwable $e) { http_response_code(500); exit(json_encode(['error' => 'Database error'])); }
  exit(json_encode(['ok' => true]));
}
try { echo json_encode(db()->query("SELECT name, stars, text, DATE_FORMAT(created_at,'%e %b %Y') AS date FROM reviews WHERE visible=1 ORDER BY id")->fetchAll(), JSON_UNESCAPED_UNICODE); }
catch (Throwable $e) { http_response_code(500); echo '[]'; }
