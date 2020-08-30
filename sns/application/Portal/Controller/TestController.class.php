<?php
// +----------------------------------------------------------------------
// | ThinkCMF [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2014 http://www.thinkcmf.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: Dean <zxxjjforever@163.com>
// +----------------------------------------------------------------------
namespace Portal\Controller;
use Common\Controller\HomebaseController; 
use Admin\Controller\PublicController;
/**
 * 首页
 */
class TestController extends HomebaseController {
	function _initialize(){
		parent::_initialize();
		header("Content-type: text/html; charset=utf-8");
	}
	public function demo16(){
		dump(json_decode('"\u53f6\u5c0f\u4fca"', true));
	}
	//redis测试
    public function demo15(){
//         $list = M('weixin_reply_text')->cache('test01')->select();
//         $Cache = new \Redis();
        //$Cache->sort('test01', array('id' => 'desc'));
//         dump(S('test01'));
    }
	//发送邮件测试
	public function send_email(){
	    $where = array('option_name'=>'member_email_active');
	    $option = M('Options')->where($where)->find();
	    if($option){
	        $options = json_decode($option['option_value'], true);
	        $result = sp_send_email('767684610@qq.com', $options['title'], str_replace('#data#', '{ "TicketType": "13","TicketCount": "2","UserName": "1111","MobilePhone": "18817966305","SetID": "5","OrderType": "2","OrderID": "160785312943","PayType": "2","ak": "","CreateTime" : "201607231304"}', $options['template']));
	        dump($result);
	        if($result['error'] == '1'){
	            \Think\Log::write('邮件错误：'.json_encode($options));
	        }
	    }
	}
	public function demo14(){
	    echo addslashes("挥一挥衣袖''扇风");
	    //echo strtotime(date('2016-07-14'));
	}
	public function demo13(){
	    //$json = '{"today":{"qty":2738,"enters":2526,"amount":632380.00,"cash":281020.00,"visa":280100.00,"wechat":30740.00,"alipay":40520.00},"hourqty":{"hour":[0,2,2,3,4,5,6,7,8,9,10,11],"sellqty":[0,2,0,0,0,0,0,0,0,0,0,0],"enterqty":[0,0,0,0,0,0,0,0,0,0,0,0]},"houramount":{"hour":[0,2,2,3,4,5,6,7,8,9,10,11],"cash":[0,0.00,0,0,0,0,0,0,0,0,0,0],"visa":[0,0.00,0,0,0,0,0,0,0,0,0,0],"wechat":[0,0.00,0,0,0,0,0,0,0,0,0,0],"alipay":[0,520.00,0,0,0,0,0,0,0,0,0,0]},"daysqty":{"days":["2016-07-07","2016-07-08","2016-07-09","2016-07-10","2016-07-11","2016-07-12","2016-07-13","2016-07-14","2016-07-15","2016-07-16"],"sellqty":[1216,1031,1063,747,347,1407,2129,860,1983,2738],"enterqty":[1259,1032,1054,738,305,1379,2194,1243,2053,2549]},"daysamount":{"hour":["2016-07-07","2016-07-08","2016-07-09","2016-07-10","2016-07-11","2016-07-12","2016-07-13","2016-07-14","2016-07-15","2016-07-16"],"cash":[73670.00,66600.00,8140.00,2440.00,2600.00,900.00,208750.00,73480.00,175750.00,281020.00],"visa":[81720.00,74900.00,186840.00,122280.00,45480.00,211060.00,143410.00,57980.00,126510.00,280100.00],"wechat":[35680.00,27120.00,29040.00,25100.00,14330.00,28480.00,22500.00,18200.00,35540.00,40520.00],"alipay":[15060.00,8640.00,21260.00,22440.00,3940.00,19400.00,15340.00,8510.00,27300.00,30740.00]}}';
	    $json = '{"today":{"qty":2807,"enters":2616,"amount":646280.00,"cash":286420.00,"visa":282220.00,"wechat":31180.00,"alipay":46460.00},"hourqty":{"hour":[0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18],"sellqty":[2,0,2,0,0,0,0,0,200,109,283,478,342,431,227,200,196,298,39],"enterqty":[0,0,0,0,0,0,0,0,0,113,343,538,367,233,379,238,246,92,90]},"houramount":{"hour":[0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18],"cash":[0.00,0,0.00,0,0,0,0,0,40000.00,620.00,11620.00,52800.00,46940.00,44000.00,2540.00,15100.00,15600.00,57200.00,0.00],"visa":[0.00,0,0.00,0,0,0,0,0,0.00,19980.00,45460.00,40000.00,27300.00,44320.00,44960.00,28360.00,21880.00,7840.00,2120.00],"wechat":[520.00,0,0.00,0,0,0,0,0,0.00,1280.00,3620.00,6480.00,7220.00,6260.00,3320.00,4080.00,6340.00,1400.00,5940.00],"alipay":[0.00,0,520.00,0,0,0,0,0,0.00,440.00,0.00,4360.00,5740.00,7560.00,4280.00,2700.00,5140.00,0.00,440.00]},"daysqty":{"days":["2016-07-07","2016-07-08","2016-07-09","2016-07-10","2016-07-11","2016-07-12","2016-07-13","2016-07-14","2016-07-15","2016-07-16"],"sellqty":[1216,1031,1063,747,347,1407,2129,860,1983,2807],"enterqty":[1259,1032,1054,738,305,1379,2194,1243,2053,2640]},"daysamount":{"hour":["2016-07-07","2016-07-08","2016-07-09","2016-07-10","2016-07-11","2016-07-12","2016-07-13","2016-07-14","2016-07-15","2016-07-16"],"cash":[73670.00,66600.00,8140.00,2440.00,2600.00,900.00,208750.00,73480.00,175750.00,286420.00],"visa":[81720.00,74900.00,186840.00,122280.00,45480.00,211060.00,143410.00,57980.00,126510.00,282220.00],"wechat":[35680.00,27120.00,29040.00,25100.00,14330.00,28480.00,22500.00,18200.00,35540.00,46460.00],"alipay":[15060.00,8640.00,21260.00,22440.00,3940.00,19400.00,15340.00,8510.00,27300.00,31180.00]}}';
	    dump(json_decode($json, true));
	}
	    
