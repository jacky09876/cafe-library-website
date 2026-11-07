<?php
require __DIR__.'/../api/config.php'; session_start(); $err = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (hash_equals(ADMIN_USER, (string)($_POST['u'] ?? '')) && hash_equals(ADMIN_PASS, (string)($_POST['p'] ?? ''))) { session_regenerate_id(true); $_SESSION['admin'] = true; header('Location: index.php'); exit; }
  $err = 'Wrong username or password.'; sleep(1);
}
?><!DOCTYPE html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><meta name="robots" content="noindex"><title>Admin login</title><link rel="stylesheet" href="../css/style.css"></head>
<body><main class="wrap" style="max-width:420px;padding:4rem 0"><h1 style="font-size:2.2rem">Admin login</h1><?php if ($err) echo '<p role="alert" style="color:var(--amber);margin-top:1rem">'.h($err).'</p>'; ?>
<form method="post"><label>Username<input name="u" required autocomplete="username"></label><label>Password<input name="p" type="password" required autocomplete="current-password"></label><button class="btn" type="submit">Log in</button></form></main></body></html>
