<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/8/19
 * Time: 14:15
 */
namespace Mobile\Controller;
use Think\Controller;
use Org\Util\String;
class TradingController extends CommonController
{

	public function sendsms() {
	/*header("Content-Type:text/html;charset=utf-8");*/
	$userid=session('userid');
	$mobile = M('user')->where('userid='.$userid)->getField('mobile');
	
	  //$trade_code = String::randString(6, 1);
	  $trade_code=rand(100000,999999);
      session('trade_code', $trade_code);
	  $msg['code'] = $trade_code;
		$msg = json_encode($msg);
		$url = "http://api.sms.cn/sms/?ac=send&uid=sun2033539&pwd=c1d1e28244ca3b1293dd3bde4725409d&template=407343&mobile=".$mobile."&content=".$msg;		
		/*print_r($url);
			return;*/
		$info =  file_get_contents($url);
		$sns = iconv('GBK', 'UTF-8', $info);
			$sns = json_decode($sns,true);
			if($sns['stat'] == 100){
				$this->ajaxReturn(array(
                'msg' => '获取成功，请注意查收短信',
                'success' => 1
            ));
				return;
			}else{
				$this->ajaxReturn(array(
                'msg' => '获取失败',
                'success' => 1
            ));
		       
				return;
			}
		
		return $info;
     
    }
	
