<?php
/* Template: Colecciones FRÍO / CALOR / ESTILO */
get_header();

$term = get_queried_object();
$slug = $term->slug;
$is_coleccion = in_array($slug, ['frio', 'calor', 'estilo']);

// Default sort: newest first
$orderby = isset($_GET['orderby']) ? sanitize_text_field($_GET['orderby']) : 'date';
$allowed = ['date', 'price', 'price-desc'];
if (!in_array($orderby, $allowed)) $orderby = 'date';

$orderby_map = [
    'date'       => ['orderby' => 'date',        'order' => 'DESC'],
    'price'      => ['orderby' => 'meta_value_num', 'order' => 'ASC',  'meta_key' => '_price'],
    'price-desc' => ['orderby' => 'meta_value_num', 'order' => 'DESC', 'meta_key' => '_price'],
];

$args = array_merge([
    'post_type'      => 'product',
    'posts_per_page' => 24,
    'tax_query'       => [[
        'taxonomy' => 'product_cat',
        'field'    => 'slug',
        'terms'    => $slug,
    ]],
], $orderby_map[$orderby]);

$query = new WP_Query($args);

// Style data per category
$styles = [
    'frio'   => ['color' => '#1a3a5c', 'tag' => 'HEARTLESS', 'title' => 'FRÍO',   'desc' => 'Para cuando el suelo está mojado y un error puede ser una caída. Botas, borceguíes, pisada firme.'],
    'calor'  => ['color' => '#c25a1e', 'tag' => 'REAL PLAYAS', 'title' => 'CALOR',  'desc' => 'Para el asfalto que quema, el día largo, la calle sin tregua. Ligeros, frescos, con carácter.'],
    'estilo' => ['color' => '#6b2fa0', 'tag' => 'DESTACA',    'title' => 'ESTILO', 'desc' => 'No es temporada. Es actitud. Lo que no necesita excusa ni clima. Puro sello personal.'],
];
$style = isset($styles[$slug]) ? $styles[$slug] : $styles['calor'];

$order_labels = [
    'date'       => 'Novedades',
    'price'      => 'Precio: menor a mayor',
    'price-desc' => 'Precio: mayor a menor',
];
?>

<div class="trocha-coleccion-page" style="--colec-color: <?php echo $style['color']; ?>">

    <div class="trocha-coleccion-hero">
        <div class="trocha-coleccion-hero__stamp"><?php echo $style['tag']; ?></div>
        <h1 class="trocha-coleccion-hero__title"><?php echo $style['title']; ?></h1>
        <p class="trocha-coleccion-hero__desc"><?php echo $style['desc']; ?></p>
    </div>

    <div class="trocha-coleccion-toolbar">
        <div class="trocha-coleccion-toolbar__count">
            <?php echo $query->found_posts; ?> producto<?php echo $query->found_posts !== 1 ? 's' : ''; ?>
        </div>

        <div class="trocha-sort-wrapper">
            <label class="trocha-sort-label" for="trocha-sort-select">ORDENAR</label>
            <div class="trocha-sort-select-wrap">
                <select id="trocha-sort-select" class="trocha-sort-select">
                    <?php foreach ($order_labels as $val => $label): ?>
                        <option value="<?php echo $val; ?>" <?php selected($orderby, $val); ?>>
                            <?php echo $label; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <span class="trocha-sort-arrow">▾</span>
            </div>
        </div>
    </div>

    <div class="trocha-coleccion-grid">
        <?php if ($query->have_posts()): ?>
            <?php while ($query->have_posts()): $query->the_post();
                global $product;
                if (!$product) continue;
            ?>
                <div class="trocha-coleccion-card">
                    <a href="<?php the_permalink(); ?>" class="trocha-coleccion-card__link">
                        <div class="trocha-coleccion-card__image-wrap">
                            <?php if (has_post_thumbnail()): ?>
                                <?php the_post_thumbnail('woocommerce_thumbnail', ['class' => 'trocha-coleccion-card__img']); ?>
                            <?php else: ?>
                                <div class="trocha-coleccion-card__img trocha-coleccion-card__img--placeholder"></div>
                            <?php endif; ?>
                            <?php if ($product->is_on_sale()): ?>
                                <span class="trocha-coleccion-card__badge">OFERTA</span>
                            <?php endif; ?>
                        </div>
                        <div class="trocha-coleccion-card__body">
                            <h3 class="trocha-coleccion-card__title"><?php the_title(); ?></h3>
                            <span class="trocha-coleccion-card__price"><?php echo $product->get_price_html(); ?></span>
                        </div>
                    </a>
                </div>
            <?php endwhile; wp_reset_postdata(); ?>
        <?php else: ?>
            <p class="trocha-coleccion-empty">No hay productos en esta colección todavía.</p>
        <?php endif; ?>
    </div>

</div>

<script>
(function(){
    var select = document.getElementById('trocha-sort-select');
    if (!select) return;
    select.addEventListener('change', function(){
        var val = this.value;
        var url = new URL(window.location.href);
        url.searchParams.set('orderby', val);
        window.location.href = url.toString();
    });
})();
</script>

<?php get_footer(); ?>
