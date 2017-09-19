<?php
namespace Admin\Controller;
use Think\Controller;
class WealthController extends CommonController {
	//首页
	public function index(){
        echo '<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />';
			
            $where['account']=I('account');

        if ($where['account']){
            $udb = M('user'); 
		    $userdata = $udb->field('userid')->where($where)->find();
		
        if(!$userdata){
				echo '<script>alert("账号不存在")</script>';
				echo '<script>javascript:history.back(-1)</script>';		
		}else{
            $uid=$userdata['userid'];
            $userdata=M('store')->where('uid='.$uid.'')->find();






			}
		}


		$this->userdata=$userdata;
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
       		  echo '<script>alert("注册成功");</script>';
          	  echo "<script>window.location.href='".U('Admin/Spadmin/index')."';</script>";
		}

	
	}


    #给用户赠送激活码
    public function give_jhm(){
 
        echo '<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />';
        if(!IS_POST){
         $this->display();
        } else{
            $dbcode=M('nzcode');
           


            $udb = M('user');
            $account=I('post.mobile');
            $jhm_num=I('post.jhm_num');

            $num11=$dbcode->where("cd_state=0")->count();
            if (($jhm_num*2+10)>$num11) {

                echo "<script>alert('电话卡不足,请重新输入');</script>";
                echo "<script>javascript:history.back(-1);</script>";
                return;
            }

            $user_arr=$udb->where("mobile=$account")->field('userid,jhcode')->find();

            #修改对应用户的激活码  setDec()减 setInc加
            $bool=$udb->where("mobile = $account")->setInc('jhcode',$jhm_num); // 加code数据
            $cddb=M('nzcode');
            $cdkey=$cddb->where("cd_state = 0 ")->limit($jhm_num)->select();

            $shushu = count($cdkey);

            $cdid=array();
            foreach ($cdkey as $k => $v) {
                $cdid[]=$v['cd_id'];
                $addcode[]=array('act_uid'=>$user_arr['userid'],
                    'act_actcode'=>$v['cd_codeid'],
                    'act_actpsw'=>$v['cd_psw'],
                    'act_time'=>time(),
                    'act_title'=>'官方购买',
                    'act_price'=>'30',
                    'act_accno'=>'',
                    'act_note'=>1
                                  );
            }
      
            $actdb = M('nzactivation');
           /* p($addcode);
            exit;*/
            $actdb->addAll($addcode);
          // echo  $actdb->_sql();
            
            $cdwhere['cd_id']=array('in',$cdid);
            $ccc = $cddb->where($cdwhere)->setField('cd_state','1');

            if($bool){
                $cdrdb = M('nzcdrecord');
                $dcrdata['cr_uid'] = $user_arr['userid'];
                $dcrdata['cr_record'] ="官方购买--激活码*".$jhm_num.",电话卡*".$jhm_num;
                $dcrdata['cr_num'] =$jhm_num;
                $dcrdata['cr_time'] =time();
                $tiaojian4 = $cdrdb->add($dcrdata);
                echo "<script>alert('赠送成功');</script>";
                echo "<script>window.location.href='".U('Admin/Wealth/give_jhm')."';</script>";

            }
        }
    }
	
}

