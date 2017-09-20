$(function(){
    $('.liTitle').click(function(){
        if($(this).hasClass('liTitleActive')){

            return;
        }
        $('.liTitle').removeClass('liTitleActive');
        $(this).addClass('liTitleActive');
        $('.ulDiv ').slideUp();
        $(this).next().slideDown(1000);
    });
    $('.ulDiv li').click(function(){
        $('.ulDiv li').removeClass('liActive');
        $(this).addClass('liActive');
    });
    /*点击控制左侧控制列表的收缩*/
    $('#controDiv').click(function(){

        var divWidth=$('.leftControle').css('width');
        if(divWidth != '0px'){
            $(this).removeClass('leftControDiv');
            $(this).addClass('rightControDiv');
            $('.leftControle').css({
                'width':'0px',
                'transition':'1s'
            });
            $('.rightPageChange').css({
                'width':'100%',
                'transition':'1s'
            });
        }else {
            $(this).addClass('leftControDiv');
            $(this).removeClass('rightControDiv');
            $('.leftControle').css({
                'width':'15%',
                'transition':'1s'
            });
            $('.rightPageChange').css({
                'width':'84.5%',
                'transition':'1s'
            });
        }} );
    /*
	*
	*点击切换右侧显示页面
	*
	**/
	//会员管理功能
    $('.testOne').click(function(){
        $('iframe').attr('src','index.php?s=/super/Index/administrationPage.html');
    });
    $('.testTwo').click(function(){
        $('iframe').attr('src','index.php?s=/super/Index/updateMessagePage.html');
    });
    //公告选项
	$(".addNoticePage").click(function(){
        $('iframe').attr('src','index.php?s=/super/Index/addNoticePage.html');

    });
	$('.noticeListPage').click(function(){
        $('iframe').attr('src','index.php?s=/super/Index/noticeListPage.html');
    });
	$('.emailToUserPage').click(function(){
        $('iframe').attr('src','index.php?s=/super/Index/emailToUserPage.html');
    });
	//参数概率
	$('.functionValueReset').click(function(){
		$('iframe').attr('src','index.php?s=/super/Index/functionValueResetPage.html');									
	});
	$('.operationLog').click(function(){
		$('iframe').attr('src','index.php?s=/super/Index/operationLogPage.html');							  
	 });
	$('.helpDocument').click(function(){
		$('iframe').attr('src','index.php?s=/super/Index/helpDocumentList.html');
	});
	//参数概率-点击查看文章
	$('.documentSelect').click(function(){
			$('.documentReadonly').slideDown();
			$('.documentClose').show();
	});
	$('.documentClose').click(function(){
			$('.documentReadonly').slideUp();
			$('.documentClose').hide();					   
	});
	//参数概率-点击修改文章
	$('.documentUpdata').click(function(){
			$('.documentRechange').slideDown();
			$('.documentChange').show();
	});
	$('.documentChange').click(function(){
			$('.documentRechange').slideUp();
			$('.documentChange').hide();					   
	});
	//后台管理
	$('.websiteSwitch').click(function(){
		$('iframe').attr("src","index.php?s=/super/Index/websiteSwitchPage.html");
	});
	//管理员选项
	$('.adminAppend').click(function(){
		 $('iframe').attr('src','index.php?s=/super/Index/adminAppendPage.html');							 
	});
	$('.adminList').click(function(){
		 $('iframe').attr('src','index.php?s=/super/Index/adminListPage.html');							 
	});
	/**
	*
	*用户列表 下方 切换数据显示页面 
	*
	*/
	// //接受传来的 页数值 动态添加底部切换页码
	// var userMessageSpanLength = 9;
	//
	// //循环添加页码
	// for(var i = userMessageSpanLength; i > 0; i--){
	// 	$('.userMessageUp').after("<span>"+ i +"</span>");
	// }
	// //为第一个页码添加高亮样式
	// $('.userMessageUp').next().addClass('bottomPageSelectActive');
	// //点击左右箭头 切换页码高亮
	// //获取 高亮元素的下标
	// var pageNumber = $('.bottomPageSelect span');
	// var pageNumberIndex;
	// //向上切换
	// $('.userMessageUp').click(function(){
	// 	for(var i=0; i < pageNumber.length; i++){
	// 		if($(pageNumber[i]).hasClass('bottomPageSelectActive')){
	// 			pageNumberIndex = 	$(pageNumber[i]).index();
	// 		}
	// 	}
	// 	//如果等于1 则前面再无页码 退出方法
	// 	if(pageNumberIndex == 1){
	// 		return;
	// 	}else{
	// 		//首先移除已有高亮样式
	// 		$(pageNumber).removeClass('bottomPageSelectActive');
	// 		//添加高亮样式
	// 		$(pageNumber[pageNumberIndex-1]).addClass('bottomPageSelectActive');
	// 	}
	// });
	// //向下切换
	// $('.userMessageDown').click(function(){
	//
	// 	for(var i=0; i < pageNumber.length; i++){
	// 		if($(pageNumber[i]).hasClass('bottomPageSelectActive')){
	// 			pageNumberIndex = 	$(pageNumber[i]).index();
	// 		}
	// 	}
	// 	//如果等于最大页码 则后面再无页码 退出方法 userMessageSpanLength为接收的最大页码值
	// 	if(pageNumberIndex == userMessageSpanLength){
	// 		return;
	// 	}else{
	// 		//首先移除已有高亮样式
	// 		$(pageNumber).removeClass('bottomPageSelectActive');
	// 		//添加高亮样式
	// 		$(pageNumber[pageNumberIndex+1]).addClass('bottomPageSelectActive');
	// 	}
	// });
	
	
	
})