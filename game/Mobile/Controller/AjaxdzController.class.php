<?php
namespace Mobile\Controller;
use Think\Controller;
class AjaxdzController extends CommonController {
	// ==================开垦ajax开始=======================开垦ajax开始====================开垦ajax开始========= 
     //===============黄土地1开垦=============================
	public function kaiken(){
          $db_farm=M('nzusfarm');
          $db_store=M('store');
          $userid=session('userid');
          //=====仓库取条用户的数据来做判断=============
          $ckInfo=$db_store->where("uid=$userid")->find();
          $guoNum=$ckInfo['cangku_num'];
          $type=I('post.farmtype');//土地类型

           //农田里最低和最高保留的水果数  
          $mm=F('minmaxxx','','./Public/data/');
          switch ($type) {
          	case '1':
          		$min_guozi=$mm['huang_min'];
          		$max_guozi=$mm['huang_max'];
          		break;
          	case '2':
          		$min_guozi=$mm['hong_min'];
          		$max_guozi=$mm['hong_max'];
          		break;
          	default:
          		$min_guozi=$mm['hei_min'];
          		$max_guozi=$mm['hei_max'];
          		break;
          }

          //如果是黄土地需要300桔子
            if ($guoNum<$min_guozi) { 
            	$data['content']=C('KAIKEN')."此地至少需要".$min_guozi."个水果"; 
            	$data['status']="huangtdsb";
            	$this->ajaxReturn($data);
            	return; 
            }
       
          $farmid=I('post.farmid'); 
          $zdh_value=I('post.zdh_value');

        if($zdh_value=='kaiken'){
        	$db_farm->startTrans();
        	$where['u_id']=$userid;
        	$where['f_id']=$farmid;

        	$data['show_tu']=1;
        	$data['guozi_num']=$min_guozi;
			$data['land_leve']=1;
        	$kaiken=$db_farm->where($where)->save($data);//把树种到地里

			//通过地数判断是否赠送哈士奇和藏獒
   			$db_farm = M('nzusfarm');
   			$map['u_id'] = $userid;
   			$map['show_tu'] = array('GT', 0);
   			$farmnum = $db_farm->where($map)->count();
   			$dbstore = M('store');
   			switch (true) {
       			case $farmnum >= 5 && $farmnum < 10:
           		$dbstore->where('uid='.$userid)->setField('zangao_num',1);
          	 break;

       			case $farmnum >= 10 && $farmnum < 15:
          		 $dbstore->where('uid='.$userid)->setField('zangao_num',2);
          		 break;
				 
				 case $farmnum >= 15:
          		 $dbstore->where('uid='.$userid)->setField('zangao_num',3);
          		 break;
       		
   			}
			
        	// 去更新仓库表的cangku_num字段
        	$a=$db_store->where('uid='.$userid.'')->setDec('cangku_num',$data['guozi_num']);
        	 
        	//更新仓库表的种植字段
            $plant_num=$db_store->where('uid='.$userid.'')->setInc('plant_num',$data['guozi_num']);
           
            //再把更新后种植字段和仓库字段更新到总水果字段
            $bbb=$db_store->where("uid=$userid")->find();
            $zsg=$bbb['plant_num']+$bbb['cangku_num'];
            $cc=$db_store->where('uid='.$userid.'')->setField('fruit_total',$zsg);


        	if ($kaiken&&$a&&$plant_num) {
        	   $db_farm->commit();
        	   $data['content']=C('KAIKEN').'成功'; 
	           $data['status']="kkcg";
	           $this->ajaxReturn($data);
        	}else{
        	   $db_farm->rollback();
        	   $data['content']=C('KAIKEN').'失败'; 
	           $data['status']="kksb";
	           $this->ajaxReturn($data);
        	}
        } 

	}
  


//============================增加==================================================

   //==============第1块地增加==========
   public function bozhong(){  
          $db_farm=M('nzusfarm');
          $db_store=M('store'); 
          $db_sow=M('sow');
          $userid=session('userid');
         //===ajax_post过来的数据==========
          $farmid=I('post.farmid'); 
          $zdh_value=I('post.zdh_value');
          $type=I('post.farmtype');
          $bz_num=I('post.bz_num');

           //农田里最低和最高保留的水果数  
          $mm=F('minmaxxx','','./Public/data/');
          switch ($type) {
            case '1':
              $min_guozi=$mm['huang_min'];
              $max_guozi=$mm['huang_max'];
              break;
            case '2':
              $min_guozi=$mm['hong_min'];
              $max_guozi=$mm['hong_max'];
              break;
            default:
              $min_guozi=$mm['hei_min'];
              $max_guozi=$mm['hei_max'];
              break;
          }
          //====农田的条件=======
          $fwhere['u_id']=$userid;
          $fwhere['f_id']=$farmid;
          $f_guozi=$db_farm->where($fwhere)->getField('guozi_num');
          $farmgz=$f_guozi+$bz_num;
          //判断增加的水果是否大于最大值
            if ($farmgz>$max_guozi) {
          	   $data['content']='最多能种'.$max_guozi.C('GUOZI'); 
	           $data['status']="cgmx";
	           $this->ajaxReturn($data); 
	           return;
            }

          //=====仓库取条用户的数据来做判断=============
          $ckInfo=$db_store->where("uid=$userid")->find();
          if ($ckInfo['cangku_num']<$bz_num) {
          	   $data['content']='仓库的'.C('GUOZI').'不够'; 
	           $data['status']="gzbg";
	           $this->ajaxReturn($data); 
	           return;
          }
           
          $db_store->startTrans();
          // ====把种子种到地里面========= 
          $a=$db_farm->where($fwhere)->setField('show_tu',2);//农田表增加  
 
          $b=$db_farm->where($fwhere)->setInc('guozi_num',$bz_num);//农田表数量的增加
		  $guozi_num = $db_farm->where($fwhere)->getField('guozi_num');
		  $sanbei = $min_guozi*3;
		  if($guozi_num>$min_guozi&&$guozi_num<$sanbei){
		  	$db_farm->where($fwhere)->setField('land_leve',2);
		  }
		  if($guozi_num>=$sanbei){
		  	$db_farm->where($fwhere)->setField('land_leve',3);
		  }
          $c=$db_store->where('uid='.$userid.'')->setDec('cangku_num',$bz_num);//仓库表仓库字段减少播进去的数量
          $d=$db_store->where('uid='.$userid.'')->setInc('plant_num',$bz_num);//仓库表种植字段增加播进去的数量
         //========增加完之后再去改变总水果的数量==============
          $ck=$db_store->where('uid='.$userid.'')->find(); 
          $uph_fruittotal=$ck['cangku_num']+$ck['plant_num'];
          $e=$db_store->where('uid='.$userid.'')->setField('fruit_total',$uph_fruittotal);//更新仓库表总数
         //把增加记录起来
          $databz['u_id']=$userid;
          $databz['farm_id']=$farmid;
          $databz['sow_num']=$bz_num;
          $databz['farm_type']=$type;
          $databz['sow_type']="增种";
          $databz['sow_time']=time();
          $bzjl=$db_sow->data($databz)->add();  
           
          
          if ($a||$c&&$d&&$bzjl) {
          	   $db_store->commit();
          	   $data['content']=C('BOZHONG').'成功'; 
	           $data['status']="bzcg";
	           $this->ajaxReturn($data); 
	           return;
          }else{
          	   $db_store->rollback();
          	   $data['content']=C('BOZHONG').'失败'; 
	           $data['status']="bzsb";
	           $this->ajaxReturn($data); 
	           return;
          }
   }


   

//========增加结束啦=============增加结束啦============增加结束啦==========增加结束啦====== ==增加结束啦========


 





//=============施肥开始啦============施肥开始啦==============施肥开始啦=========施肥开始啦===========施肥开始啦==================================



