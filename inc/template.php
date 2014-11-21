<?php
/**
 * sem_template
 *
 * @package Semiologic Reloaded
 **/

class sem_template {
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
        if ( !is_admin() ) {
        	add_action('wp', array($this, 'wp'), 0);
        	add_action('template_redirect', array($this, 'template_redirect'), 0);
        	add_action('wp_enqueue_scripts', array($this, 'scripts'), 11);
        	add_action('wp_enqueue_scripts', array($this, 'styles'), 10);
        	add_action('wp_head', array($this, 'trackback_rdf'), 100);
	        add_action('wp_head', array($this, 'wp_head'), 20);
        	add_filter('body_class', array($this, 'body_class'));
        	add_filter('widget_title', array($this, 'widget_title'));
        	add_action('wp_footer', array($this, 'display_credits'), 5);
        	add_filter('query_string', array($this, 'archive_query_string'), 20);
        	add_filter('dynamic_sidebar_params', array($this, 'the_header_sidebar_params'), 15);
        	add_filter('dynamic_sidebar_params', array($this, 'the_footer_sidebar_params'), 15);
        	remove_action('wp_print_styles', array($this, 'styles'), 5);
	        add_filter( 'wp_title', array($this, 'sem_wp_title'), 10, 2);

        } else {
        	add_action('admin_menu', array($this, 'admin_menu'));
        	add_action('admin_menu', array($this, 'meta_boxes'));
	        add_action('admin_enqueue_scripts', array($this, 'admin_scripts'));
//	        add_action('admin_menu', array($this, 'add_editor_styles'));
//	        add_filter('tiny_mce_before_init', array($this, 'add_editor_body_class'));
        }
    }

    /**
	 * admin_menu()
	 *
	 * @return void
	 **/

	function admin_menu() {

		$sem_menu = add_menu_page(
			__('Semiologic', 'sem-pinnacle'),
			__('Semiologic', 'sem-pinnacle'),
			'edit_theme_options',
			'sem_menu',
			'',
			'dashicons-welcome-widgets-menus',
			59
		);

		add_submenu_page(
			'sem_menu',
			__('Manage Header', 'sem-pinnacle'),
			__('Header', 'sem-pinnacle'),
			'edit_theme_options',
			'sem_menu',
			array('sem_header', 'edit_options')
			);
		add_submenu_page(
			'sem_menu',
			__('Manage Layout', 'sem-pinnacle'),
			__('Layout', 'sem-pinnacle'),
			'edit_theme_options',
			'layout',
			array('sem_layout', 'edit_options')
			);
		add_submenu_page(
			'sem_menu',
			__('Manage Skin', 'sem-pinnacle'),
			__('Skin', 'sem-pinnacle'),
			'edit_theme_options',
			'skin',
			array('sem_skin', 'edit_options')
			);
		add_submenu_page(
			'sem_menu',
			__('Manage Font', 'sem-pinnacle'),
			__('Font', 'sem-pinnacle'),
			'edit_theme_options',
			'font',
			array('sem_font', 'edit_options')
			);
		if ( !( function_exists('is_multisite') && is_multisite() ) ) {
			add_submenu_page(
				'sem_menu',
				__('Manage Custom', 'sem-pinnacle'),
				__('Custom CSS', 'sem-pinnacle'),
				'edit_theme_options',
				'custom',
				array('sem_custom', 'edit_options')
				);
		}
	} # admin_menu()


	/**
	 * meta_boxes()
	 *
	 * @return void
	 **/

	function meta_boxes() {
		if ( current_user_can('switch_themes') ) {
			add_meta_box('sem_header', __('Post-Specific Header', 'sem-pinnacle'), array('sem_header', 'edit_entry'), 'post');
			add_meta_box('sem_header', __('Page-Specific Header', 'sem-pinnacle'), array('sem_header', 'edit_entry'), 'page');
		}
	} # meta_boxes()


	/**
	 * body_class()
	 *
	 * @param array $classes
	 * @return array $classes
	 **/

	function body_class($classes) {
		global $sem_theme_options;

		$active_layout = apply_filters('active_layout', $sem_theme_options['active_layout']);

		$classes[] = $active_layout;

		if ( $active_layout != 'letter' ) {
			$extra_layout = str_replace(array('s', 't'), 'm', $active_layout);

			if ( $extra_layout != $active_layout) {
				$classes[] = $extra_layout;
				$classes[] = str_replace(array('s', 't'), '', $active_layout)
					. ( substr_count(str_replace('t', 's', $active_layout), 's')) . 's';
			}
		}

		$classes[] = preg_replace("/[^a-z]+/", '_', $sem_theme_options['active_skin']);

		if ( $sem_theme_options['active_font'] )
			$classes[] = preg_replace("/[^a-z]+/", '_', $sem_theme_options['active_font']);

		if ( is_page() ) {
			global $wp_the_query;

			$template = get_post_meta(intval($wp_the_query->get_queried_object_id()), '_wp_page_template', true);

			if ( $template != 'default' ) {
				$template = preg_replace("/\.[^\.]+$/", "", $template);

				$classes[] = $template;
			}
		}

        if ( is_singular() ) {
            global $post;
            if ( isset( $post ) ) {
                $classes[] = $post->post_type . '-' . $post->post_name;
            }
        }

		return $classes;
	} # body_class()


	/**
	 * scripts()
	 *
	 * @return void
	 **/

	function scripts() {
		wp_enqueue_script('jquery');

		global $wp_the_query;
		if ( is_singular() && comments_open($wp_the_query->get_queried_object_id()) ) {
			wp_enqueue_script('comment-reply');
		}

		if(preg_match('/(?i)msie [1-8]/',$_SERVER['HTTP_USER_AGENT'])) {
			wp_enqueue_script( 'respond-js', sem_url . '/js/respond.min.js',  null, '1.4.2', false );
		}
		wp_enqueue_script( 'sem-helper', sem_url . '/js/sem-helper.js', array('jquery'), '1.0.0', false );

		if ( is_admin() ) {
			if( !isset($_GET['page']) || ( isset($_GET['page']) && strstr($_GET['page'], 'widgetkit' ) === false ) ) {
				wp_enqueue_script('jquery-ui-tabs');
			}
		}

	} # scripts()

	/**
	 * admin_scripts()
	 *
	 * @return void
	 **/

	function admin_scripts() {
		// add workaround for widgetkit that chokes if we load the standard WP jquery-ui-tab
		if( !isset($_GET['page']) || ( isset($_GET['page']) && strstr($_GET['page'], 'widgetkit' ) === false ) ) {
			wp_enqueue_script('jquery-ui-tabs');
		}

	} # admin_scripts()


	/**
	 * styles()
	 *
	 * @return void
	 **/

	function styles() {
		global $sem_theme_options;
		global $sem_stock_skins;

		$custom_skin = !in_array( strtolower($sem_theme_options['active_skin']), $sem_stock_skins );
		$skin_path = $custom_skin ?  sem_content_path . '/skins/' . $sem_theme_options['active_skin']  : sem_path . '/skins/' . $sem_theme_options['active_skin'];
		$skin_url = $custom_skin ?  sem_content_url . '/skins/' . $sem_theme_options['active_skin'] : sem_url . '/skins/' . $sem_theme_options['active_skin'];

		wp_enqueue_style('style', sem_url . '/style.css', null, sem_last_mod);

		$this->fonts();

		if ( file_exists($skin_path . '/icons.css') )
			wp_enqueue_style('custom-icons', $skin_url . '/icons.css', null, filemtime($skin_path . '/icons.css'));
		else
			wp_enqueue_style('icons', sem_url . '/css/icons.css', null, sem_last_mod);

		if ( apply_filters('active_layout', $sem_theme_options['active_layout']) == 'letter' ) {
			wp_enqueue_style('letter', sem_url . '/css/letter.css', null, sem_last_mod);
			if ( file_exists($skin_path . '/letter.css') )
				wp_enqueue_style('custom-letter', $skin_url . '/letter.css', null, filemtime($skin_path . '/letter.css'));
		} else {
			wp_enqueue_style('skin', $skin_url . '/skin.css', null, sem_last_mod);
			wp_enqueue_style('responsive', sem_url . '/css/responsive.css', null, sem_last_mod);
			if ( file_exists($skin_path . '/custom.css') )
				wp_enqueue_style('custom-skin', $skin_url . '/custom.css', null, filemtime($skin_path . '/custom.css'));
		}

		if ( file_exists(sem_content_path . '/custom/custom.css') )
			wp_enqueue_style('custom-theme', sem_content_url . '/custom/custom.css', null, filemtime(sem_content_path . '/custom/custom.css'));

		if ( ( isset($_GET['action']) && $_GET['action'] == 'print' ) || is_singular() ) {
			wp_enqueue_style('print', sem_url . '/css/print.css', null, sem_last_mod, 'print');
			if ( file_exists($skin_path . '/print.css') )
				wp_enqueue_style('custom-print', $skin_url . '/print.css', null, filemtime($skin_path . '/print.css', 'print'));
		}


	} # styles()


	/**
	 * wp_head()
	 *
	 * @return void
	 **/

	function wp_head() {
echo <<<EOS
<!--[if gte IE 8]>
  <style type="text/css">
    .gradient {
       filter: none !important;
    }
  </style>
<![endif]-->

EOS;
	} # wp_head()

	/**
	 * fonts()
	 *
	 * @return void
	 **/
	function fonts() {
		global $sem_theme_options;

		echo '<link href="//maxcdn.bootstrapcdn.com/font-awesome/4.1.0/css/font-awesome.min.css" rel="stylesheet">' . "\n";

		sem_template::load_font( $sem_theme_options['active_font']);

		if ( isset($sem_theme_options['addl_fonts']))
			foreach( $sem_theme_options['addl_fonts'] as $font)
				if ( $sem_theme_options['active_font'] != $font )
					sem_template::load_font( $font );
	}

	/**
	 * load_font()
	 *
	 * @param $font
	 * @return void
	 */

	static function load_font( $font ) {
		switch ($font) {
			case 'lato':
				echo "<link href='http://fonts.googleapis.com/css?family=Lato:400,700,400italic,700italic' rel='stylesheet' type='text/css'>" . "\n";
				break;
			case 'lora':
				echo "<link href='http://fonts.googleapis.com/css?family=Lora:400,700,400italic,700italic' rel='stylesheet' type='text/css'>" . "\n";
				break;
			case 'merriweather':
				echo "<link href='http://fonts.googleapis.com/css?family=Merriweather:400,400italic,700,700italic' rel='stylesheet' type='text/css'>" . "\n";
				break;
			case 'open_sans':
				echo "<link href='http://fonts.googleapis.com/css?family=Open+Sans:400italic,400,700,700italic' rel='stylesheet' type='text/css'>" . "\n";
				break;
			case 'pt_sans':
				echo "<link href='http://fonts.googleapis.com/css?family=PT+Sans:400,700,700italic' rel='stylesheet' type='text/css'>" . "\n";
				break;
			case 'roboto':
				echo "<link href='http://fonts.googleapis.com/css?family=Roboto:400,400italic,700,700italic' rel='stylesheet' type='text/css'>" . "\n";
				break;
			case 'source_sans_pro':
				echo "<link href='http://fonts.googleapis.com/css?family=Source+Sans+Pro:400,700,400italic,700italic' rel='stylesheet' type='text/css'>" . "\n";
				break;
			case 'ubuntu':
				echo "<link href='http://fonts.googleapis.com/css?family=Ubuntu:400,700,400italic,700italic' rel='stylesheet' type='text/css'>" . "\n";
			default:
				break;
		}
	}

	/**
	 *
	 * sem_wp_title()
	 * Filter the page title.
	 *
	 *
	 * @param string $title Default title text for current view.
	 * @param string $sep   Optional separator.
	 * @return string The filtered title.
	 */
	function sem_wp_title( $title, $sep ) {
		global $paged, $page;

		if ( is_feed() )
			return $title;

		// Add the site name.
		$title .= get_bloginfo( 'name', 'display' );

		// Add the site description for the home/front page.
		$site_description = get_bloginfo( 'description', 'display' );
		if ( $site_description && ( is_home() || is_front_page() ) )
			$title = "$title $sep $site_description";

		// Add a page number if necessary.
		if ( ( $paged >= 2 || $page >= 2 ) && ! is_404() )
			$title = "$title $sep " . sprintf( __( 'Page %s', 'sem-pinnacle' ), max( $paged, $page ) );

		return $title;
	}

	/**
	 * strip_sidebars()
	 *
	 * @param string $layout
	 * @return string $layout
	 **/

	static function strip_sidebars($layout) {

		return str_replace(array('s', 't'), 'm', $layout);
	} # strip_sidebars()


	/**
	 * force_letter()
	 *
	 * @param string $layout
	 * @return string $layout
	 **/

	static function force_letter($layout) {
		global $content_width;
		$content_width = 620;

		return 'letter';
	} # force_letter()


	/**
	 * trackback_rdf()
	 *
	 * @return void
	 **/

	function trackback_rdf() {
		global $wp_the_query;
		if ( is_singular() && comments_open($wp_the_query->get_queried_object_id()) ) {
			echo '<!--' . "\n";
			trackback_rdf();
			echo "\n" . '-->' . "\n";
		}
	} # trackback_rdf()


	/**
	 * wp()
	 *
	 * @param object &$wp
	 * @return void
	 **/

	function wp(&$wp) {
		static $done = false;

		if ( $done )
			return;

		if ( is_attachment() ) {
			add_filter('option_blog_public', 'false');
			add_filter('comments_open', 'false');
			add_filter('pings_open', 'false');
		} elseif ( is_404() || is_search() ) {
			add_filter('option_blog_public', 'false');
		} elseif ( !is_admin() && !current_user_can('manage_options') ) {
			# avoid cap-related issues
			if ( preg_match("|://[^/]*[Ss]e[Mm]io[Ll]o[Gg]ic[^/]+\.|i", home_url()) )
				add_filter('option_blog_public', 'false', 1000);
		}

		if ( is_singular() ) {
			global $post;
			global $wp_the_query;
			$post = $wp_the_query->posts[0];
			setup_postdata($post);
		}

		$done = true;
	} # wp()


	/**
	 * template_redirect()
	 *
	 * @return void
	 **/

	function template_redirect() {
		if ( !isset($_GET['action']) || $_GET['action'] != 'print' )
			return;

		if ( has_filter('template_redirect', 'redirect_canonical') )
			redirect_canonical();

		add_filter('option_blog_public', 'false');
		add_filter('comments_open', 'false');
		add_filter('pings_open', 'false');
		add_filter('active_layout', array($this, 'strip_sidebars'));
		remove_action('wp_footer', array($this, 'display_credits'), 5);

		include_once sem_path . '/print.php';
		die;
	} # template_redirect()


	/**
	 * widget_title()
	 *
	 * @param string $title
	 * @return string $title
	 **/

	function widget_title($title) {
		return $title == '&nbsp;' ? '' : $title;
	} # widget_title()


	/**
	 * display_credits()
	 *
	 * @return void
	 **/

	static function display_credits() {
		global $sem_theme_options;

		echo '<div id="credits" class="wrapper">' . "\n";

		if ( $sem_theme_options['credits'] ) {
			$theme_credits = sem_template::get_theme_credits();
			$skin_credits = sem_template::get_skin_credits();

			$credits = sprintf($sem_theme_options['credits'], $theme_credits, $skin_credits['skin_name'], $skin_credits['skin_author']);

			if ( current_user_can('manage_options') ) {
				$credits .= ' - '
					. '<a href="' . esc_url(admin_url() . 'themes.php?page=skin#sem_credits') . '">'
					. __('Edit', 'sem-pinnacle')
					. '</a>';
			}

			echo $credits . "\n";
		}

		echo '</div><!-- credits -->' . "\n";
	} # display_credits()


	/**
	 * get_theme_credits()
	 *
	 * @return string $credits
	 **/

	static function get_theme_credits() {
		if ( get_option('sem_api_key') ) {
			return '<a href="http://www.getsemiologic.com">'
				. __('Semiologic Pro', 'sem-pinnacle')
				. '</a>';
		} else {
			$theme_descriptions = array(
				__('the <a href="http://www.semiologic.com/software/sem-reloaded/">Semiologic theme</a>', 'sem-pinnacle'),
				__('an <a href="http://www.semiologic.com/software/sem-reloaded/">easy to use WordPress theme</a>', 'sem-pinnacle'),
				__('an <a href="http://www.semiologic.com/software/sem-reloaded/">easy to customize WordPress theme</a>', 'sem-pinnacle'),
				);

			$i = rand(0, sizeof($theme_descriptions) - 1);

			return $theme_descriptions[$i];
		}
	} # get_theme_credits()


	/**
	 * get_skin_credits()
	 *
	 * @return array $credits
	 **/

	static function get_skin_credits() {
		global $sem_theme_options;
		global $sem_stock_skins;

		if ( !isset($sem_theme_options['skin_data']) || !is_array($sem_theme_options['skin_data']) ) {
			$custom_skin = !in_array( strtolower($sem_theme_options['active_skin']), $sem_stock_skins );
			$skin_path = $custom_skin ?  sem_content_path . '/skins/' . $sem_theme_options['active_skin']  : sem_path . '/skins/' . $sem_theme_options['active_skin'];
			$details = sem_template::get_skin_data( $sem_theme_options['active_skin'], $skin_path );
			$sem_theme_options['skin_data'] = $details;
			if ( !defined('sem_install_test') )
				update_option('sem6_options', $sem_theme_options);
		} else {
			$details = $sem_theme_options['skin_data'];
		}

		$name = $details['uri']
			? ( '<a href="' . esc_url($details['uri']) . '">'
				. $details['name']
				. '</a>' )
			: $details['name'];
		$author = $details['author_uri']
			? ( '<a href="' . esc_url($details['author_uri']) . '">'
				. $details['author_name']
				. '</a>' )
			: $details['author_name'];

		return array(
			'skin_name' => $name,
			'skin_author' => $author,
			);
	} # get_skin_credits()


    /**
     * get_skin_data()
     *
     * @param $skin_id
     * @return array $data
     */

	static function get_skin_data($skin_id, $skin_location = '' ) {
		$fields = array( 'name', 'uri', 'version', 'author_name', 'author_uri', 'description', 'tags', 'colors', 'type' );

		$allowed_tags = array(
			'a' => array(
				'href' => array(),'title' => array()
				),
			'abbr' => array(
				'title' => array()
				),
			'acronym' => array(
				'title' => array()
				),
			'code' => array(),
			'em' => array(),
			'strong' => array()
		);

		if ( empty( $skin_location ) )
			$skin_location = sem_path . '/skins';

		$fp = @fopen( trailingslashit( $skin_location ) . $skin_id . '/skin.css', 'r' );

		if ( !$fp ) {
			foreach ( $fields as $field )
				$$field = '';
			return compact($fields);
		}

		$skin_data = fread( $fp, 4096 );

		fclose($fp);

		$skin_data = str_replace("\r", "\n", $skin_data);

		preg_match('/Skin(?:\s+name)?\s*:(.*)/i', $skin_data, $name);
		preg_match('/Skin\s+ur[il]\s*:(.*)/i', $skin_data, $uri);
		preg_match('/Version\s*:(.*)/i', $skin_data, $version);
		preg_match('/Author\s*:(.*)/i', $skin_data, $author_name);
		preg_match('/Author\s+ur[il]\s*:(.*)/i', $skin_data, $author_uri);
		preg_match('/Description\s*:(.*)/i', $skin_data, $description);
		preg_match('/Tags\s*:(.*)/i', $skin_data, $tags);
		preg_match('/Colors\s*:(.*)/i', $skin_data, $colors);
		preg_match('/Type\s*:(.*)/i', $skin_data, $type);

		foreach ( $fields as $field ) {
			if ( !empty( ${$field} ) )
				${$field} = _cleanup_header_comment(${$field}[1]);
			else
				${$field} = '';

			switch ( $field ) {
			case 'uri':
			case 'author_uri':
				$$field = esc_url_raw($$field);
				break;
			case 'tags':
			case 'colors':
				$$field = strip_tags($$field);
				if ( $$field ) {
					$$field = explode(',', $$field);
					$$field = array_map('trim', $$field);
				} else {
					$$field = array();
				}
			case 'description':
				$$field = wp_kses($$field, $allowed_tags);
				break;
			default:
				$$field = strip_tags($$field);
				break;
			}
		}

		return compact($fields);
	} # get_skin_data()


	/**
	 * archive_query_string()
	 *
	 * @param array $query_string
	 * @return array $query_string
	 **/

	function archive_query_string($query_string) {
		parse_str($query_string, $qv);
		unset($qv['paged'], $qv['debug']);

		if ( empty($qv) )
			return $query_string;

		foreach ( array(
			'order',
			'pagename',
			'feed',
			'p',
			'page_id',
			'attachment_id',
			's',
			) as $bail ) {
			if ( !empty($qv[$bail]) )
				return $query_string;
		}

		global $wp_the_query;

		$wp_the_query->parse_query($query_string);

		if ( is_feed() || !is_date() )
			return $query_string;

		parse_str($query_string, $args);

		if ( !isset($args['order']) )
			$args['order'] = 'asc';

		$query_string = http_build_query($args);

		return $query_string;
	} # archive_query_string()


	/**
	 * the_header_sidebar_params()
	 *
	 * @param array $params
	 * @return array $params
	 **/

	function the_header_sidebar_params($params) {
		if ( !is_array($params) || !is_array($params[0]) || $params[0]['id'] != 'the_header' )
			return $params;

		global $did_header;
		global $did_navbar;
		global $did_top_widgets;
		global $did_middle_widgets;
		global $did_bottom_widgets;

		global $wp_registered_widgets;
		$widget_id = $params[0]['widget_id'];
		if ( is_array($wp_registered_widgets[$widget_id]['callback']) ) {
			$type = get_class($wp_registered_widgets[$widget_id]['callback'][0]);
			if ( is_a($wp_registered_widgets[$widget_id]['callback'][0], 'WP_Widget') ) {
				if ( !$did_header && !$did_navbar ) {
					if ( !$did_top_widgets ) {
						echo '<div id="header_top_wrapper" class="header_section"><div id="header_top">' . "\n";
						$did_top_widgets = true;
					}
				}
				$instance = $wp_registered_widgets[$widget_id]['callback'][0]->get_settings();
				$instance = isset($instance[$wp_registered_widgets[$widget_id]['callback'][0]->number]) ?
                    $instance[$wp_registered_widgets[$widget_id]['callback'][0]->number] : $instance;
				if ( apply_filters('widget_display_callback', $instance, $wp_registered_widgets[$widget_id]['callback'][0], $params) === false )
					return $params;

				if ( is_a($wp_registered_widgets[$widget_id]['callback'][0], 'header_boxes') ) {
					if ( !is_active_sidebar('the_header_boxes') )
						return $params;
				}
			}
		} else {
			$type = $wp_registered_widgets[$widget_id]['callback'];
		}

		switch ( $type ) {
		case 'header':
			if ( $did_navbar ) {
				if ( $did_middle_widgets ) {
					echo '</div></div>' . "\n";
				}
			} else {
				if ( $did_top_widgets ) {
					echo '</div></div>' . "\n";
				}
			}
			break;

		case 'navbar':
			if ( $did_header ) {
				if ( $did_middle_widgets ) {
					echo '</div></div>' . "\n";
				}
			} else {
				if ( $did_top_widgets ) {
					echo '</div></div>' . "\n";
				}
			}
			break;

		default:
			if ( !$did_header && !$did_navbar ) {
				if ( !$did_top_widgets ) {
					echo '<div id="header_top_wrapper" class="header_section"><div id="header_top">' . "\n";
					$did_top_widgets = true;
				}
			} elseif ( $did_header && $did_navbar ) {
				if ( !$did_bottom_widgets ) {
					echo '<div id="header_bottom_wrapper" class="header_section"><div id="header_bottom">' . "\n";
					$did_bottom_widgets = true;
				}
			} else {
				if ( !$did_middle_widgets ) {
					echo '<div id="header_middle_wrapper" class="header_section"><div id="header_middle">' . "\n";
					$did_middle_widgets = true;
				}
			}
			break;
		}

		return $params;
	} # the_header_sidebar_params()


	/**
	 * the_footer_sidebar_params()
	 *
	 * @param array $params
	 * @return array $params
	 **/

	function the_footer_sidebar_params($params) {
		if ( !is_array($params) || !is_array($params[0]) || ($params[0]['id'] != 'the_footer' && $params[0]['id'] != 'the_footer_boxes') )
			return $params;

		global $did_footer;
		global $did_top_widgets;
		global $did_bottom_widgets;

		global $wp_registered_widgets;
		$widget_id = $params[0]['widget_id'];
		if ( is_array($wp_registered_widgets[$widget_id]['callback']) ) {
			$type = get_class($wp_registered_widgets[$widget_id]['callback'][0]);
			if ( is_a($wp_registered_widgets[$widget_id]['callback'][0], 'WP_Widget') ) {
				if ( !$did_footer ) {
					if ( !$did_top_widgets ) {
						echo '<div id="footer_top_wrapper" class="footer_section"><div id="footer_top">' . "\n";
						$did_top_widgets = true;
					}
				}
				$instance = $wp_registered_widgets[$widget_id]['callback'][0]->get_settings();
				$instance = isset($instance[$wp_registered_widgets[$widget_id]['callback'][0]->number]) ?
                    $instance[$wp_registered_widgets[$widget_id]['callback'][0]->number] : $instance;
				if ( apply_filters('widget_display_callback', $instance, $wp_registered_widgets[$widget_id]['callback'][0], $params) === false )
					return $params;

				if ( is_a($wp_registered_widgets[$widget_id]['callback'][0], 'footer_boxes') ) {
					if ( !is_active_sidebar('the_footer_boxes') )
						return $params;
				}
			}
		} else {
			$type = $wp_registered_widgets[$widget_id]['callback'];
		}

		global $in_footer_boxes_panel;
		global $closed_top_widgets;

		if ( !$in_footer_boxes_panel ) {

			switch ( $type ) {
			case 'footer':
				if ( $did_top_widgets ) {
					echo '</div></div>' . "\n";
					$closed_top_widgets = true;
				}
				break;

			default:
				if ( !$did_footer ) {
					if ( !$did_top_widgets ) {
						echo '<div id="footer_top_wrapper" class="footer_section"><div id="footer_top">' . "\n";
						$did_top_widgets = true;
					}
				} else {
					if ( !$did_bottom_widgets ) {
						if ( !$closed_top_widgets ) {
							echo '</div></div>' . "\n";
							$closed_top_widgets = true;
						}
						echo '<div id="footer_bottom_wrapper" class="footer_section"><div id="footer_bottom">' . "\n";
						$did_bottom_widgets = true;
					}
				}
				break;
			}
		}

		return $params;
	} # the_footer_sidebar_params()


	/**
	 * custom_background_cb()
	 *
	 * @return void
	 **/
	static function custom_background_cb() {
		ob_start();
		_custom_background_cb();
		$o = ob_get_clean();
		$o = str_replace('body.custom-background', 'html', $o);
        if (strpos($o, 'background-image') === FALSE)
            $o = str_replace('background-color', 'background', $o);
        echo $o;
	} # custom_background_cb()


	/**
	 * add_editor_styles()
	 *
	 * @return void
	 **/

	function add_editor_styles() {
		if ( ! is_admin() )
			return;

		global $sem_theme_options;
		$skin_path = sem_path . '/skins/' . $sem_theme_options['active_skin'];
		$skin_url = sem_url . '/skins/' . $sem_theme_options['active_skin'];

		// call WP function to add the theme's style.css.  Make theme check happy
		add_editor_style( sem_url . '/style.css' );

		// now add the additional style sheets we use.  Add directly to WP global array.  No other way
		$stylesheets    = array();

		$stylesheets[] = sem_url . '/css/layout.css';

		if ( file_exists($skin_path . '/icons.css') )
			$stylesheets[] =  $skin_url . '/icons.css';
		else
			$stylesheets[] = sem_url . '/css/icons.css';

		if ( apply_filters('active_layout', $sem_theme_options['active_layout']) == 'letter' ) {
			$stylesheets[] =  sem_url . '/css/letter.css';
			if ( file_exists($skin_path . '/letter.css') )
				$stylesheets[] =  $skin_url . '/letter.css';
		} else {
			$stylesheets[] =  $skin_url . '/skin.css';
			if ( file_exists(sem_path . '/custom.css') )
				$stylesheets[] = sem_url . '/custom.css';
			if ( file_exists($skin_path . '/custom.css') )
				$stylesheets[] =  $skin_url . '/custom.css';
		}

		global $editor_styles;
		$editor_styles = (array) $editor_styles;
		$editor_styles = array_merge( $editor_styles, $stylesheets );
	} // add_editor_styles()


	/**
	 * add_editor_body_class()
	 *
	 * @param array $mceInit
	 * @return array $$mceInit
	 */

	function add_editor_body_class($mceInit) {
		global $sem_theme_options;

		$classes = array();

		$active_layout = apply_filters('active_layout', $sem_theme_options['active_layout']);

		$classes[] = $active_layout;

		if ( $active_layout != 'letter' ) {
			$extra_layout = str_replace(array('s', 't'), 'm', $active_layout);

			if ( $extra_layout != $active_layout) {
				$classes[] = $extra_layout;
				$classes[] = str_replace(array('s', 't'), '', $active_layout)
					. ( substr_count(str_replace('t', 's', $active_layout), 's')) . 's';
			}
		}

		$classes[] = preg_replace("/[^a-z]+/", '_', $sem_theme_options['active_skin']);

		if ( $sem_theme_options['active_font'] )
			$classes[] = preg_replace("/[^a-z]+/", '_', $sem_theme_options['active_font']);

		$mceInit['body_class'] .= ' '  . implode( ' ', $classes);

		return $mceInit;
	}

	/**
	 * Initialize Filesystem object
	 *
	 * @param str $form_url - URL of the page to display request form
	 * @param str $method - connection method
	 * @param str $context - destination folder
	 * @param array $fields - fields of $_POST array that should be preserved between screens
	 * @return bool/str - false on failure, stored text on success
	 **/
	static function filesystem_init($form_url, $method, $context, $fields = null) {
	  global $wp_filesystem;

	  /* first attempt to get credentials */
	  if (false === ($creds = request_filesystem_credentials($form_url, $method, false, $context, $fields))) {

	    /**
	     * if we comes here - we don't have credentials
	     * so the request for them is displaying
	     * no need for further processing
	     **/
	    return false;
	  }

	  /* now we got some credentials - try to use them*/
	  if (!WP_Filesystem($creds)) {

	    /* incorrect connection data - ask for credentials again, now with error message */
	    request_filesystem_credentials($form_url, $method, true, $context);
	    return false;
	  }

	  return true; //filesystem object successfully initiated
	}
} # sem_template


//$sem_template = new sem_template();
sem_template::get_instance();
