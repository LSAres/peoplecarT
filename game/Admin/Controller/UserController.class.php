<?php
namespace Admin\Controller;
use Think\Controller;
class UserController extends CommonController {
      #会员列表
      public function user_list(){


    $cwhere=I('where');
    $start_time = strtotime(I('start_time'));
    $end_time = strtotime(I('end_time'));

    if($start_time && $end_time){
        $where['add_time'] = array('between',"$start_time,$end_time"); 
    }
          $tb_user=M('user');

     if(I('condition')){
            $we = I('condition');
            $value=trim(I('text'));

            if($we == 'parent_id' &&  strlen($value) == 11 ){
               $wherepid =  $tb_user->where("mobile = $value ")->getField($we);
               $where[$we] = $wherepid;
            }else{
                echo '<script>请输入11位手机号</script>';
            }
                if($value !='' && $we != 'parent_id'){
                $where[$we] =array('like',"%$value%");
                }
        }
		if(I('post.money')){
			$money = I('post.money');
			$money=$money-0.001;
			$c['fruit_total'] = array('gt',$money);
			$id_array = M('store')->where($c)->getField('uid',true);
			$where['userid']=array('in',$id_array);
		}

        //锁定
        if(($farm_lock = I('farm_lock') != '')){
            $where['farm_lock'] = $farm_lock;
        }
        if(($lockuser = I('lockuser'))){
          $where['lockuser'] = array(array('NOTIN','2,3'),array('NEQ',0));    
        }/*else{
          $where['lockuser'] = array('NOTIN','2,3');
        }*/
    $clockwhere = 'clockwhere_'.MODULE_NAME.CONTROLLER_NAME.ACTION_NAME ;
     if($cwhere){
        session($clockwhere,null); 
        session($clockwhere,$where); 
     }
     $where = session($clockwhere)?session($clockwhere):$where;



          //锁定用户
          if(IS_GET){
              $lockuser=I('get.lockuser_status');
              $userid=I('get.userid');
              $lockuser = ($lockuser> 0 )?0:2;
              $up_lockuser=array(
                  'lockuser'=>$lockuser
              );
              $tb_user->where(array('userid'=>$userid))->save($up_lockuser);

          }
          $pagesize =20;
          //$where=true;
          $p = getpage($tb_user, $where, $pagesize);
          $pageshow   = $p->show();

          $userArr=$tb_user->where($where)
                           ->field('userid,account,username,lockuser,add_time,wealthb,farm_lock,parent_id,login_ip,limits,status,jiaoyi_pw')
                           ->order('userid desc ')
                           ->select();

          $this->assign(array(
              'userArr'=>$userArr,
              'pageshow'=>$pageshow,
          ));
         $this->display();
      }



    public function user_list24(){
    $cwhere=I('where');
    $start_time = strtotime(I('start_time'));
    $end_time = strtotime(I('end_time'));




    if($start_time && $end_time){
        $where['add_time'] = array('between',"$start_time,$end_time"); 
    }



          $tb_user=M('user');

     if(I('condition')){
            $we = I('condition');
            $value=trim(I('text'));

            if($we == 'parent_id' &&  strlen($value) == 11 ){
               $wherepid =  $tb_user->where("mobile = $value ")->getField($we);
               $where[$we] = $wherepid;
            }else{
                echo '<script>请输入11位手机号</script>';
            }
                if($value !='' && $we != 'parent_id'){
                $where[$we] =array('like',"%$value%");
                }
        }

        //锁定
        if(($farm_lock = I('farm_lock') != '')){
            $where['farm_lock'] = $farm_lock;
        }
   
          $where['lockuser'] = array('IN','2,3');
        


    

    $clockwhere = 'clockwhere_'.MODULE_NAME.CONTROLLER_NAME.ACTION_NAME ;
     if($cwhere){
        session($clockwhere,null); 
        session($clockwhere,$where); 
     }
     $where = session($clockwhere)?session($clockwhere):$where;



          //锁定用户
          if(IS_GET){
              $lockuser=I('get.lockuser_status');
              $userid=I('get.userid');
              $lockuser = ($lockuser> 0 )?0:1;
              $up_lockuser=array(
                  'lockuser'=>$lockuser
              );
              $tb_user->where(array('userid'=>$userid))->save($up_lockuser);

          }
          $pagesize =20;
          //$where=true;
          $p = getpage($tb_user, $where, $pagesize);
          $pageshow   = $p->show();

          $userArr=$tb_user->where($where)
                           ->field('userid,account,username,lockuser,add_time,wealthb,farm_lock,parent_id,limits')
                           ->order('userid desc ')
                           ->select();


// echo $tb_user->_sql();




          $this->assign(array(
              'userArr'=>$userArr,
              'pageshow'=>$pageshow,
          ));
         $this->display();
      }




