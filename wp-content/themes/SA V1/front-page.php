<?php
/**
 * Front Page - Página de inicio
 */

get_header();


// =========================================================
// CATEGORÍAS
// =========================================================
//
// Solo el nivel superior respeta el límite de 10.
// Los descendientes se buscan posteriormente sin límite,
// utilizando siempre la relación real de WooCommerce:
//
//     parent => term_id
//
// Esto conserva la configuración de familias y subcategorías.
//

$categories = get_terms(array(
    'taxonomy'   => 'product_cat',
    'hide_empty' => true,
    'parent'     => 0,
    'number'     => 10,
));


// =========================================================
// PRODUCTOS DESTACADOS
// =========================================================

$featured_products = wc_get_products(array(
    'featured' => true,
    'limit'    => 8,
    'status'   => 'publish',
    'orderby'  => 'rand',
));


// =========================================================
// MÁS VENDIDOS
// =========================================================

$best_sellers = wc_get_products(array(
    'limit'    => 8,
    'status'   => 'publish',
    'orderby'  => 'popularity',
));


// =========================================================
// FUNCIÓN RECURSIVA DE CATEGORÍAS
// =========================================================
//
// Renderiza una familia y sus subcategorías directas.
//
// La relación padre/hijo se determina EXCLUSIVAMENTE mediante:
//
//     'parent' => $term_id
//
// No se utilizan nombres, slugs ni posiciones.
//
// La tarjeta muestra:
//
// 1. Imagen de la familia
// 2. Nombre de la familia
// 3. Descripción
// 4. Enlaces pequeños a sus subcategorías
//
// No utiliza dropdown ni JavaScript.
//

if (!function_exists('ardensa_render_category_dropdown_tree')) {

    function ardensa_render_category_dropdown_tree($term, $level = 0) {

        // =====================================================
        // DATOS BÁSICOS DE LA CATEGORÍA
        // =====================================================

        $term_id = (int) $term->term_id;

        $link = get_term_link($term);

        if (is_wp_error($link)) {
            return;
        }


        // =====================================================
        // MINIATURA OFICIAL DE WOOCOMMERCE
        // =====================================================

        $thumbnail_id = get_term_meta(
            $term_id,
            'thumbnail_id',
            true
        );

        $icon = '';

        if ($thumbnail_id) {

            $icon = wp_get_attachment_image_url(
                $thumbnail_id,
                'medium'
            );

        }


        // =====================================================
        // DESCRIPCIÓN DE LA CATEGORÍA
        // =====================================================

        $description = '';

        if (!empty($term->description)) {

            $description = wp_trim_words(
                wp_strip_all_tags($term->description),
                18,
                '...'
            );

        }


        // =====================================================
        // BUSCAR HIJOS REALES
        // =====================================================
        //
        // IMPORTANTE:
        // Aquí se conserva la configuración jerárquica actual.
        //
        // WooCommerce busca solamente las categorías cuyo
        // parent sea exactamente este term_id.
        //

        $children = get_terms(array(
            'taxonomy'   => 'product_cat',
            'hide_empty' => true,
            'parent'     => $term_id,
            'number'     => 0,
            'orderby'    => 'name',
            'order'      => 'ASC',
        ));


        // Si WooCommerce devuelve un error,
        // simplemente consideramos que no tiene hijos.

        if (is_wp_error($children)) {

            $children = array();

        }


        $has_children = !empty($children);

        ?>


        <!-- =================================================
             CONTENEDOR DE LA CATEGORÍA
        ================================================== -->

        <div
            class="category-item-wrapper"
            data-level="<?php echo esc_attr($level); ?>"
        >


            <!-- =================================================
                 TARJETA DE LA FAMILIA
            ================================================== -->

            <div class="category-card">


                <!-- =============================================
                     IMAGEN
                ============================================== -->

                <a
                    href="<?php echo esc_url($link); ?>"
                    class="category-card-image"
                    aria-label="<?php echo esc_attr($term->name); ?>"
                >

                    <?php if ($icon) : ?>

                        <img
                            src="<?php echo esc_url($icon); ?>"
                            alt="<?php echo esc_attr($term->name); ?>"
                            loading="lazy"
                        >

                    <?php else : ?>

                        <span class="category-card-placeholder">
                            📦
                        </span>

                    <?php endif; ?>

                </a>


                <!-- =============================================
                     CONTENIDO
                ============================================== -->

                <div class="category-card-content">


                    <!-- =========================================
                         NOMBRE DE LA FAMILIA
                    ========================================== -->

                    <h3 class="category-card-title">

                        <a href="<?php echo esc_url($link); ?>">

                            <?php echo esc_html($term->name); ?>

                        </a>

                    </h3>


                    <!-- =========================================
                         DESCRIPCIÓN
                    ========================================== -->

                    <?php if ($description) : ?>

                        <p class="category-card-description">

                            <?php echo esc_html($description); ?>

                        </p>

                    <?php endif; ?>


                    <!-- =========================================
                         SUBCATEGORÍAS
                    ========================================== -->

                    <?php if ($has_children) : ?>

                        <div class="category-card-children">


                            <?php foreach ($children as $child) : ?>

                                <?php

                                $child_link = get_term_link($child);

                                if (is_wp_error($child_link)) {
                                    continue;
                                }

                                ?>


                                <a
                                    href="<?php echo esc_url($child_link); ?>"
                                    class="category-card-child-link"
                                >

                                    <?php echo esc_html($child->name); ?>

                                </a>


                            <?php endforeach; ?>


                        </div>

                    <?php endif; ?>


                </div>


            </div>


        </div>


        <?php

    }

}


