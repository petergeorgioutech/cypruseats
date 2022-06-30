<?php 

/**
 * layout_wrapper_start
 */
function layout_wrapper_start()
{
	$place_gallery     = get_post_meta( get_the_ID(), GOLO_METABOX_PREFIX . 'place_images', true);
	$type_single_place = golo_get_option('type_single_place', 'type-1' );
	$type_single_place = !empty($_GET['layout']) ? golo_clean(wp_unslash($_GET['layout'])) : $type_single_place;
	$class_layout      = array('site-layout');

	$place_id = '';
	$place_id = get_the_ID();
	$place_meta_data = get_post_custom( $place_id );
	$place_booking_type = isset($place_meta_data[GOLO_METABOX_PREFIX . 'place_booking_type']) ? $place_meta_data[GOLO_METABOX_PREFIX . 'place_booking_type'][0] : '';
	
	if( is_single() && ( get_post_type() == 'place' ) ){
		$class_layout[] = $type_single_place;
	}

	if ( $place_gallery ) :
		$class_layout[] = 'has-gallery';
	endif;

	if( is_tax() || is_archive() || get_query_var('country') ){
		if( !is_author() ){
			$class_layout[] = 'no-sidebar';
		}
	}else{
		if( is_active_sidebar('place_sidebar') || !empty($place_booking_type) ) {
			$class_layout[] = 'has-sidebar';
		}
	}

	if( is_author() ) {
		$class_layout[] = 'has-sidebar';
	}

   	?>
		<div class="main-content"><div class="container"><div class="<?php echo join(' ', $class_layout); ?>">
   	<?php
}

/**
 * layout_wrapper_end
 */
function layout_wrapper_end()
{
   	?>
		</div></div></div>
   	<?php
}

/**
 * output_content_wrapper
 */
function output_content_wrapper_start()
{
    golo_get_template('global/wrapper-start.php');
}

/**
 * output_content_wrapper
 */
function output_content_wrapper_end()
{
    golo_get_template('global/wrapper-end.php');
}

/**
 * archive place before
 */
function archive_place_post()
{
	golo_get_template( 'global/related-post.php' );
}

/**
 * archive page title
 */
function archive_page_title()
{
	$taxonomy_name = get_query_var('taxonomy');
	$archive_city_layout_style = golo_get_option('archive_city_layout_style', 'layout-default');

	$layout = !empty($_GET['layout']) ? golo_clean(wp_unslash($_GET['layout'])) : '';
	if( !empty($layout) ){
		$archive_city_layout_style = $layout;
	}

	if( $archive_city_layout_style == 'layout-default' || $taxonomy_name !== 'place-city' ) {
		golo_get_template('place/place-search-map/place-search-map.php');
	}
    golo_get_template('archive-place/page-title.php');
}

/**
 * archive information
 */
function archive_information()
{
    golo_get_template('archive-place/information.php');
}

/**
 * archive categories
 */
function archive_categories()
{
    golo_get_template('archive-place/categories.php');
}

/**
 * archive page title
 */
function archive_related_city()
{
    golo_get_template('archive-place/related-city.php');
}

/**
 * archive map filter
 */
