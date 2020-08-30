<?php
namespace Common\Controller;
use Common\Controller\HomebaseController;
class WeixinbaseController extends HomebaseController{
	public $_wechat_user_info;
	function _initialize() {
		parent::_initialize();
		if(!session('?wechat_user_info') && !isset($_GET['code'])){
			session('redirect_url', get_url());
		    $url="https://open.weixin.qq.com/connect/oauth2/authorize?appid=".C('APP_ID')."&redirect_uri=http://".$_SERVER['SERVER_NAME']."/index.php/Wechat/Index/oauth2&response_type=code&scope=snsapi_userinfo&state=zqy#wechat_redirect";
			//header('Location:'.$url);
		}
		$this->_wechat_user_info = session('wechat_user_info');
	}
}