jQuery.noConflict();
jQuery(window).on("load", function () {

    var showMeridian = true;
    if (jQuery('#b2sUserLang').val() == 'de') {
        showMeridian = false;
    }
    jQuery('.b2s-settings-sched-item-input-time').timepicker({
        minuteStep: 30,
        appendWidgetTo: 'body',
        showSeconds: false,
        showMeridian: showMeridian,
        defaultTime: 'current'
    });
    var b2sShowSection = jQuery('#b2sShowSection').val();
    if (b2sShowSection != "") {
        jQuery("." + b2sShowSection).trigger("click");
    }

});


jQuery('.b2sSaveSocialMetaTagsSettings').validate({
    ignore: "",
    errorPlacement: function () {
        return false;
    },
    submitHandler: function (form) {
        jQuery('.b2s-settings-user-success').hide();
        jQuery('.b2s-settings-user-error').hide();
        jQuery(".b2s-loading-area").show();
        jQuery(".b2s-user-settings-area").hide();
        jQuery('.b2s-server-connection-fail').hide();
        jQuery('.b2s-meta-tags-success').hide();
        jQuery('.b2s-meta-tags-danger').hide();
        jQuery.ajax({
            processData: false,
            url: ajaxurl,
            type: "POST",
            dataType: "json",
            cache: false,
            data: jQuery(form).serialize(),
            error: function () {
                jQuery('.b2s-server-connection-fail').show();
                return false;
            },
            success: function (data) {
                jQuery(".b2s-loading-area").hide();
                jQuery(".b2s-user-settings-area").show();
                if (data.result == true) {
                    jQuery('.b2s-settings-user-success').show();
                    if (data.b2s == true) {
                        if (data.yoast == true) {
                            jQuery('.b2s-meta-tags-yoast').show();
                        }
                        if (data.aioseop) {
                            jQuery('.b2s-meta-tags-aioseop').show();
                        }
                        if (data.webdados) {
                            jQuery('.b2s-meta-tags-webdados').show();
                        }
                    }
                } else {
                    jQuery('.b2s-settings-user-error').show();
                }
            }
        });
        return false;
    }
});


jQuery(document).on('click', '.b2sClearSocialMetaTags', function () {
    
    jQuery('.b2s-settings-user-success').hide();
    jQuery('.b2s-settings-user-error').hide();
    jQuery('.b2s-clear-meta-tags').hide();
    jQuery(".b2s-loading-area").show();
    jQuery(".b2s-user-settings-area").hide();
    jQuery('.b2s-server-connection-fail').hide();
    jQuery.ajax({
        url: ajaxurl,
        type: "POST",
        dataType: "json",
        cache: false,
        data: {
            'action': 'b2s_reset_social_meta_tags',
        },
        error: function () {
            jQuery('.b2s-server-connection-fail').show();
            return false;
        },
        success: function (data) {
            jQuery(".b2s-loading-area").hide();
            jQuery(".b2s-user-settings-area").show();
            if (data.result == true) {
                jQuery('.b2s-clear-meta-tags-success').show();
            } else {
                jQuery('.b2s-clear-meta-tags-error').show();
            }
        }
    });
   return false; 
});



jQuery(document).on('click', '.b2s-upload-image', function () {
    var targetId = jQuery(this).attr('data-id');
    if (typeof wp !== 'undefined' && wp.media && wp.media.editor) {
        wpMedia = wp.media({
            title: jQuery('#b2s_wp_media_headline').val(),
            button: {
                text: jQuery('#b2s_wp_media_btn').val(),
            },
            multiple: false,
            library: {type: 'image'}
        });
        wpMedia.open();

        wpMedia.on('select', function () {
            var validExtensions = ['jpg', 'jpeg', 'png'];
            var attachment = wpMedia.state().get('selection').first().toJSON();

            jQuery('#' + targetId).val(attachment.url);
        });
    } else {
        jQuery('.b2s-upload-image-no-permission').show();
    }
    return false;
});




