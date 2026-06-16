<?php get_header(); ?>

<div class="trocha-section">
    <div class="trocha-container">
        <h1 class="trocha-page-title">ARCHIVO</h1>
        <?php if (have_posts()) : ?>
            <div style="display:flex;flex-direction:column;gap:1rem;">
                <?php while (have_posts()) : the_post(); ?>
                    <article <?php post_class(); ?> style="background:var(--dark2);border:1px solid var(--border);padding:1rem;">
                        <h2 style="font-family:Impact,'Arial Black',sans-serif;font-size:1.3rem;">
                            <a href="<?php the_permalink(); ?>" style="color:var(--text);text-decoration:none;"><?php the_title(); ?></a>
                        </h2>
                        <div style="font-size:0.75rem;color:var(--text-dim);margin-bottom:0.5rem;">
                            <?php echo get_the_date(); ?>
                        </div>
                        <div style="color:var(--text-sub);font-size:0.85rem;">
                            <?php the_excerpt(); ?>
                        </div>
                    </article>
                <?php endwhile; ?>
            </div>
            <div style="margin-top:1.5rem;text-align:center;">
                <?php the_posts_pagination(); ?>
            </div>
        <?php else : ?>
            <p style="text-align:center;color:var(--text-dim);padding:2rem;font-size:0.85rem;">
                No hay nada aquí todavía.
            </p>
        <?php endif; ?>
    </div>
</div>

<?php get_footer(); ?>
