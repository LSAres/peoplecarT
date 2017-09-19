<?php
namespace Admin\Controller;
use Think\Controller;
class ZnmailController extends CommonController {

    //会员站内信息交流明细
    public function member_letter(){

        $cwhere=I('where');

        $start_time = strtotime(I('start_time'));
        $end_time = strtotime(I('end_time'));

        if($start_time && $end_time){
            $where['l.time'] = array('between',"$start_time,$end_time");
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
        #统计电话卡数量和金额
       /* $sql_photok="select count(*) num from syd_nzcode WHERE cd_state=1 UNION  select count(*) num2 from syd_nzcode ";
        $arr_cont=M()->query($sql_photok);*/


        $syd_nzletter=M("nzletter");
        $count   = $syd_nzletter->alias('l')->join('syd_user as u ON l.send_id=u.userid','left')
            ->field('l.*,u.mobile')
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


        $syd_nzletter_arr   =  $syd_nzletter->alias('l')->join('syd_user as u ON l.send_id=u.userid','left')
            ->field('l.*,u.mobile')
            ->where($where)
            ->order('l.letter_id desc')
            ->limit($Page->firstRow.','.$Page->listRows)
            ->select();

        // p($syd_nzactivation_arr);
        // $pageshow=$Page->pageInfo();
        $show=$Page->show();
        $this->assign('pageinfo',$show);

        $this->assign(
            array(
                'arrData' => $syd_nzletter_arr,
                'where'=> $where,
                'arr_cont'=>$arr_cont
            )
        );
        $this->display();
    }





    //收件列表页
    public function shoujian(){
        echo "<meta charset='utf-8'>";
        $userid=$_SESSION['userid'];
        $letter=M('nzletter');
        $count =$letter->where(array(
            'recipient_id'=>0,
        ))->count();


        $Page=new \Think\Page($count,20);


        $Page->setConfig('header', '<li class="rows">共<b>%TOTAL_ROW%</b>条记录&nbsp;第<b>%NOW_PAGE%</b>页/共<b>%TOTAL_PAGE%</b>页</li>');
        $Page->setConfig('prev', '上一页');
        $Page->setConfig('next', '下一页');
        $Page->setConfig('last', '末页');
        $Page->setConfig('first', '首页');
        $Page->setConfig('theme', '%FIRST%%UP_PAGE%%LINK_PAGE%%DOWN_PAGE%%END%%HEADER%');
        $Page->lastSuffix = false;//最后一页不显示为总页数


        $arrData=$letter->where(array(
            'recipient_id'=>0,
        ))->order('letter_id desc')->limit($Page->firstRow.','.$Page->listRows)->select();
       /* p($arrData);
        exit;*/

        $show=$Page->show();
        $this->assign('pageinfo',$show);
        $this->assign(array(
            'arrData'=>$arrData,
        ));
        $this->display();

    }


    //收件列表页
    public function fajian(){
        echo "<meta charset='utf-8'>";
        $userid=$_SESSION['userid'];
        $letter=M('nzletter');
        $count =$letter->where(array(
            'send_id'=>0,
        ))->count();


        $Page=new \Think\Page($count,20);


        $Page->setConfig('header', '<li class="rows">共<b>%TOTAL_ROW%</b>条记录&nbsp;第<b>%NOW_PAGE%</b>页/共<b>%TOTAL_PAGE%</b>页</li>');
        $Page->setConfig('prev', '上一页');
        $Page->setConfig('next', '下一页');
        $Page->setConfig('last', '末页');
        $Page->setConfig('first', '首页');
        $Page->setConfig('theme', '%FIRST%%UP_PAGE%%LINK_PAGE%%DOWN_PAGE%%END%%HEADER%');
        $Page->lastSuffix = false;//最后一页不显示为总页数


        $arrData=$letter->where(array(
            'send_id'=>0,
        ))->order('letter_id desc')->limit($Page->firstRow.','.$Page->listRows)->select();


        $show=$Page->show();
        $this->assign('pageinfo',$show);
        $this->assign(array(
            'arrData'=>$arrData,
        ));
        $this->display();

    }

    //详情页
    public function content(){
        $getid=I('get.letter_id');
        $letter=M('nzletter');
        $letter->where('letter_id='.$getid.'')->setField('status',1);
        $value=$letter->where('letter_id='.$getid.'')->find();
        $this->assign('value',$value);
        $this->display();
    }

    //写信
    public function formail(){
        $friend  =session('get_tree');
        $this->friend=$friend;
        $this->display();
    }

    //提交过来的数据
    public function form_tj(){
        echo '<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />';

        if (I('post.')) {
            $db_letter=M('nzletter');
           // $userid=$_SESSION['userid'];

            $fmobile=I('post.recipient_mobile');

            $recipient_id=M('user')->where("mobile='".$fmobile."'")->getField('userid');
            if(!$recipient_id){
                echo "<script>alert('没有此账号，请重新输入');</script>";
                echo "<script>javascript:history.back(-1)</script>";die;
            }

            $data['send_id']= 0;
            $data['recipient_id']= $recipient_id;
            $data['title']= I('post.title');
            $data['content']= I('post.content');
            $data['time']= time();

            $bool=$db_letter->data($data)->add();

            if ($bool) {
                echo "<script>alert('发送成功');</script>";
                echo "<script>window.location.href='".U('admin/Znmail/formail')."';</script>";die;
            }else{
                echo "<script>alert('发送失败');</script>";
                echo "<script>window.location.href='".U('admin/Znmail/formail')."';</script>";die;
            }
        }
    }

    #站内信之单独删除
    public function Del_one(){
        echo "<meta charset='utf-8'>";
        $letter=M('nzletter');
        $getid=I('get.letter_id');
        $bool=$letter->where("letter_id=$getid")->delete();
        if($bool){
            echo "<script>alert('删除成功');</script>";
             echo "<script>javascript:history.back(-1);</script>";

        }else{
            echo "<script>alert('删除失败');</script>";
            echo "<script>javascript:history.back(-1);</script>";
        }

    }

    #站内信之选中全部删除
    public function Del_all(){
        echo "<meta charset='utf-8'>";
        $letter=M('nzletter');
        $arr=I('post.test','');
        foreach ($arr as $key => $value) {
            $id.=$value.',';
        }
        $str = substr($id,0,-1);
        $bool=$letter->where("letter_id in(".$str.")")->delete();
        if($bool){
            echo "<script>alert('删除成功');</script>";
            echo "<script>window.location.href='".U('Znmail/index')."'</script>";

        }else{
            echo "<script>alert('删除失败');</script>";
            echo "<script>javascript:history.back(-1);</script>";
        }
    }





}

