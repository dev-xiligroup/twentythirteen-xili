<?php

// dev.xiligroup.com - msc - 2013-03-03 - initial release
// dev.xiligroup.com - msc - 2013-05-28 - public release
// dev.xiligroup.com - msc - 2013-07-15 - more options to propagate - see 2013-xili example
// 1.0 - 2013-08-20 - first downloadable version - http://2013.extend.xiligroup.org
// 1.0.2 - 2013-10-10 - add option for multilingual bk banner
// 1.1.0 - 2013-11-03 - aligned to parent 2013 v 1.1
// 1.1.1 - 2013-11-03 - improved permalinks options
// 1.1.2 - 2013-11-11 - fixes permalinks options
// 1.1.3 - 2014-01-08 - fixes wp_title issue with new filter twentythirteen_xili_wp_title
// 1.1.4 - 2014-01-19 - fixes require_once of multilingual-functions.php (thanks to Herold) - add is_xili_adjacent_filterable (reserved future uses and in class embedding)
// 1.1.5 - 2014-02-09 - Need XL 2.10.0+ - Adaptated for new class of permalinks
// 1.1.6 - 2014-03-04 - Add searchform.php
// 1.2.0 - 2014-05-11 - Update for parent 1.2 and WP 3.9.1 and XL 2.12
// 1.2.1 - 2014-06-09 - fixes if XL is inactive
// 1.2.2 - 2014-07-24 - ready for XL 2.15+
// 1.2.3 - 2014-08-24 - ready for XL 2.15.1+
// 1.2.4 - 2014-11-30 - ready for XL 2.15.3+
// 1.5 - 2015-04-24 - parent 2013 v1.5 - XL 2.17+ - WP 4.2


define( 'TWENTYTHIRTEEN_XILI_VER', '1.5'); // as parent style.css

// main initialisation functions

function twentythirteen_xilidev_setup () {

	$theme_domain = 'twentythirteen';

	load_theme_textdomain( $theme_domain, get_stylesheet_directory() . '/langs' ); // now use .mo of child

	$xl_required_version = false;

	$minimum_xl_version = '2.14.9';

	if ( class_exists('xili_language') ) { // if temporary disabled

		$xl_required_version = version_compare ( XILILANGUAGE_VER, $minimum_xl_version, '>' );

		global $xili_language;

		$xili_language_includes_folder = $xili_language->plugin_path .'xili-includes';

		$xili_functionsfolder = get_stylesheet_directory() . '/functions-xili' ;

		if ( file_exists( $xili_functionsfolder . '/multilingual-classes.php') ) {
			require_once ( $xili_functionsfolder . '/multilingual-classes.php' ); // xili-options created by developers in child theme in priority

		} elseif ( file_exists( $xili_language_includes_folder . '/theme-multilingual-classes.php') ) {
			require_once ( $xili_language_includes_folder . '/theme-multilingual-classes.php' ); // ref xili-options based in plugin
		}

		if ( file_exists( $xili_functionsfolder . '/multilingual-functions.php') ) {
			require_once ( $xili_functionsfolder . '/multilingual-functions.php' );
		}

		global $xili_language_theme_options ; // used on both side
	// Args dedicaced to this theme named Twenty Thirteen
		$xili_args = array (
			'customize_clone_widget_containers' => true, // comment or set to true to clone widget containers
			'settings_name' => 'xili_2013_theme_options', // name of array saved in options table
			'theme_name' => 'Twenty Thirteen',
			'theme_domain' => $theme_domain,
			'child_version' => TWENTYTHIRTEEN_XILI_VER
		);

		if ( is_admin() ) {

		// Admin args dedicaced to this theme

			$xili_admin_args = array_merge ( $xili_args, array (
				'customize_adds' => true, // add settings in customize page
				'customize_addmenu' => false, // done by 2013
				'capability' => 'edit_theme_options',
				'authoring_options_admin' => false,
			) );

			if ( class_exists ( 'xili_language_theme_options_admin' ) ) {
				$xili_language_theme_options = new xili_language_theme_options_admin ( $xili_admin_args );
				$class_ok = true ;
			} else {
				$class_ok = false ;
			}


		} else { // visitors side - frontend

			if ( class_exists ( 'xili_language_theme_options' ) ) {
				$xili_language_theme_options = new xili_language_theme_options ( $xili_args );
				$class_ok = true ;
			} else {
				$class_ok = false ;
			}
		}

		// new ways to add parameters in authoring propagation
		add_theme_support('xiliml-authoring-rules', array (
			'post_content' => array('default' => '1',
				'data' => 'post',
				'hidden' => '',
				'name' => 'Post Content',
				/* translators: added in child functions by xili */
				'description' => __('Will copy content in the future translated post', 'twentythirteen')
		),
			'post_parent' => array('default' => '1',
				'data' => 'post',
				'name' => 'Post Parent',
				'hidden' => '1',
				/* translators: added in child functions by xili */
				'description' => __('Will copy translated parent id (if original has parent and translated parent)!', 'twentythirteen')
		))
		); //

		if ( $class_ok ) {
			$xili_theme_options = get_theme_xili_options() ;
			// to collect checked value in xili-options of theme
			if ( file_exists( $xili_functionsfolder . '/multilingual-permalinks.php') && $xili_language->is_permalink && isset( $xili_theme_options['perma_ok'] ) && $xili_theme_options['perma_ok']) {
				require_once ( $xili_functionsfolder . '/multilingual-permalinks.php' ); // require subscribing premium services
			}
			if ( $xl_required_version ) { // msg choice is inside class
				$msg = $xili_language_theme_options->child_installation_msg( $xl_required_version, $minimum_xl_version, $class_ok );
			} else {
				$msg = '
				<div class="error">'.
					/* translators: added in child functions by xili */
					'<p>' . sprintf ( __('The %1$s child theme requires xili_language version more recent than %2$s installed', 'twentythirteen' ), get_option( 'current_theme' ), $minimum_xl_version ) .'</p>
				</div>';

			}
		} else {

			$msg = '
			<div class="error">'.
				/* translators: added in child functions by xili */
				'<p>' . sprintf ( __('The %s child theme requires xili_language_theme_options class installed and activated', 'twentythirteen' ), get_option( 'current_theme' ) ).'</p>
			</div>';

		}

	} else {

		$msg = '
		<div class="error">'.
			/* translators: added in child functions by xili */
			'<p>' . sprintf ( __('The %s child theme requires xili-language plugin installed and activated', 'twentythirteen' ), get_option( 'current_theme' ) ).'</p>
		</div>';

	}
	// after activation and in themes list
	if ( isset( $_GET['activated'] ) || ( ! isset( $_GET['activated'] ) && ( ! $xl_required_version || ! $class_ok ) ) )
		add_action( 'admin_notices', $c = create_function( '', 'echo "' . addcslashes( $msg, '"' ) . '";' ) );

	// end errors...
	;
}
add_action( 'after_setup_theme', 'twentythirteen_xilidev_setup', 11 ); // after parent functions

