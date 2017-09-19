<?php
namespace Pc\Controller;
use Think\Controller;
class IndexController extends Controller {
    public function login() {
        $this->display();
    }

    public function index(){
        // 设备检测
        $device_name = is_wap() ? 'wap' : 'pc';
        if ($device_name == 'wap') {
            redirect(U('Mobile/Index/ncgl'));
        }

    	$userid = session('userid');
    	if (empty($userid)) {
    		cookie('browser', 'pc');
    		redirect(U('Index/login'));
    	}
    	$user_row = M('user')->field('account')->where(array(
    		'userid'=>$userid
    	))->find();
    	$this->assign('user_row', $user_row);

    	$news_row = M('nznew')->field('new_id')->order('new_id asc')->find();
    	$this->assign('news_row', $news_row); 	

    	$this->display();
    }
}