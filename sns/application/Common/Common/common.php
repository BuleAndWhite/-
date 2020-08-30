<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2015-2016 http://www.anzaisoft.net All rights reserved.
// +----------------------------------------------------------------------
// | Author: ZhangQingYuan <767684610@qq.com>
// +----------------------------------------------------------------------

/*
 * 
 * 发送短信
 * 
 * 
 */
function sendsms($tel,$content){

	$url="http://106.ihuyi.cn/webservice/sms.php?method=Submit";
	$post_data = "account=cf_jiro01&password=yah136717&mobile=".$tel."&content=".$content;
	
	$gets =xml_to_array(Post($post_data, $url));
	//   $gets['SubmitResult']['code']=2;
	if($gets['SubmitResult']['code']==2){
	
		return true;
		 
	}else{
		return $gets['SubmitResult']['msg'];
		
	}
	
}



 function Post($curlPost,$url){
	$curl = curl_init();
	curl_setopt($curl, CURLOPT_URL, $url);
	curl_setopt($curl, CURLOPT_HEADER, false);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($curl, CURLOPT_NOBODY, true);
	curl_setopt($curl, CURLOPT_POST, true);
	curl_setopt($curl, CURLOPT_POSTFIELDS, $curlPost);
	$return_str = curl_exec($curl);
	curl_close($curl);
	return $return_str;
}



function xml_to_array($xml){
	$reg = "/<(\w+)[^>]*>([\\x00-\\xFF]*)<\\/\\1>/";
	if(preg_match_all($reg, $xml, $matches)){
		$count = count($matches[0]);
		for($i = 0; $i < $count; $i++){
			$subxml= $matches[2][$i];
			$key = $matches[1][$i];
			if(preg_match( $reg, $subxml )){
				$arr[$key] =xml_to_array( $subxml );
			}else{
				$arr[$key] = $subxml;
			}
		}
	}
	return $arr;
}




/**
 * 建立请求，以表单HTML形式构造（默认）
 * @param $param 请求参数数组
 * @param $method 提交方式。两个值可选：post、get
 * @param $button_name 确认按钮显示文字
 * @return 提交表单HTML文本
 */
function build_request_form($param, $method = 'POST', $button_name='提交') {
	$sHtml = '<!DOCTYPE html>

<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width,initial-scale=1,user-scalable=0">

<title>在线订票</title>

<link rel="stylesheet" href="'.__ROOT__.'/public/css/weui.min.css"/><div id="loadingToast" class="weui_loading_toast" style="">

</head>

<body>
    <div class="weui_mask_transparent"></div>
    <div class="weui_toast">
        <div class="weui_loading">
            <div class="weui_loading_leaf weui_loading_leaf_0"></div>
            <div class="weui_loading_leaf weui_loading_leaf_1"></div>
            <div class="weui_loading_leaf weui_loading_leaf_2"></div>
            <div class="weui_loading_leaf weui_loading_leaf_3"></div>
            <div class="weui_loading_leaf weui_loading_leaf_4"></div>
            <div class="weui_loading_leaf weui_loading_leaf_5"></div>
            <div class="weui_loading_leaf weui_loading_leaf_6"></div>
            <div class="weui_loading_leaf weui_loading_leaf_7"></div>
            <div class="weui_loading_leaf weui_loading_leaf_8"></div>
            <div class="weui_loading_leaf weui_loading_leaf_9"></div>
            <div class="weui_loading_leaf weui_loading_leaf_10"></div>
            <div class="weui_loading_leaf weui_loading_leaf_11"></div>
        </div>
        <p class="weui_toast_content">数据加载中</p>
    </div>
</div>';
	//待请求参数数组
	$sHtml .= "<form style='display:none;' id='alipaysubmit' action='".C('URL_ADD_ONLINE_ORDER')."' method='".$method."'>";
	foreach ($param as $k => $v) {
		$sHtml.= "<input type='hidden' name='".$k."' value='".htmlspecialchars($v)."'/>";
	}
	//submit按钮控件请不要含有name属性
	$sHtml = $sHtml."<input type='submit' value='".$button_name."'></form>";

	$sHtml = $sHtml."<script>document.getElementById('alipaysubmit').submit();</script>";
	$sHtml .= '</body></html>';
	return $sHtml;
}
/**
 * 获取sign签名
 * @param json 签名json
 * @return sign
 */
