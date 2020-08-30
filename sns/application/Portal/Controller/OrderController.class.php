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
/**
 * 首页
 */
header("content-type:text/html; charset=utf-8");
class OrderController extends HomebaseController {

  /*   public function usses(){
    	unset($_SESSION['user_email']);
    	unset($_SESSION['uid']);
    }
     */

    //首页
    public function index() {
    	$this->display(":order");
    }
  
    //第三步2
    public function  three2(){
    	if(!session('?uid')){
    		redirect(U('User/Login/index'));
    	}
    	if(isset($_REQUEST['piaoid']) && !empty($_REQUEST['piaoid'])){
            
            
            $this->rs=M("ticket")->where(array('id'=>I('piaoid')))->find();
            if($this->rs){
            	$this->display(":orderstep3_2");exit;
            }
            
            
        }
        $this->display(":error");
    }
    
    //第三步
    public function  three(){
    	if(!session('?uid')){
    		redirect(U('User/Login/index'));
    	}
        if(isset($_REQUEST['piaoid']) && !empty($_REQUEST['piaoid'])){
            $this->rs=M("ticket")->where(array('id'=>I('piaoid')))->find();
            if($this->rs){
            	$this->display(":orderstep3_1");exit;
            }
        }
        $this->display(":error");
    }
    
    //第二步2
    public  function two2() {
       
            if(isset($_SESSION['user_email'])){
            	session('show_notice', 1);
            	$now_time = date('H:i');
            	$now_date = time();
            	$param['parent_id'] = 2;
            	/*
            	$param['sale_start_time'] = array('lt', $now_time);
            	$param['sale_end_time'] = array('gt', $now_time);
            	*/
            	$param['start_date'] = array('lt', $now_date);
            	$param['end_date'] = array('gt', $now_date);
            	//$week = date('w') == '0' ? 7 : date('w');
            	//$param['_string'] = "find_in_set(" . $week . ",week)";
            	$rs=M("ticket")->where($param)->select();
           $key=1;
            foreach ($rs as $k=>$r)  {
                if($key!=1){
                    $ts="display:none";
                }else{
                    $ts="";
                    $posa='id="first1"';
                }
                $str.='<td style="width:150px;position:relative" '.$posa.'  class="xp" value="p'.$key.'"  piaoid="'.$r["id"].'"  >
            					<div style="background:url(/themes/simplebootx/Public/images/tik1.png) no-repeat;background-size:150px 70px;width:150px;font-size:26px;padding-left:15px;line-height:70px;cursor:pointer" class="my-ticket">
            					'.(int)$r['price'].'<div style="position:absolute;top:22px;left:10px;font-size:10px">入园：'.$r['start_time'].'~'.$r['end_time'].'</div>
            					</div>
            					<div style="position:absolute;top:-10px;right:0px;z-index:1;'.$ts.'" class="gou">
            						<img src="/themes/simplebootx/Public/images/yes1.png" style="" />
            					</div>
				</td>';
                
               
                $str0='<div  style="width:100%;margin-top:50px;'.$ts.'" id="p'.$key.'" class="pt">';
                
                $os=explode(",", $r['week']);
                
                $str2='';
                
                foreach ($os as $k=>$p){
                    
                    if($p==1){
                        $a="周一";
                    }
                    else if($p==2){
                        
                        $a="周二";
                    }else if($p==3){
                        
                        $a="周三";
                    }else if($p==4){
                        
                        $a="周四";
                    }else if($p==5){
                        
                        $a="周五";
                    }else if($p==6){
                        
                        $a="周六";
                    }else if($p==7){
                        
                        $a="周日";
                    }
                    $str2.='
			
						<div class="weekend-con">
							<div class="weekend">
								'.$a.'
							</div>
						</div>	';
                    
                }
              
					
					$str3='	
						
						<div style="clear:both;">
						</div>
						<div style="color:#FFF;font-size:12px;margin-top:20px;font-size:18px;">
								'.$r['comment'].'
						</div>
						
		</div>';
                
                $ac=$str0.$str2.$str3;
                $str4.=$ac;
                
                $key++;
            }
         
                $this->str=$str;
                $this->str4=$str4;
                $this->display(":orderstep2_2");
            
             }else{
                 
            	redirect(U('User/Login/index'));
             }
    }
    
