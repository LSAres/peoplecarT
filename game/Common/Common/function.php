<?php

function is_wap() { 
    if (isset ($_SERVER['HTTP_X_WAP_PROFILE'])) return true;
    if (isset ($_SERVER['HTTP_VIA'])) return stristr($_SERVER['HTTP_VIA'], "wap") ? true : false;
    if (isset ($_SERVER['HTTP_USER_AGENT'])) {
        $clientkeywords = array ('nokia','sony','ericsson','mot','samsung','htc','sgh','lg','sharp','sie-','philips','panasonic','alcatel','lenovo','iphone','ipod','blackberry','meizu','android','netfront','symbian','ucweb','windowsce','palm','operamini','operamobi','openwave','nexusone','cldc','midp','wap','mobile'); 
        if (preg_match("/(" . implode('|', $clientkeywords) . ")/i", strtolower($_SERVER['HTTP_USER_AGENT']))) return true;
    } 
    if (isset($_SERVER['HTTP_ACCEPT'])) {
        if ((strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') !== false) && (strpos($_SERVER['HTTP_ACCEPT'], 'text/html') === false || (strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') < strpos($_SERVER['HTTP_ACCEPT'], 'text/html')))) return true;
    } 
    return false;
} 

function   p ($arr){
       echo '<pre>',print_r($arr,true),'</pre>';
}


//激活码生成
function create_code($namespace = 'huilian') {  
    static $guid = '';  
    $uid = uniqid ( "", true );  
      
    $data = $namespace;  
    $data .= $_SERVER ['REQUEST_TIME'];     // 请求那一刻的时间戳  
    $data .= $_SERVER ['HTTP_USER_AGENT'];  // 获取访问者在用什么操作系统  
    $data .= $_SERVER ['SERVER_ADDR'];      // 服务器IP  
    $data .= $_SERVER ['SERVER_PORT'];      // 端口号  
    $data .= $_SERVER ['REMOTE_ADDR'];      // 远程IP  
    $data .= $_SERVER ['REMOTE_PORT'];      // 端口信息  
      
    $hash = strtoupper ( hash ( 'ripemd128', $uid . $guid . md5 ( $data ) ) );  
   // $guid = '{' . substr ( $hash, 0, 8 ) . '-' . substr ( $hash, 8, 4 ) . '-' . substr ( $hash, 12, 4 ) . '-' . substr ( $hash, 16, 4 ) . '-' . substr ( $hash, 20, 12 ) . '}';       
    return $hash;  
} 

