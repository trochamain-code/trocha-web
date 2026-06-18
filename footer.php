</main>

<div class="trocha-marquee">
    <span>
        GRACIAS POR VISITAR &mdash; VUELVE PRONTO &mdash;
        CADA PRENDA TIENE UN PROPÓSITO &mdash; HECHO DESDE EL ASFALTO &mdash;
        TROCHA &mdash; NO ES ROPA. ES CAMINO. &mdash;
    </span>
</div>

<footer class="trocha-footer">
    <div class="trocha-footer__inner">

        <div class="trocha-footer__section">
            <a href="<?php echo esc_url(home_url('/')); ?>" class="trocha-footer__logo">TROCHA</a>
            <p class="trocha-footer__text">No es ropa. Es camino.</p>
        </div>

        <div class="trocha-footer__section">
            <h4>RUTAS</h4>
            <ul class="trocha-footer__list">
                <li><a href="<?php echo esc_url(home_url('/')); ?>">INICIO</a></li>
                <li><a href="<?php echo esc_url(home_url('/tienda')); ?>">TIENDA</a></li>
                <li><a href="<?php echo esc_url(home_url('/categorias-pies')); ?>">COLECCIONES</a></li>
                <li><a href="<?php echo esc_url(home_url('/carrito')); ?>">PEDIDO</a></li>
                <li><a href="<?php echo esc_url(home_url('/historia')); ?>">ORIGEN</a></li>
            </ul>
        </div>

        <div class="trocha-footer__section">
            <h4>CONTACTO</h4>
            <ul class="trocha-footer__list">
                <li><a href="<?php echo esc_url(home_url('/contacto')); ?>">CONTACTO</a></li>
                <li><a href="<?php echo esc_url(home_url('/historia')); ?>">CAMINO PROPIO</a></li>
                <li><a href="<?php echo esc_url(home_url('/mi-cuenta')); ?>">MI CUENTA</a></li>
                <li><a href="mailto:info@trocha.es">INFO@TROCHA.ES</a></li>
            </ul>
        </div>

    </div>

    <div class="trocha-footer__micro">
        Hecho a mano desde el asfalto. &copy; <?php echo date('Y'); ?> TROCHA
    </div>
</footer>

</div><!-- .trocha-body-grid -->

<?php wp_footer(); ?>

<!-- =====================================================
     MINI-PLAYER FLOTANTE — reproducción continua
     ===================================================== -->
<audio id="trocha-persistent-audio"
       src="<?php echo esc_url(get_template_directory_uri()); ?>/assets/mp3/caceria.mp3"
       preload="auto" loop></audio>

<div id="trocha-miniplayer" class="trocha-miniplayer" aria-label="Reproductor">
    <button id="trocha-mini-btn" class="trocha-mini-btn" aria-label="Play/Stop">
        <span class="trocha-mini-icon">
            <svg class="icon-play" viewBox="0 0 24 24" fill="currentColor"><polygon points="5,3 19,12 5,21"/></svg>
            <svg class="icon-stop" viewBox="0 0 24 24" fill="currentColor"><rect x="6" y="4" width="4" height="16"/><rect x="14" y="4" width="4" height="16"/></svg>
        </span>
        <span class="trocha-mini-label">CACERÍA</span>
    </button>
</div>

<script>
(function(){
    var a = document.getElementById('trocha-persistent-audio');
    var player = document.getElementById('trocha-miniplayer');
    var btn = document.getElementById('trocha-mini-btn');
    var K = 'ta_play', KT = 'ta_time';
    var isPlaying = false;
    var vol = 0.6;
    a.volume = vol;

    function setPlaying(s) {
        isPlaying = s;
        btn.classList.toggle('is-playing', s);
        player.classList.toggle('is-playing', s);
        localStorage.setItem(K, s ? '1' : '0');
        document.dispatchEvent(new CustomEvent('trocha:playstate', { detail: { playing: s } }));
    }

    function play() {
        a.play().then(function(){ setPlaying(true); }).catch(function(){});
    }

    function pause() {
        a.pause();
        setPlaying(false);
    }

    /* Save time continuously */
    setInterval(function() {
        if (isPlaying) localStorage.setItem(KT, a.currentTime);
    }, 250);

    /* Save on unload */
    window.addEventListener('beforeunload', function() {
        localStorage.setItem(KT, a.currentTime);
        localStorage.setItem(K, isPlaying ? '1' : '0');
    });

    /* Restore on load */
    var wasPlaying = localStorage.getItem(K) === '1';
    var t = parseFloat(localStorage.getItem(KT) || '0');

    if (wasPlaying) {
        a.currentTime = Math.max(0, t - 0.15);
        a.play().then(function() {
            setPlaying(true);
            /* Fine-tune to exact position after playback starts */
            a.currentTime = t;
        }).catch(function() {
            /* Browser blocked autoplay — show play button */
            setPlaying(false);
        });
    }

    btn.addEventListener('click', function() {
        isPlaying ? pause() : play();
    });

    /* Resume if browser pauses us (background tab) */
    document.addEventListener('visibilitychange', function() {
        if (!document.hidden && isPlaying && a.paused) play();
    });
})();
</script>

