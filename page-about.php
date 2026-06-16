<?php
/* Template Name: About */
get_header();
?>

<div class="trocha-section trocha-section--about">
    <div class="trocha-container trocha-container--narrow">

        <?php while (have_posts()) : the_post(); ?>
            <?php if (get_the_content()) : ?>
                <div class="trocha-elementor-content">
                    <?php the_content(); ?>
                </div>
            <?php else : ?>

        <div class="trocha-about-header">
            <h1 class="trocha-about-header__title glitch" data-text="ORIGEN">ORIGEN</h1>
            <div class="trocha-about-header__stamp">MANIFIESTO</div>
        </div>

        <div class="trocha-about-content trocha-content">
            <p class="trocha-lead">Esto no viene de oficinas. Viene de calles, trabajos, errores y decisiones.</p>

            <p>TROCHA nace en el asfalto. En las conversaciones de después del curro, en los trayectos en metro, en los momentos antes de dormir cuando piensas en lo que podrías haber hecho y en lo que vas a hacer mañana.</p>

            <p>No somos una marca de ropa. Somos una forma de entender el camino. La calle no es un escenario: es el origen. El barrio no es una estética: es la realidad que te forma.</p>

            <p>Cada prenda es un mensaje. Cada colección es un estado de ánimo. Cada drop es una decisión.</p>

            <h2>BREGA</h2>
            <p>La lucha silenciosa. El trabajo que no se ve. La constancia del que madruga y del que no se rinde. BREGA es la colección de los que se parten el lomo sin pedir nada a cambio.</p>

            <h2>TEMPLE</h2>
            <p>La cabeza fría en el momento caliente. Saber esperar. Saber cuándo dar el paso. TEMPLE es para los que entienden que la paciencia también es una forma de inteligencia.</p>

            <h2>TIENTO</h2>
            <p>El instinto. Ese momento en que todo se alinea y sabes exactamente qué hacer. TIENTO es para las decisiones que cambian el rumbo.</p>

            <div class="trocha-separator">✕ ✕ ✕</div>

            <p>No hay campañas de marketing. No hay estrategia de marca. Hay calles, hay historia, hay personas que eligen llevar algo con significado.</p>

            <p class="trocha-signoff">
                <strong>TROCHA</strong><br>
                No es ropa. Es camino.
            </p>
        </div>

            <?php endif; ?>
        <?php endwhile; ?>

    </div>
</div>

<?php get_footer(); ?>
