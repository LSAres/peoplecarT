<?php
namespace Admin\Controller;
use Think\Controller;
use Think\Page;
/*import("Org.Util.Page");
$objpage=new Page();*/

class MoneyController extends CommonController {

    #激活码流水明细
    public function ActivationCode(){

$cwhere=I('where');
    
    //时间
    $start_time = strtotime(I('start_time'));
    $end_time = strtotime(I('end_time'));

    if($start_time && $end_time){
        $where['c.cr_time'] = array('between',"$start_time,$end_time"); 
    }


        if(I('post.text')){
            $we = 'u.mobile';
            $value=trim(I('text'));
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

        $syd_nzcdrecord=M("nzcdrecord");

        #查用户身上所剩激活
        $sql_activation="SELECT COUNT(jhcode) renshu,SUM(jhcode) geshu FROM `syd_user` where jhcode <>0";
        $activation_arr_yu=M()->query($sql_activation);
      /*  p($activation_arr_yu);
        exit;*/
        $count   = $syd_nzcdrecord->alias('c')->join('syd_user as u ON c.cr_uid=u.userid')
            ->field('c.*,u.mobile')
            ->where($where)
            ->order('c.cr_time desc')
            ->count();

    $Page=new \Think\Page($count,20);


    $Page->setConfig('header', '<li class="rows">共<b>%TOTAL_ROW%</b>条记录&nbsp;第<b>%NOW_PAGE%</b>页/共<b>%TOTAL_PAGE%</b>页</li>');
    $Page->setConfig('prev', '上一页');
    $Page->setConfig('next', '下一页');
    $Page->setConfig('last', '末页');
    $Page->setConfig('first', '首页');
    $Page->setConfig('theme', '%FIRST%%UP_PAGE%%LINK_PAGE%%DOWN_PAGE%%END%%HEADER%');
    $Page->lastSuffix = false;//最后一页不显示为总页数


    $syd_nzcdrecord_arr   = $syd_nzcdrecord->alias('c')->join('syd_user as u ON c.cr_uid=u.userid')
            ->field('c.*,u.mobile')
            ->where($where)
            ->order('c.cr_time desc')
            ->limit($Page->firstRow.','.$Page->listRows)
            ->select();

  //  p($syd_nzcdrecord_arr);

        // $pageshow=$Page->pageInfo();
        $show=$Page->show();
        $this->assign('pageinfo',$show);

        $this->assign(
            array(
                'arrData' => $syd_nzcdrecord_arr,
                'where'=> $where,
                'activation_arr_yu'=>$activation_arr_yu
            )
        );
        $this->display();


        
    }


    #电话卡密码流水明细
    public function KamiDetail(){

$cwhere=I('where');

    $start_time = strtotime(I('start_time'));
    $end_time = strtotime(I('end_time'));

    if($start_time && $end_time){
        $where['a.act_time'] = array('between',"$start_time,$end_time"); 
    }


        if(I('condition')){


            $we = I('condition');
            $value=trim(I('text'));
            echo I('post.act_note');
            exit;


            if($we!=''){
                //$where[$we] ="".$we." like '%".$value."%'" ;
                $where[$we] =array('like',"%$value%");
            }else{
                $where[$we] =$value;
            }
        }

        if(($act_note = I('post.act_note')) != ''){
                  

              $where['act_note'] = $act_note;
               
           }
        $clockwhere = 'clockwhere_'.MODULE_NAME.CONTROLLER_NAME.ACTION_NAME ;
     if($cwhere){
        session($clockwhere,null); 
        session($clockwhere,$where); 
     }

     $where = session($clockwhere)?session($clockwhere):$where;
         #统计电话卡数量和金额
         $sql_photok="select count(*) num from syd_nzcode WHERE cd_state=1 UNION  select count(*) num2 from syd_nzcode ";
         $arr_cont=M()->query($sql_photok);


        $syd_nzactivation=M("nzactivation");
        $count   = $syd_nzactivation->alias('a')->join('syd_user as u ON a.act_uid=u.userid','LEFT')
            ->field('a.*,u.mobile')
            ->where($where)
            ->count();
      /* echo $syd_nzactivation->_sql();
        echo "<br/>";
        echo  $count ;*/


    $Page=new \Think\Page($count,20);


    $Page->setConfig('header', '<li class="rows">共<b>%TOTAL_ROW%</b>条记录&nbsp;第<b>%NOW_PAGE%</b>页/共<b>%TOTAL_PAGE%</b>页</li>');
    $Page->setConfig('prev', '上一页');
    $Page->setConfig('next', '下一页');
    $Page->setConfig('last', '末页');
    $Page->setConfig('first', '首页');
    $Page->setConfig('theme', '%FIRST%%UP_PAGE%%LINK_PAGE%%DOWN_PAGE%%END%%HEADER%');
    $Page->lastSuffix = false;//最后一页不显示为总页数


    $syd_nzactivation_arr   = $syd_nzactivation->alias('a')->join('syd_user as u ON a.act_uid=u.userid','LEFT')
            ->field('a.*,u.mobile')
            ->where($where)
            ->order('a.act_id desc')
            ->limit($Page->firstRow.','.$Page->listRows)
            ->select();
    //echo $syd_nzactivation->_sql();
           // p($syd_nzactivation_arr);
        // $pageshow=$Page->pageInfo();
        $show=$Page->show();
        $this->assign('pageinfo',$show);

        $this->assign(
            array(
                'arrData' => $syd_nzactivation_arr,
                'where'=> $where,
                'arr_cont'=>$arr_cont
            )
        );
        $this->display();
    }


    #充值流水明细
    public function top_up(){

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
        $where['r.cz_time'] = array('between',"$start_time,$end_time"); 
    }

   $clockwhere = 'clockwhere_'.MODULE_NAME.CONTROLLER_NAME.ACTION_NAME ;


     if($cwhere){
        session($clockwhere,null); 
        session($clockwhere,$where); 
     }


    $where = session($clockwhere)?session($clockwhere):$where;


        $syd_nzrecharge=M("nzrecharge");
        #查询充值总金额
        $sql_totalmoney="select sum(cz_tradeamt)  totalmoney from syd_nzrecharge";
        $totalmoney=M()->query($sql_totalmoney);

$count = $syd_nzrecharge->alias('r')->join('syd_user as u ON r.cz_uid=u.userid')->field('r.*,u.mobile')->where($where)->order('r.cz_id desc')->count();

$Page = new \Think\Page($count,20);// 实例化分页类 传入总记录数和每页显示的记录数(25)



    $Page->setConfig('header', '<li class="rows">共<b>%TOTAL_ROW%</b>条记录&nbsp;第<b>%NOW_PAGE%</b>页/共<b>%TOTAL_PAGE%</b>页</li>');
    $Page->setConfig('prev', '上一页');
    $Page->setConfig('next', '下一页');
    $Page->setConfig('last', '末页');
    $Page->setConfig('first', '首页');
    $Page->setConfig('theme', '%FIRST%%UP_PAGE%%LINK_PAGE%%DOWN_PAGE%%END%%HEADER%');
    $Page->lastSuffix = false;//最后一页不显示为总页数
$show       = $Page->show();// 分页显示输出

$list =$syd_nzrecharge->alias('r')->join('syd_user as u ON r.cz_uid=u.userid')
            ->field('r.*,u.mobile')
            ->where($where)
            ->order('r.cz_id desc')
            ->limit($Page->firstRow.','.$Page->listRows)
            ->select();
$this->assign('arrData',$list);// 赋值数据集
$this->assign('pageshow',$show);// 赋值分页输出
$this->assign('totalmoney',$totalmoney);// 总金额输出
$this->display(); // 输出模板




    }


