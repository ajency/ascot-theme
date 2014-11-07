<?php
/**
 * The base configurations of the WordPress.
 *
 * This file has the following configurations: MySQL settings, Table Prefix,
 * Secret Keys, WordPress Language, and ABSPATH. You can find more information
 * by visiting {@link http://codex.wordpress.org/Editing_wp-config.php Editing
 * wp-config.php} Codex page. You can get the MySQL settings from your web host.
 *
 * This file is used by the wp-config.php creation script during the
 * installation. You don't have to use the web site, you can just copy this file
 * to "wp-config.php" and fill in the values.
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', 'ascotep');

/** MySQL database username */
define('DB_USER', 'root');

/** MySQL database password */
define('DB_PASSWORD', '');

/** MySQL hostname */
define('DB_HOST', 'localhost');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8');

/** The Database Collate type. Don't change this if in doubt. */
define('DB_COLLATE', '');

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         '+Rm$,rOo!q:a##Tr1<Fvtb)$8P}-,*;&-rxRjOVb|SF|`3zB.wjo#--^Gl+4||lE');
define('SECURE_AUTH_KEY',  '!h6=DGXz@%<DR+[&%J .9&}{xTs>fOQgEbbW|c@)rP<I]tr@LXJZ~;h+WPv..8Ei');
define('LOGGED_IN_KEY',    'c[zh}dM4BL5j|gG.-8I@!n^zhO;1Fc$HZYoxdl(?&4JE=Oqu}/@5lFdd9-_p7i:H');
define('NONCE_KEY',        'bzu?j>Ex#m1 5^Eq1DL~+C]0E!K5ub0SU[&Q{6kXf`H(|bDe!GCR00qn5-*E4&h8');
define('AUTH_SALT',        'g-T&hY>f3NzXRs`TX773Jjdml-tG fJ9NPg|joXkMj>MM{-*O>Al kV&0I_-_H]g');
define('SECURE_AUTH_SALT', 'x|i|lY[| =.-c7+K;N8+Xn|8T+Y-%@kwCe5>VA$|?c@!!^u!nTA(A~pB-(KO<2-U');
define('LOGGED_IN_SALT',   'ikP$/z@HxI^h[^h|lB?qrvxLMS1dG5+z~qE`GN(Bv0.VQ?%ptfP=cbV1jznQA6a)');
define('NONCE_SALT',       'uzXlbTCs#S]9 JQKZ$R|wD(XqAN@fD&`0ndDK#_Pd7o/yG,t/mf]=#buN[5=Z|w}');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each a unique
 * prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wp_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 */
define('WP_DEBUG', false);

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
