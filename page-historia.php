<?php
/* Template Name: Historia */
get_header();
?>

<?php while (have_posts()) : the_post(); ?>
    <?php if (get_the_content()) : ?>
        <div class="trocha-elementor-content">
            <?php the_content(); ?>
        </div>
    <?php else : ?>

<!-- ============================================================
     HISTORIA — Dos niños en las calles de España
     ============================================================ -->

<!-- BLOQUE 1: El barrio -->
<div class="trocha-historia-block trocha-historia-block--barrio">
    <div class="trocha-historia-bg" style="background-image:url('<?php echo esc_url(get_template_directory_uri()); ?>/assets/img/pitbull.jpg');"></div>
    <div class="trocha-historia-content">
        <div class="trocha-historia-eyebrow">/// EL PRINCIPIO ///</div>
        <h1 class="trocha-historia-title">DOS NIÑOS.<br>UNA CALLE.</h1>
        <div class="trocha-historia-divider"></div>
        <p class="trocha-historia-text">
            No nacimos en ningún sitio especial. Nacimos en un barrio español de los de verdad — de los que no salen en las revistas, pero que te forjan de una manera que ninguna escuela puede replicar. Asfalto agrietado, bloques de pisos, el ruido de la ciudad a todas horas. Eso era nuestro mundo.
        </p>
        <p class="trocha-historia-text">
            Éramos niños, pero la calle nos trató como hombres desde el primer día. Y nosotros respondimos a la altura. Aprendimos a movernos, a observar, a entender las reglas no escritas de un entorno que no perdona a los que no prestan atención. La calle enseña rápido. O aprendes, o te quedas atrás.
        </p>
    </div>
</div>

<!-- BLOQUE 2: La búsqueda -->
<div class="trocha-historia-block trocha-historia-block--busqueda">
    <div class="trocha-historia-bg" style="background-image:url('<?php echo esc_url(get_template_directory_uri()); ?>/assets/img/pitbull.jpg');"></div>
    <div class="trocha-historia-content">
        <div class="trocha-historia-eyebrow">/// LA BÚSQUEDA ///</div>
        <h2 class="trocha-historia-subtitle">TOCAMOS TODO<br>LO QUE SE CRUZÓ</h2>
        <div class="trocha-historia-divider"></div>
        <p class="trocha-historia-text">
            Desde pequeños teníamos una curiosidad que no se podía apagar. Queríamos saber qué había dentro y fuera de nosotros mismos. Así que lo tocamos todo. Cada experiencia que se cruzó por nuestro camino, la aceptamos. Ninguna rechazada. Ninguna descartada sin antes entender qué podía enseñarnos.
        </p>
        <p class="trocha-historia-text">
            Nos buscamos la vida de todas las formas posibles. Con esfuerzo, con creatividad, con el instinto de quien sabe que nadie va a venir a salvarte. Cada tropiezo era una lección. Cada caída, una oportunidad de levantarse con más información que antes. Y así, paso a paso, fuimos construyendo algo que no estaba en ningún plan — nos construimos a nosotros mismos.
        </p>
    </div>
</div>

<!-- BLOQUE 3: El sistema y la libertad -->
<div class="trocha-historia-block trocha-historia-block--libertad">
    <div class="trocha-historia-bg" style="background-image:url('<?php echo esc_url(get_template_directory_uri()); ?>/assets/img/pitbull.jpg');"></div>
    <div class="trocha-historia-content">
        <div class="trocha-historia-eyebrow">/// EL SISTEMA ///</div>
        <h2 class="trocha-historia-subtitle">LIBERTAD NO ES<br>UNA PALABRA. ES UNA DECISIÓN.</h2>
        <div class="trocha-historia-divider"></div>
        <p class="trocha-historia-text">
            Crecimos viendo cómo funcionaba el tablero. Cómo el sistema está diseñado con trampas silenciosas — trabajos que te atrapan, deudas que te encadenan, rutinas que te adormecen. Cárceles sin rejas, pero cárceles al fin. Y los que no las ven, viven dentro de ellas toda la vida convencidos de que es libertad.
        </p>
        <p class="trocha-historia-text">
            Nosotros las vimos. Y decidimos buscar otra salida. No porque seamos especiales — sino porque teníamos hambre de algo que ningún sueldo a fin de mes puede darte. Hambre de tiempo propio. De decisiones propias. De libertad financiera real. De ser los dueños de nuestro camino, no los inquilinos.
        </p>
        <p class="trocha-historia-text">
            TROCHA nació de esa necesidad. De demostrar que desde el asfalto también se puede construir algo propio, algo con alma, algo que represente no solo a dos niños de barrio — sino a todos los que han entendido lo mismo.
        </p>
    </div>
