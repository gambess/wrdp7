jQuery.noConflict();

if (typeof wp.heartbeat !== "undefined") {
    jQuery(document).on('heartbeat-send', function (e, data) {
        data['b2s_heartbeat'] = 'b2s_listener';
    });
    wp.heartbeat.connectNow();
}
jQuery(window).on("load", function () {
    jQuery('#b2sPagination').val("1");
    b2sSortFormSubmit();
    if (jQuery('#b2sType').val() != "sched") {
        jQuery('.b2s-sched-calendar-btn').hide();

    }
    jQuery('#b2s-sched-calendar-area').hide();
});


jQuery(document).on('click', '.b2s-sched-calendar-btn', function () {
    if (jQuery('#b2s-sched-calendar-area').is(":visible")) {
        jQuery('#b2s-sched-calendar-btn-text').text(jQuery(this).attr('data-show-calendar-btn-title'));
        jQuery('#b2s-sched-calendar-area').hide();
    } else {
        jQuery('#b2s-sched-calendar-btn-text').text(jQuery(this).attr('data-hide-calendar-btn-title'));
        jQuery('#b2s-sched-calendar-area').show();
    }
});

jQuery(document).on('click', '.b2sDetailsPublishPostBtn', function () {
    var postId = jQuery(this).attr('data-post-id');
    var showByDate = jQuery(this).attr('data-search-date');
    if (!jQuery(this).find('i').hasClass('isload')) {
        jQuery('.b2s-server-connection-fail').hide();
        jQuery.ajax({
            url: ajaxurl,
            type: "POST",
            dataType: "json",
            cache: false,
            data: {
                'action': 'b2s_publish_post_data',
                'postId': postId,
                'showByDate': showByDate
            },
            error: function () {
                jQuery('.b2s-server-connection-fail').show();
                return false;
            },
            success: function (data) {
                if (data.result == true) {
                    jQuery('.b2s-post-publish-area[data-post-id="' + data.postId + '"]').html(data.content);
                }
                wp.heartbeat.connectNow();
            }
        });
        jQuery(this).find('i').removeClass('glyphicon-chevron-down').addClass('glyphicon-chevron-up').addClass('isload').addClass('isShow');
    } else {
        if (jQuery(this).find('i').hasClass('isShow')) {
            jQuery('.b2s-post-publish-area[data-post-id="' + postId + '"]').hide();
            jQuery(this).find('i').removeClass('isShow').addClass('isHide').removeClass('glyphicon-chevron-up').addClass('glyphicon-chevron-down');
        } else {
            jQuery('.b2s-post-publish-area[data-post-id="' + postId + '"]').show();
            jQuery(this).find('i').removeClass('isHide').addClass('isShow').removeClass('glyphicon-chevron-down').addClass('glyphicon-chevron-up');
        }
    }
});


jQuery(document).on('click', '#b2s-sort-submit-btn', function () {
    jQuery('#b2sPagination').val("1");
    b2sSortFormSubmit();
    return false;
});


jQuery(document).on('keypress', '#b2sSortPostTitle', function (event) {
    if (event.keyCode == 13) {  //Hide Enter
        return false;
    }
});

jQuery(document).on('click', '.b2s-pagination-btn', function () {
    jQuery('#b2sPagination').val(jQuery(this).attr('data-page'));
    b2sSortFormSubmit();
    return false;
});

jQuery(document).on('change', '.b2s-select', function () {
    jQuery('#b2sPagination').val("1");
    b2sSortFormSubmit();
    return false;
});

jQuery(document).on('click', '#b2s-sort-reset-btn', function () {
    jQuery('#b2sPagination').val("1");
    jQuery('#b2sSortPostTitle').val("");
    jQuery('#b2sSortPostAuthor').prop('selectedIndex', 0);
    jQuery('#b2sSortPostCat').prop('selectedIndex', 0);
    jQuery('#b2sSortPostType').prop('selectedIndex', 0);
    jQuery('#b2sSortPostSchedDate').prop('selectedIndex', 0);
    jQuery('#b2sShowByDate').val("");
    jQuery('#b2sUserAuthId').val("");
    jQuery('#b2sSortPostStatus').prop('selectedIndex', 0);
    jQuery('#b2sSortPostPublishDate').prop('selectedIndex', 0);
    b2sSortFormSubmit();
    return false;
});


