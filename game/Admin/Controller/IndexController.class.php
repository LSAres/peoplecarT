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
       if(($lockuser = I('lockuser'))){
           $where['lockuser'] = array(array('NOTIN','2,3'),array('NEQ',0));
       }
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