jQuery(document).ready(function($) {
    var imgIndex = 1;

    $('.ai-wrapper').each(function() {
        $(this).append(
            $('<div>').addClass('la-ball-scale-ripple-multiple loading-animation')
        );
        for (var i = 0; i < 3; i++) {
            $('<div>').appendTo($(this).children('.loading-animation'));
        };
        var id = 'img-cont-'+imgIndex++;
        var imgCont = $(this).attr('id', id);
        var data = {
            'action': 'ai_load_image',
            'id': $(this).data('image-id'),
            'service': $(this).data('image-service'),
            'width': (window.devicePixelRatio || Math.round(window.screen.availWidth / document.documentElement.clientWidth)) * $(this).width()
        }


        $.getJSON(ajaxurl, data, function(response) {
            if (!data.service) {
                return;
            }

            // console.log({data, response, imgCont});
            var image = new Image();
            image.src = response;

            image.onload = function() {
                imgCont.addClass('image-container').append(
                    $('<div>').addClass('image').css('background-image', 'url('+$(this).attr('src')+')')
                );
                imgCont.trigger('aiLoaded');
                setTimeout(function() {
                    imgCont.children('.loading-animation').fadeOut('2000', function() {
                        $(this).remove();
                    });;
                });
            };

            image.onerror = function() {
                console.log(data);
            };
        });
    });
});
