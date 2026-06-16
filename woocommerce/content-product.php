<div <?php wc_product_class('trocha-product-card'); ?>>
    <?php
    global $product;
    $stamps = ['NUEVO', 'LIMITADO', 'BREGA', 'DROP'];
    $stamp = $stamps[array_rand($stamps)];

    $is_pies_cat = false;
    if (is_product_category()) {
        $term = get_queried_object();
        $is_pies_cat = $term && in_array($term->slug, ['frio', 'calor', 'estilo']);
    }
    ?>
    <div class="trocha-product-card__stamp"><?php echo esc_html($stamp); ?></div>

    <div class="trocha-product-card__image">
        <a href="<?php the_permalink(); ?>">
            <?php echo $product->get_image('medium'); ?>
        </a>
    </div>

    <div class="trocha-product-card__body">
        <h3 class="trocha-product-card__title">
            <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
        </h3>

        <div class="trocha-product-card__price">
            <?php echo $product->get_price_html(); ?>
        </div>

        <?php if ($product->is_type('simple')) : ?>
            <a href="<?php echo esc_url($product->add_to_cart_url()); ?>" class="trocha-btn trocha-btn--small trocha-btn--primary add_to_cart_button ajax_add_to_cart" data-product_id="<?php echo esc_attr($product->get_id()); ?>" data-quantity="1">
                COMPRAR
            </a>
        <?php else : ?>
            <a href="<?php echo esc_url(get_permalink($product->get_id())); ?>" class="trocha-btn trocha-btn--small trocha-btn--primary">
                ELEGIR TALLA
            </a>
        <?php endif; ?>
    </div>
</div>
