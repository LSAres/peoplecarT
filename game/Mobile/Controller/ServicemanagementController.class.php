<?php
namespace Mobile\Controller;
use Think\Controller;
class ServicemanagementController extends CommonController {
    public function user_memberupgrade(){
        if(!I('post.')) {
            $userid = session('userid');
            $userInfo = M('user')->where('userid=' . $userid)->find();
            if ($userInfo['leve'] == 1) {
                $userInfo['shenfen'] = "普通会员";
            }
            if ($userInfo['leve'] == 2) {
                $userInfo['shenfen'] = "代理商";
            }
            $this->assign('userInfo', $userInfo);
            $this->display();
        }else{
            $degree=I('post.degree');
            $userid=session('userid');
            $condition['uid']=$userid;
            $condition['status']=0;
            $is_have=M('change_degree')->where($condition)->find();
            if($is_have){
                echo "<script>alert('您已经发出申请，不可再次申请！');</script>";
                echo "<script>javascript:history.back(-1);</script>";die;
            }
            $data['uid']=$userid;
            $data['degree']=$degree;
            $data['status']=0;
            $data['time']=time();
            $res = M('change_degree')->data($data)->add();
            if($res){
                echo "<script>alert('申请成功，请等待客服人员与您联系');location.href='".U('Index/copyPageTwo')."'</script>";
                exit();
            }else{
                echo "<script>alert('申请失败');</script>";
                echo "<script>javascript:history.back(-1);</script>";die;
            }
        }
    }
    public function user_register(){
        $this->display();
    }
    public function user_reportcenter(){
        $this->display();
    }
}