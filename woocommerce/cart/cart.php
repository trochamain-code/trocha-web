<?php get_header(); ?>

<div class="trocha-section trocha-section--cart">
    <div class="trocha-container">

        <h1 class="trocha-page-title glitch" data-text="CARRITO">CARRITO</h1>

        <?php do_action('woocommerce_before_cart'); ?>

        <form class="trocha-cart-form" action="<?php echo esc_url(wc_get_cart_url()); ?>" method="post">

            <div class="trocha-cart-items">
                <?php do_action('woocommerce_before_cart_table'); ?>

                <div class="trocha-cart-table">
                    <?php foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item) :
                        $_product = apply_filters('woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key);
                        if ($_product && $_product->exists() && $cart_item['quantity'] > 0) : ?>
                        <div class="trocha-cart-row">
                            <div class="trocha-cart-row__image">
                                <?php echo $_product->get_image('thumbnail'); ?>
                            </div>
                            <div class="trocha-cart-row__name">
                                <?php echo esc_html($_product->get_name()); ?>
                            </div>
                            <div class="trocha-cart-row__quantity">
                                <?php
                                woocommerce_quantity_input([
                                    'input_name'  => "cart[{$cart_item_key}][qty]",
                                    'input_value' => $cart_item['quantity'],
                                    'max_value'   => $_product->get_max_purchase_quantity(),
                                    'min_value'   => '0',
                                ]);
                                ?>
                            </div>
                            <div class="trocha-cart-row__price">
                                <?php echo WC()->cart->get_product_price($_product); ?>
                            </div>
                            <div class="trocha-cart-row__remove">
                                <?php echo apply_filters('woocommerce_cart_item_remove_link', sprintf(
                                    '<a href="%s" class="trocha-remove">✕</a>',
                                    esc_url(wc_get_cart_remove_url($cart_item_key))
                                ), $cart_item_key); ?>
                            </div>
                        </div>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>

                <?php do_action('woocommerce_cart_contents'); ?>
                <?php do_action('woocommerce_after_cart_table'); ?>
            </div>

            <div class="trocha-cart-totals">
                <h2 class="trocha-cart-totals__title">[ TOTAL ]</h2>
                <?php do_action('woocommerce_cart_collaterals'); ?>
                <button type="submit" class="trocha-btn trocha-btn--primary trocha-btn--full" name="update_cart" value="Actualizar">
                    ACTUALIZAR
                </button>
                <?php do_action('woocommerce_proceed_to_checkout'); ?>
            </div>

        </form>

        <?php do_action('woocommerce_after_cart'); ?>

    </div>
</div>

<?php get_footer(); ?>
