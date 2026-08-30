<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?= isset($meta_description) ? html_escape($meta_description) : 'Find genuine car parts - VAutoSpare' ?>">
    <meta name="keywords" content="<?= isset($meta_keywords) ? html_escape($meta_keywords) : 'car spare parts, accessories, auto parts' ?>">
    <title><?= isset($meta_title) ? html_escape($meta_title) : 'V Auto Spare - Car Parts Store' ?></title>
    <link rel="shortcut icon" href="<?= base_url('assets/image/favicon icon/favicon.ico') ?>" type="image/x-icon">
    <link rel="icon" type="image/png" sizes="16x16" href="<?= base_url('assets/image/favicon icon/favicon-16x16.png') ?>">
    <link rel="icon" type="image/png" sizes="32x32" href="<?= base_url('assets/image/favicon icon/favicon-32x32.png') ?>">
    <link rel="apple-touch-icon" sizes="180x180" href="<?= base_url('assets/image/favicon icon/apple-touch-icon.png') ?>">
    <link rel="manifest" href="<?= base_url('assets/image/favicon icon/site.webmanifest') ?>">
    <meta name="theme-color" content="#f97316">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Jost:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="<?= base_url('assets/css/style.css') ?>" rel="stylesheet">
</head>
<body class="va-storefront">
    <header class="va-site-header">
        <div class="va-header-top">
            <div class="container va-header-top-inner">
                <a class="va-brand" href="<?= base_url() ?>">
                    <img src="<?= base_url('/assets/image/logo.jpeg') ?>" alt="<?= html_escape($site_name ?? 'V Auto Spare') ?> Logo">
                    <span><?= html_escape($site_name ?? 'V Auto Spare') ?></span>
                </a>

                <form action="<?= base_url('accessories') ?>" method="get" class="va-header-search">
                    <select name="category" aria-label="Category">
                        <option value="">All Categories</option>
                        <?php if (!empty($accessory_categories)): ?>
                            <?php foreach ($accessory_categories as $cat): ?>
                                <option value="<?= $cat->id ?>"><?= html_escape($cat->name) ?></option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                    <input type="text" name="keyword" placeholder="Search products">
                    <button type="submit" aria-label="Search"><i class="bi bi-search"></i></button>
                </form>

                <div class="va-header-actions">
                    <a class="va-header-shop-link" href="<?= $this->session->userdata('user_id') ? base_url('account') : base_url('login') ?>" title="<?= $this->session->userdata('user_id') ? 'My Account' : 'Login' ?>" aria-label="<?= $this->session->userdata('user_id') ? 'My Account' : 'Login' ?>"><i class="bi bi-person"></i></a>
                    <button type="button" class="va-header-shop-link" title="Compare" aria-label="Open compare list"><i class="bi bi-shuffle"></i><b id="compareCount">0</b></button>
                    <button type="button" class="va-header-shop-link" title="Wishlist" aria-label="Open wishlist"><i class="bi bi-heart"></i><b id="wishlistCount">0</b></button>
                    <button type="button" class="va-header-shop-link va-header-cart-trigger" title="Cart" aria-label="Open shopping cart"><i class="bi bi-bag"></i><b id="cartCount">0</b></button>
                </div>
            </div>
        </div>

        <nav class="va-header-nav navbar navbar-expand-lg">
            <div class="container">
                <a class="va-category-link" href="<?= base_url('accessories') ?>"><i class="bi bi-grid"></i> All Categories</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMain" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navMain">
                    <ul class="navbar-nav mx-auto">
                        <li class="nav-item"><a class="nav-link" href="<?= base_url() ?>">Home</a></li>
                        <li class="nav-item"><a class="nav-link" href="<?= base_url('accessories') ?>">Shop</a></li>
                        <li class="nav-item"><a class="nav-link" href="<?= base_url('cars') ?>">Cars Section</a></li>
                        <li class="nav-item"><a class="nav-link" href="<?= base_url('accessories') ?>">Parts</a></li>
                        <li class="nav-item"><a class="nav-link" href="<?= base_url('new-arrivals') ?>">New Arrivals</a></li>
                        <li class="nav-item"><a class="nav-link" href="<?= base_url('about') ?>">About</a></li>
                        <li class="nav-item"><a class="nav-link" href="<?= base_url('contact') ?>">Contact</a></li>
                    </ul>
                </div>
                <a class="va-nav-phone" href="<?= html_escape($whatsapp_url ?? '#') ?>" target="_blank" rel="noopener" aria-label="Contact us">
                    <i class="bi bi-telephone-fill"></i>
                    <span><?= html_escape($whatsapp_number ?? '91 2345 678') ?></span>
                </a>
            </div>
        </nav>
    </header>

    <main class="va-main">
        <div class="container va-page-frame">
