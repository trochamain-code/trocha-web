<?php
/**
 * TROCHA — Payment system bootstrap.
 *
 * Configures WooCommerce store defaults and enables payment gateways so the
 * checkout can actually be completed. Runs once (versioned) on init.
 */
defined('ABSPATH') || exit;

define('TROCHA_PAYMENTS_VERSION', '3');

/**
 * One-time store + gateway configuration.
 */
function trocha_configure_store() {
    if (!class_exists('WooCommerce')) {
        return;
    }
    if (get_option('trocha_payments_configured') === TROCHA_PAYMENTS_VERSION) {
        return;
    }

    // --- Disable "Coming Soon" / store-only mode ---------------------
    // This mode hides product/shop content behind a placeholder page.
    update_option('woocommerce_coming_soon', 'no');
    update_option('woocommerce_store_pages_only', 'no');

    // --- Store base / currency ---------------------------------------
    if (!get_option('woocommerce_default_country')) {
        update_option('woocommerce_default_country', 'ES');
    }
    update_option('woocommerce_currency', 'EUR');
    update_option('woocommerce_currency_pos', 'right_space');
    update_option('woocommerce_price_thousand_sep', '.');
    update_option('woocommerce_price_decimal_sep', ',');
    update_option('woocommerce_price_num_decimals', 2);

    // Allow selling without forcing an address-restricted region.
    update_option('woocommerce_allowed_countries', 'all');
    update_option('woocommerce_ship_to_countries', '');

    // Make checkout frictionless (no forced account).
    update_option('woocommerce_enable_guest_checkout', 'yes');
    update_option('woocommerce_enable_checkout_login_reminder', 'no');
    update_option('woocommerce_enable_signup_and_login_from_checkout', 'no');

    // Skip cart redirect oddities.
    update_option('woocommerce_cart_redirect_after_add', 'no');
    update_option('woocommerce_enable_ajax_add_to_cart', 'yes');

    // --- Cash on delivery (Contra reembolso) -------------------------
    $cod = get_option('woocommerce_cod_settings', []);
    $cod = is_array($cod) ? $cod : [];
    $cod = array_merge([
        'enabled'            => 'yes',
        'title'              => 'Contra reembolso',
        'description'        => 'Paga en efectivo a la entrega.',
        'instructions'       => 'Paga en efectivo cuando recibas tu pedido.',
        'enable_for_methods' => [],
        'enable_for_virtual' => 'yes',
    ], $cod);
    $cod['enabled'] = 'yes';
    update_option('woocommerce_cod_settings', $cod);

    // --- Bank transfer (BACS) ----------------------------------------
    $bacs = get_option('woocommerce_bacs_settings', []);
    $bacs = is_array($bacs) ? $bacs : [];
    $bacs = array_merge([
        'enabled'      => 'yes',
        'title'        => 'Transferencia bancaria',
        'description'  => 'Realiza el pago por transferencia. Tu pedido se procesará al recibirlo.',
        'instructions' => 'Usa el número de pedido como referencia de pago.',
    ], $bacs);
    $bacs['enabled'] = 'yes';
    update_option('woocommerce_bacs_settings', $bacs);

    // --- WooPayments (card / Apple Pay / Google Pay / Klarna / Amazon Pay) ---
    $wcpay = get_option('woocommerce_woocommerce_payments_settings', []);
    if (is_array($wcpay)) {
        $wcpay['enabled'] = 'yes';
        // Methods shown in the unified payment element.
        $wcpay['upe_enabled_payment_method_ids'] = ['card', 'klarna', 'amazon_pay'];
        // Express checkout buttons (Apple Pay / Google Pay via payment request).
        $wcpay['payment_request']                 = 'yes';
        $wcpay['payment_request_button_locations'] = ['product', 'cart', 'checkout'];
        $wcpay['express_checkout_checkout_methods'] = ['payment_request', 'woopay', 'amazon_pay'];
        $wcpay['express_checkout_cart_methods']     = ['payment_request', 'woopay', 'amazon_pay'];
        $wcpay['express_checkout_product_methods']  = ['payment_request', 'woopay', 'amazon_pay'];
        update_option('woocommerce_woocommerce_payments_settings', $wcpay);
    }

    // Make sure each WooPayments split gateway stays enabled.
    foreach (['', '_apple_pay', '_google_pay', '_klarna', '_amazon_pay'] as $suffix) {
        $opt = 'woocommerce_woocommerce_payments' . $suffix . '_settings';
        $s = get_option($opt, []);
        if (is_array($s)) {
            $s['enabled'] = 'yes';
            update_option($opt, $s);
        }
    }

    // Order in which gateways appear.
    update_option('woocommerce_gateway_order', [
        'woocommerce_payments' => 0,
        'cod'                  => 1,
        'bacs'                 => 2,
    ]);

    update_option('trocha_payments_configured', TROCHA_PAYMENTS_VERSION);
}
add_action('init', 'trocha_configure_store', 15);

/**
 * Safety net: force-enable COD + BACS in the live gateway list in case the
 * stored settings were cached before configuration ran.
 */
function trocha_force_enable_gateways($gateways) {
    if (isset($gateways['cod'])) {
        $gateways['cod']->enabled = 'yes';
    }
    if (isset($gateways['bacs'])) {
        $gateways['bacs']->enabled = 'yes';
    }
    return $gateways;
}
add_filter('woocommerce_available_payment_gateways', 'trocha_force_enable_gateways', 20);

