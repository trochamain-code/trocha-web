<?php
defined('ABSPATH') || exit;

function trocha_enqueue() {
    $dir = get_template_directory();
    $uri = get_template_directory_uri();

    // Google Fonts — Special Elite + Bebas Neue
    wp_enqueue_style('trocha-fonts', 'https://fonts.googleapis.com/css2?family=Special+Elite&family=Bebas+Neue&display=swap', [], null);

    // Use file modification time so CSS/JS edits always bust the cache.
    $cani_ver = file_exists($dir . '/assets/css/cani.css') ? filemtime($dir . '/assets/css/cani.css') : '1.0.0';
    wp_enqueue_style('trocha-cani', $uri . '/assets/css/cani.css', [], $cani_ver);

    if (class_exists('WooCommerce')) {
        $wc_ver = file_exists($dir . '/assets/css/woocommerce.css') ? filemtime($dir . '/assets/css/woocommerce.css') : '1.0.0';
        wp_enqueue_style('trocha-woocommerce', $uri . '/assets/css/woocommerce.css', ['trocha-cani'], $wc_ver);
    }

    $js_ver = file_exists($dir . '/assets/js/main.js') ? filemtime($dir . '/assets/js/main.js') : '1.0.0';
    wp_enqueue_script('trocha-main', $uri . '/assets/js/main.js', [], $js_ver, true);

    if (is_singular() && comments_open() && get_option('thread_comments')) {
        wp_enqueue_script('comment-reply');
    }
}
add_action('wp_enqueue_scripts', 'trocha_enqueue');
