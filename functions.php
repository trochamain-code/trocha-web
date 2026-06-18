<?php
defined('ABSPATH') || exit;

require_once get_template_directory() . '/inc/setup.php';
require_once get_template_directory() . '/inc/enqueue.php';
require_once get_template_directory() . '/inc/smtp.php';
require_once get_template_directory() . '/inc/contact-form.php';

if (class_exists('WooCommerce')) {
    require_once get_template_directory() . '/inc/woocommerce-setup.php';
    require_once get_template_directory() . '/inc/product-seeder.php';
    require_once get_template_directory() . '/inc/payments.php';
}

function trocha_elementor_support() {
    add_theme_support('elementor');
}
add_action('after_setup_theme', 'trocha_elementor_support');

add_action('elementor/theme/register_locations', function($elementor_theme_manager) {
    $elementor_theme_manager->register_location('header');
    $elementor_theme_manager->register_location('footer');
});

function trocha_create_categorias_pies_page() {
    $slug = 'categorias-pies';
    $existing = get_page_by_path($slug);
    if (!$existing) {
        wp_insert_post([
            'post_title'   => 'Vistete por los Pies',
            'post_name'    => $slug,
            'post_type'    => 'page',
            'post_status'  => 'publish',
            'page_template' => 'page-categorias-pies.php',
        ]);
    }
}
add_action('init', 'trocha_create_categorias_pies_page');

function trocha_create_historia_page() {
    $slug = 'historia';
    $existing = get_page_by_path($slug);
    if (!$existing) {
        wp_insert_post([
            'post_title'   => 'Camino Propio — Historia',
            'post_name'    => $slug,
            'post_type'    => 'page',
            'post_status'  => 'publish',
            'page_template' => 'page-historia.php',
        ]);
    }
}
add_action('init', 'trocha_create_historia_page');

function trocha_ensure_wc_pages() {
    if (!class_exists('WooCommerce')) return;

    // Native WooCommerce block content for cart & checkout.
    $block_cart     = '<!-- wp:woocommerce/cart --><div class="wp-block-woocommerce-cart is-loading">[woocommerce_cart]</div><!-- /wp:woocommerce/cart -->';
    $block_checkout = '<!-- wp:woocommerce/checkout --><div class="wp-block-woocommerce-checkout is-loading">[woocommerce_checkout]</div><!-- /wp:woocommerce/checkout -->';

    $pages = [
        'carrito' => [
            'title'   => 'Carrito',
            'content' => $block_cart,
            'option'  => 'woocommerce_cart_page_id',
        ],
        'checkout' => [
            'title'   => 'Checkout',
            'content' => $block_checkout,
            'option'  => 'woocommerce_checkout_page_id',
        ],
        'tienda' => [
            'title'   => 'Tienda',
            'content' => '',
            'option'  => 'woocommerce_shop_page_id',
        ],
        'mi-cuenta' => [
            'title'   => 'Mi cuenta',
            'content' => '[woocommerce_my_account]',
            'option'  => 'woocommerce_myaccount_page_id',
        ],
    ];
    foreach ($pages as $slug => $data) {
        $existing = get_page_by_path($slug);
        if ($existing) {
            $id = $existing->ID;
        } else {
            $id = wp_insert_post([
                'post_title'   => $data['title'],
                'post_name'    => $slug,
                'post_type'    => 'page',
                'post_status'  => 'publish',
                'post_content' => $data['content'],
            ]);
        }
        // Always (re)link the page to its WooCommerce option.
        if ($id && !is_wp_error($id) && (int) get_option($data['option']) !== (int) $id) {
            update_option($data['option'], $id);
        }
    }

    // One-time migration: convert legacy shortcode checkout to native block.
    if (get_option('trocha_block_checkout_migrated') !== '1') {
        $checkout_id = (int) get_option('woocommerce_checkout_page_id');
        if ($checkout_id) {
            $post = get_post($checkout_id);
            if ($post && strpos($post->post_content, 'wp:woocommerce/checkout') === false) {
                wp_update_post([
                    'ID'           => $checkout_id,
                    'post_content' => $block_checkout,
                ]);
            }
        }
        update_option('trocha_block_checkout_migrated', '1');
    }
}
add_action('init', 'trocha_ensure_wc_pages');

