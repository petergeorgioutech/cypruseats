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
 * * ABSPATH
 *
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'cypruseats' );

/** Database username */
define( 'DB_USER', 'root' );

/** Database password */
define( 'DB_PASSWORD', 'root' );

/** Database hostname */
define( 'DB_HOST', 'localhost' );

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
define('AUTH_KEY',         'I^oB0`fA|y+-]j=yAMYyMa)tWM-qxUqe`!3nm*f0|]&/+:a+FNrcv)>m+tW76#Ms');
define('SECURE_AUTH_KEY',  '@:CAj~]2Mb FAxbs$k89f<.arPYcO%oXT*={U(>[Av;=rY,JGXsmRNtY3Egiz.^1');
define('LOGGED_IN_KEY',    '2 =;p}exxcL.|>qBU)]hsyt?sQVz-!$z  ={YUme9>xB2F+A-t-[+BEqN<A%E(qW');
define('NONCE_KEY',        '$}LOW{iJs]wkE(uf5Uft|ixv(CJTtz`-ffOs6+g :~k,|S)|7-4_8oUc[#g/?gjX');
define('AUTH_SALT',        '^oljFN5owe]wuqcA/ 9pAj}4D(#^^/xAR<5_fHs.bmQ[s3vrqX|+~^}lhSt/-)YB');
define('SECURE_AUTH_SALT', '?(cS[|qaHBd&i|!9-kXJK[!, P+[B:qC2{Ob#vR6 52u+DH0Yxn:?Rx}!7?0K.O?');
define('LOGGED_IN_SALT',   '>nNIVA-Dy}&MFNkC@GQ;I3~&-@s7m-V&$]ttoiw|8|-h4A<F2,S3[sm6z%O}5yZg');
define('NONCE_SALT',       '[<~j|Ka:Z-y2RYO5K-O;{Z%.UL!gj)G{{LAnD:=}xf7K+%_%mGo?:6YU1=-=CaA=');

/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
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
 * @link https://wordpress.org/support/article/debugging-in-wordpress/
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
