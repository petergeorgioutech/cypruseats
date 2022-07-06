'use strict';
(function($) {

    disableSearchType();

    var $where  = $("#find_city"),
        $find   = $("#find_input");

    $where.click(function() {    
        //clear value
        $("input[name=ciid]").attr("value", "");

        $(".location-field .area-result").hide();
        $(".location-field .focus-result").show();
    });
    
    $find.click(function() {    
        //clear value
        $("input[name=ciid]").attr("value", "");

        if ( $(".input-field .search-result").is(':visible') ) {
            $("#find_input").removeAttr("readonly").css("cursor", "text");
        }
        else {
            $("#find_input").attr("readonly", "").css("cursor", "default");
        }
    });

    function disableSearchType() {
        $("#find_city").attr("readonly", "").css("cursor", "default");
        $("#find_input").attr("readonly", "").css("cursor", "default");
    }

})( jQuery );