function b2sSortFormSubmit(sched_dates) {
    jQuery('.b2s-server-connection-fail').hide();
    jQuery('.b2s-loading-area').show();
    jQuery('.b2s-sort-result-area').hide();
    jQuery('.b2s-sort-result-item-area').html("").hide();
    jQuery('.b2s-sort-pagination-area').html("").hide();

    jQuery.ajax({
        url: ajaxurl,
        type: "POST",
        dataType: "json",
        cache: false,
        data: {
            'action': 'b2s_sort_data',
            'b2sSortPostTitle': jQuery('#b2sSortPostTitle').val(),
            'b2sSortPostAuthor': jQuery('#b2sSortPostAuthor').val(),
            'b2sSortPostCat': jQuery('#b2sSortPostCat').val(),
            'b2sSortPostType': jQuery('#b2sSortPostType').val(),
            'b2sSortPostSchedDate': jQuery('#b2sSortPostSchedDate').val(),
            'b2sUserAuthId': jQuery('#b2sUserAuthId').val(),
            'b2sType': jQuery('#b2sType').val(),
            'b2sShowByDate': jQuery('#b2sShowByDate').val(),
            'b2sPagination': jQuery('#b2sPagination').val(),
            'b2sSortPostStatus': jQuery('#b2sSortPostStatus').val(),
            'b2sSortPostPublishDate': jQuery('#b2sSortPostPublishDate').val(),
            'b2sUserLang': jQuery('#b2sUserLang').val()
        },
        error: function () {
            jQuery('.b2s-server-connection-fail').show();
            return false;
        },
        success: function (data) {
            if (typeof data === 'undefined' || data === null) {
                jQuery('.b2s-server-connection-fail').show();
                return false;
            }
            if (data.result == true) {
                jQuery('.b2s-loading-area').hide();
                jQuery('.b2s-sort-result-area').show();
                jQuery('.b2s-sort-result-item-area').html(data.content).show();
                jQuery('.b2s-sort-pagination-area').html(data.pagination).show();
                if (jQuery('#b2sType').val() == "sched") {
                    if (sched_dates != false) {
                        jQuery('#b2sCalendarSchedDates').val(data.schedDates);
                        jQuery('#b2s-sched-datepicker-area').datepicker('destroy');
                        getB2SSchedDatepicker();
                        return false;
                    }
                }
            } else {
                jQuery('.b2s-server-connection-fail').show();
                return false;
            }
        }
    });
}

