<?php 

if ( ! class_exists( 'Golo_Base_Elementor_Widget' ) ) {

	class Golo_Base_Elementor_Widget {

	    function __construct()
	    {
	        add_action('elementor/init', array($this, 'widget_add_section') );
	        add_action('elementor/widgets/widgets_registered', array($this, 'widget_register') );
	        add_action('elementor/frontend/after_enqueue_scripts', array($this, 'enqueue_script'), 10 );
	        add_action('elementor/frontend/after_enqueue_styles', array($this, 'enqueue_frontend_styles'), 10);
	        add_action('elementor/editor/after_enqueue_styles', array( $this, 'elementor_editor_styles' ) );

	        add_action('elementor/widget/posts/skins_init', array($this, 'add_skin') );

	        add_filter('elementor/icons_manager/additional_tabs', array($this, 'add_icons_library') );

	        add_filter('elementor/shapes/additional_shapes', array($this , 'add_shapes_devide') );

	        add_action('get_header', array($this, 'handle_theme_support'), 8 );
	    }

	    /**
		 * Register Widgets
		 *
		 * Register new Elementor widgets.
		 */
	    public function widget_register() {
	        require_once ( GOLO_PLUGIN_DIR . 'modules/elementor/includes/places.php' );
	        require_once ( GOLO_PLUGIN_DIR . 'modules/elementor/includes/place-search.php' );
	        require_once ( GOLO_PLUGIN_DIR . 'modules/elementor/includes/place-categories.php' );
	        require_once ( GOLO_PLUGIN_DIR . 'modules/elementor/includes/city.php' );
	        require_once ( GOLO_PLUGIN_DIR . 'modules/elementor/includes/countdown.php' );
	        require_once ( GOLO_PLUGIN_DIR . 'modules/elementor/includes/cities.php' );
	        require_once ( GOLO_PLUGIN_DIR . 'modules/elementor/includes/dropdown-cities.php' );
	        require_once ( GOLO_PLUGIN_DIR . 'modules/elementor/includes/nav-menu.php' );
	        require_once ( GOLO_PLUGIN_DIR . 'modules/elementor/includes/user-manager.php' );
	        require_once ( GOLO_PLUGIN_DIR . 'modules/elementor/includes/testimonial-carousel.php' );

	        if ( class_exists('WooCommerce') ) {
				require_once ( GOLO_PLUGIN_DIR . 'modules/elementor/includes/canvas-cart.php' );
			}
	    }

		private function get_theme_builder_module() {
			return \ElementorPro\Modules\ThemeBuilder\Module::instance();
		}

		private function get_theme_support_instance() {
			$module = $this->get_theme_builder_module();
			return $module->get_component( 'theme_support' );
		}

		public function handle_theme_support() {
			/**
			 * @var \ElementorPro\Modules\ThemeBuilder\Module $module
			 */
			$module = $this->get_theme_builder_module();
			$conditions_manager = $module->get_conditions_manager();
			$headers = $conditions_manager->get_documents_for_location( 'header' );
			$footers = $conditions_manager->get_documents_for_location( 'footer' );
			
			if ( empty( $headers ) ) {
				// only $headers is empty so remove the theme support header
				$this->remove_theme_support_action( 'header' );
			} 

			if ( empty( $footers ) ) {
				// only footer is empty so remove the theme support footer
				$this->remove_theme_support_action( 'footer' );
			}
		}

		public function remove_theme_support_action( $action ) {
			$handler = 'get_' . $action;
			$instance = $this->get_theme_support_instance();
			remove_action( $handler, [ $instance, $handler ] );
		}

	    /**
		 * Sections
		 *
		 * Create new section on elementor
		 */
	    public function widget_add_section() {

	        Elementor\Plugin::instance()->elements_manager->add_category(
	            'golo-framework',
	            array(
	                'title'  => __('Golo Framework', 'golo-framework'),
	                'active' => false,
	            ),
	            1
	        );

	    }

	    public function enqueue_script(){
	        wp_enqueue_script('widget-scripts', GOLO_PLUGIN_URL . 'modules/elementor/assets/js/widget.js', array(),  false, true );
	        wp_enqueue_script('countdown-timer-script', GOLO_PLUGIN_URL . 'modules/elementor/assets/js/jquery.countdownTimer.js', array(), '1.0.0', true);
	    }

		public function elementor_editor_styles(){
	        wp_enqueue_style('editor-style', GOLO_PLUGIN_URL . 'modules/elementor/assets/css/editor.css', array(), GOLO_THEME_VERSION );
	    }

	    public function enqueue_frontend_styles(){
	        wp_enqueue_style('widget-style', GOLO_PLUGIN_URL . 'modules/elementor/assets/css/widget.css', array(), GOLO_THEME_VERSION );
	        wp_enqueue_style('countdown-timer-style', GOLO_PLUGIN_URL . 'modules/elementor/assets/css/countdown-timer-widget.css', true);
	    }

	    public function add_skin( $widget ) {

		    require_once ( GOLO_PLUGIN_DIR . 'modules/elementor/classes/posts-categories.php' );

		    $widget->add_skin( new Golo_Posts_Categories( $widget ) );

		}

	    public function add_icons_library(){
            return [
                'la' => [
					'name'          => 'line_awesome',
					'label'         => __( 'Line Awesome', 'golo-framework' ),
					'url'           => GOLO_PLUGIN_URL . 'assets/libs/line-awesome/css/line-awesome.min.css',
					'enqueue'       => [ GOLO_PLUGIN_URL . 'assets/libs/line-awesome/css/line-awesome.min.css' ],
					'prefix'        => '',
					'displayPrefix' => '',
					'labelIcon'     => '',
					'ver'           => '1.0.1',
					'fetchJson'     =>  GOLO_PLUGIN_URL . 'assets/libs/line-awesome/line-awesome.json',
					'native'        => true,
                ]
            ];
        }

        public function add_shapes_devide(){
            $additional_shapes['oval'] = [
				'title'        => _x( 'Oval', 'Shapes', 'golo-framework' ),
				'has_negative' => true,
				'path'         => GOLO_PLUGIN_DIR . 'modules/elementor/assets/images/oval.svg',
				'url'          => GOLO_PLUGIN_URL . 'modules/elementor/assets/images/oval.svg',
			];

			return $additional_shapes;
        }
	}

	new Golo_Base_Elementor_Widget();
}