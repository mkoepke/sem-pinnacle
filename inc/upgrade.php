<?php

/**
 * upgrade_to_sem_pinnacle()
 *
 * @return void
 **/

function upgrade_to_sem_pinnacle() {
	# convert multi-level menus excludes to semiologic menu exclude
	$menu = get_option('pixopoint-menu', array());
	if ( !empty( $menu )) {
		$excludepages = isset($menu['excludepages'] ) ? $menu['excludepages'] : '';
		$excludepages = explode( ',', $excludepages );
		foreach ( $excludepages as $i => $post_id ) {
			update_post_meta($post_id, '_menu_exclude', '1');
		}
	}

	wp_mkdir_p( sem_content_path . '/skins' );
	wp_mkdir_p( sem_content_path . '/custom' );

} # upgrade_to_sem_pinnacle()



global $sem_theme_options;

if ( version_compare($sem_theme_options['version'], '2.1', '<') )
	upgrade_to_sem_pinnacle();

if ( !defined('sem_upgrade_test') ) {
	$sem_theme_options['version'] = sem_theme_version;
}

#dump($sem_theme_options);die;

if ( !defined('sem_install_test') ) {
	write_sem_options( $sem_theme_options);
}

do_action('flush_cache');
