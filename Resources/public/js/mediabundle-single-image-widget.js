function MediaAjaxImageFetch () {
    $.ajax({
        method: 'post',
        url: Routing.generate('mykees_media_ajax_fetch_for_model', {model:'SiteUser'}),
        success: function (response, status) {
            $('.media-selection-container').html(response);
        }
    });
}

$(function(){
    MediaAjaxImageFetch();
});
