<?php
/**
 * entry_header
 *
 * @package Semiologic Reloaded
 **/

class entry_header extends WP_Widget {
	/**
	 * Constructor.
	 *
	 */
	public function __construct() {
		$widget_name = __('Entry: Header', 'sem-pinnacle');
		$widget_ops = array(
			'classname' => 'entry_header',
			'description' => __('The entry\'s title and date. Must be placed in the loop (each entry).', 'sem-pinnacle'),
			);
		$control_ops = array(
			'width' => 330,
			);

		$this->WP_Widget('entry_header', $widget_name, $widget_ops, $control_ops);
	} # entry_header()


	/**
	 * widget()
	 *
	 * @param array $args widget args
	 * @param array $instance widget options
	 * @return void
	 **/

	function widget($args, $instance) {
		if ( $args['id'] != 'the_entry' || !class_exists('widget_contexts') && is_letter() )
			return;

		$instance = wp_parse_args($instance, entry_header::defaults());
		extract($args, EXTR_SKIP);
		extract($instance, EXTR_SKIP);

		$date = false;
		if ( $show_post_date && !is_sticky() && ( is_single() || !is_singular() && !is_day() ) )
			$date = the_date('', '', '', false);

		$title = the_title('', '', false);

		if ( $title && !is_singular() ) {
			$permalink = apply_filters('the_permalink', get_permalink());
			$title = '<a href="' . esc_url($permalink) . '" title="' . esc_attr($title) . '" rel="bookmark">'
				. $title
				. '</a>';
		}

        $byline = '';
        if ( $show_author_byline) {
            $author = get_the_author();
            $author_url = get_author_posts_url( get_the_author_meta( 'ID' ) );

            $byline = '<span class="byline_author vcard">'
				. $author_byline . ' '
				. '<a class="url fn" href="' . esc_url($author_url) . '" rel="author">'
				. $author
				. '</a>'
				. '</span>' . "\n";
        }

		$entry_date = '';
		if ( $date ) {
			$entry_date = '<time class="entry_date updated" datetime="' . esc_attr( get_the_date( 'c' ) ) . '">'
				. '<span>'
				. $date
				. '</span>'
		        . '</time>';
		}

		if ( $title ) {
			echo '<div class="entry_header">' . "\n"
				. '<h1 class="entry-title">'
				. $title
				. '</h1>' . "\n"
				. '</div>' . "\n";
		}

		echo '<div class="entry_meta">' . "\n"
			. $entry_date . ' ' . $byline . "\n"
			. '</div>' . "\n";

	} # widget()

	/**
	 * update()
	 *
	 * @param array $new_instance
	 * @param array $old_instance
	 * @return array $instance
	 **/

	function update($new_instance, $old_instance) {
		$instance['show_post_date'] = isset($new_instance['show_post_date']);
        $instance['show_author_byline'] = isset($new_instance['show_author_byline']);
      	$instance['author_byline'] = trim(strip_tags($new_instance['author_byline']));

		return $instance;
	} # update()


	/**
	 * form()
	 *
	 * @param array $instance widget options
	 * @return void
	 **/

	function form($instance) {
		$instance = wp_parse_args($instance, entry_header::defaults());
		extract($instance, EXTR_SKIP);

		echo '<h3>' . __('Config', 'sem-pinnacle') . '</h3>' . "\n";

		echo '<p>'
			. '<label>'
			. '<input type="checkbox"'
			. ' name="' . $this->get_field_name('show_post_date') . '"'
			. checked($show_post_date, true, false)
			. ' />'
			. '&nbsp;'
			. __('Show post dates.', 'sem-pinnacle')
			. '</label>'
			. '</p>' . "\n";

        echo '<p>'
     			. '<label>'
     			. '<input type="checkbox"'
     			. ' name="' . $this->get_field_name('show_author_byline') . '"'
     			. checked($show_author_byline, true, false)
     			. ' />'
     			. '&nbsp;'
     			. __('Show author byline.', 'sem-pinnacle')
     			. '</label>'
     			. '</p>' . "\n";

        echo '<h3>' . __('Captions', 'sem-pinnacle') . '</h3>' . "\n";

 		echo '<p>'
 			. '<label>'
 			. '<code>' . __('by', 'sem-pinnacle') . '</code>'
 			. '<br />' . "\n"
 			. '<input type="text" class="widefat"'
 			. ' name="' . $this->get_field_name('author_byline') . '"'
 			. ' value="' . esc_attr($author_byline) . '"'
 			. ' />'
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
			'show_post_date' => true,
            'show_author_byline' => false,
            'author_byline' => __('by', 'sem-pinnacle'),
			);
	} # defaults()
} # entry_header


