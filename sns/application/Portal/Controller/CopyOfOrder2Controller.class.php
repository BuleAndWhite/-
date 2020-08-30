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
class Order2Controller extends HomebaseController {
	

    
    
    //首页
	public function index() {
// 		redirect('/admin');
         $url="https://open.weixin.qq.com/connect/oauth2/authorize?appid=".C('appid')."&redirect_uri=".C('huidiao')."&response_type=code&scope=snsapi_userinfo&state=yah#wechat_redirect";
        //  echo $url;
        header('Location:'.$url);
        
    }

    
    
    public function usertemp2(){
        header("content-type:text/html; charset=utf-8");
     
        if($_SESSION['user_email'])
        {
            
            $this->display(":order2");
        }else{
        
           
            $url='https://api.weixin.qq.com/sns/oauth2/access_token?appid='.C("appid").'&secret='.C('secret').'&code='.$_REQUEST['code'].'&grant_type=authorization_code';
            $res = file_get_contents($url); //获取文件内容或获取网络请求的内容
  
            
            //echo $res;
            $result = json_decode($res, true); //接受一个 JSON 格式的字符串并且把它转换为 PHP 变量
            
            if($result['errcode']){
                $url="https://open.weixin.qq.com/connect/oauth2/authorize?appid=".C('appid')."&redirect_uri=".C('huidiao')."&response_type=code&scope=snsapi_userinfo&state=yah#wechat_redirect";
                //  echo $url;
                header('Location:'.$url);
            }
            
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
            
            $this->assign("users",$users);
            $rs=M("oauth_user")->where("openid='".$users['openid']."'")->find();
            if($rs){
                // //已绑定
                $rs=M("users")->where("id='".$rs['uid']."'")->find();
               $_SESSION['user_email']=$rs['user_email'];
               $this->display(":order2");
                
            }else{
               //未绑定
                
                $this->display(":user");
                
            }
       
        }
      
    }
    
    
    public function yzisreg(){
        
        header("content-type:text/html; charset=utf-8");
       
     
        $tm=time()-120;
        $rs=M("radom")->where("tel='".$_REQUEST['tel']."' and radom='".$_REQUEST['yzm']."' and addtime >'".$tm."'")->find();
        if($rs){
            $rs=M("users")->where("user_email='".$_REQUEST['tel']."'")->find();
            if($rs){
                $data['from']="weixin";
                $data['name']=$_REQUEST['nickname'];
                $data['uid']=$rs['id'];
                $data['create_time']=time();
                $data['last_login_time']=time();
                $data['last_login_ip']=get_client_ip();
                $data['status']=1;
                $data['openid']=$_REQUEST['openid'];
                $rs=M("oauth_user")->where("openid='".$_REQUEST['openid']."'")->find();
                if($rs){
                    exit("4");
                }else{
                    M("oauth_user")->add($data);
                }
                $_SESSION["user_email"]=$_REQUEST['tel'];
                exit("1");
            }else{
                $data['nickname']=$_REQUEST['nickname'];
                $data['tel']=$_REQUEST['tel'];
                $data['openid']=$_REQUEST['openid'];
                $this->assign("users",$data);
                $rs= $this->fetch(":reg_bd_user");
                exit($rs);
            }
        }else{
            exit("2");
        }
    }

    
    
    public function yzisbd(){
    
        header("content-type:text/html; charset=utf-8");
        if(isset($_REQUEST['pwd'])){
            $data['pwd']=$_REQUEST['pwd'];
        }else{
            //没有获取密码
            exit("2");
        }
     
        
        $rs=$_REQUEST['datatmp'];

                $data['from']="weixin";
                $data['name']=$_REQUEST['nickname'];
               // $data['uid']=$rs['id'];
                $data['create_time']=time();
                $data['last_login_time']=time();
                $data['last_login_ip']=get_client_ip();
                $data['status']=1;
                $data['openid']=$_REQUEST['openid'];
                $rs=M("oauth_user")->where("openid='".$_REQUEST['openid']."'")->find();
                $res=M("users")->where("user_email='".$_REQUEST['tel']."'")->find();
                
                
                $dt['user_email']=$_REQUEST['tel'];
                $dt['user_pass']=md5($_REQUEST['pwd']);
                $dt['user_login']=$_REQUEST['tel'];
                $dt['user_nicename']=$_REQUEST['nickname'];
                $dt['last_login_ip']=get_client_ip() ;
                $dt['last_login_time']=time();
                $dt['create_time']=time();
                $dt['user_status']=1;
                $dt['user_type']=2;
                 
                
                if($rs){
                    //已注册用户
                    exit("4");
                }else{
                    if($res){
                        //已注册用户
                        exit("4");
                    }else{
                     if( M("users")->add($dt)){  
                        $rs= M("users")->where("user_email='".$_REQUEST['tel']."'")->find();
                         $data['uid']=$rs['id'];
                         M("oauth_user")->add($data);
                        $_SESSION["user_email"]=$_REQUEST['tel'];
                        exit("1");
                     }else{
                         //添加失败
                         exit("3");
                     }
                    }
                }
             
              
        
    }
    
   
    
