<?php
namespace Admin\Controller;
use Think\Controller;
class SpadminController extends CommonController {
	//首页
	public function index(){
		$spdb=M('nzspuser');
		$spall = $spdb->select();
	
		$this->spall=$spall;
		$this->display();
	}
    #查出管理员日志
    public function manage_log(){

        $cwhere=I('where');
        $start_time = strtotime(I('start_time'));
        $end_time = strtotime(I('end_time'));

        if($start_time && $end_time){
            $where['m.time'] = array('between',"$start_time,$end_time");
        }


        if(I('account')){
            $we = 'user_account';
            $value=trim(I('account'));
            if($we!=''){
                $where[$we] =array('like',"%$value%");
            }else{
                $where[$we] =$value;
            }
        }


        $clockwhere = 'clockwhere_'.MODULE_NAME.CONTROLLER_NAME.ACTION_NAME ;
        if($cwhere){
            session($clockwhere,null);
            session($clockwhere,$where);
        }

        $where = session($clockwhere)?session($clockwhere):$where;

        $nzmanage_log=M("nzmanage_log");
        $count   = $nzmanage_log->alias('m')->join('syd_nzspuser as s ON m.namage_id=s.sp_id')
            ->field('m.*,s.sp_username')
            ->where($where)
            ->order('m.log_id desc')
            ->count();
        $Page=new \Think\Page($count,20);


        $Page->setConfig('header', '<li class="rows">共<b>%TOTAL_ROW%</b>条记录&nbsp;第<b>%NOW_PAGE%</b>页/共<b>%TOTAL_PAGE%</b>页</li>');
        $Page->setConfig('prev', '上一页');
        $Page->setConfig('next', '下一页');
        $Page->setConfig('last', '末页');
        $Page->setConfig('first', '首页');
        $Page->setConfig('theme', '%FIRST%%UP_PAGE%%LINK_PAGE%%DOWN_PAGE%%END%%HEADER%');
        $Page->lastSuffix = false;//最后一页不显示为总页数


        $nzmanage_log_arr   = $nzmanage_log->alias('m')->join('syd_nzspuser as s ON m.namage_id=s.sp_id')
            ->field('m.*,s.sp_username')
            ->where($where)
            ->order('m.log_id desc')
            ->limit($Page->firstRow.','.$Page->listRows)
            ->select();

        // p($syd_nzactivation_arr);
        // $pageshow=$Page->pageInfo();
        $show=$Page->show();
        $this->assign('pageinfo',$show);
        $this->assign(
            array(
                'aar_log' => $nzmanage_log_arr,
                'where'=> $where
            )
        );
        $this->display();

    }
//用户日志
   public function user_log(){

        $cwhere=I('where');
        $start_time = strtotime(I('start_time'));
        $end_time = strtotime(I('end_time'));

        if($start_time && $end_time){
            $where['m.time'] = array('between',"$start_time,$end_time");
        }


        if(I('sip')){
            $we = 'log_ip';
            $value=trim(I('sip'));
            if($we!=''){
                $where[$we] =array('like',"%$value%");
            }else{
                $where[$we] =$value;
            }
        }
		if(I('account')){
			$account=I('account');
			$uid = M('user')->where("account='".$account."'")->getField('userid');
			$where['uid']=$uid;
		}


        $clockwhere = 'clockwhere_'.MODULE_NAME.CONTROLLER_NAME.ACTION_NAME ;
        if($cwhere){
            session($clockwhere,null);
            session($clockwhere,$where);
        }

        $where = session($clockwhere)?session($clockwhere):$where;



        $user_log=M("user_log");
        $count   = $user_log->alias('l')->join('syd_user as u ON l.uid=u.userid')
            ->field('l.*,u.account,u.username')
            ->where($where)
            ->order('l.id desc')
            ->count();
        $Page=new \Think\Page($count,20);


        $Page->setConfig('header', '<li class="rows">共<b>%TOTAL_ROW%</b>条记录&nbsp;第<b>%NOW_PAGE%</b>页/共<b>%TOTAL_PAGE%</b>页</li>');
        $Page->setConfig('prev', '上一页');
        $Page->setConfig('next', '下一页');
        $Page->setConfig('last', '末页');
        $Page->setConfig('first', '首页');
        $Page->setConfig('theme', '%FIRST%%UP_PAGE%%LINK_PAGE%%DOWN_PAGE%%END%%HEADER%');
        $Page->lastSuffix = false;//最后一页不显示为总页数


        $user_log_arr =  $user_log->alias('l')->join('syd_user as u ON l.uid=u.userid')
            ->field('l.*,u.account,u.username')
            ->where($where)
            ->order('l.id desc')
            ->limit($Page->firstRow.','.$Page->listRows)
            ->select();

        // p($syd_nzactivation_arr);
        // $pageshow=$Page->pageInfo();
        $show=$Page->show();
        $this->assign('pageinfo',$show);
        $this->assign(
            array(
                'user_log' => $user_log_arr,
                'where'=> $where
            )
        );
        $this->display();

    }

