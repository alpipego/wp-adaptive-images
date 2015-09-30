adaptiveImages = {
    checkUrlId: function(idOrUrl) {
        this.genericGet(
            {
                'action': 'ai_check_url_id',
                'idOrUrl': idOrUrl
            },
            function(response) {
                adaptiveImages.adminNoticeSuccess();
                adaptiveImages.getFlickrImage(response);
            }
        );
    },

    adminNoticeError: function(e) {
        var $ = jQuery;
        $(this.target).siblings('.error').remove();
        if (typeof this.valid !== 'undefined' && this.valid.attr('checked') === 'checked') {
            this.valid.click();
        };
        $(this.target).parent().append($('<div>').addClass('error').text(e.msg));
    },

    adminNoticeSuccess: function() {
        jQuery(this.target).siblings('.error').remove();
    },

    getFlickrImage: function(id) {
        this.genericGet(
            {
                'action': 'ai_get_image',
                'id': id
            },
            this.valid.click()
        );
    },

    genericGet: function(data, sucessCallback) {
        var $ = jQuery;
        $.get(
            ajaxurl, 
            data
        )
        .then(function(response) {
            response = $.parseJSON(response);
            if (response.error) {
                return $.Deferred().reject(response.error)
            } else {
                return response;
            }
        })
        .done(sucessCallback)
        .fail(function(err) {
            adaptiveImages.adminNoticeError(err)
        });    
    }
}
// $('.image-container').each(function() {
//     var id = 'img-cont-'+imgIndex++;
//     var imgCont = $(this).attr('id', id);
//     var data = {
//         'action': 'ai_check_url_id',
//         'id': $(this).data('image-id'),
//         'containerWidth': $(this).width(),
//         'pixelRatio': window.devicePixelRatio || Math.round(window.screen.availWidth / document.documentElement.clientWidth)
//     }
    
//     // console.log([ajaxurl, data]);    
//     $.post(ajaxurl, data, function(response) {
//         response = $.parseJSON(response);
//         $('<img>').attr('src', response).on('load', function() {
//             imgCont.append(
//                 $('<div>').addClass('image').css('background-image', 'url(data:image/jpg;base64,'+$(this).attr('src')+')')
//             );
//         });
//     });
// });