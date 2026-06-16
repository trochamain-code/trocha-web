<?php
defined('ABSPATH') || exit;

function trocha_wc_setup() {
    add_theme_support('woocommerce', [
        'thumbnail_image_width' => 500,
        'single_image_width'    => 800,
        'product_grid'          => [
            'default_rows'    => 4,
            'min_rows'        => 2,
            'max_rows'        => 8,
            'default_columns' => 3,
            'min_columns'     => 1,
            'max_columns'     => 4,
        ],
    ]);
}
add_action('after_setup_theme', 'trocha_wc_setup');

add_filter('woocommerce_enqueue_styles', '__return_empty_array');

function trocha_wc_body_class($classes) {
    if (is_woocommerce() || is_cart() || is_checkout() || is_account_page()) {
        $classes[] = 'trocha-wc';
    }
    return $classes;
}
add_filter('body_class', 'trocha_wc_body_class');

remove_action('woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10);
remove_action('woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10);
remove_action('woocommerce_sidebar', 'woocommerce_get_sidebar', 10);

add_filter('woocommerce_show_page_title', '__return_false');

add_filter('woocommerce_add_to_cart_fragments', 'trocha_cart_fragment');
function trocha_cart_fragment($fragments) {
    ob_start();
    ?><span class="trocha-cart__count" id="trocha-cart-count"><?php echo WC()->cart->get_cart_contents_count(); ?></span><?php
    $fragments['.trocha-cart__count'] = ob_get_clean();
    return $fragments;
}


