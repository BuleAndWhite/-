<?php
/**
 * 会员注册
 */
namespace User\Controller;
use Common\Controller\HomebaseController;
class LoginController extends HomebaseController {
	
	function index(){
		session('show_notice', 1);
		if (isset($_REQUEST['type']) && $_REQUEST['type'] == 'logout'){
			session('uid' , null);
			session('nickname' , null);
			session('user_email' , null);
		}
	    if(sp_is_user_login()){ //已经登录时直接跳到首页
	        redirect(__APP__);
	    }else{
	        $this->display(":login");
	    }
	}
	
	function active(){
		$this->check_login();
		$this->display(":active");
	}
	
	function doactive(){
		$this->check_login();
		$this->_send_to_active();
		$this->success('激活邮件发送成功，激活请重新登录！',U("user/index/logout"));
	}
	
	function find_pwd(){
		$this->display(":fpwd");
	}
	
	function do_pwd_reset(){
		!IS_AJAX && exit;
		if(!check_mobile_verify_code()){
			$this->error("验证码错误！");
		}
		else{
			$password = sp_password(I('password'));
			if(M('users')->where(array('mobile'=> I('mobile')))->setField('user_pass', $password)){
				$this->success('修改成功！');
			}
			else{
				$this->success('修改失败！');
			}
		}
	}
	function do_find_pwd(){
		if(IS_POST){
			if(!sp_check_verify_code()){
				$this->error("验证码错误！");
			}else{
				$mobile=I("post.mobile");
				$find_user=M('users')->where(array("mobile"=>$mobile))->find();
				if($find_user){
					$data['mobile'] = $mobile;
					$data['number'] = rand_number(1000, 999999);
					$data['time'] = time() + 300;
					session('find_pwd_info', $data);
					$send_content = '您的验证码是：'.$data['number'].'。请不要把验证码泄露给其他人。';
					$status = sendsms($data['mobile'], $send_content);
					if($status == 1){
						session('find_pwd_mobile', $data['mobile']);
						$this->success('验证码发送成功！', U('User/Login/pwd_reset'));
					}
					else{
						$this->error($status);
					}
				}else {
					$this->error("手机号不存在！");
				}
			}
			
		}
	}
	
	protected  function _send_to_resetpass($user){
		$options=get_site_options();
		//邮件标题
		$title = $options['site_name']."密码重置";
		$uid=$user['id'];
		$username=$user['user_login'];
	
		$activekey=md5($uid.time().uniqid());
		$users_model=M("Users");
	
		$result=$users_model->where(array("id"=>$uid))->save(array("user_activation_key"=>$activekey));
		if(!$result){
			$this->error('密码重置激活码生成失败！');
		}
		//生成激活链接
		$url = U('user/login/password_reset',array("hash"=>$activekey), "", true);
		//邮件内容
		$template =<<<hello
		#username#，你好！<br>
		<a href="http://#link#">http://#link#</a>
		请点击或复制下面链接进行密码重置：<br>
hello;
		$content = str_replace(array('http://#link#','#username#'), array($url,$username),$template);
	
		$send_result=sp_send_email($user['user_email'], $title, $content);
	
		if($send_result['error']){
			$this->error('密码重置邮件发送失败！');
		}
	}
	
	
	function pwd_reset(){
		!isset($_SESSION['find_pwd_mobile']) && redirect(U('User/Login/find_pwd'));
		$this->display(":pwd_reset");
	}
	
	function dopassword_reset(){
		if(IS_POST){
			if(!sp_check_verify_code()){
				$this->error("验证码错误！");
			}else{
				$users_model=M("Users");
				$rules = array(
						//array(验证字段,验证规则,错误提示,验证条件,附加规则,验证时间)
						array('password', 'require', '密码不能为空！', 1 ),
						array('repassword', 'require', '重复密码不能为空！', 1 ),
						array('repassword','password','确认密码不正确',0,'confirm'),
						array('hash', 'require', '重复密码激活码不能空！', 1 ),
				);
				if($users_model->validate($rules)->create()===false){
					$this->error($users_model->getError());
				}else{
					$password=sp_password(I("post.password"));
					$hash=I("post.hash");
					$result=$users_model->where(array("user_activation_key"=>$hash))->save(array("user_pass"=>$password,"user_activation_key"=>""));
					if($result){
						$this->success("密码重置成功，请登录！",U("user/login/index"));
					}else {
						$this->error("密码重置失败，重置码无效！");
					}
					
				}
				
			}
		}
	}
	
	
    //登录验证
    function dologin(){

    	if(!sp_check_verify_code()){
    		//$this->error("验证码错误！");
    	}
    	
    	$users_model=M("Users");
    	$rules = array(
    			//array(验证字段,验证规则,错误提示,验证条件,附加规则,验证时间)
    			array('mobile', 'require', '手机号不能为空！', 1 ),
    			array('password','require','密码不能为空！',1),
    	
    	);
    	if($users_model->validate($rules)->create()===false){
    		$this->error($users_model->getError());
    	}
    	
    	$mobile=$_POST['mobile'];
    	
    	if(preg_match('/^\d+$/', $mobile)){//手机号登录
    	    $this->_do_mobile_login();
    	}else{
    	    $this->_do_email_login(); // 用户名或者邮箱登录
    	}
    	
    	
    	 
    }
	
