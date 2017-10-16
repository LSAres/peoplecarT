<?php
namespace Mobile\Controller;
use Think\Controller;
class UsersettingsController extends CommonController {
    public function user_bankmessage(){
        $this->display();
    }

    public function user_message(){
        $this->display();
    }

    public function user_passwordchange(){
        $this->display();
    }

    public function user_privateemail(){
        $userid = session('userid');
        $private_email = M('useremail')->where('uid='.$userid)->select();
        $this->assign('private_email',$private_email);
        $this->display();
    }

    public function user_recommendedstructure(){
        $this->display();
    }

    public function user_systemnotice(){
        $db_new = M('new');
        $new_list = $db_new->where()->select();
        $this->assign('new_list',$new_list);
        $this->display();
    }

}