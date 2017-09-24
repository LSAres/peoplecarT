<?php
namespace Mobile\Controller;
use Think\Controller;
use Org\Util\String;
class RegusController extends Controller {
	



	//用户登陆
	public function login(){
		// echo  date('Y-m-d H:i:s');
	$this->display();
	}

	public function smsfind() {
		if (IS_POST) {
			$sms_code = I('post.sms_code');
			$trade_code= session('trade_code');
			if ($sms_code != $trade_code) {
				echo "<script>alert('短信验证码错误');history.back();</script>;";
				exit();
			}
			session('trade_code', null); // 清空session

			$password = I('post.password');
			$password_repeat = I('post.password_repeat');
			if ($password != $password_repeat) {
				echo "<script>alert('两次密码不一致');history.back();</script>;";
				exit();
			}
			$mobile = I('post.mobile');
			$UserModel= M('user');
			$user_row = $UserModel->where(array(
				'mobile'=>$mobile
			))->find();
			if (empty($user_row)) {
				echo "<script>alert('手机号码不存在');history.back();</script>;";
				exit();				
			}
			$password = md5(md5($password).$user_row['salt']);
			$UserModel->where(array(
				'userid'=>$user_row['userid']
			))->setField(array(
				'password'=>$password
			));
			echo "<script>alert('密码修改成功');location.href='".U('Regus/login')."'</script>";
			exit();
		} else {
			$this->display('Mobile@Public/template_01');
		}
	}

	public function sendsms() {
	/*header("Content-Type:text/html;charset=utf-8");*/
	$mobile=I('post.mobile');
	  //$trade_code = String::randString(6, 1);
	  $trade_code=rand(100000,999999);
      session('trade_code', $trade_code);
	  $msg['code'] = $trade_code;
		$msg = json_encode($msg);
		
		$url = "http://api.sms.cn/sms/?ac=send&uid=sun2033539&pwd=c1d1e28244ca3b1293dd3bde4725409d&template=405382&mobile=".$mobile."&content=".$msg;		
		/*print_r($url);
			return;*/
		$info =  file_get_contents($url);
		$sns = iconv('GBK', 'UTF-8', $info);
			$sns = json_decode($sns,true);
			if($sns['stat'] == 100){
				$this->ajaxReturn(array(
                'msg' => '获取成功，请注意查收短信',
                'success' => 1
            ));
				return;
			}else{
				$this->ajaxReturn(array(
                'msg' => '获取失败',
                'success' => 1
            ));
		       
				return;
			}
		
		return $info;
     
    }

	public function find(){
		$account = I('request.account');
		if (!empty($account)) {
			$user_row= M('user')->where(array(
				'account'=>$account
			))->find();
			if (empty($user_row)) {
				echo "<script>alert('帐号不存在');history.back();</script>;";
				exit();
			} else {
				$question_row = M('nzmibao')->where(array(
					'userid'=>$user_row['userid']
				))->find();
				if (empty($question_row)) {
					echo "<script>alert('没有设置密保，请联系管理员找回密码');history.back();</script>";
					exit();
				}
				if (IS_POST) {
					$answer_arr = I('post.answer_arr', array());
					foreach ($answer_arr as $key=>$answer) {
						if ($answer != $question_row['answer'.$key]) {
							echo "<script>alert('问题回答错误');history.back();</script>";
							exit();							
						}
					}

					// 重置密码
					$randnum  = mt_rand(100000, 999999);
					$password = md5(md5($randnum).$user_row['salt']);
					M('user')->where(array(
						'userid'=>$user_row['userid']
					))->setField(array(
						'password'=>$password
					));
					echo "<script>alert('密码重置为".$randnum."，请登录后马上修改');location.href='".U('Regus/login')."'</script>";
					exit();					
				}
				if (IS_GET) {
					$this->assign('account', $account);
					$this->assign('question_row', $question_row);
					$this->display('Mobile@Public/template_01');
				}			
			}
		} else {
			$this->display('Mobile@Public/template_01');
		}
	}
	 
	
	//验证码
	public function verify(){
		ob_clean();
		$config =    array(
		'codeSet' =>  '0123456789',   
		'fontSize'    =>    30,    // 验证码字体大小   
		'length'      =>    4,     // 验证码位数    
		'fontttf'     =>   '4.ttf',
		'useCurve'    => false,
		'useNoise'	  => false
		);
		$Verify =     new \Think\Verify($config);
		$Verify->entry();
	}
	

	
	//登陆验证
	public function testid(){
		echo '<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />';
		if(!IS_POST){
			redirect(U(MODULE_NAME.'/Regus/login'));
		}
    	$account=I('post.account');
	    $psw=I('post.password','');
		$udb=M('user');
		$usinfo=$udb->where("account='".$account."'")->find();
        if ($usinfo['lockuser']==1) {
        	echo '<script>alert("您的账号还未激活，请您联系您的推荐人")</script>';
 	        echo '<script>javascript:history.back(-1);</script>';
			exit(); 
        }
		if ($usinfo['lockuser']==2) {
        	echo '<script>alert("您的账号已被冻结，请您联系管理员")</script>';
 	        echo '<script>javascript:history.back(-1);</script>';
			exit(); 
        }

			
		$us_old=md5(md5($psw).$usinfo['salt']);

        if($usinfo){
				
			if($us_old == $usinfo['password']){ 
				session('userid',$usinfo['userid'],1296000);
				session('account',$account,1296000);
			    $browser = cookie('browser');
                redirect(U(MODULE_NAME.'/Index/copyPageTwo'));
			}else{
				echo "<script>alert('密码错误');location.href='".U('Regus/login')."'</script>";
			}
		}else{
			echo "<script>alert('账号错误');location.href='".U('Regus/login')."'</script>";
		}
		
	}
	

