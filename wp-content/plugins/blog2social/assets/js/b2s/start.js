jQuery.noConflict();

jQuery(document).on('click', '.b2s-mail-btn', function () {
    if (isMail(jQuery('#b2s-mail-update-input').val())) {
        jQuery.ajax({
            url: ajaxurl,
            type: "POST",
            dataType: "json",
            cache: false,
            data: {
                'action': 'b2s_post_mail_update',
                'email': jQuery('#b2s-mail-update-input').val(),
                'lang': jQuery('#user_lang').val()
            }
        });
        jQuery('.b2s-mail-update-area').hide();
        jQuery('.b2s-mail-update-success').show();
    } else {
        jQuery('#b2s-mail-update-input').addClass('error');
    }
    return false;
});


jQuery(window).on("load", function () {
    jQuery('.b2s-faq-area').show();
    if (typeof wp.heartbeat == "undefined") {
        jQuery('#b2s-heartbeat-fail').show();
    }
    jQuery.ajax({
        url: ajaxurl,
        type: "POST",
        dataType: "json",
        cache: false,
        data:{
            'action' : 'b2s_get_faq_entries'
        },
        error: function () {
            jQuery('.b2s-faq-area').hide();
            return false;
        },
        success: function (data) {
            if (data.result == true) {
                jQuery('.b2s-loading-area-faq').hide();
                jQuery('.b2s-faq-content').html(data.content);
            } else {
                jQuery('.b2s-faq-area').hide();
            }
        }
    });

});


function isMail(mail) {
    var regex = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    return regex.test(mail);
}



