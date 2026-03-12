<?php
require_once __DIR__ . '/auth.php';
$user = auth_user();
$pageTitle = $pageTitle ?? 'Army DAK System';
$activeMenu = $activeMenu ?? '';
$isAdminPanel = str_starts_with($_SERVER['PHP_SELF'] ?? '', '/admin/');
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= esc($pageTitle) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>
<div class="app-shell d-flex">
