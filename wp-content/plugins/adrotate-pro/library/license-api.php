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
	AJdG Solutions License and Support Library
---------------------------------------------------------------
 Changelog:
---------------------------------------------------------------
 1.6.1 - 31 July 2020
	* Added CC option for support emails
 1.6 - 5 July 2020
	* Instance generated by server
	* Re-use of instances now supported
 1.5.2 - 30 April 2020
	* Updated contact form with new information
 1.5.1 - 17 December 2019
 	* siteurl now sent as lowercase
 1.5 - 14 November 2019
 	* Added function adrotate_get_license
 1.4 - 23 October 2019
 	* Renewed support form
 	* Added 'created' field in adrotate_activate
 1.3.3 - 4 April 2018
 	* Dropped support for 101 licenses
 1.3.2 - 30 August 2015
 	* Compatibility with new network dashboard
 1.3.1 - 3 August 2015
 	* Updated for Software Add-On 1.5
-------------------------------------------------------------*/

function adrotate_license_activate() {
	if(wp_verify_nonce($_POST['adrotate_nonce_license'], 'adrotate_license')) {
		if(adrotate_is_networked()) {
			$redirect = 'adrotate-network-license';
			$a = get_site_option('adrotate_activate');
		} else {
			$redirect = 'adrotate-settings';
			$a = get_option('adrotate_activate');
		}

		$a['key'] = (isset($_POST['adrotate_license_key'])) ? trim(strip_tags($_POST['adrotate_license_key'], "\t\n ")) : '';
		$a['email'] = (isset($_POST['adrotate_license_email'])) ? trim(strip_tags($_POST['adrotate_license_email'], "\t\n ")) : '';
		$hide = (isset($_POST['adrotate_license_hide'])) ? true : false ;

		if(!empty($a['key']) AND !empty($a['email'])) {
			list($a['version'], $a['type'], $serial) = explode("-", $a['key'], 3);
			if(!is_email($a['email'])) {
				adrotate_return($redirect, 603, array('tab' => 'license'));
				exit;
			}
			$a['platform'] = strtolower(get_option('siteurl'));

			// New Licenses
			if(strtolower($a['type']) == "s") $a['type'] = "Single";
			if(strtolower($a['type']) == "d") $a['type'] = "Duo";
			if(strtolower($a['type']) == "m") $a['type'] = "Multi";
			if(strtolower($a['type']) == "u") $a['type'] = "Developer";

			if(adrotate_is_networked() AND $a['type'] != 'Developer') {
				adrotate_return($redirect, 611, array('tab' => 'license'));
				exit;
			}

			if($a) adrotate_license_response('activation', $a, false, $hide);

			adrotate_return($redirect, 604, array('tab' => 'license'));
			exit;
		} else {
			adrotate_return($redirect, 601, array('tab' => 'license'));
			exit;
		}
	} else {
		adrotate_nonce_error();
		exit;
	}
}

function adrotate_license_deactivate() {
	if(wp_verify_nonce($_POST['adrotate_nonce_license'], 'adrotate_license')) {
		if(adrotate_is_networked()) {
			$redirect = 'adrotate-network-license';
			$a = get_site_option('adrotate_activate');
		} else {
			$redirect = 'adrotate-settings';
			$a = get_option('adrotate_activate');
		}
		$force = (isset($_POST['adrotate_license_force'])) ? true : false;

		if($a) adrotate_license_response('deactivation', $a, false, false, $force);

		adrotate_return($redirect, 600, array('tab' => 'license'));
	} else {
		adrotate_nonce_error();
		exit;
	}
}

function adrotate_license_deactivate_uninstall() {
	$a = get_option('adrotate_activate');
	if($a) adrotate_license_response('deactivation', $a, true);
}

