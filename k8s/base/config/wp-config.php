<?php
/**
 * The base configuration for WordPress
 *
 * This has been slightly modified (to read environment variables) for use in Docker.
 *
 * @link https://developer.wordpress.org/advanced-administration/wordpress/wp-config/
 * @package WordPress
 */

// a helper function to lookup "env_FILE", "env", then fallback
if (!function_exists('getenv_docker')) {
  function getenv_docker($env, $default) {
    if ($fileEnv = getenv($env . '_FILE')) {
      return rtrim(file_get_contents($fileEnv), "\r\n");
    } else if (($val = getenv($env)) !== false) {
      return $val;
    } else {
      return $default;
    }
  }
}

// ** Database settings ** //
define( 'DB_NAME',     getenv_docker('WORDPRESS_DB_NAME', 'wordpress') );
define( 'DB_USER',     getenv_docker('WORDPRESS_DB_USER', 'wordpress') );
define( 'DB_PASSWORD', getenv_docker('WORDPRESS_DB_PASSWORD', 'wordpress') );
define( 'DB_HOST',     getenv_docker('WORDPRESS_DB_HOST', '127.0.0.1:3306') );

define( 'DB_CHARSET', getenv_docker('WORDPRESS_DB_CHARSET', 'utf8') );
define( 'DB_COLLATE', getenv_docker('WORDPRESS_DB_COLLATE', '') );

define( 'AUTH_KEY',         getenv_docker('WORDPRESS_AUTH_KEY',         'b7af66c8ca3f50163c863e95e12ff9bf4441ce34') );
define( 'SECURE_AUTH_KEY',  getenv_docker('WORDPRESS_SECURE_AUTH_KEY',  '019a92c13e5fc52ff2912f34a7f9f1b54746cc02') );
define( 'LOGGED_IN_KEY',    getenv_docker('WORDPRESS_LOGGED_IN_KEY',    '1a2bbd22fcd61ef70c2ef1920fb9fe26d7d71afd') );
define( 'NONCE_KEY',        getenv_docker('WORDPRESS_NONCE_KEY',        '589fa7de94ac58875dab9c437f783d3a9a5cdc18') );
define( 'AUTH_SALT',        getenv_docker('WORDPRESS_AUTH_SALT',        'a0fc5a1991211a8afd2aabf7d1fa63ce0ca4e335') );
define( 'SECURE_AUTH_SALT', getenv_docker('WORDPRESS_SECURE_AUTH_SALT', '5ca47f5aa081ca07e31510e8302fe42a93dd2ca8') );
define( 'LOGGED_IN_SALT',   getenv_docker('WORDPRESS_LOGGED_IN_SALT',   '1684a267b3b10d55ad1d477f107fff32126820c4') );
define( 'NONCE_SALT',       getenv_docker('WORDPRESS_NONCE_SALT',       '23be2672f8721abd41a865979e135ecd5da7a736') );

$table_prefix = getenv_docker('WORDPRESS_TABLE_PREFIX', 'wp_');

define( 'WP_ALLOW_MULTISITE', true );

if (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && strpos($_SERVER['HTTP_X_FORWARDED_PROTO'], 'https') !== false) {
  $_SERVER['HTTPS'] = 'on';
}

if ($configExtra = getenv_docker('WORDPRESS_CONFIG_EXTRA', '')) {
  eval($configExtra);
}

define( 'MULTISITE', true );
define( 'SUBDOMAIN_INSTALL', false );
define( 'DOMAIN_CURRENT_SITE', 'www.wp-stg.mfeed.ad.jp' );
define( 'PATH_CURRENT_SITE', '/' );
define( 'SITE_ID_CURRENT_SITE', 1 );
define( 'BLOG_ID_CURRENT_SITE', 1 );

define( 'WP_DEBUG', true );
define( 'WP_DEBUG_LOG', true );
define( 'WP_DEBUG_DISPLAY', false );

define('ALLOW_UNFILTERED_UPLOADS', true);
define('FS_METHOD', 'direct');

if ( ! defined( 'ABSPATH' ) ) {
  define( 'ABSPATH', __DIR__ . '/' );
}

require_once ABSPATH . 'wp-settings.php';