function get_sign($json){
	//if(empty($json))return false;
	$url = C('URL_SIGN');
	$private_key = C('PRIVATE_KEY');
	return file_get_contents($url."?key=".urlencode($private_key)."&content=".$json);
}
//请求订单
function create_new_order($post_data){
	return curl_http(C('URL_CREATE_NEW_ORDER'), 'POST', $post_data);
}
//生成订单号
function get_order_id(){
	$order_id = date("ym") . substr(time(), -1) . substr(microtime(), 2, 5) . sprintf('%02d', rand(0, 99));
	if(M('order')->where(array('order_id'=>$order_id, 'uid'=> session('uid')))->find()){
		$order_id = get_order_id();
	}
	return $order_id;
}
function api_get_access_token(){
	$json = '{}';
	$param['json'] = $json;
	$param['deviceno'] = C('DEVICENO');
	$param['sign'] = get_sign($json);
	$result = curl_http(C('URL_GET_ACCESS_TOKEN'), 'POST', $param);
	if($result['success'] != 1){
		$result = api_get_access_token();
	}
	return $result['accesstoken'];
}
// php获取当前访问的完整url地址
function GetCurUrl() {
	$url = 'http://';
	if (isset ( $_SERVER ['HTTPS'] ) && $_SERVER ['HTTPS'] == 'on') {
		$url = 'https://';
	}
	if ($_SERVER ['SERVER_PORT'] != '80') {
		$url .= $_SERVER ['HTTP_HOST'] . ':' . $_SERVER ['SERVER_PORT'] . $_SERVER ['REQUEST_URI'];
	} else {
		$url .= $_SERVER ['HTTP_HOST'] . $_SERVER ['REQUEST_URI'];
	}
	// 兼容后面的参数组装
	if (stripos ( $url, '?' ) === false) {
		$url .= '?t=' . time ();
	}
	return $url;
}
// 获取当前用户的OpenId
function get_openid($openid = NULL) {
	$token = get_token1 ();
	if ($openid !== NULL && $openid != '-1') {
		session ( 'openid_' . $token, $openid );
	} elseif (! empty ( $_REQUEST ['openid'] ) && $_REQUEST ['openid'] != '-1' && $_REQUEST ['openid'] != '-2') {
		session ( 'openid_' . $token, $_REQUEST ['openid'] );
	}
	$openid = session ( 'openid_' . $token );
	if ((empty ( $openid ) || $openid == '-1') && $_REQUEST ['openid'] != '-2' && IS_GET) {
		$callback = GetCurUrl ();
		//dump($callback);exit;
		OAuthWeixin ( $callback, $token );
	}
	if (empty ( $openid )) {
		return '-1';
		// exit ( 'openid获取失败error' );
	}
	return $openid;
}
function OAuthWeixin($callback, $token = '') { // echo '444';
		$callback = urldecode ( $callback );
		if (strpos ( $callback, '?' ) === false) {
			$callback .= '?';
		} else {
			$callback .= '&';
		}
		$param ['appid'] = 'wx43b0f4e0bc3299d0';
		if (! isset ( $_GET ['getOpenId'] )) {
			$param ['redirect_uri'] = $callback . 'getOpenId=1';
			$param ['response_type'] = 'code';
			$param ['scope'] = 'snsapi_base';//snsapi_base';
			$param ['state'] = 123;
			$url = 'https://open.weixin.qq.com/connect/oauth2/authorize?' . http_build_query ( $param ) . '#wechat_redirect';
// 			dump($url);exit;
			redirect ( $url );
		} elseif ($_GET ['state']) {
			$param ['secret'] = 'e17528da8143932c99d6f61bab3c2d61';
			$param ['code'] = I ( 'code' );
			$param ['grant_type'] = 'authorization_code';
			$url = 'https://api.weixin.qq.com/sns/oauth2/access_token?' . http_build_query ( $param );
// 			dump($param);exit;
			$content = file_get_contents ( $url );
			$content = json_decode ( $content, true );
			redirect ( $callback . 'openid=' . $content ['openid'] );
		}
}
// 获取当前用户的Token

function get_token1($token=NULL) {
	return 'ypchUqSsjFYRk9Gz8CXxe3syoiHVP832hm8Kyqp5vto_qeWpAIN0VXlRKwDZbsS5-FVhwPCEShAlk0izieceXg-_IYJOCAzsA2wl1qHi5KHDcEuAqJXS16RxT0GWfHmNRMJiAEAJEB';
	if($token!==NULL){
		session ( 'TOKEN', $token );
	}elseif (! empty ( $_REQUEST ['token'] )) {
		session ( 'TOKEN', $_REQUEST ['token'] );
	}
	$token = session ( 'TOKEN' );
	if (empty ( $token )) {
		return - 1;
	}
	return $token;
}
//价格保留两位小数
function format_price($price){
	return sprintf("%.2f", $price);
}
//php模拟post提交
function curl_post($url, $param){
	$options = array(
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_HEADER         => false,
			CURLOPT_POST           => true,
			CURLOPT_POSTFIELDS     => $param,
	);
	$ch = curl_init($url);
	curl_setopt_array($ch, $options);
	$result = curl_exec($ch);
	curl_close($ch);
	return json_decode($result, true);
}

//取得订单支付的参数集
function pay_param($price = '0.01', $orderno = '', $from_type = 0, $paycode = 'wechatonline'){
	$json = '{price:'.$price.',paycode:"'.$paycode.'",consumerauthcode:"",userid:"'.C('USER_ID').'",orderno:"'.$orderno.'"}';
	$param['json'] = $from_type ? addslashes($json) : $json;
	$param['deviceno'] = C('DEVICENO');
	$param['sign'] = get_sign($json);
	$param['openid'] = session('?wechat_user_info.openid') ? session('wechat_user_info.openid') : '';//"oAbzrwz5d4XQXgW2aTVz20TSHbhw";
	return $param;
}

