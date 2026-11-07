<?php
require __DIR__.'/../api/config.php'; session_start();
if (empty($_SESSION['admin'])) { header('Location: login.php'); exit; }
$_SESSION['csrf'] ??= bin2hex(random_bytes(16));
$W = defined('PAPER_MM') ? (int)PAPER_MM : 80;
function rows(string $w): array { $r = db()->query("SELECT id,type,table_no,note,items,status,created_at FROM orders WHERE $w ORDER BY id")->fetchAll();
  foreach ($r as $k => $o) { $r[$k]['items'] = json_decode($o['items'], true) ?: []; $r[$k]['code'] = 'CL'.str_pad((string)$o['id'], 4, '0', STR_PAD_LEFT); } return $r; }
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  header('Content-Type: application/json');
  if (!hash_equals($_SESSION['csrf'], (string)($_POST['csrf'] ?? ''))) { http_response_code(403); exit('{}'); }
  $id = (int)($_POST['id'] ?? 0);
  if (($_POST['act'] ?? '') === 'printed') db()->prepare('UPDATE orders SET printed=1 WHERE id=?')->execute([$id]);
  if (($_POST['act'] ?? '') === 'status' && in_array($_POST['status'] ?? '', ['preparing', 'ready'], true)) db()->prepare('UPDATE orders SET status=? WHERE id=?')->execute([$_POST['status'], $id]);
  exit('{"ok":true}');
}
if (isset($_GET['json'])) { header('Content-Type: application/json'); echo json_encode(['new' => rows("printed=0 AND status<>'cancelled'"), 'active' => rows("status IN ('new','preparing','ready')")], JSON_UNESCAPED_UNICODE); exit; }
?><!DOCTYPE html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><meta name="robots" content="noindex"><title>Kitchen | The Cafe Library</title><link rel="stylesheet" href="../css/style.css">
<style>#ticket{display:none}@page{size:<?= $W ?>mm auto;margin:2mm}
@media print{body>*:not(#ticket){display:none!important}body{background:#fff!important}#ticket{display:block;width:<?= $W - 8 ?>mm;color:#000;font:15px/1.35 monospace}#ticket h1{font-size:26px}#ticket .it{font-size:18px;font-weight:bold}#ticket hr{border:0;border-top:2px dashed #000}}</style></head>
<body><header><div class="wrap bar"><span class="brand">Kitchen</span><nav style="display:flex"><a href="index.php">Dashboard</a><a href="logout.php">Log out</a></nav></div></header>
<main class="wrap admin"><h1 style="font-size:2rem">Kitchen orders</h1><p class="lead">Keep this page open on the kitchen computer. New orders print on the roll printer by themselves.</p>
<label style="display:flex;gap:.5rem;font-weight:400;margin-top:1rem"><input type="checkbox" id="auto" checked> Print new orders automatically</label><p id="st" class="note">Starting...</p><button class="add" id="test" type="button">Print a test ticket</button><div id="list"></div></main><div id="ticket"></div>
<script>
const csrf = "<?= h($_SESSION['csrf']) ?>", list = document.getElementById("list"), t = document.getElementById("ticket"); let busy = false, active = {};
const esc = (s) => String(s).replace(/[&<>"']/g, c => ({"&":"&amp;","<":"&lt;",">":"&gt;",'"':"&quot;","'":"&#39;"}[c]));
const post = (act, id, status = "") => fetch("kitchen.php", {method: "POST", headers: {"Content-Type": "application/x-www-form-urlencoded"}, body: new URLSearchParams({act, id, status, csrf})});
const place = (o) => (o.type === "Takeaway" ? "TAKEAWAY" : "TABLE " + esc(o.table_no));
const printOrder = async (o) => { t.innerHTML = `<h2>KITCHEN ORDER</h2><h1>${place(o)}</h1><p>${o.code} - ${esc(o.created_at.slice(11, 16))}</p><hr>${o.items.map(i => `<p class="it">${i.qty} x ${esc(i.name)}</p>`).join("")}<hr>${o.note ? `<p>NOTE: ${esc(o.note)}</p>` : ""}`; window.print(); await new Promise(r => setTimeout(r, 1200)); };
function draw(rows) { active = Object.fromEntries(rows.map(o => [o.id, o]));
  list.innerHTML = rows.map(o => `<article class="kcard"><h3>${o.code} - ${place(o)} <span class="tag">${o.status}</span></h3>${o.items.map(i => `<div>${i.qty} x ${esc(i.name)}</div>`).join("")}${o.note ? `<em>Note: ${esc(o.note)}</em>` : ""}
    <div class="row"><button class="add" data-a="print" data-id="${o.id}">Print again</button><button class="add" data-a="preparing" data-id="${o.id}">Preparing</button><button class="add" data-a="ready" data-id="${o.id}">Ready</button></div></article>`).join("") || '<p class="lead" style="margin-top:1rem">No active orders.</p>'; }
list.onclick = async (e) => { const b = e.target.closest("button"); if (!b) return; const o = active[b.dataset.id];
  if (b.dataset.a === "print") await printOrder(o); else { await post("status", o.id, b.dataset.a); tick(); } };
async function tick() { if (busy) return; busy = true;
  try { const d = await (await fetch("kitchen.php?json=1")).json(); draw(d.active);
    if (document.getElementById("auto").checked) for (const o of d.new) { beep(); await printOrder(o); await post("printed", o.id); }
    st.textContent = "Checked at " + new Date().toLocaleTimeString() + ". Waiting for new orders."; }
  catch (e) { st.textContent = "Cannot reach the server, or you were logged out. Refresh this page."; }
  busy = false; }
const st = document.getElementById("st");
const beep = () => { try { const a = new AudioContext(), o = a.createOscillator(); o.connect(a.destination); o.frequency.value = 880; o.start(); setTimeout(() => { o.stop(); a.close(); }, 300); } catch (e) {} };
document.getElementById("test").onclick = () => printOrder({code: "TEST", type: "Dine-in", table_no: "0", created_at: "0000-00-00 00:00:00", items: [{qty: 1, name: "Test item"}], note: "Printer test"});
tick(); setInterval(tick, 5000);
</script></body></html>
