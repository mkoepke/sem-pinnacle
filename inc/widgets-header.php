<?php
/**
 * header
 *
 * @package Semiologic Pinnacle
 **/

if ( !class_exists('sem_nav_menu') )
	include sem_path . '/inc/widgets-navmenu.php';

class header extends WP_Widget {
	/**
	 * Constructor.
	 *
	 */
	public function __construct() {

		$widget_name = __('Header: Site Header', 'sem-pinnacle');
		$widget_ops = array(
			'classname' => 'header',
			'description' => __('The site\'s header. Must be placed in the Header Area panel.', 'sem-pinnacle'),
			);
		$control_ops = array(
			'width' => 330,
			);

		parent::__construct('header', $widget_name, $widget_ops, $control_ops);
	} # header()


	/**
	 * widget()
	 *
	 * @param array $args widget args
	 * @param array $instance widget options
	 * @return void
	 **/

	function widget($args, $instance) {
		if ( $args['id'] != 'the_header' )
			return;

		$instance = wp_parse_args($instance, header::defaults());
		extract($instance, EXTR_SKIP);

		$header = header::get();

		echo '<div id="header" class="header_container'
				. ( $invert_header
					? ' invert_header'
					: ''
					)
				. '"'
 			. ' title="'
				. esc_attr(get_option('blogname'))
				. ' &bull; '
				. esc_attr(get_option('blogdescription'))
				. '" '
                . 'role="banner"'
				. ' itemscope="itemscope" itemtype="http://schema.org/WPHeader">';

		echo "\n";

		echo '<div id="header_img">' . "\n";

		$title_class = ( $header ) ? ' hidden' : '';

		$tagline = '<h2 id="tagline" itemprop="description" class="tagline' . $title_class . '">'
			. get_option('blogdescription')
			. '</h2>' . "\n";

		$site_name = '<h1 id="sitename" itemprop="headline" class="sitename' . $title_class . '">'
			. '<a href="' . esc_url(user_trailingslashit(home_url())) . '" rel="home">' . get_option('blogname') . '</a>'
			. '</h1>' . "\n";

		if ( $invert_header ) {
			echo $site_name;
			echo $tagline;
		} else {
			echo $tagline;
			echo $site_name;
		}

		if ( $header ) {
			echo header::display($header);
		}
		echo '</div>' . "\n";

		echo '</div><!-- header -->' . "\n";

		global $did_header;
		$did_header = true;
	} # widget()


	/**
	 * display()
	 *
	 * @param string $header
	 * @return string $html
	 **/

	function display($header = null) {
		if ( !$header )
			$header = header::get();

		if ( !$header )
			return;

		echo header::display_header_image($header);

	} # display()

	/**
	 * display_header_image()
	 *
	 * @param string $header
	 * @return string html
	 */
	function display_header_image($header) {
		if (false === $header_size = wp_cache_get('sem_header', 'sem_header'))
			$header_size = @getimagesize(WP_CONTENT_DIR . $header);
		list($width, $height) = $header_size;

		$html = '<img src="' . WP_CONTENT_URL . $header . '"'
			. ' width="' . intval($width) . '"'
			. ' height="' . intval($height) . '"'
			. ' alt="'
				. esc_attr(get_option('blogname'))
				. ' &bull; '
				. esc_attr(get_option('blogdescription'))
				. '"'
			. ' />';

		$html = '<a'
		. ' href="' . esc_url(user_trailingslashit(home_url())) . '"'
		. ' title="'
			. esc_attr(get_option('blogname'))
			. ' &bull; '
			. esc_attr(get_option('blogdescription'))
			. '" rel="home"'
		. '>' . $html . '</a>';

		return $html;
	}

	/**
	 * display_image()
	 *
	 * @param string $header
	 * @return string $html
	 **/
	static function display_image($header = null) {
		if ( !$header )
			$header = header::get_header();

		if ( !$header )
			return;

		list($width, $height) = wp_cache_get('sem_header', 'sem_header');

		$header = esc_url(content_url() . $header);

		return '<img src="' . $header . '" height="' . $height . '" width="' . $width . '" alt="'
			. esc_attr(get_option('blogname'))
			. ' &bull; '
			. esc_attr(get_option('blogdescription'))
			. '" />';
	} # display_image()


    /**
     * letter()
     *
     *
     * @return void
     */

	static function letter() {
		$header = header::get();

		if ( !$header || $header != get_post_meta(get_the_ID(), '_sem_header', true) )
			return;

		echo header::display($header);
	} # letter()


