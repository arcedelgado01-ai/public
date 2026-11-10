<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="theme-color" content="#ffffff">
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>

<?php
$cart_count = WC()->cart ? count(WC()->cart->get_cart()) : 0;
$is_logged = is_user_logged_in();
$logo_url = 'https://solucionesardensa.com/wp-content/uploads/2026/02/Black-White-Modern-Handwritten-Square-Studio-Logo-2-1-e1772134255240.png';
?>

<!-- HEADER DESKTOP (600px+) -->
<header class="header-desktop">
    <div class="header-desktop-inner">
        <!-- Logo + Inicio (izquierda) -->
        <div class="header-desktop-left">
            <a href="<?php echo home_url(); ?>" class="header-desktop-logo">
                <img src="<?php echo $logo_url; ?>" alt="Soluciones Ardensa">
            </a>
            <a href="<?php echo home_url(); ?>" class="header-desktop-inicio">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z"/></svg>
                <span>Inicio</span>
            </a>
        </div>

        <!-- Buscador (centro) -->
        <div class="header-desktop-search">
            <form role="search" method="get" action="<?php echo home_url('/'); ?>">
                <input type="text" name="s" placeholder="Buscar productos..." value="<?php echo get_search_query(); ?>">
                <input type="hidden" name="post_type" value="product">
                <button type="submit">🔍</button>
            </form>
        </div>

        <!-- Navegación (derecha) -->
        <nav class="header-desktop-nav">
            <a href="<?php echo home_url('/contenido/'); ?>" class="header-desktop-nav-item">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M17 1.01L7 1c-1.1 0-2 .9-2 2v18c0 1.1.9 2 2 2h10c1.1 0 2-.9 2-2V3c0-1.1-.9-1.99-2-1.99zM17 19H7V5h10v14z"/></svg>
                <span>Contenido</span>
            </a>
            <a href="<?php echo home_url('/mi-cuenta/'); ?>" class="header-desktop-nav-item">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>
                <span><?php echo $is_logged ? 'Mi cuenta' : 'Ingresar'; ?></span>
            </a>
            <a href="<?php echo home_url('/carrito/'); ?>" class="header-desktop-nav-item">
                <span style="position:relative;">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M7 18c-1.1 0-1.99.9-1.99 2S5.9 22 7 22s2-.9 2-2-.9-2-2-2zM1 2v2h2l3.6 7.59-1.35 2.45c-.16.28-.25.61-.25.96 0 1.1.9 2 2 2h12v-2H7.42c-.14 0-.25-.11-.25-.25l.03-.12.9-1.63h7.45c.75 0 1.41-.41 1.75-1.03l3.58-6.49c.08-.14.12-.31.12-.48 0-.55-.45-1-1-1H5.21l-.94-2H1zm16 16c-1.1 0-1.99.9-1.99 2s.89 2 1.99 2 2-.9 2-2-.9-2-2-2z"/></svg>
                    <span class="cart-badge-desktop" id="cart-badge-desktop" style="<?php echo $cart_count > 0 ? 'display:flex;' : 'display:none;'; ?>"><?php echo $cart_count; ?></span>
                </span>
                <span>Cotización</span>
            </a>
        </nav>
    </div>
</header>

<!-- HEADER MOBILE (<600px) -->
<header class="header-app">
    <div class="header-top">
        <a href="<?php echo home_url(); ?>" class="logo-tienda">
            <img src="<?php echo $logo_url; ?>" alt="Soluciones Ardensa" class="logo-img">
        </a>
    </div>
    <div class="search-bar">
        <form role="search" method="get" action="<?php echo home_url('/'); ?>" style="display:flex; width:100%;">
            <input type="text" name="s" placeholder="Buscar productos..." value="<?php echo get_search_query(); ?>">
            <input type="hidden" name="post_type" value="product">
            <button type="submit">🔍</button>
        </form>
    </div>
</header>

<!-- Notificación -->
<div id="cart-notification" style="display:none; position:fixed; top:0; left:0; right:0; z-index:9999; padding:12px 16px; text-align:center; font-size:14px; color:#fff; font-weight:bold;">
    <span id="cart-notification-text"></span>
</div>

<main class="main-content">