<?php
/**
 * Template Name: Carrito Personalizado
 */

get_header();

if (!function_exists('WC') || !WC()->cart) {
    echo '<p style="padding:20px;">Error al cargar</p>';
    get_footer();
    return;
}

$is_logged = is_user_logged_in();
$current_user = $is_logged ? wp_get_current_user() : null;

if (WC()->cart->is_empty()) :
?>

<div class="cart-empty">
    <div class="cart-empty-icon">📋</div>
    <h2>Tu lista de cotización está vacía</h2>
    <p>Agrega productos para solicitar una cotización</p>
    <a href="<?php echo home_url(); ?>" class="btn-continue-shopping">
        Explorar productos
    </a>
</div>

<?php else : ?>

<div class="cart-page">
    
    <div class="cart-header">
        <h1>📋 Lista de cotización</h1>
        <span class="cart-item-count"><?php echo count(WC()->cart->get_cart()); ?> producto<?php echo count(WC()->cart->get_cart()) > 1 ? 's' : ''; ?></span>
    </div>
    
    <div class="cart-layout">
        
        <!-- Columna: Productos -->
        <div class="cart-col-products">
            <div class="cart-items">
                <?php
                foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item) :
                    $product = $cart_item['data'];
                    $product_id = $cart_item['product_id'];
                    $quantity = $cart_item['quantity'];
                    $product_name = $product->get_name();
                    $product_url = $product->get_permalink();
                ?>
                
                <div class="cart-item" id="cart-item-<?php echo $cart_item_key; ?>">
                    <a href="<?php echo $product_url; ?>" class="cart-item-image">
                        <?php if (has_post_thumbnail($product_id)) : ?>
                            <?php echo $product->get_image('thumbnail'); ?>
                        <?php else : ?>
                            <div class="cart-item-no-image">📷</div>
                        <?php endif; ?>
                    </a>
                    
                    <div class="cart-item-details">
                        <div class="cart-item-info">
                            <a href="<?php echo $product_url; ?>" class="cart-item-name">
                                <?php echo $product_name; ?>
                            </a>
                            
                            <?php if (!empty($cart_item['variation'])) : ?>
                                <div class="cart-item-variation">
                                    <?php
                                    foreach ($cart_item['variation'] as $attr_name => $attr_value) :
                                        if (empty($attr_value)) continue;
                                        $label = wc_attribute_label(str_replace('attribute_', '', $attr_name));
                                    ?>
                                        <span><?php echo $label; ?>: <?php echo $attr_value; ?></span>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="cart-item-qty">
                            <div class="qty-inline-selector qty-small" data-product-id="<?php echo $product_id; ?>" data-cart-key="<?php echo $cart_item_key; ?>">
                                <button type="button" class="qty-inline-btn" onclick="cambiarCantidadCarritoInline(this,-1)">−</button>
                                <span class="qty-inline-value"><?php echo $quantity; ?></span>
                                <button type="button" class="qty-inline-btn" onclick="cambiarCantidadCarritoInline(this,1)">+</button>
                            </div>
                        </div>
                    </div>
                </div>
                
                <?php endforeach; ?>
            </div>
        </div>
        
        <!-- Columna: Resumen -->
        <div class="cart-col-summary">
            <div class="cart-summary">
                <h3>Resumen de cotización</h3>
                
                <div class="cart-summary-row">
                    <span>Total de productos</span>
                    <span id="summary-count"><?php echo count(WC()->cart->get_cart()); ?></span>
                </div>
                
                <div class="quote-note">
                    <p>📌 Al enviar tu cotización, nuestro equipo te contactará con precios y disponibilidad.</p>
                </div>
                
                <div class="quote-form">
                    
                    <?php if (!$is_logged) : ?>
                    <h4>Datos para la cotización</h4>
                    <div class="quote-fields">
                        <input type="text" id="quote-name" placeholder="Tu nombre completo">
                        <input type="email" id="quote-email" placeholder="Tu correo electrónico">
                        <input type="tel" id="quote-phone" placeholder="Tu teléfono (WhatsApp)">
                    </div>
                    <?php else : ?>
                    <input type="hidden" id="quote-name" value="<?php echo esc_attr($current_user->display_name); ?>">
                    <input type="hidden" id="quote-email" value="<?php echo esc_attr($current_user->user_email); ?>">
                    <input type="hidden" id="quote-phone" value="<?php echo esc_attr(get_user_meta($current_user->ID, 'billing_phone', true)); ?>">
                    <?php endif; ?>
                    
                    <textarea id="quote-notes" class="quote-textarea" placeholder="Notas adicionales (opcional)" rows="3"></textarea>
                    
                    <!-- Método de contacto -->
                    <div style="text-align:center; margin-bottom:16px; padding:12px 0; border-top:1px solid #e0e0e0;">
                        <p style="font-size:12px; font-weight:bold; margin-bottom:10px;">¿Cómo prefieres que te contactemos?</p>
                        <div style="display:flex; justify-content:center; gap:10px;">
                            <label id="method-whatsapp" onclick="seleccionarMetodo('whatsapp')" style="display:flex; flex-direction:column; align-items:center; gap:4px; padding:8px 0; border:2px solid #cc2229; border-radius:8px; cursor:pointer; background:#fff5f5; width:80px;">
                                <input type="radio" name="contact_method" value="whatsapp" checked style="display:none;">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="#25D366"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                                <span style="font-size:10px; font-weight:bold;">WhatsApp</span>
                            </label>
                            <label id="method-email" onclick="seleccionarMetodo('email')" style="display:flex; flex-direction:column; align-items:center; gap:4px; padding:8px 0; border:2px solid #e0e0e0; border-radius:8px; cursor:pointer; background:#ffffff; width:80px;">
                                <input type="radio" name="contact_method" value="email" style="display:none;">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="#EA4335"><path d="M20 4H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"/></svg>
                                <span style="font-size:10px; font-weight:bold;">Correo</span>
                            </label>
                        </div>
                    </div>
                    
                    <button type="button" class="btn-checkout" onclick="enviarCotizacion()">
                        📩 Solicitar cotización
                    </button>
                    
                </div>
                
                <a href="<?php echo home_url(); ?>" class="btn-continue-shopping-small">
                    Seguir explorando
                </a>
            </div>
        </div>
        
    </div>
    
</div>

<!-- Modal de éxito -->
<div class="modal-overlay" id="modalExito" style="display:none;">
    <div class="modal-content">
        <div class="modal-icon">✅</div>
        <h2 class="modal-title">¡Pedido realizado con éxito!</h2>
        <p class="modal-text">Hemos recibido tu cotización. Nuestro equipo te contactará pronto.</p>
        <div class="modal-actions">
            <button type="button" onclick="abrirWhatsApp()" class="modal-btn modal-btn-whatsapp">
                💬 Consultar por WhatsApp
            </button>
            <a href="<?php echo home_url(); ?>" class="modal-btn modal-btn-continue">
                🛒 Seguir comprando
            </a>
        </div>
    </div>
</div>

<?php endif; ?>

<?php get_footer(); ?>