/**
 * Full site header: brand, toolbar (home+back), marquee, sidebar, body-grid.
 * Hooked to wp_body_open (proven to render on every page, bypasses plugin buffers).
 * Static flag prevents double-output when the hook fires more than once.
 */
function trocha_site_header() {
    static $fired = false;
    if ( $fired ) return;
    $fired = true;

    $logo_url  = get_template_directory_uri() . '/assets/img/logo.svg';
    $is_home   = ( is_front_page() || is_home() );

    $wcon      = function_exists('WC') && class_exists('WooCommerce');
    $cart_url  = $wcon && function_exists('wc_get_cart_url') && wc_get_cart_url()
                 ? wc_get_cart_url() : home_url('/carrito');
    $cart_count = $wcon && isset(WC()->cart) ? WC()->cart->get_cart_contents_count() : 0;
    ?>

    <div class="trocha-toolbar trocha-toolbar--top">
        <div class="trocha-toolbar__left">
            <button type="button" class="trocha-burger" id="trocha-burger" aria-label="Menú" aria-expanded="false" aria-controls="trocha-drawer">
                <span></span><span></span><span></span>
            </button>
            <?php if ( ! $is_home ) : ?>
                <a href="javascript:history.back()" class="trocha-toolbar__back">&larr; VOLVER</a>
            <?php endif; ?>
        </div>
        <div class="trocha-toolbar__nav">
            <a href="<?php echo esc_url( home_url('/') ); ?>" class="trocha-toolbar__home">&#8962; HOME</a>
            <a href="<?php echo esc_url( home_url('/tienda') ); ?>">TIENDA</a>
            <a href="<?php echo esc_url( home_url('/categorias-pies') ); ?>">COLECCIONES</a>
            <a href="<?php echo esc_url( home_url('/carrito') ); ?>">PEDIDO</a>
        </div>
        <div class="trocha-toolbar__cart">
            <a href="<?php echo esc_url($cart_url); ?>" class="trocha-cart__link" id="trocha-cart">
                <span class="trocha-cart__icon">&#128722;</span>
                <span class="trocha-cart__label">CARRITO</span>
                <span class="trocha-cart__count" id="trocha-cart-count"><?php echo (int)$cart_count; ?></span>
            </a>
        </div>
    </div>

    <!-- Mobile drawer menu -->
    <div class="trocha-drawer-overlay" id="trocha-drawer-overlay"></div>
    <nav class="trocha-drawer" id="trocha-drawer" aria-hidden="true">
        <div class="trocha-drawer__head">
            <span class="trocha-drawer__title">MENÚ</span>
            <button type="button" class="trocha-drawer__close" id="trocha-drawer-close" aria-label="Cerrar">&times;</button>
        </div>
        <ul class="trocha-drawer__list">
            <li><a href="<?php echo esc_url( home_url('/') ); ?>">INICIO</a></li>
            <li><a href="<?php echo esc_url( home_url('/tienda') ); ?>">TIENDA</a></li>
            <li><a href="<?php echo esc_url( home_url('/categorias-pies') ); ?>">COLECCIONES</a></li>
            <li><a href="<?php echo esc_url( home_url('/carrito') ); ?>">PEDIDO</a></li>
            <li><a href="<?php echo esc_url( home_url('/historia') ); ?>">CAMINO PROPIO</a></li>
            <li><a href="<?php echo esc_url( home_url('/contacto') ); ?>">CONTACTO</a></li>
            <li><a href="<?php echo esc_url( home_url('/mi-cuenta') ); ?>">MI CUENTA</a></li>
        </ul>
        <a href="<?php echo esc_url($cart_url); ?>" class="trocha-drawer__cart">
            VER CARRITO (<span id="trocha-drawer-cart-count"><?php echo (int)$cart_count; ?></span>)
        </a>
    </nav>

    <div class="trocha-top-banner">
        <img src="<?php echo esc_url(get_template_directory_uri()); ?>/assets/img/trocha-header-nuevo.jpg?v=1781745397"
             alt="TROCHA — No es ropa. Es camino."
             class="trocha-header-img">
    </div>

    <div class="trocha-marquee">
        <span>
            MUCHAS PALABRAS HEMOS REPRESENTADO, UNA ACTITUD NOS REPRESENTA. BUSCAVIDAS &mdash; MUCHAS PALABRAS HEMOS REPRESENTADO, UNA ACTITUD NOS REPRESENTA. BUSCAVIDAS &mdash; MUCHAS PALABRAS HEMOS REPRESENTADO, UNA ACTITUD NOS REPRESENTA. BUSCAVIDAS &mdash;
            MUCHAS PALABRAS HEMOS REPRESENTADO, UNA ACTITUD NOS REPRESENTA. BUSCAVIDAS &mdash; MUCHAS PALABRAS HEMOS REPRESENTADO, UNA ACTITUD NOS REPRESENTA. BUSCAVIDAS &mdash; MUCHAS PALABRAS HEMOS REPRESENTADO, UNA ACTITUD NOS REPRESENTA. BUSCAVIDAS &mdash;
    </div>

    <div class="trocha-body-grid trocha-body-grid--no-sidebar">

    <main class="trocha-main-content" id="trocha-pjax-container">
    <?php
}
add_action('wp_body_open', 'trocha_site_header', 5);

