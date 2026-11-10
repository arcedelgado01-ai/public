</main>

<?php
$cart_count = WC()->cart ? count(WC()->cart->get_cart()) : 0;
$current_page = '';
if (is_front_page()) $current_page = 'inicio';
elseif (is_page('carrito') || is_cart()) $current_page = 'carrito';
elseif (is_page('contenido')) $current_page = 'contenido';
elseif (is_page('mi-cuenta') || is_account_page()) $current_page = 'cuenta';
?>

<!-- NAVEGACIÓN MOBILE (<600px) -->
<nav class="bottom-nav">
    <a href="<?php echo home_url(); ?>" class="nav-item <?php echo $current_page === 'inicio' ? 'active' : ''; ?>">
        <span class="nav-icon">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="currentColor"><path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z"/></svg>
        </span>
        <span>Inicio</span>
    </a>
    <a href="<?php echo home_url('/contenido/'); ?>" class="nav-item <?php echo $current_page === 'contenido' ? 'active' : ''; ?>">
        <span class="nav-icon">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="currentColor"><path d="M17 1.01L7 1c-1.1 0-2 .9-2 2v18c0 1.1.9 2 2 2h10c1.1 0 2-.9 2-2V3c0-1.1-.9-1.99-2-1.99zM17 19H7V5h10v14z"/></svg>
        </span>
        <span>Contenido</span>
    </a>
    <a href="<?php echo home_url('/carrito/'); ?>" class="nav-item <?php echo $current_page === 'carrito' ? 'active' : ''; ?>">
        <span class="nav-icon" style="position:relative;">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="currentColor"><path d="M7 18c-1.1 0-1.99.9-1.99 2S5.9 22 7 22s2-.9 2-2-.9-2-2-2zM1 2v2h2l3.6 7.59-1.35 2.45c-.16.28-.25.61-.25.96 0 1.1.9 2 2 2h12v-2H7.42c-.14 0-.25-.11-.25-.25l.03-.12.9-1.63h7.45c.75 0 1.41-.41 1.75-1.03l3.58-6.49c.08-.14.12-.31.12-.48 0-.55-.45-1-1-1H5.21l-.94-2H1zm16 16c-1.1 0-1.99.9-1.99 2s.89 2 1.99 2 2-.9 2-2-.9-2-2-2z"/></svg>
            <span class="cart-badge" id="cart-badge" style="<?php echo $cart_count > 0 ? 'display:flex;' : 'display:none;'; ?>"><?php echo $cart_count; ?></span>
        </span>
        <span>Cotizacion</span>
    </a>
    <a href="<?php echo home_url('/mi-cuenta/'); ?>" class="nav-item <?php echo $current_page === 'cuenta' ? 'active' : ''; ?>">
        <span class="nav-icon">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="currentColor"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>
        </span>
        <span>Mi cuenta</span>
    </a>
</nav>



<?php wp_footer(); ?>
</body>
</html>