   public function shifei(){

          $db_farm=M('nzusfarm');
          $db_store=M('store'); 

          $userid=session('userid');
         //===ajax_post过来的数据==========
          $farmid=I('post.farmid'); 
          $zdh_value=I('post.zdh_value');
          $type=I('post.farmtype');
          $bz_num=I('post.bz_num');
          //农田里最低和最高保留的水果数  
          $mm=F('minmaxxx','','./Public/data/');
          switch ($type) {
            case '1':
              $min_guozi=$mm['huang_min'];
              $max_guozi=$mm['huang_max'];
              break;
            case '2':
              $min_guozi=$mm['hong_min'];
              $max_guozi=$mm['hong_max'];
              break;
            default:
              $min_guozi=$mm['hei_min'];
              $max_guozi=$mm['hei_max'];
              break;
          }

          //=====仓库取条用户的数据来做判断=============
          $ckInfo=$db_store->where("uid=$userid")->find();
          if ($ckInfo['huafei_num']<=0) {
               $data['content']=C('FEILIAO').'没有了'; 
             $data['status']="hfmyl";
             $this->ajaxReturn($data); 
             return;
          }
           
          $db_store->startTrans();
          // ====农田的条件=========
          $fwhere['u_id']=$userid;
          $fwhere['f_id']=$farmid;
          
          // 去看用户的仓库还有多少肥料
          $ufeiliao=$db_store->where('uid='.$userid.'')->getField('huafei_num');
          

          //去拿用户田里已经有多少水果
          $uguozi=$db_farm->where($fwhere)->getField('guozi_num');  
          //如果田里面一达到最大值，我就不让他施肥了 
          if ($uguozi>$max_guozi||$uguozi==$max_guozi) {
               $data['content']='这块地已达到最大种植数量，请换块地施肥'; 
               $data['status']="hdsf";
               $this->ajaxReturn($data); 
               return;
          } 
          $pdsf=$ufeiliao+$uguozi;//判断该相这块地施多少肥料
          if ($pdsf>=$max_guozi) { 
              //实际施肥
              $sjsf=$max_guozi-$uguozi;
          }else{
              $sjsf=$ufeiliao;
          }
          
          //用户仓库减少施去的化肥   
           $c=$db_store->where('uid='.$userid.'')->setDec('huafei_num',$sjsf);//仓库表化肥字段减少施肥的数量 
          //农田里面增加施肥的水果  
           $jj=$db_farm->where($fwhere)->setInc('guozi_num',$sjsf);//更新农田表的水果 
           //更新仓库表的种植水果
           $hh=$db_store->where('uid='.$userid.'')->setInc('plant_num',$sjsf);
          //每一次施肥都要把之前其他块地的清掉
               $qd['shifei_status']=0;
               $qd['shifei_xiaoyi']=0;
               $qd['caimi_idstr']="";
               $qd['shifei_time']=0;
         $qing=$db_farm->where('u_id='.$userid.'')->save($qd);
       

          //把施肥后所产生的效益和状态去更新这块地
            $mv['shifei_status']=1;
            $mv['shifei_xiaoyi']=$sjsf;
            $mv['show_tu']=2;
            $mv['caimi_idstr']="";
            $mv['shifei_time']=time();
            $xx=$db_farm->where($fwhere)->save($mv); 
			
			$SysConfig=F('SysConfig','','./Public/data/');
			$sj_shouyi = $sjsf*$SysConfig['c_cm1j']*0.01;
			$parent_id = M('user')->where('userid='.$userid)->getField('parent_id');
			if($parent_id!=null){
				M('store')->where('uid='.$userid)->setInc('sj_shouyi',$sj_shouyi);
				M('store')->where('uid='.$parent_id)->setInc('cangku_num',$sj_shouyi);
				$sc['u_id']=$parent_id;
				$sc['from_id']=$userid;
				$sc['farm_id']=1;
				$sc['caimi_num']=$sj_shouyi;
				$sc['caimi_time']=time();
				M('caimi')->data($sc)->add();
			}

         //========施肥完之后再去改变总水果的数量==============
          $ck=$db_store->where('uid='.$userid.'')->find(); 
          $uph_fruittotal=$ck['cangku_num']+$ck['plant_num'];
           //更新仓库表总数
          $e=$db_store->where('uid='.$userid.'')->setField('fruit_total',$uph_fruittotal);

         //把他记入累计施肥而不是总收益（总收益是采蜜和施肥之和）
          $ljsf=$db_store->where('uid='.$userid.'')->setInc('huafei_total',$sjsf);
         //再去记入总收益  
          $sy=$db_store->where('uid='.$userid.'')->setInc('zongshouyi',$sjsf);
         //施肥玩之后去拿他的施累计施肥总量，看它到底能能否奖励水果农夫和藏獒
           $zongsy=$db_store->where('uid='.$userid.'')->getField('huafei_total');
           if ($zongsy>=50000) {
               $this->jiangli();
           }
          // 关于打印sql语句// echo $db_store->_sql();如果此表有多条语句，它打印成功的那一条，所以此打印不可全信 //还有关于执行更新语句跑到查询语句的是因为表的错误和字段的错误  
         
         //把施肥记录起来
          $dbsf=M('shifeijl');
          $datasf['u_id']=$userid;
          $datasf['farm_id']=$farmid;
          $datasf['shifei_num']=$sjsf;
          $datasf['tudi_type']=$type;  
          $datasf['shifei_time']=time();
          $sfjl=$dbsf->data($datasf)->add();   

          
          if ($c&&$jj&&$hh&&$xx&&$sfjl&&$sy&&$ljsf) {
               $db_store->commit();
               $data['content']=C('SHIFEI').'成功'; 
             $data['status']="sfcg";
             $this->ajaxReturn($data); 
             return;
          }else{
               $db_store->rollback();
               $data['content']=C('SHIFEI').'失败'; 
             $data['status']="sfsb";
             $this->ajaxReturn($data); 
             return;
          }
   }

   

    //===================收割开始=============================================================
    
