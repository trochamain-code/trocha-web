<?php
defined('ABSPATH') || exit;

$total   = isset($total) ? $total : wc_get_loop_prop('total_pages');
$current = isset($current) ? $current : wc_get_loop_prop('current_page');

if ($total <= 1) {
    return;
}
?>
<nav class="woocommerce-pagination">
    <?php
    echo paginate_links(apply_filters('woocommerce_pagination_args', [
        'base'      => esc_url_raw(str_replace(999999999, '%#%', remove_query_arg('add-to-cart', get_pagenum_link(999999999, false)))),
        'format'    => '',
        'add_args'  => false,
        'current'   => max(1, $current),
        'total'     => $total,
        'prev_text' => '←',
        'next_text' => '→',
        'type'      => 'list',
        'end_size'  => 3,
        'mid_size'  => 3,
    ]));
    ?>
</nav>