	  //每天刷新的收益每天执行一次
   public function dayshouyi(){
   	     
  //============================================
  //ignore_user_abort(); //即使Client断开(如关掉浏览器)，PHP脚本也可以继续执行.
 // set_time_limit(0); // 执行时间为无限制，php默认执行时间是30秒，可以让程序无限制的执行下去
 

    echo "<meta charset='utf-8'>";
         $dbu=M('user');
         $cfbs=M('cfbs');
    	 $dbstore=M('store');
         $dbcfbsjl=M('cfbsjl');
    	 $userid=session('userid');
         $ciInfo=$cfbs->select();
         $uInfo=$dbstore->where('uid='.$userid.'')->find();

         //拆分表比例
         $jccf=$ciInfo[0]['cfbs_value'];//基础拆分倍数
         $hsqcf=$ciInfo[2]['cfbs_value'];//水果农夫拆分倍数
         $zacf=$ciInfo[3]['cfbs_value'];//藏獒拆分倍数
         $dcrcf=$ciInfo[1]['cfbs_value'];//水果农夫拆分倍数
         $zkbl=$ciInfo[4]['cfbs_value'];//总扣比例
         //p($ciInfo);
          
         //用户的道具信息
         $zangao_num=$uInfo['zangao_num'];   //藏獒数量
         $hashiqi_num=$uInfo['hashiqi_num'];  //水果农夫数量
         $dcr_num=$uInfo['dcr_num'];      //水果农夫数量
         //要扣掉的比例
      
         $koubl=$zkbl-$dcr_num*$dcrcf;
          
        //今天最后收益比例
        $totalcf=$jccf+$zacf*$zangao_num+$hsqcf*$hashiqi_num-$koubl;

       
        ($totalcf<0.01)?$totalcf=0.01:$totalcf=$totalcf;

        $huafei_num = $uInfo['huafei_total'];

       $tui_num= $dbu->where('parent_id='.$userid.'')->count();
      /* if($tui_num<3){
			if($uInfo['huafei_total']>=$uInfo['plant_num']*5){
                $totalcf-=0.02;
			}
           if($uInfo['huafei_total']>=$uInfo['plant_num']*4&&$uInfo['huafei_total']<$uInfo['plant_num']*5){
               $totalcf-=0.015;
           }
           if($uInfo['huafei_total']>=$uInfo['plant_num']*3&&$uInfo['huafei_total']<$uInfo['plant_num']*4){
               $totalcf-=0.01;
           }
           if($uInfo['huafei_total']>=$uInfo['plant_num']*2&&$uInfo['huafei_total']<$uInfo['plant_num']*3){
               $totalcf-=0.005;
           }
	   }

        if ($zangao_num == 1) {
	        	if ($huafei_num >= 108000 && $huafei_num < 126000) $totalcf-=0.005;
	        	if ($huafei_num >= 126000 && $huafei_num < 144000) $totalcf-=0.01;
	        	if ($huafei_num >= 144000 && $huafei_num < 162000) $totalcf-=0.015;
	        	if ($huafei_num >= 162000) $totalcf = 0;
        } else {
	        if ($dcr_num == 1) {
	        	if ($huafei_num >= 54000 && $huafei_num < 72000) $totalcf-=0.005;
	        	if ($huafei_num >= 72000 && $huafei_num < 90000) $totalcf-=0.01;
	        	if ($huafei_num >= 90000 && $huafei_num < 108000) $totalcf-=0.015;
	        	if ($huafei_num >= 108000) $totalcf = 0;
	        }

	        if ($dcr_num == 2) {
	        	if ($huafei_num >= 72000 && $huafei_num < 90000) $totalcf-=0.005;
	        	if ($huafei_num >= 90000 && $huafei_num < 108000) $totalcf-=0.01;
	        	if ($huafei_num >= 108000 && $huafei_num < 126000) $totalcf-=0.015;
	        	if ($huafei_num >= 126000) $totalcf = 0;
	        }

	        if ($dcr_num == 3) {
	        	if ($huafei_num >= 90000 && $huafei_num < 108000) $totalcf-=0.005;
	        	if ($huafei_num >= 108000 && $huafei_num < 126000) $totalcf-=0.01;
	        	if ($huafei_num >= 126000 && $huafei_num < 144000) $totalcf-=0.015;
	        	if ($huafei_num >= 144000) $totalcf = 0;
	        }

            if ($dcr_num == 4) {
                if ($huafei_num >= 90000 && $huafei_num < 108000) $totalcf-=0.005;
                if ($huafei_num >= 108000 && $huafei_num < 126000) $totalcf-=0.01;
                if ($huafei_num >= 126000 && $huafei_num < 144000) $totalcf-=0.015;
                if ($huafei_num >= 144000) $totalcf = 0;
            }
        }*/

        //向用户仓库添加肥料  
        $total_fl=$uInfo['plant_num']*$totalcf;//今天产生的总肥料

        
        
        $dbstore->where('uid='.$userid)->setField('huafei_num',$total_fl);
         
        
        //用户直接推荐了多少会员 
        $zthy=$dbu->where('parent_id='.$userid)->getField('userid',true);
        $hyNum=count($zthy); 
        $data=array();
        //把数据存入农场生长记录表 
         $data['u_id']=$userid;
         $data['zthy']=$hyNum; 
         $data['huiliao']=$total_fl;
         $data['jccf']=$jccf;//	基础拆分
         $data['hscf']=$hsqcf*$hashiqi_num; //水果农夫拆分
         $data['zacf']=$zacf*$zangao_num;//藏獒拆分
         $data['dcrcf']=$dcr_num*$dcrcf;//水果农夫拆分
		 $data['kccf']=-$koubl ; //扣除拆分
		 $data['zcf']= $totalcf;  //总拆分
		 $data['time']= time(); //时间

		 $dbcfbsjl->data($data)->add();
         //echo  $dbcfbsjl->_sql();

   }

