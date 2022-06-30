(
	function( $ ) {
		'use strict';

		var GoloModernMenuHandler = function( $scope, $ ) {
			$('.elementor-widget-golo-modern-menu ul.elementor-nav-menu>li.menu-item-has-children>a').append('<span class="sub-arrow"><i class="fa"></i></span>');
		};

		$( window ).on( 'elementor/frontend/init', function() {
			elementorFrontend.hooks.addAction( 'frontend/element_ready/golo-modern-menu.default', GoloModernMenuHandler );
		} );
	}
)( jQuery );