function archive_map_filter()
{
	wp_enqueue_script('google-map');
	wp_enqueue_script('markerclusterer');
	$map_type             = golo_get_option('map_type', 'google_map');
	if( $map_type == 'mapbox' ) {
	    $mapbox_api_key         = Golo_Helper::golo_get_option('mapbox_api_key', 'pk.eyJ1Ijoic2F5aTc3NDciLCJhIjoiY2tpcXRmYW1tMWpjMjJzbGllbThieTFlaCJ9.eDj6zNLBZpG-veFqXiyVPw');
    	$map_zoom_level         = golo_get_option('map_zoom_level', '15');
    	$google_map_style       = golo_get_option('mapbox_style', 'streets-v11');
	} else if( $map_type == 'openstreetmap' ) {
	    $openstreetmap_api_key      = Golo_Helper::golo_get_option('openstreetmap_api_key', 'pk.eyJ1Ijoic2F5aTc3NDciLCJhIjoiY2tpcXRmYW1tMWpjMjJzbGllbThieTFlaCJ9.eDj6zNLBZpG-veFqXiyVPw');
    	$map_zoom_level             = golo_get_option('map_zoom_level', '15');
    	$openstreetmap_style        = golo_get_option('openstreetmap_style', 'streets-v11');
	}

	$place_details_order_default = array(
	    'sort_order'                            => 'enable_sp_amenities|enable_sp_description|enable_sp_menu|enable_sp_location|enable_sp_contact|enable_sp_additional_fields|enable_sp_time_opening|enable_sp_video|enable_sp_faqs|enable_sp_nearby_yelp_review|enable_sp_google_review|enable_sp_author_info|enable_sp_review|enable_sp_product',
	    'enable_sp_amenities'                   => 'enable_sp_amenities',
	    'enable_sp_description'                 => 'enable_sp_description',
	    'enable_sp_menu'                        => 'enable_sp_menu',
	    'enable_sp_location'                    => 'enable_sp_location',
	    'enable_sp_contact'                     => 'enable_sp_contact',
	    'enable_sp_additional_fields'           => 'enable_sp_additional_fields',
	    'enable_sp_time_opening'                => 'enable_sp_time_opening',
	    'enable_sp_video'                       => 'enable_sp_video',
	    'enable_sp_faqs'                        => 'enable_sp_faqs',
	    'enable_sp_nearby_yelp_review'          => 'enable_sp_nearby_yelp_review',
	    'enable_sp_google_review'               => 'enable_sp_google_review',
	    'enable_sp_author_info'                 => 'enable_sp_author_info',
	    'enable_sp_review'                      => 'enable_sp_review',
	    'enable_sp_product'                     => 'enable_sp_product',
	);

	$place_details_order    = golo_get_option( 'place_details_order', $place_details_order_default );

	if (in_array('enable_sp_review', $place_details_order )) {
		$review = '';
	} else {
		$review = 'hidden';
	}
	
	?>
	<div class="filter-place-search" data-review="<?php echo esc_attr($review); ?>">
	    <div class="entry-map">
	    	<a href="#" class="btn-close">
                <i class="la la-times medium"></i>
            </a>
	        <input id="pac-input" class="controls" type="text" placeholder="<?php esc_html_e('Search...', 'golo-framework'); ?>">
	        <?php if( $map_type == 'google_map' ) { ?>
	            <div id="place-map-filter" class="golo-map-filter maptype" style="width: 100%;" data-maptype="<?php echo $map_type; ?>"></div>
	        <?php } else if( $map_type == 'openstreetmap' ) { ?>
	            <div id="maps" class="golo-openstreetmap-filter maptype" style="width: 100%; height: 100%;" data-maptype="<?php echo $map_type; ?>" data-key="<?php if( $openstreetmap_api_key ) { echo $openstreetmap_api_key; } ?>" data-level="<?php if( $map_zoom_level ) { echo $map_zoom_level; } ?>" data-style="<?php if( $openstreetmap_style ) { echo $openstreetmap_style; } ?>"></div>
	        <?php } else { ?>
	            <div id="map" class="maptype" style="width: 100%; height: 100%;" data-maptype="<?php echo $map_type; ?>" data-key="<?php if( $mapbox_api_key ) { echo $mapbox_api_key; } ?>" data-level="<?php if( $map_zoom_level ) { echo $map_zoom_level; } ?>" data-type="<?php if( $google_map_style ) { echo $google_map_style; } ?>"></div>
	        <?php } ?>
	        <div class="golo-loading-effect"><span class="golo-dual-ring"></span></div>
	        <div class="no-result"><span><?php esc_html_e("We didn't find any results", 'golo-framework'); ?></span></div>
	    </div>
	</div>
	<?php
}