    //============出售水果===============
    public function chushouguozi()
    { 
        echo "<meta charset='utf-8'>";
        $userid=session('userid');
		$zhuanzhang=F('zhuan_zhang','','./Public/data/');
		//dump($zhuanzhang);die;
		//P($zhuanzhang);
		 
		/*$this->zhuanzhang=$zhuanzhang;
		$bl0=$zhuanzhang['zhuanzhang']/100;
		$bl1=$zhuanzhang['zhuanzhang1']/100;*/
        if (!I('post.')) {						
			$guozi_num=M('store')->where("uid=".$userid)->getField('cangku_num');
			$this->assign('guozi_num',$guozi_num);
            $this->display();  
        }else{
			$t=I('post.');
			
				//判断是否开启了团队交易限制
        $dbu=M('user');
        $buy_account=I('post.buy_account');
		$buy_userid = $dbu->where("account='".$buy_account."'")->getField('userid');
		if($buy_userid==$userid){
			 echo "<script>alert('不可出售给自己');</script>";
                    echo "<script>javascript:history.back(-1);</script>";die;
		}
        $status=$dbu->where('userid='.$userid)->getField('status');
        if($status==1){
            $mima = $dbu->where('userid='.$userid)->getField('jiaoyi_pw');
            $mima1 = $dbu->where("account='".$buy_account."'")->getField('jiaoyi_pw');
            if($mima!=$mima1){
                
                   echo "<script>alert('不在同一团队');</script>";
                    echo "<script>javascript:history.back(-1);</script>";die;
                
            }
        }
		$buy_status = $dbu->where("account='".$buy_account."'")->getField('status');
		if($buy_status==1){
			 $mima = $dbu->where('userid='.$userid)->getField('jiaoyi_pw');
            $mima1 = $dbu->where("account='".$buy_account."'")->getField('jiaoyi_pw');
            if($mima!=$mima1){
                
                   echo "<script>alert('不在同一团队');</script>";
                    echo "<script>javascript:history.back(-1);</script>";die;
                
            }
		}
			foreach($t as $v){
				if($v == ''){
					echo "<script>alert('请确认输入完成');</script>";
                    echo "<script>javascript:history.back(-1);</script>";die;
                }
			}

     /* // 验证码是否正确
      $sms_code = I('post.sms_code');
      $trade_code= session('trade_code');
      if ($sms_code != $trade_code) {
        echo "<script>alert('短信验证码错误');history.back();</script>;";
        exit();
      }*/
      
      // 新注册不能交易
      $add_time = M('user')->where("userid=".$userid)->getField('add_time');
      $day =  M('day')->where("id=1")->getField('day');
      if (NOW_TIME - $add_time <= 86400*$day) {
        echo "<script>alert('新注册用户".$day."天内不能进行交易哦');history.back(-1);</script>";
        exit();
      }

      // 是否种树
      $farm_num = M('nzusfarm')->where(array(
        'u_id'=>$userid,
        'show_tu'=>array('GT', 0)
      ))->count();
      if ($farm_num == 0) {
        echo "<script>alert('没有".C('KAIKEN')."土地不能进行交易');history.back(-1);</script>";
        exit();
      }

			$guozi_num=M('store')->where("uid=".$userid)->getField('cangku_num');

      $m1=trim(I('post.sell_num'));
      $m=$m1+0;
      if(!is_numeric($m1) || $m<=0){

        echo "<script>alert('输入数量错误!');</script>";
        echo "<script>javascript:history.back(-1);</script>";die;
                
      }

			if($guozi_num<trim(I('post.sell_num'))){
				echo "<script>alert('代售数量不能大于现有数量');</script>";
                echo "<script>javascript:history.back(-1);</script>";die;
			}
            $two_pw=I('post.two_pw'); 
            $buy_account=I('post.buy_account');
			      $buy_name=I('post.buy_name');
            $num=trim(I('post.sell_num'));
            $userid=session('userid');
            $dbu=M('user');
            $dbsellfruit=M('nzsell_fruit');

            $userInfo=$dbu->where('userid='.$userid.'')->find();
            $twopw=md5(md5($two_pw).$userInfo['safety_salt']);
            $yu=$num%10;
            if ($yu!=0) {
                echo "<script>alert('数量请输入10的倍数');</script>";
                echo "<script>javascript:history.back(-1);</script>";die;
                 
            }

            //验证是否存在目标客户
            $buyname=$dbu->where('account="'.$buy_account.'" AND username="'.$buy_name.'"')->find();
            if (!$buyname) {
                echo "<script>alert('目标用户信息不正确');</script>";
                echo "<script>javascript:history.back(-1);</script>";
                return;
            }

//            验证交易密码
            if ($userInfo['safety_pw']!=$twopw) {
                 echo "<script>alert('交易密码错误');</script>";
                 echo "<script>javascript:history.back(-1);</script>";
                 return;
             }
             $uuid=$dbu->where('account="'.$buy_account.'" AND username="'.$buy_name.'"')->getField('userid');
			
            $data['sell_id']=session('userid');
            $data['sell_num']=trim(I('post.sell_num'));
			     /* $aa=M('nzusfarm')->where('u_id='.$userid)->getField('show_tu',true);
			      $count = count($aa);

			
			if ($count!=15){
               
		 	$data['formality']=trim(I('post.sell_num'))*$bl0;
	
			$data['seller_num']=trim(I('post.sell_num'))*(1-$bl0);  
		 
			$guozi_num1=trim(I('post.sell_num'))*(1-$bl0);
			  }else{

                $data['formality'] = trim(I('post.sell_num'))*$bl1;

                $data['seller_num'] = trim(I('post.sell_num'))*(1 - $bl1);

                $guozi_num1 = trim(I('post.sell_num'))*(1 - $bl1);
			  }*/
			
	 

      $guozi_num=trim(I('post.sell_num', 0, 'intval'));
            $data['buy_account']=I('post.buy_account');
            $data['buy_name']=I('post.buy_name');
            $data['sell_time']=time();  
			
			
			
			
		$ttime=time();
		$sell=trim(I('post.sell_num'));
		$chtime=M('user')->where('userid='.$userid)->getField('chtime');
		$ych=M('user')->where('userid='.$userid)->getField('ych');
		//$limits=M('user')->where('userid='.$userid)->getField('limits');
	
    // 会员等级
     $db_farm = M('nzusfarm');
    $db_user = M('store');
    $db_quote = M('quote');
    $limit = M('user')->where('userid='.$userid)->getField('limits');
    if ($limit==5){
        $limits=5;
    }else{
        $farmnum = $db_farm->where(array(
            'u_id'=>$userid,
            'show_tu'=>array('GT', 0)
        ))->count();
        $total = $db_user->where('uid='.$userid)->getField('fruit_total');
        if (0<$farmnum&&$farmnum<2) {
            $limits = $limit;
        } else if(2<=$farmnum&&$farmnum<3){
            $proportion = $db_quote->where('id=1')->getField('proportion');
            $limits =$total*$proportion/100 ;
        }else if(3<=$farmnum&&$farmnum<5){
            $proportion = $db_quote->where('id=2')->getField('proportion');
            $limits =$total*$proportion/100 ;
        } else if(5<=$farmnum&&$farmnum<7){
            $proportion = $db_quote->where('id=3')->getField('proportion');
            $limits =$total*$proportion/100 ;
        }else if(7<=$farmnum&&$farmnum<9){
            $proportion = $db_quote->where('id=4')->getField('proportion');
            $limits =$total*$proportion/100 ;
        }else if(9<=$farmnum&&$farmnum<13){
            $proportion = $db_quote->where('id=5')->getField('proportion');
            $limits =$total*$proportion/100 ;
        }else if(13<=$farmnum&&$farmnum<=15){
            $proportion = $db_quote->where('id=6')->getField('proportion');
            $limits =$total*$proportion/100 ;
        }
    }


    if ($num>$limits) {
			echo "<script>alert('您每天出售不能大于".$limits."个');</script>";
			echo "<script>javascript:history.back(-1);</script>";
			return;
		}
		if($chtime!=0&&$chtime!=''){			
			$dq=date('Y-m-d',$ttime);
			$gq=date('Y-m-d',$chtime);
			if($dq!=$gq){
				M('user')->where('userid='.$userid)->setField('ych',$num);
			    M('user')->where('userid='.$userid)->setField('chtime',$ttime);
			}else{
				$all=$ych+$num;
				$jian=$limits-$ych;
				if($all>$limits){
					echo "<script>alert('您今天出售的剩余数量不能大于".$jian."个');</script>";
					echo "<script>javascript:history.back(-1);</script>";
					return;
				}else{
					M('user')->where('userid='.$userid)->setField('ych',$all);
			        M('user')->where('userid='.$userid)->setField('chtime',$ttime);
				}
			}
		}else{
			
			M('user')->where('userid='.$userid)->setField('ych',$num);
			M('user')->where('userid='.$userid)->setField('chtime',$ttime);
			
		}
            //$data['status'] = $data['dakaun_qr'] = 1;
          /*  if ($farmnum == 15) {
              $data['seller_num'] = $guozi_num-$guozi_num*$bl1;
              $data['formality']  = $guozi_num*$bl1;
            }else{
                $data['seller_num'] = $guozi_num-$guozi_num*$bl0;
                $data['formality']  = $guozi_num*$bl0;
            }*/
			$data['seller_num'] = $guozi_num-$guozi_num*0.2;
              $data['formality']  = $guozi_num*0.1;
			  $data['jifen']  = $guozi_num*0.1;
            $bool=$dbsellfruit->data($data)->add();

            M('store')->where("uid=".$userid)->setDec('cangku_num',$guozi_num);
            //M('store')->where("uid=".$buyname['userid'])->setInc('cangku_num', $data['seller_num']);
           
            if($bool){      
                //跳转到
              echo '<script>alert("出售成功");</script>';
              echo '<script>window.location.href="'.U('Trading/chushouguozi').'"</script>';
            }else{
                
                echo '<script>alert("出售失败");</script>';
                echo '<script>javascript:history.back(-1);</script>';die;
            } 

        }
     
    }

