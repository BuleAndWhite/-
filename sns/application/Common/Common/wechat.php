<?php
// +----------------------------------------------------------------------
// | AnzaiSoft [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2015-2016 http://www.anzaisoft.net All rights reserved.
// +----------------------------------------------------------------------
// | Author: ZhangQingYuan <767684610@qq.com>
// +----------------------------------------------------------------------
/**
 * 获取自动回复规则
 * @return array
 * */
function get_current_autoreply_info(){
	$url = "https://api.weixin.qq.com/cgi-bin/get_current_autoreply_info?access_token=".get_access_token();
	return curl_http($url);
}
/**
 * 返回文本格式信息
 * @param $object 消息类型
 * @param $content 消息内容
 * @return 处理过的具有格式的文本消息
 */
function response_text($object,$content){
	$textTpl = "<xml>
				<ToUserName><![CDATA[%s]]></ToUserName>
				<FromUserName><![CDATA[%s]]></FromUserName>
				<CreateTime>%s</CreateTime>
				<MsgType><![CDATA[text]]></MsgType>
				<Content><![CDATA[%s]]></Content>
				<FuncFlag>%d</FuncFlag>
				</xml>";
	$resultStr = sprintf($textTpl, $object->FromUserName, $object->ToUserName, time(), $content, $flag);
	return $resultStr;
}
/**
 * 单图文格式信息
 * @param $object 消息类型
 * @param $newsContent 消息内容
 * @return 处理过的具有格式的图文消息
 */
function response_news($object,$newsContent)
{
	$newsTplHead = "<xml>
				    <ToUserName><![CDATA[%s]]></ToUserName>
				    <FromUserName><![CDATA[%s]]></FromUserName>
				    <CreateTime>%s</CreateTime>
				    <MsgType><![CDATA[news]]></MsgType>
				    <ArticleCount>1</ArticleCount>
				    <Articles>";
	$newsTplBody = "<item>
				    <Title><![CDATA[%s]]></Title>
				    <Description><![CDATA[%s]]></Description>
				    <PicUrl><![CDATA[%s]]></PicUrl>
				    <Url><![CDATA[%s]]></Url>
				    </item>";
	$newsTplFoot = "</Articles>
					<FuncFlag>0</FuncFlag>
				    </xml>";

	$header = sprintf($newsTplHead, $object->FromUserName, $object->ToUserName, time());

	$title = $newsContent['title'];
	$desc = $newsContent['description'];
	$picUrl = $newsContent['picUrl'];
	$url = $newsContent['url'];
	$body = sprintf($newsTplBody, $title, $desc, $picUrl, $url);

	$FuncFlag = 0;
	$footer = sprintf($newsTplFoot, $FuncFlag);

	return $header.$body.$footer;
}
/**
 * 多图文格式信息
 * @param $object 消息类型
 * @param $newsContent 消息内容
 * @return 处理过的具有格式的图文消息
 */
function response_multiNews($object,$newsContent)
{
	$newsTplHead = "<xml>
				    <ToUserName><![CDATA[%s]]></ToUserName>
				    <FromUserName><![CDATA[%s]]></FromUserName>
				    <CreateTime>%s</CreateTime>
				    <MsgType><![CDATA[news]]></MsgType>
				    <ArticleCount>%s</ArticleCount>
				    <Articles>";
	$newsTplBody = "<item>
				    <Title><![CDATA[%s]]></Title>
				    <Description><![CDATA[%s]]></Description>
				    <PicUrl><![CDATA[%s]]></PicUrl>
				    <Url><![CDATA[%s]]></Url>
				    </item>";
	$newsTplFoot = "</Articles>
					<FuncFlag>0</FuncFlag>
				    </xml>";

	$bodyCount = count($newsContent);
	$bodyCount = $bodyCount < 10 ? $bodyCount : 10;

	$header = sprintf($newsTplHead, $object->FromUserName, $object->ToUserName, time(), $bodyCount);

	foreach($newsContent as $key => $value){
		$body .= sprintf($newsTplBody, $value['title'], $value['description'], $value['picUrl'], $value['url']);
	}

	$FuncFlag = 0;
	$footer = sprintf($newsTplFoot, $FuncFlag);

	return $header.$body.$footer;
}
/**
 * 音乐格式信息
 * @param $object 消息类型
 * @param $musicKeyword 消息内容
 * @return 处理过的具有格式的图文消息
 */
