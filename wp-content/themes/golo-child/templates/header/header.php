<div class="container-fluid">
	<div class="row">
		<div class="col-5">
			<div class="left-header">
				<?php echo Golo_Templates::canvas_menu(); ?>
				<?php echo Golo_Templates::site_logo('dark'); ?>
				
				<div class="d-none d-xl-block">
					<?php echo Golo_Templates::block_search('input', true); ?>
				</div>
			</div>
		</div>

		<div class="col-7">
			<div class="right-header">
				<div class="d-none d-xl-block">
					<?php echo Golo_Templates::main_menu(); ?>
				</div>

				<div class="d-none d-xl-block">
					<?php echo Golo_Templates::dropdown_categories('place-city', __('Destinations', 'golo')); ?>
				</div>
				<div class="d-none d-xl-block">
					<?php echo Golo_Templates::account(); ?>
				</div>

				<div class="d-xl-none calendar-icon-wrapper">
					<a href="/event" class="calendar-icon"><img src="<?php echo get_bloginfo( 'stylesheet_directory' ); ?>/assets/img/calendar.png" alt="Calendar"></a>
				</div>
				
				<div class="d-xl-none">
					<?php echo Golo_Templates::block_search('icon', true); ?>
				</div>

				<?php echo Golo_Templates::wc_cart(); ?>
				
				<div class="d-none d-xl-block">
					<?php echo Golo_Templates::add_place(); ?>
				</div>
			</div>
		</div>
	</div>
</div><!-- .container-fluid -->