//用户权限  
function  us_lv($id){
	$userid =$id?$id:session('userid');
	 $udb = M('user');
	 $zhitui = $udb->where("parent_id ={$userid} and farm_num != 0 ")->count("parent_id");
	// p($pcount);
	if($zhitui>=20){
		$tudiroot['lv3']=4;
		$tudiroot['lv2']=4;
		$tudiroot['lv1']=4;

		$tudiroot['fishroot']['lv10']=2;
		$tudiroot['fishroot']['lv8']=2;
		$tudiroot['fishroot']['lv6']=2;
		$tudiroot['fishroot']['lv0']=1;

		$tudiroot['userlv']=11;
	} else if( $zhitui >= 18 ){
		$tudiroot['lv3']=3;
		$tudiroot['lv2']=4;
		$tudiroot['lv1']=4;

		$tudiroot['fishroot']['lv10']=1;
		$tudiroot['fishroot']['lv8']=2;
		$tudiroot['fishroot']['lv6']=2;
		$tudiroot['fishroot']['lv0']=1;

		$tudiroot['userlv']=10;
	} else if ($zhitui >= 16){
		$tudiroot['lv3']=2;
		$tudiroot['lv2']=4;
		$tudiroot['lv1']=4;

		$tudiroot['fishroot']['lv10']=0;
		$tudiroot['fishroot']['lv8']=2;
		$tudiroot['fishroot']['lv6']=2;
		$tudiroot['fishroot']['lv0']=1;

		$tudiroot['userlv']=9;
	} else if ($zhitui >= 14){
		$tudiroot['lv3']=1;
		$tudiroot['lv2']=4;
		$tudiroot['lv1']=4;

		$tudiroot['fishroot']['lv10']=0;
		$tudiroot['fishroot']['lv8']=1;
		$tudiroot['fishroot']['lv6']=2;
		$tudiroot['fishroot']['lv0']=1;

		$tudiroot['userlv']=8;
	} else if ($zhitui >= 12){
		$tudiroot['lv3']=0;
		$tudiroot['lv2']=4;
		$tudiroot['lv1']=4;


		$tudiroot['fishroot']['lv10']=0;
		$tudiroot['fishroot']['lv8']=0;
		$tudiroot['fishroot']['lv6']=2;
		$tudiroot['fishroot']['lv0']=1;

		$tudiroot['userlv']=7;
	} else if ($zhitui >= 10){
		$tudiroot['lv3']=0;
		$tudiroot['lv2']=3;
		$tudiroot['lv1']=4;

			
		$tudiroot['fishroot']['lv10']=0;
		$tudiroot['fishroot']['lv8']=0;
		$tudiroot['fishroot']['lv6']=1;
		$tudiroot['fishroot']['lv0']=1;

		$tudiroot['userlv']=6;
	} else if ($zhitui >= 8){
		$tudiroot['lv3']=0;
		$tudiroot['lv2']=2;
		$tudiroot['lv1']=4;
		$tudiroot['userlv']=5;
	} else if ($zhitui >= 6){
		$tudiroot['lv3']=0;
		$tudiroot['lv2']=1;
		$tudiroot['lv1']=4;
		$tudiroot['userlv']=4;
	} else if ($zhitui >= 4){
		$tudiroot['lv3']=0;
		$tudiroot['lv2']=0;
		$tudiroot['lv1']=4;
		$tudiroot['userlv']=3;
	} else if ($zhitui >= 2){
		$tudiroot['lv3']=0;
		$tudiroot['lv2']=0;
		$tudiroot['lv1']=3;
		$tudiroot['userlv']=2;
	}else if ($zhitui < 2){
		$tudiroot['lv3']=0;
		$tudiroot['lv2']=0;
		$tudiroot['lv1']=2;
		$tudiroot['userlv']=1;
	}
		$tudiroot['zhitui']=$zhitui;
	return $tudiroot;
} 


function deep_in_array($value, $array) { 
foreach($array as $item) { 
if(!is_array($item)) { 
if ($item == $value) {
return true;
} else {
continue; 
}
} 
if(in_array($value, $item)) {
return true; 
} else if(deep_in_array($value, $item)) {
return true; 
}
} 
return false; 
}


/**
 * TODO 基础分页的相同代码封装，使前台的代码更少
 * @param $m 模型，引用传递
 * @param $where 查询条件
 * @param int $pagesize 每页查询条数
 * @return \Think\Page
 */
function getpage(&$m,$where,$pagesize=10){
    $m1=clone $m;//浅复制一个模型
    $count = $m->where($where)->count();//连惯操作后会对join等操作进行重置
    $m=$m1;//为保持在为定的连惯操作，浅复制一个模型
    $p=new Think\Page($count,$pagesize);
    $p->lastSuffix=false;
    $p->setConfig(array('header'=>'条记录','prev'=>'上一页','next'=>'下一页','first'=>'首页','last'=>'末页','theme'=>' %totalRow% %header% %nowPage%/%totalPage% 页 %upPage% %downPage% %first%  %prePage%  %linkPage%  %nextPage% %end%'));
    $p->setConfig('prev','上一页');
    $p->setConfig('next','下一页');
    $p->setConfig('last','末页');
    $p->setConfig('first','首页');
    $p->setConfig('theme','%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% %HEADER%');

    $p->parameter=I('get.');

    $m->limit($p->firstRow,$p->listRows);

    return $p;
}


    function file_uploading($images_path='/Uploads/')
    {       
        if (!is_dir($images_path)) {
            mkdir($images_path);
        }
            $upload             = new \Think\Upload();// 实例化上传类    
            $upload->maxSize    =     3145728 ;// 设置附件上传大小    
            $upload->exts       =     array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型    
            $upload->rootPath   =      './Public/'; // 设置上传根目录    // 上传文件     
            $upload->savePath   =      $img_path; // 设置上传子目录    // 上传文件     
            $info               =   $upload->upload(); 

                if(!$info) {// 上传错误提示错误信息        
                    return $upload->getError();
                }else{// 上传成功 
                    // dump($info);die;
                    foreach($info as $file){ 

                           $img_path = $file['savepath'].$file['savename'];
                           return $img_path;

                    }
                    
                }
    }


