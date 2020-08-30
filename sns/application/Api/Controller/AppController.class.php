<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Author: XiaoYuan <767684610@qq.com>
// +----------------------------------------------------------------------
/**
 * Api 访问授权
 * 创建日期：2017-04-07
 */

namespace Api\Controller;

use Think\Controller;

class AppController extends Controller
{
    protected $app_id;
    protected $app_secret;
    public $dataL;

    function _initialize()
    {
        $this->app_id = C('MAPP_ID');
        $this->app_secret = C('MAPP_SECRET');
        $raw_post_data = file_get_contents('php://input', 'r');
        $this->dataL = "";
        if ($raw_post_data) {
            $this->dataL = json_decode($raw_post_data, true);
        }
    }

    //验证客户端 token，没有则返回无效token
    protected function checkAppToken()
    {
        if (!isset($_GET['access_token']) || !S(C('APP_ACCESS_TOKEN_B_') . $_GET['access_token']) || is_null($_GET['access_token'])) {
            $this->apiReturn('', '无效的access_token', -1);
        }
    }


    //验证用户token
    protected function checkUserToken()
    {
        $user_access_token = $_REQUEST['user_access_token'];
        $user_id = get_user_id($user_access_token);
        if (!$user_id) {
            $this->apiReturn('', '用户未登录或已失效', -2);
        }
        return $user_id;
    }

    /**
     * [apiReturn 用于给app提供接口使用 带有请求结果状态表示,和结果提示，默认返回json]
     * @param  [number] $status  [请求结果的状态标识，设定后要在文档中给予说明]
     * @param  string $message [请求结果的提示语句]
     * @param  [array] $data    [请求返回的数据,app前端需要的数据]
     * @param  [string] $type    [要返回的数据类型，支持json,xml，默认返回json]
     * @return [json或xml]          [返回数据]
     */
    protected function apiReturn($data, $message = 'success', $status = 1, $type = 'json')
    {
        if (!is_numeric($status) || !is_string($message)) {
            $this->apiReturn('0', '参数错误');
        }
        $res = array();
        $res['msg'] = $message;
        $res['status'] = $status;
        if (!empty($data)) {
            $res = array_merge($res, $data);
        }
        $this->ajaxReturn($res, $type);

    }

    /**
     * @param null $msg 返回正确的提示信息
     * @param flag success CURD 操作成功
     * @param array $data 具体返回信息
     * Function descript: 返回带参数，标志信息，提示信息的json 数组
     *
     */
    protected function apiSuccess($msg = 'success', $type = 'json')
    {
        $result = array(
            'status' => '1',
            'msg' => $msg
        );
        $this->ajaxReturn($result, $type);
    }

    /**
     * @param null $msg 返回具体错误的提示信息
     * @param flag success CURD 操作失败
     * Function descript:返回标志信息 ‘Error’，和提示信息的json 数组
     */
    public function apiError($msg = 'error', $type = 'json')
    {
        $result = array(
            'status' => '0',
            'msg' => $msg,
        );

        $this->ajaxReturn($result, $type);
    }

    /**
     * 空操作
     * @return error
     */
    public function _empty($name)
    {
        $this->apiError('访问错误');
    }
    /**
     * 对ws服务器进行通信
     * @code 提交给ws服务器的协议参数
     * @param 提交给ws服务器的数据参数
     * @path  ws服务器的请求路径
     * @return
     */
    public function sendToWsServer($code,$userList,$param,$path){
        $wsHost = "127.0.0.1:3000";
        $url = $wsHost.'/'.$path;
        $reqData = array(
            'code'=>$code,
            'uidList'=>$userList,
            'data'=>$param
        );
        $req = curl_http($url,'POST',$reqData);
        if($req&&$req.status === 0){
            return true;
        }else{
            return false;
        }
    }
}