      public function shouge(){

          $db_farm=M('nzusfarm');
          $db_store=M('store'); 
          $userid=session('userid');
         //===ajax_post过来的数据==========
          $farmid=I('post.farmid'); 
          $zdh_value=I('post.zdh_value');
          $type=I('post.farmtype');

          //农田里最低和最高保留的水果数  
         $mm=F('minmaxxx','','./Public/data/');
          switch ($type) {
            case '1':
              $min_guozi=$mm['huang_min'];
              $max_guozi=$mm['huang_max'];
              break;
            case '2':
              $min_guozi=$mm['hong_min'];
              $max_guozi=$mm['hong_max'];
              break;
            default:
              $min_guozi=$mm['hei_min'];
              $max_guozi=$mm['hei_max'];
              break;
          }
          $db_store->startTrans();
          // ====收割提交过来的条件=========
          $fwhere['u_id']=$userid;
          $fwhere['f_id']=$farmid;
    
          $sgInfo=$db_farm->where($fwhere)->find();//去田里拿对应田的数据
          
          //收割要保留地里最少有300个水果,即收割
          $shouge_num=$sgInfo['guozi_num']-$min_guozi; 
          $sgdata['guozi_num']=$min_guozi;
          $sgdata['show_tu']=1;
          //$sgdata['shifei_xiaoyi']=0;
          //$sgdata['shifei_status']=0;
          //$sgdata['shifei_time']=0;
          //$sgdata['caimi_idstr']="";
          $sgdata['caimi_status']=0;
          $sg=$db_farm->where($fwhere)->save($sgdata);
          //收割完之后去改仓库表的种植字段
          $b=$db_store->where('uid='.$userid.'')->setDec('plant_num',$shouge_num);
          //仓库表仓库字段增加收割的水果数量
          $c=$db_store->where('uid='.$userid.'')->setInc('cangku_num',$shouge_num);

         //========收割完之后再去改变总水果的数量==============
          $ck=$db_store->where('uid='.$userid.'')->find(); 
          $uph_fruittotal=$ck['cangku_num']+$ck['plant_num'];
          $e=$db_store->where('uid='.$userid.'')->setField('fruit_total',$uph_fruittotal);//更新仓库表总数


         
         //收割记录syd_shouge
          $dbsg=M('shouge');
          $datasg['u_id']=$userid;
          $datasg['farm_id']=$farmid;
          $datasg['shouge_num']=$shouge_num;
          $datasg['farm_type']=$type;  
          $datasg['shouge_time']=time();
          $sgjl=$dbsg->data($datasg)->add(); 

          if ($sg&&$c&&$b&&$sgjl) {
          	   $db_store->commit();
          	   $data['content']=C('SHOUGE').'成功'; 
	           $data['status']="sgcg";
	           $this->ajaxReturn($data); 
	           return;
          }else{
          	   $db_store->rollback();
          	   $data['content']=C('SHOUGE').'失败'; 
	           $data['status']="sgsb";
	           $this->ajaxReturn($data); 
	           return;
          }

   }

       
    
   //奖励藏獒和水果农夫
   public function jiangli(){
        // $db_store=M('store');
        // $userid=session('userid');
        // $usif=$db_store->where('uid='.$userid.'')->find();
        // // $zongsy=$usif['zongshouyi'];
        // $zongsy=$usif['huafei_total'];//累计施肥总量

        // switch (true) {
        //   case  $zongsy>=50000&&$zongsy<150000:
        //       if (!$usif['hashiqi_num']) {
        //         $db_store->where('uid='.$userid.'')->setField('hashiqi_num',1);
        //         //echo $db_store->_sql();
        //       }  
        //   break;
        //   case  $zongsy>=150000:
        //     if (!$usif['zangao_num']) {
        //        $db_store->where('uid='.$userid.'')->setField('zangao_num',1);
        //        // echo $db_store->_sql();
        //     } 
        //   break; 
        //   default:
        //     echo "<meta charset='utf-8'>思密达";
        //   break;     
        // }
   }







































	//收益    有两种   用户自己的    和别人偷的
	public function ajax_zzgain(){

		$userid=session('userid');
		$f_id=I('tu_id')+0;
		//$f_id=4;
		$usfdb=M('nzusfarm');
		$tudiinfo=$usfdb->where("u_id={$userid} and f_id={$f_id}")->find();
		
		if($tudiinfo['f_cycle'] <= $tudiinfo['wt_count'])
	    {

		//只要有被偷的情况下才会减少收益   被偷  只能得到  99 收益和  98 收益
		switch ($tudiinfo['theft']) {
			case 1:
				$tqattr=0.99;
				$data['content'] ='有人来偷东西.幸好有狗,被偷1%';
				break;
 			case 2:
				$tqattr=0.98;
				$data['content'] ='有人来光顾,被偷2%';
				break;
			default:
				$tqattr=1;
				$data['content'] ='';
		}



			//计算收益
		$sqldb=M();
		$tugoinfo=$sqldb->query("SELECT * FROM `syd_nzusfarm` as a INNER JOIN syd_nzgoods as b on a.s_id =  b.id  where  a.u_id ={$userid} and a.f_id={$f_id}");
		


		$mingxi ='收益lv'.$tugoinfo[0]['lv'].$tugoinfo[0]['name'];
		$wealthb = $tugoinfo[0]['wealthb']*$tqattr;
		$numberb = $tugoinfo[0]['numberb']*$tqattr;
		$integral = $tugoinfo[0]['integral']*$tqattr;



			//数字币   财富币   积分   暂还没做
			$udb=M('user');
			$udb->startTrans();
			
			$condition1 = $udb ->where("userid={$userid}")->setInc('wealthb',$wealthb); // 财富币
			$condition2 = $udb ->where("userid={$userid}")->setInc('numberb',$numberb); // 数字币
			$condition3 = $udb ->where("userid={$userid}")->setInc('integral',$integral); // 积分 
			
			//添加记录
			
			 $nbdb=M('nzbill');
			 //用户  id 
			 $dill['bill_userid']=$userid;
			 $dill['bill_wealth']=$wealthb;
			 $dill['bill_number']=$numberb;
			 $dill['bill_integral']=$integral;
			 $dill['bill_reason']='lv'.$tugoinfo['0']['lv'].$tugoinfo['0']['name'];
			 $dill['bill_time']=time();
			 $fcondition4 =$nbdb->add($dill);			 
			


			//$fdata['s_id'] =0;
			$fdata['s_sid'] =0;
			$fdata['f_time'] =0;
			$fdata['water'] =0;
			$fdata['z_grass'] =0;
			$fdata['wormy'] =0;
			$fdata['s_props'] =0;
			$fdata['f_cycle'] =0;
			$fdata['wt_count'] =0;
			$fdata['usf_kc'] =1;
			$fdata['theft'] =0; 
			$fcondition=$usfdb->where("u_id={$userid} and f_id={$f_id}")->save($fdata);


			    //3级分润


				function get_array($id=0,$num=0){
					$udb=M('user');

					$uinf=$udb->field('parent_id')->where(" userid = $id  and parent_id != 0 ")->select();
					
					$arr = array();
					if($num < 3 ){
						if($uinf){//如果有子类
							foreach ($uinf as $v ) { //循环记录集

								$arr[] = $v; //组合数组
								$arr=array_merge($arr,get_array($v['parent_id'],$num+1)); //调用函数，传入参数，继续查询下级
							}
						}
					}
						return $arr;
				}



		$getpidall = get_array($userid);



		foreach ($getpidall as $v) {
			$pidall[]=$v['parent_id'];
		}




		//计算上级
		$cnumpid=count($pidall);



		$sydb =M('nzbonus'); 

		$sy_time= time();


			if($cnumpid){
			switch($cnumpid){
				case 1 :		//如果父级只有一级  上级收益5%
										 //收益用户                收益时间           收益生产人            直推关系
					$dataList[0] = array('sy_zuid'=>$pidall[0],'sy_time'=>$sy_time,'sy_scuid'=>$userid,'sy_zlv'=>'1','sy_money'=>$tugoinfo[0]['price']*0.05,'sy_mingxi'=>$mingxi);
					$udb->where("userid = {$pidall[0]} ")->setInc('wealthb',$tugoinfo[0]['price']*0.05); // 用户的积分加1
					$fcondition5 = $sydb->addAll($dataList);
					break;
				case 2:
					//我的上级
					$dataList[0] = array('sy_zuid'=>$pidall[0],'sy_time'=>$sy_time,'sy_scuid'=>$userid,'sy_zlv'=>'1','sy_money'=>$tugoinfo[0]['price']*0.05,'sy_mingxi'=>$mingxi);
					$udb->where("userid = {$pidall[0]} ")->setInc('wealthb',$tugoinfo[0]['price']*0.05); // 用户的积分加1
					
					//我的上上级
					$dataList[1] = array('sy_zuid'=>$pidall[1],'sy_time'=>$sy_time,'sy_scuid'=>$userid,'sy_zlv'=>'2','sy_money'=>$tugoinfo[0]['price']*0.03,'sy_mingxi'=>$mingxi);

			    	$udb->where("userid = {$pidall[1]} ")->setInc('wealthb',$tugoinfo[0]['price']*0.03); // 用户的积分加1

					$fcondition5 = $sydb->addAll($dataList);
					break;

				case 3:
					//我的上级
					$dataList[0] = array('sy_zuid'=>$pidall[0],'sy_time'=>$sy_time,'sy_scuid'=>$userid,'sy_zlv'=>'1','sy_money'=>$tugoinfo[0]['price']*0.05,'sy_mingxi'=>$mingxi);
					$udb->where("userid = {$pidall[0]} ")->setInc('wealthb',$tugoinfo[0]['price']*0.05); // 用户的积分加1


					//我的上上级
					$dataList[1] = array('sy_zuid'=>$pidall[1],'sy_time'=>$sy_time,'sy_scuid'=>$userid,'sy_zlv'=>'2','sy_money'=>$tugoinfo[0]['price']*0.03,'sy_mingxi'=>$mingxi);
					$udb->where("userid = {$pidall[1]} ")->setInc('wealthb',$tugoinfo[0]['price']*0.03); // 用户的积分加1

					//我的上上上级
					$dataList[2] = array('sy_zuid'=>$pidall[2],'sy_time'=>$sy_time,'sy_scuid'=>$userid,'sy_zlv'=>'3','sy_money'=>$tugoinfo[0]['price']*0.01,'sy_mingxi'=>$mingxi);
					$udb->where("userid = {$pidall[2]} ")->setInc('wealthb',$tugoinfo[0]['price']*0.01); // 用户的积分加1

					$fcondition5 = $sydb->addAll($dataList);
					break;
			}

			}else{
				$fcondition5= 1;
			}





			if($condition1 && $condition2 && $condition3 && $fcondition && $fcondition4 && $fcondition5 ){
				$udb->commit();
				$data['status']  ='zzsy';
				$data['content'] .='收益财富币+'.$wealthb.'数字币+'.$numberb.'积分+'.$integral;
				$this->ajaxReturn($data);


			}else{
				$udb->rollback();
			}
			
			
		}else{
			$tianday=$tudiinfo['f_cycle']-$tudiinfo['wt_count'];

			$redata['status'] = 'mycs';
			$redata['content'] = '还有'.$tianday.'天';
			$this->ajaxReturn($redata);

		}
		
	}
	
























