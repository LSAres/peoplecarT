<?php
namespace User\Controller;
use Think\Controller;
class GoldController extends CommonController {

    public function index(){

        $where['s.sell_state'] = 0;
        $time48 = time()-172800;
        $time = time();
        $where['s.sell_time'] = array('between',"$time48,$time");
        $user   = M("user");
        $Order  = M("sell");
        $Order_arr= $Order->alias('s')->join('syd_userinfo as u ON u.uif_userid = s.userid','left')->field('s.sell_id,s.sell_number,s.sell_time,u.uif_name,u.uif_wechat,u.uif_mobile,u.uif_alipay,u.uif_cardnum')->where($where)->order('sell_time desc')->limit(10)->select();


        $userid=session('userid');
        $user_arr=$user->where('userid='.$userid)->getField('wealthb');

        $this->assign(array(
              "user_arr" => $user_arr,
              "sell_arr" => $Order_arr,

          ));
		    $this->display();
    }


    public function WD(){

        $userid=session('userid');
        $pagesize = 14;
        $where['userid']=$userid;

        $user      = M("user");
        $Order     = M("nzwd");

        $p = getpage($Order, $where, $pagesize);
        $Order_arr  = $Order->where($where)->order('wd_id desc')->select();

        // echo $Order->_SQL();

        $pageshow   = $p->show();


        $user_arr=$user->where('userid='.$userid)->getField('wealthb');


        $this->assign(array(
              "user_arr" => $user_arr,
              "wd_arr" => $Order_arr,
              "pageshow" => $pageshow,
              "12" => 12
          ));

        $this->display("jygl");
    }


    public function trade1(){
      $user      = M("user");
      $Order     = M("sell");

      $userid=session('userid');
      //接收参数
      $sell_id = I("get.sell_id");
      if ($sell_id) {

       
          //更新转态
         $duifang =  $Order->where("sell_id=".$sell_id."")->getField('userid');


         $uindb = M('userinfo');
        $dfuserinfo = $uindb->where("uif_userid = $duifang and uif_mobile != '' and uif_name != '' and uif_alipay != '' and uif_cardnum !='' and  uif_wechat !='' ")->getField('uif_id');
        if(!$dfuserinfo){
          echo "<script>alert('对方信息不全,不能交易')</script>";
          echo "<script>history.back(-1);</script>";
          return;
        }



             $arr = array(
                "sell_state"=>1,
                "buy_userid"=>$userid
            );
          $Order->where("sell_id=".$sell_id."")->save($arr);
      }  
      
      $pagesize = 5;
      $where['syd_sell.buy_userid']=$userid;


      $p = getpage($Order, $where, $pagesize);
                                                        //用户信息  = 挂单者
      $Order_arr  = $Order->join('syd_userinfo as u ON u.uif_userid = syd_sell.userid','left')->field('syd_sell.sell_id,syd_sell.sell_number,syd_sell.sell_state,syd_sell.sell_time,u.uif_name,u.uif_wechat,u.uif_mobile,u.uif_cardnum')->where($where)->order('syd_sell.sell_state asc,syd_sell.sell_time desc')->select();

        $pageshow   = $p->show();
        

        $this->assign(array(
              "sell_arr" => $Order_arr,
              "pageshow" => $pageshow,
              "12" => 12
          ));

        $this->display("jbgmjl");
    }
    //确认记录   收钱  
    public function trade2(){
      $user      = M("user");
      $Order     = M("sell");

      $userid=session('userid');
      
      
      $pagesize = 5;
                                // 状态为已购
      $where['syd_sell.userid']=$userid;


      $p = getpage($Order, $where, $pagesize);
      $Order_arr  = $Order->join('syd_userinfo as u ON u.uif_userid=syd_sell.buy_userid','left')->field('syd_sell.sell_id,syd_sell.sell_number,syd_sell.sell_state,syd_sell.sell_time,u.uif_name,u.uif_wechat,u.uif_mobile,u.uif_cardnum')->where($where)->order('syd_sell.sell_state asc ')->select();

        $pageshow   = $p->show();
        
       // p($Order_arr);
       // die;
        $this->assign(array(
              "sell_arr" => $Order_arr,
              "pageshow" => $pageshow,
              "12" => 12
          ));

        $this->display("jbmcjl");
    }
     //查出金币转账页面数据
    public function jb(){

      $user      = M("user");
      $Order     = M("nzacc");

      $userid=session('userid');

      $pagesize = 5;
      $where="userid=".$userid."";

      $p = getpage($Order, $where, $pagesize);

      $Order_arr  = $Order->join('syd_userinfo ON syd_userinfo.uif_userid=syd_nzacc.acc_userid','left')->field("syd_userinfo.uif_name,syd_nzacc.acc_id,syd_userinfo.uif_mobile,syd_nzacc.acc_fee,syd_nzacc.acc_wealthb,syd_nzacc.acc_time,syd_nzacc.acc_state")->where($where)->order('acc_id desc')->select();
       $pageshow   = $p->show();

 //echo $Order->_SQL();

$where2="acc_userid=".$userid."";
$two = getpage($Order, $where2, $pagesize);

$Order_arr2 = $Order->join('syd_userinfo ON syd_userinfo.uif_userid=syd_nzacc.acc_userid','left')->field("syd_userinfo.uif_name,syd_nzacc.acc_id,syd_userinfo.uif_mobile,syd_nzacc.acc_wealthb,syd_nzacc.acc_fee,syd_nzacc.acc_time,syd_nzacc.acc_state")->where($where2)->order('acc_id desc')->select();
//p($Order_arr2);
$pagetwo  = $two->show();

     
      $wealthb=$user->where('userid='.$userid)->getField('wealthb');



      // dump($Order_arr);
      $this->assign(array(
              "arrData_zzzd" => $Order_arr,   //转出
              "Order_arr2" =>$Order_arr2,
              "wealthb" => $wealthb,
              "pageshow" => $pageshow,
              'pagetwo'=>$pagetwo,
              "12" => 12
          ));

        //查出原金币有多少
        // $jb=M('user')->where('userid='.$userid);
        // $this->arrData_jb=$jb->select();

        //查出转账账单
        // $sql="select u.true_name,a.acc_id,a.acc_wealthb,a.acc_time,a.acc_state from syd_user u inner join syd_nzacc a on u.userid=a.acc_userid order by a.acc_id desc ";
        // $arrData3=M()->query($sql);
        // $this->arrData_zzzd=$arrData3;
        $this->display('jbzz');
    }

