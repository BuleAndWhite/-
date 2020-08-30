<?php
/**
 * Created by PhpStorm.
 * User: 仙瑜005
 * Date: 2020/7/15
 * Time: 11:21
 */
/**
 * 用户登录相关
 * 创建日期：2017-04-07
 */


namespace Api\Controller;
use Think\Controller;

class UserLoginController extends Controller
{

    protected $app_id;
    protected $app_secret;
    public $dataL;

    function __construct()
    {
        $this->app_id = C('MAPP_ID');
        $this->app_secret = C('MAPP_SECRET');
        $raw_post_data = file_get_contents('php://input', 'r');
        $this->dataL = "";
        if ($raw_post_data) {
            $this->dataL = json_decode($raw_post_data, true);
        }
    }

    /**
     * Notes: 账号密码登录
     * name wuyu
     * data 2020年8月26日19：38
     * $password  密码
     * $phoneNumber 手机号码
     * $token 登录时随机生成的token
     */

    public function userLogin()
    {
        //接受用户登录所填写的账号密码数据

        $password = I('password');
        $phoneNumber = I('phoneNumber');

//        $num=mt_rand(100000,999999).time();
//        $sort=dechex($num);
//        $this->ajaxReturn($sort, "json");

        $where = [
            "phone" => $phoneNumber
        ];
        $Salt = M('user')->where($where)->getField(salt);
        if (!$Salt) {
            $result = array(
                'status' => '201',
                'msg' => "用户不存在"
            );
            $this->ajaxReturn($result, "json");
        } else {
            $pwd = md5(MD5($password . $Salt));
            $where = [
                "password" => $pwd,
            ];
            $pwd_res = M('user')->where($where)->getField(id);

            if (!$pwd_res) {
                $result = array(
                    'status' => '202',
                    'msg' => "密码错误"
                );
                $this->ajaxReturn($result, "json");
            } else {
                //token自定义生成32位
                $token_son = $pwd_res . time() . mt_rand(000000, 999999);
                $user_token = md5(MD5($token_son));
                $time = time();
                $res = M('user')->where(['id' => $pwd_res])->save(['user_token' => $user_token, 'cut_page_time' => $time, last_login_time => date("Y-m-d H:i:s", $time)]);
                if ($res) {
                    $result = array(
                        'status' => '200',
                        'msg' => '登陆成功',
                        'token' => $user_token
                    );
                    $this->ajaxReturn($result, "json");
                }
            }
        }
    }

    /**
     * Notes: 用户退出
     * name wuyu
     * data 2020年8月27日10:44
     */
    public function outLogin()
    {
        $result = array(
            'status' => '200',
            'msg'=>'用户退出成功'
        );
        $this->ajaxReturn($result, "json");
    }
    /**
     * Notes:请求发送验证码
     * name wuyu
     * data 2020年8月27日10:44
     */
    public function sen_code()
    {
        !IS_POST && $this->apiError('请求失败');
        $mobile=I("phone");
        $sen_code=new PublicController;
        $send=$sen_code->send_code($mobile);
        if($send){
            $result = array(
                'status' => '200',
                'msg'=>'验证码已发送手机'
            );
            $this->ajaxReturn($result, "json");
        }
    }
    /**
     * Notes:手机号验证码登录
     * name wuyu
     * data 2020年8月27日10:44
     */
    public function codeLogin()
    {
        !IS_POST && $this->apiError('请求失败');
        $mobile=I("phone");
        $where=[
            "phone"=>$mobile
        ];
        $phone_res=M('user')->where($where)->select();
        $code=I("code");
        $where=['code'=>$code,'is_use'=>0,'phone'=>$mobile];
        $res=M('code')->where($where)->select();
        //判断验证码有没有过期
        if(strtotime($res[0]["maturity_time"])<time()){
            $this->ajaxReturn(['status'=>205,'msg'=>'验证码过期请重新发送验证码'], "json");
        }
        if($phone_res){
            if($res){
                if($phone_res[0]["password"]!=null){
                    $token_son=$phone_res.time().mt_rand(000000,999999);
                    $user_token=md5(MD5($token_son));
                    $time=time();
                    M('user')->where(['id'=>$phone_res])->save(['user_token'=>$user_token,'cut_page_time'=>$time,last_login_time=>date("Y-m-d H:i:s", $time)]);
                    $this->ajaxReturn(['status'=>200,'msg'=>'登录成功',"token"=>$user_token], "json");
                }else{
                    $this->ajaxReturn(['status'=>202,'msg'=>'有过微信注册但没有设置密码',"phone"=>$mobile], "json");
                }
            }else{
                $this->ajaxReturn(['status'=>201,'msg'=>'注册过的验证码错误'], "json");    
            }
        }else{
            if($res){
                $this->ajaxReturn(['status'=>203,'msg'=>'没注册过验证码通过请设置密码',"phone"=>$mobile], "json");
            }else{
                $this->ajaxReturn(['status'=>204,'msg'=>'没注册过的验证码错误'], "json");   
            }
        }
    }
    /**
     * Notes:忘记密码手机验证码确认
     * name wuyu
     * data 2020年8月27日10:44
     */
    public function changeForCode()
    {
        !IS_POST && $this->apiError('请求失败');
        $mobile=I("phone");
        $where=[
            "phone"=>$mobile
        ];
        $resPhone=M('user')->where($where)->select();
        if($resPhone){
            $code=I("code");
            $res=M('code')->where(['code'=>$code,'is_use'=>0])->select();
            if(strtotime($res[0]["maturity_time"])<time()){
                 $this->ajaxReturn(['status'=>205,'msg'=>'验证码过期请重新发送验证码'], "json");
            }else{
                 $this->ajaxReturn(['status'=>200,'msg'=>'验证码通过请设置密码'], "json");
            }
        }else{
            $this->ajaxReturn(['status'=>206,'msg'=>'您的手机还未注册'], "json");
        }
    }
    /**
     * Notes：新用户注册
     * name wuyu
     * data 2020年8月27日10:44
     */
    public function creatUserData()
    {
        !IS_POST && $this->apiError('请求失败');
        $mobile=I("phone");
        $password=I('password');
        $num=mt_rand(100000,999999).time();
        $Salt=dechex($num);
        $password=md5(MD5($password.$Salt));
        $setResPhone=M('user')->add(['phone'=>$mobile,'password'=>$password,'user_token' => $user_token]);
        $token_son=$setResPhone.time().mt_rand(000000,999999);
        $user_token=md5(MD5($token_son));
        $time=time();
        $res = M('user')->where(['id' => $setResPhone])->save(['user_token' => $user_token, 'cut_page_time' => $time, last_login_time => date("Y-m-d H:i:s", $time)]);
        if($res){
            $this->ajaxReturn(['status'=>200,'msg'=>'新用户注册成功'], "json");
        }
    }
    /**
     * Notes：密码重新设置
     * name wuyu
     * data 2020年8月27日10:44
     */
    public function updataUserData()
    {
        !IS_POST && $this->apiError('请求失败');
        $mobile=I("phone");
        $password=I('password');
        $num=mt_rand(100000,999999).time();
        $Salt=dechex($num);
        $pwd=md5(MD5($password.$Salt));
        $resPhone=M('user')->where(['phone'=>$mobile])->field(id);
        $setResPhone=M('user')->where(['id'=>$resPhone])->save(['password'=>$pwd]);
        if($setResPhone){
            $this->ajaxReturn(['status'=>200,'msg'=>'密码重置成功'], "json");
        }else{
            $this->ajaxReturn(['status'=>201,'msg'=>'密码重置失败'],"json");
        }
    }

}




