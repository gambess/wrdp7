jQuery.noConflict();

jQuery(document).ready(function () {

    jQuery('#b2s_calendar').fullCalendar({
        editable: b2s_has_premium,
        locale: b2s_calendar_locale,
        eventLimit: 2,
        timeFormat: 'H:mm',
        events: ajaxurl + '?action=b2s_get_calendar_events',
        eventRender: function (event, element) {
            $header = jQuery("<div>").addClass("b2s-calendar-header");
            $network_name = jQuery("<span>").text(event.author).addClass("network-name").css("display", "block");
            element.find(".fc-time").after($network_name);
            element.html(element.html());
            $parent = element.parent();
            $header.append(element.find(".fc-content"));
            element.append($header);
            $body = jQuery("<div>").addClass("b2s-calendar-body");
            $body.append(event.avatar);
            $body.append(element.find(".fc-title"));
            $body.append(jQuery("<br>"));
            var $em = jQuery("<em>").css("padding-top", "5px").css("display", "block");
            $em.append("<img src='" + b2s_plugin_url + "assets/images/portale/" + event.network_id + "_flat.png' style='height: 16px;width: 16px;display: inline-block;padding-right: 2px;padding-left: 2px;' />")
            $em.append(event.network_name);
            $em.append(jQuery("<span>").text(": " + event.profile));
            $body.append($em);

            element.append($body);
        },
        eventDrop: function (event, delta, revertFunc) {
            jQuery.ajax({
                url: ajaxurl,
                type: "POST",
                dataType: "json",
                cache: false,
                data: {
                    'action': 'b2s_calendar_move_post',
                    'b2s_id': event.b2s_id,
                    'user_timezone': event.user_timezone,
                    'sched_date': event.start.format(),
                },
                success: function (data) {
                    wp.heartbeat.connectNow();
                }
            });
        },
        eventAllow: function (dropLocation, draggedEvent) {
            return dropLocation.start.isAfter(b2s_calendar_date) && draggedEvent.start.isAfter(b2s_calendar_datetime);
        },
        eventClick: function (calEvent, jsEvent, view) {

            if (jQuery('#b2s-edit-event-modal-' + calEvent.b2s_id).length == 1)
            {
                jQuery('#b2s-edit-event-modal-' + calEvent.b2s_id).remove();
            }
            b2s_current_post_id = calEvent.post_id;
            var $modal = jQuery("<div>");

            jQuery.ajax({
                url: ajaxurl,
                type: "POST",
                cache: false,
                async: false,
                data: {
                    'action': 'b2s_get_calendar_edit_modal',
                    'id': calEvent.b2s_id
                },
                success: function (data) {
                    $modal = $modal.html(data);
                }
            });
            jQuery("body").append($modal);

            jQuery('#b2sUserTimeZone').val(jQuery('#user_timezone').val());

            jQuery('#b2s-edit-event-modal-' + calEvent.b2s_id).modal('show');
            activatePortal(calEvent.network_auth_id);
            initSceditor(calEvent.network_auth_id);
            networkCount(calEvent.network_auth_id);

            if (jQuery('.b2s-post-ship-item-post-format-text[data-network-type="' + calEvent.network_type + '"][data-network-id="' + calEvent.network_id + '"]').length > 0) {
                var postFormatText = b2s_calendar_formats;

                var isSetPostFormat = false;
                //is set post format => override current condidtions by user settings for this post
                if (calEvent.post_format !== null) {
                    jQuery('.b2sNetworkSettingsPostFormatCurrent[data-network-type="' + calEvent.network_type + '"][data-network-id="' + calEvent.network_id + '"]').val(calEvent.post_format);
                    jQuery('.b2s-post-ship-item-post-format-text[data-network-auth-id="' + calEvent.network_auth_id + '"]').html(postFormatText[calEvent.post_format]);
                    jQuery('.b2s-post-item-details-post-format[data-network-auth-id="' + calEvent.network_auth_id + '"]').val(calEvent.post_format);

                    //edit modal select post format
                    jQuery('.b2s-user-network-settings-post-format[data-network-type="' + calEvent.network_type + '"][data-network-id="' + calEvent.network_id + '"]').removeClass('b2s-settings-checked');
                    jQuery('.b2s-user-network-settings-post-format[data-network-type="' + calEvent.network_type + '"][data-network-id="' + calEvent.network_id + '"][data-post-format="' + calEvent.post_format + '"]').addClass('b2s-settings-checked');

                } else {
                    jQuery('.b2s-post-ship-item-post-format-text[data-network-auth-id="' + calEvent.network_auth_id + '"]').html(postFormatText[jQuery('.b2sNetworkSettingsPostFormatCurrent[data-network-type="' + calEvent.network_type + '"][data-network-id="' + calEvent.network_id + '"]').val()]);
                    jQuery('.b2s-post-item-details-post-format[data-network-auth-id="' + calEvent.network_auth_id + '"]').val(jQuery('.b2sNetworkSettingsPostFormatCurrent[data-network-type="' + calEvent.network_type + '"][data-network-id="' + calEvent.network_id + '"]').val());
                }

                //if linkpost then show btn meta tags
                var isMetaChecked = false;
                if (calEvent.network_id == "1" && jQuery('#isOgMetaChecked').val() == "1") {
                    isMetaChecked = true;
                }
                if (calEvent.network_id == "2" && jQuery('#isCardMetaChecked').val() == "1") {
                    isMetaChecked = true;
                }
                if (isMetaChecked && jQuery('.b2sNetworkSettingsPostFormatCurrent[data-network-type="' + calEvent.network_type + '"][data-network-id="' + calEvent.network_id + '"]').val() == "0") {
                    jQuery('.b2s-post-item-details-preview-title[data-network-auth-id="' + calEvent.network_auth_id + '"]').prop("readonly", false);
                    jQuery('.b2s-post-item-details-preview-desc[data-network-auth-id="' + calEvent.network_auth_id + '"]').prop("readonly", false);
                    jQuery('.b2s-post-item-details-preview-url-reload[data-network-id="' + calEvent.network_id + '"]').hide();

                    var dataMetaType = jQuery('.b2s-post-item-details-preview-desc[data-network-auth-id="' + calEvent.network_auth_id + '"]').attr("data-meta-type");
                    if (dataMetaType == "og") {
                        jQuery('#b2sChangeOgMeta').val("1");
                    } else {
                        jQuery('#b2sChangeCardMeta').val("1");
                    }

                } else {
                    jQuery('.b2s-post-item-details-preview-title[data-network-auth-id="' + calEvent.network_auth_id + '"]').prop("readonly", true);
                    jQuery('.b2s-post-item-details-preview-desc[data-network-auth-id="' + calEvent.network_auth_id + '"]').prop("readonly", true);
                    jQuery('.b2s-post-item-details-preview-url-reload[data-network-id="' + calEvent.network_id + '"]').show();
                    jQuery('.b2s-post-item-details-preview-url-reload[data-network-id="' + calEvent.network_id + '"]').trigger("click");
                }


            }

            jQuery("#b2sPostId").val(calEvent.post_id);

            var today = new Date();
            var dateFormat = "yyyy-mm-dd";
            var language = "en";
            var showMeridian = true;
            if (jQuery('#b2sUserLang').val() == "de") {
                dateFormat = "dd.mm.yyyy";
                language = "de";
                showMeridian = false;
                //printDateFormat(calEvent.network_auth_id);
            }

            jQuery(".b2s-post-item-details-release-input-date").datepicker({
                format: dateFormat,
                language: language,
                maxViewMode: 2,
                todayHighlight: true,
                startDate: today,
                calendarWeeks: true,
                autoclose: true
            });

            jQuery('.b2s-post-item-details-release-input-time').timepicker({
                minuteStep: 15,
                appendWidgetTo: 'body',
                showSeconds: false,
                showMeridian: showMeridian,
                defaultTime: 'current',
                snapToStep: true
            });


            jQuery(".b2s-post-item-details-release-input-date").datepicker().on('changeDate', function (e) {
                checkSchedDateTime(calEvent.network_auth_id);
            });
            jQuery('.b2s-post-item-details-release-input-time').timepicker().on('changeTime.timepicker', function (e) {
                checkSchedDateTime(calEvent.network_auth_id);

            });

            init();

            if (!b2s_has_premium)
            {
                jQuery('#b2s-edit-event-modal-' + calEvent.b2s_id).find("input, textarea, button").each(function () {
                    if (!jQuery(this).hasClass('b2s-modal-close')) {
                        jQuery(this).prop("disabled", true);
                    }
                });
            }
        }

    });

    jQuery(".b2s-loading-area").hide();

    jQuery(document).on('click', '.b2s-select-image-modal-open', function () {
        jQuery('.b2s-network-select-image-content').html("");
        jQuery.ajax({
            url: ajaxurl,
            type: "POST",
            cache: false,
            async: false,
            data: {
                'action': 'b2s_get_image_modal',
                'id': jQuery(this).data('post-id'),
                'image_url': jQuery(this).data('image-url')
            },
            success: function (data) {
                jQuery(".b2s-network-select-image-content").html(data);
            }
        });

        var authId = jQuery(this).data('network-auth-id');
        jQuery('.b2s-image-change-this-network').attr('data-network-auth-id', authId);
        jQuery('.b2s-upload-image').attr('data-network-auth-id', authId);

        var content = "<img class='b2s-post-item-network-image-selected-account' height='22px' src='" + jQuery('.b2s-post-item-network-image[data-network-auth-id="' + authId + '"]').attr('src') + "' /> " + jQuery('.b2s-post-item-details-network-display-name[data-network-auth-id="' + authId + '"]').html();
        jQuery('.b2s-selected-network-for-image-info').html(content);
        jQuery('#b2sInsertImageType').val("0");

        jQuery('.networkImage').each(function () {
            var width = this.naturalWidth;
            var height = this.naturalHeight;
            jQuery(this).parents('.b2s-image-item').find('.b2s-image-item-caption-resolution').html(width + 'x' + height);
        });
        jQuery('#b2s-network-select-image').modal('show');
        return false;
    });

    jQuery(document).on("click", ".b2s-calendar-delete", function () {
        var id = jQuery(this).data("b2s-id");
        var post_id = jQuery(this).data("post-id");
        jQuery.ajax({
            url: ajaxurl,
            type: "POST",
            dataType: "json",
            cache: false,
            data: {
                'action': 'b2s_calendar_delete',
                'b2s_id': id,
                'post_id': post_id
            },
            success: function (data) {
                jQuery('#b2s-edit-event-modal-' + id).modal('hide');

                refreshCalender();
                wp.heartbeat.connectNow();
            }
        });
    });

    jQuery(document).on("click", ".b2s-calendar-save-all", function (e) {
        jQuery('#save_method').val("apply-all");
        e.preventDefault();
        var id = jQuery(this).data("b2s-id");
        jQuery.ajax({
            url: ajaxurl,
            type: "POST",
            dataType: "json",
            cache: false,
            data: jQuery(this).closest("form").serialize(),
            success: function (data) {
                jQuery('#b2s-edit-event-modal-' + id).modal('hide');

                refreshCalender();
                jQuery('#b2s-edit-event-modal-' + id).remove();
                wp.heartbeat.connectNow();
            }
        });
    });

    jQuery(document).on("click", ".b2s-calendar-save-this", function (e) {
        e.preventDefault();
        jQuery('#save_method').val("apply-this");
        var id = jQuery(this).data("b2s-id");
        jQuery.ajax({
            url: ajaxurl,
            type: "POST",
            dataType: "json",
            cache: false,
            data: jQuery(this).closest("form").serialize(),
            success: function (data) {
                jQuery('#b2s-edit-event-modal-' + id).modal('hide');
                refreshCalender();
                jQuery('#b2s-edit-event-modal-' + id).remove();
                wp.heartbeat.connectNow();
            }
        });
    });

    jQuery(document).on("click", ".release_locks", function () {
        jQuery.ajax({
            url: ajaxurl,
            type: "POST",
            cache: false,
            async: false,
            data: {
                'action': 'b2s_get_calendar_release_locks',
                'post_id': jQuery('#post_id').val()
            },
            success: function (data) {
                wp.heartbeat.connectNow();
            }
        });
    });
});

