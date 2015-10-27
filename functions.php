<?php
/**
 * RedAndBlack functions and definitions.
 *
 * @link https://codex.wordpress.org/Functions_File_Explained
 *
 */

if ( ! function_exists( 'redandblack_setup' ) ) :
/**
 * Sets up theme defaults and registers support for various WordPress features.
 *
 * Note that this function is hooked into the after_setup_theme hook, which
 * runs before the init hook. The init hook is too late for some features, such
 * as indicating support for post thumbnails.
 */
function redandblack_setup() {
	/*
	 * Let WordPress manage the document title.
	 * By adding theme support, we declare that this theme does not use a
	 * hard-coded <title> tag in the document head, and expect WordPress to
	 * provide it for us.
	 */
	add_theme_support( 'title-tag' );

	// This theme styles the visual editor to resemble the theme style.
	add_editor_style();

	// Add RSS feed links to <head> for posts and comments.
	add_theme_support( 'automatic-feed-links' );

	/*
	 * Enable support for Post Thumbnails on posts and pages.
	 *
	 * @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
	 *
	 * Note: also enables "featured images" functionality on pages
	 */
	add_theme_support( 'post-thumbnails' );
	set_post_thumbnail_size( 766, 292, true ); // default Post Thumbnail dimensions (cropped)
}
endif; // redandblack_setup
add_action( 'after_setup_theme', 'redandblack_setup' );


if ( ! function_exists( 'redandblack_content_width' ) ) :
/**
 * Set the content width in pixels, based on the theme's design and stylesheet.
 *
 * Priority 0 to make it available to lower priority callbacks.
 *
 * @global int $content_width
 */
function redandblack_content_width() {
	$GLOBALS['content_width'] = apply_filters( 'redandblack_content_width', 766 );
}
endif;
add_action( 'after_setup_theme', 'redandblack_content_width', 0 );


if ( ! function_exists( 'redandblack_menus_init' ) ) :
/*
 * Add two menus - one for header, one for footer
 */
function redandblack_menus_init() {
	register_nav_menus( array(
		'header-menu' => __( 'Header Menu', 'redandblack' ),
		'footer-menu'  => __( 'Footer Menu', 'redandblack' ),
	) );
}
endif;
add_action( 'init', 'redandblack_menus_init' );


if ( ! function_exists( 'redandblack_widgets_init' ) ) :
/**
 * Register RedAndBlack widget areas.
 */
function redandblack_widgets_init() {
	register_sidebar( array(
		'name'		  => __( 'Blog Sidebar', 'redandblack' ),
		'id'			=> 'blog-sidebar',
		'description'   => __( 'Sidebar to appear on the left side of the blog (posts) page.', 'redandblack' ),
	) );
 }
endif;
add_action( 'widgets_init', 'redandblack_widgets_init' );


/**
 * Enqueue scripts and styles.
 */
function redandblack_scripts() {
	wp_enqueue_style( 'redandblack-style', get_stylesheet_uri() );

	wp_enqueue_script( 'redandblack-navigation-header', get_template_directory_uri() . '/js/navigation_header.js', array(), '20150819', true );
	wp_enqueue_script( 'redandblack-navigation-footer', get_template_directory_uri() . '/js/navigation_footer.js', array(), '20150819', true );

	wp_enqueue_script( 'redandblack-skip-link-focus-fix', get_template_directory_uri() . '/js/skip-link-focus-fix.js', array(), '20130115', true );

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'redandblack_scripts' );

/**
 * Custom template tags for this theme.
 */
require get_template_directory() . '/inc/template-tags.php';

/**
 * Custom 'Customizer' functionality.
 */
require get_template_directory() . '/inc/customizer.php';
