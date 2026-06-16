<?php
/* Template Name: Categorías Pies */
get_header();
?>

<div class="trocha-section trocha-categorias-section">
    <div class="trocha-container">

        <?php while (have_posts()) : the_post(); ?>
            <?php if (get_the_content()) : ?>
                <div class="trocha-elementor-content">
                    <?php the_content(); ?>
                </div>
            <?php else : ?>

        <h1 class="trocha-page-title">VÍSTETE POR LOS PIES</h1>
        <p class="trocha-page-subtitle">/// TRES CAMINOS. UN DESTINO. ///</p>

        <div class="trocha-categorias-grid">

            <!-- FRÍO — Hospital/sala médica años 60, azul-amarillento, patrón inclinado -->
            <a href="<?php echo esc_url(home_url('/categoria-producto/frio')); ?>"
               class="trocha-categoria-card trocha-categoria-card--frio" id="frio">
                <div class="trocha-cat-bg trocha-cat-bg--frio"></div>
                <div class="trocha-cat-overlay"></div>
                <div class="trocha-categoria-card__inner">
                    <div class="trocha-categoria-card__stamp">HEARTLESS</div>
                    <h2 class="trocha-categoria-card__title">FRÍO</h2>
                    <p class="trocha-categoria-card__desc">Para cuando el suelo está mojado y un error puede ser una caída. Botas, borceguíes, pisada firme.</p>
                    <span class="trocha-cat-cta">EXPLORAR →</span>
                </div>
            </a>

            <!-- CALOR — Industrial, polígono, grafiti en pared, Sevillano -->
            <a href="<?php echo esc_url(home_url('/categoria-producto/calor')); ?>"
               class="trocha-categoria-card trocha-categoria-card--calor" id="calor">
                <div class="trocha-cat-bg trocha-cat-bg--calor"></div>
                <div class="trocha-cat-overlay"></div>
                <div class="trocha-categoria-card__inner">
                    <div class="trocha-categoria-card__stamp">REAL PLAYAS</div>
                    <h2 class="trocha-categoria-card__title">CALOR</h2>
                    <p class="trocha-categoria-card__desc">Para el asfalto que quema, el día largo, la calle sin tregua. Ligeros, frescos, con carácter.</p>
                    <span class="trocha-cat-cta">EXPLORAR →</span>
                </div>
            </a>

            <!-- ESTILO — Gangster años 80, Los Ángeles, líneas largas y afiladas -->
            <a href="<?php echo esc_url(home_url('/categoria-producto/estilo')); ?>"
               class="trocha-categoria-card trocha-categoria-card--estilo" id="estilo">
                <div class="trocha-cat-bg trocha-cat-bg--estilo"></div>
                <div class="trocha-cat-overlay"></div>
                <div class="trocha-categoria-card__inner">
                    <div class="trocha-categoria-card__stamp">DESTACA</div>
                    <h2 class="trocha-categoria-card__title">ESTILO</h2>
                    <p class="trocha-categoria-card__desc">No es temporada. Es actitud. Lo que no necesita excusa ni clima. Puro sello personal.</p>
                    <span class="trocha-cat-cta">EXPLORAR →</span>
                </div>
            </a>

        </div>

            <?php endif; ?>
        <?php endwhile; ?>

    </div>
</div>

<?php get_footer(); ?>