	//施肥   施肥一小时内.长虫
	public function ajax_huafei(){
	
		

		$hf_lv=I('hf_lv/d');   //化肥  lv  这个要
 		$hf_count=I('hf_count/d'); //化肥次数  不个是无用的
		$hf_name=I('hf_name');   //  化肥的名了?
		$hf_id=I('hf_id/d');	//化肥  id 
		$tu_id=I('tu_id/d');    //土地ID
		$zzid=I('zzid/d');      //用用
		$zzspid=I('zzspid/d');   //用户实际  土地里种的种子
		
		$userid=session('userid');

		$newdb=M();

		//购买表  和   道具表
		$bbhfinfo=$newdb->query("SELECT a.id ,b.id as pid ,b.name ,a.s_count,b.moved_up,b.prop_lv,b.imgpath FROM `syd_nzshoping` as a LEFT JOIN syd_nzprop as b on a.s_id = b.id where a.s_type=2 and b.p_type=1 and a.s_count !=0 and a.u_id={$userid} and a.id={$hf_id}");
		//echo $newdb->_sql();
		
		
		//化肥 的资料
		//p($bbhfinfo);
		if(!$bbhfinfo ){
			echo '背包化肥为空';
			return false;
		}
		
		
		//种子表...各农田表	(这个查的是田里实际是什么种子)				提交的种子,和实际的种子必需相同
		 $tdgoods = $newdb->query("SELECT f.id,f.f_id,f.f_time,f.s_id,f.z_grass,f.s_sid,f.wormy,f.s_props,g.name,g.lv from syd_nzusfarm as f  INNER JOIN syd_nzgoods as g on f.s_id=g.id where f.u_id={$userid} and f_id={$tu_id}");
		//p($tdgoods);
		//die;
		
		if($tdgoods[0]['wormy'] > 0  || $tdgoods[0]['wormy'] >0  ){
			$data['state'] = 'ycz';
			$this->ajaxReturn($data);
		}  
		

		  //背包里有化肥    提交的种子,和实际的种子必需相同
		if($bbhfinfo && $tdgoods[0]['s_id'] == $zzid && $bbhfinfo[0]['s_count'] == $hf_count  && $bbhfinfo[0]['name'] == $hf_name && $bbhfinfo[0]['s_sid'] == $zzspi){

			switch ($tdgoods[0]['lv']) {
				case 1:
					$use_hfgs=1;
					break;
				case 4:
					$use_hfgs=2;
					break;
				case 8:
					$use_hfgs=3;
					break;
				default:
					return;
   				 	break;
			}

										//  黄土地  使用
			if( $use_hfgs != $bbhfinfo[0]['prop_lv'] ){
				$data['state'] = '2';
				$this->ajaxReturn($data);
			}
			// 用户  购买表里道具  -1    农作 + 天数		长虫的时间   和生成下次施肥的时间  // 一天一次    
			
			//使用肥料的时间
		
		   //肥料的时间必须小于..当前的时间..才能施肥
		  $shifei=$tdgoods[0]['s_props']?$tdgoods[0]['s_props']:$tdgoods[0]['f_time'];
		
		
		//施肥成功后...  时间,改成明天的时间
			if( $shifei < time()){
				$usfdb=M('nzusfarm');
				$usfdb->startTrans();
				
				//明天可施肥时间
				$usfdb->s_props=$farm_water = strtotime( date('Y-m-d',strtotime("+360 days"))) + rand(1,7200); 
				//长虫时间
				$usfdb->wormy=time()+rand(1,3600); 
				$condition1=$usfdb->where("u_id={$userid} and f_id ={$tu_id}")->save();	

				// 提前天数
				$condition2= $usfdb->where("u_id={$userid} and f_id ={$tu_id}")->setInc('wt_count',$bbhfinfo[0]['moved_up']); 

				
				
				//道具 使用减一次
				$usspdb=M('nzshoping');
				$usfsp=$usspdb->where("id={$hf_id} and u_id ={$userid} ")->setDec('s_count');
				
				if($condition1 && $condition2 && $usfsp ){
					$usfdb->commit();
					$data['state'] = '3';
					$this->ajaxReturn($data);
				}else{
					$usfdb->rollback();
					$data['state'] = '4';
					$this->ajaxReturn($data);
				}
				
				

			}else{
				$data['state'] = '1';
				$this->ajaxReturn($data);
				
			}

			
		}else{
			$data['state'] = '0';
				$this->ajaxReturn($data);
		}	
	}
	










