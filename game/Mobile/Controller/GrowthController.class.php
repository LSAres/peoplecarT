<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/8/19
 * Time: 12:09
 */

namespace Mobile\Controller;
use Think\Controller;
class GrowthController extends CommonController {
	/* $list=$m->field(true)->where($where)->order('id desc')->select();
		$this->list=$list; */
    public function chengzhangjilu(){
        	

    	$this->display();
    }


     
   

      //===========农场生长记录=============== 
    public function guoyuanshengzhang(){
    	$dbcfjl=M('cfbsjl');
        $dbstore= M('store');
    	$userid=session('userid');
        $us=$dbstore->where('uid='.$userid.'')->find();
           
        $cInfo=$dbcfjl->where('u_id='.$userid.'')->order('id desc ')->limit(0, 80)->select();
        $this->assign(
              array(
             'cInfo'=>$cInfo,
             'us'     =>$us 
              	)
        	);

        $this->display();
    }

    //============增加记录===============
    public function bozhongjilu(){
		$userid=session('userid');
		$m=M('sow');
		$where='u_id='.$userid;
		// $p=getpage($m,$where,10);
		$arr=$m->where($where)->order('id desc')->limit(0, 80)->select();
		/* $this->list=$list; */
		// $this->page=$p->show();
		$this->assign('arr',$arr);
        $this->display();
    }

    //===========采蜜记录===============
    public function caimijilu(){
		$userid=session('userid');
		$m=M('caimi')->table('syd_caimi c,syd_user u')->field('c.*,u.account,u.username');
		$where='c.from_id=u.userid AND c.u_id='.$userid;
		// $p=getpage($m,$where,10);
		// $this->page=$p->show();		
		$arr=$m->where($where)->order('id desc')->limit(0, 80)->select();
		$sum = 0;
		foreach($arr as $k){
			$sum = $sum+$k['caimi_num'];
		}
		$this->assign('sum',$sum);
		$this->assign('arr',$arr);
        $this->display();
    }


    //===========水果农夫奖励记录===============
    public function daocaorenjiangli(){
		$userid=session('userid');
		$m=M('dcrjiangli');
		$where='u_id='.$userid;
		// $p=getpage($m,$where,10);		
		// $this->page=$p->show();
		$arr=$m->where($where)->select();
		$this->assign('arr',$arr);
        $this->display();
    }

  
   

    //===========看门狗===============
    public function kanmengou(){
		$userid=session('userid');
		$m=M('kmgreward');
		$where='u_id='.$userid;
		// $p=getpage($m,$where,10);		
		// $this->page=$p->show();
		$arr=$m->where($where)->select();
		$this->assign('arr',$arr);
        $this->display();
    }

    //===========施肥记录===============
    public function shifeijilu(){
		$userid=session('userid');
		$m=M('shifeijl');
		$where='u_id='.$userid;
		// $p=getpage($m,$where,10);		
		// $this->page=$p->show();
		$sum = 0;
		$arr=$m->where($where)->order('id desc')->limit(0, 80)->select();
		foreach($arr as $k){
			$sum = $sum+$k['shifei_num'];
		}
		$this->assign('sum',$sum);
		$this->assign('arr',$arr);
        $this->display();
    }
	//===========直推奖励记录===============
    public function ztjl(){
		$userid=session('userid');
		$m=M('ztjl');
		$where='u_id='.$userid;
		// $p=getpage($m,$where,10);		
		// $this->page=$p->show();
		$arr=$m->where($where)->order('id desc')->limit(0, 80)->select();
		$this->assign('arr',$arr);
        $this->display();
    }
	
	//===========小哈士奇奖励记录===============
    public function jlhsq(){
		$userid=session('userid');
		$m=M('jlhsq');
		$where='u_id='.$userid;
		// $p=getpage($m,$where,10);		
		// $this->page=$p->show();
		$arr=$m->where($where)->order('id desc')->limit(0, 80)->select();
		$this->assign('arr',$arr);
        $this->display();
    }
	
	//===========开地奖励记录===============
    public function kdjl(){
		$userid=session('userid');
		$m=M('nzusfarm');
		$condition['u_id']=$userid;
		$condition['show_tu'] = array('gt',0);
		// $p=getpage($m,$where,10);		
		// $this->page=$p->show();
		$count=$m->where($condition)->count();
		$wukuai=0;
		$shikuai=0;
		$shiwukuai=0;
		if($count>=5){
			$wukuai=1;
		}
		if($count>=10){
			$shikuai=1;
		}
		if($count>=15){
			$shiwukuai=1;
		}
		
		$this->assign('wukuai',$wukuai);
		$this->assign('shikuai',$shikuai);
		$this->assign('shiwukuai',$shiwukuai);
        $this->display();
    }
	
	//===========一键采蜜购买记录===============
    public function buyonecaimi(){
		$userid=session('userid');
		$m=M('buycaimi');
		$where='userid='.$userid;
		// $p=getpage($m,$where,10);		
		// $this->page=$p->show();
		$arr=$m->where($where)->limit(0, 80)->select();
		$this->assign('arr',$arr);
        $this->display();
    }
	
	//===========转盘奖励记录===============
    public function zhuanpanjl(){
		$userid=session('userid');
		$m=M('nzbill');
		$where='bill_userid='.$userid;
		// $p=getpage($m,$where,10);		
		// $this->page=$p->show();
		$arr=$m->where($where)->order('bill_id desc')->limit(0, 80)->select();
		$this->assign('arr',$arr);
        $this->display();
    }


    //===========收割记录===============
    public function shougejilu(){
		$userid=session('userid');
		$m=M('shouge');
		$where='u_id='.$userid;
		// $p=getpage($m,$where,10);		
		// $this->page=$p->show();
		$arr=$m->where($where)->order('id desc')->limit(0, 80)->select();
		$this->assign('arr',$arr);
        $this->display();
    }

    //==========种子奖励记录===============
    public function zhongzijiangli(){
		$userid=session('userid');
		$m=M('zhongzijiangli')->field('z.*,u.account,u.username')->table('syd_zhongzijiangli z,syd_user u');
		$where='z.recommond_id=u.userid AND z.u_id='.$userid;
		// $p=getpage($m,$where,10);		
		// $this->page=$p->show();
		$arr=$m->where($where)->order('id desc')->limit(0, 80)->select();
		$this->assign('arr',$arr);
        $this->display();
    }















}