/**
 * entry_content
 *
 * @package Semiologic Reloaded
 **/

class entry_content extends WP_Widget {
	/**
	 * Constructor.
	 *
	 */
	public function __construct() {
		$widget_name = __('Entry: Content', 'sem-pinnacle');
		$widget_ops = array(
			'classname' => 'entry_content',
			'description' => __('The entry\'s content. Must be placed in the loop (each entry).', 'sem-pinnacle'),
			);
		$control_ops = array(
			'width' => 330,
			);

		$this->WP_Widget('entry_content', $widget_name, $widget_ops, $control_ops);

		if ( class_exists('fancy_excerpt') )
			add_filter('the_content_more_link', array($this, 'more_link'), 0);
	} # entry_content()


	/**
	 * widget()
	 *
	 * @param array $args widget args
	 * @param array $instance widget options
	 * @return void
	 **/

	function widget($args, $instance) {
		if ( $args['id'] != 'the_entry' )
			return;

		global $post;
		$instance = wp_parse_args($instance, entry_content::defaults());
		extract($args, EXTR_SKIP);
		extract($instance, EXTR_SKIP);

		$title = the_title('', '', false);

		if ( $show_excerpts && !is_singular() ) {
			$content = apply_filters('the_excerpt', get_the_excerpt());
            $content_class = "entry-summary";
		} else {
			$more_link = sprintf($more_link, $title);

			$content = get_the_content($more_link, 0, '');

			if ( is_attachment() && $post->post_parent && preg_match("/^image\//i", $post->post_mime_type) ) {
				# strip wpautop junk
				$content = preg_replace("/<br\s*\/>\s+$/", '', $content);

				# add gallery links
				$attachments = get_children(array(
						'post_parent' => $post->post_parent,
						'post_type' => 'attachment',
						'post_mime_type' => 'image',
						'order_by' => 'menu_order ID',
						));

				foreach ( $attachments as $k => $attachment )
					if ( $attachment->ID == $post->ID )
						break;

				$prev_image = isset($attachments[$k-1])
					? wp_get_attachment_link($attachments[$k-1]->ID, 'thumbnail', true)
					: '';
				$next_image = isset($attachments[$k+1])
					? wp_get_attachment_link($attachments[$k+1]->ID, 'thumbnail', true)
					: '';

				if ( $prev_image || $next_image ) {
					$content .= '<div class="gallery_nav">' . "\n"
						. '<div class="prev_image">' . "\n"
						. $prev_image
						. '</div>' . "\n"
						. '<div class="next_image">' . "\n"
						. $next_image
						. '</div>' . "\n"
						. '<div class="spacer"></div>' . "\n"
						. '</div>' . "\n";
				}
			}

			$content = apply_filters('the_content', $content);

			$content .= wp_link_pages(
				array(
					'before' => '<div class="entry_nav"> ' . $paginate . ' ',
					'after' => '</div>' . "\n",
					'echo' => 0,
					)
				);
            $content_class = "entry-content";
		}

		$actions = '';

		if ( !isset($_GET['action']) || $_GET['action'] != 'print' ) {
			global $post;

			$edit_link = get_edit_post_link($post->ID, 'raw');
			if ( $edit_link ) {
				$edit_link = '<a class="post-edit-link"'
					. ' href="' . esc_url($edit_link) . '"'
					. ' title="' . esc_attr(__('Edit', 'sem-pinnacle')) . '">'
					. __('Edit', 'sem-pinnacle')
					. '</a>';
				$edit_link = apply_filters('edit_post_link', $edit_link, $post->ID);

				$actions .= '<span class="edit_entry">'
					. $edit_link
					. '</span>' . "\n";
			}

			$num_comments = (int) get_comments_number();

			if ( $show_comment_box && ( $num_comments || comments_open() ) ) {
				$comments_link = apply_filters('the_permalink', get_permalink());
				$comments_link .= $num_comments ? '#comments' : '#respond';

				$actions .= '<span class="comment_box">'
					. '<a href="' . esc_url($comments_link) . '">'
					. $num_comments
					. '</a>'
					. '</span>' . "\n";
			}

			if ( $actions ) {
				$actions = '<div class="entry_actions">' . "\n"
					. $actions
					. '</div>' . "\n";
			}
		}

		$thumbnail = '';
		if ( !is_single() && $show_thumbnail && function_exists('get_the_post_thumbnail') ) {
			add_filter('image_downsize', array($this, 'thumbnail_downsize'), 10, 3);
			$thumbnail = get_the_post_thumbnail();
			remove_filter('image_downsize', array($this, 'thumbnail_downsize'), 10, 3);
		}

		if ( $thumbnail ) {
			$thumbnail = '<div class="wp_thumbnail">'
				. $thumbnail
				. '</div>' . "\n";
		}

		if ( $actions || $content ) {
			echo '<div class="entry_content ' . $content_class . '">' . "\n"
				. $actions
				. $thumbnail
				. $content
				. '<div class="spacer"></div>' . "\n"
				. $after_widget;
		}
	} # widget()


