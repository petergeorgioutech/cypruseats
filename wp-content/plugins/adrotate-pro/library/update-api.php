<?php
/* ------------------------------------------------------------------------------------
*  COPYRIGHT AND TRADEMARK NOTICE
*  Copyright 2012-2020 Arnan de Gans. All Rights Reserved.
*  ADROTATE is a trademark of Arnan de Gans.

*  COPYRIGHT NOTICES AND ALL THE COMMENTS SHOULD REMAIN INTACT.
*  By using this code you agree to indemnify Arnan de Gans from any
*  liability that might arise from it's use.
------------------------------------------------------------------------------------ */

/*-------------------------------------------------------------
 AJdG Solutions Update Library
---------------------------------------------------------------
 Changelog:
---------------------------------------------------------------
2.1.4 - 22 March 2022
	* Better error handling for server responses
2.1.3 - 15 December 2020
	* Better error handling for server responses
2.1.2 - 31 July 2020
	* Better WordPress 5.5 compatibility
2.1.1 - 14 July 2020
	* Better error handling, comm status visible in Settings > Maintenance
2.1 - 5 July 2020
	* Smaller data request (speed increase)
2.0.1 - 5 May 2020
	* Improved error checking for invalid responses
	* Request timeout increased to 5 seconds
	* Optimized all code
	* Transient is valid for 1 days to exceed WP checks
2.0 - 17 February 2020
	* Now uses API 8
	* Standardized API requests
	* Optimized all code
1.5.2 - 11 January 2020
	* Replaced 12 hour schedule for update checks with transient (similar to version 1.5)
	* Transient is valid for 12 hours
1.5.1 - 1 December 2019
	* Replaced $response['new_version'] with $response['version'] in adrotate_update_check()
1.5 - 20 October 2019
	* Redone most code
	* More efficient/lean data for showing updates
	* 12 Hour caching cycle with transients (to reduce API requests)
1.4 - 1 July2019
	* Now uses API 7
	* Improved error checking
	* Improved backwards compatibility for older PHP versions
1.3.1 - 27 June 2019
	* Added error checking for update and info requests
1.3 - 29 May 2019
	* All new basic_check request
	* All new plugin_information request
1.2.4 - 21 February 2019
	* Fixed slug not listed correctly on line 72
1.2.3 - 4 April 2018
	* Dropped support for 101 licenses
1.2.2 - 28 February 2016
	* changed unserialize() into maybe_unserialize() on line 49
1.2.1 - 5 June 2015
	* Added extra check if plugin exists in update array
-------------------------------------------------------------*/

function adrotate_licensed_update() {
	add_filter('site_transient_update_plugins', 'adrotate_get_update_information');
	add_filter('plugins_api', 'adrotate_get_plugin_information', 10, 3); // Plugin info popup
}

/*-------------------------------------------------------------
 Name:      adrotate_update_check
 Purpose:   Get plugin data from ajdg.solutions
-------------------------------------------------------------*/
function adrotate_update_check() {
	$data = get_transient('ajdg_update_adrotatepro');

	if(!$data) {
		$license = adrotate_get_license();
		$plugins = get_plugins();
		$plugin_version = $plugins['adrotate-pro/adrotate-pro.php']['Version'];

		$request = array('slug' => "adrotate-pro", 'instance' => $license['instance'], 'platform' => strtolower(get_option('siteurl')), 'action' => 'basic_check', 'et' => microtime(true));
		$args = array('headers' => array('Accept' => 'multipart/form-data'), 'body' => array('r' => serialize($request)), 'user-agent' => 'AdRotate Pro/'.$plugin_version.';', 'sslverify' => false, 'timeout' => 5);

		$response = wp_remote_post('https://ajdg.solutions/api/updates/8/', $args);

	    if(!is_wp_error($response)) {
			$data = json_decode($response['body'], 1);

			if(!is_array($data)) $data = array();
			
			$data['slug'] = (array_key_exists('slug', $data)) ? $data['slug'] : '';
			$data['name'] = (array_key_exists('name', $data)) ? $data['name'] : '';
			$data['release_date'] = (array_key_exists('release_date', $data)) ? $data['release_date'] : '';
			$data['version'] = (array_key_exists('version', $data)) ? $data['version'] : 0;
			$data['tested'] = (array_key_exists('tested', $data)) ? $data['tested'] : '4.6';
			$data['requires_wp'] = (array_key_exists('requires_wp', $data)) ? $data['requires_wp'] : '4.6';
			$data['requires_php'] = (array_key_exists('requires_php', $data)) ? $data['requires_php'] : '5.6';
			$data['author'] = (array_key_exists('author', $data)) ? $data['author'] : '';
			$data['donate_link'] = (array_key_exists('donate_link', $data)) ? $data['donate_link'] : '';
			$data['plugin_url'] = (array_key_exists('plugin_url', $data)) ? $data['plugin_url'] : '';
			$data['download_url'] = (array_key_exists('download_url', $data)) ? $data['download_url'] : '';
			$data['active_installs'] = (array_key_exists('active_installs', $data)) ? $data['active_installs'] : 0;
			$data['upgrade_note'] = (array_key_exists('upgrade_note', $data)) ? $data['upgrade_note'] : '';

			if(array_key_exists('icons', $data)) {
				$data['icons']['low'] = (array_key_exists('low', $data['icons'])) ? $data['icons']['low'] : '';
				$data['icons']['high'] = (array_key_exists('high', $data['icons'])) ? $data['icons']['high'] : '';
			} else {
				$data['icons'] = array('low' => '', 'high' => '');
			}

			if(array_key_exists('banners', $data)) {
				$data['banners']['low'] = (array_key_exists('low', $data['banners'])) ? $data['banners']['low'] : '';
				$data['banners']['high'] = (array_key_exists('high', $data['banners'])) ? $data['banners']['high'] : '';
			} else {
				$data['banners'] = array('low' => '', 'high' => '');
			}

			if(array_key_exists('sections', $data)) {
				$data['sections']['description'] = (array_key_exists('description', $data['sections'])) ? $data['sections']['description'] : '';
				$data['sections']['changelog'] = (array_key_exists('changelog', $data['sections'])) ? $data['sections']['changelog'] : '';
			} else {
				$data['sections'] = array('description' => 'Visit <a href="https://ajdg.solutions/" target="_blank">ajdg.solutions</a>.', 'changelog' => 'Visit <a href="https://ajdg.solutions/" target="_blank">ajdg.solutions</a>.');
			}

			$data['created'] = (array_key_exists('created', $data)) ? $data['created'] : 0;

			// Store response
			set_transient('ajdg_update_adrotatepro', $data, 43200); // 12 hours

		    if($response['response']['code'] === 200) { // Show the good news
				set_transient('ajdg_update_response', array('code' => $response['response']['code'], 'message' => $response['response']['message'], 'last_checked' => date_i18n('F j, Y, g:i a')), 43190); // 11:50 hours
			} else { // Show the bad news
				set_transient('ajdg_update_response', array('code' => $data['code'], 'message' => $data['error'], 'last_checked' => date_i18n('F j, Y, g:i a')), 43190); // 11:50 hours
				if($data['code'] == 403) set_transient('adrotate_api_banned', $data['code'], 172790); //47:50 hours
			}
		} 
	}

	return $data;
}

