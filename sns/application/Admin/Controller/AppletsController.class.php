<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Author: XiaoYuan <767684610@qq.com>
// +----------------------------------------------------------------------
/**
 * 小程序访问入口
 * 创建日期：2017-04-24
 */

namespace Api\Controller;

use Think\Controller;

class AppletsController extends AppController
{
    public $orderid = null;
    protected $appid;
    protected $appsecret;
    protected $mch_id;
    protected $users_model;
    protected $temperature_model;
    protected $electrocardiogram_model;
    protected $blood_oxygen_model;
    protected $blood_sugar_model;
    protected $blood_pressure_model;
    protected $human_extracts_model;
    protected $weight_model;
    protected $pedometer_model;
    protected $urine_model;
    protected $cholesterol_model;
    protected $sleep_model;
    protected $watch_positioning_model;
    protected $cardio_cerebral_vessels_model;
    protected $token_model;
    protected $sign_model;
    protected $withdraw_model;
    protected $feedback_model;
    protected $address_model;
    protected $hemoglobin_model;
    protected $glycosylated_hemoglobin_model;
    protected $payment_add_model;
    protected $order_model;
    protected $small_code_model;
    protected $term_relationships_model;
    protected $message_content_model;
    protected $slide_model;
    protected $skin_test_model;
    protected $tongue_fur_model;
    protected $key;
    public $OK;
    public $IllegalAesKey;
    public $IllegalIv;
    public $IllegalBuffer;
    public $DecodeBase64Error;

    function __construct()
    {
        header("Access-Control-Allow-Origin: *");
        header("Content-Type: text/html; charset=UTF-8");
        //授权访问
//        parent::checkAppToken();
        parent::_initialize();
        $this->appid = "wxea6d1e32583c3461";
        $this->appsecret = "5691c51a06ca71d120448e29742e9f4d";
        $this->key = "shanghaikuaifengkejijiankang2018";
        $this->mch_id = "1502179691";
        //用户登录
        //parent::checkUserToken();
        $this->users_model = M('User');
        $this->temperature_model = M('Temperature');
        $this->electrocardiogram_model = M('Electrocardiogram');
        $this->blood_oxygen_model = M('BloodOxygen');
        $this->blood_sugar_model = M('BloodSugar');
        $this->blood_pressure_model = M('BloodPressure');
        $this->human_extracts_model = M('HumanExtracts');
        $this->weight_model = M('Weight');
        $this->pedometer_model = M('Pedometer');
        $this->urine_model = M('Urine');
        $this->cholesterol_model = M('Cholesterol');
        $this->sleep_model = M('Sleep');
        $this->watch_positioning_model = M('WatchPositioning');
        $this->cardio_cerebral_vessels_model = M('CardioCerebralVessels');
        $this->glycosylated_hemoglobin_model = M('GlycosylatedHemoglobin');
        $this->message_content_model = M('MessageContent');
        $this->slide_model = M('Slide');
        $this->feedback_model = M('Feedback');
        $this->token_model = M('Token');
        $this->sign_model = M('Sign');
        $this->withdraw_model = M('Withdraw');
        $this->address_model = M('Address');
        $this->hemoglobin_model = M('Hemoglobin');
        $this->payment_add_model = M('PaymentAdd');
        $this->order_model = M('Order');
        $this->small_code_model = M('SmallCode');
        $this->term_relationships_model = M('TermRelationships');
        $this->skin_test_model = M('SkinTest');
        $this->tongue_fur_model = M('TongueFur');
        $this->OK = "0";
        $this->IllegalAesKey = "-41001";
        $this->IllegalIv = "-41002";
        $this->IllegalBuffer = "-41003";
        $this->DecodeBase64Error = "-41004";
    }




    /**
     * name 刘柏林
     * data 2018年5月18日14:37:26
     * @return string
     * 推送消息
     */
    public function pushInformation($openid)
    {
        $access_token = $this->obtainTokens($this->appid, $this->appsecret);
        header("content-type:text/html;charset=utf-8");
        $c = new \Curl();
        $user_token = $this->Tokens($access_token);

//        $userLists = "https://api.weixin.qq.com/cgi-bin/user/info?access_token=".$user_token."&openid=oRSA91dydi7aPk1eviLbRuCOmBHo&lang=zh_CN";
//        print_r($c->get($userLists));
//        return ;

        //推送模板消息
//        $userList = "https://api.weixin.qq.com/cgi-bin/user/get?access_token=" . $user_token['access_token'];
//        $userList = "https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=" . $user_token;
//        $params = array('touser' => 'oRSA91dydi7aPk1eviLbRuCOmBHo', 'template_id' => '-eLD4SlLsLwdaS0UDJQpmGJYjM7ph926nPvr7juSmHU', 'url' => 'http://bolon.kuaifengpay.com/index.php/Api/Index/authorized/', 'topcolor' => '#173177');
//        $params['data'] = array();
//        $params['data']['first'] = array('value' => '您通过提交的体温报告', 'color' => '#173177');
//        $params['data']['keyword1'] = array('value' => "38度 \n建议：饮食上，早餐食用红枣薏米山药粥，用红枣、薏米、山药煮成粥，红枣可以补血；山药有健脾的作用；薏米有助于散除湿气。用生姜泡红茶，生姜中的辛辣成分能燃烧体内的", 'color' => '#173177');
//        $params['data']['keyword2'] = array('value' => date("Y-m-d H:i:s"), 'color' => '#173177');
//        $params['data']['remark'] = array('value' => '您的体温正常', 'color' => '#173177');
//        $post = json_encode($params);
//        print_r($c->post($userList, $post));
        $token = $openid;
        //推送消息
        $userList = "https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token=" . $user_token;
//        $params = array('touser' => 'oRSA91dydi7aPk1eviLbRuCOmBHo', 'msgtype' => 'text', 'text' => array("content"=>"您好，请点击菜单进入小程序查看体检报道。"));

        $post = '{"touser":"' . $token . '", "msgtype":"text", "text":{"content":"支付成功，请点击菜单进入小程序查看体检报告。"}}';
        print_r($c->post($userList, $post));

//        print_r($c->get($userList));
    }

    /**
     * name 刘柏林
     * data 2018年5月18日15:27:08
     * @return mixed
     * 获取token 判断token是否过期 过期重新获取 推送token
     */
    public function Tokens()
    {
        $access_tokens = S("access_tokens");
        if ($access_tokens) {
            return $access_tokens;
        } else {
            return $this->obtainTokens($this->appid, $this->appsecret);
        }
    }

    /**
     * name 刘北林
     * data 2018年5月18日15:12:17
     * @param $appid
     * @param $appsecret
     * @return mixed
     * 获取 access_token 推送token
     */
    public function obtainTokens($appid, $appsecret)
    {
        $c = new \Curl();
        $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=" . $appid . "&secret=" . $appsecret;
        $user = $c->get($url);
        $user_token = json_decode($user, true);
        S("access_tokens", $user_token['access_token'], 60 * 60 * 2);
        return $user_token['access_token'];
//        $c = new \Curl();
//
//        $url = "https://api.weixin.qq.com/sns/oauth2/refresh_token?appid=" . $appid . "&grant_type=refresh_token&refresh_token=" . S("refresh_token");
//        $user = $c->get($url);
//        $user_token = json_decode($user, true);
//        return $user_token;
    }


    /**
     * name 刘柏林
     * data 2018年5月18日15:27:08
     * @return mixed
     * 获取token 判断token是否过期 过期重新获取
     */
    public function Token()
    {
        $code = $_REQUEST['code'];
        if (!$code) {
            $this->apiError("非法错误");
        }
        $this->apiReturn($this->obtainToken($this->appid, $this->appsecret, $code));
    }