<script>
/* ── SPA NAVIGATION: audio nunca se para ──── */
(function(){
    var MAIN = 'main';
    var base = location.origin;
    var fetching = false;

    function swap(html, url) {
        var doc = new DOMParser().parseFromString(html, 'text/html');
        var newMain = doc.querySelector(MAIN);
        var oldMain = document.querySelector(MAIN);
        if (!newMain || !oldMain) return false;

        /* Update title */
        var t = doc.querySelector('title');
        if (t) document.title = t.textContent;

        /* Swap content */
        oldMain.innerHTML = '';
        /* Move children one by one to preserve event listeners where possible */
        while (newMain.firstChild) {
            oldMain.appendChild(newMain.firstChild);
        }

        /* Re-run inline scripts from the new page */
        var scripts = oldMain.querySelectorAll('script');
        scripts.forEach(function(s) {
            var ns = document.createElement('script');
            Array.from(s.attributes).forEach(function(a) { ns.setAttribute(a.name, a.value); });
            ns.textContent = s.textContent;
            s.parentNode.replaceChild(ns, s);
        });

        /* Notify WooCommerce */
        if (typeof jQuery !== 'undefined') {
            jQuery(document.body).trigger('wc_fragment_refresh');
            jQuery(document).trigger('page_loaded');
            /* Re-init variation forms */
            jQuery('.variations_form').each(function() { jQuery(this).wc_variation_form(); });
            /* Re-init product gallery */
            jQuery('.woocommerce-product-gallery').each(function() { jQuery(this).wc_product_gallery(); });
        }

        /* Dispatch DOM-like event for any listeners */
        window.dispatchEvent(new Event('trocha:page_swapped'));

        window.scrollTo(0, 0);
        return true;
    }

    function navigate(href, push) {
        if (fetching) return;
        fetching = true;
        var navTimeout = setTimeout(function() {
            fetching = false;
            window.location.href = href;
        }, 8000);
        fetch(href, { credentials: 'same-origin', cache: 'no-store' })
            .then(function(r) {
                if (!r.ok) throw new Error('HTTP ' + r.status);
                return r.text();
            })
            .then(function(html) {
                if (swap(html, href)) {
                    if (push !== false) history.pushState({ url: href }, '', href);
                } else {
                    window.location.href = href;
                }
                clearTimeout(navTimeout);
                fetching = false;
            })
            .catch(function() {
                clearTimeout(navTimeout);
                fetching = false;
                window.location.href = href;
            });
    }

    document.addEventListener('click', function(e) {
        var link = e.target.closest('a');
        if (!link) return;
        var href = link.getAttribute('href');
        if (!href || href === '#') return;
        if (link.hostname !== location.hostname) return;
        if (/^#/.test(link.getAttribute('href'))) return;
        if (/wp-admin|wp-content\/uploads|wp-json|wp-login|\.xml|\.pdf/.test(href)) return;
        if (/^(tel|mailto|javascript):/.test(href)) return;
        if (link.target || link.hasAttribute('download')) return;
        /* Don't intercept WooCommerce AJAX buttons */
        if (link.classList.contains('add_to_cart_button') || link.classList.contains('ajax_add_to_cart')) return;
        /* Don't intercept variation reset */
        if (link.classList.contains('reset_variations')) return;
        /* Don't intercept cart/checkout/account — full page load needed */
        if (/\/carrito|\/checkout|\/finalizar-compra|\/mi-cuenta|\/pedido|\/contacto/.test(href)) return;

        e.preventDefault();
        navigate(href, true);
    });

    /* Back/forward */
    window.addEventListener('popstate', function(e) {
        if (e.state && e.state.url) {
            navigate(e.state.url, false);
        }
    });
})();
</script>
</body>
</html>






