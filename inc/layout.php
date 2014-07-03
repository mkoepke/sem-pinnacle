<?php
/**
 * sem_layout
 *
 * @package Semiologic Reloaded
 **/

class sem_layout {
	/**
	 * Holds the instance of this class.
	 *
	 * @since  0.5.0
	 * @access private
	 * @var    object
	 */
	private static $instance;


	/**
	 * Returns the instance.
	 *
	 * @since  0.5.0
	 * @access public
	 * @return object
	 */
	public static function get_instance() {

		if ( !self::$instance )
			self::$instance = new self;

		return self::$instance;
	}

	/**
	 * Constructor.
	 *
	 */
	public function __construct() {
        add_action('semiologic_page_layout', array($this, 'save_options'), 0);
        add_action('admin_head', array($this, 'admin_head'));
		add_action('wp_enqueue_scripts', array($this, 'scripts'));
    }

    /**
	 * admin_head()
	 *
	 * @return void
	 **/
	
	function admin_head() {
		echo <<<EOS

<style type="text/css">
#current_option img {
	border: solid 1px #999;
	float: left;
	clear: right;
	margin-right: 0.625em;
}

.current_option_details th {
	text-align: left;
	padding-right: 0.3125em;
}

.available_option {
	text-align: center;
	width: 17.1875em;
}

.available_option img {
	border: solid 1px #ccc;
}

.available_option label {
	cursor: pointer !important;
}

#available_options {
	border-collapse: collapse;
}

#available_options td {
	padding: 0.625em;
	border: solid 1px #ccc;
}

#available_options td.top {
	border-top: none;
}

#available_options td.bottom {
	border-bottom: none;
}

#available_options td.left {
	border-left: none;
}

#available_options td.right {
	border-right: none;
}
</style>

<script type="text/javascript">
jQuery(document).ready(function() {
	jQuery("#available_options label").click(function() {
		jQuery(this).closest('td').find('input:radio').attr('checked', 'checked');
		jQuery('#option_picker').trigger('submit');
	});
});
</script>

