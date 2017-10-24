<?php
namespace Mobile\Controller;
use Think\Controller;
class FinancemanagementController extends CommonController {
    public function finance_bonusdetailed(){
        $userid = session('userid');
        $bonusArr = M('bonus_record')->where('uid='.$userid)->order('id desc')->select();
        $this->assign('bonusArr',$bonusArr);
        $this->display();
    }
    public function finance_charge(){
        $this->display();
    }

    public function finance_detailed(){
        $userid = session('userid');
        $goldArr = M('getgold_record')->where('uid='.$userid)->order('id desc')->select();
        $bonusArr = M('getbonus_record')->where('uid='.$userid)->order('id desc')->select();
        $buyCarArr = M('getbuycarmoney_record')->where('uid='.$userid)->order('id desc')->select();
        $this->assign('goldArr',$goldArr);
        $this->assign('bonusArr',$bonusArr);
        $this->assign('buyCarArr',$buyCarArr);
        $this->display();
    }

    //购车基金转换报单币
    public function finance_fundconversion(){
        if(!I('post.')){
            $userid = session('userid');
            $storeInfo = M('store')->where('uid='.$userid)->find();
            $this->assign('storeInfo',$storeInfo);
            $this->display();
        }else{
            $num = I('post.num');
            $userid = session('userid');
            $storeInfo = M('store')->where('uid='.$userid)->find();
            if($num<=0){
                echo "<script>alert('转换数量错误');</script>";
                echo "<script>javascript:history.back(-1);</script>";die;
            }
            if($num>$storeInfo['buycar_money']){
                echo "<script>alert('转换数量超过现有购车基金数量');</script>";
                echo "<script>javascript:history.back(-1);</script>";die;
            }
            $declaration_fee = M('function_parameters')->where('id=1')->getField('declaration_fee');
            $conversions_num = $num-$num*$declaration_fee;
            $res = M('store')->where('uid='.$userid)->setDec('buycar_money',$num);
            $rem = M('store')->where('uid='.$userid)->setInc('report_money',$conversions_num);
            if($res&&$rem){
                echo "<script>alert('转换成功');</script>";
                echo "<script>window.location.href='".U('Index/copyPageTwo')."'</script>";
            }else{
                echo "<script>alert('转换失败');</script>";
                echo "<script>javascript:history.back(-1);</script>";die;
            }
        }
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
        $buycar_money = M('store')->where('uid='.$userid)->getField('buycar_money');
        if($buycar_money<$money){
            echo "<script>alert('提现数量超过购车基金数量');</script>";
            echo "<script>javascript:history.back(-1);</script>";die;
        }
        $rem = M('store')->where('uid='.$userid)->setDec('buycar_money',$money);
        $fund_fee = M('function_parameters')->where('id=1')->getField('fund_fee');
        $moneyT = $money-$money*$fund_fee;

        $data['uid'] = $userid;
        $data['realname'] = $realname;
        $data['bank_name'] = $bank_name;
        $data['bank_address'] = $bank_address;
        $data['bank_card'] = $bank_card;
        $data['money'] = $moneyT;
        $data['status'] = 0;
        $data['reason'] = "购车基金提现";
        $data['time'] = time();

        $res = M('withdraw')->data($data)->add();
        if($res&&$rem){
            echo "<script>alert('提现申请成功，请等待工作人员与您联系');</script>";
            echo "<script>window.location.href='".U('Financemanagement/finance_withdrawalsHistory')."'</script>";
        }else{
            echo "<script>alert('提现申请失败');</script>";
            echo "<script>javascript:history.back(-1);</script>";die;
        }
    }

    //出局奖金提现
    public function addwithdrawalsB(){
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
        $bonus = M('store')->where('uid='.$userid)->getField('bonus');
        if($bonus<$money){
            echo "<script>alert('提现数量超过出局奖金数量');</script>";
            echo "<script>javascript:history.back(-1);</script>";die;
        }
        $rem = M('store')->where('uid='.$userid)->setDec('bonus',$money);
        $bonus_fee = M('function_parameters')->where('id=1')->getField('bonus_fee');
        $moneyT = $money-$money*$bonus_fee;

        $data['uid'] = $userid;
        $data['realname'] = $realname;
        $data['bank_name'] = $bank_name;
        $data['bank_address'] = $bank_address;
        $data['bank_card'] = $bank_card;
        $data['money'] = $moneyT;
        $data['status'] = 0;
        $data['reason'] = "出局奖金提现";
        $data['time'] = time();

        $res = M('withdraw')->data($data)->add();
        if($res&&$rem){
            echo "<script>alert('提现申请成功，请等待工作人员与您联系');</script>";
            echo "<script>window.location.href='".U('Financemanagement/finance_withdrawalsHistory')."'</script>";
        }else{
            echo "<script>alert('提现申请失败');</script>";
            echo "<script>javascript:history.back(-1);</script>";die;
        }
    }

