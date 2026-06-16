<?php get_header(); ?>

<div class="trocha-section">
    <div class="trocha-container trocha-container--narrow">
        <?php while (have_posts()) : the_post(); ?>
            <article <?php post_class('trocha-single'); ?>>
                <h1 class="trocha-page-title"><?php the_title(); ?></h1>
                <div class="trocha-page__content">
                    <?php the_content(); ?>
                </div>
            </article>
        <?php endwhile; ?>
    </div>
</div>

<?php get_footer(); ?>