    //第二步
    public  function two() {
         
            if(isset($_SESSION['user_email'])){
            	session('show_notice', 1);
    			$now_time = date('H:i');
    			$now_date = time();
    			$param['parent_id'] = 1;
    			$param['sale_start_time'] = array('lt', $now_time);
    			$param['sale_end_time'] = array('gt', $now_time);
    			$param['start_date'] = array('lt', $now_date);
    			$param['end_date'] = array('gt', $now_date);
    			$week = date('w') == '0' ? 7 : date('w');
    			$param['_string'] = "find_in_set(" . $week . ",week)";
                $rs=M("ticket")->where($param)->select();
                if(!$rs){
                	$this->display(':error');exit;
                }
                $key=1;
                foreach ($rs as $k=>$r)  {
                    if($key!=1){
                        $ts="display:none";
                    }else{
                        $ts="";
                        $posa='id="first1"';
                    }
                    $str.='<td style="width:150px;position:relative" '.$posa.'  class="xp" value="p'.$key.'"  piaoid="'.$r["id"].'"  >
            					<div style="position:relative;background:url(/themes/simplebootx/Public/images/tik1.png) no-repeat;background-size:150px 70px;width:140px;font-size:26px;padding-left:15px;line-height:70px;cursor:pointer">
            					'.(int)$r['price'].'
            					    <div style="position:absolute;top:22px;left:10px;font-size:10px">入园：'.$r["start_time"].'~'.$r["end_time"].'</div>
            					</div>
            					<div style="position:absolute;top:-10px;right:0px;z-index:1;'.$ts.'" class="gou">
            						<img src="/themes/simplebootx/Public/images/yes1.png" style="" />
            					</div>
				</td>';
    
                    $str0='<div  style="width:100%;margin-top:50px;'.$ts.'" id="p'.$key.'" class="pt">';
                    $str2='';
                    $str3='
						<div style="color:#FFF;font-size:12px;margin-top:20px;font-size:18px;">
								'.$r['comment'].'
						</div>
		</div>';
                    $ac=$str0.$str2.$str3;
                    $str4.=$ac;
    
                    $key++;
                }
                $this->str=$str;
                $this->str4=$str4;
                $this->display(":orderstep2_1");
            }else{
            	redirect(U('User/Login/index'));
            }
    }

    //随机数
    public function random($length = 6 , $numeric = 0) {
	PHP_VERSION < '4.2.0' && mt_srand((double)microtime() * 1000000);
	
		$hash = sprintf('%0'.$length.'d', mt_rand(0, pow(10, $length) - 1));
	
	return $hash;
}

//获取验证码
public function hqyzm(){
    $yzm=$this->random();
    $tel=$_REQUEST['tel'];
    
    $rs=M("users")->where("user_email='".$_REQUEST['tel']."'")->find();
    if($rs){
        exit("该手机号已注册");
        
    }else{
      
        $rs=M("radom")->where("tel='".$tel."'")->find();
        $data['radom']=$yzm;
        $data['ip']=get_client_ip() ;
        $data['addtime']=time();
        
        
        $url="http://106.ihuyi.cn/webservice/sms.php?method=Submit";
    $post_data = "account=cf_jiro01&password=yah136717&mobile=".$tel."&content=您的验证码是：".$yzm."。请不要把验证码泄露给其他人。";
        
   $gets =$this->xml_to_array($this->Post($post_data, $url));
 //   $gets['SubmitResult']['code']=2;
        if($gets['SubmitResult']['code']==2){
            if($rs){
                M("radom")->where("id=".$rs['id'])->save($data);
            }else{
                $data['tel']=$_REQUEST['tel'];
                M("radom")->add($data);
            }
            echo 1;
            exit();
             
        }else{
         echo $gets['SubmitResult']['msg'];
            exit();
        }
    }
}

//完成注册
public function dozc(){
    if(isset($_REQUEST['tel'])){
        $rs=M("users")->where("user_email='".$_REQUEST['tel']."'")->find();
        if($rs){
            exit("该手机号已注册!");
        }else{
            $tm=time()-120;
            $rs=M("radom")->where("tel='".$_REQUEST['tel']."' and radom='".$_REQUEST['yzm']."' and addtime >'".$tm."'")->find();

            if($rs){
                 
                $data['user_email']=$_REQUEST['tel'];
                $data['user_pass']=md5($_REQUEST['pwd']);
                $data['user_login']=$_REQUEST['tel'];
                $data['user_nicename']=$_REQUEST['nick'];
                $data['last_login_ip']=get_client_ip() ;
                $data['last_login_time']=time();
                $data['create_time']=time();
                $data['user_status']=1;
                $data['user_type']=2;
                 
                $rs=M("users")->add($data);
                if($rs){
                    
                   $_SESSION['user_email']=$_REQUEST['tel'];
                   $_SESSION['uid']=$rs;
                    exit("1");
                }else{
                    exit("注册失败");
                }
            }else{
                 
                exit("验证码错误或验证码已过期");
            }
        }
    }
}

//可删除，post函数
//public function Post($curlPost,$url){
//		$curl = curl_init();
//		curl_setopt($curl, CURLOPT_URL, $url);
//		curl_setopt($curl, CURLOPT_HEADER, false);
//		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
//		curl_setopt($curl, CURLOPT_NOBODY, true);
//		curl_setopt($curl, CURLOPT_POST, true);
//		curl_setopt($curl, CURLOPT_POSTFIELDS, $curlPost);
//		$return_str = curl_exec($curl);
//		curl_close($curl);
//		return $return_str;
//}


//可删除，post函数
//public function xml_to_array($xml){
//	$reg = "/<(\w+)[^>]*>([\\x00-\\xFF]*)<\\/\\1>/";
//	if(preg_match_all($reg, $xml, $matches)){
//		$count = count($matches[0]);
//		for($i = 0; $i < $count; $i++){
//		$subxml= $matches[2][$i];
//		$key = $matches[1][$i];
//			if(preg_match( $reg, $subxml )){
//				$arr[$key] =$this-> xml_to_array( $subxml );
//			}else{
//				$arr[$key] = $subxml;
//			}
//		}
//	}
//	return $arr;
//}