    /**
     * @param $unionid
     * @return float|int
     * 计算余额
     */
    public function totals($unionid){
        $userOne = M("user")->where(array("unionid" => $unionid))->find();
        $totals =0;
        $listU =array();
        if ($userOne) {

            $usersModel =M("users")->where(array("did"=>$userOne['sn']))->find();
            if($usersModel['user_type'] ==4){

                //余额
                $months =M('User')->where(array("parent_did" => $usersModel['did'], "is_vip" => 2))->count();//半年用户数量
                $year = M('User')->where(array("parent_did" => $usersModel['did'], "is_vip" => 3))->count();//一年用户数量
                $once = M('User')->where(array("parent_did" => $usersModel['did'], "is_vip" => 1))->count();//一年用户数量
                $twoYears = M('User')->where(array( "parent_did" => $usersModel['did'],"is_vip" => 4))->count();//一年用户数量
                $threeYears = M('User')->where(array("parent_did" => $usersModel['did'], "is_vip" => 5))->count();//一年用户数量
                $machineOwner= M('User')->where(array("parent_did" => $usersModel['did'], "is_vip" => 7))->count();//虚拟机主用户数量
                $owner = M('User')->where(array("parent_did" => $usersModel['did'], "is_vip" => 8))->count();//机主用户数量
                $total = M('User')->where(array("sn" => $usersModel['did'], "is_vip" => 5))->find();//机主总盈利
                $countTs = M('User')->where(array("parent_did" => $usersModel['did']))->count();//数量
                $total =$this->total($total['owner_id']);
                $machineOwner= $machineOwner * 10000;//天使
                $owner = $owner * 100000;
                $threeYears = $threeYears * 10000;
                $twoYears = $twoYears * 2000;
                $months = $months * 218;
                $year = $year * 365;
                $once = $once * 29.9;
                $whereLs['status'] = array('in',"1,2");//cid在这个数组中
                $whereL['parent_did'] = $usersModel['did'];//cid在这个数组中

                $userOne['totalRevenue'] = ($once + $months + $year+$threeYears+$twoYears) ;
                $recording = $this->withdraw_model->where(array("unionid" =>  $unionid, "status" => 2))->sum("money");//提现成功
                $underReview = $this->withdraw_model->where(array("unionid" =>  $unionid, "status" => 1))->sum("money");//正在审核
                $listU['total'] = floor((($once*0.274) + ($months*0.274) + ($year*0.274)+($threeYears*0.1)+($twoYears*0.25)+($total['talCount']*0.01)) - ($recording + $underReview)) ;
                $listU['count'] = $countTs;
            }
            else if($usersModel['user_type'] ==5){
                //余额
                $months =M('User')->where(array("parent_did" => $usersModel['did'], "is_vip" => 2))->count();//半年用户数量
                $year = M('User')->where(array("parent_did" => $usersModel['did'], "is_vip" => 3))->count();//一年用户数量
                $once = M('User')->where(array("parent_did" => $usersModel['did'], "is_vip" => 1))->count();//一年用户数量
                $twoYears = M('User')->where(array( "parent_did" => $usersModel['did'],"is_vip" => 4))->count();//一年用户数量
                $threeYears = M('User')->where(array("parent_did" => $usersModel['did'], "is_vip" => 5))->count();//一年用户数量
                $machineOwner= M('User')->where(array("parent_did" => $usersModel['did'], "is_vip" => 7))->count();//虚拟机主用户数量
                $owner = M('User')->where(array("parent_did" => $usersModel['did'], "is_vip" => 8))->count();//机主用户数量
                $countTs = M('User')->where(array("parent_did" => $usersModel['did']))->count();//数量
                $machineOwner= $machineOwner * 10000;//天使
                $owner = $owner * 100000;
                $threeYears = $threeYears * 10000;
                $twoYears = $twoYears * 2000;
                $months = $months * 218;
                $year = $year * 365;
                $once = $once * 29.9;
                $whereLs['status'] = array('in',"1,2");//cid在这个数组中
                $whereL['parent_did'] = $usersModel['did'];//cid在这个数组中
                $recording = $this->withdraw_model->where(array("unionid" =>  $unionid, "status" => 2))->sum("money");//提现成功
                $underReview = $this->withdraw_model->where(array("unionid" =>  $unionid, "status" => 1))->sum("money");//正在审核

                $listU['total']  = floor((($once*0.274) + ($months*0.274) + ($year*0.274)+($threeYears*0.1)+($twoYears*0.25)) - ($recording + $underReview)) ;
                $listU['count']  =$countTs;
            }
            else if($usersModel['user_type'] ==3){
                //余额
                $months =M('User')->where(array("owner_id" => $usersModel['did'], "is_vip" => 2))->count();//半年用户数量
                $year = M('User')->where(array("owner_id" => $usersModel['did'], "is_vip" => 3))->count();//一年用户数量
                $once = M('User')->where(array("owner_id" => $usersModel['did'], "is_vip" => 1))->count();//一年用户数量
                $twoYears = M('User')->where(array( "owner_id" => $usersModel['did'],"is_vip" => 4))->count();//一年用户数量
                $threeYears = M('User')->where(array("owner_id" => $usersModel['did'], "is_vip" => 5))->count();//一年用户数量
                $partner = M('User')->where(array("owner_id" => $usersModel['did'], "is_vip" => 5))->count();//一年用户数量
                $machineOwner= M('User')->where(array("owner_id" => $usersModel['did'], "is_vip" => 7))->count();//虚拟机主用户数量
                $owner = M('User')->where(array("owner_id" => $usersModel['did'], "is_vip" => 8))->count();//机主用户数量
                $countTs = M('User')->where(array("owner_id" => $usersModel['did']))->count();//数量
                $machineOwner= $machineOwner * 10000;//天使
                $owner = $owner * 100000;
                $threeYears = $threeYears * 10000;
                $twoYears = $twoYears * 2000;
                $months = $months * 218;
                $year = $year * 365;
                $once = $once * 29.9;
                $whereLs['status'] = array('in',"1,2");//cid在这个数组中
                $whereL['owner_id'] = $usersModel['did'];//cid在这个数组中

//        $userList['totalRevenue'] = ($once + $months + $year+$threeYears+$twoYears+$machineOwner+$owner) ;
                $userOne['totalRevenue'] = ($once + $months + $year+$threeYears+$twoYears) ;
                $userOne['partnerCount'] = $partner ;
//            $userList['total'] = ($once + $months + $year+$threeYears+$twoYears) * ($postReplyS['percentage']/100) -$sum;

//            $userList['total'] = (($once*0.274) + ($months*0.274) + ($year*0.274)+($threeYears*0.3)+($twoYears*0.5)+($machineOwner*0.1)+($owner*0.1)) * ($postReplyS['percentage']/100) ;
                $userE =M("user")->where(array("parent_did"=>$usersModel['did']))->select();
                $talCount=0;
                $countUs =0;
                if($userE){
                    $arrayC =0;
                    foreach ($userE as $key=>$value){
                        $monthss =M('User')->where(array("parent_did" => $value['parent_did'], "is_vip" => 2))->count();//半年用户数量
                        $years = M('User')->where(array("parent_did" => $value['parent_did'], "is_vip" => 3))->count();//一年用户数量
                        $onces = M('User')->where(array("parent_did" => $value['parent_did'], "is_vip" => 1))->count();//一年用户数量
                        $twoYearss = M('User')->where(array( "parent_did" => $value['parent_did'],"is_vip" => 4))->count();//一年用户数量
                        $threeYearss = M('User')->where(array("parent_did" => $value['parent_did'], "is_vip" => 5))->count();//一年用户数量
                        $machineOwners= M('User')->where(array("parent_did" => $value['parent_did'], "is_vip" => 7))->count();//虚拟机主用户数量
                        $owners = M('User')->where(array("parent_did" => $value['parent_did'], "is_vip" => 8))->count();//机主用户数量
                        $machineOwner= $machineOwner * 10000;//天使
                        $owners = $owners * 100000;
                        $threeYearss = $threeYearss * 10000;
                        $twoYearss = $twoYearss * 2000;
                        $monthss = $monthss * 218;
                        $years = $years * 365;
                        $onces = $onces * 29.9;
                        $countUs = M('User')->where(array("parent_did" =>  $value['parent_did']))->count();
                        $arrayC =  (($onces*0.274) + ($monthss*0.274) + ($years*0.274)+($threeYearss*0.1)+($twoYearss*0.25)) ;
                    }
                    $talCount =(($once * 0.274) + ($months * 0.274) + ($year * 0.274) + ($threeYears * 0.5) + ($twoYears * 0.3)+$arrayC);
                    $recording = $this->withdraw_model->where(array("unionid" =>  $unionid, "status" => 2))->sum("money");//提现成功
                    $underReview = $this->withdraw_model->where(array("unionid" =>  $unionid, "status" => 1))->sum("money");//正在审核

                    $listU['total']=floor($talCount-($talCount *($partner*0.01)) - ($recording + $underReview));
                    $listU['count'] = $countTs;
                }else {
                    $talCount =(($once * 0.274) + ($months * 0.274) + ($year * 0.274) + ($threeYears * 0.5) + ($twoYears * 0.3));
                    $recording = $this->withdraw_model->where(array("unionid" => $unionid, "status" => 2))->sum("money");//提现成功
                    $underReview = $this->withdraw_model->where(array("unionid" =>$unionid, "status" => 1))->sum("money");//正在审核

                    $listU['total']  =floor($talCount-($talCount*($partner*0.01)) - ($recording + $underReview));
                    $listU['count']  =$countTs;
                }

            }
            else if($usersModel['user_type'] ==2) {

                //余额
                $months =M('User')->where(array("operation" => $usersModel['did'], "is_vip" => 2))->count();//半年用户数量
                $year = M('User')->where(array("operation" => $usersModel['did'], "is_vip" => 3))->count();//一年用户数量
                $once = M('User')->where(array("operation" => $usersModel['did'], "is_vip" => 1))->count();//一年用户数量
                $twoYears = M('User')->where(array( "operation" => $usersModel['did'],"is_vip" => 4))->count();//一年用户数量
                $threeYears = M('User')->where(array("operation" => $usersModel['did'], "is_vip" => 5))->count();//一年用户数量
                $machineOwner= M('User')->where(array("operation" => $usersModel['did'], "is_vip" => 7))->count();//虚拟机主用户数量
                $owner = M('User')->where(array("operation" => $usersModel['did'], "is_vip" => 8))->count();//机主用户数量
                $countTs = M('User')->where(array("operation" => $usersModel['did']))->count();//数量
                $machineOwner= $machineOwner * 10000;//天使
                $owner = $owner * 100000;
                $threeYears = $threeYears * 10000;
                $twoYears = $twoYears * 2000;
                $months = $months * 218;
                $year = $year * 365;
                $once = $once * 29.9;
                $whereLs['status'] = array('in',"1,2");//cid在这个数组中
                $whereL['operation'] = $usersModel['did'];//cid在这个数组中


                $userOne['totalRevenue'] = ($once + $months + $year+$threeYears+$twoYears+$machineOwner+$owner) ;
//            $userList['total'] = ($once + $months + $year+$threeYears+$twoYears) * ($postReplyS['percentage']/100) -$sum;
//            $userList['total'] = (($once*0.178) + ($months*0.178) + ($year*0.178)+($threeYears*0.1)+($twoYears*0.15)+($machineOwner*0.4)+($owner*0.4));
                $recording = $this->withdraw_model->where(array("unionid" => $unionid, "status" => 2))->sum("money");//提现成功
                $underReview = $this->withdraw_model->where(array("unionid" => $unionid, "status" => 1))->sum("money");//正在审核

                $listU['total'] = floor((($once*0.178) + ($months*0.178) + ($year*0.178)+($threeYears*0.1)+($twoYears*0.15)+($owner*0.4))- ($recording + $underReview));
                $listU['total'] = $countTs;
            }
            return $listU;
        }
        else{
            return $listU;
        }
    }
    /**
     * @param $did
     * @return float|int
     * 刘北林
     * 2018年9月13日15:12:25
     * 合伙人收益
     */
    public function total($did){
        //余额
        $months =M('User')->where(array("owner_id" =>$did, "is_vip" => 2))->count();//半年用户数量
        $year = M('User')->where(array("owner_id" =>$did, "is_vip" => 3))->count();//一年用户数量
        $once = M('User')->where(array("owner_id" =>$did, "is_vip" => 1))->count();//一年用户数量
        $twoYears = M('User')->where(array( "owner_id" => $did,"is_vip" => 4))->count();//一年用户数量
        $threeYears = M('User')->where(array("owner_id" => $did, "is_vip" => 5))->count();//一年用户数量
        $partner = M('User')->where(array("owner_id" => $did, "is_vip" => 5))->count();//一年用户数量
        $machineOwner= M('User')->where(array("owner_id" =>$did, "is_vip" => 7))->count();//虚拟机主用户数量
        $owner = M('User')->where(array("owner_id" =>$did, "is_vip" => 8))->count();//机主用户数量

        $machineOwner= $machineOwner * 10000;//天使
        $owner = $owner * 100000;
        $threeYears = $threeYears * 10000;
        $twoYears = $twoYears * 2000;
        $months = $months * 218;
        $year = $year * 365;
        $once = $once * 29.9;

        $userE =M("user")->where(array("parent_did"=>$did))->select();
        $talCount=0;
        $listU =0;
        if($userE){
            $arrayC =0;
            foreach ($userE as $key=>$value){
                $monthss =M('User')->where(array("parent_did" => $value['parent_did'], "is_vip" => 2))->count();//半年用户数量
                $years = M('User')->where(array("parent_did" => $value['parent_did'], "is_vip" => 3))->count();//一年用户数量
                $onces = M('User')->where(array("parent_did" => $value['parent_did'], "is_vip" => 1))->count();//一年用户数量
                $twoYearss = M('User')->where(array("parent_did" => $value['parent_did'],"is_vip" => 4))->count();//一年用户数量
                $threeYearss = M('User')->where(array("parent_did" => $value['parent_did'], "is_vip" => 5))->count();//一年用户数量
                $machineOwners= M('User')->where(array("parent_did" => $value['parent_did'], "is_vip" => 7))->count();//虚拟机主用户数量
                $owners = M('User')->where(array("parent_did" => $value['parent_did'], "is_vip" => 8))->count();//机主用户数量
                $machineOwner= $machineOwner * 10000;//天使
                $owners = $owners * 100000;
                $threeYearss = $threeYearss * 10000;
                $twoYearss = $twoYearss * 2000;
                $monthss = $monthss * 218;
                $years = $years * 365;
                $onces = $onces * 29.9;
                $arrayC =  (($onces*0.274) + ($monthss*0.274) + ($years*0.274)+($threeYearss*0.1)+($twoYearss*0.25)) ;
                $listU = M('User')->where(array("parent_did" => $value['parent_did']))->count();
            }

            $talCount =(($once * 0.274) + ($months * 0.274) + ($year * 0.274) + ($threeYears * 0.5) + ($twoYears * 0.3)+$arrayC);
            return array("talCount"=>$talCount,"count"=>$listU);

        }else {
            $talCount =(($once * 0.274) + ($months * 0.274) + ($year * 0.274) + ($threeYears * 0.5) + ($twoYears * 0.3));
            $listU = M('User')->where(array("parent_did" =>$did))->count();
            return array("talCount"=>$talCount,"count"=>$listU);
        }

    }
    /**
     * 2018年5月29日17:26:48
     * 刘北林
     * 获取用户信息
     */
    public function userInfo()
    {
        $unionid = isset($_REQUEST['unionid']) ? $_REQUEST['unionid'] : "";

        if ($unionid) {
            $userList['userList'] = $this->users_model->where(array("unionid" => $unionid))->field("idCard,name,sex,national,birthday,weight,profession,phone,email,avatar,registeraddress,realaddress,local,physicalID,time,update_time,is_vip,password,time_maturity,gifts_integral")->find();
            $userList['userList']['name'] = $userList['userList']['name'] ? $userList['userList']['name'] : "";
            //余额
//            $userModel = $this->users_model->where(array("unionid" => $unionid))->find();
//            $months = $this->users_model->where(array("parent_id" => $userModel['unionid'], "is_vip" => 2))->count();//半年用户数量
//            $year = $this->users_model->where(array("parent_id" => $userModel['unionid'], "is_vip" => 3))->count();//一年用户数量
//            $once = $this->users_model->where(array("parent_id" => $userModel['unionid'], "is_vip" => 1))->count();//一年用户数量
//            $twoYears = $this->users_model->where(array("parent_id" => $userModel['unionid'], "is_vip" => 4))->count();//两年用户数量
//            $threeYears = $this->users_model->where(array("parent_id" => $userModel['unionid'], "is_vip" => 5))->count();//三年用户数量
//            $threeYears = $threeYears * 999.9;
//            $twoYears = $twoYears * 699.9;
//            $months = $months * 218;
//            $year = $year * 365;
//            $once = $once * 29.9;
//            $recording = $this->withdraw_model->where(array("unionid" => $unionid, "status" => 2))->sum("money");//提现成功
//            $underReview = $this->withdraw_model->where(array("unionid" => $unionid, "status" => 1))->sum("money");//正在审核
//            $userList['userList']['money'] = ($once + $months + $year + $threeYears + $twoYears) * 0.25 - ($recording + $underReview);
            $tO = $this->totals($unionid);
            $userList['userList']['money'] = $tO['total'];
            $userList['userList']['countRecommend'] =$tO['count'];
            //积分
            $sign = $this->sign_model->where(array("unionid" => $unionid))->find();
            $userList['userList']['integral'] = $sign['integral'] ? $sign['integral'] : 0;

            //会员天数
            if ($userList['userList']['is_vip'] != 0) {
                $userList['userList']['superlus'] = intval((strtotime($userList['userList']['time_maturity']) - strtotime(date("Y-m-d H:i:s", time()))) / 86400) + 1;
            } else {
                $userList['userList']['superlus'] = 0;
            }
            //是否设置密码
            if ($userList['userList']['password']) {
                $userList['userList']['is_set_password'] = 1;
            } else {
                $userList['userList']['is_set_password'] = 0;
            }


            //是否设置支付宝
            $paymentList = $this->payment_add_model->where(array("unionid" => $unionid, "type" => 1, "is_del" => 0))->find();
            if ($paymentList) {
                $userList['userList']['is_set_alipay'] = 1;
            } else {
                $userList['userList']['is_set_alipay'] = 0;
            }
            //是否设置银行卡
            $paymentLists = $this->payment_add_model->where(array("unionid" => $unionid, "type" => 2, "is_del" => 0))->find();
            if ($paymentLists) {
                $userList['userList']['is_set_bank'] = 1;
            } else {
                $userList['userList']['is_set_bank'] = 0;
            }

            if ($userList['userList']) {
                $this->apiReturn($userList);
            } else {
                $this->apiError("请前往公众号支付");
            }

        } else {
            $this->apiError("请前往公众号支付");
        }
    }


    /**
     * 检验数据的真实性，并且获取解密后的明文.
     * @param $encryptedData string 加密的用户数据
     * @param $iv string 与用户数据一同返回的初始向量
     * @param $data string 解密后的原文
     *刘北林
     * 2018年6月5日12:57:55
     * @return int 成功0，失败返回对应的错误码
     */
    public function decryptData($encryptedData, $iv, &$user_token, $session_key)
    {
        if (strlen($session_key) != 24) {
            return $this->IllegalAesKey;
        }
        $aesKey = base64_decode($session_key);

        if (strlen($iv) != 24) {
            return $this->IllegalIv;
        }
        $aesIV = base64_decode($iv);

        $aesCipher = base64_decode($encryptedData);

        $result = openssl_decrypt($aesCipher, "AES-128-CBC", $aesKey, 1, $aesIV);

        $dataObj = json_decode($result);
        if ($dataObj == NULL) {
            return $this->IllegalBuffer;
        }
        if ($dataObj->watermark->appid != $this->appid) {
            return $this->IllegalBuffer;
        }
        $user_token = $result;
        return $this->OK;
    }

    /**
     * 2018年6月5日14:10:06
     * 刘北林
     * 分享授权
     */
    public function sharingMedicalExamination()
    {
        $encryptedData = $_REQUEST['encryptedData'] ? $_REQUEST['encryptedData'] : "";
        $iv = $_REQUEST['iv'] ? $_REQUEST['iv'] : "";
        //模糊匹配
        $time = $_REQUEST['time'] ? date('Y-m-d', strtotime($_REQUEST['time'])) : "";
        $encryptionId = $_REQUEST['encryptionId'] ? $_REQUEST['encryptionId'] : "";
        $parent = base64_decode($encryptionId);
        if (!$encryptedData || !$iv || !$parent) {
            $this->apiError('非法错误');
        }

        $errCode = $this->decryptData($encryptedData, $iv, $user_token, S("session_key"));
        $user_token = json_decode($user_token, true);
        $userLists = $this->users_model->where(array("unionid" => $user_token['unionId']))->find();
        //用户存在则绑定用户 不存在添加用户
        if ($userLists) {
            if ($user_token['unionId']) {
                $data['unionid'] = $this->remove_xss($user_token['unionId']);
                S("unionid", $this->remove_xss($user_token['unionId']), 60 * 60 * 24 * 3000);
                S("xopenid", $this->remove_xss($user_token['openId']), 60 * 60 * 24 * 3000);
                $data['xopenid'] = $this->remove_xss($user_token['openId']);
                $data['name'] = $this->remove_xss($user_token['nickName']);
//                $data['parent_id'] = $this->remove_xss($parent);
                $data['avatar'] = $this->remove_xss($user_token['avatarUrl']);
                $data['ip'] = $this->remove_xss($_SERVER["REMOTE_ADDR"]);
                $data['update_time'] = date("Y-m-d H:i:s", time());
                $this->users_model->where(array("id" => $userLists['id']))->save($data);
            }
        } else {
            if ($user_token['unionId']) {

                $data['unionid'] = $this->remove_xss($user_token['unionId']);
                S("unionid", $this->remove_xss($user_token['unionId']), 60 * 60 * 24 * 3000);
                S("xopenid", $this->remove_xss($user_token['openId']), 60 * 60 * 24 * 3000);
                $data['xopenid'] = $this->remove_xss($user_token['openId']);
                $data['name'] = $this->remove_xss($user_token['nickName']);
                $data['avatar'] = $this->remove_xss($user_token['avatarUrl']);
                $data['parent_id'] = $this->remove_xss($parent);
                $data['ip'] = $this->remove_xss($_SERVER["REMOTE_ADDR"]);
                $data['session_key'] = $this->remove_xss($user_token['session_key']);
                $data['time'] = date("Y-m-d H:i:s", time());
                $this->users_model->add($data);
            }
        }
        if ($errCode == 0) {
            $this->apiSuccess("Success");
        } else {
            $this->apiError($errCode);
        }
    }

