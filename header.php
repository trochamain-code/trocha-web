<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php wp_head(); ?>
    <?php if (is_front_page() || is_home()): ?>
    <script>
    // Fix meta description if AIOSEO didn't set it properly
    (function(){
        var metas = document.getElementsByTagName('meta');
        var desc = 'Descubre TROCHA, la marca de ropa urbana espa\u00f1ola con estilo propio. Camisetas, chaquetas, zapatillas y streetwear aut\u00e9ntico. Env\u00edos r\u00e1pidos a toda Espa\u00f1a. Compra ahora.';
        for(var i=0;i<metas.length;i++){
            if(metas[i].name==='description' || metas[i].getAttribute('property')==='og:description'){
                metas[i].content = desc;
            }
        }
    })();
    </script>
    <?php endif; ?>
</head>
<body <?php body_class(); ?>>
<?php if (function_exists('wp_body_open')) { wp_body_open(); } ?>
