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
//		$res=$this->validation_filter_id_card($identity_card);
//		if($res==false){
//			echo "<script>alert('身份证号格式不正确');</script>";
//            echo "<script>javascript:history.back(-1);</script>";die;
//		}
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
		$data['recommend_id'] = $parent_id;
		$data['salt'] = $salt;
		$data['password'] = $password;
		$data['safety_salt'] = $two_salt;
		$data['paypassword'] = $two_password;
		$data['lockuser'] = 1;
		$data['leve'] = 1;
		$data['time'] = time();
		$res = M('user')->data($data)->add();

		$userid = M('user')->where("account='".$account."'")->getField('userid');

		$record['uid'] = $userid;
		$rem = M('store')->data($record)->add();
		if($res&&$rem){
			echo "<script>alert('注册成功');location.href='".U('Index/copyPageTwo')."'</script>";
			exit();
		}else{
			echo "<script>alert('注册失败');location.href='".U('Index/copyPageTwo')."'</script>";
			exit();
		}
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

}