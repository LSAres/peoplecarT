<?php
namespace Admin\Controller;
use Think\Controller;
class ProportionController extends CommonController {
	//转盘概率
	public function zhuanpan(){
		$SysConfig=F('SysConfig','','./Public/data/');
		$gailv=$SysConfig['gailv'];
		$this->gailv=$gailv;
		$this->display();
	}

	//接收修改数据
	public function savezhuanpan(){
        $SysConfig=F('SysConfig','','./Public/data/');
        $SysConfig['gailv']=I('post.');
        $SysConfig=F('SysConfig',$SysConfig,'./Public/data/');
        echo "<script>alert('修改成功');</script>";
        echo "<script>location.href='".U('Proportion/zhuanpan')."'</script>";
	}
	



	//修改拆分倍数调用页面
	public function cs(){
		$dbcf=M('cfbs');
		$gailv=$dbcf->select();
		/*P($gailv);
		exit;*/
		$this->gailv=$gailv;
		$this->display();
	}
	//修改拆分倍数改数据
	public function savecs(){
		$dbcf=M('cfbs');
		$data = I('post.');
         
	   foreach($data as $key=>$value){
          $aabb=$dbcf->where('cfbs_id='.$key.'')->setField('cfbs_value',$value);   
	   }

       echo "<script>alert('修改成功')</script>";
	   echo "<script>location.href='".U('Proportion/cs')."'</script>"; 
	}


	//修改推荐奖
	public function zhitui(){
		$zhitui=F('zhi_tui','','./Public/data/');
		//p($zhitui);
		$this->zhitui=$zhitui;
		$this->display();
	}

	//修改
	public function savezhitui(){
	$zhitui = I('post.');
	//限制反限最大值
	$zhitui['zhitui']  = ($zhitui['zhitui'] > 100 )?100:$zhitui['zhitui'];
	F('zhi_tui',$zhitui,'./public/data/');
	echo "<script>alert('修改成功!'); history.back(-1);</script>";
	}
	//修改手续费
	public function zhuanzhang(){
		$zhuanzhang=F('zhuan_zhang','','./Public/data/');

		
		$this->zhuanzhang=$zhuanzhang;
		$this->display();
	}

	//修改
	public function savezhuanzhang(){
	$zhuanzhang = I('post.');
	foreach($zhuanzhang as $v){
		if($v==''){
			echo "<script>alert('请输入完整!');history.back(-1);</script>";
			
		}
	}

	F('zhuan_zhang',$zhuanzhang,'./Public/data/');
	echo "<script>alert('修改成功!'); history.back(-1);</script>";
	}





  //农田最大值和最小值
  public function minmax(){
      $mm=F('minmaxxx','','./Public/data/');
      $this->mm=$mm;
      $this->display();
  }


   //农田最大值和最小值save
  public function minmaxsave(){
        $data=I('post.');  
        $a=F('minmaxxx',$data,'./Public/data/');
        echo "<script>alert('修改成功');</script>";
         echo "<script>location.href='".U('Proportion/minmax')."'</script>";
  }


  public function SysConfig(){
      $SysConfig=F('SysConfig','','./Public/data/');
      $num = M('day')->where()->count();
      if($num<1){
          $data['id']=1;
          $data['day']=5;
          $data['create_time']=time();
          $data['update_time']=time();
          M('day')->add($data);
      }
      $day = M('day')->where("id=1")->getField('day');
      $this->assign('day',$day);
      $this->SysConfig=$SysConfig;
      $this->display();
  }


  public function SysConfigSave(){
        $SysConfig=F('SysConfig','','./Public/data/');
        $data=I('post.');
        $data['gailv']=$SysConfig['gailv'];
        $day = $data['day'];
        M('day')->where('id=1')->setField('day',$day);
        $SysConfig=F('SysConfig',$data,'./Public/data/');
        echo "<script>alert('修改成功');</script>";
        echo "<script>location.href='".U('Proportion/SysConfig')."'</script>";
  }

