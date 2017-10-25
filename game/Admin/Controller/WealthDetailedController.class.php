<?php
namespace Admin\Controller;
use Think\Controller;

class WealthDetailedController extends CommonController{
    public function bonusDetails(){
        $where = null;
        $tb_new = M('bonus_record');
        $pagesize =10;
        $p = getpage($tb_new, $where, $pagesize);
        $pageshow   = $p->show();

        $newArr=$tb_new->where($where)
            ->order('id desc ')
            ->select();

        foreach($newArr as $k=>$v){
            $userInfo = M('user')->where('userid='.$v['uid'])->find();
            $newArr[$k]['account'] = $userInfo['account'];
            $newArr[$k]['username'] = $userInfo['username'];
        }

        $this->assign(array(
            'userArr'=>$newArr,
            'pageshow'=>$pageshow,
        ));
        $this->display();
    }
    public function cashHistory(){
        $where = null;
        $tb_new = M('withdraw');
        $pagesize =10;
        $p = getpage($tb_new, $where, $pagesize);
        $pageshow   = $p->show();

        $newArr=$tb_new->where($where)
            ->field('id,uid,realname,bank_name,bank_address,bank_card,money,status,time')
            ->order('id desc ')
            ->select();

        foreach($newArr as $k=>$v){
            $userInfo = M('user')->where('userid='.$v['uid'])->find();
            $newArr[$k]['account'] = $userInfo['account'];
            $newArr[$k]['username'] = $userInfo['username'];
        }

        $this->assign(array(
            'userArr'=>$newArr,
            'pageshow'=>$pageshow,
        ));
        $this->display();
    }
    public function refundAplyHistory(){
        $this->display();
    }
    public function remittanceHistory(){
        $where = null;
        $tb_new = M('remittance');
        $pagesize =10;
        $p = getpage($tb_new, $where, $pagesize);
        $pageshow   = $p->show();

        $newArr=$tb_new->where($where)
            ->field('id,receivables_account,receivables_username,remittance_money,remittance_bank,remittance_user')
            ->order('id desc ')
            ->select();

        $this->assign(array(
            'userArr'=>$newArr,
            'pageshow'=>$pageshow,
        ));
        $this->display();
    }

    public function userChargeHistory(){
        $where = null;
        $tb_new = M('charge');
        $pagesize =10;
        $p = getpage($tb_new, $where, $pagesize);
        $pageshow   = $p->show();

        $newArr=$tb_new->where($where)
            ->field('id,uid,phone,wx_no,alipay_no,bank_name,bank_no,card_name,order_no,money,time,status')
            ->order('id desc ')
            ->select();

        foreach($newArr as $k=>$v){
            $userInfo = M('user')->where('userid='.$v['uid'])->find();
            $newArr[$k]['account'] = $userInfo['account'];
            $newArr[$k]['username'] = $userInfo['username'];
        }

        $this->assign(array(
            'userArr'=>$newArr,
            'pageshow'=>$pageshow,
        ));
        $this->display();
    }

    public function userDetails(){
        $where = null;
        $tb_new = M('registration_record');
        $pagesize =10;
        $p = getpage($tb_new, $where, $pagesize);
        $pageshow   = $p->show();

        $userArr=$tb_new->where($where)
            ->field('id,uid,account,username,time')
            ->order('id desc ')
            ->select();

        $this->assign(array(
            'userArr'=>$userArr,
            'pageshow'=>$pageshow,
        ));
        $this->display();
    }

