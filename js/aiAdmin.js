    jQuery(document).ready(function($) {
        var initialVal = $('#acf-field_560aabcf8dbef').val();
        $('[data-name="ai_valid"').hide();
        $('#acf-field_560aabcf8dbef').on('paste', function(e) {
            var pasteData = e.originalEvent.clipboardData.getData('text');
            if (initialVal !== pasteData) {
                initialVal = pasteData;
                adaptiveImages.target = e.target;
                adaptiveImages.valid = $('#acf-field_560bb27f970d2-1');
                adaptiveImages.checkUrlId(pasteData);
            };
        });
    });