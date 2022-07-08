'use strict';
(function($) {

    disableSearchType();

    var $whereWrapper       = $("#wrapper .location-search"),
        $findWrapper        = $("#wrapper .input-area"),
        $wherePopup         = $(".search-form-wrapper .location-search"),
        $findPopup          = $(".search-form-wrapper .input-area");

    $whereWrapper.click(function() {    
        //clear value
        $("#wrapper input[name=ciid]").attr("value", "");

        $("#wrapper .location-field .area-result").hide();
        $("#wrapper .location-field .focus-result").show();
    });
    
    $findWrapper.click(function() {    
        //clear value
        $("#wrapper input[name=ciid]").attr("value", "");

        if ( $("#wrapper .input-field .search-result").is(':visible') ) {
            $("#wrapper #find_input").removeAttr("readonly").css("cursor", "text");
        }
        else {
            $("#wrapper #find_input").attr("readonly", "").css("cursor", "default");
        }
    });
    
    $wherePopup.click(function() {    
        //clear value
        $(".search-form-wrapper input[name=ciid]").attr("value", "");

        $(".search-form-wrapper .location-field .area-result").hide();
        $(".search-form-wrapper .location-field .focus-result").show();
    });
    
    $findPopup.click(function() {    
        //clear value
        $(".search-form-wrapper input[name=ciid]").attr("value", "");

        if ( $(".search-form-wrapper .input-field .search-result").is(':visible') ) {
            $(".search-form-wrapper #find_input").removeAttr("readonly").css("cursor", "text");
        }
        else {
            $(".search-form-wrapper #find_input").attr("readonly", "").css("cursor", "default");
        }
    });

    function disableSearchType() {
        $("#wrapper #find_city").attr("readonly", "").css("cursor", "default");
        $("#wrapper #find_input").attr("readonly", "").css("cursor", "default");
        $(".search-form-wrapper #find_city").attr("readonly", "").css("cursor", "default");
        $(".search-form-wrapper #find_input").attr("readonly", "").css("cursor", "default");
    }

})( jQuery );