/**
 * Whether WooPayments is genuinely usable (account connected + ready).
 * On a local HTTP domain this is false, so we must NOT offer card/Apple/Google
 * Pay — they would hijack "Place order" and fail. They auto-enable on the live
 * HTTPS site once the Stripe account validates.
 */
function trocha_woopayments_ready() {
    if (!class_exists('\WC_Payments') || !method_exists('\WC_Payments', 'get_account_service')) {
        return false;
    }
    try {
        $svc = \WC_Payments::get_account_service();
        if (!$svc || !$svc->is_stripe_connected()) {
            return false;
        }
    } catch (\Throwable $e) {
        return false;
    }
    // Card payments also require HTTPS to work in the browser.
    return is_ssl();
}

/**
 * Control WooPayments gateways at checkout:
 *  - If connected + HTTPS  -> make sure card/Apple Pay/Google Pay are offered.
 *  - Otherwise             -> remove them so they can't break "Place order".
 *    (COD + BACS remain as working payment methods.)
 */
function trocha_manage_woopayments($available) {
    if (!function_exists('WC')) {
        return $available;
    }

    $ready = trocha_woopayments_ready();

    if ($ready) {
        // Surface every enabled WooPayments gateway.
        $all = WC()->payment_gateways() ? WC()->payment_gateways()->payment_gateways() : [];
        foreach ($all as $id => $gateway) {
            if (strpos($id, 'woocommerce_payments') === 0
                && isset($gateway->enabled) && 'yes' === $gateway->enabled
                && !isset($available[$id])) {
                $available[$id] = $gateway;
            }
        }
    } else {
        // Not usable here — strip WooPayments so checkout stays functional.
        foreach (array_keys($available) as $id) {
            if (strpos($id, 'woocommerce_payments') === 0) {
                unset($available[$id]);
            }
        }
    }

    return $available;
}
add_filter('woocommerce_available_payment_gateways', 'trocha_manage_woopayments', 30);

/**
 * Country-code safety net.
 *
 * On the classic checkout the select2/selectWoo country field can post the
 * country NAME ("España") instead of its CODE ("ES"), which triggers
 * "'España' no es un código de país válido". This converts any posted country
 * name back to its ISO code before validation, on both checkout and the
 * cart/shipping address forms.
 */
function trocha_country_name_to_code($value) {
    if (!is_string($value) || $value === '' || !function_exists('WC') || !WC()->countries) {
        return $value;
    }
    $countries = WC()->countries->get_countries();
    // Already a valid 2-letter code? leave it.
    if (isset($countries[$value])) {
        return $value;
    }
    // Try to match by name (case-insensitive, accent-aware).
    foreach ($countries as $code => $name) {
        if (mb_strtolower($name) === mb_strtolower($value)) {
            return $code;
        }
    }
    return $value;
}

function trocha_fix_posted_country_fields() {
    foreach (['billing_country', 'shipping_country', 'calc_shipping_country'] as $field) {
        if (isset($_POST[$field])) {
            $_POST[$field] = trocha_country_name_to_code(wc_clean(wp_unslash($_POST[$field])));
        }
    }
    // WooCommerce blocks / AJAX sometimes nest under 'post_data' or 'country'.
    if (isset($_POST['country'])) {
        $_POST['country'] = trocha_country_name_to_code(wc_clean(wp_unslash($_POST['country'])));
    }
}
// Run before WooCommerce reads the posted checkout data.
add_action('woocommerce_checkout_process', 'trocha_fix_posted_country_fields', 1);
add_action('woocommerce_before_checkout_process', 'trocha_fix_posted_country_fields', 1);

// Also normalise via the field-value filter as a second layer of defense.
add_filter('woocommerce_checkout_posted_data', function ($data) {
    foreach (['billing_country', 'shipping_country'] as $f) {
        if (!empty($data[$f])) {
            $data[$f] = trocha_country_name_to_code($data[$f]);
        }
    }
    return $data;
});

/**
 * Fix the default country format: 'ES:SE' (country:state) is fine, but ensure
 * the base country option is a clean code so nothing downstream mis-parses it.
 */
add_filter('option_woocommerce_default_country', function ($value) {
    if (!is_string($value)) return $value;
    // Convertir ES:* a ES
    if (preg_match('/^ES(:.*)?$/', $value)) return 'ES';
    // Convertir nombre a código
    foreach (['España','Espana','Spain'] as $n) {
        if (stripos($value, $n) !== false) return 'ES';
    }
    return $value;
});

/**
 * Safety net: force "Coming Soon" / store-only mode off at runtime so product
 * and shop pages are always visible.
 */
add_filter('option_woocommerce_coming_soon', function () { return 'no'; });
add_filter('option_woocommerce_store_pages_only', function () { return 'no'; });
add_filter('woocommerce_coming_soon_exclude', '__return_true');


/**
 * TROCHA — SMTP via PHPMailer (Gmail + App Password)
 */
add_action('phpmailer_init', function($phpmailer) {
    $phpmailer->isSMTP();
    $phpmailer->Host       = 'smtp.gmail.com';
    $phpmailer->SMTPAuth   = true;
    $phpmailer->Port       = 587;
    $phpmailer->SMTPSecure = 'tls';
    $phpmailer->Username   = 'trochamain@gmail.com';
    $phpmailer->Password   = 'mqusyyaswhtgpsic';
    $phpmailer->From       = 'trochamain@gmail.com';
    $phpmailer->FromName   = 'TROCHA';
});
