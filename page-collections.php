<?php
/* Template Name: Colecciones */
get_header();
?>

<div class="trocha-section trocha-section--collections">
    <div class="trocha-container">

        <?php while (have_posts()) : the_post(); ?>
            <?php if (get_the_content()) : ?>
                <div class="trocha-elementor-content">
                    <?php the_content(); ?>
                </div>
            <?php else : ?>

        <h1 class="trocha-page-title glitch" data-text="COLECCIONES">COLECCIONES</h1>
        <p class="trocha-page-subtitle">Tres submundos dentro de TROCHA</p>

        <div class="trocha-collections-full">

            <div class="trocha-collection-full" id="brega">
                <div class="trocha-collection-full__header" style="border-color: #5a6b3d;">
                    <div class="trocha-collection-full__seal">BREGA</div>
                    <div class="trocha-collection-full__info">
                        <h2>BREGA</h2>
                        <p>La lucha. El día a día. La constancia del que no abandona.</p>
                        <a href="<?php echo esc_url(home_url('/coleccion/brega')); ?>" class="trocha-btn trocha-btn--primary">VER COLECCIÓN</a>
                    </div>
                </div>
            </div>

            <div class="trocha-collection-full" id="temple">
                <div class="trocha-collection-full__header" style="border-color: #c49a3c;">
                    <div class="trocha-collection-full__seal">TEMPLE</div>
                    <div class="trocha-collection-full__info">
                        <h2>TEMPLE</h2>
                        <p>La cabeza fría. La paciencia estratégica del que sabe esperar.</p>
                        <a href="<?php echo esc_url(home_url('/coleccion/temple')); ?>" class="trocha-btn trocha-btn--primary">VER COLECCIÓN</a>
                    </div>
                </div>
            </div>

            <div class="trocha-collection-full" id="tiento">
                <div class="trocha-collection-full__header" style="border-color: #8b4513;">
                    <div class="trocha-collection-full__seal">TIENTO</div>
                    <div class="trocha-collection-full__info">
                        <h2>TIENTO</h2>
                        <p>El instinto. La decisión en el momento justo.</p>
                        <a href="<?php echo esc_url(home_url('/coleccion/tiento')); ?>" class="trocha-btn trocha-btn--primary">VER COLECCIÓN</a>
                    </div>
                </div>
            </div>

        </div>

            <?php endif; ?>
        <?php endwhile; ?>

    </div>
</div>

<?php get_footer(); ?>
