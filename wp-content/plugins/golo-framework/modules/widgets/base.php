<?php
if (!class_exists('Golo_Base_Widget')) {
	class Golo_Base_Widget {

		public function __construct(){
			add_action('widgets_init', array($this,'register_widget'), 1);
			$this->includes();
			spl_autoload_register(array($this,'autoload'));
		}

		public function autoload($class_name) {
			$class = preg_replace('/^Golo_Widget_/', '', $class_name);
			if ($class != $class_name) {
				$class = str_replace('_', '-', $class);
				$class = strtolower($class);
				include_once( GOLO_PLUGIN_DIR . 'modules/widgets/includes/' . $class .'.php');
			}
		}

		private function includes(){
			include_once( GOLO_PLUGIN_DIR . 'modules/widgets/widget-config.php' );
		}
		
		public function register_widget(){
			if ( class_exists( 'WooCommerce' ) ) {
				register_widget('Golo_Widget_Products');
			}
			register_widget('Golo_Widget_Popular_Posts');
		}
	}
	
	new Golo_Base_Widget();
}