   public function implement(){
       $db_cfbs = M('cfbs');
       $db_cfbsrq = M('cfbsrq');
       $cfbs_value = $db_cfbs->where('cfbs_id=1')->getField('cfbs_value');
       $data['date'] = date('Y-m-d',time());
       $data['value'] = $cfbs_value;
       $db_cfbsrq->add($data);
   }


	//用户注册
	public function reglist(){
		$this->display();
	}
	

	//用户退出 
	public function logout(){
        $userid=session('userid');
        if ($userid==null){
            redirect(U('Mobile/Regus/login'));
		}
        $db_store = M('store');
        $fruit_last = $db_store->where('uid='.$userid)->getField('fruit_total');
        $data['fruit_last']=$fruit_last;
        $db_store->where('uid='.$userid)->save($data);
		      session('userid',null);
		      session('mobile',null);
		$browser = cookie('browser');
		if ($browser == 'pc') {
			redirect(U('Pc/Index/login'));
		} else {
			redirect(U('Mobile/Regus/login'));
		}
	}
	

	
	public function zhuce(){
		if(!IS_POST){
			$this->display();
		}else{
			
			$information = I('post.');
			foreach($information as $k){
				if($k==null){
					echo "<script>alert('请完整填写信息');</script>";
                 	 echo "<script>javascript:history.back(-1);</script>";die;
				}
			}
			
			$parent_account = $information['parent_account'];
			$parent_id = M('user')->where("account='".$parent_account."'")->getField('userid');
			if(!$parent_id){
					 echo "<script>alert('推荐人不存在');</script>";
                 	 echo "<script>javascript:history.back(-1);</script>";die;
			}		
			
			
			if($information['password']!=$information['passwordT']){
					 echo "<script>alert('两次输入的密码不一致');</script>";
                 	 echo "<script>javascript:history.back(-1);</script>";die;
			}
			
			
			if($information['paypassword']!=$information['paypasswordT']){
					 echo "<script>alert('两次输入的密码不一致');</script>";
                 	 echo "<script>javascript:history.back(-1);</script>";die;
			}
			
			$account = $information['account'];
			$ishave_account = M('user')->where("account='".$account."'")->find();
			$ishave_mobile = M('user')->where("mobile='".$account."'")->find();
	
			if(!empty($ishave_account)||!empty($ishave_mobile)){
				echo '<script>alert("账号名已经存在，请重新输入");</script>';
                echo '<script>javascript:history.back(-1);</script>';
                exit();
			}
			
			
			if($information['wx_no']!=0){
				$wx_no = $information['wx_no'];
				$ishave_wx_no=M('user')->where("wx_no='".$wx_no."'")->find();
				if(!empty($ishave_wx_no)){
					echo '<script>alert("微信号已经存在，请重新输入");</script>';
					echo '<script>javascript:history.back(-1);</script>';
					exit();
				}
			}
			
			if($information['alipay']!=0){
				$alipay = $information['alipay'];
				$ishave_wx_no=M('user')->where("alipay='".$alipay."'")->find();
				if(!empty($ishave_wx_no)){
					echo '<script>alert("支付宝号已经存在，请重新输入");</script>';
					echo '<script>javascript:history.back(-1);</script>';
					exit();
				}
			}
			
			//=============登录密码加密==============
        	$salt= substr(md5(time()),0,3);
        	$password=md5(md5(trim($information['password'])).$salt);
        
			
        	//=============安全密码加密==============
        	$safety_salt= substr(md5(time()),0,3);
        	$paypassword=md5(md5(trim($information['paypassword'])).$safety_salt); 
			
			$registerInfo=array(
                'account'        =>trim($information['account']),
                'parent_id'      =>$parent_id,
                'username'       =>trim($information['username']),
                'sex'            =>1,
                'mobile'         =>trim($information['account']),
                'wx_no'          =>trim($information['wx_no']),
                'alipay'         =>trim($information['alipay']), 
                'password'       =>$password,
                'salt'           =>$salt,
                'safety_pw'      =>$paypassword,
                'safety_salt'    =>$safety_salt,
				'lockuser'		 =>1,
                'add_time'		 =>time(),
                'ip'			 =>get_client_ip(), 
                'note'			 =>0,
                'limits'		 =>100000, 
				'last_login'	 =>0,

            );
			//添加用户数据
			$zhuce=M('user')->data($registerInfo)->add();
			
			//检查是否添加数据成功
			$check_zhuce=M('user')->where("account='".$registerInfo['account']."'")->find();
			if($check_zhuce){
				$db_store = M('store');
				$db_farm = M('nzusfarm');
				for ($i=1; $i <=15 ; $i++) { 
                     $datafarm['u_id']=$check_zhuce['userid'];
                     $datafarm['f_id']=$i; 
                     if ($i<=5) {
                          $datafarm['farm_type']=1;  
                      }else if(5<$i && $i<=10) {
                          $datafarm['farm_type']=2;
                      }else{
                          $datafarm['farm_type']=3;
                      }
                     //============给用户开启6块农田=========
                     $f_bool=$db_farm->data($datafarm)->add();  
                } 
				//============把用户数据放到仓库表=========
             	$datastore['uid']=$check_zhuce['userid'];  
             	$datastore['cangku_num']=0; 
             	$ckInfo=$db_store->where("uid=".$check_zhuce['userid'])->find();

             	if (!$ckInfo) {
                 	$s_bool=$db_store->data($datastore)->add();
             	}else{
                  	echo "<script>alert('仓库表有问题请联系管理员');</script>";
					
					echo '<script>javascript:history.back(-1);</script>';
                  	exit();
             	}
				
				
				echo "<script>alert('注册成功');</script>";
				echo '<script>javascript:history.back(-1);</script>';
                exit();   
			}else{
				echo "<script>alert('注册失败');</script>";
				echo '<script>javascript:history.back(-1);</script>';
                exit();
			}
			
		}
	}
	
