<?php get_header(); ?>

<style id="trocha-reveal-fix">
.trocha-hero,.trocha-hero h2,.trocha-hero p,.trocha-hero .hero-actions,
.trocha-section,.trs{opacity:1!important;transform:none!important;visibility:visible!important}
</style>

<section class="th-hero">
    <div class="th-hero__slides">
        <div class="th-hero__slide th-hero__slide--1" style="background-image:url('<?php echo esc_url(get_template_directory_uri()); ?>/assets/img/hero-slide-1.png');">
            <a href="<?php echo esc_url(home_url('/producto/dios-es-un-nio')); ?>" class="th-hero__slide-link"></a>
            <a href="<?php echo esc_url(home_url('/producto/embroidered-work-jacket-cornerstone-duck-cloth-hooded-zip-up')); ?>" class="th-hero__product-link">
                <img src="<?php echo esc_url(get_template_directory_uri()); ?>/assets/img/hero-product-1.png" alt="" class="th-hero__product">
            </a>
        </div>
        <div class="th-hero__slide th-hero__slide--2" style="background-image:url('<?php echo esc_url(get_template_directory_uri()); ?>/assets/img/hero-slide-2.png');">
            <a href="<?php echo esc_url(home_url('/producto/embroidered-work-jacket-cornerstone-duck-cloth-hooded-zip-up')); ?>" class="th-hero__product-link">
                <img src="<?php echo esc_url(get_template_directory_uri()); ?>/assets/img/hero-product-2.png" alt="" class="th-hero__product">
            </a>
        </div>
    </div>
    <div class="th-hero__overlay"></div>
    <div class="th-hero__content">
        <div class="th-hero__tag">// NUEVA COLECCIÓN</div>
        <h1 class="th-hero__title">DECLARACIÓN<br>DE INTENCIONES.</h1>
        <p class="th-hero__sub">Cada prenda tiene un propósito. Hecho desde el asfalto.</p>
        <div class="th-hero__ctas">
            <a href="<?php echo esc_url(home_url('/tienda')); ?>" class="th-btn th-btn--primary">COMPRAR AHORA</a>
            <a href="<?php echo esc_url(home_url('/categorias-pies')); ?>" class="th-btn th-btn--ghost">COLECCIONES</a>
        </div>
    </div>
    <div class="th-hero__scroll">↓</div>
</section>

<?php if (class_exists('WooCommerce')) :
    $trocha_prods = wc_get_products(['limit' => 6, 'orderby' => 'date', 'order' => 'DESC', 'status' => 'publish']);
    if ($trocha_prods) : ?>

<section class="th-drops">
    <div class="th-drops__head">
        <div class="th-drops__label">// ÚLTIMOS DROPS</div>
        <h2 class="th-drops__title">NUEVAS PRENDAS</h2>
        <a href="<?php echo esc_url(home_url('/tienda')); ?>" class="th-drops__all">VER TODO →</a>
    </div>
    <div class="th-drops__slider-wrap">
        <div class="trs" id="trochaSlider">
            <div class="trs__track" id="trochaTrack">
                <?php for ($si = 0; $si < 3; $si++) : foreach ($trocha_prods as $p) :
                    $clone = ($si !== 1);
                    $price = $p->get_price_html();
                    $badge = get_post_meta($p->get_id(), '_trocha_badge', true);
                    $cats  = wp_get_post_terms($p->get_id(), 'product_cat');
                    $cat   = (!empty($cats) && !is_wp_error($cats)) ? strtoupper($cats[0]->slug !== "sin-categorizar" ? $cats[0]->name : "DROP") : 'DROP';
                ?>
                <div class="trs__item<?php echo $clone ? ' trs__item--clone' : ''; ?>"<?php echo $clone ? ' aria-hidden="true"' : ''; ?>>
                    <a href="<?php echo esc_url(get_permalink($p->get_id())); ?>" class="th-card">
                        <div class="th-card__img-wrap">
                            <?php echo $p->get_image('woocommerce_single'); ?>
                            <div class="th-card__hover-cta">VER PRODUCTO</div>
                            <?php if ($badge) : ?>
                                <span class="th-card__badge"><?php echo esc_html($badge); ?></span>
                            <?php endif; ?>
                        </div>
                        <div class="th-card__body">
                            <span class="th-card__cat"><?php echo esc_html($cat); ?></span>
                            <h3 class="th-card__name"><?php echo esc_html($p->get_name()); ?></h3>
                            <div class="th-card__price"><?php echo $price; ?></div>
                            <span class="th-card__cta">AÑADIR AL CARRITO →</span>
                        </div>
                    </a>
                </div>
                <?php endforeach; endfor; ?>
            </div>
        </div>
    </div>
</section>
<?php endif; endif; ?>

