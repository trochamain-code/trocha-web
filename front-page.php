<?php get_header(); ?>

<!-- ============================================================
     HERO — Fondo difuminado + texto animado
     ============================================================ -->
<div class="trocha-hero trocha-hero--brand-bg">
    <div class="trocha-hero-bg-img" style="background-image:url('<?php echo esc_url(get_template_directory_uri()); ?>/assets/img/pitbull.jpg');"></div>
    <div class="accent-bar"></div>
    <h2>DECLARACIÓN DE INTENCIONES.</h2>
    <p>/// CADA PRENDA ES UNA DECLARACIÓN DE INTENCIONES DE TI HACIA TI. HECHO DESDE EL ASFALTO, CON PROPÓSITO FIJO. ///</p>
    <div class="hero-actions">
        <a href="<?php echo esc_url(home_url('/categorias-pies')); ?>" class="trocha-btn trocha-btn--primary">COLECCIONES</a>
    </div>
</div>

<?php while (have_posts()) : the_post(); ?>

<!-- ============================================================
     SECCIÓN 2 — ÚLTIMAS PRENDAS
     ============================================================ -->
<div class="trocha-section">
    <h2 class="trocha-page-title">ÚLTIMAS PRENDAS</h2>
    <p style="text-align:center;color:var(--text-sub);font-size:0.75rem;margin-bottom:1.5rem;font-family:'Courier New',monospace;">
        /// CADA DROP TIENE UN PROPÓSITO. CADA PRENDA, UNA HISTORIA. ///
    </p>

    <?php if (class_exists('WooCommerce')) : ?>
        <?php
        $products = wc_get_products([
            'limit'   => 4,
            'orderby' => 'date',
            'order'   => 'DESC',
        ]);
        if ($products) : ?>
        <div class="trocha-products-grid">
            <?php foreach ($products as $product) : ?>
                <div class="trocha-product-card">
                    <div class="trocha-product-card__stamp">NUEVO</div>
                    <div class="trocha-product-card__image">
                        <?php echo $product->get_image('medium'); ?>
                    </div>
                    <div class="trocha-product-card__body">
                        <h3 class="trocha-product-card__title">
                            <a href="<?php echo esc_url(get_permalink($product->get_id())); ?>">
                                <?php echo esc_html($product->get_name()); ?>
                            </a>
                        </h3>
                        <div class="trocha-product-card__price">
                            <?php echo $product->get_price_html(); ?>
                        </div>
                        <a href="<?php echo esc_url(get_permalink($product->get_id())); ?>"
                           class="trocha-btn trocha-btn--small trocha-btn--primary <?php echo $product->is_type('simple') ? 'add_to_cart_button ajax_add_to_cart' : ''; ?>"
                           <?php if ($product->is_type('simple')) : ?>
                           data-product_id="<?php echo esc_attr($product->get_id()); ?>"
                           data-quantity="1"
                           <?php endif; ?>>
                            <?php echo $product->is_type('simple') ? 'AÑADIR AL CARRITO' : 'VER PRODUCTO'; ?>
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    <?php endif; ?>

    <div style="text-align:center;margin-top:1.5rem;">
        <a href="<?php echo esc_url(home_url('/categorias-pies')); ?>" class="trocha-btn">VER TODO</a>
    </div>
</div>

<!-- ============================================================
     SECCIÓN 3 — HISTORIA / CAMINO PROPIO  (2 columnas + spotlight)
     ============================================================ -->