    #点击用户直接进去用户个人中心
    public function input_user(){
        echo '<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />';
       $userid=I('get.userid');
       session('user_get_id',$userid);
         
              $spdb=M('nzspuser');
              $dbuser=M('user');
              $manage_id=$_SESSION['sp_user'];#拿到进来的管理员id
              $manage_arr=$spdb->where("sp_id=$manage_id")->find();
              $manage_pw=I('post.manage_pw');
              $input_pw=md5(md5($manage_pw).$manage_arr['sp_salt']);
              /*if($input_pw!=$manage_arr['sp_password']){
                 echo "<script>alert('密码错误');</script>";
                 echo '<script>javascript:history.back(-1);</script>';die;
              }*/
              $userid=$_SESSION['user_get_id'];
              $user_account=$dbuser->where("userid=$userid")->getField('mobile');
              session('userid',$userid);
              session('mobile',$user_account);
              session('userroot',null);
              session('gx_array',null);
              session('get_tree',null);
              session('get_pidtree',null);
              redirect(U('/Pc/Index/index'));
          
    }

    function updateparentid() {
      $userid = I('request.userid', 0, 'intval');
      $db_user= M('user');
      
       if(!$_POST){
          $user_row = $db_user->field('account, username, mobile, userid,parent_id')->where(array('userid'=>$userid))->find();
          $this->assign('user_row', $user_row);
          $this->display();
          }else{
              $dbuser=M('user');
              $tjr=I('post.tjr');
              $tjr_arr=$dbuser->where(array('account'=>$tjr))->find();
              if($tjr_arr){
                 $uppid=$db_user->where(array('userid'=>$userid))->setField('parent_id', $tjr_arr['userid']);
               if ($uppid) {
                  echo "<script>alert('修改成功');</script>";
                  echo "<script>window.location.href='".U('User/user_list')."'</script>";
               }else{
                  echo "<script>alert('修改失败');</script>";
                  echo "<script>javascript:history.back(-1);</script>";
               }
             }else{
                echo "<script>alert('未能找到此推荐人，请联系系统管理员！');</script>";
                echo "<script>javascript:history.back(-1);</script>";
              }
      }
    }

    function updatemobile() {
      $userid = I('request.userid', 0, 'intval');
      $db_user= M('user');
      if ($userid) {
        if (IS_POST) {
          $mobile = I('post.mobile');
		  $alipay = I('post.alipay');
		  $wx_no = I('post.wx_no');
		  if($mobile!=null){
			  if (strlen($mobile) != 11 || !is_numeric($mobile)) {
				echo "<script>alert('手机号码格式错误');history.back();</script>";
				exit();
			  }
		  }

          // 扣5朵茶叶
          $db_store = M('store');
          $kou = $db_store->where(array(
            'uid'=>$userid
          ))->setDec('cangku_num', 5);
          if (!$kou) {
            echo "<script>alert('".C('GUOZI')."不足');history.back();</script>";
            exit();  
          }
		  if($mobile!=null){
			  $db_user->where(array(
				'userid'=>$userid
			  ))->setField(array(
				'mobile'=>$mobile
			  ));
		  }
		  if($alipay!=null){
			  $db_user->where(array(
				'userid'=>$userid
			  ))->setField(array(
				'alipay'=>$alipay
			  ));
		  }
		  if($wx_no){
			  $db_user->where(array(
				'userid'=>$userid
			  ))->setField(array(
				'wx_no'=>$wx_no
			  ));
		  }
		   $password=I('post.login_pw');
		   if($password){     
                $date['salt']=substr(md5(time()),0,3);   
                $date['password']=md5(md5($password).$date['salt']);
                $up_password=M('user')->where("userid=$userid")->save($date);
			}
			  $safety_pw=I('post.safety_pw');
			 if($safety_pw){    
                $date['safety_salt']=substr(md5(time()),0,3);   
                $date['safety_pw']=md5(md5($safety_pw).$date['safety_salt']);
                
                $up_password=M('user')->where("userid=$userid")->save($date);
			}

          // 发短信
          $update_mobile_template_code = C('SMS_UPDATE_MOBILE_TEMPLATE_CODE');
          if (!empty($update_mobile_template_code)) {
          	$account = I('post.account');
            import('Common.Extend.Alisms');
            $AlismsModel = new \Alisms($update_mobile_template_code);
            $AlismsModel->send_verify($mobile, "{\"account\":\"$account\"}");
          }

              echo "<script>alert('修改成功');</script>";
               echo "<script>location.href='".U('User/user_list')."'</script>";


        } else {
          $user_row = $db_user->field('account, username, mobile, userid,alipay,wx_no')->where(array(
            'userid'=>$userid
          ))->find();
          $this->assign('user_row', $user_row);
          $this->display();
        }
      }
    }