	public function demo12(){
	    $param['create_time'] = array('between',array('1468425600','1469203200'));
	    $param['status'] = 3;
	    $order_list = M('order')->where('status=3')->select();
	    foreach ($order_list as $k => $v){
	        $json = '{price:"",paycode:"",consumerauthcode:"",userid:"'.C('USER_ID').'",orderno:"'.$v['order_no'].'"}';
	        $param['json'] = addslashes($json);
	        $param['deviceno'] = C('DEVICENO');
	        $param['sign'] = get_sign($json);
	        $result = curl_http(C('URL_QUERY_PAY_STATE'), 'POST', $param);
	        $data = $result;
	        $data['orderno'] = $v['order_no'];
	        dump($data);
	    }
	     
	}
	public function demo11(){
	    $json = '{price:"",paycode:"",consumerauthcode:"",userid:"'.C('USER_ID').'",orderno:"NO14201607040292"}';
	    $param['json'] = addslashes($json);
	    $param['deviceno'] = C('DEVICENO');
	    $param['sign'] = get_sign($json);
	    $result = curl_http(C('URL_QUERY_PAY_STATE'), 'POST', $param);
	    dump($result);
	}
	public function demo10(){
	    //echo time().'<br/>';
	    //echo date('YmdHi', 1467708063);exit;
	    //$json = '{ "TicketType": "17","TicketCount": "1","UserName": "zzz","MobilePhone": "13333333333","SetID": "0","OrderType": "1","OrderID": "160760079398","PayType": "2","ak": "","CreateTime" : "201607151200"}';
	    //echo date('YmdHi', 1469683787);exit;
	    $json = '{ "TicketType": "18","TicketCount": "1","UserName": "9053","MobilePhone": "13985208237","SetID": "0","OrderType": "1","OrderID": "160774996611","PayType": "2","ak": "","CreateTime" : "201607281329"}';
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
	    dump($result);
	    $result = json_decode(str_replace('(', '', str_replace(')', '', $result)), true);
	    if($result['status'] == '0'){
	        $data = explode(',', str_replace('{', '', str_replace('}', '', $result['TicketNumbers'])));
	    }
	    dump($json);
	}
	public function demo9(){
		//echo  strtotime('2016-06-26 00:00:00');exit;
		$param['create_time'] = array('gt', '1466870400');
		dump(M('order')->where($param)->getField('sum(num)'));
	}
	public function demo8(){
		if('12:00' < '13:00'){
			echo 111;
		}else{
			echo 222;
		}
		//echo date("w");
		//session('test123', 1111);
	}
	public function demo6(){
		$url = 'http://120.25.163.27:6666/signature/api/qz';
		$private_key = C('PRIVATE_KEY');
		$url4 = 'http://pay.fyitgroup.com:8080/webmain/pos/addOnlineOrder.do';
		$url2 = 'http://pay.fyitgroup.com:8080/webmain/pos//pos/addOrder.do';
		$json = '{price:"0.01",paycode:"alipayosao",consumerauthcode:"",userid:"91",orderno:""}';
		$params['json'] = $json;
		//$para['json'] = addslashes($json);
		$params['deviceno']='HIFGGO9U';
		$sign = curl_http($url."?key=".urlencode($private_key)."&content=".$json);
		$params['sign'] = $sign;
		$params['openid'] = '';
		//$this->params = $params;
		dump(curl_post($url2, $params));
	}
	public function demo5(){
// 		$ticket_temp = array();
// 		$num = 0;
// 		for($i = 0; $i< 9; $i++){
// 			$num++;
// 			$ticket_temp[] = $num;
// 			if($num == 3){
// 				$num = 0;
// 				echo implode(',', $ticket_temp).'<br/>';
// 				$ticket_temp = null;
// 			}
// 		}
// 		dump($ticket_temp);
// 		echo implode(',', $ticket_temp);
// 		exit;
		//dump(get_order_numbers());
		$json = '{ "TicketType": "1","TicketCount": "10","UserName": "武建亮","MobilePhone": "18910715571","SetID": "3","OrderType": "2","OrderID": "NO20160061109300211","PayType": "1","ak": ""}';
		$param['json'] = $json;//addslashes($json);
		dump($json);
		dump(file_get_contents(C('URL_CREATE_NEW_ORDER').'?json='.urlencode($json)));
	}
	public function demo4(){
		$json = '{price:0.01,paycode:"wechatonline",consumerauthcode:"",userid:"91",orderno:""}';
		$param['json'] = $json;
		//$param['orderno'] = 'NO201606150193';
		$param['deviceno'] = C('DEVICENO');
		$param['sign'] = get_sign($json);
		$param['openid']="oAbzrwz5d4XQXgW2aTVz20TSHbhw";
		dump(curl_post(C('URL_ADD_ONLINE_ORDER'), $param));
	}
	//获取订单状态
	public function demo3(){
		$json = '{price:0.01,paycode:"wechatonline",consumerauthcode:"",userid:"91",orderno:""}';
		$param['json'] = $json;
		//$param['orderno'] = 'NO201606150193';
		$param['deviceno'] = C('DEVICENO');
		$param['sign'] = get_sign($json);
		$param['openid']="oAbzrwz5d4XQXgW2aTVz20TSHbhw";
		
	    $options = array(
	        CURLOPT_RETURNTRANSFER => true,
	        CURLOPT_HEADER         => false,
	        CURLOPT_POST           => true,
	        CURLOPT_POSTFIELDS     => $param,
	    );
	
	    $ch = curl_init("http://pay.fyitgroup.com:8080/webmain/pos/addOnlineOrder.do");
	    curl_setopt_array($ch, $options);
	    $result = curl_exec($ch);
	    curl_close($ch);
	    //return $result;
		dump($result);
	}
	//获取用户id
	public function demo7(){
		$url = 'http://120.25.163.27:6666/signature/api/qz';
		$url1 = 'http://pay.fyitgroup.com:8080/webmain/pos/login.do';
		$private_key = 'MIICdgIBADANBgkqhkiG9w0BAQEFAASCAmAwggJcAgEAAoGBAKmvl11+RDSjff9CCmNsoqEAAUyZRvcC/NhKziw1ARv2x9RFN0G/Z18d2DbBfl1CgwwgGM2sNaeU3E+EQVdhB/2mmemU4gzuT9YWUUcbGGJLyPUbxooVv+g9VItpV81vt/dxW6Nz5Es9esQiq4QceQh2hVFPNNvO59eLurzFS2xfAgMBAAECgYAbDoaiP8n8Yr1qgEtLwzzDU07huecY5/8NNhBd+C2vGdCPRmIjN7Px9L5PLNdLY5mly1BpKzZ6/D+M5lfM6Qnu4uUry6smIvn85rYCxYyJ5oADaoh3BN4PQXt4ya0ags7eNU2dRJFvqj0IeQlFTtHKcMp80rAEnO5hX7AOAmOCsQJBAPOQdbBOeh0CAicPLDkUTffI38yg2HVM4lAyDAHst+yJbj3ihB22IQpdrOHiarN6BDbrCHSXuBI4IHGlH07vb50CQQCyWX50jN2t4J7bDMMLlK2Cvm6EHS3+MyFf9jMKkzTfHK1B4c+qe92UQQkwkwySOSSeuohrxBuhLP0QvYdz31ErAkBB1EyKv1sv1eghCG/KaZt9GhSq6No4MfSE+lHWf1Vin+5k2YEdyqj5dUIRtzFhmtviv/mEEbGVcwgJzzg8DGjdAkBSsaIrgkEq5PJjGKi7DuXgsFFn2mu/6I86AlqqNZUXOiGvYXc7UhFsMzeCmTwD9JDhNBYa+aPh+I0iHO7YMBxfAkEAkskJcaNwz0L3VFJgo+bvdtucjK+iHyEr4gRfDUSwaQICbN3CA4/cpfitSv7NFA9uHWTET9q3y7kygl8UrEcEkw==';//'MIICeAIBADANBgkqhkiG9w0BAQEFAASCAmIwggJeAgEAAoGBAKgYs26WOySMv7vYprwA1sB7Vc45YZDEGwm6UTO8w1aoszBODevHNixueseLSQz1jMX9CCZBzr78rXeVp6k26nqwyxQnmOHCFjYs+yeEP/1VtXFLUINLG/bJ0U6QReMqL1rT/zziS9acFEr3+7LwkJiGXbUsXqRrct0gq6wUqEUbAgMBAAECgYBJKqc/ZWjM2e7C5uR8RKk1EvogT12mU5WpQE4EBQ/Jcpi8V907WXS9FkGfBYOAfokNzLt1W4OQIg+nU56syth4ZUKCm2/ZvhK5CJQR67InB+FGHpQC7LG7gxAD1kiC8/SjOXteD1kxYMWW3Aph3q6AkuCSPPkQCQnjzgdNXZ1MIQJBAOcgrCCwndIx+NiUSMv3BW1tv2UfMzWrpu7K1LyjdAWe98u/5jZUlM3EgLxgBScicM1Go2QQyiSoFwG8CcsPn/kCQQC6L5Xwl9hodzXQxno1zGq95px858OhTkeXlDUZXIgNG+Pm0mm6Brjgd4gGJW9HzXf1Yw22U3GLwEQ1xk3LpTqzAkEAuJbygcUstnQsryR2o1ds4UGWa3eomYO29d3OW74bamXUt8hSXy/cDB6VRl1VoDS0bG2vDrsOBoqsnTkFhUS8WQJBALP0qogRPCo8jtdr/1NgcQt7imVv3bZbYvcvWONafGWvP2gql+Yl+St8XQ0Twas0/W1AgFBp9qWNAGC5exgKa+ECQQDXeO28qlzF7dF/bcHOA/GKwKfRWWTN2oTRkO5JTDfGjalQxgOj9g7dXQT5vAkwv4vPc5y2FiuGAz/PMF12Rkof';
		$json = '{username:"ticketmachine",password:"'.strtoupper(md5('feng2016')).'"}';
		$para['json'] = addslashes($json);
		$para['deviceno']='C849UPNC';
		$sign = file_get_contents($url."?key=".urlencode($private_key)."&content=".$json);
		$para['sign'] = $sign;
		//$para11['deviceno']='H3NTZJSQ';
		dump($para);
		dump(curl_http($url1, 'POST', $para));
	}
	//获取私钥
	public function demo1(){
		$url = C('URL_GET_PRIVATE_KEY');
    	$para11['deviceno']='74DRBG5P';
    	$result = curl_http($url, 'POST', $para11);
//     	dump($result);
    	return $result['privatekey'];
	}
	//获取登录用户信息,返回用户id
	public function demo2(){
		//$post_data = '{json:"{username:\"zz001\",password:\"dino2016\"}",deviceno:"T37ZJA1Q4N",sign:"WffOl+qlHMwEiFAbICghwVTW4iGvi9fqPKy6IEfytBlGqIuClbmbZyc80+9+0c4GfrW7edUwdMj91FzIIHfNRu3y2yiZjG1vf6/2Lw7gJ4N75e9jaX92lPzv50585bGJZWVqe4XrWBrztT9QPW4kINpDpz+hO9Hy1P5X5RgYudc="}';
		//$post_data['deviceno'] = 'HIFGGO9U';
		//$post_data['username'] = '';
		$url = C('URL_SIGN');
		$url1 = C('URL_LOGIN');
		$private_key = $this->demo1();//'MIICeAIBADANBgkqhkiG9w0BAQEFAASCAmIwggJeAgEAAoGBAKgYs26WOySMv7vYprwA1sB7Vc45YZDEGwm6UTO8w1aoszBODevHNixueseLSQz1jMX9CCZBzr78rXeVp6k26nqwyxQnmOHCFjYs+yeEP/1VtXFLUINLG/bJ0U6QReMqL1rT/zziS9acFEr3+7LwkJiGXbUsXqRrct0gq6wUqEUbAgMBAAECgYBJKqc/ZWjM2e7C5uR8RKk1EvogT12mU5WpQE4EBQ/Jcpi8V907WXS9FkGfBYOAfokNzLt1W4OQIg+nU56syth4ZUKCm2/ZvhK5CJQR67InB+FGHpQC7LG7gxAD1kiC8/SjOXteD1kxYMWW3Aph3q6AkuCSPPkQCQnjzgdNXZ1MIQJBAOcgrCCwndIx+NiUSMv3BW1tv2UfMzWrpu7K1LyjdAWe98u/5jZUlM3EgLxgBScicM1Go2QQyiSoFwG8CcsPn/kCQQC6L5Xwl9hodzXQxno1zGq95px858OhTkeXlDUZXIgNG+Pm0mm6Brjgd4gGJW9HzXf1Yw22U3GLwEQ1xk3LpTqzAkEAuJbygcUstnQsryR2o1ds4UGWa3eomYO29d3OW74bamXUt8hSXy/cDB6VRl1VoDS0bG2vDrsOBoqsnTkFhUS8WQJBALP0qogRPCo8jtdr/1NgcQt7imVv3bZbYvcvWONafGWvP2gql+Yl+St8XQ0Twas0/W1AgFBp9qWNAGC5exgKa+ECQQDXeO28qlzF7dF/bcHOA/GKwKfRWWTN2oTRkO5JTDfGjalQxgOj9g7dXQT5vAkwv4vPc5y2FiuGAz/PMF12Rkof';
		$json = '{username:"zx004",password:"'.strtoupper(md5('feng2016')).'"}';
		$param['json'] = addslashes($json);
		$param['deviceno']='74DRBG5P';
		$sign = file_get_contents($url."?key=".urlencode($private_key)."&content=".$json);
		$param['sign'] = $sign;
		$result = curl_http($url1, 'POST', $param);
		//return $result['userid'];
		dump($result);
	}
    public function demo(){
    	//$json = '{"orderno":"NO201606150193"}';
    	$json = '{price:"",paycode:"",consumerauthcode:"",userid:"91",orderno:"NO201606150193"}';
    	$param['json'] = addslashes($json);
    	//$param['orderno'] = 'NO201606150193';
    	$param['deviceno'] = C('DEVICENO');
    	$param['sign'] = get_sign($json);
    	$result = curl_http(C('URL_QUERY_PAY_STATE'), 'POST', $param);
    	dump($result);
    }
    public function test1(){
    	header("Content-type: text/html; charset=utf-8");
    	$url123 = 'http://pay.fyitgroup.com:8080/webmain/pos/getPrivateKey.do';
     	//$post_data = '{json:"{username:\"zz001\",password:\"dino2016\"}",deviceno:"T37ZJA1Q4N",sign:"WffOl+qlHMwEiFAbICghwVTW4iGvi9fqPKy6IEfytBlGqIuClbmbZyc80+9+0c4GfrW7edUwdMj91FzIIHfNRu3y2yiZjG1vf6/2Lw7gJ4N75e9jaX92lPzv50585bGJZWVqe4XrWBrztT9QPW4kINpDpz+hO9Hy1P5X5RgYudc="}';
    	//$post_data['deviceno'] = 'HIFGGO9U';
    	//$post_data['username'] = '';
    	$url = 'http://120.25.163.27:6666/signature/api/qz';
    	$url1 = 'http://pay.fyitgroup.com:8080/webmain/pos/login.do';
    	$private_key = C('PRIVATE_KEY');//'MIICeAIBADANBgkqhkiG9w0BAQEFAASCAmIwggJeAgEAAoGBAKgYs26WOySMv7vYprwA1sB7Vc45YZDEGwm6UTO8w1aoszBODevHNixueseLSQz1jMX9CCZBzr78rXeVp6k26nqwyxQnmOHCFjYs+yeEP/1VtXFLUINLG/bJ0U6QReMqL1rT/zziS9acFEr3+7LwkJiGXbUsXqRrct0gq6wUqEUbAgMBAAECgYBJKqc/ZWjM2e7C5uR8RKk1EvogT12mU5WpQE4EBQ/Jcpi8V907WXS9FkGfBYOAfokNzLt1W4OQIg+nU56syth4ZUKCm2/ZvhK5CJQR67InB+FGHpQC7LG7gxAD1kiC8/SjOXteD1kxYMWW3Aph3q6AkuCSPPkQCQnjzgdNXZ1MIQJBAOcgrCCwndIx+NiUSMv3BW1tv2UfMzWrpu7K1LyjdAWe98u/5jZUlM3EgLxgBScicM1Go2QQyiSoFwG8CcsPn/kCQQC6L5Xwl9hodzXQxno1zGq95px858OhTkeXlDUZXIgNG+Pm0mm6Brjgd4gGJW9HzXf1Yw22U3GLwEQ1xk3LpTqzAkEAuJbygcUstnQsryR2o1ds4UGWa3eomYO29d3OW74bamXUt8hSXy/cDB6VRl1VoDS0bG2vDrsOBoqsnTkFhUS8WQJBALP0qogRPCo8jtdr/1NgcQt7imVv3bZbYvcvWONafGWvP2gql+Yl+St8XQ0Twas0/W1AgFBp9qWNAGC5exgKa+ECQQDXeO28qlzF7dF/bcHOA/GKwKfRWWTN2oTRkO5JTDfGjalQxgOj9g7dXQT5vAkwv4vPc5y2FiuGAz/PMF12Rkof';
    	$json = '{username:"online",password:"'.strtoupper(md5('feng2016')).'"}';
    	$para['json'] = addslashes($json);
    	$para['deviceno']='HIFGGO9U';
    	$sign = file_get_contents($url."?key=".urlencode($private_key)."&content=".$json);
    	$para['sign'] = $sign;
    	$para11['deviceno']='HIFGGO9U';
    	//dump(strtoupper(md5('111111')));
    	dump(($para));
    	dump(curl_http($url1, 'POST', $para));
    	//dump(file_get_contents($url."?key=".urlencode($private_key)."&content=sdsdsd"));
		//echo $url."?key=".$private_key."&content=".$json;
    }
    
