function MediaAjaxImageFetch (el) {
    el = $(el);
    panel = el.closest('.media-widget');

    $.ajax({
        method: 'post',
        url: Routing.generate('mykees_media_ajax_fetch_for_model', {model:'SiteUser'}),
        success: function (response, status) {
            el.html(response);
            $('.remove-on-ajax-complete').remove();
            PrepareSelectionActions(el, panel);
        }
    });
}

function PrepareSelectionActions (el, panel) {
    currentId = $('', panel)
    $('.media-option', el).each(function(a, mediaItem){
        mediaItem = $(mediaItem);
        mediaItem.addClass('clickable');
        mediaItem.click({clicked: this, context: panel}, ClickMediaOption);
    });
}

function ClickMediaOption(e) {
    // Don't trigger on label propagation
    if (e.target.tagName.toLowerCase() == 'input') { 
        clicked = e.data.clicked;
        context = e.data.context;

        // Add classes
        $('.media-option', context).removeClass('selected');
        $(clicked).addClass('selected');

        // Update "Current" and "Previous"
        UpdateCurrentAndPreviousMedia(clicked, context.parent());
    }
}

function UpdateCurrentAndPreviousMedia(clicked, context) {
    // Play suffle with the values so user knows what status is
    var previous = $('.previous-image-container', context);
    var current = $('.current-image-container', context);
    
    var previousImg = $('img', previous);
    var currentImg = $('img', current);
    var clickedImg = $('img', clicked);
    
    var clickedValue = $('input:radio', clicked).val();
    var originalValue = $('.original-value', context).val();
    var lastSetValue = $('.last-set-value', context).val();

    var previousName = $('.image-info-text.filename', previous);
    var currentName = $('.image-info-text.filename', current);
    var clickedName = $('.filename', clicked);

    var previousDatetime = $('.image-info-text .upload-datetime', previous);
    var currentDatetime = $('.image-info-text .upload-datetime', current);
    var clickedDatetime = $('.upload-datetime', clicked);

    // Only previous if this is the first item selected
    if (originalValue == lastSetValue) {
        previousImg.attr('src', currentImg.attr('src'));
        previousName.html(currentName.html());
        previousDatetime.html(currentDatetime.html());
    }
    $('.last-set-value', context).val(clickedValue);
    console.log(clickedName);
    console.log(currentName);
    console.log(currentDatetime);
    currentName.html(clickedName.html());
    currentDatetime.html(clickedDatetime.html());
    currentImg.attr('src', clickedImg.attr('src'));

    if (previous.is(':hidden')) {
        previous.slideDown("slow");
    }

}

$(function(){
    $('.media-selection-container').each(function (a, el) {
        MediaAjaxImageFetch(el);
    });
});
