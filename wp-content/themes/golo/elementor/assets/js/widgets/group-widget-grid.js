(
	function( $ ) {
		'use strict';

		var GoloGridHandler = function( $scope, $ ) {
			var $element = $scope.find( '.golo-grid-wrapper' );

			$element.GoloGridLayout();
		};

		$( window ).on( 'elementor/frontend/init', function() {
			elementorFrontend.hooks.addAction( 'frontend/element_ready/golo-image-gallery.default', GoloGridHandler );
			elementorFrontend.hooks.addAction( 'frontend/element_ready/golo-testimonial-grid.default', GoloGridHandler );
			elementorFrontend.hooks.addAction( 'frontend/element_ready/golo-product-categories.default', GoloGridHandler );
		} );
	}
)( jQuery );
