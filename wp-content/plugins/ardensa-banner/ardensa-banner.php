<?php
/**
 * Plugin Name: Ardensa Banner Slider
 * Description: Banner carrusel para la página de inicio
 * Version: 1.1
 * Author: Soluciones Ardensa
 */

if (!defined('ABSPATH')) exit;

// ========================================
// REGISTRAR POST TYPE "Banner"
// ========================================
function ardensa_banner_post_type() {
    register_post_type('banner', array(
        'labels' => array(
            'name'          => 'Banners',
            'singular_name' => 'Banner',
            'add_new'       => 'Agregar banner',
            'add_new_item'  => 'Agregar nuevo banner',
            'edit_item'     => 'Editar banner',
        ),
        'public'        => false,
        'show_ui'       => true,
        'show_in_menu'  => true,
        'menu_icon'     => 'dashicons-images-alt2',
        'supports'      => array('title', 'thumbnail'),
        'menu_position' => 5,
    ));
}
add_action('init', 'ardensa_banner_post_type');

// ========================================
// META BOX: Link del banner
// ========================================
function ardensa_banner_meta_box() {
    add_meta_box('banner_link_box', 'Configuración del banner', 'ardensa_banner_link_html', 'banner', 'normal', 'high');
}
add_action('add_meta_boxes', 'ardensa_banner_meta_box');

function ardensa_banner_link_html($post) {
    $link = get_post_meta($post->ID, '_banner_link', true);
    wp_nonce_field('ardensa_banner_nonce', 'ardensa_banner_nonce_field');
    ?>
    <p>
        <label><strong>URL del enlace (opcional):</strong></label><br>
        <input type="url" name="banner_link" value="<?php echo esc_attr($link); ?>" style="width:100%; padding:8px; margin-top:4px;">
        <small>Deja vacío si no quieres que el banner sea clickeable</small>
    </p>
    <?php
}

function ardensa_banner_save_meta($post_id) {
    if (!isset($_POST['ardensa_banner_nonce_field'])) return;
    if (!wp_verify_nonce($_POST['ardensa_banner_nonce_field'], 'ardensa_banner_nonce')) return;
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    
    if (isset($_POST['banner_link'])) {
        update_post_meta($post_id, '_banner_link', esc_url_raw($_POST['banner_link']));
    }
}
add_action('save_post_banner', 'ardensa_banner_save_meta');

// ========================================
// FUNCIÓN PARA MOSTRAR EL BANNER
// ========================================
function ardensa_mostrar_banner() {
    $banners = get_posts(array(
        'post_type'      => 'banner',
        'posts_per_page' => 10,
        'post_status'    => 'publish',
        'orderby'        => 'date',
        'order'          => 'DESC',
    ));
    
    if (empty($banners)) return;
    
    $slides = array();
    foreach ($banners as $banner) {
        $img = get_the_post_thumbnail_url($banner->ID, 'full');
        if (!$img) continue;
        $link = get_post_meta($banner->ID, '_banner_link', true);
        $slides[] = array(
            'img'   => $img,
            'link'  => $link,
            'title' => $banner->post_title
        );
    }
    
    if (empty($slides)) return;
    
    $total = count($slides);
    ?>

    <div class="ab-carousel" id="ab-carousel">

        <div class="ab-slides" id="ab-slides">

            <?php foreach ($slides as $slide) : ?>

                <?php if (!empty($slide['link'])) : ?>

                    <a href="<?php echo esc_url($slide['link']); ?>" class="ab-slide">

                        <img
                            src="<?php echo esc_url($slide['img']); ?>"
                            alt="<?php echo esc_attr($slide['title']); ?>"
                        >

                        <div class="ab-caption">
                            <?php echo esc_html($slide['title']); ?>
                        </div>

                    </a>

                <?php else : ?>

                    <div class="ab-slide">

                        <img
                            src="<?php echo esc_url($slide['img']); ?>"
                            alt="<?php echo esc_attr($slide['title']); ?>"
                        >

                        <div class="ab-caption">
                            <?php echo esc_html($slide['title']); ?>
                        </div>

                    </div>

                <?php endif; ?>

            <?php endforeach; ?>

        </div>

        <?php if ($total > 1) : ?>

            <!-- NUEVO: Flecha izquierda -->
            <button class="ab-arrow ab-arrow-prev" type="button">
                ❮
            </button>

            <!-- NUEVO: Flecha derecha -->
            <button class="ab-arrow ab-arrow-next" type="button">
                ❯
            </button>

            <div class="ab-dots" id="ab-dots">
                <?php for ($i = 0; $i < $total; $i++) : ?>
                    <span class="ab-dot<?php echo $i === 0 ? ' active' : ''; ?>"></span>
                <?php endfor; ?>
            </div>

        <?php endif; ?>

    </div>

    <?php
}


// ========================================
// CARGAR CSS Y JS
// ========================================
function ardensa_banner_assets() {
    wp_enqueue_style('ardensa-banner-css', plugin_dir_url(__FILE__) . 'banner.css', array(), '2.0');
    wp_enqueue_script('ardensa-banner-js', plugin_dir_url(__FILE__) . 'banner.js', array(), '2.0', true);
}
add_action('wp_enqueue_scripts', 'ardensa_banner_assets');