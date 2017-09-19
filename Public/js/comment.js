$(function(){

	$('.bodyHeight').css('height',($(document).outerHeight()+'px'));

	$('.backButton').click(function(){
		window.history.back();
	})

	

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
	////����ͨ�� ���ʱ�� ��ʾ��ϸ��Ϣ	
	$('.details_Data').click(function(){
		$(this).parent().find('.details_MessageRow').slideToggle();
	})
	//����ͨ�� ���input ��ʾѡ���б�
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






