<?php
# Skin, layout, font
$sem_theme_options['active_skin'] = 'boxed';
$sem_theme_options['active_layout'] = 'mts';
$sem_theme_options['active_font'] = '';

# Credits
$sem_theme_options['credits'] = __('Made with %1$s &bull; %2$s skin by %3$s', 'sem-reloaded');

# Version
$sem_theme_options['version'] = sem_theme_version;

add_option('init_sem_panels', '1');

# Update
if ( !defined('sem_install_test') )
	write_sem_options( $sem_theme_options);