    #后台修改用户资料
    public function updateuser(){
     
      $dbuserinfo=M('userinfo');
      if (!IS_POST) {
          $get_uid=I('get.userid');
          session('userid',$get_uid);
          $get_uid=session('userid');
          $udata=M()->query('select u.account,u.username,u.userid,s.cangku_num from syd_user u INNER JOIN syd_store s ON u.userid=s.uid WHERE u.userid='.$get_uid.'');
          $this->assign('udata',$udata);
          $this->display();
         
      }else{
           $dbst=M('store');
           $dbazg=M('admin_zhuangz');
           $cangku_num=I('post.cangku_num');
           $uid=I('post.userid');
           //判断库存是否还大于0
          
           #查询平台总充值了多少茶叶
           $sql_totalmoney="select sum(bofa_num)  totalmoney from syd_bofamx";
           $totalmoney=M()->query($sql_totalmoney);
          //查询转给用户的总茶叶量
           $sql_zhuantotal="select sum(guozi_num) zhuantotal from syd_admin_zhuangz";
           $zhuantoyal=M()->query($sql_zhuantotal);

           $yy=$totalmoney[0]['totalmoney']-$zhuantoyal[0]['zhuantotal'];
           if ($yy<$cangku_num) {
                
               echo "<script>alert('库存不足');</script>";
               echo "<script>javascript:history.back(-1);</script>";
               return;
           }

           $up=$dbst->where('uid='.$uid.'')->setInc('cangku_num',$cangku_num);
           //把数据记录到茶叶流水明细
             $data['manage_id']=$manage_id=session('sp_user');
             $data['u_id']=$uid;
             $data['guozi_num']=$cangku_num; 
             $data['zhuan_time']=time();
             $data['ip']=get_client_ip();//$_SERVER["REMOTE_ADDR"];
           $jl=$dbazg->data($data)->add();  

        if ($up&&$jl) {
              echo "<script>alert('修改成功');</script>";
               echo "<script>window.location.href='".U('User/updateuser')."'</script>";

           }else{
             echo "<script>alert('修改失败');</script>";
               echo "<script>javascript:history.back(-1);</script>";
           }        
      }
    }
    
    #后台修改用户安全码和登录密码
    public function up_pw(){
           $db_user=M('user');
            
           if (I('post.login_pw')) {
                $date['password']=I('post.login_pw');
                $userid=I('post.userid');     
                $date['salt']=substr(md5(time()),0,3);   
                $date['password']=md5(md5($date['password']).$date['salt']);
                $up_password=$db_user->where("userid=$userid")->save($date);
                 
               if ($up_password) {
                  echo "<script>alert('修改成功');</script>";
                  echo "<script>window.location.href='".U('admin/User/up_pw')."'</script>";
               }else{
                  echo "<script>alert('修改失败');</script>";
                  echo "<script>javascript:history.back(-1);</script>";
               }
                
               
           }else if(I('post.safety_pw')){
                $date['safety_pw']=I('post.safety_pw');
                $userid=I('post.userid');     
                $date['safety_salt']=substr(md5(time()),0,3);   
                $date['safety_pw']=md5(md5($date['safety_pw']).$date['safety_salt']);
                
                $up_password=$db_user->where("userid=$userid")->save($date);
                 
               if ($up_password) {
                  echo "<script>alert('修改成功');</script>";
                  echo "<script>window.location.href='".U('admin/User/up_pw')."'</script>";
               }else{
                  echo "<script>alert('修改失败');</script>";
                  echo "<script>javascript:history.back(-1);</script>";
               }
              die;
           }
            $this->display();
        
    }


    //系谱图
    public function familytree(){
	$sum=0;
    
if($_POST['account']){
        
    $udb = M('user');
    $account = I('post.account');
    
    $uid = $udb->where("account ='". $account."'")->getField('userid');
	$id =array();
        $uInfo['sum'] = M('user')->where('parent_id='.$uid)->count();
        $sum=$sum+ $uInfo['sum'];
        $oneid1[0] = M('user')->where('parent_id='.$uid)->getField('userid',true);
		for($i=0;$i<99;$i++){
		    if($oneid1[$i]==null){
		        break;
            }
			$id = array_merge($id,$oneid1[$i]);
            $condition['parent_id']=array('in',$oneid1[$i]);
            $uInfo['sum'] = M('user')->where($condition)->count();
            $sum=$sum+ $uInfo['sum'];
            $oneid1[$i+1] = M('user')->where($condition)->getField('userid',true);
        }


         function get_str($id = 0) {
          global $str;
          $udb=M('user');
          $uinf=$udb->field('userid,parent_id,true_name,mobile,add_time,account,username')->where("parent_id= {$id}")->select();
         static  $i=1;
        if($uinf){//如果有子类
          $str .= '<ul>';
          foreach ($uinf as $v ) { //循环记录集
             $str .=  "<li>".$i++."┠━&nbsp;编号$v[userid]&nbsp;".$v['account']."&nbsp;".$v['true_name']."&nbsp;".$v['username']."&nbsp;(".date('Y-m-d',$v[add_time])."加入)"; //构建字符串
            get_str($v['userid']); //调用get_str()，将记录集中的id参数传入函数中，继续查询下级
          }
          $str .= '</ul>';
        }
        return $str;
      }
      if($uid){
$aaa = get_str($uid);
$this->assign('sum',$sum);
$this->tree = $aaa;
      }else{
        echo "<script>alert('帐号不存在');</script>";
      }
}



      $this->display();

    }
	