    /**
     * 2018年6月4日11:44:52
     * 刘北林
     * 分享 接口 post $time $unionid
     */
    public function shareIt()
    {
        $count = 100;
        //模糊匹配
        $time = $_REQUEST['time'] ? date('Y-m-d', strtotime($_REQUEST['time'])) : "";
        $encryptionId = $_REQUEST['encryptionId'] ? $_REQUEST['encryptionId'] : "";
        $parent = base64_decode($encryptionId);
//        $unionid = $_REQUEST['unionid'] ? $_REQUEST['unionid'] : "oaKS01NIIAui_M1DhbSMsVsIwQVA";

//        //存入父级id
//        $datas['parent_id'] = $parent;
//        $this->users_model->where(array("unionid" => $unionid))->save($datas);
        if (!$encryptionId) {
            $this->apiError('非法错误');
        }

        $userModel = $this->users_model->where(array("unionid" => $parent))->find();
        if (!empty($time)) {
            $where['time'] = array('like', '%' . $time . '%');
            $wheres['startTime'] = array('like', '%' . $time . '%');
        }
        if ($userModel) {
            $where['idcard'] = $userModel['idcard'];
            $wheres['idcard'] = $userModel['idcard'];
        }

        //血氧
        $bloodOxygen = $this->blood_oxygen_model->where($where)->order('time DESC')->find();
        //糖化血红蛋白
        $glycosylatedHemoglobin = $this->glycosylated_hemoglobin_model->where($where)->order('time DESC')->find();
        //血红蛋白
        $hemoglobin = $this->hemoglobin_model->where($where)->order('time DESC')->find();
        //体重数据接口
        $weight = $this->weight_model->where($where)->order('time DESC')->find();

        //血压数据
        $physicalExaminationDetails = $this->blood_pressure_model->where($where)->order('time DESC')->find();

        //人提成分数据 mei
        $humanExtracts = $this->human_extracts_model->where($where)->order('time DESC')->find();
        //体温 度数
        $temperature = $this->temperature_model->where($where)->order('time DESC')->find();
        //心电 mei
        $electrocardiogram = $this->electrocardiogram_model->where($wheres)->order('startTime DESC')->find();
        //睡眠
        $sleep = $this->sleep_model->where($wheres)->order('startTime DESC')->find();
        $medical = array();
        //收缩压
        $systolic = isset($physicalExaminationDetails['systolic']) ? $physicalExaminationDetails['systolic'] : "";
        if ($systolic < 90 || $systolic > 139) {
            $count = $count - 1;
            $medical['systolic'] = 99;
        } else {
            $medical['systolic'] = 100;
        }
        //舒张压
        $diastolic = isset($physicalExaminationDetails['diastolic']) ? $physicalExaminationDetails['diastolic'] : "";
        if ($diastolic < 60 || $diastolic > 89) {
            $count = $count - 1;
            $medical['diastolic'] = 99;
        } else {
            $medical['diastolic'] = 100;
        }
        //脉搏
        $pulse = isset($physicalExaminationDetails['pulse']) ? $physicalExaminationDetails['pulse'] : "";
        if ($pulse < 60 || $pulse > 100) {
            $count = $count - 1;
            $medical['pulse'] = 99;
        } else {
            $medical['pulse'] = 100;
        }
        //水分
        $moisture = isset($humanExtracts['moisture']) ? $humanExtracts['moisture'] : "";
        if ($moisture < 50.1 || $moisture > 70) {
            $count = $count - 1;
            $medical['moisture'] = 99;
        } else {
            $medical['moisture'] = 100;
        }
        //脂肪率
        $adiposerate = isset($humanExtracts['adiposerate']) ? $humanExtracts['adiposerate'] : "";
        if ($adiposerate < 10 || $adiposerate > 19.9) {
            $count = $count - 1;
            $medical['adiposerate'] = 99;
        } else {
            $medical['adiposerate'] = 100;
        }

        //骨量
        $bone = isset($humanExtracts['bone']) ? $humanExtracts['bone'] : "";
        if ($bone < 2.3 || $bone > 2.7) {
            $count = $count - 1;
            $medical['bone'] = 99;

        } else {
            $medical['bone'] = 100;
        }
        //基础代谢
        $basalmetabolism = isset($humanExtracts['basalmetabolism']) ? $humanExtracts['basalmetabolism'] : "";
        if ($basalmetabolism < 1100 || $basalmetabolism > 1850) {
            $count = $count - 1;
            $medical['basalmetabolism'] = 99;
        } else {
            $medical['basalmetabolism'] = 100;
        }
        //健康指数
        $metabolism = isset($humanExtracts['bmi']) ? $humanExtracts['bmi'] : "";
        if ($metabolism < 18.5 || $metabolism > 23.9) {
            $count = $count - 1;
            $medical['bmi'] = 99;
        } else {
            $medical['bmi'] = 100;
        }
        //肌肉量
        $muscle = isset($humanExtracts['muscle']) ? $humanExtracts['muscle'] : "";
        if ($muscle < 25.0 || $muscle > 32.1) {
            $count = $count - 1;
            $medical['muscle'] = 99;
        } else {
            $medical['muscle'] = 100;
        }
        //内脏脂肪
        $visceralfat = isset($humanExtracts['visceralfat']) ? $humanExtracts['visceralfat'] : "";
        if ($visceralfat < 1 || $visceralfat > 9) {
            $count = $count - 1;
            $medical['visceralfat'] = 99;
        } else {
            $medical['visceralfat'] = 100;
        }

        //体温$temperature['temperature']
        $temperatures = isset($temperature['temperature']) ? $temperature['temperature'] : "";
        if ($temperatures < 36 || $temperatures > 37) {
            $count = $count - 1;
            $medical['temperature'] = 99;
        } else {
            $medical['temperature'] = 100;
        }
        //血氧饱和度
        $spo2 = isset($sleep['spo2']) ? $sleep['spo2'] : "";
        if ($spo2 < 94 || $spo2 > 100) {
            $count = $count - 1;
            $medical['spo2'] = 99;
        } else {
            $medical['spo2'] = 100;
        }
        $data['data'] = $medical;
        $data['userInfo'] = $this->users_model->where(array("unionid" => $parent))->find();
        $data['fraction'] = $count;


        $this->apiReturn($data);

    }


    /**
     * name 刘北林
     * data 2018年5月18日15:12:17
     * @param $appid
     * @param $appsecret
     * @return mixed
     * 获取 access_token
     */
    public function obtainToken($appid, $secret, $code)
    {
//        $c = new \Curl();
//        $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=" . $appid . "&secret=" . $appsecret;
//        $user = $c->get($url);
//        $user_token = json_decode($user, true);
//        return $user_token['access_token'];
        $c = new \Curl();

        $url = "https://api.weixin.qq.com/sns/jscode2session?appid=" . $appid . "&secret=" . $secret . "&js_code=" . $code . "&grant_type=authorization_code";
        $user = $c->get($url);
        $user_token = json_decode($user, true);

        $userLists = $this->users_model->where(array("unionid" => $user_token['unionid']))->find();
        //用户存在则绑定用户 不存在添加用户
        if ($userLists) {
            $data['unionid'] = $user_token['unionid'];
            S("unionid", $user_token['unionid'], 60 * 60 * 24 * 3000);
            S("xopenid", $user_token['openid'], 60 * 60 * 24 * 3000);
            S("session_key", $user_token['session_key'], 60 * 60 * 24 * 3000);
            $data['xopenid'] = $user_token['openid'];
            $data['session_key'] = $user_token['session_key'];
            $data['ip'] = $_SERVER["REMOTE_ADDR"];
            $data['update_time'] = date("Y-m-d H:i:s", time());
            $this->users_model->where(array("id" => $userLists['id']))->save($data);
        } else {
            if ($user_token['unionid']) {
                $data['unionid'] = $user_token['unionid'];
                S("unionid", $user_token['unionid'], 60 * 60 * 24 * 3000);
                S("xopenid", $user_token['openid'], 60 * 60 * 24 * 3000);
                S("session_key", $user_token['session_key'], 60 * 60 * 24 * 3000);
                $data['xopenid'] = $user_token['openid'];
                $data['ip'] = $_SERVER["REMOTE_ADDR"];
                $data['session_key'] = $user_token['session_key'];
                $data['time'] = date("Y-m-d H:i:s", time());
                $this->users_model->add($data);
            }
        }
        return array("xopenid" => $user_token['openid'] ? $user_token['openid'] : "", "unionid" => $user_token['unionid'] ? $user_token['unionid'] : "", "encryptionId" => $user_token['unionid'] ? base64_encode($user_token['unionid']) : "", "session_key" => $user_token['session_key'] ? $user_token['session_key'] : "");

    }


    /**
     * name 刘北林
     * data 2018年5月19日12:13:30
     * 微信授权
     */
    public function authorized()
    {
        $userList = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=" . $this->appid . "&redirect_uri=" . urlencode("http://bolon.kuaifengpay.com/Api/Index/callback") . "&response_type=code&scope=snsapi_userinfo&state=STATE#wechat_redirect";
//        $params = array('touser' => 'oRSA91XTOTFfPcgsBEbPNdNqByLw', 'template_id' => '-eLD4SlLsLwdaS0UDJQpmGJYjM7ph926nPvr7juSmHU', 'url' => 'https://admin.kuaifengpay.com/mp/index.php?id=52/', 'topcolor' => '#173177');
//        $params['data'] = array();
//        $params['data']['first'] = array('value' => '您通过提交的体温报告', 'color' => '#173177');
//        $params['data']['keyword1'] = array('value' => "38度 \n建议：饮食上，早餐食用红枣薏米山药粥，用红枣、薏米、山药煮成粥，红枣可以补血；山药有健脾的作用；薏米有助于散除湿气。用生姜泡红茶，生姜中的辛辣成分能燃烧体内的", 'color' => '#173177');
//        $params['data']['keyword2'] = array('value' => date("Y-m-d H:i:s"), 'color' => '#173177');
//        $params['data']['remark'] = array('value' => '您的体温正常', 'color' => '#173177');
//        $post = json_encode($params);
        header("Location:" . $userList);
    }

    /**
     * name 刘北林
     * data 2018年5月19日12:13:30
     * 回调链接地址
     */
    public function callback()
    {
        $c = new \Curl();
        $userUrl = "https://api.weixin.qq.com/sns/oauth2/access_token?appid=" . $this->appid . "&secret=" . $this->appsecret . "&code=" . $_GET['code'] . "&grant_type=authorization_code";
        $userList = $this->users_model->where(array("idCard" => "320924199403075716"))->find();

        $authorizedList = json_decode($c->get($userUrl), true);
        S("access_token", $authorizedList['access_token'], 60 * 60 * 2);
        S("refresh_token", $authorizedList['refresh_token'], 60 * 60 * 24 * 30);
        S("openid", $authorizedList['openid'], 60 * 60 * 24 * 30);
        //用户存在则绑定用户 不存在添加用户
        if ($userList) {
            $userLists = "https://api.weixin.qq.com/sns/userinfo?access_token=" . $authorizedList['access_token'] . "&openid=" . $authorizedList['openid'] . "&lang=zh_CN";
            $userLists = json_decode($c->get($userLists), true);
            $data['unionid'] = $userLists['unionid'];
            $data['access_token'] = $authorizedList['access_token'];
            $data['openid'] = $authorizedList['openid'];
            $data['update_time'] = date("Y-m-d H:i:s");
            $this->users_model->where(array("id" => $userList['id']))->save($data);
        } else {
            $userLists = "https://api.weixin.qq.com/sns/userinfo?access_token=" . $authorizedList['access_token'] . "&openid=" . $authorizedList['openid'] . "&lang=zh_CN";
            $userLists = json_decode($c->get($userLists), true);
            $data['unionid'] = $userLists['unionid'];
            $data['access_token'] = $authorizedList['access_token'];
            $data['openid'] = $authorizedList['openid'];
            $data['time'] = date("Y-m-d H:i:s");
            $this->users_model->add($data);
        }

//        $times = time();//当前时间
        $tokenModel = $this->token_model->where(array("openid" => $authorizedList['openid']))->find();
        if ($tokenModel) {
            $data['time'] = time() + 6000;
            $data['access_token'] = $authorizedList['access_token'];
            $data['refresh_token'] = $authorizedList['refresh_token'];
            $data['openid'] = $authorizedList['openid'];
            $this->token_model->where(array("id" => $tokenModel['id']))->save($data);
        } else {
            $data['time'] = time() + 6000;
            $data['access_token'] = $authorizedList['access_token'];
            $data['refresh_token'] = $authorizedList['refresh_token'];
            $data['openid'] = $authorizedList['openid'];
            $this->token_model->add($data);
        }


        if ($authorizedList) {
            $this->apiReturn(array('info' => $authorizedList['openid']), '授权成功');
        } else {
            $this->apiError('授权失败');
        }
    }

    /**
     * 2018年5月30日20:40:57
     * 刘北林
     * 签到
     */
    public function sign()
    {
//        $unionid = urlencode(base64_encode("oaKS01NIIAui_M1DhbSMsVsIwQVA"));
//        $unionid   =base64_decode(urldecode($unionid));

        $unionid = $_REQUEST['unionid'] ? $_REQUEST['unionid'] : "";//用户id
        if (!$unionid) {
            $this->apiError('非法错误');
        }
        $begintime = date("Y-m-d H:i:s", mktime(0, 0, 0, date('m'), date('d'), date('Y')));
        $endtime = date("Y-m-d H:i:s", mktime(0, 0, 0, date('m'), date('d') + 1, date('Y')) - 1);
        $where = array(
            'unionid' => $unionid,
            'time' => array(array('gt', $begintime), array('lt', $endtime)),
        );
        $wheres = array(
            'unionid' => $unionid,
        );
        $res = $this->sign_model->where($where)->find();
        if (!$res) {
            $data = $this->sign_model->where($wheres)->find();
            if ($data) {
                //转换成时间戳；
                $timestrap = strtotime(date('Y-m-d', time()));

                $times = strtotime(date("Y-m-d", strtotime($data['time'])));

                if (($timestrap - $times) / 3600 > 24) {
                    $data['continuous'] = 0;
                    $data["is_sign"] = 0;//未签到
                    $this->apiReturn($data);
                } else {
                    $data["is_sign"] = 0;//未签到
                    $this->apiReturn($data);
                }
            } else {
                $data["is_sign"] = 0;//未签到
                $this->apiReturn($data);
            }
        } else {
            $data = $this->sign_model->where($wheres)->find();
            if ($data) {
                $data["is_sign"] = 1;//已签到
                $this->apiReturn($data);
            } else {
                $data["is_sign"] = 1;//已签到
                $this->apiReturn($data);
            }
        }
    }