jQuery(document).on('click', '.b2s-save-settings-pro-info', function () {
    return false;
});
jQuery('#b2sSaveUserSettingsSchedTime').validate({
    ignore: "",
    errorPlacement: function () {
        return false;
    },
    submitHandler: function (form) {
        jQuery('.b2s-settings-user-success').hide();
        jQuery('.b2s-settings-user-error').hide();
        jQuery(".b2s-loading-area").show();
        jQuery(".b2s-user-settings-area").hide();
        jQuery('.b2s-server-connection-fail').hide();
        jQuery.ajax({
            processData: false,
            url: ajaxurl,
            type: "POST",
            dataType: "json",
            cache: false,
            data: jQuery(form).serialize(),
            error: function () {
                jQuery('.b2s-server-connection-fail').show();
                return false;
            },
            success: function (data) {
                jQuery(".b2s-loading-area").hide();
                jQuery(".b2s-user-settings-area").show();
                if (data.result == true) {
                    jQuery('.b2s-settings-user-success').show();
                } else {
                    jQuery('.b2s-settings-user-error').show();
                }
            }
        });
        return false;
    }
});
jQuery(document).on('click', '#b2s-user-network-settings-short-url', function () {
    jQuery('.b2s-settings-user-success').hide();
    jQuery('.b2s-settings-user-error').hide();
    jQuery(".b2s-loading-area").show();
    jQuery(".b2s-user-settings-area").hide();
    jQuery('.b2s-server-connection-fail').hide();
    jQuery.ajax({
        url: ajaxurl,
        type: "POST",
        dataType: "json",
        cache: false,
        data: {
            'action': 'b2s_user_network_settings',
            'short_url': jQuery('#b2s-user-network-settings-short-url').val(),
        },
        error: function () {
            jQuery('.b2s-server-connection-fail').show();
            return false;
        },
        success: function (data) {
            jQuery(".b2s-loading-area").hide();
            jQuery(".b2s-user-settings-area").show();
            if (data.result == true) {
                jQuery('.b2s-settings-user-success').show();
                jQuery('#b2s-user-network-settings-short-url').val(data.content);
                if (jQuery("#b2s-user-network-settings-short-url").is(":checked")) {
                    jQuery('#b2s-user-network-settings-short-url').prop('checked', false);
                } else {
                    jQuery('#b2s-user-network-settings-short-url').prop('checked', true);
                }
            } else {
                jQuery('.b2s-settings-user-error').show();
            }
        }
    });
    return false;
});

jQuery('#b2s-user-network-settings-auto-post').validate({
    ignore: "",
    errorPlacement: function () {
        return false;
    },
    submitHandler: function (form) {
        jQuery('.b2s-settings-user-success').hide();
        jQuery('.b2s-settings-user-error').hide();
        jQuery(".b2s-loading-area").show();
        jQuery(".b2s-user-settings-area").hide();
        jQuery('.b2s-server-connection-fail').hide();
        jQuery.ajax({
            processData: false,
            url: ajaxurl,
            type: "POST",
            dataType: "json",
            cache: false,
            data: jQuery(form).serialize(),
            error: function () {
                jQuery('.b2s-server-connection-fail').show();
                return false;
            },
            success: function (data) {
                jQuery(".b2s-loading-area").hide();
                jQuery(".b2s-user-settings-area").show();
                if (data.result == true) {
                    jQuery('.b2s-settings-user-success').show();
                } else {
                    jQuery('.b2s-settings-user-error').show();
                }
            }
        });
        return false;
    }
});
jQuery(document).on('click', '.b2s-post-type-select-btn', function () {
    var type = jQuery(this).attr('data-post-type');
    var tempCurText = jQuery(this).text();
    if (jQuery(this).attr('data-select-toogle-state') == "0") { //0=select
        jQuery('.b2s-post-type-item-' + type).prop('checked', true);
        jQuery(this).attr('data-select-toogle-state', '1');
    } else {
        jQuery('.b2s-post-type-item-' + type).prop('checked', false);
        jQuery(this).attr('data-select-toogle-state', '0');
    }
    jQuery(this).text(jQuery(this).attr('data-select-toogle-name'));
    jQuery(this).attr('data-select-toogle-name', tempCurText);
    return false;
});