	/**
	 * thumbnail_downsize()
	 *
	 * @param mixed $in
	 * @param int $id
	 * @param string $size
	 * @return false on failure, array on success
	 **/

	function thumbnail_downsize($in, $id, $size) {
		if ( $in || $size != 'post-thumbnail' )
			return $in;
		return image_downsize($id, 'thumbnail');
	} # thumbnail_downsize()


	/**
	 * more_link()
	 *
	 * @param string $more
	 * @return string $more
	 **/

	function more_link($more) {
		if ( !$this->number )
			return $more;

		$instance = $this->get_settings();
		$instance = $instance[$this->number];
		$instance = wp_parse_args($instance, entry_content::defaults());

		$more = '<a href="' . apply_filters('the_permalink', get_permalink()) . '#more-' . get_the_ID() . '" class="more-link">'
			. sprintf($instance['more_link'], get_the_title())
			. '</a>';

		return $more;
	} # more_link()


	/**
	 * update()
	 *
	 * @param array $new_instance
	 * @param array $old_instance
	 * @return array $instance
	 **/

	function update($new_instance, $old_instance) {
		$instance['show_comment_box'] = isset($new_instance['show_comment_box']);
		$instance['show_excerpts'] = isset($new_instance['show_excerpts']);
		$instance['more_link'] = trim(strip_tags($new_instance['more_link']));
		$instance['paginate'] = trim(strip_tags($new_instance['paginate']));
        $instance['show_thumbnail'] = isset($new_instance['show_thumbnail']);

		return $instance;
	} # update()


	/**
	 * form()
	 *
	 * @param array $instance widget options
	 * @return void
	 **/