// since XL 2.15.1
function twentythirteen_bundled_themes_support_flags () {
	$listlanguages = array(
				'ar_ar','ar_ma', 'ar_xx', 'cn_cn',
				'de_de', 'en_us', 'es_es', 'fr_be', 'fr_ca', 'fr_fr',
				'it_it', 'ja_ja', 'ja', 'km_kh', 'pt_pt', 'ru_ru', 'zh_cn') ;

	remove_theme_support ( 'custom_xili_flag');
	$args = array();
	foreach ( $listlanguages as $one_language ) {
				$args[$one_language] = array(
						'path' => '%2$s/images/flags/'.$one_language.'.png',
						'height'				=> 16,
						'width'					=> 11
					);
			}
	add_theme_support ( 'custom_xili_flag', $args );
}

add_action( 'after_setup_theme', 'twentythirteen_bundled_themes_support_flags', 12 ); // after plugin functions - called after

function xili_customize_js_footer () {

	wp_enqueue_script( 'customize-xili-js-footer', get_stylesheet_directory_uri(). '/functions-xili' . '/js/xili_theme_customizer.js' , array( 'customize-preview' ), TWENTYTHIRTEEN_XILI_VER, true );

}
// need to be here not as hook not in class
add_action( 'customize_preview_init', 'xili_customize_js_footer', 9 ); // before parent 2013 to be in footer

function twentythirteen_xilidev_setup_custom_header () {

	// %2$s = in child
	register_default_headers( array(
		'xili2013' => array(

			'url'			=> '%2$s/images/headers/xili-2013.jpg',
			'thumbnail_url' => '%2$s/images/headers/xili-2013-thumbnail.jpg',
			/* translators: added in child functions by xili */
			'description'	=> _x( '2013 by xili', 'header image description', 'twentythirteen' )
		))
	);

	$args = array(
		// Text color and image (empty to use none).
		'default-text-color'	=> 'fffff0', // diff of parent
		'default-image'			=> '%2$s/images/headers/xili-2013.jpg',

		// Set height and width, with a maximum value for the width.
		'height'		=> 230,
		'width'			=> 1600,

		// Callbacks for styling the header and the admin preview.
		'wp-head-callback'			=> 'twentythirteen_xili_header_style',
		'admin-head-callback'		=> 'twentythirteen_admin_header_style',
		'admin-preview-callback'	=> 'twentythirteen_admin_header_image',
	);

	add_theme_support( 'custom-header', $args ); // need 8 in add_action to overhide parent

}
add_action( 'after_setup_theme', 'twentythirteen_xilidev_setup_custom_header', 9 );


