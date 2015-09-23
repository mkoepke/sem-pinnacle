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
get_header();

# content
echo '<main id="main" class="main" role="main" itemprop="mainContentOfPage">' . "\n";

echo '<div class="main_content entry">' . "\n";

echo '<div id="your_id" class="body_section">' . "\n";

echo '</div><!-- your_id -->' . "\n";

echo '</div><!-- main_content -->' . "\n";

echo '</main><!-- main -->' . "\n";

get_sidebar();

# show footer
get_footer();