// AJAX: get cart count for mobile SPA updates
function trocha_get_cart_count() {
    $count = 0;
    if (class_exists('WooCommerce') && WC()->cart) {
        $count = WC()->cart->get_cart_contents_count();
    }
    wp_send_json(['count' => $count]);
}
add_action('wp_ajax_trocha_get_cart_count', 'trocha_get_cart_count');
add_action('wp_ajax_nopriv_trocha_get_cart_count', 'trocha_get_cart_count');
   // outputs the full header incl. cart

function trocha_cart_js() {
    if (!class_exists('WooCommerce')) return;
    ?>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        var countEl = document.getElementById('trocha-cart-count');
        var drawerCountEl = document.getElementById('trocha-drawer-cart-count');
        function refreshCount() {
            if (!countEl && !drawerCountEl) return;
            var req = new XMLHttpRequest();
            req.open('GET', '<?php echo admin_url('admin-ajax.php'); ?>?action=trocha_cart_count');
            req.onload = function() {
                if (req.status === 200) {
                    if (countEl) countEl.textContent = req.responseText;
                    if (drawerCountEl) drawerCountEl.textContent = req.responseText;
                }
            };
            req.send();
        }
        if (countEl || drawerCountEl) { refreshCount(); }

        // WooCommerce dispara added_to_cart como evento jQuery y también como nativo
        document.body.addEventListener('added_to_cart', refreshCount);

        // Soporte jQuery (WooCommerce lo usa internamente)
        if (typeof jQuery !== 'undefined') {
            jQuery(document.body).on('added_to_cart wc_fragments_refreshed', refreshCount);
        }

        document.querySelectorAll('.trocha-add-to-cart-form').forEach(function(form) {
            var select = form.querySelector('.trocha-size-select');
            var varInput = form.querySelector('.trocha-variation-id');
            if (!select || !varInput) return;

            function syncVariation() {
                var vmap = {};
                try { vmap = JSON.parse(select.dataset.vmap || '{}'); } catch (e) {}
                varInput.value = vmap[select.value] || '';
            }
            select.addEventListener('change', syncVariation);

            form.addEventListener('submit', function(e) {
                syncVariation();
                if (!select.value || !varInput.value) {
                    e.preventDefault();
                    select.focus();
                    select.style.borderColor = '#C4A000';
                }
                var btn = this.querySelector('button[type="submit"]');
                if (btn) {
                    btn.textContent = 'AÑADIENDO...';
                    btn.disabled = true;
                }
            });
        });

        /* ---- Form nativo de WooCommerce (single product) ---- */
        /* Garantizar que el botón "Añadir al carrito" siempre funcione.
           Re-habilitar si quedó disabled por un intento anterior fallido. */
        document.querySelectorAll('form.cart').forEach(function(form) {
            var btn = form.querySelector('.single_add_to_cart_button');
            if (!btn) return;

            form.addEventListener('submit', function() {
                // Re-habilitar tras 3s por si el AJAX falla silenciosamente
                if (btn.disabled) {
                    setTimeout(function() {
                        btn.disabled = false;
                        btn.classList.remove('disabled');
                    }, 3000);
                }
            });
        });
    });
    </script>
    <?php
}
add_action('wp_footer', 'trocha_cart_js');

