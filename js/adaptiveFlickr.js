/*
 * BG Loaded
 * 
 *
 * Copyright (c) 2014 Jonathan Catmull
 * Licensed under the MIT license.
 */
 
 (function($){
    $.fn.bgLoaded = function() {

        var self = this;

        var bg = $(this).css('background-image');

        if (bg) {
            var img = bg.replace(/^url\(["']?/, '').replace(/["']?\)$/, '');
            $('<img/>').attr('src', img).load(function() {
                // $(this).remove(); // prevent memory leaks
                return 'loaded';
            });
        }
    }
})(jQuery);

var $ = jQuery;
var imgIndex = 1;

$('.image-container').each(function() {
    var id = 'img-cont-'+imgIndex++;
    var imgCont = $(this).attr('id', id);
    var data = {
        'action': 'ai_load_image',
        'id': $(this).data('image-id'),
        'containerWidth': $(this).width(),
        'pixelRatio': window.devicePixelRatio || Math.round(window.screen.availWidth / document.documentElement.clientWidth)
    }
    
    // console.log([ajaxurl, data]);    
    $.post(ajaxurl, data, function(response) {
        response = $.parseJSON(response);
        $('<img>').attr('src', response).on('load', function() {
            imgCont.append(
                $('<div>').addClass('image').css('background-image', 'url(data:image/jpg;base64,'+$(this).attr('src')+')')
            );
        });
    });
});