    //登录
    public function dl(){
        
        if(isset($_REQUEST['tel'])&&isset($_REQUEST['pwd'])){
            if(!empty($_REQUEST['tel'])&&!empty($_REQUEST['pwd'])){
                $rs=M("users")->where("user_type=2 and user_email='".$_REQUEST['tel']."'  and  user_pass='".md5($_REQUEST['pwd'])."'")->find();
                if($rs){
                   
                    $_SESSION['user_email']=$rs['user_email'];
                    $_SESSION['uid']=$rs['id'];
                    exit('1');
                }else{
                    
                    exit("2");
                }
            }else{
                exit("2");
            }
         
            
        }else{
         
            exit("2");
        }
    }

    //确认订单
    public function order(){
      
   
        if(isset($_REQUEST['piaoid'])){
     
            $data['ticket_id']=$_REQUEST['piaoid'];
            $data['piaozhong_id']=$_REQUEST['piaotype'];
      /*       $data['piaoqu']=$_REQUEST['piaoqu']; */
       /*      $data['isxuyaofapiao']=$_REQUEST['isxuyaofapiao']; */
       /*      $data['receiver_name']=$_REQUEST['xm'];
            $data['receiver_mobile']=$_REQUEST['mobile'];
            $data['receiver_zip']=$_REQUEST['youbian'];
            $data['receiver_district']=$_REQUEST['quyu'];
            $data['receiver_address']=$_REQUEST['dizhi'];
            	
            $data['receiver_province']="上海";
            $data['receiver_city']="上海市"; */
            $data['num']=$_REQUEST['num'];
            
       /*      $data['invoice']=$_REQUEST['taitou']; */
            $data['payway']=$_REQUEST['zhifufangshi'];
            $data['order_ip']=get_client_ip() ;
            $data["create_time"]=time();
            $data["status"]=1;
            $user=M("users")->where("user_email='".$_SESSION['user_email']."'")->find();
            $data['uid']=$user['id'];
            
            $rs=M("ticket")->where("id ='".$_REQUEST['piaoid']."'")->find();
            if($rs){
                
                $data['price']=$rs['price'];
               
                 
                 if($data['piaoqu']==2){
                     $data['ship_fee']=12;
                     $data['baojia']=$data['price']*$data['num']*0.005;
                     $data['total_money']=$data['price']*$data['num']+12+$data['baojia'];
                 }else{
                     $data['total_money']=$data['price']*$data['num'];
                 }
                 $data['order_id']=date("ym") . substr(time(), -1) . substr(microtime(), 2, 5) . sprintf('%02d', rand(0, 99));
                 
              
                 $rs=M("order")->add($data);
                 if($rs){
                      echo 1;
                        exit();
                 }else{
                     echo "订单提交繁忙！";
                       exit();
                 }
                 
                 
            }else{
                
                  echo "订单提交繁忙！";
                       exit();
                
            }
           
            
         
            
            
            
        }else{
            echo "订单提交繁忙！";
            exit();
    
        }
    
    
    }