    #管理员修改账号和密码
    public function manage_up(){


        $spdb=M('nzspuser');
        if ($_GET['sp_id']) {
            $sp_id=$_GET['sp_id'];
        }else{
            $sp_id=$_SESSION['sp_user'];
        }
        $sp_arr=$spdb->where("sp_id=$sp_id")->field('sp_id,sp_username,sp_password,sp_salt')->find();
        if(!IS_POST){
            $this->assign('sp_arr',$sp_arr);
            $this->display();
        }else{
            #修改资料

            $account=I('post.account');
            $y_pw=I('post.y_pw');
            $x_pw=I('post.new_pw');
            $x_pwr=I('post.new_pwr');
            $post_sp_id=I('post.sp_id');

            if ($y_pw=='') {
               $data['sp_username']=$account;
               $sp_update=$spdb->where("sp_id=$post_sp_id")->save($data);
                if ($sp_update) {
                    echo "<script>alert('修改成功');</script>";
                    echo "<script>window.location.href='".U('Admin/Spadmin/manage_up')."';</script>";
                }else{
                    echo "<script>alert('修改失败');</script>";
                    echo "<script>javascript:history.back(-1);</script>";die;
                }
            }else{
              $input_pw=md5(md5($y_pw).$sp_arr['sp_salt']);
                if ($input_pw!=$sp_arr['sp_password']) {
                    echo "<script>alert('原密码错误');</script>";
                    echo "<script>javascript:history.back(-1);</script>";die;
                }

                if($x_pw!=$x_pwr){
                    echo "<script>alert('两次输入密码不一致');</script>";
                    echo "<script>javascript:history.back(-1);</script>";die;
                }

                $data2['sp_salt'] = substr(md5(time()),0,4);
                $data2['sp_username']=$account;
                $data2['sp_password']=md5(md5($x_pwr).$data2['sp_salt']);
                $sp_update=$spdb->where("sp_id=$post_sp_id")->save($data2);

                if ($sp_update) {
                    echo "<script>alert('修改成功');</script>";
                    echo "<script>window.location.href='".U('Admin/Spadmin/manage_up')."';</script>";
                }else{
                    echo "<script>alert('修改失败');</script>";
                    echo "<script>javascript:history.back(-1);</script>";die;

                }
            }

        }

    }

    //是否开启 注册后 7 不激活 锁定     和  租用7 天后  锁定 
    public function lockstart(){
        $lockstart=F('lock_start','','./Public/data/');
        p($lockstart);
        $this->lockstart = $lockstart;
        $this->display();
    }

        //接收修改数据
    public function savelockstart(){
        $data = I('post.');
         F('lock_start',$data,'./public/data/');
        echo "<script>alert('修改成功!'); history.back(-1);</script>";

    }


}

