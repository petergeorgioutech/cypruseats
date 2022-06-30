<?php
/* ------------------------------------------------------------------------------------
*  COPYRIGHT AND TRADEMARK NOTICE
*  Copyright 2008-2022 Arnan de Gans. All Rights Reserved.
*  ADROTATE is a registered trademark of Arnan de Gans.

*  COPYRIGHT NOTICES AND ALL THE COMMENTS SHOULD REMAIN INTACT.
*  By using this code you agree to indemnify Arnan de Gans from any
*  liability that might arise from its use.
------------------------------------------------------------------------------------ */

/*-------------------------------------------------------------
 Name:      adrotate_ad
 Purpose:   Show requested ad
-------------------------------------------------------------*/
function adrotate_ad($banner_id, $opt = null) {
	global $wpdb, $adrotate_config, $adrotate_crawlers;

	$output = '';

	if($banner_id) {
		$defaults = array(
			'wrapper' => 'yes', // Group wrapper (yes|no, Default mode)
			'site' => 'no' // Network site (yes|no)
		);
		$options = wp_parse_args($opt, $defaults);

		$license = adrotate_get_license();
		$network = get_site_option('adrotate_network_settings');

		if($options['site'] == 'yes' AND adrotate_is_networked() AND $license['type'] == 'Developer') {
			$current_blog = $wpdb->blogid;
			switch_to_blog($network['primary']);
		}

		$banner = $wpdb->get_row($wpdb->prepare("SELECT `id`, `title`, `bannercode`, `tracker`, `show_everyone`, `image`, `crate`, `irate`, `budget` FROM `{$wpdb->prefix}adrotate` WHERE `id` = %d AND (`type` = 'active' OR `type` = '2days' OR `type` = '7days');", $banner_id));

		if($banner) {
			$selected = array($banner->id => 0);
			$selected = adrotate_filter_show_everyone($selected, $banner);
			$selected = adrotate_filter_schedule($selected, $banner);

			if($adrotate_config['enable_advertisers'] == 'Y' AND ($banner->crate > 0 OR $banner->irate > 0)) {
				$selected = adrotate_filter_budget($selected, $banner);
			}
		} else {
			$selected = false;
		}

		if($selected) {
			$image = str_replace('%folder%', $adrotate_config['banner_folder'], $banner->image);

			if($options['wrapper'] == 'yes') $output .= '<div class="a'.$adrotate_config['adblock_disguise'].'-single a'.$adrotate_config['adblock_disguise'].'-'.$banner->id.'">';
			$output .= adrotate_ad_output($banner->id, 0, $banner->title, $banner->bannercode, $banner->tracker, $image);
			if($options['wrapper'] == 'yes') $output .= '</div>';

			if($adrotate_config['stats'] == 1 AND ($banner->tracker == "Y" OR $banner->tracker == "I")) {
				adrotate_count_impression($banner->id, 0, $options['site']);
			}
		} else {
			$output .= adrotate_error('ad_expired', array('banner_id' => $banner_id));
		}
		unset($banner);

		if($options['site'] == 'yes' AND adrotate_is_networked() AND $license['type'] == 'Developer') {
			switch_to_blog($current_blog);
		}

	} else {
		$output .= adrotate_error('ad_no_id');
	}

	return $output;
}