jQuery(document).on('click', '.b2sDetailsSchedPostBtn', function () {
    var postId = jQuery(this).attr('data-post-id');
    var showByDate = jQuery(this).attr('data-search-date');
    var userAuthId = jQuery('#b2sUserAuthId').val();
    if (!jQuery(this).find('i').hasClass('isload')) {
        jQuery('.b2s-server-connection-fail').hide();
        jQuery.ajax({
            url: ajaxurl,
            type: "POST",
            dataType: "json",
            cache: false,
            data: {
                'action': 'b2s_sched_post_data',
                'postId': postId,
                'showByDate': showByDate,
                'userAuthId': userAuthId
            },
            error: function () {
                jQuery('.b2s-server-connection-fail').show();
                return false;
            },
            success: function (data) {
                if (data.result == true) {
                    jQuery('.b2s-post-sched-area[data-post-id="' + data.postId + '"]').html(data.content);
                }
            }
        });
        jQuery(this).find('i').removeClass('glyphicon-chevron-down').addClass('glyphicon-chevron-up').addClass('isload').addClass('isShow');
    } else {
        if (jQuery(this).find('i').hasClass('isShow')) {
            jQuery('.b2s-post-sched-area[data-post-id="' + postId + '"]').hide();
            jQuery(this).find('i').removeClass('isShow').addClass('isHide').removeClass('glyphicon-chevron-up').addClass('glyphicon-chevron-down');
        } else {
            jQuery('.b2s-post-sched-area[data-post-id="' + postId + '"]').show();
            jQuery(this).find('i').removeClass('isHide').addClass('isShow').removeClass('glyphicon-chevron-down').addClass('glyphicon-chevron-up');
        }
    }

});
jQuery(document).on('click', '.b2sDetailsPublishPostTriggerLink', function () {
    jQuery(this).parent().prev().find('button').trigger('click');
    return false;
});
jQuery(document).on('click', '.b2sDetailsSchedPostTriggerLink', function () {
    jQuery(this).parent().prev().find('button').trigger('click');
    return false;
});
jQuery(document).on('click', '.checkbox-all', function () {
    if (jQuery('.checkbox-all').is(":checked")) {
        jQuery('.checkboxes[data-blog-post-id="' + jQuery(this).attr('data-blog-post-id') + '"]').prop("checked", true);
    } else {
        jQuery('.checkboxes[data-blog-post-id="' + jQuery('.checkbox-all').attr('data-blog-post-id') + '"]').prop("checked", false);
    }
});
jQuery(document).on('click', '.checkbox-post-sched-all-btn', function () {
    var checkboxes = jQuery('.checkboxes[data-blog-post-id="' + jQuery(this).attr('data-blog-post-id') + '"]:checked');
    if (checkboxes.length > 0) {
        var items = [];
        jQuery(checkboxes).each(function (i, selected) {
            items[i] = jQuery(selected).val();
        });
        jQuery('#b2s-delete-confirm-post-id').val(items.join());
        jQuery('#b2s-delete-confirm-post-count').html(items.length);
        jQuery('.b2s-delete-sched-modal').modal('show');
        jQuery('.b2s-sched-delete-confirm-btn').prop('disabeld', false);
    }
});
jQuery(document).on('click', '.b2s-post-sched-area-drop-btn', function () {
    jQuery('#b2s-delete-confirm-post-id').val(jQuery(this).attr('data-post-id'));
    jQuery('#b2s-delete-confirm-post-count').html('1');
    jQuery('.b2s-delete-sched-modal').modal('show');
    jQuery('.b2s-sched-delete-confirm-btn').prop('disabeld', false);
});
jQuery(document).on('click', '.b2s-sched-delete-confirm-btn', function () {
    jQuery('.b2s-post-remove-fail').hide();
    jQuery('.b2s-post-remove-success').hide();
    jQuery('.b2s-sched-delete-confirm-btn').prop('disabeld', true);
    jQuery('.b2s-server-connection-fail').hide();
    jQuery.ajax({
        url: ajaxurl,
        type: "POST",
        dataType: "json",
        cache: false,
        data: {
            'action': 'b2s_delete_user_sched_post',
            'postId': jQuery('#b2s-delete-confirm-post-id').val()
        },
        error: function () {
            jQuery('.b2s-server-connection-fail').show();
            return false;
        },
        success: function (data) {
            jQuery('.b2s-delete-sched-modal').modal('hide');
            if (data.result == true) {
                var count = parseInt(jQuery('.b2s-sched-count[data-post-id="' + data.blogPostId + '"]').html());
                var newCount = count - data.postCount;
                jQuery('.b2s-sched-count[data-post-id="' + data.blogPostId + '"]').html(newCount);
                if (newCount >= 1) {
                    jQuery.each(data.postId, function (i, id) {
                        jQuery('.b2s-post-sched-area-li[data-post-id="' + id + '"]').remove();
                    });
                } else {
                    jQuery('.b2s-post-sched-area-li[data-post-id="' + data.postId[0] + '"]').closest('ul').closest('li').remove();
                }
                jQuery('.b2s-post-remove-success').show();
            } else {
                jQuery('.b2s-post-remove-fail').show();
            }
            wp.heartbeat.connectNow();
            return true;
        }
    });
});
jQuery(document).on('click', '.checkbox-post-publish-all-btn', function () {
    var checkboxes = jQuery('.checkboxes[data-blog-post-id="' + jQuery(this).attr('data-blog-post-id') + '"]:checked');
    if (checkboxes.length > 0) {
        var items = [];
        jQuery(checkboxes).each(function (i, selected) {
            items[i] = jQuery(selected).val();
        });
        jQuery('#b2s-delete-confirm-post-id').val(items.join());
        jQuery('#b2s-delete-confirm-post-count').html(items.length);
        jQuery('.b2s-delete-publish-modal').modal('show');
        jQuery('.b2s-publish-delete-confirm-btn').prop('disabeld', false);
    }
});
jQuery(document).on('click', '.b2s-post-publish-area-drop-btn', function () {
    jQuery('#b2s-delete-confirm-post-id').val(jQuery(this).attr('data-post-id'));
    jQuery('#b2s-delete-confirm-post-count').html('1');
    jQuery('.b2s-delete-publish-modal').modal('show');
    jQuery('.b2s-publish-delete-confirm-btn').prop('disabeld', false);
});
jQuery(document).on('click', '.b2s-publish-delete-confirm-btn', function () {
    jQuery('.b2s-post-remove-fail').hide();
    jQuery('.b2s-post-remove-success').hide();
    jQuery('.b2s-publish-delete-confirm-btn').prop('disabeld', true);
    jQuery('.b2s-server-connection-fail').hide();
    jQuery.ajax({
        url: ajaxurl,
        type: "POST",
        dataType: "json",
        cache: false,
        data: {
            'action': 'b2s_delete_user_publish_post',
            'postId': jQuery('#b2s-delete-confirm-post-id').val()
        },
        error: function () {
            jQuery('.b2s-server-connection-fail').show();
            return false;
        },
        success: function (data) {
            jQuery('.b2s-delete-publish-modal').modal('hide');
            if (data.result == true) {
                var count = parseInt(jQuery('.b2s-publish-count[data-post-id="' + data.blogPostId + '"]').html());
                var newCount = count - data.postCount;
                jQuery('.b2s-publish-count[data-post-id="' + data.blogPostId + '"]').html(newCount);
                if (newCount >= 1) {
                    jQuery.each(data.postId, function (i, id) {
                        jQuery('.b2s-post-publish-area-li[data-post-id="' + id + '"]').remove();
                    });
                } else {
                    jQuery('.b2s-post-publish-area-li[data-post-id="' + data.postId[0] + '"]').closest('ul').closest('li').remove();
                }
                jQuery('.b2s-post-remove-success').show();
            } else {
                jQuery('.b2s-post-remove-fail').show();
            }
            wp.heartbeat.connectNow();
            return true;
        }
    });
});
var dateFormat = "yyyy-mm-dd";
var language = "en";
var showMeridian = true;
if (jQuery('#b2sUserLang').val() == "de") {
    dateFormat = "dd.mm.yyyy";
    language = "de";
    showMeridian = false;
}