    /*function sendsms() {
      $userid=session('userid');
      $mobile=M('user')->where("userid=".$userid)->getField('mobile');
      $trade_code = String::randString(6, 1);
      session('trade_code', $trade_code);
      import('Common.Extend.Alisms');
      $AlismsModel = new \Alisms(C('SMS_TRADE_TEMPLATE_CODE'));
      $status = $AlismsModel->send_verify($mobile, "{\"code\":\"$trade_code\"}");
      if (!$status) {
        $this->ajaxReturn(array(
          'message'=>'短信发送失败'
        ));
      } else {
        $this->ajaxReturn(array(
          'message'=>'短信发送成功'
        ));
      }
    }
*/


    //============出售记录===============
    public function chushoujilu()
    {   
        $userid=session('userid');
    $m=M('nzsell_fruit');
    
        //去找出售的时间
        // $Arr=$m->where('sell_id='.$userid.'')->select();
        // foreach($Arr as $key=>$value){
        //  $guo=$value['sell_time']+86400;//找到出售超过24小时没确认的时间
          
        //     if ($guo<=time()&&$value['status']==0) {//过期没确认
        //     $tuihui=$value['sell_num'];//退回的水果
        //     $dbs=M('store');
        //     $dbs->startTrans();

        //          $where['id']=$value['id'];
        //     $where['sell_id']=$userid;
        //     $where['sell_time']=$value['sell_time'];
        //     $where['status']=0;
            
        //          $z=$m->where($where)->delete();//改变出售表
        //     $t=$dbs->where('uid='.$userid.'')->setInc('cangku_num',$tuihui);  
            
        //         if ($z&&$t) {
        //      $dbs->commit();    
        //     }else{
        //      $dbs->rollback();  
        //     }
        //  }
        // }
 
       
        $where1='sell_id='.$userid; 
        // $p=getpage($m,$where1,10);
        $arr=$m->where($where1)->order('id desc')->limit(0, 80)->select(); 
    // $this->page=$p->show();     
    $this->assign('arr',$arr);
        $this->display();
    }

