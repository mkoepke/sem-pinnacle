<?php
/**
 * footer
 *
 * @package Semiologic Reloaded
 **/

if ( !class_exists('sem_nav_menu') )
	include sem_path . '/inc/widgets-navmenu.php';

class footer extends sem_nav_menu {
	/**
	 * Constructor.
	 *
	 */
	public function __construct() {
		$widget_name = __('Footer: Nav Menu', 'sem-pinnacle');
		$widget_ops = array(
			'classname' => 'footer',
			'description' => __('The footer\'s navigation menu, with an optional copyright notice. Must be placed in the footer area.', 'sem-pinnacle'),
			);
		$control_ops = array(
			'width' => 330,
			);

		$this->ul_menu_class = "footer_menu";

		$this->use_menu_exclusion = false;

		parent::__construct('footer', $widget_name, $widget_ops, $control_ops);
	} # footer()


	/**
	 * widget()
	 *
	 * @param array $args widget args
	 * @param array $instance widget options
	 * @return void
	 **/

	function widget($args, $instance) {
		if ( $args['id'] != 'the_footer' && $args['id'] !='the_footer_boxes' )
			return;

		$instance = wp_parse_args($instance, footer::defaults());
		extract($args, EXTR_SKIP);
		extract($instance, EXTR_SKIP);

		$footer_class = '';
		if ( $sep )
			$footer_class .= ' sep_nav';
		if ( $float_footer && $copyright ) {
			$footer_class .= ' float_nav';
			if ( $sep )
				$footer_class .= ' float_sep_nav';
		}

		echo '<div id="footer" class="footer_section ' . $footer_class . '" role="contentinfo" itemscope="itemscope" itemtype="http://schema.org/WPFooter">' . "\n";

		echo '<nav id="footer_nav" class="footer_nav inline_menu menu" role="navigation" itemscope="itemscope" itemtype="http://schema.org/SiteNavigationElement">';

		sem_nav_menu::widget($args, $instance);

		echo '</nav><!-- footer_nav -->' . "\n";

		$year = date('Y');
		$site_name = strip_tags(get_option('blogname'));

		$copyright = sprintf($copyright, $site_name, $year);

		if ( $copyright ) {
			echo '<div id="copyright_notice">';
			echo $copyright;
			echo '</div><!-- #copyright_notice -->' . "\n";
		}

		echo '<div class="spacer"></div>' . "\n";

		echo '</div><!-- footer -->' . "\n";

		global $did_footer;
		$did_footer = true;
	} # widget()


	/**
	 * update()
	 *
	 * @param array $new_instance new widget options
	 * @param array $old_instance old widget options
	 * @return array $instance
	 **/

	function update($new_instance, $old_instance) {
		$instance = parent::update($new_instance, $old_instance);
		$instance['float_footer'] = isset($new_instance['float_footer']);
		if ( current_user_can('unfiltered_html') ) {
			$instance['copyright'] = trim($new_instance['copyright']);
		} else {
			$instance['copyright'] = $old_instance['copyright'];
		}

		return $instance;
	} # update()


	/**
	 * form()
	 *
	 * @param array $instance widget options
	 * @return void
	 **/