/*-------------------------------------------------------------
 Name:      adrotate_group
 Purpose:   Group output
-------------------------------------------------------------*/
function adrotate_group($group_ids, $opt = null) {
	global $wpdb, $adrotate_config;

	$output = $group_select = $weightoverride = $mobileoverride = $mobileosoverride = $showoverride = '';
	if($group_ids) {

		$defaults = array(
			'fallback' => 0, // Fallback group ID
			'weight' => 0, // Minimum weight (0, 1-10)
			'site' => 'no' // Network site (yes|no)
		);
		$options = wp_parse_args($opt, $defaults);

		$license = adrotate_get_license();
		$network = get_site_option('adrotate_network_settings');

		if($options['site'] == 'yes' AND adrotate_is_networked() AND $license['type'] == 'Developer') {
			$current_blog = $wpdb->blogid;
			switch_to_blog($network['primary']);
		}

		$now = current_time('timestamp');

		$group_array = (preg_match('/,/is', $group_ids)) ? explode(",", $group_ids) : array($group_ids);
		$group_array = array_filter($group_array);

		foreach($group_array as $key => $value) {
			$group_select .= " `{$wpdb->prefix}adrotate_linkmeta`.`group` = ".$wpdb->prepare('%d', $value)." OR";
		}
		$group_select = rtrim($group_select, " OR");

		// Grab settings to use from first group
		$group = $wpdb->get_row($wpdb->prepare("SELECT * FROM `{$wpdb->prefix}adrotate_groups` WHERE `name` != '' AND `id` = %d;", $group_array[0]));

		if($group) {
			if($group->mobile == 1) {
				if(!adrotate_is_mobile() AND !adrotate_is_tablet()) { // Desktop
					$mobileoverride = "AND `{$wpdb->prefix}adrotate`.`desktop` = 'Y'";
				} else if(adrotate_is_mobile()) { // Phones
					$mobileoverride = "AND `{$wpdb->prefix}adrotate`.`mobile` = 'Y'";
				} else if(adrotate_is_tablet()) { // Tablets
					$mobileoverride = "AND `{$wpdb->prefix}adrotate`.`tablet` = 'Y'";
				}

				if(!adrotate_is_ios() AND !adrotate_is_android()) { // Other OS
					$mobileosoverride = "AND `{$wpdb->prefix}adrotate`.`os_other` = 'Y'";
				} else if(adrotate_is_ios()) { // iOS
					$mobileosoverride = "AND `{$wpdb->prefix}adrotate`.`os_ios` = 'Y'";
				} else if(adrotate_is_android()) { // Android
					$mobileosoverride = "AND `{$wpdb->prefix}adrotate`.`os_android` = 'Y'";
				}
			}

			$weightoverride = ($options['weight'] > 0) ? "AND `{$wpdb->prefix}adrotate`.`weight` >= {$options['weight']} " : '';
			$options['fallback'] = ($options['fallback'] == 0) ? $group->fallback : $options['fallback'];

			// Get all ads in all selected groups
			$ads = $wpdb->get_results(
				"SELECT
					`{$wpdb->prefix}adrotate`.`id`, `title`, `bannercode`, `image`, `tracker`, `show_everyone`, `weight`,
					`crate`, `irate`, `budget`, `state_req`, `cities`, `states`, `countries`, `{$wpdb->prefix}adrotate_linkmeta`.`group`
				FROM
					`{$wpdb->prefix}adrotate`,
					`{$wpdb->prefix}adrotate_linkmeta`
				WHERE
					({$group_select})
					AND `{$wpdb->prefix}adrotate_linkmeta`.`user` = 0
					AND `{$wpdb->prefix}adrotate`.`id` = `{$wpdb->prefix}adrotate_linkmeta`.`ad`
					{$mobileoverride}
					{$mobileosoverride}
					{$weightoverride}
					AND (`{$wpdb->prefix}adrotate`.`type` = 'active'
						OR `{$wpdb->prefix}adrotate`.`type` = '2days'
						OR `{$wpdb->prefix}adrotate`.`type` = '7days')
				GROUP BY `{$wpdb->prefix}adrotate`.`id`
				ORDER BY `{$wpdb->prefix}adrotate`.`id`;");

			if($ads) {
				foreach($ads as $ad) {
					$selected[$ad->id] = $ad;

					if($adrotate_config['duplicate_adverts_filter'] == 'Y') {
						
						if (is_home() AND !in_the_loop()) {
					    	$session_page = get_option('page_for_posts');
						} elseif (is_post_type_archive() OR is_category()){
							$session_page = get_query_var('cat');
						} else {
							$session_page = get_the_ID();
						}
											
						$session_page = 'adrotate-post-'.$session_page;
						$selected = adrotate_filter_duplicates($selected, $ad->id, $session_page);
					}

					$selected = adrotate_filter_show_everyone($selected, $ad);
					$selected = adrotate_filter_schedule($selected, $ad);

					if($adrotate_config['enable_advertisers'] == 'Y' AND ($ad->crate > 0 OR $ad->irate > 0)) {
						$selected = adrotate_filter_budget($selected, $ad);
					}

					if($adrotate_config['enable_geo'] > 0 AND $group->geo == 1) {
						$selected = adrotate_filter_location($selected, $ad);
					}
				}

				$array_count = count($selected);
				if($array_count > 0) {
					$before = $after = '';
					$before = str_replace('%id%', $group_array[0], stripslashes(html_entity_decode($group->wrapper_before, ENT_QUOTES)));
					$after = str_replace('%id%', $group_array[0], stripslashes(html_entity_decode($group->wrapper_after, ENT_QUOTES)));

					$output .= '<div class="g'.$adrotate_config['adblock_disguise'].' g'.$adrotate_config['adblock_disguise'].'-'.$group->id.'">';

					// Kill dynamic mode for mobile users
					if($adrotate_config['mobile_dynamic_mode'] == 'Y' AND $group->modus == 1 AND wp_is_mobile()) {
						$group->modus = 0;
					}

					if($group->modus == 1) { // Dynamic ads
						$i = 1;

						// Limit group to save resources
						$amount = ($group->adspeed >= 10000) ? 10 : 20;

						// Randomize and trim output
						$selected = adrotate_shuffle($selected);
						foreach($selected as $key => $banner) {
							if($i <= $amount) {
								$image = str_replace('%folder%', $adrotate_config['banner_folder'], $banner->image);

								$output .= '<div class="g'.$adrotate_config['adblock_disguise'].'-dyn a'.$adrotate_config['adblock_disguise'].'-'.$banner->id.' c-'.$i.'">';
								$output .= $before.adrotate_ad_output($banner->id, $group->id, $banner->title, $banner->bannercode, $banner->tracker, $image).$after;
								$output .= '</div>';
								$i++;
							}
						}
					} else if($group->modus == 2) { // Block of ads
						$block_count = $group->gridcolumns * $group->gridrows;
						if($array_count < $block_count) $block_count = $array_count;
						$columns = 1;

						for($i=1;$i<=$block_count;$i++) {
							$banner_id = adrotate_pick_weight($selected);

							$image = str_replace('%folder%', $adrotate_config['banner_folder'], $selected[$banner_id]->image);

							$output .= '<div class="g'.$adrotate_config['adblock_disguise'].'-col b'.$adrotate_config['adblock_disguise'].'-'.$group->id.' a'.$adrotate_config['adblock_disguise'].'-'.$selected[$banner_id]->id.'">';
							$output .= $before.adrotate_ad_output($selected[$banner_id]->id, $group->id, $selected[$banner_id]->title, $selected[$banner_id]->bannercode, $selected[$banner_id]->tracker, $image).$after;
							$output .= '</div>';

							if($columns == $group->gridcolumns AND $i != $block_count) {
								$output .= '</div><div class="g'.$adrotate_config['adblock_disguise'].' g'.$adrotate_config['adblock_disguise'].'-'.$group->id.'">';
								$columns = 1;
							} else {
								$columns++;
							}

							if($adrotate_config['stats'] == 1 AND ($selected[$banner_id]->tracker == "Y" OR $selected[$banner_id]->tracker == "I")) {
								adrotate_count_impression($selected[$banner_id]->id, $group->id, $options['site']);
							}

							// Store advert ID's in session
							if($adrotate_config['duplicate_adverts_filter'] == 'Y') {
								$_SESSION['adrotate-duplicate-ads'][$session_page]['adverts'][] = $banner_id;
							}

							unset($selected[$banner_id]);
						}
					} else { // Default (single ad)
						$banner_id = adrotate_pick_weight($selected);

						$image = str_replace('%folder%', $adrotate_config['banner_folder'], $selected[$banner_id]->image);

						$output .= '<div class="g'.$adrotate_config['adblock_disguise'].'-single a'.$adrotate_config['adblock_disguise'].'-'.$selected[$banner_id]->id.'">';
						$output .= $before.adrotate_ad_output($selected[$banner_id]->id, $group->id, $selected[$banner_id]->title, $selected[$banner_id]->bannercode, $selected[$banner_id]->tracker, $image).$after;
						$output .= '</div>';

						if($adrotate_config['stats'] == 1 AND ($selected[$banner_id]->tracker == "Y" OR $selected[$banner_id]->tracker == "I")) {
							adrotate_count_impression($selected[$banner_id]->id, $group->id, $options['site']);
						}

						// Store advert ID's in session
						if($adrotate_config['duplicate_adverts_filter'] == 'Y') {
							$_SESSION['adrotate-duplicate-ads'][$session_page]['adverts'][] = $banner_id;
						}
					}

					$output .= '</div>';

					unset($selected, $banner_id);
				} else {
					if($options['site'] == 'yes' AND adrotate_is_networked() AND $license['type'] == 'Developer') {
						switch_to_blog($current_blog);
					}
					$output .= adrotate_fallback($options['fallback'], 'expired', $options['site']);
				}
			} else {
				if($options['site'] == 'yes' AND adrotate_is_networked() AND $license['type'] == 'Developer') {
					switch_to_blog($current_blog);
				}
				$output .= adrotate_fallback($options['fallback'], 'unqualified', $options['site']);
			}
		} else {
			$output .= adrotate_error('group_not_found', array('group_id' => $group_array[0]));
		}

		if($options['site'] == 'yes' AND adrotate_is_networked() AND $license['type'] == 'Developer') {
			switch_to_blog($current_blog);
		}

	} else {
		$output .= adrotate_error('group_no_id');
	}

	return $output;
}

/*-------------------------------------------------------------
 Name:      adrotate_shortcode
 Purpose:   Prepare function requests for calls on shortcodes
-------------------------------------------------------------*/
function adrotate_shortcode($atts, $content = null) {
	global $adrotate_config;

	$banner_id = (!empty($atts['banner'])) ? trim($atts['banner'], "\r\t ") : 0;
	$group_ids = (!empty($atts['group'])) ? trim($atts['group'], "\r\t ") : 0;
	$fallback = (!empty($atts['fallback'])) ? trim($atts['fallback'], "\r\t "): 0; // Optional: for groups (ID)
	$weight	= (!empty($atts['weight']))	? trim($atts['weight'], "\r\t "): 0; // Optional: for groups (0, 1-10)
	$site = (!empty($atts['site'])) ? trim($atts['site'], "\r\t ") : 'no'; // Optional: for networks (yes|no)
	$wrapper = (!empty($atts['wrapper'])) ? trim($atts['wrapper'], "\r\t ") : 'yes'; // Optional: for inline advert (yes|no, single advert only)

	$output = '';
	if($adrotate_config['w3caching'] == "Y") {
		$output .= '<!-- mfunc '.W3TC_DYNAMIC_SECURITY.' -->';

		if($banner_id > 0 AND ($group_ids == 0 OR $group_ids > 0)) { // Show one Ad
			$output .= 'echo adrotate_ad('.$banner_id.', array("wrapper" => "'.$wrapper.'", "site" => "'.$site.'"));';
		}

		if($banner_id == 0 AND $group_ids > 0) { // Show group
			$output .= 'echo adrotate_group('.$group_ids.', array("fallback" => '.$fallback.', "weight" => '.$weight.', "site" => "'.$site.'"));';
		}

		$output .= '<!-- /mfunc '.W3TC_DYNAMIC_SECURITY.' -->';
	} else if($adrotate_config['borlabscache'] == "Y" AND function_exists('BorlabsCacheHelper')) {
		if(BorlabsCacheHelper()->willFragmentCachingPerform()) {
			$borlabsphrase = BorlabsCacheHelper()->getFragmentCachingPhrase();

			$output .= '<!--[borlabs cache start: '.$borlabsphrase.']--> ';
			if($banner_id > 0 AND ($group_ids == 0 OR $group_ids > 0)) { // Show one Ad
				$output .= 'echo adrotate_ad('.$banner_id.', array("wrapper" => "'.$wrapper.'", "site" => '.$site.'));';
			}
			if($banner_id == 0 AND $group_ids > 0) { // Show group
				$output .= 'echo adrotate_group('.$group_ids.', array("fallback" => '.$fallback.', "weight" => '.$weight.', "site" => "'.$site.'"));';
			}
			$output .= ' <!--[borlabs cache end: '.$borlabsphrase.']-->';

			unset($borlabsphrase);
		}
	} else {
		if($banner_id > 0 AND ($group_ids == 0 OR $group_ids > 0)) { // Show one Ad
			$output .= adrotate_ad($banner_id, array('wrapper' => $wrapper, 'site' => $site));
		}

		if($banner_id == 0 AND $group_ids > 0) { // Show group
			$output .= adrotate_group($group_ids, array('fallback' => $fallback, 'weight' => $weight, 'site' => $site));
		}
	}

	return $output;
}

/*-------------------------------------------------------------
 Name:      adrotate_inject_posts
 Purpose:   Add an advert to a single page or post
-------------------------------------------------------------*/
function adrotate_inject_posts($post_content) {
	global $wpdb, $post, $adrotate_config;

	$group_array = array();
	if(is_page()) {
		// Inject ads into page
		$ids = $wpdb->get_results("SELECT `id`, `page`, `page_loc`, `page_par` FROM `{$wpdb->prefix}adrotate_groups` WHERE `page_loc` > 0 AND  `page_loc` < 5;");

		foreach($ids as $id) {
			$pages = explode(",", $id->page);
			if(!is_array($pages)) $pages = array();

			if(in_array($post->ID, $pages)) {
				$group_array[$id->id] = array('location' => $id->page_loc, 'paragraph' => $id->page_par, 'ids' => $pages);
			}
		}
		unset($ids, $pages);
	}

	if(is_single()) {
		// Inject ads into posts in specified category
		$ids = $wpdb->get_results("SELECT `id`, `cat`, `cat_loc`, `cat_par` FROM `{$wpdb->prefix}adrotate_groups` WHERE `cat_loc` > 0 AND `cat_loc` < 5;");
		$wp_categories = get_terms('category', array('fields' => 'ids'));

		foreach($ids as $id) {
			$categories = explode(",", $id->cat);
			if(!is_array($categories)) $categories = array();

			foreach($wp_categories as &$value) {
				if(in_array($value, $categories)) {
					$group_array[$id->id] = array('location' => $id->cat_loc, 'paragraph' => $id->cat_par, 'ids' => $categories);
				}
			}
		}
		unset($ids, $wp_categories, $categories);
	}

	$group_array = adrotate_shuffle($group_array);
	$group_count = count($group_array);

	if($group_count > 0) {
		$before = $after = $inside = 0;
		$advert_output = '';
		foreach($group_array as $group_id => $group) {
			if(is_page($group['ids']) OR has_category($group['ids'])) {
				// Caching or not?
				if($adrotate_config['w3caching'] == 'Y') {
					$advert_output = '<!-- mfunc '.W3TC_DYNAMIC_SECURITY.' -->';
					$advert_output .= 'echo adrotate_group('.$group_id.');';
					$advert_output .= '<!-- /mfunc '.W3TC_DYNAMIC_SECURITY.' -->';
				} else if($adrotate_config['borlabscache'] == "Y" AND function_exists('BorlabsCacheHelper')) {
					if(BorlabsCacheHelper()->willFragmentCachingPerform()) {
						$borlabsphrase = BorlabsCacheHelper()->getFragmentCachingPhrase();

						$advert_output = '<!--[borlabs cache start: '.$borlabsphrase.']-->';
						$advert_output .= 'echo adrotate_group('.$group_id.');';
						$advert_output .= '<!--[borlabs cache end: '.$borlabsphrase.']-->';

						unset($borlabsphrase);
					}
				} else {
					$advert_output = adrotate_group($group_id);
				}

				// Advert in front of content
				if(($group['location'] == 1 OR $group['location'] == 3) AND $before == 0) {
					$post_content = $advert_output.$post_content;
					unset($group_array[$group_id]);
					$before = 1;
				}

				// Advert behind the content
				if(($group['location'] == 2 OR $group['location'] == 3) AND $after == 0) {
					$post_content = $post_content.$advert_output;
					unset($group_array[$group_id]);
					$after = 1;
				}

				// Adverts inside the content
				if($group['location'] == 4) {

/*
$content = "<p>First paragraph</p><blockquote><p>Don't count me</p>or me</blockquote><p>Second paragraph</p>";
					$post_content = preg_replace("/<blockquote.+?<\/blockquote>/i", "yoink", $content);
					$xpl = explode("</p>", $post_content);



					echo $count = count(array_filter($xpl));
//print_r($xpl);
*/


				    $paragraphs = explode('</p>', $post_content);
					$paragraph_count = count($paragraphs);
					$count_p = ($group['paragraph'] == 99) ? ceil($paragraph_count / 2) : $group['paragraph'];

				    foreach($paragraphs as $index => $paragraph) {
				        if(trim($paragraph)) {
				            $paragraphs[$index] .= '</p>';
				        }

				        if($count_p == $index + 1 AND $inside == 0) {
				            $paragraphs[$index] .= $advert_output;
							unset($group_array[$group_id]);
				            $inside = 1;
				        }
				    }

				    $inside = 0; // Reset for the next paragraph
				    $post_content = implode('', $paragraphs);
					unset($paragraphs, $paragraph_count);
				}
			}
		}
		unset($group_array, $before, $after, $inside, $advert_output);
	}

	return $post_content;
}

/*-------------------------------------------------------------
 Name:      adrotate_preview
 Purpose:   Show preview of selected ad (Dashboard)
-------------------------------------------------------------*/
function adrotate_preview($banner_id) {
	global $wpdb, $adrotate_config;

	if($banner_id) {
		$now = current_time('timestamp');

		$banner = $wpdb->get_row($wpdb->prepare("SELECT * FROM `{$wpdb->prefix}adrotate` WHERE `id` = %d;", $banner_id));

		if($banner) {
			$image = str_replace('%folder%', $adrotate_config['banner_folder'], $banner->image);
			$output = adrotate_ad_output($banner->id, 0, $banner->title, $banner->bannercode, $banner->tracker, $image);
		} else {
			$output = adrotate_error('ad_expired', array('banner_id' => $banner_id));
		}
	} else {
		$output = adrotate_error('ad_no_id');
	}

	return $output;
}

/*-------------------------------------------------------------
 Name:      adrotate_ad_output
 Purpose:   Prepare the output for viewing
-------------------------------------------------------------*/
function adrotate_ad_output($id, $group, $name, $bannercode, $tracker, $image) {
	global $blog_id, $adrotate_config;

	$banner_output = $bannercode;
	$banner_output = stripslashes(htmlspecialchars_decode($banner_output, ENT_QUOTES));

	if($adrotate_config['stats'] > 0 AND $tracker != "N") {
		if(empty($blog_id) or $blog_id == '') {
			$blog_id = 0;
		}

		$tracking_pixel = "data:image/gif;base64,R0lGODlhAQABAIAAAP///////yH5BAEKAAEALAAAAAABAAEAAAICTAEAOw==";

		if($adrotate_config['stats'] == 1 AND ($tracker == "Y" OR $tracker == "C")) { // Internal tracker
			preg_match_all('/<a[^>](?:.*?)>/i', $banner_output, $matches, PREG_SET_ORDER);
			if(isset($matches[0])) {
				$banner_output = str_replace('<a ', '<a data-track="'.adrotate_hash($id, $group, $blog_id).'" ', $banner_output);
				foreach($matches[0] as $value) {
					if(preg_match('/<a[^>]+class=\"(.+?)\"[^>]*>/i', $value, $regs)) {
					    $result = $regs[1]." gofollow";
						$banner_output = str_ireplace('class="'.$regs[1].'"', 'class="'.$result.'"', $banner_output);
					} else {
						$banner_output = str_ireplace('<a ', '<a class="gofollow" ', $banner_output);
					}
					unset($value, $regs, $result);
				}
			}
		}

		if($adrotate_config['stats'] == 2 OR $adrotate_config['stats'] == 4 OR $adrotate_config['stats'] == 5) { // Google Analytics || Matomo
			preg_match_all('/<(?:a|img|iframe)[^>](?:.*?)>/i', $banner_output, $matches, PREG_SET_ORDER);

			if(isset($matches[0])) {
				if($adrotate_config['stats'] == 2) { // Matomo
					// _paq.push(['trackEvent', 'Adverts', 'Click|Impression', 'advert_name']);
					$click_event = "_paq.push(['trackEvent', 'Adverts', 'Click', '$name']);";
					$impression_event = "_paq.push(['trackEvent', 'Adverts', 'Impression', '$name']);";
				}

				if($adrotate_config['stats'] == 4) { // gtag.js
					// gtag('event', 'click|impression', {'event_category': 'Adverts', 'event_label': advert_name, 'value': action_value, 'non_interaction': true});
					$click_event = "gtag('event', 'click', {'event_category': 'Adverts', 'event_label': '$name', 'value': ".$adrotate_config['google_click_value'].",  'non_interaction': true});";
					$impression_event = "gtag('event', 'impression', {'event_category': 'Adverts', 'event_label': '$name', 'value': ".$adrotate_config['google_impression_value'].", 'non_interaction': true});";
				}

				if($adrotate_config['stats'] == 5) { // gtm.js
					// dataLayer.push({'event': 'AdRotatePro', 'eventCategory': 'Adverts', 'eventAction': 'Click|Impression', 'eventLabel': advert_name, 'eventValue': action_value});
					$click_event = "dataLayer.push({'event': 'AdRotatePro', 'AdCategory': 'Adverts', 'AdAction': 'Click', 'AdLabel': '$name', 'AdValue': ".$adrotate_config['google_click_value']."});";
					$impression_event = "dataLayer.push({'event': 'AdRotatePro', 'AdCategory': 'Adverts', 'AdAction': 'Impression', 'AdLabel': '$name', 'AdValue': ".$adrotate_config['google_impression_value']."});";
				}

				// Image banner
				if(stripos($banner_output, '<a') !== false AND stripos($banner_output, '<img') !== false) {
					if(!preg_match('/<a[^>]+onClick[^>]*>/i', $banner_output, $url)) {
						$banner_output = str_ireplace('<a ', '<a onClick="'.$click_event.'" ', $banner_output);
					}
					if(!preg_match('/<img[^>]+onload[^>]*>/i', $banner_output, $img)) {
						$banner_output = str_ireplace('<img ', '<img onload="'.$impression_event.'" ', $banner_output);
					}
				}

				// Text banner (With tagged tracking pixel for impressions)
				if(stripos($banner_output, '<a') !== false AND stripos($banner_output, '<img') === false) {
					if(!preg_match('/<a[^>]+onClick[^>]*>/i', $banner_output, $url)) {
						$banner_output = str_ireplace('<a ', '<a onClick="'.$click_event.'" ', $banner_output);
					}
					$banner_output .= '<img width="0" height="0" src="'.$tracking_pixel.'" onload="'.$impression_event.'" />';
				}

				// HTML5/iFrame advert (Only supports impressions)
				if(stripos($banner_output, '<iframe') !== false) {
					if(!preg_match('/<iframe[^>]+onload[^>]*>/i', $banner_output, $url)) {
						$banner_output = str_ireplace('<iframe ', '<iframe onload="'.$impression_event.'" ', $banner_output);
					}
				}
				unset($url, $img, $click_event, $impression_event);
			}
		}
		unset($matches);
	}

	$image = apply_filters('adrotate_apply_photon', $image);

	$banner_output = str_replace('%title%', $name, $banner_output);
	$banner_output = str_replace('%random%', rand(100000,999999), $banner_output);
	$banner_output = str_replace('%asset%', $image, $banner_output);
	$banner_output = str_replace('%id%', $id, $banner_output);
	$banner_output = do_shortcode($banner_output);

	return $banner_output;
}

/*-------------------------------------------------------------
 Name:      adrotate_fallback
 Purpose:   Fall back to the set group or show an error if no fallback is set
-------------------------------------------------------------*/
function adrotate_fallback($group, $case, $site = 'no') {

	$fallback_output = '';
	if($group > 0) {
		$fallback_output = adrotate_group($group, array('site' => $site));
	} else {
		if($case == 'expired') {
			$fallback_output = adrotate_error('ad_expired', array('banner_id' => 'n/a'));
		}

		if($case == 'unqualified') {
			$fallback_output = adrotate_error('ad_unqualified');
		}
	}

	return $fallback_output;
}

/*-------------------------------------------------------------
 Name:      adrotate_header
 Purpose:   Add required CSS to wp_head (action)
-------------------------------------------------------------*/
function adrotate_header() {

	$output = "\n<!-- This site is using AdRotate v".ADROTATE_DISPLAY." to display their advertisements - https://ajdg.solutions/ -->\n";
	$output .= adrotate_custom_css();
	
	$header = get_option('adrotate_header_output', false);
	if($header) {
		$header = stripslashes(htmlspecialchars_decode($header, ENT_QUOTES));
		$header = str_replace('%random%', rand(100000,999999), $header);
		$output .= $header."\n";
		unset($header);
	}

	$gam = get_option('adrotate_gam_output', false);
	if($gam) {
		$gam = stripslashes(htmlspecialchars_decode($gam, ENT_QUOTES));
		$gam = str_replace('%random%', rand(100000,999999), $gam);
		$output .= $gam."\n\n";
		unset($gam);
	}
	echo $output;
}

/*-------------------------------------------------------------
 Name:      adrotate_custom_css
 Purpose:   Add group CSS to adrotate_header()
-------------------------------------------------------------*/
function adrotate_custom_css() {
	global $adrotate_config;

	// Grab group settings from primary site
	$generated_css = $network_css = array();
	$license = adrotate_get_license();
	if(adrotate_is_networked() AND $license['type'] == 'Developer') {
		$network = get_site_option('adrotate_network_settings');
		$current_blog = $wpdb->blogid;

		switch_to_blog($network['primary']);
		$network_css = get_option('adrotate_group_css');
		switch_to_blog($current_blog);
	}

	$generated_css = array_merge(get_option('adrotate_group_css', array()), $network_css);

	$output = "";
	$output .= "<!-- AdRotate CSS -->\n";
	$output .= "<style type=\"text/css\" media=\"screen\">\n";
	$output .= "\t.g".$adrotate_config['adblock_disguise']." { margin:0px; padding:0px; overflow:hidden; line-height:1; zoom:1; }\n";
	$output .= "\t.g".$adrotate_config['adblock_disguise']." img { height:auto; }\n";
	$output .= "\t.g".$adrotate_config['adblock_disguise']."-col { position:relative; float:left; }\n";
	$output .= "\t.g".$adrotate_config['adblock_disguise']."-col:first-child { margin-left: 0; }\n";
	$output .= "\t.g".$adrotate_config['adblock_disguise']."-col:last-child { margin-right: 0; }\n";
	foreach($generated_css as $group_id => $css) {
		if(strlen($css) > 0) {
			$output .= $css;
		}
	}
	unset($generated_css);
	$output .= "\t@media only screen and (max-width: 480px) {\n";
	$output .= "\t\t.g".$adrotate_config['adblock_disguise']."-col, .g".$adrotate_config['adblock_disguise']."-dyn, .g".$adrotate_config['adblock_disguise']."-single { width:100%; margin-left:0; margin-right:0; }\n";
	$output .= "\t}\n";
	if($adrotate_config['widgetpadding'] == "Y") {
		$advert_string = get_option('adrotate_dynamic_widgets_advert', 'temp_1');
		$group_string = get_option('adrotate_dynamic_widgets_group', 'temp_2');
		$output .= ".ajdg_bnnrwidgets, .ajdg_grpwidgets { overflow:hidden; padding:0; }\n";
		$output .= ".".$advert_string.", .".$group_string." { overflow:hidden; padding:0; }\n";
	}
	$output .= "</style>\n";
	$output .= "<!-- /AdRotate CSS -->\n\n";

	return $output;
}

/*-------------------------------------------------------------
 Name:      adrotate_scripts
 Purpose:   Add required scripts to wp_enqueue_scripts (action)
-------------------------------------------------------------*/
function adrotate_scripts() {
	global $adrotate_config;

	$in_footer = ($adrotate_config['jsfooter'] == "Y") ? true : false;

	if($adrotate_config['jquery'] == 'Y') {
		wp_enqueue_script('jquery', false, false, null, $in_footer);
	}

	if(get_option('adrotate_dynamic_required') > 0) {
		wp_enqueue_script('adrotate-dyngroup', plugins_url('/library/jquery.adrotate.dyngroup.js', __FILE__), false, null, $in_footer);
	}

	if($adrotate_config['stats'] == 1) {
		wp_enqueue_script('adrotate-clicktracker', plugins_url('/library/jquery.adrotate.clicktracker.js', __FILE__), false, null, $in_footer);
		wp_localize_script('adrotate-clicktracker', 'click_object', array('ajax_url' => admin_url('admin-ajax.php')));
		wp_localize_script('adrotate-dyngroup', 'impression_object', array('ajax_url' => admin_url( 'admin-ajax.php')));
	}

	if(!$in_footer) {
		add_action('wp_head', 'adrotate_custom_javascript');
	} else {
		add_action('wp_footer', 'adrotate_custom_javascript', 100);
	}
}

/*-------------------------------------------------------------
 Name:      adrotate_custom_javascript
 Purpose:   Add required JavaScript to adrotate_scripts()
-------------------------------------------------------------*/
function adrotate_custom_javascript() {
	global $wpdb, $adrotate_config;

	$groups = $groups_network = array();
	// Grab group settings from primary site
	$network = get_site_option('adrotate_network_settings');
	$license = adrotate_get_license();
	if(adrotate_is_networked() AND $license['type'] == 'Developer') {
		$current_blog = $wpdb->blogid;
		switch_to_blog($network['primary']);
		$groups_network = $wpdb->get_results("SELECT `id`, `adspeed`, `repeat_impressions` FROM `{$wpdb->prefix}adrotate_groups` WHERE `name` != '' AND `modus` = 1 ORDER BY `id` ASC;", ARRAY_A);
		switch_to_blog($current_blog);
	}

	$groups = $wpdb->get_results("SELECT `id`, `adspeed`, `repeat_impressions` FROM `{$wpdb->prefix}adrotate_groups` WHERE `name` != '' AND `modus` = 1 ORDER BY `id` ASC;", ARRAY_A);
	$groups = array_merge($groups, $groups_network);

	if(count($groups) > 0) {
		$output = "<!-- AdRotate JS -->\n";
		$output .= "<script type=\"text/javascript\">\n";
		$output .= "jQuery(document).ready(function(){if(jQuery.fn.gslider) {\n";
		foreach($groups as $group) {
			$output .= "\tjQuery('.g".$adrotate_config['adblock_disguise']."-".$group['id']."').gslider({groupid:".$group['id'].",speed:".$group['adspeed'].",repeat_impressions:'".$group['repeat_impressions']."'});\n";
		}
		$output .= "}});\n";
		$output .= "</script>\n";
		$output .= "<!-- /AdRotate JS -->\n\n";
		unset($groups);
		echo $output;
	}
}

/*-------------------------------------------------------------
 Name:      adrotate_custom_profile_fields
 Purpose:   Add profile fields to user creation and editing dashboards
-------------------------------------------------------------*/
function adrotate_custom_profile_fields($user) {
	global $adrotate_config;

    if(current_user_can('adrotate_advertiser_manage') AND $adrotate_config['enable_advertisers'] == 'Y') {
		if($user != 'add-new-user') {
		    $advertiser = get_user_meta($user->ID, 'adrotate_is_advertiser', 1);
		    $permissions = get_user_meta($user->ID, 'adrotate_permissions', 1);
		    // Check for gaps
		    if(empty($advertiser)) $advertiser = 'N';
		    if(empty($permissions)) $permissions = array('create' => 'N', 'edit' => 'N', 'advanced' => 'N', 'geo' => 'N', 'group' => 'N', 'schedule' => 'N');
			if(!isset($permissions['create'])) $permissions['create'] = 'N';
			if(!isset($permissions['edit'])) $permissions['edit'] = 'N';
			if(!isset($permissions['advanced'])) $permissions['advanced'] = 'N';
			if(!isset($permissions['geo'])) $permissions['geo'] = 'N';
			if(!isset($permissions['group'])) $permissions['group'] = 'N';
			if(!isset($permissions['schedule'])) $permissions['schedule'] = 'N';
		    $notes = get_user_meta($user->ID, 'adrotate_notes', 1);
		} else {
			$advertiser = 'N';
			$permissions = array('create' => 'N', 'edit' => 'N', 'advanced' => 'N', 'geo' => 'N', 'group' => 'N', 'schedule' => 'N');
			$notes = '';
		}
		?>
	    <h3><?php _e('AdRotate Advertiser', 'adrotate-pro'); ?></h3>
	    <table class="form-table">
	      	<tr>
		        <th valign="top"><?php _e('Enable', 'adrotate-pro'); ?></th>
		        <td>
		        	<label for="adrotate_is_advertiser"><input tabindex="100" type="checkbox" name="adrotate_is_advertiser" <?php if($advertiser == 'Y') { ?>checked="checked" <?php } ?> /> <?php _e('Is this user an AdRotate Advertiser?', 'adrotate-pro'); ?></label><br />
		        </td>
	      	</tr>
	      	<tr>
		        <th valign="top"><?php _e('Permissions', 'adrotate-pro'); ?></th>
		        <td>
		        	<label for="adrotate_can_create"><input tabindex="101" type="checkbox" name="adrotate_can_create" <?php if($permissions['create'] == 'Y') { ?>checked="checked" <?php } ?> /> <?php _e('Can create adverts?', 'adrotate-pro'); ?></label><br />
		        	<label for="adrotate_can_edit"><input tabindex="102" type="checkbox" name="adrotate_can_edit" <?php if($permissions['edit'] == 'Y') { ?>checked="checked" <?php } ?> /> <?php _e('Can edit their own adverts?', 'adrotate-pro'); ?></label>
		        </td>
	      	</tr>
	      	<tr>
		        <th valign="top"><?php _e('Advert Features', 'adrotate-pro'); ?></th>
		        <td>
		        	<label for="adrotate_can_advanced"><input tabindex="103" type="checkbox" name="adrotate_can_advanced" <?php if($permissions['advanced'] == 'Y') { ?>checked="checked" <?php } ?> /> <?php _e('Can change advanced settings in adverts?', 'adrotate-pro'); ?></label><br />
		        	<label for="adrotate_can_geo"><input tabindex="104" type="checkbox" name="adrotate_can_geo" <?php if($permissions['geo'] == 'Y') { ?>checked="checked" <?php } ?> /> <?php _e('Can change Geo Targeting?', 'adrotate-pro'); ?></label><br />
		        	<label for="adrotate_can_group"><input tabindex="105" type="checkbox" name="adrotate_can_group" <?php if($permissions['group'] == 'Y') { ?>checked="checked" <?php } ?> /> <?php _e('Can change schedules in adverts?', 'adrotate-pro'); ?></label><br />
		        	<label for="adrotate_can_schedule"><input tabindex="106" type="checkbox" name="adrotate_can_schedule" <?php if($permissions['schedule'] == 'Y') { ?>checked="checked" <?php } ?> /> <?php _e('Can change groups in adverts?', 'adrotate-pro'); ?></label>
		        </td>
	      	</tr>
		    <tr>
				<th valign="top"><label for="adrotate_notes"><?php _e('Notes', 'adrotate-pro'); ?></label></th>
				<td>
					<textarea tabindex="104" name="adrotate_notes" cols="50" rows="5"><?php echo esc_attr($notes); ?></textarea><br />
					<em><?php _e('Also visible in the advertiser profile.', 'adrotate-pro'); ?></em>
					</td>
			</tr>
	    </table>
<?php
	}
}

/*-------------------------------------------------------------
 Name:      adrotate_nonce_error
 Purpose:   Display a formatted error if Nonce fails
-------------------------------------------------------------*/
function adrotate_nonce_error() {
	$message = 'WordPress was unable to verify the authenticity of the url you have clicked. Verify if the url used is valid or log in via your browser.<br />'.
	'Contact AdRotate support if the issue persists: <a href="https://ajdg.solutions/forums/" title="AdRotate Support" target="_blank">AJdG Solutions Support</a>.';
	wp_die($message);
}

/*-------------------------------------------------------------
 Name:      adrotate_error
 Purpose:   Show errors for problems in using AdRotate, should they occur
-------------------------------------------------------------*/
function adrotate_error($action, $arg = null) {
	switch($action) {
		// Ads
		case "ad_expired" :
			$result = '<!-- '.sprintf(__('Error, Ad (%s) is not available at this time due to schedule/budgeting/geolocation/mobile restrictions!', 'adrotate-pro'), $arg['banner_id']).' -->';
			return $result;
		break;

		case "ad_unqualified" :
			$result = '<!-- '.__('Either there are no banners, they are disabled or none qualified for this location!', 'adrotate-pro').' -->';
			return $result;
		break;

		case "ad_no_id" :
			$result = '<span style="font-weight: bold; color: #f00;">'.__('Error, no Ad ID set! Check your syntax!', 'adrotate-pro').'</span>';
			return $result;
		break;

		// Groups
		case "group_no_id" :
			$result = '<span style="font-weight: bold; color: #f00;">'.__('Error, no group ID set! Check your syntax!', 'adrotate-pro').'</span>';
			return $result;
		break;

		case "group_not_found" :
			$result = '<span style="font-weight: bold; color: #f00;">'.__('Error, group does not exist! Check your syntax!', 'adrotate-pro').' (ID: '.$arg['group_id'].')</span>';
			return $result;
		break;

		// Database
		case "db_error" :
			$result = '<span style="font-weight: bold; color: #f00;">'.__('There was an error locating the database tables for AdRotate. Please deactivate and re-activate AdRotate from the plugin page!!', 'adrotate-pro').'<br />'.__('If this does not solve the issue please seek support at', 'adrotate-pro').' <a href="https://ajdg.solutions/forums/forum/adrotate-for-wordpress/">ajdg.solutions/forums/forum/adrotate-for-wordpress/</a></span>';
			return $result;
		break;

		// Possible XSS or malformed URL
		case "error_loading_item" :
			$result = '<span style="font-weight: bold; color: #f00;">'.__('There was an error loading the page. Please try again by reloading the page via the menu on the left.', 'adrotate').'<br />'.__('If the issue persists please seek help at', 'adrotate').' <a href="https://ajdg.solutions/forums/forum/adrotate-for-wordpress/">ajdg.solutions/forums/forum/adrotate-for-wordpress/</a></span>';
			return $result;
		break;

		// Misc
		default:
			$result = '<span style="font-weight: bold; color: #f00;">'.__('An unknown error occured.', 'adrotate-pro').'</span>';
			return $result;
		break;
	}
}

/*-------------------------------------------------------------
 Name:      adrotate_dashboard_error
 Purpose:   Show errors for problems in using AdRotate
-------------------------------------------------------------*/
function adrotate_dashboard_error() {
	global $wpdb, $adrotate_config;

	$oneyear = current_time('timestamp') - (DAY_IN_SECONDS * 365);

	// License
	$license = adrotate_get_license();
	if($license['status'] == 0) {
		$error['adrotate_license'] = __('You did not yet activate your AdRotate Professional license. Activate and get updates, premium support and access to AdRotate Geo!', 'adrotate-pro'). ' <a href="'.admin_url('/admin.php?page=adrotate-settings&tab=license').'">'.__('Activate license', 'adrotate-pro').'</a>.';
	}

	// Adverts
	$status = get_option('adrotate_advert_status');
	$adrotate_notifications	= get_option("adrotate_notifications");
	if($adrotate_notifications['notification_dash'] == "Y") {
		if($status['expired'] > 0 AND $adrotate_notifications['notification_dash_expired'] == "Y") {
			$error['advert_expired'] = sprintf(_n('One advert is expired.', '%1$s adverts expired!', $status['expired'], 'adrotate-pro'), $status['expired']).' <a href="'.admin_url('admin.php?page=adrotate').'">'.__('Check adverts', 'adrotate-pro').'</a>.';
		}
		if($status['expiressoon'] > 0 AND $adrotate_notifications['notification_dash_soon'] == "Y") {
			$error['advert_soon'] = sprintf(_n('One advert expires in less than 2 days.', '%1$s adverts are expiring in less than 2 days!', $status['expiressoon'], 'adrotate-pro'), $status['expiressoon']).' <a href="'.admin_url('admin.php?page=adrotate').'">'.__('Check adverts', 'adrotate-pro').'</a>.';
		}
		if($status['expiresweek'] > 0 AND $adrotate_notifications['notification_dash_week'] == "Y") {
			$error['advert_week'] = sprintf(_n('One advert expires in less than a week.', '%1$s adverts are expiring in less than a week!', $status['expiresweek'], 'adrotate-pro'), $status['expiresweek']).' <a href="'.admin_url('admin.php?page=adrotate').'">'.__('Check adverts', 'adrotate-pro').'</a>.';
		}
	}
	if($status['error'] > 0) {
		$error['advert_config'] = sprintf(_n('One advert with configuration errors.', '%1$s adverts have configuration errors!', $status['error'], 'adrotate-pro'), $status['error']).' <a href="'.admin_url('admin.php?page=adrotate').'">'.__('Check adverts', 'adrotate-pro').'</a>.';
	}

	// Schedules
	if($adrotate_notifications['notification_dash'] == "Y") {
		if($adrotate_notifications['notification_schedules'] == "Y") {
			$schedules = $wpdb->get_results("SELECT * FROM `{$wpdb->prefix}adrotate_schedule` WHERE `name` != '' ORDER BY `id` ASC;");
			if($schedules) {
				$now = current_time('timestamp');
				$in2days = $now + 172800;
				$schedule_warning = array();

				foreach($schedules as $schedule) {
					if(($schedule->spread == 'Y' OR $schedule->spread_all == 'Y') AND $schedule->maximpressions == 0) $schedule_warning[] = $schedule->id;
					if(($schedule->spread == 'Y' OR $schedule->spread_all == 'Y') AND $schedule->maximpressions < 2000) $schedule_warning[] = $schedule->id;
					if($schedule->day_mon == 'N' AND $schedule->day_tue == 'N' AND $schedule->day_wed == 'N' AND $schedule->day_thu == 'N' AND $schedule->day_fri == 'N' AND $schedule->day_sat == 'N' AND $schedule->day_sun == 'N') $schedule_warning[] = $schedule->id;
//					if($schedule->stoptime < $in2days) $schedule_warning[] = $schedule->id;
//					if($schedule->stoptime < $now) $schedule_warning[] = $schedule->id;
				}
				
				$schedule_warning = count(array_unique($schedule_warning));
			}
			if($schedule_warning > 0) {
				$error['schedule_warning'] = sprintf(_n('One schedule has a warning.', '%1$s schedules have warnings!', $schedule_warning, 'adrotate-pro'), $schedule_warning).' <a href="'.admin_url('admin.php?page=adrotate-schedules').'">'.__('Check schedules', 'adrotate-pro').'</a>.';
			}
			unset($schedule_warning, $schedules);
		}
	}

	// Caching
	if($adrotate_config['w3caching'] == "Y" AND !is_plugin_active('w3-total-cache/w3-total-cache.php')) {
		$error['w3tc_not_active'] = __('You have enabled caching support but W3 Total Cache is not active on your site!', 'adrotate-pro').' <a href="'.admin_url('/admin.php?page=adrotate-settings&tab=misc').'">'.__('Disable W3 Total Cache Support', 'adrotate-pro').'</a>.';
	}
	if($adrotate_config['w3caching'] == "Y" AND !defined('W3TC_DYNAMIC_SECURITY')) {
		$error['w3tc_no_hash'] = __('You have enable caching support but the W3TC_DYNAMIC_SECURITY definition is not set.', 'adrotate-pro').' <a href="'.admin_url('/admin.php?page=adrotate-settings&tab=misc').'">'.__('How to configure W3 Total Cache', 'adrotate-pro').'</a>.';
	}

	if($adrotate_config['borlabscache'] == "Y" AND !is_plugin_active('borlabs-cache/borlabs-cache.php')) {
		$error['borlabs_not_active'] = __('You have enable caching support but Borlabs Cache is not active on your site!', 'adrotate-pro').' <a href="'.admin_url('/admin.php?page=adrotate-settings&tab=misc').'">'.__('Disable Borlabs Cache Support', 'adrotate-pro').'</a>.';
	}
	if($adrotate_config['borlabscache'] == "Y" AND is_plugin_active('borlabs-cache/borlabs-cache.php')) {
		$borlabs_config = get_option('BorlabsCacheConfigInactive');
		if($borlabs_config['cacheActivated'] == 'yes' AND strlen($borlabs_config['fragmentCaching']) < 1) {
			$error['borlabs_fragment_error'] = __('You have enabled Borlabs Cache support but Fragment caching is not enabled!', 'adrotate-pro').' <a href="'.admin_url('/admin.php?page=borlabs-cache-fragments').'">'.__('Enable Fragment Caching', 'adrotate-pro').'</a>.';
		}
		unset($borlabs_config);
	}

	// Notifications
	if($adrotate_notifications['notification_email'] == 'Y' AND $adrotate_notifications['notification_mail_geo'] == 'N' AND $adrotate_notifications['notification_mail_status'] == 'N' AND $adrotate_notifications['notification_mail_queue'] == 'N' AND $adrotate_notifications['notification_mail_approved'] == 'N' AND $adrotate_notifications['notification_mail_rejected'] == 'N') {
		$error['mail_not_configured'] = __('You have enabled email notifications but did not select anything to be notified about. You are wasting server resources!', 'adrotate-pro').' <a href="'.admin_url('/admin.php?page=adrotate-settings&tab=notifications').'">'.__('Set up notifications', 'adrotate-pro').'</a>.';
	}

	// Geo Related
	$lookups = get_option('adrotate_geo_requests');

	if($license['status'] == 0 AND $adrotate_config['enable_geo'] == 5) {
		$error['geo_license'] = __('The AdRotate Geo service can only be used after you activate your license for this website.', 'adrotate-pro'). ' <a href="'.admin_url('/admin.php?page=adrotate-settings&tab=license').'">'.__('Activate license', 'adrotate-pro').'</a>!';
	}
	if(($adrotate_config['enable_geo'] == 3 OR $adrotate_config['enable_geo'] == 4 OR $adrotate_config['enable_geo'] == 5) AND $lookups > 0 AND $lookups < 1000) {
		$error['geo_almostnolookups'] = sprintf(__('You are running out of Geo Targeting Lookups. You have less than %d remaining lookups.', 'adrotate-pro'), $lookups);
	}
	if(($adrotate_config['enable_geo'] == 3 OR $adrotate_config['enable_geo'] == 4) AND $lookups < 1) {
		$error['geo_nolookups'] = __('Geo Targeting is no longer working because you have no more lookups.', 'adrotate-pro');
	}
	if($adrotate_config['enable_geo'] == 5 AND $lookups < 1) {
		$error['geo_nolookups'] = __('AdRotate Geo is no longer working because you have no more lookups for today. This resets at midnight UTC/GMT.', 'adrotate-pro');
	}
	if(($adrotate_config['enable_geo'] == 3 OR $adrotate_config['enable_geo'] == 4) AND (strlen($adrotate_config["geo_email"]) < 1 OR strlen($adrotate_config["geo_pass"]) < 1)) {
		$error['geo_maxmind_details'] = __('Geo Targeting is not working because your MaxMind account details are incomplete.', 'adrotate-pro').' <a href="'.admin_url('/admin.php?page=adrotate-settings&tab=geo').'">'.__('Enter MaxMind account details', 'adrotate-pro').'</a>.';
	}
	if($adrotate_config['enable_geo'] == 6 AND !isset($_SERVER["HTTP_CF_IPCOUNTRY"])) {
		$error['geo_cloudflare_header'] = __('Geo Targeting is not working. Check if IP Geolocation is enabled in your CloudFlare account.', 'adrotate-pro');
	}
	if($adrotate_config['enable_geo'] == 7 AND strlen($adrotate_config["geo_pass"]) < 1) {
		$error['geo_ipstack_details'] = __('Geo Targeting is not working because your ipstack account API key is missing.', 'adrotate-pro').' <a href="'.admin_url('/admin.php?page=adrotate-settings&tab=geo').'">'.__('Enter API key', 'adrotate-pro').'</a>.';
	}

	// Misc
	if(!is_writable(WP_CONTENT_DIR.'/'.$adrotate_config['banner_folder'])) {
		$error['banners_folder'] = __('Your AdRotate Banner folder is not writable or does not exist.', 'adrotate-pro').' <a href="https://ajdg.solutions/support/adrotate-manuals/manage-banner-images/" target="_blank">'.__('Set up your banner folder', 'adrotate-pro').'</a>.';
	}
	if(is_dir(WP_PLUGIN_DIR."/adrotate/")) {
		$error['adrotate_free_version_exists'] = __('You still have the free version of AdRotate installed. Please remove it!', 'adrotate-pro').' <a href="'.admin_url('/plugins.php?s=adrotate&plugin_status=all').'">'.__('Delete AdRotate plugin', 'adrotate-pro').'</a>.';
	}
	if(basename(__DIR__) != 'adrotate' AND basename(__DIR__) != 'adrotate-pro') {
		$error['adrotate_folder_names'] = __('Something is wrong with your installation of AdRotate Pro. Either the plugin is installed twice or your current installation has the wrong folder name. Please install the plugin properly!', 'adrotate-pro').' <a href="https://ajdg.solutions/support/adrotate-manuals/installing-adrotate-on-your-website/" target="_blank">'.__('Installation instructions', 'adrotate-pro').'</a>.';
	}

	$error = (isset($error) AND is_array($error)) ? $error : false;

	return $error;
}

/*-------------------------------------------------------------
 Name:      adrotate_notifications_dashboard
 Purpose:   Show dashboard notifications
-------------------------------------------------------------*/
function adrotate_notifications_dashboard() {
	global $current_user;

	if(current_user_can('adrotate_ad_manage')) {
		$displayname = (strlen($current_user->user_firstname) > 0) ? $current_user->user_firstname : $current_user->display_name;
		$page = (isset($_GET['page'])) ? $_GET['page'] : '';

		// These only show on AdRotate pages
		if(strpos($page, 'adrotate') !== false) {
			if(isset($_GET['hide']) AND $_GET['hide'] == 0) update_option('adrotate_hide_update', current_time('timestamp') + (7 * DAY_IN_SECONDS));
			if(isset($_GET['hide']) AND $_GET['hide'] == 1) update_option('adrotate_hide_review', 1);
			if(isset($_GET['hide']) AND $_GET['hide'] == 2) update_option('adrotate_hide_birthday', current_time('timestamp') + (10 * MONTH_IN_SECONDS));

			// Write a review
			$review_banner = get_option('adrotate_hide_review');
			$license = adrotate_get_license();
			if($license['status'] == 1 AND $review_banner != 1 AND $review_banner < (current_time('timestamp') - (8 * DAY_IN_SECONDS))) {
				$license = (!$license) ? 'single' : strtolower($license['type']);
				echo '<div class="ajdg-notification notice" style="">';
				echo '	<div class="ajdg-notification-logo" style="background-image: url(\''.plugins_url('/images/notification.png', __FILE__).'\');"><span></span></div>';
				echo '	<div class="ajdg-notification-message">Hello <strong>'.$displayname.'</strong>! You have been using <strong>AdRotate Professional</strong> for a few days. If you like the plugin, please share <strong>your experience</strong> and help promote AdRotate Pro.<br />Tell your followers that you use AdRotate Pro. A <a href="https://twitter.com/intent/tweet?hashtags=wordpress%2Cplugin%2Cadvertising&related=arnandegans%2Cwordpress&text=I%20am%20using%20AdRotate%20for%20@WordPress.%20Check%20it%20out.&url=https%3A%2F%2Fwordpress.org/plugins/adrotate/" target="_blank" class="ajdg-notification-act goosebox">Tweet</a> or <a href="https://www.facebook.com/sharer/sharer.php?u=https%3A%2F%2Fwordpress.org%2Fplugins%2Fadrotate%2F&amp;src=adrotate" target="_blank" class="ajdg-notification-act goosebox">Facebook Share</a> helps a lot and is super awesome!<br />If you have questions, complaints or something else that does not belong in a review, please use the <a href="'.admin_url('admin.php?page=adrotate-support').'">contact form</a>!</div>';
				echo '	<div class="ajdg-notification-cta">';
				echo '		<a href="https://ajdg.solutions/product/adrotate-pro-'.$license.'/?pk_campaign=adrotatepro&pk_keyword=review_notification#tab-reviews" class="ajdg-notification-act button-primary">Review AdRotate</a>';
				echo '		<a href="admin.php?page=adrotate&hide=1" class="ajdg-notification-dismiss">Maybe later</a>';
				echo '	</div>';
				echo '</div>';
			}

			// Birthday
			$birthday_banner = get_option('adrotate_hide_birthday');
			if($birthday_banner < current_time('timestamp') AND date('M', current_time('timestamp')) == 'Feb') {
				echo '<div class="ajdg-notification notice" style="">';
				echo '	<div class="ajdg-notification-logo" style="background-image: url(\''.plugins_url('/images/birthday.png', __FILE__).'\');"><span></span></div>';
				echo '	<div class="ajdg-notification-message">Hey <strong>'.$displayname.'</strong>! Did you know it is Arnan his birtyday this month? February 9th to be exact. Wish him a happy birthday via Telegram!<br />Who is Arnan? He made AdRotate for you - Check out his <a href="https://www.arnan.me/?pk_campaign=adrotatepro&pk_keyword=birthday_banner" target="_blank">website</a> or <a href="https://www.arnan.me/donate.html?pk_campaign=adrotatepro&pk_keyword=birthday_banner" target="_blank">send a gift</a>.</div>';
				echo '	<div class="ajdg-notification-cta">';
				echo '		<a href="https://t.me/arnandegans" target="_blank" class="ajdg-notification-act button-primary goosebox"><i class="icn-tg"></i>Wish Happy Birthday</a>';
				echo '		<a href="admin.php?page=adrotate&hide=2" class="ajdg-notification-dismiss">Done it</a>';
				echo '	</div>';
				echo '</div>';
			}
		}

		// Advert notifications, errors, important stuff
		$adrotate_has_error = adrotate_dashboard_error();
		if($adrotate_has_error) {
			echo '<div class="ajdg-notification notice" style="">';
			echo '	<div class="ajdg-notification-logo" style="background-image: url(\''.plugins_url('/images/notification.png', __FILE__).'\');"><span></span></div>';
			echo '	<div class="ajdg-notification-message"><strong>AdRotate Professional</strong> has detected '._n('one issue that requires', 'several issues that require', count($adrotate_has_error), 'adrotate-pro').' '.__('your attention:', 'adrotate').'<br />';
			foreach($adrotate_has_error as $error => $message) {
				echo '&raquo; '.$message.'<br />';
			}
			echo '	</div>';
			echo '</div>';
		}
	}

	if(current_user_can('update_plugins')) {
		// Updates are available
		$has_update = get_transient('ajdg_update_adrotatepro');
		$update_banner = get_option('adrotate_hide_update');
		if($update_banner < current_time('timestamp') AND $has_update !== false) {
			$plugin_version = get_plugins();
			$plugin_version = $plugin_version['adrotate-pro/adrotate-pro.php']['Version'];
			if(array_key_exists('version', $has_update) AND version_compare($plugin_version, $has_update['version'], '<')) {
				$license = adrotate_get_license();
				$now = current_time('timestamp');
				$oneyearago = $now - (DAY_IN_SECONDS * 365);
	
				if($license['status'] == 1 AND $license['created'] > $oneyearago) { // Valid, show update
					$message = '<strong>AdRotate Professional '.$has_update['version'].'</strong> is available now! You have version '.$plugin_version.'. Please update as soon as possible.<br />Updates often include new or updated features, bugfixes and the occasional security patch. Thank you!';
					$button_url = admin_url('update-core.php?force-check=1');
					$button_txt = 'Install update';
				} else if($license['status'] == 1 AND $license['created'] <= $oneyearago) { // License expired
					$message = '<strong>AdRotate Professional '.$has_update['version'].'</strong> is available! You have version '.$plugin_version.'.<br />Unfortunately your license has expired. Please get a new license so you can install the update. Thank you!';
					$button_url = 'https://ajdg.solutions/support/adrotate-manuals/adrotate-pro-license-renewal/?pk_campaign=adrotatepro&pk_keyword=update_notification';
					$button_txt = 'Get new license';
				} else { // No active license (mostly for new setups installing old versions)
					$message = '<strong>AdRotate Professional version '.$has_update['version'].' is available!</strong> You have version '.$plugin_version.'. You are missing out!<br />Activate your license to get access to updates and premium support!';
					$button_url = admin_url('admin.php?page=adrotate-settings&tab=license');
					$button_txt = 'Activate license';
				}
	
				echo '<div class="ajdg-notification notice" style="">';
				echo '	<div class="ajdg-notification-logo" style="background-image: url(\''.plugins_url('/images/notification.png', __FILE__).'\');"><span></span></div>';
				echo '	<div class="ajdg-notification-message">'.$message.'<br />For an overview of what has changed take a look at the <a href="https://ajdg.solutions/support/adrotate-development/?pk_campaign=adrotatepro&pk_keyword=update_notification" target="_blank">development page</a> and usually there is an article on <a href="https://ajdg.solutions/blog/?pk_campaign=adrotatepro&pk_keyword=update_notification" target="_blank">the blog</a> with more information as well.</div>';
				echo '	<div class="ajdg-notification-cta">';
				echo '		<a href="'.$button_url.'" class="ajdg-notification-act button-primary">'.$button_txt.'</a>';
				echo '		<a href="admin.php?page=adrotate&hide=0" class="ajdg-notification-dismiss">Later</a>';
				echo '	</div>';
				echo '</div>';
			}
		}
	
		// Finish update
		// Keep for manual updates
		$adrotate_db_version = get_option("adrotate_db_version");
		$adrotate_version = get_option("adrotate_version");
		if($adrotate_db_version['current'] < ADROTATE_DB_VERSION OR $adrotate_version['current'] < ADROTATE_VERSION) {
			echo '<div class="ajdg-notification notice" style="">';
			echo '	<div class="ajdg-notification-logo" style="background-image: url(\''.plugins_url('/images/notification.png', __FILE__).'\');"><span></span></div>';
			echo '	<div class="ajdg-notification-message">Thanks for updating <strong>'.$displayname.'</strong>! You have almost completed updating <strong>AdRotate</strong> to version <strong>'.ADROTATE_DISPLAY.'</strong>!<br />To complete the update <strong>click the button on the right</strong>. This may take a few seconds to complete!<br />For an overview of what has changed take a look at the <a href="https://ajdg.solutions/support/adrotate-development/?pk_campaign=adrotatepro&pk_keyword=finish_update_notification" target="_blank">development page</a> and usually there is an article on <a href="https://ajdg.solutions/blog/" target="_blank">the blog</a> with more information as well.</div>';
			echo '	<div class="ajdg-notification-cta">';
			echo '		<a href="admin.php?page=adrotate-settings&tab=maintenance&action=update-db" class="ajdg-notification-act button-primary update-button">Finish update</a>';
			echo '	</div>';
			echo '</div>';
		}
	}
}

/*-------------------------------------------------------------
 Name:      adrotate_welcome_pointer
 Purpose:   Show dashboard pointers
-------------------------------------------------------------*/
function adrotate_welcome_pointer() {
    $pointer_content = '<h3>AdRotate '.ADROTATE_DISPLAY.'</h3>';
    $pointer_content .= '<p>'.__('Thanks for choosing AdRotate Professional. Everything related to AdRotate is in this menu. If you need help getting started take a look at the', 'adrotate-pro').' <a href="http:\/\/ajdg.solutions\/support\/adrotate-manuals\/" target="_blank">'.__('manuals', 'adrotate-pro').'</a> '.__('and', 'adrotate-pro').' <a href="https:\/\/ajdg.solutions\/forums\/forum\/adrotate-for-wordpress\/" target="_blank">'.__('forums', 'adrotate-pro').'</a>. '.__('You can also ask questions via', 'adrotate-pro').' <a href="admin.php?page=adrotate-support">'.__('email', 'adrotate-pro').'</a> '.__('if you have a valid license.', 'adrotate-pro').' These links and more are also available in the help tab in the top right.</p>';

    $pointer_content .= '<p><strong>Ad blockers</strong><br />Disable your ad blocker in your browser so your adverts and dashboard show up correctly. Take a look at this manual to <a href="https://ajdg.solutions/support/adrotate-manuals/configure-adblockers-for-your-own-website/" target="_blank">whitelist your site</a>.</p>';
?>
	<script type="text/javascript">
		jQuery(document).ready(function($) {
			$('#toplevel_page_adrotate').pointer({
				'content':'<?php echo $pointer_content; ?>',
				'position':{ 'edge':'left', 'align':'middle'	},
				close: function() {
	                $.post(ajaxurl, {
	                    pointer:'adrotate_pro',
	                    action:'dismiss-wp-pointer'
	                });
				}
			}).pointer("open");
		});
	</script>
<?php
}

/*-------------------------------------------------------------
 Name:      adrotate_dashboard_help
 Purpose:   Help tab on all pages
-------------------------------------------------------------*/
function adrotate_dashboard_help() {
    $screen = get_current_screen();

    $screen->add_help_tab(array(
        'id' => 'adrotate_thanks',
        'title' => 'Thanks to you',
        'content' => '<h4>Thank you for using AdRotate</h4>'.
        '<p>AdRotate Professional is one of the most popular WordPress plugins for Advertising and is a household name for many companies and news sites around the world. AdRotate would not be possible without your support and my life would not be what it is today without your help.</p><p><em>- Arnan</em></p>'.
        '<p>My website <a href="https://ajdg.solutions/?pk_campaign=adrotatepro&pk_keyword=helptab" target="_blank">ajdg.solutions</a>.<br />My profile <a href="https://www.arnan.me/?pk_campaign=adrotatepro&pk_keyword=helptab" target="_blank">Arnan de Gans</a>.</p>'
		)
    );

    $screen->add_help_tab(array(
        'id' => 'adrotate_support',
        'title' => 'Getting Support',
        'content' => '<h4>Get help using AdRotate</h4>'.
        '<p>AdRotate has many guides and manuals as well as a Support Forum on the AdRotate website to answer most common questions.<br />All the relevant links to getting help and the Professional Services I offer can be found on the <a href="'.admin_url('admin.php?page=adrotate-support').'">Support dashboard</a>.</p>'.
        '<p>Exclusive for AdRotate Professional users there is a contact form right there in your dashboard, for extra fast support. Check out the <a href="'.admin_url('admin.php?page=adrotate-support').'">Support dashboard</a> for more information.</p>'.
        '<p><a href="https://ajdg.solutions/support/adrotate-manuals/?pk_campaign=adrotatepro&pk_keyword=helptab" target="_blank">AdRotate Manuals</a><br />AJdG Solutions <a href="https://ajdg.solutions/forums/forum/adrotate-for-wordpress/?pk_campaign=adrotatepro&pk_keyword=helptab" target="_blank">Support Forum</a><br /><a href="https://ajdg.solutions/recommended-products/?pk_campaign=adrotatepro&pk_keyword=helptab" target="_blank">Recommended products and services</a></p>'
		)
    );

    $screen->add_help_tab(array(
        'id' => 'adrotate_social',
        'title' => 'Spread the word',
        'content' => '<h4>Tell your friends</h4>'.

		'<p>Consider writing a review or sharing AdRotate in Social media if you like the plugin or if you find it useful. Writing a review and sharing AdRotate on social media costs you nothing but doing so is super helpful as promotion which helps to ensure future development.</p>'.
		'<p><a href="https://twitter.com/intent/tweet?hashtags=wordpress%2Cplugin%2Cadvertising%2Cadrotate&related=arnandegans%2Cwordpress&text=I%20am%20using%20AdRotate%20Pro%20for%20WordPress%20by%20@arnandegans.%20Check%20it%20out.&url=https%3A%2F%2Fwordpress.org/plugins/adrotate/" target="_blank" class="button-primary goosebox"><i class="icn-t"></i>'.__('Post Tweet').'</a> <a href="https://www.facebook.com/sharer/sharer.php?u=https%3A%2F%2Fwordpress.org%2Fplugins%2Fadrotate%2F&hashtag=#adrotate" target="_blank" class="button-primary goosebox"><i class="icn-fb"></i>'.__('Share on Facebook').'</a> <a class="button-primary" target="_blank" href="https://ajdg.solutions/product-category/adrotate-pro/">'.__('Write review on ajdg.solutions').'</a></p>
	<p><em>- '.__('Thank you very much for your help and support!').'</em></p>'
		)
    );
}

/*-------------------------------------------------------------
 Name:      adrotate_action_links
 Purpose:	Plugin page link
-------------------------------------------------------------*/
function adrotate_action_links($links) {
	$custom_actions = array();
	$custom_actions['adrotate-help'] = sprintf('<a href="%s">%s</a>', admin_url('admin.php?page=adrotate-support'), 'Support');
	$custom_actions['adrotate-news'] = sprintf('<a href="%s">%s</a>', 'https://ajdg.solutions/blog/?pk_campaign=adrotatepro&pk_keyword=action_links', 'News');
	$custom_actions['adrotate-ajdg'] = sprintf('<a href="%s" target="_blank">%s</a>', 'https://ajdg.solutions/?pk_campaign=adrotatepro&pk_keyword=action_links', 'AJdG Solutions');

	return array_merge($custom_actions, $links);
}

/*-------------------------------------------------------------
 Name:      adrotate_user_notice
 Purpose:   Credits shown on user statistics
-------------------------------------------------------------*/
function adrotate_user_notice() {

	echo '<table class="widefat" style="margin-top: .5em">';

	echo '<thead>';
	echo '<tr valign="top">';
	echo '	<th colspan="2">'.__('Your adverts', 'adrotate-pro').'</th>';
	echo '</tr>';
	echo '</thead>';

	echo '<tbody>';
	echo '<tr>';
	echo '<td><center><a href="https://ajdg.solutions/product-category/adrotate-pro/?pk_campaign=adrotatepro&pk_keyword=credits" title="AdRotate plugin for WordPress"><img src="'.plugins_url('/images/logo-60x60.png', __FILE__).'" alt="logo-60x60" width="60" height="60" /></a></center></td>';
	echo '<td>'.__('The overall stats do not take adverts from other advertisers into account.', 'adrotate-pro').'<br />'.__('All statistics are indicative. They do not nessesarily reflect results counted by other parties.', 'adrotate-pro').'<br />'.__('Your ads are published with', 'adrotate-pro').' <a href="https://ajdg.solutions/product-category/adrotate-pro/?pk_campaign=adrotatepro&pk_keyword=credits" target="_blank">AdRotate Professional for WordPress</a>.</td>';

	echo '</tr>';
	echo '</tbody>';

	echo '</table>';
	echo adrotate_trademark();
}

/*-------------------------------------------------------------
 Name:      adrotate_trademark
 Purpose:   Trademark notice
-------------------------------------------------------------*/
function adrotate_trademark() {
 return '<center><small>AdRotate<sup>&reg;</sup> '.__('is a registered trademark', 'adrotate-pro').'</small></center>';
}
?>