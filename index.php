<?php get_header(); ?>

<div class="trocha-section trocha-section--archive">
    <div class="trocha-container">
        <h1 class="trocha-page-title"><?php _e('[ ARCHIVO ]', 'trocha'); ?></h1>
        <?php if (have_posts()) : ?>
            <div class="trocha-posts">
                <?php while (have_posts()) : the_post(); ?>
                    <article <?php post_class('trocha-post-card'); ?>>
                        <h2 class="trocha-post-card__title">
                            <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                        </h2>
                        <div class="trocha-post-card__meta">
                            <time><?php echo get_the_date(); ?></time>
                        </div>
                        <div class="trocha-post-card__excerpt">
                            <?php the_excerpt(); ?>
                        </div>
                    </article>
                <?php endwhile; ?>
            </div>
            <?php the_posts_navigation(); ?>
        <?php else : ?>
            <p><?php _e('No hay entradas.', 'trocha'); ?></p>
        <?php endif; ?>
    </div>
</div>

<?php get_footer(); ?>
