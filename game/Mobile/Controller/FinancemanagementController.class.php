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

    }
}