  public function Quota(){
      $num = M('quote')->where()->count();
      if (num<7){
          $count = 7-$num;
          for ($i=0;$i<$count;$i++){
              $data['proportion']=10;
              $data['id']=$count-$i;
              M('quote')->add($data);
          }
      }
      $data = M('quote')->where()->select();
      $shuinong = $data[0]['proportion'];
      $huonong = $data[1]['proportion'];
      $munong = $data[2]['proportion'];
      $jinnong = $data[3]['proportion'];
      $funong = $data[4]['proportion'];
      $dizhu = $data[5]['proportion'];
      $this->assign('shuinong',$shuinong);
      $this->assign('huonong',$huonong);
      $this->assign('munong',$munong);
      $this->assign('jinnong',$jinnong);
      $this->assign('funong',$funong);
      $this->assign('dizhu',$dizhu);
      $this->display();
  }

  public function QuoteSave(){
      $data = I('post.');
      M('quote')->where('id='.$data['id1'])->setField('proportion',$data['proportion1']);
      M('quote')->where('id='.$data['id2'])->setField('proportion',$data['proportion2']);
      M('quote')->where('id='.$data['id3'])->setField('proportion',$data['proportion3']);
      M('quote')->where('id='.$data['id4'])->setField('proportion',$data['proportion4']);
      M('quote')->where('id='.$data['id5'])->setField('proportion',$data['proportion5']);
      M('quote')->where('id='.$data['id6'])->setField('proportion',$data['proportion6']);
      echo "<script>alert('修改成功');</script>";
      echo "<script>location.href='".U('Proportion/Quota')."'</script>";
  }
  