    //============购买记录===============
    public function goumaijilu()
    { 
      $userid=session('userid');
    $account=M('user')->where('userid='.$userid)->getField('account');
    $m=M('nzsell_fruit');
    
        //过期时间没确认开始===========================
        $Arr=$m->where("buy_account='".$account."'")->select();
        foreach($Arr as $key=>$value){
          $guo=$value['sell_time']+86400;//找到出售超过24小时没确认的时间
          if ($guo<=time()&&$value['status']==0) {//过期没确认
             $tuihui=$value['sell_num'];//退回的水果
            
             $dbs=M('store');
             $dbs->startTrans();
                 $where['id']=$value['id'];
             $where['buy_account']=$account;
             $where['sell_time']=$value['sell_time'];
             $where['status']=0;
             
             $z=$m->where($where)->delete();//改变出售表
             
             $t=$dbs->where('uid='.$value['sell_id'].'')->setInc('cangku_num',$tuihui);  


             if ($z&&$t) {
              $dbs->commit();
             }else{
              $dbs->rollback(); 
             }
          }
        }
        //过期时间没确认结束=============================

    $where1='buy_account="'.$account.'"';
        // $p=getpage($m,$where1,10);
        $arr=$m->where($where1)->order('id desc')->limit(0, 80)->select(); 
    // $this->page=$p->show();     
    $this->assign('arr',$arr);
        $this->display();
    }


    //确认卖方转果
    public function confirmBuy(){
        echo "<meta charset='utf-8'>";
         $userid=session('userid');
         $dbs=M('store');
         $dbsellfruit=M('nzsell_fruit');
         $sell_id=I('get.sellfruit_id');
           $dbs->startTrans(); 

           

           //去拿对应的确认id
           $buyArr=$dbsellfruit->where('id='.$sell_id)->find();

           $buy_id=M('user')->where("account='".$buyArr['buy_account']."'")->getField('userid');
           if ($userid != $buyArr['sell_id'] && $userid != $buy_id) die();
           
          
           //把他的水果打给买方，即$userid
          // $a=$dbs->where('uid='.$userid.'')->setInc('cangku_num',$buyArr['seller_num']);
           
           //再去改变成交的时间
           
           $data['status']=1;
           $b=$dbsellfruit->where('id='.$sell_id.'')->setField($data);
           
           if ($b) {
               $dbs->commit();
               echo "<script>alert('确认成功');</script>";  
                echo "<script>location.href='".U('Trading/goumaijilu')."'</script>";    
               return;

           }else{
               $dbs->rollback();
                  echo "<script>alert('确认失败');</script>";  
                echo "<script>javascript:history.back(-1);</script>";
                return;
           }

    } 

    //确认收到款
     