function NoRand($begin=0,$end=20,$limit=3){ 
    $rand_array=range($begin,$end); 
    shuffle($rand_array);//调用现成的数组随机排列函数 
    // array_slice($rand_array,0,$limit);//截取前$limit个 
    foreach (array_slice($rand_array,0,$limit) as $key => $value) {
        $randstr.=$value;
    }
    return $randstr;

} 

function real_ip(){
            static $realip = NULL;

            if ($realip !== NULL)
            {
                return $realip;
            }

            if (isset($_SERVER))
            {
                if (isset($_SERVER['HTTP_X_FORWARDED_FOR']))
                {
                    $arr = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);

                    /* 取X-Forwarded-For中第一个非unknown的有效IP字符串 */
                    foreach ($arr AS $ip)
                    {
                        $ip = trim($ip);

                        if ($ip != 'unknown')
                        {
                            $realip = $ip;

                            break;
                        }
                    }
                }
                elseif (isset($_SERVER['HTTP_CLIENT_IP']))
                {
                    $realip = $_SERVER['HTTP_CLIENT_IP'];
                }
                else
                {
                    if (isset($_SERVER['REMOTE_ADDR']))
                    {
                        $realip = $_SERVER['REMOTE_ADDR'];
                    }
                    else
                    {
                        $realip = '0.0.0.0';
                    }
                }
            }
            else
            {
                if (getenv('HTTP_X_FORWARDED_FOR'))
                {
                    $realip = getenv('HTTP_X_FORWARDED_FOR');
                }
                elseif (getenv('HTTP_CLIENT_IP'))
                {
                    $realip = getenv('HTTP_CLIENT_IP');
                }
                else
                {
                    $realip = getenv('REMOTE_ADDR');
                }
            }

            preg_match("/[\d\.]{7,15}/", $realip, $onlineip);
            $realip = !empty($onlineip[0]) ? $onlineip[0] : '0.0.0.0';

            return $realip;
        }


#取一条数据
function get_One($sql){

    $result = mysql_query($sql);
    $data = array();
    if($result && mysql_num_rows($result)>0){
        $data = mysql_fetch_assoc($result);
    }
    return $data;
}

#取所有数据
function get_All($sql){
  $result = mysql_query($sql);
  $data = array();
  if($result && mysql_num_rows($result)>0){
    while($row = mysql_fetch_assoc($result)){
      $data[] = $row;
    }
  }
  return $data;
}
#返回id值
function sql_sentence($sql){

    $res = mysql_query($sql);
    if($res && mysql_affected_rows()>0){
        return mysql_insert_id();
    }else{
        return false;
    }
}

