<?php
/**
 * Hero slider — panel de admin para los textos de identidad de marca.
 * Aparece en: Apariencia → Hero TROCHA.
 */
defined('ABSPATH') || exit;

function trocha_hero_default_phrases() {
    return [
        "NO ES ROPA.\nES CAMINO.",
        "HECHO DESDE\nEL ASFALTO.",
        "RAÍCES DURAS,\nSUEÑOS GRANDES.",
        "ASUME RIESGOS.\nCONFÍA EN TI.",
    ];
}

/** Devuelve las 4 frases (guardadas o, si están vacías, las de por defecto). */
function trocha_get_hero_phrases() {
    $saved = get_option('trocha_hero_phrases', []);
    $def   = trocha_hero_default_phrases();
    $out   = [];
    for ($i = 0; $i < 4; $i++) {
        $out[$i] = (is_array($saved) && isset($saved[$i]) && trim($saved[$i]) !== '')
            ? $saved[$i]
            : $def[$i];
    }
    return $out;
}

add_action('admin_menu', function () {
    add_theme_page('Hero TROCHA', 'Hero TROCHA', 'manage_options', 'trocha-hero', 'trocha_hero_settings_page');
});

add_action('admin_init', function () {
    register_setting('trocha_hero_group', 'trocha_hero_phrases', [
        'type'              => 'array',
        'sanitize_callback' => 'trocha_hero_sanitize_phrases',
        'default'           => [],
    ]);
});

function trocha_hero_sanitize_phrases($input) {
    $out = [];
    if (is_array($input)) {
        foreach ($input as $i => $v) {
            $out[(int) $i] = sanitize_textarea_field($v);
        }
    }
    return $out;
}

function trocha_hero_settings_page() {
    $phrases = get_option('trocha_hero_phrases', []);
    $def     = trocha_hero_default_phrases();
    ?>
    <div class="wrap">
        <h1>Hero TROCHA — textos del slider</h1>
        <p>Cada frase aparece a la izquierda de su slide. El slider usa los <strong>4 últimos productos</strong> de la tienda; estas frases son el texto de identidad de marca que va sobre cada foto. Usa saltos de línea para dividir en varias líneas. Deja un campo vacío para usar el texto por defecto.</p>
        <form method="post" action="options.php">
            <?php settings_fields('trocha_hero_group'); ?>
            <table class="form-table" role="presentation">
                <?php for ($i = 0; $i < 4; $i++) :
                    $val = (is_array($phrases) && isset($phrases[$i])) ? $phrases[$i] : $def[$i];
                ?>
                <tr>
                    <th scope="row"><label for="trocha_hero_<?php echo (int) $i; ?>">Slide <?php echo (int) ($i + 1); ?></label></th>
                    <td>
                        <textarea id="trocha_hero_<?php echo (int) $i; ?>" name="trocha_hero_phrases[<?php echo (int) $i; ?>]" rows="3" cols="45" style="font-family:monospace"><?php echo esc_textarea($val); ?></textarea>
                        <p class="description">Por defecto: <?php echo esc_html(str_replace("\n", ' / ', $def[$i])); ?></p>
                    </td>
                </tr>
                <?php endfor; ?>
            </table>
            <?php submit_button('Guardar textos'); ?>
        </form>
    </div>
    <?php
}
