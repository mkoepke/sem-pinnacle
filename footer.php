<?php
#
# DO NOT EDIT THIS FILE
# ---------------------
# You would lose your changes when you upgrade your site. Use php widgets instead.
#

			
		# end content
		echo '</div><!-- main_content -->' . "\n";

		echo '</main><!-- main -->' . "\n";

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

	echo '</div><!-- main_wrapper -->' . "\n";

	echo '<div id="body_bottom_sidebar" class="sidebar" role="complementary" itemscope="itemscope" itemtype="http://schema.org/WPSideBar">' . "\n";

	sem_panels::display('bottom_body_sidebar');

	echo '</div><!-- body_bottom_sidebar -->' . "\n";

	# end body
	
	echo '</div><!-- body_middle -->' . "\n";
	
	echo '<div id="body_bottom" class="body_section"><div class="hidden"></div></div>' . "\n";

	echo '</div><!-- body_wrapper -->' . "\n";
	
	# footer
	
	if ( $active_layout != 'letter') :
		
		sem_panels::display('the_footer');
		
	endif;


do_action('wp_footer');

# end wrapper

echo '</div><!-- wrapper_middle -->' . "\n";

echo '<div id="wrapper_bottom" class="wrapper_section"><div class="hidden"></div></div>' . "\n";

echo '</div><!-- wrapper -->' . "\n";


echo '</div><!-- site_container -->' . "\n";

do_action('after_the_canvas');

do_action('body_close');
?>
</body>
</html>