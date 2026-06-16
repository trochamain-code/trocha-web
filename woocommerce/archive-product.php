<?php get_header(); ?>

<div class="trocha-section">
    <div class="trocha-container">

        <?php
        $category_headers = [
            'frio'   => ['title' => 'FRÍO',   'sub' => 'HEARTLESS — Botas, borceguíes, pisada firme.'],
            'calor'  => ['title' => 'CALOR',  'sub' => 'REAL PLAYAS — Ligeros, frescos, con carácter.'],
            'estilo' => ['title' => 'ESTILO', 'sub' => 'DESTACA — Puro sello personal.'],
        ];
        $term = get_queried_object();
        $cat_slug = ($term && isset($term->slug) && isset($category_headers[$term->slug])) ? $term->slug : null;
        ?>

        <div class="trocha-shop-header" style="text-align:center;margin-bottom:1.5rem;">
            <?php if ($cat_slug) : ?>
                <h1 class="trocha-page-title"><?php echo esc_html($category_headers[$cat_slug]['title']); ?></h1>
                <p style="color:var(--text-sub);font-size:0.85rem;"><?php echo esc_html($category_headers[$cat_slug]['sub']); ?></p>
            <?php else : ?>
                <h1 class="trocha-page-title">TIENDA</h1>
                <p style="color:var(--text-sub);font-size:0.85rem;">Cada prenda tiene un propósito.</p>
            <?php endif; ?>
        </div>

        <?php if (woocommerce_product_loop()) : ?>

            <?php do_action('woocommerce_before_shop_loop'); ?>

            <div class="trocha-products-grid">
                <?php while (have_posts()) : the_post(); ?>
                    <?php wc_get_template_part('content', 'product'); ?>
                <?php endwhile; ?>
            </div>

            <?php do_action('woocommerce_after_shop_loop'); ?>

        <?php else : ?>
            <p style="text-align:center;color:var(--text-dim);padding:2rem;font-size:0.85rem;">
                No hay productos disponibles. Próximo drop pronto.
            </p>
        <?php endif; ?>

    </div>
</div>

<?php get_footer(); ?>