</div>

<!-- BLOQUE 4: Para quién -->
<div class="trocha-historia-block trocha-historia-block--tuyo">
    <div class="trocha-historia-bg" style="background-image:url('<?php echo esc_url(get_template_directory_uri()); ?>/assets/img/pitbull.jpg');"></div>
    <div class="trocha-historia-content">
        <div class="trocha-historia-eyebrow">/// ESTO ES PARA TI ///</div>
        <h2 class="trocha-historia-subtitle">NO UN PREMIO.<br>UN CAMINO.</h2>
        <div class="trocha-historia-divider"></div>
        <p class="trocha-historia-text">
            Si has llegado hasta aquí es porque algo en esta historia resuena contigo. Porque tú también lo has sentido — ese fuego que no te deja conformarte, esa voz que dice que hay algo más, esa certeza de que con esfuerzo y creatividad puedes llegar donde quieres llegar.
        </p>
        <p class="trocha-historia-text">
            No buscamos darte un trofeo ni venderte un sueño. Lo que tenemos para ti es un objetivo — un camino. Porque eso es lo que alimenta el espíritu del hombre de verdad. No los aplausos. No el reconocimiento. El camino mismo. La sensación de avanzar con las propias piernas hacia algo que tiene sentido para ti.
        </p>
        <p class="trocha-historia-text" style="color:#c49a3c;font-style:italic;font-size:1rem;line-height:1.6;">
            "No encontramos un diamante en el camino.<br>Nos convertimos en él."
        </p>
        <p class="trocha-historia-text" style="font-size:0.75rem;opacity:0.5;margin-top:-0.5rem;">
            &mdash; TROCHA, <?php echo date('Y'); ?>
        </p>
    </div>
</div>

<!-- CTA FINAL -->
<style>
@keyframes trochaSlideUp {
    from { opacity: 0; transform: translateY(60px); }
    to   { opacity: 1; transform: translateY(0); }
}
.trocha-cta-animated {
    animation: trochaSlideUp 1.1s cubic-bezier(0.22,1,0.36,1) both;
    animation-delay: 0.2s;
}
</style>
<div class="trocha-historia-cta">
    <div class="trocha-historia-divider" style="margin:0 auto 2rem;"></div>
    <div class="trocha-historia-eyebrow">/// TU TURNO ///</div>
    <h2 class="trocha-historia-cta-title trocha-cta-animated" style="font-family:'Bebas Neue',cursive;font-size:clamp(3rem,9vw,8rem);letter-spacing:0.08em;margin-bottom:1rem;line-height:1.05;max-width:40%;margin-left:auto;margin-right:auto;text-align:center;">
        <span style="display:block;background:linear-gradient(to bottom,#ffffff 0%,#ffffff 30%,#A67C00 100%);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;">EMPIEZA</span>
        <span style="display:block;background:linear-gradient(to bottom,#ffffff 0%,#ffffff 30%,#A67C00 100%);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;">TU</span>
        <span style="display:block;background:linear-gradient(to bottom,#ffffff 0%,#ffffff 30%,#A67C00 100%);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;">CAMINO</span>
    </h2>
    <p style="color:#8a7a6a;font-size:0.85rem;font-family:'Courier New',monospace;margin-bottom:2.5rem;max-width:480px;margin-left:auto;margin-right:auto;line-height:1.8;">
        Cada prenda que llevamos es una declaración.<br>No de moda. De mentalidad.
    </p>
    <div style="display:flex;justify-content:center;">
        <a href="<?php echo esc_url(home_url('/categorias-pies')); ?>" class="trocha-btn trocha-btn--primary" data-pjax-ignore style="background:#C4A000;border-color:#C4A000;color:#0B0B0D;width:50%;text-align:center;padding:1.2rem 2rem;font-size:1.2rem;">
            VER DROPS
        </a>
    </div>
</div>

    <?php endif; ?>
<?php endwhile; ?>

<?php get_footer(); ?>
