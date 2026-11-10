<?php
/**
 * Categorías - Catálogo con persistencia de carrito
 */

get_header();

global $wp_query;

$current_term = get_queried_object();

$current_term = get_queried_object();
?>


<div style="background:#ffffff; padding:15px 12px; border-bottom:1px solid #e0e0e0;">
    <?php if (is_product_category()) : ?>
        <h1 style="font-size:20px; margin:0;"><?php echo esc_html($current_term->name); ?></h1>
        <?php if ($current_term->description) : ?>
            <p style="font-size:13px; color:#565959; margin-top:4px;"><?php echo esc_html($current_term->description); ?></p>
        <?php endif; ?>
    <?php else : ?>
        <h1 style="font-size:20px; margin:0;">Todos los productos</h1>
    <?php endif; ?>
    <p style="font-size:12px; color:#565959; margin-top:4px;">
        <?php echo $wp_query->found_posts; ?> productos
    </p>
</div>

<pre style="background:#000;color:#0f0;padding:20px;">
FOUND POSTS:
<?php echo $wp_query->found_posts; ?>

POSTS PER PAGE:
<?php echo $wp_query->query_vars['posts_per_page']; ?>

MAX PAGES:
<?php echo $wp_query->max_num_pages; ?>
</pre>

<div class="products-grid">
    <?php if (have_posts()) : ?>
        <?php while (have_posts()) : the_post();
            global $product;
            if (!$product) continue;
            $producto_id = get_the_ID();
            $producto_url = get_the_permalink();
            $en_stock = $product->is_in_stock();
        ?>
        
        <div class="product-card">
            <div onclick="window.location.href='<?php echo $producto_url; ?>'" style="cursor:pointer;">
                <div class="product-image product-image-wrapper">
                    <?php if (!$en_stock) : ?>
                        <div class="badge-agotado"><span>No disponible</span></div>
                    <?php endif; ?>
                    <?php if (has_post_thumbnail()) : ?>
                        <?php the_post_thumbnail('medium', array('style' => 'width:100%;height:150px;object-fit:contain;')); ?>
                    <?php else : ?>
                        <div style="width:100%;height:150px;background:#f5f5f5;display:flex;align-items:center;justify-content:center;font-size:40px;">📷</div>
                    <?php endif; ?>
                </div>
                <div class="product-info">
                    <h3 class="product-title"><?php the_title(); ?></h3>
                    <?php if ($product->get_short_description()) : ?>
                        <p class="product-excerpt"><?php echo wp_trim_words($product->get_short_description(), 8); ?></p>
                    <?php endif; ?>
                </div>
            </div>
            <div style="padding: 0 10px 10px; margin-top:auto;">
                <?php if (!$en_stock) : ?>
                    <div class="btn-agotado">No disponible</div>
                <?php else :
                    $qty_en_carrito = mitienda_cantidad_en_carrito($producto_id);
                    if ($qty_en_carrito > 0) : ?>
                        <div class="qty-inline-selector" data-product-id="<?php echo $producto_id; ?>">
                            <button type="button" class="qty-inline-btn qty-minus" onclick="cambiarCantidadInline(this, -1)">−</button>
                            <span class="qty-inline-value"><?php echo $qty_en_carrito; ?></span>
                            <button type="button" class="qty-inline-btn qty-plus" onclick="cambiarCantidadInline(this, 1)">+</button>
                        </div>
                    <?php else : ?>
                        <button type="button" class="btn-add-cart" data-product-id="<?php echo $producto_id; ?>" onclick="agregarAlCarrito(this); return false;">
                            Agregar a cotización
                        </button>
                    <?php endif;
                endif; ?>
            </div>
        </div>
        
        <?php endwhile; ?>
    <?php else : ?>
        <div style="padding:40px 20px; text-align:center; grid-column: 1/-1;">
            <p style="font-size:16px;">No se encontraron productos.</p>
        </div>
    <?php endif; ?>
</div>

<pre style="background:#fff;color:#000;padding:20px;">
<?php
echo "Productos encontrados: ";
echo $wp_query->found_posts;

echo "\nProductos por página: ";
echo $wp_query->query_vars['posts_per_page'];

echo "\nNúmero de páginas: ";
echo $wp_query->max_num_pages;
?>
</pre>

<?php if ($wp_query->max_num_pages > 1) : ?>

<div class="category-pagination">

    <?php

    echo paginate_links(array(
        'current'  => max(1, get_query_var('paged')),
        'total'    => $wp_query->max_num_pages,
        'mid_size' => 1,
        'prev_text'=> '←',
        'next_text'=> '→',
    ));

    ?>

</div>

<?php endif; ?>

<?php get_footer(); ?>