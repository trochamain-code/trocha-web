<?php get_header(); ?>

<div class="trocha-section" style="text-align:center;padding:3rem 1rem;">
    <div class="trocha-container trocha-container--narrow">
        <h1 class="trocha-page-title">ERROR 404</h1>
        <p style="color:var(--text-sub);font-size:0.9rem;margin-bottom:1.5rem;">
            Esta ruta no lleva a ningún lado. Como algunos caminos, mejor dar la vuelta.
        </p>
        <a href="<?php echo esc_url(home_url('/')); ?>" class="trocha-btn">VOLVER AL INICIO</a>
    </div>
</div>

<?php get_footer(); ?>
