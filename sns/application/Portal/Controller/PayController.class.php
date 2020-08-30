<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2015-2016 http://www.anzaisoft.net All rights reserved.
// +----------------------------------------------------------------------
// | Author: ZhangQingYuan <767684610@qq.com>
// +----------------------------------------------------------------------
namespace Portal\Controller;
use Common\Controller\MemberbaseController;
use Admin\Controller\PublicController;
/**
 * 去支付订单
 */
class PayController extends MemberbaseController {
	//提交订单
	public function payment(){
		!IS_AJAX && exit;
		$payway = array('alipayonline', 'wechatonline');
		$data = I('post.');
		if(!is_numeric($data['num']) || $data['num'] <= 0 || $data['num'] > 5){
			$this->error('购票数量不正确！');
		}
		$this->error('服务器正忙！');
		$condition['create_time'] = array('gt', strtotime(date('Y-m-d')));
		$condition['status'] = 3;
		$condition['uid'] = session('uid');
		$toltal_num = M('order')->where($condition)->getField('sum(num)');
		if($toltal_num + $data['num'] > 5){
			$this->error('同一手机号每天仅限预定5张门票！');
		}
		//验证销售门票是否在当前时间内
		$param['id'] = $data['ticket_id'];
		$ticket_info = M('ticket')->where($param)->find();
		$status = true;
		$now_date = time();
		if($ticket_info['parent_id'] == 1){
			$now_time = date('H:i');
    		$week = date('w') == '0' ? 7 : date('w');
			if(strpos($ticket_info['week'], (string)$week)  === false || $now_time < $ticket_info['sale_start_time'] || $now_time > $ticket_info['sale_end_time'] || $now_date < $ticket_info['start_date'] || $now_date > $ticket_info['end_date']){
				$status = false;
			}
		}else{
			if($now_date < $ticket_info['start_date'] || $now_date > $ticket_info['end_date']){
				$status = false;
			}
		}
		if(!$status || !$ticket_info){
			$this->error('此门票不存在或不在可销售时间范围内！');
		}
		$data['piaozhong_id'] = $_REQUEST['ticket_type'];
		$data['payway'] = $payway[$data['payway']];
		$total_money = $ticket_info['price'] * $data['num'];
		$param = pay_param(0.01, '', 0, $data['payway']);
		//渲染模板数据
		$this->payway = $_REQUEST['payway'] == 1 ? '微信在线' : '支付宝在线';
		$this->param = $param;
		$info = curl_post(C('URL_ADD_ONLINE_ORDER'), $param);
		
		if($info['success'] == '1'){
			//保存订单数据FFFFFFFF
			$data['uid'] = session('uid');
			$data['status'] = 1;
			$data['order_id'] = get_order_id();
			$data['order_ip'] = get_client_ip();
			$data['create_time'] = time();
			$data['price'] = $ticket_info['price'];
			$data['order_no'] = $info['orderno'];
			$data['qrcodeurl'] = $info['qrcodeurl'];
			$data['piaozhong_id'] = $data['ticket_type'];
			$data['total_money'] = $total_money;
			unset($data['ticket_type']);
			M('order')->add($data);
			session('order_info', $param);
			
			for ($i = 0; $i < $data['num']; $i++){
				$ticket_data['order_id'] = $data['order_id'];
				$ticket_data['ticket_id'] = $ticket_info['id'];
				$ticket_data['num'] = 1;
				$ticket_data['price'] = $ticket_info['price'];
				M('order_ticket')->add($ticket_data);
			}
			/*
			 
			$this->order_info = $data;
			$content = $this->fetch('payment');
			$this->ajaxReturn(array('status'=>1, 
					'content'=>$content, 
					'payway'=>$this->payway, 
					'url'=> U('Portal/Pay/return_url'),
					'orderno' => $info['orderno']
					)
			);
			*/
			$param = pay_param(0.01, $info['orderno'], 0, $data['payway']);
			session('order_info', $param);
			$this->success('订单提交成功！', U('Portal/Pay/buld_request_pay'));
			exit;
		}
		//$this->success('订单提交成功！', U('pay/buld_request_pay'));
		$this->error('系统错误，请稍后再试！');
	}
	//去请求支付
	public function buld_request_pay(){
		if(session('?order_info')){
			//dump(session('order_info'));exit;
			echo build_request_form(session('order_info'));exit;
		}
		$this->error('当前服务器正忙，请稍后重试！');
	}
	//我的订单，未支付订单去重新支付
	public function re_payment(){
		if(isset($_REQUEST['order_id']) && IS_AJAX){
			//同一用户每天限定5张门票
			$condition['create_time'] = array('gt', strtotime(date('Y-m-d')));
			$condition['status'] = 3;
			$condition['uid'] = session('uid');
			$toltal_num = M('order')->where($condition)->getField('sum(num)');
			if($toltal_num + $data['num'] > 5){
				$this->error('同一手机号每天仅限预定5张门票！');
			}
			$data['order_id'] = I('order_id');
			//验证支付门票是否在当前销售时间内
			$order_info = M('order')->where($data)->find();
			$param['id'] = $order_info['ticket_id'];
			$ticket_info = M('ticket')->where($param)->find();
			$status = true;
			$now_date = time();
			if($ticket_info['parent_id'] == 1){
				$now_time = date('H:i');
	    		$week = date('w') == '0' ? 7 : date('w');
	    		//strripos($ticket_info['week'], $week)  === false || 
				if(strpos($ticket_info['week'], (string)$week)  === false || $now_time < $ticket_info['sale_start_time'] || $now_time > $ticket_info['sale_end_time'] || $now_date < $ticket_info['start_date'] || $now_date > $ticket_info['end_date']){
					$status = false;
				}
			}else{
				if($now_date < $ticket_info['start_date'] || $now_date > $ticket_info['end_date']){
					$status = false;
				}
			}
			if(!$status || !$ticket_info){
				$this->error('此门票不存在或不在可销售时间范围内！');
			}
			
			if($order_info['status'] == 1){
				$this->order_info = $order_info;
				$content = $this->fetch('payment');
				//print_r($content);
				$this->ajaxReturn(array('status'=>1,
						'content' => $content,
						'payway' => stripos($order_info['payway'], 'chat') === true ? '微信扫码' : '支付宝扫码',
						'url'=> U('Portal/Pay/return_url'),
						'orderno' => $order_info['order_no']
				));exit;
			}
			else if($order_info['status'] == 3){
				$this->error('请勿重复支付订单！');
			}
			else{
				$this->error('订单异常！');
			}
		}
		else{
			$this->error('当前服务器正忙，请稍后重试！');
		}
	}
	//获取订单状态
	public function get_order_status($is_wechat = '0'){
		if(isset($_REQUEST['orderno'])){
			$json = '{price:"",paycode:"",consumerauthcode:"",userid:"'.C('USER_ID').'",orderno:"'.I('orderno').'"}';
	    	$param['json'] = addslashes($json);
	    	//$param['orderno'] = 'NO201606150193';
	    	$param['deviceno'] = C('DEVICENO');
	    	$param['sign'] = get_sign($json);
	    	/*
	    	$list = array('211.152.53.68','127.0.0.1','223.167.226.201','139.196.38.222');
	    	$cokie_data['ip'] = get_client_ip();
	    	$cokie_data['time'] = 60;
	    	if(!in_array($_SERVER['REMOTE_ADDR'],exit('You don\'t have permission to access!'); $list)){
	    		exit('You don\'t have permission to access!');
	    	}
	    	\Think\Log::write(get_client_ip());
	    	*/
	    	$result = curl_http(C('URL_QUERY_PAY_STATE'), 'POST', $param);
    		if($result['paystate'] == 1){
    			$data['status'] = 3;
    			$data['pay_time'] = time();
    			M('order')->where(array('order_no'=>I('orderno'), 'uid'=>session('uid')))->save($data);
    			$order_id = M('order')->where(array('order_no'=>I('orderno')))->getField('order_id');
    			if($is_wechat){
    				return true;
    			}else{
	    			$this->ajaxReturn(array('status'=>1, 'info'=>'支付成功！', 'url' => U('Portal/Pay/return_url', array('order_id'=>$order_id), '')));exit;
    			}
    		}
    		else{
    		if($is_wechat){
    				return false;
    			}else{
	    			$this->ajaxReturn(array('status'=>0, 'info'=>'暂未支付'));
    			}
    		}
	    	
			
		}
	}
	/**
	 * 回调地址，成功页面
	 */
	public function return_url(){
		$wap = '';
		if(session('?wechat_user_info')){
			$wap = 'wechat_';
		}
		if(!empty($_REQUEST['orderno']) || isset($_REQUEST['order_id'])){
			if(isset($_REQUEST['orderno'])){
				$param['order_no'] = I('orderno');
				$this->get_order_status(1);
			}else{				
				$param['order_id'] = I('order_id');
			}
			$param['status'] = 3;
			$param['uid'] = session('uid');
			
			$result = M('order')->where($param)->find();
			
			if($result){
				session('order_info', null);
				$this->order_info = $result;
				//未获取门票序列号的订单去获取并保存
				$temp_order_id = array();
				$order_ticket_list = M('order_ticket')->where(array('order_id'=>$result['order_id']))->select();
				for ($i = 0; $i < count($order_ticket_list); $i++){
					if(!$order_ticket_list[$i]['xuliehao']){
						$temp_order_id[] = $order_ticket_list[$i]['id'];
					}
				}
				
				//如果该订单没有获取门票序列号则去获取，已获取则不执行
				if(!empty($temp_order_id)){
					$ordertype = '1';
					if ($result["piaozhong_id"]=="2") $ordertype='2';
					$paytype='3';
					if (strripos($result["payway"], 'wechat') !== false) $paytype='2';
					//门票序列号返回号码
					$ticket_info = M('ticket')->where('id='.$result['ticket_id'])->find();
					$ticket_data = get_order_numbers($ticket_info['ticket_id'],//门票编号
							count($temp_order_id), //门票数
							get_user_info(session('uid'), 'user_nicename'),//用户名
							session('user_email'), //用户手机
							$ticket_info['ticket_type'],//对方门票类型  set_id
							$ordertype,  //门票类型 当日票1，预售票2
							$result['order_id'],//订单号
							$paytype);//支付类型，支付宝1、微信2
					for ($i = 0; $i < count($temp_order_id); $i++){
						M('order_ticket')->where(array('id'=>$temp_order_id[$i]))->setField('xuliehao', $ticket_data[$i]);
					}
					//您已成功下单，凭取票码：【变量】，【变量】，【变量】，【变量】，【变量】，至自动取票处取票，官方客服电话021-64783333转7
					//交易成功，取票号短信推送
					//dump($ticket_data);exit;
					if(!empty($ticket_data)){
						$ticket_data = implode('，', $ticket_data);
						$send_content = '您已成功下单，凭取票码：'.$ticket_data.'，至自动取票处取票，官方客服电话021-64783333转7';
						$status = sendsms(session('user_email'), $send_content);
						$where['order_id'] = $result['order_id'];
						if($status){
							M('order_ticket')->where($where)->setField('status', 1);
						}
					}
					
				}
				$this->display($wap.'success');
			}else{
				$this->error_info = '不存在此订单。';
				$this->display($wap.'error');exit;
			}
		}else{
			$this->error_info = '非法操作！';
			$this->display($wap.'error');
		}
	}
	public function test(){
		/*
		header("Content-type: text/html; charset=utf-8");
		$send_ticket = '1606123463';
		$content = '您的验证码是：'.$send_ticket.'。请不要把验证码泄露给其他人。';
		dump(sendsms('13636690679', $content));exit;
		*/
		$where['order_id'] = '160678277635';
		$order_info = M('order')->where($where)->find();
		$data_arr = array(
				'TicketType' => $order_info['ticket_id'],
				'TicketCount' => $order_info['num'],
				'UserName' => session('user_email'),
				'MobilePhone' => session('user_email'),
				'SetID' => '0',
				'OrderType' => $order_info['piaozhong_id'],
				'OrderID' => $order_info['order_id'],
				'PayType' => $order_info['payway'],
				'ak' => C('DEVICENO')
		);
		$this->ticket_data = addslashes(JSON($data_arr));
		$this->display('success');
	}
	//保存门票串号数据并发送取票编号短信
	public function send_mess(){
		if(IS_AJAX && isset($_REQUEST['ticket_info'])){
			$data = I('ticket_info');
			$where['order_id'] = $data['order_id'];
			$where['uid'] = session('uid');
			$order_info = M('order')->where($where)->find();
			$send_ticket = '';
			foreach ($data['ticket_numbers'] as $value){
				$ticket_data['order_id'] = $order_info['order_id'];
				$ticket_data['xuliehao'] = $value;
				$ticket_data['ticket_id'] = $order_info['ticket_id'];
				$ticket_data['num'] = 1;
				$ticket_data['price'] = $order_info['price'];
				M('order_ticket')->add($ticket_data);
				$send_ticket = $send_ticket ? $send_ticket : ',' . $send_ticket;
			}
			$send_ticket = '1606123463,1606123464,1606123465,1606123466';
			$content = '您的验证码是：'.$send_ticket.'。请不要把验证码泄露给其他人。';
			dump(sendsms($tel, $content));
		}else{
			$this->error('参数有误！');
		}
	}
}