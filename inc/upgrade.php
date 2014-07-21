<?php

/**
 * upgrade_to_sem_pinnacle()
 *
 * @return void
 **/

function upgrade_to_sem_pinnacle() {
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

	// clone the sem_reloaded options to the sem_pinnacle options on first time use
	global $_wp_sidebars_widgets;

	$reloaded_widgets = get_option('theme_mods_sem-reloaded');
	if ( $reloaded_widgets !== FALSE ) {

		$sidebars_widgets = wp_get_sidebars_widgets();
		$sidebars = array_keys($sidebars_widgets);
		foreach ( $sidebars as $sidebar ) {
            $sidebars_widgets[$sidebar] = array();
		}

		$reloaded_widgets = $reloaded_widgets['sidebars_widgets']['data'];
		$reloaded_sidebars = array_keys($reloaded_widgets);
		foreach ( $reloaded_sidebars as $sidebar ) {
            $sidebars_widgets[$sidebar] = (array) $reloaded_widgets[$sidebar];
		}

		global $_wp_sidebars_widgets;

		$_wp_sidebars_widgets = $sidebars_widgets;

		wp_set_sidebars_widgets( $sidebars_widgets );
	}

	// convert entry_categories to entry_footer and delete entry_tags
	$sidebars_widgets = wp_get_sidebars_widgets();
	$entry_widgets = $sidebars_widgets['the_entry'];

	foreach( $entry_widgets as $widget => $widget_name ) {
		if ( ( strpos( $widget_name, "entry_tags" ) !== false ) || ( strpos( $widget_name, "entry_categories" ) !== false )) {
			unset( $entry_widgets[$widget] );
			continue;
		}

	}

	$sidebars_widgets['the_entry'] = $entry_widgets;

	$_wp_sidebars_widgets = $sidebars_widgets;

	wp_set_sidebars_widgets( $sidebars_widgets );

	$sem_theme_options['active_skin'] = 'boxed';

	update_option('init_sem_panels', '1');

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