EOS;
	} # admin_head()

	/**
	 * scripts()
	 *
	 * @return void
	 **/

	function scripts() {
		wp_enqueue_script('jquery');
	} # scripts()
	
	/**
	 * save_options()
	 *
	 * @return void
	 **/

	function save_options() {
		if ( !$_POST || !current_user_can('switch_themes') )
			return;
		
		check_admin_referer('sem_layout');
		
		global $sem_theme_options;
		$sem_theme_options['active_layout'] = preg_replace("/[^mst]/", "", $_POST['layout']);
		
		write_sem_options( $sem_theme_options);
		
		echo '<div class="updated fade">'
			. '<p><strong>'
			. __('Settings saved.', 'sem-pinnacle')
			. '</strong></p>'
			. '</div>' . "\n";
	} # save_options()
	
	
	/**
	 * edit_options()
	 *
	 * @return void
	 **/

	static function edit_options() {
		echo '<div class="wrap">' . "\n";
		echo '<form method="post" action="" id="option_picker">' . "\n";
		
		wp_nonce_field('sem_layout');
		
		global $sem_theme_options;
		$layouts = sem_layout::get_layouts();
		
		echo '<h2>' . __('Manage Layout', 'sem-pinnacle') . '</h2>' . "\n";
		
		echo '<h3>' . __('Current Layout', 'sem-pinnacle') . '</h3>' . "\n";
		
		$details = $layouts[$sem_theme_options['active_layout']];
		$screenshot = sem_url . '/inc/img/' . $sem_theme_options['active_layout'] . '.png';
		
		echo '<div id="current_option">' . "\n";
		
		echo '<img src="' . esc_url($screenshot) . '" alt="" />' . "\n";
		
		echo '<h4>' . $details['name'] . '</h4>';
		
		echo '<table class="current_option_details">' . "\n";
		
		foreach ( array(
			'wrapper' => __('Canvas', 'sem-pinnacle'),
			'content' => __('Content', 'sem-pinnacle'),
			'wide_sidebars' => __('Wide Sidebar', 'sem-pinnacle'),
			'sidebars' => __('Sidebars', 'sem-pinnacle'),
			'inline_boxes' => __('Inline Boxes', 'sem-pinnacle'),
			) as $key => $detail ) {
			echo '<tr>'
				. '<th scope="row">'
				. $detail
				. '</th>'
				. '<td>'
				. $details[$key]
				. '</td>' . "\n";
		}	
		
		echo '</table>' . "\n";
		
		echo '<p>' . __('Note: These numbers may vary slightly depending on the skin you are using.', 'sem-pinnacle') . '</p>' . "\n";
		
		echo '<div style="clear: both;"></div>' . "\n";
		
		echo '</div>' . "\n";
		
		echo '<h3>' . __('Available Layouts', 'sem-pinnacle') . '</h3>' . "\n";
		
		echo '<p class="hide-if-no-js">'
			. __('Click on a layout below to activate it immediately.', 'sem-pinnacle')
			. '</p>' . "\n";
		
		echo '<table id="available_options" cellspacing="0" cellpadding="0">' . "\n";
		
		$row_size = 4;
		$num_rows = ceil(count($layouts) / $row_size);
		
		$i = 0;
		
		foreach ( $layouts as $layout => $details ) {
			if ( $i && !( $i % $row_size ) )
				echo '</tr>' . "\n";
			
			if ( !( $i % $row_size ) )
				echo '<tr>' . "\n";
			
			$classes = array('available_option');
			if ( ceil(( $i + 1 ) / $row_size) == 1 )
				$classes[] = 'top';
			if ( ceil(( $i + 1 ) / $row_size) == $num_rows )
				$classes[] = 'bottom';
			if ( !( $i % $row_size ) )
				$classes[] = 'left';
			elseif ( !( ( $i + 1 ) % $row_size ) )
				$classes[] = 'right';
			
			$i++;
			
			echo '<td class="' . implode(' ', $classes) . '">' . "\n";
			
			$screenshot = sem_url . '/inc/img/' . $layout . '.png';
			
			echo '<h4>'
				. '<label for="layout-' . $layout . '">'
				. '<img src="' . esc_url($screenshot) . '" alt="" />'
				. '</label>'
				. '</p>' . "\n"
				. '<p>'
				. '<label for="layout-' . $layout . '">'
				. '<span class="hide-if-js">'
				. '<input type="radio" name="layout" value="' . $layout . '" id="layout-' . $layout . '"'
					. checked($sem_theme_options['active_layout'], $layout, false)
					. ' />' . '&nbsp;' . "\n"
				. '</span>'
				. $details['name']
				. '</label>'
				. '</h4>' . "\n";
			
			echo '</td>' . "\n";
		}
		
		while ( $i % $row_size ) {
			$classes = array('available_option');
			if ( ceil(( $i + 1 ) / $row_size) == 1 )
				$classes[] = 'top';
			if ( ceil(( $i + 1 ) / $row_size) == $num_rows )
				$classes[] = 'bottom';
			if ( !( $i % $row_size ) )
				$classes[] = 'left';
			elseif ( !( ( $i + 1 ) % $row_size ) )
				$classes[] = 'right';
			
			$i++;
			
			echo '<td class="' . implode(' ', $classes) . '">&nbsp;</td>' . "\n";
		}
		
		echo '</tr>' . "\n";
		
		echo '</table>' . "\n";
		
		echo '<p class="submit hide-if-js">'
			. '<input type="submit" value="' . esc_attr(__('Save Changes', 'sem-pinnacle')) . '" />'
			. '</p>' . "\n";
		
		echo '</form>' . "\n";
		echo '</div>' . "\n";
	} # edit_options()
	
	
	/**
	 * get_layouts()
	 *
	 * @return array $layout_options
	 **/

	static function get_layouts() {
		return array(
			'mts' => array(
				'name' => __('Content, Wide Sidebar', 'sem-pinnacle'),
				'wrapper' => __('950px', 'sem-pinnacle'),
				'content' => __('550px (490px net)', 'sem-pinnacle'),
				'wide_sidebars' => __('400px (370px net)', 'sem-pinnacle'),
				'sidebars' => __('2 x 200px (170px net)', 'sem-pinnacle'),
				'inline_boxes' => __('3 x 317px (287px net)', 'sem-pinnacle'),
				),
			'tsm' => array(
				'name' => __('Wide Sidebar, Content', 'sem-pinnacle'),
				'wrapper' => __('950px', 'sem-pinnacle'),
				'content' => __('550px (490px net)', 'sem-pinnacle'),
				'wide_sidebars' => __('400px (370px net)', 'sem-pinnacle'),
				'sidebars' => __('2 x 200px (170px net)', 'sem-pinnacle'),
				'inline_boxes' => __('3 x 317px (287px net)', 'sem-pinnacle'),
				),
			'sms' => array(
				'name' => __('Sidebar, Content, Sidebar', 'sem-pinnacle'),
				'wrapper' => __('950px', 'sem-pinnacle'),
				'content' => __('550px (490px net)', 'sem-pinnacle'),
				'wide_sidebars' => __('Not Available', 'sem-pinnacle'),
				'sidebars' => __('2 x 200px (170px net)', 'sem-pinnacle'),
				'inline_boxes' => __('3 x 317px (287px net)', 'sem-pinnacle'),
				),
			'mms' => array(
				'name' => __('Wide Content, Sidebar', 'sem-pinnacle'),
				'wrapper' => __('950px', 'sem-pinnacle'),
				'content' => __('650px (590px net)', 'sem-pinnacle'),
				'wide_sidebars' => __('Not Available', 'sem-pinnacle'),
				'sidebars' => __('1 x 300px (270px net)', 'sem-pinnacle'),
				'inline_boxes' => __('3 x 317px (287px net)', 'sem-pinnacle'),
				),
			'smm' => array(
				'name' => __('Sidebar, Wide Content', 'sem-pinnacle'),
				'wrapper' => __('950px', 'sem-pinnacle'),
				'content' => __('650px (590px net)', 'sem-pinnacle'),
				'wide_sidebars' => __('Not Available', 'sem-pinnacle'),
				'sidebars' => __('1 x 300px (270px net)', 'sem-pinnacle'),
				'inline_boxes' => __('3 x 317px (287px net)', 'sem-pinnacle'),
				),
			'ms' => array(
				'name' => __('Content, Sidebar', 'sem-pinnacle'),
				'wrapper' => __('750px', 'sem-pinnacle'),
				'content' => __('550px (490px net)', 'sem-pinnacle'),
				'wide_sidebars' => __('Not Available', 'sem-pinnacle'),
				'sidebars' => __('1 x 200px (170px net)', 'sem-pinnacle'),
				'inline_boxes' => __('2 x 375px (345px net)', 'sem-pinnacle'),
				),
			'sm' => array(
				'name' => __('Sidebar, Content', 'sem-pinnacle'),
				'wrapper' => __('750px', 'sem-pinnacle'),
				'content' => __('550px (490px net)', 'sem-pinnacle'),
				'wide_sidebars' => __('Not Available', 'sem-pinnacle'),
				'sidebars' => __('1 x 200px (170px net)', 'sem-pinnacle'),
				'inline_boxes' => __('2 x 375px (345px net)', 'sem-pinnacle'),
				),
			'm' => array(
				'name' => __('Narrow Content', 'sem-pinnacle'),
				'wrapper' => __('620px', 'sem-pinnacle'),
				'content' => __('620px (560px net)', 'sem-pinnacle'),
				'wide_sidebars' => __('Not Available', 'sem-pinnacle'),
				'sidebars' => __('Not Available', 'sem-pinnacle'),
				'inline_boxes' => __('2 x 310px (290px net)', 'sem-pinnacle'),
				),
			'mm' => array(
				'name' => __('Wide Content', 'sem-pinnacle'),
				'wrapper' => __('750px', 'sem-pinnacle'),
				'content' => __('750px (690px net)', 'sem-pinnacle'),
				'wide_sidebars' => __('Not Available', 'sem-pinnacle'),
				'sidebars' => __('Not Available', 'sem-pinnacle'),
				'inline_boxes' => __('2 x 375px (345px net)', 'sem-pinnacle'),
				),
			'mmm' => array(
				'name' => __('Extra Wide Content', 'sem-pinnacle'),
				'wrapper' => __('950px', 'sem-pinnacle'),
				'content' => __('950px (890px net)', 'sem-pinnacle'),
				'wide_sidebars' => __('Not Available', 'sem-pinnacle'),
				'sidebars' => __('Not Available', 'sem-pinnacle'),
				'inline_boxes' => __('3 x 317px (287px net)', 'sem-pinnacle'),
				),
			);
	} # get_layouts()
} # sem_layout

//$sem_layout = new sem_layout();
sem_layout::get_instance();