    /**
     * 2018年5月30日20:40:57
     * 刘北林
     * 签到
     */
    public function signIn()
    {
        $unionid = isset($_REQUEST['unionid']) ? $_REQUEST['unionid'] : "";//用户id
        $continuous = isset($_REQUEST['continuous']) ? $_REQUEST['continuous'] : 0;//第几天签到
        $integral = isset($_REQUEST['integral']) ? $_REQUEST['integral'] : "";//此次签到获取积分
        $time = date("Y-m-d H:i:s", time());//此次签到获取积分
        if (!$unionid || !$integral) {
            $this->apiError('非法错误');
        }
        $where = array(
            'unionid' => $unionid,
        );

        $sginList = $this->sign_model->where($where)->find();
        if ($sginList) {
            $data = array(
                'unionid' => $unionid,
                'continuous' => $continuous == 0 ? 1 : $sginList['continuous'] + $continuous,
                'integral' => $sginList['integral'] + $integral,
                'time' => $time
            );
            $this->sign_model->where($where)->save($data);
        } else {
            $data = array(
                'unionid' => $unionid,
                'continuous' => $continuous,
                'integral' => $integral,
                'time' => $time
            );
            $this->sign_model->add($data);
        }
        $this->apiSuccess("签到成功");

    }




    /**
     * 2018年6月4日14:56:44
     * 刘北林
     * 钱包接口
     */
    public function wallet()
    {
        $unionid = isset($_REQUEST['unionid']) ? $_REQUEST['unionid'] : "";//用户id
        if (!$unionid) {
            $this->apiError('非法错误');
        }
//        $userModel = $this->users_model->where(array("unionid" => $unionid))->find();
//        $months = $this->users_model->where(array("parent_id" => $userModel['unionid'], "is_vip" => 2))->count();//半年用户数量
//        $year = $this->users_model->where(array("parent_id" => $userModel['unionid'], "is_vip" => 3))->count();//一年用户数量
//        $once = $this->users_model->where(array("parent_id" => $userModel['unionid'], "is_vip" => 1))->count();//一年用户数量
//        $twoYears = $this->users_model->where(array("parent_id" => $userModel['unionid'], "is_vip" => 4))->count();//两年用户数量
//        $threeYears = $this->users_model->where(array("parent_id" => $userModel['unionid'], "is_vip" => 5))->count();//三年用户数量
//        $threeYears = $threeYears * 999.9;
//        $twoYears = $twoYears * 699.9;
////        $months = (floor($months / 3)) * 218;
////        $year = (floor($year / 3)) * 365;
//        $months = $months * 218;
//        $year = $year * 365;
//        $once = $once * 29.9;
//        $recording = $this->withdraw_model->where(array("unionid" => $unionid, "status" => 2))->sum("money");//提现成功
//        $underReview = $this->withdraw_model->where(array("unionid" => $unionid, "status" => 1))->sum("money");//正在审核
        $tO =$this->totals($unionid);
        $data['money']= $tO['total'];
        $data['count']= $tO['count'];
//        $data['money'] = ($once + $months + $year + $threeYears + $twoYears) * 0.25 - ($recording + $underReview);
//        $data['count'] = $this->users_model->where(array("parent_id" => $userModel['unionid']))->count();
        $this->apiReturn($data);
    }

    /**
     * 2018年6月4日14:56:44
     * 刘北林
     * 提现记录
     */
    public function recording()
    {
        $unionid = isset($_REQUEST['unionid']) ? $_REQUEST['unionid'] : "";//用户id
        if (!$unionid) {
            $this->apiError('非法错误');
        }
        $data['data'] = $this->withdraw_model->where(array("unionid" => $unionid))->select();
        $this->apiReturn($data);
    }

    /**
     * 2018年6月4日14:56:44
     * 刘北林
     * 我的团队
     */
    public function teams()
    {
        $unionid = isset($_REQUEST['unionid']) ? $_REQUEST['unionid'] : "";//用户id
        if (!$unionid) {
            $this->apiError('非法错误');
        }
        $userModel = $this->users_model->where(array("unionid" => $unionid))->find();
        $months = $this->users_model->where(array("parent_id" => $userModel['unionid'], "is_vip" => 2))->count();//半年用户数量
        $year = $this->users_model->where(array("parent_id" => $userModel['unionid'], "is_vip" => 3))->count();//一年用户数量
        $once = $this->users_model->where(array("parent_id" => $userModel['unionid'], "is_vip" => 1))->count();//一年用户数量
        $twoYears = $this->users_model->where(array("parent_id" => $userModel['unionid'], "is_vip" => 4))->count();//两年用户数量
        $threeYears = $this->users_model->where(array("parent_id" => $userModel['unionid'], "is_vip" => 5))->count();//三年用户数量
        $threeYears = $threeYears * 999.9;
        $twoYears = $twoYears * 699.9;
//        $months = (floor($months / 3)) * 218;
//        $year = (floor($year / 3)) * 365;
        $months = $months * 218;
        $year = $year * 365;
        $once = $once * 29.9;
        $data['money'] = ($months + $year + $once + $threeYears + $twoYears) * 0.25;//我的收益
        $data['data'] = $this->users_model->where(array("parent_id" => $unionid))->field("avatar,name,is_vip")->select();//我的团队
        $this->apiReturn($data);
    }

    /**
     * 我的团队 小程序
     * 刘北林
     * 2018年9月13日15:13:48
     */
    public function myTeam(){
        $unionid = isset($_REQUEST['unionid']) ? $_REQUEST['unionid'] : "";//用户id
        if (!$unionid) {
            $this->apiError('非法错误');
        }
        $userModel = $this->users_model->where(array("unionid" => $unionid))->find();
        $usersModel = M("users")->where(array("did" => $userModel['sn']))->find();
        if($usersModel['user_type'] ==4){
            $where =array("parent_did"=>$usersModel['did'] ,"status"=>1);
        }
        else if($usersModel['user_type'] ==5){
            $where =array("parent_did"=>$usersModel['did'] ,"status"=>1);
        }
        else if($usersModel['user_type'] ==3){
            $where =array("owner_id"=>$usersModel['did'] ,"status"=>1);
        }
        else if($usersModel['user_type']==2) {
            $where =array("operation"=>$usersModel['did'] ,"status"=>1);
        }


        $oauth_user_model = M('User');
        $userList =array();
        $lists = $oauth_user_model
            ->where($where)
            ->field("avatar,name,is_vip")
            ->select();
        $userList['data'] =$lists;
        $this->apiReturn($userList);
    }

    /**
     * 2018年6月4日17:22:47
     * 刘北林
     * 地址
     */
    public function addressList()
    {
        $data['data'] = $this->address_model->field("latitude,longitude,name,address")->select();
        $this->apiReturn($data);
    }

    /**
     * 2018年6月21日16:17:10
     * 刘北林
     * 测试
     */
    public function imgs()
    {
        $mid = "10005";
        $token = "ee4997a9a87083106343e102c8043d12";
        list($t1, $t2) = explode(' ', microtime());
        $time = (float)sprintf('%.0f', (floatval($t1) + floatval($t2)) * 1000);
        $data = $mid . $token . $time;
        $md5 = md5($data);
        $img = file_get_contents("https://bolon.kuaifengpay.com/public/images/222.png");
        $img = base64_encode($img);

        $arr = array(
            'mid' => $mid,
            'sign' => $md5,
            'base64' => $img,
            'timestamp' => $time,
            'ext' => "jpg"
        );

        //json也可以
        $data_string = json_encode($arr);
        //curl验证成功
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, "https://open3z.com/hj_zhida_api/api/ton.do");
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data_string);
        curl_setopt($curl, CURLOPT_HEADER, 0);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json; charset=utf-8',
                'Content-Length: ' . strlen($data_string)
            )
        );
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $res = curl_exec($curl);
        curl_close($curl);
        echo $res;

