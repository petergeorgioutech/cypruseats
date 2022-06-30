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

<form name="settings" id="post" method="post" action="admin.php?page=adrotate-settings&tab=maintenance">
<?php wp_nonce_field('adrotate_settings','adrotate_nonce_settings'); ?>
<input type="hidden" name="adrotate_settings_tab" value="<?php echo $active_tab; ?>" />

<h2><?php _e('Maintenance', 'adrotate-pro'); ?></h2>
<span class="description"><?php _e('Use these functions when you are running into trouble with your adverts or you notice your database is slow, unresponsive and sluggish. Normally you should not need these functions, but sometimes they are a lifesaver!', 'adrotate-pro'); ?></span>
<table class="form-table">
	<tr>
		<th valign="top"><?php _e('Check adverts', 'adrotate-pro'); ?></th>
		<td>
			<input type="submit" id="post-role-submit" name="adrotate_evaluate_submit" value="<?php _e('Check all adverts for configuration errors', 'adrotate-pro'); ?>" class="button-secondary" onclick="return confirm('<?php _e('You are about to check all adverts for errors.', 'adrotate-pro'); ?>\n\n<?php _e('This might take a few seconds!', 'adrotate-pro'); ?>\n\n<?php _e('OK to continue, CANCEL to stop.', 'adrotate-pro'); ?>')" />
			<br /><br />
			<span class="description"><?php _e('Apply all evaluation rules to all adverts to see if any error slipped in.', 'adrotate-pro'); ?></span>
		</td>
	</tr>
	<tr>
		<th valign="top"><?php _e('Clean-up Setup', 'adrotate-pro'); ?></th>
		<td>
			<input type="submit" id="post-role-submit" name="adrotate_cleanup_submit" value="<?php _e('Clean-up database and old files', 'adrotate-pro'); ?>" class="button-secondary" onclick="return confirm('<?php _e('You are about to do maintenance on your setup of AdRotate Professional.', 'adrotate-pro'); ?>\n\n<?php _e('This optionally may delete expired schedules, old statistics and tries to delete unused advert images and old export files.', 'adrotate-pro'); ?>\n\n<?php _e('Are you sure you want to continue?', 'adrotate-pro'); ?>\n<?php _e('THIS ACTION CAN NOT BE UNDONE!', 'adrotate-pro'); ?>')" />
			<br /><br />
			<label for="adrotate_db_cleanup_db"><input type="checkbox" name="adrotate_db_cleanup_db" value="0" checked disabled /> <?php _e('Basic database maintenance.', 'adrotate-pro'); ?></label><br />
			<label for="adrotate_db_cleanup_schedules"><input type="checkbox" name="adrotate_db_cleanup_schedules" id="adrotate_db_cleanup_schedules" value="1" /> <?php _e('Delete expired schedules.', 'adrotate-pro'); ?></label><br />
			<label for="adrotate_db_cleanup_statistics"><input type="checkbox" name="adrotate_db_cleanup_statistics" id="adrotate_db_cleanup_statistics" value="1" /> <?php _e('Delete statistics older than 356 days.', 'adrotate-pro'); ?></label><br />
			<label for="adrotate_db_cleanup_trash"><input type="checkbox" name="adrotate_db_cleanup_trash" id="adrotate_db_cleanup_trash" value="1" /> <?php _e('Delete all adverts and relevant data from the trash.', 'adrotate-pro'); ?></label><br />
			<label for="adrotate_asset_cleanup_assets"><input type="checkbox" name="adrotate_asset_cleanup_assets" id="adrotate_asset_cleanup_assets" value="1" /> <?php _e('Delete unused advert images.', 'adrotate-pro'); ?></label><br />
			<label for="adrotate_asset_cleanup_exportfiles"><input type="checkbox" name="adrotate_asset_cleanup_exportfiles" id="adrotate_asset_cleanup_exportfiles" value="1" /> <?php _e('Delete leftover export files.', 'adrotate-pro'); ?></label><br />
			<span class="description"><?php _e('For when you create an advert, group or schedule and it does not save or keep changes you make. Or updates are not shown while your license is active.', 'adrotate-pro'); ?><br /><?php _e('Additionally you can delete old schedules, statistics, trashed adverts, unused advert images and old export files. Running this routine from time to time may improve the speed of your site but is generally not necessary.', 'adrotate-pro'); ?></span>
		</td>
	</tr>
