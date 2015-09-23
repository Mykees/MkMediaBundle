jQuery(function($){
    /**
     * STICKY NAVIGATION
     * @type {[type]}
     */
    var $sidebar = $('#aside');
    var $sticky = $('#sticky');
    var $pTop = $sticky.offset().top;

    $(window).resize(function(event){
        var $resize = $(this);

        if($resize.width() > 600){
            stickyMenu($(window),$pTop,$sticky,$sidebar,$resize);
        }else{
            event.preventDefault();
        }
    });
    if($(window).width() > 600){
        stickyMenu($(window),$pTop,$sticky,$sidebar,$(window).resize());
    }

    /**
     * SCROLLSPY
     */
     var sections = [];
     var id = false;
     var $nav = $("#sticky");
     var $nav_link = $nav.find('a');
     var scroll_id = false;
     $nav_link.each(function(){
        sections.push($($(this).attr('href')));
     });
     $(window).scroll(function(e){
        var $scrollTop = $(this).scrollTop() + ($(window).height() / 3);
        for(var i in sections)
        {
            var section = sections[i];
            if($scrollTop > section.offset().top)
            {
                scroll_id = section.attr('id');
            }
        }
        if(scroll_id !== id)
        {
            id= scroll_id;
            $nav_link.removeClass('current');
            $nav.find('a[href="#'+id+'"]').addClass('current');
        }

     });


});

 function stickyMenu($window,$pTop,$sticky,$sidebar,$resize)
 {
    $window.scroll(function(){
        var $this = $(this);

        if($resize.width() > 800)
        {
            if($this.scrollTop() > $pTop)
            {
                $sticky.stop().css({top:$this.scrollTop()-$sidebar.offset().top+90});
            }else{
                $sticky.stop().css({top:$pTop-$sidebar.offset().top});
            }
        }
    });
    if($resize.width > 800)
    {
        if($window.scrollTop() > $pTop)
        {
            $sticky.stop().css({top:$window.scrollTop()-$sidebar.offset().top+90});
        }
    }
 }