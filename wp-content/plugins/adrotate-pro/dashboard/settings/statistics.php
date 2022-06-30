<?php
/* ------------------------------------------------------------------------------------
*  COPYRIGHT AND TRADEMARK NOTICE
*  Copyright 2008-2019 Arnan de Gans. All Rights Reserved.
*  ADROTATE is a registered trademark of Arnan de Gans.

*  COPYRIGHT NOTICES AND ALL THE COMMENTS SHOULD REMAIN INTACT.
*  By using this code you agree to indemnify Arnan de Gans from any
*  liability that might arise from it's use.
------------------------------------------------------------------------------------ */
?>

<form name="settings" id="post" method="post" action="admin.php?page=adrotate-settings&tab=stats">
<?php wp_nonce_field('adrotate_settings','adrotate_nonce_settings'); ?>
<input type="hidden" name="adrotate_settings_tab" value="<?php echo $active_tab; ?>" />

<h2><?php _e('Statistics', 'adrotate-pro'); ?></h2>
<span class="description"><?php _e('Track statistics for your adverts.', 'adrotate-pro'); ?></span>
<table class="form-table">
	<tr>
		<th valign="top"><?php _e('How to track stats', 'adrotate-pro'); ?></th>
		<td>
			<select name="adrotate_stats">
				<option value="0" <?php if($adrotate_config['stats'] == 0) { echo 'selected'; } ?>><?php _e('Disabled - Do not track stats', 'adrotate-pro'); ?></option>
				<option value="1" <?php if($adrotate_config['stats'] == 1) { echo 'selected'; } ?>>AdRotate Statistics (<?php _e('Default', 'adrotate-pro'); ?>)</option>
				<option value="2" <?php if($adrotate_config['stats'] == 2) { echo 'selected'; } ?>>Matomo</option>
				<option value="4" <?php if($adrotate_config['stats'] == 4) { echo 'selected'; } ?>>Google Global Tag</option>
				<option value="5" <?php if($adrotate_config['stats'] == 5) { echo 'selected'; } ?>>Google Tag Manager (<?php _e('Advanced', 'adrotate-pro'); ?>)</option>
			</select><br />
			<span class="description">
				<strong>AdRotate Statistics</strong> - <?php _e('Tracks impressions and clicks locally', 'adrotate-pro'); ?> - <a href="https://ajdg.solutions/support/adrotate-manuals/adrotate-statistics/?pk_campaign=adrotatepro&pk_keyword=settings_stats&pk_content=adrotate_tracker" target="_blank"><?php _e('Setup guide', 'adrotate-pro'); ?></a>.<br />
				<strong><?php _e('Supports:', 'adrotate-pro'); ?></strong> <em><?php _e('Clicks and Impressions, Click and impression limits, impression spread for schedules. Javascript/HTML5 adverts will only track impressions.', 'adrotate-pro'); ?></em><br /><br />

				<strong>Matomo</strong> - <?php _e('Requires the Matomo tracking code installed in your sites footer. See the manual for details.', 'adrotate-pro'); ?> - <a href="https://ajdg.solutions/support/adrotate-manuals/track-advert-stats-with-matomo/?pk_campaign=adrotatepro&pk_keyword=settings_stats&pk_content=matomotracker" target="_blank"><?php _e('Setup guide', 'adrotate-pro'); ?></a>.<br />
				<strong><?php _e('Supports:', 'adrotate-pro'); ?></strong> <em><?php _e('Clicks and Impressions via events. Javascript/HTML5 adverts will only track impressions.', 'adrotate-pro'); ?></em><br /><br />

				<strong>Google Global Tag</strong> - <?php _e('Requires Google Global Tag tracking code installed in your sites footer!', 'adrotate-pro'); ?> - <a href="https://ajdg.solutions/support/adrotate-manuals/track-advert-stats-with-google-analytics/installing-and-using-google-global-site-tag/?pk_campaign=adrotatepro&pk_keyword=settings_stats&pk_content=globaltag_tracker" target="_blank"><?php _e('Setup guide', 'adrotate-pro'); ?></a>.<br />
				<strong><?php _e('Supports:', 'adrotate-pro'); ?></strong> <em><?php _e('Clicks and Impressions via events. Javascript/HTML5 adverts will only track impressions.', 'adrotate-pro'); ?></em><br /><br />

				<strong>Google Tag Manager</strong> - <?php _e('Requires Google Tag Manager installed in your sites head tag and a Google Analytics Account!', 'adrotate-pro'); ?>  - <a href="https://ajdg.solutions/support/adrotate-manuals/track-advert-stats-with-google-analytics/installing-and-using-google-tag-manager/?pk_campaign=adrotatepro&pk_keyword=settings_stats&pk_content=tagmanager_tracker" target="_blank"><?php _e('Setup guide', 'adrotate-pro'); ?></a>.<br />
				<strong><?php _e('Supports:', 'adrotate-pro'); ?></strong> <em><?php _e('Clicks and Impressions via custom events. Javascript/HTML5 adverts will only track impressions.', 'adrotate-pro'); ?></em><br /><br />
			</span>
		</td>
	</tr>