</table>
<span class="description"><?php _e('DISCLAIMER: The above functions are intended to be used to OPTIMIZE your database or clean up overhead data. They only apply to files and database items related to AdRotate and not to other settings or other parts of WordPress! Always always make a backup! If for any reason your data is lost, damaged or otherwise becomes unusable in any way or by any means in whichever way I will not take responsibility. You should always have a backup of your database. These functions do NOT destroy data. If data is lost, damaged or unusable in any way, your database likely was beyond repair already. Claiming it worked before clicking these buttons is not a valid point in any case.', 'adrotate-pro'); ?></span>

<h2><?php _e('Status indicators', 'adrotate-pro'); ?></h2>
<table class="form-table">
	<tr>
		<th width="15%"><?php _e('Current status of adverts', 'adrotate-pro'); ?></th>
		<td colspan="3"><?php _e('Normal', 'adrotate-pro'); ?>: <?php echo $advert_status['normal']; ?>, <?php _e('Over Limit', 'adrotate-pro'); ?>: <?php echo $advert_status['limit']; ?>, <?php _e('Error', 'adrotate-pro'); ?>: <?php echo $advert_status['error']; ?>, <?php _e('Expired', 'adrotate-pro'); ?>: <?php echo $advert_status['expired']; ?>, <?php _e('Expires Soon', 'adrotate-pro'); ?>: <?php echo $advert_status['expiressoon']; ?>, <?php _e('Unknown', 'adrotate-pro'); ?>: <?php echo $advert_status['unknown']; ?>.</td>
	</tr>
	<tr>
		<th width="15%"><?php _e('Banners/assets Folder', 'adrotate-pro'); ?></th>
		<td colspan="3">
			<?php
			echo WP_CONTENT_DIR.'/'.$adrotate_config['banner_folder'].'/ -> ';
			echo (is_writeable(WP_CONTENT_DIR.'/'.$adrotate_config['banner_folder']).'/') ? '<span style="color:#009900;">'.__('Exists and appears writable', 'adrotate-pro').'</span>' : '<span style="color:#CC2900;">'.__('Not writable or does not exist', 'adrotate-pro').'</span>';
			?>
		</td>
	</tr>
	<tr>
		<th width="15%"><?php _e('Reports Folder', 'adrotate-pro'); ?></th>
		<td colspan="3">
			<?php
			echo WP_CONTENT_DIR.'/reports/'.' -> ';
			echo (is_writable(WP_CONTENT_DIR.'/reports/')) ? '<span style="color:#009900;">'.__('Exists and appears writable', 'adrotate-pro').'</span>' : '<span style="color:#CC2900;">'.__('Not writable or does not exist', 'adrotate-pro').'</span>';
			?>
		</td>
	</tr>
	<tr>
		<th width="15%"><?php _e('ads.txt file', 'adrotate-pro'); ?></th>
		<td colspan="3">
			<?php
			echo ABSPATH.$adrotate_config['adstxt_file'].'ads.txt. -> ';
			echo (file_exists(ABSPATH.$adrotate_config['adstxt_file'].'ads.txt')) ? '<span style="color:#009900;">'.__('Exists', 'adrotate-pro').'</span>' : '<span style="color:#CC2900;">'.__('Not found', 'adrotate-pro').'</span>';
			?>
		</td>
	</tr>
	<tr>
		<th width="15%"><?php _e('Fix folder/ads.txt issue', 'adrotate-pro'); ?></th>
		<td colspan="3">
			<input type="submit" id="post-role-submit" name="adrotate_create_folders_submit" value="<?php _e('Create missing folders/files', 'adrotate-pro'); ?>" class="button-secondary" onclick="return confirm('<?php _e('You are about to create a banners folder, reports folder and ads.txt file. If these already exists the task is skipped.', 'adrotate-pro'); ?>\n\n<?php _e('This may fail due to file permissions set by your hosting provider. Contact them if the issue is not resolved after using this function.', 'adrotate-pro'); ?>\n\n<?php _e('Are you sure you want to continue?', 'adrotate-pro'); ?>')" />
		</td>
	</tr>
	<tr>
		<th width="15%"><?php _e('Check adverts for errors', 'adrotate-pro'); ?></th>
		<td width="35%"><?php if(!$adevaluate) '<span style="color:#CC2900;">'._e('Not scheduled!', 'adrotate-pro').'</span>'; else echo '<span style="color:#009900;">'.date_i18n(get_option('date_format')." H:i", $adevaluate).'</span>'; ?></td>
		<th width="15%"><?php _e('Send email notifications', 'adrotate-pro'); ?></th>
		<td><?php if(!$adschedule) '<span style="color:#CC2900;">'._e('Not scheduled!', 'adrotate-pro').'</span>'; else echo '<span style="color:#009900;">'.date_i18n(get_option('date_format')." H:i", $adschedule).'</span>'; ?></td>
	</tr>
	<tr>
		<th><?php _e('Delete adverts from trash', 'adrotate-pro'); ?></th>
		<td><?php if(!$trash) '<span style="color:#CC2900;">'._e('Not scheduled!', 'adrotate-pro').'</span>'; else echo '<span style="color:#009900;">'.date_i18n(get_option('date_format')." H:i", $trash).'</span>'; ?></td>
		<th><?php _e('Delete expired trackerdata', 'adrotate-pro'); ?></th>
		<td><?php if(!$tracker) '<span style="color:#CC2900;">'._e('Not scheduled!', 'adrotate-pro').'</span>'; else echo '<span style="color:#009900;">'.date_i18n(get_option('date_format')." H:i", $tracker).'</span>'; ?></td>
	</tr>
	<tr>
		<th><?php _e('Delete expired adverts', 'adrotate-pro'); ?></th>
		<td><?php if(!$autodelete) '<span style="color:#CC2900;">'._e('Not scheduled!', 'adrotate-pro').'</span>'; else echo '<span style="color:#009900;">'.date_i18n(get_option('date_format')." H:i", $autodelete).'</span>'; ?></td>
		<th>&nbsp;</th>
		<td>&nbsp;</td>
	</tr>
	<tr>
		<th valign="top"><?php _e('Background tasks', 'adrotate-pro'); ?></th>
		<td colspan="3">
			<a class="button" href="<?php echo admin_url('admin.php?page=adrotate-settings&tab=maintenance&action=reset-tasks'); ?>"><?php _e('Reset background tasks', 'adrotate-pro'); ?></a>
		</td>
	</tr>
	<tr>
		<th valign="top"><?php _e('Update server response', 'adrotate-pro'); ?></th>
		<td colspan="3">
			<?php
			$update_response = get_transient('ajdg_update_response');
			if($update_response) {
				$api_status = $update_response['last_checked'].' - '.$update_response['code'].' '.$update_response['message'];
				echo ($update_response['code'] != 200) ? ' <span style="color:#CC2900;">'.$api_status.'</span>' : '<span style="color:#009900;">'.$api_status.'</span>';
			} else {
				echo 'N/A';
			}
			?>
		</td>
	</tr>
	<tr>
		<th valign="top"><?php _e('Geo Targeting server status', 'adrotate-pro'); ?></th>
		<td colspan="3">
			<?php
			$geo_response = get_transient('ajdg_geo_response');
			if($geo_response) {
				$geo_status = $geo_response['last_checked'].' - '.$geo_response['code'].' '.$geo_response['message'];
				echo ($geo_response['code'] != 200) ? ' <span style="color:#CC2900;">'.$geo_status.'</span>' : '<span style="color:#009900;">'.$geo_status.'</span>';
			} else {
				echo 'N/A';
			}
			?>
		</td>
	</tr>
	<tr>
		<th valign="top"><?php _e('Unsupported plugins', 'adrotate'); ?></th>
		<td colspan="3">
			<a class="button" href="admin.php?page=adrotate-settings&tab=maintenance&action=disable-3rdparty"><?php _e('Disable 3rd party plugins', 'adrotate'); ?></a>
			<?php if(is_plugin_active('adrotate-extra-settings/adrotate-extra-settings.php') OR is_plugin_active('adrotate-email-add-on/adrotate-email-add-on.php') OR is_plugin_active('ad-builder-for-adrotate/ad-builder-for-adrotate.php') OR is_plugin_active('extended-adrotate-ad-placements/index.php')) { ?>
			<span style="color:#CC2900;"> <?php _e('One or more unsupported 3rd party plugins detected.', 'adrotate'); ?></span>
			<?php } ?>
			<br /><br />
			<span class="description"><?php _e('These are plugins that alter functions of AdRotate or highjack parts of the dashboard which may affect security and/or stability.', 'adrotate'); ?></span>
		</td>
	</tr>
