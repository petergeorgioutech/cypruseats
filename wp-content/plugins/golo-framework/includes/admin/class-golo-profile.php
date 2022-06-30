<?php
if ( !defined('ABSPATH') ) {
    exit; // Exit if accessed directly
}

if (!class_exists('Golo_Profile')) {

    /**
     * Class Golo_Profile
     */
    class Golo_Profile
    {

    	public function custom_user_profile_fields($user)
        {	
			$agent_id = $user->ID;
            ?>
            <h3><?php esc_html_e('Profile Info', 'golo-framework'); ?></h3>
            <table class="form-table">
                <tbody>
                	<tr class="author-avatar-image-wrap">
						<th><label for="author_avatar_image_url"><?php echo esc_html__('Avatar', 'golo-framework'); ?></label></th>
	                    <td>
	                    	<img class="show_author_avatar_image_url" src="<?php echo esc_attr(get_the_author_meta('author_avatar_image_url', $user->ID)); ?>" style="width: 96px;height: 96px; object-fit: cover;display: block;margin-bottom: 10px;">
	                    	<input type="text" name="author_avatar_image_url" id="author_avatar_image_url" value="<?php echo esc_attr(get_the_author_meta('author_avatar_image_url', $user->ID)); ?>" style="display: block;margin-bottom: 10px;max-width: 350px;width: 100%;">
	                    	<input type="hidden" name="author_avatar_image_id" id="author_avatar_image_id" value="<?php echo esc_attr(get_the_author_meta('author_avatar_image_id', $user->ID)); ?>">
							<input type='button' class="button-primary" value="Upload Image" id="uploadimage"/>
	                    </td>
                	</tr>
	                <tr class="author-mobile-number-wrap">
	                    <th><label for="<?php echo esc_attr(GOLO_METABOX_PREFIX . 'author_mobile_number');?>"><?php echo esc_html__('Mobile', 'golo-framework'); ?></label></th>
	                    <td><input type="text" name="<?php echo esc_attr(GOLO_METABOX_PREFIX . 'author_mobile_number');?>" id="<?php echo esc_attr(GOLO_METABOX_PREFIX . 'author_mobile_number'); ?>" value="<?php echo esc_attr(get_the_author_meta(GOLO_METABOX_PREFIX . 'author_mobile_number', $user->ID)); ?>" class="regular-text"></td>
	                </tr>
	                <tr class="author-fax-number-wrap">
	                    <th><label for="<?php echo esc_attr(GOLO_METABOX_PREFIX . 'author_fax_number'); ?>"><?php echo esc_html__('Fax Number', 'golo-framework'); ?></label></th>
	                    <td><input type="text" name="<?php echo esc_attr(GOLO_METABOX_PREFIX . 'author_fax_number'); ?>" id="<?php echo esc_attr(GOLO_METABOX_PREFIX . 'author_fax_number'); ?>" value="<?php echo esc_attr(get_the_author_meta(GOLO_METABOX_PREFIX . 'author_fax_number', $user->ID)); ?>" class="regular-text"></td>
	                </tr>
	                <tr class="author-skype-wrap">
	                    <th><label for="<?php echo esc_attr(GOLO_METABOX_PREFIX . 'author_skype'); ?>"><?php echo esc_html__('Skype', 'golo-framework'); ?></label></th>
	                    <td><input type="text" name="<?php echo esc_attr(GOLO_METABOX_PREFIX . 'author_skype'); ?>" id="<?php echo esc_attr(GOLO_METABOX_PREFIX . 'author_skype') ; ?>" value="<?php echo esc_attr(get_the_author_meta(GOLO_METABOX_PREFIX . 'author_skype', $user->ID)); ?>" class="regular-text"></td>
	                </tr>
            	</tbody>
            </table>

            <h2><?php echo esc_html__('Socials Profile', 'golo-framework'); ?></h2>
            <table class="form-table">
                <tbody>
                <tr class="author-facebook-url-wrap">
                    <th><label for="<?php echo esc_attr(GOLO_METABOX_PREFIX . 'author_facebook_url') ; ?>"><?php echo esc_html__('Facebook', 'golo-framework'); ?></label></th>
                    <td><input type="text" name="<?php echo esc_attr(GOLO_METABOX_PREFIX . 'author_facebook_url') ; ?>" id="<?php echo esc_attr(GOLO_METABOX_PREFIX . 'author_facebook_url') ; ?>" value="<?php echo esc_attr(get_the_author_meta(GOLO_METABOX_PREFIX . 'author_facebook_url', $user->ID)); ?>" class="regular-text"></td>
                </tr>
                <tr class="author-twitter-url-wrap">
                    <th><label for="<?php echo esc_attr(GOLO_METABOX_PREFIX . 'author_twitter_url') ; ?>"><?php echo esc_html__('Twitter', 'golo-framework'); ?></label></th>
                    <td><input type="text" name="<?php echo esc_attr(GOLO_METABOX_PREFIX . 'author_twitter_url') ; ?>" id="<?php echo esc_attr(GOLO_METABOX_PREFIX . 'author_twitter_url') ; ?>" value="<?php echo esc_attr(get_the_author_meta(GOLO_METABOX_PREFIX . 'author_twitter_url', $user->ID)); ?>" class="regular-text"></td>
                </tr>
                <tr class="author-instagram-url-wrap">
                    <th><label for="<?php echo esc_attr(GOLO_METABOX_PREFIX . 'author_instagram_url') ; ?>"><?php echo esc_html__('Instagram', 'golo-framework'); ?></label></th>
                    <td><input type="text" name="<?php echo esc_attr(GOLO_METABOX_PREFIX . 'author_instagram_url') ; ?>" id="<?php echo esc_attr(GOLO_METABOX_PREFIX . 'author_instagram_url') ; ?>" value="<?php echo esc_attr(get_the_author_meta(GOLO_METABOX_PREFIX . 'author_instagram_url', $user->ID)); ?>" class="regular-text"></td>
                </tr>
                <tr class="author-linkedin-url-wrap">
                    <th><label for="<?php echo esc_attr(GOLO_METABOX_PREFIX . 'author_linkedin_url') ; ?>"><?php echo esc_html__('LinkedIn', 'golo-framework'); ?></label></th>
                    <td><input type="text" name="<?php echo esc_attr(GOLO_METABOX_PREFIX . 'author_linkedin_url') ; ?>" id="<?php echo esc_attr(GOLO_METABOX_PREFIX . 'author_linkedin_url') ; ?>" value="<?php echo esc_attr(get_the_author_meta(GOLO_METABOX_PREFIX . 'author_linkedin_url', $user->ID)); ?>" class="regular-text"></td>
                </tr>
                <tr class="author-pinterest-url-wrap">
                    <th><label for="<?php echo esc_attr(GOLO_METABOX_PREFIX . 'author_pinterest_url'); ?>"><?php echo esc_html__('Pinterest', 'golo-framework'); ?></label></th>
                    <td><input type="text" name="<?php echo esc_attr(GOLO_METABOX_PREFIX . 'author_pinterest_url') ; ?>" id="<?php echo esc_attr(GOLO_METABOX_PREFIX . 'author_pinterest_url') ; ?>" value="<?php echo esc_attr(get_the_author_meta(GOLO_METABOX_PREFIX . 'author_pinterest_url', $user->ID)); ?>" class="regular-text"></td>
                </tr>
                <tr class="author-youtube-url-wrap">
                    <th><label for="<?php echo esc_attr(GOLO_METABOX_PREFIX . 'author_youtube_url') ; ?>"><?php echo esc_html__('Youtube', 'golo-framework'); ?></label></th>
                    <td><input type="text" name="<?php echo esc_attr(GOLO_METABOX_PREFIX . 'author_youtube_url'); ?>" id="<?php echo esc_attr(GOLO_METABOX_PREFIX . 'author_youtube_url'); ?>" value="<?php echo esc_attr(get_the_author_meta(GOLO_METABOX_PREFIX . 'author_youtube_url', $user->ID)); ?>" class="regular-text"></td>
                </tr>
                </tbody>
            </table>
            <?php
        }

        public function user_package_available($user_id)
        {
            $package_id = get_the_author_meta(GOLO_METABOX_PREFIX . 'package_id', $user_id);
            if (empty($package_id)) {
                return 0;
            } else {
                $golo_package = new Golo_Package();
                $package_unlimited_time = get_post_meta($package_id, GOLO_METABOX_PREFIX . 'package_unlimited_time', true);
                if ($package_unlimited_time == 0) {
                    $expired_date = $golo_package->get_expired_time($package_id, $user_id);
                    $today = time();
                    if ($today > $expired_date) {
                        return -1;
                    }
                }
                $package_num_places = get_the_author_meta(GOLO_METABOX_PREFIX . 'package_number_listings', $user_id);
                if ($package_num_places != -1 && $package_num_places < 1) {
                    return -2;
                }
            }
            return 1;
        }

        public function update_custom_user_profile_fields($user_id)
        {
        	global $current_user;
            wp_get_current_user();
			
            if (current_user_can('edit_user', $user_id)) {

				$author_avatar_image_url = isset($_POST['author_avatar_image_url']) ? golo_clean(wp_unslash($_POST['author_avatar_image_url'])) : '';
				$author_avatar_image_id  = isset($_POST['author_avatar_image_id']) ? golo_clean(wp_unslash($_POST['author_avatar_image_id'])) : '';
				$author_mobile_number    = isset($_POST[GOLO_METABOX_PREFIX . 'author_mobile_number']) ? golo_clean(wp_unslash($_POST[GOLO_METABOX_PREFIX . 'author_mobile_number'])) : '';
				$author_fax_number       = isset($_POST[GOLO_METABOX_PREFIX . 'author_fax_number']) ? golo_clean(wp_unslash($_POST[GOLO_METABOX_PREFIX . 'author_fax_number'])) : '';
				$author_skype            = isset($_POST[GOLO_METABOX_PREFIX . 'author_skype']) ? golo_clean(wp_unslash($_POST[GOLO_METABOX_PREFIX . 'author_skype'])) : '';
				$author_facebook_url     = isset($_POST[GOLO_METABOX_PREFIX . 'author_facebook_url']) ? esc_url_raw(wp_unslash($_POST[GOLO_METABOX_PREFIX . 'author_facebook_url'])) : '';
				$author_twitter_url      = isset($_POST[GOLO_METABOX_PREFIX . 'author_twitter_url']) ? esc_url_raw(wp_unslash($_POST[GOLO_METABOX_PREFIX . 'author_twitter_url'])) : '';
				$author_linkedin_url     = isset($_POST[GOLO_METABOX_PREFIX . 'author_linkedin_url']) ? esc_url_raw(wp_unslash($_POST[GOLO_METABOX_PREFIX . 'author_linkedin_url'])) : '';
				$author_pinterest_url    = isset($_POST[GOLO_METABOX_PREFIX . 'author_pinterest_url']) ? esc_url_raw(wp_unslash($_POST[GOLO_METABOX_PREFIX . 'author_pinterest_url'])) : '';
				$author_instagram_url    = isset($_POST[GOLO_METABOX_PREFIX . 'author_instagram_url']) ? esc_url_raw(wp_unslash($_POST[GOLO_METABOX_PREFIX . 'author_instagram_url'])) : '';
				$author_youtube_url      = isset($_POST[GOLO_METABOX_PREFIX . 'author_youtube_url']) ? esc_url_raw(wp_unslash($_POST[GOLO_METABOX_PREFIX . 'author_youtube_url'])) : '';

				update_user_meta($user_id, 'author_avatar_image_url', $author_avatar_image_url);
				update_user_meta($user_id, 'author_avatar_image_id', $author_avatar_image_id);
                update_user_meta($user_id, GOLO_METABOX_PREFIX . 'author_mobile_number', $author_mobile_number);
                update_user_meta($user_id, GOLO_METABOX_PREFIX . 'author_fax_number', $author_fax_number);
                update_user_meta($user_id, GOLO_METABOX_PREFIX . 'author_skype', $author_skype);
                update_user_meta($user_id, GOLO_METABOX_PREFIX . 'author_facebook_url', $author_facebook_url);
                update_user_meta($user_id, GOLO_METABOX_PREFIX . 'author_twitter_url', $author_twitter_url);
                update_user_meta($user_id, GOLO_METABOX_PREFIX . 'author_linkedin_url', $author_linkedin_url);
                update_user_meta($user_id, GOLO_METABOX_PREFIX . 'author_pinterest_url', $author_pinterest_url);
                update_user_meta($user_id, GOLO_METABOX_PREFIX . 'author_instagram_url', $author_instagram_url);
                update_user_meta($user_id, GOLO_METABOX_PREFIX . 'author_youtube_url', $author_youtube_url);
                
            }
        }

        function my_profile_upload_js() 
        { 
        	wp_enqueue_media();
        	?>
		    <script type="text/javascript">
		        jQuery(document).ready(function() {
		        
		            jQuery(document).find("input[id^='uploadimage']").on('click', function(e){
		            	e.preventDefault();

			            var button = jQuery(this),
			                custom_uploader = wp.media({
				            title: 'Insert image',
				            library : {
				                // uncomment the next line if you want to attach image to the current post
				                // uploadedTo : wp.media.view.settings.post.id, 
				                type : 'image'
				            },
				            button: {
				                text: 'Use this image' // button label text
				            },
				            multiple: false // for multiple image selection set to true
				        }).on('select', function() { // it also has "open" and "close" events 
				            var attachment = custom_uploader.state().get('selection').first().toJSON();
				            jQuery(button).removeClass('button').html('<img class="true_pre_image" src="' + attachment.url + '" style="max-width:95%;display:block;" />').next().val(attachment.id).next().show();
				            jQuery('#author_avatar_image_url').val( attachment.url );
				            jQuery('#author_avatar_image_id').val( attachment.id );
		                    jQuery('.show_author_avatar_image_url').attr('src',  attachment.url);
				        })
				        .open();
		            });
		        });
		    </script>
			<?php 
		}

    }

}