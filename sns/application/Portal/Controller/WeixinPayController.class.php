<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2015-2016 http://www.anzaisoft.net All rights reserved.
// +----------------------------------------------------------------------
// | Author: ZhangQingYuan <767684610@qq.com>
// +----------------------------------------------------------------------
namespace Portal\Controller;
use Common\Controller\WeixinbaseController;
use Admin\Controller\PublicController;
/**
 * 去支付订单
 */
class WeixinPayController extends WeixinbaseController {
	public function payment(){
		!IS_AJAX && exit;
		//$payway = array('alipaysao', 'wechatsao');
		$data = I('post.');
		$ticket_info = M('ticket')->where(array('ticket_id'=>$data['ticket_id']))->find();
		if(!$ticket_info){
			$this->error('此门票不存在！');
		}
		$data['piaozhong_id'] = $_REQUEST['ticket_type'];
		$total_money = $ticket_info['price'] * $data['num'];
		/*
		$json = '{price:0.01,paycode:"wechatonline",consumerauthcode:"",userid:"91",orderno:""}';
		$param['json'] = $json;
		//$param['orderno'] = 'NO201606150193';
		$param['deviceno'] = C('DEVICENO');
		$param['sign'] = get_sign($json);
		$param['openid'] = "oAbzrwz5d4XQXgW2aTVz20TSHbhw";
		*/
		$param = pay_param('0.01');
		//echo ($param['openid']);exit;
		$result = curl_post(C('URL_ADD_ONLINE_ORDER'), $param);
		if($result['success'] == 1){
			//保存订单数据
			$data['uid'] = session('uid');
			$data['status'] = 1;
			$data['order_id'] = get_order_id();
			$data['order_ip'] = get_client_ip();
			$data['create_time'] = time();
			$data['price'] = $ticket_info['price'];
			$data['order_no'] = $result['orderno'];
			$data['total_money'] = $total_money;
			$data['payway'] = 'wechatonline';
			if(M('order')->add($data)){
				for ($i = 0; $i < $data['num']; $i++){
					$ticket_data['order_id'] = $data['order_id'];
					$ticket_data['ticket_id'] = $ticket_info['ticket_id'];
					$ticket_data['num'] = 1;
					$ticket_data['price'] = $ticket_info['price'];
					M('order_ticket')->add($ticket_data);
				}
			}else{
				$this->error('生成订单失败！');
			}
			$param = pay_param($total_money, $result['orderno']);
			session('order_info', $param);
			$this->success('订单提交成功！', U('WeixinPay/buld_request_pay'));
		}else{
			$this->error('生成订单失败！');
		}
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
			$data['order_id'] = I('order_id');
			$data['uid'] = session('uid');
			$order_info = M('order')->where($data)->find();
			if($order_info){
				if($order_info['status'] == 1){
					$param = pay_param($order_info['total_money'], $order_info['orderno']);
					echo build_request_form($param);
				}
				$this->error('订单异常！');exit;
			}else{
				$this->error('没有未支付订单！');
			}
		}
		else{
			$this->error('当前服务器正忙，请稍后重试！');
		}
	}
	/**
	 * 回调地址
	 */
	public function return_url(){
		if(!empty($_REQUEST['orderno']) && $_REQUEST['status'] == 1){
			$data['status'] = 3;
			$data['pay_time'] = time();
			$param['order_id'] = I('orderno');
			$param['uid'] = 6;//session('uid');
			$result = M('order')->where($param)->save($data);
			if($result){
				//{ \"TicketType\": \"1\",\"TicketCount\": \"10\",\"UserName\": \"武建亮\",\"MobilePhone\": \"18910715571\",\"SetID\": \"3\",\"OrderType\": \"2\",\"OrderID\": \"NO20160061109300211\",\"PayType\": \"1\",\"ak\": \"DHYEUIYTQWER\"};
				session('order_info', null);
				$where['uid'] = $param['uid'];
				$where['order_id'] = $param['order_id'];
				$order_info = M('order')->where($where)->find();
				M('order')->where($where)->setField('status', 3);
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
				//$post_data['json'] = addslashes(JSON($data_arr));
				//$ticket_result = create_new_order($post_data);
				$post_data['json'] = JSON($data_arr);
				$this->post_data = $post_data;
				$this->display('success');
			}else{
				$this->display('error');
			}
		}else{
			$this->error('非法数据！');
		}
	}
	public function test(){
		header("Content-type: text/html; charset=utf-8");
		$send_ticket = '1606123463';
		$content = '您的验证码是：'.$send_ticket.'。请不要把验证码泄露给其他人。';
		dump(sendsms('13636690679', $content));exit;
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
	public function send_message(){
		if(IS_AJAX && isset($_REQUEST['ticket_info'])){
			$data = I('ticket_info');
			$where['order_id'] = $data['order_id'];
			$where['uid'] = 6;//session('uid');
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