	function form($instance) {
		$instance = wp_parse_args($instance, entry_content::defaults());
		extract($instance, EXTR_SKIP);

		echo '<h3>' . __('Config', 'sem-pinnacle') . '</h3>' . "\n";

		echo '<p>'
			. '<label>'
			. '<input type="checkbox"'
			. ' name="' . $this->get_field_name('show_comment_box') . '"'
			. checked($show_comment_box, true, false)
			. ' />'
			. '&nbsp;'
			. __('Display the box with the number of comments.', 'sem-pinnacle')
			. '</label>'
			. '</p>' . "\n";

		echo '<p>'
			. '<label>'
			. '<input type="checkbox"'
			. ' name="' . $this->get_field_name('show_excerpts') . '"'
			. checked($show_excerpts, true, false)
			. ' />'
			. '&nbsp;'
			. __('Use the post\'s excerpt on blog and archive pages.', 'sem-pinnacle')
			. '</label>'
			. '</p>' . "\n";

        echo '<p>'
            . '<label>'
            . '<input type="checkbox"'
            . ' name="' . $this->get_field_name('show_thumbnail') . '"'
            . checked($show_thumbnail, true, false)
            . ' />'
            . '&nbsp;'
            . __('Show the featured image, if set, for the post.', 'sem-pinnacle')
            . '</label>'
            . '</p>' . "\n";

      	echo '<h3>' . __('Captions', 'sem-pinnacle') . '</h3>' . "\n";

		echo '<p>'
			. '<label>'
			. '<code>' . __('Read more on %s...', 'sem-pinnacle') . '</code>'
			. '<br />' . "\n"
			. '<input type="text" class="widefat"'
			. ' name="' . $this->get_field_name('more_link') . '"'
			. ' value="' . esc_attr($more_link) . '"'
			. ' />'
			. '</label>'
			. '</p>' . "\n";

		echo '<p>'
			. '<label>'
			. '<code>' . __('Pages:', 'sem-pinnacle') . '</code>'
			. '<br />' . "\n"
			. '<input type="text" class="widefat"'
			. ' name="' . $this->get_field_name('paginate') . '"'
			. ' value="' . esc_attr($paginate) . '"'
			. ' />'
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
			'show_comment_box' => true,
			'show_excerpts' => false,
			'more_link' => __('Read more on %s...', 'sem-pinnacle'),
			'paginate' => __('Pages:', 'sem-pinnacle'),
            'show_thumbnail' => true,
			);
	} # defaults()
} # entry_content


/**
 * entry_categories
 *
 * @package Semiologic Reloaded
 **/

class entry_categories extends WP_Widget {
	/**
	 * Constructor.
	 *
	 */
	public function __construct() {
		$widget_name = __('Entry: Categories', 'sem-pinnacle');
		$widget_ops = array(
			'classname' => 'entry_categories',
			'description' => __('The entry\'s categories. Will only display on individual posts if placed outside of the loop (each entry).', 'sem-pinnacle'),
			);
		$control_ops = array(
			'width' => 330,
			);

		$this->WP_Widget('entry_categories', $widget_name, $widget_ops, $control_ops);
	} # entry_categories()


	/**
	 * widget()
	 *
	 * @param array $args widget args
	 * @param array $instance widget options
	 * @return void
	 **/