    public function appendNewMessageButton(){
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
            $receivables_account=I('post.receivables_account');
            $receivables_username=I('post.receivables_username');
            $remittance_money=I('post.remittance_money');
            $remittance_bank=I('post.remittance_bank');
            $remittance_user=I('post.remittance_user');
            $data['receivables_account']=$receivables_account;
            $data['receivables_username']=$receivables_username;
            $data['remittance_money']=$remittance_money;
            $data['remittance_bank']=$remittance_bank;
            $data['remittance_user']=$remittance_user;
            $data['time']=time();
            $res = M('remittance')->data($data)->add();
            if($res){
                echo "<script>alert('添加成功');location.href='".U('WealthDetailed/remittanceHistory')."'</script>";
                exit();
            }else{
                echo "<script>alert('添加失败');</script>";
                echo "<script>javascript:history.back(-1);</script>";die;
            }
        }
    }

    public function userActivationDetails(){
        $where = null;
        $tb_new = M('activation_record');
        $pagesize =10;
        $p = getpage($tb_new, $where, $pagesize);
        $pageshow   = $p->show();

        $userArr=$tb_new->where($where)
            ->field('id,uid,account,username,time')
            ->order('id desc ')
            ->select();

        $this->assign(array(
            'userArr'=>$userArr,
            'pageshow'=>$pageshow,
        ));
        $this->display();
    }

    public function userCapitalOffset(){
        $where = null;
        $tb_new = M('store');
        $pagesize =10;
        $p = getpage($tb_new, $where, $pagesize);
        $pageshow   = $p->show();

        $userArr=$tb_new->where($where)
            ->order('id desc ')
            ->select();
        foreach ($userArr as $k=>$v){
            $userInfo = M('user')->where('userid='.$v['uid'])->find();
            $userArr[$k]['username']=$userInfo['account'];
            $userArr[$k]['account']=$userInfo['username'];
        }
        $this->assign(array(
            'userArr'=>$userArr,
            'pageshow'=>$pageshow,
        ));
        $this->display();
    }

    public function userCapitalOffset_Rechange(){
        if(!I('post.')){
            $id = I('get.id');
            $storeInfo = M('store')->where('id='.$id)->find();
            $userInfo = M('user')->where('userid='.$storeInfo['uid'])->find();
            $this->assign('userInfo',$userInfo);
            $this->assign('storeInfo',$storeInfo);
            $this->display();
        }else{
            $id = I('post.id');
//            $report_money = I('post.report_money');
            $buycar_money = I('post.buycar_money');
            if(!$id){
                echo "<script>alert('系统错误');</script>";
                echo "<script>javascript:history.back(-1);</script>";die;
            }
//            if($report_money<0){
//                echo "<script>alert('报单币数量不可为负');</script>";
//                echo "<script>javascript:history.back(-1);</script>";die;
//            }
            if($buycar_money<0){
                echo "<script>alert('购车基金数量不可为负');</script>";
                echo "<script>javascript:history.back(-1);</script>";die;
            }
//            $res = M('store')->where('id='.$id)->setField('report_money',$report_money);
            $rec = M('store')->where('id='.$id)->setField('buycar_money',$buycar_money);
            if($rec){
                echo "<script>alert('修改成功');location.href='".U('WealthDetailed/userCapitalOffset')."'</script>";
                exit();
            }else{
                echo "<script>alert('修改失败');</script>";
                echo "<script>javascript:history.back(-1);</script>";die;
            }
        }
    }

    public function updateCharge(){
        $id = I('get.id');
        $res = M('charge')->where('id='.$id)->setField('status',1);
        if($res){
            echo "<script>alert('审核成功');location.href='".U('WealthDetailed/userChargeHistory')."'</script>";
            exit();
        }else{
            echo "<script>alert('审核失败');</script>";
            echo "<script>javascript:history.back(-1);</script>";die;
        }
    }

    public function updateCash(){
        $id = I('get.id');
        $res = M('withdraw')->where('id='.$id)->setField('status',1);
        if($res){
            echo "<script>alert('审核成功');location.href='".U('WealthDetailed/cashHistory')."'</script>";
            exit();
        }else{
            echo "<script>alert('审核失败');</script>";
            echo "<script>javascript:history.back(-1);</script>";die;
        }
    }
}