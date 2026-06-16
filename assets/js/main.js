/* ============================================================
   TROCHA — UI/UX interactions
   Smooth scroll · scroll reveal · micro-interactions · feedback
   ============================================================ */
(function () {
    'use strict';

    var reduceMotion = window.matchMedia &&
        window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    document.documentElement.classList.add('js');

    /* ---- Page-load fade-in ---------------------------------- */
    function onReady(fn) {
        if (document.readyState !== 'loading') { fn(); }
        else { document.addEventListener('DOMContentLoaded', fn); }
    }

    onReady(function () {
        document.body.classList.add('trocha-loaded');

        initScrollReveal();
        initSmoothAnchors();
        initScrollProgress();
        initBackToTop();
        initRipples();
        initCardTilt();
        initStampRotation();
        initCartFeedback();
        initHeaderShrink();
    });

    /* ---- Scroll reveal (IntersectionObserver) --------------- */
    function initScrollReveal() {
        var selectors = [
            '.trocha-section',
            '.trocha-hero',
            '.trocha-product-card',
            '.trocha-categoria-card',
            '.trocha-page-title',
            '.trocha-single-product__summary',
            '.trocha-single-product__gallery',
            '.wpforms-container',
            '.woocommerce-checkout #customer_details',
            '.woocommerce-checkout #order_review',
            '.trocha-order-summary',
            '.trocha-elementor-content > .elementor'
        ];
        var els = document.querySelectorAll(selectors.join(','));
        if (!els.length) return;

        if (reduceMotion || !('IntersectionObserver' in window)) {
            els.forEach(function (el) { el.classList.add('reveal-in'); });
            return;
        }

        // Stagger items that share a parent grid.
        els.forEach(function (el) {
            el.classList.add('reveal');
        });

        var io = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                if (entry.isIntersecting) {
                    var el = entry.target;
                    // stagger within product grids
                    var parent = el.parentElement;
                    if (parent && parent.classList.contains('trocha-products-grid')) {
                        var idx = Array.prototype.indexOf.call(parent.children, el);
                        el.style.transitionDelay = (Math.min(idx, 8) * 70) + 'ms';
                    }
                    el.classList.add('reveal-in');
                    io.unobserve(el);
                }
            });
        }, { threshold: 0.12, rootMargin: '0px 0px -8% 0px' });

        els.forEach(function (el) { io.observe(el); });
    }

    /* ---- Smooth anchor scrolling ---------------------------- */
    function initSmoothAnchors() {
        document.addEventListener('click', function (e) {
            var a = e.target.closest('a[href^="#"]');
            if (!a) return;
            var id = a.getAttribute('href');
            if (id.length < 2) return;
            var target = document.querySelector(id);
            if (!target) return;
            e.preventDefault();
            target.scrollIntoView({
                behavior: reduceMotion ? 'auto' : 'smooth',
                block: 'start'
            });
        });
    }

    /* ---- Scroll progress bar -------------------------------- */
    function initScrollProgress() {
        var bar = document.createElement('div');
        bar.className = 'trocha-scroll-progress';
        document.body.appendChild(bar);

        var ticking = false;
        function update() {
            var h = document.documentElement;
            var max = h.scrollHeight - h.clientHeight;
            var pct = max > 0 ? (h.scrollTop / max) * 100 : 0;
            bar.style.width = pct + '%';
            ticking = false;
        }
        window.addEventListener('scroll', function () {
            if (!ticking) { window.requestAnimationFrame(update); ticking = true; }
        }, { passive: true });
        update();
    }

    /* ---- Back-to-top button --------------------------------- */
    function initBackToTop() {
        var btn = document.createElement('button');
        btn.className = 'trocha-to-top';
        btn.type = 'button';
        btn.setAttribute('aria-label', 'Volver arriba');
        btn.innerHTML = '\u2191';
        document.body.appendChild(btn);

        btn.addEventListener('click', function () {
            window.scrollTo({ top: 0, behavior: reduceMotion ? 'auto' : 'smooth' });
        });

        var ticking = false;
        function toggle() {
            if (window.scrollY > 500) { btn.classList.add('is-visible'); }
            else { btn.classList.remove('is-visible'); }
            ticking = false;
        }
        window.addEventListener('scroll', function () {
            if (!ticking) { window.requestAnimationFrame(toggle); ticking = true; }
        }, { passive: true });
    }

    /* ---- Button ripple -------------------------------------- */
    function initRipples() {
        if (reduceMotion) return;
        document.addEventListener('click', function (e) {
            var btn = e.target.closest('.trocha-btn');
            if (!btn) return;
            var rect = btn.getBoundingClientRect();
            var circle = document.createElement('span');
            var size = Math.max(rect.width, rect.height);
            circle.className = 'trocha-ripple';
            circle.style.width = circle.style.height = size + 'px';
            circle.style.left = (e.clientX - rect.left - size / 2) + 'px';
            circle.style.top = (e.clientY - rect.top - size / 2) + 'px';
            btn.appendChild(circle);
            setTimeout(function () { circle.remove(); }, 600);
        });
    }

    /* ---- Subtle 3D tilt on cards (desktop) ------------------ */
    function initCardTilt() {
        if (reduceMotion || window.matchMedia('(hover: none)').matches) return;
        var cards = document.querySelectorAll('.trocha-product-card, .trocha-categoria-card');
        cards.forEach(function (card) {
            card.addEventListener('mousemove', function (e) {
                var r = card.getBoundingClientRect();
                var px = (e.clientX - r.left) / r.width - 0.5;
                var py = (e.clientY - r.top) / r.height - 0.5;
                card.style.transform =
                    'perspective(700px) rotateX(' + (-py * 4) + 'deg) rotateY(' + (px * 4) + 'deg) translateY(-4px)';
            });
            card.addEventListener('mouseleave', function () {
                card.style.transform = '';
            });
        });
    }

    /* ---- Random stamp rotation ------------------------------ */
    function initStampRotation() {
        var stamps = document.querySelectorAll('.trocha-product-card__stamp');
        stamps.forEach(function (s) {
            var deg = (Math.random() * 8 - 4).toFixed(1);
            s.style.transform = 'rotate(' + deg + 'deg)';
        });
    }

    /* ---- Header shrink on scroll ---------------------------- */
    function initHeaderShrink() {
        var bar = document.querySelector('.trocha-toolbar--top');
        if (!bar) return;
        var ticking = false;
        function update() {
            if (window.scrollY > 40) { bar.classList.add('is-scrolled'); }
            else { bar.classList.remove('is-scrolled'); }
            ticking = false;
        }
        window.addEventListener('scroll', function () {
            if (!ticking) { window.requestAnimationFrame(update); ticking = true; }
        }, { passive: true });
        update();
    }

    /* ---- Add-to-cart feedback (pulse + toast) --------------- */
    function initCartFeedback() {
        document.body.addEventListener('added_to_cart', function () {
            var cart = document.getElementById('trocha-cart');
            if (cart) {
                cart.classList.remove('cart-bump');
                // force reflow to restart animation
                void cart.offsetWidth;
                cart.classList.add('cart-bump');
            }
            showToast('Añadido al carrito');
        });
    }

    var toastTimer;
    function showToast(msg) {
        var toast = document.getElementById('trocha-toast');
        if (!toast) {
            toast = document.createElement('div');
            toast.id = 'trocha-toast';
            toast.className = 'trocha-toast';
            document.body.appendChild(toast);
        }
        toast.textContent = msg;
        toast.classList.add('is-visible');
        clearTimeout(toastTimer);
        toastTimer = setTimeout(function () {
            toast.classList.remove('is-visible');
        }, 2200);
    }
    window.trochaToast = showToast;

    /* ---- Botón Play / Stop — delega en el mini-player persistente --- */
    function initPlayButton() {
        var btn   = document.getElementById('trocha-play-btn');
        var label = document.getElementById('trocha-play-label');
        if (!btn) return;

        // Escuchar eventos del mini-player global (definido en footer.php)
        function syncState(playing) {
            btn.classList.toggle('is-playing', playing);
            if (label) label.textContent = playing ? 'STOP' : 'PRESS PLAY';
        }

        btn.addEventListener('click', function() {
            // Disparar click en el mini-player para que haya una sola fuente de verdad
            var miniBtn = document.getElementById('trocha-mini-btn');
            if (miniBtn) miniBtn.click();
        });

        // Escuchar cambios de estado del mini-player
        document.addEventListener('trocha:playstate', function(e) {
            syncState(e.detail && e.detail.playing);
        });
    }

    onReady(function() {
        initPlayButton();
    });

})();
