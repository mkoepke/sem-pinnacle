<?php
#
#
# Based on (special.PHP) - Different Layouts
#
/*
Template Name: Sidebar, Wide Content
*/

$sem_theme_options['active_layout'] = 'smm';

function sem_template_active_layout( $layout ) {
	return 'smm';
}

//* filter active layout for this page
add_filter( 'active_layout', 'sem_template_active_layout' );

include sem_path . '/index.php';