function array_iconv($in_charset,$out_charset,$arr){  
            return eval('return '.iconv($in_charset,$out_charset,var_export($arr,true).';'));  
    }


    if(!function_exists('upload')){
            function upload($name,$dir='uploads',$size=2048576,$arr=array('jpg','png','gif')){
                if($_FILES[$name]['error']>0){
                    switch($_FILES[$name]['error']){
                        case 1:
                            $res['error'] = 1;
                            $res['msg'] = "文件超过2M";
                            return $res;
                            break;
                        case 2:
                            $res['error'] = 1;
                            $res['msg'] = "文件超出MAX_FILE_SIZE大小";
                            return $res;
                            break;
                        case 3:
                            $res['error'] = 1;
                            $res['msg'] = "文件上传失败";
                            return $res;
                            break;
                        case 4:
                            $res['error'] = 1;
                            $res['msg'] = "请选择文件";
                            return $res;
                            break;
                        default:
                            $res['error'] = 1;
                            $res['msg'] = "未知错误";
                            return $res;
                            break;
                    }
                }

                if($_FILES[$name]['size']>$size){
                    $res['error'] = 1;
                    $res['msg'] = "上传文件超出指定大小";
                    return $res;
                }

                $ext = pathinfo($_FILES[$name]['name'],PATHINFO_EXTENSION);
                if(!in_array($ext,$arr)){
                    $res['error'] = 1;
                    $res['msg'] = "限传'jpg','png','gif'类型的文件";
                    return $res;
                }

                $tmp_dir = date('Y_m_d');
                $dir = $dir.'/'.$tmp_dir;
                if(!is_dir($dir)){
                    mkdir($dir,0777,true);
                }
                do{                                                 //循环体
                    $filename = time().rand(1000,9999);
                    $file = $dir.'/'.$filename.'.'.$ext;
                }while(is_file($file));                             //循环体
                $return_file = $tmp_dir.'/'.$filename.'.'.$ext;

                if(is_uploaded_file($_FILES[$name]['tmp_name'])){
                    move_uploaded_file($_FILES[$name]['tmp_name'], $file);
                    $res['error'] = 2;
                    $res['msg'] = "上传成功";
                    $res['path'] = $return_file;

                    return $res;
                }
            }
        }   


//发展等级
function wd_zhituilv($str){
    switch ($str) {
        case 1:
            return "一代收益";
            break;
        case 2:
            return "二代收益";
            break;
        case 3:
            return "三代收益";
            break;
        case 4:
            return "四代收益";
            break;
        case 5:
            return "五代收益";
            break;
        case 30:
            return "推荐激活";
            break;        

    }

}

    //得到关系普
    function gx_guanxi($userid){
        $ndb = M();
/*   --   1  -  5   最先
      $ndall = $ndb->query(
      "select userid as id , parent_id as pid , mobile, add_time from syd_user where userid in   
      (SELECT gx_1uid FROM `syd_nzguanxi` where gx_uid = {$userid} and gx_1uid != 0 GROUP BY gx_1uid union
      SELECT gx_2uid FROM `syd_nzguanxi` where gx_uid = {$userid} and gx_2uid != 0 GROUP BY gx_2uid union
      SELECT gx_3uid FROM `syd_nzguanxi` where gx_uid = {$userid} and gx_3uid != 0 GROUP BY gx_3uid UNION  
      SELECT gx_4uid FROM `syd_nzguanxi` where gx_uid = {$userid} and gx_4uid != 0 GROUP BY gx_4uid UNION  
      SELECT gx_5uid FROM `syd_nzguanxi` where gx_uid = {$userid} and gx_5uid != 0 GROUP BY gx_5uid )"); */

      $ndall = $ndb->query(
      "select userid as id , parent_id as pid , mobile,true_name, add_time , farm_num ,farm_lock from syd_user where userid in   
      (SELECT gx_1uid FROM `syd_nzguanxi` where gx_uid = {$userid} and gx_1uid != 0 GROUP BY gx_1uid union
      SELECT gx_2uid FROM `syd_nzguanxi` where gx_uid = {$userid} and gx_2uid != 0 GROUP BY gx_2uid union
      SELECT gx_3uid FROM `syd_nzguanxi` where gx_uid = {$userid} and gx_3uid != 0 GROUP BY gx_3uid ) ");
      return $ndall;
    }



    function lock_user($user_status){
        switch($user_status){
            case 1:
                $res = "未激活";
                return $res;
                break;
            case 2:
                $res = "冻结";
                return $res;
                break;
                break;
            case 3:
                $res = "挂单24小时内未确认冻结";
                return $res;
                break;
            case 4:
                $res = "农田租用时间到七天内未打款冻结";
                return $res;
                break;
            case 5:
                $res = "注册后七天未激活农田冻结";
                return $res;
                break;

            case 0:
                $res = "未锁定";
                return $res;
                break;

        }

    }

function is_weixin() {
    if ( strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false ) {
        return true;
    }
    if ( strpos($_SERVER['HTTP_REFERER'], 'Pc/Index/index') !== false) {
        return true;
    }
	return false;
}

?>