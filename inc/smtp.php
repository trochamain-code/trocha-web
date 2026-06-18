<?php
/**
 * TROCHA — SMTP delivery via Hostinger.
 *
 * Routes all WordPress mail (incl. the WPForms contact form) through
 * Hostinger's SMTP server so messages actually reach the mailbox.
 *
 * Credentials are read from wp-config.php constants when available
 * (recommended), otherwise from the fallbacks defined here.
 *
 * Hostinger SMTP reference:
 *   Host: smtp.hostinger.com
 *   Port: 465  (SSL)   — or 587 (TLS)
 *   User: full email address  (e.g. alvarocontacto@trocha.shop)
 *   Pass: that mailbox password
 *   From: must match the authenticated mailbox
 */
defined('ABSPATH') || exit;

/**
 * Central place for the SMTP settings.
 */
function trocha_smtp_settings() {
    return [
        'host'       => defined('TROCHA_SMTP_HOST') ? TROCHA_SMTP_HOST : 'smtp.hostinger.com',
        'port'       => defined('TROCHA_SMTP_PORT') ? TROCHA_SMTP_PORT : 465,
        'encryption' => defined('TROCHA_SMTP_ENC')  ? TROCHA_SMTP_ENC  : 'ssl', // 'ssl' (465) or 'tls' (587)
        'username'   => defined('TROCHA_SMTP_USER') ? TROCHA_SMTP_USER : 'trochamain@gmail.com',
        'password'   => defined('TROCHA_SMTP_PASS') ? TROCHA_SMTP_PASS : '',
        'from_email' => defined('TROCHA_SMTP_FROM') ? TROCHA_SMTP_FROM : 'trochamain@gmail.com',
        'from_name'  => defined('TROCHA_SMTP_NAME') ? TROCHA_SMTP_NAME : 'TROCHA',
    ];
}

/**
 * Configure PHPMailer to use Hostinger SMTP for every outgoing email.
 */
function trocha_configure_smtp($phpmailer) {
    $cfg = trocha_smtp_settings();

    // Without a password we cannot authenticate — bail (falls back to default mailer).
    if (empty($cfg['password'])) {
        return;
    }

    $phpmailer->isSMTP();
    $phpmailer->Host        = $cfg['host'];
    $phpmailer->SMTPAuth    = true;
    $phpmailer->Port        = (int) $cfg['port'];
    $phpmailer->Username    = $cfg['username'];
    $phpmailer->Password    = $cfg['password'];
    $phpmailer->SMTPSecure  = $cfg['encryption']; // 'ssl' or 'tls'

    // Sender must match the authenticated mailbox (SPF/DKIM + Hostinger policy).
    $phpmailer->setFrom($cfg['from_email'], $cfg['from_name'], false);
    $phpmailer->Sender = $cfg['from_email']; // Return-Path / envelope sender

    // Local dev environments often have broken CA bundles — relax verification
    // so the TLS handshake to Hostinger succeeds on localhost.
    $phpmailer->SMTPOptions = [
        'ssl' => [
            'verify_peer'       => false,
            'verify_peer_name'  => false,
            'allow_self_signed' => true,
        ],
    ];
}
add_action('phpmailer_init', 'trocha_configure_smtp');

/**
 * Force the From name/email globally (some plugins override it otherwise).
 */
add_filter('wp_mail_from', function ($email) {
    $cfg = trocha_smtp_settings();
    return $cfg['from_email'] ?: $email;
});
add_filter('wp_mail_from_name', function ($name) {
    $cfg = trocha_smtp_settings();
    return $cfg['from_name'] ?: $name;
});

/**
 * Admin-only test endpoint:  /?trocha_smtp_test=1&to=you@example.com
 * Sends a test email and prints the result. Requires being logged in as admin.
 */
function trocha_smtp_test_endpoint() {
    if (empty($_GET['trocha_smtp_test'])) {
        return;
    }
    if (!current_user_can('manage_options')) {
        wp_die('No autorizado.');
    }

    header('Content-Type: text/plain; charset=utf-8');

    $cfg = trocha_smtp_settings();
    echo "=== TROCHA SMTP TEST ===\n";
    echo "Host: {$cfg['host']}:{$cfg['port']} ({$cfg['encryption']})\n";
    echo "User: {$cfg['username']}\n";
    echo "Password set: " . (empty($cfg['password']) ? 'NO — set TROCHA_SMTP_PASS in wp-config.php' : 'yes') . "\n";
    echo "From: {$cfg['from_name']} <{$cfg['from_email']}>\n\n";

    $to = isset($_GET['to']) ? sanitize_email($_GET['to']) : $cfg['from_email'];

    // Capture the full SMTP conversation for diagnostics.
    $debug = [];
    add_action('phpmailer_init', function ($pm) use (&$debug) {
        $pm->SMTPDebug   = 2; // client + server messages
        $pm->Debugoutput = function ($str, $level) use (&$debug) {
            $debug[] = rtrim($str);
        };
    }, 99);

    $error = '';
    add_action('wp_mail_failed', function ($e) use (&$error) { $error = $e->get_error_message(); });

    $ok = wp_mail(
        $to,
        'TROCHA — Test SMTP ' . date('H:i:s'),
        "Si recibes este correo, el SMTP de Hostinger funciona correctamente.\n\nEnviado desde " . home_url(),
        ['Content-Type: text/plain; charset=UTF-8']
    );

    echo "Destinatario: $to\n";
    echo "wp_mail() => " . var_export($ok, true) . "\n";
    if ($error) echo "ERROR: $error\n";
    echo "\n--- SMTP LOG ---\n" . implode("\n", $debug) . "\n";
    echo "\nRevisa la bandeja (y SPAM) de $to.\n";
    exit;
}
add_action('init', 'trocha_smtp_test_endpoint');