    public function  three2(){
        
        if(isset($_REQUEST['piaoid'])){
            
            
            $rs=M("ticket")->where("ticket_id=".$_REQUEST['piaoid'])->find();
            
            exit('<div style="width:100%;height:100%;margin-top:20px;color:white;">
		<div style="float:left;margin-left:10px;">
			<div style="font-size:26px">
				在线订票
			</div>
			<div>
			T: (021)64783333-2371
			</div>
		</div>
			<div style="float:right;margin-top:5px;margin-right:10px">
			<div>
			<div style="float:left;cursor:pointer"  onclick="myorder()" ><img style="width:20px" src="/themes/simplebootx/Public/images/fenlei.png"  /></div><div style="float:left;margin-top:-1px;margin-left:10px;cursor:pointer"  onclick="myorder()">我的订单</div>
			</div>
		</div>
            
		<div style="clear:both"></div>
            
		<div style="margin-left:10px;margin-top:20px;margin-right:10px">
                <div>请选择数量和取票方式</div>
			<img src="/themes/simplebootx/Public/images/t3.png"  style="width:100%;margin-top:-20px" />
		</div>
            
		<div style="margin-top:10px;margin-left:10px;margin-right:10px">
            
            
		<table class="datatable1" cellpadding="10" cellspacing="0" style="width:100%">
              <tbody>
              <tr style="font-size:12px">
                <th width="30%" style="border-bottom:#f7aa66 1px solid;">类型</th>
                <th width="5%" ></th>
                <th width="30%" style="border-bottom:#f7aa66 1px solid;">数量</th>
                <th width="5%" ></th>
                <th width="30%" style="border-bottom:#f7aa66 1px solid;">价格</th>
              </tr>
               <tr >
              	<td  style="">
              	</td>
              	</tr>
              <tr >
              	<td  style="position:relative;">
              	<div id="pj" style="background:url(/themes/simplebootx/Public/images/tik1.png) no-repeat;background-size:150px 70px;width:150px;font-size:22px;padding-left:15px;line-height:75px;color:black;font-weight:bold">
						'.(int)$rs['price'].'
                 
              	</td>
              	<td>
        
              	</td>
              	<td style="text-align:center">
              		<input type="text"  value="1"  id="num"  isrt="1" style="border:2px solid  #f7aa66;border-radius:50px;width:50px;height:25px;outline:none; text-align:center" onkeyup="ak(this)" />
              	</td>
            
            
	              <td>
	       
	              </td>
	       
	              <td  style="text-align:center">
	             	 RMB<font size="+2" id="zj">	'.(int)$rs['price'].'</font>
	              </td>
              </tr>
            
            
            </tbody></table>
            
		<div style="width:100%;border-bottom:1px solid #f7aa66;margin-top:20px">
		</div>
            
		<div style="margin-top:50px">
            
		<div style="float:left;width:50px;position:relative;font-size:14px;cursor:pointer" value="ziqu"  class="qupiao">
            
            
				自取
			<img src="/themes/simplebootx/Public/images/yes1.png"  class="gou" style="position:absolute;top:-20px;right:-10px;z-index:1" />
            
		</div>
            
            
		<div style="float:left;width:50px;position:relative;font-size:14px;color:black;margin-left:50px;cursor:pointer" value="kuaidi"   class="qupiao">
            
            
				快递
			<img src="/themes/simplebootx/Public/images/yes1.png"  class="gou"  style="position:absolute;top:-20px;right:-10px;z-index:1;display:none" />
            
		</div>
            
            
		<div style="float:right;width:300px" id="ziqu" class="fs">
            
		网购窗口取票时间：9:00-18:00
请务必带好订票人身份证
(每日16点前付款，次日领票，16点之后，隔日领票)
		</div>
            
		<div style="float:right;width:300px;display:none" id="kuaidi" class="fs">
				<div style="color:black;font-size:14px">
				快递费&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			<font color="white">RMB12</font>
				</div>
            
				<div style="color:black;font-size:14px">
				保价(票价5‰)&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				<font  color="white"  id="baojia">RMB0.75</font>
				</div>
            
            
    
            
            
		</div>
            
		</div>
            
            
            
            
		<div style="clear:both"></div>
            
      <div  id="address" style="display:none;margin-top:10px">
      <div>
        收货信息
      </div>
        <table>
                <tr>
                        <td>
                                姓名
                        </td>
                
                <td>
                                <input type="text" name="xm" id="xm"  style="border:2px solid #ccc;border-radius:50px;wdith:80px"  />
                        </td>
                </tr>
                <tr>
                         <td>
                               手机号
                        </td>
                        <td>
                                <input type="text" name="mobile" id="mobile"  style="border:2px solid #ccc;border-radius:50px;wdith:80px"  />
                        </td>
                </tr>
                <tr>
                        <td>
                              邮编
                        </td>
                        <td>
                                <input type="text" name="youbian" id="youbian"  style="border:2px solid #ccc;border-radius:50px;wdith:80px"  />
                        </td>
                </tr>
            
                <tr>
                        <td>
                                地址
                        </td>
                        <td >
                          
                                    <select  id="quyu">
                                            <option value="黄浦区">
                                                    黄浦区
                                            </option>
                                          <option value="卢湾区">
                                                 卢湾区
                                            </option >
                                            <option value="徐汇区">
                                                 徐汇区
                                            </option>
                                             <option value="长宁区">
                                                长宁区
                                            </option>
                                            <option value="静安区">
                                                 静安区
                                            </option><option value="普陀区">
                                                普陀区
                                            </option><option value="闸北区">
                                                 闸北区
                                            </option><option value="虹口区">
                                                 虹口区
                                            </option><option value="杨浦区">
                                                 杨浦区
                                            </option><option value="闵行区">
                                                 闵行区
                                            </option><option value="宝山区">
                                                 宝山区
                                            </option><option value="嘉定区">
                                                 嘉定区
                                            </option><option value="浦东新区">
                                                 浦东新区
                                            </option><option value="金山区">
                                                 金山区
                                            </option><option value="松江区">
                                                 松江区
                                            </option><option value="青浦区">
                                                 青浦区
                                            </option><option value="南汇区">
                                                 南汇区
                                            </option><option value="奉贤区">
                                                 奉贤区
                                            </option><option value="崇明县">
                                                 崇明县
                                            </option>
                                    </select>
                
      <input type="text" name="dizhi" id="dizhi"  style="border:2px solid #ccc;border-radius:50px;"  />
                        </td>
             
                </tr>
            
            
         </table>
            
      </div>
            
            
            
		<div style="margin-top:50px;cursor:pointer;" id="isfapiao">
			需要发票
		</div>
            
		<div style="display:none;font-size:12px;margin-top:20px"  id="fapiao">
		<font color="#f7aa66" >发票抬头：</font>
		<span id="taitou">个人</span>&nbsp;&nbsp;<a style="color:black;cursor:pointer" id="edittaitou">修改</a>
		</div>
            
            
            
            
            
	<div style="width:100%;border-bottom:1px solid #f7aa66;margin-top:20px">
		</div>
            
            
		<div style="float:right;margin-top:20px">
		<div style="float:left">总计</div>
            
		<div style="float:left;color:#FCE77E;font-size:18px">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;RMB</div>
		<div style="float:left;color:#FCE77E;font-size:18px" id="zuihoujia">'.$rs['price'].'</div>
		</div>
            
            
		</div>
            
		<div style="clear:both">
		</div>
            
	<div style="margin-top:50px;margin-left:10px;margin-right:10px">
		<div style="float:left;color:white;cursor:pointer;" onclick="fhtwo()"  class="fhtwo"><上一步</div>
			<div style="float:right;color:white;cursor:pointer;" class="three">
            
      <div style="float:left">
      结算方式: &nbsp; &nbsp;
      </div>
      <div style="float:left">
                <img src="/themes/simplebootx/Public/images/zfb.png" onclick="order(1)"  />
      &nbsp;
      &nbsp;
              <img src="/themes/simplebootx/Public/images/wx.png"  onclick="order(1)"   />
            
      </div>
            </div>
	</div>
            
            
	</div>
            
            
            
      <script>
            
      	var isxuyaofapiao=1;
	$("#isfapiao").click(function(){
		if(isxuyaofapiao==1){
			isxuyaofapiao=2;
			$("#fapiao").show();
		}else{
		
			isxuyaofapiao=1;
			$("#fapiao").hide();
		}
            
	})
            
            
	$("#edittaitou").click(function(){
		var tmp=$("#taitou").html();
		if(tmp=="个人"){
		
			tmp="";
		}
		$("#edittaitou").hide();
		$("#taitou").html("<input type=\'text\'  id=\'gaitaitou\' onblur=\'gblur()\' value=\'"+tmp+"\'  />");
		$("#gaitaitou").focus();
            
	});
$(".qupiao").click(function(){
            
		if($(this).attr("value")=="kuaidi"){
            
            $("#piaoqu").val(2);
        $("#address").show();
			$("#num").attr("isrt","2");
			var baoj=$("#num").val()*$("#pj").html()*0.005;
            
			var zpj=$("#pj").html()*$("#num").val()+12+baoj;
			$("#baojia").html("RMB"+baoj);
			var zpj2=$("#pj").html()*$("#num").val();
			$("#zj").html(zpj2);
			$("#zuihoujia").html(zpj);
		
		}else{
            $("#piaoqu").val(1);
        $("#address").hide();
			$("#num").attr("isrt","1");
			var zpj=$("#pj").html()*$("#num").val();
			$("#zj").html(zpj);
			$("#zuihoujia").html(zpj);
		}
            
            
		$(".qupiao").css("color","black");
		$(this).css("color","white");
		$(".gou").hide();
		$(this).children(".gou").show();
		$(".fs").hide();
		$("#"+$(this).attr("value")).show();
            
	});
            
      </script>
            
            
            
            
            
      ');
            
        }
        
 
     
        
        
    }
    
    public function  three(){
    
        if(isset($_REQUEST['piaoid'])){
    
    
            $rs=M("ticket")->where("ticket_id=".$_REQUEST['piaoid'])->find();
    
            exit('<div style="width:100%;height:100%;color:white;">
		<div style="float:left;margin-left:10px">
			<div style="font-size:26px">
				在线订票
			</div>
			<div style="color:white">
			T: (021)64783333-2371
			</div>
		</div>
			<div style="float:right;margin-top:5px;margin-left:10px;margin-right:10px">
			<div>
			<div style="float:left;cursor:pointer"  onclick="myorder()" ><img style="width:20px" src="/themes/simplebootx/Public/images/fenlei.png"  /></div><div style="float:left;margin-top:-1px;margin-left:10px;cursor:pointer"  onclick="myorder()">我的订单</div>
			</div>
		</div>
    
		<div style="clear:both"></div>
    
		<div style="margin-top:20px;margin-left:10px;margin-right:10px">
                <div>请选择数量和取票方式</div>
			<img src="/themes/simplebootx/Public/images/t3.png"  style="width:100%;margin-top:-20px" />
		</div>
    
		<div style="margin-top:10px;margin-left:10px;margin-right:10px">
    
    
		<table class="datatable1" cellpadding="10" cellspacing="0" style="width:100%">
              <tbody>
              <tr style="font-size:12px">
                <th width="30%" style="border-bottom:#f7aa66 1px solid;">类型</th>
                <th width="5%" ></th>
                <th width="30%" style="border-bottom:#f7aa66 1px solid;">数量</th>
                <th width="5%" ></th>
                <th width="30%" style="border-bottom:#f7aa66 1px solid;">价格</th>
              </tr>
               <tr >
              	<td  style="">
              	</td>
              	</tr>
              <tr >
              	<td  style="position:relative;">
              	<div id="pj"  price="'.(int)$rs['price'].'" style="position:relative;background:url(/themes/simplebootx/Public/images/tik1.png) no-repeat;background-size:150px 70px;width:150px;font-size:22px;padding-left:15px;line-height:75px;color:black;font-weight:bold">
						'.(int)$rs['price'].'
                 <div style="position:absolute;top:20px;left:10px;font-size:10px;color:white">'.$rs["start_time"].'~'.$rs["end_time"].'</div>
					</div>
		
					
              	</td>
              	<td>
    
              	</td>
              	<td style="text-align:center">
              		<input type="text"  value="1"  id="num"  isrt="1" style="border:2px solid  #f7aa66;border-radius:50px;width:50px;height:25px;outline:none; text-align:center" onkeyup="ak2(this)" />
              	</td>
    
    
	              <td>
    
	              </td>
    
	              <td  style="text-align:center">
	             	 RMB<font size="+2" id="zj">	'.(int)$rs['price'].'</font>
	              </td>
              </tr>
    
    
            </tbody></table>
    
		<div style="width:100%;border-bottom:1px solid #f7aa66;margin-top:20px">
		</div>
    
		<div style="margin-top:50px">
    
		<div style="float:left;width:50px;position:relative;font-size:14px;cursor:pointer" value="ziqu"  >
    
    
				自取
			<img src="/themes/simplebootx/Public/images/yes1.png"  class="gou" style="position:absolute;top:-20px;right:-10px;z-index:1" />
    
		</div>
    
    
		
    
    
		<div style="float:right;width:300px" id="ziqu" class="fs">
    
		网购窗口取票时间：9:00-18:00
请务必带好订票人身份证
(每日16点前付款，次日领票，16点之后，隔日领票)
		</div>
    
		<div style="float:right;width:300px;display:none" id="kuaidi" class="fs">
				<div style="color:black;font-size:14px">
				快递费&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			<font color="white">RMB12</font>
				</div>
    
				<div style="color:black;font-size:14px">
				保价(票价5‰)&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				<font  color="white"  id="baojia">RMB0.75</font>
				</div>
    
    
    
    
    
		</div>
    
		</div>
    
    
    
    
		<div style="clear:both"></div>
    
      <div  id="address" style="display:none;margin-top:10px">
      <div>
        收货信息
      </div>
        <table>
                <tr>
                        <td>
                                姓名
                        </td>
                        <td>
                                <input type="text" name="xm" id="xm"  style="border:2px solid #ccc;border-radius:50px;wdith:80px"  />
                        </td>
                         <td>
                               手机号
                        </td>
                        <td>
                                <input type="text" name="mobile" id="mobile"  style="border:2px solid #ccc;border-radius:50px;wdith:80px"  />
                        </td>
                        <td>
                              邮编
                        </td>
                        <td>
                                <input type="text" name="youbian" id="youbian"  style="border:2px solid #ccc;border-radius:50px;wdith:80px"  />
                        </td>
                </tr>
    
                <tr>
                        <td>
                                地址
                        </td>
                        <td colspan="5">
                            上海市
                                    <select  id="quyu">
                                            <option value="黄浦区">
                                                    黄浦区
                                            </option>
                                          <option value="卢湾区">
                                                 卢湾区
                                            </option >
                                            <option value="徐汇区">
                                                 徐汇区
                                            </option>
                                             <option value="长宁区">
                                                长宁区
                                            </option>
                                            <option value="静安区">
                                                 静安区
                                            </option><option value="普陀区">
                                                普陀区
                                            </option><option value="闸北区">
                                                 闸北区
                                            </option><option value="虹口区">
                                                 虹口区
                                            </option><option value="杨浦区">
                                                 杨浦区
                                            </option><option value="闵行区">
                                                 闵行区
                                            </option><option value="宝山区">
                                                 宝山区
                                            </option><option value="嘉定区">
                                                 嘉定区
                                            </option><option value="浦东新区">
                                                 浦东新区
                                            </option><option value="金山区">
                                                 金山区
                                            </option><option value="松江区">
                                                 松江区
                                            </option><option value="青浦区">
                                                 青浦区
                                            </option><option value="南汇区">
                                                 南汇区
                                            </option><option value="奉贤区">
                                                 奉贤区
                                            </option><option value="崇明县">
                                                 崇明县
                                            </option>
                                    </select>
    
      <input type="text" name="dizhi" id="dizhi"  style="border:2px solid #ccc;border-radius:50px;width:300px"  />
                        </td>
       
                </tr>
    
    
         </table>
    
      </div>
    
    
    
		<div style="margin-top:50px;cursor:pointer;" id="isfapiao">
			需要发票
		</div>
    
		<div style="display:none;font-size:12px;margin-top:20px"  id="fapiao">
		<font color="#f7aa66" >发票抬头：</font>
		<span id="taitou">个人</span>&nbsp;&nbsp;<a style="color:black;cursor:pointer" id="edittaitou">修改</a>
		</div>
    
    
    
    
    
	<div style="width:100%;border-bottom:1px solid #f7aa66;margin-top:20px">
		</div>
    
    
		<div style="float:right;margin-top:20px">
		<div style="float:left">总计</div>
    
		<div style="float:left;color:#FCE77E;font-size:18px">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;RMB</div>
		<div style="float:left;color:#FCE77E;font-size:18px" id="zuihoujia">'.$rs['price'].'</div>
		</div>
    
    
		</div>
    
		<div style="clear:both">
		</div>
    
	<div style="margin-top:50px;margin-left:10px;margin-right:10px">
		<div style="float:left;color:white;cursor:pointer;" onclick="fhtwo()"  class="fhtwo"><上一步</div>
			<div style="float:right;color:white;cursor:pointer;" class="three">
    
      <div style="float:left">
      结算方式: &nbsp; &nbsp;
      </div>
      <div style="float:left">
                <img src="/themes/simplebootx/Public/images/zfb.png" onclick="order(1)"  />
      &nbsp;
      &nbsp;
              <img src="/themes/simplebootx/Public/images/wx.png"  onclick="order(1)"   />
    
      </div>
            </div>
	</div>
    
    
	</div>
    
    
    
      <script>
    
      	var isxuyaofapiao=1;
	$("#isfapiao").click(function(){
		if(isxuyaofapiao==1){
			isxuyaofapiao=2;
			$("#fapiao").show();
		}else{
    
			isxuyaofapiao=1;
			$("#fapiao").hide();
		}
    
	})
    
    
	$("#edittaitou").click(function(){
		var tmp=$("#taitou").html();
		if(tmp=="个人"){
    
			tmp="";
		}
		$("#edittaitou").hide();
		$("#taitou").html("<input type=\'text\'  id=\'gaitaitou\' onblur=\'gblur()\' value=\'"+tmp+"\'  />");
		$("#gaitaitou").focus();
    
	});
$(".qupiao").click(function(){
    
		if($(this).attr("value")=="kuaidi"){
    
            $("#piaoqu").val(2);
        $("#address").show();
			$("#num").attr("isrt","2");
			var baoj=$("#num").val()*$("#pj").html()*0.005;
    
			var zpj=$("#pj").html()*$("#num").val()+12+baoj;
			$("#baojia").html("RMB"+baoj);
			var zpj2=$("#pj").html()*$("#num").val();
			$("#zj").html(zpj2);
			$("#zuihoujia").html(zpj);
    
		}else{
            $("#piaoqu").val(1);
        $("#address").hide();
			$("#num").attr("isrt","1");
			var zpj=$("#pj").html()*$("#num").val();
			$("#zj").html(zpj);
			$("#zuihoujia").html(zpj);
		}
    
    
		$(".qupiao").css("color","black");
		$(this).css("color","white");
		$(".gou").hide();
		$(this).children(".gou").show();
		$(".fs").hide();
		$("#"+$(this).attr("value")).show();
    
	});
    
      </script>
    
    
    
    
    
      ');
    
        }
    
    
         
    
    
    }
    
    public  function two2() {
       
        if(isset($_REQUEST['iscg'])&&$_REQUEST['iscg']=='yes2'){
            
    
           $rs=M("ticket")->where("parent_id=2")->select();     
           $key=1;
            foreach ($rs as $k=>$r)  {
                if($key!=1){
                    $ts="display:none";
                }else{
                    $ts="";
                    $posa='id="first1"';
                }
                $str.='<td style="float:left;width:150px;position:relative;margin-left:10px;" '.$posa.'  class="xp" value="p'.$key.'"  piaoid="'.$r["ticket_id"].'"  >
            					<div style="background:url(/themes/simplebootx/Public/images/tik1.png) no-repeat;background-size:150px 70px;width:150px;font-size:26px;padding-left:15px;line-height:70px;cursor:pointer">
            					'.(int)$r['price'].'
            					</div>
            					<div style="position:absolute;top:-10px;right:-10px;z-index:1;'.$ts.'" class="gou">
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
			
						<div style="width:12%;float:left;margin-left:10px;">
							<div style="background:url(/themes/simplebootx/Public/images/b1.png) no-repeat;background-size:60 30;text-align:center;line-height:30px;width:60px">
								'.$a.'
							</div>
						</div>	';
                    
                }
              
					
					$str3='	
						
						<div style="clear:both;">
						</div>
						<div style="color:#CCC;font-size:12px;margin-top:20px;margin-left:10px;margin-right:10px">
								'.$r['comment'].'
						</div>
						
		</div>';
                
                $ac=$str0.$str2.$str3;
                $str4.=$ac;
                
                $key++;
            }
         
            exit('
                	<div style="width:100%;color:white;margin-top:20px;">
		<div style="float:left;margin-left:10px">
			<div style="font-size:26px">
				在线订票
			</div>
			<div>
			T: (021)64783333-2371
			</div>
		</div>
			<div style="float:right;margin-top:5px;margin-right:10px">
			<div>
			<div style="float:left;cursor:pointer"  onclick="myorder()" ><img style="width:20px" src="/themes/simplebootx/Public/images/fenlei.png"  /></div><div style="float:left;margin-top:-1px;margin-left:10px;cursor:pointer"  onclick="myorder()">我的订单</div>
			</div>
		</div>
		
		<div style="clear:both"></div>
		
		<div style="margin-top:20px;margin-left:10px;margin-right:10px">
                <div>请选择门票类型</div>
			<img src="/themes/simplebootx/Public/images//bg2.png"  style="width:100%;margin-top:-20px" />
		</div>
		
		<div style="margin-top:10px;margin-left:10px;margin-right:10px">
		
		<table style="width:100%">
			<tr>
                
          '.$str.'
					
					
					<td>
					<div style="clear:both"></div>
					
					
					
				</td>
                
                
                
			</tr>
		
		</table>
		
			
		
			
			
		</div>
		
		
		
		
		'.$str4.'
		
	
	
	
	
	<div style="color:#FFE599;MARGIN-TOP:20px;font-size:14px;margin-left:10px;margin-right:10px">
		如需购买当日优惠时段门票，特殊人群（儿童）门票，请到售票窗口直接购买；如您的家人、朋友已网购一张电子门票，只需再到乐园大门口领取当日活动卡，便可以通过在线购票快速通道直接加买当日的特价票。

在线购票一旦交易成功，本公司不办理退票、换票、退款、返还打折票价业务，本公司不会因为天气（高温、下雨）等原因办理退票手续。
	
	</div>
	
	
	
	<div style="margin-top:50px;margin-left:10px;margin-right:10px">
		<div style="float:left;color:white;cursor:pointer;"  class="fhone"><上一步</div>
			<div style="float:right;color:white;cursor:pointer;" onclick="two()" class="two">下一步></div>
	</div>
	
	
	</div>
	<script>
                
                $(".xp").click(function(){
		$(".gou").hide();
		$(this).children(".gou").show();
		
            $("#piaoid").val($(this).attr("piaoid"));    
                
		$(".pt").hide();
		$("#"+$(this).attr("value")).show();
		
	});
                
                
                $(".fhone").click(function(){
      $("#piaoid").val("");
           $("#body").html("    <div style=\'width:100%;margin-top:20px;color:white;\'><div style=\'float:left;margin-left:10px\'><div style=\'font-size:26px\'>在线订票	</div><div>T: (021)64783333-2371	</div></div>	<div style=\'float:right;margin-top:5px;margin-right:10px\'><div><div style=\'float:left;cursor:pointer\' onclick=\'myorder()\'><img style=\'width:20px\' src=\'/themes/simplebootx/Public/images/fenlei.png\'  /></div><div style=\'float:left;margin-top:-1px;margin-left:10px;cursor:pointer\' onclick=\'myorder()\'>我的订单</div></div></div><div style=\'clear:both\'></div><div style=\'margin-top:20px;margin-left:10px;margin-right:10px\'><div>请选择门票类型</div><img src=\'/themes/simplebootx/Public/images//bg1.png\'  style=\'width:100%;margin-top:-20px\' /></div><div style=\'margin-top:10px;\'> <center ><div style=\'margin-top:10%\'><div onclick=\'one()\'><div><img src=\'/themes/simplebootx/Public/images/piao.png\'  /></div><div style=\'color:white\'>当日票</div></div><div onclick=\'one2()\'><div><img src=\'/themes/simplebootx/Public/images/piao.png\'  /></div><div style=\'color:white\'>预售票</div></div></div></center></div></div></div>");
                
        });
   
               
        
       
           </script>     
                
                
                
                
                ');
            
          
            
        }
        
        
    }
    
    
    public  function two() {
         
        if(isset($_REQUEST['iscg'])&&$_REQUEST['iscg']=='yes'){
    
     
    
    
                $rs=M("ticket")->where("parent_id=1")->select();
                $key=1;
                foreach ($rs as $k=>$r)  {
                    if($key!=1){
                        $ts="display:none";
                    }else{
                        $ts="";
                        $posa='id="first1"';
                    }
                    $str.='<td style="float:left;width:150px;position:relative;margin-left:10px" '.$posa.'  class="xp" value="p'.$key.'"  piaoid="'.$r["ticket_id"].'"  >
            					<div style="position:relative;background:url(/themes/simplebootx/Public/images/tik1.png) no-repeat;background-size:150px 70px;width:150px;font-size:26px;padding-left:15px;line-height:70px;cursor:pointer">
            					'.(int)$r['price'].'
            					    <div style="position:absolute;top:22px;left:10px;font-size:10px">'.$r["start_time"].'~'.$r["end_time"].'</div>
            					</div>
            					<div style="position:absolute;top:-10px;right:0px;z-index:1;'.$ts.'" class="gou">
            						<img src="/themes/simplebootx/Public/images/yes1.png" style="" />
            					</div>
				</td>';
    
                     
                    $str0='<div  style="width:100%;margin-top:50px;'.$ts.'" id="p'.$key.'" class="pt">';
    
                
    
                    $str2='';
    
                 
    
                    	
                    $str3='
    
					
						<div style="color:#CCC;font-size:12px;margin-top:20px;margin-left:10px;margin-right:10px">
								'.$r['comment'].'
						</div>
    
		</div>';
    
                    $ac=$str0.$str2.$str3;
                    $str4.=$ac;
    
                    $key++;
                }
                 
                exit('
                	<div style="width:100%;color:white;margin-top:20px">
		<div style="margin-left:10px;float:left">
			<div style="font-size:26px">
				在线订票
			</div>
			<div>
			T: (021)64783333-2371
			</div>
		</div>
			<div style="float:right;margin-top:5px;margin-right:10px">
			      <div>
	             <div style="float:left;cursor:pointer"  onclick="myorder()" ><img style="width:20px" src="/themes/simplebootx/Public/images/fenlei.png"  /></div><div style="float:left;margin-top:-1px;margin-left:10px;cursor:pointer"  onclick="myorder()">我的订单</div>
			</div>
		</div>
    
		<div style="clear:both"></div>
    
		<div style="margin-top:20px;margin-left:10px;margin-right:10px">
                <div>请选择门票类型</div>
			<img src="/themes/simplebootx/Public/images/bg2.png"  style="width:100%;margin-top:-20px" />
		</div>
    
		<div style="margin-top:10px;margin-left:10px;margin-right:10px">
    
		<table style="width:100%">
			<tr>
    
          '.$str.'
			
			
					<td>
					<div style="clear:both"></div>
			
			
			
				</td>
    
    
    
			</tr>
    
		</table>
    
		
    
		
		
		</div>
    
    
    
    
		'.$str4.'
    
    
    
    
    
	<div style="color:#FFE599;MARGIN-TOP:20px;font-size:14px;margin-left:10px;margin-right:10px">
		如需购买当日优惠时段门票，特殊人群（儿童）门票，请到售票窗口直接购买；如您的家人、朋友已网购一张电子门票，只需再到乐园大门口领取当日活动卡，便可以通过在线购票快速通道直接加买当日的特价票。
    
在线购票一旦交易成功，本公司不办理退票、换票、退款、返还打折票价业务，本公司不会因为天气（高温、下雨）等原因办理退票手续。
    
	</div>
    
    
    
	<div style="margin-top:50px;margin-left:10px;margin-right:10px">
		<div style="float:left;color:white;cursor:pointer;"  class="fhone"><上一步</div>
			<div style="float:right;color:white;cursor:pointer;" onclick="two2()" class="two2">下一步></div>
	</div>
    
    
	</div>
	<script>
    
                $(".xp").click(function(){
		$(".gou").hide();
		$(this).children(".gou").show();
    
            $("#piaoid").val($(this).attr("piaoid"));
    
		$(".pt").hide();
		$("#"+$(this).attr("value")).show();
    
	});
    
    
                $(".fhone").click(function(){
      $("#piaoid").val("");
                 $("#body").html("    <div style=\'width:100%;margin-top:20px;color:white\'><div style=\'float:left;margin-left:10px\'><div style=\'font-size:26px\'>在线订票	</div><div>T: (021)64783333-2371	</div></div>	<div style=\'float:right;margin-top:5px;margin-right:10px\'><div><div style=\'float:left;cursor:pointer\' onclick=\'myorder()\'><img style=\'width:20px\' src=\'/themes/simplebootx/Public/images/fenlei.png\'  /></div><div style=\'float:left;margin-top:-1px;margin-left:10px;cursor:pointer\' onclick=\'myorder()\'>我的订单</div></div></div><div style=\'clear:both\'></div><div style=\'margin-top:20px;margin-left:10px;margin-right:10px\'><div>请选择门票类型</div><img src=\'/themes/simplebootx/Public/images//bg1.png\'  style=\'width:100%;margin-top:-20px\' /></div><div style=\'margin-top:10px;\'> <center ><div style=\'margin-top:10%\'><div onclick=\'one()\'><div><img src=\'/themes/simplebootx/Public/images/piao.png\'  /></div><div style=\'color:white\'>当日票</div></div><div onclick=\'one2()\'><div><img src=\'/themes/simplebootx/Public/images/piao.png\'  /></div><div style=\'color:white\'>预售票</div></div></div></center></div></div></div>");
                
        });
  
        
    
    
           </script>
    
    
    
    
                ');
    
       
    
        }
    
    
    }
    
    
    
    
    public function random($length = 6 , $numeric = 0) {
	PHP_VERSION < '4.2.0' && mt_srand((double)microtime() * 1000000);
	
		$hash = sprintf('%0'.$length.'d', mt_rand(0, pow(10, $length) - 1));
	
	return $hash;
}


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


public function bdyzm(){
    $yzm=$this->random();
    $tel=$_REQUEST['tel'];

    $rs=M("users")->where("user_email='".$_REQUEST['tel']."'")->find();
  

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
    

public function Post($curlPost,$url){
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

public function xml_to_array($xml){
	$reg = "/<(\w+)[^>]*>([\\x00-\\xFF]*)<\\/\\1>/";
	if(preg_match_all($reg, $xml, $matches)){
		$count = count($matches[0]);
		for($i = 0; $i < $count; $i++){
		$subxml= $matches[2][$i];
		$key = $matches[1][$i];
			if(preg_match( $reg, $subxml )){
				$arr[$key] =$this-> xml_to_array( $subxml );
			}else{
				$arr[$key] = $subxml;
			}
		}
	}
	return $arr;
}
    public function dl(){
        
        if(isset($_REQUEST['tel'])&&isset($_REQUEST['pwd'])){
            if(!empty($_REQUEST['tel'])&&!empty($_REQUEST['pwd'])){
                $rs=M("users")->where("user_type=2 and user_email='".$_REQUEST['tel']."'  and  user_pass='".md5($_REQUEST['pwd'])."'")->find();
                if($rs){
                   
                    $_SESSION['user_email']=$rs['user_email'];
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
    
    
    
    
    public function order(){
      
   
        if(isset($_REQUEST['piaoid'])){
     
            $data['ticket_id']=$_REQUEST['piaoid'];
            $data['piaozhong_id']=$_REQUEST['piaotype'];
            $data['piaoqu']=$_REQUEST['piaoqu'];
            $data['isxuyaofapiao']=$_REQUEST['isxuyaofapiao'];
            $data['receiver_name']=$_REQUEST['xm'];
            $data['receiver_mobile']=$_REQUEST['mobile'];
            $data['receiver_zip']=$_REQUEST['youbian'];
            $data['receiver_district']=$_REQUEST['quyu'];
            $data['receiver_address']=$_REQUEST['dizhi'];
            	
            $data['receiver_province']="上海";
            $data['receiver_city']="上海市";
            $data['num']=$_REQUEST['num'];
            
            $data['invoice']=$_REQUEST['taitou'];
            $data['payway']=$_REQUEST['zhifufangshi'];
            $data['order_ip']=get_client_ip() ;
            $data["create_time"]=time();
            $user=M("users")->where("user_email='".$_SESSION['user_email']."'")->find();
            $data['uid']=$user['id'];
            
            $rs=M("ticket")->where("ticket_id ='".$_REQUEST['piaoid']."'")->find();
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
    
    
    public function myorder(){
           if(isset($_SESSION['user_email'])){
             $user=M("users")->where("user_email='".$_SESSION['user_email']."'")->find();
                $rs=M("order")->where("uid='".$user['id']."'")->order("create_time desc")->select();
                foreach ($rs as $key =>$r){
                    $os=M("ticket")->where("ticket_id='".$r['ticket_id']."'")->find();
                    $rs[$key]['start_time']=$os['start_time'];
                    $rs[$key]['end_time']=$os['end_time'];
                }
                $this->assign("item",$rs);
               echo  $this->fetch(":myorder");
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
    
    
    
    
    
}