    //我的订单
    public function myorder(){
        $this->title = '我的订单';
           if(isset($_SESSION['user_email'])){
             $user=M("users")->where(array('id'=>session('uid')))->find();
             
             //获取订单状态
             $rs=M("order")->where("uid='".$user['id']."' and status=1")->order("create_time desc")->select();
             
             foreach ($rs as $k=>$r){
             	/*
             	$json = '{"orderno":"'.$r['order_no'].'","userid":"91"}';
             	$param['json'] =addslashes($json) ;
             	$param['deviceno'] = 'HIFGGO9U';
             	$param['sign'] = get_sign($json);
             	 
             	$url=C('URL_ORDER_STATUS');
             	//echo  get_sign($json);
             	//  $result=$this->Post($param, $url);
             	$result=curl_http($url,'POST',$param);
             	//  $result=  file_get_contents($url);
             	//	$res=json_decode($result);
             	//echo $result['success'];
             	if($result['paystate']==1){
           		  	M("order")->where("uid='".$user['id']."' and id=".$r['id'])->save(array("status"=>2));
             	}//echo $result['paystate'];
             	*/
             }
            
             
             //4为未付款删除，5未已付款删除（支付成功），4,5不在查询范围内
             $param['status'] = array('not in', '4,5');
             $param['uid'] = $user['id'];
             
             $count=M("order")->where($param)->order("create_time desc")->count();
            
             if(isset($_REQUEST["num"])){
                $num=$_REQUEST["num"];
                 
             }else{
                 
                 $num=0;
             }
             
             if($num+5>=$count){
                 $isgd=0;
             }else{
                 $isgd=1;
             }
             
             /*
              $count = M()->table('__ORDER__ a,__ORDER_TICKET__ b,__TICKET__ c')
    		->where(array('a.order_id = b.order_id', 'b.ticket_id = c.ticket_id', $params))
    		->count();
    	$page = $this->page($count, 20);
    	$order_list = M()->table('__ORDER__ a,__ORDER_TICKET__ b,__TICKET__ c')
		    	->where(array('a.order_id = b.order_id', 'b.ticket_id = c.ticket_id', $params))
		    	->order('a.create_time desc')
		    	->field('a.id,c.name,a.order_id,a.receiver_name,a.receiver_mobile,a.create_time,a.status')
		    	->limit($page->firstRow . ',' . $page->listRows)
		    	->select();
    	//dump($order_list);
    	$this->assign("page", $page->show('Admin'));
    	*/
             $page = $this->page($count, 10);
             $this->assign("isgd",$isgd);
             	
                $rs=M("order")->where($param)->order("id desc")->limit($page->firstRow . ',' . $page->listRows)->select();
                foreach ($rs as $key =>$r){
                    
                  
                    $os=M("ticket")->where("id='".$r['ticket_id']."'")->find();
                    
                    if($r['status']==2&&$os['parent_id']==2&&$r['piaoqu']!=2){
                    	$r['caozuo']=2;
                    }elseif($r['status']==1){
                    	$r['caozuo']=1;
                    }else{
                    	$r['caozuo']=3;
                    }
                    
                    
                    
                    $rs[$key]['piaozhong_id']=$os['parent_id'];
                    
                    $rs[$key]['caozuo']=$r['caozuo'];
                    
                    $rs[$key]['start_time']=$os['start_time'];
                    $rs[$key]['end_time']=$os['end_time'];
                    
                    $order_ticket=M("order_ticket")->where("order_id=".$r['order_id'])->select();
                    
                    $rs[$key]['order_ticket']=$order_ticket;
                    
                }
                //dump($rs);
                $this->assign("page", $page->show());
                $this->assign("item",$rs);
               $this->display(":myorder");
         }else{
          	redirect(U("User/Login/index"));
         }
    }