    public function pay(){
		//dump(get_openid());exit;
    	$url = 'http://120.25.163.27:6666/signature/api/qz';
    	$url2 = 'http://pay.fyitgroup.com:8080/webmain/pos/addOnlineOrder.do';
    	$private_key = C('PRIVATE_KEY');
    	$json = '{"price":"0.01","paycode":"wechatonline","consumerauthcode":"","userid":"91","orderno":"NO201606160025"}';
    	$params['json'] = $json;
    	$params['deviceno']='HIFGGO9U';
    	$sign = curl_http($url."?key=".urlencode($private_key)."&content=".$json);
    	$params['sign'] = $sign;
    	$params['openid'] = '';
    	$this->params = $params;
    	//dump($params);
//     	dump(curl_http($url2, 'POST', $params));
//     	$result = curl_http($url2, 'POST', $params);
//     	dump($result);
//     	exit;
    	$this->display(":pay");exit;
    	//dump($result);
    	
    	vendor("phpqrcode.phpqrcode");
    	$data = $result['qrcodeurl'];
    	// 纠错级别：L、M、Q、H
    	$level = 'L';
    	// 点的大小：1到10,用于手机端4就可以了
    	$size = 4;
    	// 下面注释了把二维码图片保存到本地的代码,如果要保存图片,用$fileName替换第二个参数false
    	//$path = "images/";
    	// 生成的文件名
    	//$fileName = $path.$size.'.png';
    	\QRcode::png($data, false, $level, $size);
    	/*
    	$url = 'http://120.25.163.27:6666/signature/api/qz';
    	$private_key = C('PRIVATE_KEY');
    	$url4 = 'http://pay.fyitgroup.com:8080/webmain/pos/addOnlineOrder.do';
    	$url2 = 'http://pay.fyitgroup.com:8080/webmain/pos//pos/addOrder.do';
    	$json = '{price:"0.01",paycode:"alipayonline",consumerauthcode:"",userid:"91",orderno:""}';
    	$params['json'] = $json;
    	//$para['json'] = addslashes($json);
    	$params['deviceno']='HIFGGO9U';
    	$sign = curl_http($url."?key=".urlencode($private_key)."&content=".$json);
    	$params['sign'] = $sign;
    	$params['openid'] = '';
    	$this->params = $params;
    	echo (JSON($params));
    	//curl_http($url4, 'POST', $para);
    	$this->display(':pay');
    	$url = 'http://pay.fyitgroup.com:8080/webmain/pos/queryPayState.do';
    	$json = '{orderno:"1111111111",userid:"91"}';
    	$param['json'] = addslashes($json);
    	$param['deviceno'] = C('DEVICENO');
    	$param['sign'] = get_sign($json);
    	dump($param);
    	dump(curl_http($url, 'POST', $param));
    	exit;
    	$json = '{TicketType:"1",TicketCount:"10",UserName:"武建亮",MobilePhone:"18910715571",SetID:"3",OrderType:"2",OrderID:"NO20160061109300211",PayType:"1",ak:"DHYEUIYTQWER"}';
    	$param['json'] = addslashes($json);	 
    	$result = create_new_order($param);
    	dump($result);
    	*/
    }
    public function login(){
    	header("Content-type: text/html; charset=utf-8");
    	$url = 'http://120.25.163.27:6666/signature/api/qz';
    	$url2 = 'http://pay.fyitgroup.com:8080/webmain/pos/login.do';
    	//$json = '{price:"0.01",paycode:"alipayonline",consumerauthcode:"",userid:"91",orderno:""}';
    	$json = '{username:"online",password:"'.strtoupper(md5('feng2016')).'"}';
    	$para['json'] = addslashes($json);
    	$para['deviceno']='HIFGGO9U';
    	$sign = file_get_contents($url."?key=".urlencode($private_key)."&content=".$json);
    	$para['sign'] = $sign;
    	//$para11['deviceno']='HIFGGO9U';
    	dump(curl_http($url2, 'POST', $para));
    	//openssl_sign($param['json'], $signature, C('PRIVATEKEY'));
    	//echo base64_encode('243242');
    	//$pem = chunk_split(C('PRIVATEKEY'),64,"\n");
    	//$pem = "-----BEGIN RSA PRIVATE KEY-----\n".$pem."-----END RSA PRIVATE KEY-----\n";
    	//dump(base64_decode($pem));
    }
	public function test(){
		//echo C('PRIVATEKEY');
		//获取秘钥
		//$url = 'http://pay.fyitgroup.com:8080/webmain/pos/getPrivateKey.do';
		//获取微信授权地址（微信在线支付）
		//$url = 'http://pay.fyitgroup.com:8080/webmain/pos/getValidUrl.do';
		//$url = 'http://pay.fyitgroup.com:8080/webmain/pos/addOnlineOrder.do';
		$url = 'http://pay.fyitgroup.com:8080/webmain/pos/addOnlineOrder.do';
		$json = array(
		'price' => '0.01',
		'paycode' => 'alipayonline',
		'consumerauthcode' => '11111',
		'userid' => '1',
		'orderno' => '160642332341'
		);
		//$json = urlencode($json);
		$PRIVATEKEY = C('PRIVATEKEY');
		$private_key1 = "-----BEGIN RSA PRIVATE KEY-----
{$PRIVATEKEY}
-----END RSA PRIVATE KEY-----";
		//echo $private_key1;//openssl_pkey_get_private($private_key1);
		//$param['deviceno'] = '5YTHRUE6';
		$param['json'] = addslashes(JSON($json));
		$private_key = '-----BEGIN RSA PRIVATE KEY-----
MIICXQIBAAKBgQDpW9z9Wf/7QV/4uxma71oLa6CpZm+h/60meZHVFTBeoAyJKqy9
aylPFRYNWlBGHQIBSLgNBbIzL2zTofx32Wff7lrKUW2mAgNftiFVcwTBWzTieGW9
B5tPS64qNTwlmpBpI2wX6zMwQyMNWC45mRnxSzyolz5HAwbRdeqh5y5hSQIDAQAB
AoGATisgdplsPvziKg56gETHE+ZElhojMM/Vb3rvl8IWRsw0BsqNvJGl78Cvd1+W
XjYfUtDKHkqXK8AqIyYyzO69dUinQGsQrTCdp45kGoSlDbFghEe5MiR/rgdYIOHJ
Tg8nLR9bxlTRkECaPglfr04vBzDcEDJQn7uSCaD0PB/SD9ECQQD9UTvPmy15NQb9
GzfFP1xqI5s15uCoJLCRGrAWomAvKuGPFs3GLTvOU1aF2jAn0bNh4zIjw5xLIHI+
IhgWaJOVAkEA69SFNZpakrtKQCWmKlGJHCRNSSwCT4NHLPirwziBmpZIPaT5iPMw
6wcTDhtyzb2Vu+ESLtrKS3nch7MdnCGp5QJAGS+edsHC/64aB8hQ/zeRhKwNnopa
A93CAGta3qU+UvI8gvGNfAq7S4RVsfFDoHHlF/Jy5cNpIr8THMJfCrtTEQJBANvI
5pT8U6kob5y0+dW6w4O8uWKWZ1jfSjg5USrRwMfng1AgLodZzp9bqoCdSDNCmwfM
TPvp4FrTKZo2bkQSg5kCQQCSAOcByeZY0wy+2bK6543/Hf/bPx8uC/u4dDP2lqzZ
Cqbf6BRDZ5M0NldPT76CQYK/hABVPmosaNRLCsNm755B
-----END RSA PRIVATE KEY-----';
		$pi_key =  openssl_pkey_get_private($private_key);
		$encrypted = '';
		openssl_sign('1111111', $signature, C('PRIVATEKEY'));
		dump($signature);exit;
		openssl_private_encrypt('111', $encrypted, $pi_key);//私钥加密
		//$encrypted = base64_encode($encrypted);
		dump($encrypted);exit;
		$param['deviceno'] = '5YTHRUE6';
		$param['sign'] = C('PRIVATEKEY');
		$param['openid'] = 'openid';
		dump($param);exit;
// 		curl_http($url, 'POST', $param);
		dump(curl_http($url, 'POST', $param));
	}
}


