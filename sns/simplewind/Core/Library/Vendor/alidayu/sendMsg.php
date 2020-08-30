<?php
/**
 * @author LiuR_Fun
 * @date 2016-02-02
 * @version 1.0
 * @email admin@lrfun.com
 */
	include "TopSdk.php";
	$code = "www.lrfun.com";
	$c = new TopClient;
	$c->appkey = $appkey;
	$c->secretKey = $secret;
	$c->format = "json";
	$req = new AlibabaAliqinFcSmsNumSendRequest;
	$req->setSmsType("normal");
	$req->setSmsFreeSignName("注册验证");
	$req->setSmsParam("{\"code\":\"".$code."\",\"product\":\"LiuR_Fun\",\"item\":\"LiuR_Fun\"}");
	$req->setRecNum("13000000000");
	$req->setSmsTemplateCode("SMS_585014");
	$resp = $c->execute($req);
	$arr = objectArray(resp);
	print_r($arr);

	//stdClass Object 转 数组
	function objectArray($array){
		if(is_object($array)){
			$array = (array)$array;
		}
		if(is_array($array)){
			foreach($array as $key=>$value){
				$array[$key] = objectArray($value);
			}
		}
		return $array;
	}
?>