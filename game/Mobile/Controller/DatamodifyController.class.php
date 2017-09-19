<?php
namespace Mobile\Controller;
use Think\Controller;
class DatamodifyController extends CommonController{

  public function user_message(){
  	$userid = session('userid');
	$udb = M('user');
	$userInfo = $udb->where('userid='.$userid)->find();
	$this->assign('userInfo',$userInfo);
    $this->display();
  }
  
  public function updateInfo(){
  	$userid = session('userid');
  	$phone = I('post.phone');
	$identity_card = I('post.identity_card');
	$userInfo = M('user')->where('userid='.$userid)->find();
	if($phone==null){
		echo "<script>alert('手机号不可为空');location.href='".U('Datamodify/user_message')."'</script>";
		exit();
	}
	if($identity_card==null){
		echo "<script>alert('身份证号不可为空');location.href='".U('Datamodify/user_message')."'</script>";
		exit();
	}
	if($phone==$userInfo['phone']&&$identity_card==$userInfo['identity_card']){
		echo "<script>alert('资料无任何修改');location.href='".U('Datamodify/user_message')."'</script>";
		exit();
	}
	$res = M('user')->where('userid='.$userid)->setField('phone',$phone);
	$ren = M('user')->where('userid='.$userid)->setField('identity_card',$identity_card);
	echo "<script>alert('资料修改成功');location.href='".U('Datamodify/user_message')."'</script>";
	exit();
  }
  
  public function user_bankmessage(){
  	$userid = session('userid');
	$bankInfo = M('bank')->where('uid='.$userid)->find();
	$this->assign('bankInfo',$bankInfo);
  	$this->display();
  }
  
  public function updateBankInfo(){
  	$userid = session('userid');
	$realname = I('post.realname');
	$bank_name = I('post.bank_name');
	$bank_card = I('post.bank_card');
	$bank_address = I('post.bank_address');
	if($realname==null){
		echo "<script>alert('真实姓名不可为空');history.back();</script>";
		exit();
	}
	if($bank_name==null){
		echo "<script>alert('开户银行不可为空');history.back();</script>";
		exit();
	}
	if($bank_card==null){
		echo "<script>alert('银行卡号不可为空');history.back();</script>";
		exit();
	}
	if($bank_address==null){
		echo "<script>alert('开户地址不可为空');history.back();</script>";
		exit();
	}
	$bankInfo = M('bank')->where('uid='.$userid)->find();
	if($bankInfo){
		M('bank')->where('uid='.$userid)->setField('realname',$realname);
		M('bank')->where('uid='.$userid)->setField('bank_name',$bank_name);
		M('bank')->where('uid='.$userid)->setField('bank_card',$bank_card);
		M('bank')->where('uid='.$userid)->setField('bank_address',$bank_address);
	}else{
		$data['uid'] = $userid;
		$data['realname'] = $realname;
		$data['bank_name'] = $bank_name;
		$data['bank_card'] = $bank_card;
		$data['bank_address'] = $bank_address;
		$res = M('bank')->data($data)->add();
	}
	echo "<script>alert('银行资料修改成功');location.href='".U('Datamodify/user_bankmessage')."'</script>";
	exit();
	
	
  }
  
  public function user_passwordchange(){
  	$this->display();
  }
  
  public function updatepassword(){
  	$password = I('post.password');
	$newpassword = I('post.newpassword');
	$newpasswordT = I('post.newpasswordT');
	if($password==null){
		echo "<script>alert('原登陆密码不可为空');history.back();</script>";
		exit();
	}
	if($newpassword==null){
		echo "<script>alert('新登陆密码不可为空');history.back();</script>";
		exit();
	}
	if($newpasswordT==null){
		echo "<script>alert('确认新密码不可为空');history.back();</script>";
		exit();
	}
	if($newpassword!=$newpasswordT){
		echo "<script>alert('两次输入的新密码不一致');history.back();</script>";
		exit();
	}
  }
}