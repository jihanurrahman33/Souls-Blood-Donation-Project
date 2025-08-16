<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($pageTitle) ? $pageTitle . ' - ' . APP_NAME : APP_NAME ?></title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-danger">
        <div class="container">
            <a class="navbar-brand fw-bold" href="<?= APP_URL ?>">
                <i class="fas fa-heartbeat me-2"></i><?= APP_NAME ?>
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="<?= APP_URL ?>">
                            <i class="fas fa-home me-1"></i>Home
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= APP_URL ?>donate">
                            <i class="fas fa-hand-holding-heart me-1"></i>Donate
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= APP_URL ?>request">
                            <i class="fas fa-search me-1"></i>Request
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= APP_URL ?>forum">
                            <i class="fas fa-comments me-1"></i>Forum
                        </a>
                    </li>
                </ul>
                
                <ul class="navbar-nav">
                    <?php if (isLoggedIn()): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= APP_URL ?>home/dashboard">
                                <i class="fas fa-tachometer-alt me-1"></i>Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= APP_URL ?>chat">
                                <i class="fas fa-comments me-1"></i>Chat
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= APP_URL ?>search">
                                <i class="fas fa-search me-1"></i>Search
                            </a>
                        </li>
                        <?php if (isAdmin()): ?>
                            <li class="nav-item">
                                <a class="nav-link" href="<?= APP_URL ?>admin">
                                    <i class="fas fa-cog me-1"></i>Admin
                                </a>
                            </li>
                        <?php endif; ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                                <i class="fas fa-user me-1"></i><?= $_SESSION['first_name'] ?>
                            </a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="<?= APP_URL ?>auth/logout">
                                    <i class="fas fa-sign-out-alt me-1"></i>Logout
                                </a></li>
                            </ul>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= APP_URL ?>auth/login">
                                <i class="fas fa-sign-in-alt me-1"></i>Login
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= APP_URL ?>auth/register">
                                <i class="fas fa-user-plus me-1"></i>Register
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Flash Messages -->
    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show m-3" role="alert">
            <i class="fas fa-check-circle me-2"></i><?= $_SESSION['success'] ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['errors'])): ?>
        <div class="alert alert-danger alert-dismissible fade show m-3" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>
            <?php if (is_array($_SESSION['errors'])): ?>
                <ul class="mb-0">
                    <?php foreach ($_SESSION['errors'] as $error): ?>
                        <li><?= $error ?></li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <?= $_SESSION['errors'] ?>
            <?php endif; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php unset($_SESSION['errors']); ?>
    <?php endif; ?>

    <!-- Main Content -->
    <main class="container my-4"> 