<?php
defined('ABSPATH') || exit;

global $product;

if ($product && $product->is_type('simple')) :
    echo apply_filters('woocommerce_loop_add_to_cart_link',
        sprintf('<a href="%s" data-quantity="1" data-product_id="%s" class="trocha-btn trocha-btn--small add_to_cart_button ajax_add_to_cart">%s</a>',
            esc_url($product->add_to_cart_url()),
            esc_attr($product->get_id()),
            esc_html($product->add_to_cart_text())
        ),
        $product
    );
else : ?>
    <a href="<?php echo esc_url(get_permalink($product->get_id())); ?>" class="trocha-btn trocha-btn--small">VER</a>
<?php endif; ?>
