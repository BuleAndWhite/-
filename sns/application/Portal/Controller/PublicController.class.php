<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2015-2016 http://www.anzaisoft.net All rights reserved.
// +----------------------------------------------------------------------
// | Author: ZhangQingYuan <767684610@qq.com>
// +----------------------------------------------------------------------
namespace Portal\Controller;
use Common\Controller\HomebaseController;
class PublicController extends HomebaseController {
    public function get_pay_code($content){
    	vendor("phpqrcode.phpqrcode");
    	$data = $content;
    	// 纠错级别：L、M、Q、H
    	$level = 'L';
    	// 点的大小：1到10,用于手机端4就可以了
    	$size = 4;
    	// 下面注释了把二维码图片保存到本地的代码,如果要保存图片,用$fileName替换第二个参数false
    	//$path = "images/";
    	// 生成的文件名
    	//$fileName = $path.$size.'.png';
    	\QRcode::png($data, false, $level, $size);
    }
    //登录
    public function login(){
    	if(isset($_SESSION['user_email'])){
    		$this->redirect("Order/index");
    
    	}else{
    		$this->display(":login1");
    		 
    	}
    }
    //注册
    public function register(){
    	if(isset($_SESSION['user_email'])){
    		$this->redirect("Order/index");
    
    	}else{
    		$this->display("register");
    		 
    	}
    }
    public function send_find_pwd_mess(){
    	!IS_AJAX && exit;
    	$data['mobile'] = I('mobile');
    	if(!M('users')->where($data)->find()){
    		$this->error('手机号不存在！');
    	}
    	
    	$data['number'] = rand_number(1000, 999999);
    	$data['time'] = time() + 300;
    	session('find_pwd_info', $data);
    	$send_content = '您的验证码是：'.$data['number'].'。请不要把验证码泄露给其他人。';
    	$status = sendsms($data['mobile'], $send_content);
    	if($status == 1){
    		$this->success('验证码发送成功！');
    	}
    	$this->error($status);
    }
    public function send_mess(){
    	!IS_AJAX && exit;
    	$data['mobile'] = I('mobile');
    	if(M('users')->where($data)->find()){
    		if(!session('?wechat_user_info')){
	    		$this->error('该手机号已注册！');
    		}
    	}
    	$data['number'] = rand_number(1000, 999999);
    	$data['time'] = time() + 300;
    	session('register_info', $data);
    	$send_content = '您的验证码是：'.$data['number'].'。请不要把验证码泄露给其他人。';
    	$status = sendsms($data['mobile'], $send_content);
    	if($status == 1){
	    	$this->success('验证码发送成功！');    		
    	}
    	$this->error($status);
    }
}