    //财富币转账
    public function ajax_cfb(){


        $user      = M("user");
        $acc       =M('nzacc');
        $userid    =session('userid');

        $wealthb=I('post.wealthb');

        //取整
        $wealthb = floor($wealthb/10)*10;

    
        if($wealthb<10 || $wealthb>5000){
           return;
        }

        $mobile=I('post.mobile');

        //手机号长度
        if (strlen($mobile) != 11) {
          return;
        }

        $zzjjmobile = $_SESSION['mobile'];


  $safety_pw=I('post.safety_pw');
  $check_arr=$user->where("userid=$userid")->field("safety_pw,safety_salt")->find();   
  $from_pw=md5(md5($safety_pw).$check_arr['safety_salt']);  

  if($check_arr['safety_pw']!==$from_pw  ){ 
        $cfdata['status']  = "aqmcw";
        $cfdata['content'] = '安全密码错误';
        $this->ajaxReturn($cfdata);
      return;
    }
   
        $sqlwealthb=$user->where('userid='.$userid)->getField('wealthb');//查出当前拥有的金币
        $zzyh=$user->where('mobile='.$mobile)->find();  //查出收款用户信息


        if(!empty($zzyh['mobile'])){ //判断转账用户是否存在          
		    if($zzyh['mobile']== $zzjjmobile){ //判断转账账号是否和自己账号相等
        $cfdata['status']  = "notnext";
        $cfdata['content'] = '自己不需要转给自己';
        $this->ajaxReturn($cfdata);
                 return;
			  }else{
                  if($sqlwealthb >= $wealthb){ //判断当前拥有的金币够不够转账

                     $user->startTrans();//开启事务

                    //  好友金币  + b*90%         自己金币 - b*100%
                     $zj=$user->where('userid='.$zzyh['userid'])->setInc('wealthb',$wealthb*0.9); //好友币增加

                     $js=$user->where('userid='.$userid)->setDec('wealthb',$wealthb); //自己的币减少

                     if($zj && $js){ //判断事务是否成功
                         $user->commit(); //提交事务成功
                        //记录转账信息（成功）
                         $data['userid']=$userid;
                         $data['acc_userid']=$zzyh['userid'];
                         $data['acc_wealthb']=$wealthb;
                         $data['acc_fee']=$wealthb*0.9;
                         $data['acc_time']=time();
                         $data['acc_state']='1';
                         $acc->add($data);
                    $cfdata['status']  = "success";
                    $cfdata['content'] = '转账成功';
                    $this->ajaxReturn($cfdata);
                    

                     }else{
                     $M->rollback(); //提交事务有错回滚
                     $cfdata['status']  = "error";
                    $cfdata['content'] = '转账失败';
                    $this->ajaxReturn($cfdata);
                     }

                 }else{
                     // echo "<script>alert('您没有那么多金币');window.location.href='".U('User/index/jb')."';</script>";
                     // redirect (U('User/index/jb'), 2, '页面跳转中...');
                    $cfdata['status']  = "notm";
                    $cfdata['content'] = '您没有那么多金币';
                    $this->ajaxReturn($cfdata);

                     
                 }
			}
        
		}else{
        $cfdata['status']  = "userbcz";
        $cfdata['content'] = '用户不存在';
        $this->ajaxReturn($cfdata);


        }
    }


