<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/7/22
 * Time: 10:49
 */
namespace Admin\Controller;
use Think\Controller;
class TradingfloorController extends CommonController{
    public function weathb_sell(){

        $cwhere=I('where');
        $start_time = strtotime(I('start_time'));
        $end_time = strtotime(I('end_time'));

        if($start_time && $end_time){
            $where['s.sell_time'] = array('between',"$start_time,$end_time");
        }


        if(I('post.condition')){
            $we = I('post.condition');
            $value=trim(I('post.account'));
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
        $db_sell=M("sell");


        //修改状态
        if(I('post.quan')){

            $quanArr=$_POST['quan'];


            $up_wd_state=array(
                'sell_state'=>2,
            );

            $where['sell_id']=array('in',$quanArr);

               $db_sell->where($where)->save($up_wd_state);
                //echo $db_sell->_sql();
        }

        $time48 = time()-172800;
        $where['s.sell_time'] = array('between',"0,$time48");

         $count   = $db_sell->alias('s')
                            ->join('syd_user as u ON s.userid=u.userid')
                            ->join('syd_userinfo as i ON i.uif_userid=s.userid')
                            ->field('s.*,u.true_name,i.*')
                            ->where($where)
                            ->order('s.sell_id desc')
                            ->count();

        $Page=new \Think\Page($count,20);


        $Page->setConfig('header', '<li class="rows">共<b>%TOTAL_ROW%</b>条记录&nbsp;第<b>%NOW_PAGE%</b>页/共<b>%TOTAL_PAGE%</b>页</li>');
        $Page->setConfig('prev', '上一页');
        $Page->setConfig('next', '下一页');
        $Page->setConfig('last', '末页');
        $Page->setConfig('first', '首页');
        $Page->setConfig('theme', '%FIRST%%UP_PAGE%%LINK_PAGE%%DOWN_PAGE%%END%%HEADER%');
        $Page->lastSuffix = false;//最后一页不显示为总页数


        $db_sell_arr   =$db_sell->alias('s')->join('syd_user as u ON s.userid=u.userid')
            ->join('syd_userinfo as i ON i.uif_userid=s.userid')
            ->field('s.*,u.true_name,i.*')
            ->where($where)
            ->order('s.sell_id desc')
            ->limit($Page->firstRow.','.$Page->listRows)
            ->select();
       /* p($db_sell_arr);
        exit;*/
        // p($syd_nzactivation_arr);
        // $pageshow=$Page->pageInfo();
        $show=$Page->show();
        $this->assign('pageinfo',$show);
        $this->assign(
            array(
                'weathb_sell_arr' => $db_sell_arr,
                'where'=> $where
            )
        );
        $this->display();
    }
    //24小时末打款
    public function playlock(){
        $sedb = M('sell');
        $seall = $sedb ->where('sell_state = 1 and  sell_lock = 1')->select();
        $this->seall = $seall;
        $this->display();
    }

    //确认收货
    public function vouchergo(){
    echo '<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />';
    // p(I('get.'));
      $sdb = M('sell');
       $userid=I('get.userid');  
       $listid =I('get.listid')+0;
     // $listid ='18';

        // 挂单者等于我                 购买者 已经上图                        并且 没有确认
       $oldlist=$sdb->where("sell_id ={$listid} and userid = {$userid} and sell_confirm = 0 ")->find();

       if(!$oldlist['sell_img'] && $oldlist){
        echo "<script>alert('对方还没有上传凭证');</script>";
        echo "<script>javascript:history.back(-1);</script>";
        return;
       }

       if(!$oldlist){
          echo "<script>alert('找不到定单');javascript:history.back(-1);</script>";
          return;
       }

       $udb=M('user');
       $udb->startTrans();


       $buyuser=$udb->where("userid = $oldlist[buy_userid]")->find();
       $condition=$udb->where("userid = $oldlist[buy_userid]")->setInc('wealthb',$oldlist['sell_number']*0.9); // 财富币 + 



       $setdata = array('sell_confirm'=>'1','sell_state'=>'2');
       $sellcondition=$sdb->where("sell_id ={$listid} and userid = {$userid}")->setField($setdata);
    

       //修改  购买者与  挂单者  锁定状态
       
       $uidwhere['userid']=array('in',"$oldlist[buy_userid],$userid");
       $uslock =  $udb->where($uidwhere)->setField("lockuser",'0');


       if($condition && $sellcondition && $uslock){
            $udb->commit();
              echo "<script>alert('确认成功');window.location.href='".U('Tradingfloor/playlock')."'</script>";
             return;           
       }else{
            $udb->rollback();
            echo "<script>javascript:history.back(-1);</script>";
            return;
       }


    }
    // 解除购买者的关系   -->修改挂单时间 为当前的时间   
    public function remove(){

    echo '<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />';
    // p(I('get.'));
    // 
    // 
        $udb = M('user');
        $sdb = M('sell');
        $udb -> startTrans();
        $buy_userid=I('get.buy_userid');  
        $listid =I('get.listid')+0;
       
       //修改 
        $userid = $sdb->where("sell_id ={$listid} and buy_userid = {$buy_userid} and sell_lock = 1 ")->getField('userid');

     if(!$userid){
        echo "<script>alert('定单出错');</script>";
        echo "<script>javascript:history.back(-1);</script>";
        return;
       }


        $setuser = $udb->where("userid = $userid ")->setField('lockuser','0');

       $stlist['sell_time'] = time();
       $stlist['sell_state'] = 0;
       $stlist['buy_userid'] = 0;
       $stlist['sell_img'] = '';
       $stlist['sell_confirm'] = 0;
       $stlist['sell_lock'] = 0;
       $oldlist=$sdb->where("sell_id ={$listid} and buy_userid = {$buy_userid} and sell_lock = 1 ")->setField($stlist);


       if($oldlist){
            $udb->commit();
              echo "<script>alert('修改成功');window.location.href='".U('Tradingfloor/playlock')."'</script>";
             return;           
       }else{
            $udb->rollback();
            echo "<script>alert('修改失败');javascript:history.back(-1);</script>";
            return;
       }

    }
}