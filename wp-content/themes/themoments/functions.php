<?php
/**
 * themoments functions and definitions
 *
 * @package themoments
 */

if ( ! function_exists( 'themoments_setup' ) ) :
/**
 * Sets up theme defaults and registers support for various WordPress features.
 *
 * Note that this function is hooked into the after_setup_theme hook, which
 * runs before the init hook. The init hook is too late for some features, such
 * as indicating support for post thumbnails.
 */
function themoments_setup() {
	/*
	 * Make theme available for translation.
	 * Translations can be filed in the /languages/ directory.
	 * If you're building a theme based on themoments, use a find and replace
	 * to change 'themoments' to the name of your theme in all the template files
	 */
	load_theme_textdomain( 'themoments', get_template_directory() . '/languages' );

	// Add default posts and comments RSS feed links to head.
	add_theme_support( 'automatic-feed-links' );

	/*
	 * Let WordPress manage the document title.
	 * By adding theme support, we declare that this theme does not use a
	 * hard-coded <title> tag in the document head, and expect WordPress to
	 * provide it for us.
	 */
	add_theme_support( 'title-tag' );

	/*
	 * Enable support for Post Thumbnails on posts and pages.
	 *
	 * @link http://codex.wordpress.org/Function_Reference/add_theme_support#Post_Thumbnails
	 */
	add_theme_support( 'post-thumbnails' );
	
	// This theme uses wp_nav_menu() in one location.
	register_nav_menus( array(
		'primary' => esc_html__( 'Primary Menu', 'themoments' ),
		'secondary' => esc_html__( 'Footer Menu', 'themoments' ),
	) );

	/*
	 * Switch default core markup for search form, comment form, and comments
	 * to output valid HTML5.
	 */
	add_theme_support( 'html5', array(
		'search-form',
		'comment-form',
		'comment-list',
		'gallery',
		'caption',
	) );

	/*
	 * Enable support for Post Formats.
	 * See http://codex.wordpress.org/Post_Formats
	 */
	add_theme_support( 'custom-logo', array(
   'height'      => 90,
   'width'       => 400,
   'flex-width' => true,
	));

	// Set up the WordPress core custom background feature.
	add_theme_support( 'custom-background', apply_filters( 'themoments_custom_background_args', array(
		'default-color' => 'ffffff',
		'default-image' => '',
	) ) );

	add_theme_support( "custom-header", 
		array(
		'default-color' => 'ffffff',
		'default-image' => '',
			)  
		);
	add_editor_style() ;
}
endif; // themoments_setup
add_action( 'after_setup_theme', 'themoments_setup' );




/**
 * Enqueue scripts and styles.
 */
function themoments_scripts() {
	wp_enqueue_style( 'bootstrap', get_template_directory_uri().'/css/bootstrap.css' );	
	wp_enqueue_style( 'fontawesome', get_template_directory_uri().'/css/font-awesome.css' );
	wp_enqueue_style( 'themoments-googlefonts', '//fonts.googleapis.com/css?family=Raleway:300,400,500,700,900');
	wp_enqueue_style( 'themoments-style', get_stylesheet_uri() );


	if ( is_rtl() ) {
		wp_enqueue_style( 'themoments-style', get_stylesheet_uri() );
		wp_style_add_data( 'themoments-style', 'rtl', 'replace' );
		wp_enqueue_style( 'themoments-css-rtl', get_template_directory_uri().'/css/bootstrap-rtl.css' );
		wp_enqueue_script( 'themoments-js-rtl', get_template_directory_uri() . '/js/bootstrap.rtl.js', array('jquery'), '1.0.0', true );
	}

	wp_enqueue_script( 'bootstrap', get_template_directory_uri() . '/js/bootstrap.js', array('jquery'), '1.0.0', true );
	wp_enqueue_script( 'themoments-scripts', get_template_directory_uri() . '/js/script.js', array('jquery'), '1.0.0', true );


	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'themoments_scripts' );





/**
 * Set the content width in pixels, based on the theme's design and stylesheet.
 *
 * Priority 0 to make it available to lower priority callbacks.
 *
 * @global int $content_width
 */
if ( ! isset( $content_width ) ) $content_width = 900;
function themoments_content_width() {
	$GLOBALS['content_width'] = apply_filters( 'themoments_content_width', 640 );

}
add_action( 'after_setup_theme', 'themoments_content_width', 0 );


function themoments_filter_front_page_template( $template ) {
    return is_home() ? '' : $template;
}
add_filter( 'frontpage_template', 'themoments_filter_front_page_template' );



/**
 * Register widget area.
 *
 * @link https://developer.wordpress.org/themes/functionality/sidebars/#registering-a-sidebar
 */


function themoments_widgets_init() {
		
	register_sidebar( array(
		'name'          => esc_html__( 'Sidebar', 'themoments' ),
		'id'            => 'sidebar-1',
		'description'   => '',
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h4 class="widget-title">',
		'after_title'   => '</h4>',
	) );

	register_sidebar( array(
			'name'          => esc_html__('Footer One','themoments'),
			'id'            => 'footer-1',
			'description'   => esc_html__('Instagram Widget','themoments'),
			'before_widget' => '<div class="full-widget">',
			'after_widget'  => '</div>',
			'before_title'  => '<h4 class="widget-title">',
			'after_title'   => '</h4>',
		) );
	register_sidebar( array(
			'name'          => esc_html__('Footer Two','themoments'),
			'id'            => 'footer-2',
			'description'   => esc_html__('Contact Widget','themoments'),
			'before_widget' => '<div class="full-widget">',
			'after_widget'  => '</div>',
			'before_title'  => '<h4 class="widget-title">',
			'after_title'   => '</h4>',
		) );
	
}
add_action( 'widgets_init', 'themoments_widgets_init' );


/**
 * Custom template tags for this theme.
 */
require get_template_directory() . '/inc/class.php';

//require get_template_directory() . '/inc/wp_bootstrap_navwalker.php';

/**
 * Implement the Custom Header feature.
 */
require get_template_directory() . '/inc/custom-header.php';

/**
 * Custom template tags for this theme.
 */
require get_template_directory() . '/inc/template-tags.php';

/**
 * Custom functions that act independently of the theme templates.
 */
require get_template_directory() . '/inc/extras.php';

/**
 * Customizer additions.
 */
require get_template_directory() . '/inc/customizer.php';

/**
 * Load Jetpack compatibility file.
 */
require get_template_directory() . '/inc/jetpack.php';

// Register Custom Navigation Walker
require get_template_directory() . '/inc/wp_bootstrap_navwalker.php';

// Load Go to Pro Feature
require_once( trailingslashit( get_template_directory() ) . 'trt-customize-pro/themoments/class-customize.php' );