</table>

<h3><?php _e('AdRotate Statistics', 'adrotate-pro'); ?></h3></td>
<span class="description"><?php _e('The settings below are for the internal tracker and have no effect when using Google Analytics or Matomo.', 'adrotate-pro'); ?></span>
<table class="form-table">
	<tr>
		<th valign="top"><?php _e('Admin stats', 'adrotate-pro'); ?></th>
		<td>
			<label for="adrotate_enable_admin_stats"><input type="checkbox" name="adrotate_enable_admin_stats" id="adrotate_enable_admin_stats" <?php if($adrotate_config['enable_admin_stats'] == 'Y') { ?>checked="checked" <?php } ?> /> <?php _e('Track statistics from admin users.', 'adrotate-pro'); ?></label>
		</td>
	</tr>
	<tr>
		<th valign="top"><?php _e('Logged in impressions', 'adrotate-pro'); ?></th>
		<td>
			<label for="adrotate_enable_loggedin_impressions"><input type="checkbox" name="adrotate_enable_loggedin_impressions" id="adrotate_enable_loggedin_impressions" <?php if($adrotate_config['enable_loggedin_impressions'] == 'Y') { ?>checked="checked" <?php } ?> /> <?php _e('Track impressions from logged in users.', 'adrotate-pro'); ?></label>
		</td>
	</tr>
	<tr>
		<th valign="top"><?php _e('Logged in clicks', 'adrotate-pro'); ?></th>
		<td>
			<label for="adrotate_enable_loggedin_clicks"><input type="checkbox" name="adrotate_enable_loggedin_clicks" id="adrotate_enable_loggedin_clicks" <?php if($adrotate_config['enable_loggedin_clicks'] == 'Y') { ?>checked="checked" <?php } ?> /> <?php _e('Track clicks from logged in users.', 'adrotate-pro'); ?></label>
		</td>
	</tr>
	<tr>
		<th valign="top"><?php _e('Impression timer', 'adrotate-pro'); ?></th>
		<td>
			<input name="adrotate_impression_timer" type="text" class="search-input" size="6" value="<?php echo $adrotate_config['impression_timer']; ?>" autocomplete="off" /> <?php _e('Seconds.', 'adrotate-pro'); ?><br />
			<span class="description"><?php _e('Default: 60.', 'adrotate-pro'); ?> <?php _e('This number may not be empty, be lower than 10 or exceed 3600 (1 hour).', 'adrotate-pro'); ?></span>
		</td>
	</tr>
	<tr>
		<th valign="top"><?php _e('Click timer', 'adrotate-pro'); ?></th>
		<td>
			<input name="adrotate_click_timer" type="text" class="search-input" size="6" value="<?php echo $adrotate_config['click_timer']; ?>" autocomplete="off" /> <?php _e('Seconds.', 'adrotate-pro'); ?><br />
			<span class="description"><?php _e('Default: 86400.', 'adrotate-pro'); ?> <?php _e('This number may not be empty, be lower than 60 or exceed 86400 (24 hours).', 'adrotate-pro'); ?></span>
		</td>
	</tr>
</table>

<h3><?php _e('Google Analytics', 'adrotate-pro'); ?></h3></td>
<span class="description"><?php _e('The settings below only have effect when you use Google Analytics. Either the Universal Tracker of the Global Tag Tracker.', 'adrotate-pro'); ?></span>
<table class="form-table">
	<tr>
		<th valign="top"><?php _e('Click value', 'adrotate-pro'); ?></th>
		<td>
			<input name="adrotate_google_click_value" type="text" class="search-input" size="6" value="<?php echo $adrotate_config['google_click_value']; ?>" autocomplete="off" /><br />
			<span class="description"><?php _e('Default: 1.00.', 'adrotate-pro'); ?> <?php _e('This number may not be empty, be lower than 0.1 or higher than 10.', 'adrotate-pro'); ?></span>
		</td>
	</tr>
	<tr>
		<th valign="top"><?php _e('Impression value', 'adrotate-pro'); ?></th>
		<td>
			<input name="adrotate_google_impression_value" type="text" class="search-input" size="6" value="<?php echo $adrotate_config['google_impression_value']; ?>" autocomplete="off" /><br />
			<span class="description"><?php _e('Default: 2.00.', 'adrotate-pro'); ?> <?php _e('This number may not be empty, be lower than 0.1 or higher than 10.', 'adrotate-pro'); ?></span>
		</td>
	</tr>
</table>

<p class="submit">
  	<input type="submit" name="adrotate_save_options" class="button-primary" value="<?php _e('Update Options', 'adrotate-pro'); ?>" />
</p>
</form>