	function widget($args, $instance) {
		if ( is_admin() || is_singular() && !is_single() ) {
			return;
		} elseif ( $args['id'] != 'the_entry' ) {
			if ( !is_single() )
				return;

			global $post, $wp_the_query;
			$post = $wp_the_query->get_queried_object();
			setup_postdata($post);
		}

		$instance = wp_parse_args($instance, entry_categories::defaults());
		extract($args, EXTR_SKIP);
		extract($instance, EXTR_SKIP);

		if ( !$filed_under_by )
			return;

		$categories = get_the_category_list(', ');

		$author = get_the_author();
		$author_url = get_author_posts_url( get_the_author_meta( 'ID' ) );

        $author = '<span class="entry_author vcard">'
            . '<a class="url fn" href="' . esc_url($author_url) . '" rel="author">'
            . $author
            . '</a>'
            . '</span>';

		$date = apply_filters('the_time', get_the_time(__('M jS, Y', 'sem-pinnacle')), __('M jS, Y', 'sem-pinnacle'));

		if ( !is_day() )
			$date = '<a href="' . esc_url(get_month_link(get_the_time('Y'), get_the_time('m'))) . '">' . $date . '</a>';

		$date = '<span class="entry_date">'
            . '<time class="updated" datetime="' . esc_attr( get_the_date( 'c' ) ) . '">'
            . $date
            . '</time>'
            . '</span>';

		$comments = '';
		$num = get_comments_number();
		if ( $num && !is_single() ) {
			if ( $num > 1 ) {
				$comments = str_replace(' ', '&nbsp;', sprintf($n_comments, number_format_i18n($num)));
				$anchor = '#comments';
				$class = 'entry_replies';
			} elseif ( $num ) {
				$comments = str_replace(' ', '&nbsp;', $one_comment);
				$anchor = '#comments';
				$class = 'entry_replies';
			}
		} elseif ( comments_open() && ( $num && is_single() || !is_single() ) ) {
			$comments = $add_comment;
			$anchor = '#respond';
			$class = 'leave_reply';
		}

		if ( $comments ) {
			$comments = '<span class="' . $class . '">'
				. '<a href="' . esc_url(apply_filters('the_permalink', get_permalink()) . $anchor) . '">'
				. $comments
				. '</a>'
				. '</span>';
		}

		$link = '&nbsp;'
			. '<span class="link_entry">'
			. '<a href="' . esc_url(apply_filters('the_permalink', get_permalink())) . '" title="#">'
			. '<img src="' . sem_url . '/icons/pixel.gif' . '" width="14" height="12" class="no_icon" alt="#" />'
			. '</a>'
			. '</span>' . "\n";

		$title = $args['id'] != 'the_entry' && $title
			? apply_filters('widget_title', $title)
			: false;

		echo '<div class="entry_categories">'
			. ( $title
				? '<h2>' . $title . '</h2>'
				: ''
				)
			. '<p>'
			. str_replace('. .', '.', sprintf($filed_under_by, $categories, $author, $date, $comments))
			. $link
			. '</p>' . "\n"
			. '</div>';
	} # widget()


	/**
	 * update()
	 *
	 * @param array $new_instance new widget options
	 * @param array $old_instance old widget options
	 * @return array $instance
	 **/

	function update($new_instance, $old_instance) {
		foreach ( array_keys(entry_categories::defaults()) as $field )
			$instance[$field] = trim(strip_tags($new_instance[$field]));

		return $instance;
	} # update()


	/**
	 * form()
	 *
	 * @param array $instance widget options
	 * @return void
	 **/