    public function  deqianqr(){


          echo "<meta charset='utf-8'>";
           $userid=session('userid');
           
           $dbs=M('store');
           $dbsellfruit=M('nzsell_fruit');
           $sell_id=I('get.sellfruit_id', 0, 'intval');
           $dbs->startTrans(); 
		   $dakaun_qr=$dbsellfruit->where('id='.$sell_id.'')->getField('dakaun_qr');
		   if($dakaun_qr==1){
		   		 echo "<script>alert('交易状态已经发生改变');</script>";  
                echo "<script>location.href='".U('Trading/chushoujilu')."'</script>";    
               return;
		   }
           

           //去拿对应的确认id
           $buyArr=$dbsellfruit->where('id='.$sell_id.'')->find();

           $buy_id=M('user')->where("account='".$buyArr['buy_account']."'")->getField('userid');
           if ($userid != $buyArr['sell_id'] && $userid != $buy_id) die();
          
           //再去改变成交的时间
           
           $data['dakaun_qr']=1;
           $b=$dbsellfruit->where('id='.$sell_id.'')->setField($data);

           
           // 改完状态之后再去拿
           //$nimei=$dbsellfruit->where('id='.$sell_id.'')->find();

           //if ($nimei['dakaun_qr']==1&&$nimei['status']==1) {
                  
               
               //把他的水果打给买方，即$userid
              $a=$dbs->where("uid=$buy_id")->setInc('cangku_num',$buyArr['seller_num']);
			  $b = M('store')->where('uid='.$buyArr['sell_id'])->setInc('sc_jifen',$buyArr['jifen']);
           //}

           if ($a&&$b) {
               $dbs->commit();
               echo "<script>alert('交易成功');</script>";  
                echo "<script>location.href='".U('Trading/chushoujilu')."'</script>";    
               return;

           }else{
               $dbs->rollback();
                  echo "<script>alert('交易失败');</script>";  
                echo "<script>javascript:history.back(-1);</script>";
                return;
           }





    }

    
   //============种子转换水果===============
    public function zhongzizhuanshuiguo()
    {
        echo "<meta charset='utf-8'>";
        $userid=session('userid'); 
    $zhuanzhang=F('zhuan_zhang','','./Public/data/');
    $this->zhuanzhang=$zhuanzhang;
    $bl=$zhuanzhang['zhuanzhang4']/100;
        if (!I('post.')) {
      $zhongzi_num=M('store')->where("uid=".$userid)->getField('zhongzi_num');
      $this->assign('zhongzi_num',$zhongzi_num);
            $this->display();  
        }else{
      $t=I('post.');
      foreach($t as $v){
        if($v == ''){
          echo "<script>alert('请确认输入完成');</script>";
                    echo "<script>javascript:history.back(-1);</script>";die;
                }
      }
      $zhongzi_num=M('store')->where("uid=".$userid)->getField('zhongzi_num');
      if($zhongzi_num<100){
        echo "<script>alert('{:C('ZHONGZI')}低于100无法兑换');</script>";
                echo "<script>javascript:history.back(-1);</script>";die;
      }
      if($zhongzi_num<trim(I('post.znum'))){
        echo "<script>alert('兑换{:C('ZHONGZI')}数量不能大于现有种子数量');</script>";
                echo "<script>javascript:history.back(-1);</script>";die;
      }
      $two_pw=I('post.two_pw');
      $safe=M('user')->where("userid=".$userid)->find();
      $twopw=md5(md5($two_pw).$safe['safety_salt']);
            if ($safe['safety_pw']!=$twopw) {
                echo "<script>alert('交易密码错误');</script>";
                echo "<script>javascript:history.back(-1);</script>";
                return;
            }
      $data['u_id']=session('userid');
      $data['ztime']=time();
      $data['znum']=trim(I('post.znum')); 
      $zhongzi_num=trim(I('post.znum')); 
      $data['snum']=trim(I('post.znum'))*(1-$bl);
      $guozi_num=trim(I('post.znum'))*(1-$bl);
      $data['formality']=trim(I('post.znum'))*$bl;
      $num=trim(I('post.znum'));
      $yu=$num%100;
            if ($yu!=0) {
                echo "<script>alert('数量请输入100的倍数');</script>";
                echo "<script>javascript:history.back(-1);</script>";die;
                 
            }
      $bool=M("zh")->data($data)->add();
            M('store')->where("uid=".$userid)->setInc('cangku_num',$guozi_num);   
            M('store')->where("uid=".$userid)->setDec('zhongzi_num',$zhongzi_num);      
            if($bool){      
                //跳转到
              echo '<script>alert("转换成功");</script>';
              echo '<script>window.location.href="'.U('Trading/zhongzizhuanshuiguo').'"</script>';
            }else{
                
                echo '<script>alert("转换失败");</script>';
                echo '<script>javascript:history.back(-1);</script>';die;
            } 
    } 
    } 

   

    //============种子转换水果记录===============
    public function zhongzizhuanhuan()
    {    
      echo "<meta charset='utf-8'>";
        $userid=session('userid'); 
    $m=M('zh');
    $where='u_id='.$userid;
        // $p=getpage($m,$where,10);    
        $arr=$m->where($where)->order('id desc')->limit(0, 80)->select(); 
        // $this->page=$p->show();    
    $this->assign('arr',$arr);
        $this->display();
    }

