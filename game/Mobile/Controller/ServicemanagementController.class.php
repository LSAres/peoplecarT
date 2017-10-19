<?php
namespace Mobile\Controller;
use Think\Controller;
class ServicemanagementController extends CommonController {
    public function user_memberupgrade(){
        if(!I('post.')) {
            $userid = session('userid');
            $userInfo = M('user')->where('userid=' . $userid)->find();
            if ($userInfo['leve'] == 1) {
                $userInfo['shenfen'] = "普通会员";
            }
            if ($userInfo['leve'] == 2) {
                $userInfo['shenfen'] = "代理商";
            }
            $this->assign('userInfo', $userInfo);
            $this->display();
        }else{
            $degree=I('post.degree');
            $userid=session('userid');
            $condition['uid']=$userid;
            $condition['status']=0;
            $is_have=M('change_degree')->where($condition)->find();
            if($is_have){
                echo "<script>alert('您已经发出申请，不可再次申请！');</script>";
                echo "<script>javascript:history.back(-1);</script>";die;
            }
            $data['uid']=$userid;
            $data['degree']=$degree;
            $data['status']=0;
            $data['time']=time();
            $res = M('change_degree')->data($data)->add();
            if($res){
                echo "<script>alert('申请成功，请等待客服人员与您联系');location.href='".U('Index/copyPageTwo')."'</script>";
                exit();
            }else{
                echo "<script>alert('申请失败');</script>";
                echo "<script>javascript:history.back(-1);</script>";die;
            }
        }
    }
    public function user_register(){
        $this->display();
    }
    public function user_reportcenter(){
        $this->display();
    }

    public function user_provinceapply(){
        if(!I('post.')){
            $this->display();
        }else{
            $province = I('post.province');
            $provinceDataCode = I('post.provinceDataCode');
            if(!$province){
                echo "<script>alert('请选择要代理的省份');</script>";
                echo "<script>javascript:history.back(-1);</script>";die;
            }
            if(!$provinceDataCode){
                echo "<script>alert('系统出错，请重新选择要代理的省份');</script>";
                echo "<script>javascript:history.back(-1);</script>";die;
            }
            $userid = session('userid');
            $data['uid']=$userid;
            $data['type']=1;
            $data['province']=$province;
            $data['province_code']=$provinceDataCode;
            $date['time']=time();
            $data['status']=0;
            $res = M('apply_agent')->data($data)->add();
            if($res){
                echo "<script>alert('申请成功，请等待客服人员与您联系');location.href='".U('Index/copyPageTwo')."'</script>";
                exit();
            }else{
                echo "<script>alert('申请失败');</script>";
                echo "<script>javascript:history.back(-1);</script>";die;
            }
        }
    }

    public function user_cityapply(){
        if(!I('post.')){
            $this->display();
        }else{
            $province = I('post.province');
            $provinceDataCode = I('post.provinceDataCode');
            $city = I('post.city');
            $cityDataCode = I('post.cityDataCode');

            if(!$province){
                echo "<script>alert('请选择要代理的省份');</script>";
                echo "<script>javascript:history.back(-1);</script>";die;
            }
            if(!$provinceDataCode){
                echo "<script>alert('系统出错，请重新选择要代理的省份');</script>";
                echo "<script>javascript:history.back(-1);</script>";die;
            }
            if(!$city){
                echo "<script>alert('请选择要代理的城市');</script>";
                echo "<script>javascript:history.back(-1);</script>";die;
            }
            if(!$cityDataCode){
                echo "<script>alert('系统出错，请重新选择要代理的城市');</script>";
                echo "<script>javascript:history.back(-1);</script>";die;
            }
            $userid = session('userid');
            $data['uid']=$userid;
            $data['type']=2;
            $data['province']=$province;
            $data['province_code']=$provinceDataCode;
            $data['city']=$city;
            $data['city_code']=$cityDataCode;
            $date['time']=time();
            $data['status']=0;
            $res = M('apply_agent')->data($data)->add();
            if($res){
                echo "<script>alert('申请成功，请等待客服人员与您联系');location.href='".U('Index/copyPageTwo')."'</script>";
                exit();
            }else{
                echo "<script>alert('申请失败');</script>";
                echo "<script>javascript:history.back(-1);</script>";die;
            }
        }
    }

    public function user_areaapply(){
        if(!I('post.')){
            $this->display();
        }else{
            $province = I('post.province');
            $provinceDataCode = I('post.provinceDataCode');
            $city = I('post.city');
            $cityDataCode = I('post.cityDataCode');
            $area = I('post.area');
            $area = I('post.areaDataCode');
            dump(I('post.'));die;
            if(!$province){
                echo "<script>alert('请选择要代理的省份');</script>";
                echo "<script>javascript:history.back(-1);</script>";die;
            }
            if(!$provinceDataCode){
                echo "<script>alert('系统出错，请重新选择要代理的省份');</script>";
                echo "<script>javascript:history.back(-1);</script>";die;
            }
            if(!$city){
                echo "<script>alert('请选择要代理的城市');</script>";
                echo "<script>javascript:history.back(-1);</script>";die;
            }
            if(!$cityDataCode){
                echo "<script>alert('系统出错，请重新选择要代理的省份');</script>";
                echo "<script>javascript:history.back(-1);</script>";die;
            }
            $userid = session('userid');
            $data['uid']=$userid;
            $data['type']=2;
            $data['province']=$province;
            $data['province_code']=$provinceDataCode;
            $data['city']=$city;
            $data['city_code']=$cityDataCode;
            $date['time']=time();
            $data['status']=0;
            $res = M('apply_agent')->data($data)->add();
            if($res){
                echo "<script>alert('申请成功，请等待客服人员与您联系');location.href='".U('Index/copyPageTwo')."'</script>";
                exit();
            }else{
                echo "<script>alert('申请失败');</script>";
                echo "<script>javascript:history.back(-1);</script>";die;
            }
        }
    }
}