function response_music($object,$musicKeyword)
{
	$musicTpl = "<xml>
				 <ToUserName><![CDATA[%s]]></ToUserName>
				 <FromUserName><![CDATA[%s]]></FromUserName>
				 <CreateTime>%s</CreateTime>
				 <MsgType><![CDATA[music]]></MsgType>
				 <Music>
				 <Title><![CDATA[%s]]></Title>
				 <Description><![CDATA[%s]]></Description>
				 <MusicUrl><![CDATA[%s]]></MusicUrl>
				 <HQMusicUrl><![CDATA[%s]]></HQMusicUrl>
				 </Music>
				 <FuncFlag>0</FuncFlag>
				 </xml>";

	$query = "SELECT * FROM tbl_music WHERE music_name LIKE '%$musicKeyword%'";
	$result = _select_data($query);
	$rows = mysql_fetch_array($result, MYSQL_ASSOC);

	$music_id = $rows[music_id];

	if($music_id <> '')
	{
		$music_name = $rows[music_name];
		$music_singer = $rows[music_singer];
		$musicUrl = "http://thinkshare.duapp.com/music/".$music_id.".mp3";
		$HQmusicUrl = "http://thinkshare.duapp.com/music/".$music_id.".mp3";

		$resultStr = sprintf($musicTpl, $object->FromUserName, $object->ToUserName, time(), $music_name, $music_singer, $musicUrl, $HQmusicUrl);
		return $resultStr;
	}else{
		return "";
	}
}
/**
 * 根据OpenID列表群发
 * @param string  $user_data  关注者列表 okO4Dt8cMohAAdnwog0fXkd9tWiE,okO4Dtzdv1w-ZGnQlBlQhSaPADAg
 * @param string $msgtype 群发消息类型
 * @param string $media_id 素材id
 * @return array
 * */
function send_mess($media_id, $msgtype = 'news'){
	$url = "https://api.weixin.qq.com/cgi-bin/message/mass/send?access_token=".get_access_token();
	$user_data = get_user();//array('okO4Dt8cMohAAdnwog0fXkd9tWiE', 'okO4Dtzdv1w-ZGnQlBlQhSaPADAg');
	switch ($msgtype){
		case 'news':
			$msgtype = 'mpnews';
			break;
		default:$msgtype = 'mpnews';break;
	}
	$post_data = array('touser'=>$user_data, 'msgtype' => $msgtype, $msgtype => array('media_id'=>$media_id));
	return curl_http($url, 'POST', json_encode($post_data));
}
/**
 * 根据分组群发
 * @param string $msgtype 群发消息类型
 * @param string $media_id 素材id
 * @param string $group_id 分组编号
 * @param string $send_type 推送类型 1：分组，0：所有人
 * @return array
 * */
function sendall_mess($msgtype = 'news', $media_id, $group_id, $send_type = 0){
	$url = "https://api.weixin.qq.com/cgi-bin/message/mass/sendall?access_token=".get_access_token();
	if(!$send_type){
		$user_data = array('is_to_all'=> true);
	}else{
		$user_data = array('is_to_all'=> false, 'group_id'=>$group_id);
	}
	switch ($msgtype){
		case 'video':
			$msgtype = 'mpvideo';
			break;
		default:$msgtype = 'mpnews';break;
	}
	$post_data = array('filter'=>$user_data, 'msgtype' => $msgtype, $msgtype => array('media_id'=>$media_id));
	return curl_http($url, 'POST', json_encode($post_data));
}
/**
 * 获取用户基本信息
 * @param string $openid 普通用户的标识，对当前公众号唯一 
 * @param string $lang  返回国家地区语言版本，zh_CN 简体，zh_TW 繁体，en 英语 
 * @return array
 * */
function get_userinfo($openid, $lang = 'zh_CN'){
	$url = "https://api.weixin.qq.com/cgi-bin/user/info?access_token=".get_access_token()."&openid=$openid&lang=$lang";
	return curl_http($url);
}
/**
 * 获取关注者列表
 * @param string $next_openid 第一个拉取的OPENID，默认从头开始拉取 
 * @param int $num 初始0，每次获取10000条，递归
 * @return array
 * */
function get_user($next_openid = '', $num = 0){
	$url = "https://api.weixin.qq.com/cgi-bin/user/get?access_token=".get_access_token()."&next_openid=$next_openid";
	$result = curl_http($url);
	$user_list = $result['data']['openid'];
	$num++;
	if( $num  * 10000 < $result['total']){
		$temp_list = get_user($user_list[count($user_list)-1], $num);
		$user_list = array_merge($user_list, $temp_list);
	}
	return $user_list;
}
/**
 * 获取分组列表
 * @param string $next_openid 第一个拉取的OPENID，默认从头开始拉取
 * @return array
 * */
