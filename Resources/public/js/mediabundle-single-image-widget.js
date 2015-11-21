function MediaAjaxImageFetch () {
    $.ajax({
        method: 'post',
        url: '/media/widget/SiteUser',
        success: function (a, b) {
            console.log("success");
            console.log(a);
            console.log(b);
        }
    });
}

$(function(){
    MediaAjaxImageFetch();
});
