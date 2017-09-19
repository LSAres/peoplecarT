<?

public function familytree(){
       
 
/**
*  无级递归分类
*  @param  int   $assortPid  要查询分类的父级id
*  @param  mixed  $tag     上下级分类之间的分隔符
*  @return string $tree    返回的分类树型结构结果 
*
*/
function recursiveAssort($assortPid, $tag ='')
{  
  $assort = M('users')->where("pid = $assortPid")->field('pid,userid,username,regdate,vip')->select();

  foreach ($assort as $value) {
    $tree .= '<option value="'. $value['userid'] . '">'. $tag .$tag ."|—".'姓名:'. $value['username'] ."—"."类型：".us_lv($value['vip']). "—"."加入时间:"."(".date('Y-m-d',$value[regdate]).")";'</option>';
    $tree .= recursiveAssort($value['userid'],$tag . ' ');
    }
  return $tree;
   }


 $aaa=recursiveAssort(0);
 
 $this->aaa=$aaa;
 $this->display();

    }





?>