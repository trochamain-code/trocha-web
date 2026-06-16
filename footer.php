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
     MINI-PLAYER FLOTANTE — persiste entre páginas
     ===================================================== -->
<audio id="trocha-persistent-audio"
       src="<?php echo esc_url(get_template_directory_uri()); ?>/assets/mp3/caceria.mp3"
       preload="none" loop></audio>

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
    var audio    = document.getElementById('trocha-persistent-audio');
    var player   = document.getElementById('trocha-miniplayer');
    var btn      = document.getElementById('trocha-mini-btn');
    var SK       = 'trocha_audio_playing';
    var SK_TIME  = 'trocha_audio_time';
    var isPlaying = false;

    function setPlaying(state) {
        isPlaying = state;
        btn.classList.toggle('is-playing', state);
        player.classList.toggle('is-playing', state);
        // Notificar al botón grande de la home
        document.dispatchEvent(new CustomEvent('trocha:playstate', { detail: { playing: state } }));
    }

    function play() {
        audio.volume = 0.75;
        audio.play().then(function(){
            setPlaying(true);
            sessionStorage.setItem(SK, '1');
        }).catch(function(){});
    }

    function pause() {
        audio.pause();
        setPlaying(false);
        sessionStorage.setItem(SK, '0');
    }

    // Guardar tiempo actual justo antes de salir de la página
    window.addEventListener('beforeunload', function(){
        sessionStorage.setItem(SK_TIME, audio.currentTime);
        sessionStorage.setItem(SK, isPlaying ? '1' : '0');
    });

    // Al cargar: si estaba sonando, reanudar desde donde quedó
    var wasPlaying = sessionStorage.getItem(SK) === '1';
    var savedTime  = parseFloat(sessionStorage.getItem(SK_TIME) || '0');

    if (wasPlaying) {
        audio.addEventListener('canplay', function onCanPlay(){
            audio.removeEventListener('canplay', onCanPlay);
            audio.currentTime = savedTime || 0;
            play();
        }, { once: true });
        audio.load();
    }

    btn.addEventListener('click', function(){
        if (isPlaying) { pause(); } else { play(); }
    });

})();

</script>

<!-- =====================================================
     PJAX — Navegación sin recarga para que el audio no se corte
     ===================================================== -->
