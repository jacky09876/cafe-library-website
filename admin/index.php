<?php
require __DIR__.'/../api/config.php'; session_start();
if (empty($_SESSION['admin'])) { header('Location: login.php'); exit; }
$_SESSION['csrf'] ??= bin2hex(random_bytes(16));
if ($_SERVER['REQUEST_METHOD'] === 'POST' && hash_equals($_SESSION['csrf'], (string)($_POST['csrf'] ?? ''))) {
  $id = (int)($_POST['id'] ?? 0);
  if (($_POST['do'] ?? '') === 'order' && in_array($_POST['status'] ?? '', ['new', 'preparing', 'ready', 'completed', 'cancelled'], true))
    db()->prepare('UPDATE orders SET status=? WHERE id=?')->execute([$_POST['status'], $id]);
  if (($_POST['do'] ?? '') === 'hide') db()->prepare('UPDATE reviews SET visible=0 WHERE id=?')->execute([$id]);
  header('Location: index.php?'.http_build_query(['s' => $_GET['s'] ?? ''])); exit;
}
$f = $_GET['s'] ?? ''; $sts = ['new', 'preparing', 'ready', 'completed', 'cancelled'];
$q = db()->prepare('SELECT * FROM orders'.(in_array($f, $sts, true) ? ' WHERE status=?' : '').' ORDER BY id DESC LIMIT 200');
$q->execute(in_array($f, $sts, true) ? [$f] : []); $orders = $q->fetchAll();
$st = db()->query("SELECT COUNT(*) n, COALESCE(SUM(CASE WHEN status<>'cancelled' THEN total END),0) rev, COALESCE(SUM(CASE WHEN paid=1 AND status<>'cancelled' THEN total END),0) paid, SUM(status='new') fresh FROM orders")->fetch();
$rv = db()->query('SELECT * FROM reviews WHERE visible=1 ORDER BY id DESC')->fetchAll();
$avg = $rv ? round(array_sum(array_column($rv, 'stars')) / count($rv), 1) : '-';
?><!DOCTYPE html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><meta name="robots" content="noindex"><title>Admin | The Cafe Library</title><link rel="stylesheet" href="../css/style.css"></head>
<body><header><div class="wrap bar"><span class="brand">Admin dashboard</span><nav style="display:flex"><a href="../index.html">View site</a><a href="kitchen.php">Kitchen</a><a href="logout.php">Log out</a></nav></div></header>
<main class="wrap admin"><div class="stats"><div><b><?= (int)$st['n'] ?></b>Orders</div><div><b><?= (int)$st['fresh'] ?></b>New orders</div><div><b>₹<?= (int)$st['rev'] ?></b>Sales</div><div><b>₹<?= (int)$st['paid'] ?></b>Marked paid</div><div><b><?= $avg ?></b>Rating (<?= count($rv) ?>)</div></div>
<h2>Orders</h2><p class="tabs"><?php foreach (array_merge([''], $sts) as $x) echo '<a href="?s='.h($x).'" class="btn ghost" style="padding:.2rem .9rem;'.($f === $x ? 'border-color:var(--amber)' : '').'">'.h($x ?: 'all').'</a> '; ?></p>
<div class="scroll"><table><tr><th>Order</th><th>Time</th><th>Customer</th><th>Items</th><th>Total</th><th>Payment</th><th>Update</th></tr>
<?php foreach ($orders as $o): ?><tr><td>CL<?= str_pad((string)$o['id'], 4, '0', STR_PAD_LEFT) ?><br><span class="tag"><?= h($o['status']) ?></span></td><td><?= h($o['created_at']) ?></td>
<td><?= h($o['name']) ?><br><a href="https://wa.me/91<?= h(substr(preg_replace('/\D/', '', $o['phone']), -10)) ?>"><?= h($o['phone']) ?></a><br><?= h($o['type'].($o['table_no'] ? ' - Table '.$o['table_no'] : '')) ?><?= $o['note'] ? '<br><em>'.h($o['note']).'</em>' : '' ?></td>
<td><?php foreach (json_decode($o['items'], true) ?: [] as $i) echo h($i['qty'].' x '.$i['name']).'<br>'; ?></td><td>₹<?= (int)$o['total'] ?></td>
<td><?= h(strtoupper((string)$o['pay'])) ?><br><span class="tag <?= $o['paid'] ? '' : 'no' ?>"><?= $o['paid'] ? 'paid' : 'unpaid' ?></span></td>
<td><form method="post"><input type="hidden" name="csrf" value="<?= h($_SESSION['csrf']) ?>"><input type="hidden" name="do" value="order"><input type="hidden" name="id" value="<?= (int)$o['id'] ?>">
<select name="status" aria-label="Order status"><?php foreach ($sts as $x) echo '<option'.($x === $o['status'] ? ' selected' : '').'>'.$x.'</option>'; ?></select>
<button class="btn" type="submit">Save</button></form><a href="bill.php?id=<?= (int)$o['id'] ?>">Bill and payment</a></td></tr>
<?php endforeach; if (!$orders) echo '<tr><td colspan="7">No orders yet.</td></tr>'; ?></table></div>
<h2 style="margin-top:3rem">Reviews</h2><?php foreach ($rv as $r): ?><article class="rev"><div class="top"><h3><?= h($r['name']) ?></h3><span class="stars"><?= str_repeat('★', (int)$r['stars']).str_repeat('☆', 5 - (int)$r['stars']) ?></span></div><p><?= h($r['text']) ?></p>
<form method="post"><input type="hidden" name="csrf" value="<?= h($_SESSION['csrf']) ?>"><input type="hidden" name="do" value="hide"><input type="hidden" name="id" value="<?= (int)$r['id'] ?>"><button class="add" type="submit">Hide review</button></form></article>
<?php endforeach; if (!$rv) echo '<p class="lead">No reviews yet.</p>'; ?></main></body></html>
