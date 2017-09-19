<?php
namespace Admin\Controller;
use Think\Controller;
class NewController extends CommonController {

    //查出新闻标题
    public function new_select(){
        $db_news=M('nznew')->order('new_id desc');
        $this->arrData_news = $db_news->select();
        $this->display('New/new_select');
    }

    //添加新闻公告
    public function new_add(){
        if(empty($_POST)){ //如果post为空，直接调用模板
        $this->display();
        }else{ //如果post不为空，则添加数据

        $db_newadd=M('nznew');
        $data['new_title']=$_POST['new_title'];
        $data['new_content']=$_POST['new_content'];
        $data['new_time']=time();
        $boolData=$db_newadd->data($data)->add();

        if($boolData){  //判断添加是否成功

            echo "<meta charset=\"utf-8\"/> <script>alert('新闻公告添加成功')</script>";
            echo '<script>location.href="'.U('New/new_select').'"</script>';
        }else{
            echo "<meta charset=\"utf-8\"/> <script>alert('新闻公告添加失败')</script>";
            echo '<script>javascript:history.back(-1);</script>';
        }

        }
    }

    //删除新闻公告
    public function new_delete($new_id=null){
        $obj = M("nznew");
        $data = $obj->delete($new_id);
        if($data){
            echo "<meta charset=\"utf-8\"/> <script>alert('新闻公告删除成功')</script>";
            echo '<script>location.href="'.U('New/new_select').'"</script>';
        }else{
            echo "<meta charset=\"utf-8\"/> <script>alert('新闻公告删除失败')</script>";
            echo '<script>location.href="'.U('New/new_select').'"</script>';
        }
    }

    //查看新闻公告详情
    public function new_ckxq($new_id =nul){
        $db_newckxq =M('nznew')->where('new_id='.$new_id);
        $this->arrData_newckxq=$db_newckxq->select();
        $this->display('New/new_ckxq');
    }
	
	//添加视频链接
    public function shipin(){
        if(empty($_POST)){ //如果post为空，直接调用模板
        $this->display();
        }else{ //如果post不为空，则添加数据

        $db_newadd=M('shipin');
        $data['address']=$_POST['address'];
		$data['picture_address'] = $_POST['picture_address'];
        $boolData=$db_newadd->data($data)->add();

        if($boolData){  //判断添加是否成功

            echo "<meta charset=\"utf-8\"/> <script>alert('视频链接地址添加成功')</script>";
            echo '<script>location.href="'.U('New/shipinlist').'"</script>';
        }else{
            echo "<meta charset=\"utf-8\"/> <script>alert('视频链接地址添加失败')</script>";
            echo '<script>javascript:history.back(-1);</script>';
        }

        }
    }
	
	 public function shipinlist(){
         $cwhere=I('where');
         if(I('condition')) {
             $we = I('condition');
             $value = trim(I('text'));
             if ($we != '') {
                 //$where[$we] ="".$we." like '%".$value."%'" ;
                 $where[$we] = array('like', "%$value%");
             } else {
                 $where[$we] = $value;
             }
         }
         $clockwhere = 'clockwhere_'.MODULE_NAME.CONTROLLER_NAME.ACTION_NAME ;
         if($cwhere){
             session($clockwhere,null);
             session($clockwhere,$where);
         }
         $where = session($clockwhere)?session($clockwhere):$where;
         $m=M('shipin');
         $pagesize =20;
         $p = getpage($m, $where, $pagesize);
         $pageshow   = $p->show();
         $data = $m->alias('a')
             ->where($where)
             ->order('id desc ')
             ->select();
        
         $this->assign('data', $data);
         $this->assign('pageshow',$pageshow);
         $this->display();
     }
	 
	  public function shipinDelete($id=null){
        $m = M("shipin");
        $Info = $m->where('id='.$id)->find();
        $data = $m->delete($id);
        if($data==1){
            echo "<meta charset=\"utf-8\"/> <script>alert('删除成功')</script>";
                echo '<script>location.href="'.U('New/shipinlist').'"</script>';
        }else{
            echo "<meta charset=\"utf-8\"/> <script>alert('删除成功')</script>";
            echo '<script>location.href="'.U('New/shipinlist').'"</script>';
        }
    }
	
	public function new_change($new_id=null){
		$newInfo = M('nznew')->where('new_id='.$new_id)->find();
		$this->assign('newInfo',$newInfo);
		$this->display();
	}
	
	public function new_update(){
		$db_newadd=M('nznew');
		$id = $_POST['new_id'];
        $data['new_title']=$_POST['new_title'];
        $data['new_content']=$_POST['new_content'];
        $data['new_time']=time();
        $boolData=$db_newadd->where('new_id='.$id)->save($data);

        if($boolData){  //判断添加是否成功

            echo "<meta charset=\"utf-8\"/> <script>alert('新闻公告修改成功')</script>";
            echo '<script>location.href="'.U('New/new_select').'"</script>';
        }else{
            echo "<meta charset=\"utf-8\"/> <script>alert('新闻公告修改失败')</script>";
            echo '<script>javascript:history.back(-1);</script>';
        }

	}


}