?>




<!-- =========================================================
     BANNER
     ========================================================= -->

<?php

if (function_exists('ardensa_mostrar_banner')) {
    ardensa_mostrar_banner();
}

?>



<!-- =========================================================
     PRODUCTOS DESTACADOS
     ========================================================= -->

<?php if (!empty($featured_products)) : ?>

<div class="section-title">
    Productos destacados
</div>

<div class="products-grid">

    <?php foreach ($featured_products as $product) : ?>

        <?php

        $product_id = $product->get_id();

        $image_id = $product->get_image_id();

        $image_url = $image_id
            ? wp_get_attachment_image_url(
                $image_id,
                'medium'
            )
            : '';

        $in_stock = $product->is_in_stock();

        $qty_in_cart = mitienda_cantidad_en_carrito(
            $product_id
        );

        ?>

        <div class="product-card">


            <a
                href="<?php echo esc_url($product->get_permalink()); ?>"
                class="product-image-wrapper"
            >

                <?php if ($image_url) : ?>

                    <img
                        src="<?php echo esc_url($image_url); ?>"
                        alt="<?php echo esc_attr($product->get_name()); ?>"
                    >

                <?php else : ?>

                    <div class="no-image-placeholder">
                        <span>📷</span>
                    </div>

                <?php endif; ?>


                <?php if (!$in_stock) : ?>

                    <div class="badge-agotado">
                        <span>Agotado</span>
                    </div>

                <?php endif; ?>

            </a>



            <div class="product-info">

                <a href="<?php echo esc_url($product->get_permalink()); ?>">

                    <h3 class="product-title">
                        <?php echo esc_html($product->get_name()); ?>
                    </h3>

                </a>

            </div>



            <div style="padding:0 10px 10px; margin-top:auto;">

                <?php if (!$in_stock) : ?>

                    <div class="btn-agotado">
                        Agotado
                    </div>


                <?php elseif ($qty_in_cart > 0) : ?>

                    <div
                        class="qty-inline-selector"
                        data-product-id="<?php echo esc_attr($product_id); ?>"
                    >

                        <button
                            type="button"
                            class="qty-inline-btn"
                            onclick="cambiarCantidadInline(this,-1)"
                        >
                            −
                        </button>

                        <span class="qty-inline-value">
                            <?php echo esc_html($qty_in_cart); ?>
                        </span>

                        <button
                            type="button"
                            class="qty-inline-btn"
                            onclick="cambiarCantidadInline(this,1)"
                        >
                            +
                        </button>

                    </div>


                <?php else : ?>

                    <button
                        type="button"
                        class="btn-add-cart"
                        data-product-id="<?php echo esc_attr($product_id); ?>"
                        onclick="agregarAlCarrito(this);return false;"
                    >
                        Agregar a cotización
                    </button>

                <?php endif; ?>

            </div>


        </div>

    <?php endforeach; ?>

</div>

<?php endif; ?>



<!-- =========================================================
     BENEFICIOS
     ========================================================= -->

<section class="beneficios">


    <div class="beneficio">

        <div class="beneficio-icono">

            <svg
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="2"
                stroke-linecap="round"
                stroke-linejoin="round"
            >

                <rect
                    x="1"
                    y="3"
                    width="15"
                    height="13"
                ></rect>

                <path d="M16 8h4l3 3v5h-7"></path>

                <circle
                    cx="5.5"
                    cy="18.5"
                    r="2"
                ></circle>

                <circle
                    cx="18.5"
                    cy="18.5"
                    r="2"
                ></circle>

            </svg>

        </div>

        <h3>
            Envíos Gratis
        </h3>

        <p>
            GAM gratis y envíos nacionales.
        </p>

    </div>



    <div class="beneficio">

        <div class="beneficio-icono">

            <svg
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="2"
                stroke-linecap="round"
                stroke-linejoin="round"
            >

                <path d="M3 21h18"></path>

                <path d="M5 21V7l7-4 7 4v14"></path>

                <path d="M9 12h6"></path>

            </svg>

        </div>

        <h3>
            Precios Profesionales
        </h3>

        <p>
            Talleres, detailing y negocios.
        </p>

    </div>



    <div class="beneficio">

        <div class="beneficio-icono">

            <svg
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="2"
                stroke-linecap="round"
                stroke-linejoin="round"
            >

                <path d="M2 8l10-5 10 5-10 5z"></path>

                <path d="M6 10v4c0 2 3 4 6 4s6-2 6-4v-4"></path>

            </svg>

        </div>

        <h3>
            Capacitaciones
        </h3>

        <p>
            Entrenamiento gratuito.
        </p>

    </div>


