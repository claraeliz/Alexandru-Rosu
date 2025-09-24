<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the web site, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * Localized language
 * * ABSPATH
 *
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'wp_vznnn' );

/** Database username */
define( 'DB_USER', 'wp_vzjjw' );

/** Database password */
define( 'DB_PASSWORD', 'HwXD17tK4?Mso@vq' );

/** Database hostname */
define( 'DB_HOST', 'localhost:3306' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8' );

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
define('AUTH_KEY', 'NA6(Vc;8E9V81J3SK9FxTo]Co%S3oxP3h37h(76O@5ZnFTdw995UR*/83)::y9z*');
define('SECURE_AUTH_KEY', 'TN6n5w1%|d~C74bZ|5/@jQpIG@Ki~M~Z&K49sdMw(w%]5+%+MFlAa/2yc!2-f[l0');
define('LOGGED_IN_KEY', 'V4Rh!&4[9:z848U*7XVK23&iGF%@%a%_;39PetdBGE#UI;xVtC1]1#5!T&@MeP]f');
define('NONCE_KEY', '5OusI302r3I%7aFh14jFokX6)C@ddRY5m0)8w94&+T|7@(a710F!ho1Bf+#T)E:w');
define('AUTH_SALT', '+u;lWsH1a2tMy_O*JCoh[5*53S:2pw;P63/9Q|@(95L_PP828aTMf[;t[934[7Y~');
define('SECURE_AUTH_SALT', ')GXHp9([:!(pC;(KwGy3[wb9nu!)_g4&u5!+AWws7F(09&5W5a3DPB#[cH1DsbA~');
define('LOGGED_IN_SALT', 'pbk]jH*vC&4ap75S*w)D36vEIn|wb95E7%pbRsLlah8(IE|%jK@wcbQFpiO256&y');
define('NONCE_SALT', '~B@@90xz5R1ec([2e92m69I%MD6_~-(N1Sk@G[azbTkEd9jo*EBLdV3]Dm!3xY8I');


/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'pdn6Aiod_';


/* Add any custom values between this line and the "stop editing" line. */

define('WP_ALLOW_MULTISITE', true);
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
 * @link https://wordpress.org/support/article/debugging-in-wordpress/
 */
if ( ! defined( 'WP_DEBUG' ) ) {
	define( 'WP_DEBUG', true );
}

define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
define('WP_DEBUG_DISPLAY', true);


define( 'DISABLE_WP_CRON', true );
define( 'DISALLOW_FILE_EDIT', true );
define( 'CONCATENATE_SCRIPTS', false );
/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