	/**
	 * get_basedir()
	 *
	 * @return string $header_basedir
	 **/
	static function get_basedir() {
		static $header_basedir;

		if ( isset($header_basedir) )
			return $header_basedir;

		$header_basedir = '/header';
		if ( defined('SUBDOMAIN_INSTALL') && SUBDOMAIN_INSTALL )
			$header_basedir .= '/' . $_SERVER['HTTP_HOST'];
		if ( function_exists('is_multisite') && is_multisite() ) {
			$home_path = parse_url(home_url());
			$home_path = isset($home_path['path']) ? rtrim($home_path['path'], '/') : '';
			$header_basedir .= $home_path;
		}

		return $header_basedir;
	}


	/**
	 * get()
	 *
	 * @return string $header
	 **/

	static function get() {
		static $header;

		if ( !is_admin() && isset($header) )
			return $header;

		global $sem_theme_options;

		# try post specific header
		if ( is_singular() ) {
			global $wp_the_query;
			$post_ID = intval($wp_the_query->get_queried_object_id());
		} else {
			$post_ID = false;
		}

		# try cached header
		if ( !is_admin() && !sem_header_cache_debug ) {
			switch ( is_singular() ) {
			case true:
				$header = get_post_meta($post_ID, '_sem_header', true);
				if ( !$header ) {
					$header = false;
					break;
				} elseif ( $header != 'default' ) {
					break;
				}
			default:
				$header = get_transient('sem_header');
			}
		} else {
			$header = false;
		}

		if ( !empty($header) ) {
			$header_size = @getimagesize(WP_CONTENT_DIR . $header);
			if ( $header_size ) {
				wp_cache_set('sem_header', $header_size, 'sem_header');
				return $header;
			}
		}

		$header_basedir = header::get_basedir();

		if ( defined('GLOB_BRACE') ) {
			$header_scan = "header{,-*}.{jpg,jpeg,png,gif}";
			$skin_scan = "header.{jpg,jpeg,png,gif}";
			$scan_type = GLOB_BRACE;
		} else {
			$header_scan = "header-*.{jpg,jpeg}";
			$skin_scan = "header.{jpg,jpeg}";
			$scan_type = false;
		}

		if ( is_singular() ) {
			# entry-specific header
			$header = glob(WP_CONTENT_DIR . "$header_basedir/$post_ID/$header_scan", $scan_type);
			if ( $header ) {
				$header = current($header);
				$header = str_replace(WP_CONTENT_DIR, '', $header);
				$header_size = @getimagesize(WP_CONTENT_DIR . $header);
				if ( $header_size ) {
					if ( get_post_meta($post_ID, '_sem_header', true) != $header )
						update_post_meta($post_ID, '_sem_header', $header);
					wp_cache_set('sem_header', $header_size, 'sem_header');
					return $header;
				}
			}
		}

		switch ( true ) {
		default:
			# uploaded header
			$header = glob(WP_CONTENT_DIR . "$header_basedir/$header_scan", $scan_type);
			if ( $header )
				break;

			# skin-specific header
			$active_skin = $sem_theme_options['active_skin'];
			$header = glob(sem_path . "/skins/$active_skin/$skin_scan", $scan_type);
			if ( $header )
				break;

			# no header
			$header = false;
			break;
		}

		if ( is_singular() && get_post_meta($post_ID, '_sem_header', true) != 'default' )
			update_post_meta($post_ID, '_sem_header', 'default');

		if ( $header ) {
			$header = current($header);
			$header = str_replace(WP_CONTENT_DIR, '', $header);
			$header_size = @getimagesize(WP_CONTENT_DIR . $header);
			if ( false !== $header_size ) {
				wp_cache_set('sem_header', $header_size, 'sem_header');
				set_transient('sem_header', $header);
				return $header;
			}
		}

		set_transient('sem_header', '0');
		return false;
	} # get()


	/**
	 * update()
	 *
	 * @param array $new_instance new widget options
	 * @param array $old_instance old widget options
	 * @return array $instance
	 **/

	function update($new_instance, $old_instance) {
		$instance['invert_header'] = isset($new_instance['invert_header']);

		return $instance;
	} # update()


	/**
	 * form()
	 *
	 * @param array $instance widget options
	 * @return void
	 **/

	function form($instance) {
		$defaults = header::defaults();
		$instance = wp_parse_args($instance, $defaults);
		extract($instance, EXTR_SKIP);

		echo '<h3>' . __('Config', 'sem-pinnacle') . '</h3>' . "\n";

		echo '<p>'
			. '<label>'
			. '<input type="checkbox"'
				. ' name="' . $this->get_field_name('invert_header') . '"'
				. checked($invert_header, true, false)
				. ' />'
			. '&nbsp;'
			. __('Output the site\'s name before the tagline.', 'sem-pinnacle')
			. '</label>'
			. '</p>' . "\n";
	} # form()


