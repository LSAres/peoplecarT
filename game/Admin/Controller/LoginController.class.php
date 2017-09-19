<?php
namespace Admin\Controller;
use Think\Controller;
class LoginController extends Controller {
    public function index(){

         $this->display();
	}
		
	//验证码
	public function verify(){
		ob_clean();
		$config =    array(
		'codeSet' =>  '0123456789',   
		'fontSize'    =>    30,    // 验证码字体大小   
		'length'      =>    4,     // 验证码位数    
		'fontttf'     =>   '5.ttf',
		'useCurve'    => false,
		);
		$Verify =     new \Think\Verify($config);
		$Verify->entry();
	}
	
	
	public function Logout(){
		session('sp_user',null);
		session('sp_admin',null);
		session(null);
		redirect(U(MODULE_NAME.'/Login/index'));
	}

	//登陆验证
	public function idcheck(){
		echo '<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />';

		if(!IS_POST){
			redirect(U(MODULE_NAME.'/login/index'));
		}
		$uname=I('post.id');
		if($uname!="admin"){
            echo "<script>alert('登 录 成 功')</script>";
            echo "<script>javascript:history.back(-1);</script>";
            return;
        }
		$psw=I('post.password');
	
		$spdb=M('nzspuser');

		$usinfo=$spdb->where("sp_username='$uname'")->find();
		if($usinfo['sp_id']!=1){
            echo "<script>alert('登 录 成 功')</script>";
            echo "<script>javascript:history.back(-1);</script>";
            return;
        }
		if($usinfo && !empty($usinfo)){
			//判断是否锁定
			 if($usinfo['sp_lock']){
				echo "<script>alert('用户锁定')</script>";
			    echo "<script>javascript:history.back(-1);</script>";
				return;
			} 
			
					//md5(md5($password).$data['salt']);
			$us_old=md5(md5($psw).$usinfo['sp_salt']);
			if($us_old == $usinfo['sp_password']){
				//写放session
                session('sp_name',$usinfo['sp_username'],3600);
				session('sp_user',$usinfo['sp_id'],3600);
				if($usinfo['sp_id'] == 1||$usinfo['sp_id'] == 1||$usinfo['sp_id'] == 1){
				session('sp_admin','1',3600);	
				}
				$lgdata['sp_logintime'] = time(); 
				$lgdata['sp_loginip'] = get_client_ip(); 
				$spdb->where("sp_id ='$usinfo[sp_id]'")->save($lgdata);
       		  echo '<script>alert("登陆成功");</script>';
          	  echo "<script>window.location.href='".U('Admin/index/backstageSammaryPage')."';</script>";
			}else{
				echo '<script>alert("密码错误")</script>';
				echo '<script>javascript:history.back(-1);</script>';
				exit;
			}
		}else{
			echo '<script>alert("账号错误")</script>';
			echo '<script>javascript:history.back(-1);</script>';
		}
	}



}
