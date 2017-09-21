<?php
namespace Admin\Controller;
use Think\Controller;
class ParameterProbabilityController extends CommonController{
    public function functionValueResetPage(){
        $this->display();
    }

    public function helpDocumentList(){
        $where = null;
        $tb_helpintroduce = M('helpintroduce');
        $pagesize =10;
        $p = getpage($tb_helpintroduce, $where, $pagesize);
        $pageshow   = $p->show();

        $helpArr=$tb_helpintroduce->where($where)
            ->field('id,title,content,time')
            ->order('id desc ')
            ->select();

        $this->assign(array(
            'helpArr'=>$helpArr,
            'pageshow'=>$pageshow,
        ));
        $this->display();
    }

    public function operationLogPage(){
        $this->display();
    }

    public function addHelpDocument(){
        $this->display();
    }

    public function addhelpintroduce(){
        $t=I('post.');
        foreach($t as $v){
            if($v == ''){
                echo "<script>alert('请确认输入完成');</script>";
                echo "<script>javascript:history.back(-1);</script>";die;
            }
        }
        $title = I('post.title');
        $content = I('post.content');
        $data['title'] = $title;
        $data['content'] = $content;
        $data['time'] = time();
        $res = M('helpintroduce')->data($data)->add();
        if($res){
            echo "<script>alert('添加成功');</script>";
            echo "<script>window.location.href='".U('ParameterProbability/helpdocumentList')."'</script>";
        }else{
            echo "<script>alert('添加失败');</script>";
            echo "<script>javascript:history.back(-1);</script>";die;
        }
    }

    public function edithelpintroduce(){
        $id = I('post.id');
        $content = I('post.content');
        $res = M('helpintroduce')->where('id='.$id)->setField('content',$content);
        if($res){
            echo "<script>alert('修改成功');</script>";
            echo "<script>window.location.href='".U('ParameterProbability/helpdocumentList')."'</script>";
        }else{
            echo "<script>alert('修改失败');</script>";
            echo "<script>javascript:history.back(-1);</script>";die;
        }
    }
    public function deletehelpintroduce(){
        $id = I('get.id');
        $res = M('helpintroduce')->where('id='.$id)->delete();
        if($res){
            echo "<script>alert('删除成功');</script>";
            echo "<script>window.location.href='".U('ParameterProbability/helpdocumentList')."'</script>";
        }else{
            echo "<script>alert('删除失败');</script>";
            echo "<script>javascript:history.back(-1);</script>";die;
        }
    }
}