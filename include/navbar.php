<?php
if (!isset($pageTitle)) {
    $pageTitle = 'Svu City Events';
}

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$isAdminLoggedIn = isset($_SESSION['admin_id']);
?>
<!doctype html>
<html lang="en" data-bs-theme="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo htmlspecialchars($pageTitle); ?></title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="assets/css/styles.css">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark sticky-top" id="siteNavbar">
    <div class="container">
        <a class="navbar-brand" href="index.php">Svu City Events</a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar" aria-controls="mainNavbar" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="mainNavbar">
            <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link" href="index.php">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="events.php">Events</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="about.php">About</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="contact.php">Contact</a>
                </li>
                <li class="nav-item">
                    <?php if ($isAdminLoggedIn): ?>
                        <a class="nav-link" href="admin/dashboard.php">Dashboard</a>
                    <?php else: ?>
                        <a class="nav-link" href="admin/login.php">Admin Login</a>
                    <?php endif; ?>
                </li>
            </ul>

            <button class="btn btn-outline-light ms-lg-3 theme-toggle" id="themeToggle" type="button" aria-label="Toggle dark mode" aria-pressed="true">
                <i class="bi bi-moon-stars-fill me-2" id="themeToggleIcon"></i>
                <span id="themeToggleText">Dark</span>
            </button>
        </div>
    </div>
</nav>
<main class="content-wrap">
    <div class="container">