	//除虫后..时间改为零
	public function ajax_chucong(){
		//p(I());
		$userid=session('userid');
		$f_id=I('tu_id/d');
		//$f_id=4;
		$usfdb=M('nzusfarm');
		$tudiinfo=$usfdb->where("u_id={$userid} and f_id={$f_id}")->find();
		//  长草的时间 -现在的时间  是不是小于0   小于.才是真有草
		 if($tudiinfo['wormy'] <time() && $tudiinfo['wormy'] >= 10000 ){
			//echo '需要除虫';
			//die;
			$udb=M('user');
			$udb->startTrans();
			//+经验
			$usexp=$udb->where("userid=$userid")->setInc('exp',5);
			// 土地
			$usfdb->wormy='0';  //除虫后,字段变成0
			$usfcc=$usfdb->where("u_id={$userid} and f_id ={$f_id}")->save();

				$exdb=M('nzexperience');
				$expdata['exp_userid']=$userid;
				$expdata['exp_genre']='除虫';
				$expdata['exp_price']='5';
				$expdata['exp_time']=time();
				$usupexp=$exdb->data($expdata)->add();


			if($usexp && $usfcc && $usupexp){
				$udb->commit();
				exit(json_encode("除虫成功,用户经验+5"));
			 }else{
				$udb->rollback();
				exit(json_encode("您对虫子造成的伤害-0"));
			 }
			die;
		 }
		 exit(json_encode("并没有虫子"));
	}
	//养鱼
	public function ajax_yangyu(){
		/**
		    判断   用户的  是否有种植权限   
		*/

		$userroot=session('userroot');

		if($userroot['zhitui'] <10){
			$data['status']  ='ztbz0';
			$data['content'] = "您直推人数为{$userroot['zhitui']},需要真推10人才能养鱼";
			$this->ajaxReturn($data);
		}

	 	$userid=session('userid');
		$fsdata['u_id']=$userid;
		$fsdata['shop_id']=I('fsh_spid')+0;  //商店的id
		$fsdata['fish_id']=I('fsh_gid')+0;	 //鱼的id号.
		$fsdata['f_time']=time();

		$usdb=M('user');
		//判断用户是不是  farm_lock是不是锁定
		$farm_lock = $usdb->where("userid={$userid}")->getfield('farm_lock');
		$farm_lock?$this->ajaxReturn($data=array('status'=>'myqx')):$farm_lock;
		
		
		$shopdb=M('nzshoping');
		$usfish=$shopdb->where("s_id={$fsdata['fish_id']} and id={$fsdata['shop_id']} and u_id={$userid} and s_count != 0 ")->find();	

		//有没有鱼
		if(!$usfish ){
			$this->ajaxReturn($data=array('status'=>'myym'));
		}
		
		//得到   单个物品的  属性
		$goodsdb=M('nzgoods');
		$goodsinfo=$goodsdb->where("id={$fsdata['fish_id']}")->find();

		$fsdata['fish_cycle']=$goodsinfo['cycle']; //收获周期
		$fsdata['fish_lv']=$goodsinfo['lv']; //收获周期



			//如果  lv3 = 0  用户不能养这个鱼
		if(!$userroot['fishroot']["lv{$goodsinfo['lv']}"]){
				$data['status']  ='ymlv';
				$data['content'] ='暂时不能养lv'.$goodsinfo['lv'].'鱼,多去推广后再来试试把~~';
				$this->ajaxReturn($data);
			
			return;
		}
		
		
		//开启事物   物品表 次数  -1    池塘  表  数据+1
		$shopdb->startTrans();
	
		//  用户购买表  商品次数 - 1
		$usshop =$shopdb->where("s_id={$fsdata['fish_id']} and id={$fsdata['shop_id']} and u_id={$userid}")->setDec('s_count'); 

		$uspdb=M('nzuspond');  //池塘数据
		
	
			//用户  能种的鱼   如果一 就是能养一条  二是能养两条
			$countfish = $userroot['fishroot']["lv{$goodsinfo['lv']}"];
		  
		  
		  switch( $countfish){
				case 1:
					$us_cyt=$uspdb->where("u_id={$userid} and fish_lv = {$goodsinfo['lv']}")->count();
					switch($us_cyt){
						case 0:  //为空  意思就是说可以随变插入
						$us_cyt2=$uspdb->data($fsdata)->add();
						break;
						default;
						//所有的
						$us_cytall=$uspdb->where("u_id={$userid} and fish_lv = {$goodsinfo['lv']}")->count();
						// 有多少格子
						$us_cytfall=$uspdb->where("u_id={$userid} and fish_lv = {$goodsinfo['lv']} and fish_id = 0 ")->count();
					
						if($us_cytall - $us_cytfall == 0 ){
								$us_cytf =$uspdb->where("u_id={$userid} and fish_lv = {$goodsinfo['lv']} and fish_id = 0 ")->order('f_time asc')->find();
								
								if($us_cytf){
								$us_cyt2 =$uspdb->where("id={$us_cytf['id']}")->save($fsdata);
								}
						}else{
							$data['status']  ='yjyy';
							$data['content'] = '你鱼塘已经养了一条lv'.$goodsinfo['lv'].'鱼了';
							$this->ajaxReturn($data);
				
							//echo '你鱼塘lv'.$goodsinfo['lv'].'已经养了一条鱼了';
						}
						break;
					}
				break;
				case 2:
					$us_cyt=$uspdb->where("u_id={$userid} and fish_lv = {$goodsinfo['lv']}")->count();
					switch($us_cyt){
					case 0:  //为空  意思就是说可以随变插入
					$us_cyt2=$uspdb->data($fsdata)->add();
						break;
					case 1:  //  已经养了一条了    也可以变插入
					$us_cyt2=$uspdb->data($fsdata)->add();
						break;
					case 2:   //判断  usid  and   fish_id  != 0   and  lv =X    有没有?  有修改  ..没有返回
					
					$us_cytf =$uspdb->where("u_id={$userid} and fish_lv = {$goodsinfo['lv']} and fish_id = 0 ")->order('f_time asc')->find();

						if($us_cytf){
							
							//echo '还可以养鱼';
							$us_cyt2=$uspdb->where("id={$us_cytf['id']}")->save($fsdata);
						}else{
							$data['status']  ='yjyy';
							$data['content'] = '你鱼塘已经养了两条lv'.$goodsinfo['lv'].'鱼了';
							$this->ajaxReturn($data);
				
						}
						
						//p($us_cyt2);
						break;
				}
				break;
		  }



		 
			//   ------记录----
		if($usshop && $us_cyt2 ){

			$shopdb->commit();
			$data['status']  ='xrcg';
			$this->ajaxReturn($data);
		}else{
			//echo '数据回滚';
			$shopdb->rollback();
			$data['status']  ='sjhg';
			$this->ajaxReturn($data);
		}
	}
	//鱼苗收益
	public function ajax_shouyiym(){

		$userid=session('userid');
		//鱼塘id    池塘 鱼的ID/goods表的id     goods表的ID   
		//$ctf[pid],    $ctf[fish_id],         $ctf[shop_id]
		$fis_val=I('fis_val');
		//p($fis_val);
		//die;
		$exp_val=explode(',',$fis_val);
		
		$attrname=array('pid','fishid','shopid');
		
		$pondattr=array_combine($attrname,$exp_val);
		//  先查用户池塘表里是不是有鱼     &&  用户是不是有买这个商品
		$pdb = M('nzuspond');
		$pfish=$pdb->where("u_id=$userid and fish_id = $pondattr[fishid] and  id= $pondattr[pid] and shop_id = $pondattr[shopid]")->find();




		//只要有被偷的情况下才会减少收益   被偷  只能得到  99 收益和  98 收益
		switch ($pfish['thief']) {
			case 1:
				$tqattr=0.99;
				$data['content'] ='有人来偷东西.幸好有狗,被偷1%';
				break;
 			case 2:
				$tqattr=0.98;
				$data['content'] ='有人来光顾,被偷2%';
				break;
			default:
				$tqattr=1;
				$data['content'] ='';
		}
		

		$ndb=M();
		$usgoods = $ndb->query("select s.id as sid ,g.id as gid ,g.name,g.cycle,g.wealthb,g.numberb,g.integral from syd_nzshoping as s  LEFT JOIN syd_nzgoods as g  on s_id =g.id where u_id ={$userid} and s_type=1 and s.id={$pondattr[shopid]} and g.id ={$pondattr[fishid]} and s.s_count =0");
		//echo $ndb->_sql();

/*echo $wealthb = $usgoods[0]['wealthb']*$tqattr;
echo '<br>';
echo $numberb = $usgoods[0]['numberb']*$tqattr;
echo '<br>';
echo $integral = $usgoods[0]['integral']*$tqattr;
echo '<br>';*/

//		die;


$wealthb = $usgoods[0]['wealthb']*$tqattr;
$numberb = $usgoods[0]['numberb']*$tqattr;
$integral = $usgoods[0]['integral']*$tqattr;




		//鱼塘里有 && 用户有这个商品
		if($pfish && $usgoods){
		
			//判断 收益时间到没有  用fish  fish_cycle 
			 if ( time() > $pfish['f_time']+(86400*$pfish['fish_cycle'])){
				 //用户表          加收益      池塘   变0
				 
				/* echo 'ccee';
				 p($usgoods);
				 die;*/
				 $usdb=M('user');
				 $usdb->startTrans();
				 //收益
				 $condition1=$usdb->where("userid={$userid}")->setInc('wealthb',$wealthb); // 财富币
				 $condition2=$usdb->where("userid={$userid}")->setInc('numberb',$numberb); // 数据字
				 $condition3=$usdb->where("userid={$userid}")->setInc('integral',$integral); // 积分
				 
				//修改池塘表
				 $savect['fish_id']='0';
				 $savect['shop_id']='0';
				 $savect['f_time']='0';
				 $savect['thief']='0';
				 $condition4 = $pdb->where("u_id=$userid and  id= $pondattr[pid] and shop_id = $pondattr[shopid]")->save($savect);
				 //echo $pdb->_sql();
				 
		

			 $nbdb=M('nzbill');
			 //用户  id 
			 $dill['bill_userid']=$userid;
			 $dill['bill_wealth']=$wealthb;
			 $dill['bill_number']=$numberb;
			 $dill['bill_integral']=$integral;
			 $dill['bill_reason']='lv'.$tugoinfo['0']['lv'].$tugoinfo['0']['name'];
			 $dill['bill_time']=time();
			
			  $fcondition =$nbdb->add($dill);
/*			  echo $nbdb->_sql();
			  die;
*/



				 if($condition1 && $condition2 && $condition3 && $condition4 && $fcondition){
					 $usdb->commit();
					 //echo 'sycg';
					 $data['status']  ='sycg';
					 $data['content'] .="财富币+\"$wealthb\"数字币+\"$numberb\"积分+\"$integral\"";




					 //$data['content']  ="你好$usgoods[0][wealthb]$usgoods[0][numberb]$usgoods[0][integral]";
					 $this->ajaxReturn($data);
				 }else{
				 	$usdb->rollback();
					$data['status']  ='syerror';
					$this->ajaxReturn($data);
				 }
				 
				 
				
			 }else{
					$data['status']  ='bkysy';
					$this->ajaxReturn($data);
			 }
			

		}else{
			 $data['status']  ='myym';
			 $this->ajaxReturn($data);
		}
		
	}
	//使用鱼饵
	public function ajax_shiyyuer(){
		//p(I());   
		$userid=session('userid');
		    $yuer_spid =I('yuer_spid')+0;  //  shop表 id
		    $yuer_lv =I('yuer_lv')+0;     //鱼饵等级
		    $fish_lv =I('fish_lv')+0;	  //鱼的等级
		    $poid =I('poid')+0;		  //池塘的ID
		    $fiid =I('fiid')+0;		  //池塘鱼的ID
		    $yuer_ppid =I('yuer_ppid')+0;		  //道具表   商品的ID号

		    // 鱼饵的ID  用  shop ID和  鱼饵  g_type =2 查  用户是否有这个商品   次数!=0    背包是否有这个东西
		    $shdb=M('nzshoping');
		    $usshop= $shdb->where("u_id = {$userid} and id={$yuer_spid} and s_id ={$yuer_ppid} and s_type=2 and  s_count != 0  ")->find();
	  	  //p($usshop);

		    //养鱼的ID   池塘编号 + 用户ID  +鱼的ID查是 用户是否有这个东西				池塘是否有这个东西
    	    $fpdb=M('nzuspond');                                    						// fish_cycle 周期是否大于零
		    $pfs=$fpdb->where("u_id = {$userid} and id={$poid}  and fish_id ={$fiid} and fish_lv ={$fish_lv} and fish_cycle > 0 ")->find();
		    //p($pfs);
	    	
	    	//道具的 属性是不是正确
	    	$djdb=M('nzprop');
	    	$djgoods=$djdb->where("id={$yuer_ppid} and prop_lv={$yuer_lv}")->find();

		   // p($djgoods);
		      //池塘鱼 && 背包有   &&道具
		    if($pfs && $usshop && $djgoods){

		    	//鱼的成长时间    -  道具的时间    一天只能吃一次  生成下次可吃的时间
		    	$baittime=$pfs['baittime']?$pfs['baittime']:$pfs['f_time'];
		    	//echo   $baittime;
		    	if($baittime < time() ){
		    		//  池塘   与    购物表  关联
		    		$shdb->startTrans();  //SHOP表

		    		//修改下次  鱼饵的时间
		    		$yertime['baittime']= strtotime( date('Y-m-d',strtotime("+365 days"))) + rand(1,7200);
		    		$condition0 = $fpdb->where("u_id = {$userid} and id={$poid}  and fish_id ={$fiid} and fish_lv ={$fish_lv}")->save($yertime);

  		    		
  		    		//商品表  次数 -1  
					$condition1 = $shdb->where("u_id = {$userid} and id={$yuer_spid} and s_id ={$yuer_ppid} and s_type=2")->setDec('s_count'); 	    		
  		    		//提前天数-   商品的天数
					$condition2 = $fpdb->where("u_id = {$userid} and id={$poid}  and fish_id ={$fiid} and fish_lv ={$fish_lv}")->setDec('fish_cycle',$djgoods['moved_up']);

					if($condition1 && $condition2 && $condition0 ){
						$shdb->commit();
						$data['status']  ='yesucc';
		    			$data['content'] ="鱼儿吃鱼饵,成长天数-{$djgoods['moved_up']}";
		    			$this->ajaxReturn($data);
						//echo "鱼儿吃鱼饵,成长天数-{$djgoods['moved_up']}";
					}else{
						$shdb->rollback();
						$data['status']  ='yeerr';
		    			$this->ajaxReturn($data);
						//echo '失败';
					}
		    	}else{
		    		$data['status']  ='yjwy';
		    		$this->ajaxReturn($data);
		    		//echo '您今天已经喂过鱼了';
		    	}
		    }else{
			    	$data['status']  ='clkq';
		    		$this->ajaxReturn($data);
				    	echo '出错了';
		    }
	}

	//超级鱼苗,和超级种子的购买
	public function ajax_supergoods(){
		p(I());

	}

	//雇佣购买
	//超级鱼苗,和超级种子的购买
	public function ajax_toolgoods(){
		//p(I());
		//得到   道具id  道具名字   如果工具ID是2(铁)   只能买一次    如果是机器人..判断是否到期..到期才能买 
	$userid=session('userid');
    $g_name = I('g_name');
    $g_tid = I('g_tid')+0;
    $g_tool = I('g_tool')+0;
    $gwdb=M('nzshoping');  //购物表 
    $djdb=M('nzprop');
    $usdb=M('user');
    $usdb->startTrans();
    $userinfo = $usdb->where("userid={$userid}")->find();
     //p($userinfo);
    		switch ($g_tool){
    			case 2:
    			 $usdj=$gwdb->where("s_id={$g_tid} and u_id={$userid} and s_type=2")->count();
    			 if($usdj){
    			 	// echo "$g_name,你已经买了一把了";
    			 	$ajxdata['status']  ='yjgm';
    			 	$ajxdata['content'] = "$g_name,你已经买了一把了";
    			 	$this->ajaxReturn($ajxdata);

    			 	return false;
    			 }else{
    			 	// 购买道具  铲子..没有属性    只要扣  user 的钱
    			 	$gmdj=$djdb->where("id={$g_tid}")->find();

    			 	//echo $gmdj['price'];
    			 	
    			 	 if($userinfo['wealthb'] > $gmdj['price']){
    			 	$condition1= $usdb->where("userid={$userid}")->setDec('wealthb',$gmdj['price']);	
    			 	 }
    			 	//开启事务  -->  用户表 扣钱   购物表  写数据  成功写入  否则返回   (外加写入记录表)
					$data['u_id']=$userid;  //用户ID;
					$data['s_type']=2;   //道具类型
					$data['s_id']=$gmdj['id']; //道具ID
					$data['s_count']=1; //铲子好像没有次数
					$data['s_day']=''; //使用天数
					$data['s_time']=time();  //购买时间
					$condition2=$gwdb->add($data);

					//修改用户表 是否拥有铁铲
					$savedata[$g_tid._shovel]=1;
					$userinfo = $usdb->where("userid={$userid}")->save($savedata);


    			 }
    			break;
    			case 4:
    			//先查..有没有这个     
    			 $usdj=$gwdb->where("s_id={$g_tid} and u_id={$userid} and s_count != 0 ")->order('s_time desc')->find();
    			

    			 //道具表里面的    商品
    			$gmdj=$djdb->where("id={$g_tid} and p_type=4")->find();



    			//  现在的时间是不是    小于   这个时间.才能购买 
    			 $old_time = $usdj['s_time']+86400*$gmdj['c_day'];

    			if(time() < $old_time ){
    				//echo '您的'.$gmdj['name'].date('Y-m-d H:i',$old_time).'过期,暂时不需要购买';
    				$ajxdata['status']  ='zbxy';
    			 	$ajxdata['content'] = '您的'.$gmdj['name'].date('Y-m-d H:i',$old_time).'过期,暂不需要购买';
    			 	$this->ajaxReturn($ajxdata);
    			 	
    				return false;
    			}
    			 //echo $userinfo['wealthb'];
    			 //钱够用的情况下
    			 if($userinfo['wealthb'] > $gmdj['price']){
					$condition1= $usdb->where("userid={$userid}")->setDec('wealthb',$gmdj['price']); 
					$data['u_id']=$userid;  //用户ID;
					$data['s_type']=2;   //道具类型
					$data['s_id']=$gmdj['id']; //道具ID
					$data['s_count']=1; //铲子好像没有次数
					$data['s_day']=''; //使用天数
					$data['s_time']=time();  //购买时间
					$condition2=$gwdb->add($data);
			 	 }
    			break;
    		}


    		//写入数据
    		$nbdb=M('nzbill');
			$dill['bill_userid']=$userid;
			$dill['bill_wealth']=-$gmdj['price'];
			$dill['bill_reason']="购买".$gmdj['name'];
			$dill['bill_time']=time();
			$fcondition4 =$nbdb->add($dill);

		if($condition1 && $condition2 ){
					$usdb->commit();
    			 	$ajxdata['status']  ='gmcg';
    			 	$this->ajaxReturn($ajxdata);
			//echo '购买成功';
		}else{
			$usdb->rollback();
    			 	$ajxdata['status']  ='cfbz';
    			 	$this->ajaxReturn($ajxdata);
			// echo '财富币不足';
		}
	}
	
	//铲土地
	public function ajax_chancao(){
		//P(I());
			$tu_id=I('post.tu_id')+0;
			$userid=session('userid');
			//$tu_id='5';
			$fdb=M('nzusfarm');

			$finfo=$fdb->where("u_id= $userid and f_id = $tu_id ")->getField('usf_kc');
			//p($finfo);
			
			//判断用户是否有铲子
			$chanattr=array('5','6','7','8','9','10','11','12');
            
            if(in_array($tu_id,$chanattr)){
            	$spdb = M('nzshoping');
            	switch ($tu_id){
            		case  $tu_id <= '8' :
            		//判断用户是否有银铲子
            		$czwhere['s_id']='4';
            		$czsay="没有购买银铲子"; 
            	
            		break;
            		case  $tu_id <= '12':
            		//判断用户是否有金铲子
            		$czwhere['s_id']='15';
            		$czsay="没有购买金铲子"; 
        		 	break; 
            	}
            	$czwhere['s_type'] = '2'; //类型道具
            	$czwhere['u_id'] = $userid; //类型道具

            	$uscz = $spdb->where($czwhere)->find();
            	if(!$uscz){
            	$data['status']  = 'mycz';
				$data['content'] = $czsay;
				$this->ajaxReturn($data);
            	}
            }




			if(!$finfo){
				//echo '成熟收获后才能翻土哦';
				$data['status']  = 'mcs';
				$data['content'] = '成熟收获后才能翻土哦';
				$this->ajaxReturn($data);
				return;
			}

			//修改  农田表    得到经验
			$fdb->startTrans();
			$setfdata = array('s_id'=>'0','usf_kc'=>'0');
			$condition1=$fdb->where("u_id= $userid and f_id = $tu_id ")->setField($setfdata);

			//用户经验 + 
			$udb=M('user');
			$condition2=$udb->where("userid = $userid")->setInc('exp',5); // 用户的积分加3

			//经验
			$edb=M('nzexperience');
			$edata['exp_userid']=$userid;
			$edata['exp_genre']='翻土';
			$edata['exp_price']='5';
			$edata['exp_time']=time();
			$condition3=$edb->add($edata);

			if($condition1 && $condition2 && $condition3){
				//echo '经验+5';
				$fdb->commit();
				$data['status']  = 'ftcg';
				$data['content'] = '翻土 经验+5';
				$this->ajaxReturn($data);

			}else{
				//echo '不需要';
				$fdb->rollback();
				$data['status']  = 'ftsb';
				$data['content'] = '不需要翻土';
				$this->ajaxReturn($data);

			}
	}
	//购买超级种子
	public function ajax_superbuy(){
		//p(I());
		
		$gdb=M('nzgoods');
		$userroot=session('userroot');  //  这里有直推
		$userfriend = count(session('gx_array'));  //这里有一共多少人

  		$userid=session('userid');
		$where['id']=I('gid')+0;
		$where['g_type']=array('EQ',2);  //  1种子 2特殊种子 3鱼  4特殊鱼',
		$where['on_goods']=array('EQ',1);  // 是否上架
		$ginfo = $gdb->where($where)->find();



		$udb = M('user');
		$exp = $udb->where("userid ={$userid} ")->getField('exp'); // 减
		//经验
		if($exp < $ginfo['price']){
			$reduce =  $ginfo['price'] - $exp;
			$data['content'] .= '购买条件不足,经验差'.$reduce."\n";		
		}
		//团推
		if(($tuantui = $userfriend) < $ginfo['team'] ){
			$reduce1 =  $ginfo['team'] - $tuantui;
			$data['content'] .= '购买条件不足,团队人数还差'.$reduce1."\n";
		}
		//直推
		if($ginfo['direct'] > $userroot['zhitui']){
			$reduce2 =  $ginfo['team'] - $userroot['zhitui'];
			$data['content'] .= '购买条件不足,直推人数还差'.$reduce2."\n";
		}

		if($data['content']){
			$data['status']  ='tjbz';
			$this->ajaxReturn($data);
		}


		$udb->startTrans();
		$condition1 = $udb->where("userid ={$userid} ")->setDec('exp',$ginfo['price']); // 减

		$spdata['u_id']=$userid;  //用户ID;
		$spdata['s_type']='1';   //道具类型
		$spdata['s_id']=$where['id']; //道具ID
		$spdata['s_count']=1; //种子只能用一次!!!
		$spdata['s_day']=$ginfo['cycle']; //使用天数
		$spdata['s_time']=time();  //购买时间
		$spdb=M('nzshoping');
		$shop=$spdb->add($spdata);
		//echo $spdb->_sql();

		// 减经验记录
		$exdb = M('nzexperience');
		$exdata['exp_userid'] = $userid;
		$exdata['exp_price'] = -$ginfo['price'];
		$exdata['exp_genre'] = '购买'.$ginfo['name'];
		$exdata['exp_time'] = time();

		$condition3 = $exdb->add($exdata);
		


		if($condition1 && $shop && $condition3 ){
			$udb->commit();
			$data['status']  ='gmcg';
			$this->ajaxReturn($data);
		}else{
			$udb->rollback();
			$data['status']  ='gmsb';
			$this->ajaxReturn($data);
		}


	}
	//金种子
		public function ajax_jinseed(){
	 /*
    [farmid] => 13
    [jinseedid] => 9
    [jinshopid] => 194 */

		$zztu_id =I('farmid')+0;   //1-15的进土地  id号  要查这块地是不是空
		$s_szzid=I('jinshopid')+0;    //shop 表的id号       ????????  查出种子的LV  是不是  等于  土地的LV  相等才能种
		$s_zzid=I('jinseedid')+0;    //种和种子id

		

		$userid=session('userid');

		$udb=M('user');
		$uflock=$udb->field('farm_lock')->where("userid=$userid")->find();
		


		//p($uflock);      土地是否激活   有租用期
		if($uflock['farm_lock']){
			$data['state'] = 'myzzqx';
			//$data['state'] = '用户没有种植权限';
			//$data = '用户没有种植权限';
			$this->ajaxReturn($data);
			return;
		}


		$farmid=$zztu_id; //土地id号
		//判断是否
		$farmarr=array('13','14','15');
		//不存在不执行	
	if(!in_array($farmid , $farmarr )){
		return;
	}


		$shopdb=M();
		//查出用户   是 不是  有当前的种子     次数不能为零
		$usgoods=$shopdb->query("SELECT * FROM `syd_nzshoping` LEFT JOIN syd_nzgoods  on  syd_nzshoping.s_id = syd_nzgoods.id where syd_nzshoping.s_type = 1 and syd_nzshoping.id={$s_szzid} and syd_nzshoping.u_id={$userid} and syd_nzshoping.s_count != 0  and  syd_nzgoods.farmland_rt = 4 and syd_nzgoods.g_type = 2");
		//echo $shopdb->_sql();
		
		if(!$usgoods){
			return;
		}


		//用户的这块土地有没有种?
		 $ufdb=M('nzusfarm');

		 //判断地址是否有种子.....土地上是不是长枯草
		 $usfarm=$ufdb->where("u_id={$userid} and f_id ={$zztu_id}")->field('s_id,usf_kc')->find();

		 if($usfarm['usf_kc']){
		 	//有枯草
		 	$data['state'] = 'kc';
			$this->ajaxReturn($data);
			return;
		 }
        
         //判断金土地是不是种同一种植物
         $where5['f_id']=array('in',"13,14,15");
         $where5['u_id']=$userid;
         $where5['s_id']=$s_zzid;
         $jin_cf=$ufdb->where($where5)->find();
        // echo $ufdb->getLastSql();
         if ($jin_cf) {
         	 $data['state'] = 'cf';
			 $this->ajaxReturn($data);
			 return; 
         }


		//有这个东西 并且土地为空    
		if( $usfarm['s_id'] == false && $usgoods['0']){
				//echo '正常';
			
            


			// 改syd_nzshoping  身上的道具 次数 -1     usfarm 土地写入 id 时间
			$shdb=M('nzshoping');
			$shdb->startTrans();
			//$sash['s_count'] =1;  使用次数减一
			$syzz=$shdb->where("u_id={$userid} and id={$s_szzid}")->setDec('s_count');



			//农田 写入参数
			$usfdb=M('nzusfarm');
			$usfdata['s_id']=$s_zzid;   //种子ID
			$usfdata['s_sid']=$s_szzid; //shop 种子id 
			$usfdata['f_time']=time();  //种下的时间
			$usfdata['f_cycle']=$usgoods['0']['cycle'];   //收益周期
			$sytd=$usfdb->where("f_id={$zztu_id} and  u_id ={$userid}")->save($usfdata);




			if( $syzz &&  $sytd ){	
				$shdb->commit();			
				$data['state'] = 'zzcg';
				$data['content'] = $usgoods['0']['name'].'种植成功';
				$data['img']=$usgoods['0']['imgpath1'];
				$this->ajaxReturn($data);
			return;
			}else{
				$shdb->rollback();
				$data['state'] = 'zzsb';
				$this->ajaxReturn($data);
			return;
			}
			
			
		}
	}
}