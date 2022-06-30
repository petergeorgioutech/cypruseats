(
	function( $ ) {
		'use strict';

		var SwiperHandler = function( $scope, $ ) {
			var $element = $scope.find( '.golo-slider-widget' );

			$element.GoloSwiper();
		};

		var SwiperLinkedHandler = function( $scope, $ ) {
			var $element = $scope.find( '.golo-slider-widget' );

			if ( $scope.hasClass( 'golo-swiper-linked-yes' ) ) {
				var thumbsSlider = $element.filter( '.golo-thumbs-swiper' ).GoloSwiper();
				var mainSlider = $element.filter( '.golo-main-swiper' ).GoloSwiper( {
					thumbs: {
						swiper: thumbsSlider
					}
				} );
			} else {
				$element.GoloSwiper();
			}
		};

		$( window ).on( 'elementor/frontend/init', function() {
			elementorFrontend.hooks.addAction( 'frontend/element_ready/golo-image-carousel.default', SwiperHandler );
			elementorFrontend.hooks.addAction( 'frontend/element_ready/golo-modern-carousel.default', SwiperHandler );
			elementorFrontend.hooks.addAction( 'frontend/element_ready/golo-modern-slider.default', SwiperHandler );
			elementorFrontend.hooks.addAction( 'frontend/element_ready/golo-team-member-carousel.default', SwiperHandler );
			elementorFrontend.hooks.addAction( 'frontend/element_ready/golo-product-carousel.default', SwiperHandler );

			elementorFrontend.hooks.addAction( 'frontend/element_ready/golo-testimonial.default', SwiperLinkedHandler );
		} );
	}
)( jQuery );
