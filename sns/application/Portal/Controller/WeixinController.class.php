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
class WeixinController extends HomebaseController {
	protected $app_token;
    protected $weixin_options;
    function _initialize() {
        parent::_initialize();
        $this->weixin_options = M('weixin_options')->where(array('id'=>I('get.id')))->find();
        $this->app_token = $this->weixin_options['app_token'];
    }
    /**
     *微信接口
     */
    public function index(){
    	if(isset($_GET['echostr'])){
			$echoStr = $_GET["echostr"];
			if(checkSignature($this->app_token)){
				echo $echoStr;
				exit;
			}
		}else{
    		$this->responseMsg();
    	}
    }
    public function test(){
    	if (isset($_SERVER['HTTP_ORIGIN'])) {
    		header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
    		header('Access-Control-Allow-Credentials: true');
    		header('Access-Control-Max-Age: 86400');    // cache for 1 day
    	}
    	
    	// Access-Control headers are received during OPTIONS requests
    	if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    	
    		if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
    			header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
    	
    		if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
    			header("Access-Control-Allow-Headers:        {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");
    	
    		exit(0);
    	}
    	$this->ajaxReturn(array('totalItems'=>100,'id'=>$_REQUEST['id']));
    }
    
    
    
    
    public function user(){
        
        $url="https://open.weixin.qq.com/connect/oauth2/authorize?appid=".C('appid')."&redirect_uri=".C('huidiao')."&response_type=code&scope=snsapi_userinfo&state=yah#wechat_redirect";
     //  echo $url;
      header('Location:'.$url);
    
 // dump($rs);
    }
    
    
    public function usertemp2(){
        header("content-type:text/html; charset=utf-8");
           $url='https://api.weixin.qq.com/sns/oauth2/access_token?appid='.C("appid").'&secret='.C('secret').'&code='.$_REQUEST['code'].'&grant_type=authorization_code';
        $res = file_get_contents($url); //获取文件内容或获取网络请求的内容
                //echo $res;
        $result = json_decode($res, true); //接受一个 JSON 格式的字符串并且把它转换为 PHP 变量
        $access_token = $result['access_token'];
        $openid = $result['openid'];
        $refresh_token = $result['refresh_token'];
        $expires_in = $result['expires_in'];
        $scope = $result['scope'];
        $url2='https://api.weixin.qq.com/sns/userinfo?access_token='.$access_token.'&openid='.$openid.'&lang=zh_CN';
        $user = file_get_contents($url2);
        $users = json_decode($user, true);
      /*   $data['openid'] = $users['openid'];
        $data['nickname'] = $users['nickname'];
        $data['sex'] = $users['sex'];
        $data['headimgurl'] = $users['headimgurl'];
        $data['city'] = $users['city'];
        $data['province'] = $users['province'];
        $data['country'] = $users['country']; */
        
        return $user;
        
        
    }
    public function wechat_oauth2(){
    	if (isset($_GET['code'])){
    		$code = $_GET['code'];
    		$url = "https://api.weixin.qq.com/sns/oauth2/access_token?appid=".$this->weixin_options['app_id']."&secret=".$this->weixin_options['app_sercet']."&code={$code}&grant_type=authorization_code";
    		$data = file_get_contents($url);
    		//$url = "https://api.weixin.qq.com/cgi-bin/user/info?access_token=".get_access_token(true)."&openid=okO4Dt8cMohAAdnwog0fXkd9tWiE&lang=zh_CN";
    		//dump(curl_http($url));
    		$url = "https://api.weixin.qq.com/sns/userinfo?access_token=".$data['access_token']."&openid=".$data['openid'];
    		return file_get_contents($url);
    	}
    }
    /**
     * 定时群发
     * */
    public function send_mess(){
    	$list = array('139.196.38.222','127.0.0.1','101.81.52.17');
    	if(!in_array($_SERVER['REMOTE_ADDR'],$list)){
			//exit('You don\'t have permission to access!');
    	}
    	$condition['status'] = 0;
    	$condition['sendtime'] = strtotime(date('Y-m-d H:i'));
    	$send_list = M('send')->where($condition)->select();
    	if(!$send_list){
    		exit('No data!');
    	}
    	foreach ($send_list as $k => $v){
    		session('TOKEN',$v['token']);
    		$result = sendall_mess($v['msgtype'], $v['media_id'], $v['group_id'], $v['send_type']);
    		if($result['errcode'] == '0'){
    			M('send')->where("id=".$v['id'])->setField('status',1);
    		}else{
    			\Think\Log::write(json_encode($result));
    		}
    	}
    }
    public function oauth2(){
    	if (isset($_GET['code'])){
    		$code = $_GET['code'];
    		$url = "https://api.weixin.qq.com/sns/oauth2/access_token?appid=wxa2439ea67ee3d383&secret=38f39f6d6ea19ef4d68bac45038011c1&code={$code}&grant_type=authorization_code";
    		$data = curl_http($url);
    		//$url = "https://api.weixin.qq.com/cgi-bin/user/info?access_token=".get_access_token(true)."&openid=okO4Dt8cMohAAdnwog0fXkd9tWiE&lang=zh_CN";
    		//dump(curl_http($url));
    		$url = "https://api.weixin.qq.com/sns/userinfo?access_token=".$data['access_token']."&openid=".$data['openid'];
    		dump(curl_http($url));
    	}else{
    		echo "NO CODE";
    	}
    }
    /**
     * 显示图片
     */
    public function weixin_thumb($mid) {
    	//$data = $this->weixin->get_material($mid);
    	//$base64 = is_null(json_decode($data, true)) ? base64_encode($data) : $data;
    	$thumb_data = get_material($mid);
    	if(is_array($thumb_data)){
    		echo json_encode($thumb_data);
    	}else{
    		header('Content-type: image/jpg');
    		echo $thumb_data;
    	}
    }
    public function responseMsg()
    {
    	$postStr = $GLOBALS["HTTP_RAW_POST_DATA"];
    
    	//extract post data
    	if (!empty($postStr)){
    
    		$postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
    		$RX_TYPE = trim($postObj->MsgType);
    
    		switch($RX_TYPE)
    		{
    			case "text":
    				$resultStr = $this->handleText($postObj);
    				break;
    			case "event":
    				$resultStr = $this->handleEvent($postObj);
    				break;
    			default:
    				$resultStr = "Unknow msg type: ".$RX_TYPE;
    				break;
    		}
    		echo $resultStr;
    	}else{
    		echo "";
    		exit;
    	}
    }
    
    private function handleText($postObj)
    {
    	$keyword = trim($postObj->Content);
    
    	if(!empty( $keyword ))
    	{
    		$contentStr = $this->wechat_reply($keyword);
    		//$resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
    		$resultStr = response_text($postObj,$contentStr);
    		echo $resultStr;
    	}else{
    		echo "";
    	}
    }
    private function wechat_reply($keyword){
    	return '请拨打64783333咨询。';//$keyword;
    }
    private function handleEvent($object)
    {
    	$contentStr = "";
    	$weixin_info = json_decode($this->weixin_options['welcome_config'], true);
    	switch ($object->Event)
    	{
    		case "subscribe":
    			$contentStr = stripslashes(htmlspecialchars_decode($weixin_info['content']));
    			if($weixin_info['send_type']){
    				$newsContent['title'] = $weixin_info['title'];
    				$newsContent['description'] = $contentStr;
    				$newsContent['picUrl'] = $weixin_info['image_url'];
    				$newsContent['url'] = $weixin_info['url'];
    				$resultStr = response_news($object, $newsContent);
    			}else{
    				
    				$resultStr = response_text($object, $contentStr);
    			}
    			break;
    		default :
    			$contentStr = "Unknow Event: ".$object->Event;
    			break;
    	}
    	return $resultStr;
    }
}


