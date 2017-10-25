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
       //判断是否为第一次激活，等于1为刚注册用户，等于0为已激活用户，等于2为封停账号
       if($lockuser==0){
           //老用户修改锁定状态，封停
           $res = M('user')->where('userid='.$userid)->setField('lockuser',2);
       }else{
           //新用户后台激活，进行分组
           if($lockuser==1){
               $userInfo = M('user')->where('userid='.$userid)->find();
               $data['uid']=$userid;
               $data['account']=$userInfo['account'];
               $data['username']=$userInfo['username'];
               $data['time']=time();
               $cc = M('activation_record')->data($data)->add();
               if(!$cc){
                   echo "<script>alert('操作失败，请重新激活');</script>";
                   echo "<script>javascript:history.back(-1);</script>";die;
               }
               //查询上级，将购车基金返给上级，并将金额计算到总金额中
               $recommend_id = M('user')->where('userid='.$userid)->getField('recommend_id');
               $rs=M('store')->where('uid='.$recommend_id)->setInc('frozen_money',8000);
               if($rs){
                   $information['uid']=$userid;
                   $information['money']=8000;
                   $information['reason']="直推奖励购车基金";
                   $information['time']=time();
                   $rc = M('bonus_record')->data($data)->add();

                   $cg['uid']=$userid;
                   $cg['money']="+8000";
                   $cg['reason']="直推奖励购车基金";
                   $cg['time']=time();
                   M('getbuycarmoney_record')->data($cg)->add();
               }
               //判断奖励是否发放成功
               if(!$rs||!$rc){
                   echo "<script>alert('奖励发放失败，请重新操作');</script>";
                   echo "<script>javascript:history.back(-1);</script>";die;
               }
               //查询推荐人是否为小组顶端
               $ishave_upside_id = M('user')->where('userid='.$userid)->getField('upside_id');
               if($ishave_upside_id){
                   $recommend_id = $ishave_upside_id;
               }
               $is_top_id = M('group')->where('one_id='.$recommend_id)->find();
               //推荐人为小组顶端时将注册用户进行分组
               if($is_top_id){
                   //分组
                   $this->group($recommend_id,$userid);
               }else {
                   //查询推荐人的上级，并判断推荐人的上级是否为小组顶端，如果是，将注册用户分组
                   $recommend_id_two = M('user')->where('userid=' . $recommend_id)->getField('parent_id');
                   $is_top_id = M('group')->where('one_id=' . $recommend_id_two)->find();
                   if ($is_top_id) {
                       //进行分组
                       $this->group($recommend_id_two, $userid);
                   }else {
                       //查询推荐人上两级，并判断是否为小组顶端，如果是，将注册用户进行分组
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
        //统计小组内第二层的人数
        $two_group = M('user')->where('parent_id='.$top_id)->count();
        //查询小组数据
        $group = M('group')->where('one_id='.$top_id)->find();
        //如果小组第二层人数小于2，将新用户放入小组第二层
        if($two_group<2){
            //将组长设置为新成员的父级
            M('user')->where('userid='.$userid)->setField('parent_id',$top_id);
            //判断二号组员是否为空，如果为空，将新用户放置到2号成员。如果不为空，将新用户放置到3号成员
            if($group['two_id']==null){
                M('group')->where('one_id='.$top_id)->setField('two_id',$userid);
                M('group')->where('one_id='.$top_id)->setField('two_time',time());
                M('group')->where('one_id='.$top_id)->setField('last_time',time());
            }else{
                M('group')->where('one_id='.$top_id)->setField('three_id',$userid);
                M('group')->where('one_id='.$top_id)->setField('three_time',time());
                M('group')->where('one_id='.$top_id)->setField('last_time',time());
            }
        }else{
            $recommend_id = M('user')->where('userid='.$userid)->getField('recommend_id');
            //判断三层4-7号位置哪个为空，查找为空位置，将新成员放置
            if($group['four_id']==null){
                M('user')->where('userid='.$userid)->setField('parent_id',$group['two_id']);
                M('group')->where('one_id='.$top_id)->setField('four_id',$userid);
                M('group')->where('one_id='.$top_id)->setField('four_time',time());
                M('group')->where('one_id='.$top_id)->setField('last_time',time());

                $data['uid'] = $recommend_id;
                $data['upside_id'] = $group['three_id'];
                $data['money'] = 8000;
                $data['time']=time();
                M('upside_record')->data($data)->add();
            }else{
                if($group['five_id']==null){
                    M('user')->where('userid='.$userid)->setField('parent_id',$group['two_id']);
                    M('group')->where('one_id='.$top_id)->setField('five_id',$userid);
                    M('group')->where('one_id='.$top_id)->setField('five_time',time());
                    M('group')->where('one_id='.$top_id)->setField('last_time',time());

                    $data['uid'] = $recommend_id;
                    $data['upside_id'] = $group['three_id'];
                    $data['money'] = 8000;
                    $data['time']=time();
                    M('upside_record')->data($data)->add();
                }else{
                    if ($group['six_id']==null){
                        M('user')->where('userid='.$userid)->setField('parent_id',$group['three_id']);
                        M('group')->where('one_id='.$top_id)->setField('six_id',$userid);
                        M('group')->where('one_id='.$top_id)->setField('six_time',time());
                        M('group')->where('one_id='.$top_id)->setField('last_time',time());

                        $data['uid'] = $recommend_id;
                        $data['upside_id'] = $group['two_id'];
                        $data['money'] = 8000;
                        $data['time']=time();
                        M('upside_record')->data($data)->add();
                    }else{
                        $data['uid'] = $recommend_id;
                        $data['upside_id'] = $group['two_id'];
                        $data['money'] = 8000;
                        $data['time']=time();
                        M('upside_record')->data($data)->add();

                        //如果7号位置为空，将原小组打散，顶端出局，其余组员组成两个新小组
                        $data['one_id']=$group['two_id'];
                        $data['one_time']=time();
                        M('group')->data($data)->add();
                        M('group')->where('one_id='.$group['two_id'])->setField('two_id',$group['four_id']);
                        M('group')->where('one_id='.$group['two_id'])->setField('two_time',time());
                        M('user')->where('userid='.$group['four_id'])->setField('parent_id',$group['two_id']);
                        M('group')->where('one_id='.$group['two_id'])->setField('three_id',$group['five_id']);
                        M('group')->where('one_id='.$group['two_id'])->setField('three_time',time());
                        M('user')->where('userid='.$group['five_id'])->setField('parent_id',$group['two_id']);
                        M('group')->where('one_id='.$group['two_id'])->setField('last_time',time());
                        $dbtb['one_id']=$group['three_id'];
                        $dbtb['one_time']=time();
                        M('group')->data($dbtb)->add();
                        M('group')->where('one_id='.$group['three_id'])->setField('two_id',$group['six_id']);
                        M('group')->where('one_id='.$group['three_id'])->setField('two_time',time());
                        M('user')->where('userid='.$group['six_id'])->setField('parent_id',$group['three_id']);
                        M('group')->where('one_id='.$group['three_id'])->setField('three_id',$userid);
                        M('group')->where('one_id='.$group['three_id'])->setField('three_time',time());
                        M('user')->where('userid='.$userid)->setField('parent_id',$group['three_id']);
                        M('group')->where('one_id='.$group['three_id'])->setField('last_time',time());
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
        $upside_list = M('upside_record')->where('upside_id='.$top_id)->select();
        foreach($upside_list as $k=>$v){
            $frozen_money = M('store')->where('uid='.$v['uid'])->getField('frozen_money');
            if($frozen_money>=8000){
                $res = M('store')->where('uid='.$v['uid'])->setDec('frozen_money',$v['money']);
                if($res){
                    $rnn = M('store')->where('uid='.$v['uid'])->setInc('buycar_money',$v['money']);
                    $cn = M('upside_record')->where('id='.$v['id'])->delete();
                }
            }else{
                $rcc = M('store')->where('uid='.$v['uid'])->setInc('buycar_money',$v['money']);
                $rcs = M('upside_record')->where('id='.$v['id'])->delete();
            }
        }

        $recommend_id = M('user')->where('userid='.$top_id)->getField('recommend_id');
        $ree=M('store')->where('uid='.$top_id)->setInc('bonus',2500);
        if($ree){
            $information['uid']=$top_id;
            $information['money']=2500;
            $information['reason']="出局奖励";
            $information['time']=time();
            $rcm = M('bonus_record')->data($information)->add();

            $nn['uid']=$top_id;
            $nn['money'] = "+2500";
            $nn['reason'] = "出局奖励";
            $nn['time'] = time();
            $recc = M('getBonus_record')->data($nn)->add();
        }
        if($recommend_id==1){
            $data['one_id']=$top_id;
            $data['one_time']=time();
            $data['last_time']=time();
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

    public function userRecommendStructure(){
        if(I('post.')){
            $account = I('post.account');
            $userid = M('user')->where("account='".$account."'")->getField('userid');
            $db_group = M('group');
            $is_top = $db_group->where('one_id='.$userid)->find();
            if($is_top){
                $structureInfo=$this->getstructureInfo($is_top);
                //dump($structureInfo);die;
            }else {
                $parent_id = M('user')->where('userid=' . $userid)->getField('parent_id');
                $is_top = $db_group->where('one_id=' . $parent_id)->find();
                if ($is_top) {
                    $structureInfo = $this->getstructureInfo($is_top);
                } else {
                    $parent_id_two = M('user')->where('userid=' . $parent_id)->getField('parent_id');
                    $is_top = $db_group->where('one_id=' . $parent_id_two)->find();
                    $structureInfo = $this->getstructureInfo($is_top);
                }
            }
            $this->assign('structureInfo',$structureInfo);
        }
        $this->display();
    }

    //查询分组结构
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

    public function buyCarApply(){
        $start_time = strtotime(I('start_time'));
        $end_time = strtotime(I('end_time'));

        if($start_time && $end_time){
            $where['time'] = array('between',"$start_time,$end_time");
        }
        $tb_user=M('applycar');

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
            ->field('id,uid,username,phone,account,time,status')
            ->order('id desc ')
            ->select();

        $this->assign(array(
            'userArr'=>$userArr,
            'pageshow'=>$pageshow,
        ));
        $this->display();
    }

    //申请购车审核
    public function examine(){
        $id =I('get.id');
        $res = M('applycar')->where('id='.$id)->setField('status',1);
        if($res){
            echo "<script>alert('审核成功');location.href='".U('UserAdministration/buyCarApply')."'</script>";
            exit();
        }else{
            echo "<script>alert('审核失败');location.href='".U('UserAdministration/buyCarApply')."'</script>";
            exit();
        }

    }

    public function agentExamine(){
        $start_time = strtotime(I('start_time'));
        $end_time = strtotime(I('end_time'));

        if($start_time && $end_time){
            $where['time'] = array('between',"$start_time,$end_time");
        }
        $tb_user=M('apply_agent');
        if(I('condition')){
            $we = I('condition');
            $value=trim(I('text'));
            if($we=="username"){
                $where[$we] =array('like',"%$value%");
            }else {
                $where[$we] = $value;
            }
        }
        $where['status']=0;
        $pagesize =10;
        $p = getpage($tb_user, $where, $pagesize);
        $pageshow   = $p->show();

        $userArr=$tb_user->where($where)
            ->field('id,uid,type,province,city,area,time,status')
            ->order('id desc ')
            ->select();
        foreach ($userArr as $k=>$v){
            $userInfo = M('user')->where('userid='.$v['uid'])->find();
            $userArr[$k]['username']=$userInfo['username'];
            $userArr[$k]['account']=$userInfo['account'];
            $userArr[$k]['phone']=$userInfo['phone'];
        }
        $this->assign(array(
            'userArr'=>$userArr,
            'pageshow'=>$pageshow,
        ));
        $this->display();
    }

    public function agentExamine_userMessage(){
        $id = I('get.id');
        $orderInfo = M('apply_agent')->where('id='.$id)->find();
        $user = M('user');
        $userInfo = $user->where("userid=".$orderInfo['uid'])->find();
        $storeInfo = M('store')->where('uid='.$orderInfo['uid'])->find();
        $orderInfo['account']=$userInfo['account'];
        $orderInfo['phone']=$userInfo['phone'];
        $orderInfo['register_time']=$userInfo['time'];
        $orderInfo['money']=$storeInfo['buycar_money'];
        $this->assign('userInfo',$orderInfo);
        $this->display();
    }

    public function deadAgent(){
        $now_time = time();
        $limit_time = $now_time-86400*60;
        $start_time = strtotime(I('start_time'));
        $end_time = strtotime(I('end_time'));

        if($start_time && $end_time){
            $where['time'] = array('between',"$start_time,$end_time");
        }
        $tb_user=M('group');
        if(I('condition')){
            $we = I('condition');
            $value=trim(I('text'));
            if($we=="username"){
                $where[$we] =array('like',"%$value%");
            }else {
                $where[$we] = $value;
            }
        }
        $where['last_time'] = array('lt',$limit_time);
        $pagesize =10;
        $p = getpage($tb_user, $where, $pagesize);
        $pageshow   = $p->show();

        $userArr=$tb_user->where($where)
            ->order('last_time desc ')
            ->select();
        foreach($userArr as $k=>$v){
            $userInfo = M('user')->where('userid='.$v['one_id'])->find();
            $userArr[$k]['account']=$userInfo['account'];
            $userArr[$k]['username']=$userInfo['username'];
        }
        $this->assign(array(
            'userArr'=>$userArr,
            'pageshow'=>$pageshow,
        ));
        $this->display();
    }

    public function deadAgent_HrefPage(){
        $id = I('get.id');
        $groupInfo = M('group')->where('id='.$id)->find();
        $one_userInfo = M('user')->where('userid='.$groupInfo['one_id'])->find();
        $groupInfo['one_account']=$one_userInfo['account'];
        $groupInfo['one_username']=$one_userInfo['username'];
        if($groupInfo['two_id']){
            $two_userInfo = M('user')->where('userid='.$groupInfo['two_id'])->find();
            $groupInfo['two_account'] = $two_userInfo['account'];
            $groupInfo['two_username'] = $two_userInfo['username'];
        }
        if($groupInfo['three_id']){
            $three_userInfo = M('user')->where('userid='.$groupInfo['three_id'])->find();
            $groupInfo['three_account'] = $three_userInfo['account'];
            $groupInfo['three_username'] = $three_userInfo['username'];
        }
        if($groupInfo['four_id']){
            $four_userInfo = M('user')->where('userid='.$groupInfo['four_id'])->find();
            $groupInfo['four_account'] = $four_userInfo['account'];
            $groupInfo['four_username'] = $four_userInfo['username'];
        }
        if($groupInfo['five_id']){
            $five_userInfo = M('user')->where('userid='.$groupInfo['five_id'])->find();
            $groupInfo['five_account'] = $five_userInfo['account'];
            $groupInfo['five_username'] = $five_userInfo['username'];
        }
        if($groupInfo['six_id']){
            $six_userInfo = M('user')->where('userid='.$groupInfo['six_id'])->find();
            $groupInfo['six_account'] = $six_userInfo['account'];
            $groupInfo['six_username'] = $six_userInfo['username'];
        }

        $this->assign('groupInfo',$groupInfo);
        $this->display();
    }

    public function develop_agent(){
        $id = I('get.id');
        $agentInfo = M('apply_agent')->where('id='.$id)->find();
        if($agentInfo['type']==1){
            $data['uid']=$agentInfo['uid'];
            $data['province']=$agentInfo['province'];
            $data['province_code']=$agentInfo['province_code'];
            $data['time']=time();
            $res = M('agent_province')->data($data)->add();
            $rem = M('user')->where('userid='.$agentInfo['uid'])->setField('agent_leve',1);
            $rec = M('apply_agent')->where('id='.$id)->setField('status',1);
        }
        if($agentInfo['type']==2){
            $data['uid']=$agentInfo['uid'];
            $data['province']=$agentInfo['province'];
            $data['province_code']=$agentInfo['province_code'];
            $data['city']=$agentInfo['city'];
            $data['city_code']=$agentInfo['city_code'];
            $data['time']=time();
            $res = M('agent_city')->data($data)->add();
            $rem = M('user')->where('userid='.$agentInfo['uid'])->setField('agent_leve',2);
            $rec = M('apply_agent')->where('id='.$id)->setField('status',1);
        }
        if($agentInfo['type']==3){
            $data['uid']=$agentInfo['uid'];
            $data['province']=$agentInfo['province'];
            $data['province_code']=$agentInfo['province_code'];
            $data['city']=$agentInfo['city'];
            $data['city_code']=$agentInfo['city_code'];
            $data['area']=$agentInfo['area'];
            $data['area_code']=$agentInfo['area_code'];
            $data['time']=time();
            $res = M('agent_area')->data($data)->add();
            $rem = M('user')->where('userid='.$agentInfo['uid'])->setField('agent_leve',3);
            $rec = M('apply_agent')->where('id='.$id)->setField('status',1);
        }
        if($res&&$rem&&$rec){
            echo "<script>alert('提升代理成功');location.href='".U('UserAdministration/agentExamine')."'</script>";
            exit();
        }else{
            echo "<script>alert('提升代理失败');location.href='".U('UserAdministration/agentExamine')."'</script>";
            exit();
        }
    }

    public function regionAgent(){
        $tb_agent_province = M('agent_province');
        $province_where=array();
        $province_pagesize =10000;
        $p = getpage($tb_agent_province, $province_where, $province_pagesize);
        $province_pageshow   = $p->show();

        $provinceArr=$tb_agent_province->where($province_where)
            ->order('id desc ')
            ->select();
        foreach ($provinceArr as $k=>$v){
            $userInfo = M('user')->where('userid='.$v['uid'])->find();
            $userSum = M('user')->where('province_code='.$v['province_code'])->count();
            $userid_array = M('user')->where('province_code='.$v['province_code'])->getField('userid',true);
            $buycar_money_sum=0;
            if($userid_array) {
                $condition['uid'] = array('in', $userid_array);
                $buycar_money_array = M('store')->where($condition)->getField('buycar_money', true);
                $buycar_money_sum = array_sum($buycar_money_array);
            }
            $provinceArr[$k]['userSum']=$userSum;
            $provinceArr[$k]['buycar_money_sum']=$buycar_money_sum;
            $provinceArr[$k]['account']=$userInfo['account'];
            $provinceArr[$k]['username']=$userInfo['username'];
        }
        $this->assign(array(
            'provinceArr'=>$provinceArr,
            'province_pageshow'=>$province_pageshow,
        ));

        $tb_agent_city = M('agent_city');
        $city_where=array();
        $city_pagesize =10000;
        $p = getpage($tb_agent_city, $city_where, $city_pagesize);
        $city_pageshow   = $p->show();

        $cityArr=$tb_agent_city->where($city_where)
            ->order('id desc ')
            ->select();
        foreach ($cityArr as $k=>$v){
            $userInfo = M('user')->where('userid='.$v['uid'])->find();
            $userSum = M('user')->where('city_code='.$v['city_code'])->count();
            $userid_array = M('user')->where('city_code='.$v['city_code'])->getField('userid',true);
            $buycar_money_sum=0;
            if($userid_array) {
                $condition['uid'] = array('in', $userid_array);
                $buycar_money_array = M('store')->where($condition)->getField('buycar_money', true);
                $buycar_money_sum = array_sum($buycar_money_array);
            }
            $cityArr[$k]['userSum']=$userSum;
            $cityArr[$k]['buycar_money_sum']=$buycar_money_sum;
            $cityArr[$k]['account']=$userInfo['account'];
            $cityArr[$k]['username']=$userInfo['username'];
        }
        $this->assign(array(
            'cityArr'=>$cityArr,
            'city_pageshow'=>$city_pageshow,
        ));

        $tb_agent_area = M('agent_area');
        $area_where=array();
        $area_pagesize =10000;
        $p = getpage($tb_agent_area, $area_where, $area_pagesize);
        $area_pageshow   = $p->show();

        $areaArr=$tb_agent_area->where($area_where)
            ->order('id desc ')
            ->select();
        foreach ($areaArr as $k=>$v){
            $userInfo = M('user')->where('userid='.$v['uid'])->find();
            $userSum = M('user')->where('area_code='.$v['area_code'])->count();
            $userid_array = M('user')->where('area_code='.$v['area_code'])->getField('userid',true);
            $buycar_money_sum=0;
            if($userid_array) {
                $condition['uid'] = array('in', $userid_array);
                $buycar_money_array = M('store')->where($condition)->getField('buycar_money', true);
                $buycar_money_sum = array_sum($buycar_money_array);
            }
            $areaArr[$k]['userSum']=$userSum;
            $areaArr[$k]['buycar_money_sum']=$buycar_money_sum;
            $areaArr[$k]['account']=$userInfo['account'];
            $areaArr[$k]['username']=$userInfo['username'];
        }
        $this->assign(array(
            'areaArr'=>$areaArr,
            'area_pageshow'=>$area_pageshow,
        ));
        $this->display();
    }
}