	/**
	 * defaults()
	 *
	 * @return array $defaults
	 **/

	function defaults() {
		return array(
			'invert_header' => false,
			);
	} # defaults()
} # header



class navbar extends sem_nav_menu {
	/**
	 * Constructor.
	 *
	 */
	public function __construct() {
		$widget_name = __('Header: Nav Menu', 'sem-pinnacle');
		$widget_ops = array(
			'classname' => 'navbar',
			'description' => __('The header\'s navigation menu, with an optional search form. Must be placed in the Header Area panel.', 'sem-pinnacle'),
			);
		$control_ops = array(
			'width' => 330,
			);

		$this->multi_level = true;

		$this->ul_menu_class = "header_menu";

		parent::__construct('navbar', $widget_name, $widget_ops, $control_ops);
	} # navbar()


	/**
	 * widget()
	 *
	 * @param array $args widget args
	 * @param array $instance widget options
	 * @return void
	 **/

	function widget($args, $instance) {
		if ( ! in_array( $args['id'], array( 'the_header', 'the_header_boxes', 'header_section-1',
			'header_section-2', 'header_section-3', 'header_section-4') ) )
			return;

		$instance = wp_parse_args($instance, navbar::defaults());
		extract($args, EXTR_SKIP);
		extract($instance, EXTR_SKIP);

		$navbar_class = '';
		if ( $show_search_form )
			$navbar_class .= ' float_nav';
		if ( $sep )
			$navbar_class .= ' sep_nav';

		echo '<nav id="navbar" class="header_container navbar' . $navbar_class . '">' . "\n";

		echo '<div id="header-menu-icon">' . $resp_menubar_text . '<span class="fa fa-bars"></span></div>';
		echo '<div id="header_nav" class="header_nav inline_menu menu" role="navigation" itemscope="itemscope" itemtype="http://schema.org/SiteNavigationElement">';

		parent::widget($args, $instance);

		echo '</div><!-- header_nav -->' . "\n";

		if ( $show_search_form ) {
			echo '<div id="search_form" class="search_form">';

			if ( is_search() )
				$search = apply_filters('the_search_form', get_search_query());
			else
				$search = $search_field;

			$search_caption = addslashes(esc_attr($search_field));
			if ( $search_caption ) {
				$onfocusblur = ' onfocus="if ( this.value == \'' . $search_caption . '\' )'
							. ' this.value = \'\';"'
						. ' onblur="if ( this.value == \'\' )'
						 	. ' this.value = \'' . $search_caption . '\';"';
			} else {
				$onfocusblur = '';
			}

			$go = $search_button;

			if ( $go !== '' )
				$go = '<input type="submit" id="go" class="go button submit" value="' . esc_attr($go) . '" />';

			echo '<form method="get"'
					. ' action="' . esc_url(user_trailingslashit(home_url())) . '"'
					. ' id="searchform" name="searchform"'
					. ' role="search"'
					. '>'
				. '&nbsp;'				# force line-height
				. '<input type="text" id="s" class="s" name="s"'
					. ' value="' . esc_attr($search) . '"'
					. $onfocusblur
					. ' />'
				. $go
				. '</form>';

			echo '</div><!-- search_form -->';
		}

		echo '<div class="spacer"></div>' . "\n";

		echo '</nav><!-- navbar -->' . "\n";

		global $did_navbar;
		$did_navbar = true;
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
		$instance['show_search_form'] = isset($new_instance['show_search_form']);
		$instance['search_field'] = trim(strip_tags($new_instance['search_field']));
		$instance['search_button'] = trim(strip_tags($new_instance['search_button']));
		$instance['resp_menubar_text'] = trim(strip_tags($new_instance['resp_menubar_text']));

		return $instance;
	} # update()


	/**
	 * form()
	 *
	 * @param array $instance widget options
	 * @return void
	 **/

