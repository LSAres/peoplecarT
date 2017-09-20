<?php
namespace Mobile\Controller;
use Think\Controller;
use Org\Util\String;
class IndexController extends CommonController {

    public function copyPageTwo(){
		$userid = session('userid');
		$dbu = M('user');
		$userInfo = $dbu->where('userid='.$userid)->find();
		if($userInfo['leve']==1){
			$userInfo['levename']="平民";
		}
		if($userInfo['leve']==2){
			$userInfo['levename']="贵族";
		}
		if($userInfo['leve']==3){
			$userInfo['levename']="管理员";
		}
		$this->assign('userInfo',$userInfo);
		$this->display();
	}
	public function index(){
		$userid = session('userid');
		$dbu = M('user');
		$userInfo = $dbu->where('userid='.$userid)->find();
		if($userInfo['leve']==1){
			$userInfo['levename']="平民";
		}
		if($userInfo['leve']==2){
			$userInfo['levename']="贵族";
		}
		if($userInfo['leve']==3){
			$userInfo['levename']="管理员";
		}
		$this->assign('userInfo',$userInfo);
		$this->display('copyPageTwo');
	}
	
	//注册页面
	public function user_register(){
		$this->display();
	}
	
	//用户提交注册信息完成注册
	public function register(){
		$t=I('post.');
		foreach($t as $v){
			if($v == ''){
				echo "<script>alert('请确认输入完成');</script>";
                echo "<script>javascript:history.back(-1);</script>";die;
            }
		}
		$account = I('post.account');
		$username = I('post.username');
		$identity_card = I('post.identity_card');
		$phone = I('post.phone');
		$parent_account = I('post.parent_account');
		$password = I('post.password');
		$passwordT = I('post.passwordT');
		$paypassword = I('post.paypassword');
		$paypasswordT = I('post.paypasswordT');
		$parent_id = M('user')->where("account='".$parent_account."'")->getField('userid');
		if($parent_id==null){
			echo "<script>alert('推荐人不存在');</script>";
            echo "<script>javascript:history.back(-1);</script>";die;
		}
		$is_account = M('user')->where("account='".$account."'")->find();
		if($is_account){
			echo "<script>alert('该用户名已经存在');</script>";
            echo "<script>javascript:history.back(-1);</script>";die;
		}
		$is_phone = M('user')->where("phone='".$phone."'")->find();
		if($is_phone){
			echo "<script>alert('该手机号已经存在');</script>";
            echo "<script>javascript:history.back(-1);</script>";die;
		}
		$res=$this->validation_filter_id_card($identity_card);
		if($res==false){
			echo "<script>alert('身份证号格式不正确');</script>";
            echo "<script>javascript:history.back(-1);</script>";die;
		}
		if($password!=$passwordT){
			echo "<script>alert('两次输入的登陆密码不一致');</script>";
            echo "<script>javascript:history.back(-1);</script>";die;
		}
		if($paypassword!=$paypasswordT){
			echo "<script>alert('两次输入的安全密码不一致');</script>";
            echo "<script>javascript:history.back(-1);</script>";die;
		}
		
		//=============登录密码加密==============
        $salt= substr(md5(time()),0,3);
        $password=md5(md5(trim($password)).$salt);
        

        //=============安全密码加密==============
        $two_salt= substr(md5(time()),0,3);
        $two_password=md5(md5(trim($paypassword)).$two_salt); 
		
		$data['account'] = $account;
		$data['username'] = $username;
		$data['identity_card'] = $identity_card;
		$data['phone'] = $phone;
		$data['parent_id'] = $parent_id;
		$data['salt'] = $salt;
		$data['password'] = $password;
		$data['safety_salt'] = $two_salt;
		$data['paypassword'] = $two_password;
		$data['lockuser'] = 0;
		$data['leve'] = 1;
		$data['time'] = time();
		$res = M('user')->data($data)->add();
		if($res){
			echo "<script>alert('注册成功');location.href='".U('Index/copyPageTwo')."'</script>";
			exit();
		}else{
			echo "<script>alert('注册失败');location.href='".U('Index/copyPageTwo')."'</script>";
			exit();
		}
		dump($res);die;
	}
	
