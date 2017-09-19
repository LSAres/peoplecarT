<?php
namespace Mobile\Controller;
use Think\Controller;
class CommonController extends Controller {
	public function _initialize(){
                
          if(!session('userid')){
            redirect(U('Regus/login'));
           }

    }

    
    public function notin(){
        $site=M('nzsiteclose');
        $dbTest=M('sitetest');
        $userid=session('userid');
         
        $status=$site->where('id=1')->getField('site_status');//网站状太
        $arrTest=$dbTest->getField('u_id',true);//拿有权限的人
        $a=in_array($userid, $arrTest);
       
        if ($status==1&&!$a) {
           session('userid',null);   
           $this->closeInfo();die;
        }
    }

    public function closeInfo(){
        echo "<meta charset='utf-8'>";
        $site=M('nzsiteclose');
        $reason=$site->where('id=1')->getField('reason');//网站关闭原因
        echo $reason;
        exit;
    }

}