add_action("admin_head-appearance_page_custom-header", "twentythirteen_xili_header_help", 15);

function twentythirteen_xili_header_help ( ) {
	global $xili_language_theme_options;
	$header_setting_url = admin_url('/themes.php?page='. $xili_language_theme_options->settings_name );

	get_current_screen()->add_help_tab( array(
			'id'		=> 'set-header-image-xili',
			/* translators: added in child functions by xili */
			'title'		=> __('Multilingual Header Image in 2013-xili', 'twentythirteen'),
			'content'	=>
				/* translators: added in child functions by xili */
				'<p>' . __( 'You can set a custom image header for your site according each current language. When the language changes, the header image will change. The default header image is assigned to unknown unaffected language.', 'twentythirteen' ) . '</p>' .
				/* translators: added in child functions by xili */
				'<p>' . sprintf( __( 'The images will be assigned to the language in the %1$sXili-Options%2$s Appearance settings page.', 'twentythirteen'),'<a href="'.$header_setting_url.'">' ,'</a>' ). '</p>'
		) );

}

// function twentythirteen_header_style() for xili
function twentythirteen_xili_header_style () {

	$header_image_url = get_header_image();
	$text_color = get_header_textcolor();

	// If no custom options for text are set, let's bail.
	if ( empty( $header_image_url ) && $text_color == get_theme_support( 'custom-header', 'default-text-color' ) )
		return;

	// If we get this far, we have custom styles.
	?>
	<style type="text/css" id="twentythirteen-header-css">
	<?php
		if ( ! empty( $header_image_url ) ) :
			if ( class_exists ( 'xili_language' ) ) {
				$xili_theme_options = get_theme_xili_options() ;
				if ( isset ( $xili_theme_options['xl_header'] ) && $xili_theme_options['xl_header'] ) {
				global $xili_language, $xili_language_theme_options ;
				// check if image exists in current language
				// 2013-10-10 - Tiago suggestion
				$curlangslug = ( '' == the_curlang() ) ? strtolower( $xili_language->default_lang ) : the_curlang() ;


					$headers = get_uploaded_header_images(); // search in uploaded header list

					$this_default_headers = $xili_language_theme_options->get_processed_default_headers () ;
					if ( ! empty( $this_default_headers ) ) {
						$headers = array_merge( $this_default_headers, $headers );
					}
					foreach ( $headers as $header_key => $header ) {

						if ( isset ( $xili_theme_options['xl_header_list'][$curlangslug] ) && $header_key == $xili_theme_options['xl_header_list'][$curlangslug] ) {
							$header_image_url = $header['url'];
							break ;
						}
					}
				}
			}
	?>
		.site-header {
			background: url(<?php echo $header_image_url; ?>) no-repeat scroll top;
			background-size: 1600px auto;
		}
	<?php
		endif; // image exists

		// Has the text been hidden?
		if ( ! display_header_text() ) :
	?>
		.site-title,
		.site-description {
			position: absolute;
			clip: rect(1px 1px 1px 1px); /* IE7 */
			clip: rect(1px, 1px, 1px, 1px);
		}
	<?php
			if ( empty( $header_image ) ) :
	?>
		.site-header .home-link {
			min-height: 0;
		}
	<?php
			endif;

		// If the user has set a custom color for the text, use that.
		elseif ( $text_color != get_theme_support( 'custom-header', 'default-text-color' ) ) :
	?>
		.site-title,
		.site-description {
			color: #<?php echo esc_attr( $text_color ); ?>;
		}
	<?php endif; ?>
	</style>
	<?php
}



function twentythirteen_reset_default_theme_value ( $theme ) {
	set_theme_mod( 'header-text-color', 'fffff0' ); // to force first insertion // same in css
}
add_action('after_switch_theme', 'twentythirteen_reset_default_theme_value' );


/**
 * define when search form is completed by radio buttons to sub-select language when searching
 *
 */
function special_head() {

	// to change search form of widget
	// if ( is_front_page() || is_category() || is_search() )
	if ( is_search() ) {
		add_filter('get_search_form', 'my_langs_in_search_form_2013', 10, 1); // in multilingual-functions.php
	}
	$xili_theme_options = get_theme_xili_options() ;

}
if ( class_exists('xili_language') )	// if temporary disabled
	add_action( 'wp_head', 'special_head', 11);

/**
 * overhide default twentythirteen_entry_meta
 */