	 //系谱图
    public function superiortree(){
    
if($_POST['account']){
        
    $udb = M('user');
    $account = I('post.account');
    
    $uid = $udb->where("account ='". $account."'")->getField('parent_id');

         function get_str($id = 0) {
          global $str;
          $udb=M('user');
          $uinf=$udb->field('userid,parent_id,true_name,mobile,add_time,account,username')->where("userid= {$id}")->select();
         static  $i=1;
        if($uinf){//如果有子类
          $str .= '<ul>';
          foreach ($uinf as $v ) { //循环记录集
             $str .=  "<li>".$i++."┠━&nbsp;编号$v[userid]&nbsp;".$v['account']."&nbsp;".$v['true_name']."&nbsp;".$v['username']."&nbsp;(".date('Y-m-d H:i:s',$v[add_time])."加入)"; //构建字符串
            get_str($v['parent_id']); //调用get_str()，将记录集中的id参数传入函数中，继续查询下级
          }
          $str .= '</ul>';
        }
        return $str;
      }
      if($uid){
$aaa = get_str($uid);
$this->tree = $aaa;
      }else{
        echo "<script>alert('帐号不存在');</script>";
      }
}



      $this->display();

    }
	
	public function setsjvd(){
		$where['userid']=I('u','','intval');
		$data['limits']=I('l')?I('l'):'#';
		$tdb=M('user');
        $bool=$tdb->where($where)->data($data)->save();
        if($bool!==false){
		echo 'ggggg';
		}
	}

    public function UserDelete($userid=null){
        $obj = M("user");
        $UserArr = $obj->where(array('userid='.$userid))->field('userid,account,mobile')->find();
        $del_num=M('nzmanage_log')->where('time>'.strtotime(date("Y-m-d")))->count();
        if($del_num < 6 ){  //删除限制
        //删除会员
        $data = $obj->delete($userid);
        if($data){
          $logInfo=array(
                'namage_id'=>session('sp_user'),
                'ip'     =>get_client_ip(),
                'type'=>'Delete',
                'user_account'=> $UserArr['account'],
                'mobile'=>$UserArr['mobile'],
                'time'=>time(),
           );
           //记录日志
           M('nzmanage_log')->data($logInfo)->add();  
            echo "<meta charset=\"utf-8\"/> <script>alert('会员删除成功')</script>";
            echo '<script>location.href="'.U('User/user_list').'"</script>';
        }else{
            echo "<meta charset=\"utf-8\"/> <script>alert('会员删除失败')</script>";
            echo '<script>location.href="'.U('User/user_list').'"</script>';
        }
        }else{
            echo "<meta charset=\"utf-8\"/> <script>alert('会员删除超过限制')</script>";
            echo '<script>location.href="'.U('User/user_list').'"</script>';
        }
    }

    public function cash(){
        $cwhere=I('where');
        if(I('condition')) {
            $we = I('condition');
            $value = trim(I('text'));
            if ($we != '') {
                //$where[$we] ="".$we." like '%".$value."%'" ;
                $where[$we] = array('like', "%$value%");
            } else {
                $where[$we] = $value;
            }
        }
        $clockwhere = 'clockwhere_'.MODULE_NAME.CONTROLLER_NAME.ACTION_NAME ;
        if($cwhere){
            session($clockwhere,null);
            session($clockwhere,$where);
        }
        $where = session($clockwhere)?session($clockwhere):$where;
        $m=M('duihuanjl');
        $pagesize =20;
        $p = getpage($m, $where, $pagesize);
        $pageshow   = $p->show();
        $data = $m->alias('a')
            ->where($where)
            ->order('id desc ')
            ->select();
        foreach ($data as $k=>$v){
            $userinfo = M('user')->where("userid='" . $v['uid'] . "'")->find();
            $data[$k]['username']=$userinfo['username'];
            $data[$k]['account']=$userinfo['account'];
        }
        $this->assign('data', $data);
        $this->assign('pageshow',$pageshow);
        $this->display();
    }

    public function updateStatus(){
        $id=I('get.userid');
        $data['status']=1;
        $res = M('duihuanjl')->where('id='.$id)->save($data);
        if($res){
            echo "<meta charset=\"utf-8\"/> <script>alert('订单完成')</script>";
            echo '<script>location.href="'.U('User/cash').'"</script>';
        }else{
            echo "<meta charset=\"utf-8\"/> <script>alert('失败')</script>";
            echo '<script>location.href="'.U('User/cash').'"</script>';
        }
    }

    public function goods(){
        $cwhere=I('where');
        if(I('condition')) {
            $we = I('condition');
            $value = trim(I('text'));
            if ($we != '') {
                //$where[$we] ="".$we." like '%".$value."%'" ;
                $where[$we] = array('like', "%$value%");
            } else {
                $where[$we] = $value;
            }
        }
        $clockwhere = 'clockwhere_'.MODULE_NAME.CONTROLLER_NAME.ACTION_NAME ;
        if($cwhere){
            session($clockwhere,null);
            session($clockwhere,$where);
        }
        $where = session($clockwhere)?session($clockwhere):$where;
        $m=M('dhsp');
        $pagesize =20;
        $p = getpage($m, $where, $pagesize);
        $pageshow   = $p->show();
        $data = $m->alias('a')
            ->where($where)
            ->order('id desc ')
            ->select();
        foreach ($data as $k=>$v){
            $userinfo = M('user')->where("userid='" . $v['uid'] . "'")->find();
            $data[$k]['username']=$userinfo['username'];
            $data[$k]['account']=$userinfo['account'];
        }
        $this->assign('data', $data);
        $this->assign('pageshow',$pageshow);
        $this->display();
    }

