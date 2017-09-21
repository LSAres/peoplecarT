<?php
namespace Mobile\Controller;
use Think\Controller;
class FinancemanagementController extends CommonController {
    public function finance_bonusdetailed(){
        $this->display();
    }
    public function finance_charge(){
        $this->display();
    }

    public function finance_detailed(){
        $this->display();
    }

    public function finance_fundconversion(){
        $this->display();
    }

    public function finance_transfer(){
        $this->display();
    }
    public function finance_withdrawals(){
        $this->display();
    }

    public function addcharge(){
        $t=I('post.');
        foreach($t as $v){
            if($v == ''){
                echo "<script>alert('请确认输入完成');</script>";
                echo "<script>javascript:history.back(-1);</script>";die;
            }
        }
        $userid = session('userid');
        $money = I('post.money');
        $phone = I('post.phone');
        $wx_no = I('post.wx_no');
        if($money<=0){
            echo "<script>alert('金额数值错误');</script>";
            echo "<script>javascript:history.back(-1);</script>";die;
        }
        $data['uid']=$userid;
        $data['money'] = $money;
        $data['phone'] = $phone;
        $data['wx_no'] = $wx_no;
        $data['time'] = time();
        $data['status'] = 0;
        $res = M('charge')->data($data)->add();
        if($res){
            echo "<script>alert('充值申请成功，请等待工作人员与您联系');</script>";
            echo "<script>window.location.href='".U('Financemanagement/finance_chargeHistory')."'</script>";
        }else{
            echo "<script>alert('充值申请失败');</script>";
            echo "<script>javascript:history.back(-1);</script>";die;
        }

    }

    public function finance_chargeHistory(){
        $userid = session('userid');
        $condition['uid'] = $userid;
        $chargelist = M('charge')->where($condition)->select();
        $this->assign('chargelist',$chargelist);
        $this->display();
    }
}