<div class="trocha-section trocha-section--brand-story">
    <!-- Fondo de marca con baja opacidad -->
    <div class="trocha-section-bg" style="background-image:url('<?php echo esc_url(get_template_directory_uri()); ?>/assets/img/dogs-fight-bw.jpg');"></div>

    <div class="trocha-brand-story-inner">
        <!-- Columna texto -->
        <div class="trocha-brand-story__text">
            <div style="width:40px;height:2px;background:var(--accent);margin-bottom:1rem;box-shadow:0 0 8px var(--accent);"></div>
            <blockquote style="font-size:1rem;color:var(--text-sub);font-style:italic;margin-bottom:1rem;line-height:1.6;">
                "Esto no viene de oficinas. Viene de calles, trabajos, errores y decisiones."
            </blockquote>
            <div style="font-size:0.7rem;color:var(--text-dim);font-family:'Courier New',monospace;margin-bottom:2rem;">
                &mdash; TROCHA, <?php echo date('Y'); ?>
            </div>
            <h2 style="font-size:1.6rem;color:#D0CABB;margin-bottom:0.75rem;letter-spacing:0.12em;">CAMINO PROPIO</h2>
            <div style="width:40px;height:2px;background:var(--accent);margin:0 0 1rem;"></div>
            <p style="font-size:0.85rem;color:#A4A098;line-height:1.7;margin-bottom:1.5rem;font-family:'Courier New',monospace;">
                No vinimos de ninguna parte. Nacimos buscándonos la vida en la calle, sin más herramienta que el instinto y más techo que el asfalto. Esto no es una marca. Es la prueba de que desde abajo también se sale.
            </p>
            <a href="<?php echo esc_url(home_url('/historia/')); ?>" class="trocha-btn trocha-btn--primary" style="background:#C4A000;border-color:#C4A000;color:#0B0B0D;">
                HISTORIA
            </a>
        </div>

        <!-- Columna producto con iluminación icónica -->
        <div class="trocha-product-spotlight">
            <div class="trocha-product-spotlight__light"></div>
            <div class="trocha-product-spotlight__img-wrap">
                <img src="<?php echo esc_url(get_template_directory_uri()); ?>/assets/img/dogs-fight-bw.jpg"
                     alt="TROCHA — La marca"
                     class="trocha-product-spotlight__img">
            </div>
            <div class="trocha-product-spotlight__label">EDICIÓN LIMITADA</div>
            <div class="trocha-product-spotlight__name">TROCHA<br><span>NO ES ROPA. ES CAMINO.</span></div>
        </div>
    </div>
</div>

<!-- ============================================================
     SECCIÓN 4 — CUANDO LA TROCHA ES EL ÚNICO CAMINO
     (Easter egg — galería emocional con audio)
     ============================================================ -->
<div class="trocha-section trocha-section--mood" id="trocha-mood-section">
    <div class="trocha-mood-inner">
        <div class="trocha-mood-text">
            <div class="trocha-mood-eyebrow">/// LATROCHA ESTADO MENTAL///</div>
            <h2 class="trocha-mood-title">CUANDO LA TROCHA<br>ES LA ÚNICA SALIDA</h2>
            <div class="trocha-mood-phrases">
                <span>AMBICION</span>
                <span class="sep">////</span>
                <span>FAMILIA</span>
                <span class="sep">////</span>
                <span>AMOR</span>
                <span class="sep">////</span>
                <span>PRINCIPIOS</span>
            </div>
        </div>
        <!-- Botón Play/Stop — audio gestionado por mini-player global del footer -->
        <div class="trocha-play-wrap">
            <button class="trocha-play-btn" id="trocha-play-btn" aria-label="Reproducir">
                <span class="trocha-play-btn__icon">
                    <svg class="icon-play" viewBox="0 0 24 24" fill="currentColor"><polygon points="5,3 19,12 5,21"/></svg>
                    <svg class="icon-stop" viewBox="0 0 24 24" fill="currentColor"><rect x="6" y="4" width="4" height="16"/><rect x="14" y="4" width="4" height="16"/></svg>
                </span>
                <span class="trocha-play-btn__label" id="trocha-play-label">PRESS PLAY</span>
            </button>
        </div>
    </div>
</div>

<!-- ============================================================
     MODAL GALERÍA EMOCIONAL
     ============================================================ -->
