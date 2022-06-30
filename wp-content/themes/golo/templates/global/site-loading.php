<?php 
if ( !defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

$type_loading_effect      = Golo_Helper::get_setting('type_loading_effect');
$animation_loading_effect = Golo_Helper::get_setting('animation_loading_effect');
$image_loading_effect     = Golo_Helper::get_setting('image_loading_effect');

$args = array('css-1'  => '<span class="golo-ldef-circle golo-ldef-loading"><span></span></span>','css-2'  => '<span class="golo-ldef-dual-ring golo-ldef-loading"></span>','css-3'=> '<span class="golo-ldef-facebook golo-ldef-loading"><span></span><span></span><span></span></span>','css-4'  => '<span class="golo-ldef-heart golo-ldef-loading"><span></span></span>','css-5'  => '<span class="golo-ldef-ring golo-ldef-loading"><span></span><span></span><span></span><span></span></span>','css-6'  => '<span class="golo-ldef-roller golo-ldef-loading"><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span></span>','css-7'  => '<span class="golo-ldef-default golo-ldef-loading"><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span></span>','css-8'  => '<span class="golo-ldef-ellipsis golo-ldef-loading"><span></span><span></span><span></span><span></span></span>','css-9'  => '<span class="golo-ldef-grid golo-ldef-loading"><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span></span>','css-10'  => '<span class="golo-ldef-hourglass golo-ldef-loading"></span>','css-11'  => '<span class="golo-ldef-ripple golo-ldef-loading"><span></span><span></span></span>','css-12'  => '<span class="golo-ldef-spinner golo-ldef-loading"><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span></span>');

?>

<?php if( $type_loading_effect !== 'none' ){ ?>

<div class="page-loading-effect">
	<div class="bg-overlay"></div>

	<div class="entry-loading">
		<?php if( $type_loading_effect == 'css_animation' ) { ?>
			<?php echo wp_kses( $args[ $animation_loading_effect ], Golo_Helper::golo_kses_allowed_html() ); ?>
		<?php } ?>

		<?php if( $type_loading_effect == 'image' ) { ?>
			<img src="<?php echo esc_url( $image_loading_effect ); ?>" alt="<?php esc_attr_e('Image Effect','golo'); ?>">
		<?php } ?>
	</div>
</div>
		
<?php } ?>