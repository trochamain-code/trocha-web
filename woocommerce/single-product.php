<?php get_header(); ?>

<div class="trocha-section">
    <div class="trocha-container">
        <?php while (have_posts()) : the_post(); ?>
            <?php global $product; ?>

            <div class="trocha-single-product">

                <div class="trocha-single-product__gallery">
                    <?php do_action('woocommerce_before_single_product_summary'); ?>
                </div>

                <div class="trocha-single-product__summary">
                    <div class="trocha-single-product__meta">
                        <span class="trocha-single-product__collection">
                            <?php
                            $terms = wp_get_post_terms($product->get_id(), 'product_cat');
                            if (!empty($terms)) {
                                echo esc_html($terms[0]->name);
                            }
                            ?>
                        </span>
                        <span class="trocha-single-product__ref">#<?php echo esc_html($product->get_sku() ?: $product->get_id()); ?></span>
                    </div>

                    <h1 class="trocha-single-product__title"><?php the_title(); ?></h1>

                    <div class="trocha-single-product__price">
                        <?php echo $product->get_price_html(); ?>
                    </div>

                    <div class="trocha-single-product__description trocha-content">
                        <?php the_excerpt(); ?>
                    </div>

                    <div class="trocha-single-product__form">
                        <?php
                        /**
                         * Native WooCommerce add-to-cart (handles simple +
                         * variable products, AJAX, variation validation, etc.).
                         */
                        woocommerce_template_single_add_to_cart();
                        ?>
                    </div>
                </div>

            </div>

            <div class="trocha-single-product__tabs">
                <div class="trocha-related-divider"></div>
                <?php do_action('woocommerce_after_single_product_summary'); ?>
            </div>

        <?php endwhile; ?>
    </div>
</div>

<?php get_footer(); ?>
