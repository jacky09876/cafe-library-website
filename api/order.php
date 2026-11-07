<?php
require __DIR__.'/config.php'; header('Content-Type: application/json');
$d = json_decode(file_get_contents('php://input'), true) ?: [];
$items = array_values(array_filter(array_map(fn($i) => ['name' => mb_substr((string)($i['name'] ?? ''), 0, 60), 'qty' => max(1, min(50, (int)($i['qty'] ?? 0))), 'price' => max(0, (int)($i['price'] ?? 0))], array_filter((array)($d['items'] ?? []), 'is_array')), fn($i) => $i['name'] !== ''));
$name = trim((string)($d['name'] ?? '')); $phone = trim((string)($d['phone'] ?? '')); $table = mb_substr(trim((string)($d['table'] ?? '')), 0, 6);
$type = ($d['type'] ?? '') === 'Takeaway' ? 'Takeaway' : 'Dine-in';
if (!$items || $name === '' || strlen($phone) < 8) { http_response_code(400); exit(json_encode(['error' => 'Please fill in your name and phone'])); }
if ($type === 'Dine-in' && $table === '') { http_response_code(400); exit(json_encode(['error' => 'Please enter your table number'])); }
$total = array_sum(array_map(fn($i) => $i['qty'] * $i['price'], $items));
try {
$s = db()->prepare('INSERT INTO orders (name,phone,type,table_no,note,items,total) VALUES (?,?,?,?,?,?,?)');
$s->execute([mb_substr($name, 0, 80), mb_substr($phone, 0, 20), $type, $table, mb_substr((string)($d['note'] ?? ''), 0, 300), json_encode($items, JSON_UNESCAPED_UNICODE), $total]);
echo json_encode(['code' => 'CL'.str_pad((string)db()->lastInsertId(), 4, '0', STR_PAD_LEFT)]);
} catch (Throwable $e) { http_response_code(500); echo json_encode(['error' => 'Database error. Check that MySQL is running, database.sql is imported and api/config.php exists.']); }