</table>

<h2><?php _e('Internal Versions', 'adrotate-pro'); ?></h2>
<span class="description"><?php _e('Unless you experience database issues or a warning shows below, these numbers are not really relevant for troubleshooting. Support may ask for them to verify your database status.', 'adrotate-pro'); ?></span>
<table class="form-table">
	<tr>
		<th width="15%" valign="top"><?php _e('AdRotate version', 'adrotate-pro'); ?></th>
		<td><?php _e('Current:', 'adrotate-pro'); ?> <?php echo '<span style="color:#009900;">'.$adrotate_version['current'].'</span>'; ?> <?php if($adrotate_version['current'] != ADROTATE_VERSION) { echo '<span style="color:#CC2900;">'; _e('Should be:', 'adrotate-pro'); echo ' '.ADROTATE_VERSION; echo '</span>'; } ?><br /><?php _e('Previous:', 'adrotate-pro'); ?> <?php echo $adrotate_version['previous']; ?></td>
		<th width="15%" valign="top"><?php _e('Database version', 'adrotate-pro'); ?></th>
		<td><?php _e('Current:', 'adrotate-pro'); ?> <?php echo '<span style="color:#009900;">'.$adrotate_db_version['current'].'</span>'; ?> <?php if($adrotate_db_version['current'] != ADROTATE_DB_VERSION) { echo '<span style="color:#CC2900;">'; _e('Should be:', 'adrotate-pro'); echo ' '.ADROTATE_DB_VERSION; echo '</span>'; } ?><br /><?php _e('Previous:', 'adrotate-pro'); ?> <?php echo $adrotate_db_version['previous']; ?></td>
	</tr>
	<tr>
		<th valign="top"><?php _e('Manual upgrade', 'adrotate-pro'); ?></th>
		<td colspan="3">
			<a class="button" href="admin.php?page=adrotate-settings&tab=maintenance&action=update-db" onclick="return confirm('<?php _e('YOU ARE ABOUT TO DO A MANUAL UPDATE FOR ADROTATE.', 'adrotate'); ?>\n<?php _e('Make sure you have a database backup!', 'adrotate-pro'); ?>\n\n<?php _e('This might take a while and may slow down your site during this action!', 'adrotate-pro'); ?>\n\n<?php _e('OK to continue, CANCEL to stop.', 'adrotate'); ?>')"><?php _e('Update Settings and Database', 'adrotate-pro'); ?></a>
		</td>
	</tr>
</table>

<p class="submit">
  	<input type="submit" name="adrotate_save_options" class="button-primary" value="<?php _e('Update Options', 'adrotate-pro'); ?>" />
</p>
</form>