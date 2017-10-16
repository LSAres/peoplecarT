<?php
namespace Mobile\Controller;
use Think\Controller;
class TeamcontrolController extends CommonController {
    public function team_recommenddetailed(){
        $userid = session('userid');
        $username = M('user')->where('userid='.$userid)->getField('username');
        $childInfo = M('user')->where('recommend_id='.$userid)->select();
        foreach ($childInfo as $k=>$v){
            $count = M('user')->where('recommend_id='.$v['userid'])->count();
            $childInfo[$k]['recommend_count']=$count;
        }
        $this->assign('username',$username);
        $this->assign('childInfo',$childInfo);
        $this->display();
    }
}