    public function ajax_sell(){
        $user=M('user');
        $sell=M('sell');
        $userid=session('userid');


        //用户有没有信息 没有不给提交
        $uindb = M('userinfo');

        $userinfo = $uindb->where("uif_userid = $userid and uif_mobile != '' and uif_name != '' and uif_alipay != '' and uif_cardnum !='' and  uif_wechat !='' ")->find();
        //echo $uindb->_sql();
   
        if(!$userinfo){
          $redata['status']  = 'xxxkong';
          $redata['content'] = '请填写个人信息';
          $this->ajaxReturn($redata);
        }


        $wealthb = $user->where("userid=".$userid."")->getField('wealthb');
        $sell_number = intval(I("post.wealthb"));
        $sell_number = floor( $sell_number/100)*100;

        $safety_pw=I('post.aqmm');
        $check_arr=$user->where("userid=$userid")->field("safety_pw,safety_salt")->find();   
        $from_pw=md5(md5($safety_pw).$check_arr['safety_salt']);  
        
        if($check_arr['safety_pw']!==$from_pw){
          $redata['status']  = 'aqmicw';
          $redata['content'] = '安全密码错误';
          $this->ajaxReturn($redata);
          return; 
        } 


        if ($sell_number < 500 && $sell_number > 5000){
          $redata['status']  = 'yqbd';
          $redata['content'] = '输入500-5000金额';
          $this->ajaxReturn($redata);
        }

         if($wealthb < $sell_number){
          $redata['status']  = 'qbz';
          $redata['content'] = '财富币不足';
          $this->ajaxReturn($redata);
         }

            //开启事务
            $user->startTrans();

            //修改金额
            $i_wealthb = $wealthb-$sell_number;
            $arr['wealthb'] = $i_wealthb;
          $condition1 = $user->where("userid=".$userid."")->save($arr);


            $data = array(
            "sell_number"=>$sell_number,
            "seel_truenum"=>$sell_number*0.8,
            "userid"=>$userid,
            "sell_time"=>time()
             );
          $condition2 = $sell->add($data);
             
         if($condition1 &&  $condition2 ){
            $user->commit();
          $redata['status']  = 'success';
          $redata['content'] = '出售成功！';
          $this->ajaxReturn($redata);
         }else{
          $user->rollback();
          $redata['status']  = 'error';
          $redata['content'] = '出售失败!';
          $this->ajaxReturn($redata);
         }
        

          

        
    }
    public function ajax_wd(){


        $user=M('user');
        $sell=M('nzwd');
        $userid=session('userid');
        #首先判断安全密码是否正确
        $safety_pw=I('post.safety_pw');
        $check_arr=$user->where("userid=$userid")->field("safety_pw,safety_salt")->find();   
        $from_pw=md5(md5($safety_pw).$check_arr['safety_salt']);  

      if($check_arr['safety_pw']!==$from_pw){ 
         $redata['status']  = 'anqm';
         $redata['content'] = '安全密码错误';
         $this->ajaxReturn($redata);
       }


        
        $sell_number = intval(I("post.wealthb"));
        $sell_number = floor($sell_number/100)*100;      
        if ($sell_number < 100 || $sell_number > 5000) {
                return;
        }

        $wealthb = $user->where("userid=".$userid."")->getField('wealthb');

        $user->startTrans();

        if($wealthb < $sell_number){
            return;
        }

        //修改财富币   减身上的财富币
        $i_wealthb = $wealthb-$sell_number;
        $arr['wealthb'] = $i_wealthb;
        $condition1 = $user->where("userid=$userid")->save($arr);

        //添加挂卖信息
        $data = array(
            "userid"=>$userid,
            "wd_number"=>$sell_number,
            "actual_money"=>$sell_number*0.8,
            "wd_time"=>time()
         );

        $condition2 = $result = $sell->add($data);
            
       if($condition1 && $condition2 ){
            $user->commit();
            $data['status']  = "success";
            $data['content'] = '提交成功！';
            $this->ajaxReturn($data);
       }else{
            $user->rollback();
            $data['status']  = "error";
            $data['content'] = '提交失败';
            $this->ajaxReturn($data);
       }
    }
	