function trocha_ajax_cart_count() {
    echo WC()->cart ? WC()->cart->get_cart_contents_count() : 0;
    wp_die();
}
add_action('wp_ajax_trocha_cart_count', 'trocha_ajax_cart_count');
add_action('wp_ajax_nopriv_trocha_cart_count', 'trocha_ajax_cart_count');

function trocha_ajax_guestbook_add() {
    $name = isset($_POST['gb_name']) ? substr(trim(wp_unslash($_POST['gb_name'])), 0, 30) : '';
    $message = isset($_POST['gb_message']) ? substr(trim(wp_unslash($_POST['gb_message'])), 0, 200) : '';
    if (empty($name) || empty($message)) {
        wp_die('error', 400);
    }
    $entries = get_option('trocha_guestbook_entries', []);
    $entries[] = [
        'name' => $name,
        'message' => $message,
        'date' => date('d/m/Y H:i'),
    ];
    $entries = array_slice($entries, -50);
    update_option('trocha_guestbook_entries', $entries, false);
    wp_die('ok');
}
add_action('wp_ajax_trocha_guestbook_add', 'trocha_ajax_guestbook_add');
add_action('wp_ajax_nopriv_trocha_guestbook_add', 'trocha_ajax_guestbook_add');


/* =====================================================
   CAMPO TELÉFONO — Prefijo con bandera real (custom dropdown)
   El <select> nativo de Windows no muestra emojis de bandera.
   Usamos un widget custom: imagen PNG (flagcdn.com) + prefijo.
   ===================================================== */

// Añadir campo hidden para guardar el prefijo — el dropdown real es 100% JS
add_filter('woocommerce_billing_fields', function($fields) {
    if (isset($fields['billing_phone'])) {
        // Campo hidden que el JS rellena con el prefijo seleccionado
        $phone_priority = $fields['billing_phone']['priority'] ?? 100;
        $fields['billing_phone_prefix'] = [
            'type'     => 'hidden',
            'label'    => '',
            'required' => false,
            'default'  => '+34',
            'priority' => $phone_priority - 1,
            'class'    => ['trocha-phone-prefix-hidden'],
        ];
        // Ajustar placeholder del teléfono
        $fields['billing_phone']['placeholder'] = '600 000 000';
    }
    return $fields;
}, 20);

// Guardar prefijo en los metadatos del pedido
add_action('woocommerce_checkout_update_order_meta', function($order_id) {
    if (!empty($_POST['billing_phone_prefix'])) {
        update_post_meta($order_id, '_billing_phone_prefix', sanitize_text_field($_POST['billing_phone_prefix']));
    }
});

