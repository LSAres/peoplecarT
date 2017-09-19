<?php

namespace Admin\Controller;
use Think\Controller;
use Think\Page;

class GuoziController extends CommonController {


     //播水果到到平台
     public function boguozi(){
     	if (!I('post.')) {
     		$this->display();
     	}else{
     		$db_bofa=M('bofamx');
     		$manage_id=session('sp_user');
     		$guozi_num=I('post.guozi_num');
     		$note=I('post.note');

     		$data['manage_id']=$manage_id;
     		$data['bofa_num']=$guozi_num;
     		$data['time']=time();
     		$data['note']=$note;
            
            $bf=$db_bofa->data($data)->add();

	            if ($bf) {
	            	echo "<script>alert('拨发成功');</script>";
	                $this->display();
	            }else{
	            	echo "<script>alert('拨发失败');</script>";
	            	echo "<script>javascript:history.back(-1);</script>";
	            	return;
	            }
     	}
     }

     
     //播水果明细
     public function boguozimx(){
            $cwhere=I('where');
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


         $start_time = strtotime(I('start_time'));
         $end_time = strtotime(I('end_time'));
         //时间内
         if($start_time && $end_time){
             $where['b.time'] = array('between',"$start_time,$end_time"); 
         }

        $clockwhere = 'clockwhere_'.MODULE_NAME.CONTROLLER_NAME.ACTION_NAME ;


          if($cwhere){
             session($clockwhere,null); 
             session($clockwhere,$where); 
          }


      $where = session($clockwhere)?session($clockwhere):$where;

        
        $db_bofa=M('bofamx');
         
         #查询平台总充值了多少水果
           $sql_totalmoney="select sum(bofa_num)  totalmoney from syd_bofamx";
           $totalmoney=M()->query($sql_totalmoney);
          //查询转给用户的总水果量
           $sql_zhuantotal="select sum(guozi_num) zhuantotal from syd_admin_zhuangz";
           $zhuantoyal=M()->query($sql_zhuantotal);
              
          
          $count = $db_bofa->alias('b')->join('syd_nzspuser as s ON b.id=s.sp_id')->field('b.*,s.sp_username')->where($where)->order('b.id desc')->count();
          
          $Page = new \Think\Page($count,20);// 实例化分页类 传入总记录数和每页显示的记录数(25)
              $Page->setConfig('header', '<li class="rows">共<b>%TOTAL_ROW%</b>条记录&nbsp;第<b>%NOW_PAGE%</b>页/共<b>%TOTAL_PAGE%</b>页</li>');
              $Page->setConfig('prev', '上一页');
              $Page->setConfig('next', '下一页');
              $Page->setConfig('last', '末页');
              $Page->setConfig('first', '首页');
              $Page->setConfig('theme', '%FIRST%%UP_PAGE%%LINK_PAGE%%DOWN_PAGE%%END%%HEADER%');
              $Page->lastSuffix = false;//最后一页不显示为总页数
          $show       = $Page->show();// 分页显示输出

          $list =$db_bofa->alias('b')->join('syd_nzspuser as s ON b.manage_id=s.sp_id')
                         ->field('b.*,s.sp_username')
                         ->where($where)
                         ->order('b.id desc')
                         ->limit($Page->firstRow.','.$Page->listRows)
                         ->select();
         

          $this->assign('mxArr',$list);// 赋值数据集
          $this->assign('pageshow',$show);// 赋值分页输出
          $this->assign('totalmoney',$totalmoney);// 总金额输出
          $this->assign('zhuantoyal',$zhuantoyal);
          $this->display(); // 输出模板

     }

