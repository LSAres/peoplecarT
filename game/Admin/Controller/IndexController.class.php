<?php
namespace Admin\Controller;
use Think\Controller;
class IndexController extends CommonController {
    //左侧主菜单
    public function backstageSammaryPage(){
        $sp_username = session('sp_name');
        $this->assign('sp_username',$sp_username);
        $this->display();
    }

    public function outlogin(){
        session(null);
        redirect(U(MODULE_NAME.'/Login/index'));
    }
}