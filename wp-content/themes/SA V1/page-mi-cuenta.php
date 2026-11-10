<?php
/**
 * Template Name: Mi Cuenta Personalizada
 */

get_header();

$is_logged = is_user_logged_in();
$cart_count = WC()->cart ? count(WC()->cart->get_cart()) : 0;

$vista = 'login';
if ($is_logged) {
    $vista = 'perfil';
    if (isset($_GET['vista'])) {
        $vista = sanitize_text_field($_GET['vista']);
    }
} else {
    if (isset($_GET['vista'])) {
        $vista = sanitize_text_field($_GET['vista']);
    }
}
?>

<div class="account-page">

    <?php if ($vista === 'perfil') :
        $current_user = wp_get_current_user();
    ?>

    <!-- PERFIL -->
    <div style="width:100%; background:#ffffff; padding:24px 16px; text-align:center; border-bottom:1px solid #f0f0f0;">
        <h2 style="font-size:18px; margin-bottom:2px;"><?php echo $current_user->display_name; ?></h2>
        <p style="font-size:13px; color:#565959;"><?php echo $current_user->user_email; ?></p>
    </div>

    <div style="width:100%; background:#ffffff; margin-top:8px;">
        <a href="?vista=editar-perfil" style="display:flex; align-items:center; padding:14px 16px; border-bottom:1px solid #f5f5f5; color:#0f1111; text-decoration:none;">
            <span style="flex:1; font-size:15px;">Editar perfil</span>
            <span style="font-size:18px; color:#cccccc;">›</span>
        </a>
        <a href="?vista=pedidos" style="display:flex; align-items:center; padding:14px 16px; border-bottom:1px solid #f5f5f5; color:#0f1111; text-decoration:none;">
            <span style="flex:1; font-size:15px;">Mis pedidos</span>
            <span style="font-size:18px; color:#cccccc;">›</span>
        </a>
        <a href="/carrito/" style="display:flex; align-items:center; padding:14px 16px; border-bottom:1px solid #f5f5f5; color:#0f1111; text-decoration:none;">
            <span style="flex:1; font-size:15px;">Mi cotización</span>
            <?php if ($cart_count > 0) : ?>
                <span style="background:#cc2229; color:#fff; font-size:11px; font-weight:bold; min-width:20px; height:20px; border-radius:10px; display:flex; align-items:center; justify-content:center; padding:0 6px; margin-right:8px;"><?php echo $cart_count; ?></span>
            <?php endif; ?>
            <span style="font-size:18px; color:#cccccc;">›</span>
        </a>
        <a href="?vista=cambiar-password" style="display:flex; align-items:center; padding:14px 16px; border-bottom:1px solid #f5f5f5; color:#0f1111; text-decoration:none;">
            <span style="flex:1; font-size:15px;">Cambiar contraseña</span>
            <span style="font-size:18px; color:#cccccc;">›</span>
        </a>
        <a href="<?php echo wp_logout_url(home_url('/mi-cuenta/')); ?>" style="display:flex; align-items:center; padding:14px 16px; border-bottom:1px solid #f5f5f5; text-decoration:none;">
            <span style="flex:1; font-size:15px; color:#cc2229;">Cerrar sesión</span>
            <span style="font-size:18px; color:#cccccc;">›</span>
        </a>
    </div>

    <!-- Contacto y Síguenos - Solo en vista perfil -->
    <div class="account-contact-social">
        <div class="account-contact-col">
            <h3>Contacto</h3>
            <div class="account-contact-icons">
                <a href="tel:+521234567890">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="#25D366"><path d="M6.62 10.79c1.44 2.83 3.76 5.14 6.59 6.59l2.2-2.2c.27-.27.67-.36 1.02-.24 1.12.37 2.33.57 3.57.57.55 0 1 .45 1 1V20c0 .55-.45 1-1 1-9.39 0-17-7.61-17-17 0-.55.45-1 1-1h3.5c.55 0 1 .45 1 1 0 1.25.2 2.45.57 3.57.11.35.03.74-.25 1.02l-2.2 2.2z"/></svg>
                    <span>Llamar</span>
                </a>
                <a href="mailto:info@solucionesardensa.com">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="#EA4335"><path d="M20 4H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"/></svg>
                    <span>Correo</span>
                </a>
                <a href="https://wa.me/521234567890" target="_blank">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="#25D366"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                    <span>WhatsApp</span>
                </a>
            </div>
        </div>
        <div class="account-social-col">
            <h3>Síguenos</h3>
            <div class="account-contact-icons">
                <a href="https://facebook.com/solucionesardensa" target="_blank">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="#1877F2"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                    <span>Facebook</span>
                </a>
                <a href="https://instagram.com/solucionesardensa" target="_blank">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="#E4405F"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/></svg>
                    <span>Instagram</span>
                </a>
                <a href="https://tiktok.com/@solucionesardensa" target="_blank">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="#000000"><path d="M12.525.02c1.31-.02 2.61-.01 3.91-.02.08 1.53.63 3.09 1.75 4.17 1.12 1.11 2.7 1.62 4.24 1.79v4.03c-1.44-.05-2.89-.35-4.2-.97-.57-.26-1.1-.59-1.62-.93-.01 2.92.01 5.84-.02 8.75-.08 1.4-.54 2.79-1.35 3.94-1.31 1.92-3.58 3.17-5.91 3.21-1.43.08-2.86-.31-4.08-1.03-2.02-1.19-3.44-3.37-3.65-5.71-.02-.5-.03-1-.01-1.49.18-1.9 1.12-3.72 2.58-4.96 1.66-1.44 3.98-2.13 6.15-1.72.02 1.48-.04 2.96-.04 4.44-.99-.32-2.15-.23-3.02.37-.63.41-1.11 1.04-1.36 1.75-.21.51-.15 1.07-.14 1.61.24 1.64 1.82 3.02 3.5 2.87 1.12-.01 2.19-.66 2.77-1.61.19-.33.4-.67.41-1.06.1-1.79.06-3.57.07-5.36.01-4.03-.01-8.05.02-12.07z"/></svg>
                    <span>TikTok</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <div style="padding:24px 16px; text-align:center;">
        <p style="font-size:11px; color:#aaaaaa;">Soluciones Ardensa</p>
    </div>

    <?php elseif ($vista === 'editar-perfil') :
        $current_user = wp_get_current_user();

        if (isset($_POST['guardar_perfil'])) {
            $nombre = sanitize_text_field($_POST['display_name']);
            $telefono = sanitize_text_field($_POST['telefono']);
            wp_update_user(array('ID' => $current_user->ID, 'display_name' => $nombre));
            update_user_meta($current_user->ID, 'billing_phone', $telefono);
            $current_user = wp_get_current_user();
            echo '<div style="background:#4CAF50; color:#fff; padding:12px; text-align:center; font-size:14px; width:100%;">✓ Perfil actualizado</div>';
        }

        $telefono = get_user_meta($current_user->ID, 'billing_phone', true);
    ?>

    <!-- EDITAR PERFIL -->
    <div style="width:100%; background:#ffffff; padding:20px 16px;">
        <a href="?vista=perfil" style="font-size:14px; color:#007185; text-decoration:none;">← Volver</a>
        <h2 style="font-size:18px; margin:16px 0;">Editar perfil</h2>

        <form method="post" style="max-width:320px; margin:0 auto;">
            <div style="margin-bottom:12px;">
                <label style="font-size:13px; color:#565959; display:block; margin-bottom:4px;">Nombre</label>
                <input type="text" name="display_name" value="<?php echo esc_attr($current_user->display_name); ?>" style="width:100%; padding:12px; border:1px solid #d5d9d9; border-radius:8px; font-size:14px; box-sizing:border-box;">
            </div>
            <div style="margin-bottom:12px;">
                <label style="font-size:13px; color:#565959; display:block; margin-bottom:4px;">Correo electrónico</label>
                <input type="email" value="<?php echo esc_attr($current_user->user_email); ?>" disabled style="width:100%; padding:12px; border:1px solid #d5d9d9; border-radius:8px; font-size:14px; background:#f5f5f5; box-sizing:border-box;">
            </div>
            <div style="margin-bottom:12px;">
                <label style="font-size:13px; color:#565959; display:block; margin-bottom:4px;">Teléfono</label>
                <input type="tel" name="telefono" value="<?php echo esc_attr($telefono); ?>" placeholder="Tu número de teléfono" style="width:100%; padding:12px; border:1px solid #d5d9d9; border-radius:8px; font-size:14px; box-sizing:border-box;">
            </div>
            <button type="submit" name="guardar_perfil" style="width:100%; padding:13px; background:#ffd814; border:1px solid #fcd200; border-radius:22px; font-size:15px; font-weight:bold; cursor:pointer; color:#0f1111; box-sizing:border-box;">Guardar cambios</button>
        </form>
    </div>

    <?php elseif ($vista === 'cambiar-password') :
        $current_user = wp_get_current_user();

        if (isset($_POST['cambiar_pass'])) {
            $pass_actual = $_POST['pass_actual'];
            $pass_nueva = $_POST['pass_nueva'];
            $pass_confirmar = $_POST['pass_confirmar'];

            if (!wp_check_password($pass_actual, $current_user->user_pass, $current_user->ID)) {
                echo '<div style="background:#f44336; color:#fff; padding:12px; text-align:center; font-size:14px; width:100%;">✗ Contraseña actual incorrecta</div>';
            } elseif ($pass_nueva !== $pass_confirmar) {
                echo '<div style="background:#f44336; color:#fff; padding:12px; text-align:center; font-size:14px; width:100%;">✗ Las contraseñas no coinciden</div>';
            } elseif (strlen($pass_nueva) < 6) {
                echo '<div style="background:#f44336; color:#fff; padding:12px; text-align:center; font-size:14px; width:100%;">✗ Mínimo 6 caracteres</div>';
            } else {
                wp_set_password($pass_nueva, $current_user->ID);
                wp_set_auth_cookie($current_user->ID);
                echo '<div style="background:#4CAF50; color:#fff; padding:12px; text-align:center; font-size:14px; width:100%;">✓ Contraseña actualizada</div>';
            }
        }
    ?>

    <!-- CAMBIAR CONTRASEÑA -->
    <div style="width:100%; background:#ffffff; padding:20px 16px;">
        <a href="?vista=perfil" style="font-size:14px; color:#007185; text-decoration:none;">← Volver</a>
        <h2 style="font-size:18px; margin:16px 0;">Cambiar contraseña</h2>

        <form method="post" style="max-width:320px; margin:0 auto;">
            <div style="margin-bottom:12px;">
                <label style="font-size:13px; color:#565959; display:block; margin-bottom:4px;">Contraseña actual</label>
                <input type="password" name="pass_actual" required style="width:100%; padding:12px; border:1px solid #d5d9d9; border-radius:8px; font-size:14px; box-sizing:border-box;">
            </div>
            <div style="margin-bottom:12px;">
                <label style="font-size:13px; color:#565959; display:block; margin-bottom:4px;">Nueva contraseña</label>
                <input type="password" name="pass_nueva" required style="width:100%; padding:12px; border:1px solid #d5d9d9; border-radius:8px; font-size:14px; box-sizing:border-box;">
            </div>
            <div style="margin-bottom:12px;">
                <label style="font-size:13px; color:#565959; display:block; margin-bottom:4px;">Confirmar nueva contraseña</label>
                <input type="password" name="pass_confirmar" required style="width:100%; padding:12px; border:1px solid #d5d9d9; border-radius:8px; font-size:14px; box-sizing:border-box;">
            </div>
            <button type="submit" name="cambiar_pass" style="width:100%; padding:13px; background:#ffd814; border:1px solid #fcd200; border-radius:22px; font-size:15px; font-weight:bold; cursor:pointer; color:#0f1111; box-sizing:border-box;">Cambiar contraseña</button>
        </form>
    </div>

    <?php elseif ($vista === 'ver-pedido') :
        if (!is_user_logged_in()) {
            wp_redirect(get_permalink(wc_get_page_id('myaccount')));
            exit;
        }
        $current_user = wp_get_current_user();
        $order_id = isset($_GET['pedido']) ? intval($_GET['pedido']) : 0;
        $order = wc_get_order($order_id);

        if (!$order || $order->get_customer_id() !== $current_user->ID) :
    ?>
        <div style="width:100%; background:#ffffff; padding:40px 16px; text-align:center;">
            <div style="font-size:40px; margin-bottom:12px;">⚠️</div>
            <h2 style="font-size:18px; margin-bottom:8px;">Pedido no encontrado</h2>
            <p style="font-size:14px; color:#565959; margin-bottom:20px;">Este pedido no existe o no tienes acceso.</p>
            <a href="?vista=pedidos" style="display:inline-block; padding:10px 24px; background:#ffd814; border-radius:20px; font-size:14px; font-weight:bold; color:#0f1111; text-decoration:none;">← Volver a mis pedidos</a>
        </div>
    <?php else :
        $items = $order->get_items();
        $status = $order->get_status();
        $date = $order->get_date_created()->date('d/m/Y');
        $time = $order->get_date_created()->date('H:i');
        $note = $order->get_customer_note();

        $status_colors = array(
            'pending'    => array('bg' => '#fff3e0', 'color' => '#e65100', 'text' => 'Pendiente'),
            'processing' => array('bg' => '#e3f2fd', 'color' => '#1565c0', 'text' => 'En proceso'),
            'completed'  => array('bg' => '#e8f5e9', 'color' => '#2e7d32', 'text' => 'Completado'),
            'on-hold'    => array('bg' => '#fce4ec', 'color' => '#c62828', 'text' => 'En espera'),
            'cancelled'  => array('bg' => '#f5f5f5', 'color' => '#666666', 'text' => 'Cancelado'),
        );
        $s = isset($status_colors[$status]) ? $status_colors[$status] : array('bg' => '#f5f5f5', 'color' => '#666', 'text' => ucfirst($status));
    ?>

    <!-- VER PEDIDO -->
    <div class="order-detail-page">

        <div class="order-detail-header">
            <a href="?vista=pedidos" class="order-back-link">← Volver a mis pedidos</a>
            <div class="order-detail-title-row">
                <h2 class="order-detail-title">Pedido #<?php echo $order->get_order_number(); ?></h2>
                <span class="order-status-badge" style="background:<?php echo $s['bg']; ?>; color:<?php echo $s['color']; ?>;">
                    <?php echo $s['text']; ?>
                </span>
            </div>
            <p class="order-detail-date">📅 <?php echo $date; ?> a las <?php echo $time; ?></p>
        </div>

        <div class="order-detail-section">
            <h3 class="order-section-title">Productos (<?php echo count($items); ?>)</h3>
            <div class="order-items-list">
                <?php foreach ($items as $item) :
                    $product = $item->get_product();
                    $qty = $item->get_quantity();
                    $name = $item->get_name();
                    $permalink = $product ? $product->get_permalink() : '#';
                    $thumbnail = $product ? $product->get_image('thumbnail') : '';
                ?>
                <div class="order-item">
                    <div class="order-item-image">
                        <?php if ($thumbnail) : ?>
                            <a href="<?php echo $permalink; ?>"><?php echo $thumbnail; ?></a>
                        <?php else : ?>
                            <div class="order-item-no-image">📷</div>
                        <?php endif; ?>
                    </div>
                    <div class="order-item-info">
                        <a href="<?php echo $permalink; ?>" class="order-item-name"><?php echo $name; ?></a>
                        <span class="order-item-qty">Cantidad: <?php echo $qty; ?></span>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="order-detail-section">
            <h3 class="order-section-title">Información</h3>
            <div class="order-info-grid">
                <div class="order-info-item">
                    <span class="order-info-label">Estado</span>
                    <span class="order-info-value" style="color:<?php echo $s['color']; ?>; font-weight:bold;"><?php echo $s['text']; ?></span>
                </div>
                <div class="order-info-item">
                    <span class="order-info-label">Fecha</span>
                    <span class="order-info-value"><?php echo $date; ?></span>
                </div>
                <div class="order-info-item">
                    <span class="order-info-label">Productos</span>
                    <span class="order-info-value"><?php echo $order->get_item_count(); ?> artículo<?php echo $order->get_item_count() > 1 ? 's' : ''; ?></span>
                </div>
                <?php if ($order->get_billing_phone()) : ?>
                <div class="order-info-item">
                    <span class="order-info-label">Teléfono</span>
                    <span class="order-info-value"><?php echo $order->get_billing_phone(); ?></span>
                </div>
                <?php endif; ?>
                <?php if ($order->get_billing_email()) : ?>
                <div class="order-info-item">
                    <span class="order-info-label">Correo</span>
                    <span class="order-info-value"><?php echo $order->get_billing_email(); ?></span>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <?php if ($note) : ?>
        <div class="order-detail-section">
            <h3 class="order-section-title">Notas</h3>
            <div class="order-note-box">
                <p><?php echo esc_html($note); ?></p>
            </div>
        </div>
        <?php endif; ?>

        <div class="order-detail-actions">
            <a href="https://wa.me/521234567890?text=<?php echo urlencode('Hola, tengo una consulta sobre mi pedido #' . $order->get_order_number()); ?>" target="_blank" class="order-action-btn order-action-whatsapp">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                Consultar por WhatsApp
            </a>
            <a href="<?php echo home_url(); ?>" class="order-action-btn order-action-shop">
                Seguir comprando
            </a>
        </div>

    </div>

    <?php endif; ?>

    <?php elseif ($vista === 'pedidos') :
        if (!is_user_logged_in()) {
            wp_redirect(get_permalink(wc_get_page_id('myaccount')));
            exit;
        }
        $current_user = wp_get_current_user();
        $orders = array();
        if ($current_user->ID > 0) {
            $orders = wc_get_orders(array(
                'customer_id' => $current_user->ID,
                'limit'       => 10,
                'orderby'     => 'date',
                'order'       => 'DESC',
                'status'      => array('wc-pending', 'wc-processing', 'wc-completed', 'wc-on-hold'),
            ));
        }
    ?>

    <!-- MIS PEDIDOS -->
    <div style="width:100%; background:#ffffff; padding:20px 16px;">
        <a href="?vista=perfil" style="font-size:14px; color:#007185; text-decoration:none;">← Volver</a>
        <h2 style="font-size:18px; margin:16px 0;">Mis pedidos</h2>

        <?php if (empty($orders)) : ?>
            <div style="text-align:center; padding:40px 20px;">
                <div style="font-size:40px; opacity:0.5; margin-bottom:12px;">📦</div>
                <p style="font-size:14px; color:#565959;">No tienes pedidos aún</p>
                <a href="<?php echo home_url(); ?>" style="display:inline-block; margin-top:16px; padding:10px 24px; background:#ffd814; border-radius:20px; font-size:14px; font-weight:bold; color:#0f1111; text-decoration:none;">Explorar productos</a>
            </div>
        <?php else : ?>
            <?php foreach ($orders as $order) :
                $status = $order->get_status();
                $status_colors = array(
                    'pending'    => array('bg' => '#fff3e0', 'color' => '#e65100'),
                    'processing' => array('bg' => '#e3f2fd', 'color' => '#1565c0'),
                    'completed'  => array('bg' => '#e8f5e9', 'color' => '#2e7d32'),
                    'on-hold'    => array('bg' => '#fce4ec', 'color' => '#c62828'),
                );
                $s = isset($status_colors[$status]) ? $status_colors[$status] : array('bg' => '#f5f5f5', 'color' => '#666');
            ?>
                <a href="?vista=ver-pedido&pedido=<?php echo $order->get_id(); ?>" style="display:block; border:1px solid #e0e0e0; border-radius:8px; padding:14px; margin-bottom:10px; text-decoration:none; color:inherit;">
                    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:8px;">
                        <span style="font-size:14px; font-weight:bold; color:#0f1111;">#<?php echo $order->get_order_number(); ?></span>
                        <span style="font-size:12px; padding:4px 8px; border-radius:4px; background:<?php echo $s['bg']; ?>; color:<?php echo $s['color']; ?>;">
                            <?php echo wc_get_order_status_name($order->get_status()); ?>
                        </span>
                    </div>
                    <p style="font-size:12px; color:#565959; margin-bottom:6px;">
                        <?php echo $order->get_date_created()->date('d/m/Y'); ?>
                    </p>
                    <div style="display:flex; justify-content:space-between; align-items:center;">
                        <p style="font-size:13px; color:#333;">
                            <?php echo $order->get_item_count(); ?> producto<?php echo $order->get_item_count() > 1 ? 's' : ''; ?>
                        </p>
                        <span style="font-size:14px; color:#cccccc;">›</span>
                    </div>
                </a>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <?php elseif ($vista === 'crear-cuenta') : ?>

    <!-- CREAR CUENTA -->
    <div style="width:100%; background:#ffffff; padding:40px 16px; text-align:center;">
        <div style="font-size:40px; margin-bottom:12px;">📧</div>
        <h2 style="font-size:20px; margin-bottom:4px;">Crear cuenta</h2>
        <p style="font-size:14px; color:#565959; margin-bottom:24px;">Ingresa tu correo y te enviaremos la contraseña</p>

        <div style="width:100%; max-width:300px; margin:0 auto;">
            <input type="email" id="registro-email" placeholder="Tu correo electrónico" style="display:block; width:100%; padding:12px 14px; border:1px solid #d5d9d9; border-radius:8px; font-size:14px; outline:none; box-sizing:border-box; margin-bottom:10px;">
            <button type="button" onclick="crearCuenta()" id="btn-crear" style="display:block; width:100%; padding:13px; background:#ffd814; border:1px solid #fcd200; border-radius:22px; font-size:15px; font-weight:bold; cursor:pointer; color:#0f1111; box-sizing:border-box;">Crear cuenta</button>
            <div id="registro-msg" style="margin-top:12px; font-size:13px;"></div>
        </div>

        <a href="<?php echo get_permalink(wc_get_page_id('myaccount')); ?>" style="display:block; margin-top:20px; font-size:14px; color:#007185; text-decoration:none;">
            ← Ya tengo cuenta, iniciar sesión
        </a>
    </div>

    <?php elseif ($vista === 'recuperar') : ?>

    <!-- RECUPERAR CONTRASEÑA -->
    <div style="width:100%; background:#ffffff; padding:40px 16px; text-align:center;">
        <div style="font-size:40px; margin-bottom:12px;">🔑</div>
        <h2 style="font-size:20px; margin-bottom:4px;">Recuperar contraseña</h2>
        <p style="font-size:14px; color:#565959; margin-bottom:24px;">Te enviaremos una nueva contraseña a tu correo</p>

        <div style="width:100%; max-width:300px; margin:0 auto;">
            <input type="email" id="recuperar-email" placeholder="Tu correo electrónico" style="display:block; width:100%; padding:12px 14px; border:1px solid #d5d9d9; border-radius:8px; font-size:14px; outline:none; box-sizing:border-box; margin-bottom:10px;">
            <button type="button" onclick="recuperarPassword()" id="btn-recuperar" style="display:block; width:100%; padding:13px; background:#ffd814; border:1px solid #fcd200; border-radius:22px; font-size:15px; font-weight:bold; cursor:pointer; color:#0f1111; box-sizing:border-box;">Enviar nueva contraseña</button>
            <div id="recuperar-msg" style="margin-top:12px; font-size:13px;"></div>
        </div>

        <a href="<?php echo get_permalink(wc_get_page_id('myaccount')); ?>" style="display:block; margin-top:20px; font-size:14px; color:#007185; text-decoration:none;">
            ← Volver a iniciar sesión
        </a>
    </div>

    <?php else : ?>

    <!-- LOGIN -->
    <div style="width:100%; background:#ffffff; padding:40px 16px; text-align:center;">
        <div style="font-size:50px; margin-bottom:12px; opacity:0.6;">👤</div>
        <h2 style="font-size:20px; margin-bottom:4px;">Bienvenido</h2>
        <p style="font-size:14px; color:#565959; margin-bottom:24px;">Inicia sesión en tu cuenta</p>

        <div style="width:100%; max-width:300px; margin:0 auto;">
            <form method="post" action="<?php echo get_permalink(wc_get_page_id('myaccount')); ?>" style="display:flex; flex-direction:column; gap:10px; width:100%;">
                <input type="text" name="username" placeholder="Correo electrónico" required style="display:block; width:100%; padding:12px 14px; border:1px solid #d5d9d9; border-radius:8px; font-size:14px; outline:none; box-sizing:border-box;">
                <input type="password" name="password" placeholder="Contraseña" required style="display:block; width:100%; padding:12px 14px; border:1px solid #d5d9d9; border-radius:8px; font-size:14px; outline:none; box-sizing:border-box;">
                <?php wp_nonce_field('woocommerce-login', 'woocommerce-login-nonce'); ?>
                <input type="hidden" name="login" value="1">
                <button type="submit" style="display:block; width:100%; padding:13px; background:#ffd814; border:1px solid #fcd200; border-radius:22px; font-size:15px; font-weight:bold; cursor:pointer; color:#0f1111; box-sizing:border-box;">Iniciar sesión</button>
            </form>

            <a href="?vista=recuperar" style="display:block; margin-top:12px; font-size:13px; color:#007185; text-decoration:none;">
                ¿Olvidaste tu contraseña?
            </a>

            <a href="?vista=crear-cuenta" style="display:block; width:100%; padding:12px; margin-top:16px; border:1px solid #d5d9d9; border-radius:22px; font-size:14px; text-align:center; color:#0f1111; text-decoration:none; box-sizing:border-box;">
                Crear cuenta nueva
            </a>
        </div>
    </div>

    <?php endif; ?>

</div>

<?php get_footer(); ?>