	function form($instance) {
		$instance = wp_parse_args($instance, entry_categories::defaults());
		extract($instance, EXTR_SKIP);

		echo '<h3>' . __('Captions', 'sem-pinnacle') . '</h3>' . "\n";

		echo '<p>'
			. '<label>'
			. __('Title:', 'sem-pinnacle')
			. '<br />' . "\n"
			. '<input type="text" class="widefat"'
				. ' id="' . $this->get_field_id('title') . '"'
				. ' name="' . $this->get_field_name('title') . '"'
				. ' value="' . esc_attr($title) . '"'
				. ' />'
			. '</label>'
			. '</p>' . "\n";

		echo '<p>'
			. __('This widget\'s title is displayed only when this widget is placed out of the loop (each entry).', 'sem-pinnacle')
			. '</p>' . "\n";

		echo '<p>'
			. '<label>'
			. '<code>' . __('Filed under %1$s by %2$s on %3$s. %4$s.', 'sem-pinnacle') . '</code>'
			. '<br />' . "\n"
            . '<code>' . __('%1$s - categories, %2$s - $author, %3$s - $date, %4$s - $comments', 'sem-pinnacle') . '</code>'
         	. '<br />' . "\n"
			. '<input type="text" class="widefat"'
            . ' name="' . $this->get_field_name('filed_under_by') . '"'
            . ' value="' . esc_attr($filed_under_by) . '"'
            . ' />'
			. '</label>'
			. '</p>' . "\n";

		echo '<p>'
			. '<label>'
			. '<code>' . __('1 Comment', 'sem-pinnacle') . '</code>'
			. '<br />' . "\n"
			. '<input type="text" class="widefat"'
			. ' name="' . $this->get_field_name('one_comment') . '"'
			. ' value="' . esc_attr($one_comment) . '"'
			. ' />'
			. '</label>'
			. '</p>' . "\n";

		echo '<p>'
			. '<label>'
			. '<code>' . __('%d Comments', 'sem-pinnacle') . '</code>'
			. '<br />' . "\n"
			. '<input type="text" class="widefat"'
			. ' name="' . $this->get_field_name('n_comments') . '"'
			. ' value="' . esc_attr($n_comments) . '"'
			. ' />'
			. '</label>'
			. '</p>' . "\n";

		echo '<p>'
			. '<label>'
			. '<code>' . __('Comment', 'sem-pinnacle') . '</code>'
			. '<br />' . "\n"
			. '<input type="text" class="widefat"'
			. ' name="' . $this->get_field_name('add_comment') . '"'
			. ' value="' . esc_attr($add_comment) . '"'
			. ' />'
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
			'title' => __('Categories', 'sem-pinnacle'),
			'filed_under_by' => __('Filed under %1$s by %2$s on %3$s. %4$s.', 'sem-pinnacle'),
			'one_comment' => __('1 Comment', 'sem-pinnacle'),
			'n_comments' => __('%d Comments', 'sem-pinnacle'),
			'add_comment' => __('Comment', 'sem-pinnacle'),
			);
	} # defaults()
} # entry_categories


/**
 * entry_tags
 *
 * @package Semiologic Reloaded
 **/

class entry_tags extends WP_Widget {
	/**
	 * Constructor.
	 *
	 */
	public function __construct() {
		$widget_name = __('Entry: Tags', 'sem-pinnacle');
		$widget_ops = array(
			'classname' => 'entry_tags',
			'description' => __('The entry\'s tags. Will only display on individual entries if placed outside of the loop (each entry).', 'sem-pinnacle'),
			);
		$control_ops = array(
			'width' => 330,
			);

		$this->WP_Widget('entry_tags', $widget_name, $widget_ops, $control_ops);
	} # entry_tags()


	/**
	 * widget()
	 *
	 * @param array $args widget args
	 * @param array $instance widget options
	 * @return void
	 **/

	function widget($args, $instance) {
		if ( is_admin() ) {
			return;
		} elseif ( !in_the_loop() ) {
			if ( !is_singular() )
				return;

			global $post, $wp_the_query;
			$post = $wp_the_query->get_queried_object();
			setup_postdata($post);
		}

		if ( !class_exists('widget_contexts') && is_letter() )
			return;

		$instance = wp_parse_args($instance, entry_tags::defaults());
		extract($args, EXTR_SKIP);
		extract($instance, EXTR_SKIP);

		$term_links = array();
		$terms = get_the_terms(get_the_ID(), 'post_tag');

		if ( $terms && !is_wp_error($terms) ) {
			foreach ( $terms as $term ) {
				if ( $term->count == 0 )
					continue;
				$tag_link = get_term_link( $term, 'post_tag' );
				if ( is_wp_error( $tag_link ) )
					continue;
				$term_links[] = '<a href="' . esc_url($tag_link) . '" rel="tag">' . $term->name . '</a>';
			}

			$term_links = apply_filters( "term_links-post_tag", $term_links );
		}

		$_tags = apply_filters('the_tags', join(', ', $term_links));

		if ( $_tags ) {
			$title = apply_filters('widget_title', $title);

			echo '<div class="spacer"></div><div class="entry_tags">'
				. ( $args['id'] != 'the_entry' && $title
					? '<h2>' . $title . '</h2>'
					: ''
					)
				. '<p>'
				. sprintf($tags, $_tags)
				. '</p>' . "\n"
				. '</div>';
		}
	} # widget()


