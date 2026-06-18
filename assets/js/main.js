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
        // reveal class removed: opacity controlled via CSS only

        // Helper reveal
        function revealEl(el) {
            if (el.classList.contains('reveal-in')) return;
            var parent = el.parentElement;
            if (parent && parent.classList.contains('trocha-products-grid')) {
                var idx = Array.prototype.indexOf.call(parent.children, el);
                el.style.transitionDelay = (Math.min(idx, 8) * 70) + 'ms';
            }
            el.classList.add('reveal-in');
        }

        var io = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                if (entry.isIntersecting) {
                    revealEl(entry.target);
                    io.unobserve(entry.target);
                }
            });
        }, { threshold: 0.05, rootMargin: '0px 0px 0px 0px' });

        els.forEach(function (el) { io.observe(el); });

        // SAFETY NET: tras 1.2s revelar todo lo que siga oculto
        setTimeout(function () { els.forEach(revealEl); }, 1200);
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

    /* ---- Add-to-cart feedback (pulse + toast + count update) --------------- */
    function updateCartCounts() {
        fetch(trochaData.ajaxUrl + '?action=trocha_get_cart_count')
            .then(function(r){ return r.json(); })
            .then(function(data){
                if (data && data.count !== undefined) {
                    var els = document.querySelectorAll('#trocha-cart-count, #trocha-drawer-cart-count');
                    els.forEach(function(el){ el.textContent = data.count; });
                }
            })
            .catch(function(){});
    }
    function initCartFeedback() {
        document.body.addEventListener('added_to_cart', function () {
            var cart = document.getElementById('trocha-cart');
            if (cart) {
                cart.classList.remove('cart-bump');
                void cart.offsetWidth;
                cart.classList.add('cart-bump');
            }
            showToast('Añadido al carrito');
            updateCartCounts();
        });
        document.body.addEventListener('removed_from_cart', function () {
            updateCartCounts();
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
window.trochaScrollRevealInit = initScrollReveal;
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

/* ── MOCKUP THUMBNAIL STRIP — horizontal scroll + auto-center active ──── */
(function() {
    function setupMockupThumbs() {
        var thumbs = document.querySelector('.woocommerce-product-gallery .flex-control-thumbs');
        if (!thumbs) return;

        /* Mousewheel horizontal scroll */
        thumbs.addEventListener('wheel', function(e) {
            if (Math.abs(e.deltaY) > Math.abs(e.deltaX)) {
                e.preventDefault();
                thumbs.scrollLeft += e.deltaY;
            }
        }, { passive: false });

        /* Auto-scroll active thumbnail into view */
        var observer = new MutationObserver(function() {
            var activeImg = thumbs.querySelector('img.flex-active');
            if (activeImg) {
                var li = activeImg.closest('li');
                if (li) {
                    li.scrollIntoView({ behavior: 'smooth', block: 'nearest', inline: 'center' });
                }
            }
        });
        observer.observe(thumbs, { attributes: true, subtree: true, attributeFilter: ['class', 'src'] });

        /* Touch/drag scroll */
        var isDown = false, startX, scrollStart;
        thumbs.addEventListener('mousedown', function(e) {
            isDown = true;
            startX = e.pageX - thumbs.offsetLeft;
            scrollStart = thumbs.scrollLeft;
            thumbs.style.cursor = 'grabbing';
        });
        thumbs.addEventListener('mouseleave', function() { isDown = false; thumbs.style.cursor = ''; });
        thumbs.addEventListener('mouseup', function() { isDown = false; thumbs.style.cursor = ''; });
        thumbs.addEventListener('mousemove', function(e) {
            if (!isDown) return;
            e.preventDefault();
            var x = e.pageX - thumbs.offsetLeft;
            var walk = (x - startX) * 2;
            thumbs.scrollLeft = scrollStart - walk;
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', setupMockupThumbs);
    } else {
        setupMockupThumbs();
    }

    /* Re-run after WooCommerce variation changes (gallery reloads) */
    jQuery && jQuery(document).on('found_variation', function() {
        setTimeout(setupMockupThumbs, 100);
    });
window.trochaMockupThumbsInit = function() { setupMockupThumbs(); };
})();



/* ── PRODUCT IMAGE ZOOM: cursor-follow, any selected mockup ──── */
(function() {
    var ZOOM = 2.8;
    var viewport = null;
    var currentImg = null;
    var zoomedIn = false;

    function getImg() {
        /* Always target the currently visible/active mockup, not the first in DOM */
        var img = document.querySelector('.flex-viewport .flex-active-slide img');
        if (!img) img = document.querySelector('.woocommerce-product-gallery__image.flex-active-slide img');
        if (!img) img = document.querySelector('.flex-viewport .woocommerce-product-gallery__wrapper .flex-active-slide img');
        /* Fallback: any visible (non-clone) image */
        if (!img) img = document.querySelector('.flex-viewport .woocommerce-product-gallery__image:not(.clone) img');
        return img;
    }

    function onEnter() {
        currentImg = getImg();
        if (!currentImg) return;
        currentImg.style.transition = 'transform 0.12s ease-out, transform-origin 0.08s ease-out';
        currentImg.style.transform = 'scale(' + ZOOM + ')';
        currentImg.style.maxHeight = 'none';
        currentImg.style.maxWidth = 'none';
        zoomedIn = true;
    }

    function onMove(e) {
        if (!zoomedIn) {
            currentImg = getImg();
            if (!currentImg) return;
            currentImg.style.transition = 'transform 0.12s ease-out, transform-origin 0.08s ease-out';
            currentImg.style.transform = 'scale(' + ZOOM + ')';
            currentImg.style.maxHeight = 'none';
            currentImg.style.maxWidth = 'none';
            zoomedIn = true;
        }
        var r = viewport.getBoundingClientRect();
        var xPct = ((e.clientX - r.left) / r.width) * 100;
        var yPct = ((e.clientY - r.top) / r.height) * 100;
        /* Always operate on the live image so mockup switches work */
        var img = getImg();
        if (img) img.style.transformOrigin = xPct + '% ' + yPct + '%';
    }

    function onLeave() {
        var img = getImg();
        if (img) {
            img.style.transform = 'scale(1)';
            img.style.transformOrigin = 'center center';
            img.style.maxHeight = '620px';
            img.style.maxWidth = '100%';
        }
        zoomedIn = false;
        currentImg = null;
    }

    function init() {
        viewport = document.querySelector('.flex-viewport');
        if (!viewport) return;
        viewport.style.overflow = 'hidden';
        viewport.style.cursor = 'crosshair';

        viewport.addEventListener('mouseenter', onEnter);
        viewport.addEventListener('mousemove', onMove);
        viewport.addEventListener('mouseleave', onLeave);

        /* Touch: drag to pan */
        viewport.addEventListener('touchmove', function(e) {
            if (e.touches.length !== 1) return;
            e.preventDefault();
            var t = e.touches[0];
            var r = viewport.getBoundingClientRect();
            var xPct = ((t.clientX - r.left) / r.width) * 100;
            var yPct = ((t.clientY - r.top) / r.height) * 100;
            var img = getImg();
            if (!img) return;
            if (!zoomedIn) {
                img.style.transform = 'scale(' + ZOOM + ')';
                img.style.maxHeight = 'none';
                img.style.maxWidth = 'none';
                zoomedIn = true;
            }
            img.style.transformOrigin = xPct + '% ' + yPct + '%';
        }, { passive: false });

        viewport.addEventListener('touchend', function() {
            var img = getImg();
            if (img) {
                img.style.transform = 'scale(1)';
                img.style.transformOrigin = 'center center';
                img.style.maxHeight = '620px';
                img.style.maxWidth = '100%';
            }
            zoomedIn = false;
        });

        /* Re-init on WooCommerce events (variation change / gallery reset) */
        jQuery && jQuery(document).off('found_variation.zoom reset_data.zoom').on('found_variation reset_data', function() {
            zoomedIn = false;
            currentImg = null;
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function() { setTimeout(init, 500); });
    } else {
        setTimeout(init, 500);
    }
    /* Extra retry for flexslider delay */
    setTimeout(init, 1200);
window.trochaZoomInit = function() { init(); };
})();

/* ── DRAWER MENU TOGGLE ── */
window.trochaDrawerInit = function() {
    var burger = document.getElementById('trocha-burger');
    var drawer = document.getElementById('trocha-drawer');
    var overlay = document.getElementById('trocha-drawer-overlay');
    var closeBtn = document.getElementById('trocha-drawer-close');
    
    if (!burger || !drawer || !overlay || !closeBtn) return;
    
    function open() {
        burger.classList.add('is-active');
        burger.setAttribute('aria-expanded', 'true');
        drawer.classList.add('is-open');
        drawer.setAttribute('aria-hidden', 'false');
        overlay.classList.add('is-open');
        document.body.style.overflow = 'hidden';
    }
    function closeDrawer() {
        burger.classList.remove('is-active');
        burger.setAttribute('aria-expanded', 'false');
        drawer.classList.remove('is-open');
        drawer.setAttribute('aria-hidden', 'true');
        overlay.classList.remove('is-open');
        document.body.style.overflow = '';
    }
    
    // Replace with clone to remove old listeners
    var newBurger = burger.cloneNode(true);
    var newClose = closeBtn.cloneNode(true);
    var newOverlay = overlay.cloneNode(true);
    burger.parentNode.replaceChild(newBurger, burger);
    closeBtn.parentNode.replaceChild(newClose, closeBtn);
    overlay.parentNode.replaceChild(newOverlay, overlay);
    
    newBurger.addEventListener('click', function() {
        var d = document.getElementById('trocha-drawer');
        if (d && d.classList.contains('is-open')) { closeDrawer(); }
        else { open(); }
    });
    newClose.addEventListener('click', closeDrawer);
    newOverlay.addEventListener('click', closeDrawer);
    
    // ESC to close
    document.addEventListener('keydown', function(e) {
        var d = document.getElementById('trocha-drawer');
        if (e.key === 'Escape' && d && d.classList.contains('is-open')) closeDrawer();
    });
};

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', window.trochaDrawerInit);
} else {
    window.trochaDrawerInit();
}


/* ── Re-init after SPA page swap ──── */
window.addEventListener('trocha:page_swapped', function() {
    /* Re-init zoom — needs delay for flexslider to rebuild DOM */
    if (typeof window.trochaZoomInit === 'function') {
        setTimeout(window.trochaZoomInit, 400);
        setTimeout(window.trochaZoomInit, 1200);
    }
    /* Re-init mockup thumbnails */
    if (typeof window.trochaMockupThumbsInit === 'function') {
        setTimeout(window.trochaMockupThumbsInit, 200);
        setTimeout(window.trochaMockupThumbsInit, 800);
    }
    /* Re-init scroll reveal for new elements */
    if (typeof window.trochaScrollRevealInit === 'function') {
        setTimeout(window.trochaScrollRevealInit, 100);
    }
    /* Re-init miniplayer button */
    if (typeof initPlayButton === 'function') initPlayButton();
    /* Re-init product carousel slider */
    if (typeof window.trochaSliderInit === 'function') {
        setTimeout(window.trochaSliderInit, 200);
    }
    /* Re-init drawer menu */
    if (typeof window.trochaDrawerInit === 'function') {
        window.trochaDrawerInit();
    }
});


/* ── PRODUCT CAROUSEL: cursor-follow + drag + snap ── */
window.trochaSliderInit = function() {
    // Clean up previous instance
    if (window._trochaSliderCleanup) {
        window._trochaSliderCleanup();
        window._trochaSliderCleanup = null;
    }

    var wrap = document.getElementById("trochaSlider");
    var track = document.getElementById("trochaTrack");
    if (!wrap || !track) return;
    var allItems = track.querySelectorAll(".trs__item");
    var realItems = track.querySelectorAll(".trs__item:not(.trs__item--clone)");
    var total = realItems.length;
    if (!total) return;

    function iw() { return allItems[0] ? allItems[0].getBoundingClientRect().width : 0; }

    var offset = 0, itemW = 0, baseW = 0;
    var initTimeout;
    function init() {
        var w = iw();
        if (w === 0) { initTimeout = setTimeout(init, 100); return; }
        itemW = w; baseW = w * total;
        offset = baseW;
        track.style.transition = "none";
        track.style.transform = "translateX(-" + offset + "px)";
    }
    init();

    var onResize = function() { setTimeout(init, 50); };
    window.addEventListener("resize", onResize);

    /* Cursor-follow */
    var targetOffset = offset;
    var hovering = false;
    var raf = null;

    function lerp(a, b, t) { return a + (b - a) * t; }

    function smoothScroll() {
        offset = lerp(offset, targetOffset, 0.08);
        track.style.transform = "translateX(-" + offset + "px)";
        var w = itemW || iw();
        if (w === 0) { raf = requestAnimationFrame(smoothScroll); return; }
        if (offset < w / 2) { offset += baseW; targetOffset += baseW; track.style.transform = "translateX(-" + offset + "px)"; }
        else if (offset > baseW * 2 - w / 2) { offset -= baseW; targetOffset -= baseW; track.style.transform = "translateX(-" + offset + "px)"; }
        raf = requestAnimationFrame(smoothScroll);
    }

    wrap.addEventListener("mouseenter", function() {
        hovering = true;
        if (!raf) raf = requestAnimationFrame(smoothScroll);
    });

    wrap.addEventListener("mousemove", function(e) {
        if (!hovering) return;
        var rect = wrap.getBoundingClientRect();
        var pct = (e.clientX - rect.left) / rect.width;
        pct = Math.max(0, Math.min(1, pct));
        var w = iw();
        if (w === 0) return;
        var totalScroll = baseW - w;
        targetOffset = baseW - w / 2 + pct * totalScroll;
    });

    wrap.addEventListener("mouseleave", function() {
        hovering = false;
        snap();
    });

    function snap() {
        var w = iw();
        if (w === 0) return;
        var idx = Math.round(offset / w);
        targetOffset = idx * w;
        function snapLoop() {
            offset = lerp(offset, targetOffset, 0.15);
            track.style.transform = "translateX(-" + offset + "px)";
            if (Math.abs(offset - targetOffset) < 0.5) {
                offset = targetOffset;
                track.style.transform = "translateX(-" + offset + "px)";
                return;
            }
            requestAnimationFrame(snapLoop);
        }
        requestAnimationFrame(snapLoop);
    }

    /* Drag (touch + mouse fallback) */
    var sx = 0, so = 0, drag = false, moved = false;
    function pStart(x) { drag = true; moved = false; sx = x; so = offset; targetOffset = offset; track.style.transition = "none"; }
    function pMove(x) { if (!drag) return; var d = x - sx; if (Math.abs(d) > 3) moved = true; offset = so - d; targetOffset = offset; track.style.transform = "translateX(-" + offset + "px)"; }
    function pEnd() { if (!drag) return; drag = false; snap(); }
    wrap.addEventListener("mousedown", function(e) { if (e.button !== 0) return; pStart(e.clientX); });
    var onWinMouseMove = function(e) { if (drag) pMove(e.clientX); };
    var onWinMouseUp = function() { if (drag) pEnd(); };
    window.addEventListener("mousemove", onWinMouseMove);
    window.addEventListener("mouseup", onWinMouseUp);
    wrap.addEventListener("touchstart", function(e) { pStart(e.touches[0].clientX); }, { passive: true });
    wrap.addEventListener("touchmove", function(e) { pMove(e.touches[0].clientX); }, { passive: true });
    wrap.addEventListener("touchend", function() { pEnd(); });
    wrap.addEventListener("click", function(e) { if (moved) { e.preventDefault(); e.stopPropagation(); } }, true);

    /* Arrow buttons removed — slider uses cursor-follow + drag */

    // Store cleanup for re-init
    window._trochaSliderCleanup = function() {
        if (initTimeout) clearTimeout(initTimeout);
        if (raf) cancelAnimationFrame(raf);
        window.removeEventListener("resize", onResize);
        window.removeEventListener("mousemove", onWinMouseMove);
        window.removeEventListener("mouseup", onWinMouseUp);
    };
};

// Run on initial load
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', window.trochaSliderInit);
} else {
    window.trochaSliderInit();
}