// Inyectar el widget custom en el footer del checkout
add_action('wp_footer', function() {
    if (!is_checkout()) return;
    ?>
    <script>
    (function(){

    var COUNTRIES = [
        {code:'ES',prefix:'+34',name:'España',       colors:['#c60b1e','#f1bf00','#c60b1e']},
        {code:'US',prefix:'+1', name:'EE.UU.',       colors:['#B22234','#FFFFFF','#3C3B6E']},
        {code:'GB',prefix:'+44',name:'Reino Unido',  colors:['#012169','#FFFFFF','#C8102E']},
        {code:'FR',prefix:'+33',name:'Francia',      colors:['#002395','#FFFFFF','#ED2939']},
        {code:'DE',prefix:'+49',name:'Alemania',     colors:['#000000','#DD0000','#FFCE00']},
        {code:'IT',prefix:'+39',name:'Italia',       colors:['#009246','#FFFFFF','#CE2B37']},
        {code:'PT',prefix:'+351',name:'Portugal',    colors:['#006600','#FF0000','#FFD700']},
        {code:'MX',prefix:'+52',name:'México',       colors:['#006847','#FFFFFF','#CE1126']},
        {code:'AR',prefix:'+54',name:'Argentina',    colors:['#74ACDF','#FFFFFF','#74ACDF']},
        {code:'CO',prefix:'+57',name:'Colombia',     colors:['#FCD116','#003087','#CE1126']},
        {code:'CL',prefix:'+56',name:'Chile',        colors:['#D52B1E','#FFFFFF','#003087']},
        {code:'VE',prefix:'+58',name:'Venezuela',    colors:['#CF142B','#003087','#CF142B']},
        {code:'PE',prefix:'+51',name:'Perú',         colors:['#D91023','#FFFFFF','#D91023']},
        {code:'EC',prefix:'+593',name:'Ecuador',     colors:['#FFD100','#003087','#CE1126']},
        {code:'BO',prefix:'+591',name:'Bolivia',     colors:['#D52B1E','#F4E400','#007A3D']},
        {code:'PY',prefix:'+595',name:'Paraguay',    colors:['#D52B1E','#FFFFFF','#0038A8']},
        {code:'UY',prefix:'+598',name:'Uruguay',     colors:['#FFFFFF','#0038A8','#FFFFFF']},
        {code:'DO',prefix:'+1809',name:'R. Dominicana',colors:['#002D62','#FFFFFF','#CE1126']},
        {code:'CU',prefix:'+53',name:'Cuba',         colors:['#002A8F','#FFFFFF','#CF142B']},
        {code:'GT',prefix:'+502',name:'Guatemala',   colors:['#4997D0','#FFFFFF','#4997D0']},
        {code:'HN',prefix:'+504',name:'Honduras',    colors:['#0073CF','#FFFFFF','#0073CF']},
        {code:'SV',prefix:'+503',name:'El Salvador', colors:['#0F47AF','#FFFFFF','#0F47AF']},
        {code:'CR',prefix:'+506',name:'Costa Rica',  colors:['#002B7F','#FFFFFF','#CE1126']},
        {code:'PA',prefix:'+507',name:'Panamá',      colors:['#FFFFFF','#D21034','#003580']},
        {code:'BE',prefix:'+32',name:'Bélgica',      colors:['#000000','#FAE042','#EF3340']},
        {code:'NL',prefix:'+31',name:'Países Bajos', colors:['#AE1C28','#FFFFFF','#21468B']},
        {code:'CH',prefix:'+41',name:'Suiza',        colors:['#FF0000','#FFFFFF','#FF0000']},
        {code:'AT',prefix:'+43',name:'Austria',      colors:['#ED2939','#FFFFFF','#ED2939']},
        {code:'SE',prefix:'+46',name:'Suecia',       colors:['#006AA7','#FECC02','#006AA7']},
        {code:'NO',prefix:'+47',name:'Noruega',      colors:['#EF2B2D','#FFFFFF','#002868']},
        {code:'DK',prefix:'+45',name:'Dinamarca',    colors:['#C60C30','#FFFFFF','#C60C30']},
        {code:'FI',prefix:'+358',name:'Finlandia',   colors:['#FFFFFF','#003580','#FFFFFF']},
        {code:'PL',prefix:'+48',name:'Polonia',      colors:['#FFFFFF','#DC143C','#FFFFFF']},
        {code:'RU',prefix:'+7', name:'Rusia',        colors:['#FFFFFF','#0039A6','#D52B1E']},
        {code:'UA',prefix:'+380',name:'Ucrania',     colors:['#005BBB','#FFD500','#005BBB']},
        {code:'TR',prefix:'+90',name:'Turquía',      colors:['#E30A17','#FFFFFF','#E30A17']},
        {code:'GR',prefix:'+30',name:'Grecia',       colors:['#0D5EAF','#FFFFFF','#0D5EAF']},
        {code:'MA',prefix:'+212',name:'Marruecos',   colors:['#C1272D','#006233','#C1272D']},
        {code:'EG',prefix:'+20',name:'Egipto',       colors:['#CE1126','#FFFFFF','#000000']},
        {code:'ZA',prefix:'+27',name:'Sudáfrica',    colors:['#007A4D','#FFB612','#DE3831']},
        {code:'NG',prefix:'+234',name:'Nigeria',     colors:['#008751','#FFFFFF','#008751']},
        {code:'BR',prefix:'+55',name:'Brasil',       colors:['#009C3B','#FFDF00','#002776']},
        {code:'JP',prefix:'+81',name:'Japón',        colors:['#FFFFFF','#BC002D','#FFFFFF']},
        {code:'KR',prefix:'+82',name:'Corea del Sur',colors:['#FFFFFF','#003478','#CD2E3A']},
        {code:'CN',prefix:'+86',name:'China',        colors:['#DE2910','#FFDE00','#DE2910']},
        {code:'IN',prefix:'+91',name:'India',        colors:['#FF9933','#FFFFFF','#138808']},
        {code:'SA',prefix:'+966',name:'Arabia Saudí',colors:['#006C35','#FFFFFF','#006C35']},
        {code:'AE',prefix:'+971',name:'Emiratos',    colors:['#00732F','#FFFFFF','#FF0000']},
        {code:'AU',prefix:'+61',name:'Australia',    colors:['#00008B','#FFFFFF','#FF0000']},
        {code:'NZ',prefix:'+64',name:'Nueva Zelanda',colors:['#00247D','#FFFFFF','#CC142B']},
    ];

    var current = COUNTRIES[0]; // España por defecto

    // Genera un SVG circular con 3 franjas de los colores de la bandera
    function flagSVG(colors, size) {
        size = size || 20;
        var r = size / 2;
        var w = Math.ceil(size / 3);
        return 'data:image/svg+xml,' + encodeURIComponent(
            '<svg xmlns="http://www.w3.org/2000/svg" width="' + size + '" height="' + size + '" viewBox="0 0 ' + size + ' ' + size + '">' +
            '<defs><clipPath id="c"><circle cx="' + r + '" cy="' + r + '" r="' + r + '"/></clipPath></defs>' +
            '<g clip-path="url(#c)">' +
            '<rect x="0"       y="0" width="' + w       + '" height="' + size + '" fill="' + colors[0] + '"/>' +
            '<rect x="' + w  + '" y="0" width="' + w     + '" height="' + size + '" fill="' + colors[1] + '"/>' +
            '<rect x="' + (w*2) + '" y="0" width="' + (size - w*2) + '" height="' + size + '" fill="' + colors[2] + '"/>' +
            '</g>' +
            '</svg>'
        );
    }

    function buildWidget() {
        var phoneField = document.getElementById('billing_phone_field');
        if (!phoneField || document.getElementById('trocha-phone-widget')) return;

        var phoneInput = document.getElementById('billing_phone');
        if (!phoneInput) return;

        var inputWrapper = phoneInput.closest('.woocommerce-input-wrapper') || phoneInput.parentElement;

        var hiddenInput = document.getElementById('billing_phone_prefix') ||
                          document.querySelector('input[name="billing_phone_prefix"]');

        // Ocultar el campo hidden del prefijo
        var hiddenField = document.getElementById('billing_phone_prefix_field');
        if (hiddenField) hiddenField.style.display = 'none';

        // ---- Crear el botón trigger ----
        var trigger = document.createElement('button');
        trigger.type = 'button';
        trigger.id = 'trocha-phone-widget';
        trigger.className = 'trocha-phone-trigger';
        trigger.setAttribute('aria-haspopup', 'listbox');
        trigger.setAttribute('aria-expanded', 'false');

        var flagImg = document.createElement('img');
        flagImg.src = flagSVG(current.colors, 21);
        flagImg.alt = current.name;
        flagImg.className = 'trocha-phone-flag';
        flagImg.width = 21;
        flagImg.height = 21;

        var prefixSpan = document.createElement('span');
        prefixSpan.className = 'trocha-phone-prefix-text';
        prefixSpan.textContent = current.prefix;

        var arrow = document.createElement('span');
        arrow.className = 'trocha-phone-arrow';
        arrow.textContent = '▾';

        trigger.appendChild(flagImg);
        trigger.appendChild(prefixSpan);
        trigger.appendChild(arrow);

        // ---- Crear el dropdown ----
        var dropdown = document.createElement('ul');
        dropdown.className = 'trocha-phone-dropdown';
        dropdown.setAttribute('role', 'listbox');

        COUNTRIES.forEach(function(c) {
            var li = document.createElement('li');
            li.setAttribute('role', 'option');
            li.dataset.prefix = c.prefix;
            li.dataset.code   = c.code;

            var img = document.createElement('img');
            img.src    = flagSVG(c.colors, 20);
            img.alt    = c.name;
            img.width  = 20;
            img.height = 20;
            img.className = 'trocha-phone-flag';

            var txt = document.createElement('span');
            txt.textContent = c.prefix + ' ' + c.name;

            li.appendChild(img);
            li.appendChild(txt);

            li.addEventListener('click', function() {
                current = c;
                flagImg.src = flagSVG(c.colors, 21);
                flagImg.alt = c.name;
                prefixSpan.textContent = c.prefix;
                if (hiddenInput) hiddenInput.value = c.prefix;
                dropdown.classList.remove('is-open');
                trigger.setAttribute('aria-expanded', 'false');
            });
            dropdown.appendChild(li);
        });

        // Toggle dropdown
        trigger.addEventListener('click', function(e) {
            e.stopPropagation();
            var open = dropdown.classList.toggle('is-open');
            trigger.setAttribute('aria-expanded', open ? 'true' : 'false');
        });

        // Cerrar al clicar fuera
        document.addEventListener('click', function() {
            dropdown.classList.remove('is-open');
            trigger.setAttribute('aria-expanded', 'false');
        });

        // ---- Insertar trigger DENTRO del woocommerce-input-wrapper, antes del input ----
        // Así ambos quedan en el mismo contenedor flex sin mover el <p> completo
        inputWrapper.insertBefore(trigger, phoneInput);
        inputWrapper.appendChild(dropdown); // dropdown cuelga del mismo contenedor

        // Inicializar hidden input
        if (hiddenInput) hiddenInput.value = current.prefix;

        // Autodetectar país por IP
        fetch('https://ipapi.co/json/')
            .then(function(r){return r.json();})
            .then(function(d){
                var code = d.country_code;
                var found = COUNTRIES.find(function(c){return c.code === code;});
                if (found) {
                    current = found;
                    flagImg.src = flagSVG(found.colors, 21);
                    flagImg.alt = found.name;
                    prefixSpan.textContent = found.prefix;
                    if (hiddenInput) hiddenInput.value = found.prefix;
                }
            })
            .catch(function(){});
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', buildWidget);
    } else {
        buildWidget();
    }
    if (typeof jQuery !== 'undefined') {
        jQuery(document.body).on('updated_checkout', buildWidget);
    }

    })();
    </script>
    <?php
});


