(function($) {
	"use strict";

	var ajax_url 	  = golo_my_profile_vars.ajax_url,
	 	golo_site_url = golo_my_profile_vars.site_url;

	$(document).ready( function() {

		$('.form-profile').validate({
            ignore: ":hidden", // any children of hidden desc are ignored
            errorElement: "span", // wrap error elements in span not label
            rules: {
                user_firstname: {
                    required: true
                },
                user_lastname: {
                    required: true
                },
                user_email: {
                    required: true
                },
                user_mobile_number: {
                    required: true
                }
            },
            messages: {
                user_firstname: "This field is required",
                user_lastname: "This field is required",
                user_email: "This field is required",
                user_mobile_number: "This field is required"
            },
            invalidHandler: function(form, validator) {
                
                if (!validator.numberOfInvalids())
                    return;
                                
                $('html, body').animate({
                    scrollTop: $(validator.errorList[0].element).parent().offset().top
                }, 500);
            }    
        });

        $("#golo_update_profile").on('click', function () {
			var $this        = $(this);
			var $form        = $this.parents('form');
			var $alert_title = $this.text();
            if ($form.valid()) {
                $.ajax({
                    type: 'POST',
                    url: ajax_url,
                    dataType: 'json',
                    data: {
                        'action': 'golo_update_profile_ajax',
                        'user_firstname': $("#user_firstname").val(),
                        'user_lastname': $("#user_lastname").val(),
                        'user_des': $("#user_des").val(),
                        'user_email': $("#user_email").val(),
                        'author_mobile_number': $("#author_mobile_number").val(),
                        'author_fax_number': $("#author_fax_number").val(),
                        'user_facebook_url': $("#user_facebook_url").val(),
                        'user_twitter_url': $("#user_twitter_url").val(),
                        'user_linkedin_url': $("#user_linkedin_url").val(),
                        'user_pinterest_url': $("#user_pinterest_url").val(),
                        'user_instagram_url': $("#user_instagram_url").val(),
                        'user_skype': $("#user_skype").val(),
                        'user_description': $("#user_description").val(),
                        'user_youtube_url': $("#user_youtube_url").val(),
                        'user_image_url': $("#author_avatar_image_url").val(),
                        'user_image_id': $("#author_avatar_image_id").val(),
                        'golo_security_update_profile': $('#golo_security_update_profile').val()
                    },
                    beforeSend: function () {
                        $this.find('.btn-loading').fadeIn();
                    },
                    success: function (response) {
                        $this.find('.btn-loading').fadeOut();
                        if (response.success) {
                            location.reload();
                        }
                    },
                    error: function () {
                        $this.find('.btn-loading').fadeOut();
                    }
                });
            }
        });
        /*-------------------------------------------------------------------
         *  Change Password
         * ------------------------------------------------------------------*/
        $('.form-change-password').validate({
            errorElement: "span", // wrap error elements in span not label
            rules: {
                oldpass: {
                    required: true
                },
                newpass: {
                    required: true,
                    minlength: 4
                },
                confirmpass: {
                    required: true
                }
            },
            messages: {
                oldpass: "",
                newpass: "",
                confirmpass: ""
            }
        });

        $("#golo_change_pass").on('click', function () {
            var securitypassword, oldpass, newpass, confirmpass;

			var $this        = $(this);
			var $form        = $this.parents('form');
			var $alert_title = $this.text();

			oldpass          = $("#oldpass").val();
			newpass          = $("#newpass").val();
			confirmpass      = $("#confirmpass").val();
			securitypassword = $("#golo_security_change_password").val();
			
            if ($form.valid()) {
                $.ajax({
                    type: 'POST',
                    dataType: 'json',
                    url: ajax_url,
                    data: {
                        'action': 'golo_change_password_ajax',
                        'oldpass': oldpass,
                        'newpass': newpass,
                        'confirmpass': confirmpass,
                        'golo_security_change_password': securitypassword
                    },
                    beforeSend: function () {
                        $this.find('.btn-loading').fadeIn();
                    },
                    success: function (response) {
                    	$this.find('.btn-loading').fadeOut();
                        if (response.success) {
                            window.location.href = golo_site_url;
                        }
                    },
                    error: function () {
                        $this.find('.btn-loading').fadeOut();
                    }
                });
            }
        });
	});
})(jQuery);