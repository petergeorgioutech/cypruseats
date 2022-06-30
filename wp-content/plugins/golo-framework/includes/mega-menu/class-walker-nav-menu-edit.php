<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Golo_Walker_Nav_Menu_Edit' ) ) {
	/**
	 * Copied from Walker_Nav_Menu_Edit class in core
	 *
	 * Create HTML list of nav menu input items.
	 */
	class Golo_Walker_Nav_Menu_Edit extends Walker_Nav_Menu {

		/**
		 * Starts the list before the elements are added.
		 *
		 * @see   Walker_Nav_Menu::start_lvl()
		 *
		 * @since 3.0.0
		 *
		 * @param string $output Passed by reference.
		 * @param int    $depth  Depth of menu item. Used for padding.
		 * @param array  $args   Not used.
		 */
		public function start_lvl( &$output, $depth = 0, $args = array() ) {
		}

		/**
		 * Ends the list of after the elements are added.
		 *
		 * @see   Walker_Nav_Menu::end_lvl()
		 *
		 * @since 3.0.0
		 *
		 * @param string $output Passed by reference.
		 * @param int    $depth  Depth of menu item. Used for padding.
		 * @param array  $args   Not used.
		 */
		public function end_lvl( &$output, $depth = 0, $args = array() ) {
		}

		/**
		 * Start the element output.
		 *
		 * @see   Walker_Nav_Menu::start_el()
		 * @since 3.0.0
		 *
		 * @global int   $_wp_nav_menu_max_depth
		 *
		 * @param string $output Passed by reference. Used to append additional content.
		 * @param object $item   Menu item data object.
		 * @param int    $depth  Depth of menu item. Used for padding.
		 * @param array  $args   Not used.
		 * @param int    $id     Not used.
		 */
		public function start_el( &$output, $item, $depth = 0, $args = array(), $id = 0 ) {
			global $_wp_nav_menu_max_depth;
			$_wp_nav_menu_max_depth = $depth > $_wp_nav_menu_max_depth ? $depth : $_wp_nav_menu_max_depth;

			$indent = ( $depth ) ? str_repeat( "\t", $depth ) : '';

			ob_start();
			$item_id      = esc_attr( $item->ID );
			$removed_args = array(
				'action',
				'customlink-tab',
				'edit-menu-item',
				'menu-item',
				'page-tab',
				'_wpnonce',
			);

			$original_title = '';
			if ( 'taxonomy' == $item->type ) {
				$original_title = get_term_field( 'name', $item->object_id, $item->object, 'raw' );
				if ( is_wp_error( $original_title ) ) {
					$original_title = false;
				}
			} elseif ( 'post_type' == $item->type ) {
				$original_object = get_post( $item->object_id );
				$original_title  = $original_object->post_title;
			} elseif ( 'post_type_archive' == $item->type ) {
				$original_object = get_post_type_object( $item->object );
				if ( $original_object ) {
					$original_title = $original_object->labels->archives;
				}
			}

			$classes = array(
				'menu-item menu-item-depth-' . $depth,
				'menu-item-' . esc_attr( $item->object ),
				'menu-item-edit-' . ( ( isset( $_GET['edit-menu-item'] ) && $item_id == $_GET['edit-menu-item'] ) ? 'active' : 'inactive' ),
			);

			$title = $item->title;

			if ( ! empty( $item->_invalid ) ) {
				$classes[] = 'menu-item-invalid';
				/* translators: %s: title of menu item which is invalid */
				$title = sprintf( __( '%s (Invalid)', 'golo-framework' ), $item->title );
			} elseif ( isset( $item->post_status ) && 'draft' == $item->post_status ) {
				$classes[] = 'pending';
				/* translators: %s: title of menu item in draft status */
				$title = sprintf( __( '%s (Pending)', 'golo-framework' ), $item->title );
			}

			$title = ( ! isset( $item->label ) || '' == $item->label ) ? $title : $item->label;

			$submenu_text = '';
			if ( 0 == $depth ) {
				$submenu_text = 'style="display: none;"';
			}

			?>
			<li id="menu-item-<?php echo esc_attr( $item_id ); ?>" class="<?php echo implode( ' ', $classes ); ?>">
			<div class="menu-item-bar">
				<div class="menu-item-handle">
				<span class="item-title"><span class="menu-item-title"><?php echo esc_html( $title ); ?></span> <span
						class="is-submenu" <?php echo esc_attr( $item_id ); ?>><?php esc_html_e( 'sub item', 'golo-framework' ); ?></span></span>
					<span class="item-controls">
                        <span class="item-type"><?php echo esc_html( $item->type_label ); ?></span>
                        <span class="item-order hide-if-js">
                            <a href="<?php
                            echo wp_nonce_url( add_query_arg( array(
                              	'action'    => 'move-up-menu-item',
                              	'menu-item' => $item_id,
                          	), remove_query_arg( $removed_args, admin_url( 'nav-menus.php' ) ) ), 'move-menu_item' );
                            ?>" class="item-move-up"
                               aria-label="<?php esc_attr_e( 'Move up', 'golo-framework' ) ?>">&#8593;</a>
                            |
                            <a href="<?php
                            echo wp_nonce_url( add_query_arg( array(
                              	'action'    => 'move-down-menu-item',
                              	'menu-item' => $item_id,
                          	), remove_query_arg( $removed_args, admin_url( 'nav-menus.php' ) ) ), 'move-menu_item' );
                            ?>" class="item-move-down" aria-label="<?php esc_attr_e( 'Move down', 'golo-framework' ) ?>">&#8595;</a>
                        </span>
                        <a class="item-edit" id="edit-<?php echo esc_attr( $item_id ); ?>" href="<?php
                        echo ( isset( $_GET['edit-menu-item'] ) && $item_id == $_GET['edit-menu-item'] ) ? admin_url( 'nav-menus.php' ) : add_query_arg( 'edit-menu-item', $item_id, remove_query_arg( $removed_args, admin_url( 'nav-menus.php#menu-item-settings-' . $item_id ) ) );
                        ?>" aria-label="<?php esc_attr_e( 'Edit menu item', 'golo-framework' ); ?>"><?php esc_html_e( 'Edit', 'golo-framework' ); ?></a>
                    </span>
				</div>
			</div>

			<div class="menu-item-settings wp-clearfix" id="menu-item-settings-<?php echo esc_attr( $item_id ); ?>">
				<?php if ( 'custom' == $item->type ) : ?>
					<p class="field-url description description-wide">
						<label for="edit-menu-item-url-<?php echo esc_attr( $item_id ); ?>">
							<?php esc_html_e( 'URL', 'golo-framework' ); ?><br/>
							<input type="text" id="edit-menu-item-url-<?php echo esc_attr( $item_id ); ?>"
							       class="widefat code edit-menu-item-url"
							       name="menu-item-url[<?php echo esc_attr( $item_id ); ?>]"
							       value="<?php echo esc_attr( $item->url ); ?>"/>
						</label>
					</p>
				<?php endif; ?>
				<p class="description description-wide">
					<label for="edit-menu-item-title-<?php echo esc_attr( $item_id ); ?>">
						<?php esc_html_e( 'Navigation Label', 'golo-framework' ); ?><br/>
						<input type="text" id="edit-menu-item-title-<?php echo esc_attr( $item_id ); ?>"
						       class="widefat edit-menu-item-title"
						       name="menu-item-title[<?php echo esc_attr( $item_id ); ?>]"
						       value="<?php echo esc_attr( $item->title ); ?>"/>
					</label>
				</p>
				<p class="field-title-attribute field-attr-title description description-wide">
					<label for="edit-menu-item-attr-title-<?php echo esc_attr( $item_id ); ?>">
						<?php esc_html_e( 'Title Attribute', 'golo-framework' ); ?><br/>
						<input type="text" id="edit-menu-item-attr-title-<?php echo esc_attr( $item_id ); ?>"
						       class="widefat edit-menu-item-attr-title"
						       name="menu-item-attr-title[<?php echo esc_attr( $item_id ); ?>]"
						       value="<?php echo esc_attr( $item->post_excerpt ); ?>"/>
					</label>
				</p>
				<p class="field-link-target description">
					<label for="edit-menu-item-target-<?php echo esc_attr( $item_id ); ?>">
						<input type="checkbox" id="edit-menu-item-target-<?php echo esc_attr( $item_id ); ?>"
						       value="_blank"
						       name="menu-item-target[<?php echo esc_attr( $item_id ); ?>]"<?php checked( $item->target, '_blank' ); ?> />
						<?php esc_html_e( 'Open link in a new tab', 'golo-framework' ); ?>
					</label>
				</p>
				<p class="field-css-classes description description-thin">
					<label for="edit-menu-item-classes-<?php echo esc_attr( $item_id ); ?>">
						<?php esc_html_e( 'CSS Classes (optional)', 'golo-framework' ); ?><br/>
						<input type="text" id="edit-menu-item-classes-<?php echo esc_attr( $item_id ); ?>"
						       class="widefat code edit-menu-item-classes"
						       name="menu-item-classes[<?php echo esc_attr( $item_id ); ?>]"
						       value="<?php echo esc_attr( implode( ' ', $item->classes ) ); ?>"/>
					</label>
				</p>
				<p class="field-xfn description description-thin">
					<label for="edit-menu-item-xfn-<?php echo esc_attr( $item_id ); ?>">
						<?php esc_html_e( 'Link Relationship (XFN)', 'golo-framework' ); ?><br/>
						<input type="text" id="edit-menu-item-xfn-<?php echo esc_attr( $item_id ); ?>"
						       class="widefat code edit-menu-item-xfn"
						       name="menu-item-xfn[<?php echo esc_attr( $item_id ); ?>]"
						       value="<?php echo esc_attr( $item->xfn ); ?>"/>
					</label>
				</p>
				<p class="field-description description description-wide">
					<label for="edit-menu-item-description-<?php echo esc_attr( $item_id ); ?>">
						<?php esc_html_e( 'Description', 'golo-framework' ); ?><br/>
						<textarea id="edit-menu-item-description-<?php echo esc_attr( $item_id ); ?>"
						          class="widefat edit-menu-item-description" rows="3" cols="20"
						          name="menu-item-description[<?php echo esc_attr( $item_id ); ?>]"><?php echo esc_html( $item->description ); // textarea_escaped ?></textarea>
						<span
							class="description"><?php esc_html_e( 'The description will be displayed in the menu if the current theme supports it.', 'golo-framework' ); ?></span>
					</label>
				</p>

				<?php
				// This is the added section
				do_action( 'wp_nav_menu_item_custom_fields', $item_id, $item, $depth, $args );
				// end added section
				?>

				<p class="field-move hide-if-no-js description description-wide">
					<label>
						<span><?php esc_html_e( 'Move', 'golo-framework' ); ?></span>
						<a href="#" class="menus-move menus-move-up" data-dir="up"><?php esc_html_e( 'Up one', 'golo-framework' ); ?></a>
						<a href="#" class="menus-move menus-move-down" data-dir="down"><?php esc_html_e( 'Down one', 'golo-framework' ); ?></a>
						<a href="#" class="menus-move menus-move-left" data-dir="left"></a>
						<a href="#" class="menus-move menus-move-right" data-dir="right"></a>
						<a href="#" class="menus-move menus-move-top"
						   data-dir="top"><?php esc_html_e( 'To the top', 'golo-framework' ); ?></a>
					</label>
				</p>

				<div class="menu-item-actions description-wide submitbox">
					<?php if ( 'custom' != $item->type && $original_title !== false ) : ?>
						<p class="link-to-original">
							<?php printf( __( 'Original: %s', 'golo-framework' ), '<a href="' . esc_attr( $item->url ) . '">' . esc_html( $original_title ) . '</a>' ); ?>
						</p>
					<?php endif; ?>
					<a class="item-delete submitdelete deletion" id="delete-<?php echo esc_attr( $item_id ); ?>" 
						href="<?php
					   	echo wp_nonce_url( add_query_arg( array(
                            'action'    => 'delete-menu-item',
                            'menu-item' => $item_id,
                        ), remove_query_arg( $removed_args, admin_url( 'nav-menus.php' ) ) ), 'delete-menu_item_' . $item_id ); ?>"><?php esc_html_e( 'Remove', 'golo-framework' ); ?></a>
					<span class="meta-sep"> | </span> <a
						class="item-cancel submitcancel" id="cancel-<?php echo esc_attr( $item_id ); ?>"
						href="<?php echo esc_url( add_query_arg( array(
                            'edit-menu-item' => $item_id,
                            'cancel'         => time(),
                        ), remove_query_arg( $removed_args, admin_url( 'nav-menus.php' ) ) ) );
						?>#menu-item-settings-<?php echo esc_attr( $item_id ); ?>"><?php esc_html_e( 'Cancel', 'golo-framework' ); ?></a>
				</div>

				<input class="menu-item-data-db-id" type="hidden"
				       name="menu-item-db-id[<?php echo esc_attr( $item_id ); ?>]"
				       value="<?php echo esc_attr( $item_id ); ?>"/>
				<input class="menu-item-data-object-id" type="hidden"
				       name="menu-item-object-id[<?php echo esc_attr( $item_id ); ?>]"
				       value="<?php echo esc_attr( $item->object_id ); ?>"/>
				<input class="menu-item-data-object" type="hidden"
				       name="menu-item-object[<?php echo esc_attr( $item_id ); ?>]"
				       value="<?php echo esc_attr( $item->object ); ?>"/>
				<input class="menu-item-data-parent-id" type="hidden"
				       name="menu-item-parent-id[<?php echo esc_attr( $item_id ); ?>]"
				       value="<?php echo esc_attr( $item->menu_item_parent ); ?>"/>
				<input class="menu-item-data-position" type="hidden"
				       name="menu-item-position[<?php echo esc_attr( $item_id ); ?>]"
				       value="<?php echo esc_attr( $item->menu_order ); ?>"/>
				<input class="menu-item-data-type" type="hidden"
				       name="menu-item-type[<?php echo esc_attr( $item_id ); ?>]"
				       value="<?php echo esc_attr( $item->type ); ?>"/>
			</div><!-- .menu-item-settings-->
			<ul class="menu-item-transport"></ul>
			<?php
			$output .= ob_get_clean();
		}
	}

	// add custom field
	add_action( 'wp_nav_menu_item_custom_fields', 'golo_nav_menu_item_custom_fields', 9, 4 );
	function golo_nav_menu_item_custom_fields( $item_id, $item, $depth, $args ) { ?>

	<?php if ( $item->object !== 'golo_mega_menu' ) { ?>

		<strong style="float:left;width: 392px;background-color: #ededed;padding: 10px;margin: 30px 0 10px -10px;">
			<?php esc_attr_e( 'Custom Fields (from theme)', 'golo-framework' ); ?>
		</strong>

		<p class="description description-wide">
			<label for="menu_item_layout-<?php echo esc_attr( $item_id ); ?>">
				<?php esc_html_e( 'Mega Menu Layout', 'golo-framework' ); ?><br>
				<select id="menu_item_layout-<?php echo esc_attr( $item_id ); ?>" class="widefat" name="menu-item-layout[<?php echo esc_attr( $item_id ); ?>]">
					<option value="default" <?php selected( $item->layout, 'default', true ); ?>><?php esc_html_e( 'Default', 'golo-framework' ); ?></option>
					<option value="full-width" <?php selected( $item->layout, 'full-width', true ); ?>><?php esc_html_e( 'Full width', 'golo-framework' ); ?></option>
					<option value="container" <?php selected( $item->layout, 'container', true ); ?>><?php esc_html_e( 'Container', 'golo-framework' ); ?></option>
					<option value="custom" <?php selected( $item->layout, 'custom', true ); ?>><?php esc_html_e( 'Custom', 'golo-framework' ); ?></option>
				</select>
			</label>
			<span class="description"><?php esc_html_e('Select the mega menu layout', 'golo-framework'); ?></span>
		</p>
		<p class="description description-wide custom-width "<?php echo( 'custom' != $item->layout ? ' style="display:none;"' : '' ); ?>>
			<label for="menu_item_width-<?php echo esc_attr( $item_id ); ?>">
				<?php esc_html_e( 'Mega Menu width', 'golo-framework' ); ?><br>
				<input type="number" id="menu_item_width-<?php echo esc_attr( $item_id ); ?>" class="widefat" name="menu-item-width[<?php echo esc_attr( $item_id ); ?>]" value="<?php echo esc_attr( $item->width ); ?>">
			</label>
		</p>
	<?php
	}

	?>
		<script type='text/javascript'>
			jQuery( document ).ready( function( $ ) {
				$( '#menu_item_layout-<?php echo esc_attr( $item_id ); ?>' ).on( 'change', function() {

					var value = $( this ).val();

					if ('custom' == value) {
						$( '#menu-item-settings-<?php echo esc_attr( $item_id ); ?> .custom-width' ).show();
					} else {
						$( '#menu-item-settings-<?php echo esc_attr( $item_id ); ?> .custom-width' ).hide();
					}
				});
			} );
		</script>
	<?php

	}

	/*
	 * Saves new field to postmeta for navigation
	 */
	add_action( 'wp_update_nav_menu_item', 'golo_nav_update', 10, 3 );
	function golo_nav_update( $menu_id, $menu_item_db_id, $args ) {

		if ( isset ( $_REQUEST['menu-item-layout'] ) && is_array( $_REQUEST['menu-item-layout'] ) && isset( $_REQUEST['menu-item-layout'][ $menu_item_db_id ] ) ) {
			update_post_meta( $menu_item_db_id, '_menu_item_layout', $_REQUEST['menu-item-layout'][ $menu_item_db_id ] );
		}

		if ( isset ( $_REQUEST['menu-item-width'] ) && is_array( $_REQUEST['menu-item-width'] ) && isset( $_REQUEST['menu-item-width'][ $menu_item_db_id ] ) ) {
			update_post_meta( $menu_item_db_id, '_menu_item_width', $_REQUEST['menu-item-width'][ $menu_item_db_id ] );
		}
	}

	/*
	 * Adds value of new field to $item object that will be passed to Golo_Walker_Nav_Menu_Edit
	 */
	add_filter( 'wp_setup_nav_menu_item', 'golo_nav_item' );
	function golo_nav_item( $menu_item ) {

		$menu_item->layout = get_post_meta( $menu_item->ID, '_menu_item_layout', true );
		$menu_item->width  = get_post_meta( $menu_item->ID, '_menu_item_width', true );

		return $menu_item;
	}

	add_filter( 'wp_edit_nav_menu_walker', 'golo_nav_edit_walker', 10, 2 );
	function golo_nav_edit_walker( $walker, $menu_id ) {
		return 'Golo_Walker_Nav_Menu_Edit';
	}
}

new Golo_Walker_Nav_Menu_Edit();