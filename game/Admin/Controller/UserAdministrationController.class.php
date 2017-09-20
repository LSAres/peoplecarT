<?php
namespace Admin\Controller;
use Think\Controller;
class UserAdministrationController extends CommonController {

   //用户列表数据查询
   public function administrationPage(){
       $start_time = strtotime(I('start_time'));
       $end_time = strtotime(I('end_time'));

       if($start_time && $end_time){
           $where['time'] = array('between',"$start_time,$end_time");
       }
       $tb_user=M('user');

       if(I('condition')){
           $we = I('condition');
           $value=trim(I('text'));
           if($we=="username"){
               $where[$we] =array('like',"%$value%");
           }else {
               $where[$we] = $value;
           }
       }

       $pagesize =10;
       $p = getpage($tb_user, $where, $pagesize);
       $pageshow   = $p->show();

       $userArr=$tb_user->where($where)
           ->field('userid,account,username,lockuser,time,parent_id,identity_card,phone')
           ->order('userid desc ')
           ->select();

       $this->assign(array(
           'userArr'=>$userArr,
           'pageshow'=>$pageshow,
       ));
       $this->display();
   }

   //修改用户账号状态
   public function updatelockuser(){
       $userid = I('get.userid');
       $lockuser = M('user')->where('userid='.$userid)->getField('lockuser');
       if($lockuser==0){
           $res = M('user')->where('userid='.$userid)->setField('lockuser',1);
       }else{
           $res = M('user')->where('userid='.$userid)->setField('lockuser',0);
       }
       if($res){
           echo "<script>alert('修改成功');</script>";
           echo "<script>window.location.href='".U('UserAdministration/administrationPage')."'</script>";
       }else{
           echo "<script>alert('修改失败');</script>";
           echo "<script>javascript:history.back(-1);</script>";die;
       }
   }

   //删除用户
   public function deleteuser(){
       $userid = I('get.userid');
       $res = M('user')->where('userid='.$userid)->delete();
       if($res){
           echo "<script>alert('删除成功');</script>";
           echo "<script>window.location.href='".U('UserAdministration/administrationPage')."'</script>";
       }else{
           echo "<script>alert('删除失败');</script>";
           echo "<script>javascript:history.back(-1);</script>";die;
       }
   }

   //修改用户资料界面
    public function updateUserDataPage(){
       $userid = I('get.userid');
       $userInfo = M('user')->where('userid='.$userid)->find();
       $this->assign('userInfo',$userInfo);
       $this->display();
    }

    //接收修改的用户资料
    public function edituserInfo(){
        $userid = I('post.userid');
        $username = I('post.username');
        $phone = I('post.phone');
        $identity_card = I('post.identity_card');
        $password = I('post.password');
        $paypassword = I('post.paypassword');
        if($username==null&&$phone==null&&$identity_card==null&&$password==null&&$paypassword==null){
            echo "<script>alert('无任何修改');</script>";
            echo "<script>javascript:history.back(-1);</script>";die;
        }
        if($username!=null){
            $data['username']=$username;
        }
        if($phone!=null){
            $data['phone']=$phone;
        }
        if($identity_card!=null){
            $data['identity_card']=$identity_card;
        }
        if($password!=null){
            //=============登录密码加密==============
            $salt= substr(md5(time()),0,3);
            $password=md5(md5(trim($password)).$salt);
            $data['salt']=$salt;
            $data['password']=$password;
        }
        if($paypassword!=null){
            //=============安全密码加密==============
            $two_salt= substr(md5(time()),0,3);
            $two_password=md5(md5(trim($paypassword)).$two_salt);
            $data['safety_salt'] = $two_salt;
            $data['paypassword'] = $two_password;
        }

        //向用户表修改用户资料
        $res = M('user')->where('userid='.$userid)->save($data);
        if($res){
            echo "<script>alert('修改成功');</script>";
            echo "<script>window.location.href='".U('UserAdministration/administrationPage')."'</script>";
        }else{
            echo "<script>alert('修改失败');</script>";
            echo "<script>javascript:history.back(-1);</script>";die;
        }
    }

}