<section class="th-strip">
    <div class="th-strip__item">
        <span class="th-strip__icon">🚚</span>
        <span class="th-strip__text">ENVÍO RÁPIDO A ESPAÑA</span>
    </div>
    <div class="th-strip__divider">///</div>
    <div class="th-strip__item">
        <span class="th-strip__icon">⚡</span>
        <span class="th-strip__text">EDICIONES LIMITADAS</span>
    </div>
    <div class="th-strip__divider">///</div>
    <div class="th-strip__item">
        <span class="th-strip__icon">🔒</span>
        <span class="th-strip__text">PAGO 100% SEGURO</span>
    </div>
</section>

<section class="th-cats">
    <div class="th-cats__head">
        <div class="th-cats__label">// EXPLORA</div>
        <h2 class="th-cats__title">ELIGE TU CAMINO</h2>
    </div>
    <div class="th-cats__grid">
        <a href="<?php echo esc_url(home_url('/categoria-producto/frio')); ?>" class="th-cat-card th-cat-card--frio">
            <div class="th-cat-card__bg"></div>
            <div class="th-cat-card__overlay"></div>
            <div class="th-cat-card__inner">
                <span class="th-cat-card__stamp">HEARTLESS</span>
                <h3 class="th-cat-card__title">FRÍO</h3>
                <p class="th-cat-card__desc">Pisada firme. Botas y borceguíes para cuando el suelo está mojado.</p>
                <span class="th-cat-card__cta">EXPLORAR →</span>
            </div>
        </a>
        <a href="<?php echo esc_url(home_url('/categoria-producto/calor')); ?>" class="th-cat-card th-cat-card--calor">
            <div class="th-cat-card__bg"></div>
            <div class="th-cat-card__overlay"></div>
            <div class="th-cat-card__inner">
                <span class="th-cat-card__stamp">REAL PLAYAS</span>
                <h3 class="th-cat-card__title">CALOR</h3>
                <p class="th-cat-card__desc">Ligeros, frescos, con carácter. Para el asfalto que quema.</p>
                <span class="th-cat-card__cta">EXPLORAR →</span>
            </div>
        </a>
        <a href="<?php echo esc_url(home_url('/categoria-producto/estilo')); ?>" class="th-cat-card th-cat-card--estilo">
            <div class="th-cat-card__bg"></div>
            <div class="th-cat-card__overlay"></div>
            <div class="th-cat-card__inner">
                <span class="th-cat-card__stamp">DESTACA</span>
                <h3 class="th-cat-card__title">ESTILO</h3>
                <p class="th-cat-card__desc">No es temporada. Es actitud. Puro sello personal.</p>
                <span class="th-cat-card__cta">EXPLORAR →</span>
            </div>
        </a>
    </div>
</section>

<section class="th-story">
    <div class="th-story__bg" style="background-image:url('<?php echo esc_url(get_template_directory_uri()); ?>/assets/img/dogs-fight-bw.jpg');"></div>
    <div class="th-story__overlay"></div>
    <div class="th-story__inner">
        <div class="th-story__text">
            <div class="th-story__label">// NUESTRA HISTORIA</div>
            <h2 class="th-story__title">CAMINO PROPIO</h2>
            <p class="th-story__quote">"Esto no viene de oficinas.<br>Viene de calles, trabajos, errores y decisiones."</p>
            <p class="th-story__body">No vinimos de ninguna parte. Nacimos buscándonos la vida en la calle, sin más herramienta que el instinto. Esto no es una marca. Es la prueba de que desde abajo también se sale.</p>
            <a href="<?php echo esc_url(home_url('/historia/')); ?>" class="th-btn th-btn--primary">NUESTRA HISTORIA</a>
        </div>
        <div class="th-story__img-wrap">
            <img src="<?php echo esc_url(get_template_directory_uri()); ?>/assets/img/dogs-fight-bw.jpg" alt="TROCHA" class="th-story__img">
            <div class="th-story__img-label">
                <span>EDICIÓN LIMITADA</span>
                <strong>TROCHA</strong>
            </div>
        </div>
    </div>
</section>

<section class="th-cta-final">
    <div class="th-cta-final__tag">/// TODO EL CATÁLOGO ///</div>
    <h2 class="th-cta-final__title">VER TODAS<br>LAS PRENDAS</h2>
    <a href="<?php echo esc_url(home_url('/tienda')); ?>" class="th-btn th-btn--primary th-btn--lg">ENTRAR A LA TIENDA</a>
</section>

<section class="trocha-section trocha-section--mood" id="trocha-mood-section">
    <div class="trocha-mood-inner">
        <div class="trocha-mood-text">
            <div class="trocha-mood-eyebrow">/// LA TROCHA — ESTADO MENTAL ///</div>
            <h2 class="trocha-mood-title">CUANDO LA TROCHA<br>ES LA ÚNICA SALIDA</h2>
            <div class="trocha-mood-phrases">
                <span>AMBICION</span><span class="sep">////</span>
                <span>FAMILIA</span><span class="sep">////</span>
                <span>AMOR</span><span class="sep">////</span>
                <span>PRINCIPIOS</span>
            </div>
        </div>
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
</section>

<?php get_footer(); ?>
