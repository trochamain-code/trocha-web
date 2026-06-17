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

        <!-- BARRA DE FILTROS -->
        <?php
        $filtros = [
            'todos'      => ['label' => 'TODOS',      'slug' => ''],
            'camisetas'  => ['label' => 'CAMISETAS',  'slug' => 'camisetas'],
            'sudaderas'  => ['label' => 'SUDADERAS',  'slug' => 'sudaderas'],
            'chaquetas'  => ['label' => 'CHAQUETAS',  'slug' => 'chaquetas'],
            'pantalones' => ['label' => 'PANTALONES', 'slug' => 'pantalones'],
            'shorts'     => ['label' => 'SHORTS',     'slug' => 'shorts'],
            'accesorios' => ['label' => 'ACCESORIOS', 'slug' => 'accesorios'],
        ];
        $current_slug = ($term && isset($term->slug)) ? $term->slug : '';
        ?>
        <div class="trocha-filter-bar">
            <?php foreach ($filtros as $key => $filtro) :
                $url = $filtro['slug'] ? get_term_link($filtro['slug'], 'product_cat') : get_permalink(wc_get_page_id('shop'));
                $is_active = ($filtro['slug'] === $current_slug) || ($filtro['slug'] === '' && !$cat_slug && !in_array($current_slug, array_column($filtros, 'slug')));
                if (!$filtro['slug'] && $cat_slug) $is_active = false;
                if (!$filtro['slug'] && !is_shop() && !is_product_category()) $is_active = true;
                if (is_shop() && $filtro['slug'] === '') $is_active = true;
            ?>
                <a href="<?php echo esc_url(is_wp_error($url) ? get_permalink(wc_get_page_id('shop')) : $url); ?>"
                   class="trocha-filter-btn <?php echo $is_active ? 'active' : ''; ?>">
                    <?php echo esc_html($filtro['label']); ?>
                </a>
            <?php endforeach; ?>
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