<div class="trocha-gallery-modal" id="trocha-gallery-modal" aria-hidden="true">
    <button class="trocha-gallery-close" id="trocha-gallery-close" aria-label="Cerrar">✕</button>
    <div class="trocha-gallery-slides" id="trocha-gallery-slides">

        <?php
        $frases = [
            ['label' => 'MOTIVACIÓN',       'sub' => 'El único límite eres tú mismo.'],
            ['label' => 'RABIA CONTROLADA', 'sub' => 'Úsala. No te uses a ti mismo.'],
            ['label' => 'ENFOQUE',          'sub' => 'Un objetivo. Todo lo demás es ruido.'],
            ['label' => 'ÚLTIMA BALA',      'sub' => 'Cuando no queda nada, queda la voluntad.'],
            ['label' => 'DOMINANTE',        'sub' => 'No pides permiso para existir.'],
            ['label' => 'SERIO',            'sub' => 'Sin excusas. Sin aplausos necesarios.'],
            ['label' => 'CAMUFLADO',        'sub' => 'El entorno te subestima. Bien.'],
            ['label' => 'EFECTIVO',         'sub' => 'Resultados. No explicaciones.'],
            ['label' => 'SILENCIOSO',       'sub' => 'Que hablen tus actos.'],
            ['label' => 'TRIUNFADOR NATO',  'sub' => 'Naciste para esto. Ya lo sabes.'],
        ];
        $img_url = get_template_directory_uri() . '/assets/img/pitbull.jpg';
        foreach ($frases as $i => $f) :
        ?>
        <div class="trocha-gallery-slide <?php echo $i === 0 ? 'active' : ''; ?>"
             style="background-image:url('<?php echo esc_url($img_url); ?>');">
            <div class="trocha-gallery-slide__overlay"></div>
            <div class="trocha-gallery-slide__content">
                <div class="trocha-gallery-slide__label"><?php echo esc_html($f['label']); ?></div>
                <div class="trocha-gallery-slide__sub"><?php echo esc_html($f['sub']); ?></div>
            </div>
            <div class="trocha-gallery-slide__num"><?php echo str_pad($i+1, 2, '0', STR_PAD_LEFT); ?> / <?php echo count($frases); ?></div>
        </div>
        <?php endforeach; ?>

        <!-- Slide final: producto -->
        <div class="trocha-gallery-slide trocha-gallery-slide--product">
            <div class="trocha-gallery-slide__overlay"></div>
            <div class="trocha-gallery-slide__content trocha-gallery-slide__content--product">
                <div class="trocha-gallery-slide__label" style="font-size:0.9rem;letter-spacing:0.3em;opacity:0.6;">TÚ YA LO SABES.</div>
                <div class="trocha-gallery-slide__cta-title">HAZTE CON LA TUYA</div>
                <?php if (class_exists('WooCommerce')) :
                    $prods = wc_get_products(['limit' => 1, 'orderby' => 'date', 'order' => 'DESC']);
                    if ($prods) : $p = $prods[0]; ?>
                    <div class="trocha-gallery-product">
                        <?php echo $p->get_image('thumbnail'); ?>
                        <div class="trocha-gallery-product__name"><?php echo esc_html($p->get_name()); ?></div>
                        <div class="trocha-gallery-product__price"><?php echo $p->get_price_html(); ?></div>
                        <a href="<?php echo esc_url($p->add_to_cart_url()); ?>" class="trocha-btn trocha-btn--primary">AÑADIR AL CARRITO</a>
                    </div>
                    <?php endif; endif; ?>
            </div>
        </div>
    </div>
    <audio id="trocha-gallery-audio" loop preload="auto">
        <source src="<?php echo esc_url(get_template_directory_uri()); ?>/assets/mp3/reggaeton.wav" type="audio/wav">
    </audio>
</div>

<?php endwhile; ?>

<?php get_footer(); ?>