    //图片上传
    public function listimg(){

	echo '<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />';
      $userid=session('userid');
      $sdb = M('sell');
      $listid =I('post.listid');
       $oldimg=$sdb->where("sell_id ={$listid} and buy_userid = {$userid} ")->find();
       if(!$oldimg){
          echo "<script>javascript:history.back(-1);</script>";
          return;
       }

        //图片上传
        $redata = $this->up_img();
        //为真写入数据库
        if($redata['status']){
       
        //有图片,删除旧图
        if($oldimg['sell_img']){
          $old_img ='./Public/'.$oldimg['sell_img'];
          unlink($old_img);
        }

         $sdata=$sdb->where("sell_id ={$listid} and buy_userid = {$userid} ")->setfield('sell_img',$redata['content']);
           if($sdata){
            echo "<script>alert('上传成功')</script>";
            echo "<script>javascript:history.back(-1);</script>";
           }
      }else{
        echo "<script>alert('".$redata['content']."')</script>";
        echo "<script>javascript:history.back(-1);</script>";
      }

    }

    //购买详细
    public function jbgmxq(){
      //p(I('get.'));
      $userid=session('userid');
      $confirm=I('get.confirm');
      $sdb=M('sell');
      $sdata=$sdb->where("sell_id= {$confirm} and buy_userid = {$userid}")->find();
      $uifdb=M('userinfo');
      $usinfo=$uifdb->where("uif_userid = {$sdata['userid']}")->find();
      $jyinfo=array_merge($sdata,$usinfo);
      //p($jyinfo);
      $this->jyinfo=$jyinfo;
      $this->display();
    }
        //购买详细 2
    public function jbgmxq2(){
      //p(I('get.'));
      $userid=session('userid');
      $confirm=I('get.confirm');

      $sdb=M('sell');
                        //定单号为         and   是自己卖的
      $sdata=$sdb->where("sell_id= {$confirm} and userid = {$userid}")->find();
      $uifdb=M('userinfo');
      $usinfo=$uifdb->where("uif_userid = {$sdata['buy_userid']}")->find();

      $jyinfo=array_merge($sdata,$usinfo);

     // p($jyinfo);
      //die;
      $this->jyinfo=$jyinfo;
      $this->display();
    }
    //确认收货
    public function vouchergo(){

	echo '<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />';
	// p(I('get.'));
      $userid=session('userid');
      $sdb = M('sell');
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
          echo "<script>javascript:history.back(-1);</script>";
          return;
       }

       $udb=M('user');
       $udb->startTrans();


       $buyuser=$udb->where("userid = $oldlist[buy_userid]")->find();

       $condition=$udb->where("userid = $oldlist[buy_userid]")->setInc('wealthb',$oldlist['sell_number']*0.9); // 财富币 + 
  
       $setdata = array('sell_confirm'=>'1','sell_state'=>'2');

       $sellcondition=$sdb->where("sell_id ={$listid} and userid = {$userid}")->setField($setdata);
    


       if($condition && $sellcondition){
            $udb->commit();
            echo "<script>alert('确认成功');window.location.href='".U('User/Gold/trade2',array('moduleid'=>'1'))."'</script>";
             return;
       }else{
          $udb->rollback();
           echo "<script>javascript:history.back(-1);</script>";
          return;
       }


    }

    public function ajax_truename(){
            //p(I());
            $mobile =I('post.shouji')+0;
            $udb=M('user');
            $ustn = $udb ->field('true_name,mobile')->where("mobile = {$mobile}")->find();

  
            if($ustn){
              $ustn['true_name']?$ustn['true_name']:$ustn['true_name']='账号存在/用户名为空';

            }


           $ustn?$data=array('status'=>'yzh','content'=>$ustn['true_name']):$data=array('status'=>'error','content'=>'没有这个账号');

           $this->ajaxReturn($data);
           return;
    }

}

