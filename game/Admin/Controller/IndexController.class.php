<?php
namespace Admin\Controller;
use Think\Controller;
class IndexController extends CommonController {
    //左侧主菜单
    public function backstageSammaryPage(){
        $this->display();
    }
}