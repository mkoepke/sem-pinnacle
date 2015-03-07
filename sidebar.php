<?php

global $sem_theme_options;

if ( !isset( $active_layout ) )
	$active_layout = apply_filters('active_layout', $sem_theme_options['active_layout']);

# sidebars
switch ( $active_layout ) :

case 'mts' :
case 'tsm' :


	# sidebars wrapper

	echo '<div id="sidebars">' . "\n";

	echo '<div class="sidebars_content">' . "\n";


	# top sidebar

	echo '<div id="top_sidebar" class="sidebar wide_sidebar" role="complementary" itemscope="itemscope" itemtype="http://schema.org/WPSideBar">' . "\n";

	sem_panels::display('top_sidebar');

	echo '</div><!-- top sidebar -->' . "\n";


	# spacer

	echo '<div class="spacer"></div>' . "\n";


	# left sidebar

	echo '<div id="sidebar" class="sidebar" role="complementary" itemscope="itemscope" itemtype="http://schema.org/WPSideBar">' . "\n";

	sem_panels::display('left_sidebar');

	echo '</div><!-- left sidebar -->' . "\n";


	# right sidebar

	echo '<div id="sidebar2" class="sidebar" role="complementary" itemscope="itemscope" itemtype="http://schema.org/WPSideBar">' . "\n";

	sem_panels::display('right_sidebar');

	echo '</div><!-- right sidebar -->' . "\n";


	# spacer

	echo '<div class="spacer"></div>' . "\n";


	# bottom sidebar

	echo '<div id="bottom_sidebar" class="sidebar wide_sidebar" role="complementary" itemscope="itemscope" itemtype="http://schema.org/WPSideBar">' . "\n";

	sem_panels::display('bottom_sidebar');

	echo '</div><!-- bottom sidebar -->' . "\n";


	# spacer

	echo '<div class="spacer"></div>' . "\n";


	# end sidebars wrapper

	echo '</div>' . "\n";

	echo '</div><!-- sidebars -->' . "\n";


	# spacer

	echo '<div class="spacer"></div>' . "\n";

	break;


case 'sms' :


	# left sidebar

	echo '<div id="sidebar" class="sidebar" role="complementary" itemscope="itemscope" itemtype="http://schema.org/WPSideBar">' . "\n";

	echo '<div class="sidebar_content">' . "\n";

	sem_panels::display('left_sidebar');

	echo '</div>' . "\n";

	echo '</div><!-- left sidebar -->' . "\n";


	# spacer

	echo '<div class="spacer"></div>' . "\n";


	# end sidebar wrapper

	echo '</div><!-- sidebar wrapper -->' . "\n";


	# right sidebar

	echo '<div id="sidebar2" class="sidebar" role="complementary" itemscope="itemscope" itemtype="http://schema.org/WPSideBar">' . "\n";

	echo '<div class="sidebar_content">' . "\n";

	sem_panels::display('right_sidebar');

	echo '</div>' . "\n";

	echo '</div><!-- right sidebar -->' . "\n";


	# spacer

	echo '<div class="spacer"></div>' . "\n";

	break;


case 'mms' :
case 'smm' :


	# left sidebar

	echo '<div id="sidebar" class="sidebar" role="complementary" itemscope="itemscope" itemtype="http://schema.org/WPSideBar">' . "\n";

	echo '<div class="sidebar_content">' . "\n";

	sem_panels::display('left_sidebar');

	echo '</div>' . "\n";

	echo '</div><!-- left sidebar -->' . "\n";


	# spacer

	echo '<div class="spacer"></div>' . "\n";

	break;


case 'ms' :
case 'sm' :


	# left sidebar

	echo '<div id="sidebar" class="sidebar" role="complementary" itemscope="itemscope" itemtype="http://schema.org/WPSideBar">' . "\n";

	echo '<div class="sidebar_content">' . "\n";

	sem_panels::display('left_sidebar');

	echo '</div>' . "\n";

	echo '</div><!-- left sidebar -->' . "\n";


	# spacer

	echo '<div class="spacer"></div>' . "\n";

	break;

endswitch;

