<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? $pageTitle . ' - ' : ''; ?><?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/public/css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="<?php echo isset($isAuthPage) ? 'auth-page' : ''; ?>">
    <?php if (!isset($isAuthPage) && isLoggedIn()): ?>
    <div class="app-container">
        <?php include __DIR__ . '/sidebar.php'; ?>
        <div class="main-content">
            <?php include __DIR__ . '/top-header.php'; ?>
            <main class="content">
    <?php endif; ?>
