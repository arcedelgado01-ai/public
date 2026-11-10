<?php
/**
 * Funciones del tema Mi Tienda App
 * Versión 6.2 - Soluciones Ardensa
 */

// ========================================
// CARGAR RECURSOS
// ========================================
function mitienda_cargar_recursos() {
    wp_enqueue_style('mitienda-estilos', get_stylesheet_uri(), array(), '7.0');
wp_enqueue_script('mitienda-scripts', get_template_directory_uri() . '/js/app.js', array('jquery'), '7.0', true);
    wp_localize_script('mitienda-scripts', 'mitienda_ajax', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce'    => wp_create_nonce('mitienda_nonce'),
    ));
}
add_action('wp_enqueue_scripts', 'mitienda_cargar_recursos');
// ========================================
// SETUP DEL TEMA
// ========================================
function mitienda_setup() {
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('woocommerce');
    register_nav_menus(array(
        'menu-principal' => 'Menú Principal',
    ));
}
add_action('after_setup_theme', 'mitienda_setup');

// ========================================
// LIMPIAR HEAD
// ========================================
function mitienda_limpiar_head() {
    remove_action('wp_head', 'wp_generator');
}
add_action('init', 'mitienda_limpiar_head');

// ========================================
// QUITAR ESTILOS DE WOOCOMMERCE
// ========================================
function mitienda_quitar_estilos_woo() {
    wp_dequeue_style('woocommerce-general');
    wp_dequeue_style('woocommerce-layout');
    wp_dequeue_style('woocommerce-smallscreen');
    wp_dequeue_style('wc-blocks-style');
    wp_dequeue_style('wc-blocks-vendors-style');
}
add_action('wp_enqueue_scripts', 'mitienda_quitar_estilos_woo', 99);

add_filter('show_admin_bar', '__return_false');

// ========================================
// NO CACHEAR NINGUNA PÁGINA
// ========================================
function mitienda_no_cache_paginas() {
    if (!is_admin()) {
        define('DONOTCACHEPAGE', true);
        nocache_headers();
        header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
        header('Pragma: no-cache');
        header('Expires: Thu, 01 Jan 1970 00:00:00 GMT');
    }
}
add_action('send_headers', 'mitienda_no_cache_paginas');

// ========================================
// PERMITIR AGREGAR PRODUCTOS SIN PRECIO
// ========================================
add_filter('woocommerce_is_purchasable', '__return_true');

add_filter('woocommerce_product_get_price', 'mitienda_precio_cotizacion', 10, 2);
add_filter('woocommerce_product_get_regular_price', 'mitienda_precio_cotizacion', 10, 2);
function mitienda_precio_cotizacion($price, $product) {
    if (empty($price) || $price == '') {
        return 0;
    }
    return $price;
}

add_filter('woocommerce_get_price_html', '__return_empty_string');
add_filter('woocommerce_cart_item_price', '__return_empty_string');
add_filter('woocommerce_cart_item_subtotal', '__return_empty_string');
add_filter('woocommerce_cart_subtotal', '__return_empty_string');
add_filter('woocommerce_cart_total', '__return_empty_string');

// ========================================
// AJAX: Agregar producto a cotización
// ========================================
function mitienda_agregar_al_carrito() {
    check_ajax_referer('mitienda_nonce', 'nonce');
    $product_id = intval($_POST['product_id']);
    $quantity = isset($_POST['quantity']) ? intval($_POST['quantity']) : 1;
    if ($product_id <= 0) wp_send_json_error(array('message' => 'Producto no válido'));
    $product = wc_get_product($product_id);
    if (!$product) wp_send_json_error(array('message' => 'Producto no encontrado'));

    if ($product->is_type('variable')) {
        $variations = $product->get_available_variations();
        $added = false;
        foreach ($variations as $variation) {
            $variation_obj = wc_get_product($variation['variation_id']);
            if ($variation_obj && $variation_obj->is_in_stock()) {
                $added = WC()->cart->add_to_cart($product_id, $quantity, $variation['variation_id'], $variation['attributes']);
                break;
            }
        }
        if (!$added) {
            wp_send_json_error(array('message' => 'Selecciona las opciones', 'redirect' => get_permalink($product_id)));
        }
    } elseif ($product->is_type('grouped')) {
        wp_send_json_error(array('message' => 'Ver opciones', 'redirect' => get_permalink($product_id)));
    } else {
        $added = WC()->cart->add_to_cart($product_id, $quantity);
    }

    if ($added) {
        wp_send_json_success(array('message' => '¡Agregado a cotización!', 'cart_count' => count(WC()->cart->get_cart())));
    } else {
        wp_send_json_error(array('message' => 'No se pudo agregar'));
    }
}
add_action('wp_ajax_mitienda_add_to_cart', 'mitienda_agregar_al_carrito');
add_action('wp_ajax_nopriv_mitienda_add_to_cart', 'mitienda_agregar_al_carrito');

