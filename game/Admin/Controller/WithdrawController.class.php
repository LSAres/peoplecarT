<?php
namespace Admin\Controller;
use Think\Controller;
class WithdrawController extends CommonController {
	//首页
	public function index(){
		$spdb=M('nzspuser');
		$spall = $spdb->select();
	
		$this->spall=$spall;
		$this->display();
	}
	//添加列表
	public function addsplist(){
		$this->display();
	}
	
	//添加账号
	public function addmember(){
	echo '<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />';

        $spid=I('post.spid');
        $password=I('post.sppsw1');
        $passwordmin=I('post.sppsw2');

		if($password  !== $passwordmin){
			echo '<script>alert("两次密码不一样");</script>';
 			echo '<script>javascript:history.back(-1);</script>';
           return;
		}

		$spdb=M('nzspuser');

		
		$oldus=$spdb->where("sp_username = '$spid'")->count();

		if($oldus){
               // echo "<meta charset=\"utf-8\"/><script>alert('账号存在')</script>";
                echo '<script>alert("账号存在");</script>';
 				echo '<script>javascript:history.back(-1);</script>';
	            return;
		}

		$data['sp_salt'] = substr(md5(time()),0,4);

		$data['sp_username']=$spid;
		$data['sp_password']=md5(md5($password).$data['sp_salt']);
		$data['sp_addtime']=time();

        //获取本机ip

		if($spdb->add($data)){
			echo $spdb->_sql();
			die;
       		  echo '<script>alert("注册成功");</script>';
          	  echo "<script>window.location.href='".U('Admin/Spadmin/index')."';</script>";
		}

	
	}
	
	
}