/**
 * archive heading filter
 */
function archive_heading_filter( $current_city, $current_term, $total_post ) 
{	
	$term_slug = $count = $layout = $city_id = '';
	$key = isset( $_GET['s'] ) ? golo_clean(wp_unslash($_GET['s'])) : '';
	$location = isset( $_GET['place_location'] ) ? golo_clean(wp_unslash($_GET['place_location'])) : '';
	$filter_classes = array();
	$taxonomy_name  = get_query_var('taxonomy');
	$archive_place_layout_style = golo_get_option('archive_place_layout_style', 'layout-default');
	$archive_city_layout_style  = golo_get_option('archive_city_layout_style', 'layout-default');
	$enable_city_filter         = golo_get_option('enable_city_filter', '1');
	$enable_archive_filter      = golo_get_option('enable_archive_filter', '1');
	$enable_open_now_filter    = golo_get_option('enable_open_now_filter', '1');

	$meta_query = array(
		'relation'	=> 'AND',
	);

	if( $current_term )
	{
		$term_slug 	= $current_term->slug;
		$count     	= golo_get_category_count($current_city, $term_slug);
		$tax 		= get_term_by('slug', $term_slug, $taxonomy_name);
		$city_id 	= $tax->term_id;
		if ($city_id) {
			$meta_query[] = array(
		        'key' => 'place_neighborhood_city',
		        'value' => $city_id,
		        'compare' => '=',
		    );
		}
	}

	if( $total_post )
	{
		$count = $total_post;
	}

	if( is_search() || is_tax() ) 
	{

		$layout = !empty($_GET['layout']) ? golo_clean(wp_unslash($_GET['layout'])) : $archive_place_layout_style;

		if( $layout == 'layout-column' || $layout == 'layout-top-filter' )
		{
			$filter_classes[] = 'filter-dropdown';
		}

		if( $layout == 'layout-default' )
		{
			$filter_classes[] = 'filter-toggle';
		}

		$enable_filter = $enable_archive_filter;
	}


	if( $taxonomy_name == 'place-city' ) 
	{

		$layout = !empty($_GET['layout']) ? golo_clean(wp_unslash($_GET['layout'])) : $archive_city_layout_style;

		if( $layout == 'layout-column' )
		{
			$filter_classes[] = 'filter-dropdown';

			$enable_filter = $enable_city_filter;
		}

		if( $layout == 'layout-default' )
		{
			$filter_classes[] = 'filter-toggle';
		}
	}

	if ($location) {
		$placecity = get_term_by('name', $location, 'place-city');

		$placecity_id 	= $placecity->term_id;
	
		if ($placecity_id) {
			$meta_query[] = array(
		        'key' => 'place_neighborhood_city',
		        'value' => $placecity_id,
		        'compare' => '=',
		    );
		}
	}

    ?>
	
	<?php if( !empty($enable_filter) ) { ?>
	<div class="archive-filter block-heading category-heading <?php echo join(' ', $filter_classes); ?>">

		<div class="bg-overlay"></div>

		<div class="inner-filter custom-scrollbar">

			<?php if( $key && $layout == 'layout-default' ) : ?>
				<h3><?php echo sprintf( __( 'Search result for: "%s"', 'golo-framework' ), $key); ?></h3>
			<?php endif; ?>

			<div class="top-heading">
				
				<?php if( $layout == 'layout-default' ) : ?>
	    		<h3 class="entry-title">
	    			<?php if($current_term) : ?>
	    				<span><?php echo esc_html($current_term->name); ?></span>
	    			<?php endif; ?>
	    			
	    			<?php if( $current_term ) { ?>
			    		<span class="result-count">
			    			<?php printf( _n( '(%s Place)', '(%s Places)', $count, 'golo-framework' ), '<span class="count">' . esc_html( $count ) . '</span>' ); ?>
			    		</span>
			    	<?php }else{ ?>
						<span class="result-count">
			    			<?php printf( _n( '(%s Result)', '(%s Results)', $count, 'golo-framework' ), '<span class="count">' . esc_html( $count ) . '</span>' ); ?>
			    		</span>
			    	<?php } ?>
	    		</h3>
	    		<?php endif; ?>
				
				<div class="golo-nav-filter">
					<div class="golo-clear-filter">
						<i class="fal fa-sync"></i>
						<span><?php esc_html_e('Clear All', 'golo-framework'); ?></span>
					</div>

			    	<div class="golo-filter-toggle">
			    		<span><?php esc_html_e('Filter', 'golo-framework'); ?></span>
						<i class="las la-angle-down"></i>
			    	</div>
				</div>
	    	</div>

	    	<div class="golo-menu-filter">
	    		<div class="row">
										
	    			<?php
                    $search_fields = golo_get_option('search_fields', array('sort_by',  'filter_price', 'filter_city', 'filter_categories', 'filter_type', 'filter_amenities', 'filter_neighborhood', 'open_now'));
                    if ($search_fields): foreach ($search_fields as $field) {
                        switch ($field) {
							
							case 'open_now':

								if ( $layout == 'layout-default' ) : ?>
								<div class="filter-open-time col col-xl-2 col-lg-4 col-md-4 col-sm-4 col-xs-4">
									<div class="entry-filter">
										<h4><?php esc_html_e('Open Now', 'golo-framework'); ?></h4>
									</div>
								</div>
								<?php endif;

								break;

                            case 'sort_by':

	                            if( $layout == 'layout-default' ) : ?>
					    		<div class="col col-xl-2 col-lg-4 col-md-4 col-sm-4 col-xs-4">
					    			<div class="entry-filter">
					    				<h4><?php esc_html_e('Sort By', 'golo-framework'); ?></h4>
					    				<ul class="sort-by filter-control custom-scrollbar">
						    				<li><a href="#" data-sort="newest"><?php esc_html_e('Newest', 'golo-framework'); ?></a></li>
						    				<li><a href="#" data-sort="rating"><?php esc_html_e('Average rating', 'golo-framework'); ?></a></li>
						    				<li><a href="#" data-sort="featured"><?php esc_html_e('Featured', 'golo-framework'); ?></a></li>
						    			</ul>
					    			</div>
					    		</div>
					    		<?php endif;

                            	break;

                            case 'filter_price':

                            	?>
                            	<div class="filter-price col col-xl-2 col-lg-4 col-md-4 col-sm-4 col-xs-4">
					    			<div class="entry-filter">
					    				<h4><?php esc_html_e('Price', 'golo-framework'); ?></h4>
					    				<?php 
					    				$currency_sign = golo_get_option('currency_sign', '$');
										$low_price     = golo_get_option('low_price', '$');
										$medium_price  = golo_get_option('medium_price', '$$');
										$high_price    = golo_get_option('high_price', '$$$');
					    				?>
						    			<ul class="price filter-control custom-scrollbar">
						    				<li><a href="#" data-price="1"><?php esc_html_e('Free', 'golo-framework'); ?></a></li>
						    				<li><a href="#" data-price="2"><?php echo sprintf( esc_html__( 'Low: %s', 'golo-framework' ), $low_price ); ?></a></li>
						    				<li><a href="#" data-price="3"><?php echo sprintf( esc_html__( 'Medium: %s', 'golo-framework' ), $medium_price ); ?></a></li>
						    				<li><a href="#" data-price="4"><?php echo sprintf( esc_html__( 'High: %s', 'golo-framework' ), $high_price ); ?></a></li>
						    			</ul>
						    		</div>
					    		</div>
                                <?php

                                break;

                            case 'filter_city':

                            	if( $taxonomy_name !== 'place-city' && !isset($_GET['city']) ) : ?>
						    		<div class="filter-city col col-xl-2 col-lg-4 col-md-4 col-sm-4 col-xs-4">
						    			<div class="entry-filter">
						    				<h4><?php esc_html_e('City', 'golo-framework'); ?></h4>
							    			<ul class="city filter-control custom-scrollbar">
							    				<?php 
						                        $place_cities = get_categories(array(
						                            'taxonomy'   => 'place-city',
						                            'hide_empty' => 1,
						                            'orderby'    => 'name',
						                            'order'      => 'ASC',
						                            'parent' 	 => 0,
						                        ));
												
												if (isset($_GET['ciid'])) {
						                        	$city_id = golo_clean(wp_unslash($_GET['ciid']));
						                        } else {
						                        	$city_id = '';
						                        }
												
						                        if($place_cities) :
						                            foreach ($place_cities as $place_city) {
						                            	$termchildren = get_term_children( $place_city->term_id, 'place-city' );
						                            ?>
						                                <li>
						                                    <input type="checkbox" id="golo_<?php echo esc_attr($place_city->slug); ?>" class="custom-checkbox input-control" name="cities" value="<?php echo esc_attr($place_city->term_id); ?>" <?php if( $place_city->term_id == $city_id ) : echo 'checked';endif; ?> />
						                                    <label for="golo_<?php echo esc_attr($place_city->slug); ?>"><?php echo esc_html($place_city->name); ?></label>
						                                    <?php if ($termchildren) { ?>
							                                <ul>
							                                	<?php
							                                		foreach ( $termchildren as $child ) {
	    															$term = get_term_by( 'id', $child, 'place-city' );
							                                	?>
							                                	<li>
							                                		<input type="checkbox" id="golo_<?php echo esc_attr($term->slug); ?>" class="custom-checkbox input-control" name="cities" value="<?php echo esc_attr($term->term_id); ?>" <?php if( $term->term_id == $city_id ) : echo 'checked';endif; ?> />
						                                    		<label for="golo_<?php echo esc_attr($term->slug); ?>"><?php echo esc_html($term->name); ?></label>
							                                	</li>
							                                	<?php } ?>
							                                </ul>
							                                <?php } ?>
						                                </li>
						                            <?php } ?>
						                        <?php endif; ?>
							    			</ul>
							    		</div>
						    		</div>
						    	<?php endif; 
                                    
                                break;

                            case 'filter_categories':

                            	if( $taxonomy_name !== 'place-categories' ): ?>
						    		<div class="filter-categories col col-xl-2 col-lg-4 col-md-4 col-sm-4 col-xs-4">
						    			<div class="entry-filter">
						    				<h4><?php esc_html_e('Categories', 'golo-framework'); ?></h4>
							    			<ul class="categories filter-control custom-scrollbar">
							    				<?php 
						                        $place_categories = get_categories(array(
						                            'taxonomy'   => 'place-categories',
						                            'hide_empty' => 1,
						                            'orderby'    => 'name',
						                            'order'      => 'ASC',
						                            'parent' 	 => 0,
						                        ));

						                        $category_id = isset($_GET['caid']) ? golo_clean(wp_unslash($_GET['caid'])) : '';

						                        if($place_categories) :
						                            foreach ($place_categories as $place_category) {
						                            	$termchildren = get_term_children( $place_category->term_id, 'place-categories' );
						                            ?>
						                                <li>
						                                    <input type="checkbox" id="golo_<?php echo esc_attr($place_category->slug); ?>" class="custom-checkbox input-control" name="categories" value="<?php echo esc_attr($place_category->term_id); ?>" <?php if( $place_category->term_id == $category_id ) : echo 'checked';endif; ?> />
						                                    <label for="golo_<?php echo esc_attr($place_category->slug); ?>"><?php echo esc_html($place_category->name); ?></label>

						                                    <?php if ($termchildren) { ?>
							                                <ul>
							                                	<?php
							                                		foreach ( $termchildren as $child ) {
	    															$term = get_term_by( 'id', $child, 'place-categories' );
							                                	?>
							                                	<li>
							                                		<input type="checkbox" id="golo_<?php echo esc_attr($term->slug); ?>" class="custom-checkbox input-control" name="categories" value="<?php echo esc_attr($term->term_id); ?>" <?php if( $term->term_id == $category_id ) : echo 'checked';endif; ?> />
							                                    		<label for="golo_<?php echo esc_attr($term->slug); ?>"><?php echo esc_html($term->name); ?></label>
							                                	</li>
							                                	<?php } ?>
							                                </ul>
							                                <?php } ?>
						                                </li>
						                               
						                            <?php } ?>
						                        <?php endif; ?>
							    			</ul>
							    		</div>
						    		</div>
					    		<?php endif;
                                    
                                break;

                            case 'filter_type':

                            	if( $taxonomy_name !== 'place-type' ): ?>
						    		<div class="filter-type col col-xl-2 col-lg-4 col-md-4 col-sm-4 col-xs-4">
						    			<div class="entry-filter">
						    				<h4><?php esc_html_e('Place Type', 'golo-framework'); ?></h4>
							    			<ul class="type filter-control custom-scrollbar">
							    				<?php 
						                        $place_types = get_categories(array(
						                            'taxonomy'   	=> 'place-type',
						                            'hide_empty' 	=> 1,
						                            'orderby' 		=> 'name',
    												'order'   		=> 'ASC',
    												'parent' 	 => 0,
						                        ));

						                        if (isset($_GET['tyid'])) {
						                        	$place_type_id = golo_clean(wp_unslash($_GET['tyid']));
						                        } else {
						                        	$place_type_id = '';
						                        }

						                        if($place_types) :
						                            foreach ($place_types as $place_type) {
						                            	$termchildren = get_term_children( $place_type->term_id, 'place-type' );
						                            ?>
						                                <li>
						                                    <input type="checkbox" id="golo_<?php echo esc_attr($place_type->term_id); ?>" class="custom-checkbox input-control" name="types" value="<?php echo esc_attr($place_type->term_id); ?>" <?php if( $place_type->term_id == $place_type_id ) : echo 'checked';endif; ?> />
						                                    <label for="golo_<?php echo esc_attr($place_type->term_id); ?>"><?php echo esc_html($place_type->name); ?></label>

						                                    <?php if ($termchildren) { ?>
							                                <ul>
							                                	<?php
							                                		foreach ( $termchildren as $child ) {
	    															$term = get_term_by( 'id', $child, 'place-type' );
							                                	?>
							                                	<li>
							                                		<input type="checkbox" id="golo_<?php echo esc_attr($term->term_id); ?>" class="custom-checkbox input-control" name="types" value="<?php echo esc_attr($term->term_id); ?>" <?php if( $term->term_id == $place_type_id ) : echo 'checked';endif; ?> />
						                                    		<label for="golo_<?php echo esc_attr($term->term_id); ?>"><?php echo esc_html($term->name); ?></label>
							                                	</li>
							                                	<?php } ?>
							                                </ul>
							                                <?php } ?>
						                                </li>
						                            <?php } ?>
						                        <?php endif; ?>
							    			</ul>
							    		</div>
						    		</div>
					    		<?php endif;
                                    
                                break;

                            case 'filter_amenities':

                            	if( $taxonomy_name !== 'place-amenities' ): ?>
						    		<div class="filter-amenities col col-xl-2 col-lg-4 col-md-4 col-sm-4 col-xs-4">
						    			<div class="entry-filter">
						    				<h4><?php esc_html_e('Amenities', 'golo-framework'); ?></h4>
							    			<ul class="amenities filter-control custom-scrollbar">
							    				<?php
						                        $place_amenities = get_categories(array(
													'taxonomy'   => 'place-amenities',
													'hide_empty' => 0,
													'orderby'    => 'name',
													'order'      => 'ASC',
													'parent' 	 => 0,
						                        ));

						                        if (isset($_GET['amid'])) {
						                        	$amenities_id = golo_clean(wp_unslash($_GET['amid']));
						                        } else {
						                        	$amenities_id = '';
						                        }

						                        if ($place_amenities) :
						                            foreach ($place_amenities as $place_amenity) {
						                            	$termchildren = get_term_children( $place_amenity->term_id, 'place-amenities' );
						                            ?>
						                                <li>
						                                    <input type="checkbox" id="golo_<?php echo esc_attr($place_amenity->slug); ?>" class="custom-checkbox input-control" name="amenities" value="<?php echo esc_attr($place_amenity->term_id); ?>"  <?php if( $place_amenity->term_id == $amenities_id ) : echo 'checked';endif; ?> />
						                                    <label for="golo_<?php echo esc_attr($place_amenity->slug); ?>"><?php echo esc_html($place_amenity->name); ?></label>

						                                    <?php if ($termchildren) { ?>
							                                <ul>
							                                	<?php
							                                		foreach ( $termchildren as $child ) {
	    															$term = get_term_by( 'id', $child, 'place-amenities' );
							                                	?>
							                                	<li>
							                                		<input type="checkbox" id="golo_<?php echo esc_attr($term->slug); ?>" class="custom-checkbox input-control" name="amenities" value="<?php echo esc_attr($term->term_id); ?>"  <?php if( $term->term_id == $amenities_id ) : echo 'checked';endif; ?> />
						                                    		<label for="golo_<?php echo esc_attr($term->slug); ?>"><?php echo esc_html($term->name); ?></label>
							                                	</li>
							                                	<?php } ?>
							                                </ul>
							                                <?php } ?>
						                                </li>
						                            <?php } ?>
						                        <?php endif; ?>
							    			</ul>
										</div>
						    		</div>
					    		<?php endif;
                                    
                                break;

                           	case 'filter_neighborhood':

                            	if( $taxonomy_name !== 'place-neighborhood' ): ?>
                            		<?php
                            			$place_neighborhoods = get_categories(array(
											'taxonomy'   => 'place-neighborhood',
											'hide_empty' => 0,
											'orderby'    => 'name',
											'order'      => 'ASC',
											'meta_query' => $meta_query
				                        ));
				                        if ($place_neighborhoods) :
                            		?>
						    		<div class="filter-neighborhood col col-xl-2 col-lg-4 col-md-4 col-sm-4 col-xs-4">
						    			<div class="entry-filter">
						    				<h4><?php esc_html_e('Neighborhood', 'golo-framework'); ?></h4>
							    			<ul class="neighborhood filter-control custom-scrollbar">
							    				<?php
					                            foreach ($place_neighborhoods as $place_neighborhood) {
					                            ?>
					                                <li>
					                                    <input type="checkbox" id="golo_<?php echo esc_attr($place_neighborhood->slug); ?>" class="custom-checkbox input-control" name="neighborhood" value="<?php echo esc_attr($place_neighborhood->term_id); ?>" />
					                                    <label for="golo_<?php echo esc_attr($place_neighborhood->slug); ?>"><?php echo esc_html($place_neighborhood->name); ?></label>
					                                </li>
					                            <?php } ?>
							    			</ul>
										</div>
						    		</div>
						    		<?php endif; ?>
					    		<?php endif;
                                    
                                break;

                        }
                    }
                    endif;
                    ?>

					<?php if( $layout == 'layout-top-filter' ) { ?>
		    		<div class="golo-nav-filter">
						<div class="golo-clear-filter">
							<i class="fal fa-sync"></i>
							<span><?php esc_html_e('Clear All', 'golo-framework'); ?></span>
						</div>
					</div>
					<?php } ?>
				</div>
			
	    	</div>

    	</div>

	</div>
	<?php } ?>

	<input type="hidden" name="current_term" value="<?php echo esc_attr($city_id); ?>">
	<input type="hidden" name="type_term" value="<?php echo esc_attr($taxonomy_name); ?>">
	<input type="hidden" name="title" value="<?php echo esc_attr($key); ?>">
	<input type="hidden" name="place_location" value="<?php echo esc_attr($location); ?>">

    <?php
}