   public function dayshouyi(){

        //============================================
        //ignore_user_abort(); //即使Client断开(如关掉浏览器)，PHP脚本也可以继续执行.
        // set_time_limit(0); // 执行时间为无限制，php默认执行时间是30秒，可以让程序无限制的执行下去
		
		echo "<meta charset='utf-8'>";

        $num = M('data')->where()->order('id desc')->limit(0,1)->select();
		if( $num==null){
			$last_login_time=0;
		}else{
			$last_login_time=M('data')->where('id='.$num[0]['id'])->getField('time');
		}
        $now_login=time();
        $dbu=M('user');
        $cfbs=M('cfbs');
        $dbstore=M('store');
        $dbcfbsjl=M('cfbsjl');
        $userid=session('userid');
        $ciInfo=$cfbs->select();
        $useInfo=$dbu->where()->select();

        //用户的道具信息
        foreach ($useInfo as $k){
            //拆分表比例
            $jccf=$ciInfo[0]['cfbs_value'];//基础拆分倍数
            $hsqcf=$ciInfo[2]['cfbs_value'];//哈士奇拆分倍数
            $zacf=$ciInfo[4]['cfbs_value'];//藏獒拆分倍数
            $dcrcf=$ciInfo[1]['cfbs_value'];//稻草人拆分倍数
            $zkbl=$ciInfo[3]['cfbs_value'];//总扣比例
            $uInfo=$dbstore->where('uid='.$k['userid'].'')->find();
            //用户的道具信息
            $zangao_num=$uInfo['zangao_num'];   //藏獒数量
            $hashiqi_num=$uInfo['hashiqi_num'];  //哈士奇数量
            $dcr_num=$uInfo['dcr_num'];      //稻草人数量
            //要扣掉的比例

            $koubl=$zkbl-$dcr_num*$dcrcf;

            //今天最后收益比例
            $totalcf=$jccf+$zacf*$zangao_num+$hsqcf*$hashiqi_num-$koubl;


            ($totalcf<0.01)?$totalcf=0.01:$totalcf=$totalcf;

            $huafei_num = $uInfo['huafei_total'];

            $tui_num= $dbu->where('parent_id='.$k['userid'].'')->count();
            if($tui_num<3){
                if($uInfo['huafei_total']>=$uInfo['plant_num']*5){
                    $totalcf-=0.02;
                }
                if($uInfo['huafei_total']>=$uInfo['plant_num']*4&&$uInfo['huafei_total']<$uInfo['plant_num']*5){
                    $totalcf-=0.015;
                }
                if($uInfo['huafei_total']>=$uInfo['plant_num']*3&&$uInfo['huafei_total']<$uInfo['plant_num']*4){
                    $totalcf-=0.01;
                }
                if($uInfo['huafei_total']>=$uInfo['plant_num']*2&&$uInfo['huafei_total']<$uInfo['plant_num']*3){
                    $totalcf-=0.005;
                }
            }
            if($tui_num>=3&&$tui_num<10){
                if($uInfo['huafei_total']>=$uInfo['plant_num']*5){
                    $totalcf-=0.015;
                }
                if($uInfo['huafei_total']>=$uInfo['plant_num']*4&&$uInfo['huafei_total']<$uInfo['plant_num']*5){
                    $totalcf-=0.01;
                }
                if($uInfo['huafei_total']>=$uInfo['plant_num']*3&&$uInfo['huafei_total']<$uInfo['plant_num']*4){
                    $totalcf-=0.005;
                }
                if($uInfo['huafei_total']>=$uInfo['plant_num']*2&&$uInfo['huafei_total']<$uInfo['plant_num']*3){
                    $totalcf-=0.002;
                }
            }

            if ($zangao_num == 1) {
                if ($huafei_num >= 108000 && $huafei_num < 126000) $totalcf-=0.005;
                if ($huafei_num >= 126000 && $huafei_num < 144000) $totalcf-=0.01;
                if ($huafei_num >= 144000 && $huafei_num < 162000) $totalcf-=0.015;
                if ($huafei_num >= 162000) $totalcf = 0;
            } else {
                if ($dcr_num == 1) {
                    if ($huafei_num >= 54000 && $huafei_num < 72000) $totalcf-=0.005;
                    if ($huafei_num >= 72000 && $huafei_num < 90000) $totalcf-=0.01;
                    if ($huafei_num >= 90000 && $huafei_num < 108000) $totalcf-=0.015;
                    if ($huafei_num >= 108000) $totalcf = 0;
                }

                if ($dcr_num == 2) {
                    if ($huafei_num >= 72000 && $huafei_num < 90000) $totalcf-=0.005;
                    if ($huafei_num >= 90000 && $huafei_num < 108000) $totalcf-=0.01;
                    if ($huafei_num >= 108000 && $huafei_num < 126000) $totalcf-=0.015;
                    if ($huafei_num >= 126000) $totalcf = 0;
                }

                if ($dcr_num == 3) {
                    if ($huafei_num >= 90000 && $huafei_num < 108000) $totalcf-=0.005;
                    if ($huafei_num >= 108000 && $huafei_num < 126000) $totalcf-=0.01;
                    if ($huafei_num >= 126000 && $huafei_num < 144000) $totalcf-=0.015;
                    if ($huafei_num >= 144000) $totalcf = 0;
                }

                if ($dcr_num == 4) {
                    if ($huafei_num >= 90000 && $huafei_num < 108000) $totalcf-=0.005;
                    if ($huafei_num >= 108000 && $huafei_num < 126000) $totalcf-=0.01;
                    if ($huafei_num >= 126000 && $huafei_num < 144000) $totalcf-=0.015;
                    if ($huafei_num >= 144000) $totalcf = 0;
                }
            }
			
			$zongshouyi = M('store')->where('uid='.$k['userid'])->getField('zongshouyi');
			$khjq = M('user')->where('userid='.$k['userid'])->getField('khjq');
			if($zongshouyi>=$khjq){
				M('store')->where('uid='.$k['userid'])->setField('zangao_num',0);
			}
			

            //向用户仓库添加鸭蛋
            $total_fl=$uInfo['plant_num']*$totalcf;//今天产生的总鸭蛋



            $dbstore->where('uid='.$k['userid'])->setField('huafei_num',$total_fl);


            //用户直接推荐了多少会员
            $zthy=$dbu->where('parent_id='.$k['userid'])->getField('userid',true);
            $hyNum=count($zthy);
            $data=array();
            //把数据存入农场生长记录表
            $data['u_id']=$k['userid'];
            $data['zthy']=$hyNum;
            $data['huiliao']=$total_fl;
            $data['jccf']=$jccf;//	基础拆分
            $data['hscf']=$hsqcf*$hashiqi_num; //哈士奇拆分
            $data['zacf']=$zacf*$zangao_num;//藏獒拆分
            $data['dcrcf']=$dcr_num*$dcrcf;//稻草人拆分
			$data['dcr_daycf'] = $ciInfo[3]['cfbs_value'];//当天稻草人拆分率
            $data['kccf']=-$koubl ; //扣除拆分
            $data['zcf']= $totalcf;  //总拆分
            $data['time']= time(); //时间
			

            $dbcfbsjl->data($data)->add();
            $db_dcrjiangli = M('dcrjiangli');
            $dcrjl = array();
            $dcrjl['u_id'] = $k['userid'];
            $dcrjl['reward_reason'] = "直推奖励";
            $dcrjl['reward_time'] = time();
            $db_dcrjiangli->data($dcrjl)->add();
        }
        if (date('Ymd', 	$last_login_time) != date('Ymd',$now_login)){
            if ($last_login_time<$now_login){

                $condition['farm_type']=1;
                $condition['show_tu'] = array('gt',0);
                $condition1['farm_type']=2;
                $condition1['show_tu'] = array('gt',0);
                $condition2['farm_type']=3;
                $condition2['show_tu'] = array('gt',0);
                $huang_count = M('nzusfarm')->where($condition)->count('id');
                $hong_count = M('nzusfarm')->where($condition1)->count('id');
                $hei_count = M('nzusfarm')->where($condition2)->count('id');
                $mm=F('minmaxxx','','./Public/data/');
                $sum=M('user')->where()->count('userid');
                $awake_sum = M('user')->where('lockuser=0')->count('userid');
                if($num<1||$num==null){
                    $open_user_sum = $sum;
                    $awake = $awake_sum;
                }else{
                    $renshu = M('data')->where('id='.$num[0]['id'])->getField('user_sum');
                    $open_user_sum = $sum-$renshu;
                    $awake_renshu = M('data')->where('id='.$num[0]['id'])->getField('awake_sum');
                    $awake = $awake_sum-$awake_renshu;
                }


                $data['time']=time();
                $data['product_sum'] = M('store')->where()->sum('fruit_total');
                $data['farm_product_sum'] = M('nzusfarm')->where()->sum('guozi_num');
                $data['reclaim_product_sum'] = $huang_count*$mm['huang_min']+$hong_count*$mm['hong_min']+$hei_count*$mm['hei_min'];
                $data['store_product_sum'] = M('store')->where()->sum('cangku_num');
                $data['profit_sum'] = M('store')->where()->sum('huafei_total');
                $data['Profit'] = M('store')->where()->sum('huafei_num');
                $data['chart']= M('cfbs')->where('cfbs_id=1')->getField('cfbs_value');
                $data['open_account_sum'] = $open_user_sum;
                $data['user_sum']=$sum;
                $data['trading_volume'] = M('nzsell_fruit')->where($condition3)->sum('sell_num');
                if ($data['trading_volume']==null){
                    $data['trading_volume']=0;
                }
                $data['awake_sum'] = $awake_sum;
                $data['awake_num']=$awake;
				$data['zongjifen']=M('store')->where()->sum('sc_jifen');
                M('data')->data($data)->add();
				
				$db_cfbs = M('cfbs');
       		 $db_cfbsrq = M('cfbsrq');
       		 $cfbs_value = $db_cfbs->where('cfbs_id=1')->getField('cfbs_value');
       		 $dmm['date'] = date('Y-m-d',time());
       		 $dmm['value'] = $cfbs_value;
             $db_cfbsrq->add($dmm);
            }

        }
		echo "<script>alert('拆分成功');</script>";
        echo "<script>location.href='".U('Proportion/cs')."'</script>";


    }













	
}

