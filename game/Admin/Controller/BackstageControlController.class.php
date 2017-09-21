<?php
namespace Admin\Controller;
use Think\Controller;
class BackstageControlController extends CommonController{
    public function websiteSwitchPage(){
        $status = M('switch')->where('id=1')->getField('status');
        if($status==0){
            $zhuangtai = "开启";
        }else{
            $zhuangtai = "关闭";
        }
        $this->assign('zhuangtai',$zhuangtai);
        $this->display();
    }

    public function editSwitch(){
        $status = M('switch')->where('id=1')->getField('status');
        if($status==0){
            $res = M('switch')->where('id=1')->setField('status',1);
        }else{
            $res = M('switch')->where('id=1')->setField('status',0);
        }
        if($res){
            echo "<script>alert('网站状态修改成功');</script>";
            echo "<script>window.location.href='".U('BackstageControl/websiteSwitchPage')."'</script>";
        }else{
            echo "<script>alert('网站状态修改失败');</script>";
            echo "<script>javascript:history.back(-1);</script>";die;
        }
    }
}