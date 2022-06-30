<?php
if ( !defined('ABSPATH') ) {
    exit;
}
if (!class_exists('Golo_Admin_Setup')) {
    /**
     * Class Golo_Admin_Setup
     */
    class Golo_Admin_Setup
    {
        /**
         * admin_menu
         */
        public function admin_menu()
        {
            add_menu_page(
                esc_html__('Golo', 'golo-framework' ),
                esc_html__('Golo', 'golo-framework'),
                'manage_options',
                'golo_welcome',
                array($this, 'menu_welcome_page_callback'),
                GOLO_PLUGIN_URL . 'assets/images/icon.png',
                2
            );
            add_submenu_page(
                'golo_welcome',
                esc_html__('Welcome', 'golo-framework'),
                esc_html__('Welcome', 'golo-framework'),
                'manage_options',
                'golo_welcome',
                array($this, 'menu_welcome_page_callback')
            );
            add_submenu_page(
                'golo_welcome',
                esc_html__('System', 'golo-framework'),
                esc_html__('System', 'golo-framework'),
                'manage_options',
                'golo_system',
                array($this, 'system_page_callback')
            );
            add_submenu_page(
                'golo_welcome',
                esc_html__('Import', 'golo-framework'),
                esc_html__('Import', 'golo-framework'),
                'manage_options',
                'golo_import',
                array($this, 'import_page_callback')
            );
            
            if (defined('WP_DEBUG') && true === WP_DEBUG) {
               add_submenu_page(
                    'golo_welcome',
                    esc_html__('Export', 'golo-framework'),
                    esc_html__('Export', 'golo-framework'),
                    'manage_options',
                    'golo_export',
                    array($this, 'export_page_callback')
                );
            };

            add_submenu_page(
                'golo_welcome',
                esc_html__('Theme Options', 'golo-framework'),
                esc_html__('Theme Options', 'golo-framework'),
                'manage_options',
                'admin.php?page=golo-framework'
            );
            add_submenu_page(
                'golo_welcome',
                esc_html__('Setup Page', 'golo-framework'),
                esc_html__('Setup Page', 'golo-framework'),
                'manage_options',
                'golo_setup',
                array($this, 'setup_page')
            );
        }

        public function menu_welcome_page_callback()
        {   
            if ( isset( $_POST['purchase_code'] ) ) {
                $purchase_info = Golo_Updater::check_purchase_code( sanitize_key( $_POST['purchase_code'] ) );
                update_option( 'uxper_purchase_code', $_POST['purchase_code'] );
            }
            $purchase_code = get_option( 'uxper_purchase_code' );
            $purchase_class = '';
            $verified = '';
            $check_code = esc_html__('Not verified', 'golo-framework');
            if( $purchase_code ) {
                $purchase_code_info = Golo_Updater::check_purchase_code( $purchase_code );
                if( $purchase_code_info['status_code'] === 200 ) {
                    $purchase_class = 'verified hidden-code';
                    $verified = 'verified';
                    $check_code = esc_html__('Verified', 'golo-framework');
                }
            }
            ?>

            <?php
            $update = Golo_Updater::check_theme_update();
            $new_version = isset( $update['new_version'] ) ? $update['new_version'] : GOLO_THEME_VERSION;
            $get_info = Golo_Updater::get_info();
            if ( $update ) {
                ?>
                <div class="alert-wrap alert-success about-wrap">
                    <div class="msg-update">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><rect x="0" fill="none" width="24" height="24"></rect><g><path d="M12 2C6.477 2 2 6.477 2 12s4.477 10 10 10 10-4.477 10-10S17.523 2 12 2zm1 15h-2v-2h2v2zm0-4h-2l-.5-6h3l-.5 6z"></path></g></svg>

                        <div class="inner-msg">
                        <?php
                        if ( Golo_Updater::check_valid_update() ) {

                            printf( __( 'There is a new version of %1$s available. <a href="%2$s" %3$s>View version %4$s details</a> or <a href="%5$s" %6$s>update now</a>.',
                                'golo-framework' ),
                                GOLO_THEME_NAME,
                                esc_url( add_query_arg( 'action',
                                    'uxper_get_changelogs',
                                    admin_url( 'admin-ajax.php' ) ) ),
                                sprintf( 'class="thickbox" name="Changelogs" aria-label="%s"',
                                    esc_attr( sprintf( __( 'View %1$s version %2$s details' ),
                                        GOLO_THEME_NAME,
                                        GOLO_THEME_VERSION ) ) ),
                                $new_version,
                                wp_nonce_url( self_admin_url( 'update.php?action=upgrade-theme&theme=' ) . GOLO_THEME_SLUG,
                                    'upgrade-theme_' . GOLO_THEME_SLUG ),
                                sprintf( 'id="update-theme" aria-label="%s"',
                                    esc_attr( sprintf( __( 'Update %s now' ), GOLO_THEME_NAME ) ) ) );
                        } else {

                            printf( __( 'There is a new version of %1$s available. <strong>Please enter your purchase code to update the theme.</strong>',
                                'golo-framework' ),
                                GOLO_THEME_NAME );
                        }
                        ?>
                        </div>
                    </div>
                </div>
                <?php
            }
            ?>

            <div class="golo-wrap wrap about-wrap purchase-wrap">
                <div class="entry-heading">
                    <h4><?php esc_html_e('Purchase code', 'golo-framework'); ?><span class="check-code <?php echo esc_html($verified); ?>"><?php echo esc_html($check_code); ?></span></h4>
                </div>

                <form action="" class="purchase-form <?php echo esc_attr($purchase_class); ?>" method="post">
                    <span class="purchase-icon">
                        <svg class="valid" fill="#000000" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 50 50" width="20px" height="20px"><path d="M 22.78125 0 C 21.605469 -0.00390625 20.40625 0.164063 19.21875 0.53125 C 12.902344 2.492188 9.289063 9.269531 11.25 15.59375 L 11.25 15.65625 C 11.507813 16.367188 12.199219 18.617188 12.625 20 L 9 20 C 7.355469 20 6 21.355469 6 23 L 6 47 C 6 48.644531 7.355469 50 9 50 L 41 50 C 42.644531 50 44 48.644531 44 47 L 44 23 C 44 21.355469 42.644531 20 41 20 L 14.75 20 C 14.441406 19.007813 13.511719 16.074219 13.125 15 L 13.15625 15 C 11.519531 9.722656 14.5 4.109375 19.78125 2.46875 C 25.050781 0.832031 30.695313 3.796875 32.34375 9.0625 C 32.34375 9.066406 32.34375 9.089844 32.34375 9.09375 C 32.570313 9.886719 33.65625 13.40625 33.65625 13.40625 C 33.746094 13.765625 34.027344 14.050781 34.386719 14.136719 C 34.75 14.226563 35.128906 14.109375 35.375 13.832031 C 35.621094 13.550781 35.695313 13.160156 35.5625 12.8125 C 35.5625 12.8125 34.433594 9.171875 34.25 8.53125 L 34.25 8.5 C 32.78125 3.761719 28.601563 0.542969 23.9375 0.0625 C 23.550781 0.0234375 23.171875 0 22.78125 0 Z M 9 22 L 41 22 C 41.554688 22 42 22.445313 42 23 L 42 47 C 42 47.554688 41.554688 48 41 48 L 9 48 C 8.445313 48 8 47.554688 8 47 L 8 23 C 8 22.445313 8.445313 22 9 22 Z M 25 30 C 23.300781 30 22 31.300781 22 33 C 22 33.898438 22.398438 34.6875 23 35.1875 L 23 38 C 23 39.101563 23.898438 40 25 40 C 26.101563 40 27 39.101563 27 38 L 27 35.1875 C 27.601563 34.6875 28 33.898438 28 33 C 28 31.300781 26.699219 30 25 30 Z"/></svg>

                        <svg class="invalid" fill="#000000" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 50 50" width="20px" height="20px"><path d="M 25 3 C 18.363281 3 13 8.363281 13 15 L 13 20 L 9 20 C 7.355469 20 6 21.355469 6 23 L 6 47 C 6 48.644531 7.355469 50 9 50 L 41 50 C 42.644531 50 44 48.644531 44 47 L 44 23 C 44 21.355469 42.644531 20 41 20 L 37 20 L 37 15 C 37 8.363281 31.636719 3 25 3 Z M 25 5 C 30.566406 5 35 9.433594 35 15 L 35 20 L 15 20 L 15 15 C 15 9.433594 19.433594 5 25 5 Z M 9 22 L 41 22 C 41.554688 22 42 22.445313 42 23 L 42 47 C 42 47.554688 41.554688 48 41 48 L 9 48 C 8.445313 48 8 47.554688 8 47 L 8 23 C 8 22.445313 8.445313 22 9 22 Z M 25 30 C 23.300781 30 22 31.300781 22 33 C 22 33.898438 22.398438 34.6875 23 35.1875 L 23 38 C 23 39.101563 23.898438 40 25 40 C 26.101563 40 27 39.101563 27 38 L 27 35.1875 C 27.601563 34.6875 28 33.898438 28 33 C 28 31.300781 26.699219 30 25 30 Z"/></svg>
                    </span>
                    <input class="purchase-code" name="purchase_code" type="text" value="<?php echo esc_attr($purchase_code); ?>" placeholder="<?php esc_attr_e('Purchase code', 'golo-framework'); ?>" autocomplete="off"/>
                    <input type="submit" class="button action" value="Submit"/>
                </form>
                <div class="purchase-desc">
                    <?php 
                    if ( isset( $_POST['purchase_code'] ) ) {
                        $purchase_info = Golo_Updater::check_purchase_code( sanitize_key( $_POST['purchase_code'] ) );
                        if( $purchase_info['status_code'] !== 200 ) {
                            esc_html_e('The purchase code was invalid.', 'golo-framework');
                        }else{
                            esc_html_e('Success! The purchase code was valid.', 'golo-framework');
                        }
                    }else{
                        if( $purchase_code ) {
                            $purchase_info = Golo_Updater::check_purchase_code( $purchase_code );
                            if( $purchase_info['status_code'] === 200 ) {
                                esc_html_e('Please do not provide purchase code to anyone.', 'golo-framework');
                            }else{
                                esc_html_e('The purchase code was invalid. Please try again.', 'golo-framework');
                            }
                        }else{
                            esc_html_e('Show us your ThemeForest purchase code to get the automatic update.', 'golo-framework');
                        }
                    }
                    ?>
                </div>
            </div>

            <div class="golo-wrap wrap about-wrap welcome-wrap">
                <h3><?php esc_html_e( 'Welcome to Golo Theme', 'golo-framework'); ?></h3>
                <p><?php esc_html_e("We've assembled some links to get you started", 'golo-framework'); ?></p>
                <div class="wrap-column wrap-column-3 col-started">
                    <div class="panel-column">
                        <div class="entry-heading">
                            <h4><?php esc_html_e('Get Started', 'golo-framework'); ?></h4>
                        </div>
                        <div class="entry-detail">

                            <a href="<?php echo esc_url(admin_url('admin.php?page=golo_import')); ?>" class="button button-primary"><?php esc_html_e( 'Install Sample Data', 'golo-framework' ); ?></a>

                            <p>
                                <span><?php esc_html_e('or,', 'golo-framework') ?></span>
                                <a href="<?php echo esc_url( admin_url( 'customize.php' ) ); ?>"><?php esc_html_e( 'Customize your site', 'golo-framework' ); ?></a>
                            </p>
                        </div>
                    </div>
                    <div class="panel-column col-update">
                        <div class="entry-heading">
                            <h4>
                                <?php esc_html_e('Update', 'golo-framework'); ?>
                            </h4>
                        </div>
                        <div class="entry-detail">
                            <div class="box-detail">
                                <span class="entry-title"><?php esc_html_e( 'Current Version', 'golo-framework'); ?></span>
                                <p><?php echo esc_html(GOLO_THEME_VERSION); ?></p>
                            </div>
                            <div class="box-detail">
                                <span class="entry-title">
                                    <?php esc_html_e( 'Lastest Version', 'golo-framework'); ?>
                                    <?php
                                    if ( Golo_Updater::check_valid_update() && $update ) {

                                        printf( __( '<a class="button uxper-update" href="%1$s" %2$s>Update now</a>',
                                            'golo-framework' ),
                                            wp_nonce_url( self_admin_url( 'update.php?action=upgrade-theme&theme=' ) . GOLO_THEME_SLUG,
                                                'upgrade-theme_' . GOLO_THEME_SLUG ),
                                            sprintf( 'id="update-theme" aria-label="%s"',
                                                esc_attr( sprintf( __( 'Update %s now' ), GOLO_THEME_NAME ) ) ) );
                                    }
                                    ?>        
                                </span>
                                <p><?php echo esc_html($new_version); ?></p>
                            </div>
                        </div>
                    </div>
                    <div class="panel-column col-support">
                        <div class="entry-heading">
                            <h4><?php esc_html_e('Support', 'golo-framework'); ?></h4>
                        </div>
                        <div class="entry-detail">
                            <div class="box-detail">
                                <a class="entry-title" href="<?php echo esc_attr($get_info['docs']); ?>" target="_blank"><?php esc_html_e( 'Online Documentation', 'golo-framework'); ?></a>
                                <p><?php esc_html_e('Detailed instruction to get the right way with our theme.', 'golo-framework'); ?></p>
                            </div>
                            <div class="box-detail">
                                <a class="entry-title" href="<?php echo esc_attr($get_info['support']); ?>" target="_blank"><?php esc_html_e( 'Request Support', 'golo-framework'); ?></a>
                                <p><?php esc_html_e('Need help? Our users enjoy premium 24/7 support.', 'golo-framework'); ?></p>
                            </div>
                        </div>
                    </div>
                </div>
                
            </div>
            
            <?php
            $golo_tgm_plugins       = apply_filters( 'golo_tgm_plugins', array() );
            $installed_plugins      = class_exists( 'TGM_Plugin_Activation' ) ? TGM_Plugin_Activation::$instance->plugins : array();
            $required_plugins_count = 0;
            ?>
            <div class="golo-wrap wrap about-wrap plugins-wrap">
                <div class="entry-heading">
                    <h4><?php esc_html_e('Plugins', 'golo-framework'); ?></h4>
                    <p><?php esc_html_e('Please install and activate plugins to use all functionality.', 'golo-framework'); ?></p>
                </div>

                <div class="wrap-content">
                    <?php if ( ! empty( $golo_tgm_plugins ) && class_exists( 'TGM_Plugin_Activation' ) ) : ?>
                        <div class="grid columns-3">
                        <?php foreach ( $golo_tgm_plugins as $plugin ) : ?>
                            <?php
                            $plugin_obj = $installed_plugins[ $plugin['slug'] ];

                            $css_class = '';
                            if ( $plugin['required'] ) {
                                if ( TGM_Plugin_Activation::$instance->is_plugin_active( $plugin['slug'] ) ) {
                                    $css_class .= 'plugin-activated';
                                } else {
                                    $css_class .= 'plugin-deactivated';
                                }
                            }

                            $thumb = isset( $plugin['thumb'] ) ? esc_html( $plugin['thumb'] ) : '';
                            ?>
                            <div class="item <?php echo esc_attr($css_class); ?>">
                                <div class="plugin-thumb">
                                    <img src="<?php echo esc_url($thumb); ?>" alt="<?php echo esc_html( $plugin['name'] ); ?>">

                                    <div class="plugin-type">
                                        <span><?php echo $plugin['required'] ? esc_html__( 'Required', 'golo-framework' ) : esc_html__( 'Recommended', 'golo-framework' ); ?></span>
                                    </div>
                                </div>
                                <div class="entry-detail">
                                    <div class="plugin-name">
                                        <span><?php echo esc_html( $plugin['name'] ); ?></span>
                                        <sup><?php echo isset( $plugin['version'] ) ? esc_html( $plugin['version'] ) : ''; ?></sup>
                                    </div>
                                    
                                    <div class="plugin-action">
                                        <?php echo Golo_Plugins::get_plugin_action( $plugin_obj ); ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <?php else : ?>

                        <p><?php esc_html_e( 'This theme doesn\'t require any plugins.', 'golo-framework' ); ?></p>

                    <?php endif; ?>

                </div><!-- end .wrap-content -->
            </div>
            
            <div class="golo-wrap wrap about-wrap changelogs-wrap">
                <div class="entry-heading">
                    <h4><?php esc_html_e('Changelogs', 'golo-framework'); ?></h4>
                </div>

                <div class="wrap-content">
                    <table class="table-changelogs">
                        <thead>
                            <tr>
                                <th><?php esc_html_e('Version', 'golo-framework'); ?></th>
                                <th><?php esc_html_e('Description', 'golo-framework'); ?></th>
                                <th><?php esc_html_e('Date', 'golo-framework'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php echo Golo_Updater::get_changelogs( true ); ?>
                        </tbody>
                    </table>
                </div><!-- end .wrap-content -->
            </div>

            <?php
        }

        public function system_page_callback()
        {
            add_thickbox();
            function golo_core_let_to_num( $size ) {
                $l   = substr( $size, - 1 );
                $ret = substr( $size, 0, - 1 );
                switch ( strtoupper( $l ) ) {
                    case 'P':
                        $ret *= 1024;
                    case 'T':
                        $ret *= 1024;
                    case 'G':
                        $ret *= 1024;
                    case 'M':
                        $ret *= 1024;
                    case 'K':
                        $ret *= 1024;
                }

                return $ret;
            }

            ?>
            <div class="golo-system-page">
                <div class="about-wrap box">
                    <div class="box-header">
                        <span class="icon"><i class="lar la-lightbulb"></i></span>
                        <?php esc_html_e('WordPress Environment', 'golo-framework'); ?>
                    </div>
                    <div class="box-body">
                        <table class="wp-list-table widefat striped system" cellspacing="0">
                            <tbody>
                            <tr>
                                <td class="help"><?php echo '<a href="#" class="hint--right" aria-label="' . esc_attr__( 'The URL of your site\'s homepage.', 'golo-framework' ) . '">[?]</a>'; ?></td>
                                <td class="title"><?php _e( 'Home URL', 'golo-framework' ); ?></td>
                                <td><?php form_option( 'home' ); ?></td>
                            </tr>
                            <tr>
                                <td class="help"><?php echo '<a href="#" class="hint--right" aria-label="' . esc_attr__( 'The root URL of your site.', 'golo-framework' ) . '">[?]</a>'; ?></td>
                                <td class="title"><?php _e( 'Site URL', 'golo-framework' ); ?></td>
                                <td><?php form_option( 'siteurl' ); ?></td>
                            </tr>
                            <tr>
                                <td class="help"><?php echo '<a href="#" class="hint--right" aria-label="' . esc_attr__( 'The version of WordPress installed on your site.', 'golo-framework' ) . '">[?]</a>'; ?></td>
                                <td class="title"><?php _e( 'WP Version', 'golo-framework' ); ?></td>
                                <td><?php bloginfo( 'version' ); ?></td>
                            </tr>
                            <tr>
                                <td class="help"><?php echo '<a href="#" class="hint--right" aria-label="' . esc_attr__( 'Whether or not you have WordPress Multisite enabled.', 'golo-framework' ) . '">[?]</a>'; ?></td>
                                <td class="title"><?php _e( 'WP Multisite', 'golo-framework' ); ?></td>
                                <td>
                                    <?php if ( is_multisite() ) {
                                        echo '&#10004;';
                                    } else {
                                        echo '&ndash;';
                                    } ?>        
                                </td>
                            </tr>
                            <tr>
                                <td class="help"><?php echo '<a href="#" class="hint--right" aria-label="' . esc_attr__( 'The maximum amount of memory (RAM) that your site can use at one time.', 'golo-framework' ) . '">[?]</a>'; ?></td>
                                <td class="title"><?php _e( 'WP Memory Limit', 'golo-framework' ); ?></td>
                                <td>
                                <?php
                                $memory = golo_core_let_to_num( WP_MEMORY_LIMIT );

                                if ( function_exists( 'memory_get_usage' ) ) {
                                    $server_memory = golo_core_let_to_num( @ini_get( 'memory_limit' ) );
                                    $memory        = max( $memory, $server_memory );
                                }

                                if ( $memory < 134217728 ) {
                                    echo '<mark class="error">' . sprintf( __( '%s - We recommend setting memory to at least 128MB. See: <a href="%s" target="_blank">Increasing memory allocated to PHP</a>', 'golo-framework' ), size_format( $memory ), 'http://codex.wordpress.org/Editing_wp-config.php#Increasing_memory_allocated_to_PHP' ) . '</mark>';
                                } else {
                                    echo '<mark class="yes">' . size_format( $memory ) . '</mark>';
                                }
                                ?>
                                </td>
                            </tr>
                            <tr>
                                <td class="help"><?php echo '<a href="#" class="hint--right" aria-label="' . esc_attr__( 'Displays whether or not WordPress is in Debug Mode.', 'golo-framework' ) . '">[?]</a>'; ?></td>
                                <td class="title"><?php _e( 'WP Debug Mode', 'golo-framework' ); ?></td>
                                <td>
                                <?php if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
                                    echo '<mark class="yes">&#10004;</mark>';
                                } else {
                                    echo '&ndash;';
                                } ?>  
                                </td>
                            </tr>
                            <tr>
                                <td class="help"><?php echo '<a href="#" class="hint--right" aria-label="' . esc_attr__( 'The current language used by WordPress. Default = English', 'golo-framework' ) . '">[?]</a>'; ?></td>
                                <td class="title"><?php _e( 'Language', 'golo-framework' ); ?></td>
                                <td><?php echo get_locale() ?></td>
                            </tr>
                            <tr>
                                <td class="help"><?php echo '<a href="#" class="hint--right" aria-label="' . esc_attr__( 'The current theme name', 'golo-framework' ) . '">[?]</a>'; ?></td>
                                <td class="title"><?php _e( 'Theme Name', 'golo-framework' ); ?></td>
                                <td><?php echo GOLO_THEME_NAME; ?></td>
                            </tr>
                            <tr>
                                <td class="help"><?php echo '<a href="#" class="hint--right" aria-label="' . esc_attr__( 'The current theme version', 'golo-framework' ) . '">[?]</a>'; ?></td>
                                <td class="title"><?php _e( 'Theme Version', 'golo-framework' ); ?></td>
                                <td><?php echo GOLO_THEME_VERSION; ?></td>
                            </tr>
                            <tr>
                                <td class="help"><?php echo '<a href="#" class="hint--right" aria-label="' . esc_attr__( 'Installed plugins', 'golo-framework' ) . '">[?]</a>'; ?></td>
                                <td class="title"><?php _e( 'Activated Plugins', 'golo-framework' ); ?></td>
                                <td>
                                    <?php
                                    $all_plugins = get_plugins();
                                    foreach ( $all_plugins as $key => $val ) {
                                        if ( is_plugin_active( $key ) ) {
                                            echo $val['Name'] . ' ' . $val['Version'] . ', ';
                                        }
                                    }
                                    ?>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="about-wrap box">
                    <div class="box-header">
                        <span class="icon"><i class="lar la-lightbulb"></i></span>
                        <?php esc_html_e('Server Environment', 'golo-framework'); ?>
                    </div>
                    <div class="box-body">
                        <table class="wp-list-table widefat striped system" cellspacing="0">
                            <tbody>
                            <tr>
                                <td class="help"><?php echo '<a href="#" class="hint--right" aria-label="' . esc_attr__( 'Information about the web server that is currently hosting your site.', 'golo-framework' ) . '">[?]</a>'; ?></td>
                                <td class="title"><?php _e( 'Server Info', 'golo-framework' ); ?></td>
                                <td><?php echo esc_html( $_SERVER['SERVER_SOFTWARE'] ); ?></td>
                            </tr>
                            <tr>
                                <td class="help"><?php echo '<a href="#" class="hint--right" aria-label="' . esc_attr__( 'The version of PHP installed on your hosting server.', 'golo-framework' ) . '">[?]</a>'; ?></td>
                                <td class="title"><?php _e( 'PHP Version', 'golo-framework' ); ?></td>
                                <td><?php if ( function_exists( 'phpversion' ) ) {
                                        $php_version = esc_html( phpversion() );

                                        if ( version_compare( $php_version, '5.6', '<' ) ) {
                                            echo '<mark class="error">' . esc_html__( 'Golo framework requires PHP version 5.6 or greater. Please contact your hosting provider to upgrade PHP version.', 'golo-framework' ) . '</mark>';
                                        } else {
                                            echo $php_version;
                                        }
                                    }
                                    ?></td>
                            </tr>
                            <?php if ( function_exists( 'ini_get' ) ) : ?>
                                <tr>
                                    <td class="help"><?php echo '<a href="#" class="hint--right" aria-label="' . esc_attr__( 'The largest filesize that can be contained in one post.', 'golo-framework' ) . '">[?]</a>'; ?></td>
                                    <td class="title"><?php _e( 'PHP Post Max Size', 'golo-framework' ); ?></td>
                                    <td><?php echo size_format( golo_core_let_to_num( ini_get( 'post_max_size' ) ) ); ?></td>
                                </tr>
                                <tr>
                                    <td class="help"><?php echo '<a href="#" class="hint--right" aria-label="' . esc_attr__( 'The amount of time (in seconds) that your site will spend on a single operation before timing out (to avoid server lockups)', 'golo-framework' ) . '">[?]</a>'; ?></td>
                                    <td class="title"><?php _e( 'PHP Time Limit', 'golo-framework' ); ?></td>
                                    <td><?php
                                        $time_limit = ini_get( 'max_execution_time' );

                                        if ( $time_limit > 0 && $time_limit < 180 ) {
                                            echo '<mark class="error">' . sprintf( __( '%s - We recommend setting max execution time to at least 180. See: <a href="%s" target="_blank">Increasing max execution to PHP</a>', 'golo-framework' ), $time_limit, 'http://codex.wordpress.org/Common_WordPress_Errors#Maximum_execution_time_exceeded' ) . '</mark>';
                                        } else {
                                            echo '<mark class="yes">' . $time_limit . '</mark>';
                                        }
                                        ?></td>
                                </tr>
                                <tr>
                                    <td class="help"><?php echo '<a href="#" class="hint--right" aria-label="' . esc_attr__( 'The maximum number of variables your server can use for a single function to avoid overloads.', 'golo-framework' ) . '">[?]</a>'; ?></td>
                                    <td class="title"><?php _e( 'PHP Max Input Vars', 'golo-framework' ); ?></td>
                                    <td><?php
                                        $max_input_vars = ini_get( 'max_input_vars' );

                                        if ( $max_input_vars < 5000 ) {
                                            echo '<mark class="error">' . sprintf( __( '%s - Max input vars limitation will truncate POST data such as menus. Required >= 5000', 'golo-framework' ), $max_input_vars ) . '</mark>';
                                        } else {
                                            echo '<mark class="yes">' . $max_input_vars . '</mark>';
                                        }
                                        ?></td>
                                </tr>
                            <?php endif; ?>
                            <tr>
                                <td class="help"><?php echo '<a href="#" class="hint--right" aria-label="' . esc_attr__( 'The version of MySQL installed on your hosting server.', 'golo-framework' ) . '">[?]</a>'; ?></td>
                                <td class="title"><?php _e( 'MySQL Version', 'golo-framework' ); ?></td>
                                <td>
                                    <?php
                                    global $wpdb;
                                    echo $wpdb->db_version();
                                    ?>
                                </td>
                            </tr>
                            <tr>
                                <td class="help"><?php echo '<a href="#" class="hint--right" aria-label="' . esc_attr__( 'The largest filesize that can be uploaded to your WordPress installation.', 'golo-framework' ) . '">[?]</a>'; ?></td>
                                <td class="title"><?php _e( 'Max Upload Size', 'golo-framework' ); ?></td>
                                <td><?php echo size_format( wp_max_upload_size() ); ?></td>
                            </tr>
                            <tr>
                                <td class="help"><?php echo '<a href="#" class="hint--right" aria-label="' . esc_attr__( 'The default timezone for your server.', 'golo-framework' ) . '">[?]</a>'; ?></td>
                                <td class="title"><?php _e( 'Default Timezone is UTC', 'golo-framework' ); ?></td>
                                <td><?php
                                    $default_timezone = date_default_timezone_get();
                                    if ( 'UTC' !== $default_timezone ) {
                                        echo '<mark class="error">&#10005; ' . sprintf( __( 'Default timezone is %s - it should be UTC', 'golo-framework' ), $default_timezone ) . '</mark>';
                                    } else {
                                        echo '<mark class="yes">&#10004;</mark>';
                                    } ?>
                                </td>
                            </tr>
                            <?php
                            $checks = array();
                            // fsockopen/cURL
                            $checks['fsockopen_curl']['name'] = 'fsockopen/cURL';
                            $checks['fsockopen_curl']['help'] = '<a href="#" class="hint--right" aria-label="' . esc_attr__( 'Plugins may use it when communicating with remote services.', 'golo-framework' ) . '">[?]</a>';
                            if ( function_exists( 'fsockopen' ) || function_exists( 'curl_init' ) ) {
                                $checks['fsockopen_curl']['success'] = true;
                            } else {
                                $checks['fsockopen_curl']['success'] = false;
                                $checks['fsockopen_curl']['note']    = __( 'Your server does not have fsockopen or cURL enabled. Please contact your hosting provider to enable it.', 'golo-framework' ) . '</mark>';
                            }
                            // DOMDocument
                            $checks['dom_document']['name'] = 'DOMDocument';
                            $checks['dom_document']['help'] = '<a href="#" class="hint--right" aria-label="' . esc_attr__( 'WordPress Importer use DOMDocument.', 'golo-framework' ) . '">[?]</a>';
                            if ( class_exists( 'DOMDocument' ) ) {
                                $checks['dom_document']['success'] = true;
                            } else {
                                $checks['dom_document']['success'] = false;
                                $checks['dom_document']['note']    = sprintf( __( 'Your server does not have <a href="%s">the DOM extension</a> class enabled. Please contact your hosting provider to enable it.', 'golo-framework' ), 'http://php.net/manual/en/intro.dom.php' ) . '</mark>';
                            }
                            // XMLReader
                            $checks['xml_reader']['name'] = 'XMLReader';
                            $checks['xml_reader']['help'] = '<a href="#" class="hint--right" aria-label="' . esc_attr__( 'WordPress Importer use XMLReader.', 'golo-framework' ) . '">[?]</a>';
                            if ( class_exists( 'XMLReader' ) ) {
                                $checks['xml_reader']['success'] = true;
                            } else {
                                $checks['xml_reader']['success'] = false;
                                $checks['xml_reader']['note']    = sprintf( __( 'Your server does not have <a href="%s">the XMLReader extension</a> class enabled. Please contact your hosting provider to enable it.', 'golo-framework' ), 'http://php.net/manual/en/intro.xmlreader.php' ) . '</mark>';
                            }
                            // WP Remote Get Check
                            $checks['wp_remote_get']['name'] = __( 'Remote Get', 'golo-framework' );
                            $checks['wp_remote_get']['help'] = '<a href="#" class="hint--right" aria-label="' . esc_attr__( 'Retrieve the raw response from the HTTP request using the GET method.', 'golo-framework' ) . '">[?]</a>';
                            $response                        = wp_remote_get( GOLO_PLUGIN_URL . 'assets/test.txt' );

                            if ( ! is_wp_error( $response ) && $response['response']['code'] >= 200 && $response['response']['code'] < 300 ) {
                                $checks['wp_remote_get']['success'] = true;
                            } else {
                                $checks['wp_remote_get']['note'] = __( ' WordPress function <a href="https://codex.wordpress.org/Function_Reference/wp_remote_get">wp_remote_get()</a> test failed. Please contact your hosting provider to enable it.', 'golo-framework' );
                                if ( is_wp_error( $response ) ) {
                                    $checks['wp_remote_get']['note'] .= ' ' . sprintf( __( 'Error: %s', 'golo-framework' ), sanitize_text_field( $response->get_error_message() ) );
                                } else {
                                    $checks['wp_remote_get']['note'] .= ' ' . sprintf( __( 'Status code: %s', 'golo-framework' ), sanitize_text_field( $response['response']['code'] ) );
                                }
                                $checks['wp_remote_get']['success'] = false;
                            }
                            foreach ( $checks as $check ) {
                                $mark = ! empty( $check['success'] ) ? 'yes' : 'error';
                                ?>
                                <tr>
                                    <td class="help"><?php echo isset( $check['help'] ) ? $check['help'] : ''; ?></td>
                                    <td class="title"><?php echo esc_html( $check['name'] ); ?></td>
                                    <td>
                                        <mark class="<?php echo $mark; ?>">
                                            <?php echo ! empty( $check['success'] ) ? '&#10004' : '&#10005'; ?><?php echo ! empty( $check['note'] ) ? wp_kses_data( $check['note'] ) : ''; ?>
                                        </mark>
                                    </td>
                                </tr>
                                <?php
                            }
                            ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <?php
        }

        public function import_page_callback()
        {
            $import_issues        = Golo_Importer::get_import_issues();
            $ignore_import_issues = apply_filters( 'golo_ignore_import_issues', false );

            ?>
            <div class="golo-wrap about-wrap">

                <?php
                /**
                 * Action: golo_page_import_before_content
                 */
                do_action( 'golo_page_import_before_content' );
                ?>

                <!-- Important Notes -->
                <?php require_once GOLO_PLUGIN_DIR . 'includes/import/views/box-import-notes.php'; ?>
                <!-- /Important Notes -->

                <?php if ( ! empty( $import_issues ) && ! $ignore_import_issues ) : ?>
                    <!-- Issues -->
                    <?php require_once GOLO_PLUGIN_DIR . 'includes/import/views/box-import-issues.php'; ?>
                    <!-- /Issues -->
                <?php else : ?>
                    <!-- Import Demos -->
                    <?php require_once GOLO_PLUGIN_DIR . 'includes/import/views/box-import-demos.php'; ?>
                    <!-- /Import Demos -->
                <?php endif; ?>

                <?php
                /**
                 * Action: golo_page_import_after_content
                 */
                do_action( 'golo_page_import_after_content' );
                ?>

            </div>
            <?php
        }

        public function export_page_callback()
        {
            $export_items = Golo_Exporter::get_export_items();
            ?>
            <div class="about-wrap golo-box golo-box--gray golo-box--export">
                <div class="golo-box__body grid columns-3">

                    <?php
                    /**
                     * Action: golo_box_export_before_content
                     */
                    do_action( 'golo_box_export_before_content' );
                    ?>

                    <?php if ( ! empty( $export_items ) ) : ?>
                        <?php foreach ( $export_items as $item ) : ?>
                            <?php if ( isset( $item['name'], $item['action'], $item['icon'] ) ) : ?>
                                <!-- Export <?php echo esc_html( $item['name'] ); ?>-->
                                <div class="golo-export-item golo-export-item--<?php echo esc_attr( sanitize_title( $item['name'] ) ); ?>">
                                    <form action="<?php echo esc_url( admin_url( '/admin-post.php' ) ); ?>" method="POST" class="golo-export-item__form">
                                        <?php if ( isset( $item['description'] ) ) : ?>
                                            <span class="golo-export-item__help hint--right" aria-label="<?php echo esc_attr( $item['description'] ); ?>"><i class="fal fa-question-circle"></i></span>
                                        <?php endif; ?>

                                        <input type="hidden" name="_wpnonce" value="<?php echo esc_attr( wp_create_nonce( $item['action'] ) ); ?>">
                                        <input type="hidden" name="action" value="<?php echo esc_attr( $item['action'] ); ?>">

                                        <p class="golo-export-item__name"><i class="<?php echo esc_attr( $item['icon'] ); ?>"></i><?php echo esc_html( $item['name'] ); ?></p>

                                        <p class="golo-export-item__description"><?php echo esc_html( $item['description'] ); ?></p>

                                        <div class="golo-export-item__icon<?php echo esc_attr( isset( $item['input_file_name'] ) && $item['input_file_name'] ? ' golo-export-item__icon--has-file-name-input' : '' ); ?>">

                                            <?php if ( isset( $item['input_file_name'], $item['default_file_name'] ) && $item['input_file_name'] ) : ?>
                                                <input type="text"
                                                    name="<?php echo esc_attr( sanitize_title( $item['name'] ) . '-file-name' ); ?>"
                                                    id="<?php echo esc_attr( sanitize_title( $item['name'] ) . '-file-name' ); ?>"
                                                    class="golo-export-item__input"
                                                    value="<?php echo esc_attr( $item['default_file_name'] ); ?>">
                                            <?php endif; ?>
                                        </div>

                                        <div class="golo-export-item__footer">
                                            <?php if ( isset( $item['export_page_url'] ) && ! empty( $item['export_page_url'] ) ) : ?>
                                                <a href="<?php echo esc_url( $item['export_page_url'] ); ?>" class="button golo-export-item__button"><?php esc_html_e( 'Export', 'golo-framework' ); ?><i class="las la-download"></i></a>
                                            <?php else : ?>
                                                <button type="submit" name="export" class="button golo-export-item__button"><?php esc_html_e( 'Export', 'golo-framework' ); ?><i class="las la-download"></i></button>
                                            <?php endif; ?>
                                        </div>
                                    </form>
                                </div>
                                <!-- /Export <?php echo esc_html( $item['name'] ); ?> -->
                            <?php endif; ?>
                        <?php endforeach; ?>
                    <?php endif; ?>

                    <?php
                    /**
                     * Action: golo_box_export_after_content
                     */
                    do_action( 'golo_box_export_after_content' );
                    ?>
                </div>
            </div>
            <?php
        }

        /**
         * Redirect the setup page on first activation
         */
        public function redirect()
        {
            // Bail if no activation redirect transient is set
            if (!get_transient('_golo_activation_redirect')) {
                return;
            }

            if (!current_user_can('manage_options')) {
                return;
            }

            // Delete the redirect transient
            delete_transient('_golo_activation_redirect');

            // Bail if activating from network, or bulk, or within an iFrame
            if (is_network_admin() || isset($_GET['activate-multi']) || defined('IFRAME_REQUEST')) {
                return;
            }

            if ((isset($_GET['action']) && 'upgrade-plugin' == $_GET['action']) && (isset($_GET['plugin']) && strstr($_GET['plugin'], 'golo-framework.php'))) {
                return;
            }

            wp_redirect(admin_url('admin.php?page=golo_setup'));
            exit;
        }

        /**
         * Create page on first activation
         * @param $title
         * @param $content
         * @param $option
         */
        private function create_page($title, $content, $option)
        {
            $page_data = array(
                'post_status'    => 'publish',
                'post_type'      => 'page',
                'post_author'    => 1,
                'post_name'      => sanitize_title($title),
                'post_title'     => $title,
                'post_content'   => $content,
                'post_parent'    => 0,
                'comment_status' => 'closed'
            );
            $page_id = wp_insert_post($page_data);
            if ($option) {
                $config = get_option(GOLO_OPTIONS_NAME);
                $config[$option] = $page_id;
                update_option(GOLO_OPTIONS_NAME, $config);
            }
        }

        /**
         * Output page setup
         */
        public function setup_page()
        {
            $step = !empty($_GET['step']) ? absint(wp_unslash($_GET['step'])) : 1;
            if (3 === $step && !empty($_POST)) {
                $create_pages = isset($_POST['golo-create-page']) ? golo_clean(wp_unslash($_POST['golo-create-page']))  : array();
                $page_titles  = isset($_POST['golo-page-title']) ? golo_clean(wp_unslash($_POST['golo-page-title']))  : array();
                $pages_to_create = array(
                    'dashboard'         => '[golo_dashboard]',
                    'submit_place'      => '[golo_submit_place]',
                    'my_places'         => '[golo_my_places]',
                    'my_profile'        => '[golo_my_profile]',
                    'my_wishlist'       => '[golo_my_wishlist]',
                    'my_booking'        => '[golo_my_booking]',
                    'bookings'          => '[golo_bookings]',
                    'packages'          => '[golo_packages]',
                    'payment'           => '[golo_payment]',
                    'payment_completed' => '[golo_payment_completed]',
                    'country'           => '[golo_country]',
                );
                foreach ($pages_to_create as $page => $content) {
                    if (!isset($create_pages[$page]) || empty($page_titles[$page])) {
                        continue;
                    }
                    $this->create_page(sanitize_text_field($page_titles[$page]), $content, 'golo_' . $page . '_page_id');
                }
            }
            ?>
            <div class="golo-setup-wrap golo-wrap about-wrap setup-wrap">
                <h3><?php esc_html_e('Golo Setup', 'golo-framework'); ?></h3>
                <ul class="golo-setup-steps">
                    <li class="<?php if($step === 1) echo 'golo-setup-active-step'; ?>"><?php esc_html_e('1. Introduction', 'golo-framework'); ?></li>
                    <li class="<?php if($step === 2) echo 'golo-setup-active-step'; ?>"><?php esc_html_e('2. Page Setup', 'golo-framework'); ?></li>
                    <li class="<?php if($step === 3) echo 'golo-setup-active-step'; ?>"><?php esc_html_e('3. Done', 'golo-framework'); ?></li>
                </ul>

                <?php if(1 === $step) : ?>

                    <h3><?php esc_html_e('Setup Wizard Introduction', 'golo-framework'); ?></h3>
                    <p><?php _e('Thanks for installing <em>Golo</em>!', 'golo-framework'); ?></p>
                    <p><?php esc_html_e('This setup wizard will help you get started by creating the pages for place submission, place management, profile management, listing place, place whishlist, place booking...', 'golo-framework'); ?></p>
                    <p><?php printf(__('If you want to skip the wizard and setup the pages and shortcodes yourself manually, the process is still relatively simple. Refer to the %sdocumentation%s for help.', 'golo-framework'), '<a href="#"', '</a>'); ?></p>

                    <p class="submit">
                        <a href="<?php echo esc_url(add_query_arg('step', 2)); ?>"
                           class="button button-primary"><?php esc_html_e('Continue to page setup', 'golo-framework'); ?></a>
                        <a href="<?php echo esc_url(admin_url('admin.php?page=golo_setup&step=3')); ?>"
                           class="button"><?php esc_html_e('Skip setup. I will setup the plugin manually (Not Recommended)', 'golo-framework'); ?></a>
                    </p>

                <?php endif; ?>
                <?php if(2 === $step) : ?>

                    <h3><?php esc_html_e('Page Setup', 'golo-framework'); ?></h3>

                    <p><?php printf(__('<em>golo-framework</em> includes %1$sshortcodes%2$s which can be used within your %3$spages%2$s to output content. These can be created for you below. For more information on the golo-framework shortcodes view the %4$sshortcode documentation%2$s.', 'golo-framework'), '<a href="https://codex.wordpress.org/shortcode" title="What is a shortcode?" target="_blank" class="help-page-link">', '</a>', '<a href="http://codex.wordpress.org/Pages" target="_blank" class="help-page-link">', '<a href="#" target="_blank" class="help-page-link">'); ?></p>

                    <form action="<?php echo esc_url(add_query_arg('step', 3)); ?>" method="post">
                        <table class="golo-shortcodes widefat">
                            <thead>
                            <tr>
                                <th>&nbsp;</th>
                                <th><?php esc_html_e('Page Title', 'golo-framework'); ?></th>
                                <th><?php esc_html_e('Page Description', 'golo-framework'); ?></th>
                                <th><?php esc_html_e('Content Shortcode', 'golo-framework'); ?></th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td><input type="checkbox" checked="checked" name="golo-create-page[dashboard]"/></td>
                                <td><input type="text" value="<?php echo esc_attr(_x('Dashboard', 'Default page title (wizard)', 'golo-framework')); ?>" name="golo-page-title[dashboard]"/></td>
                                <td>
                                    <p><?php esc_html_e('This page show dashboard.', 'golo-framework'); ?></p>
                                </td>
                                <td><code>[golo_dashboard]</code></td>
                            </tr>
                            <tr>
                                <td><input type="checkbox" checked="checked" name="golo-create-page[places]"/></td>
                                <td><input type="text" value="<?php echo esc_attr(_x('Places', 'Default page title (wizard)', 'golo-framework')); ?>" name="golo-page-title[places]"/></td>
                                <td>
                                    <p><?php esc_html_e('This page show all place.', 'golo-framework'); ?></p>
                                </td>
                                <td><code>[golo_places]</code></td>
                            </tr>    
                            <tr>
                                <td><input type="checkbox" checked="checked" name="golo-create-page[submit_place]"/></td>
                                <td><input type="text" value="<?php echo esc_attr(_x('New Place', 'Default page title (wizard)', 'golo-framework')); ?>" name="golo-page-title[submit_place]"/></td>
                                <td>
                                    <p><?php esc_html_e('This page allows users to add place to your website via the front-end.', 'golo-framework'); ?></p>
                                </td>
                                <td><code>[golo_submit_place]</code></td>
                            </tr>
                            <tr>
                                <td><input type="checkbox" checked="checked" name="golo-create-page[my_places]"/></td>
                                <td><input type="text" value="<?php echo esc_attr(_x('My Places', 'Default page title (wizard)', 'golo-framework')); ?>" name="golo-page-title[my_places]"/></td>
                                <td>
                                    <p><?php esc_html_e('This page allows users to view their own "My Places" via the front-end.', 'golo-framework'); ?></p>
                                </td>
                                <td><code>[golo_my_places]</code></td>
                            </tr>
                            <tr>
                                <td><input type="checkbox" checked="checked" name="golo-create-page[my_profile]"/></td>
                                <td><input type="text" value="<?php echo esc_attr(_x('My Profile', 'Default page title (wizard)', 'golo-framework')); ?>" name="golo-page-title[my_profile]"/></td>
                                <td>
                                    <p><?php esc_html_e('This page allows users to view their own "My Profile" via the front-end.', 'golo-framework'); ?></p>
                                </td>
                                <td><code>[golo_my_profile]</code></td>
                            </tr>
                            <tr>
                                <td><input type="checkbox" checked="checked" name="golo-create-page[my_wishlist]"/></td>
                                <td><input type="text" value="<?php echo esc_attr(_x('My Wishlist', 'Default page title (wizard)', 'golo-framework')); ?>" name="golo-page-title[my_wishlist]"/></td>
                                <td>
                                    <p><?php esc_html_e('This page allows users to view their own "My Wishlist" via the front-end.', 'golo-framework'); ?></p>
                                </td>
                                <td><code>[golo_my_wishlist]</code></td>
                            </tr>
                            <tr>
                                <td><input type="checkbox" checked="checked" name="golo-create-page[my_booking]"/></td>
                                <td><input type="text" value="<?php echo esc_attr(_x('My Booking', 'Default page title (wizard)', 'golo-framework')); ?>" name="golo-page-title[my_booking]"/></td>
                                <td>
                                    <p><?php esc_html_e('This page allows users to view their own "My Booking" via the front-end.', 'golo-framework'); ?></p>
                                </td>
                                <td><code>[golo_my_booking]</code></td>
                            </tr>
                            <tr>
                                <td><input type="checkbox" checked="checked" name="golo-create-page[bookings]"/></td>
                                <td><input type="text" value="<?php echo esc_attr(_x('Bookings', 'Default page title (wizard)', 'golo-framework')); ?>" name="golo-page-title[bookings]"/></td>
                                <td>
                                    <p><?php esc_html_e('This page allows users to view their own "Bookings" via the front-end.', 'golo-framework'); ?></p>
                                </td>
                                <td><code>[golo_bookings]</code></td>
                            </tr>
                            <tr>
                                <td><input type="checkbox" checked="checked" name="golo-create-page[packages]"/></td>
                                <td><input type="text" value="<?php echo esc_attr(_x('Packages', 'Default page title (wizard)', 'golo-framework')); ?>" name="golo-page-title[packages]"/></td>
                                <td>
                                    <p><?php esc_html_e('This page allows users to view their own "Packages" via the front-end.', 'golo-framework'); ?></p>
                                </td>
                                <td><code>[golo_packages]</code></td>
                            </tr>
                            <tr>
                                <td><input type="checkbox" checked="checked" name="golo-create-page[payment]"/></td>
                                <td><input type="text" value="<?php echo esc_attr(_x('Payment', 'Default page title (wizard)', 'golo-framework')); ?>" name="golo-page-title[payment]"/></td>
                                <td>
                                    <p><?php esc_html_e('This page allows users to view their own "Payment" via the front-end.', 'golo-framework'); ?></p>
                                </td>
                                <td><code>[golo_payment]</code></td>
                            </tr>
                            <tr>
                                <td><input type="checkbox" checked="checked" name="golo-create-page[payment_completed]"/></td>
                                <td><input type="text" value="<?php echo esc_attr(_x('Payment Completed', 'Default page title (wizard)', 'golo-framework')); ?>" name="golo-page-title[payment_completed]"/></td>
                                <td>
                                    <p><?php esc_html_e('This page allows users to view their own "Payment Completed" via the front-end.', 'golo-framework'); ?></p>
                                </td>
                                <td><code>[golo_payment_completed]</code></td>
                            </tr>
                            <tr>
                                <td><input type="checkbox" checked="checked" name="golo-create-page[country]"/></td>
                                <td><input type="text" value="<?php echo esc_attr(_x('Country', 'Default page title (wizard)', 'golo-framework')); ?>" name="golo-page-title[country]"/></td>
                                <td>
                                    <p><?php esc_html_e('This page allows users to view their own "Single Country" via the front-end.', 'golo-framework'); ?></p>
                                </td>
                                <td><code>[golo_country]</code></td>
                            </tr>
                            </tbody>
                            <tfoot>
                            <tr>
                                <th colspan="4">
                                    <input type="submit" class="button button-primary" value="<?php esc_html_e('Create selected pages', 'golo-framework'); ?>"/>
                                    <a href="<?php echo esc_url(add_query_arg('step', 3)); ?>" class="button"><?php esc_html_e('Skip this step', 'golo-framework'); ?></a>
                                </th>
                            </tr>
                            </tfoot>
                        </table>
                    </form>

                <?php endif; ?>
                <?php if (3 === $step) : ?>

                    <h3><?php esc_html_e('All Done!', 'golo-framework'); ?></h3>

                    <p><?php esc_html_e('Looks like you\'re all set to start using the plugin. In case you\'re wondering where to go next:', 'golo-framework'); ?></p>

                    <ul class="golo-next-steps">
                        <li>
                            <a href="<?php echo admin_url('themes.php?page=golo-framework'); ?>"><?php esc_html_e('Plugin settings', 'golo-framework'); ?></a>
                        </li>
                        <li>
                            <a href="<?php echo admin_url('post-new.php?post_type=place'); ?>"><?php esc_html_e('Add a place the back-end', 'golo-framework'); ?></a>
                        </li>
                        <?php if ($permalink = golo_get_permalink('places')) : ?>
                            <li>
                                <a href="<?php echo esc_url($permalink); ?>"><?php esc_html_e('Show all places', 'golo-framework'); ?></a>
                            </li>
                        <?php endif; ?>
                        <?php if ($permalink = golo_get_permalink('submit_place')) : ?>
                            <li>
                                <a href="<?php echo esc_url($permalink); ?>"><?php esc_html_e('Add a place via the front-end', 'golo-framework'); ?></a>
                            </li>
                        <?php endif; ?>
                        <?php if ($permalink = golo_get_permalink('my_places')) : ?>
                            <li>
                                <a href="<?php echo esc_url($permalink); ?>"><?php esc_html_e('View user places', 'golo-framework'); ?></a>
                            </li>
                        <?php endif; ?>
                        <?php if ($permalink = golo_get_permalink('my_profile')) : ?>
                            <li>
                                <a href="<?php echo esc_url($permalink); ?>"><?php esc_html_e('View user profile', 'golo-framework'); ?></a>
                            </li>
                        <?php endif; ?>
                        <?php if ($permalink = golo_get_permalink('my_wishlist')) : ?>
                            <li>
                                <a href="<?php echo esc_url($permalink); ?>"><?php esc_html_e('View user wishlist', 'golo-framework'); ?></a>
                            </li>
                        <?php endif; ?>
                        <?php if ($permalink = golo_get_permalink('my_booking')) : ?>
                            <li>
                                <a href="<?php echo esc_url($permalink); ?>"><?php esc_html_e('View my booking', 'golo-framework'); ?></a>
                            </li>
                        <?php endif; ?>
                        <?php if ($permalink = golo_get_permalink('bookings')) : ?>
                            <li>
                                <a href="<?php echo esc_url($permalink); ?>"><?php esc_html_e('View user bookings', 'golo-framework'); ?></a>
                            </li>
                        <?php endif; ?>
                        <?php if ($permalink = golo_get_permalink('packages')) : ?>
                            <li>
                                <a href="<?php echo esc_url($permalink); ?>"><?php esc_html_e('View packages', 'golo-framework'); ?></a>
                            </li>
                        <?php endif; ?>
                        <?php if ($permalink = golo_get_permalink('payment')) : ?>
                            <li>
                                <a href="<?php echo esc_url($permalink); ?>"><?php esc_html_e('View payment', 'golo-framework'); ?></a>
                            </li>
                        <?php endif; ?>
                        <?php if ($permalink = golo_get_permalink('country')) : ?>
                            <li>
                                <a href="<?php echo esc_url($permalink); ?>"><?php esc_html_e('View country detail', 'golo-framework'); ?></a>
                            </li>
                        <?php endif; ?>
                    </ul>
                <?php endif; ?>
            </div>
            <?php
        }
    }
}