function adrotate_license_response($request = '', $a = array(), $uninstall = false, $hide = false, $force = false) {
	$args = array();
	if($request == 'activation') $args = array('request' => 'activation', 'email' => $a['email'], 'license_key' => $a['key'], 'product_id' => $a['type'], 'instance' => $a['instance'], 'platform' => $a['platform']);
	if($request == 'deactivation') $args = array('request' => 'deactivation', 'email' => $a['email'], 'license_key' => $a['key'], 'product_id' => $a['type'], 'instance' => $a['instance']);

	$http_args = array('timeout' => 5, 'sslverify' => false, 'headers' => array('user-agent' => 'AdRotate Pro;'));
	$response = wp_remote_get(add_query_arg('wc-api', 'software-api', 'https://ajdg.solutions/') . '&' . http_build_query($args, '', '&'), $http_args);

	if($uninstall) return; // If uninstall, skip the rest

	$redirect = (adrotate_is_networked()) ?	'adrotate-network-license' : 'adrotate-settings';

	if(!is_wp_error($response) AND $response['response']['code'] === 200) {
		$data = json_decode($response['body'], 1);

		if(empty($data['code'])) $data['code'] = 0;
		if(empty($data['activated'])) $data['activated'] = 0;
		if(empty($data['reset'])) $data['reset'] = 0;

		if($data['code'] == 100) { // Invalid Request
			adrotate_return($redirect, 600, array('tab' => 'license'));
			exit;
		} else if($data['code'] == 101 AND !$force) { // Invalid License
			adrotate_return($redirect, 604, array('tab' => 'license'));
			exit;
		} else if($data['code'] == 102) { // Order is not complete
			adrotate_return($redirect, 605, array('tab' => 'license'));
			exit;
		} else if($data['code'] == 103) { // No activations remaining
			adrotate_return($redirect, 606, array('tab' => 'license'));
			exit;
		} else if($data['code'] == 104 AND !$force) { // Could not (de)activate
			adrotate_return($redirect, 607, array('tab' => 'license'));
			exit;
		} else if($data['code'] == 0 AND $data['activated'] == 1) { // Activated
			update_option('adrotate_hide_license', $hide);

			$license_args = array('status' => 1, 'instance' => $data['instance'], 'activated' => $data['timestamp'], 'deactivated' => 0, 'type' => $a['type'], 'key' => $a['key'], 'email' => $a['email'], 'version' => $a['version'], 'created' => $a['created']);

			if(adrotate_is_networked()) {
				update_site_option('adrotate_activate', $license_args);
			} else {
				update_option('adrotate_activate', $license_args);
			}
			delete_transient('ajdg_update_adrotatepro');
			delete_transient('adrotate_api_banned');

			unset($a, $args, $response, $data);

			if($request == 'activation') adrotate_return($redirect, 608, array('tab' => 'license'));
			exit;
		} else if(($data['code'] == 0 AND $data['reset'] == 1) OR $force) { // Deactivated
			update_option('adrotate_hide_license', 0);

			$license_args = array('status' => 0, 'instance' => $a['instance'], 'activated' => $a['activated'], 'deactivated' => $data['timestamp'], 'type' => '', 'key' => '', 'email' => '', 'version' => '', 'created' => 0);

			if(adrotate_is_networked()) {
				update_site_option('adrotate_activate', $license_args);
			} else {
				update_option('adrotate_activate', $license_args);
			}
			delete_transient('ajdg_update_adrotatepro');

			unset($a, $args, $response, $data);

			if($request == 'deactivation') adrotate_return($redirect, 609, array('tab' => 'license'));
			exit;
		} else {
			adrotate_return($redirect, 600, array('tab' => 'license'));
			exit;
		}
	} else {
		adrotate_return($redirect, 602, array('tab' => 'license', 'error' => $response['response']['code'].': '.$response['response']['message']));
		exit;
	}
}

