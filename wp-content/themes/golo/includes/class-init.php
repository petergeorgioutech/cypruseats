<?php 

// Exit if accessed directly.
if ( !defined('ABSPATH') ) {
	exit;
}

/**
 * Initial setup for this theme
 *
 */
class Golo_Init {

	/**
	 * The constructor.
	 */
	function __construct() {

		// class Golo_Helper
		new Golo_Helper();

		// class Golo_Enqueue
		new Golo_Enqueue();

		// class Golo_Kirki
		new Golo_Kirki();

		// class Golo_Customizer
		new Golo_Customizer();

		// class Golo_Ajax
		new Golo_Ajax_Include();

		// Load the theme's textdomain.
		add_action( 'after_setup_theme', array( $this, 'load_theme_textdomain' ) );

		// Register navigation menus.
		add_action( 'after_setup_theme', array( $this, 'register_nav_menus' ) );

		// Add theme supports.
		add_action( 'after_setup_theme', array( $this, 'add_theme_supports' ) );

		// Register nav menu.
		add_action( 'after_setup_theme', array( $this, 'register_menus' ) );

		// Register widget areas.
		add_action( 'widgets_init', array( $this, 'widgets_init' ) );

		// Register head template.
		add_action( 'wp_head', array( $this, 'loading_effect' ), 9999 );

		// Register footer template.
		add_action( 'wp_footer', array( $this, 'global_template' ) );

		// Support editor style.
		add_editor_style(array('/assets/css/editor-style.css'));

		// Support Metabox.
		add_theme_support( 'wc-product-gallery-zoom' );

		add_theme_support( 'wc-product-gallery-lightbox' );

	}

	/**
	 * Registers the Menus.
	 *
	 * @access public
	 */
	public function register_nav_menus() {
		// This theme uses wp_nav_menu() in one location.
		register_nav_menus( array(
			'primary' => esc_html__( 'Primary', 'golo' ),
		) );
	}

	/**
	 * Make theme available for translation.
	 * Translations can be filed in the /languages/ directory.
	 *
	 * @access public
	 */
	public function load_theme_textdomain() {
		load_theme_textdomain( 'golo', GOLO_THEME_DIR . '/languages' );
	}

	/**
	 * Sets up theme defaults and registers support for various WordPress features.
	 *
	 * Note that this function is hooked into the after_setup_theme hook, which
	 * runs before the init hook. The init hook is too late for some features, such
	 * as indicating support for post thumbnails.
	 *
	 * @access public
	 */
	function add_theme_supports() {
		/*
		 * Add default posts and comments RSS feed links to head.
		 */
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
		 * @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
		 */
		add_theme_support( 'post-thumbnails' );

		/*
		 * Switch default core markup for search form, comment form, and comments
		 * to output valid HTML5.
		 */
		add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption' ) );

		/*
		 * Enable support for Post Formats.
		 * See https://developer.wordpress.org/themes/functionality/post-formats/
		 */
		add_theme_support( 'post-formats', array( 'aside', 'image', 'video', 'quote', 'link' ) );

		/*
		 * Set up the WordPress core custom background feature.
		 */
		add_theme_support( 'custom-background', apply_filters( 'custom_background_args', array( 'default-color' => '#ffffff', 'default-image' => '' ) ) );

		/*
		 * Support woocommerce
		 */
		add_theme_support( 'woocommerce' );

		/*
		 * Support selective refresh for widget
		 */
		add_theme_support( 'customize-selective-refresh-widgets' );

		/*
		 * Optimize speed for homepage
		 */
		add_theme_support( 'golo' );

	}

	/**
	 * Register nav menu.
	 */
	function register_menus() {
	    register_nav_menus(array(
	        'primary' => esc_html__('Primary Menu', 'golo'),
	    ));

	    register_nav_menus(array(
	        'main_menu' => esc_html__('Main Menu', 'golo'),
	    ));

	    register_nav_menus(array(
	        'mobile_menu' => esc_html__('Mobile Menu', 'golo'),
	    ));
	}

	/**
	 * Register widget area.
	 *
	 * @access public
	 * @link   https://developer.wordpress.org/themes/functionality/sidebars/#registering-a-sidebar
	 */
	function widgets_init() {
		register_sidebar( array(
			'id'            => 'sidebar',
			'name'          => esc_html__( 'Sidebar', 'golo' ),
			'description'   => esc_html__( 'Add widgets here.', 'golo' ),
			'before_widget' => '<section id="%1$s" class="widget %2$s">',
			'after_widget'  => '</section>',
			'before_title'  => '<h3 class="widget-title">',
			'after_title'   => '</h3>',
		) );
		register_sidebar(
			array(
				'id'            => 'footer',
				'name'          => esc_html__( 'Footer', 'golo' ),
				'description'   => esc_html__( 'Add widgets here.', 'golo' ),
				'before_widget' => '<section id="%1$s" class="widget %2$s">',
				'after_widget'  => '</section>',
				'before_title'  => '<h3 class="widget-title">',
				'after_title'   => '</h3>',
			)
		);
		register_sidebar( array(
			'id'            => 'place_sidebar',
			'name'          => esc_html__( 'Place Sidebar', 'golo' ),
			'description'   => esc_html__( 'Add widgets here.', 'golo' ),
			'before_widget' => '<section id="%1$s" class="widget %2$s">',
			'after_widget'  => '</section>',
			'before_title'  => '<h3 class="widget-title">',
			'after_title'   => '</h3>',
		) );
		register_sidebar( array(
			'id'            => 'shop_sidebar',
			'name'          => esc_html__( 'Shop Sidebar', 'nuss' ),
			'description'   => esc_html__( 'Add widgets here.', 'nuss' ),
			'before_widget' => '<section id="%1$s" class="widget %2$s">',
			'after_widget'  => '</section>',
			'before_title'  => '<h3 class="widget-title">',
			'after_title'   => '</h3>',
		) );
		register_sidebar(
			array(
				'id'            => 'copyright-01',
				'name'          => esc_html__( 'Copyright 01', 'golo' ),
				'description'   => esc_html__( 'Add widgets here.', 'golo' ),
				'before_widget' => '<section id="%1$s" class="widget %2$s">',
				'after_widget'  => '</section>',
				'before_title'  => '<h3 class="widget-title">',
				'after_title'   => '</h3>',
			)
		);
		register_sidebar(
			array(
				'id'            => 'copyright-02',
				'name'          => esc_html__( 'Copyright 02', 'golo' ),
				'description'   => esc_html__( 'Add widgets here.', 'golo' ),
				'before_widget' => '<section id="%1$s" class="widget %2$s">',
				'after_widget'  => '</section>',
				'before_title'  => '<h3 class="widget-title">',
				'after_title'   => '</h3>',
			)
		);
	}

	/**
	 * Register global template
	 */
	function loading_effect() {

	    get_template_part( 'templates/global/site-loading' );

	}

	/**
	 * Register global template
	 */
	function global_template() {

		get_template_part( 'templates/global/account' );

		get_template_part( 'templates/global/canvas-search' );
	}
}