//取得票务系统的取票密码
function get_order_numbers($ticket_type, $ticket_count, $username, $mobile, $set_id = '0', $order_type = '1', $order_id, $pay_type){
	/*
	$param = array(
			'TicketType' => 1,				//测试票种id，正式上线需要变回$ticket_type
			'TicketCount' => $ticket_count,	//数量
			'UserName' => $username,
			'MobilePhone' => $mobile,
			'SetID' => 3,					//测试预售票，写死3，预售票id，正式上线变回$setid
			'OrderType' => $order_type,		//预售票2或者当日票1
			'OrderId' => $order_id,			//订单id，购票站点id，非支付订单orderno
			'PayType' => $pay_type,			//支付方式 微信2，支付宝1
			'ak' => 'DHYEUIYTQWER'			//秘钥，固定不变
	);
	$param = array(
			'TicketType' => 1,				//测试票种id，正式上线需要变回$ticket_type
			'TicketCount' => 10,	//数量
			'UserName' => "111111",
			'MobilePhone' => "13636690679",
			'SetID' => 3,					//测试预售票，写死3，预售票id，正式上线变回$setid
			'OrderType' => 2,		//预售票2或者当日票1
			'OrderId' => "NO20160061109300211",			//订单id，购票站点id，非支付订单orderno
			'PayType' => 1,			//支付方式 微信2，支付宝1
			'ak' => ''			//秘钥，固定不变
	);
	$json = '{ "TicketType": "1","TicketCount": "5","UserName": "'.$username.'","MobilePhone": "'.$mobile.'","SetID": "'.$set_id.'","OrderType": "'.$order_type.'","OrderID": "'.$order_id.'","PayType": "'.$pay_type.'","ak": ""}';
	*/
	$json = '{ "TicketType": "'.$ticket_type.'","TicketCount": "'.$ticket_count.'","UserName": "'.$username.'","MobilePhone": "'.$mobile.'","SetID": "'.$set_id.'","OrderType": "'.$order_type.'","OrderID": "'.$order_id.'","PayType": "'.$pay_type.'","ak": ""}';
	/*
	$param = json_encode($param);
	echo $param;
	echo '<br/>';
	echo $json;
	dump($param);
	dump(file_get_contents(C('URL_CREATE_NEW_ORDER').'?json='.urlencode($json)));
	ECHO $result;
	*/
	$result = file_get_contents(C('URL_CREATE_NEW_ORDER').'?json='.urlencode($json));
	$result = json_decode(str_replace('(', '', str_replace(')', '', $result)), true);
	if($result['status'] == '0'){
		return explode(',', str_replace('{', '', str_replace('}', '', $result['TicketNumbers'])));
	}
	return false;
}
//获取一定范围内的随机数字 位数不足补零
function rand_number ($min, $max) {
	return sprintf("%0".strlen($max)."d", mt_rand($min,$max));
}
//根据uid获取信息
function get_user_info($uid, $field = 'mobile'){
	return M('users')->where(array('id' => $uid))->getField($field);
}
//根据id获取ticket信息
function get_ticket_info($ticket_id, $field = 'name'){
	return M('ticket')->where(array('ticket_id' => $ticket_id))->getField($field);
}
//根据id获取ticket信息
function get_ticket_info1($ticket_id, $field = 'name'){
	return M('ticket')->where(array('id' => $ticket_id))->getField($field);
}
//数组转换为字符串，带单引号
function convert_strring($arr){
    $tempstr = '';
    foreach ($arr as $v){
        $tempstr .= $tempstr ? ",'".$v."'" : "'".$v."'";
    }
    return $tempstr;
}
//用户信息
function wechat_info($wechat_user_options, $key = 'nickname', $openid = ''){
	if($openid){
		$wechat_user_options = M('oauth_user')->where(array('openid' => $openid))->getField('wechat_user_options');
		$result = json_decode($wechat_user_options, true);
		return $result;
	}
	$result = json_decode($wechat_user_options, true);
	return $result[$key];
}
/**
 * 获取当前页面完整URL地址
 */
function get_url() {
	$sys_protocal = isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == '443' ? 'https://' : 'http://';
	$php_self = $_SERVER['PHP_SELF'] ? $_SERVER['PHP_SELF'] : $_SERVER['SCRIPT_NAME'];
	$path_info = isset($_SERVER['PATH_INFO']) ? $_SERVER['PATH_INFO'] : '';
	$relate_url = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : $php_self.(isset($_SERVER['QUERY_STRING']) ? '?'.$_SERVER['QUERY_STRING'] : $path_info);
	return $sys_protocal.(isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '').$relate_url;
}
//获取订单状态
function get_order_status($status){
	$order_status = C('ORDER_STATUS');
	return $order_status[$status];
}