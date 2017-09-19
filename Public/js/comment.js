$(function(){
	//设置所有 除登陆，主页的body高度
	$('.bodyHeight').css('height',($(document).outerHeight()+'px'));
	//所有返回 按钮 返回上一级
	$('.backButton').click(function(){
		window.history.back();
	})
	//用户主页加载动画
	
	//界面通用 选择显示的列表
	$('.changeTypeSelect').click(function(){
			if($(this).index() == 0){
				$(this).siblings().removeClass('changeTypeSelect_selected');
				$(this).addClass('changeTypeSelect_selected');
				if($('.selected_1').css('display') == 'block'){
					$('.selectedDiv').slideUp();
					return;
				}
				
				$('.selectedDiv').slideUp();
				$('.selected_1').slideDown();
			}else if($(this).index() == 1){
				$(this).siblings().removeClass('changeTypeSelect_selected');
				$(this).addClass('changeTypeSelect_selected');
				if($('.selected_2').css('display') == 'block'){
					$('.selectedDiv').slideUp();
					return;
				}
				
				$('.selectedDiv').slideUp();
				$('.selected_2').slideDown();
			}else if($(this).index() == 2){
				$(this).siblings().removeClass('changeTypeSelect_selected');
				$(this).addClass('changeTypeSelect_selected');
				if($('.selected_3').css('display') == 'block'){
					$('.selectedDiv').slideUp();
					return;
				}
				
				$('.selectedDiv').slideUp();
				$('.selected_3').slideDown();
			}
	});
	////界面通用 点击时间 显示明细信息	
	$('.details_Data').click(function(){
		$(this).parent().find('.details_MessageRow').slideToggle();
	})
	//界面通用 点击input 显示选择列表
	$('.forInputSelect').click(function(){
		$('.inputSelectOuterDiv').css({'display':'block'});
		setTimeout(function(){
			$('.inputSelectOuterDiv').css({'width':'100%'});
		},500);
		
	});
	$('.inputSelecList').click(function(){
		
		$('.forInputSelect').val($(this).find('.inputSelectValue').text());	
		$('.inputSelectOuterDiv').css({'width':'0%'});
		setTimeout(function(){
			if($('.inputSelectOuterDiv').css('width') == '0px'){
				$('.inputSelectOuterDiv').css({'display':'none'});
			}
		},2500);
	})
	

});