    //推荐奖金提现
    public function addwithdrawalsC(){
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
        $gold= M('store')->where('uid='.$userid)->getField('gold');
        if($gold<$money){
            echo "<script>alert('提现数量超过推荐奖金数量');</script>";
            echo "<script>javascript:history.back(-1);</script>";die;
        }
        $rem = M('store')->where('uid='.$userid)->setDec('gold',$money);
        $cash_fee = M('function_parameters')->where('id=1')->getField('cash_fee');
        $moneyT = $money-$money*$cash_fee;

        $data['uid'] = $userid;
        $data['realname'] = $realname;
        $data['bank_name'] = $bank_name;
        $data['bank_address'] = $bank_address;
        $data['bank_card'] = $bank_card;
        $data['money'] = $moneyT;
        $data['status'] = 0;
        $data['reason'] = "推荐奖金提现";
        $data['time'] = time();

        $res = M('withdraw')->data($data)->add();
        if($res&&$rem){
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

    public function finance_transaction(){
        $userid = session('userid');
        if(!I('post.')){
            $this->display();
        }else{
            $t=I('post.');
            foreach($t as $v){
                if($v == ''){
                    echo "<script>alert('请确认输入完成');</script>";
                    echo "<script>javascript:history.back(-1);</script>";die;
                }
            }
            $account = I('post.account');
            $money = I('post.money');
            $password = I('post.password');
            $userInfo = M('user')->where('userid='.$userid)->find();
            $buy_userid = M('user')->where("account='".$account."'")->getField('userid');
            $m=$money+0;
            if(!is_numeric($money) || $m<=0){
                echo "<script>alert('输入数量错误!');</script>";
                echo "<script>javascript:history.back(-1);</script>";die;
            }
            if(!$buy_userid){
                echo "<script>alert('目标用户信息不正确');</script>";
                echo "<script>javascript:history.back(-1);</script>";die;
            }
            if($userid==$buy_userid){
                echo "<script>alert('不可出售给自己');</script>";
                echo "<script>javascript:history.back(-1);</script>";die;
            }
            $storeInfo = M('store')->where('uid='.$userid)->find();
            if($storeInfo['buycar_money']<$money){
                echo "<script>alert('出售数量超过');</script>";
                echo "<script>javascript:history.back(-1);</script>";die;
            }
            $yu=$money%100;
            if ($yu!=0) {
                echo "<script>alert('数量请输入100的倍数');</script>";
                echo "<script>javascript:history.back(-1);</script>";die;

            }
            $twopw=md5(md5($password).$userInfo['safety_salt']);
            if($twopw!=$userInfo['password']){
                echo "<script>alert('交易密码输入错误');</script>";
                echo "<script>javascript:history.back(-1);</script>";die;
            }
            $res = M('store')->where('uid='.$userid)->setDec('buycar_money',$money);
            if(!$res){
                echo "<script>alert('出售失败');</script>";
                echo "<script>javascript:history.back(-1);</script>";die;
            }
            $rem = M('store')->where('uid='.$buy_userid)->setInc('buycar_money',$money);
            if(!$rem){
                echo "<script>alert('出售未到账，请截图并联系客服');</script>";
                echo "<script>javascript:history.back(-1);</script>";die;
            }
            $data['uid']=$userid;
            $data['buy_id']=$buy_userid;
            $data['money']=$money;
            $data['status']=1;
            $data['time']=time();
            $ren = M('transaction')->data($data)->add();
            if($ren&&$res&&$rem){
                echo "<script>alert('交易成功');</script>";
                echo "<script>window.location.href='".U('Financemanagement/finance_transactiondetailed')."'</script>";
            }else{
                echo "<script>alert('记录写入失败');</script>";
                echo "<script>javascript:history.back(-1);</script>";die;
            }
        }
    }

    public function finance_transactiondetailed(){
        $userid = session('userid');
        $transaction_list = M('transaction')->where('uid='.$userid)->select();
        foreach ($transaction_list as $k=>$v){
            $buy_account = M('user')->where('userid='.$v['buy_id'])->getField('account');
            $transaction_list[$k]['buy_account']=$buy_account;
        }
        $this->assign('transaction_list',$transaction_list);
        $this->display();
    }
}