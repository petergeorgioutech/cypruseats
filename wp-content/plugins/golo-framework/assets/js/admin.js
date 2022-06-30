(function ($) {
    "use strict";

    var $body = $('body');

    var ajax_url = golo_admin_vars.ajax_url;

    $('body').on('click', '.open-popup', function(event) {
        event.preventDefault();
        var id = $(this).attr('href');
        $(id).addClass('active');
        $( '.site-header' ).addClass( 'show-popup' );
    });

    $('body').on('click', '.btn-close,.bg-overlay', function() {
        $(this).parents('.golo-popup').removeClass('active');
        $( '.site-header' ).removeClass( 'show-popup' );
        return false;
    });

    $('body').on( 'click', '.install-data', function(event) {
    	event.preventDefault();
		ajaxCall();
	});

    $('.claim-wrap').on( 'click', 'input.button', function(event) {
        event.preventDefault();
        var _this = $( this );
        var status = $( this ).data( 'status' );
        var place_id = $( this ).data( 'place_id' );
        var tr = $( this ).parents( 'tr' );
        $.ajax({
            type: 'POST',
            url: ajax_url,
            dataType: 'json',
            data: {
                'action': 'golo_action_claim_listing',
                'status': status,
                'place_id': place_id
            },
            beforeSend: function () {
                $( '.claim-wrap' ).find('.golo-loading-effect').fadeIn();
            },
            success: function (data) {
                $( '.claim-wrap' ).find('.golo-loading-effect').fadeOut();
                if( data.success === true ) {
                    if (data.stt == 'delete') {
                        tr.remove();
                        location.reload();
                    }
                    _this.parents( 'tr' ).find( 'td.status' ).attr( 'data-status', data.stt );
                    _this.parents( 'tr' ).find( 'td.status' ).text( data.stt );
                }
            },
        });
    });

    $("#checkall").on( 'click', function () {
        $('input[name="claim_item"]:checkbox').not(this).prop('checked', this.checked);
    });

    $(".claim-action .button-delete").on( 'click', function (event) {
        event.preventDefault();
        $( '.claim-wrap table tbody tr' ).each( function() {
            var checkbox = $( this ).find('input[name="claim_item"]');
            var btn_delete = $( this ).find('input.button-delete');
            if (checkbox.is(':checked')) {
                btn_delete.trigger( 'click' );
            }
        });
    });

    $('.golo-countries-settings .remove-all').on('click', function(event) {
        event.preventDefault();
        $(this).parents('.golo-countries-settings').find("input[type='checkbox']").prop("checked", false);
    });

    function purchase_form() {
        $('.hidden-code input').prop('disabled', true);

        $('.purchase-form.verified .purchase-icon').on('click', function() {
            if( $(this).closest('.purchase-form').hasClass('hidden-code') ) {
                $(this).closest('.purchase-form').removeClass('hidden-code');
                $('.purchase-form input').prop('disabled', false);
            }else{
                $(this).closest('.purchase-form').addClass('hidden-code');
                $('.purchase-form input').prop('disabled', true);
            }
            
        });
    }
	
	function timeout_trigger() {
	   $(".progress").css("max-width",p+"%");
	   $(".progress-view").text(p+"%");
	   if(p!=100) {
	       setTimeout('timeout_trigger()', 50);
	   }
	   p++;
	}

    function toggle() {
        const CURRENT_SECTION = 'glf_theme_options_current_section';
        var _current_page = $('#_current_page').val(),
            currentSection = localStorage.getItem(CURRENT_SECTION + '_' + _current_page);
        if (currentSection === null) {
            var sectionActive = $('.glf-tab li:first').data('id');
            currentSection = sectionActive;
            if (typeof (sectionActive) != 'undefined') {
                localStorage.setItem(CURRENT_SECTION + '_' + _current_page, sectionActive);
            }
            else {
                /**
                 * Off reset section if not exist section
                 */
                $('.glf-theme-options-reset-section').remove();
            }
        }
        $('.glf-tab li').removeClass('active');
        $('.glf-fields-wrapper > .glf-section-container').hide();
        $('.glf-tab li[data-id="' + currentSection + '"]').addClass('active');
        $('.glf-fields-wrapper > .glf-section-container[id="' + currentSection + '"]').show();

        /**
         * Store currentSection when section clicked
         */
        $('.glf-tab li').on('click', function () {
            localStorage.setItem(CURRENT_SECTION + '_' + _current_page, $(this).data('id'));
        });
    }

    function save_options() {
        $('.glf-theme-options-save-options').on('click', function() {
            window.onbeforeunload = null;
        });
    }

    function changeWidthContent() {
        var $tab = $('.area-theme-options .glf-tab');
        if ($tab.length > 0) {
            var $wrap = $('.area-theme-options .glf-meta-box-wrap'),
                $fields = $('.area-theme-options .glf-fields'),
                tabWidth = $tab.outerWidth(),
                wrapWidth = $wrap.width();
            $fields.css({
                'float': 'left',
                'width': (wrapWidth - tabWidth) + 'px',
                'overflow': 'visible'
            });
        }
    }

	/**
	 * The main AJAX call, which executes the import process.
	 *
	 * @param FormData data The data to be passed to the AJAX call.
	 */
	function ajaxCall(data) {
		$.ajax({
           	dataType: 'html',
            url: ajax_url,
            data: {
                'action': 'golo_import_demo_data',
            },
            beforeSend: function () {
            	$('.popup-import .progress-bar-container').removeClass('done');
                $('.popup-import .progress-bar-container').addClass('active');
            },
            success: function (response) {
            	$('.popup-import .progress-bar-container').addClass('done');
            	$('.popup-import .progress-bar-container').removeClass('active');
            	$('.result-import').html(response);
            },
        });
    }

    function plugin_action() {
        $( '.golo-plugin-action' ).on( 'click', function(e) {
            e.preventDefault();

            var $el = $( e.currentTarget ),
                $pluginsTable = $( '.golo-box--plugins table' ),
                $pluginRow = $el.closest( '.golo-plugin--required' ),
                pluginAction = $el.attr( 'data-plugin-action' ),
                $icon = $pluginRow.find( 'i, .svg-inline--fa' ),
                ajaxData = {
                    'action': 'process_plugin_actions',
                    'slug': $el.attr( 'data-slug' ),
                    'source': $el.attr( 'data-source' ),
                    'plugin_action': $el.attr( 'data-plugin-action' ),
                    '_wpnonce': $el.attr( 'data-nonce' )
                };

            if ( 'deactivate-plugin' === pluginAction ) {
                $el.html( '<i class="las la-circle-notch la-spin"></i>Deactivating' );
            }

            if ( 'activate-plugin' === pluginAction ) {
                $el.html( '<i class="las la-circle-notch la-spin"></i>Activating' );
            }

            $.ajax({
                type: 'POST',
                url: ajax_url,
                data: ajaxData,
                timeout: 20000
            }).done( ( response ) => {
                console.log(response);
                if ( response.success ) {
                    if ( 'deactivate-plugin' === pluginAction ) {
                        $pluginRow.removeClass( 'golo-plugin--activated' ).addClass( 'golo-plugin--deactivated' );
                        $el.text( 'Activate' )
                            .attr( 'data-plugin-action', 'activate-plugin' )
                            .attr( 'data-nonce', response.data )
                            .removeClass( 'plugin-deactivate' )
                            .addClass( 'plugin-activate' );
                        $icon.addClass( 'fa-times' ).removeClass( 'fa-check' );
                    }

                    if ( 'activate-plugin' === pluginAction ) {
                        $pluginRow.removeClass( 'golo-plugin--deactivated' ).addClass( 'golo-plugin--activated' );
                        $el.text( 'Deactivate' )
                            .attr( 'data-plugin-action', 'deactivate-plugin' )
                            .attr( 'data-nonce', response.data )
                            .removeClass( 'plugin-activate' )
                            .addClass( 'plugin-deactivate' );
                        $icon.addClass( 'fa-check' ).removeClass( 'fa-times' );
                    }

                    var requiredPluginCount = $pluginsTable.find( '.golo-plugin--required.golo-plugin--deactivated' ).length,
                        $pluginCount = $( '.golo-box--plugins .golo-box__footer span' );

                    if ( requiredPluginCount ) {
                        $pluginCount.css( 'color', '#dc433f' ).text( 'Please install and activate all required plugins (' + requiredPluginCount + ')' );
                    } else {
                        $pluginCount.css( 'color', '#6fbcae' ).text( 'All required plugins are activated. Now you can import the demo data.' );
                    }
                } else {
                    $el.text( 'Error' );
                }
            });
        });
    }

    function additionalFieldProcess() {
        var $wrapAdditionalField = $('#additional_fields');
        additionalFieldReadOnly();
        additionalFieldEventProcess($wrapAdditionalField);
        $wrapAdditionalField.on('glf_add_clone_field', function (event) {
            var $target = $(event.target);
            additionalFieldEventProcess($target);

            $target.find('.glf-field-panel-content,.glf-clone-field-panel-inner')
                .find('input').removeAttr('readonly');

            $target.find('.glf-field-panel-content,.glf-clone-field-panel-inner')
                .find('#additional_fields_label input,#additional_fields_id input,#additional_fields_select_choices textarea')
                .val('');

            $target.find('.glf-field-panel-content,.glf-clone-field-panel-inner')
                .find('#additional_fields_field_type select').val('text');
        });

        $(document).on('glf_save_option_success', function () {
            additionalFieldReadOnly();
        });
    }

    function additionalFieldReadOnly() {
        $('.glf-field-panel-content,.glf-clone-field-panel-inner').each(function () {
            var $label = $(this).find('#additional_fields_label input'),
                $id = $label.closest('.glf-field-panel-content,.glf-clone-field-panel-inner').find('#additional_fields_id input');
            if ($id.val() !== '') {
                $id.attr('readonly', 'readonly');
            }
        });

    }

    function additionalFieldEventProcess($wrap) {
        $wrap.find('.glf-field-panel-content,.glf-clone-field-panel-inner').find('#additional_fields_label input').on('change', function () {
            var $label = $(this),
                $id = $label.closest('.glf-field-panel-content,.glf-clone-field-panel-inner').find('#additional_fields_id input');
            if ($id.attr('readonly') !== 'readonly') {
                $id.val(toSlug($label.val()));
            }
        });
    }

    function additionalFeaturesProcess() {
        var $wrap = $('#golo_additional_features');

        $wrap.find('button').on('click', function () {
            var count = $wrap.find('tbody tr').length;
            var html = '<tr>\n' +
                '    <td class="sort">\n' +
                '        <span><i class="dashicons dashicons-menu"></i></span>\n' +
                '    </td>\n' +
                '    <td class="title">\n' +
                '        <input type="text" name="golo_additional_feature_title[' + count +  ']" value="">\n' +
                '    </td>\n' +
                '    <td class="value">\n' +
                '        <input type="text" name="golo_additional_feature_value[' + count + ']" value="">\n' +
                '    </td>\n' +
                '    <td class="remove"><i class="dashicons dashicons-dismiss"></i></td>\n' +
                '</tr>';

            $wrap.find('tbody').append(html);
            $wrap.find('.total').val(count + 1);
        });

        $wrap.find('tbody').sortable({
            'items': 'tr',
            handle: '.sort > span',
            update: function( event, ui ) {
                reindexAdditionalFeatures($wrap);
            },
            stop: function (event, ui) {}
        });

        $wrap.on('click', '.remove > i', function () {
            $(this).closest('tr').remove();
            $wrap.find('.total').val($wrap.find('tbody tr').length);
            reindexAdditionalFeatures($wrap);
        });
    }

    function reindexAdditionalFeatures($wrap) {
        $wrap.find(' tbody > tr').each(function (index) {
            $(this).find('input').each(function () {
                var name = $(this).attr('name');
                name = name.replace( /^(\w+\[)(\d+)(\].*)$/g , function(m,p1,p2,p3){ return p1+index+p3; });
                $(this).attr('name', name);
            });
        });
    }

    function toSlug(str) {
        str = String(str).toString();
        str = str.replace(/^\s+|\s+$/g, "");
        str = str.toLowerCase();

        var swaps = {
            '0': ['°', '₀', '۰', '０'],
            '1': ['¹', '₁', '۱', '１'],
            '2': ['²', '₂', '۲', '２'],
            '3': ['³', '₃', '۳', '３'],
            '4': ['⁴', '₄', '۴', '٤', '４'],
            '5': ['⁵', '₅', '۵', '٥', '５'],
            '6': ['⁶', '₆', '۶', '٦', '６'],
            '7': ['⁷', '₇', '۷', '７'],
            '8': ['⁸', '₈', '۸', '８'],
            '9': ['⁹', '₉', '۹', '９'],
            'a': ['à', 'á', 'ả', 'ã', 'ạ', 'ă', 'ắ', 'ằ', 'ẳ', 'ẵ', 'ặ', 'â', 'ấ', 'ầ', 'ẩ', 'ẫ', 'ậ', 'ā', 'ą', 'å', 'α', 'ά', 'ἀ', 'ἁ', 'ἂ', 'ἃ', 'ἄ', 'ἅ', 'ἆ', 'ἇ', 'ᾀ', 'ᾁ', 'ᾂ', 'ᾃ', 'ᾄ', 'ᾅ', 'ᾆ', 'ᾇ', 'ὰ', 'ά', 'ᾰ', 'ᾱ', 'ᾲ', 'ᾳ', 'ᾴ', 'ᾶ', 'ᾷ', 'а', 'أ', 'အ', 'ာ', 'ါ', 'ǻ', 'ǎ', 'ª', 'ა', 'अ', 'ا', 'ａ', 'ä'],
            'b': ['б', 'β', 'ب', 'ဗ', 'ბ', 'ｂ'],
            'c': ['ç', 'ć', 'č', 'ĉ', 'ċ', 'ｃ'],
            'd': ['ď', 'ð', 'đ', 'ƌ', 'ȡ', 'ɖ', 'ɗ', 'ᵭ', 'ᶁ', 'ᶑ', 'д', 'δ', 'د', 'ض', 'ဍ', 'ဒ', 'დ', 'ｄ'],
            'e': ['é', 'è', 'ẻ', 'ẽ', 'ẹ', 'ê', 'ế', 'ề', 'ể', 'ễ', 'ệ', 'ë', 'ē', 'ę', 'ě', 'ĕ', 'ė', 'ε', 'έ', 'ἐ', 'ἑ', 'ἒ', 'ἓ', 'ἔ', 'ἕ', 'ὲ', 'έ', 'е', 'ё', 'э', 'є', 'ə', 'ဧ', 'ေ', 'ဲ', 'ე', 'ए', 'إ', 'ئ', 'ｅ'],
            'f': ['ф', 'φ', 'ف', 'ƒ', 'ფ', 'ｆ'],
            'g': ['ĝ', 'ğ', 'ġ', 'ģ', 'г', 'ґ', 'γ', 'ဂ', 'გ', 'گ', 'ｇ'],
            'h': ['ĥ', 'ħ', 'η', 'ή', 'ح', 'ه', 'ဟ', 'ှ', 'ჰ', 'ｈ'],
            'i': ['í', 'ì', 'ỉ', 'ĩ', 'ị', 'î', 'ï', 'ī', 'ĭ', 'į', 'ı', 'ι', 'ί', 'ϊ', 'ΐ', 'ἰ', 'ἱ', 'ἲ', 'ἳ', 'ἴ', 'ἵ', 'ἶ', 'ἷ', 'ὶ', 'ί', 'ῐ', 'ῑ', 'ῒ', 'ΐ', 'ῖ', 'ῗ', 'і', 'ї', 'и', 'ဣ', 'ိ', 'ီ', 'ည်', 'ǐ', 'ი', 'इ', 'ی', 'ｉ'],
            'j': ['ĵ', 'ј', 'Ј', 'ჯ', 'ج', 'ｊ'],
            'k': ['ķ', 'ĸ', 'к', 'κ', 'Ķ', 'ق', 'ك', 'က', 'კ', 'ქ', 'ک', 'ｋ'],
            'l': ['ł', 'ľ', 'ĺ', 'ļ', 'ŀ', 'л', 'λ', 'ل', 'လ', 'ლ', 'ｌ'],
            'm': ['м', 'μ', 'م', 'မ', 'მ', 'ｍ'],
            'n': ['ñ', 'ń', 'ň', 'ņ', 'ŉ', 'ŋ', 'ν', 'н', 'ن', 'န', 'ნ', 'ｎ'],
            'o': ['ó', 'ò', 'ỏ', 'õ', 'ọ', 'ô', 'ố', 'ồ', 'ổ', 'ỗ', 'ộ', 'ơ', 'ớ', 'ờ', 'ở', 'ỡ', 'ợ', 'ø', 'ō', 'ő', 'ŏ', 'ο', 'ὀ', 'ὁ', 'ὂ', 'ὃ', 'ὄ', 'ὅ', 'ὸ', 'ό', 'о', 'و', 'θ', 'ို', 'ǒ', 'ǿ', 'º', 'ო', 'ओ', 'ｏ', 'ö'],
            'p': ['п', 'π', 'ပ', 'პ', 'پ', 'ｐ'],
            'q': ['ყ', 'ｑ'],
            'r': ['ŕ', 'ř', 'ŗ', 'р', 'ρ', 'ر', 'რ', 'ｒ'],
            's': ['ś', 'š', 'ş', 'с', 'σ', 'ș', 'ς', 'س', 'ص', 'စ', 'ſ', 'ს', 'ｓ'],
            't': ['ť', 'ţ', 'т', 'τ', 'ț', 'ت', 'ط', 'ဋ', 'တ', 'ŧ', 'თ', 'ტ', 'ｔ'],
            'u': ['ú', 'ù', 'ủ', 'ũ', 'ụ', 'ư', 'ứ', 'ừ', 'ử', 'ữ', 'ự', 'û', 'ū', 'ů', 'ű', 'ŭ', 'ų', 'µ', 'у', 'ဉ', 'ု', 'ူ', 'ǔ', 'ǖ', 'ǘ', 'ǚ', 'ǜ', 'უ', 'उ', 'ｕ', 'ў', 'ü'],
            'v': ['в', 'ვ', 'ϐ', 'ｖ'],
            'w': ['ŵ', 'ω', 'ώ', 'ဝ', 'ွ', 'ｗ'],
            'x': ['χ', 'ξ', 'ｘ'],
            'y': ['ý', 'ỳ', 'ỷ', 'ỹ', 'ỵ', 'ÿ', 'ŷ', 'й', 'ы', 'υ', 'ϋ', 'ύ', 'ΰ', 'ي', 'ယ', 'ｙ'],
            'z': ['ź', 'ž', 'ż', 'з', 'ζ', 'ز', 'ဇ', 'ზ', 'ｚ'],
            'aa': ['ع', 'आ', 'آ'],
            'ae': ['æ', 'ǽ'],
            'ai': ['ऐ'],
            'ch': ['ч', 'ჩ', 'ჭ', 'چ'],
            'dj': ['ђ', 'đ'],
            'dz': ['џ', 'ძ'],
            'ei': ['ऍ'],
            'gh': ['غ', 'ღ'],
            'ii': ['ई'],
            'ij': ['ĳ'],
            'kh': ['х', 'خ', 'ხ'],
            'lj': ['љ'],
            'nj': ['њ'],
            'oe': ['ö', 'œ', 'ؤ'],
            'oi': ['ऑ'],
            'oii': ['ऒ'],
            'ps': ['ψ'],
            'sh': ['ш', 'შ', 'ش'],
            'shch': ['щ'],
            'ss': ['ß'],
            'sx': ['ŝ'],
            'th': ['þ', 'ϑ', 'ث', 'ذ', 'ظ'],
            'ts': ['ц', 'ც', 'წ'],
            'ue': ['ü'],
            'uu': ['ऊ'],
            'ya': ['я'],
            'yu': ['ю'],
            'zh': ['ж', 'ჟ', 'ژ'],
            '(c)': ['©'],
            'A': ['Á', 'À', 'Ả', 'Ã', 'Ạ', 'Ă', 'Ắ', 'Ằ', 'Ẳ', 'Ẵ', 'Ặ', 'Â', 'Ấ', 'Ầ', 'Ẩ', 'Ẫ', 'Ậ', 'Å', 'Ā', 'Ą', 'Α', 'Ά', 'Ἀ', 'Ἁ', 'Ἂ', 'Ἃ', 'Ἄ', 'Ἅ', 'Ἆ', 'Ἇ', 'ᾈ', 'ᾉ', 'ᾊ', 'ᾋ', 'ᾌ', 'ᾍ', 'ᾎ', 'ᾏ', 'Ᾰ', 'Ᾱ', 'Ὰ', 'Ά', 'ᾼ', 'А', 'Ǻ', 'Ǎ', 'Ａ', 'Ä'],
            'B': ['Б', 'Β', 'ब', 'Ｂ'],
            'C': ['Ç', 'Ć', 'Č', 'Ĉ', 'Ċ', 'Ｃ'],
            'D': ['Ď', 'Ð', 'Đ', 'Ɖ', 'Ɗ', 'Ƌ', 'ᴅ', 'ᴆ', 'Д', 'Δ', 'Ｄ'],
            'E': ['É', 'È', 'Ẻ', 'Ẽ', 'Ẹ', 'Ê', 'Ế', 'Ề', 'Ể', 'Ễ', 'Ệ', 'Ë', 'Ē', 'Ę', 'Ě', 'Ĕ', 'Ė', 'Ε', 'Έ', 'Ἐ', 'Ἑ', 'Ἒ', 'Ἓ', 'Ἔ', 'Ἕ', 'Έ', 'Ὲ', 'Е', 'Ё', 'Э', 'Є', 'Ə', 'Ｅ'],
            'F': ['Ф', 'Φ', 'Ｆ'],
            'G': ['Ğ', 'Ġ', 'Ģ', 'Г', 'Ґ', 'Γ', 'Ｇ'],
            'H': ['Η', 'Ή', 'Ħ', 'Ｈ'],
            'I': ['Í', 'Ì', 'Ỉ', 'Ĩ', 'Ị', 'Î', 'Ï', 'Ī', 'Ĭ', 'Į', 'İ', 'Ι', 'Ί', 'Ϊ', 'Ἰ', 'Ἱ', 'Ἳ', 'Ἴ', 'Ἵ', 'Ἶ', 'Ἷ', 'Ῐ', 'Ῑ', 'Ὶ', 'Ί', 'И', 'І', 'Ї', 'Ǐ', 'ϒ', 'Ｉ'],
            'J': ['Ｊ'],
            'K': ['К', 'Κ', 'Ｋ'],
            'L': ['Ĺ', 'Ł', 'Л', 'Λ', 'Ļ', 'Ľ', 'Ŀ', 'ल', 'Ｌ'],
            'M': ['М', 'Μ', 'Ｍ'],
            'N': ['Ń', 'Ñ', 'Ň', 'Ņ', 'Ŋ', 'Н', 'Ν', 'Ｎ'],
            'O': ['Ó', 'Ò', 'Ỏ', 'Õ', 'Ọ', 'Ô', 'Ố', 'Ồ', 'Ổ', 'Ỗ', 'Ộ', 'Ơ', 'Ớ', 'Ờ', 'Ở', 'Ỡ', 'Ợ', 'Ø', 'Ō', 'Ő', 'Ŏ', 'Ο', 'Ό', 'Ὀ', 'Ὁ', 'Ὂ', 'Ὃ', 'Ὄ', 'Ὅ', 'Ὸ', 'Ό', 'О', 'Θ', 'Ө', 'Ǒ', 'Ǿ', 'Ｏ', 'Ö'],
            'P': ['П', 'Π', 'Ｐ'],
            'Q': ['Ｑ'],
            'R': ['Ř', 'Ŕ', 'Р', 'Ρ', 'Ŗ', 'Ｒ'],
            'S': ['Ş', 'Ŝ', 'Ș', 'Š', 'Ś', 'С', 'Σ', 'Ｓ'],
            'T': ['Ť', 'Ţ', 'Ŧ', 'Ț', 'Т', 'Τ', 'Ｔ'],
            'U': ['Ú', 'Ù', 'Ủ', 'Ũ', 'Ụ', 'Ư', 'Ứ', 'Ừ', 'Ử', 'Ữ', 'Ự', 'Û', 'Ū', 'Ů', 'Ű', 'Ŭ', 'Ų', 'У', 'Ǔ', 'Ǖ', 'Ǘ', 'Ǚ', 'Ǜ', 'Ｕ', 'Ў', 'Ü'],
            'V': ['В', 'Ｖ'],
            'W': ['Ω', 'Ώ', 'Ŵ', 'Ｗ'],
            'X': ['Χ', 'Ξ', 'Ｘ'],
            'Y': ['Ý', 'Ỳ', 'Ỷ', 'Ỹ', 'Ỵ', 'Ÿ', 'Ῠ', 'Ῡ', 'Ὺ', 'Ύ', 'Ы', 'Й', 'Υ', 'Ϋ', 'Ŷ', 'Ｙ'],
            'Z': ['Ź', 'Ž', 'Ż', 'З', 'Ζ', 'Ｚ'],
            'AE': ['Æ', 'Ǽ'],
            'Ch': ['Ч'],
            'Dj': ['Ђ'],
            'Dz': ['Џ'],
            'Gx': ['Ĝ'],
            'Hx': ['Ĥ'],
            'Ij': ['Ĳ'],
            'Jx': ['Ĵ'],
            'Kh': ['Х'],
            'Lj': ['Љ'],
            'Nj': ['Њ'],
            'Oe': ['Œ'],
            'Ps': ['Ψ'],
            'Sh': ['Ш'],
            'Shch': ['Щ'],
            'Ss': ['ẞ'],
            'Th': ['Þ'],
            'Ts': ['Ц'],
            'Ya': ['Я'],
            'Yu': ['Ю'],
            'Zh': ['Ж']
        };
        Object.keys(swaps).forEach(function (swap) {
            swaps[swap].forEach(function (s) {
                str = str.replace(new RegExp(s, "g"), swap);
            });
        });
        return str.replace(/[^a-z0-9 -]/g, "").replace(/\s+/g, "-").replace(/-+/g, "-").replace(/^-+/, "").replace(/-+$/, "");
    }

    var css_class_wrap = '.golo-place-select-meta-box-wrap';
    var golo_get_city_by_country = function () {
        var $this = $(".golo-place-country-ajax", css_class_wrap);
        var $place_city = $(".golo-place-city-ajax", css_class_wrap);
        var $is_slug = $place_city.attr('data-slug');
        if (typeof($is_slug) === 'undefined') {
            $is_slug = '1';
        }
        if ($this.length && $place_city.length) {
            var selected_country = $this.val();
            $.ajax({
                type: "POST",
                url: ajax_url,
                data: {
                    'action': 'golo_get_city_by_country_ajax',
                    'country': selected_country,
                    'type': 0,
                    'is_slug': $is_slug
                },
                beforeSend: function () {
                    $place_city.parent().children('.golo-loading').remove();
                    $place_city.parent().append('<span class="golo-loading"><i class="las la-circle-notch la-spin golo-loading__icon"></i></span>');
                },
                success: function (response) {
                    $place_city.html(response);
                    var val_selected = $place_city.attr('data-selected');
                    if (typeof val_selected !== 'undefined') {
                        $place_city.val(val_selected);
                    }
                    $place_city.parent().children('.golo-loading').remove();
                },
                error: function () {
                    $place_city.parent().children('.golo-loading').remove();
                },
                complete: function () {
                    $place_city.parent().children('.golo-loading').remove();
                }
            });
        }
    };

    var golo_get_neighborhoods_by_city = function () {
        var $this = $(".golo-place-city-ajax", css_class_wrap);
        var $place_neighborhood = $(".golo-place-neighborhood-ajax", css_class_wrap);
        var $is_slug = $place_neighborhood.attr('data-slug');
        if (typeof($is_slug) === 'undefined') {
            $is_slug='1';
        }
        if ($this.length && $place_neighborhood.length) {
            var selected_city = $this.val();
            $.ajax({
                type: "POST",
                url: ajax_url,
                data: {
                    'action': 'golo_get_neighborhoods_by_city_ajax',
                    'city': selected_city,
                    'type': 0,
                    'is_slug': $is_slug
                },
                beforeSend: function () {
                    $place_neighborhood.parent().children('.golo-loading').remove();
                    $place_neighborhood.parent().append('<span class="golo-loading"><i class="las la-circle-notch la-spin golo-loading__icon"></i></span>');
                },
                success: function (response) {
                    $place_neighborhood.html(response);
                    var val_selected = $place_neighborhood.attr('data-selected');
                    if (typeof val_selected !== 'undefined') {
                        $place_neighborhood.val(val_selected);
                    }
                    $place_neighborhood.parent().children('.golo-loading').remove();
                },
                error: function () {
                    $place_neighborhood.parent().children('.golo-loading').remove();
                },
                complete: function () {
                    $place_neighborhood.parent().children('.golo-loading').remove();
                }
            });
        }
    };

    $(document).ready(function() {

        additionalFieldProcess();

        purchase_form();

        toggle();

        save_options();

        plugin_action();

        setTimeout(function() {
            changeWidthContent();
        }, 100);

        var css_class_wrap = '.golo-place-select-meta-box-wrap';

        golo_get_city_by_country();
        $(".golo-place-country-ajax", css_class_wrap).on('change', function () {
            golo_get_city_by_country();
        });

        golo_get_neighborhoods_by_city();
        $(".golo-place-city-ajax", css_class_wrap).on('change', function () {
            golo_get_neighborhoods_by_city();
        });

    });

})(jQuery);