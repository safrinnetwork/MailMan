<?php
require_once __DIR__ . '/../vendor/autoload.php';

use MailMan\Auth;

$auth = new Auth();
$auth->requireAuth();

$currentPage = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?? 'MailMan' ?></title>
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>
    <nav class="navbar">
        <div class="navbar-content">
            <a href="/pages/send.php" class="navbar-brand">MailMan</a>
            <ul class="navbar-nav">
                <li><a href="/pages/send.php" class="nav-link <?= $currentPage === 'send.php' ? 'active' : '' ?>">Kirim Email</a></li>
                <li><a href="/pages/templates.php" class="nav-link <?= $currentPage === 'templates.php' ? 'active' : '' ?>">Template</a></li>
                <li><a href="/pages/logs.php" class="nav-link <?= $currentPage === 'logs.php' ? 'active' : '' ?>">Log</a></li>
                <li><a href="/pages/config.php" class="nav-link <?= $currentPage === 'config.php' ? 'active' : '' ?>">Konfigurasi</a></li>
                <li><a href="/pages/logout.php" class="nav-link">Logout</a></li>
            </ul>
        </div>
    </nav>
    <div class="container">
