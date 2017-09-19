$(function(){
    var $span = $(".round span");
    var $media = $(".medias .media");
    $span.click(function(){
        var indexs = $(this).index();
        $media.eq(indexs).show().siblings().hide();
        $(this).addClass("on").siblings().removeClass("on");
    });


    $(".news .sleeps .slps li").hover(function(){
        $(this).find("h5").css("color","#8fc31f");
            },function(){
        $(this).find("h5").css("color","#888889");
    });

})


