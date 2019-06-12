<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the
 * installation. You don't have to use the web site, you can
 * copy this file to "wp-config.php" and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * MySQL settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://codex.wordpress.org/Editing_wp-config.php
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 's4l' );

/** MySQL database username */
define( 'DB_USER', 'root' );

/** MySQL database password */
define( 'DB_PASSWORD', 'rootroot' );

/** MySQL hostname */
define( 'DB_HOST', 'localhost' );

/** Database Charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

/** The Database Collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         'K?qo`w<Ro]BzMrNTr+[3W:lH+q^pJ82t6M3e+;Q`4etOH3o]|%bSZ5G<hN_0PXE:' );
define( 'SECURE_AUTH_KEY',  '+CQ5vH>)4t5E1a]VJfa!&QsnO>(~:HYb{~55LG$j~)il`4k<!xTI0I,GdAi I7iQ' );
define( 'LOGGED_IN_KEY',    'OYTg@|>s97|`ifRm04n+0g~XMj)k7`)jg(uBd&>?(b^_1pWDaJ6a0S&lydkjAk+)' );
define( 'NONCE_KEY',        'vgwSfEkr$;PWQTLiybGcEH77S}/m@$-^X3)lA>stC.taKU1x(evX;ueR>?Xursk1' );
define( 'AUTH_SALT',        '28acdH}$b#vk{*b-{@><<$8(k+~oWnpt$J$%P.S9v(q{pZVoAv2>yKJ$&ac-+^k5' );
define( 'SECURE_AUTH_SALT', '3thZJP7WGvv9ii$Js7X;b%,*<?R*BCXRaKV0H7<]/4Ky6(*)N|Jm3%Rgm1.<sYN$' );
define( 'LOGGED_IN_SALT',   '5Czp|r8_|gA`DnE6m}X2i$5Dgr%IHDbrR~lw*HhS1IAQM ykM1wDVs?6-X>bA2F0' );
define( 'NONCE_SALT',       'a%@MDM&rD)*Y@aW2D?_vgH+,;?&O*R)OVGl/n67-w0=DZ=)U|~hbra9*IT{|ZZvF' );

/**#@-*/

/**
 * WordPress Database Table prefix.
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
 * visit the Codex.
 *
 * @link https://codex.wordpress.org/Debugging_in_WordPress
 */
define( 'WP_DEBUG', false );

/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', dirname( __FILE__ ) . '/' );
}

/** Sets up WordPress vars and included files. */
require_once( ABSPATH . 'wp-settings.php' );