function get_groups(){
	$url = "https://api.weixin.qq.com/cgi-bin/groups/get?access_token=".get_access_token();
	return curl_http($url);
}

/**
 * 获取微信素材
 * @param $material_data type,offset,count
 * @return array
*/
function getbatch_material($material_data) {
    $url = "https://api.weixin.qq.com/cgi-bin/material/batchget_material?access_token=".get_access_token();
    $material_data = urldecode(json_encode($material_data));
    return curl_http($url, 'POST', $material_data);
}
/**
 * 获取永久素材
 * @param string $media_id 素材id
 * @return array
 */
function get_material($media_id) {
    $url = "https://api.weixin.qq.com/cgi-bin/material/get_material?access_token=".get_access_token();
    $media_data = json_encode(array(
        "media_id" => $media_id
    ));
    return curl_http($url, 'POST', $media_data);
}
/**
 *获取临时素材
 */
function get_media($media_id) {
    $url = "https://api.weixin.qq.com/cgi-bin/media/get?access_token=".get_access_token()."&media_id=$media_id";
    return curl_http($url);
}
/**
 *创建微信菜单
 */
function create_menu($menu_data) {
    $url = "https://api.weixin.qq.com/cgi-bin/menu/create?access_token=".get_access_token();
    $menu_json = urldecode(json_encode(array(
        'button' => $menu_data
    )));
    return curl_http($url, 'POST', $menu_json);
}
/**
 *清除菜单
 */
function del_menu() {
    $url = "https://api.weixin.qq.com/cgi-bin/menu/delete?access_token=".get_access_token();
    return curl_http($url);
}
/**
 * 获取授权token
 * @param boolean $flag 标记变量
 * @return string
 */
function get_access_token($flag = false) {
	if(!isset($_SESSION['ADMIN_ID']) || !isset($_SESSION['TOKEN'])){
		//return '';
	}
	$weixin_options = M('weixin_options')->where(array('app_wxid'=>session('TOKEN')))->find();
	$app_id = $weixin_options['app_id'];
	$app_secret = $weixin_options['app_secret'];
    $access_token = S('weixin_access_token_'.$_SESSION['TOKEN']);
    if ($access_token && !$flag) { //已缓存，直接使用
        return $access_token;
    } else { //获取access_token
        $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid={$app_id}&secret={$app_secret}";
        $access_token = curl_http($url);
        S('weixin_access_token_'.$_SESSION['TOKEN'], $access_token['access_token'], 7200);
        return $access_token['access_token'];
    }
}
/**
* curl	请求
* @param string $url 请求地址
* @param string $method 请求方式 post,get
* @param string $post_data post数据
* @return array or string
*/
function curl_http($url, $method, $post_data, $is_file = false, $filename = '') {
	if (! $is_file && is_array ( $post_data )) {
		$post_data = JSON( $post_data );
	}
	if ($is_file) {
		if($filename){
			$filename = end(explode('/', $filename));
			$header [] = "content-type: multipart/form-data; filename=$filename;charset=UTF-8";
		}else{
			$header [] = "content-type: multipart/form-data; charset=UTF-8";
		}
		
	} else {
		$header [] = "content-type: application/json; charset=UTF-8";
	}
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
	curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
	curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
	switch ($method) {
		case 'POST':
			curl_setopt($ch, CURLOPT_POST, true);
			if (!empty($post_data)) {
				curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
			}
			break;
	}
	curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
	curl_setopt($ch, CURLOPT_URL, $url);
	$result = curl_exec($ch);
	$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
	curl_close($ch);
	$result = is_null(json_decode($result, true)) ? $result : json_decode($result, true);
	/* if ($result['errcode'] == '40001') {
		$access_token = get_access_token(true);
		return curl_http();
	} */
	return $result;
}
/**
 * 其它版本
 * 使用方法：
 * $post_string = "app=request&version=beta";
 * request_by_other('http://facebook.cn/restServer.php',$post_string);
 */
function request_by_other($remote_server, $post_string)
{
	$context = array(
			'http' => array(
					'method' => 'POST',
					'header' => 'Content-type: application/x-www-form-urlencoded' .
					'\r\n'.'User-Agent : Jimmy\'s POST Example beta' .
					'\r\n'.'Content-length:' . strlen($post_string) + 8,
					'content' => 'mypost=' . $post_string)
	);
	$stream_context = stream_context_create($context);
	$data = file_get_contents($remote_server, false, $stream_context);
	return $data;
}
/**
 * Curl版本
 * 使用方法：
 * $post_string = "app=request&version=beta";
 * request_by_curl('http://facebook.cn/restServer.php',$post_string);
 */