    //我的订单--更多
    public function moreorder(){
        if(isset($_SESSION['user_email'])){
            $user=M("users")->where("user_email='".$_SESSION['user_email']."'")->find();
             
          
             
             
            $count=M("order")->where("uid='".$user['id']."'")->count();
    
          
                $num=$_REQUEST["num"];
                if($num>=$count){
                    echo 1;
                    exit();
                }else{
                        $num2=$num;
                        $rs=M("order")->where("uid='".$user['id']."'")->order("create_time desc")->limit($num,5)->select();
                        foreach ($rs as $key =>$r){
                        
                      
                           
                            $os=M("ticket")->where("id='".$r['ticket_id']."'")->find();
                            
                            if($r['status']==2&&$os['parent_id']==2&&$r['piaoqu']!=2){
                            	$r['caozuo']=2;
                            }elseif($r['status']==1){
                            	$r['caozuo']=1;
                            }else{
                            	$r['caozuo']=3;
                            }
                            $rs[$key]['caozuo']=$r['caozuo'];
                            $rs[$key]['start_time']=$os['start_time'];
                            $rs[$key]['end_time']=$os['end_time'];
                
                            $order_ticket=M("order_ticket")->where("order_id='".$r['order_id']."' and ticket_id='".$r["ticket_id"]."'")->select();
                
                            $rs[$key]['order_ticket']=$order_ticket;
                
                        }
                
                        //  dump($rs);
                        $this->assign("item",$rs);
                        $this->assign("num",$num2);
                        echo  $this->fetch(":moreorder");
                }
        }else{
            exit('
               <div style="position:absolute;top:0px;left:0px;bottom:0px;right:0px;width:100%;height:100%;">
	<div style="position:relative;top:30%;color:white">
                     <center>
			<table style="line-height:30px">
                     <tr>
                            <td>
                                    手机号
                            </td>
                             <td>
                                <input type="tel" id="tel" onblur="check(this)" style="border:2px solid #ccc;border-radius:50px"  />
                             </td>
      
                      </tr>
                     <tr>
                            <td>
                                    密码
                            </td>
                             <td>
                                <input type="password" name="pwd"  id="pwd" style="border:2px solid #ccc;border-radius:50px"  />
                             </td>
                      </tr>
      
                     <tr>
                     <td>
      
                     </td>
                        <td>
      
      
                         <div style="float:left;background:url(/themes/simplebootx/Public/images/b1.png) no-repeat;background-size:80px 40px;width:80px;height:40px;text-align:center;line-height:40px;"   onclick="dl()"  >
                       登录
                       </div>
                        	  <div style="float:left;margin-left:10px;background:url(/themes/simplebootx/Public/images/b1.png) no-repeat;background-size:80px 40px;width:80px;height:40px;text-align:center;line-height:40px;" onclick="zc()"  >
                       注册
                       </div>
      
       
                        </td>
                     </tr>
      
            </table>
                     </center>
    
	</div>
                     </div>
       
      
                     ');
        }
    }
    
    
    //保存快递
    public function baocunkuaidi(){
        
        if(isset($_REQUEST['orderid'])){
             $data['receiver_name']=$_REQUEST['xm'];
             $data['receiver_address']=$_REQUEST['dizhi'];
             $data['receiver_zip']=$_REQUEST['youbian'];
             $data['receiver_mobile']=$_REQUEST['mobile'];
            //$data['orderid']=$_REQUEST['orderid'];
             $data['piaoqu']=2;
             $rs=M("order")->where("id=".$_REQUEST['orderid'])->save($data);
             if($rs){
                 exit("1");
             }else{
                 exit("2");
             }
        }else{
            exit("2");
        }
        
    }
    
    
    
    //请求订单支付状态
    public function orderstatus($post_data,$url){
    	
    	$rs=$this->Post($post_data, $url);
    	return $rs;
    }
    //取消订单
    public function order_cancel(){
    	!IS_AJAX && EXIT;
    	$this->check_login();
    	$param['status'] = 1;
    	$param['order_id'] = I('order_id');
    	$order_no = M('order')->where($param)->getField('order_no');
    	if(get_order_pay_state($order_no)){
    	    file_get_contents('http://ticket.64783333.com/index.php/Api/System/index');
    	    $this->error('您已支付成功，请刷新页面重试！');
    	}
    	$result = M('order')->where($param)->setField('status', 2);
    	if($result){
    	    $json = '{"userid":"'.C('USER_ID').'","orderno":"'.$order_no.'"}';
    	    $param['json'] = addslashes($json);
    	    $param['deviceno'] = C('DEVICENO');
    	    $param['sign'] = get_sign($json);
    	    $rs = curl_http(C('ULR_CANCEL_ORDER'), 'POST', $param);
    		$this->success('订单取消成功！', U('Portal/Order/myorder'));
    	}
    	else{
    		$this->error('订单取消失败！');
    	}
    }
    //删除订单
    public function order_del(){
    	!IS_AJAX && EXIT;
    	$this->check_login();
    	$param['order_id'] = I('order_id');
    	$order_info = M('order')->where($param)->find();
    	if($order_info){
    		//4为未付款删除，5未已付款删除（支付成功）
    		if($order_info['status'] == 2){
    			$result = M('order')->where($param)->setField('status', 4);
    		}
    		else{
    		    \Think\Log::write(json_encode($order_info));
    			$result = M('order')->where($param)->setField('status', 5);
    		}
    		$this->success('订单已删除！', U('Portal/Order/myorder'));
    	}
    	else{
    		$this->error('删除失败！');
    	}
    }
    
}


