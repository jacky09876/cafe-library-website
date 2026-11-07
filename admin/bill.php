<?php
require __DIR__.'/../api/config.php'; session_start();
if (empty($_SESSION['admin'])) { header('Location: login.php'); exit; }
$_SESSION['csrf'] ??= bin2hex(random_bytes(16));
$W = defined('PAPER_MM') ? (int)PAPER_MM : 80;
$id = (int)($_GET['id'] ?? 0); $q = db()->prepare('SELECT * FROM orders WHERE id=?'); $q->execute([$id]); $o = $q->fetch();
if (!$o) { http_response_code(404); exit('Order not found'); }
if ($_SERVER['REQUEST_METHOD'] === 'POST' && hash_equals($_SESSION['csrf'], (string)($_POST['csrf'] ?? '')) && !$o['paid'] && in_array($_POST['pay'] ?? '', ['cash', 'upi', 'card'], true)) {
  db()->prepare("UPDATE orders SET pay=?, paid=1, paid_at=NOW(), status='completed' WHERE id=?")->execute([$_POST['pay'], $id]); header("Location: bill.php?id=$id"); exit; }
$items = json_decode($o['items'], true) ?: []; $code = 'CL'.str_pad((string)$o['id'], 4, '0', STR_PAD_LEFT);
?><!DOCTYPE html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><meta name="robots" content="noindex"><title>Bill <?= h($code) ?></title><link rel="stylesheet" href="../css/style.css">
<style>.rec{background:#fff;color:#000;font:15px/1.4 monospace;padding:1rem;max-width:340px;margin:1.5rem 0}.rec p{display:flex;justify-content:space-between;gap:1rem}.rec hr{border:0;border-top:2px dashed #000}.rec .c{text-align:center;display:block}@page{size:<?= $W ?>mm auto;margin:2mm}
@media print{body>*:not(main),main>*:not(.rec){display:none!important}body{background:#fff!important}.rec{margin:0;padding:0;max-width:<?= $W - 8 ?>mm}}</style></head>
<body><header><div class="wrap bar"><span class="brand">Bill</span><nav style="display:flex"><a href="index.php">Dashboard</a><a href="kitchen.php">Kitchen</a></nav></div></header>
<main class="wrap admin"><div class="rec"><b class="c">THE CAFE LIBRARY</b><span class="c">Vapi, Gujarat</span><hr><p><span><?= h($code) ?></span><span><?= h($o['created_at']) ?></span></p><p><span><?= h($o['type']) ?><?= $o['table_no'] ? ', Table '.h($o['table_no']) : '' ?></span></p><hr>
<?php foreach ($items as $i) echo '<p><span>'.h($i['qty'].' x '.$i['name']).'</span><span>'.(int)($i['qty'] * $i['price']).'</span></p>'; ?><hr><p><b>TOTAL</b><b>Rs <?= (int)$o['total'] ?></b></p>
<p><span><?= $o['paid'] ? 'PAID by '.h(strtoupper($o['pay'])) : 'NOT PAID YET' ?></span></p><hr><span class="c">Thank you! Please leave a review on our website.</span></div>
<?php if (!$o['paid']): ?><form method="post" style="max-width:340px"><input type="hidden" name="csrf" value="<?= h($_SESSION['csrf']) ?>"><fieldset><legend>Customer pays by</legend>
<label><input type="radio" name="pay" value="cash" checked> Cash</label><label><input type="radio" name="pay" value="upi"> UPI</label><label><input type="radio" name="pay" value="card"> Card (swipe machine)</label></fieldset>
<div id="qrbox" hidden><img class="qr" src="../images/upi-qr.png" alt="UPI QR code"><p class="note">Customer scans, pays Rs <?= (int)$o['total'] ?>, then tap Mark as paid.</p></div><button class="btn" type="submit">Mark as paid</button></form><?php endif; ?>
<p style="margin-top:1rem"><button class="btn ghost" onclick="window.print()">Print bill</button></p></main>
<script>document.querySelectorAll('input[name=pay]').forEach(r => r.onchange = () => { const q = document.getElementById('qrbox'); if (q) q.hidden = document.querySelector('input[name=pay]:checked').value !== 'upi'; });</script></body></html>
