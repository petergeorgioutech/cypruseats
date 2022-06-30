<?php

if ( !defined( 'ABSPATH' ) ) {
	exit;
}
if ( !class_exists('Golo_Shortcode_Place') ) {
	/**
	 * Class Golo_Shortcode_Place
	 */
	class Golo_Shortcode_Place
	{

		/**
		 * Constructor.
		 */
		public function __construct()
		{
			add_shortcode('golo_dashboard', array($this, 'dashboard'));
			add_shortcode('golo_my_places', array($this, 'my_places'));
			add_shortcode('golo_submit_place', array($this, 'submit_place'));
			add_shortcode('golo_my_profile', array($this, 'my_profile'));
			add_shortcode('golo_my_wishlist', array($this, 'my_wishlist'));
			add_shortcode('golo_my_booking', array($this, 'my_booking'));
			add_shortcode('golo_bookings', array($this, 'bookings'));
			add_shortcode('golo_packages', array($this, 'package'));
			add_shortcode('golo_payment', array($this, 'payment'));
			add_shortcode('golo_payment_completed', array($this, 'payment_completed'));
			add_shortcode('golo_country', array($this, 'country'));
		}

		/**
		 * Dashboard
		 */
		public function dashboard()
		{
			ob_start();
				golo_get_template('place/dashboard.php');
			return ob_get_clean();
		}

		/**
		 * My places
		 */
		public function my_places()
		{
			ob_start();
				golo_get_template('place/my-places.php');
			return ob_get_clean();
		}

		/**
		 * Submit place
		 */
		public function submit_place()
		{
			ob_start();
				golo_get_template('place/place-submit.php');
			return ob_get_clean();
		}

		/**
		 * My profile
		 */
		public function my_profile()
		{
			ob_start();
				golo_get_template('place/my-profile.php');
			return ob_get_clean();
		}

		/**
		 * My whishlist
		 */
		public function my_wishlist()
		{
			ob_start();
				golo_get_template('place/my-wishlist.php');
			return ob_get_clean();
		}

		/**
		 * My booking
		 */
		public function my_booking()
		{
			ob_start();
				golo_get_template('place/my-booking.php');
			return ob_get_clean();
		}

		/**
		 * Bookings
		 */
		public function bookings()
		{
			ob_start();
				golo_get_template('place/bookings.php');
			return ob_get_clean();
		}

		/**
		 * My package
		 */
		public function package()
		{
			ob_start();
				golo_get_template('package/package.php');
			return ob_get_clean();
		}

		/**
		 * My payment
		 */
		public function payment()
		{
			ob_start();
				golo_get_template('payment/payment.php');
			return ob_get_clean();
		}

		/**
		 * My payment completed
		 */
		public function payment_completed()
		{
			ob_start();
				golo_get_template('payment/payment-completed.php');
			return ob_get_clean();
		}

		/**
		 * Single country
		 */
		public function country()
		{
			ob_start();
				golo_get_template('single-country.php');
			return ob_get_clean();
		}
	}

	new Golo_Shortcode_Place();
}