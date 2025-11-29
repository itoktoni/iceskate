<?php
add_action('init', function () {

    if (!isset($_GET['token'])) return;

    $secret = $_SERVER['APP_KEY']
       ?? $_ENV['APP_KEY']
       ?? getenv('APP_KEY')
       ?? null;

    if (!$secret) return;

    list($b64, $sig) = explode('.', $_GET['token']);

    // Verify signature
    $calc = hash_hmac('sha256', $b64, $secret);

    if (!hash_equals($calc, $sig)) {
        return; // tampered or invalid
    }

    // Decode payload
    $json = base64_decode($b64);
    $data = json_decode($json, true);
    if (!$data) return;

    // Token expires after 20 seconds
    if (time() - $data['time'] > 10) return;

    // Login
    $user = get_user_by('email', $data['email']);
    if (!$user) return;

    wp_set_current_user($user->ID);
    wp_set_auth_cookie($user->ID);

    wp_safe_redirect(admin_url());
    exit;
});
