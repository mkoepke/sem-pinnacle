<?php
#
# Setup Theme
#


global $sem6_options;
global $sem_theme_options;


//* Run the pinnacle_pre_setup hook
do_action( 'pinnacle_pre_setup' );

# set content width
switch ( $sem_theme_options['active_layout'] ) {
case 'm':
	$content_width = 550;
	break;
case 'mm':
	$content_width = 680;
	break;
case 'mmm':
	$content_width = 880;
	break;
case 'smm':
case 'mms':
	$content_width = 580;
	break;
default:
	$content_width = 480;
	break;
}

# initialize options
add_option('sem_api_key', '');

# load depends
include sem_path . '/inc/utils.php';
include sem_path . '/inc/panels.php';
include sem_path . '/inc/widgets.php';
if ( !class_exists('sem_template') )
	include sem_path . '/inc/template.php';

if ( file_exists(sem_path . '/custom.php') )
	include sem_path . '/custom.php';
if ( file_exists(sem_content_path . '/custom/custom.php') )
	include sem_content_path . '/custom/custom.php';

if ( is_admin() ) {
	include_once sem_path . '/inc/header.php';
	include_once sem_path . '/inc/layout.php';
	include_once sem_path . '/inc/skin.php';
	include_once sem_path . '/inc/font.php';
	include_once sem_path . '/inc/custom.php';


	function sem_header_admin() {
		include_once sem_path . '/inc/header.php';
	}

	foreach ( array('post.php', 'post-new.php', 'page.php', 'page-new.php') as $hook ) {
		add_action("load-$hook", 'sem_header_admin');
	}

	function sem_update() {
		include_once sem_path . '/inc/update.php';
	}

    if ( !empty($_GET['action']) && in_array( $_GET['action'], array( 'upgrade-theme', 'update-selected-themes')) )
        sem_update();

//	add_action('load-update.php', 'sem_update');
} elseif ( isset($_GET['preview']) && $_GET['preview'] == 'custom-css' ) {
	include_once dirname(__FILE__) . '/inc/custom.php';
}

//* Run the pinnacle_setup hook
do_action( 'pinnacle_post_setup' );


/**
* sempinnacle_postsetup()
*
* @return void
**/
function sempinnacle_postsetup() {
	# load textdomain
	load_theme_textdomain('sem-pinnacle', sem_path . '/lang');

	# kill page comments
	if ( !is_admin() )
		add_filter('option_page_comments', 'false');

	# kill resource hungry queries
	remove_action('wp_head', 'index_rel_link');
	remove_action('wp_head', 'parent_post_rel_link');
	remove_action('wp_head', 'start_post_rel_link');
	remove_action('wp_head', 'adjacent_posts_rel_link');

	# fix calendar, see http://core.trac.wordpress.org/ticket/9588
	if ( !class_exists('sem_fixes') ) {
		if ( function_exists('date_default_timezone_set') )
			date_default_timezone_set('UTC');
		wp_timezone_override_offset();
	}

	add_theme_support('post-thumbnails');
    add_theme_support( 'automatic-feed-links' );

	add_theme_support( 'custom-background', array ('wp-head-callback' => array('sem_template', 'custom_background_cb')) );

	add_theme_support( 'html5', array( 'comment-list', 'comment-form', 'search-form', 'gallery', 'caption' ) );
}

add_action( 'after_setup_theme', 'sempinnacle_postsetup' );