function refreshCalender() {
    jQuery('#b2s_calendar').fullCalendar('refetchEvents');
}

jQuery('#b2s-info-meta-tag-modal').on('hidden.bs.modal', function (e) {
    jQuery('body').addClass('modal-open');
});

jQuery('#b2s-network-select-image').on('hidden.bs.modal', function (e) {
    jQuery('body').addClass('modal-open');
});

jQuery('#b2s-post-ship-item-post-format-modal').on('hidden.bs.modal', function (e) {
    jQuery('body').addClass('modal-open');
});


//jQuery(this).attr('data-network-auth-id') 
function checkSchedDateTime(dataNetworkAuthId) {
    var dateElement = '.b2s-post-item-details-release-input-date[data-network-auth-id="' + dataNetworkAuthId + '"]';
    var timeElement = '.b2s-post-item-details-release-input-time[data-network-auth-id="' + dataNetworkAuthId + '"]';
    var dateStr = jQuery(dateElement).val();
    var minStr = jQuery(timeElement).val();

    var timeZone = parseInt(jQuery('#user_timezone').val()) * (-1);

    if (jQuery('#b2sUserLang').val() == 'de') {
        dateStr = dateStr.substring(6, 10) + '-' + dateStr.substring(3, 5) + '-' + dateStr.substring(0, 2);
    } else {
        var minParts = minStr.split(' ');
        var minParts2 = minParts[0].split(':');
        if (minParts[1] == 'PM') {
            minParts2[0] = parseInt(minParts2[0]) + 12;
        }
        minStr = minParts2[0] + ':' + minParts2[1];
    }

    var minParts3 = minStr.split(':');
    if (minParts3[0] < 10) {
        minParts3[0] = '0' + minParts3[0];
    }
    var dateParts = dateStr.split('-');

    //utc current time
    var now = new Date();
    //offset between utc und user 
    var offset = (parseInt(now.getTimezoneOffset()) / 60) * (-1);
    //enter hour to user time
    var hour = parseInt(minParts3[0]) + timeZone + offset;
    //calculate datetime in utc
    var enter = new Date(dateParts[0], dateParts[1] - 1, dateParts[2], hour, minParts3[1]);
    //compare enter date time with allowed user time
    if (enter.getTime() < now.getTime()) {
        //enter set on next 15 minutes and calculate on user timezone
        enter.setTime(now.getTime() + (900000 - (now.getTime() % 900000)) - (3600000 * (timeZone + offset)));
        jQuery(dateElement).datepicker('update', enter);
        jQuery(timeElement).timepicker('setTime', enter);
    }
}


function printDateFormat(dataNetworkAuthId) {
    var dateElement = '.b2s-post-item-details-release-input-date[data-network-auth-id="' + dataNetworkAuthId + '"]';
    var dateStr = jQuery(dateElement).val();
    dateStr = dateStr.substring(8, 10) + '.' + dateStr.substring(5, 7) + '.' + dateStr.substring(0, 4);
    jQuery(dateElement).val(dateStr);
}

