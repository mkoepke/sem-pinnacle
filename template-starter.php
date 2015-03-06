<?php
/*
Template Name: Name Here
*/

function sem_template_active_layout( $layout ) {
	return 'smm';
}

//* filter active layout for this page
add_filter( 'active_layout', 'sem_template_active_layout' );


# show header
include sem_path . '/header.php';

	echo '<div id="your_id" class="body_section">' . "\n";

	echo '</div><!-- your_id -->' . "\n";

# show footer
include sem_path . '/footer.php';
