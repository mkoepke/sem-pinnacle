<?php
#
# Initialize
#

if ( !defined('sem_theme') )
	define('sem_theme', 'sem-pinnacle');

if ( !defined('sem_theme_version') )
	define('sem_theme_version', '2.0');

if ( !defined('sem_html5_only') )
	define('sem_html5_only', true);

if ( !defined('sem_debug') )
	define('sem_debug', isset($_GET['debug']) );
elseif ( !isset($_GET['debug']) && !$_POST )
	$_GET['debug'] = sem_debug;

if ( !defined('sem_widget_cache_debug') )
	define('sem_widget_cache_debug', false);

if ( !defined('sem_header_cache_debug') )
	define('sem_header_cache_debug', false);

if ( !defined('sem_css_debug') )
	define('sem_css_debug', false);

define('sem_last_mod', sem_css_debug ? time() : '20140108');

if ( function_exists('memory_get_usage') && ( (int) @ini_get('memory_limit') < 64 ) )
	@ini_set('memory_limit', '64M');

define('sem_path', dirname(dirname(__FILE__)));
define('sem_url', get_stylesheet_directory_uri());
define('sem_content_path', WP_CONTENT_DIR . '/semiologic' );
define('sem_content_url', WP_CONTENT_URL . '/semiologic' );

#
# extra functions
#

if ( !function_exists('true') ) :
/**
 * true()
 *
 * @param bool $bool
 * @return bool true
 **/

function true($bool = null) {
	return true;
} # true()
endif;


if ( !function_exists('false') ) :
/**
 * false()
 *
 * @param bool $bool
 * @return bool false
 **/

function false($bool = null) {
	return false;
} # false()
endif;


if ( !function_exists('is_letter') ) :
/**
 * is_letter()
 *
 * @return bool $is_letter
 **/

function is_letter() {
	return is_page() && get_post_meta(get_the_ID(), '_wp_page_template', true) == 'letter.php';
} # is_letter()
endif;


if ( !function_exists('dump') ) :
/**
 * dump()
 *
 * @param mixed $in
 * @return mixed $in
 **/

function dump($in = null) {
	echo '<pre style="margin-left: 0px; margin-right: 0px; padding: 10px; border: solid 1px black; background-color: ghostwhite; color: black; text-align: left;">';
	foreach ( func_get_args() as $var ) {
		echo "\n";
		if ( is_string($var) ) {
			echo "$var\n";
		} else {
			var_dump($var);
		}
	}
	echo '</pre>' . "\n";
	
	return $in;
} # dump()
endif;

if ( sem_debug )
	include sem_path . '/inc/debug.php';


$sem_stock_skins = array("boxed", "clean");

#
# Initialize options
#

$sem_theme_options = read_sem_options();

# autoinstall test
#$sem_theme_options = false;


#
# install / upgrade
#
if ( !isset($sem_theme_options['version']) ) {
	$sem6_options = get_option('sem6_options');
	$sem5_options = get_option('sem5_options');

	if ( isset( $sem6_options['version'] ) || isset( $sem5_options['version'] ) ) {
		// try convert the semiologic and sem-reloaded themes
		$sem_options = isset( $sem6_options['version'] ) ? $sem6_options : $sem5_options;
		if ( !defined('DOING_CRON') )
			include sem_path . '/inc/upgrade-legacy.php';

		$sem6_options = get_option('sem6_options');

		$sem_theme_options = $sem6_options;
		$sem_theme_options['version'] = 0;

		if ( !defined('DOING_CRON') )
			include sem_path . '/inc/upgrade.php';

		// clone the sem_reloaded options to the sem_pinnacle options on first time use
		if ( get_option('theme_mods_sem-pinnacle') === FALSE ) {
			$o = get_option('theme_mods_sem-reloaded');
			if ( $o !== FALSE ) {
				update_option( 'theme_mods_sem-pinnacle', $o );
				update_option( 'sidebars_widgets', $o['sidebars_widgets'] );
			}
		}
	}
	else {
		include sem_path . '/inc/install.php';
	}
} elseif ( ( $sem_theme_options['version'] != sem_theme_version ) || defined('sem_upgrade_test') ) {
	if ( !defined('DOING_CRON') )
		include sem_path . '/inc/upgrade.php';
}