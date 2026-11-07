<?php
// Copy this file to config.php and edit. config.php is ignored by Git so passwords are never published.
const DB_HOST = 'localhost', DB_NAME = 'cafe_library', DB_USER = 'root', DB_PASS = '';
ini_set('session.cookie_httponly', '1'); ini_set('session.cookie_samesite', 'Lax'); // safer admin login cookie
const PAPER_MM = 80; // roll printer paper width: 80 or 58
const ADMIN_USER = 'admin', ADMIN_PASS = 'ChangeMe@123'; // CHANGE THESE before using the site for real
function db(): PDO { static $p; return $p ??= new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME.';charset=utf8mb4', DB_USER, DB_PASS, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC]); }
function h($s): string { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }
