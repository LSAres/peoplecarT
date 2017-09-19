<?php
namespace Admin\Controller;
use Think\Controller;
class PropController extends CommonController {
    public function index(){
		$this->display();
   }
	//道具管理
    public function propgl(){
		$dgdb=M('nzprop');
		$p_type=I('get.p_type','','intval')?I('get.p_type','','intval'):1;
		$where['p_type']=$p_type;
		$dginfo=$dgdb->where($where)->select();
		$this->p_type=$p_type;
		$this->dginfo=$dginfo;
		$this->display();
   }
   //道具添加
   public function addprop(){
	$this->display();
    }
	//插入数据   
	public function rnaddprop(){
		$data=I();
		unset($data['id']);
		$data['time']=time();
		//如果存在图片上传
		if($_FILES['photo']['name'][0] != ''){
		   $imgup=$this->_imgupload('prop');
		   $info=$imgup->upload();
		 if(!$info){
			$this->error($imgup->getError());
		}else{
				foreach($info as $file){
					$data['imgpath']= $file['savepath'].$file['savename'];   
					}
			}
		}
		$pdb=M('nzprop');
		if($rtinfo=$pdb->add($data)){
			echo '<script>alert("添加成功")</script>';
			echo '<script>javascript:history.back(-1);</script>';
		}else{
			echo '<script>alert("添加失败,请重试")</script>';
			echo '<script>javascript:history.back(-1);</script>';
		}
		
    }
	//修改数据页面
	public function saprop(){
		$pwhere['id']=I('get.p_id')+0;
		$pinfo=M('nzprop')->where($pwhere)->find();
		//p($pinfo);
		$this->pinfo=$pinfo;
		$this->display('addprop');
	}
	//修改属性
	public function saveprop(){
		$data=I();
		$pdb=M('nzprop');
		//判断是否有图片上传
		if($_FILES['photo']['name'][0]){
		$img_p=$pdb->field('imgpath')->where("id=".$data['p_id'])->find();
		$delpath= './public'.$img_p['imgpath'];
		unlink($delpath);
		//图片上传
		$imgup=$this->_imgupload('prop');
		   $info=$imgup->upload();
		 if(!$info){
			$this->error($imgup->getError());
		}else{
			foreach($info as $file){
				$data['imgpath']= $file['savepath'].$file['savename'];   
				}
			}
		}
		if($pdb->where("id=".$data['p_id'])->save($data)){
			echo '<script>alert("修改成功")</script>';
			echo '<script>javascript:history.back(-1);</script>';
		}else{
			echo '<script>alert("修改失败")</script>';
			echo '<script>javascript:history.back(-1);</script>';
			echo "<script>document.URL=location.href </script>";
		}
	}
	//种子类形
	public function seedtype(){
		$tdb=M('nztype');
		$tall=$tdb->select();
		$this->tall=$tall;
		$this->display();
	}
	//种子修改
	public function setstype(){
		$where['id']=I('t_id')+0;
		$tinfo=M('nztype')->where($where)->find();
		$this->tinfo=$tinfo;
		$this->display('addstype');
	}
	//种子添加  +  修改
	public function addtype(){
		$typeid=I('ttype')?I('ttype'):'';
		$data['name']=I('name');
		$tdb=M('nztype');
		if($typeid != '' ){
			$action=$tdb->where('id='.$typeid)->save($data);
			$say="修改";
		}else{
			$action=$tdb->add($data);
			$say="添加";
		}
		if($action){
			echo "<script>alert('{$say}成功')</script>";
			echo '<script>javascript:history.back(-1);</script>';
			echo "<script>document.URL=location.href </script>";
		}else{
			echo "<script>alert('{$say}失败')</script>";
			echo '<script>javascript:history.back(-1);</script>';
			echo "<script>document.URL=location.href </script>";
		}
	}
	//农田管理
	public function farmgl(){
		$fdb=M('nzfarmland');
		$fall=$fdb->select();
		$this->fall=$fall;
		//p($fall);
		$this->display();
	}
	//修改页面
	public function setfarm(){
		$where['id']=I('f_id')+0;
		$fdb=M('nzfarmland');
		$finfo=$fdb->where($where)->find();
		//p($finfo);
		$this->finfo=$finfo;
		$this->display();
	}
	public function savefarm(){
		$fdb=M('nzfarmland');
		$where['id']=I('f_id')+0;
		$data=I();
		if($where['id'] != '' ){
		$action=$fdb->where($where)->save($data);
		$say="修改";
		}else{
		$action=$fdb->add($data);
		$say="添加";
		}
		if($action){
			echo "<script>alert('{$say}成功')</script>";
			echo '<script>javascript:history.back(-1);</script>';
			echo "<script>document.URL=location.href </script>";
		}else{
			echo "<script>alert('{$say}失败')</script>";
			echo '<script>javascript:history.back(-1);</script>';
			echo "<script>document.URL=location.href </script>";
		}
	}
	//添加土地  注释了
	public function addfarm(){
		$this->display('setfarm');
	}
	//土地可种植
	public function okfarm(){
		$g_id=I('get.f_id/d');
		//echo $g_id;
		$gdb =M('nzgoods');
		$tdzz=$gdb->where("farmland_rt =$g_id")->select();
		echo $gdb->_sql();
		p($tdzz);
	
	}
	//添加 修改种子
	public function setseed(){
		//p(I());
		$where['id']=I('get.s_id');
		$gdb=M('nzgoods');
		$ginfo=$gdb->where($where)->find();
		//p($ginfo);
		$this->ginfo=$ginfo;
		$this->display('addseed');
	}
	//种子修改
	public function saveseed(){
		$data=I();
		if(!$data['s_id']){
		$data['s_id']+=0;
			redirect(U(MODULE_NAME.'/index/index'));
		}
		$pdb=M('nzgoods');


		//判断是否有图片上传
		if($_FILES['photo']['name'][0]){
		$img_p=$pdb->field('imgpath1')->where("id=".$data['s_id'])->find();
		if($img_p){
		$delpath= './public'.$img_p['imgpath1'];
		unlink($delpath);	
		}
		//图片上传
		$imgup=$this->_imgupload('goods');
		   $info=$imgup->upload();
		 if(!$info){
			$this->error($imgup->getError());
		}else{
			foreach($info as $file){
				$data['imgpath1']= $file['savepath'].$file['savename'];   
				}
			}
		}


		//判断是否有图片上传
		if($_FILES['photo']['name'][1]){
		$img_p=$pdb->field('imgpath2')->where("id=".$data['s_id'])->find();
		if($img_p){
		$delpath= './public'.$img_p['imgpath2'];
		unlink($delpath);	
		}
		//图片上传
		$imgup=$this->_imgupload('goods');
		   $info=$imgup->upload();
		 if(!$info){
			$this->error($imgup->getError());
		}else{
			foreach($info as $file){
				$data['imgpath2']= $file['savepath'].$file['savename'];   
				}
			}
		}






		if($pdb->where("id=".$data['s_id'])->save($data)){
			echo '<script>alert("修改成功")</script>';
			echo '<script>javascript:history.back(-1);</script>';
		}else{
			echo '<script>alert("修改失败")</script>';
			echo '<script>javascript:history.back(-1);</script>';
			echo "<script>document.URL=location.href </script>";
		}
		
	}
	//种子添加
	public function taddseed(){
		//p(I());
		$data=I();
		unset($data['id']);
		//如果有上传图片..就上传图片
		if($_FILES['photo']['name'][0] != ''){
		   $imgup=$this->_imgupload('goods');
		   $info=$imgup->upload();
		 if(!$info){
			$this->error($imgup->getError());
		}else{
			foreach($info as $file){
				$data['imgpath1']= $file['savepath'].$file['savename'];   
				}
			}
		}
		$gdb=M('nzgoods');
		if($gdb->add($data)){
			echo '<script>alert("添加成功")</script>';
			echo '<script>javascript:history.back(-1);</script>';
			echo "<script>document.URL=location.href </script>";
		}else{
			echo '<script>alert("添加失败")</script>';
			echo '<script>javascript:history.back(-1);</script>';
			echo "<script>document.URL=location.href </script>";
		}
		
		
	}
	
	
	
}