//        header('content-type:application/json;charset=utf8');
//        $mid = "10005";
//        $token = "a226fce9be933aa0fec34ece955a6e43";
//        $time = explode(" ", microtime());
//        $time = $time [1] . ($time [0] * 1000);
//        $time2 = explode(".", $time);
//        $time = $time2 [0];
//
//        $data = $mid . $token . $time;
//        $md5 = md5($data);
//        $c = new \Curl();
//        $userList = "https://open3z.com/hj_zhida_api/api/emo.do";
//
//        $zheng = file_get_contents("https://bolon.kuaifengpay.com/data/upload/20180619/5b28aebf549af.jpg");
//        $zheng = base64_encode($zheng);
//
//        $post = '{"mid":10005,"sign":"' . $md5 . '", "base64":"' . $zheng . '","timestamp":"' . $time . '","ext":"jpg"}';
//
//        print_r($c->post($userList, $post));
    }

    /**
     * 2018年6月7日13:10:56
     * 刘北林
     * 添加银行卡
     */
    public function cardAdd()
    {
        $unionid = isset($_REQUEST['unionid']) ? $_REQUEST['unionid'] : "";//用户id
        $type = 2;// 类型 1：支付宝 2：银行卡
        $name = isset($_REQUEST['name']) ? $_REQUEST['name'] : "";//姓名
        $phone = isset($_REQUEST['phone']) ? $_REQUEST['phone'] : "";//手机号
        $card = isset($_REQUEST['card']) ? $_REQUEST['card'] : "";//银行卡号
        $account = isset($_REQUEST['account']) ? $_REQUEST['account'] : "";//开户行
        $branch = isset($_REQUEST['branch']) ? $_REQUEST['branch'] : "";//所属支行
        if (!$unionid || !$type || !$name || !$phone || !$card || !$account || !$branch) {
            $this->apiError('非法错误');
        }
        $data = array(
            'unionid' => $unionid,
            'type' => $type,
            'name' => $name,
            'phone' => $phone,
            'card' => $card,
            'account' => $account,
            'branch' => $branch,
            "is_del" => 0,
            'time' => date('Y-m-d H:i:s', time())
        );
        $this->payment_add_model->add($data);
        $this->apiSuccess('添加成功');
    }

    /**
     * 2018年6月7日13:10:56
     * 刘北林
     * 添加支付宝
     */
    public function alipayAdd()
    {
        $unionid = isset($_REQUEST['unionid']) ? $this->remove_xss($_REQUEST['unionid']) : "";//用户id
        $type = 1;// 类型 1：支付宝 2：银行卡
        $name = isset($_REQUEST['name']) ? $this->remove_xss($_REQUEST['name']) : "";//姓名
        $phone = isset($_REQUEST['phone']) ? $this->remove_xss($_REQUEST['phone']) : "";//手机号
        $card = isset($_REQUEST['card']) ? $this->remove_xss($_REQUEST['card']) : "";//银行卡号
//        $account = isset($_REQUEST['account']) ? $_REQUEST['account'] : "";//开户行
//        $branch = isset($_REQUEST['branch']) ? $_REQUEST['branch'] : "";//所属支行
        if (!$unionid || !$type || !$name || !$phone || !$card) {
            $this->apiError('非法错误');
        }
        $data = array(
            'unionid' => $unionid,
            'type' => $type,
            'name' => $name,
            'phone' => $phone,
            'card' => $card,
            "is_del" => 0,
            'time' => date('Y-m-d H:i:s', time())
        );
        $this->payment_add_model->add($data);
        $this->apiSuccess('添加成功');
    }

    /**
     * 2018年6月7日13:10:56
     * 刘北林
     * 修改支付宝
     */
    public function alipaySave()
    {
        $unionid = isset($_REQUEST['unionid']) ? $_REQUEST['unionid'] : "";//用户id
        $type = 1;// 类型 1：支付宝 2：银行卡
        $name = isset($_REQUEST['name']) ? $_REQUEST['name'] : "";//姓名
        $phone = isset($_REQUEST['phone']) ? $_REQUEST['phone'] : "";//手机号
        $card = isset($_REQUEST['card']) ? $_REQUEST['card'] : "";//银行卡号
//        $account = isset($_REQUEST['account']) ? $_REQUEST['account'] : "";//开户行
//        $branch = isset($_REQUEST['branch']) ? $_REQUEST['branch'] : "";//所属支行
        if (!$unionid || !$type || !$name || !$phone || !$card) {
            $this->apiError('非法错误');
        }
        $data = array(
            'name' => $name,
            'phone' => $phone,
            'card' => $card,
            'update_time' => date('Y-m-d H:i:s', time())
        );
        $this->payment_add_model->where(array("unionid" => $unionid, "type" => $type, "is_del" => 0))->save($data);
        $this->apiSuccess('添加成功');
    }

    /**
     * 2018年6月7日13:10:56
     * 刘北林
     * 修改银行卡
     */
    public function cardSave()
    {
        $unionid = isset($_REQUEST['unionid']) ? $_REQUEST['unionid'] : "";//用户id
        $type = 2;// 类型 1：支付宝 2：银行卡
        $name = isset($_REQUEST['name']) ? $_REQUEST['name'] : "";//姓名
        $phone = isset($_REQUEST['phone']) ? $_REQUEST['phone'] : "";//手机号
        $card = isset($_REQUEST['card']) ? $_REQUEST['card'] : "";//银行卡号
        $account = isset($_REQUEST['account']) ? $_REQUEST['account'] : "";//开户行
        $branch = isset($_REQUEST['branch']) ? $_REQUEST['branch'] : "";//所属支行
        if (!$unionid || !$type || !$name || !$phone || !$card || !$account || !$branch) {
            $this->apiError('非法错误');
        }
        $data = array(
            'name' => $name,
            'phone' => $phone,
            'card' => $card,
            'account' => $account,
            'branch' => $branch,
            'update_time' => date('Y-m-d H:i:s', time())
        );
        $this->payment_add_model->where(array("unionid" => $unionid, "type" => $type, "is_del" => 0))->save($data);
        $this->apiReturn($data);
    }

    /**
     *查询身份证信息
     * 2018年6月7日13:37:18
     * 刘北林
     */
    public function cardSelect()
    {
        $unionid = isset($_REQUEST['unionid']) ? $_REQUEST['unionid'] : "";//用户id
        $type = 2;// 类型 1：支付宝 2：银行卡
        if (!$unionid) {
            $this->apiError('非法错误');
        }
        $paymentList = $this->payment_add_model->where(array("unionid" => $unionid, "type" => $type, "is_del" => 0))->find();
        if ($paymentList) {
            $paymentList['card'] = substr_replace($paymentList['card'], ' **** **** ', 4, -4);
            $this->apiReturn($paymentList);
        } else {
            $this->apiError('未添加银行卡');
        }
    }

    /**
     *查询支付宝信息
     * 2018年6月7日13:37:44
     * 刘北林
     */
    public function alipaySelect()
    {
        $unionid = isset($_REQUEST['unionid']) ? $_REQUEST['unionid'] : "";//用户id
        $type = 1;// 类型 1：支付宝 2：银行卡
        if (!$unionid) {
            $this->apiError('非法错误');
        }
        $paymentList = $this->payment_add_model->where(array("unionid" => $unionid, "type" => $type, "is_del" => 0))->find();
        if ($paymentList) {
            $paymentList['card'] = substr_replace($paymentList['card'], ' **** **** ', 4);
            $this->apiReturn($paymentList);
        } else {
            $this->apiError('未添加支付宝');
        }
    }

    /**
     * 2018年6月7日13:55:31
     * 刘北林
     * 修改密码
     */
    public
    function changePassword()
    {
        $unionid = isset($_REQUEST['unionid']) ? $_REQUEST['unionid'] : "";//用户id
        $idcard = isset($_REQUEST['idcard']) ? $_REQUEST['idcard'] : "";//用户id
        $password = isset($_REQUEST['password']) ? $_REQUEST['password'] : "";//用户id
        if (!$unionid || !$idcard || !$password) {
            $this->apiError('非法错误');
        }
        $usersList = $this->users_model->where(array("unionid" => $unionid))->find();
        if ($usersList) {
            if ($idcard != $usersList['idcard']) {
                $this->apiError('与绑定身份证不一致');
            } else {
                $data = array(
                    'password' => sp_password($password),
                    'create_time' => date('Y-m-d H:i:s', time())
                );
                $this->users_model->where(array("id" => $usersList['id']))->save($data);
                $this->apiSuccess('修改成功');
            }
        } else {
            $this->apiError('修改失败');
        }

    }

    /**
     * 设置密码
     * 2018年6月7日13:58:42
     * 刘北林
     */
    public
    function setPassword()
    {
        $unionid = isset($_REQUEST['unionid']) ? $_REQUEST['unionid'] : "";//用户id
        $password = isset($_REQUEST['password']) ? $_REQUEST['password'] : "";//用户id
        if (!$unionid || !$password) {
            $this->apiError('非法错误');
        }
        $usersList = $this->users_model->where(array("unionid" => $unionid))->find();
        if ($usersList) {
            $data = array(
                'password' => sp_password($password),
                'create_time' => date('Y-m-d H:i:s', time())
            );
            $this->users_model->where(array("id" => $usersList['id']))->save($data);
            $this->apiSuccess('设置密码成功');
        } else {
            $this->apiError('修改失败');
        }
    }

    /**
     *密码是否正确
     * 2018年6月7日14:03:403
     * 刘北林
     */
    public
    function passwordIsCorrect()
    {
        $unionid = isset($_REQUEST['unionid']) ? $this->remove_xss($_REQUEST['unionid']) : "";//用户id
        $password = isset($_REQUEST['password']) ? $this->remove_xss($_REQUEST['password']) : "";//用户id
        if (!$unionid || !$password) {
            $this->apiError('非法错误');
        }
        $usersList = $this->users_model->where(array("unionid" => $unionid))->find();
        if ($usersList) {
            if (sp_compare_password($password, $usersList['password'])) {
                $this->apiSuccess('密码正确');
            } else {
                $this->apiError('密码错误');
            }
        } else {
            $this->apiError('非法错误');
        }

    }

    /**
     * 2018年6月8日17:45:07
     * 刘北林
     * 删除 银行卡
     */
    public
    function cardDel()
    {
        $unionid = isset($_REQUEST['unionid']) ? $this->remove_xss($_REQUEST['unionid']) : "";//用户id
        $type = 2;// 类型 1：支付宝 2：银行卡
        if (!$unionid) {
            $this->apiError('非法错误');
        }
        $this->payment_add_model->where(array("unionid" => $unionid, "type" => $type))->save(array("is_del" => 1));
        $this->apiSuccess('删除成功');
    }

    /**
     * 2018年6月8日17:45:07
     * 刘北林
     * 删除 支付宝
     */
    public
    function alipayDel()
    {
        $unionid = isset($_REQUEST['unionid']) ? $this->remove_xss($_REQUEST['unionid']) : "";//用户id
        $type = 1;// 类型 1：支付宝 2：银行卡
        if (!$unionid) {
            $this->apiError('非法错误');
        }
        $this->payment_add_model->where(array("unionid" => $unionid, "type" => $type))->save(array("is_del" => 1));
        $this->apiSuccess('删除成功');
    }

    /**
     * 2018年6月9日23:32:13
     * 刘北林
     * 提现申请
     */
    public
    function withdrawalApplication()
    {
        $unionid = isset($_REQUEST['unionid']) ? $this->remove_xss($_REQUEST['unionid']) : "";//用户id
        $type = isset($_REQUEST['type']) ? $this->remove_xss($_REQUEST['type']) : "";//用户id
        $money = isset($_REQUEST['money']) ? $this->remove_xss($_REQUEST['money']) : "";//所属支行
        if (!$type || !$money || !$money) {
            $this->apiError('非法错误');
        }
        $pay = $this->payment_add_model->where(array("unionid" => $unionid, "type" => $type, "is_del" => 0))->find();
        $data = array(
            'unionid' => $unionid,
            'type' => $type,
            'status' => 1,
            'payment_id' => $pay['id'],
            'money' => $money,
            'time' => date('Y-m-d H:i:s', time())
        );
        $this->withdraw_model->add($data);
        $this->apiSuccess('申请成功');
    }

    /**
     * 2018年6月11日18:22:10
     * 刘北林
     * 反馈
     */
    public function feedback()
    {
        $unionid = isset($_REQUEST['unionid']) ? $this->remove_xss($_REQUEST['unionid']) : "";//用户id
        $conten = isset($_REQUEST['conten']) ? $this->remove_xss($_REQUEST['conten']) : "";
        $number = isset($_REQUEST['number']) ? $this->remove_xss($_REQUEST['number']) : "";

        if (!$conten || !$number || !$unionid) {
            $this->apiError('非法错误');
        }
        $conten = $this->escapeString($conten);
        $data = array(
            'unionid' => $unionid,
            'conten' => $conten,
            'number' => $number,
            'time' => date('Y-m-d H:i:s', time())
        );
        $feedbackList = $this->feedback_model->add($data);
        if ($feedbackList) {
            $this->apiSuccess('反馈成功');
        } else {
            $this->apiError('反馈失败');
        }
    }




    #选择支付方式#
    #记得填写授权目录
    public function actionWxhandle()
    {
        $unionid = isset($_REQUEST['unionid']) ? $this->remove_xss($_REQUEST['unionid']) : "";//用户id
        $type = $_GET['vip'] ? $_GET['vip'] : 0;
        $total_fee = $_GET['total_fee'] * 100;
        if (!$total_fee || !$unionid || !$type) {
            $this->apiError('非法错误');
        }
        $usersList = $this->users_model->where(array("unionid" => $unionid))->find();


        header("Content-Type: text/html; charset=UTF-8");
        $pay = new PayController();
        $conf = $pay->orderEntry($this->appid, $this->mch_id, $total_fee, $usersList['xopenid'], $this->key);
        if (!$conf || $conf['return_code'] == 'FAIL') exit("<script>alert('对不起，微信支付接口调用错误!" . $conf['return_msg'] . "');history.go(-1);</script>");

        $this->orderid = $conf['prepay_id'];

//        $access_token = $this->obtainTokens($this->appid, $this->appsecret);
//        //推送模板消息
//        $userList = "https://api.weixin.qq.com/cgi-bin/message/wxopen/template/send?access_token=".$access_token;
//        $c = new \Curl();
//        $params = array('touser' => $usersList['xopenid'], 'template_id' => 'vQXKKjfx4ab2mtLA82Mvjo0ARKIDpaxBwxmhT-mCGvQ', 'form_id' => $conf['prepay_id']);
//        $params['data'] = array();
//        $params['data']['keyword1'] = array('value' => "111");
//        $params['data']['keyword2'] = array('value' => date("Y-m-d H:i:s"));
//        $params['data']['keyword3'] = array('value' => "11");
//        $post = json_encode($params);
//        $c->post($userList, $post);


        //订单是否支付
        if ($this->orderid) {
            $orderModel['prepay_id'] = $conf['prepay_id'];
            $orderModel['time'] = date("Y-m-d H:i:s", time());
            $orderModel['status'] = -1;
            $orderModel['type'] = $type;
            $orderModel['uid'] = $this->getId($usersList['xopenid']);
            $orderModel['total_fee'] = $total_fee;
            $orderModel['platform'] =1;
            $orderModel['company'] = $conf["company"];
            $this->order_model->add($orderModel);
        }
        //生成页面调用参数
//        print_r($conf);
        $timeStamp = time();
        $jsApiObj = array(
            "appId" => $conf['appid'],
            "timeStamp" => "$timeStamp",
            "nonceStr" => $pay->createNoncestr(),
            "package" => "prepay_id=" . $conf['prepay_id'],
            "signType" => "MD5"
        );
        $jsApiObj["paySign"] = $pay->MakeSign($jsApiObj, $this->key);
        $data['data'] = $jsApiObj;
        $this->apiReturn($data);
//
//        $ch = curl_init();
//        $data = http_build_query($jsApiObj);
//        curl_setopt($ch, CURLOPT_URL, 'http://beilinliu.kuaifengpay.com/?total_fee=1');
//        curl_setopt($ch, CURLOPT_POST, 1);
//        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
//
//        $result = curl_exec($ch);
//        curl_close($ch);
//        return $result;
    }

    /**
     * 2018年5月24日17:46:24
     * 刘北林
     * jsapi
     * @return mixed
     * @throws WxPayException
     */
    public function orderEntry($total_fee, $openid)
    {
        $data['appid'] = "wxea6d1e32583c3461";
        $data['mch_id'] = "1502179691"; //商户号
        $data['device_info'] = 'WEB';
        $data['body'] = "健康机支付";
        $data['out_trade_no'] = $this->createNoncestr(); //订单号
        $data['total_fee'] = $total_fee; //金额
        $data['spbill_create_ip'] = $_SERVER["REMOTE_ADDR"];
        $data['notify_url'] = "https://bolon.kuaifengpay.com/Api/Applets/successfulCallback";           //通知url
        $data['trade_type'] = 'JSAPI';
        $data['openid'] = $openid;   //获取openid
        $data['nonce_str'] = $this->createNoncestr();
        $data['sign'] = $this->MakeSign($data);
        $userListss = "https://api.mch.weixin.qq.com/pay/unifiedorder";
//
//
//        $params['appid'] = "wx197d7a2fbf590ca0";//公众账号ID
//        $params['mch_id'] = "1502736831";//商户号
//        $params['device_info'] = "WEB";//设备号
//        $params['nonce_str'] = $this->GetRandStr(32);//随机字符串
//        $params['sign'] = $this->SetSign();//签名
//        $params['sign_type'] = "MD5";//签名类型
//        $params['body'] = "健康机支付";//商品描述
//        $params['detail'] = "欢迎";//商品详情
//        $params['attach'] = "快风科技";//附加数据
//        $params['out_trade_no'] = $this->GetRandStr(32);//商户订单号
//        $params['fee_type'] = "CNY"; //标价币种
//        $params['total_fee'] = "1"; //标价金额
//        $params['spbill_create_ip'] = $_SERVER["REMOTE_ADDR"]; //终端IP
//        $params['time_start'] = ""; //交易起始时间
//        $params['time_expire'] = ""; //交易结束时间
//        $params['goods_tag'] = ""; //订单优惠标记
//        $params['notify_url'] = "http://bolon.kuaifengpay.com/Api/Index/liubolin"; //通知地址
//        $params['trade_type'] = "JSAPI"; //交易类型
//        $params['product_id'] = $this->GetRandStr(32);//商品ID
//        $params['limit_pay'] = ""; //指定支付方式
//        $params['openid'] = $authorizedList['openid']; //指定支付方式
//        $params['scene_info'] = ""; //指定支付方式

        $xml = $this->ToXml($data);

        $curl = curl_init(); // 启动一个CURL会话
        curl_setopt($curl, CURLOPT_URL, $userListss); // 要访问的地址
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);

        //设置header
        curl_setopt($curl, CURLOPT_HEADER, FALSE);

        //要求结果为字符串且输出到屏幕上
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($curl, CURLOPT_POST, TRUE); // 发送一个常规的Post请求
        curl_setopt($curl, CURLOPT_POSTFIELDS, $xml); // Post提交的数据包
        curl_setopt($curl, CURLOPT_TIMEOUT, 30); // 设置超时限制防止死循环
        $tmpInfo = curl_exec($curl); // 执行操作
        curl_close($curl); // 关闭CURL会话
        $arr = $this->FromXml($tmpInfo);

        return $arr;
    }

    /**
     * 2018年5月24日17:32:38
     * 获取用户ID
     * @return mixed
     */
    public function getId($openid)
    {
        $openid = $openid;
        $userOne = $this->users_model->where(array("xopenid" => $openid))->find();
        return $userOne['id'];
    }

    /**
     * 支付成功
     */
    public function actionAucess()
    {
        $xopenid = isset($_REQUEST['xopenid']) ? $_REQUEST['xopenid'] : "";//用户id
        if (!$xopenid) {
            $this->apiError('非法错误');
        }
//        $access_token = $this->obtainTokens($this->appid, $this->appsecret);
//        //推送模板消息
//        $userList = "https://api.weixin.qq.com/cgi-bin/message/wxopen/template/send?access_token==" . $access_token;
//        $c = new \Curl();
//        $params = array('touser' => $xopenid, 'template_id' => 'vQXKKjfx4ab2mtLA82Mvjo0ARKIDpaxBwxmhT-mCGvQ', 'form_id' => 'formId', 'color' => '#173177');
//        $params['data'] = array();
//        $params['data']['keyword1'] = array('value' => "111");
//        $params['data']['keyword2'] = array('value' => date("Y-m-d H:i:s"));
//        $params['data']['keyword3'] = array('value' => 11);
//        $post = json_encode($params);
//        $c->post($userList, $post);

        $userOne = $this->users_model->where(array("xopenid" => $xopenid))->find();

        $data['status'] = 1;
        $data['success'] = 1;
        $data['update_time'] = date("Y-m-d H:i:s", time());
        $oModel = $this->order_model->where(array("uid" => $userOne['id']))->order('time DESC')->find();
        $this->order_model->where(array("id" => $oModel['id']))->save($data);
        $gifts_integral =0;
        if ($oModel['success'] != 1) {
            //会有问题 充会员
            $datas['is_vip'] = $oModel['type'];
            $times = "";
            switch ($oModel['type']) {
                case 1:
                    $times = date("Y-m-d H:i:s", strtotime("+1 day"));
                    $gifts_integral=29.9;
                    break;
                case 2:
                    $times = date("Y-m-d H:i:s", strtotime("+6 month"));
                    $gifts_integral=188.9;
                    break;
                case 3:
                    $times = date("Y-m-d H:i:s", strtotime("+1 year"));
                    $gifts_integral=365;
                    break;
                case 4:
                    $times = date("Y-m-d H:i:s", strtotime("+3 year"));
                    $gifts_integral=2000;
                    break;
                case 5:
                    $times = date("Y-m-d H:i:s", strtotime("+5 year"));
                    $gifts_integral=10000;
                    break;
                case 7:
                    $times = date("Y-m-d H:i:s", strtotime("+2 year"));
                    $gifts_integral=2000;
                    break;
                case 8:
                    $times = date("Y-m-d H:i:s", strtotime("+3 year"));
                    $gifts_integral=10000;
                    break;
            }

            if ($userOne['is_vip'] == 2 || $userOne['is_vip'] == 3 || $userOne['is_vip'] == 4 || $userOne['is_vip'] == 5|| $userOne['is_vip'] == 7|| $userOne['is_vip'] == 8) {
                $datas['time_maturity'] = date("Y-m-d H:i:s", (strtotime($userOne['time_maturity']) - strtotime(date("Y-m-d H:i:s", time()))) + strtotime($times));
            } else {
                $datas['time_maturity'] = $times;
                $datas['time_start'] = date("Y-m-d H:i:s", time());
            }
            $datas['is_pays'] = 1;
            $datas['gifts_integral'] = $gifts_integral;
            $this->users_model->where(array("xopenid" => $xopenid))->save($datas);

            $this->apiSuccess('支付成功');
        }
    }

    /**
     * 2018年5月24日17:28:25
     * 刘北林
     * 公众号支付回调
     */
    public function successfulCallback()
    {
        $xml = file_get_contents('php://input', 'r');

        $arr = json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
        $userOne = $this->users_model->where(array("xopenid" => S("xopenid")))->find();
        if (isset($arr['result_code']) == 'SUCCESS') {
//            $data['status'] = 1;
//            $data['success'] = 1;
//            $data['update_time'] = date("Y-m-d H:i:s", time());
//
//            $oModel = $this->order_model->where(array("uid" => $userOne['id']))->order('time DESC')->find();
//            $this->order_model->where(array("id" => $oModel['id']))->save($data);
//
//
//            if ($oModel['success'] != 1) {
//
//                //会有问题 充会员
//                $datas['is_vip'] = $oModel['type'];
//                $times = "";
//                switch ($oModel['type']) {
//                    case 1:
//                        $times = date("Y-m-d H:i:s", strtotime("+1 day"));
//                        break;
//                    case 2:
//                        $times = date("Y-m-d H:i:s", strtotime("+6 month"));
//                        break;
//                    case 3:
//                        $times = date("Y-m-d H:i:s", strtotime("+1 year"));
//                        break;
//                }
//
//                if ($userOne['is_vip'] == 2 || $userOne['is_vip'] == 3) {
//                    $datas['time_maturity'] = date("Y-m-d H:i:s", (strtotime($userOne['time_maturity']) - strtotime(date("Y-m-d H:i:s", time()))) + strtotime($times));
//                } else {
//                    $datas['time_maturity'] = $times;
//                    $datas['time_start'] = date("Y-m-d H:i:s", time());
//                }
//                $datas['is_pays'] = 1;
//                $this->users_model->where(array("xopenid" => S("xopenid")))->save($datas);
//                $this->pushInformation();
//            }
        }
        // 校验返回的订单金额是否与商户侧的订单金额一致。修改订单表中的支付状态。
        $return = array("return_code" => "SUCCESS", "return_msg" => "OK");
        $xml = '<xml>';
        foreach ($return as $k => $v) {
            $xml .= '<' . $k . '><![CDATA[' . $v . ']]></' . $k . '>';
        }
        $xml .= '</xml>';

        echo $xml;


//        $xml = file_get_contents('php://input', 'r');
////        $xml = file_get_contents("php://input");
//        $log = json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
//        $id = $log['out_trade_no'];  //获取单号
//        print_r("<script>alert('2');</script>");
//
//        //这里修改状态
//        exit('SUCCESS');  //打死不能去掉
    }

    public function createwxaqrcode()
    {
        $access_token = $this->obtainTokens($this->appid, $this->appsecret);

        $unionid = isset($_REQUEST['unionid']) ? $this->remove_xss($_REQUEST['unionid']) : "";//用户id
        if (!$unionid) {
            $this->apiError('非法错误');
        }
        $c = new \Curl();
        //推送消息
        $userList = "https://api.weixin.qq.com/cgi-bin/wxaapp/createwxaqrcode?access_token=" . $access_token;

        $post = '{"path":"/pages/home/my-tg-callback/my-tg-callback?encryptionId=' . base64_encode($unionid) . ', "width": 528}';

        $data['data'] = 'data:image/png;base64,' . base64_encode($c->post($userList, $post));

//        $userOne = $this->small_code_model->where(array("unionid" => $unionid))->find();
//        if($userOne){
//            $data['data'] =$userOne['img'];
//        }else{
//            $img = file_get_contents( $data['data']);
//            $t ="kuaifeng_".time().".jpg";
//            file_put_contents("./public/kuaifeng/".$t,$img);
//            $data['data'] ="https://bolon.kuaifengpay.com/public/kuaifeng/".$t;
//            $datas['unionid'] = $unionid;
//            $datas['img'] = $data['data'];
//            $datas['time'] = date("Y-m-d H:i:s", time());
//            $this->small_code_model->add($datas);
//        }

        $this->apiSuccess($data);
    }

    /**
     *
     */
    public function newss()
    {
        //        $offset = (3-1)*2; //偏移量
//        ->limit( $offset.',2')
        $posts['data'] = $this->term_relationships_model
            ->alias("a")
            ->join(C('DB_PREFIX') . "posts b ON a.object_id = b.id")
            ->join("__TERMS__ t ON a.term_id = t.term_id")
            ->field("a.tid,b.id,b.post_title,b.post_excerpt,b.smeta,b.post_modified")
            ->order("b.post_modified DESC")
            ->select();
        foreach ($posts['data'] as $key => $post) {
            $posts['data'][$key]['details'] = "https://bolon.kuaifengpay.com/portal/article/index/id/" . $post['tid'] . ".html";
            $post = json_decode($post['smeta'], true);
            $posts['data'][$key]['smeta'] = "https://bolon.kuaifengpay.com/data/upload/" . $post['thumb'];
        }
        $this->apiReturn($posts);


    }

    /**
     *2018年6月14日21:16:19
     * 刘北林
     * 新闻列表
     */
    public function News()
    {
        //        $offset = (3-1)*2; //偏移量
//        ->limit( $offset.',2')
//        $posts['data'] = $this->term_relationships_model
//            ->alias("a")
//            ->join(C('DB_PREFIX') . "posts b ON a.object_id = b.id")
//            ->join("__TERMS__ t ON a.term_id = t.term_id")
//            ->field("a.tid,b.id,b.post_title,b.post_excerpt,b.smeta,b.post_modified")
//            ->order("b.post_modified DESC")
//            ->select();
//        foreach ($posts['data'] as $key => $post) {
//            $posts['data'][$key]['details'] = "https://bolon.kuaifengpay.com/portal/article/index/id/" . $post['tid'] . ".html";
//            $post = json_decode($post['smeta'], true);
//            $posts['data'][$key]['smeta'] = "https://bolon.kuaifengpay.com/data/upload/" . $post['thumb'];
//        }
//        $this->apiReturn($posts);


        $c = new \Curl();
//        $userList = "http://g.ebelter.com/ebelter-api/article/column/list";
        $userList = "http://g.ebelter.com/ebelter-api/article/article/list?column_id=6";
        $getList = $c->get($userList);
        $user_token = json_decode($getList, true);
        $newsList = array();
        foreach ($user_token as $key => $post) {
            if ($key <= 2) {
                $newsList['data'][$key] = $post;
           }
       }
        /*$bb=array();
        $aa ='{
	"msg": "success",
	"status": 1,
	"data": [{
		"audio_url": "https://img1.utuku.china.com/532x0/toutiao/20180925/5f05fd7f-1856-4415-ae0f-44237ae3696a.jpg",
		"author": "刘仁杰",
		"content": "章子怡与汪峰刚开始恋情曝光外界并不看好，两人经历许多坎坷才最终步入婚姻殿堂。不想两人结婚后简直把生活经营成偶像剧，不仅常常高调秀恩爱，私下也很甜蜜，就连汪峰与前女友所生女儿小苹果，都和章子怡关系非常很好，如同亲生母女，一家四口是越过越幸福了。",
		"id": 1238,
		"image_tv": "https://img1.utuku.china.com/532x0/toutiao/20180925/5f05fd7f-1856-4415-ae0f-44237ae3696a.jpg",
		"origin": "熙康",
		"release_date": "2011-12-31 12:05:50",
		"short_title": "",
		"summary": "章子怡与汪峰刚开始恋情曝光外界并不看好，两人经历许多坎坷才最终步入婚姻殿堂。",
		"title": "章子怡中秋节晒一家四口团聚照",
		"title_img": "https://img1.utuku.china.com/532x0/toutiao/20180925/5f05fd7f-1856-4415-ae0f-44237ae3696a.jpg",
		"type_id": 1,
		"views": 10389
	}, {
		"audio_url": "http://p0.ifengimg.com/pmop/2018/0924/8C261877AECCE7AB70B427E4996E78A951E93595_size75_w500_h525.jpeg",
		"author": "刘仁杰",
		"content": "当然小儿子自己发挥的很好，其他人都是握拳，唯独他没有同步，吃瓜群众说这是个摇滚宝宝。",
		"id": 1223,
		"image_tv": "http://p0.ifengimg.com/pmop/2018/0924/8C261877AECCE7AB70B427E4996E78A951E93595_size75_w500_h525.jpeg",
		"origin": "熙康",
		"release_date": "2011-12-28 14:29:41",
		"short_title": "",
		"summary": "吴京二胎中秋节出生，小儿子的名字让人想笑？",
		"title": "吴京二胎中秋节出生？",
		"title_img": "http://p0.ifengimg.com/pmop/2018/0924/8C261877AECCE7AB70B427E4996E78A951E93595_size75_w500_h525.jpeg",
		"type_id": 1,
		"views": 1785
	}, {
		"audio_url": "http://p0.ifengimg.com/pmop/2018/0925/3121797F97AFF1A705AE0DED6B3F3D37E44C2502_size60_w297_h267.jpeg",
		"author": "jiang-yc",
		"content": "周杰伦在录制现场被cue到向观众送中秋祝福，耿直的周董表示",
		"id": 1324,
		"image_tv": "http://p0.ifengimg.com/pmop/2018/0925/3121797F97AFF1A705AE0DED6B3F3D37E44C2502_size60_w297_h267.jpeg",
		"origin": "熙康",
		"release_date": "2011-11-24 13:59:36",
		"short_title": "",
		"summary": "周杰伦中秋祝福：不要吃得像月亮一样圆！网友：去年不是这么说的？",
		"title": "周杰伦中秋祝福？",
		"title_img": "http://p0.ifengimg.com/pmop/2018/0925/3121797F97AFF1A705AE0DED6B3F3D37E44C2502_size60_w297_h267.jpeg",
		"type_id": 1,
		"views": 2963
	}]
}';

        $bb = json_decode($aa, true);*/
        $this->apiReturn($newsList);
    }

    /**
     *2018年7月3日21:26:11
     *新闻分类
     * 刘北林
     */
    public function categoriesOfNews()
    {
        $c = new \Curl();
        $userList = "http://g.ebelter.com/ebelter-api/article/column/list";
        $getList = $c->get($userList);
        $user_token = json_decode($getList, true);
        $newsList = array();
        foreach ($user_token as $key => $post) {
            $newsList['data'][$key]['name'] = $post['name'];
            $newsList['data'][$key]['id'] = $post['id'];
        }
        $this->apiReturn($newsList);
    }

    /**
     *2018年7月3日21:26:11
     *新闻列表
     * 刘北林
     */
    public function newsList()
    {
        $column_id = isset($_REQUEST['column_id']) ? $_REQUEST['column_id'] : 6;//用户id
        $c = new \Curl();
        $userList = "http://g.ebelter.com/ebelter-api/article/article/list?column_id=" . $column_id;
        $getList = $c->get($userList);
        $user_token = json_decode($getList, true);
        $newsList = array();
        foreach ($user_token as $key => $post) {
            $newsList['data'][$key] = $post;
        }
        $this->apiReturn($newsList);
    }


    public function bubble_sort(&$sort, &$a, $type = 'asc')
    {//默认为正序排列
        $len = count($a);
        if ($type == 'desc') {
            //从大到小，倒序排列
            for ($i = 1; $i < $len; $i++) {
                for ($j = $len - 1; $j >= $i; $j--) {

                    if ($a[$j] > $a[$j - 1]) {
                        $x = $a[$j];
                        $a[$j] = $a[$j - 1];
                        $a[$j - 1] = $x;

                        $y = $sort[$j];
                        $sort[$j] = $sort[$j - 1];
                        $sort[$j - 1] = $y;
                    }
                }
            }
        } else {
            //从小到大，正序排列
            for ($i = 1; $i < $len; $i++) {
                for ($j = $len - 1; $j >= $i; $j--) {
                    if ($a[$j] < $a[$j - 1]) {
                        $x = $a[$j];
                        $a[$j] = $a[$j - 1];
                        $a[$j - 1] = $x;

                        $y = $sort[$j];
                        $sort[$j] = $sort[$j - 1];
                        $sort[$j - 1] = $y;
                    }
                }
            }
        }
        return $sort;
    }


    /**
     * 体质答题
     * 刘北林
     * 2018年6月20日16:51:40
     */
    public function constitutionAnswer()
    {
        $data = $_REQUEST['data'];
        if (!$data) {
            $this->apiError('非法错误');
        }
        $json = json_decode($data, true);
        $sort = array();
        foreach ($json as $index => $value) {
            $sort[$index - 1]['con'] = array_sum($value);
            $sort[$index - 1]['count'] = count($value);
        }

        $constitution = array(
            '0' => array('answer' => '平和质', "suggest" => "辨识与调节方法：正常的体质。调节：饮食有节制，不要常吃过冷过热或不干净的食物，粗细粮食要合理搭配"),
            '1' => array('answer' => '气虚质', "suggest" => "辨识与调节方法：肌肉松软，声音低，易出汗，易累，易感冒。调节：多食用具有益气健脾作用的食物，如黄豆、白扁豆、鸡肉等。少食空心菜、生萝卜等。"),
            '2' => array('answer' => '阳虚质', "suggest" => "辨识与调节方法：肌肉不健壮，常常感到手脚发凉，衣服比别人穿得多，夏天不喜欢吹空调，喜欢安静，性格多沉静、内向。调节：平时可多食牛肉、羊肉等温阳之品，少食梨、西瓜、荸荠等生冷寒凉食物，少饮绿茶。"),
            '3' => array('answer' => '阴虚质', "suggest" => "辨识与调节方法：体形多瘦长，不耐暑热，常感到眼睛干涩，口干咽燥，总想喝水，皮肤干燥，经常大便干结，容易失眠。调节：多食瘦猪肉、鸭肉、绿豆、冬瓜等甘凉滋润之品，少食羊肉、韭菜、辣椒、葵花子等性温燥烈之品。适合太极拳、太极剑、气功等项目。"),
            '4' => array('answer' => '痰湿质', "suggest" => "辨识与调节方法：体形肥胖，腹部肥满而松软。易出汗，且多黏腻。经常感觉脸上有一层油。调节：饮食应以清淡为主，可多食冬瓜等。因体形肥胖，易于困倦，故应根据自己的具体情况循序渐进，长期坚持运动锻炼"),
            '5' => array('answer' => '湿热质', "suggest" => "辨识与调节方法：面部和鼻尖总是油光发亮，脸上易生粉刺，皮肤易瘙痒。常感到口苦、口臭，脾气较急躁。调节：饮食以清淡为主，可多食赤小豆、绿豆、芹菜、黄瓜、藕等甘寒的食物。适合中长跑、游泳、爬山、各种球类、武术等。"),
            '6' => array('answer' => '血瘀质', "suggest" => "辨识与调节方法：皮肤较粗糙，眼睛里的红丝很多，牙龈易出血。调节：多食山楂、醋、玫瑰花等，少食肥肉等滋腻之品。可参加各种舞蹈、步行健身法、徒手健身操等。"),
            '7' => array('answer' => '气郁质', "suggest" => "辨识与调节方法：体形偏瘦，常感到闷闷不乐、情绪低沉，常有胸闷，经常无缘无故地叹气,易失眠。调节：多食黄花菜、海带、山楂、玫瑰花等具有行气、解郁、消食、醒神作用的食物。气郁体质的人不要总待在家里，要多参加群众性的体育运动项目。"),
            '8' => array('answer' => '特禀质', "suggest" => "辨识与调节方法：这是一类体质特殊的人群。其中过敏体质的人易对药物、食物、气味、花粉、季节过敏。调节：多食益气固表的食物，少食荞麦(含致敏物质荞麦荧光素)、蚕豆等。居室宜通风良好。保持室内清洁，被褥、床单要经常洗晒，可防止对尘螨过敏。")
        );
//        $sort = array(
//            '0' => array('con' => '25', "count" => "8"),
//            '1' => array('con' => '15', "count" => "8"),
//            '2' => array('con' => '9', "count" => "8"),
//            '3' => array('con' => '9', "count" => "8"),
//            '4' => array('con' => '30', "count" => "8"),
//            '5' => array('con' => '9', "count" => "8"),
//            '6' => array('con' => '9', "count" => "8"),
//            '7' => array('con' => '9', "count" => "8"),
//            '8' => array('con' => '9', "count" => "8")
//        );
        $m = Array();
        foreach ($sort as $j => $item) {
            $m[$j]['con'] = (($item['con'] - $item['count']) / ($item['count'] * 4)) * 100;
        }

//        $a = Array();
//
//        foreach ($sort as $key => $val) {
//            $a[] = $val['con'];//$a是$sort的其中一个字段
//        }
        $b = array();//最大得分大于60
        $c = array();//其中大于40
        $d = array();//其中30～39
        $e = array();//建议 最大大于60
        $f = array();//建议 第二 大于40
        $g = array();//建议 30～39
        $h = array();//建议 30分一下
        $n = array();//建议 30分一下

//        $constitutionAnswer = $this->bubble_sort($sort, $a, 'desc');
        //最大得分大于60分
        foreach ($m as $k => $value) {
            if ($value['con'] >= 60) {
                $b["id"] = $k;
                $b["con"] = $value['con'];
//              break; // 当 $value为c时，终止循环
            } else if ($value['con'] >= 40) {
                $c["id"] = $k;
                $c["con"] = $value['con'];
            } else if ($value['con'] >= 30 && $value['con'] <= 39) {
                $d["id"] = $k;
                $d["con"] = $value['con'];
            } else {
                $h["id"] = $k;
                $h["con"] = $value['con'];
            }
        }

        foreach ($constitution as $l => $item) {
            if (isset($b['id']) || $b['id']) {
                if ($b['id'] == $l) {

                    $e['answer'] = $item['answer'];
                    $e['suggest'] = $item['suggest'];
                }
            }
            if (isset($c['id']) || $c['id']) {
                if ($c['id'] == $l) {
                    $f['answer'] = $item['answer'];
                    $f['suggest'] = $item['suggest'];
                }
            }
            if (isset($d['id']) || $d['id']) {
                if ($d['id'] == $l) {
                    $g['answer'] = $item['answer'];
                    $g['suggest'] = $item['suggest'];
                }
            }
            if (isset($h['id']) || $h['id']) {
                if ($h['id'] == $l) {
                    $n['answer'] = $item['answer'];
                    $n['suggest'] = $item['suggest'];
                }
            }
        }

        if ($f) {
            $this->apiReturn($f);
        } else if ($e) {
            $this->apiReturn($e);
        } else if ($g) {
            $this->apiReturn($g);
        } else {
            $this->apiReturn($n);
        }

    }

    public function ceshi()
    {
        //json也可以
        $data_string = json_encode(array());
        //curl验证成功
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, "http://www.kolstore.com/interface/indexaccount.php");
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data_string);
        curl_setopt($curl, CURLOPT_HEADER, 0);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json; charset=utf-8',
                'Content-Length: ' . strlen($data_string)
            )
        );
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $res = curl_exec($curl);
        curl_close($curl);
        echo $res;


    }

    public function base64EncodeImage($image_file)
    {
        $base64_image = '';
        $image_info = getimagesize($image_file);
        $image_data = fread(fopen($image_file, 'r'), filesize($image_file));
        $base64_image = 'data:' . $image_info['mime'] . ';base64,' . chunk_split(base64_encode($image_data));
        return $base64_image;
    }
    /*
    * AI皮肤检测
    * 2018年7月17日16:00:23
    */
