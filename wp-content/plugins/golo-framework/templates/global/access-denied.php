<?php 
if ( !defined('ABSPATH') ) {
    exit; // Exit if accessed directly
}

if( empty($type) )
{
    return;
}

$show_register = Golo_Helper::get_setting('show_register');

?>
<div class="access-denied not-login">
    <div class="container">
        <div class="golo-my-page">
            <div class="entry-my-page">
                <?php 
                switch ($type) {
                    case 'not_login':
                        ?>
                            <div class="account logged-out golo-message alert-success">
                                <div class="icon-message">
                                    <i class="la la-thumbs-up large"></i>
                                </div>

                                <div class="entry-message">
                                    <span><?php esc_html_e('You need login to continue.', 'golo-framework'); ?></span>
                                    <a href="#popup-form" class="btn-login"><?php esc_html_e('Login Here','golo-framework'); ?></a>
                                    <?php 
                                        if ($show_register) {
                                            ?>                                             
                                                <span><?php esc_html_e('or', 'golo-framework'); ?></span>
                                                <a href="#popup-form" class="btn-register"><?php esc_html_e('Sign Up Now','golo-framework'); ?></a>
                                            <?php
                                        }
                                    ?>
                                </div>
                            </div>
                        <?php
                        break;

                    case 'warning':
                        ?>
                            <div class="account logged-out golo-message alert-warning">
                                <div class="icon-message">
                                    <i class="la la-exclamation-circle large"></i>
                                </div>

                                <div class="entry-message">
                                    <p><?php esc_html_e('You are now a Premium Member.', 'golo-framework'); ?></p>
                                </div>
                            </div>
                        <?php
                        break;

                    case 'error':
                        ?>
                            <div class="account logged-out golo-message alert-error">
                                <div class="icon-message">
                                    <i class="la la-times-circle large"></i>
                                </div>

                                <div class="entry-message">
                                    <p><?php esc_html_e('An error occurred. Please try again.', 'golo-framework'); ?></p>
                                </div>
                            </div>
                        <?php
                        break;

                    case 'free_submit':
                        ?>
                            <div class="account logged-out golo-message alert-warning">
                                <div class="icon-message">
                                    <i class="la la-exclamation-circle large"></i>
                                </div>

                                <div class="entry-message">
                                    <p>
                                        <?php esc_html_e("You are on free submit active", 'golo-framework'); ?>
                                        <a href="<?php echo golo_get_permalink('submit_place'); ?>">
                                            <?php esc_html_e('Add place', 'golo-framework'); ?>       
                                        </a>
                                    </p>
                                </div>
                            </div>
                        <?php
                        break;
                    
                    default:
                        # code...
                        break;
                }
                ?>
                
            </div>
        </div>
    </div>
</div>