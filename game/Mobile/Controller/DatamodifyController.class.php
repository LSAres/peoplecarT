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

  //结构图查询小组
  public function structure(){
      $userid = session('userid');
      $db_group = M('group');
      $is_top = $db_group->where('one_id='.$userid)->find();
      if($is_top){
          $structureInfo=$this->getstructureInfo($is_top);
          //dump($structureInfo);die;
      }else{
          $parent_id = M('user')->where('userid='.$userid)->getField('parent_id');
          $is_top = $db_group->where('one_id='.$parent_id)->find();
          if($is_top){
              $structureInfo=$this->getstructureInfo($is_top);
              //dump($structureInfo);die;
          }else{
              $parent_id_two = M('user')->where('userid='.$parent_id)->getField('parent_id');
              $is_top = $db_group->where('one_id='.$parent_id_two)->find();
              $structureInfo=$this->getstructureInfo($is_top);
              //dump($structureInfo);die;
          }
      }
      $this->assign('structureInfo',$structureInfo);
      $this->display();

  }

  public function getstructureInfo($is_top){
        $one_userInfo=M('user')->where('userid='.$is_top['one_id'])->find();
        $one_parent_userInfo = M('user')->where('userid='.$one_userInfo['recommend_id'])->find();
        $is_top['one_account']=$one_userInfo['account'];
        $is_top['one_username']=$one_userInfo['username'];
        $is_top['one_time']=$one_userInfo['time'];
        $is_top['one_parent_account']=$one_parent_userInfo['account'];
        $is_top['one_parent_username']=$one_parent_userInfo['username'];
        if($is_top['two_id']!=null){
            $two_userInfo=M('user')->where('userid='.$is_top['two_id'])->find();
            $two_parent_userInfo=M('user')->where('userid='.$two_userInfo['recommend_id'])->find();
            $is_top['two_account']=$two_userInfo['account'];
            $is_top['two_username']=$two_userInfo['username'];
            $is_top['two_time']=$two_userInfo['time'];
            $is_top['two_parent_account']=$two_parent_userInfo['account'];
            $is_top['two_parent_username']=$two_parent_userInfo['username'];
        }
        if($is_top['three_id']!=null){
            $three_userInfo=M('user')->where('userid='.$is_top['three_id'])->find();
            $three_parent_userInfo=M('user')->where('userid='.$three_userInfo['recommend_id'])->find();
            $is_top['three_account']=$three_userInfo['account'];
            $is_top['three_username']=$three_userInfo['username'];
            $is_top['three_time']=$three_userInfo['time'];
            $is_top['three_parent_account']=$three_parent_userInfo['account'];
            $is_top['three_parent_username']=$three_parent_userInfo['username'];
        }
        if($is_top['four_id']!=null){
            $four_userInfo=M('user')->where('userid='.$is_top['four_id'])->find();
            $four_parent_userInfo=M('user')->where('userid='.$four_userInfo['recommend_id'])->find();
            $is_top['four_account']=$four_userInfo['account'];
            $is_top['four_username']=$four_userInfo['username'];
            $is_top['four_time']=$four_userInfo['time'];
            $is_top['four_parent_account']=$four_parent_userInfo['account'];
            $is_top['four_parent_username']=$four_parent_userInfo['username'];
        }
        if($is_top['five_id']!=null){
            $five_userInfo=M('user')->where('userid='.$is_top['five_id'])->find();
            $five_parent_userInfo=M('user')->where('userid='.$five_userInfo['recommend_id'])->find();
            $is_top['five_account']=$five_userInfo['account'];
            $is_top['five_username']=$five_userInfo['username'];
            $is_top['five_time']=$five_userInfo['time'];
            $is_top['five_parent_account']=$five_parent_userInfo['account'];
            $is_top['five_parent_username']=$five_parent_userInfo['username'];
        }
        if($is_top['six_id']!=null){
            $six_userInfo=M('user')->where('userid='.$is_top['six_id'])->find();
            $six_parent_userInfo=M('user')->where('userid='.$six_userInfo['recommend_id'])->find();
            $is_top['six_account']=$six_userInfo['account'];
            $is_top['six_username']=$six_userInfo['username'];
            $is_top['six_time']=$six_userInfo['time'];
            $is_top['six_parent_account']=$six_parent_userInfo['account'];
            $is_top['six_parent_username']=$six_parent_userInfo['username'];
        }
        return $is_top;

  }
}