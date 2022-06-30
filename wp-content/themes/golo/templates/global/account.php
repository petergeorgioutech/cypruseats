<?php 
// We start a session to access
// the captcha externally!
session_start();
$captcha = rand(1000, 9999);
$_SESSION["golo_captcha"] = $captcha;

if ( !defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}
if (isset($_GET['action']) && $_GET['action'] == 'rp') {
	$class_open = 'open';
} else {
	$class_open = '';
}

$show_register = Golo_Helper::get_setting('show_register');
?>
<div class="popup popup-account <?php echo $class_open; ?>" id="popup-form">
	<div class="bg-overlay"></div>
	<div class="inner-popup">
		<a href="#" class="btn-close">
			<i class="la la-times large"></i>
		</a>
		<div class="head-popup">
			<div class="tabs-form">
				<a class="btn-login active" href="#ux-login"><?php esc_html_e('Log in', 'golo'); ?></a>
				<?php 
					if ($show_register) {
						?> <a class="btn-register" href="#ux-register"><?php esc_html_e('Sign Up', 'golo'); ?></a> <?php
					}
				?>
				<div class="loading-effect"><span class="golo-dual-ring"></span></div>
			</div>
			
			<?php 
			$enable_social_login = Golo_Helper::golo_get_option('enable_social_login', '1');
			if( class_exists('Golo_Framework') && $enable_social_login ) { 
			?>
				
			<div class="addon-login">
				<?php printf( esc_html__( 'Continue with %1$s or %2$s', 'golo' ), '<a class="facebook-login" href="#">' . esc_html__('Facebook', 'golo') . '</a>', '<a class="google-login" href="#">' . esc_html__('Google', 'golo') . '</a>' ); ?>
			</div>
			
			<p>
				<span><?php esc_html_e('Or', 'golo'); ?></span>
			</p>

			<?php } ?>
		</div>

		<div class="body-popup">

			<?php
				if (isset($_GET['action']) && $_GET['action'] == 'rp') :
			?>

			<div class="golo-new-password-wrap">
			    <form action="#" method="post">
			        <div class="form-group control-password">
			            <input name="new_password" type="text" id="new-password" class="form-control control-icon" placeholder="<?php esc_html_e('Enter new password', 'golo'); ?>">
			            <span><i class="fas fa-eye"></i></span>
			        </div>
			        <div class="button-wrap">
		            	<a href="#" class="generate-password"><?php esc_html_e('Generate Password', 'golo'); ?></a>
		            	<button type="submit" id="golo_newpass" class="btn gl-button"><?php esc_html_e('Save password', 'golo'); ?></button>
		            	<input type="hidden" name="login" id="login" value="<?php echo $_GET['login']; ?>">
				        <p class="msg"><?php esc_html_e('Sending info,please wait...', 'golo'); ?></p>
		            </div>
			    </form>
			</div>

			<?php else : ?>
			
				<form action="#" id="ux-login" class="form-account active" method="post">

					<div class="form-group">
						<label for="ip_email" class="label-field"><?php esc_html_e('Account or Email', 'golo'); ?></label>
						<input type="text" id="ip_email" class="form-control input-field" name="email">
					</div>
					<div class="form-group">
						<label for="ip_password" class="label-field"><?php esc_html_e('Password', 'golo'); ?></label>
						<input type="password" id="ip_password" class="form-control input-field" name="password">
					</div>

					<?php 
					$enable_captcha = Golo_Helper::golo_get_option('enable_captcha', '');
					if( $enable_captcha ) :
					?>
					<div class="form-group form-captcha">
						<input type="text" class="form-control golo-captcha" name="ip_captcha"/>
						<input type="hidden" class="form-control golo-num-captcha" name="ip_num_captcha"/>
						<?php Golo_Helper::golo_image_captcha($captcha); ?>
					</div>
					<?php endif; ?>

					<div class="form-group">
						<div class="forgot-password">
							<span><?php esc_html_e('Forgot your password? ', 'golo'); ?></span>
							<a class="btn-reset-password" href="#"><?php esc_html_e('Reset password.', 'golo'); ?></a>
						</div>
					</div>

					<p class="msg"><?php esc_html_e('Sending login info,please wait...', 'golo'); ?></p>

					<div class="form-group">
						<button type="submit" class="gl-button btn button" value="<?php esc_attr_e( 'Sign in', 'golo' ); ?>"><?php esc_attr_e( 'Sign in', 'golo' ); ?></button>
					</div>
				</form>

				<div class="golo-reset-password-wrap form-account">
				    <div id="golo_messages_reset_password" class="golo_messages message"></div>
				    <form method="post" enctype="multipart/form-data">
				        <div class="form-group control-username">
				            <input name="user_login" id="user_login" class="form-control control-icon" placeholder="<?php esc_html_e('Enter your username or email', 'golo'); ?>">
				            <?php wp_nonce_field('golo_reset_password_ajax_nonce', 'golo_security_reset_password'); ?>
				            <input type="hidden" name="action" id="reset_password_action" value="golo_reset_password_ajax">
				            <p class="msg"><?php esc_html_e('Sending info,please wait...', 'golo'); ?></p>
				            <button type="submit" id="golo_forgetpass" class="btn gl-button"><?php esc_html_e('Get new password', 'golo'); ?></button>
				        </div>
				    </form>
				    <a class="back-to-login" href="#"><i class="las la-arrow-left"></i><?php esc_html_e('Back to login', 'golo'); ?></a>
				</div>

				<form action="#" id="ux-register" class="form-account" method="post">
					<?php
						$enable_user_role = Golo_Helper::golo_get_option('enable_user_role', '1');
						if ($enable_user_role) {
					?>
					<div class="form-group">
						<div class="row">
							<div class="col-6">
								<div class="col-group">
									<label for="guest" class="label-field radio-field">
										<input type="radio" value="guest" id="guest" name="account_type">
										<span><i class="las la-user"></i><?php esc_html_e('Guest', 'golo'); ?></span>
									</label>
								</div>
							</div>
							<div class="col-6">
								<div class="col-group">
									<label for="owner" class="label-field radio-field">
										<input type="radio" value="owner" id="owner" name="account_type" checked>
										<span><i class="las la-briefcase"></i><?php esc_html_e('Owner', 'golo'); ?></span>
									</label>
								</div>
							</div>
						</div>
					</div>
					<?php } else { ?>
					<input type="hidden" value="owner" id="owner" name="account_type" checked>
					<?php } ?>
					<div class="form-group">
						<div class="row">
							<div class="col-6">
								<div class="col-group">
									<label for="ip_reg_firstname" class="label-field"><?php esc_html_e('First Name', 'golo'); ?></label>
									<input type="text" id="ip_reg_firstname" class="form-control input-field" name="reg_firstname">
								</div>
							</div>
							<div class="col-6">
								<div class="col-group">
									<label for="ip_reg_lastname" class="label-field"><?php esc_html_e('Last Name', 'golo'); ?></label>
									<input type="text" id="ip_reg_lastname" class="form-control input-field" name="reg_lastname">
								</div>
							</div>
						</div>
					</div>
					<div class="form-group">
						<label for="ip_reg_company_name" class="label-field"><?php esc_html_e('Company Name', 'golo'); ?></label>
						<input type="text" id="ip_reg_company_name" class="form-control input-field" name="reg_company_name">
					</div>
					<div class="form-group">
						<label for="ip_reg_email" class="label-field"><?php esc_html_e('Email', 'golo'); ?></label>
						<input type="email" id="ip_reg_email" class="form-control input-field" name="reg_email">
					</div>
					<div class="form-group">
						<label for="ip_reg_password" class="label-field"><?php esc_html_e('Password', 'golo'); ?></label>
						<input type="password" id="ip_reg_password" class="form-control input-field" name="reg_password">
					</div>
					
					<?php 
					$enable_captcha = Golo_Helper::golo_get_option('enable_captcha', '');
					if( $enable_captcha ) :
					?>
					<div class="form-group form-captcha">
						<input type="text" class="form-control golo-captcha" name="ip_captcha"/>
						<input type="hidden" class="form-control golo-num-captcha" name="ip_num_captcha"/>
						<?php Golo_Helper::golo_image_captcha($captcha); ?>
					</div>
					<?php endif; ?>

					<div class="form-group accept-account">
						<?php 
						$terms_login 	= Golo_Helper::golo_get_option('terms_login');
						$privacy_policy = Golo_Helper::golo_get_option('privacy_policy_login');
						?>
						<input type="checkbox" id="ip_accept_account" class="form-control custom-checkbox" name="accept_account">
						<label for="ip_accept_account"><?php printf( esc_html__( 'Accept the %1$s and %2$s', 'golo' ), '<a href="' . get_permalink($terms_login) . '">' . esc_html__('Terms', 'golo') . '</a>', '<a href="' . get_permalink($privacy_policy) . '">' . esc_html__('Privacy Policy', 'golo') . '</a>' ); ?></label>
					</div>

					<p class="msg"><?php esc_html_e('Sending register info,please wait...', 'golo'); ?></p>

					<div class="form-group">
						<button type="submit" class="gl-button btn button" value="<?php esc_attr_e( 'Sign in', 'golo' ); ?>"><?php esc_attr_e( 'Sign up', 'golo' ); ?></button>
					</div>
				</form>

			<?php endif; ?>
		</div>
	</div>
</div>