// Style Select2 variation dropdowns via JS (CSS alone cant override Select2 inline styles)
add_action("wp_footer", function() {
    if (!is_product()) return;
    echo "<script>
(function(){
    function styleS2(){
        // Target the Select2 rendered span containers
        document.querySelectorAll(\".select2-container--default .select2-selection--single\").forEach(function(el){
            el.style.setProperty(\"background\",\"#111\",\"important\");
            el.style.setProperty(\"border\",\"1px solid #D4AF37\",\"important\");
            el.style.setProperty(\"border-radius\",\"0\",\"important\");
            el.style.setProperty(\"height\",\"44px\",\"important\");
            el.style.setProperty(\"display\",\"flex\",\"important\");
            el.style.setProperty(\"align-items\",\"center\",\"important\");
        });
        document.querySelectorAll(\".select2-selection__rendered\").forEach(function(el){
            el.style.setProperty(\"color\",\"#fff\",\"important\");
            el.style.setProperty(\"font-family\",\"Bebas Neue,sans-serif\",\"important\");
            el.style.setProperty(\"font-size\",\"0.95rem\",\"important\");
            el.style.setProperty(\"letter-spacing\",\"0.1em\",\"important\");
            el.style.setProperty(\"line-height\",\"44px\",\"important\");
            el.style.setProperty(\"padding-left\",\"12px\",\"important\");
        });
        document.querySelectorAll(\".select2-selection__arrow b\").forEach(function(el){
            el.style.setProperty(\"border-top-color\",\"#D4AF37\",\"important\");
        });
    }
    document.addEventListener(\"DOMContentLoaded\", function(){ setTimeout(styleS2,100); setTimeout(styleS2,600); setTimeout(styleS2,1500); });
    // Re-run after WC updates variation
    jQuery(document).on(\"woocommerce_variation_select_change found_variation reset_data\", function(){ setTimeout(styleS2,100); });
})();
</script>";
}, 20);
// Force 4 related products in 4 columns — no orphan
add_filter("woocommerce_output_related_products_args", function($args) {
    $args["posts_per_page"] = 4;
    $args["columns"] = 4;
    return $args;
});



add_filter('woocommerce_product_tabs', function($tabs) {
    unset($tabs['additional_information']);
    unset($tabs['reviews']);
    return $tabs;
}, 98);

/* ── Force no-cache headers for WooCommerce pages (Hostinger CDN compatible) ── */
add_action('send_headers', function() {
    if (is_cart() || is_checkout() || is_account_page() || is_wc_endpoint_url()) {
        header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0, private');
        header('Pragma: no-cache');
        header('X-Accel-Expires: 0');
    }
    // Also prevent CDN caching of product pages so session cookies can be set
    if (is_product()) {
        header('Cache-Control: private, no-cache, max-age=0, must-revalidate');
    }
});

/* ── Force no-cache for AJAX and all frontend pages ── */
add_action('send_headers', function() {
    // Already covered: is_cart, is_checkout, is_account_page, is_wc_endpoint_url, is_product
    // Add: AJAX, shop, front page, all other pages
    if (wp_doing_ajax() || is_shop() || is_front_page() || is_home() || is_product_category() || is_product_tag()) {
        if (!headers_sent()) {
            header('Cache-Control: private, no-cache, no-store, must-revalidate, max-age=0');
        }
    }
}, 5);

/* ── Eliminar dropdown ORDENAR de la tienda ── */
remove_action('woocommerce_before_shop_loop', 'woocommerce_catalog_ordering', 30);

/* ── Fix: inject wp-api-fetch nonce on checkout so shipping recalculates on country change ── */
add_action('wp_enqueue_scripts', function() {
    wp_enqueue_script('wp-api-fetch');
    wp_add_inline_script('wp-api-fetch', '
        wp.apiFetch.use(wp.apiFetch.createNonceMiddleware("' . wp_create_nonce('wp_rest') . '"));
        wp.apiFetch.use(wp.apiFetch.createRootURLMiddleware("' . esc_url_raw(rest_url()) . '"));
    ', 'after');
}, 1);

/* ── Eliminar botones de pago exprés del carrito (Stripe Google Pay/Apple Pay, PayPal) ── */
add_filter('wc_stripe_show_payment_request_on_cart', '__return_false');
add_filter('woocommerce_paypal_payments_cart_button_enabled', '__return_false');
