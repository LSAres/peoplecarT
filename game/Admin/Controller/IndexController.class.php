<?php
namespace Admin\Controller;
use Think\Controller;
class IndexController extends CommonController {

    //会员列表
    public function backstageSammaryPage(){
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
            ->field('userid,account,username,lockuser,time,parent_id,identity_card,phone')
            ->order('userid desc ')
            ->select();

        $this->assign(array(
            'userArr'=>$userArr,
            'pageshow'=>$pageshow,
        ));
        $this->display();
    }


   
   public function gameNotice(){
   		$this->display();
   }

}