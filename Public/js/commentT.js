$(function () {
    $('.liTitle').click(function () {
        if ($(this).next().css("display") == "block") {
            $(this).removeClass('liTitleActive');
            $('.ulDiv ').slideUp();
        }else{
            $('.liTitle').removeClass('liTitleActive');
            $(this).addClass('liTitleActive');
            $('.ulDiv ').slideUp();
            $(this).next().slideDown(1000);
        }



    });
    $('.ulDiv li').click(function () {
        $('.ulDiv li').removeClass('liActive');
        $(this).addClass('liActive');
    });
    /*点击控制左侧控制列表的收缩*/
    $('#controDiv').click(function () {

        var divWidth = $('.leftControle').css('width');
        if (divWidth != '0px') {
            $(this).removeClass('leftControDiv');
            $(this).addClass('rightControDiv');
            $('.leftControle').css({
                'width': '0px',
                'transition': '1s'
            });
            $('.rightPageChange').css({
                'width': '99%',
                'transition': '1s'
            });
        } else {
            $(this).addClass('leftControDiv');
            $(this).removeClass('rightControDiv');
            $('.leftControle').css({
                'width': '15%',
                'transition': '1s'
            });
            $('.rightPageChange').css({
                'width': '84%',
                'transition': '1s'
            });
        }
    });
    /*
     *
     *点击切换右侧显示页面
     *
     **/
    //会员管理功能
    $('.testOne').click(function () {
        $('iframe').attr('src', 'index.php?s=/super/UserAdministration/administrationPage.html');
    });
    $('.testTwo').click(function () {
        $('iframe').attr('src', 'index.php?s=/super/Index/updateMessagePage.html');
    });
    $('.userRecommendStructure').click(function () {
        $('iframe').attr('src', 'index.php?s=/super/UserAdministration/userRecommendStructure.html');
    });
    $(".agentExamine").click(function(){
        $('iframe').attr('src', 'index.php?s=/super/UserAdministration/agentExamine.html');
    });
    $(".regionAgent").click(function(){
        $('iframe').attr('src', 'index.php?s=/super/UserAdministration/regionAgent.html');
    });
    $(".buyCarApply").click(function(){
        $('iframe').attr('src', 'index.php?s=/super/UserAdministration/buyCarApply.html');
    });
    $(".deadAgent").click(function(){
        $('iframe').attr('src', 'index.php?s=/super/UserAdministration/deadAgent.html');
    });
    //财富汇总明细
    $('.userDetails').click(function () {
        $('iframe').attr('src', 'index.php?s=/super/WealthDetailed/userDetails.html');
        location.href = "";
    });
    $('.userActivationDetails').click(function () {
        $('iframe').attr('src', 'index.php?s=/super/WealthDetailed/userActivationDetails.html');

    });
    $('.userChargeHistory').click(function () {
        $('iframe').attr('src', 'index.php?s=/super/WealthDetailed/userChargeHistory.html');
    });
    $('.userCapitalOffset').click(function () {
        $('iframe').attr('src', 'index.php?s=/super/WealthDetailed/userCapitalOffset.html');
    });
    $('.remittanceHistory').click(function () {
        $('iframe').attr('src', 'index.php?s=/super/WealthDetailed/remittanceHistory.html');

    });
    $('.cashHistory').click(function () {
        $('iframe').attr('src', 'index.php?s=/super/WealthDetailed/cashHistory.html');
    });
    $('.bonusDetails').click(function () {
        $('iframe').attr('src', 'index.php?s=/super/WealthDetailed/bonusDetails.html');

    });
    $('.userTransactionHistory').click(function () {
        $('iframe').attr('src', 'index.php?s=/super/WealthDetailed/userTransactionHistory.html');

    });

    //公告选项
    $(".addNoticePage").click(function () {
        $('iframe').attr('src', 'index.php?s=/super/NoticControl/addNoticePage.html');

    });
    $('.noticeListPage').click(function () {
        $('iframe').attr('src', 'index.php?s=/super/NoticControl/noticeListPage.html');
    });
    $('.emailToUserPage').click(function () {
        $('iframe').attr('src', 'index.php?s=/super/NoticControl/emailToUserPage.html');
    });
    $('.emailToUserListPage').click(function () {
        $('iframe').attr('src', 'index.php?s=/super/NoticControl/emailToUserListPage.html');

    });
    //参数概率
    $('.functionValueReset').click(function () {
        $('iframe').attr('src', 'index.php?s=/super/ParameterProbability/functionValueResetPage.html');
    });
    $('.operationLog').click(function () {
        $('iframe').attr('src', 'index.php?s=/super/ParameterProbability/operationLogPage.html');
    });
    $('.helpDocument').click(function () {
        $('iframe').attr('src', 'index.php?s=/super/ParameterProbability/helpDocumentList.html');
    });
    $('.addHelpDocument').click(function () {
        $('iframe').attr('src', 'index.php?s=/super/ParameterProbability/addHelpDocument.html');

    })
    //参数概率-点击查看文章
    $('.documentSelect').click(function () {
        $('.documentReadonly').slideDown();
        $('.documentClose').show();
    });
    $('.documentClose').click(function () {
        $('.documentReadonly').slideUp();
        $('.documentClose').hide();
    });
    //参数概率-点击修改文章
    $('.documentUpdata').click(function () {
        $('.documentRechange').slideDown();
        $('.documentChange').show();
    });
    $('.documentChange').click(function () {
        $('.documentRechange').slideUp();
        $('.documentChange').hide();
    });
    //后台管理
    $('.websiteSwitch').click(function () {
        $('iframe').attr("src", "index.php?s=/super/BackstageControl/websiteSwitchPage.html");
    });
    //管理员选项
    $('.adminAppend').click(function () {
        $('iframe').attr('src', 'index.php?s=/super/AdminControl/adminAppendPage.html');
    });
    $('.adminList').click(function () {
        $('iframe').attr('src', 'index.php?s=/super/AdminControl/adminListPage.html');
    });

    //显示列表选择按钮 class:tableSelectButton
    $(".tableSelectButton").click(function(){
        var tableList = $(".tableListDiv");
        $(".tableListDiv").hide();
        if($(this).index() == 0){
            $(tableList[$(this).index()]).fadeIn();
        }
        if($(this).index() == 1){
            $(tableList[$(this).index()]).fadeIn();
        }
        if($(this).index() == 2){
            $(tableList[$(this).index()]).fadeIn();
        }
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