     //管理员给客户转水果明细==============================
     public function zhuangkh(){

                $cwhere=I('where');
         if(I('condition')){
            $we = I('condition');
            //如果搜索的是账号是比较特殊的
             
             if ($we=='z.manage_id') {
                  $value=trim(I('text'));
                  $value=M('nzspuser')->where("sp_username='".$value."'")->getField('sp_id');   
             }else if ($we=='z.u_id') {
                  $value=trim(I('text'));
                  $value=M('user')->where("account='".$value."'")->getField('userid');    
             }else{
                 $value=trim(I('text'));
             }
 
            if($we!=''){     
                $where[$we] =array('like',"%$value%");
            }else{
                $where[$we] =$value;
            }
         }


         $start_time = strtotime(I('start_time'));
         $end_time = strtotime(I('end_time'));
         //时间内
         if($start_time && $end_time){
             $where['z.zhuan_time'] = array('between',"$start_time,$end_time"); 
         }

        $clockwhere = 'clockwhere_'.MODULE_NAME.CONTROLLER_NAME.ACTION_NAME ;


          if($cwhere){
             session($clockwhere,null); 
             session($clockwhere,$where); 
          }


          $where = session($clockwhere)?session($clockwhere):$where;

        
            $adminzgz=M('admin_zhuangz');
         
          #查询平台总充值了多少水果
           $sql_totalmoney="select sum(bofa_num)  totalmoney from syd_bofamx";
           $totalmoney=M()->query($sql_totalmoney);
          //查询转给用户的总水果量
           $sql_zhuantotal="select sum(guozi_num) zhuantotal from syd_admin_zhuangz";
           $zhuantoyal=M()->query($sql_zhuantotal);
         /*  p($zhuantoyal);
          exit;*/


          $count = $adminzgz->alias('z')
                         ->alias('z')
                         ->join('syd_user as u ON z.u_id=u.userid')
                         ->join('syd_store as s ON s.uid=u.userid')
                         ->join('syd_nzspuser as a ON a.sp_id=z.manage_id')
                         ->field('z.*,u.account,u.userid,u.account,s.cangku_num,a.sp_id,a.sp_username')
                         ->where($where)
                         ->order('z.id desc')
                         ->count();

          $Page = new \Think\Page($count,20);// 实例化分页类 传入总记录数和每页显示的记录数(25)
              $Page->setConfig('header', '<li class="rows">共<b>%TOTAL_ROW%</b>条记录&nbsp;第<b>%NOW_PAGE%</b>页/共<b>%TOTAL_PAGE%</b>页</li>');
              $Page->setConfig('prev', '上一页');
              $Page->setConfig('next', '下一页');
              $Page->setConfig('last', '末页');
              $Page->setConfig('first', '首页');
              $Page->setConfig('theme', '%FIRST%%UP_PAGE%%LINK_PAGE%%DOWN_PAGE%%END%%HEADER%');
              $Page->lastSuffix = false;//最后一页不显示为总页数
          $show       = $Page->show();// 分页显示输出

          
         $list =$adminzgz->alias('z')
                         ->join('syd_user as u ON z.u_id=u.userid')
                         ->join('syd_store as s ON s.uid=u.userid')
                         ->join('syd_nzspuser as a ON a.sp_id=z.manage_id')
                         ->field('z.*,u.account,u.userid,u.account,s.cangku_num,a.sp_id,a.sp_username')
                         ->where($where)
                         ->order('z.id desc')
                         ->limit($Page->firstRow.','.$Page->listRows)
                         ->select();
        /*  p($list); $zhuantoyal
          exit;*/
 
          $this->assign('zhuanmxArr',$list);// 赋值数据集
          $this->assign('pageshow',$show);// 赋值分页输出
          $this->assign('totalmoney',$totalmoney);// 总金额输出
           $this->assign('zhuantoyal',$zhuantoyal);
          $this->display(); // 输出模板
  


     }


     //修改用户仓库，违规的才需要修改
     public function updatecaifu(){
             $dbstore=M('store');
             $dbu=M('user');
             $uid=I('get.userid');//用户id
             session('gaiid',$uid);

             if (!I('post.')) {
                  $uInfo=$dbstore->alias('s')->join('syd_user as u ON s.uid=u.userid')->field('u.account,u.userid,u.username,s.cangku_num')->where('s.uid='.session('gaiid').'')->find();
                  $this->assign('uInfo',$uInfo);
                  $this->display();
             }else{
                $userid=I('post.userid');
                $yuanyou=$dbstore->where('uid='.$userid.'')->getField('cangku_num');  
                $cangkuNum=I('post.cangku_num');
                //如果减掉的数大于原有的我不能让你减
                if($cangkuNum>$yuanyou){
                    echo "<script>alert('你减掉的数不能大于原来仓库有的数');</script>";
                    echo "<script>javascript:history.back(-1);</script>";
                    return;
                }

                $success=$dbstore->where('uid='.$userid.'')->setDec('cangku_num',$cangkuNum);
                if ($success) {
                    echo "<script>alert('减掉成功');</script>";
                    echo "<script>location.href='".U('Admin/Guozi/updatecaifu')."'</script>"; 
                    return; 
                }else{
                    echo "<script>alert('减掉失败');</script>";
                    echo "<script>javascript:history.back(-1);</script>";
                }




             }
             
     }



























}



?>