<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2015-2016 http://www.anzaisoft.net All rights reserved.
// +----------------------------------------------------------------------
// | Author: ZhangQingYuan <767684610@qq.com>
// +----------------------------------------------------------------------
/**
 * 微信接口程序
 */
namespace Wechat\Controller;
use Common\Controller\WeixinbaseController;
class IndexController extends WeixinbaseController {
	function _initialize() {
		parent::_initialize();
		$this->assign('_wechat_user_info', $this->_wechat_user_info);
	}
	//验证微信用户，进入活动首页
	public function index() {
		if(IS_POST){
			dump($_FILES['pic']);
			dump(exif_read_data($_FILES['pic']['tmp_name']));
		}
		$this->display(":index");
	}
	//微信2.0网页授权
	public function oauth2(){
	if (isset($_GET['code'])){
			$code = $_GET['code'];
			$url = "https://api.weixin.qq.com/sns/oauth2/access_token?appid=".C('APP_ID')."&secret=".C('APP_SECRET')."&code={$code}&grant_type=authorization_code";
			$data = curl_http($url);
			if(isset($data['errcode'])){
				\Think\Log::write(json_encode($data));
				header('Location:'.U('Wechat/Index/index'));exit;
			}
			$url = "https://api.weixin.qq.com/sns/userinfo?access_token=".$data['access_token']."&openid=".$data['openid'].'&lang=zh_CN';
			$result = curl_http($url);
			$info = M('oauth_user')->where(array('openid' => $result['openid']))->find();
			if(!$info)
			{
				$data['from'] = 'wechat';
				$data['status'] = 0;
				$data['name'] = $result['nickname'];
				$data['create_time'] = date('Y-m-d H:i');
				$data['last_login_time'] = date('Y-m-d H:i');
				$data['last_login_ip'] = get_client_ip();
				$data['head_img'] = $result['headimgurl'];
				$data['openid'] = $result['openid'];
				$data['init_degree'] = rand(100, 120);
				$data['wechat_user_options'] = json_encode($result);
				$param['id'] = M('oauth_user')->add($data);
				$param['nickname'] = $result['nickname'];
				$param['openid'] = $result['openid'];
				$param['head_img'] = $result['headimgurl'];
				$param['init_degree'] = $data['init_degree'];
			}else{
				$data['login_times'] = array('exp', 'login_times+1');
				$data['last_login_time'] = date('Y-m-d H:i');
				$data['last_login_ip'] = get_client_ip();
				M('oauth_user')->where(array('openid' => $info['openid']))->save($data);
				$param['id'] = $info['id'];
				$wechat_user_options = json_decode($info['wechat_user_options'], true);
				$param['nickname'] = $wechat_user_options['nickname'];
				$param['openid'] = $info['openid'];
				$param['head_img'] = $info['head_img'];
				$param['init_degree'] = $info['init_degree'];
				$param['mobile'] = $info['mobile'];
			}
			session('wechat_user_info', $param);
			redirect(session('redirect_url'));
		}
	}
}