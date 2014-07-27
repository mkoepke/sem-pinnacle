<?php

/**
 * upgrade_to_sem_pinnacle()
 *
 * @return void
 **/

function upgrade_to_sem_pinnacle() {
	global $sem_theme_options;
	global $sem_stock_skins;

	# convert multi-level menus excludes to semiologic menu exclude
	$menu = get_option('pixopoint-menu', array());
	$front_page_id = get_option('page_on_front');

	if ( !empty( $menu )) {
		$excludepages = isset($menu['excludepages'] ) ? $menu['excludepages'] : '';
		$excludepages = explode( ',', $excludepages );
		foreach ( $excludepages as $i => $post_id ) {
			if ( $post_id != $front_page_id )
				update_post_meta($post_id, '_menu_exclude', '1');
		}
	}

	// create new wp-content/semiologic folders
	wp_mkdir_p( sem_content_path . '/skins' );
	wp_mkdir_p( sem_content_path . '/custom' );


	if (  !in_array( $sem_theme_options['active_skin'], $sem_stock_skins ))
		$sem_theme_options['active_skin'] = 'boxed';

	update_option('upgrade_sempinnacle_panels', '1');

} # upgrade_to_sem_pinnacle()



global $sem_theme_options;

if ( $sem_theme_options['version'] == -1 )
	upgrade_to_sem_pinnacle();

#dump($sem_theme_options);die;

if ( !defined('sem_upgrade_test') ) {
	$sem_theme_options['version'] = sem_theme_version;
	write_sem_options( $sem_theme_options);
}

do_action('flush_cache');
