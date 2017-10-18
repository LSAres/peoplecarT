<?php
namespace Admin\Controller;
use Think\Controller;

class WealthDetailedController extends CommonController{
    public function bonusDetails(){
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
    public function userCapitalOffset(){
        $this->display();
    }
    public function userChargeHistory(){
        $where = null;
        $tb_new = M('charge');
        $pagesize =10;
        $p = getpage($tb_new, $where, $pagesize);
        $pageshow   = $p->show();

        $newArr=$tb_new->where($where)
            ->field('id,uid,phone,wx_no,money,time,status')
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
}