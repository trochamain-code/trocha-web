<?php
defined('ABSPATH') || exit;

define('TROCHA_SEED_VERSION', '3');

/**
 * Sizes used across the store. Custom (local) attribute — plain strings,
 * no taxonomy required. This is the most robust approach for variations.
 */
function trocha_sizes() {
    return ['S', 'M', 'L', 'XL', 'XXL'];
}

function trocha_seed_products() {
    if (get_option('trocha_products_seeded') === TROCHA_SEED_VERSION) {
        return;
    }

    if (!class_exists('WooCommerce') || !function_exists('wc_get_product')) {
        return;
    }

    trocha_create_category_products('frio', 'FRÍO', [
        'Borceguí FRIO',
        'Bota FRIO',
        'Botín FRIO',
    ]);

    trocha_create_category_products('calor', 'CALOR', [
        'Sandalia CALOR',
        'Alpargata CALOR',
        'Playera CALOR',
    ]);

    trocha_create_category_products('estilo', 'ESTILO', [
        'Zapatilla ESTILO',
        'Casual ESTILO',
        'Sneaker ESTILO',
    ]);

    update_option('trocha_products_seeded', TROCHA_SEED_VERSION);
}
add_action('init', 'trocha_seed_products', 20);

function trocha_create_category_products($category_slug, $category_name, $product_names) {
    $sizes = trocha_sizes();

    $cat_term = term_exists($category_name, 'product_cat');
    if (!$cat_term) {
        $cat_term = wp_insert_term($category_name, 'product_cat', ['slug' => $category_slug]);
    }
    $cat_id = is_array($cat_term) ? $cat_term['term_id'] : $cat_term;

    foreach ($product_names as $name) {
        $slug = sanitize_title($name) . '-' . $category_slug;

        // Remove any previous (possibly broken) version so we rebuild cleanly.
        $existing = get_posts([
            'name'        => $slug,
            'post_type'   => 'product',
            'post_status' => 'any',
            'fields'      => 'ids',
            'numberposts' => -1,
        ]);
        foreach ($existing as $old_id) {
            $old = wc_get_product($old_id);
            if ($old) {
                foreach ($old->get_children() as $child_id) {
                    wp_delete_post($child_id, true);
                }
            }
            wp_delete_post($old_id, true);
        }

        // --- Parent variable product -------------------------------------
        $product = new WC_Product_Variable();
        $product->set_name($name);
        $product->set_slug($slug);
        $product->set_status('publish');
        $product->set_catalog_visibility('visible');
        $product->set_description("Calzado $category_name — TROCHA. Precio único: 10€.");
        $product->set_short_description("$category_name — TROCHA");
        $product->set_category_ids([$cat_id]);
        $product->set_regular_price(10);
        $product->set_manage_stock(false);
        $product->set_stock_status('instock');

        // Custom (local) attribute: name "Talla", string options, used for variations.
        $attr = new WC_Product_Attribute();
        $attr->set_id(0);                 // 0 = custom attribute (no taxonomy)
        $attr->set_name('Talla');
        $attr->set_options($sizes);       // plain string values
        $attr->set_position(0);
        $attr->set_visible(true);
        $attr->set_variation(true);

        $product->set_attributes([$attr]);
        $product_id = $product->save();

        // --- Variations ---------------------------------------------------
        // For custom attributes the key is the sanitized attribute name.
        foreach ($sizes as $size) {
            $variation = new WC_Product_Variation();
            $variation->set_parent_id($product_id);
            $variation->set_attributes(['talla' => $size]); // key = sanitize_title('Talla')
            $variation->set_regular_price(10);
            $variation->set_manage_stock(false);
            $variation->set_stock_status('instock');
            $variation->set_status('publish');
            $variation->save();
        }

        WC_Product_Variable::sync($product_id);
    }
}

/* ============================================================
   PRODUCT IMAGES — placeholder featured images
   Downloads a themed placeholder per product (free service) and
   attaches it as the featured image. Idempotent: only products
   without a thumbnail are touched. Replace later from WP admin.
   ============================================================ */
define('TROCHA_IMAGES_VERSION', '1');

function trocha_seed_product_images() {
    if (get_option('trocha_product_images_seeded') === TROCHA_IMAGES_VERSION) {
        return;
    }
    if (!class_exists('WooCommerce') || !function_exists('media_sideload_image')) {
        // media functions require admin includes; load them.
        if (defined('ABSPATH')) {
            require_once ABSPATH . 'wp-admin/includes/file.php';
            require_once ABSPATH . 'wp-admin/includes/media.php';
            require_once ABSPATH . 'wp-admin/includes/image.php';
        }
        if (!function_exists('media_sideload_image')) {
            return;
        }
    }

    $products = get_posts([
        'post_type'   => 'product',
        'post_status' => 'publish',
        'numberposts' => -1,
        'fields'      => 'ids',
    ]);

    $all_done = true;

    foreach ($products as $pid) {
        if (has_post_thumbnail($pid)) {
            continue;
        }

        $name  = get_the_title($pid);
        $label = rawurlencode(strtoupper($name));
        // Free placeholder service (placehold.co) — dark bg, dirty-yellow text.
        $url = "https://placehold.co/800x800/0B0B0D/C4A000.png?text={$label}";

        require_once ABSPATH . 'wp-admin/includes/file.php';
        require_once ABSPATH . 'wp-admin/includes/media.php';
        require_once ABSPATH . 'wp-admin/includes/image.php';

        $attachment_id = media_sideload_image($url, $pid, $name, 'id');

        if (is_wp_error($attachment_id)) {
            $all_done = false;
            continue;
        }
        set_post_thumbnail($pid, $attachment_id);
    }

    if ($all_done) {
        update_option('trocha_product_images_seeded', TROCHA_IMAGES_VERSION);
    }
}
add_action('init', 'trocha_seed_product_images', 25);
