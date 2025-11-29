<?php

add_filter('xmlrpc_enabled', '__return_false');
add_filter('comments_open', '__return_false');
add_filter('pings_open', '__return_false');
add_filter('comments_array', '__return_empty_array');

add_filter('xmlrpc_methods', function ($methods) {
    unset($methods['pingback.ping']);
    return $methods;
});

add_filter('rest_endpoints', function ($endpoints) {
    unset($endpoints['/wp/v2/users']);
    unset($endpoints['/wp/v2/users/(?P<id>[\d]+)']);
    return $endpoints;
});

add_filter('rest_authentication_errors', function ($result) {
    if (!empty($result)) return $result;

    if (!is_user_logged_in()) {
        return new WP_Error('rest_disabled', 'REST API disabled.', array('status' => 403));
    }

    return $result;
});
