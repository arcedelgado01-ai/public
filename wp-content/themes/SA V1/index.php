<?php
/**
 * Plantilla principal de respaldo
 * WordPress requiere este archivo para que el tema sea válido.
 */

get_header();
?>

<div style="padding: 20px 12px; margin-top: 110px;">
    
    <?php if (have_posts()) : ?>
        <?php while (have_posts()) : the_post(); ?>
            <article style="background:#fff;padding:15px;margin-bottom:10px;border-radius:8px;">
                <h2><?php the_title(); ?></h2>
                <?php the_content(); ?>
            </article>
        <?php endwhile; ?>
    <?php else : ?>
        <p>No se encontró contenido.</p>
    <?php endif; ?>
    
</div>

<?php get_footer(); ?>