if (jQuery.isFunction(jQuery.fn.datepicker)) {
    jQuery("#b2s-change-date").datepicker({
        format: dateFormat,
        language: language,
        maxViewMode: 2,
        todayHighlight: true,
        startDate: new Date(),
        calendarWeeks: true,
        autoclose: true
    });

    jQuery("#b2s-change-date").datepicker().on('changeDate', function (e) {
        checkSchedDateTime();
    });

    /*jQuery("#b2s-change-date").datepicker().on('changeDate', function (e) {
     var element = '#b2s-change-time';
     var dateStr = jQuery(this).val();
     var minStr = jQuery(element).val()
     if (jQuery('#b2sUserLang').val() == 'de') {
     dateStr = dateStr.substring(6, 10) + '-' + dateStr.substring(3, 5) + '-' + dateStr.substring(0, 2);
     }
     var dateObj = new Date();
     dateObj.setTime(jQuery('#b2s-data-blog-sched-date').val());
     if (Date.parse(dateStr + ' ' + minStr + ':00') <= Date.parse(dateObj.getUTCFullYear() + '-' + (dateObj.getUTCMonth() + 1) + '-' + dateObj.getUTCDate() + ' ' + dateObj.getUTCHours() + ':' + dateObj.getUTCMinutes() + ':00')) {
     //date in past                                        
     if (dateObj.getUTCMinutes() >= 30) {
     jQuery(element).timepicker('setTime', (dateObj.getUTCHours() + 1) + ':00');
     } else {
     jQuery(element).timepicker('setTime', (dateObj.getUTCHours()) + ':30');
     }
     }
     });*/
}



if (jQuery.isFunction(jQuery.fn.timepicker)) {
    jQuery('#b2s-change-time').timepicker({
        minuteStep: 15,
        appendWidgetTo: 'body',
        showSeconds: false,
        showMeridian: showMeridian,
        defaultTime: 'current',
        snapToStep: true
    });

    jQuery('#b2s-change-time').timepicker().on('changeTime.timepicker', function (e) {
        checkSchedDateTime();
    });

    /*jQuery('#b2s-change-time').timepicker().on('changeTime.timepicker', function (e) {
     var dateStr = jQuery('#b2s-change-date').val();
     var minStr = jQuery(this).val();
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
     var dateObj = new Date();
     dateObj.setTime(jQuery('#b2s-data-blog-sched-date').val());
     if (Date.parse(dateStr + ' ' + minStr + ':00') <= Date.parse(dateObj.getUTCFullYear() + '-' + (dateObj.getUTCMonth() + 1) + '-' + dateObj.getUTCDate() + ' ' + dateObj.getUTCHours() + ':' + dateObj.getUTCMinutes() + ':00')) {
     //date in past                                        
     if (dateObj.getUTCMinutes() >= 30) {
     jQuery(this).timepicker('setTime', (dateObj.getUTCHours() + 1) + ':00');
     } else {
     jQuery(this).timepicker('setTime', (dateObj.getUTCHours()) + ':30');
     }
     }
     });*/
}

