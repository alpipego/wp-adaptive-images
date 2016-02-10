var adaptiveImages = function(target, id, authorUrl, licenseUrl, authorName) {

    this.target = target;
    this.id = id;
    this.authorUrl = authorUrl;
    this.licenseUrl = licenseUrl;
    this.authorName = authorName;

    this.checkUrlId = function(url) {
        var ai = this;
        this.genericGet(
            {
                'action': 'ai_validate_url',
                'url': url
            },
            function(response) {
                ai.adminNoticeSuccess();
                ai.id.val(response.service+';'+response.id);
                ai.getImage(response);
            }
        );
    };

    this.adminNoticeError = function(e) {
        var $ = jQuery;
        $(this.target).siblings('.error').remove();
        this.hideElements();
        $(this.target).parent().append($('<div>').addClass('error').text(e.msg));
    };

    this.adminNoticeSuccess = function() {
        jQuery(this.target).siblings('.error').remove();
    };

    this.hideElements = function() {
        this.authorUrl.val('');
        this.authorUrl.closest('.acf-field').hide();
        this.licenseUrl.val('');
        this.licenseUrl.closest('.acf-field').hide();
        this.authorName.val('');
        this.authorName.closest('.acf-field').hide();
    };

    this.getImage = function(image) {
        var ai = this;

        this.genericGet(
            {
                'action': 'ai_get_image',
                'image': image
            },
            function(response) {
                var $ = jQuery;

                if (ai.target.closest('.ai-preview-image') && ai.target.closest('.ai-preview-image').length > 0) {
                    ai.target.closest('.ai-preview-image img').attr('src', response.image);
                } else {
                    $(ai.target).closest('.acf-field').after($('<div>').addClass('ai-preview-image acf-field').html('<img src="'+response.image+'" width="100%" />'));
                }
                var url = $(ai.target).val();
                $(ai.target)
                    .hide()
                    .before($('<a>').attr({
                            href: url,
                            target: '_blank'
                        }).text('View Original')
                    );

                ai.licenseUrl.val(response.licenseUrl);
                ai.licenseUrl.parents('.acf-field').show();
                ai.authorUrl.val(response.userUrl);
                ai.authorUrl.parents('.acf-field').show();
                ai.authorName.val(response.userName);
                ai.authorName.parents('.acf-field').show();
            }
        );
    };

    this.genericGet = function(data, sucessCallback) {
        var $ = jQuery;
        var ai = this;
        $(document)
            .on('ajaxStart.thisCall', function () {
                $.each($('.spinner.acf-field'), function() {
                    $(this).remove();
                });
                $(ai.target).closest('.acf-field').after($('<div>').addClass('spinner acf-field is-active'));
            })
            .on('ajaxStop.thisCall', function () {
                $('.spinner').remove();
            });

        $(document).unbind('.thisCall');

        $.getJSON(
            ajaxurl,
            data
        )
            .then(function(response) {
                ai.hideElements();
                if (response.error) {
                    return $.Deferred().reject(response.error)
                } else {
                    return response;
                }
            })
            .done(sucessCallback)
            .fail(function(err) {
                ai.adminNoticeError(err)
            });
    };
}
