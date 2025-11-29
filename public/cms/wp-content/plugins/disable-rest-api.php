<?php
add_filter('rest_authentication_errors', function() {
    return new WP_Error('rest_disabled', 'REST API is disabled.', array('status' => 403));
});

add_action('init', function() {
    remove_post_type_support('post', 'comments');
    remove_post_type_support('page', 'comments');
});

function disable_feed() {
    wp_die('Feeds are disabled.');
}

add_action('do_feed', 'disable_feed', 1);
add_action('do_feed_rdf', 'disable_feed', 1);
add_action('do_feed_rss', 'disable_feed', 1);
add_action('do_feed_rss2', 'disable_feed', 1);
add_action('do_feed_atom', 'disable_feed', 1);

define('DISABLE_WP_CRON', true);