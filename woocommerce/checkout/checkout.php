<?php get_header(); ?>

<div class="trocha-section trocha-section--checkout">
    <div class="trocha-container trocha-container--narrow">

        <h1 class="trocha-page-title glitch" data-text="CHECKOUT">CHECKOUT</h1>

        <?php do_action('woocommerce_before_checkout_form', WC()->checkout()); ?>

        <form name="checkout" method="post" class="trocha-checkout-form" action="<?php echo esc_url(wc_get_checkout_url()); ?>">
            <?php if (WC()->checkout()->get_checkout_fields()) : ?>
                <?php do_action('woocommerce_checkout_before_customer_details'); ?>

                <div class="trocha-checkout-fields">
                    <?php do_action('woocommerce_checkout_billing'); ?>
                    <?php do_action('woocommerce_checkout_shipping'); ?>
                </div>

                <?php do_action('woocommerce_checkout_after_customer_details'); ?>
            <?php endif; ?>

            <?php do_action('woocommerce_checkout_before_order_review_heading'); ?>
            <?php do_action('woocommerce_checkout_before_order_review'); ?>

            <div class="trocha-order-review">
                <h2 class="trocha-order-review__title">[ PEDIDO ]</h2>
                <?php do_action('woocommerce_checkout_order_review'); ?>
            </div>

            <?php do_action('woocommerce_checkout_after_order_review'); ?>
        </form>

        <?php do_action('woocommerce_after_checkout_form', WC()->checkout()); ?>

    </div>
</div>

<?php get_footer(); ?>
