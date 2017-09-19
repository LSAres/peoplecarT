<?php
namespace Admin\Controller;
use Think\Controller;
use Think\Page;
/*import("Org.Util.Page");
$objpage=new Page();*/

class ActivationController extends CommonController {

    
    #卡密流水明细
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


        $syd_user=("user");
        $syd_nzactivation=M("nzactivation");
        $count   = $syd_nzactivation->alias('a')->join('syd_user as u ON a.act_uid=u.userid')
            ->field('a.*,u.mobile')
            ->where($where)
            ->order('a.act_id desc')
            ->count();

    $Page=new \Think\Page($count,20);


    $Page->setConfig('header', '<li class="rows">共<b>%TOTAL_ROW%</b>条记录&nbsp;第<b>%NOW_PAGE%</b>页/共<b>%TOTAL_PAGE%</b>页</li>');
    $Page->setConfig('prev', '上一页');
    $Page->setConfig('next', '下一页');
    $Page->setConfig('last', '末页');
    $Page->setConfig('first', '首页');
    $Page->setConfig('theme', '%FIRST%%UP_PAGE%%LINK_PAGE%%DOWN_PAGE%%END%%HEADER%');
    $Page->lastSuffix = false;//最后一页不显示为总页数


    $syd_nzactivation_arr   = $syd_nzactivation->alias('a')->join('syd_user as u ON a.act_uid=u.userid')
            ->field('a.*,u.mobile')
            ->where($where)
            ->order('a.act_id desc')
            ->limit($Page->firstRow.','.$Page->listRows)
            ->select();

           // p($syd_nzactivation_arr);
        // $pageshow=$Page->pageInfo();
        $show=$Page->show();
        $this->assign('pageinfo',$show);

        $this->assign(
            array(
                'arrData' => $syd_nzactivation_arr,
                'where'=> $where
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





$count = $syd_nzrecharge->alias('r')->join('syd_user as u ON r.cz_uid=u.userid')->field('r.*,u.mobile')->where($where)->order('r.cz_id desc')->count();

$Page = new \Think\Page($count,10);// 实例化分页类 传入总记录数和每页显示的记录数(25)



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
$this->assign('page',$show);// 赋值分页输出
$this->display(); // 输出模板



    }


}