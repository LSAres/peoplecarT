<?php
namespace Admin\Controller;
use Think\Controller;
class IndexController extends CommonController {

    //左侧主菜单
    public function backstageSammaryPage(){
        $this->display();
    }


   
   public function gameNotice(){
   		$this->display();
   }

   public function administrationPage(){

       $cwhere=I('where');
       $start_time = strtotime(I('start_time'));
       $end_time = strtotime(I('end_time'));

       if($start_time && $end_time){
           $where['time'] = array('between',"$start_time,$end_time");
       }
       $tb_user=M('user');

       if(I('condition')){
           $we = I('condition');
           $value=trim(I('text'));
           if($we=="username"){
               $where[$we] =array('like',"%$value%");
           }else {
               $where[$we] = $value;
           }
       }

       $pagesize =10;
       //$where=true;
       $p = getpage($tb_user, $where, $pagesize);
       $pageshow   = $p->show();

       $userArr=$tb_user->where($where)
           ->field('userid,account,username,lockuser,time,parent_id,identity_card,phone')
           ->order('userid desc ')
           ->select();

       $this->assign(array(
           'userArr'=>$userArr,
           'pageshow'=>$pageshow,
       ));
       $this->display();
   }

   public function updatelockuser(){
       $userid = I('get.userid');
       $lockuser = M('user')->where('userid='.$userid)->getField('lockuser');
       if($lockuser==0){
           $res = M('user')->where('userid='.$userid)->setField('lockuser',1);
       }else{
           $res = M('user')->where('userid='.$userid)->setField('lockuser',0);
       }
       if($res){
           echo "<script>alert('修改成功');</script>";
           echo "<script>window.location.href='".U('Index/administrationPage')."'</script>";
       }else{
           echo "<script>alert('修改失败');</script>";
           echo "<script>javascript:history.back(-1);</script>";die;
       }
   }

   public function deleteuser(){
       $userid = I('get.userid');
       $res = M('user')->where('userid='.$userid)->delete();
       if($res){
           echo "<script>alert('删除成功');</script>";
           echo "<script>window.location.href='".U('Index/administrationPage')."'</script>";
       }else{
           echo "<script>alert('删除失败');</script>";
           echo "<script>javascript:history.back(-1);</script>";die;
       }
   }

}