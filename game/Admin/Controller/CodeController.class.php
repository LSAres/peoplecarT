<?php
namespace Admin\Controller;
use Think\Controller;
class CodeController extends CommonController {

    //查出新闻标题
    public function importcode(){
        echo '你好';
        $this->display();
    }

    public function uploadtxt(){
        echo 'qwe sdf a';
        p($_FILES);
        $upload = new \Think\Upload();// 实例化上传类
        $upload->maxSize   =     3145728 ;// 设置附件上传大小
        $upload->exts      =     array('txt');// 设置附件上传类型
        $upload->rootPath  =      './Text/'; // 设置附件上传根目录
        $upload->savePath  =      ''; // 设置附件上传（子）目录
        // 上传文件 
        $info   =   $upload->upload();
        if(!$info) {// 上传错误提示错误信息
            $this->error($upload->getError());
        }else{// 上传成功 获取上传文件信息
            foreach($info as $file){
                $path = $file['savepath'].$file['savename'];
            }
        }
        
        $path ="./Text/".$path;
        echo $path;

       var_dump(file_exists($path));


       $content = file_get_contents($path);

               //正则分割数组
        $array=preg_split("/\n/", $content);

            
        //主成数组
        $arr=array();
        foreach ($array as $v) {
            if($v != ''){
            $arr[]=explode(',',$v);
            }
        }

        $cdb = M('nzcode');
        $cdb->startTrans();
        $sctime = time();
        foreach ($arr as $k => $v  ){
            $datalist[$k] = array('cd_codeid'=>$v[0],'cd_psw'=>$v[1],'cd_time'=> $sctime );
        }


      $addall = $cdb->addAll($datalist);
        if($addall){
            $cdb->commit();
            unlink($path);
            echo '删除成功';
        }else{
            $cdb->rollback();
            echo '导入失败';
        }

    }

}