	/**
	 * update()
	 *
	 * @param array $new_instance new widget options
	 * @param array $old_instance old widget options
	 * @return array $instance
	 **/

	function update($new_instance, $old_instance) {
		foreach ( array_keys(entry_tags::defaults()) as $field )
			$instance[$field] = trim(strip_tags($new_instance[$field]));

		return $instance;
	} # update()


	/**
	 * form()
	 *
	 * @param array $instance widget options
	 * @return void
	 **/

	function form($instance) {
		$instance = wp_parse_args($instance, entry_tags::defaults());
		extract($instance, EXTR_SKIP);

		echo '<h3>' . __('Captions', 'sem-pinnacle') . '</h3>' . "\n";

		echo '<p>'
			. '<label>'
			. __('Title:', 'sem-pinnacle')
			. '<br />' . "\n"
			. '<input type="text" class="widefat"'
				. ' id="' . $this->get_field_id('title') . '"'
				. ' name="' . $this->get_field_name('title') . '"'
				. ' value="' . esc_attr($title) . '"'
				. ' />'
			. '</label>'
			. '</p>' . "\n";

		echo '<p>'
			. __('This widget\'s title is displayed only when this widget is placed out of the loop (each entry).', 'sem-pinnacle')
			. '</p>' . "\n";

		echo '<p>'
			. '<label>'
			. '<code>' . __('Tags: %s.', 'sem-pinnacle') . '</code>'
			. '<br />' . "\n"
			. '<input type="text" class="widefat"'
				. ' name="' . $this->get_field_name('tags') . '"'
				. ' value="' . esc_attr($tags) . '"'
				. ' />'
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
			'title' => __('Tags', 'sem-pinnacle'),
			'tags' => __('Tags: %s.', 'sem-pinnacle'),
			);
	} # defaults()
} # entry_tags


/**
 * entry_comments
 *
 * @package Semiologic Reloaded
 **/

class entry_comments extends WP_Widget {
	/**
	 * Constructor.
	 *
	 */
	public function __construct() {
		$widget_name = __('Entry: Comments', 'sem-pinnacle');
		$widget_ops = array(
			'classname' => 'entry_comments',
			'description' => __('The entry\'s comments. Must be placed in the loop (each entry).', 'sem-pinnacle'),
			);
		$control_ops = array(
			'width' => 330,
			);

		$this->WP_Widget('entry_comments', $widget_name, $widget_ops, $control_ops);
	} # entry_comments()


	/**
	 * widget()
	 *
	 * @param array $args widget args
	 * @param array $instance widget options
	 * @return void
	 **/

	function widget($args, $instance) {
		if ( $args['id'] != 'the_entry' || !is_singular() || !get_comments_number() && !comments_open() )
			return;

		if ( !class_exists('widget_contexts') && is_letter() )
			return;

		echo '<div class="entry_comments">' . "\n";

		global $comments_captions;
		$comments_captions = wp_parse_args($instance, entry_comments::defaults());

		comments_template('/comments.php');

		echo '</div>' . "\n";
	} # widget()


	/**
	 * update()
	 *
	 * @param array $new_instance new widget options
	 * @param array $old_instance old widget options
	 * @return array $instance
	 **/

