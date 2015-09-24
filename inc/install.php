<?php

include sem_path . '/inc/template.php';

# Skin, layout, font
$sem_theme_options['active_skin'] = 'boxed';
$sem_theme_options['active_layout'] = 'mts';
$sem_theme_options['active_font'] = '';
$sem_theme_options['external_fonts'] = '';
$sem_theme_options['skin_data'] = sem_template::get_skin_data($sem_theme_options['active_skin']);

# Credits
$sem_theme_options['credits'] = __('Made with %1$s &bull; %2$s skin by %3$s', 'sem-pinnacle');

# Version
$sem_theme_options['version'] = sem_theme_version;

add_option('init_sem_panels', '1');

# Update
if ( !defined('sem_install_test') )
	write_sem_options( $sem_theme_options);


wp_mkdir_p( sem_content_path . '/skins' );
wp_mkdir_p( sem_content_path . '/custom' );