// ========================================
// AJAX: Actualizar cantidad en cotización
// ========================================
function mitienda_actualizar_cantidad_carrito() {
    check_ajax_referer('mitienda_nonce', 'nonce');
    $cart_item_key = sanitize_text_field($_POST['cart_item_key']);
    $quantity = intval($_POST['quantity']);
    if ($quantity <= 0) {
        WC()->cart->remove_cart_item($cart_item_key);
    } else {
        WC()->cart->set_quantity($cart_item_key, $quantity);
    }
    wp_send_json_success(array(
        'message'    => 'Lista actualizada',
        'cart_count' => count(WC()->cart->get_cart()),
        'is_empty'   => WC()->cart->is_empty(),
    ));
}
add_action('wp_ajax_mitienda_update_cart', 'mitienda_actualizar_cantidad_carrito');
add_action('wp_ajax_nopriv_mitienda_update_cart', 'mitienda_actualizar_cantidad_carrito');

// ========================================
// AJAX: Eliminar producto de cotización
// ========================================
function mitienda_eliminar_del_carrito() {
    check_ajax_referer('mitienda_nonce', 'nonce');
    $cart_item_key = sanitize_text_field($_POST['cart_item_key']);
    WC()->cart->remove_cart_item($cart_item_key);
    wp_send_json_success(array(
        'message'    => 'Producto eliminado',
        'cart_count' => count(WC()->cart->get_cart()),
        'is_empty'   => WC()->cart->is_empty(),
    ));
}
add_action('wp_ajax_mitienda_remove_from_cart', 'mitienda_eliminar_del_carrito');
add_action('wp_ajax_nopriv_mitienda_remove_from_cart', 'mitienda_eliminar_del_carrito');

// ========================================
// AJAX: Actualizar cantidad por ID de producto
// ========================================
function mitienda_actualizar_por_producto() {
    check_ajax_referer('mitienda_nonce', 'nonce');
    $product_id = intval($_POST['product_id']);
    $quantity = intval($_POST['quantity']);
    foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item) {
        if ($cart_item['product_id'] == $product_id) {
            WC()->cart->set_quantity($cart_item_key, $quantity);
            break;
        }
    }
    wp_send_json_success(array('message' => 'Cantidad actualizada', 'cart_count' => count(WC()->cart->get_cart())));
}
add_action('wp_ajax_mitienda_update_cart_by_product', 'mitienda_actualizar_por_producto');
add_action('wp_ajax_nopriv_mitienda_update_cart_by_product', 'mitienda_actualizar_por_producto');

// ========================================
// AJAX: Eliminar del carrito por ID de producto
// ========================================
function mitienda_eliminar_por_producto() {
    check_ajax_referer('mitienda_nonce', 'nonce');
    $product_id = intval($_POST['product_id']);
    foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item) {
        if ($cart_item['product_id'] == $product_id) {
            WC()->cart->remove_cart_item($cart_item_key);
            break;
        }
    }
    wp_send_json_success(array('message' => 'Producto eliminado', 'cart_count' => count(WC()->cart->get_cart()), 'is_empty' => WC()->cart->is_empty()));
}
add_action('wp_ajax_mitienda_remove_by_product', 'mitienda_eliminar_por_producto');
add_action('wp_ajax_nopriv_mitienda_remove_by_product', 'mitienda_eliminar_por_producto');

