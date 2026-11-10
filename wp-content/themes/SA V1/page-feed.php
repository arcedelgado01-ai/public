<?php
/**
 * Template Name: Feed
 * Página de contenido de redes sociales
 */

get_header();
?>

<div class="feed-page">
    
    <!-- Header del feed -->
    <div class="feed-header">
        <h1>Feed</h1>
        <p>Nuestro contenido en redes sociales</p>
    </div>
    
    <!-- Selector de red social -->
    <div class="feed-tabs">
        <button type="button" class="feed-tab active" onclick="cambiarFeed(this, 'todos')">Todos</button>
        <button type="button" class="feed-tab" onclick="cambiarFeed(this, 'instagram')">Instagram</button>
        <button type="button" class="feed-tab" onclick="cambiarFeed(this, 'tiktok')">TikTok</button>
        <button type="button" class="feed-tab" onclick="cambiarFeed(this, 'facebook')">Facebook</button>
    </div>
    
    <!-- Grid de contenido -->
    <div class="feed-grid" id="feedGrid">
        
        <?php
        // Obtener posts del tipo "feed" si existe, o mostrar contenido estático
        $feed_posts = new WP_Query(array(
            'post_type'      => 'post',
            'posts_per_page' => 12,
            'post_status'    => 'publish',
            'category_name'  => 'feed',
        ));
        
        if ($feed_posts->have_posts()) :
            while ($feed_posts->have_posts()) : $feed_posts->the_post();
                $red_social = get_post_meta(get_the_ID(), 'red_social', true);
                if (!$red_social) $red_social = 'instagram';
                $video_url = get_post_meta(get_the_ID(), 'video_url', true);
        ?>
        
        <div class="feed-item" data-red="<?php echo $red_social; ?>">
            <div class="feed-item-header">
                <span class="feed-item-red"><?php 
                    if ($red_social == 'instagram') echo '📸';
                    elseif ($red_social == 'tiktok') echo '🎵';
                    elseif ($red_social == 'facebook') echo '👍';
                    else echo '📱';
                ?></span>
                <span class="feed-item-red-name"><?php echo ucfirst($red_social); ?></span>
            </div>
            
            <?php if (has_post_thumbnail()) : ?>
                <div class="feed-item-media">
                    <?php the_post_thumbnail('medium', array('style' => 'width:100%;height:100%;object-fit:cover;')); ?>
                    <?php if ($video_url) : ?>
                        <div class="feed-play-btn">▶</div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
            
            <div class="feed-item-content">
                <h3><?php the_title(); ?></h3>
                <?php if (get_the_excerpt()) : ?>
                    <p><?php echo wp_trim_words(get_the_excerpt(), 15); ?></p>
                <?php endif; ?>
            </div>
            
            <div class="feed-item-date">
                <?php echo human_time_diff(get_the_time('U'), current_time('timestamp')) . ' atrás'; ?>
            </div>
        </div>
        
        <?php
            endwhile;
            wp_reset_postdata();
        else :
        ?>
        
        <!-- Contenido de ejemplo cuando no hay posts -->
        <div class="feed-empty">
            <span class="feed-empty-icon">📱</span>
            <h3>Próximamente</h3>
            <p>Aquí verás nuestro contenido de redes sociales, videos y demostraciones de productos.</p>
            <div class="feed-social-links">
                <a href="https://instagram.com/solucionesardensa" target="_blank" class="feed-social-btn">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="#E4405F"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/></svg>
                    Síguenos en Instagram
                </a>
                <a href="https://tiktok.com/@solucionesardensa" target="_blank" class="feed-social-btn">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="#000000"><path d="M12.525.02c1.31-.02 2.61-.01 3.91-.02.08 1.53.63 3.09 1.75 4.17 1.12 1.11 2.7 1.62 4.24 1.79v4.03c-1.44-.05-2.89-.35-4.2-.97-.57-.26-1.1-.59-1.62-.93-.01 2.92.01 5.84-.02 8.75-.08 1.4-.54 2.79-1.35 3.94-1.31 1.92-3.58 3.17-5.91 3.21-1.43.08-2.86-.31-4.08-1.03-2.02-1.19-3.44-3.37-3.65-5.71-.02-.5-.03-1-.01-1.49.18-1.9 1.12-3.72 2.58-4.96 1.66-1.44 3.98-2.13 6.15-1.72.02 1.48-.04 2.96-.04 4.44-.99-.32-2.15-.23-3.02.37-.63.41-1.11 1.04-1.36 1.75-.21.51-.15 1.07-.14 1.61.24 1.64 1.82 3.02 3.5 2.87 1.12-.01 2.19-.66 2.77-1.61.19-.33.4-.67.41-1.06.1-1.79.06-3.57.07-5.36.01-4.03-.01-8.05.02-12.07z"/></svg>
                    Síguenos en TikTok
                </a>
                <a href="https://facebook.com/solucionesardensa" target="_blank" class="feed-social-btn">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="#1877F2"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                    Síguenos en Facebook
                </a>
            </div>
        </div>
        
        <?php endif; ?>
        
    </div>
    
</div>

<?php get_footer(); ?>