<?php
// Begin AIOWPSEC Firewall
if (file_exists('D:/php/newcore/public/cms/aios-bootstrap.php')) {
	include_once('D:/php/newcore/public/cms/aios-bootstrap.php');
}
// End AIOWPSEC Firewall
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the website, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://developer.wordpress.org/advanced-administration/wordpress/wp-config/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'newcore' );

/** Database username */
define( 'DB_USER', 'root' );

/** Database password */
define( 'DB_PASSWORD', 'password' );

/** Database hostname */
define( 'DB_HOST', 'localhost' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

/** The database collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**#@+
 * Authentication unique keys and salts.
 *
 * Change these to different unique phrases! You can generate these using
 * the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}.
 *
 * You can change these at any point in time to invalidate all existing cookies.
 * This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         'Uixs4ehT).tf)|}jO;+*NFn+qW~oYYDj7Ik4_l;|S$T1/Z]t[Ybhidx`+t8 :.`;' );
define( 'SECURE_AUTH_KEY',  'iol?c~u/K:lq0ix$._|2E+n4I)`p[dOMSWnteR[I|YVE.dcdZ9<(Zq]b@Ap<qh@[' );
define( 'LOGGED_IN_KEY',    '_;1|bL0mXA^]ZY7(}VcmzQ)-J,_dxc$XkHsaqNIwMKV+a89uD!zxG}qtD}uWg%j%' );
define( 'NONCE_KEY',        '&<tJHf>9~mAPJ&]lBrO??4-x*a9Q6_1/8R{=I?E1w^I|7a%!gf9!1:]>D3n_tJP7' );
define( 'AUTH_SALT',        'q3Rp82SsZWJNrw;,UGcwj<~w |>/hYQG,JKWJUx!n}KCI8{snT?aJKW5i5X O[6p' );
define( 'SECURE_AUTH_SALT', 's.,Fpl1+B R@,@WU*5ZW;[09K$__x wT=fF-{PfIs@Y|;$v`fb)tNP} u3 0bJS5' );
define( 'LOGGED_IN_SALT',   'Q|IW7,X**{MF} t#&Eg_dO6G_;:q uau~m(%du`]le:@Yo3XFo#_w]zA)>4JSUc:' );
define( 'NONCE_SALT',       '4Tv+QfW?|I1nHQRRYT36@PgSB0>qG.&[f,!7S(D+D/YryE$.d{@C|LbsjFCAV8zX' );

/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 *
 * At the installation time, database tables are created with the specified prefix.
 * Changing this value after WordPress is installed will make your site think
 * it has not been installed.
 *
 * @link https://developer.wordpress.org/advanced-administration/wordpress/wp-config/#table-prefix
 */
$table_prefix = 'cms_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the documentation.
 *
 * @link https://developer.wordpress.org/advanced-administration/debug/debug-wordpress/
 */
define( 'WP_DEBUG', false );

/* Add any custom values between this line and the "stop editing" line. */



/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';


add_filter('xmlrpc_enabled', '__return_false');
add_filter('comments_open', '__return_false');
add_filter('pings_open', '__return_false');
add_filter('comments_array', '__return_empty_array');
add_filter( 'xmlrpc_methods', function( $methods ) {
    unset( $methods['pingback.ping'] );
    return $methods;
});
define('DISABLE_WP_CRON', true);
add_filter('rest_endpoints', function ($endpoints) {
    unset($endpoints['/wp/v2/users']);
    unset($endpoints['/wp/v2/users/(?P<id>[\d]+)']);
    return $endpoints;
});
add_filter( 'rest_authentication_errors', function( $result ) {
    if ( ! empty( $result ) ) {
        return $result;
    }
    if ( ! is_user_logged_in() ) {
        return new WP_Error('rest_disabled', 'REST API disabled.', array('status' => 403));
    }
    return $result;
});


// // Disable all public views
// if (
//     !is_admin() &&                // bukan wp-admin
//     !(defined('DOING_AJAX') && DOING_AJAX) // bukan ajax admin
// ) {
//      wp_redirect('http://ice.test', 301);
// }