	function update($new_instance, $old_instance) {
		foreach ( array_keys(entry_comments::defaults()) as $field ) {
			switch ( $field ) {
			case 'policy':
				if ( current_user_can('unfiltered_html') )
					$instance[$field] = $new_instance[$field];
				else
					$instance[$field] = $old_instance[$field];
				break;

			default:
				$instance[$field] = trim(strip_tags($new_instance[$field]));
				break;
			}
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
		$defaults = entry_comments::defaults();
		$instance = wp_parse_args($instance, $defaults);
		extract($instance, EXTR_SKIP);

		echo '<h3>' . __('Captions', 'sem-pinnacle') . '</h3>' . "\n";

		foreach ( $defaults as $field => $default ) {
			switch ( $field ) {
			case 'policy':
				echo '<p>'
					. '<label>'
					. __('Comment Policy', 'sem-pinnacle')
					. '<br />' . "\n"
					. '<textarea class="widefat" rows="4"'
						. ' name="' . $this->get_field_name($field) . '"'
						. ' >'
						. esc_html($$field)
						. '</textarea>'
					. '</label>'
					. '</p>' . "\n";
				break;

			default:
				echo '<p>'
					. '<label>'
					. '<code>' . $default . '</code>'
					. '<br />' . "\n"
					. '<input type="text" class="widefat"'
						. ' name="' . $this->get_field_name($field) . '"'
						. ' value="' . esc_attr($$field) . '"'
						. ' />'
					. '</label>'
					. '</p>' . "\n";
				break;
			}
		}
	} # form()


	/**
	 * defaults()
	 *
	 * @return array $defaults
	 **/

	function defaults() {
		return array(
			'pings_on' => __('Pings on %s', 'sem-pinnacle'),
			'comments_on' => __('Comments on %s', 'sem-pinnacle'),
			'leave_comment' => __('Leave a Comment', 'sem-pinnacle'),
			'reply_link' => __('Reply', 'sem-pinnacle'),
			'policy' => '',
			'login_required' => __('You must be logged in to post a comment. %s.', 'sem-pinnacle'),
			'logged_in_as' => __('You are logged in as %1$s. %2$s.', 'sem-pinnacle'),
			'name_field' => __('Name:', 'sem-pinnacle'),
			'email_field' => __('Email:', 'sem-pinnacle'),
			'url_field' => __('Website:', 'sem-pinnacle'),
			'required_fields' => __('Fields marked by an asterisk (*) are required.', 'sem-pinnacle'),
			'submit_field' => __('Submit Comment', 'sem-pinnacle'),
			);
	} # defaults()
} # entry_comments

/**
 * entry_navigation
 *
 * @package Semiologic Reloaded
 **/

class entry_navigation extends WP_Widget {
	/**
	 * Constructor.
	 *
	 */
	public function __construct() {
		$widget_name = __('Entry: Post Navigation', 'sem-pinnacle');
		$widget_ops = array(
			'classname' => 'entry_navigation',
			'description' => __('The next/previous blog post/page links. Must be placed in the loop (each entry).', 'sem-pinnacle'),
			);
		$control_ops = array(
			'width' => 330,
			);

		$this->WP_Widget('entry_navigation', $widget_name, $widget_ops, $control_ops);
	} # entry_comments()


	/**
	 * widget()
	 *
	 * @param array $args widget args
	 * @param array $instance widget options
	 * @return void
	 **/

	function widget($args, $instance) {
		if ( $args['id'] != 'the_entry' || !is_single() )
			return;

		global $post;

		// Don't print empty markup if there's nowhere to navigate.
		$previous = ( is_attachment() ) ? get_post( $post->post_parent ) : get_adjacent_post( false, '', true );
		$next     = get_adjacent_post( false, '', false );

		if ( ! $next && ! $previous )
			return;
		?>
		<div class="entry_navigation" role="navigation">
			<div class="nav-links">

				<div class="nav-prev">
				<?php previous_post_link( '%link', _x( '<span class="meta-nav">&laquo;</span> %title', 'Previous post link', 'sem-pinnacle' ) ); ?>
				</div>
				<div class="nav-next">
				<?php next_post_link( '%link', _x( '%title <span class="meta-nav">&raquo;</span>', 'Next post link', 'sem-pinnacle' ) ); ?>
				</div>

			</div><!-- .nav-links -->
		</div><!-- .navigation -->
		<?php

	} # widget()
} # entry_navigation