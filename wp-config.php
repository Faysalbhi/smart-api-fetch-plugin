<?php
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
define( 'DB_NAME', 'smart_data_collector_wp_local_db' );

/** Database username */
define( 'DB_USER', 'root' );

/** Database password */
define( 'DB_PASSWORD', 'password' );

/** Database hostname */
define( 'DB_HOST', 'host.docker.internal' );

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
define( 'AUTH_KEY',         'eKZd;EqYF>=E~i9hVV6*L]LxvhD-*r&BU8jwAh=jS?5m8_AF?g+:.onxivdrp7VW' );
define( 'SECURE_AUTH_KEY',  'tNr`ZULZ4>zWNjAcNj8hc*psrhTc*zfe`t4E)@+@tdp]P*qN7@aG6HF&BzVjvq{h' );
define( 'LOGGED_IN_KEY',    'G{hEl(E>`[}erjA]U.M+ByboA}ymx*2#]n(/?`Yk1Xf>BC*7ktm{jqH$<Ak<JW~[' );
define( 'NONCE_KEY',        'qaM$]!@JpW`M3gwYbeJ?%sZ((:@2nD~!iMS*n8!9r8g]@%[> )1cu<wzIb+:I$#z' );
define( 'AUTH_SALT',        '[p#%~0krbFQPDy@6xn=A{OW_X!dN)^?{:cu-EdnNsy2LriFjZlN7~T=yx&E0k]>B' );
define( 'SECURE_AUTH_SALT', '|qZ@xED13^@d/>Hz2e6$p>R]92s->7ggr:4U?OOwI.o<u(>Xv?xzGKeg=*!HDtId' );
define( 'LOGGED_IN_SALT',   'Pw*0e}v}21%P)_&lR+`1QeOk%}%f8bSx<t)N:|aNOCzz8K2*/ OgoNhFPC $JYw#' );
define( 'NONCE_SALT',       '.8T?Q+pNF~mHs+Jc=hc6{N?d$l|MYcdwpH]B|G]V>c5t{VyWYMET@W7[nV?}<RXm' );

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
$table_prefix = 'wp_';

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