	function form($instance) {
		$defaults = navbar::defaults();
		$instance = wp_parse_args($instance, $defaults);
		extract($instance, EXTR_SKIP);

		echo '<h3>' . __('Captions', 'sem-pinnacle') . '</h3>' . "\n";

		foreach ( array('search_field', 'search_button', 'resp_menubar_text') as $field ) {
			echo '<p>'
				. '<label>'
				. '<code>' . $defaults[$field] . '</code>'
				. '<br />' . "\n"
				. '<input type="text" class="widefat"'
					. ' name="' . $this->get_field_name($field) . '"'
					. ' value="' . esc_attr($$field) . '"'
					. ' />'
				. '</label>'
				. '</p>' . "\n";
		}

		echo '<h3>' . __('Config', 'sem-pinnacle') . '</h3>' . "\n";

		echo '<p>'
			. '<label>'
			. '<input type="checkbox"'
				. ' name="' . $this->get_field_name('show_search_form') . '"'
				. checked($show_search_form, true, false)
				. ' />'
			. '&nbsp;'
			. __('Show a search form in the navigation menu.', 'sem-pinnacle')
			. '</label>'
			. '</p>' . "\n";

		echo '<p>'
			. '<label>'
			. '<input type="checkbox"'
				. ' name="' . $this->get_field_name('display_justified') . '"'
				. checked($display_justified, true, false)
				. ' />'
			. '&nbsp;'
			. __('Display top level menu items justified.', 'sem-pinnacle')
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
			'search_field' => __('Search', 'sem-pinnacle'),
			'search_button' => __('Go', 'sem-pinnacle'),
			'resp_menubar_text' => __('Menu', 'sem-pinnacle'),
			'show_search_form' => false,
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
			if ( (int) get_post_meta($root_id, '_widgets_exclude', true) )
				continue;
			if ( (int) get_post_meta($root_id, '_menu_exclude', true) )
				continue;
//			if ( !wp_cache_get($root_id, 'page_children') ) # only sections
//				continue;

			$items[] = array(
				'type' => 'page',
				'ref' => $root_id,
				);
		}

		return $items;
	} # default_items()
} # navbar

/**
 * header_boxes
 *
 * @package Semiologic Reloaded
 **/

class header_boxes extends WP_Widget {
	/**
	 * Constructor.
	 *
	 */
	public function __construct() {
		$widget_name = __('Header: Boxes Bar', 'sem-pinnacle');
		$widget_ops = array(
			'classname' => 'header_boxes',
			'description' => __('Lets you decide where the Header Boxes Bar panel goes. Must be placed within the Header Area panel.', 'sem-pinnacle'),
			);

		parent::__construct('header_boxes', $widget_name, $widget_ops);
	} # header_boxes()


	/**
	 * widget()
	 *
	 * @param array $args widget args
	 * @param array $instance widget options
	 * @return void
	 **/

	function widget($args, $instance) {
		if ( $args['id'] != 'the_header' )
			return;

		global $in_header_boxes_panel;
		$in_header_boxes_panel = true;

		sem_panels::display('the_header_boxes');

		$in_header_boxes_panel = false;
	} # widget()
} # header_boxes

/**
 * header_section
 *
 * @package Semiologic Pinnacle
 **/

class header_section extends WP_Widget {
	/**
	 * Header Section number
	 *
	 * We allow for 4 sections
	 *
	 */
	protected $bar_num = 1;
	/**
	 * Constructor.
	 *
	 */
	public function __construct() {
		$classname = 'header_section';
		$classname .= '_' . $this->bar_num;

		$widget_name = __('Header: Header Section', 'sem-pinnacle');
		$widget_name .= ' ' . $this->bar_num;
		$widget_ops = array(
			'classname' => $classname,
			'description' => __('Lets you decide where the Header Section panel goes. Must be placed within the Header Area panel.', 'sem-pinnacle'),
			);

		parent::__construct($classname, $widget_name, $widget_ops);
	} # header_section()


	/**
	 * widget()
	 *
	 * @param array $args widget args
	 * @param array $instance widget options
	 * @return void
	 **/

	function widget($args, $instance) {
		if ( $args['id'] != 'the_header' )
			return;

		global $in_header_section_panel;
		$in_header_section_panel = true;

		$panel_name = 'header_section';
		$panel_name .= '-' . $this->bar_num;
		sem_panels::display( $panel_name );

		$in_header_section_panel = false;
	} # widget()
} # header_section


/**
 * header_section_2
 *
 * @package Semiologic Pinnacle
 **/

class header_section_2 extends header_section {

	/**
	 * Constructor.
	 *
	 */
	public function __construct() {
		$this->bar_num = 2;

		parent::__construct();
	} # header_section_2()
} # header_section_2


/**
 * header_section_3
 *
 * @package Semiologic Pinnacle
 **/

class header_section_3 extends header_section {

	/**
	 * Constructor.
	 *
	 */
	public function __construct() {
		$this->bar_num = 3;

		parent::__construct();
	} # header_section_3()
} # header_section_3

/**
 * header_section_4
 *
 * @package Semiologic Pinnacle
 **/

class header_section_4 extends header_section {

	/**
	 * Constructor.
	 *
	 */
	public function __construct() {
		$this->bar_num = 4;

		parent::__construct();
	} # header_section_4()
} # header_section_4