<script>
(function(){
    var container = document.getElementById('trocha-pjax-container');
    if (!container) return;

    // No interceptar estos links
    function shouldIgnore(href, el) {
        if (!href) return true;
        // Atributo explícito de exclusión
        if (el && (el.hasAttribute('data-pjax-ignore') || el.closest('[data-pjax-ignore]'))) return true;
        var url = new URL(href, location.href);
        if (url.origin !== location.origin) return true;          // externo
        if (url.pathname.match(/\.(pdf|jpg|png|zip|mp3|wav)$/i)) return true; // archivo
        if (href.indexOf('#') === 0) return true;                 // ancla
        if (url.pathname.indexOf('/wp-admin') === 0) return true; // admin
        if (url.pathname.indexOf('/wp-login') === 0) return true;
        // NUNCA interceptar carrito, checkout ni mi cuenta — WooCommerce necesita recarga completa
        if (url.pathname.indexOf('/carrito') >= 0) return true;
        if (url.pathname.indexOf('/finalizar-compra') >= 0) return true;
        if (url.pathname.indexOf('/checkout') >= 0) return true;
        if (url.pathname.indexOf('/cart') >= 0) return true;
        if (url.pathname.indexOf('/mi-cuenta') >= 0) return true;
        if (url.search.indexOf('add-to-cart') >= 0) return true;  // botón añadir al carrito
        // No interceptar páginas de producto — WooCommerce necesita sus scripts de variación
        if (url.pathname.indexOf('/producto/') >= 0) return true;
        if (url.pathname.indexOf('/product/') >= 0) return true;
        return false;
    }

    var isLoading = false;

    function navigate(url, push) {
        if (isLoading) return;
        isLoading = true;

        // Fade out suave del contenido actual
        container.style.transition = 'opacity 0.25s';
        container.style.opacity = '0.3';

        fetch(url, { headers: { 'X-PJAX': '1' } })
            .then(function(r) { return r.text(); })
            .then(function(html) {
                var parser = new DOMParser();
                var doc = parser.parseFromString(html, 'text/html');

                // Extraer nuevo <main id="trocha-pjax-container">
                var newMain = doc.getElementById('trocha-pjax-container');
                if (!newMain) {
                    // Fallback: recarga normal
                    location.href = url;
                    return;
                }

                // Actualizar título
                var newTitle = doc.querySelector('title');
                if (newTitle) document.title = newTitle.textContent;

                // Actualizar body classes (para que WooCommerce/Elementor detecten la página)
                var newBodyClass = doc.body.className;
                if (newBodyClass) document.body.className = newBodyClass;

                // Reemplazar contenido
                container.innerHTML = newMain.innerHTML;
                container.className = newMain.className;

                // Fade in
                container.style.opacity = '0.3';
                requestAnimationFrame(function(){
                    container.style.transition = 'opacity 0.3s';
                    container.style.opacity = '1';
                });

                // Push state
                if (push !== false) {
                    history.pushState({ pjax: true, url: url }, document.title, url);
                }

                // Scroll al top
                window.scrollTo(0, 0);

                // Re-ejecutar scripts inline del nuevo contenido
                var scripts = container.querySelectorAll('script');
                scripts.forEach(function(oldScript) {
                    var s = document.createElement('script');
                    if (oldScript.src) {
                        s.src = oldScript.src;
                    } else {
                        s.textContent = oldScript.textContent;
                    }
                    document.head.appendChild(s).parentNode.removeChild(s);
                });

                // Re-init botón play de la home si existe
                var playBtn = document.getElementById('trocha-play-btn');
                if (playBtn) {
                    // Sincronizar estado con el mini-player
                    var miniBtn = document.getElementById('trocha-mini-btn');
                    var isNowPlaying = miniBtn && miniBtn.classList.contains('is-playing');
                    playBtn.classList.toggle('is-playing', isNowPlaying);
                    var lbl = document.getElementById('trocha-play-label');
                    if (lbl) lbl.textContent = isNowPlaying ? 'STOP' : 'PRESS PLAY';

                    playBtn.addEventListener('click', function() {
                        var mb = document.getElementById('trocha-mini-btn');
                        if (mb) mb.click();
                    });
                }

                isLoading = false;
            })
            .catch(function() {
                // Si falla fetch, navegar normal
                location.href = url;
                isLoading = false;
            });
    }

    // Interceptar clicks en links
    document.addEventListener('click', function(e) {
        var link = e.target.closest('a[href]');
        if (!link) return;
        var href = link.getAttribute('href');
        if (shouldIgnore(href, link)) return;
        // Ignorar links que abran en nueva pestaña
        if (link.target && link.target !== '_self') return;
        // Si la página actual tiene formularios WooCommerce, no interceptar
        if (document.querySelector('form.cart, .woocommerce-checkout, .woocommerce-cart-form')) return;
        // Si el link tiene clases WooCommerce, no interceptar
        if (link.classList.contains('add_to_cart_button') ||
            link.classList.contains('ajax_add_to_cart') ||
            link.classList.contains('single_add_to_cart_button')) return;

        e.preventDefault();
        navigate(link.href, true);
    });

    // Botón atrás/adelante del navegador
    window.addEventListener('popstate', function(e) {
        if (e.state && e.state.pjax) {
            navigate(location.href, false);
        }
    });

})();
</script>

<script>
(function(){
    var burger  = document.getElementById('trocha-burger');
    var drawer  = document.getElementById('trocha-drawer');
    var overlay = document.getElementById('trocha-drawer-overlay');
    var closeBtn = document.getElementById('trocha-drawer-close');
    if (!burger || !drawer || !overlay) return;

    function openDrawer(){
        drawer.classList.add('is-open');
        overlay.classList.add('is-open');
        burger.classList.add('is-active');
        burger.setAttribute('aria-expanded','true');
        drawer.setAttribute('aria-hidden','false');
        document.body.style.overflow = 'hidden';
    }
    function closeDrawer(){
        drawer.classList.remove('is-open');
        overlay.classList.remove('is-open');
        burger.classList.remove('is-active');
        burger.setAttribute('aria-expanded','false');
        drawer.setAttribute('aria-hidden','true');
        document.body.style.overflow = '';
    }

    burger.addEventListener('click', function(){
        drawer.classList.contains('is-open') ? closeDrawer() : openDrawer();
    });
    overlay.addEventListener('click', closeDrawer);
    if (closeBtn) closeBtn.addEventListener('click', closeDrawer);
    document.addEventListener('keydown', function(e){
        if (e.key === 'Escape') closeDrawer();
    });
})();
</script>

</body>
</html>
