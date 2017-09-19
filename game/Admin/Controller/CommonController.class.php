<?php
namespace Admin\Controller;
use Think\Controller;
class CommonController extends Controller {
	public function _initialize(){
		   if(!session('sp_user')){
		          $this->redirect('Admin/Login/index');
		   }
		   echo '<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />';
           $db_user=M('user');
           $weekday = 86400 * 7;
           $oldtime = time() - $weekday;
          

           $lockstart=F('lock_start','','./Public/data/');
		//p($lockstart);        


    if(!$_COOKIE['clock_user'] &&  $lockstart['clock_user']){

 			//  注册时间  大于7天	 并且没有开通土地  并且还没有被锁定的
           $uslock = $db_user->where("add_time < $oldtime  and  farm_lock = 1 and lockuser = 0 ")->getField('userid',true);
          
           if($uslock){
		      $lcwhere['userid'] =array('in',$uslock);
		      $lczc7 = $db_user->where($lcwhere)->setField('lockuser','5');
		      if($lczc7){
           		echo '<script>alert("有  '.count($uslock).'  个用户未激活被锁定");</script>';
		      }
           }
			setcookie("clock_user" ,'1',time()+86400);
		}


        

        if(!$_COOKIE['clock_usfarm'] && $lockstart['clock_usfarm']){
           // 当前时间-7天     大于    农田时间 
           $usfarm = $db_user->where(" farm_tout < $oldtime and farm_tout != 0  and  lockuser = 0 ")->getField('userid',true);
           if($usfarm){
		      $lcwhere['userid'] =array('in',$usfarm);
			 $lcntdq = $db_user->where($lcwhere)->setField('lockuser','4');
           		if($lcntdq){
           			echo '<script>alert("有  '.count($usfarm).'  个用户未续费农田锁定");</script>';
           		}
           }
			setcookie("clock_usfarm" ,'1',time()+86400);
		}





		if(!$_COOKIE['clock_play'] && $lockstart['clock_play']){
		//抢单后  24 小时  不打款  锁定用户
		 	  $ystday = time()-86400;
		 	  echo $ysstday;
              $sedb = M('sell');
              $seall = $sedb ->field('sell_id,userid, buy_userid ')->where("sell_state = 1 and sell_time < $ystday and sell_lock = 0 ")->select();
              $gduser =array();
              $buguser = array();
              $listid = array();

              foreach ($seall as $v) {
              		if($v['userid']){
              			$gduser[] =$v['userid'];
              		}
              		if($v['buy_userid']){
              			$buguser[] =$v['buy_userid'];
              		}
              		if($v['sell_id']){
              			$listid[] =$v['sell_id'];
              		}
              }

				//修改定单
			if($seall && $listid ){
				$lswhere['sell_id'] =array('in',$listid);
				$sedb->where($lswhere)->setField('sell_lock','1');
			}
		

		 //锁定  挂单者
           if($seall &&  $gduser ){
		      $lcwhere['userid'] =array('in',$gduser);
		  	 $lcgd = $db_user->where($lcwhere)->setField('lockuser','2');
		      if($lcgd){
           	echo '<script>alert("有  '.count($gduser).'  个用挂单者被锁定");</script>';
		      }
           }

           //锁定抢单者
          if($seall &&  $buguser ){
		      $lcwhere['userid'] =array('in',$buguser);
		      $lcqd = $db_user->where($lcwhere)->setField('lockuser','3');
          	  if($lcgd){
           	echo '<script>alert("有  '.count($buguser).'  个用抢单者被锁定");</script>';
          	  }
           }
			setcookie("clock_play" ,'1',time()+86400);
		}





	}

	//图片上传
	/**
		$path存放的文件夹
	*/
	public function _imgupload($path){
	$config = array(    
		'maxSize'    =>    3145728,
		'rootPath'   =>  './Public/',
		'savePath'   =>    "/{$path}/",
		'saveName'   =>    array('uniqid',''),
		'exts'       =>    array('jpg', 'gif', 'png', 'jpeg'),
		'autoSub'    =>    true,
		'subName'    =>    array('date','Ymd')
	);
	 return  new \Think\Upload($config);
	}


}

