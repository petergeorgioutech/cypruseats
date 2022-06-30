<?php
/**
 * Fired during plugin deactivation
 *
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if (!class_exists('Golo_Deactivator')) {
	require_once GOLO_PLUGIN_DIR . 'includes/admin/class-golo-schedule.php';
	/**
	 * Fired during plugin deactivation
	 * Class Golo_Deactivator
	 */
	class Golo_Deactivator
	{
		/**
		 * Run when plugin deactivated
		 */
		public static function deactivate()
		{
		 	Golo_Schedule::clear_scheduled_hook();
		}
	}
}