    #提现流水明细
    public function withdrawal(){

$cwhere=I('where');
      $start_time = strtotime(I('start_time'));
      $end_time = strtotime(I('end_time'));
      $wd_state = I('post.wd_state')+0;
      $uif_mobile = I('post.uif_mobile')+0;
      if($uif_mobile){
      $jywhere['u.uif_mobile']  = array('like',"%$uif_mobile%");
      }
        $tb_nzwd=M('nzwd');
        //汇总提现
        $sql_huizong="select sum(actual_money) totalmoney from syd_nzwd where wd_state=0 UNION
                      select sum(actual_money) totalmoney from syd_nzwd where wd_state=1
                       ";
        $huizong=M()->query($sql_huizong);
      /*  p($huizong);
        exit;*/


    //修改状态  
    if($_POST['quan']){
        $quanArr=$_POST['quan'];
           
        $up_wd_state=array(
            'wd_state'=>1,
        );
        foreach($quanArr as $value){
            $tb_nzwd->where("wd_id=".$value)->save($up_wd_state);
        }
    }

   
    //时间内
     if($start_time && $end_time){
        $jywhere['w.wd_time'] = array('between',"$start_time,$end_time"); 
      }

        $jywhere['w.wd_state'] =$wd_state?1:0;      




        $clockwhere = 'clockwhere_'.MODULE_NAME.CONTROLLER_NAME.ACTION_NAME ;
     if($cwhere){
        session($clockwhere,null); 
        session($clockwhere,$jywhere); 
     }


     $jywhere = session($clockwhere)?session($clockwhere):$jywhere;

        $withdrawal=$tb_nzwd->alias('w')
                            ->where($jywhere)
                            ->field('u.*,w.*')
                            ->order('w.wd_time desc')
                            ->join('syd_userinfo as u on u.uif_userid=w.userid')
                            ->select();

 
$count      = $tb_nzwd->alias('w')->where($jywhere)->field('u.*,w.*')->order('w.wd_time desc')->join('syd_userinfo as u on u.uif_userid=w.userid')->count();// 查询满足要求的总记录数
$Page       = new \Think\Page($count,20);// 实例化分页类 传入总记录数和每页显示的记录数(25)





    $Page->setConfig('header', '<li class="rows">共<b>%TOTAL_ROW%</b>条记录&nbsp;第<b>%NOW_PAGE%</b>页/共<b>%TOTAL_PAGE%</b>页</li>');
    $Page->setConfig('prev', '上一页');
    $Page->setConfig('next', '下一页');
    $Page->setConfig('last', '末页');
    $Page->setConfig('first', '首页');
    $Page->setConfig('theme', '%FIRST%%UP_PAGE%%LINK_PAGE%%DOWN_PAGE%%END%%HEADER%');
    $Page->lastSuffix = false;//最后一页不显示为总页数



// 进行分页数据查询 注意limit方法的参数要使用Page类的属性
$list =$tb_nzwd->alias('w')->where($jywhere)->field('u.*,w.*')->order('w.wd_time desc')->join('syd_userinfo as u on u.uif_userid=w.userid')->limit($Page->firstRow.','.$Page->listRows)->select();


$show       = $Page->show();// 分页显示输出
$this->assign('withdrawal',$list);// 赋值数据集
$this->assign(array(
    'page'=>$show,
    'huizong'=>$huizong
));// 赋值分页输出
$this->display(); // 输出模板




    }

