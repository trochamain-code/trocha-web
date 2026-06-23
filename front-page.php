<?php get_header(); ?>

<style id="trocha-reveal-fix">
.trocha-hero,.trocha-hero h2,.trocha-hero p,.trocha-hero .hero-actions,
.trocha-section,.trs{opacity:1!important;transform:none!important;visibility:visible!important}
</style>

<?php
// Hero slider — últimos 4 productos añadidos a la tienda.
$th_hero_prods = (class_exists('WooCommerce') && function_exists('wc_get_products'))
    ? wc_get_products(['limit' => 4, 'orderby' => 'date', 'order' => 'DESC', 'status' => 'publish'])
    : [];
// Frases de identidad de marca (editables en Apariencia → Hero TROCHA).
$th_hero_phrases = function_exists('trocha_get_hero_phrases')
    ? trocha_get_hero_phrases()
    : ["NO ES ROPA.\nES CAMINO.", "HECHO DESDE\nEL ASFALTO.", "RAÍCES DURAS,\nSUEÑOS GRANDES.", "ASUME RIESGOS.\nCONFÍA EN TI."];
?>
<section class="th-hero th-hero--slider" id="thHero">
    <div class="th-hero__track" id="thHeroTrack">
        <?php if ($th_hero_prods) : foreach ($th_hero_prods as $i => $p) :
            $pid   = $p->get_id();
            $link  = get_permalink($pid);
            $name  = $p->get_name();
            // Foto del slide: imagen de héroe propia (_trocha_hero_img) si existe; si no, la imagen destacada del producto.
            $hero_img = get_post_meta($pid, '_trocha_hero_img', true);
            if (!$hero_img) { $hero_img = get_the_post_thumbnail_url($pid, 'full'); }
            if (!$hero_img) { $hero_img = get_template_directory_uri() . '/assets/img/hero-slide-1.png'; }
            $phrase = $th_hero_phrases[$i % count($th_hero_phrases)];
        ?>
        <article class="th-hslide<?php echo $i === 0 ? ' is-active' : ''; ?>" data-index="<?php echo (int) $i; ?>">
            <a href="<?php echo esc_url($link); ?>" class="th-hslide__photo-link" aria-label="Ver <?php echo esc_attr($name); ?>">
                <img src="<?php echo esc_url($hero_img); ?>" alt="<?php echo esc_attr($name); ?>" class="th-hslide__photo" loading="<?php echo $i === 0 ? 'eager' : 'lazy'; ?>">
            </a>
            <div class="th-hslide__content">
                <div class="th-hslide__eyebrow">// TROCHA — EDICIÓN <?php echo str_pad($i + 1, 2, '0', STR_PAD_LEFT); ?></div>
                <h2 class="th-hslide__title"><?php echo nl2br(esc_html($phrase)); ?></h2>
                <a href="<?php echo esc_url($link); ?>" class="th-btn th-btn--primary th-hslide__cta">VER · <?php echo esc_html($name); ?> &rarr;</a>
            </div>
        </article>
        <?php endforeach; else : ?>
        <article class="th-hslide is-active">
            <div class="th-hslide__content">
                <div class="th-hslide__eyebrow">// TROCHA</div>
                <h2 class="th-hslide__title">DECLARACIÓN<br>DE INTENCIONES.</h2>
                <a href="<?php echo esc_url(home_url('/tienda')); ?>" class="th-btn th-btn--primary th-hslide__cta">ENTRAR A LA TIENDA &rarr;</a>
            </div>
        </article>
        <?php endif; ?>
    </div>

    <?php if (count($th_hero_prods) > 1) : ?>
    <button class="th-hero__arrow th-hero__arrow--prev" id="thHeroPrev" aria-label="Anterior">&#8249;</button>
    <button class="th-hero__arrow th-hero__arrow--next" id="thHeroNext" aria-label="Siguiente">&#8250;</button>
    <div class="th-hero__dots" id="thHeroDots"></div>
    <?php endif; ?>
</section>

<script>
(function(){
    var hero = document.getElementById('thHero');
    if (!hero) return;
    var track = document.getElementById('thHeroTrack');
    var slides = track ? track.children : [];
    var n = slides.length;
    if (n < 2) return;
    var dotsWrap = document.getElementById('thHeroDots');
    var idx = 0, timer = null;
    for (var i = 0; i < n; i++) {
        var d = document.createElement('button');
        d.className = 'th-hero__dot' + (i === 0 ? ' is-active' : '');
        d.setAttribute('aria-label', 'Ir al slide ' + (i + 1));
        (function(k){ d.addEventListener('click', function(){ go(k); restart(); }); })(i);
        dotsWrap.appendChild(d);
    }
    var dots = dotsWrap.children;
    function go(k){
        idx = (k + n) % n;
        track.style.transform = 'translateX(-' + (idx * 100) + '%)';
        for (var i = 0; i < n; i++){
            slides[i].classList.toggle('is-active', i === idx);
            if (dots[i]) dots[i].classList.toggle('is-active', i === idx);
        }
    }
    function next(){ go(idx + 1); }
    function prev(){ go(idx - 1); }
    function restart(){ clearInterval(timer); timer = setInterval(next, 6000); }
    var nx = document.getElementById('thHeroNext'), pv = document.getElementById('thHeroPrev');
    if (nx) nx.addEventListener('click', function(){ next(); restart(); });
    if (pv) pv.addEventListener('click', function(){ prev(); restart(); });
    var x0 = null;
    track.addEventListener('touchstart', function(e){ x0 = e.touches[0].clientX; }, {passive:true});
    track.addEventListener('touchend', function(e){
        if (x0 === null) return;
        var dx = e.changedTouches[0].clientX - x0;
        if (Math.abs(dx) > 40){ (dx < 0 ? next : prev)(); restart(); }
        x0 = null;
    }, {passive:true});
    hero.addEventListener('mouseenter', function(){ clearInterval(timer); });
    hero.addEventListener('mouseleave', restart);
    restart();
})();
</script>

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
