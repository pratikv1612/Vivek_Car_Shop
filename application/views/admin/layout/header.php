<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($title) ? html_escape($title) : 'Admin' ?> - V Auto Spare</title>
    <!-- Favicon -->
    <link rel="shortcut icon" href="<?= base_url('assets/image/favicon icon/favicon.ico') ?>" type="image/x-icon">
    <link rel="icon" type="image/png" sizes="16x16" href="<?= base_url('assets/image/favicon icon/favicon-16x16.png') ?>">
    <link rel="icon" type="image/png" sizes="32x32" href="<?= base_url('assets/image/favicon icon/favicon-32x32.png') ?>">
    <link rel="apple-touch-icon" sizes="180x180" href="<?= base_url('assets/image/favicon icon/apple-touch-icon.png') ?>">
    <link rel="icon" type="image/png" sizes="192x192" href="<?= base_url('assets/image/favicon icon/android-chrome-192x192.png') ?>">
    <link rel="icon" type="image/png" sizes="512x512" href="<?= base_url('assets/image/favicon icon/android-chrome-512x512.png') ?>">
    <link rel="manifest" href="<?= base_url('assets/image/favicon icon/site.webmanifest') ?>">
    <meta name="theme-color" content="#ffffff">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <style>
        .navbar-brand { display: inline-flex; align-items: center; gap: .5rem; }
        .navbar-brand img { max-height: 30px; width: auto; }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand d-flex align-items-center" href="<?= base_url('admin/dashboard') ?>">
                <img src="<?= base_url('/assets/image/logo.jpeg') ?>" 
                    alt="Logo" 
                    style="width:30px; height:30px; object-fit:contain; margin-right:8px;">
                V AutoSpare Admin
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#adminNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="adminNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item"><a class="nav-link" href="<?= base_url('admin/dashboard') ?>"><i class="bi bi-speedometer2"></i> Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?= base_url('admin/banners') ?>"><i class="bi bi-images"></i> Banners</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?= base_url('admin/products') ?>"><i class="bi bi-box-seam"></i> Products</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?= base_url('admin/cars') ?>"><i class="bi bi-car-front"></i> Cars</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?= base_url('admin/accessories') ?>"><i class="bi bi-tools"></i> Accessories</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?= base_url('admin/inquiries') ?>"><i class="bi bi-chat-dots"></i> Inquiries</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?= base_url('admin/users') ?>"><i class="bi bi-people"></i> Users</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?= base_url('admin/settings') ?>"><i class="bi bi-gear"></i> Settings</a></li>
                </ul>
                <span class="navbar-text text-white me-3"><?= isset($admin) ? html_escape($admin['full_name']) : '' ?></span>
                <a href="<?= base_url('admin/logout') ?>" class="btn btn-outline-light btn-sm">Logout</a>
            </div>
        </div>
    </nav>
    <div class="container-fluid py-4">
        <?php if (!empty($flash_success)): ?>
        <div class="alert alert-success alert-dismissible fade show"><?= html_escape($flash_success) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>