jQuery(document).on('change', '#b2s-user-time-zone', function () {
    var curUserTime = calcCurrentExternTimeByOffset(jQuery('option:selected', this).attr('data-offset'), jQuery('#b2sUserLang').val());
    jQuery('#b2s-user-time').text(curUserTime);

    jQuery('.b2s-settings-user-success').hide();
    jQuery('.b2s-settings-user-error').hide();
    jQuery(".b2s-loading-area").show();
    jQuery(".b2s-user-settings-area").hide();
    jQuery('.b2s-server-connection-fail').hide();

    jQuery.ajax({
        url: ajaxurl,
        type: "POST",
        dataType: "json",
        cache: false,
        data: {
            'action': 'b2s_user_network_settings',
            'user_time_zone': jQuery(this).val()
        },
        error: function () {
            jQuery('.b2s-server-connection-fail').show();
            return false;
        },
        success: function (data) {
            jQuery(".b2s-loading-area").hide();
            jQuery(".b2s-user-settings-area").show();
            if (data.result == true) {
                jQuery('.b2s-settings-user-success').show();
            } else {
                jQuery('.b2s-settings-user-error').show();
            }
        }
    });
    return false;
});
jQuery(document).on('click', '#b2s-user-network-settings-allow-shortcode', function () {
    jQuery('.b2s-settings-user-success').hide();
    jQuery('.b2s-settings-user-error').hide();
    jQuery(".b2s-loading-area").show();
    jQuery(".b2s-user-settings-area").hide();
    jQuery('.b2s-server-connection-fail').hide();
    jQuery.ajax({
        url: ajaxurl,
        type: "POST",
        dataType: "json",
        cache: false,
        data: {
            'action': 'b2s_user_network_settings',
            'allow_shortcode': jQuery('#b2s-user-network-settings-allow-shortcode').val(),
        },
        error: function () {
            jQuery('.b2s-server-connection-fail').show();
            return false;
        },
        success: function (data) {
            jQuery(".b2s-loading-area").hide();
            jQuery(".b2s-user-settings-area").show();
            if (data.result == true) {
                jQuery('.b2s-settings-user-success').show();
                jQuery('#b2s-user-network-settings-allow-shortcode').val(data.content);
                if (jQuery("#b2s-user-network-settings-allow-shortcode").is(":checked")) {
                    jQuery('#b2s-user-network-settings-allow-shortcode').prop('checked', false);
                } else {
                    jQuery('#b2s-user-network-settings-allow-shortcode').prop('checked', true);
                }
            } else {
                jQuery('.b2s-settings-user-error').show();
            }
        }
    });
    return false;
});

jQuery('#b2s-save-time-settings-btn-trigger').on('click', function () {
    jQuery('#b2s-save-time-settings-btn').trigger('click');
});


jQuery('.b2sSaveUserSettingsPostFormatFb').validate({
    ignore: "",
    errorPlacement: function () {
        return false;
    },
    submitHandler: function (form) {
        jQuery('.b2s-settings-user-success').hide();
        jQuery('.b2s-settings-user-error').hide();
        jQuery(".b2s-loading-area").show();
        jQuery(".b2s-user-settings-area").hide();
        jQuery('.b2s-server-connection-fail').hide();
        jQuery.ajax({
            processData: false,
            url: ajaxurl,
            type: "POST",
            dataType: "json",
            cache: false,
            data: jQuery(form).serialize(),
            error: function () {
                jQuery('.b2s-server-connection-fail').show();
                return false;
            },
            success: function (data) {
                jQuery(".b2s-loading-area").hide();
                jQuery(".b2s-user-settings-area").show();
                if (data.result == true) {
                    jQuery('.b2s-settings-user-success').show();
                } else {
                    jQuery('.b2s-settings-user-error').show();
                }
            }
        });
        return false;
    }
});

