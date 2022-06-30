<?php
if (!defined('ABSPATH')) {
    exit;
}

if (!class_exists('YELP_Review')) {
    /**
     * Class YELP_Review
     */
    class YELP_Review
    {
        public function __construct() {
            
            $this->define_constants();

            add_action('wp_enqueue_scripts',array( $this, 'enqueue_styles' ) );

        }
        
        private function define_constants() {
            $plugin_url = plugin_dir_url(__FILE__);

            $plugin_url = apply_filters('czn_yelp_plugin_url',$plugin_url);

            /**
             * Define plugin URL
             */
            if (!defined('GOLO_YELP_PLUGIN_URL')) {
                define('GOLO_YELP_PLUGIN_URL', $plugin_url);
            }
        }

        public function enqueue_styles() {
            wp_enqueue_style('czn-yelp-style', GOLO_YELP_PLUGIN_URL . 'assets/css/style.css', array(), false, 'all');
        }

        /**
         * Render yelp reviews.
         *
         */
        public static function render( $instance ) {

            /**
             * As of v1.5.0, the Yelp API transitioned from v2 to v3. To ensure upgraded plugins continue to function, a backup API key has been included below.
             * It is still highly recommended that each user set up their own Yelp app and use their own API key.
             */
            $fusion_api_key = 'xjFvYO8wuhOaO1A3q6fXn2w0LQxqgGCe6EvNzsSkiI4RTzDrmhPWPeRuVQbKOgmZe80DtDqkHMFIarcmGRMwjgQrTyZpIwBL3NDtspD0_QhoYPSOyvUKXfhTtNfyXnYx';

            $yelp_sort_review = golo_get_option('yelp_sort_review', '2');
            $yelp_limit_review = golo_get_option('yelp_limit_review', '3');
            $yelp_display_address = golo_get_option('yelp_display_address', '1');
            $yelp_display_phone = golo_get_option('yelp_display_phone', '');

            // Get Widget Options.
            $title          = isset( $instance['title'] ) ? $instance['title'] : '';
            $displayOption  = isset( $instance['display_option'] ) ? $instance['display_option'] : '2';
            $term           = isset( $instance['term'] ) ? $instance['term'] : 'categories';
            $id             = isset( $instance['id'] ) ? $instance['id'] : '';
            $location       = isset( $instance['location'] ) ? $instance['location'] : '';
            $address        = isset( $instance['display_address'] ) ? $instance['display_address'] : $yelp_display_address;
            $phone          = isset( $instance['display_phone'] ) ? $instance['display_phone'] : $yelp_display_phone;
            $limit          = isset( $instance['limit'] ) ? $instance['limit'] : $yelp_limit_review;
            $profileImgSize = isset( $instance['profile_img_size'] ) ? $instance['profile_img_size'] : '100x100';
            $sort           = isset( $instance['sort'] ) ? $instance['sort'] : $yelp_sort_review;
            $align          = isset( $instance['alignment'] ) ? $instance['alignment'] : '';
            $reviewsOption  = isset( $instance['display_reviews'] ) ? $instance['display_reviews'] : '';
            $titleOutput    = isset( $instance['disable_title_output'] ) ? $instance['disable_title_output'] : '';
            $targetBlank    = isset( $instance['target_blank'] ) ? $instance['target_blank'] : '1';
            $noFollow       = isset( $instance['no_follow'] ) ? $instance['no_follow'] : '1';
            $cache          = isset( $instance['cache'] ) ? $instance['cache'] : '';

            // If cache option is enabled, attempt to get response from transient.
            if ( 'none' !== strtolower( $cache ) ) {

                $transient = $displayOption . $term . $id . $location . $limit . $sort . $profileImgSize;

                // Check for an existing copy of our cached/transient data.
                $response = get_transient( $transient );

                if ( false === $response ) {

                    // Get Time to Cache Data
                    $expiration = $cache;

                    // Assign Time to appropriate Math
                    switch ( $expiration ) {
                        case '1 Hour':
                            $expiration = 3600;
                            break;
                        case '3 Hours':
                            $expiration = 3600 * 3;
                            break;
                        case '6 Hours':
                            $expiration = 3600 * 6;
                            break;
                        case '12 Hours':
                            $expiration = 60 * 60 * 12;
                            break;
                        case '1 Day':
                            $expiration = 60 * 60 * 24;
                            break;
                        case '2 Days':
                            $expiration = 60 * 60 * 48;
                            break;
                        case '1 Week':
                            $expiration = 60 * 60 * 168;
                            break;
                    }

                    // Cache data wasn't there, so regenerate the data and save the transient
                    if ( '1' === $displayOption ) {
                        $response = self::czn_yelp_widget_fusion_get_business( $fusion_api_key, $id, $reviewsOption );
                    } else {
                        $response = self::czn_yelp_widget_fusion_search( $fusion_api_key, $term, $location, $limit, $sort );
                    }

                    set_transient( $transient, $response, $expiration );
                }
            } else {

                // No Cache option enabled
                if ( '1' === $displayOption ) {
                    // Widget is in Business mode.
                    $response = self::czn_yelp_widget_fusion_get_business( $fusion_api_key, $id, $reviewsOption );
                } else {
                    // Widget is in Search mode.
                    $response = self::czn_yelp_widget_fusion_search( $fusion_api_key, $term, $location, $limit, $sort );
                }
            }

            /* Output */
            if ( isset( $response->businesses ) ) {
                $businesses = $response->businesses;
            } else {
                $businesses = array( $response );
            }

            // Check Yelp API response for an error
            if ( isset( $response->error ) ) {

                echo self::handle_czn_yelp_api_error( $response );

            } else {

                // Verify results have been returned
                if ( ! isset( $businesses[0] ) ) {
                    echo '<div class="czn-yelp-error">' . __( 'No results found', 'czn-yelp-widget-pro' ) . '</div>';
                } else {

                    /**
                     * The response from Yelp is valid - Output Widget:
                     */

                    // Open link in new window if set.
                    if ( $targetBlank == 1 ) {
                        $targetBlank = 'target="_blank" ';
                    } else {
                        $targetBlank = '';
                    }
                    // Add nofollow relation if set.
                    if ( '1' === $noFollow ) {
                        $noFollow = 'rel="nofollow" ';
                    } else {
                        $noFollow = '';
                    }

                    ?>
                    <div class="czn-yelp-wrap">
                        <h3 class="czn-yelp-title"><?php echo esc_html($title); ?></h3>

                        <?php
                        // Begin Setting Output Variable by Looping Data from Yelp
                        for ( $x = 0; $x < $limit; $x ++ ) {
                            ?>

                            <div class="czn-yelp czn-yelp-business <?php echo $align; ?>">
                                <div class="czn-yelp-img-wrap">
                                    <a <?php echo $targetBlank . $noFollow; ?> href="<?php echo esc_attr( $businesses[ $x ]->url ); ?>" title="<?php echo esc_attr( $businesses[ $x ]->name ); ?>">
                                        <img class="czn-yelp-business-img" src="
                                        <?php
                                        if ( ! empty( $businesses[ $x ]->image_url ) ) {
                                            echo esc_attr( $businesses[ $x ]->image_url );
                                        } else {
                                            echo GOLO_YELP_PLUGIN_URL . '/assets/images/blank-biz.png';
                                        };
                                        ?>
                                        "/>
                                    </a>
                                </div>
                                <div class="czn-yelp-info-wrap">
                                    <div class="left">
                                        <a class="czn-yelp-business-name" <?php echo $targetBlank . $noFollow; ?> href="<?php echo esc_attr( $businesses[ $x ]->url ); ?>" title="<?php echo esc_attr( $businesses[ $x ]->name ); ?> Yelp page"><?php echo $businesses[ $x ]->name; ?></a>
                                        
                                        <?php

                                        // Does the User want to display Address?
                                        if ( '1' === $address ) {
                                            ?>
                                            <div class="czn-yelp-address-wrap">
                                                <address>
                                                    <?php
                                                    $add_string = '';
                                                    if ($businesses[ $x ]->location->display_address) {
                                                        // Iterate through Address Array
                                                        foreach ( $businesses[ $x ]->location->display_address as $addressItem ) {
                                                            $add_string = $add_string . $addressItem . ', ';
                                                        }
                                                        echo rtrim($add_string, ", ");
                                                    }
                                                    ?>
                                                <address>
                                            </div>

                                            <?php
                                        } //endif address

                                        // Phone
                                        if ( '1' === $phone ) {
                                            ?>
                                            <p class="ywp-phone">
                                                <a href="tel:<?php echo $businesses[ $x ]->phone; ?>"><?php
                                                    // echo pretty display_phone (only avail in biz API)
                                                    if ( ! empty( $businesses[ $x ]->display_phone ) ) {
                                                        echo $businesses[ $x ]->display_phone;
                                                    } else {
                                                        echo $businesses[ $x ]->phone;
                                                    }
                                                    ?></a>
                                            </p>


                                        <?php } //endif phone ?>
                                    </div>

                                    <div class="right">
                                        <span class="review-count"><?php echo esc_attr( $businesses[ $x ]->review_count ) . '&nbsp;' . __( 'reviews', 'czn-yelp-widget-pro' ); ?></span>
                                        <?php self::czn_yelp_widget_fusion_stars( $businesses[ $x ]->rating ); ?>
                                    </div>

                                </div>

                                <?php
                                /**
                                 * Display Reviews
                                 *
                                 * a) if reviews option is enabled
                                 * b + c) if review are present
                                 */
                                if (
                                    '1' === $reviewsOption
                                    && isset( $businesses[0]->review_count )
                                    && isset( $businesses[0]->reviews )
                                ) : ?>

                                    <div class="czn-yelp-business-reviews">

                                        <?php
                                        /**
                                         * Display Reviews
                                         */
                                        foreach ( $businesses[0]->reviews as $review ) {

                                            $review_avatar = ! empty( $review->user->image_url ) ? $review->user->image_url : GOLO_YELP_PLUGIN_URL . '/assets/images/czn-yelp-default-avatar.png';
                                            ?>

                                            <div class="czn-yelp-review yelper-avatar-60 clearfix">

                                                <div class="czn-yelp-review-avatar">

                                                    <img src="<?php echo $review_avatar; ?>" width="60" height="60" alt="<?php echo $review->user->name; ?>'s Review"/>
                                                    <span class="name"><?php echo $review->user->name; ?></span>
                                                </div>

                                                <div class="czn-yelp-review-excerpt">
                                                    <?php self::czn_yelp_widget_fusion_stars( $review->rating ); ?>
                                                    <time><?php echo date( 'n/j/Y', strtotime( $review->time_created ) ); ?></time>

                                                    <div class="czn-yelp-review-excerpt-text">
                                                        <?php echo wpautop( $review->text ); ?>
                                                    </div>

                                                    <?php
                                                    //Read More Review
                                                    $reviewMoreText = apply_filters( 'ywp_review_readmore_text', __( 'Read More &raquo;', 'czn-yelp-widget-pro' ) ); ?>
                                                    <a href="<?php echo esc_url( $review->url ); ?>" class="ywp-review-read-more" <?php echo $targetBlank . $noFollow; ?>><?php echo $reviewMoreText; ?></a>

                                                </div>

                                            </div>

                                        <?php } //end foreach ?>

                                    </div>

                                <?php endif; ?>

                            </div><!--/.czn-yelp-business -->

                            <?php

                        }
                        ?>
                    </div>
                    <?php
                }
            } //Output Widget Contents.

        }

        /**
         * Handle Yelp Error Messages
         *
         * @param $response
         */
        public static function handle_czn_yelp_api_error( $response ) {

            $output = '<div class="czn-yelp-error">';
            if ( $response->error->code == 'EXCEEDED_REQS' ) {
                $output .= __( 'The default Yelp API has exhausted its daily limit. Please enable your own API Key in your Yelp Widget Pro settings.', 'czn-yelp-widget-pro' );
            } elseif ( $response->error->code == 'BUSINESS_UNAVAILABLE' ) {
                $output .= __( '<strong>Error:</strong> Business information is unavailable. Either you mistyped the Yelp biz ID or the business does not have any reviews.', 'czn-yelp-widget-pro' );
            } elseif ( $response->error->code == 'TOKEN_MISSING' ) {
                $output .= sprintf(
                    __( '%1$sSetup Required:%2$s Enter a Yelp Fusion API Key in the %3$splugin settings screen.%4$s', 'czn-yelp-widget-pro' ),
                    '<strong>',
                    '</strong>',
                    '<a href="#">',
                    '</a>'
                );
            } //output standard error
            else {
                if ( ! empty( $response->error->code ) ) {
                    $output .= $response->error->code . ': ';
                }
                if ( ! empty( $response->error->description ) ) {
                    $output .= $response->error->description;
                }
            }
            $output .= '</div>';

            echo $output;

        }

        /**
         * Generates a star image based on numerical rating.
         *
         */
        public static function czn_yelp_widget_fusion_stars( $rating = 0 ) {
            $ext          = '.png';
            $floor_rating = floor( $rating );

            if ( $rating != $floor_rating ) {
                $image_name = $floor_rating . '_half';
            } else {
                $image_name = $floor_rating;
            }

            $uri_image_name = GOLO_YELP_PLUGIN_URL . '/assets/images/stars/regular_' . $image_name;
            $single         = $uri_image_name . $ext;
            $double         = $uri_image_name . '@2x' . $ext;
            $triple         = $uri_image_name . '@3x' . $ext;
            $srcset         = "{$single}, {$double} 2x, {$triple} 3x";
            $decimal_rating = number_format( $rating, 1, '.', '' );

            echo '<img class="rating" srcset="' . esc_attr( $srcset ) . '" src="' . esc_attr( $single ) . '" title="' . $decimal_rating . ' star rating" alt="' . $decimal_rating . ' star rating">';
        }

        /**
         * Retrieves business details based on Yelp business ID.
         */
        public static function czn_yelp_widget_fusion_get_business( $key, $id, $reviews_option = 0 ) {
            $url = 'https://api.yelp.com/v3/businesses/' . $id;

            $args = array(
                'user-agent' => '',
                'headers'    => array(
                    'authorization' => 'Bearer ' . $key,
                ),
            );

            $response = self::czn_yelp_widget_fusion_get( $url, $args );

            if ( $reviews_option ) {
                $reviews_response = self::czn_yelp_fusion_get_reviews( $key, $id );

                if ( ! empty( $reviews_response ) and isset( $reviews_response->reviews[0] ) ) {
                    $response->reviews = $reviews_response->reviews;
                }
            }

            return $response;
        }

        /**
         * Retrieves search results based on a search term and location.
         */
        public static function czn_yelp_widget_fusion_search( $key, $term, $location, $limit, $sort_by ) {
            switch ( $sort_by ) {
                case '0':
                    $sort_by = 'best_match';
                    break;
                case '1':
                    $sort_by = 'distance';
                    break;
                case '2':
                    $sort_by = 'rating';
                    break;
                default:
                    $sort_by = 'best_match';
            }

            $url = add_query_arg(
                array(
                    'term'     => $term,
                    'location' => $location,
                    'limit'    => $limit,
                    'sort_by'  => $sort_by,
                ),
                'https://api.yelp.com/v3/businesses/search'
            );

            $args = array(
                'user-agent' => '',
                'headers'    => array(
                    'authorization' => 'Bearer ' . $key,
                ),
            );

            $response = self::czn_yelp_widget_fusion_get( $url, $args );

            return $response;
        }

        /**
         * Retrieves reviews based on Yelp business ID.
         *
         * @param string $key Yelp Fusion API Key.
         * @param string $id The Yelp business ID.
         *
         * @return array Associative array containing the response body.
         */
        public static function czn_yelp_fusion_get_reviews( $key, $id ) {
            $url = 'https://api.yelp.com/v3/businesses/' . $id . '/reviews';

            $args = array(
                'user-agent' => '',
                'headers'    => array(
                    'authorization' => 'Bearer ' . $key,
                ),
            );

            $response = self::czn_yelp_widget_fusion_get( $url, $args );

            return $response;
        }

        /**
         * Retrieves a response from a safe HTTP request using the GET method.
         *
         * @see wp_safe_remote_get()
         *
         * @return bool|array Associative array containing the response body.
         */
        public static function czn_yelp_widget_fusion_get( $url, $args = array() ) {
            $response = wp_safe_remote_get( $url, $args );

            if ( is_wp_error( $response ) ) {
                return false;
            }

            $response = self::czn_yelp_update_http_for_ssl( $response );
            $response = json_decode( $response['body'] );

            /**
             * Filters the Yelp Fusion API response.
             *
             * @since 1.5.0
             */
            return apply_filters( 'czn_yelp_fusion_api_response', $response );
        }

        /**
         * Function update http for SSL
         *
         * @param $data
         *
         * @return mixed
         */
        public static function czn_yelp_update_http_for_ssl( $data ) {

            if ( ! empty( $data['body'] ) && is_ssl() ) {
                $data['body'] = str_replace( 'http:', 'https:', $data['body'] );
            } elseif ( is_ssl() ) {
                $data = str_replace( 'http:', 'https:', $data );
            }


            return $data;

        }

    }

    new YELP_Review();

}