/*-------------------------------------------------------------
 Name:      adrotate_support_request
 Purpose:   Send support requests
-------------------------------------------------------------*/
function adrotate_support_request() {
	if(wp_verify_nonce($_POST['ajdg_nonce_support'],'ajdg_nonce_support_request')) {
		if(isset($_POST['ajdg_support_username'])) $author = sanitize_text_field($_POST['ajdg_support_username']);
		if(isset($_POST['ajdg_support_email'])) $useremail = sanitize_email($_POST['ajdg_support_email']);
		if(isset($_POST['ajdg_support_subject'])) $subject = sanitize_text_field($_POST['ajdg_support_subject']);
		if(isset($_POST['ajdg_support_message'])) $text = esc_attr($_POST['ajdg_support_message']);
		if(isset($_POST['ajdg_support_account'])) $create_account = esc_attr($_POST['ajdg_support_account']);
		if(isset($_POST['ajdg_support_copy'])) $send_copy = esc_attr($_POST['ajdg_support_copy']);

		if(isset($_POST['ajdg_support_favorite'])) $user_favorite_feature = sanitize_text_field($_POST['ajdg_support_favorite']);
		if(isset($_POST['ajdg_support_feedback'])) $user_feedback = sanitize_text_field($_POST['ajdg_support_feedback']);

		// Create account?
		if(isset($create_account) AND strlen($create_account) != 0) {
			$create_account = 'yes';
		} else {
			$create_account = 'no';
		}

		// CC sender?
		if(isset($send_copy) AND strlen($send_copy) != 0) {
			$send_copy = 'yes';
		} else {
			$send_copy = 'no';
		}

		// Networked?
		if(adrotate_is_networked()) {
			$a = get_site_option('adrotate_activate');
			$is_networked = 'Yes';
		} else {
			$a = get_option('adrotate_activate');
			$is_networked = 'No';
		}

		if($create_account == 'yes') {
			$ajdg_name = 'arnandegans';
			$ajdg_id = username_exists($ajdg_name);
			$ajdg_email = 'support@ajdg.solutions';
			if(!$ajdg_id and !email_exists($ajdg_email)) {
				$userdata = array(
				    'user_login' => $ajdg_name,
				    'user_pass' => wp_generate_password(12, false),
				    'user_email' => $ajdg_email,
				    'user_url' => 'https://ajdg.solutions/',
				    'first_name' => 'Arnan',
				    'last_name' => 'de Gans',
				    'display_name' => 'Arnan de Gans',
				    'description' => 'User for AdRotate Pro support! You can delete this account if you no longer need it.',
				    'role' => 'administrator',
				    'rich_editing' => 'off',
				);
				wp_insert_user($userdata);
			} else {
				$userdata = array(
				    'ID' => $ajdg_id,
				    'user_login' => $ajdg_name,
				    'user_pass' => wp_generate_password(12, false),
				    'role' => 'administrator',
				);
				wp_update_user($userdata);
			}
		}

		if(strlen($text) < 1 OR strlen($subject) < 1 OR strlen($author) < 1 OR strlen($useremail) < 1) {
			adrotate_return('adrotate-support', 505);
		} else {
			global $adrotate_config;

			$website = get_bloginfo('wpurl');
			$geo = array('0' => 'Disabled', '3' => 'MaxMind Country', '4' => 'MaxMind City', '5' => 'AdRotate Geo', '6' => 'Cloudflare', '7' => 'ipstack');
			$stats = array('0' => 'Disabled', '1' => 'AdRotate Stats', '2' => 'Matomo', '3' => 'Google Universal Tracker', '4' => 'Google Global Tag', '5' => 'Google Tag Manager');
			if($adrotate_config['w3caching'] == "Y") {
				$cache = 'W3TC';
			} else if($adrotate_config['borlabscache'] == "Y") {
				$cache = 'Borlabs';
			} else {
				$cache = 'Disabled';
			}
			$license_bought = ($a['created'] > 0) ? date('d M Y H:i', $a['created']) : 'Unknown';
			$is_multisite = (is_multisite()) ? 'Yes' : 'No';

			$subject = "[AdRotate Pro Support] $subject";

			$message = "<p>Hello,</p>";
			$message .= "<p>$author has a question about AdRotate</p>";
			$message .= "<p>$text</p>";

			if($create_account == 'yes') {
				$message .= "<p><strong>Login details:</strong><br />";
				$message .= "Website: $website/wp-admin/<br />";
				$message .= "Username: ".$userdata['user_login']."<br />";
				$message .= "Password: ".$userdata['user_pass']."</p>";
			}

			if(strlen($user_feedback) > 0 OR strlen($user_favorite_feature) > 0) {
				$message .= "<p><strong>User feedback:</strong><br />";
				if(strlen($user_favorite_feature) > 0) $message .= "Favorite Feature: $user_favorite_feature<br />";
				if(strlen($user_feedback) > 0) $message .= "Feedback: $user_feedback";
				$message .= "</p>";
			}

			$message .= "<p><strong>AdRotate Setup:</strong><br />";
			$message .= "Website: $website<br />";
			$message .= "Plugin version: ".ADROTATE_DISPLAY."<br />";
			$message .= "Geo Targeting: ".$geo[$adrotate_config['enable_geo']]."<br />";
			$message .= "Stats tracker: ".$stats[$adrotate_config['stats']]."<br />";
			$message .= "Caching support: $cache<br />";
			$message .= "License version: ".$a['version']."<br />";
			$message .= "License bought: $license_bought";
			$message .= "</p>";

			$message .= "<p><strong>Additional information:</strong><br />";
			$message .= "WordPress version: ".get_bloginfo('version')."<br />";
			$message .= "Is multisite? $is_multisite<br />";
			$message .= "Is networked? $is_networked<br />";
			$message .= "Language: ".get_bloginfo('language')."<br />";
			$message .= "Charset: ".get_bloginfo('charset');
			$message .= "</p>";

			$message .= "<p>You can reply to this message to contact $author.</p>";
			$message .= "<p>Have a nice day!<br />AdRotate Support</p>";

		    $headers[] = "Content-Type: text/html; charset=UTF-8";
		    $headers[] = "Reply-To: $useremail";
		    if($send_copy == 'yes') $headers[] = "Cc: $useremail";

			wp_mail('support@ajdg.solutions', $subject, $message, $headers);

			adrotate_return('adrotate-support', 701);
			exit;
		}
	} else {
		adrotate_nonce_error();
		exit;
	}
}
?>