/*-------------------------------------------------------------
 Name:      adrotate_get_update_information
 Purpose:   Tell WordPress if there is an update
-------------------------------------------------------------*/
function adrotate_get_update_information($transient) {
	$plugins = get_plugins();

	if(empty($transient->checked)) return $transient;
	if(!array_key_exists('adrotate-pro/adrotate-pro.php', $plugins)) return $transient;

	$data = adrotate_update_check();
	if($data) {
		$license = adrotate_get_license();

		if($data['created'] > 0 AND $license['created'] != $data['created']) {
			$license['created'] = $data['created'];
			if(adrotate_is_networked()) {
				update_site_option('adrotate_activate', $license);
			} else {
				update_option('adrotate_activate', $license);
			}
		}

		$result = new stdClass();
		$result->id = "adrotate-pro/adrotate-pro.php"; // Not required
		$result->slug = $data['slug'];
		$result->plugin = "adrotate-pro/adrotate-pro.php";
		$result->new_version = $data['version'];
		$result->tested = $data['tested'];
		$result->requires = $data['requires_wp'];
		$result->requires_php = $data['requires_php'];
		$result->package = $data['download_url'];
		$result->url = $data['plugin_url'];
		$result->upgrade_notice = "<strong>Update Summary:</strong> ".$data['upgrade_note'];
		$result->icons = array('1x' => $data['icons']['low'], '2x' => $data['icons']['high']);
		$result->banners = array('low' => $data['banners']['low'], 'high' => $data['banners']['high']);
		$result->banners_rtl = array('low' => $data['banners']['low'], 'high' => $data['banners']['high']); // Same as banners

		$plugin_version = $plugins['adrotate-pro/adrotate-pro.php']['Version'];
		if(version_compare($plugin_version, $data['version'], '<') AND version_compare(get_bloginfo('version'), $data['requires_wp'], '>=')) {
			$transient->response[$result->plugin] = $result;
			$transient->checked[$result->plugin] = $data['version'];
		} else {
			$transient->no_update[$result->plugin] = $result;
		}
	}

	return $transient;
}

/*-------------------------------------------------------------
 Name:      adrotate_get_plugin_information
 Purpose:   Grab plugin_information from transient data
-------------------------------------------------------------*/
function adrotate_get_plugin_information($false, $action, $args) {
	if($action !== 'plugin_information') return false;
	if($args->slug != "adrotate-pro") return $false;

	$data = adrotate_update_check();
	if($data) {
		$result = new stdClass();
		$result->name = $data['name'];
		$result->slug = $data['slug'];
		$result->version = $data['version'];
		$result->tested = $data['tested'];
		$result->requires = $data['requires_wp'];
		$result->requires_php = $data['requires_php'];
		$result->download_link = $data['download_url'];
		$result->last_updated = $data['release_date'];
		$result->active_installs = $data['active_installs'];
		$result->author = $data['author'];
		$result->homepage = $data['plugin_url'];
		$result->donate_link = $data['donate_link'];
		$result->banners = array('low' => $data['banners']['low'], 'high' => $data['banners']['high']);
		$result->sections = array(
			'description' => stripslashes($data['sections']['description']),
			'changelog' => $data['sections']['changelog']
		);

		return $result;
	}

	return false;
}

/*-------------------------------------------------------------
 Name:      adrotate_update_finished
 Purpose:   Run updater after updating automatically/via the dashboard
-------------------------------------------------------------*/
function adrotate_update_finished($upgrader_object, $options) {
	if($options['action'] == 'update' AND $options['type'] === 'plugin' AND isset($options['plugins']))  {
		foreach($options['plugins'] as $plugin) {
			if($plugin == 'adrotate-pro/adrotate-pro.php') {
				$tomorrow = current_time('timestamp') + (1 * DAY_IN_SECONDS);
				update_option('adrotate_hide_update', $tomorrow);
				adrotate_check_upgrade(); // Check/run database update and settings
			}
		}
	}
}
?>