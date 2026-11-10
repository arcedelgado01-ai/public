<?php
/**
 * Template para producto individual
 */

get_header();

while (have_posts()) : the_post();
    global $product;
    
    if (!$product) {
        $product = wc_get_product(get_the_ID());
    }
    
    $product_id = $product->get_id();
    $images = $product->get_gallery_image_ids();
    $main_image_id = $product->get_image_id();
    
    // Construir array de imágenes
    $all_images = array();
    if ($main_image_id) {
        $all_images[] = $main_image_id;
    }
    if ($images) {
        $all_images = array_merge($all_images, $images);
    }
    
    $total_images = count($all_images);
    $is_variable = $product->is_type('variable');
    $in_stock = $product->is_in_stock();
    $qty_in_cart = mitienda_cantidad_en_carrito($product_id);
?>

<div class="single-product-page">
    
    <div class="single-product-layout">
        
        <!-- Columna izquierda: Galería -->
        <div class="single-product-col-gallery">
            
            <?php if (!empty($all_images)) : ?>
            <div class="product-gallery" id="product-gallery">
                <div class="gallery-slides" id="gallery-slides">
                    <?php foreach ($all_images as $img_id) :
                        $img_url = wp_get_attachment_image_url($img_id, 'large');
                    ?>
                        <div class="gallery-slide">
                            <img src="<?php echo $img_url; ?>" alt="<?php echo $product->get_name(); ?>">
                        </div>
                    <?php endforeach; ?>
                </div>
                <?php if ($total_images > 1) : ?>
                    <div class="gallery-counter"><span id="gallery-current">1</span> / <?php echo $total_images; ?></div>
                <?php endif; ?>
                
                <?php if (!$in_stock) : ?>
                    <div class="badge-agotado"><span>Agotado</span></div>
                <?php endif; ?>
            </div>
            
            <?php if ($total_images > 1) : ?>
            <div class="gallery-thumbnails">
                <?php foreach ($all_images as $index => $img_id) :
                    $thumb_url = wp_get_attachment_image_url($img_id, 'thumbnail');
                ?>
                    <div class="thumb <?php echo $index === 0 ? 'active' : ''; ?>" onclick="irASlide(<?php echo $index; ?>)">
                        <img src="<?php echo $thumb_url; ?>" alt="">
                    </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
            
            <?php else : ?>
            <div class="product-gallery">
                <div class="gallery-slide">
                    <div class="no-image-placeholder">
                        <span>📷</span>
                        <p>Sin imagen</p>
                    </div>
                </div>
            </div>
            <?php endif; ?>
            
        </div>
        
        <!-- Columna derecha: Detalles -->
        <div class="single-product-col-details">
            
            <div class="product-detail">
                <h1 class="product-detail-title"><?php echo $product->get_name(); ?></h1>
                
                <!-- Stock -->
                <div class="product-detail-stock">
                    <?php if ($in_stock) : ?>
                        <span style="color:var(--color-verde); font-weight:bold;">✓ Disponible</span>
                    <?php else : ?>
                        <span style="color:var(--color-rojo); font-weight:bold;">✗ Agotado</span>
                    <?php endif; ?>
                </div>
                
                <!-- Variaciones -->
                <?php if ($is_variable) :
                    $attributes = $product->get_variation_attributes();
                    if (!empty($attributes)) :
                ?>
                <div class="product-variations">
                    <?php foreach ($attributes as $attr_name => $options) :
                        $label = wc_attribute_label($attr_name);
                    ?>
                    <div class="variation-group">
                        <span class="variation-label"><?php echo $label; ?>:</span>
                        <div class="variation-options">
                            <?php foreach ($options as $option) : ?>
                                <span class="variation-option" data-attribute="<?php echo $attr_name; ?>" data-value="<?php echo $option; ?>" onclick="seleccionarVariacion(this)">
                                    <?php echo $option; ?>
                                </span>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; endif; ?>
                
                <!-- Botón agregar -->
                <?php if ($in_stock) : ?>
                <div class="product-actions">
                    <div style="padding: 12px 0;">
                        <?php if ($qty_in_cart > 0) : ?>
                            <div class="qty-inline-selector" data-product-id="<?php echo $product_id; ?>">
                                <button type="button" class="qty-inline-btn" onclick="cambiarCantidadInline(this,-1)">−</button>
                                <span class="qty-inline-value"><?php echo $qty_in_cart; ?></span>
                                <button type="button" class="qty-inline-btn" onclick="cambiarCantidadInline(this,1)">+</button>
                            </div>
                        <?php else : ?>
                            <button type="button" class="btn-add-cart" data-product-id="<?php echo $product_id; ?>" onclick="agregarAlCarrito(this); return false;">
                                Agregar a cotización
                            </button>
                        <?php endif; ?>
                    </div>
                </div>
                <?php else : ?>
                <div class="product-actions">
                    <button class="btn-agotado" disabled>Producto agotado</button>
                </div>
                <?php endif; ?>
                
                <!-- Descripción corta -->
                <?php
                $short_desc = $product->get_short_description();
                if ($short_desc) :
                ?>
                <div class="product-detail-section">
                    <h3>Descripción</h3>
                    <div class="product-description">
                        <?php echo wp_kses_post($short_desc); ?>
                    </div>
                </div>
                <?php endif; ?>
                
                <!-- Atributos / Especificaciones -->
                <?php
                $attributes_display = $product->get_attributes();
                if (!empty($attributes_display)) :
                ?>
                <div class="product-detail-section">
                    <h3>Especificaciones</h3>
                    <table class="product-attributes">
                        <?php foreach ($attributes_display as $attr) :
                            if ($attr->get_variation()) continue;
                            $name = wc_attribute_label($attr->get_name());
                            $values = $attr->is_taxonomy()
                                ? implode(', ', wc_get_product_terms($product_id, $attr->get_name(), array('fields' => 'names')))
                                : implode(', ', $attr->get_options());
                        ?>
                        <tr>
                            <td class="attr-label"><?php echo $name; ?></td>
                            <td class="attr-value"><?php echo $values; ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </table>
                </div>
                <?php endif; ?>
                
                <!-- Descripción larga -->
                <?php
                $full_desc = $product->get_description();
                if ($full_desc) :
                ?>
                <div class="product-detail-section">
                    <h3>Información adicional</h3>
                    <div class="product-description" id="full-description" style="max-height:150px; overflow:hidden; position:relative;">
                        <?php echo wp_kses_post($full_desc); ?>
                        <div class="description-fade" id="desc-fade"></div>
                    </div>
                    <button class="btn-show-more" id="btn-show-more" onclick="toggleDescripcion()">Ver más ▼</button>
                </div>
                <?php endif; ?>
                
            </div>
            
        </div>
        
    </div>
    
</div>

<?php endwhile; ?>

<?php get_footer(); ?>