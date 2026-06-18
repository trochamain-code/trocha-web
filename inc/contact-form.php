<?php
/**
 * TROCHA — Contact form (WPForms) + Contact page bootstrap.
 *
 * Creates a WPForms contact form and a "Contacto" page embedding it.
 * Runs once (versioned) on init. Requires WPForms (Lite) to be active.
 */
defined('ABSPATH') || exit;

define('TROCHA_CONTACT_VERSION', '3');

/**
 * Build the WPForms form definition (fields + settings) as a PHP array.
 */
function trocha_contact_form_data($form_id = 0) {
    return [
        'id'          => $form_id,
        'field_id'    => 5,
        'fields'      => [
            1 => [
                'id'          => '1',
                'type'        => 'name',
                'label'       => 'Nombre',
                'format'      => 'first-last',
                'required'    => '1',
                'size'        => 'large',
                'simple_placeholder'     => '',
                'first_placeholder'      => 'Nombre',
                'last_placeholder'       => 'Apellidos',
            ],
            2 => [
                'id'          => '2',
                'type'        => 'email',
                'label'       => 'Correo electrónico',
                'required'    => '1',
                'size'        => 'large',
                'placeholder' => 'tucorreo@ejemplo.com',
            ],
            3 => [
                'id'          => '3',
                'type'        => 'text',
                'label'       => 'Asunto',
                'required'    => '1',
                'size'        => 'large',
                'placeholder' => '¿Sobre qué nos escribes?',
            ],
            4 => [
                'id'          => '4',
                'type'        => 'textarea',
                'label'       => 'Mensaje',
                'required'    => '1',
                'size'        => 'large',
                'placeholder' => 'Cuéntanos...',
            ],
        ],
        'settings'    => [
            'form_title'                 => 'Contacto TROCHA',
            'form_desc'                  => '',
            'submit_text'                => 'ENVIAR MENSAJE',
            'submit_text_processing'     => 'Enviando...',
            'antispam_v3'                => '1',
            'ajax_submit'                => '1',
            'notification_enable'        => '1',
            'notifications'              => [
                1 => [
                    'notification_name' => 'Notificación predeterminada',
                    'email'             => 'trochamain@gmail.com',
                    'subject'           => 'Nuevo mensaje de contacto — TROCHA',
                    'sender_name'       => 'TROCHA',
                    'sender_address'    => '{admin_email}',
                    'replyto'           => '{field_id="2"}',
                    'message'           => '{all_fields}',
                ],
            ],
            'confirmations'              => [
                1 => [
                    'type'             => 'message',
                    'message'          => '<p>Gracias por tu mensaje. Te responderemos pronto.</p>',
                    'message_scroll'   => '1',
                    'redirect'         => '',
                    'page'             => '',
                ],
            ],
        ],
        'meta'        => [
            'template' => 'blank',
        ],
    ];
}

/**
 * Create the WPForms contact form + Contacto page once.
 */
function trocha_create_contact_form() {
    if (!post_type_exists('wpforms')) {
        return; // WPForms not active yet.
    }
    if (get_option('trocha_contact_created') === TROCHA_CONTACT_VERSION) {
        return;
    }

    // --- Find or create the WPForms form ---
    $existing = get_posts([
        'post_type'   => 'wpforms',
        'post_status' => 'any',
        'numberposts' => 1,
        'title'       => 'Contacto TROCHA',
    ]);

    if (!empty($existing)) {
        $form_id = $existing[0]->ID;
    } else {
        $form_id = wp_insert_post([
            'post_title'   => 'Contacto TROCHA',
            'post_status'  => 'publish',
            'post_type'    => 'wpforms',
            'post_content' => '',
        ]);
    }

    if (!$form_id || is_wp_error($form_id)) {
        return;
    }

    // Store the form definition as slash-encoded JSON (WPForms format).
    $data = trocha_contact_form_data($form_id);
    $json = wp_json_encode($data);
    wp_update_post([
        'ID'           => $form_id,
        'post_content' => wp_slash($json),
    ]);
    update_option('trocha_contact_form_id', $form_id);

    // --- Create the Contacto page embedding the form ---
    $page = get_page_by_path('contacto');
    $content = '[wpforms id="' . $form_id . '" title="false" description="false"]';
    if (!$page) {
        wp_insert_post([
            'post_title'   => 'Contacto',
            'post_name'    => 'contacto',
            'post_type'    => 'page',
            'post_status'  => 'publish',
            'post_content' => $content,
        ]);
    } else {
        // Ensure the page embeds the right form.
        if (strpos($page->post_content, '[wpforms') === false) {
            wp_update_post([
                'ID'           => $page->ID,
                'post_content' => $content,
            ]);
        }
    }

    update_option('trocha_contact_created', TROCHA_CONTACT_VERSION);
}
add_action('init', 'trocha_create_contact_form', 30);