function request_by_curl($remote_server, $post_string)
{
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $remote_server);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $post_string);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_USERAGENT, "Jimmy's CURL Example beta");
	$data = curl_exec($ch);
	curl_close($ch);
	return $data;
}
/**
 * Socket版本
 * 使用方法：
 * $post_string = "app=socket&version=beta";
 * request_by_socket('facebook.cn','/restServer.php',$post_string);
 */
function request_by_socket($remote_server, $remote_path, $post_string, $port = 8080, $timeout = 30)
{
	$socket = fsockopen($remote_server, $port, $errno, $errstr, $timeout);
	if (!$socket) die("$errstr($errno)");

	fwrite($socket, "POST $remote_path HTTP/1.0\r\n");
	fwrite($socket, "User-Agent: Socket Example\r\n");
	fwrite($socket, "HOST: $remote_server\r\n");
	fwrite($socket, "Content-type: application/x-www-form-urlencoded\r\n");
	fwrite($socket, "Content-length: " . (strlen($post_string) + 8) . '\r\n');
	fwrite($socket, "Accept:*/*\r\n");
	fwrite($socket, "\r\n");
	fwrite($socket, "mypost=$post_string\r\n");
	fwrite($socket, "\r\n");
	$header = "";
	while ($str = trim(fgets($socket, 4096))) {
		$header .= $str;
	}
	$data = "";
	while (!feof($socket)) {
		$data .= fgets($socket, 4096);
	}
	return $data;
}
function put_post(){
	//@file phpinput_post.php
	$http_entity_body = 'n=' . urldecode('perfgeeks') . '&p=' . urldecode('7788');
	$http_entity_type = 'application/x-www-form-urlencoded';
	$http_entity_length = strlen($http_entity_body);
	$host = '192.168.0.6';
	$port = 80;
	$path = '/phpinput_server.php';
	$fp = fsockopen($host, $port, $error_no, $error_desc, 30);
	if ($fp) {
		fputs($fp, "POST {$path} HTTP/1.1\r\n");
		fputs($fp, "Host: {$host}\r\n");
		fputs($fp, "Content-Type: {$http_entity_type}\r\n");
		fputs($fp, "Content-Length: {$http_entity_length}\r\n");
		fputs($fp, "Connection: close\r\n\r\n");
		fputs($fp, $http_entity_body . "\r\n\r\n");
	
		while (!feof($fp)) {
			$d .= fgets($fp, 4096);
		}
		fclose($fp);
		echo $d;
	}
	
}
function JSON($array) {
	arrayRecursive ( $array, 'urlencode', true );
	$json = json_encode ( $array );
	return urldecode ( $json );
}
/**
* ************************************************************
*
* 使用特定function对数组中所有元素做处理
*
* @param
*        	string &$array 要处理的字符串
* @param string $function
*        	要执行的函数
* @return boolean $apply_to_keys_also 是否也应用到key上
* @access public
* ***********************************************************
*/
function arrayRecursive(&$array, $function, $apply_to_keys_also = false) {
	static $recursive_counter = 0;
	if (++ $recursive_counter > 1000) {
		die ( 'possible deep recursion attack' );
	}
	foreach ( $array as $key => $value ) {
		if (is_array ( $value )) {
			arrayRecursive ( $array [$key], $function, $apply_to_keys_also );
		} else {
			$array [$key] = $function ( $value );
		}

		if ($apply_to_keys_also && is_string ( $key )) {
			$new_key = $function ( $key );
			if ($new_key != $key) {
				$array [$new_key] = $array [$key];
				unset ( $array [$key] );
			}
		}
	}
	$recursive_counter --;
}
/**
 * @param string $token 微信开发者token验证
 * @return bool
 */
function checkSignature($token)
{
	$signature = $_GET["signature"];
	$timestamp = $_GET["timestamp"];
	$nonce = $_GET["nonce"];

	$tmpArr = array($token, $timestamp, $nonce);
	// use SORT_STRING rule
	sort($tmpArr, SORT_STRING);
	$tmpStr = implode( $tmpArr );
	$tmpStr = sha1( $tmpStr );

	if( $tmpStr == $signature ){
		return true;
	}else{
		return false;
	}
}