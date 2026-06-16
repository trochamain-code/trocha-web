<?php
defined('ABSPATH') || exit;

function trocha_setup() {
    load_theme_textdomain('trocha', get_template_directory() . '/languages');

    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('html5', [
        'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script',
    ]);
    add_theme_support('customize-selective-refresh-widgets');
    add_theme_support('woocommerce');
    add_theme_support('wc-product-gallery-zoom');
    add_theme_support('wc-product-gallery-lightbox');
    add_theme_support('wc-product-gallery-slider');

    register_nav_menus([
        'primary' => esc_html__('Primary', 'trocha'),
    ]);
}
add_action('after_setup_theme', 'trocha_setup');

function trocha_content_width() {
    $GLOBALS['content_width'] = 1920;
}
add_action('after_setup_theme', 'trocha_content_width', 0);
