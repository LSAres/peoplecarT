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

    public function addcharge(){
        $userid = session('userid');
        $money = I('post.money');
        $phone = I('post.phone');
        $order_no = I('post.order_no');
        $wx_no = I('post.wx_no');
        $alipay_no = I('post.alipay_no');
        $bank_name = I('post.bank_name');
        $bank_no = I('post.bank_no');
        $card_name = I('post.card_name');
        if(!$phone){
            echo "<script>alert('手机号不可为空');</script>";
            echo "<script>javascript:history.back(-1);</script>";die;
        }
        if(!$order_no){
            echo "<script>alert('订单号不可为空');</script>";
            echo "<script>javascript:history.back(-1);</script>";die;
        }
        if(!$phone){
            echo "<script>alert('手机号不可为空');</script>";
            echo "<script>javascript:history.back(-1);</script>";die;
        }
        if(!$wx_no&&!$alipay_no&&!$bank_no&&!$bank_name&&!$card_name){
            echo "<script>alert('请填写充值方式');</script>";
            echo "<script>javascript:history.back(-1);</script>";die;
        }
        if($bank_no){
            if(!$bank_name){
                echo "<script>alert('请填写银行名称');</script>";
                echo "<script>javascript:history.back(-1);</script>";die;
            }
            if(!$card_name){
                echo "<script>alert('请填写持卡人姓名');</script>";
                echo "<script>javascript:history.back(-1);</script>";die;
            }
        }
        if($bank_name){
            if(!$bank_no){
                echo "<script>alert('请填写银行卡号');</script>";
                echo "<script>javascript:history.back(-1);</script>";die;
            }
            if(!$card_name){
                echo "<script>alert('请填写持卡人姓名');</script>";
                echo "<script>javascript:history.back(-1);</script>";die;
            }
        }
        if($card_name){
            if(!$bank_no){
                echo "<script>alert('请填写银行卡号');</script>";
                echo "<script>javascript:history.back(-1);</script>";die;
            }
            if(!$bank_name){
                echo "<script>alert('请填写银行名称');</script>";
                echo "<script>javascript:history.back(-1);</script>";die;
            }
        }

        if($money<=0){
            echo "<script>alert('金额数值错误');</script>";
            echo "<script>javascript:history.back(-1);</script>";die;
        }
        $data['uid']=$userid;
        $data['money'] = $money;
        $data['phone'] = $phone;
        $data['order_no'] = $order_no;
        $data['wx_no'] = $wx_no;
        $data['alipay_no'] = $alipay_no;
        $data['bank_name'] = $bank_name;
        $data['bank_no'] = $bank_no;
        $data['card_name'] = $card_name;
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
        $chargelist = M('charge')->where($condition)->order('id desc')->select();
        $this->assign('chargelist',$chargelist);
        $this->display();
    }

    public function finance_withdrawals(){
        $userid = session('userid');
        $condition['uid'] = $userid;
        $bankInfo = M('bank')->where($condition)->find();
        if(!$bankInfo){
            echo "<script>alert('请先绑定银行卡信息');</script>";
            echo "<script>javascript:history.back(-1);</script>";die;
        }
        $this->assign('bankInfo',$bankInfo);
        $this->display();
    }

    public function addwithdrawals(){
        $t=I('post.');
        foreach($t as $v){
            if($v == ''){
                echo "<script>alert('请确认输入完成');</script>";
                echo "<script>javascript:history.back(-1);</script>";die;
            }
        }
        $userid =session('userid');
        $realname = I('post.realname');
        $bank_name = I('post.bank_name');
        $bank_address = I('post.bank_address');
        $bank_card = I('post.bank_card');
        $money = I('post.money');
        if($money<=0){
            echo "<script>alert('金额数量错误');</script>";
            echo "<script>javascript:history.back(-1);</script>";die;
        }

        $data['uid'] = $userid;
        $data['realname'] = $realname;
        $data['bank_name'] = $bank_name;
        $data['bank_address'] = $bank_address;
        $data['bank_card'] = $bank_card;
        $data['money'] = $money;
        $data['status'] = 0;
        $data['time'] = time();

        $res = M('withdraw')->data($data)->add();
        if($res){
            echo "<script>alert('提现申请成功，请等待工作人员与您联系');</script>";
            echo "<script>window.location.href='".U('Financemanagement/finance_withdrawalsHistory')."'</script>";
        }else{
            echo "<script>alert('提现申请失败');</script>";
            echo "<script>javascript:history.back(-1);</script>";die;
        }
    }

    public function finance_withdrawalsHistory(){
        $userid = session('userid');
        $condition['uid']=$userid;
        $withdrawlist = M('withdraw')->where($condition)->order('id desc')->select();
        $this->assign('withdrawlist',$withdrawlist);
        $this->display();
    }
}