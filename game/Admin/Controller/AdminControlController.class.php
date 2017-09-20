<?php
namespace Admin\Controller;
use Think\Controller;
class AdminControlController extends CommonController {
    public function adminListPage(){
        $where = "";
        $tb_super = M('nzspuser');
        $pagesize =10;
        $p = getpage($tb_super, $where, $pagesize);
        $pageshow   = $p->show();

        $userArr=$tb_super->where($where)
            ->field('sp_id,sp_username,sp_logintime,sp_addtime,sp_loginip')
            ->order('sp_id desc ')
            ->select();

        $this->assign(array(
            'userArr'=>$userArr,
            'pageshow'=>$pageshow,
        ));
        $this->display();
    }

    public function adminAppendPage(){
        $this->display();
    }

    public function addsuper(){
        $t=I('post.');
        foreach($t as $v){
            if($v == ''){
                echo "<script>alert('请确认输入完成');</script>";
                echo "<script>javascript:history.back(-1);</script>";die;
            }
        }
        $sp_username = I('sp_username');
        $sp_password = I('sp_password');
        $sp_passwordT = I('sp_passwordT');
        $is_sp_username = M('nzspuser')->where("sp_username='".$sp_username."'")->find();
        if($is_sp_username){
            echo "<script>alert('账号已存在');</script>";
            echo "<script>javascript:history.back(-1);</script>";die;
        }
        if($sp_password!=$sp_passwordT){
            echo "<script>alert('两次输入密码不一致');</script>";
            echo "<script>javascript:history.back(-1);</script>";die;
        }

        //=============登录密码加密==============
        $salt= substr(md5(time()),0,3);
        $password=md5(md5(trim($sp_password)).$salt);

        //=============管理员资料写入数据库===========
        $data['sp_username']=$sp_username;
        $data['sp_password']=$password;
        $data['sp_salt']=$salt;
        $data['sp_addtime']=time();
        $data['sp_lock']=0;
        $res = M('nzspuser')->data($data)->add();

        //=========判断新增数据是否写入数据库========
        if($res){
            echo "<script>alert('添加成功');location.href='".U('AdminControl/adminListPage')."'</script>";
            exit();
        }else{
            echo "<script>alert('添加失败');</script>";
            echo "<script>javascript:history.back(-1);</script>";die;
        }
    }
}