	    //============系统代售水果===============
    public function xitongdaishouguozi()
    {
        //echo "<meta charset='utf-8'>";
        $userid=session('userid');
        $zhuanzhang=F('zhuan_zhang','','./Public/data/');
		$this->zhuanzhang=$zhuanzhang;	
        $rate2=$zhuanzhang['zhuanzhang2']/100;
        $rate3=$zhuanzhang['zhuanzhang3']/100;		
        if (!I('post.')) {

			$guozi_num=M('store')->where("uid=".$userid)->getField('cangku_num');
			$this->assign('guozi_num',$guozi_num);
            $this->display();  
        }else{
			$t=I('post.');
			
			foreach($t as $v){
				if($v == ''){
					echo "<script>alert('请确认输入完成');</script>";
                    echo "<script>javascript:history.back(-1);</script>";die;
                }
			}
			if(trim(strip_tags(I("post.sell_num"))) < 0){
	echo "<script>alert('代售数量必须大于0');</script>";
    echo "<script>javascript:history.back(-1);</script>";die;
	}
			$guozi_num=M('store')->where("uid=".$userid)->getField('cangku_num');
			if($guozi_num<10){
				echo "<script>alert('种子低于10无法代售');</script>";
                echo "<script>javascript:history.back(-1);</script>";die;
			}
			if($guozi_num<trim(I('post.sell_num'))){
				echo "<script>alert('代售数量不能大于现有数量');</script>";
                echo "<script>javascript:history.back(-1);</script>";die;
			}
			$two_pw=I('post.two_pw');
			$safe=M('user')->where("userid=".$userid)->find();
			$twopw=md5(md5($two_pw).$safe['safety_salt']);
            if ($safe['safety_pw']!=$twopw) {
                echo "<script>alert('交易密码错误');</script>";
                echo "<script>javascript:history.back(-1);</script>";
                return;
            }
			$data['selluser_id']=session('userid');
			$data['sell_time']=time();
			$data['sell_num']=trim(I('post.sell_num'));
			$num=trim(I('post.sell_num'));
			$guozi_num=trim(I('post.sell_num'));
			$aa=M('nzusfarm')->where('u_id='.$userid)->getField('show_tu',true);
			if (in_array(0, $aa))
			  {
			$data['seller_num']=trim(I('post.sell_num'))*(1-$rate2);
			$data['formality']=trim(I('post.sell_num'))*$rate2;
			  }
			else
			  {
			$data['seller_num']=trim(I('post.sell_num'))*(1-$rate3);
			$data['formality']=trim(I('post.sell_num'))*$rate3;
			  }
			
			/* $data['seller_num']=trim(I('post.sell_num'))*0.88;
			$data['formality']=trim(I('post.sell_num'))*0.12; */
			$data['sell_no']=time();
			$data['product_id']=1;
			$data['sell_status']=0;
            $two_pw=I('post.two_pw');  
			$num=trim(I('post.sell_num'));
			$yu=$num%10;
            if ($yu!=0) {
                echo "<script>alert('数量请输入10的倍数');</script>";
                echo "<script>javascript:history.back(-1);</script>";die;
                 
            }
			
			
			$ttime=time();
		$sell=trim(I('post.sell_num'));
		$chtime=M('user')->where('userid='.$userid.'')->getField('chtime');
		$ych=M('user')->where('userid='.$userid)->getField('ych');
		$limits=M('user')->where('userid='.$userid)->getField('limits');
dump($chtime);
		if ($num>$limits) {
			echo "<script>alert('出售数量不能大于".$chtime."');</script>";
			echo "<script>javascript:history.back(-1);</script>";
			return;
		}
		if($chtime!=0&&$chtime!=''){			
			$dq=date('Y-m-d',$ttime);
			$gq=date('Y-m-d',$chtime);
			if($dq!=$gq){
				M('user')->where('userid='.$userid)->setField('ych',$num);
			    M('user')->where('userid='.$userid)->setField('chtime',$ttime);
			}else{
				$all=$ych+$num;
				$jian=$limits-$ych;
				if($all>$limits){
					echo "<script>alert('出售剩余数量不能大于".$jian."');</script>";
					echo "<script>javascript:history.back(-1);</script>";
					return;
				}else{
					M('user')->where('userid='.$userid)->setField('ych',$all);
			        M('user')->where('userid='.$userid)->setField('chtime',$ttime);
				}
			}
		}else{
			M('user')->where('userid='.$userid)->setField('ych',$num);
			M('user')->where('userid='.$userid)->setField('chtime',$ttime);
			
		}
			$bool=M("xtdaishou")->data($data)->add(); 
			M('store')->where("uid=".$userid)->setDec('cangku_num',$guozi_num);
            if($bool){      
                //跳转到
              echo '<script>alert("代售成功");</script>';
              echo '<script>window.location.href="'.U('Trading/xitongdaishouguozi').'"</script>';
            }else{
                
                echo '<script>alert("代售失败");</script>';
                echo '<script>javascript:history.back(-1);</script>';die;
            } 
		}	
    }

