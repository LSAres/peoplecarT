<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/7/22
 * Time: 18:26
 */
namespace Admin\Controller;
use Think\Controller;
class WealthtotalController extends CommonController {

     #农田汇总
      public  function farmland_total(){
          echo '<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />';
          #所有用户财富统计
          $sql_userb="SELECT sum(wealthb) wealthb,sum(numberb) numberb,sum(integral) integral FROM syd_user";
          $wealth_arr=M('user')->query($sql_userb);

          #农田种子统计
          $db_farmland=M('nzusfarm');
          $sql_zong="SELECT s_id , count(s_id) as num , g.`name` , g.wealthb,g.numberb , g.integral  FROM `syd_nzusfarm` as f LEFT JOIN syd_nzgoods as g on g.id=f.s_id where s_id <> 0 GROUP BY s_id";
          $zongzi_arr=$db_farmland->query($sql_zong);
         // p($zongzi_arr);
          $this->assign(array(
              'wealth_arr'=> $wealth_arr,
              'zongzi_arr'=>$zongzi_arr
          ));
          $this->display();
      }

      #用户财富统计user_weath
      public  function user_wealth(){
           #激活码
           $sql_activation="SELECT COUNT(jhcode) renshu,SUM(jhcode) geshu FROM `syd_user` where jhcode <>0";
          #充值
          $sql_chong="";

      }


}