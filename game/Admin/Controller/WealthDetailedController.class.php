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
}