    //============系统购买===============
  public function xitonggoumai()
    {  
	    echo "<meta charset='utf-8'>";
	    if (!I('post.')) {
			$userid=session('userid'); 
			$m=M('xtdaishou')->table('syd_xtdaigou g,syd_xtdaishou s')->field('g.*');
			$where='g.order_no=s.sell_no AND s.selluser_id='.$userid;
			$p=getpage($m,$where,10);		
			$arr=$m->where($where)->select();
			
			$this->page=$p->show();		
			$this->assign('arr',$arr);
dump($m);			
			$this->display();
		}else{
			$t=I('post.');
			foreach($t as $v){
				if($v == ''){
					echo "<script>alert('请确认输入完成');</script>";
                    echo "<script>javascript:history.back(-1);</script>";die;
                }
			}
			$two_pw=I('post.two_pw'); 
/*            $buy_account=I('post.buy_account');
			$buy_name=I('post.buy_name');
			$buyname=M('user')->where('account="'.$buy_account.'" AND username="'.$buy_name.'"')->find();
            if (!$buyname) {
                echo "<script>alert('目标用户不存在');</script>";
                echo "<script>javascript:history.back(-1);</script>";
                return;
            }*/

			$buyname=M('user')->where('userid='.I('post.useridaa'))->find();
			$guozi_num=trim(I('post.seller_num'));
            $twopw=md5(md5($two_pw).$buyname['safety_salt']);
			if ($buyname['safety_pw']!=$twopw) {
                echo "<script>alert('交易密码错误');</script>";
                echo "<script>javascript:history.back(-1);</script>";
                return;
            }
			$bool=M('store')->where("uid=".$buyname['userid'])->setInc('cangku_num',$guozi_num);
			if($bool){ 
				$maoae['id'] = I('post.decefid') ;
				$maoae['gou_status'] = 1 ;
				M('xtdaigou') -> save($maoae);   
                //跳转到
              echo '<script>alert("交易成功");</script>';
              echo '<script>window.location.href="'.U('Trading/xitonggoumai').'"</script>';
            }else{
                
                echo '<script>alert("交易失败");</script>';
                echo '<script>javascript:history.back(-1);</script>';die;
            } 
		}	
    }
	

	 //============出售水果===============
    public function pingtaichushou()
    { 
        echo "<meta charset='utf-8'>";
        $userid=session('userid');
        if (!I('post.')) {						
			$guozi_num=M('store')->where("uid=".$userid)->getField('cangku_num');
			$this->assign('guozi_num',$guozi_num);
            $this->display();  
        }else{
			$t=I('post.');
			foreach($t as $v){
				if($v == ''){
					echo "<script>alert('请确认输入完成');</script>";
                    echo "<script>javascript:history.back(-1);</script>";die;
                }
			}
			$userInfo = M('user')->where('userid='.$userid)->find();
			$two_pw = I('post.two_pw');
			$twopw=md5(md5($two_pw).$userInfo['safety_salt']);
			if ($userInfo['safety_pw']!=$twopw) {
                 echo "<script>alert('交易密码错误');</script>";
                 echo "<script>javascript:history.back(-1);</script>";
                 return;
			}

      // 验证码是否正确
     /* $sms_code = I('post.sms_code');
      $trade_code= session('trade_code');
      if ($sms_code != $trade_code) {
        echo "<script>alert('短信验证码错误');history.back(-1);</script>;";
        exit();
      }*/
      
      // 新注册不能交易
      $add_time = M('user')->where("userid=".$userid)->getField('add_time');
      $day =  M('day')->where("id=1")->getField('day');
      if (NOW_TIME - $add_time <= 86400*$day) {
        echo "<script>alert('新注册用户".$day."天内不能进行挂单哦');history.back(-1);</script>";
        exit();
      }

      // 是否种树
      $farm_num = M('nzusfarm')->where(array(
        'u_id'=>$userid,
        'show_tu'=>array('GT', 0)
      ))->count();
      if ($farm_num == 0) {
        echo "<script>alert('没有".C('KAIKEN')."土地不能进行挂单哦');history.back(-1);</script>";
        exit();
      }

	  $guozi_num=M('store')->where("uid=".$userid)->getField('cangku_num');

      $m1=trim(I('post.sell_num'));
      $m=$m1+0;
      if(!is_numeric($m1) || $m<=0){

        echo "<script>alert('输入数量错误!');</script>";
        echo "<script>javascript:history.back(-1);</script>";die;
                
      }

			if($guozi_num<trim(I('post.sell_num'))){
				echo "<script>alert('代售数量不能大于现有数量');</script>";
                echo "<script>javascript:history.back(-1);</script>";die;
			}
            $num=trim(I('post.sell_num'));
            $dbu=M('user');
            $dbsellfruit=M('pingtai');

            $userInfo=$dbu->where('userid='.$userid.'')->find();
            $yu=$num%10;
            if ($yu!=0) {
                echo "<script>alert('数量请输入10的倍数');</script>";
                echo "<script>javascript:history.back(-1);</script>";die;
                 
            }
			$data['uid']=$userid;
			$data['u_mobile']=$userInfo['mobile'];
			$data['sell_num'] = $num;
			$data['seller_num'] = $num-$num*0.2;
			$data['formality']=$num*0.1;
			$data['jifen']=$num*0.1;
			$data['add_time'] = time();
			$data['status']=0;
			$bool = M('pingtai')->data($data)->add();

            M('store')->where("uid=".$userid)->setDec('cangku_num',$num);
            //M('store')->where("uid=".$buyname['userid'])->setInc('cangku_num', $data['seller_num']);
           
            if($bool){      
                //跳转到
              echo '<script>alert("出售成功");</script>';
              echo '<script>window.location.href="'.U('Index/zhuanchujilu').'"</script>';
            }else{
                
                echo '<script>alert("出售失败");</script>';
                echo '<script>javascript:history.back(-1);</script>';die;
            } 

        }
     
    }
	
