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
define( 'DB_NAME', 'smart_data_collector_wp_db' );

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
define( 'AUTH_KEY',         'D-F]>_I,r-DZDDWP2FE@cGFeZyTAbu7lf+.5$SBUxWnTg%n5fg(`R03h-|Fn>iTt' );
define( 'SECURE_AUTH_KEY',  '7I1^6FwJ?cs,WQ9];9m#bC+RxRVbx!UNX|6u!7KL@W;rYlKS,T8NZNMBQ%g1^(_~' );
define( 'LOGGED_IN_KEY',    'B55*%K_[0-IyQ#uv`ggx0A8uES7hlPV`SbT7cD=bD*TA#LkEUGplwFmMP7c!@MNn' );
define( 'NONCE_KEY',        '<j&]m#j[t9U(Lc-i8+N]$&[8sAzrVA.JF,JKO6W?!5LDJo>Tyd!8S},O }  XGRY' );
define( 'AUTH_SALT',        ',<X]In+$(+_l`=`-`;Gx./!JWE<Tp6T&-<![:{Oe9#7Q3`esJ gU=p(uK@bIxeZI' );
define( 'SECURE_AUTH_SALT', ')kw*N#TN0dJCJt=Wny[j?K8FeFk0_/,{22sGYoFEkCXJ={#,0l2xU6}fYi#( /?9' );
define( 'LOGGED_IN_SALT',   ';1CeBLyE{SG2 X//XFoRdxetgzc&<vfj#Y=$mJX]AgW}V>6~3PC1oA)eXchI- im' );
define( 'NONCE_SALT',       ']T6V4VwX1i~;y_0ZbDqjM,D)@4Y_3<|crmuz,9`oxLa@pLp_wfEP?BI2wT6zACVU' );

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