     #体现锁定开启与关闭
    public function tx_lock(){
        $gailv=F('tx_lock','','./Public/data/');
        $this->gailv = $gailv;
        $this->display();
    }

    //接收修改数据
    public function save_tx_lock(){
        echo "<meta charset='utf-8'>";
        $data = I();
       // p($data);

        F('tx_lock',$data,'./public/data/');
        echo "<script>alert('修改成功!');location.href='".U('admin/Money/tx_lock')."';</script>";
    }


    #网站总开关 1开启 0关闭
    public function site_close(){
       // $s_value=F('site_close','','./Public/data/');
        $siteclose=M('nzsiteclose');
        $s_value=$siteclose->find();
       
        $this->s_value=$s_value;
        $this->display();

    }

    #接收修改网站开关的参数 1开启 0关闭
    public function save_site_close(){
        echo "<meta charset='utf-8'>";
        $siteclose=M('nzsiteclose');
        $data['site_status'] = I('post.siteclose');
        if($data['site_status']==1){

            $data['site_status'] = I('post.siteclose');
            $data['reason'] = I('post.reason');
            $data['on_time']=time();
            $siteclose->where("id=1")->data($data)->save();
           echo  $siteclose->_sql();
            exit;
        }else if($data['site_status']==0){

            $data['site_status'] = I('post.siteclose');
            $data['reason'] = I('post.reason');
            $data['close_time']=time();
            $siteclose->where("id=1")->data($data)->save();
            echo  $siteclose->_sql();
            exit;
        }

       // F('site_close',$data,'./Public/data/');
        echo "<script>alert('修改成功!');location.href='".U('admin/Money/site_close')."';</script>";
    }









}