//    public function AISkinTests()
//    {
//        $img = 'public/images/slide1.jpg';
//        $base64_img = $this->base64EncodeImage($img);
//        print_r($base64_img);
//        echo '<img src="' . $base64_img . '" />';
//
//
//
//        //脸部关键区域检测
////        $host = "http://facediag.market.alicloudapi.com";
////        $path = "/api/face_region/";
////        $method = "POST";
////        $appcode = "c82cf0ec7769445585f073118e633c4e";
////        $headers = array();
////        array_push($headers, "Authorization:APPCODE " . $appcode);
////        //根据API的要求，定义相对应的Content-Type
////        array_push($headers, "Content-Type" . ":" . "application/x-www-form-urlencoded; charset=UTF-8");
////        $querys = "";
////        $bodys = "image_url=https://bolon.kuaifengpay.com/public/images/111.jpg&type=0";
////        $url = $host . $path;
////
////        $jsinChin = $this->curlPostList($method, $url, $headers, $host, $bodys);
////
////        //面部油干性检测
////        $host = "http://facediag.market.alicloudapi.com";
////        $path = "/api/face_dry/";
////        $method = "POST";
////        $appcode = "c82cf0ec7769445585f073118e633c4e";
////        $headers = array();
////        array_push($headers, "Authorization:APPCODE " . $appcode);
////        //根据API的要求，定义相对应的Content-Type
////        array_push($headers, "Content-Type" . ":" . "application/x-www-form-urlencoded; charset=UTF-8");
////        $querys = "";
////        $bodys = "image_url=https://bolon.kuaifengpay.com/public/images/111.jpg&type=0&region=" . $jsinChin;
////        $url = $host . $path;
////
////        $data['facial'] = json_decode($this->curlPostList($method, $url, $headers, $host, $bodys), true);
////
////        //年龄检测
////        $host = "http://facediag.market.alicloudapi.com";
////        $path = "/api/face_age";
////        $method = "POST";
////        $appcode = "c82cf0ec7769445585f073118e633c4e";
////        $headers = array();
////        array_push($headers, "Authorization:APPCODE " . $appcode);
////        //根据API的要求，定义相对应的Content-Type
////        array_push($headers, "Content-Type" . ":" . "application/x-www-form-urlencoded; charset=UTF-8");
////        $querys = "";
////        $bodys = "image_url=https://bolon.kuaifengpay.com/public/images/111.jpg&type=0";
////        $url = $host . $path;
////
////        $data['age'] = json_decode($this->curlPostList($method, $url, $headers, $host, $bodys), true);
////
////        //脸部基本属性
////        $host = "http://facediag.market.alicloudapi.com";
////        $path = "/api/face_attr/";
////        $method = "POST";
////        $appcode = "c82cf0ec7769445585f073118e633c4e";
////        $headers = array();
////        array_push($headers, "Authorization:APPCODE " . $appcode);
////        //根据API的要求，定义相对应的Content-Type
////        array_push($headers, "Content-Type" . ":" . "application/x-www-form-urlencoded; charset=UTF-8");
////        $querys = "";
//////        $img = file_get_contents("https://bolon.kuaifengpay.com/public/images/111.jpg");
//////        $img = base64_encode($img);
////        $bodys = "image_url=https://bolon.kuaifengpay.com/public/images/111.jpg&type=0";
////        $url = $host . $path;
////
////        $data['basicAttribute'] = json_decode($this->curlPostList($method, $url, $headers, $host, $bodys), true);
////
////        $this->apiReturn($data);
//    }
    public function sss()
    {

        $c = new \Curl();
        $userUrl = "https://www.78977a.com/api/live?code=pk10";
        //json转数组
        $authorizedList = json_decode($c->get($userUrl), true);
        print_r($authorizedList);
    }

    /**
     * AI皮肤检测
     * 2018年7月17日16:00:23
     */
    public function AISkinTest()
    {
//        $img = 'public/images/111.jpg';
//        $image_url = $this->base64EncodeImage($img);
//        $idCard ="421222199308211218";
//        $sn ="1111";
        $raw_post_data = file_get_contents('php://input', 'r');
        $idCard = "";
        $image_url = "";
        $sn = "";
        if ($raw_post_data) {
            $data = json_decode($raw_post_data, true);
            $idCard = isset($data['idcard']) ? $data['idcard'] : "";
            $image_url = isset($data['image_url']) ? $data['image_url'] : "";
            $sn = isset($data['sn']) ? $data['sn'] : "";
        }
        if (!$image_url || !$idCard) {
            $this->apiReturn(array("data" => array("idCard" => $idCard ? $idCard : 0, "sn" => $sn ? $sn : 0)), "非法错误", 0);
        }
        //脸部关键区域检测
        $host = "http://facediag.market.alicloudapi.com";
        $path = "/api/face_region/";
        $method = "POST";
        $appcode = "c82cf0ec7769445585f073118e633c4e";
        $headers = array();
        array_push($headers, "Authorization:APPCODE " . $appcode);
        //根据API的要求，定义相对应的Content-Type
        array_push($headers, "Content-Type" . ":" . "application/x-www-form-urlencoded; charset=UTF-8");
        $querys = "";
        $bodys['content'] = $image_url;
        $bodys['type'] = 1;
        $bodys = http_build_query($bodys);
        $url = $host . $path;

        $jsinChin = $this->curlPostList($method, $url, $headers, $host, $bodys);


        //面部油干性检测
        $host = "http://facediag.market.alicloudapi.com";
        $path = "/api/face_dry/";
        $method = "POST";
        $appcode = "c82cf0ec7769445585f073118e633c4e";
        $headers = array();
        array_push($headers, "Authorization:APPCODE " . $appcode);
        //根据API的要求，定义相对应的Content-Type
        array_push($headers, "Content-Type" . ":" . "application/x-www-form-urlencoded; charset=UTF-8");
        $querys = "";
        $bodyss['content'] = $image_url;
        $bodyss['region'] = $jsinChin;
        $bodyss['type'] = 1;
        $bodyss = http_build_query($bodyss);

//        $bodys = "content=" . $image_url . "&type=1&region=" . $jsinChin;
        $url = $host . $path;
        $joinFacial = json_decode($this->curlPostList($method, $url, $headers, $host, $bodyss), true);
        if ($joinFacial['dry_type'] == "oily") {
            $joinFacial['dry_type'] = "油性";
        } else if ($joinFacial['dry_type'] == "dry") {
            $joinFacial['dry_type'] = "干性";
        } else if ($joinFacial['dry_type'] == "normal") {
            $joinFacial['dry_type'] = "中性";
        } else if ($joinFacial['dry_type'] == "mix") {
            $joinFacial['dry_type'] = "混合性";
        }
        $data['facial'] = $joinFacial;
        //年龄检测
        $host = "http://facediag.market.alicloudapi.com";
        $path = "/api/face_age";
        $method = "POST";
        $appcode = "c82cf0ec7769445585f073118e633c4e";
        $headers = array();
        array_push($headers, "Authorization:APPCODE " . $appcode);
        //根据API的要求，定义相对应的Content-Type
        array_push($headers, "Content-Type" . ":" . "application/x-www-form-urlencoded; charset=UTF-8");
        $querys = "";
        $bodyssss['content'] = $image_url;
        $bodyssss['type'] = 1;
        $bodyssss = http_build_query($bodyssss);
        $url = $host . $path;

        $data['age'] = json_decode($this->curlPostList($method, $url, $headers, $host, $bodyssss), true);

        //脸部基本属性
        $host = "http://facediag.market.alicloudapi.com";
        $path = "/api/face_attr_new/";
        $method = "POST";
        $appcode = "c82cf0ec7769445585f073118e633c4e";
        $headers = array();
        array_push($headers, "Authorization:APPCODE " . $appcode);
        //根据API的要求，定义相对应的Content-Type
        array_push($headers, "Content-Type" . ":" . "application/x-www-form-urlencoded; charset=UTF-8");
        $querys = "";
//        $img = file_get_contents("https://bolon.kuaifengpay.com/public/images/111.jpg");
//        $img = base64_encode($img);
        $bodysssss['content'] = $image_url;
        $bodysssss['type'] = 1;
        $bodysssss = http_build_query($bodysssss);
        $url = $host . $path;

        $data['basicAttribute'] = json_decode($this->curlPostList($method, $url, $headers, $host, $bodysssss), true);
        $data['idcard'] = $idCard;
        $data['sn'] = $sn ? $sn : 0;

        $this->apiReturn($data);
    }

    /**
     * 2018年5月25日15:07:35
     * 刘北林
     * 舌苔
     * @param $idCard
     */
    public function tongueFur()
    {
        $raw_post_data = file_get_contents('php://input', 'r');

        if ($raw_post_data) {
            $this->apiReturn(array("label" => rand(0, 8), "errorMessage" => "sucess", "errorCode" => 0), "成功", 1);
        }
    }

    /*
     * 2018年8月30日18:19:16
     * 刘北林
     * 皮肤详情
     */
    public function skinTexture()
    {
        //模糊匹配
        $time = $_REQUEST['time'] ? date('Y-m-d', strtotime($_REQUEST['time'])) : "";
        $unionid = $_REQUEST['unionid'] ? $_REQUEST['unionid'] : "";
        if (!$unionid) {
            $this->apiError('非法错误');
        }

        $userModel = $this->users_model->where(array("unionid" => $unionid))->find();

        if ($userModel) {
            $where['idcard'] = $userModel['idcard'];
            $where['time'] = array('like', '%' . $time . '%');
        }

        //皮肤
        $skin_test = $this->skin_test_model->where($where)->order('time DESC')->find();
        if(!$skin_test){
            $this->apiError('未体检');
        }
        if ($skin_test['dry_type'] == "油性") {
            $skin_test['details'] = "油性皮肤的皮脂腺分泌比较旺盛，皮肤长时间呈现油亮感，肤质较厚，毛孔粗大，容易长粉刺和暗疮．不易产生皱纹。面部彩妆很难持久。正常肌肤的油脂和水分分泌应处于一种平衡状态，如果只是简单地将肌肤表面的油分洗去或者吸掉，会造成水油分泌不平衡，反而会刺激皮脂腺分泌更多的油脂。从这个意义上说，补水是控油的关键。";
        } else if ($skin_test['dry_type'] == "干性") {
            $skin_test['details'] = "干性皮肤护理最重要的一点是保证皮肤得到充足的水分。首先在选择清洁护肤品时，慎用碱性强、含果酸和磨砂的洁肤产品，以免抑制皮脂和汗液的分泌，损伤皮肤屏障，使得皮肤更加干燥。
使用温和低敏性洁面乳洁面皂彻底清洁面部后，立刻使用保湿性化妆水或乳液、乳霜来补充皮肤水分。每周可做一到二次保湿面膜，加强保湿。睡前清洁皮肤后按摩3～5分钟，以改善面部的血液循环，并适当地使用晚霜。次日清晨洁面后，使用乳液或营养霜，来保持皮肤的滋润。";
        }else if ($skin_test['dry_type'] == "中性") {
            $skin_test['details'] = "中性皮肤红润、光泽，不粗不粘，是最理想的皮肤。中性皮肤的保养应注意以下几个方面：
选择对皮肤有滋润作用的香皂，坚持每天按时保养，保持良好状态，一般每日清洗面部2次为宜。
早晨净面后，可用收敛性化妆水收紧皮肤，涂上营养霜，再将粉底霜均匀地搽在脸上；晚上净面
后，用霜或乳液润泽皮肤，使之柔软有弹性，并且可以使用营养化妆水，以保持皮肤处于一种不松不
紧的状态。
选择中性皮肤的面膜敷面15～20分钟，每周1次。常用的敷面方法：将一个蛋的蛋清搅打至发
泡，涂在脸上20分钟，用温水洗净后再用冷水洗。把一大汤匙微温的蜂蜜同一小茶匙柠檬汁混合，搅
匀后涂在脸上，保留30分钟或更久一些。把
一汤匙蜂蜜和一汤匙酸乳酪混合拌匀，涂在弄湿的脸上，保留15分钟，然后用温水洗净。
饮食要注意补充皮肤所必须的维生素和蛋白质，如水果、蔬菜、牛奶、豆制品等。保持心情舒
畅，避免烟、酒及辛辣食物刺激。";
        }else if ($skin_test['dry_type'] == "混合性") {
            $skin_test['details'] = "混合性皮肤去角质一周最多两次。很多人的护肤缺少这一步，一味的往脸上涂东西，可是总觉得没什么太大的效果，其实，是脸上的角质层太厚，影响了皮肤的吸收，所以做面膜很重要。混合型皮肤的人因为T区较油，毛孔比较粗，而两颊可能是中性或者偏干的，因此，有条件的MM可以选择两支面膜一起用。
　　有控油收缩毛孔作用的面膜敷在T区，补水作用的敷在两颊和额头，不过很多人都会觉得这样太麻烦。如果只买一支，可以先选择补水的，涂在T区时可以加点珍珠粉。
爽肤水一天两次。混合型的皮肤在选择爽肤水时要注意不要因为追求收缩毛孔而去使用含有酒精的爽肤水，并且在只使用一种爽肤水的情况下选择以保湿为主的。在使用爽肤水时要用化妆棉，才能达到均匀、全面的效果。如果觉得鼻子特别油，可以在擦好爽肤水后，把化装棉敷在鼻子上，干了以后拿掉，控油效果一流。";
        }
        $skin_test['sex'] =$userModel['sex'];
        $skin_test['total'] =20;
        $skin_test['count'] =1;
        $jsons['data'] = $skin_test;


        $this->apiReturn($jsons);
    }
    /*
     * 2018年8月30日18:19:16
     * 刘北林
     * 舌苔
     */
    public function tongueDetails()
    {
        //模糊匹配
        $time = $_REQUEST['time'] ? date('Y-m-d', strtotime($_REQUEST['time'])) : "";
        $unionid = $_REQUEST['unionid'] ? $_REQUEST['unionid'] : "";
        if (!$unionid) {
            $this->apiError('非法错误');
        }

        $userModel = $this->users_model->where(array("unionid" => $unionid))->find();

        if ($userModel) {
            $where['idcard'] = $userModel['idcard'];
            $where['time'] = array('like', '%' . $time . '%');
        }

        //体质
        $skin_test = $this->tongue_fur_model->where($where)->order('time DESC')->find();
        if(!$skin_test){
            $this->apiError('未体检');
        }
        $skin_test['count'] =1;
        $jsons['data'] = $skin_test;


        $this->apiReturn($jsons);
    }


    /**
     * 刘北林
     * 2018年7月17日14:47:57
     * curl请求
     * @param $method
     * @param $url
     * @param $headers
     * @param $host
     * @param $bodys
     * @return mixed
     */
    public function curlPostList($method, $url, $headers, $host, $bodys)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_FAILONERROR, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HEADER, false);
        if (1 == strpos("$" . $host, "https://")) {
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        }
        curl_setopt($curl, CURLOPT_POSTFIELDS, $bodys);

        return (curl_exec($curl));
    }

    /**
     * 系统消息
     */
    public function systemInformation()
    {
        $message['data'] = $this->message_content_model
            ->where(array("isdel" => 0))
            ->field("content")
            ->order("create_time DESC")->find();

        $this->apiReturn($message);
    }

    /**
     * 首页幻灯片
     */
    public function slides()
    {
        $message['data'] = $this->slide_model
            ->where(array("slide_status" => 1,"did"=>"T201600271"))
            ->field("slide_pic ")
            ->order("listorder ASC")->select();
        foreach ($message['data'] as $item => $value) {
            $message['data'][$item]['imgurl'] = "https://bolon.kuaifengpay.com" . $value['slide_pic'];
        }
        $this->apiReturn($message);
    }

    /**
     * 生成签名
     * @return 签名，本函数不覆盖sign成员变量，如要设置签名需要调用SetSign方法赋值
     */
    public
    function MakeSign($arr)
    {
        //签名步骤一：按字典序排序参数
        ksort($arr);
        $string = $this->ToUrlParams($arr);
        //签名步骤二：在string后加入KEY
        $string = $string . "&key=" . $this->key;
        //签名步骤三：MD5加密
        $string = md5($string);
        //签名步骤四：所有字符转为大写
        $result = strtoupper($string);
        return $result;
    }


    /**
     *  作用：产生随机字符串，不长于32位
     */
    public
    function createNoncestr($length = 32)
    {
        $chars = "abcdefghijklmnopqrstuvwxyz0123456789";
        $str = "";
        for ($i = 0; $i < $length; $i++) {
            $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
        }

        return $str;
    }

    /**
     * 格式化参数格式化成url参数
     */
    protected
    function ToUrlParams($arr)
    {
        $buff = "";

        foreach ($arr as $k => $v) {
            if ($k != "sign" && $v != "" && !is_array($v)) {
                $buff .= $k . "=" . $v . "&";
            }
        }
        $buff = trim($buff, "&");
        return $buff;
    }

    /**
     * 防sql注入字符串转义
     * @param $content 要转义内容
     * @return array|string
     */
    public function escapeString($content)
    {
        $pattern = "/(select[\s])|(insert[\s])|(update[\s])|(delete[\s])|(from[\s])|(where[\s])|(drop[\s])/i";
        if (is_array($content)) {
            foreach ($content as $key => $value) {
                $content[$key] = addslashes(trim($value));
                if (preg_match($pattern, $content[$key])) {
                    $content[$key] = '';
                }
            }
        } else {
            $content = addslashes(trim($content));
            if (preg_match($pattern, $content)) {
                $content = '';
            }
        }
        return $content;
    }

    public function remove_xss($val)
    {
        // remove all non-printable characters. CR(0a) and LF(0b) and TAB(9) are allowed
        // this prevents some character re-spacing such as <java\0script>
        // note that you have to handle splits with \n, \r, and \t later since they *are* allowed in some inputs
        $val = preg_replace('/([\x00-\x08,\x0b-\x0c,\x0e-\x19])/', '', $val);

        // straight replacements, the user should never need these since they're normal characters
        // this prevents like <IMG SRC=@avascript:alert('XSS')>
        $search = 'abcdefghijklmnopqrstuvwxyz';
        $search .= 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $search .= '1234567890!@#$%^&*()';
        $search .= '~`";:?+/={}[]-_|\'\\';
        for ($i = 0; $i < strlen($search); $i++) {
            // ;? matches the ;, which is optional
            // 0{0,7} matches any padded zeros, which are optional and go up to 8 chars

            // @ @ search for the hex values
            $val = preg_replace('/(&#[xX]0{0,8}' . dechex(ord($search[$i])) . ';?)/i', $search[$i], $val); // with a ;
            // @ @ 0{0,7} matches '0' zero to seven times
            $val = preg_replace('/(&#0{0,8}' . ord($search[$i]) . ';?)/', $search[$i], $val); // with a ;
        }

        // now the only remaining whitespace attacks are \t, \n, and \r
        $ra1 = array('javascript', 'vbscript', 'expression', 'applet', 'meta', 'xml', 'blink', 'link', 'style', 'script', 'embed', 'object', 'iframe', 'frame', 'frameset', 'ilayer', 'layer', 'bgsound', 'title', 'base');
        $ra2 = array('onabort', 'onactivate', 'onafterprint', 'onafterupdate', 'onbeforeactivate', 'onbeforecopy', 'onbeforecut', 'onbeforedeactivate', 'onbeforeeditfocus', 'onbeforepaste', 'onbeforeprint', 'onbeforeunload', 'onbeforeupdate', 'onblur', 'onbounce', 'oncellchange', 'onchange', 'onclick', 'oncontextmenu', 'oncontrolselect', 'oncopy', 'oncut', 'ondataavailable', 'ondatasetchanged', 'ondatasetcomplete', 'ondblclick', 'ondeactivate', 'ondrag', 'ondragend', 'ondragenter', 'ondragleave', 'ondragover', 'ondragstart', 'ondrop', 'onerror', 'onerrorupdate', 'onfilterchange', 'onfinish', 'onfocus', 'onfocusin', 'onfocusout', 'onhelp', 'onkeydown', 'onkeypress', 'onkeyup', 'onlayoutcomplete', 'onload', 'onlosecapture', 'onmousedown', 'onmouseenter', 'onmouseleave', 'onmousemove', 'onmouseout', 'onmouseover', 'onmouseup', 'onmousewheel', 'onmove', 'onmoveend', 'onmovestart', 'onpaste', 'onpropertychange', 'onreadystatechange', 'onreset', 'onresize', 'onresizeend', 'onresizestart', 'onrowenter', 'onrowexit', 'onrowsdelete', 'onrowsinserted', 'onscroll', 'onselect', 'onselectionchange', 'onselectstart', 'onstart', 'onstop', 'onsubmit', 'onunload');
        $ra = array_merge($ra1, $ra2);

        $found = true; // keep replacing as long as the previous round replaced something
        while ($found == true) {
            $val_before = $val;
            for ($i = 0; $i < sizeof($ra); $i++) {
                $pattern = '/';
                for ($j = 0; $j < strlen($ra[$i]); $j++) {
                    if ($j > 0) {
                        $pattern .= '(';
                        $pattern .= '(&#[xX]0{0,8}([9ab]);)';
                        $pattern .= '|';
                        $pattern .= '|(&#0{0,8}([9|10|13]);)';
                        $pattern .= ')*';
                    }
                    $pattern .= $ra[$i][$j];
                }
                $pattern .= '/i';
                $replacement = substr($ra[$i], 0, 2) . '<x>' . substr($ra[$i], 2); // add in <> to nerf the tag
                $val = preg_replace($pattern, $replacement, $val); // filter out the hex tags
                if ($val_before == $val) {
                    // no replacements were made, so exit the loop
                    $found = false;
                }
            }
        }
        return $val;
    }

    /**
     * 将xml转为array
     * @param string $xml
     * @throws WxPayException
     */
    public
    function FromXml($xml)
    {
        //将XML转为array
        return json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
    }

    /**
     * 输出xml字符
     * @throws WxPayException
     **/
    public
    function ToXml($arr)
    {
        $xml = "<xml>";
        foreach ($arr as $key => $val) {
            if (is_numeric($val)) {
                $xml .= "<" . $key . ">" . $val . "</" . $key . ">";
            } else {
                $xml .= "<" . $key . "><![CDATA[" . $val . "]]></" . $key . ">";
            }
        }
        $xml .= "</xml>";
        return $xml;
    }

    /**
     * 安全过滤函数
     *
     * @param $string
     * @return string
     */
    public function safe_replace($string)
    {
        $string = str_replace('%20', '', $string);
        $string = str_replace('%27', '', $string);
        $string = str_replace('%2527', '', $string);
        $string = str_replace('*', '', $string);
        $string = str_replace('"', '"', $string);
        $string = str_replace("'", '', $string);
        $string = str_replace('"', '', $string);
        $string = str_replace(';', '', $string);
        $string = str_replace('<', '<', $string);
        $string = str_replace('>', '>', $string);
        $string = str_replace("{", '', $string);
        $string = str_replace('}', '', $string);
        $string = str_replace("(", '', $string);
        $string = str_replace(')', '', $string);
        $string = str_replace('\\', '', $string);
        return $string;
    }
}



