=== Semiologic Pinnacle theme ===
Contributors: Denis-de-Bernardy, Mike_Koepke
Donate link: http://www.semiologic.com/partners/
Tags: semiologic, wordpress theme
Requires at least: 3.9
Tested up to: 4.1
Stable tag: trunk

The Semiologic Pinnacle theme for Wordpress


== Description ==

The latest Semiologic Theme building on the widget and panel-centric approach of Semiologic Reloaded.   The Pinnacle theme is HTML5, CSS3, and responsive meant to look the same across all devices.


= Help Me! =

The [Semiologic forum](http://forum.semiologic.com) is the best place to report issues.


== Installation ==

1. Upload the themes folder to the `/wp-content/themes/` directory
2. Activate the theme through the 'Themes' menu in WordPress

If you previously had been using the Semiologic Reloaded theme, that widget configuration will be cloned over.


== Change Log ==

= 2.0.1 =

- Fix date not being shown for blog posts.

= 2.0 =

== New Features ==

- Responsive Implementation w/ accordian-style smartphone menu.
- HTML5 sematic markup
- Dropdown Menu support using the Semiologic menu implementation found in prior themes
	- Option for top only, 1 level or 2 levels of dropdowns
	- Caption for smartphone menu
	- All child pages of parent included in menu if 1 or 2 level selected
	- New exclude from menu checkbox in page editor.  (along with prior this page in widgets support)
- New Semiologic Admin menu section for all the Semiologic theme and package settings
- Breadcrumb navigation widget
- Post navigation widget (move between single post pages)
- Added support for some of the more common Google Fonts
- Top and Bottom Sidebars Panels - these span both the Main and Left and Right Sidebar areas
- New wp-content/semiologic folder for custom skins and css.
- New Sidebar-Wide Content and Wide Content-Sidebar templates for alternative page layouts on the site.
- Better typography
- WordPress' comment system with thread comments (up to 5 levels) is now supported.



== Enhanced/Change Functionality ==

- Fonts selection now on its own configuration page
- Order of Panels in the Widgets screen is more intuitive
- Option to display Header Top Level Menu as justified
- Enhanced schema.org and microformats markup
- Comment bubble has been removed
- Entry:Categories and Entry:Tags widgets have been consolidated into a single Entry:Footer widget
- The print style sheet has been redone for better site printing
- Post date is now displayed under the post title.
- Post/page date and author are always generated (though maybe hidden) to pass Rich Snippets/microformats validation
- Comment count and edit post are now under post title
- Flash header support has been removed.
- Fix: Sidebar widget lists with long post/page titles don't overflow widget boundaries
- Fix: Creation of header_top_wrapper was occurring after 1st widget
- Added option to display/not display pings/trackbacks in comment list
- Revamped Header SEO with H1 and H2 sitename and tagline.


== Under the Hood ==

- Collapsed many of the div sections to make skinning more straightforward.  Also benefits dom performance
	- All the pad class divs have been removed
	- Most _top and _bottom divs removed
	- xxxx_bg class divs changed to xxxx_inner
- The body div (#body) has been renamed to #body_wrapper for consistency with #header_wrapper and #footer_wrapper.   It also was confusing given the html body tag
- Theme has been tested with Automattic's Theme Checker and all required errors fixed
- Full reorg of the main stylesheet (style.css)
- Implementation of the normalize.css reset
- Menu separators now implemented in css so a different character (' / ' for example) can be used instead of just a pipe ( ' | ' )
- All font sizes are now in em units.  The base font size is 1em = 16px
- print.css is now only used to style a single post/page
- Theme has it's own WP options entry that is separate from the overall Semiologic Pro options.
- Closing php brace ?> has been removed from all source files to how eliminate header sent issues
- Multi-part has been removed with elimination of flash headers
- Font-awesome icon font is now linked (via cdn) and used in the theme
- New entry_widget, main_widget and body_widget classes
- Header image is standard html img and no longer a background image
- New ie, ie7, ie8 and ie9 classes for conditional ie skinning
- Standard WP css classes have been used for comment styling



