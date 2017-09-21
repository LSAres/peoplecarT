<?php
namespace Admin\Controller;
use Think\Controller;
class NoticControlController extends CommonController{
    public function noticeListPage(){
        $where = null;
        $tb_new = M('new');
        $pagesize =10;
        $p = getpage($tb_new, $where, $pagesize);
        $pageshow   = $p->show();

        $newArr=$tb_new->where($where)
            ->field('id,title,content,add_time')
            ->order('id desc ')
            ->select();

        $this->assign(array(
            'userArr'=>$newArr,
            'pageshow'=>$pageshow,
        ));
        $this->display();
    }
    public function addNoticePage(){
        $this->display();
    }
    public function emailToUserPage(){
        $this->display();
    }
    public function addNotice(){
        $t=I('post.');
        foreach($t as $v){
            if($v == ''){
                echo "<script>alert('请确认输入完成');</script>";
                echo "<script>javascript:history.back(-1);</script>";die;
            }
        }
        $title = I('post.title');
        $content = I('post.content');
        $data['title']=$title;
        $data['content']=$content;
        $data['add_time'] = time();
        $res = M('new')->data($data)->add();
        if($res){
            echo "<script>alert('添加成功');</script>";
            echo "<script>window.location.href='".U('NoticControl/noticeListPage')."'</script>";
        }else{
            echo "<script>alert('添加失败');</script>";
            echo "<script>javascript:history.back(-1);</script>";die;
        }
    }

    public function editNotice(){
        $id = I('post.id');
        $content = I('post.content');
        $res = M('new')->where('id='.$id)->setField('content',$content);
        if($res){
            echo "<script>alert('修改成功');</script>";
            echo "<script>window.location.href='".U('NoticControl/noticeListPage')."'</script>";
        }else{
            echo "<script>alert('修改失败');</script>";
            echo "<script>javascript:history.back(-1);</script>";die;
        }
    }

    public function deletenew(){
        $id = I('get.id');
        $res = M('new')->where('id='.$id)->delete();
        if($res){
            echo "<script>alert('删除成功');</script>";
            echo "<script>window.location.href='".U('NoticControl/noticeListPage')."'</script>";
        }else{
            echo "<script>alert('删除失败');</script>";
            echo "<script>javascript:history.back(-1);</script>";die;
        }
    }

    public function addUserEmail(){
        $sp_id = session('sp_user');
        $t=I('post.');
        foreach($t as $v){
            if($v == ''){
                echo "<script>alert('请确认输入完成');</script>";
                echo "<script>javascript:history.back(-1);</script>";die;
            }
        }
        $account = I('post.account');
        $title = I('post.title');
        $content = I('post.content');

        //判断是否存在此账号
        $is_account = M('user')->where("account='".$account."'")->find();
        if(!$is_account){
            echo "<script>alert('用户账号不存在');</script>";
            echo "<script>javascript:history.back(-1);</script>";die;
        }
        $userid = M('user')->where("account='".$account."'")->getField('userid');
        $data['uid']=$userid;
        $data['sp_id']=$sp_id;
        $data['title']=$title;
        $data['content']=$content;
        $data['time']=time();
        $res = M('useremail')->data($data)->add();
        if($res){
            echo "<script>alert('发送成功');</script>";
            echo "<script>window.location.href='".U('NoticControl/noticeListPage')."'</script>";
        }else{
            echo "<script>alert('发送失败');</script>";
            echo "<script>javascript:history.back(-1);</script>";die;
        }
    }
}