	function form($instance) {
		$defaults = footer::defaults();
		$instance = wp_parse_args($instance, $defaults);
		extract($instance, EXTR_SKIP);

		echo '<h3>' . __('Captions', 'sem-pinnacle') . '</h3>' . "\n";

		foreach ( array('copyright') as $field ) {
			echo '<p>'
				. '<label for="' . $this->get_field_id($field) . '">'
				. '<code>' . htmlspecialchars($defaults[$field], ENT_QUOTES, get_option('blog_charset')) . '</code>'
				. ( isset($defaults[$field . '_label'])
					? '<br />' . "\n" . '<code>' . $defaults[$field . '_label'] . '</code>'
					: ''
					)
				. '</label>'
				. '<br />' . "\n"
				. '<textarea class="widefat" cols="20" rows="4"'
				. ' id="' . $this->get_field_id($field) . '"'
				. ' name="' . $this->get_field_name($field) . '"'
				. ( !current_user_can('unfiltered_html')
					? ' disabled="disabled"'
					: ''
					)
				. ' >'
				. esc_html($$field)
				. '</textarea>'
				. '</p>' . "\n";
		}

		echo '<h3>' . __('Config', 'sem-pinnacle') . '</h3>' . "\n";

		echo '<p>'
			. '<label>'
			. '<input type="checkbox"'
				. ' name="' . $this->get_field_name('float_footer') . '"'
				. checked($float_footer, true, false)
				. ' />'
			. '&nbsp;'
			. __('Place the footer navigation menu and the copyright on a single line.', 'sem-pinnacle')
			. '</label>'
			. '</p>' . "\n";

		parent::form($instance);
	} # form()


	/**
	 * defaults()
	 *
	 * @return array $defaults
	 **/

	function defaults() {
		return array_merge(array(
			'copyright' => __('Copyright %1$s, %2$s', 'sem-pinnacle'),
			'copyright_label' => __('%1$s - Site name, %2$s - Year', 'sem-pinnacle'),
			'float_footer' => false,
			), parent::defaults());
	} # defaults()


	/**
	 * default_items()
	 *
	 * @return array $items
	 **/

	function default_items() {
		$items = array(array('type' => 'home'));

		$roots = wp_cache_get(0, 'page_children');

		if ( !$roots )
			return $items;

		$front_page_id = get_option('show_on_front') == 'page'
			? (int) get_option('page_on_front')
			: 0;

		foreach ( $roots as $root_id ) {
			if ( $root_id == $front_page_id )
				continue;
			if ( get_post_meta($root_id, '_widgets_exclude', true) )
				continue;
			if (  $this->use_menu_exclusion && get_post_meta($root_id, '_menu_exclude', true) )
				continue;
			if ( wp_cache_get($root_id, 'page_children') ) # only non-sections
				continue;

			$items[] = array(
				'type' => 'page',
				'ref' => $root_id,
				);
		}

		return $items;
	} # default_items()
} # footer

/**
 * footer_boxes
 *
 * @package Semiologic Reloaded
 **/

class footer_boxes extends WP_Widget {
	/**
	 * Footer Boxes number
	 *
	 * We allow for 2 bars now
	 *
	 */
	protected $bar_num = 1;

	/**
	 * Constructor.
	 *
	 */
	public function __construct() {
		$classname = 'footer_boxes';
		$classname .= ( $this->bar_num > 1 ) ? '_' . $this->bar_num : '';

		$widget_name = __('Footer: Boxes Bar', 'sem-pinnacle');
		$widget_name .= ( $this->bar_num > 1 ) ? ' ' . $this->bar_num : '';
		$widget_ops = array(
			'classname' => $classname,
			'description' => __('Lets you decide where the Footer Boxes Bar panel goes. It must be placed in the footer area.', 'sem-pinnacle'),
			);

		parent::__construct($classname, $widget_name, $widget_ops);
	} # footer_boxes()


	/**
	 * widget()
	 *
	 * @param array $args widget args
	 * @param array $instance widget options
	 * @return void
	 **/

	function widget($args, $instance) {
		if ( $args['id'] != 'the_footer' )
			return;

		global $in_footer_boxes_panel;
		$in_footer_boxes_panel = true;

		$panel_name = 'the_footer_boxes';
		$panel_name .= ( $this->bar_num > 1 ) ? '-' . $this->bar_num : '';
		sem_panels::display( $panel_name );

		$in_footer_boxes_panel = false;
	} # widget()
} # footer_boxes


/**
 * footer_boxes
 *
 * @package Semiologic Reloaded
 **/

class footer_boxes_2 extends footer_boxes {

	/**
	 * Constructor.
	 *
	 */
	public function __construct() {
		$this->bar_num = 2;

		parent::__construct();
	} # footer_boxes_2()
} # footer_boxes_2