    private function _do_mobile_login(){
        $users_model=M('Users');
        $where['mobile']=$_POST['mobile'];
        $password=$_POST['password'];
        $result = $users_model->where($where)->find();
        if(!empty($result)){
            if(sp_compare_password($password, $result['user_pass'])){
                //$_SESSION["user"]=$result;
                //写入此次登录信息
                $data = array(
                    'last_login_time' => date("Y-m-d H:i:s"),
                    'last_login_ip' => get_client_ip(0,true),
                );
                $users_model->where(array('id'=>$result["id"]))->save($data);
                $redirect=empty($_SESSION['login_http_referer'])?__APP__:$_SESSION['login_http_referer'];
                $_SESSION['login_http_referer']="";
                $_SESSION['user_mobile'] = $result['mobile'];
                $_SESSION['user_email'] = $result['mobile'];
                $_SESSION['uid'] = $result['id'];
                //echo $redirect;
                $this->success("登录验证成功！", $redirect);
            }else{
                $this->error("密码错误！");
            }
        }else{
            $this->error("用户名不存在！");
        }
    }
    
    private function _do_email_login(){

        $username=$_POST['username'];
        $password=$_POST['password'];
        
        if(strpos($username,"@")>0){//邮箱登陆
            $where['user_email']=$username;
        }else{
            $where['user_login']=$username;
        }
        $users_model=M('Users');
        $result = $users_model->where($where)->find();
        $ucenter_syn=C("UCENTER_ENABLED");
        
        $ucenter_old_user_login=false;
         
        $ucenter_login_ok=false;
        if($ucenter_syn){
            setcookie("thinkcmf_auth","");
            include UC_CLIENT_ROOT."client.php";
            list($uc_uid, $username, $password, $email)=uc_user_login($username, $password);
             
            if($uc_uid>0){
                if(!$result){
                    $data=array(
                        'user_login' => $username,
                        'user_email' => $email,
                        'user_pass' => sp_password($password),
                        'last_login_ip' => get_client_ip(0,true),
                        'create_time' => time(),
                        'last_login_time' => time(),
                        'user_status' => '1',
                        'user_type'=>2,
                    );
                    $id= $users_model->add($data);
                    $data['id']=$id;
                    $result=$data;
                }
        
            }else{
                 
                switch ($uc_uid){
                    case "-1"://用户不存在，或者被删除
                        if($result){//本应用已经有这个用户
                            if(sp_compare_password($password, $result['user_pass'])){//本应用已经有这个用户,且密码正确，同步用户
                                $uc_uid2=uc_user_register($username, $password, $result['user_email']);
                                if($uc_uid2<0){
                                    $uc_register_errors=array(
                                        "-1"=>"用户名不合法",
                                        "-2"=>"包含不允许注册的词语",
                                        "-3"=>"用户名已经存在",
                                        "-4"=>"Email格式有误",
                                        "-5"=>"Email不允许注册",
                                        "-6"=>"该Email已经被注册",
                                    );
                                    $this->error("同步用户失败--".$uc_register_errors[$uc_uid2]);
                                     
                                     
                                }
                                $uc_uid=$uc_uid2;
                            }else{
                                $this->error("密码错误！");
                            }
                        }
        
                        break;
                    case -2://密码错
                        if($result){//本应用已经有这个用户
                            if(sp_compare_password($password, $result['user_pass'])){//本应用已经有这个用户,且密码正确，同步用户
                                $uc_user_edit_status=uc_user_edit($username,"",$password,"",1);
                                if($uc_user_edit_status<=0){
                                    $this->error("登陆错误！");
                                }
                                list($uc_uid2)=uc_get_user($username);
                                $uc_uid=$uc_uid2;
                                $ucenter_old_user_login=true;
                            }else{
                                $this->error("密码错误！");
                            }
                        }else{
                            $this->error("密码错误！");
                        }
                         
                        break;
                         
                }
            }
            $ucenter_login_ok=true;
            echo uc_user_synlogin($uc_uid);
        }
        //exit();
        if(!empty($result)){
            if(sp_compare_password($password, $result['user_pass'])|| $ucenter_login_ok){
                $_SESSION["user"]=$result;
                //写入此次登录信息
                $data = array(
                    'last_login_time' => date("Y-m-d H:i:s"),
                    'last_login_ip' => get_client_ip(0,true),
                );
                $users_model->where("id=".$result["id"])->save($data);
                $redirect=empty($_SESSION['login_http_referer'])?__ROOT__."/":$_SESSION['login_http_referer'];
                $_SESSION['login_http_referer']="";
                $ucenter_old_user_login_msg="";
        
                if($ucenter_old_user_login){
                    //$ucenter_old_user_login_msg="老用户请在跳转后，再次登陆";
                }
        
                $this->success("登录验证成功！", $redirect);
            }else{
                $this->error("密码错误！");
            }
        }else{
            $this->error("用户名不存在！");
        }
        
        
    }
}