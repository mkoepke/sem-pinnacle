<?php
/*
Template Name: Wide Content, Sidebar
*/

global $sem_theme_options;
$sem_theme_options['active_layout'] = 'mms';

function sem_template_active_layout( $layout ) {
	return 'mms';
}

//* filter active layout for this page
add_filter( 'active_layout', 'sem_template_active_layout' );



include sem_path . '/index.php';