jQuery('.b2sSaveUserSettingsPostFormatTw').validate({
    ignore: "",
    errorPlacement: function () {
        return false;
    },
    submitHandler: function (form) {
        jQuery('.b2s-settings-user-success').hide();
        jQuery('.b2s-settings-user-error').hide();
        jQuery(".b2s-loading-area").show();
        jQuery(".b2s-user-settings-area").hide();
        jQuery('.b2s-server-connection-fail').hide();
        jQuery.ajax({
            processData: false,
            url: ajaxurl,
            type: "POST",
            dataType: "json",
            cache: false,
            data: jQuery(form).serialize(),
            error: function () {
                jQuery('.b2s-server-connection-fail').show();
                return false;
            },
            success: function (data) {
                jQuery(".b2s-loading-area").hide();
                jQuery(".b2s-user-settings-area").show();
                if (data.result == true) {
                    jQuery('.b2s-settings-user-success').show();
                } else {
                    jQuery('.b2s-settings-user-error').show();
                }
            }
        });
        return false;
    }
});


/*jQuery(document).on('change', '.b2s-user-network-settings-post-format', function () {
 jQuery('.b2s-settings-user-success').hide();
 jQuery('.b2s-settings-user-error').hide();
 jQuery('.b2s-server-connection-fail').hide();
 jQuery(".b2s-loading-area").show();
 jQuery(".b2s-user-settings-area").hide();
 
 var networkId = jQuery(this).attr("data-network-id");
 jQuery('.b2s-user-network-settings-post-format[data-network-id="' + networkId + '"]').removeClass('b2s-settings-checked');
 jQuery(this).addClass('b2s-settings-checked');
 
 jQuery.ajax({
 url: ajaxurl,
 type: "POST",
 dataType: "json",
 cache: false,
 data: {
 'action': 'b2s_user_network_settings',
 'post_format': jQuery(this).val(),
 'network_id': networkId
 },
 error: function () {
 jQuery('.b2s-server-connection-fail').show();
 return false;
 },
 success: function (data) {
 jQuery(".b2s-loading-area").hide();
 jQuery(".b2s-user-settings-area").show();
 if (data.result == true) {
 jQuery('.b2s-settings-user-success').show();
 } else {
 jQuery('.b2s-settings-user-error').show();
 }
 }
 });
 return false;
 });*/

jQuery(document).on('click', '.b2s-get-settings-sched-time-default', function () {
    jQuery('.b2s-server-connection-fail').hide();
    jQuery.ajax({
        url: ajaxurl,
        type: "POST",
        dataType: "json",
        cache: false,
        data: {
            'action': 'b2s_get_settings_sched_time_default',
        },
        error: function () {
            jQuery('.b2s-server-connection-fail').show();
            return false;
        },
        success: function (data) {
            if (data.result == true) {
                jQuery.each(data.times, function (network_id, time) {
                    time.forEach(function (network_type_time, count) {
                        if (network_type_time != "") {
                            jQuery('.b2s-settings-sched-item-input-time[data-network-id="' + network_id + '"][data-network-type="' + count + '"]').val(network_type_time);
                            count++;
                        }
                    });
                });
            }
        }
    });
    return false;
});
function padDate(n) {
    return ("0" + n).slice(-2);
}

function calcCurrentExternTimeByOffset(offset, lang) {

    var UTCstring = (new Date()).getTime() / 1000;
    var neuerTimestamp = UTCstring + (offset * 3600);
    neuerTimestamp = parseInt(neuerTimestamp);
    var newDate = new Date(neuerTimestamp * 1000);
    var year = newDate.getUTCFullYear();
    var month = newDate.getUTCMonth() + 1;
    if (month < 10) {
        month = "0" + month;
    }

    var day = newDate.getUTCDate();
    if (day < 10) {
        day = "0" + day;
    }

    var mins = newDate.getUTCMinutes();
    if (mins < 10) {
        mins = "0" + mins;
    }

    var hours = newDate.getUTCHours();
    if (lang == "de") {
        if (hours < 10) {
            hours = "0" + hours;
        }
        return  day + "." + month + "." + year + " " + hours + ":" + mins;
    }
    var am_pm = "";
    if (hours >= 12) {
        am_pm = "PM";
    } else {
        am_pm = "AM";
    }

    if (hours == 0) {
        hours = 12;
    }

    if (hours > 12) {
        var newHour = hours - 12;
        if (newHour < 10) {
            newHour = "0" + newHour;
        }
    } else {
        var newHour = hours;
    }
    return year + "/" + month + "/" + day + " " + newHour + ":" + mins + " " + am_pm;
}