	//验证身份证
	function validation_filter_id_card($id_card){ 
	 	if(strlen($id_card)==18){ 
	 		return $this->idcard_checksum18($id_card); 
		}elseif((strlen($id_card)==15)){ 
	 		$id_card=$this->idcard_15to18($id_card); 
	 		return $this->idcard_checksum18($id_card); 
	 	}else{ 
	 		return false; 
	 	} 
	} 
	// 计算身份证校验码，根据国家标准GB 11643-1999 
	function idcard_verify_number($idcard_base){ 
		 if(strlen($idcard_base)!=17){ 
		 	return false; 
		 } 
		 //加权因子 
		 $factor=array(7,9,10,5,8,4,2,1,6,3,7,9,10,5,8,4,2); 
		 //校验码对应值 
		 $verify_number_list=array('1','0','X','9','8','7','6','5','4','3','2'); 
		 $checksum=0; 
		 for($i=0;$i<strlen($idcard_base);$i++){ 
		 	$checksum += substr($idcard_base,$i,1) * $factor[$i]; 
		 } 
		 $mod=$checksum % 11; 
		 $verify_number=$verify_number_list[$mod]; 
		 return $verify_number; 
	} 
	// 将15位身份证升级到18位 
	function idcard_15to18($idcard){ 
		 if(strlen($idcard)!=15){ 
		 	return false; 
		 }else{ 
		 // 如果身份证顺序码是996 997 998 999，这些是为百岁以上老人的特殊编码 
			 if(array_search(substr($idcard,12,3),array('996','997','998','999')) !== false){ 
				$idcard=substr($idcard,0,6).'18'.substr($idcard,6,9); 
			 }else{ 
				$idcard=substr($idcard,0,6).'19'.substr($idcard,6,9); 
			 } 
		 } 
		 $idcard=$idcard.$this->idcard_verify_number($idcard); 
		 return $idcard; 
	} 
	// 18位身份证校验码有效性检查 
	function idcard_checksum18($idcard){ 
		 if(strlen($idcard)!=18){ 
		 	return false; 
		 } 
		 $idcard_base=substr($idcard,0,17); 
		 if($this->idcard_verify_number($idcard_base)!=strtoupper(substr($idcard,17,1))){ 
		 	return false; 
		 }else{ 
		 	return true; 
		 } 
	} 
	
	
   //=========开发新农场======================================================
    public function kaifaxinguoyuan(){
                echo "<meta charset='utf-8'>";
              $SysConfig=F('SysConfig','','./Public/data/'); 
             $this->assign('SysConfig',$SysConfig);
        if (I('post.')) {
				$type = I('post.type');
				switch($type){
					case 1:
						$xiaohao=330;
						$money = 300;
						break;
					case 2:
						$xiaohao=630;
						$money = 600;
						break;
					case 3:
						$xiaohao=930;
						$money = 900;
						break;
				}
             $udb=M('user');
             $db_farm=M('nzusfarm');
             $login_id=session('userid');
             $udb->startTrans();
             $arr=I('post.'); 
            
            //判断登录人的交易密码是否正确
              $uif=$udb->where('userid='.$login_id.'')->find();
              $iptopw=md5(md5(trim($arr['my_twopw'])).$uif['safety_salt']);
              if ($iptopw!=$uif['safety_pw']) {
                 echo '<script>alert("交易密码错误");</script>';
                 echo '<script>javascript:history.back(-1);</script>';
                exit();
              }
            //判断登录人的水果是否还有330
              $shengy=M('store')->where('uid='.$login_id)->getField('cangku_num');  
              if ($shengy<$xiaohao) {
                  echo '<script>alert("您仓库的'.C('GUOZI').'不足");</script>';
                 echo '<script>javascript:history.back(-1);</script>';
                exit();
              }
        
            
            //========判断查出来的父级id是否为空============
            $recommend_ren=trim($arr['recommend_ren']); 
            $data=$udb->where("account='".$recommend_ren."'")->find(); 

            if(empty($data)){
                echo '<script>alert("推荐人不存在");</script>';
                echo '<script>javascript:history.back(-1);</script>';
                exit();
            }else{
              session('tui_id',$data['userid']);
              $tui_id=session('tui_id');
            }

            
            //========判断新的账号名是否已经存在============
            $account=trim($arr['account']); 
            $data2=$udb->where("account='".$account."'")->find(); 
            if(!empty($data2)){
                echo '<script>alert("账号名已经存在，请重新输入");</script>';
                echo '<script>javascript:history.back(-1);</script>';
                exit();
            }

            // 姓名是否填写
            $username=trim($arr['username']);
            if (empty($username)) {
              echo '<script>alert("忘记填写姓名啦");</script>';
              echo '<script>javascript:history.back(-1);</script>';
              exit();
            }
            

           //判断手机号是否有重复
            $post_mobile=trim($arr['mobile']);
            $mobileInfo=$udb->where('mobile="'.$post_mobile.'"')->find();
            if (!empty($mobileInfo)) {
                 echo '<script>alert("该手机号已注册请换个号码");</script>';
                 echo '<script>javascript:history.back(-1);</script>';
                exit();
              }
           
           //========判断两次输入的登录密码是否一致============
           if(trim($arr['password'])!== trim($arr['passwordr'])){
            echo '<script>alert("两次登录密码不一样");</script>';
            echo '<script>javascript:history.back(-1);</script>';
             exit();
            } 
             
            
            //========判断两次输入的交易密码是否一致============
           if(trim($arr['two_password'])!== trim($arr['two_passwordr'])){
            echo '<script>alert("两次交易密码不一样");</script>';
            echo '<script>javascript:history.back(-1);</script>';
             exit();
            }


            
        //=============登录密码加密==============
        $salt= substr(md5(time()),0,3);
        $password=md5(md5(trim($arr['passwordr'])).$salt);
        

        //=============安全密码加密==============
        $two_salt= substr(md5(time()),0,3);
        $two_password=md5(md5(trim($arr['two_passwordr'])).$two_salt); 

           $registerInfo=array(
                'account'        =>trim($arr['account']),
                'parent_id'      =>$data['userid'],
                'username'       =>trim($arr['username']),
                'sex'            =>trim($arr['sex']),
                'mobile'         =>trim($arr['mobile']),
                'wx_no'          =>trim($arr['wx_no']),
                'alipay'         =>trim($arr['alipay']), 
                'password'       =>$password,
                'salt'           =>$salt,
                'safety_pw'      =>$two_password,
                'safety_salt'    =>$two_salt,
				'lockuser'		 =>0,
                'add_time'=>time(),
                'ip'=>get_client_ip(), 
                'note'=>trim($arr['note']),
                'limits'=>$SysConfig['c_xrjye'], 
				'last_login'=>0,
				'khjq'=>$money,

            );
  
        //========向user表添加信息=======
        $zhuce=$udb->data($registerInfo)->add();  
        //=========检查刚才添加的是否有值============
        $check_zhuce=$udb->where("account='".$registerInfo['account']."'")->find();
        $userid=$check_zhuce['userid'];
        
          if ($check_zhuce) {
              $db_store=M('store');
              $db_huizong=M('nzhuizong');
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
                    
                     //============给用户开启15块农田=========
                     $f_bool=$db_farm->data($datafarm)->add();  
                  }    
             //============把用户数据放到仓库表=========
             $datastore['uid']=$check_zhuce['userid'];  
             $datastore['cangku_num']=$money; 
             $ckInfo=$db_store->where("uid=$userid")->find();

             if (!$ckInfo) {
                 $s_bool=$db_store->data($datastore)->add();
             }else{
                  echo "<script>alert('仓库表有问题请联系管理员');</script>";
                  exit();
             } 
            //给推荐人奖励20个种子
             //$seednum = C('REG_GIVE_SEED_NUM');
			 if($SysConfig['c_ztjl']!=0){
              	$jzz=$db_store->where('uid='.$tui_id.'')->setInc('cangku_num', $SysConfig['c_ztjl']);
			}else{
				$jzz=1;
			}
            //再干掉330的水果
              $kouguozi=$db_store->where('uid='.$login_id.'')->setDec('cangku_num', $SysConfig['c_tjcf']);
            //判断他直推的人是多少
              //$tui_num=$udb->where('parent_id='.$tui_id.'')->count(); 
              //if($tui_num=10||$tui_num=20||$tui_num=30||$tui_num=40){
                   $this->dcr($tui_id);//应该给他添加多少水果农夫
              //} 
            //推荐人奖励种子
              $dbzz=M('zhongzijiangli');
                $zz['u_id']=$tui_id;
                $zz['recommond_id']=$userid;
                $zz['seed_num']=$SysConfig['c_ztjl'];
                $zz['time']=time();
              $hdzz=$dbzz->data($zz)->add(); 
			  
			  $ztjl_num = M('zhongzijiangli')->where('u_id='.$tui_id)->count();
			  switch($ztjl_num){
			  	case 10:
					M('store')->where('uid='.$tui_id)->setField('dcr_num',1);
					$reason="奖励一个果农";
					break;
				case 20:
					M('store')->where('uid='.$tui_id)->setField('dcr_num',2);
					$reason="奖励一个果农";
					break;
				case 30:
					M('store')->where('uid='.$tui_id)->setField('dcr_num',3);
					$reason="奖励一个果农";
					break;
				case 40:
					M('store')->where('uid='.$tui_id)->setField('dcr_num',4);
					$reason="奖励一个果农";
					break;
				case 50:
					M('store')->where('uid='.$tui_id)->setField('hashiqi_num',1);
					$reason="奖励一只哈士奇";
					break;
			  }
			  if($ztjl_num==10||$ztjl_num==20||$ztjl_num==30||$ztjl_num==40||$ztjl_num==50){
			  	$msn['u_id']=$tui_id;
				$msn['recommond_id']=$userid;
				$msn['reason']=$reason;
				$msn['time']=time();
				M('ztjl')->data($msn)->add();
			  }
			  $nn['u_id']=$userid;
			  $nn['time']=time();
			  M('jlhsq')->data($nn)->add();
			  

				
              if($zhuce&&$check_zhuce&&$jzz&&$s_bool&&$f_bool&&$kouguozi&&$hdzz){
                $udb->commit();

                // 发短信通知
                $reg_template_code = C('SMS_REG_TEMPLATE_CODE');
                if (!empty($reg_template_code)) {
                  import('Common.Extend.Alisms');
                  $SmsModel = new \Alisms($reg_template_code);
                  $SmsModel->send_verify($post_mobile, "{\"account\":\"$arr[account]\", \"password\":\"$arr[password]\"}");
                }

                //跳转到
              echo '<script>alert("注册成功！");</script>';
              $this->display();
              }else{
                $udb->rollback();
                echo '<script>alert("注册失败");</script>';
                echo '<script>javascript:history.back(-1);</script>';die;
              }  
          }
          
        }else{

            $login_id=session('userid');
            // 开垦土地数
            $db_farm=M('nzusfarm');
            $map['u_id']=$login_id;
            $map['show_tu']=array('GT', 0);
            $farmnum=$db_farm->where($map)->count();
            // 如果未开满地，不能修改推荐人
            //dump($SysConfig['c_tjpx']);
              if (($farmnum == 15) || ($SysConfig['c_tjpx'] ==1)) {
              $this->recommender = "yes";
            } else {
              $this->recommender = "no";
            }
            /*if((count($farmnum)>0) && ($SysConfig['c_tjpx']<1)){
              $recommender="no";
            }
            else{
              // 已开满
              $recommender="yes";
            }*/
            $this->cangku_num=$cangku_num=M('store')->where('uid='.$login_id.'')->getField('cangku_num');
            $this->account=$account=M('user')->where('userid='.$login_id.'')->getField('account');;
            $this->display();
        }        
    }

}