	public function jihuo(){
		echo "<meta charset='utf-8'>";
		$userid = session('userid');
	    if (!I('post.')) {
			$cangku_num = M('store')->where('uid='.$userid)->getField('cangku_num');
			$this->assign('cangku_num',$cangku_num);
			$this->display();
		}else{
			$userInfoT = M('user')->where('userid='.$userid)->find();
			$two_pw = I('post.two_pw');
			$twopw=md5(md5($two_pw).$userInfoT['safety_salt']);
			if ($userInfoT['safety_pw']!=$twopw) {
                 echo "<script>alert('交易密码错误');</script>";
                 echo "<script>javascript:history.back(-1);</script>";
                 return;
			}
			$cangku_num = M('store')->where('uid='.$userid)->getField('cangku_num');
			if($cangku_num<220){
				 echo '<script>alert("仓库数量不足");</script>';
                 echo '<script>javascript:history.back(-1);</script>';die;
			}
			$account = I('post.account');
			$userInfo = M('user')->where("account='".$account."'")->find();
			$id =array();
			array_push($id,$userInfo['parent_id']);
			$oneid1[0] = M('user')->where('userid='.$userInfo['parent_id'])->getField('parent_id');
			for($i=0;$i<99;$i++){
				if($oneid1[$i]==null){
					break;
				}
				array_push($id,$oneid1[$i]);
				$condition['userid']=array('in',$oneid1[$i]);
				$oneid1[$i+1] = M('user')->where($condition)->getField('parent_id');
			}
			$panduan = in_array($userid,$id);
			if($panduan==false){
				echo '<script>alert("您不是此帐号的推荐人");</script>';
                 echo '<script>javascript:history.back(-1);</script>';die;
			}
			if($userInfo['lockuser']==0){
				echo '<script>alert("此帐号已经激活");</script>';
                 echo '<script>javascript:history.back(-1);</script>';die;
			}
			if($userInfo['lockuser']==2){
				echo '<script>alert("此帐号已经被冻结");</script>';
                 echo '<script>javascript:history.back(-1);</script>';die;
			}
			$res = M('store')->where('uid='.$userid)->setDec('cangku_num',220);
			$ren = M('user')->where("account='".$account."'")->setField('lockuser',0);
			$data['uid']=$userid;
			$data['jh_id']=$userInfo['userid'];
			$data['jh_account']=$userInfo['account'];
			$data['jh_username']=$userInfo['username'];
			$data['time']=time();
			$rem = M('jihuo')->data($data)->add();
			$where['u_id']=$userInfo['userid'];
        	$where['f_id']=1;

        	$mm['show_tu']=1;
        	$mm['guozi_num']=200;
			$mm['land_leve']=1;
        	$kaiken=M('nzusfarm')->where($where)->save($mm);
			//更新仓库表的种植字段
            $plant_num=M('store')->where('uid='.$userInfo['userid'].'')->setInc('plant_num',200);
           
            //再把更新后种植字段和仓库字段更新到总水果字段
            $bbb=M('store')->where('uid='.$userInfo['userid'])->find();
            $zsg=$bbb['plant_num']+$bbb['cangku_num'];
            M('store')->where('uid='.$userInfo['userid'].'')->setField('fruit_total',$zsg);
			  $SysConfig=F('SysConfig','','./Public/data/'); 
			  if($SysConfig['c_ztjl']!=0){
              	
				M('store')->where('uid='.$userid)->setInc('cangku_num',$SysConfig['c_ztjl']);
			}
			
			if($res&&$ren&&$rem){
				echo '<script>alert("激活成功");</script>';
              	echo '<script>window.location.href="'.U('Trading/jihuojilu').'"</script>';
			}else{
				echo '<script>alert("激活失败");</script>';
                 echo '<script>javascript:history.back(-1);</script>';die;
			}
			
		}	
	}
	
	public function jihuojilu(){
		$userid = session('userid');
		$arr = M('jihuo')->where('uid='.$userid)->order('id desc')->select();
		$this->assign('arr',$arr);
		$this->display();
	}







}