</section>



<!-- =========================================================
     NUESTRAS MARCAS
     ========================================================= -->

<div class="section-title">
    Nuestras marcas
</div>


<section class="brands-section">

    <div class="brands-scroll">

        <div class="brands-container">


            <div class="brand-item">

                <img
                    src="https://solucionesardensa.com/wp-content/uploads/2026/02/ipg-logo.png"
                    alt="IPG"
                >

            </div>


            <div class="brand-item">

                <img
                    src="https://solucionesardensa.com/wp-content/uploads/2026/02/3M_wordmark.png"
                    alt="3M"
                >

            </div>


            <div class="brand-item">

                <img
                    src="https://solucionesardensa.com/wp-content/uploads/2026/02/Milwaukee-logo.png"
                    alt="Milwaukee"
                >

            </div>


            <div class="brand-item">

                <img
                    src="https://solucionesardensa.com/wp-content/uploads/2026/02/Gliptone_Logo-e1783717269329.png"
                    alt="Gliptone"
                >

            </div>


        </div>

    </div>

</section>


<!-- =========================================================
     CATEGORÍAS
     ========================================================= -->

<?php if (!empty($categories) && !is_wp_error($categories)) : ?>

<section class="categories-section">


    <!-- =====================================================
         TÍTULO
         ===================================================== -->

    <h2 class="categories-title">
        ¿Qué andas buscando?
    </h2>


    <!-- =====================================================
         CONTENEDOR DEL CARRUSEL
         ===================================================== -->

    <div class="categories-scroll">


        <!-- ================================================
             FLECHA IZQUIERDA
             ================================================ -->

        <button
            type="button"
            class="categories-arrow categories-arrow-left"
            aria-label="Ver categorías anteriores"
        >
            ‹
        </button>


        <!-- ================================================
             CONTENEDOR DE CATEGORÍAS
             ================================================ -->

        <div class="categories-container">

            <?php foreach ($categories as $cat) : ?>

                <?php
                ardensa_render_category_dropdown_tree(
                    $cat,
                    0
                );
                ?>

            <?php endforeach; ?>

        </div>


        <!-- ================================================
             FLECHA DERECHA
             ================================================ -->

        <button
            type="button"
            class="categories-arrow categories-arrow-right"
            aria-label="Ver más categorías"
        >
            ›
        </button>


    </div>


</section>


<?php endif; ?>


<!-- =========================================================
     MÁS VENDIDOS
     ========================================================= -->

<?php if (!empty($best_sellers)) : ?>

<div class="section-title">
    Los más vendidos
</div>


<div class="products-grid">

    <?php foreach ($best_sellers as $product) : ?>

        <?php

        $product_id = $product->get_id();

        $image_id = $product->get_image_id();

        $image_url = $image_id
            ? wp_get_attachment_image_url(
                $image_id,
                'medium'
            )
            : '';

        $in_stock = $product->is_in_stock();

        $qty_in_cart = mitienda_cantidad_en_carrito(
            $product_id
        );

        ?>

        <div class="product-card">


            <a
                href="<?php echo esc_url($product->get_permalink()); ?>"
                class="product-image-wrapper"
            >

                <?php if ($image_url) : ?>

                    <img
                        src="<?php echo esc_url($image_url); ?>"
                        alt="<?php echo esc_attr($product->get_name()); ?>"
                    >

                <?php else : ?>

                    <div class="no-image-placeholder">
                        <span>📷</span>
                    </div>

                <?php endif; ?>


                <?php if (!$in_stock) : ?>

                    <div class="badge-agotado">
                        <span>Agotado</span>
                    </div>

                <?php endif; ?>

            </a>



            <div class="product-info">

                <a href="<?php echo esc_url($product->get_permalink()); ?>">

                    <h3 class="product-title">
                        <?php echo esc_html($product->get_name()); ?>
                    </h3>

                </a>

            </div>



            <div style="padding:0 10px 10px; margin-top:auto;">

                <?php if (!$in_stock) : ?>

                    <div class="btn-agotado">
                        Agotado
                    </div>


                <?php elseif ($qty_in_cart > 0) : ?>

                    <div
                        class="qty-inline-selector"
                        data-product-id="<?php echo esc_attr($product_id); ?>"
                    >

                        <button
                            type="button"
                            class="qty-inline-btn"
                            onclick="cambiarCantidadInline(this,-1)"
                        >
                            −
                        </button>

                        <span class="qty-inline-value">
                            <?php echo esc_html($qty_in_cart); ?>
                        </span>

                        <button
                            type="button"
                            class="qty-inline-btn"
                            onclick="cambiarCantidadInline(this,1)"
                        >
                            +
                        </button>

                    </div>


                <?php else : ?>

                    <button
                        type="button"
                        class="btn-add-cart"
                        data-product-id="<?php echo esc_attr($product_id); ?>"
                        onclick="agregarAlCarrito(this);return false;"
                    >
                        Agregar a cotización
                    </button>

                <?php endif; ?>

            </div>


        </div>

    <?php endforeach; ?>

</div>

<?php endif; ?>



<?php get_footer(); ?>