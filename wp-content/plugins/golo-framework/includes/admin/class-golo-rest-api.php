<?php
if ( !defined('ABSPATH') ) {
    exit; // Exit if accessed directly
}

if (!class_exists('Golo_Rest_API')) {

    /**
     * Class Golo_Rest_API
     */
    class Golo_Rest_API
    {

    	public function register_fields_api()
		{

			register_rest_route( 'global', 'countries', array(
				'methods'  => 'GET',
				'callback' => function () {
		            return golo_get_countries();
		        },
		        'permission_callback' => '__return_true'
		    ) );

		    // Register post image url
		    register_rest_field (
	            'post',
	            'featured_media_url',
	            array(
	                'get_callback' => function ( $object ) {
	                    if( $object['featured_media'] ){
					        $img = wp_get_attachment_image_src( $object['featured_media'], 'full' );
					        return $img[0];
					    }
					    return false;
	                },
	            )
	        );

			// Register featured image url
		    register_rest_field (
	            'place',
	            'featured_media_url',
	            array(
	                'get_callback' => function ( $object ) {
	                    if( $object['featured_media'] ){
					        $img = wp_get_attachment_image_src( $object['featured_media'], 'full' );
					        return $img[0];
					    }
					    return false;
	                },
	            )
	        );

	        $meta_box_configs = &glf_get_meta_boxes_config();
			$meta_box_field_keys = $this->get_meta_box_keys($meta_box_configs);

			// Add place field
			foreach ($meta_box_field_keys as $meta_id => $field_value) {

				register_rest_field(
		            'place',
		            $meta_id,
		            array(
		                'get_callback' => function ( $object ) use ( $meta_id ) {
		                    // Get field as single value from post meta.
		                    return get_post_meta( $object['id'], $meta_id, true );
		                },
		            )
		        );
			}

			// Add place gallery images
			$place_gallery = GOLO_METABOX_PREFIX . 'place_images';
			register_rest_field(
	            'place',
	            $place_gallery,
	            array(
	                'get_callback' => function ( $object ) use ( $place_gallery ) {
	                	$place_gallery = get_post_meta( $object['id'], GOLO_METABOX_PREFIX . 'place_images', true);
	                	$place_gallery = explode('|', $place_gallery);
		                $count = count($place_gallery);
		                $place_gallery_url = array();
		                foreach ( $place_gallery as $key => $image ) :
		                    $image_full_src = wp_get_attachment_url( $image, 'full');
		                    $place_gallery_url[] = $image_full_src;
		                endforeach;

	                    // Get field as single value from post meta.
	                    return $place_gallery_url;
	                },
	            )
	        );

			// Add place rating
			$place_rating = GOLO_METABOX_PREFIX . 'place_rating';
			register_rest_field(
	            'place',
	            $place_rating,
	            array(
	                'get_callback' => function ( $object ) use ( $place_rating ) {
	                	global $wpdb;

						$rating   = '';
						$total_reviews = $total_stars = 0;
						$place_id = $object['id'];
						$user_id  = $object['author'];

	                	$comments_query = "SELECT * FROM $wpdb->comments as comment INNER JOIN $wpdb->commentmeta AS meta WHERE comment.comment_post_ID = $place_id AND meta.meta_key = 'place_rating' AND meta.comment_id = comment.comment_ID AND ( comment.comment_approved = 1 OR comment.user_id = $user_id )";
						$get_comments   = $wpdb->get_results($comments_query);
						$my_review      = $wpdb->get_row("SELECT * FROM $wpdb->comments as comment INNER JOIN $wpdb->commentmeta AS meta WHERE comment.comment_post_ID = $place_id AND comment.user_id = $user_id AND meta.meta_key = 'place_rating' AND meta.comment_id = comment.comment_ID ORDER BY comment.comment_ID DESC");

						if (!is_null($get_comments)) {
						    foreach ($get_comments as $comment) {
						        if ($comment->comment_approved == 1) {
						            if( !empty($comment->meta_value) ){
						                $total_reviews++;
						            }
						            if( $comment->meta_value > 0 ){
						                $total_stars += $comment->meta_value;
						            }
						        }
						    }

						    if ($total_reviews != 0) {
						        $rating = number_format($total_stars / $total_reviews, 1);
						    }
						}

	                    // Get field as single value from post meta.
	                    return $rating;
	                },
	            )
	        );

	        // Add count place rating
			$review_count = GOLO_METABOX_PREFIX . 'review_count';
			register_rest_field(
	            'place',
	            $review_count,
	            array(
	                'get_callback' => function ( $object ) use ( $review_count ) {
	                	global $wpdb;

						$rating        = '';
						$total_reviews = 0;
						$place_id      = $object['id'];
						$user_id       = $object['author'];

	                	$comments_query = "SELECT * FROM $wpdb->comments as comment INNER JOIN $wpdb->commentmeta AS meta WHERE comment.comment_post_ID = $place_id AND meta.meta_key = 'place_rating' AND meta.comment_id = comment.comment_ID AND ( comment.comment_approved = 1 OR comment.user_id = $user_id )";
						$get_comments   = $wpdb->get_results($comments_query);
						$my_review      = $wpdb->get_row("SELECT * FROM $wpdb->comments as comment INNER JOIN $wpdb->commentmeta AS meta WHERE comment.comment_post_ID = $place_id AND comment.user_id = $user_id AND meta.meta_key = 'place_rating' AND meta.comment_id = comment.comment_ID ORDER BY comment.comment_ID DESC");

						if (!is_null($get_comments)) {
						    foreach ($get_comments as $comment) {
						        if ($comment->comment_approved == 1) {
						            if( !empty($comment->meta_value) ){
						                $total_reviews++;
						            }
						        }
						    }
						}

	                    // Get field as single value from post meta.
	                    return $total_reviews;
	                },
	            )
	        );

	        // Add comment rating
			$comment_rating = 'place_rating';
			register_rest_field(
	            'comment',
	            $comment_rating,
	            array(
	                'get_callback' => function ( $object ) use ( $comment_rating ) {
	                	global $wpdb;

						$comment_id = $object['id'];
						$post_id    = $object['post'];
						$user_id    = $object['author'];
	                	$comments_query = "SELECT * FROM $wpdb->comments as comment INNER JOIN $wpdb->commentmeta AS meta WHERE comment.comment_post_ID = $post_id AND comment.comment_ID = $comment_id AND meta.meta_key = 'place_rating' AND meta.comment_id = comment.comment_ID AND ( comment.comment_approved = 1 OR comment.user_id = $user_id )";
						$get_comment = $wpdb->get_results($comments_query);
	                    // Get field as single value from post meta.
	                    return $get_comment[0]->meta_value;
	                },
	            )
	        );

			// Add meta field place city
			$term_meta_configs = &glf_get_term_meta_config();
			$term_meta_field_keys = $this->get_term_meta_keys($term_meta_configs, 'place-city');
			foreach ($term_meta_field_keys as $meta_id => $field_value) {

				register_rest_field(
		            'place-city',
		            $meta_id,
		            array(
		                'get_callback' => function ( $object ) use ( $meta_id ) {

		                	if( $meta_id == 'place_city_country' ) {
		                		$country      = get_term_meta( $object['id'], $meta_id, true );
								$country_name = golo_get_country_by_code($country);

		                		return $country_name;
		                	}

		                    // Get field as single value from post meta.
		                    return get_term_meta( $object['id'], $meta_id, true );
		                },
		            )
		        );
			}

			// Add meta field icon categories
		    register_rest_field (
	            'place-categories',
	            'icon_marker_url',
	            array(
	                'get_callback' => function ( $object ) {
	                	$icon_marker = get_term_meta( $object['id'], 'place_categories_icon_marker', true );
                        if( !empty($icon_marker) ) {
                            $icon_marker_url = $icon_marker['url'];
                            return $icon_marker_url;
                        }
					    return false;
	                },
	            )
	        );

			// Add meta field place amenities
			$term_meta_amenities_field_keys = $this->get_term_meta_keys($term_meta_configs, 'place-amenities');
			foreach ($term_meta_amenities_field_keys as $meta_id => $field_value) {

				register_rest_field(
		            'place-amenities',
		            $meta_id,
		            array(
		                'get_callback' => function ( $object ) use ( $meta_id ) {
		                    // Get field as single value from post meta.
		                    return get_term_meta( $object['id'], $meta_id, true );
		                },
		            )
		        );
			}
			
			// Add meta field place users
			$term_meta_user_field_keys = array(
				'author_avatar_image_url',
				'author_avatar_image_id',
				GOLO_METABOX_PREFIX . 'author_mobile_number',
				GOLO_METABOX_PREFIX . 'author_fax_number',
				GOLO_METABOX_PREFIX . 'author_facebook_url',
				GOLO_METABOX_PREFIX . 'author_twitter_url',
				GOLO_METABOX_PREFIX . 'author_linkedin_url',
				GOLO_METABOX_PREFIX . 'author_pinterest_url',
				GOLO_METABOX_PREFIX . 'author_instagram_url',
				GOLO_METABOX_PREFIX . 'author_youtube_url',
				GOLO_METABOX_PREFIX . 'author_skype',
				GOLO_METABOX_PREFIX . 'place_whishlist',
			);
			foreach ($term_meta_user_field_keys as $meta_id ) {
				register_rest_field(
		            'user',
		            $meta_id,
		            array(
		                'get_callback' => function ( $object ) use ( $meta_id ) {

		                	if( $meta_id == GOLO_METABOX_PREFIX . 'place_whishlist' ) {
		                		// Get field as single value from post meta.
		                    	return get_user_meta( $object['id'], $meta_id, true );
		                	}

		                    // Get field as single value from post meta.
		                    return get_the_author_meta( $meta_id, $object['id'] );
		                },
		            )
		        );
			}

			// Remote add to wishlist
			register_rest_route('remote-user', 'bookmark', array(
		        'methods' => 'POST',
		        'callback' => function ($request) {
		            return $this->rest_add_to_wishlist($request);
		        },
		        'permission_callback' => '__return_true'
		    ));

			// Remote login
			register_rest_route('remote-user', 'login', array(
		        'methods' => 'POST',
		        'callback' => function ($request) {
		            return $this->rest_user_login($request);
		        },
		        'permission_callback' => '__return_true'
		    ));

		    // Remote login
			register_rest_route('remote-user', 'register', array(
		        'methods' => 'POST',
		        'callback' => function ($request) {
		            return $this->rest_user_register($request);
		        },
		        'permission_callback' => '__return_true'
		    ));

	    }

	    public function rest_add_to_wishlist($request = [])
		{
			$response = [
		        'success' => false,
		        'message' => __( 'You are not login', 'golo-framework' )
		    ];
			$status_code = 403;
		    $parameters = $request->get_json_params();
		    $user_id = sanitize_text_field($parameters['user_id']);
		    $place_id = sanitize_text_field($parameters['place_id']);
		    $place_id = intval( $place_id );

		    if ( $user_id > 0 ) {
                $my_favorites = get_user_meta( $user_id, GOLO_METABOX_PREFIX . 'place_whishlist', true );

                if ( ! empty( $my_favorites ) && ( ! in_array( $place_id, $my_favorites ) ) ) {
                    array_push( $my_favorites, $place_id );
                    $added = true;
                } else {
                    if ( empty( $my_favorites ) ) {
                        $my_favorites = array( $place_id );
                        $added        = true;
                    } else {
                        //Delete favorite
                        $key = array_search( $place_id, $my_favorites );
                        if ( $key !== false ) {
                            unset( $my_favorites[ $key ] );
                            $removed = true;
                        }
                    }
                }

                update_user_meta( $user_id, GOLO_METABOX_PREFIX . 'place_whishlist', $my_favorites );
                if ( $added ) {
                    $response = [
				        'success' => true,
				        'message' => __( 'Added', 'golo-framework' )
				    ];
				    $status_code = 200;
                }
                if ( $removed ) {
                    $response = [
				        'success' => true,
				        'message' => __( 'Removed', 'golo-framework' )
				    ];
				    $status_code = 200;
                }
            } else {
            	$response = [
			        'success' => false,
			        'message' => __( 'You are not login', 'golo-framework' )
			    ];
			    $status_code = 403;
            }

		    return new WP_REST_Response($response, $status_code);
		}

		public function rest_user_login($request = [])
		{
		    $response = [
		        'success' => false,
		        'message' => __('Login failed', 'golo-framework')
		    ];
		    $status_code = 403;
		    $parameters = $request->get_json_params();
		    $username = sanitize_text_field($parameters['username']);
		    $password = sanitize_text_field($parameters['password']);

		    $user = null;
		    if (!empty($username) && !empty($password)) {
		        $user = wp_authenticate($username, $password);
		    }

		    if ($user instanceof WP_User) {
		        $response['success'] = true;
		        $response['message'] = __('Login successful', 'golo-framework');
		        $status_code = 200;
		    }

		    return new WP_REST_Response($response, $status_code);
		}

		public function rest_user_register($request = [])
		{
		    $response = [
		        'success' => false,
		        'message' => __('Register failed', 'golo-framework')
		    ];
		    $status_code = 403;
		    $parameters = $request->get_json_params();
		    $firstname = sanitize_text_field($parameters['firstname']);
		    $lastname = sanitize_text_field($parameters['lastname']);
		    $email = sanitize_text_field($parameters['email']);
		    $password = sanitize_text_field($parameters['password']);
		    $user_login = $firstname.$lastname;

		    if (!empty($firstname) && !empty($lastname) && !empty($email) && !empty($password)) {
				$userdata = array(
					'user_login' => $user_login,
					'first_name' => $firstname,
					'last_name'  => $lastname,
					'user_email' => $email,
					'user_pass'  => $password
				);
				$user_id = wp_insert_user( $userdata );

				if( !is_wp_error($user_id) ) {
					$response['success'] = true;
			        $response['message'] = __('Register successful', 'golo-framework');
			        $status_code = 200;
				}else{
					$response['success'] = false;
			        $response['message'] = __('Username/Email address is existing', 'golo-framework');
				}
		    }

		    return new WP_REST_Response($response, $status_code);
		}

	    public function get_meta_box_keys($configs) {
			$field_keys = array();
			foreach ($configs as $meta_id => $config) {
				if (!is_array($config)) {
					continue;
				}

				$post_type = isset($config['post_type']) ? $config['post_type'] : array();

				if (isset($config['section'])) {
					foreach ($config['section'] as $tabs) {
						if (isset($tabs['fields'])) {
							$field_keys = array_merge($field_keys, glf_get_config_field_keys($tabs['fields'], '', $tabs['id']));
						}
					}
				} else {

					if (isset($config['fields'])) {
						$field_keys = array_merge($field_keys, glf_get_config_field_keys($config['fields'], '', ''));
					}
				}
			}

			return $field_keys;
		}

	    public function get_term_meta_keys($configs, $taxonomy = '') {
			$field_keys = array();
			foreach ($configs as $meta_id => $config) {
				if (!is_array($config)) {
					continue;
				}

				$taxonomies = isset($config['taxonomy']) ? $config['taxonomy'] : array();

				if (in_array($taxonomy, $taxonomies)) {
					if (isset($config['section'])) {

						foreach ($config['section'] as $tabs) {
							if (isset($tabs['fields'])) {
								$field_keys = array_merge($field_keys, glf_get_config_field_keys($tabs['fields'], '', $tabs['id']));
							}
						}
					} else {

						if (isset($config['fields'])) {
							$field_keys = array_merge($field_keys, glf_get_config_field_keys($config['fields'], '', ''));
						}
					}
				}
			}

			return $field_keys;
		}

    }

}