function twentythirteen_entry_meta() {
	if ( is_sticky() && is_home() && ! is_paged() )
		echo '<span class="featured-post">' . __( 'Sticky', 'twentythirteen' ) . '</span>';

	if ( ! has_post_format( 'aside' ) && ! has_post_format( 'link' ) && 'post' == get_post_type() )
		twentythirteen_entry_date();

	// translators: used between list items, there is a space after the comma.
	$categories_list = get_the_category_list( __( ', ', 'twentythirteen' ) );
	if ( $categories_list ) {
		echo '<span class="categories-links">' . $categories_list . '</span>';
	}

	// translators: used between list items, there is a space after the comma.
	$tag_list = get_the_tag_list( '', __( ', ', 'twentythirteen' ) );
	if ( $tag_list ) {
		echo '<span class="tags-links">' . $tag_list . '</span>';
	}

	// Post author
	if ( 'post' == get_post_type() ) {
		printf( '<span class="author vcard"><a class="url fn n" href="%1$s" title="%2$s" rel="author">%3$s</a></span>',
			esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ),
			esc_attr( sprintf( __( 'View all posts by %s', 'twentythirteen' ), get_the_author() ) ),
			get_the_author()
		);
	}

	if ( is_singular() && class_exists('xili_language') ) {
		global $post;
		echo '&nbsp;-&nbsp;';
		$xili_theme_options = get_theme_xili_options() ;
		if ( xiliml_new_list() ) xiliml_the_other_posts($post->ID, $xili_theme_options['linked_title']);
	}
}

/**
 * to choice xiliml_the_other_posts in singular
 * @since 1.1
 */
function xiliml_new_list() {
	if ( class_exists('xili_language') ) {
		global $xili_language;

		$xili_theme_options = get_theme_xili_options() ; // see below

		if ( $xili_theme_options['linked_posts'] == 1 ) {
			if (is_page() && is_front_page() ) {
				return false;
			} else {
				return true;
			}
		}

		if ( is_active_widget ( false, false, 'xili_language_widgets' ) ) {

			$xili_widgets = get_option('widget_xili_language_widgets', array());
			foreach ( $xili_widgets as $key => $arrprop ) {
				if ( $key != '_multiwidget' ) {
					if ( $arrprop['theoption'] == 'typeonenew' ) {	// widget with option for singular
						if ( is_active_widget( false, 'xili_language_widgets-'.$key, 'xili_language_widgets' ) ) return false ;
					}
				}
			}
		}
		// since xl 2.8.5
		if ( XILILANGUAGE_VER > '2.0.0' && isset($xili_language -> xili_settings['navmenu_check_options']) && in_array ( $xili_language -> xili_settings['navmenu_check_options']['primary']['navtype'], array ('navmenu-1', 'navmenu-1a') ) ) return false ;

	}

	return true ;

}

/**
 * add search other languages in form - see functions.php when fired
 *
 */
function my_langs_in_search_form_2013 ( $the_form ) {

	$form = str_replace ( '</form>', '', $the_form ) . '<span class="xili-s-radio">' . xiliml_langinsearchform ( $before='<span class="radio-lang">', $after='</span>', false) . '</span>';
	$form .= '</form>';
	return $form ;
}

/**
 * condition to filter adjacent links
 * @since 1.1.4
 *
 */

function is_xili_adjacent_filterable() {

	if ( is_search () ) { // for multilingual search
		return false;
	}
	return true;
}

/**
 * filters when propagate post columns - example with insertion of prefix line -
 * @see propagate_post_columns in xili-language.php
 *
 */
function my_xiliml_propagate_post_columns($from_post_column, $key, $from_lang_slug, $to_lang_slug ) {
	switch ( $key ) {
		case 'post_content':
			$from_lang = translate( xili_get_language_field ( 'full name', $from_lang_slug ), 'twentythirteen' );
			$to_lang = translate( xili_get_language_field ( 'full name', $to_lang_slug ), 'twentythirteen' );
			/* translators: added in child functions by xili */
			$to_post_column = '<p>'. sprintf (__('The content in %1$s below must be translated in %2$s !', 'twentyfourteen'), $from_lang, $to_lang ). '</p>' . $from_post_column;
			break;

		default:
		$to_post_column = $from_post_column;
	}
	return $to_post_column;
}

add_filter ('xiliml_propagate_post_columns', 'my_xiliml_propagate_post_columns', 10, 4 );



function twentythirteen_xili_credits () {
	/* translators: added in child functions by xili */
	printf( __("Multilingual child theme of Twenty Thirteen by %s", 'twentythirteen' ),"<a href=\"http://dev.xiligroup.com\">dev.xiligroup</a> - " );
}

add_action ('twentythirteen_credits','twentythirteen_xili_credits');


?>