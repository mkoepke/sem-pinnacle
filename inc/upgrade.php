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

	// create new wp-content/semiologic folders
	wp_mkdir_p( sem_content_path . '/skins' );
	wp_mkdir_p( sem_content_path . '/custom' );


	// convert entry_categories to entry_footer and delete entry_tags
	$sidebar_widgets = get_option( 'sidebars_widgets' );
	$entry_widgets = $sidebar_widgets['the_entry'];

	foreach( $entry_widgets as $widget => $widget_name ) {
		if ( strpos( $widget_name, "entry_tags" ) !== false ) {
			unset( $entry_widgets[$widget] );
			continue;
		}

		$entry_widgets[$widget] = str_replace( "entry_categories", "entry_footer", $widget_name );
	}
	$sidebar_widgets['the_entry'] = $entry_widgets;

	$sidebar_widgets['wp_inactive_widgets'] = array();

	update_option( 'sidebars_widgets', $sidebar_widgets);

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