// golo_oembed_get
function golo_oembed_get( $url, $args = '' ) {
	if ($url) {
	    // Manually build the IFRAME embed with the related videos option disabled and autoplay turned on
	    if(preg_match("/youtube.com\/watch\?v=([^&]+)/i", $url, $aMatch)){
	        return '<iframe width="560" height="315" src="http://www.youtube.com/embed/' . $aMatch[1] . '?rel=0&autoplay=1&controls=0&loop=1&mute=1&disablekb=1" frameborder="0" allowfullscreen></iframe>';
	    }

	    require_once( ABSPATH . WPINC . '/class-oembed.php' );
	    $oembed = _wp_oembed_get_object();
	    return $oembed->get_html( $url, $args );
	}
}

/**
 * sidebar place
 */
function sidebar_place()
{
    golo_get_template('global/sidebar-place.php');
}

/**
 * single place thumbnails
 */
function gallery_place()
{
    golo_get_template('single-place/thumbnails.php');
}

/**
 * single place head
 */
function single_place_head()
{
    golo_get_template('single-place/head.php');
}

/**
 * single place meta
 */
function single_place_meta()
{
    golo_get_template('single-place/meta.php');
}

/**
 * single place short description
 */
function single_place_short_description()
{
    golo_get_template('single-place/short-description.php');
}

