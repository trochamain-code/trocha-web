<?php
defined('ABSPATH') || exit;

global $product;
if ($product && $product->get_price_html()) : ?>
    <div class="trocha-product-card__price"><?php echo $product->get_price_html(); ?></div>
<?php endif; ?>