    public function updateOrderStatus(){
        $id=I('get.userid');
        $data['status']=1;
        $res = M('dhsp')->where('id='.$id)->save($data);
        if($res){
            echo "<meta charset=\"utf-8\"/> <script>alert('订单完成')</script>";
            echo '<script>location.href="'.U('User/goods').'"</script>';
        }else{
            echo "<meta charset=\"utf-8\"/> <script>alert('失败')</script>";
            echo '<script>location.href="'.U('User/goods').'"</script>';
        }
    }
    
    //============出售记录===============
public function userls(){  
        $cwhere=I('where');
        $start_time = strtotime(I('start_time'));
        $end_time = strtotime(I('end_time'));

        if($start_time && $end_time){
            $where['sell_time'] = array('between',"$start_time,$end_time");
        }
                if(I('condition')){
            $we = I('condition');
            $value=trim(I('text'));
            if($we!=''){
                //$where[$we] ="".$we." like '%".$value."%'" ;
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
$fruit=M()->query("select sum(fruit_total) User_Fruit from syd_store");

       
        
        $m=M('nzsell_fruit');
        
          $pagesize =20;
          $p = getpage($m, $where, $pagesize);
          $pageshow   = $p->show();
          $mArr=$m->alias('a')->join('syd_user as b ON a.sell_id=b.userid')
          
                          // ->field('userid,account,username,lockuser,add_time,wealthb,farm_lock,parent_id,login_ip,limits')
                          ->where($where)
                           ->order('id desc ')
                           ->select();
          $this->assign(array(
              'mArr'=>$mArr,
              'pageshow'=>$pageshow,
              'fruit'=>$fruit[0],
          ));
         // dump($where);
        $this->display();
     }
	 
	 public function data(){
         if(!I('post.')) {
             $cwhere=I('where');
             if(I('condition')) {
                 $we = I('condition');
                 $value = trim(I('text'));
                 if ($we != '') {
                     //$where[$we] ="".$we." like '%".$value."%'" ;
                     $where[$we] = array('like', "%$value%");
                 } else {
                     $where[$we] = $value;
                 }
             }
             $clockwhere = 'clockwhere_'.MODULE_NAME.CONTROLLER_NAME.ACTION_NAME ;
             if($cwhere){
                 session($clockwhere,null);
                 session($clockwhere,$where);
             }
             $where = session($clockwhere)?session($clockwhere):$where;
             $fruit=M()->query("select sum(fruit_total) User_Fruit from syd_store");
             $m=M('data');
             $pagesize =20;
             $p = getpage($m, $where, $pagesize);
             $pageshow   = $p->show();
             $data = $m->alias('a')
                 ->where($where)
                 ->order('id desc ')
                 ->select();
             $this->assign('data', $data);
             $this->assign('pageshow',$pageshow);
             $this->display();
         }else{
             $num = M('data')->where()->order('id desc')->limit(0,1)->select();
			 if($num==null){
			 	$this->assign('data',$data);
			 	$this->display('dataOne');
			 }else{
             $last_login_time=M('data')->where('id='.$num[0]['id'])->getField('time');
             $now_login=time();
             $jlSum=M('cfbsjl')->where()->order('id desc')->limit(0,1)->select();
             $condition3['sell_time']=array('between',array($jlSum[0]['time'],$now_login));
             $condition3['dakaun_qr']=1;
             $condition['farm_type']=1;
             $condition['show_tu'] = array('gt',0);
             $condition1['farm_type']=2;
             $condition1['show_tu'] = array('gt',0);
             $condition2['farm_type']=3;
             $condition2['show_tu'] = array('gt',0);
             $huang_count = M('nzusfarm')->where($condition)->count('id');
             $hong_count = M('nzusfarm')->where($condition1)->count('id');
             $hei_count = M('nzusfarm')->where($condition2)->count('id');
             $mm=F('minmaxxx','','./Public/data/');
             $sum=M('user')->where()->count('userid');
             $awake_sum = M('user')->where('lockuser=0')->count('userid');
             if($num<1){
                 $open_user_sum = $sum;
                 $awake = $awake_sum;
             }else{
                 $renshu = M('data')->where('id='.$num[0]['id'])->getField('user_sum');
                 $open_user_sum = $sum-$renshu;
                 $awake_renshu = M('data')->where('id='.$num[0]['id'])->getField('awake_sum');
                 $awake = $awake_sum-$awake_renshu;
             }


             $data['time']=time();
             $data['product_sum'] = M('store')->where()->sum('fruit_total');
             $data['farm_product_sum'] = M('nzusfarm')->where()->sum('guozi_num');
             $data['reclaim_product_sum'] = $huang_count*$mm['huang_min']+$hong_count*$mm['hong_min']+$hei_count*$mm['hei_min'];
             $data['store_product_sum'] = M('store')->where()->sum('cangku_num');
             $data['profit_sum'] = M('store')->where()->sum('huafei_total');
             $data['profit'] = M('store')->where()->sum('huafei_num');
             $data['chart']= M('cfbs')->where('cfbs_id=1')->getField('cfbs_value');
             $data['open_account_sum'] = $open_user_sum;
             $data['user_sum']=$sum;
             $data['trading_volume'] = M('nzsell_fruit')->where($condition3)->sum('sell_num');
             if ($data['trading_volume']==null){
                 $data['trading_volume']=0;
             }
             $data['awake_sum'] = $awake_sum;
             $data['awake_num']=$awake;
			 $data['zongjifen']=M('store')->where()->sum('sc_jifen');
             $this->assign('data',$data);
             $this->display('dataOne');
			 }
         }
     } 
	 
	 public function transaction_list(){
         $cwhere=I('where');
         if(I('condition')) {
             $we = I('condition');
             $value = trim(I('text'));
             if ($we != '') {
                 //$where[$we] ="".$we." like '%".$value."%'" ;
                 $where[$we] = array('like', "%$value%");
             } else {
                 $where[$we] = $value;
             }
         }
         $clockwhere = 'clockwhere_'.MODULE_NAME.CONTROLLER_NAME.ACTION_NAME ;
         if($cwhere){
             session($clockwhere,null);
             session($clockwhere,$where);
         }
         $where = session($clockwhere)?session($clockwhere):$where;
         $m=M('nzsell_fruit');
         $pagesize =20;
         $p = getpage($m, $where, $pagesize);
         $pageshow   = $p->show();
         $data = $m->alias('a')
             ->where($where)
             ->order('id desc ')
             ->select();
         foreach ($data as $k=>$v){
             $mobile = M('user')->where("account='" . $v['buy_account'] . "'")->getField('mobile');
			 $sell_username = M('user')->where('userid='.$v['sell_id'])->getField('username');
			 $sell_account = M('user')->where('userid='.$v['sell_id'])->getField('account');
             $data[$k]['buy_mobile']=$mobile;
			 $data[$k]['sell_username']=$sell_username;
			 $data[$k]['sell_account']=$sell_account;
         }
         $this->assign('data', $data);
         $this->assign('pageshow',$pageshow);
         $this->display();
     }

    public function transactionDelete($id=null){
        $m = M("nzsell_fruit");
        $Info = $m->where('id='.$id)->find();
        $data = $m->delete($id);
        if($data==1){
            echo "<meta charset=\"utf-8\"/> <script>alert('删除成功')</script>";
                echo '<script>location.href="'.U('User/transaction_list').'"</script>';
        }else{
            echo "<meta charset=\"utf-8\"/> <script>alert('删除失败')</script>";
            echo '<script>location.href="'.U('User/transaction_list').'"</script>';
        }
    }
	
	 public function pingtaijiaoyi_list(){
         $cwhere=I('where');
         if(I('condition')) {
             $we = I('condition');
             $value = trim(I('text'));
             if ($we != '') {
                 //$where[$we] ="".$we." like '%".$value."%'" ;
                 $where[$we] = array('like', "%$value%");
             } else {
                 $where[$we] = $value;
             }
         }
         $clockwhere = 'clockwhere_'.MODULE_NAME.CONTROLLER_NAME.ACTION_NAME ;
         if($cwhere){
             session($clockwhere,null);
             session($clockwhere,$where);
         }
         $where = session($clockwhere)?session($clockwhere):$where;
         $m=M('pingtai');
         $pagesize =20;
         $p = getpage($m, $where, $pagesize);
         $pageshow   = $p->show();
         $data = $m->alias('a')
             ->where($where)
             ->order('id desc ')
             ->select();
         foreach ($data as $k=>$v){
			 $sell_username = M('user')->where('userid='.$v['uid'])->getField('username');
			 $sell_account = M('user')->where('userid='.$v['uid'])->getField('account');
			 $data[$k]['sell_username']=$sell_username;
			 $data[$k]['sell_account']=$sell_account;
         }
         $this->assign('data', $data);
         $this->assign('pageshow',$pageshow);
         $this->display();
     }
	 
	  public function pingtaiDelete($id=null){
        $m = M("pingtai");
        $Info = $m->where('id='.$id)->find();
        $data = $m->delete($id);
        if($data==1){
            echo "<meta charset=\"utf-8\"/> <script>alert('删除成功')</script>";
                echo '<script>location.href="'.U('User/pingtaijiaoyi_list').'"</script>';
        }else{
            echo "<meta charset=\"utf-8\"/> <script>alert('删除失败')</script>";
            echo '<script>location.href="'.U('User/pingtaijiaoyi_list').'"</script>';
        }
    }
	
	public function jihuojilu_list(){
         $cwhere=I('where');
         if(I('condition')) {
             $we = I('condition');
             $value = trim(I('text'));
             if ($we != '') {
                 //$where[$we] ="".$we." like '%".$value."%'" ;
                 $where[$we] = array('like', "%$value%");
             } else {
                 $where[$we] = $value;
             }
         }
         $clockwhere = 'clockwhere_'.MODULE_NAME.CONTROLLER_NAME.ACTION_NAME ;
         if($cwhere){
             session($clockwhere,null);
             session($clockwhere,$where);
         }
         $where = session($clockwhere)?session($clockwhere):$where;
         $m=M('jihuo');
         $pagesize =20;
         $p = getpage($m, $where, $pagesize);
         $pageshow   = $p->show();
         $data = $m->alias('a')
             ->where($where)
             ->order('id desc ')
             ->select();
         foreach ($data as $k=>$v){
			 $username = M('user')->where('userid='.$v['uid'])->getField('username');
			 $account = M('user')->where('userid='.$v['uid'])->getField('account');
			 $data[$k]['username']=$username;
			 $data[$k]['account']=$account;
         }
         $this->assign('data', $data);
         $this->assign('pageshow',$pageshow);
         $this->display();
     }
	
	 public function change_uid(){
        if(!I('post.')) {
            $this->display();
        }else{
            $old_id = I('post.old_id');
            $new_id = I('post.new_id');
            $userinfo = M('user')->where('parent_id='.$old_id)->select();
            if(!$userinfo){
                echo "<meta charset=\"utf-8\"/> <script>alert('旧推荐人不存在')</script>";
                echo '<script>location.href="'.U('User/change_uid').'"</script>';
                return;
            }
            $newuserinfo = M('user')->where('userid='.$new_id)->find();
            if(!$newuserinfo){
                echo "<meta charset=\"utf-8\"/> <script>alert('新推荐人不存在')</script>";
                echo '<script>location.href="'.U('User/change_uid').'"</script>';
                return;
            }
            foreach ($userinfo as $k){
                $data['parent_id']=$new_id;
                $res=M('user')->where('userid='.$k['userid'])->save($data);
                if(!$res){
                    echo "<meta charset=\"utf-8\"/> <script>alert('修改失败')</script>";
                    echo '<script>location.href="'.U('User/change_uid').'"</script>';
                    return;
                }
            }
            echo "<meta charset=\"utf-8\"/> <script>alert('修改成功')</script>";
            echo '<script>location.href="'.U('User/change_uid').'"</script>';

        }
    }
	
	public function shoppinglistpage(){
		$duobao = M('duobao');
		$productInfo = $duobao->where()->select();
		foreach($productInfo as $k=>$v){
			$productInfo[$k]['shengyu'] = $v['price']-$v['sy_num'];
		}
		$this->assign('productInfo',$productInfo);
		$this->display();
	}
	
	public function updatenum(){
		$productid = I('post.productid');
		$num = I('post.num');
		$sy_num = M('duobao')->where('id='.$productid)->getField('sy_num');
		if($num>$sy_num){
			 echo "<meta charset=\"utf-8\"/> <script>alert('超出数量限制')</script>";
            echo '<script>location.href="'.U('User/shoppinglistpage').'"</script>';
		}
		$res = M('duobao')->where('id='.$productid)->setDec('sy_num',$num);
		if($res){
			 echo "<meta charset=\"utf-8\"/> <script>alert('修改成功')</script>";
            echo '<script>location.href="'.U('User/shoppinglistpage').'"</script>';
		}else{
			 echo "<meta charset=\"utf-8\"/> <script>alert('修改失败')</script>";
            echo '<script>location.href="'.U('User/shoppinglistpage').'"</script>';
		}
	}
	
	public function shoppinguserlist(){
		$id = I('get.id');
		$userInfo = M('qianggou')->where('productid='.$id)->select();
		foreach($userInfo as $k=>$v){
			$name = M('user')->where('userid='.$v['uid'])->getField('username');
			$userInfo[$k]['name'] = $name;
		}
		$sy_num=M('duobao')->where('id='.$id)->getField('sy_num');
		$this->assign('sy_num',$sy_num);
		$this->assign('userInfo',$userInfo);
		$this->display();
	}
	
	public function updatezj(){
		$id = I('get.id');
		$orderInfo = M('qianggou')->where('id='.$id)->find();
		if($orderInfo['status']!=0){
			echo "<meta charset=\"utf-8\"/> <script>alert('该商品已经开奖')</script>";
            echo '<script>location.href="'.U('User/shoppinglistpage').'"</script>';
		}
		M('qianggou')->where('productid='.$orderInfo['productid'])->setField('status',1);
		$now_time = time();
		$res = M('qianggou')->where('id='.$id)->setField('status',2);
		$rem = M('qianggou')->where('id='.$id)->setField('zj_time',$now_time);
		M('duobao')->where('id='.$orderInfo['productid'])->setField('status',1);
		if($res){
			echo "<meta charset=\"utf-8\"/> <script>alert('设定成功')</script>";
            echo '<script>location.href="'.U('User/shoppinglistpage').'"</script>';
		}else{
			echo "<meta charset=\"utf-8\"/> <script>alert('设定失败')</script>";
            echo '<script>location.href="'.U('User/shoppinglistpage').'"</script>';
		}
	}
	
	public function addgoodd(){
		echo "<meta charset='utf-8'>";
			$goodname = I('post.goodname');
			$price = I('post.price');
			$name = I('post.what');
			$allname = I('post.allname');
			$data['name'] = $goodname;
			$data['img'] = "/Public/duobao/".$allname;
			$data['price'] = $price;
			$data['status'] = 0;
			$data['sy_num'] = $price;
			$res = M('duobao')->data($data)->add();
		$upload = new \Think\Upload();
		$upload->maxSize   =     3145728 ;
		$upload->exts      =     array('jpg', 'gif', 'png', 'jpeg');
		$upload->rootPath  =     'Public/duobao/'; 
		$upload->savePath  =     ''; 
		$upload->saveName  = $name;
		$upload->autoSub   = false;
		 
		$info   =   $upload->upload();
		if(!$info) {
			$this->error($upload->getError());
		}else{
			
			$this->success('上传成功');
	
			
		}
	}
	
	public function DeleteGood(){
		$id = I('get.id');
		$duobao = M('duobao');
		$qianggou = M('qianggou');
		$res = $duobao->where('id='.$id)->delete();
		$rem = $qianggou->where('productid='.$id)->delete();
		if($res){
				echo "<meta charset=\"utf-8\"/> <script>alert('删除成功')</script>";
            	echo '<script>location.href="'.U('User/shoppinglistpage').'"</script>';
		}else{
			echo "<meta charset=\"utf-8\"/> <script>alert('删除失败')</script>";
            echo '<script>location.href="'.U('User/shoppinglistpage').'"</script>';
		}
	}
	
	 public function control(){
//        if(!I('post.')){
            $id = I('get.userid');
            $status = M('user')->where('userid='.$id )->getField('status');
            if($status==1){
                $ST = "已开启";
            }else{
                $ST = "未开启";
            }
            $this->assign('ST',$ST);
            $this->assign('id',$id);
            $this->assign('status',$status);

//        }
//        if(I('post.')){
//            $jiaoyi_pw = I('post.jiaoyi_pw');
//            $id=I('post.id');
//            $state = 1;
//            $num = array();
//            array_push($num,$id);
//            for ($i=0;$i<20;$i++){
//                $data[0]=$num;
//                $condition['parent_id']=array('in',$data[$i]);
//                $data[$i+1]=M('user')->where($condition)->getField('userid',true);
//                if($data[$i+1]==null){
//                    break;
//                }
//                $num1=array_merge($num,$data[$i+1]);
//            }
//            $status = M('user')->where('userid='.$id)->getField('status');
//            if($status){
//                foreach($num1 as $k){
//                    $da['status']="";
//                    $da['jiaoyi_pw']="";
//                    M('user')->where('userid='.$k)->save($da);
//                }
//                echo "<meta charset=\"utf-8\"/> <script>alert('团队交易控制关闭成功')</script>";
//                echo '<script>location.href="'.U('index/index').'"</script>';
//            }else{
//                foreach($num1 as $k){
//                    $da['status']=$state;
//                    $da['jiaoyi_pw']=$jiaoyi_pw;
//                    M('user')->where('userid='.$k)->save($da);
//                }
//                echo "<meta charset=\"utf-8\"/> <script>alert('团队交易控制开启成功')</script>";
//                echo '<script>location.href="'.U('index/index').'"</script>';
//            }
//
//        }

        $this->display();
     }

     public function controle(){
         $jiaoyi_pw = I('post.jiaoyi_pw');
         $id=I('post.id');
         $state = 1;
         $num = array();
         array_push($num,$id);
         $data=$num;
         $mima = M('user')->where('userid='.$id)->getField('jiaoyi_pw');
         for ($i=0;$i<99;$i++){
             $condition['parent_id']=array('in',$data[$i]);
             $data[$i+1]=M('user')->where($condition)->getField('userid',true);

             if($data[$i+1]==null){
                 break;
             }
             $num=array_merge($num,$data[$i+1]);
         }
         $status = M('user')->where('userid='.$id)->getField('status');
         if($status==1){
             foreach($num as $k){
                 $da['status']="";
                 $da['jiaoyi_pw']="";
                 $mima1=M('user')->where('userid='.$k)->getField('jiaoyi_pw');
                 if($mima1==$mima&&$mima1!=""&&$mima1!=0){
                     M('user')->where('userid='.$k)->save($da);
                 }
             }
             echo "<meta charset=\"utf-8\"/> <script>alert('团队交易控制关闭成功')</script>";
             echo '<script>location.href="'.U('user/user_list').'"</script>';
         }else{
             foreach($num as $k){
                 $da['status']=$state;
                 $da['jiaoyi_pw']=$jiaoyi_pw;
                 $mima1=M('user')->where('userid='.$k)->getField('jiaoyi_pw');
                 if ($mima1==$mima){
                     M('user')->where('userid='.$k)->save($da);
                 }
             }
             echo "<meta charset=\"utf-8\"/> <script>alert('团队交易控制开启成功')</script>";
             echo '<script>location.href="'.U('user/user_list').'"</script>';
         }
     }
  }