/**
 * single place booking
 */
function single_place_booking()
{
    golo_get_template('single-place/booking.php');
}

/**
 * single place description
 */
function single_place_description()
{
    golo_get_template('single-place/description.php');
}

/**
 * single place time opening
 */
function single_place_time_opening()
{
    golo_get_template('single-place/time-opening.php');
}

/**
 * single place amenities
 */
function single_place_amenities()
{
    golo_get_template('single-place/amenities.php');
}

/**
 * single place menu
 */
function single_place_menu()
{
    golo_get_template('single-place/menu.php');
}

/**
 * single place map
 */
function single_place_map()
{
    golo_get_template('single-place/map.php');
}

/**
 * single place contact
 */
function single_place_contact()
{
    golo_get_template('single-place/contact.php');
}

/**
 * single place additional fields
 */
function single_place_additional()
{
    golo_get_template('single-place/additional.php');
}


/**
 * single place video
 */
function single_place_video()
{
    golo_get_template('single-place/video.php');
}

/**
 * single place review
 */
function single_place_author()
{
    golo_get_template('single-place/author.php');
}

/**
 * single place review
 */
function single_place_review()
{
    golo_get_template('single-place/review.php');
}

/**
 * single place faqs
 */
function single_place_faqs()
{
    golo_get_template('single-place/faqs.php');
}

/**
 * single place yelp review
 */
function single_place_review_yelp()
{
    golo_get_template('single-place/review-yelp.php');
}

/**
 * single place google review
 */
function single_place_google_review()
{
    golo_get_template('single-place/google-review.php');
}

/**
 * related place
 */
function related_place()
{
    golo_get_template('single-place/related.php');
}

/**
 * product related place
 */
function product_related_place()
{
    golo_get_template('single-place/product-related.php');
}

/**
 * author info
 */
function author_info()
{
    golo_get_template('author/author-info.php');
}

/**
 * author place
 */
function author_place()
{
    golo_get_template('author/author-place.php');
}

/**
 * author place
 */
function author_review()
{
    golo_get_template('author/author-review.php');
}

/**
 * author place
 */
function author_about()
{
    golo_get_template('author/author-about.php');
}

add_filter( 'elementor/frontend/print_google_fonts', '__return_false' );