jQuery(document).on('click', '.b2s-post-sched-area-edittime-btn', function () {
    jQuery('#b2s-data-blog-sched-date').val(jQuery(this).attr('data-blog-sched-date'));
    jQuery('#b2s-data-b2s-sched-date').val(jQuery(this).attr('data-b2s-sched-date'));
    var dateObjBlog = new Date();
    dateObjBlog.setTime(jQuery('#b2s-data-blog-sched-date').val());
    var dateObj = new Date();
    dateObj.setTime(jQuery('#b2s-data-b2s-sched-date').val());
    jQuery('#b2s-change-date').datepicker('setStartDate', dateObjBlog);
    jQuery('#b2s-change-date').datepicker('setDate', dateObj);
    jQuery('#b2s-change-time').timepicker('setTime', (dateObj.getUTCHours()) + ':' + dateObj.getUTCMinutes());
    jQuery('#b2s-data-post-id').val(jQuery(this).attr('data-post-id'));
    jQuery('.b2s-change-datetime-modal').modal('show');
    jQuery('.b2s-change-date-btn').prop('disabled', false);
});
jQuery(document).on('click', '.b2s-change-date-btn', function () {
    if (jQuery('#b2s-change-date').val() == "") {
        jQuery('#b2s-change-date').addClass('error');
        return false;
    } else {
        jQuery('#b2s-change-date').removeClass('error');
    }

    if (jQuery('#b2s-change-time').val() == "") {
        jQuery('#b2s-change-time').addClass('error');
        return false;
    } else {
        jQuery('#b2s-change-time').removeClass('error');
    }
    jQuery('.b2s-change-date-btn').prop('disabled', true);
    jQuery('.b2s-server-connection-fail').hide();
    jQuery.ajax({
        url: ajaxurl,
        type: "POST",
        dataType: "json",
        cache: false,
        data: {
            'action': 'b2s_update_user_sched_time_post',
            'postId': jQuery('#b2s-data-post-id').val(),
            'time': jQuery('#b2s-change-time').val(),
            'date': jQuery('#b2s-change-date').val(),
            'user_timezone': jQuery("#user_timezone").val()
        },
        error: function () {
            jQuery('.b2s-server-connection-fail').show();
            return false;
        },
        success: function (data) {
            jQuery('.b2s-change-datetime-modal').modal('hide');
            if (data.result == true) {
                jQuery('.b2s-post-sched-area-sched-time[data-post-id="' + data.postId + '"]').html(data.time);
            }
            wp.heartbeat.connectNow();
            return true;
        }
    });
});
function showFilter(typ) {
    if (typ == 'show') {
        jQuery('.filterShow').hide();
        jQuery('.form-inline').show();
        jQuery('.filterHide').show();
    } else {
        jQuery('.filterShow').show();
        jQuery('.form-inline').hide();
        jQuery('.filterHide').hide();
    }
}

function getB2SSchedDatepicker() {

    var language = "en";
    if (jQuery('#b2sUserLang').val() == "de") {
        language = "de";
    }
    var sched_dates = JSON.parse(jQuery('#b2sCalendarSchedDates').val());

    jQuery('#b2s-sched-datepicker-area').datepicker({
        format: "yyyy-mm-dd",
        inline: true,
        language: language,
        calendarWeeks: true,
        todayHighlight: true,
        beforeShowDay: function (date) {
            var d = date;
            var formattedDate = d.getFullYear() + "-" + padDate(d.getMonth() + 1) + "-" + padDate(d.getDate());
            if (sched_dates != "0") {
                if (jQuery.inArray(formattedDate, sched_dates) != -1) {
                    return {classes: 'event'};
                }
            }
            return;
        }
    }).on('changeDate', function (date) {
        if (jQuery('#b2sShowByDate').val() != date.format()) {
            jQuery('#b2sPagination').val("1");
            jQuery('#b2sShowByDate').val(date.format());
            b2sSortFormSubmit(false);
        }
        return false;
    });

    if (jQuery('#b2sShowByDate').val() != "") {
        jQuery('#b2s-sched-datepicker-area').datepicker('setDate', jQuery('#b2sShowByDate').val());
    }

}


function padDate(n) {
    return ("0" + n).slice(-2);
}


function checkSchedDateTime() {
    var dateElement = '#b2s-change-date';
    var timeElement = '#b2s-change-time';
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