// ========================================
// AJAX: Crear pedido de cotización
// ========================================
function mitienda_crear_pedido_cotizacion() {
    check_ajax_referer('mitienda_nonce', 'nonce');

    $name = sanitize_text_field($_POST['name']);
    $email = sanitize_email($_POST['email']);
    $phone = sanitize_text_field($_POST['phone']);
    $notes = sanitize_textarea_field($_POST['notes']);
    $method = sanitize_text_field($_POST['method']);

    $order = wc_create_order();

    foreach (WC()->cart->get_cart() as $cart_item) {
        $order->add_product(
            wc_get_product($cart_item['product_id']),
            $cart_item['quantity']
        );
    }

    $order->set_billing_first_name($name);
    $order->set_billing_email($email);
    $order->set_billing_phone($phone);

    if (is_user_logged_in()) {
        $order->set_customer_id(get_current_user_id());
    }

    $nota = 'Método de contacto preferido: ' . ($method === 'whatsapp' ? 'WhatsApp' : 'Correo electrónico');
    if ($notes) $nota .= "\nNotas del cliente: " . $notes;
    $order->add_order_note($nota);
    $order->set_customer_note($notes);

    $order->set_status('pending', 'Cotización recibida');
    $order->save();

    WC()->cart->empty_cart();

    wp_send_json_success(array(
        'message'  => 'Pedido creado',
        'order_id' => $order->get_id(),
    ));
}
add_action('wp_ajax_mitienda_crear_pedido', 'mitienda_crear_pedido_cotizacion');
add_action('wp_ajax_nopriv_mitienda_crear_pedido', 'mitienda_crear_pedido_cotizacion');

// ========================================
// FORZAR PLANTILLAS PERSONALIZADAS
// ========================================
function mitienda_plantillas_personalizadas($template) {
    if (is_page('carrito') || is_cart()) {
        return get_template_directory() . '/page-carrito.php';
    }
    if (is_product()) {
        $custom = get_template_directory() . '/woocommerce/single-product.php';
        if (file_exists($custom)) return $custom;
    }
    if (is_page('contenido')) {
        return get_template_directory() . '/page-feed.php';
    }
    if (is_account_page()) {
        return get_template_directory() . '/page-mi-cuenta.php';
    }
    return $template;
}
add_filter('template_include', 'mitienda_plantillas_personalizadas', 999);

// ========================================
// REGISTRAR PLANTILLAS DE PÁGINA
// ========================================
function mitienda_registrar_plantillas($templates) {
    $templates['page-carrito.php'] = 'Carrito Personalizado';
    $templates['page-feed.php'] = 'Feed';
    $templates['page-mi-cuenta.php'] = 'Mi Cuenta Personalizada';
    return $templates;
}
add_filter('theme_page_templates', 'mitienda_registrar_plantillas');

// ========================================
// FUNCIÓN: Obtener cantidad en carrito
// ========================================
function mitienda_cantidad_en_carrito($product_id) {
    if (!WC()->cart) return 0;
    foreach (WC()->cart->get_cart() as $cart_item) {
        if ($cart_item['product_id'] == $product_id) {
            return $cart_item['quantity'];
        }
    }
    return 0;
}

// ========================================
// DESACTIVAR CHECKOUT
// ========================================
function mitienda_desactivar_checkout() {
    if (is_checkout()) {
        wp_redirect(home_url('/carrito/'));
        exit;
    }
}
add_action('template_redirect', 'mitienda_desactivar_checkout');

add_filter('woocommerce_cart_needs_payment', '__return_false');
add_filter('woocommerce_cart_needs_shipping', '__return_false');

// ========================================
// PEDIDOS SOLO PARA USUARIOS AUTENTICADOS
// ========================================
function mitienda_proteger_pedidos() {
    if (is_wc_endpoint_url('orders') || is_wc_endpoint_url('view-order')) {
        if (!is_user_logged_in()) {
            wp_redirect(get_permalink(wc_get_page_id('myaccount')));
            exit;
        }
    }
}
add_action('template_redirect', 'mitienda_proteger_pedidos');

// ========================================
// ELIMINAR CONTENIDO DE WOOCOMMERCE EN CARRITO
// ========================================
add_filter('the_content', function($content) {
    if (is_cart()) {
        return '';
    }
    return $content;
});
?>