	public function mnmn(){
		$information = I('post.');
			foreach($information as $k){
				if($k==null){
				
			
					echo "<script>alert('请完整填写信息');</script>";
                 	 echo "<script>javascript:history.back(-1);</script>";die;
				}
			}
			
			$parent_account = $information['parent_account'];
			$parent_id = M('user')->where("account='".$parent_account."'")->getField('userid');
			if(!$parent_id){
		
					 echo "<script>alert('推荐人不存在');</script>";
                 	 echo "<script>javascript:history.back(-1);</script>";die;
			}		
			
			
			if($information['password']!=$information['passwordT']){
			
			
					 echo "<script>alert('两次输入的密码不一致');</script>";
                 	 echo "<script>javascript:history.back(-1);</script>";die;
			}
			
			
			if($information['paypassword']!=$information['paypasswordT']){
			
					 echo "<script>alert('两次输入的密码不一致');</script>";
                 	 echo "<script>javascript:history.back(-1);</script>";die;
			}
			
			$account = $information['account'];
			$ishave_account = M('user')->where("account='".$account."'")->find();
			$ishave_mobile = M('user')->where("mobile='".$account."'")->find();
	
			if(!empty($ishave_account)||!empty($ishave_mobile)){
			
				echo '<script>alert("账号名已经存在，请重新输入");</script>';
                echo '<script>javascript:history.back(-1);</script>';
                exit();
			}
			
			
			if($information['wx_no']!="无"){
				$wx_no = $information['wx_no'];
				$ishave_wx_no=M('user')->where("wx_no='".$wx_no."'")->find();
				if(!empty($ishave_wx_no)){
				
					echo '<script>alert("微信号已经存在，请重新输入");</script>';
					echo '<script>javascript:history.back(-1);</script>';
					exit();
				}
			}
			
			if($information['alipay']!="无"){
				$alipay = $information['alipay'];
				$ishave_wx_no=M('user')->where("alipay='".$alipay."'")->find();
				if(!empty($ishave_wx_no)){
				
					echo '<script>alert("支付宝号已经存在，请重新输入");</script>';
					echo '<script>javascript:history.back(-1);</script>';
					exit();
				}
			}
			//=============登录密码加密==============
        	$salt= substr(md5(time()),0,3);
        	$password=md5(md5(trim($information['password'])).$salt);
        
			
        	//=============安全密码加密==============
        	$safety_salt= substr(md5(time()),0,3);
        	$paypassword=md5(md5(trim($information['paypassword'])).$safety_salt); 
			$registerInfo=array(
                'account'        =>trim($information['account']),
                'parent_id'      =>$parent_id,
                'username'       =>trim($information['username']),
                'sex'            =>1,
                'mobile'         =>trim($information['mobile']),
                'wx_no'          =>trim($information['wx_no']),
                'alipay'         =>trim($information['alipay']), 
                'password'       =>$password,
                'salt'           =>$salt,
                'safety_pw'      =>$paypassword,
                'safety_salt'    =>$safety_salt,
				'lockuser'		 =>1,
                'add_time'		 =>time(),
                'ip'			 =>get_client_ip(), 
                'note'			 =>0,
                'limits'		 =>100000, 
				'last_login'	 =>0,
				

            );
			
						//添加用户数据
			$udb=M('user');
			
			  $zhuce=$udb->data($registerInfo)->add();  
			
			//检查是否添加数据成功
			$check_zhuce=M('user')->where("account='".$registerInfo['account']."'")->find();
			if($check_zhuce){
				$db_store = M('store');
				$db_farm = M('nzusfarm');
				for ($i=1; $i <=15 ; $i++) { 
                     $datafarm['u_id']=$check_zhuce['userid'];
                     $datafarm['f_id']=$i; 
                     if ($i<=5) {
                          $datafarm['farm_type']=1;  
                      }else if(5<$i && $i<=10) {
                          $datafarm['farm_type']=2;
                      }else{
                          $datafarm['farm_type']=3;
                      }
                     //============给用户开启6块农田=========
                     $f_bool=$db_farm->data($datafarm)->add();  
                } 
				//============把用户数据放到仓库表=========
             	$datastore['uid']=$check_zhuce['userid'];  
             	$datastore['cangku_num']=0; 
             	$ckInfo=$db_store->where("uid=".$check_zhuce['userid'])->find();

             	if (!$ckInfo) {
                 	$s_bool=$db_store->data($datastore)->add();
             	}else{
                  	echo "<script>alert('仓库表有问题请联系管理员');</script>";
					
					echo '<script>javascript:history.back(-1);</script>';
                  	exit();
             	}
				
				
				echo "<script>alert('注册成功');</script>";
				echo '<script>window.location.href="'.U('Regus/login').'"</script>';die;
                exit();   
			}else{
				echo "<script>alert('注册失败');</script>";
				echo '<script>javascript:history.back(-1);</script>';
                exit();
			}
	}






	
}














 ?>
