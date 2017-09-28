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
           $res = M('user')->where('userid='.$userid)->setField('lockuser',2);
       }else{
           if($lockuser==1){
               $recommend_id = M('user')->where('userid='.$userid)->getField('recommend_id');
               $rs=M('store')->where('uid='.$recommend_id)->setInc('buycar_money',6000);
               $re=M('store')->where('uid='.$recommend_id)->setInc('money',6000);
               if(!$re||!$rs){
                   echo "<script>alert('奖励发放失败，请重新操作');</script>";
                   echo "<script>javascript:history.back(-1);</script>";die;
               }
               $is_top_id = M('group')->where('one_id='.$recommend_id)->find();
               if($is_top_id){
                   $this->group($recommend_id,$userid);
               }else {
                   $recommend_id_two = M('user')->where('userid=' . $recommend_id)->getField('parent_id');
                   $is_top_id = M('group')->where('one_id=' . $recommend_id_two)->find();
                   if ($is_top_id) {
                       $this->group($recommend_id_two, $userid);
                   }else {
                       $recommend_id_three = M('user')->where('userid=' . $recommend_id_two)->getField('parent_id');
                       $is_top_id = M('group')->where('one_id=' . $recommend_id_three)->find();
                       if ($is_top_id) {
                           $this->group($recommend_id_three, $userid);
                       }
                   }

               }
               $res = M('user')->where('userid='.$userid)->setField('lockuser',0);
           }else{
               $res = M('user')->where('userid='.$userid)->setField('lockuser',0);
           }
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
        $leve = I('post.leve');
        if($username==null&&$phone==null&&$identity_card==null&&$password==null&&$paypassword==null&&$leve==null){
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
        if($leve!=null){
            $data['leve']=$leve;
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

    //分组
    public function group($top_id=null,$userid=null){
        $two_group = M('user')->where('parent_id='.$top_id)->count();
        $group = M('group')->where('one_id='.$top_id)->find();
        if($two_group<2){
            M('user')->where('userid='.$userid)->setField('parent_id',$top_id);
            if($group['two_id']==null){
                M('group')->where('one_id='.$top_id)->setField('two_id',$userid);
                M('group')->where('one_id='.$top_id)->setField('two_time',time());
            }else{
                M('group')->where('one_id='.$top_id)->setField('three_id',$userid);
                M('group')->where('one_id='.$top_id)->setField('three_time',time());
            }
        }else{
            if($group['four_id']==null){
                M('user')->where('userid='.$userid)->setField('parent_id',$group['two_id']);
                M('group')->where('one_id='.$top_id)->setField('four_id',$userid);
                M('group')->where('one_id='.$top_id)->setField('four_time',time());
            }else{
                if($group['five_id']==null){
                    M('user')->where('userid='.$userid)->setField('parent_id',$group['two_id']);
                    M('group')->where('one_id='.$top_id)->setField('five_id',$userid);
                    M('group')->where('one_id='.$top_id)->setField('five_time',time());
                }else{
                    if ($group['six_id']==null){
                        M('user')->where('userid='.$userid)->setField('parent_id',$group['three_id']);
                        M('group')->where('one_id='.$top_id)->setField('six_id',$userid);
                        M('group')->where('one_id='.$top_id)->setField('six_time',time());
                    }else{
                        $data['one_id']=$group['two_id'];
                        $data['one_time']=time();
                        M('group')->data($data)->add();
                        M('group')->where('one_id='.$group['two_id'])->setField('two_id',$group['four_id']);
                        M('group')->where('one_id='.$group['two_id'])->setField('two_time',time());
                        M('user')->where('userid='.$group['four_id'])->setField('parent_id',$group['two_id']);
                        M('group')->where('one_id='.$group['two_id'])->setField('three_id',$group['five_id']);
                        M('group')->where('one_id='.$group['two_id'])->setField('three_time',time());
                        M('user')->where('userid='.$group['five_id'])->setField('parent_id',$group['two_id']);
                        $dbtb['one_id']=$group['three_id'];
                        $dbtb['one_time']=time();
                        M('group')->data($dbtb)->add();
                        M('group')->where('one_id='.$group['three_id'])->setField('two_id',$group['six_id']);
                        M('group')->where('one_id='.$group['three_id'])->setField('two_time',time());
                        M('user')->where('userid='.$group['six_id'])->setField('parent_id',$group['three_id']);
                        M('group')->where('one_id='.$group['three_id'])->setField('three_id',$userid);
                        M('group')->where('one_id='.$group['three_id'])->setField('three_time',time());
                        M('user')->where('userid='.$userid)->setField('parent_id',$group['three_id']);
                        M('group')->where('one_id='.$top_id)->delete();
                        M('group')->where('userid='.$top_id)->setField('parent_id',null);

                        $res=$this->recombination($group['one_id']);
                    }
                }
            }
        }
    }

    //顶端出局重组
    public function recombination($top_id=null){
        $recommend_id = M('user')->where('userid='.$top_id)->getField('recommend_id');
        M('store')->where('uid='.$top_id)->setInc('bonus',2000);
        if($recommend_id==1){
            $data['one_id']=$top_id;
            $data['one_time']=time();
            $res = M('group')->data($data)->add();
            if($res){
                return true;
            }else{
                return false;
            }
        }else{
            $is_top_id = M('group')->where('one_id='.$recommend_id)->find();
            if($is_top_id){
                $this->group($recommend_id,$top_id);
            }else {
                $recommend_id_two = M('user')->where('userid=' . $recommend_id)->getField('parent_id');
                $is_top_id = M('group')->where('one_id=' . $recommend_id_two)->find();
                if ($is_top_id) {
                    $this->group($recommend_id_two, $top_id);
                }else {
                    $recommend_id_three = M('user')->where('userid=' . $recommend_id_two)->getField('parent_id');
                    $is_top_id = M('group')->where('one_id=' . $recommend_id_three)->find();
                    if ($is_top_id) {
                        $this->group($recommend_id_three, $top_id);
                    }
                }
            }
        }
    }

}