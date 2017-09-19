<?php
namespace Admin\Controller;
use Think\Controller;
class IndexController extends CommonController {
    public function backstageSammaryPage(){
		$this->display();
   }
   
   public function gameNotice(){
   		$this->display();
   }

   #管理员修改账号和密码
   public function manage_up(){
        $this->display();


       /* if(!IS_POST){
         

        }else{
           #修改资料

        }*/

   } 


}