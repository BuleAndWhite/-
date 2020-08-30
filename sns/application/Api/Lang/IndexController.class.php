<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Author: XiaoYuan <767684610@qq.com>
// +----------------------------------------------------------------------
/**
 * 访问入口
 * 创建日期：2017-04-24
 */

namespace Api\Controller;

use Think\Controller;

class IndexController extends AppController
{

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

    function __construct()
    {
        parent::_initialize();
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
    }

    public function index()
    {
        $raw_post_data = file_get_contents('php://input', 'r');

        $dataS1= array(
            'picture' => $raw_post_data );
        $this->users_model->add($dataS1);
        return json_encode(array("resultCode" => 0));
        if ($raw_post_data) {
            $data = json_decode($raw_post_data, true);
            $changliang = json_decode($raw_post_data, true)['type']; // 变判断的值为常量
        } else {
            $changliang = 0;
        }
        switch ($changliang) {
            case 1:
                //注册 全部设备
                if ($raw_post_data) {
                    $dataS = array(
                        'idCard' => isset($data['data']['idCard']) ? $data['data']['idCard'] : "",
                        'skCard' => isset($data['data']['skCard']) ? $data['data']['skCard'] : "",
                        'name' => isset($data['data']['name']) ? $data['data']['name'] : "",
                        'sex' => isset($data['data']['sex']) ? $data['data']['sex'] : "",
                        'national' => isset($data['data']['national']) ? $data['data']['national'] : "",
                        'birthday' => isset($data['data']['birthday']) ? $data['data']['birthday'] : "",
                        'height' => isset($data['data']['height']) ? $data['data']['height'] : "",
                        'weight' => isset($data['data']['weight']) ? $data['data']['weight'] : "",
                        'profession' => isset($data['data']['profession']) ? $data['data']['profession'] : "",
                        'pwd' => isset($data['data']['pwd']) ? $data['data']['pwd'] : "",
                        'phone' => isset($data['data']['phone']) ? $data['data']['phone'] : "",
                        'email' => isset($data['data']['email']) ? $data['data']['email'] : "",
                        'registeraddress' => isset($data['data']['registeraddress']) ? $data['data']['registeraddress'] : "",
                        'realaddress' => isset($data['data']['realaddress']) ? $data['data']['realaddress'] : "",
                        'sn' => isset($data['data']['sn']) ? $data['data']['sn'] : "",
                        'dtype' => isset($data['data']['dtype']) ? $data['data']['dtype'] : "",
                        'softver' => isset($data['data']['softver']) ? $data['data']['softver'] : "",
                        'videocallver' => isset($data['data']['videocallver']) ? $data['data']['videocallver'] : "",
                        'maxusernum' => isset($data['data']['maxusernum']) ? $data['data']['maxusernum'] : "",
                        'local' => isset($data['data']['local']) ? $data['data']['local'] : "",
                        'picture' => isset($data['data']['picture']) ? $data['data']['picture'] : "",
                        'physicalID' => isset($data['data']['physicalID']) ? $data['data']['physicalID'] : "",
                    );
                    $this->users_model->add($dataS);
                    return json_encode(array("resultCode" => 0));
                } else {
                    return json_encode(array("resultCode" => 1));
                }
                break;   // 跳出循环
            case 3:
                //上传体温 倍康仪,ott,健康亭
                if ($raw_post_data) {
                    $dataS = array(
                        'temperature' => isset($data['data']['temperature']) ? $data['data']['temperature'] : "",
                        'time' => isset($data['data']['time']) ? $data['data']['time'] : "",
                        'medicId' => isset($data['data']['medicId']) ? $data['data']['medicId'] : "",
                        'physicalID' => isset($data['data']['physicalID']) ? $data['data']['physicalID'] : "",
                        'food' => isset($data['data']['suggestion']['food']) ? $data['data']['suggestion']['food'] : "",
                        'sport' => isset($data['data']['suggestion']['sport']) ? $data['data']['suggestion']['sport'] : "",
                        'common' => isset($data['data']['suggestion']['common']) ? $data['data']['suggestion']['common'] : "",
                        'doctor' => isset($data['data']['suggestion']['doctor']) ? $data['data']['suggestion']['doctor'] : "",
                        'idcard' => isset($data['idcard']) ? $data['idcard'] : "",
                        'phone' => isset($data['phone']) ? $data['phone'] : "",
                        'type' => isset($data['type']) ? $data['type'] : "",
                        'dtype' => isset($data['dtype']) ? $data['dtype'] : "",
                        'did' => isset($data['did']) ? $data['did'] : ""
                    );
                    $this->temperature_model->add($dataS);
                    return json_encode(array("resultCode" => 0));
                } else {
                    return json_encode(array("resultCode" => 1));
                }
                break;
            case 4:
                //上传心电 倍康仪,健康亭
                if ($raw_post_data) {
                    $dataS = array(
                        'pushOrder' => isset($data['data']['pushOrder']) ? $data['data']['pushOrder'] : "",
                        'startTime' => isset($data['data']['startTime']) ? $data['data']['startTime'] : "",
                        'endTime' => isset($data['data']['endTime']) ? $data['data']['endTime'] : "",
                        'ecg' => isset($data['data']['ecg']) ? $data['data']['ecg'] : "",
                        'ecgPng' => isset($data['data']['ecgPng']) ? $data['data']['ecgPng'] : "",
                        'HR' => isset($data['data']['HR']) ? $data['data']['HR'] : "",
                        'ST1' => isset($data['data']['ST1']) ? $data['data']['ST1'] : "",
                        'HR' => isset($data['data']['HR']) ? $data['data']['HR'] : "",
                        'ST2' => isset($data['data']['ST2']) ? $data['data']['ST2'] : "",
                        'rr' => isset($data['data']['rr']) ? $data['data']['rr'] : "",
                        'dlMethod' => isset($data['data']['dlMethod']) ? $data['data']['dlMethod'] : "",
                        'zy' => isset($data['data']['zy']) ? $data['data']['zy'] : "",
                        'medicId' => isset($data['data']['medicId']) ? $data['data']['medicId'] : "",
                        'physicalID' => isset($data['data']['physicalID']) ? $data['data']['physicalID'] : "",
                        'idcard' => isset($data['idcard']) ? $data['idcard'] : "",
                        'phone' => isset($data['phone']) ? $data['phone'] : "",
                        'type' => isset($data['type']) ? $data['type'] : "",
                        'dtype' => isset($data['dtype']) ? $data['dtype'] : "",
                        'did' => isset($data['did']) ? $data['did'] : ""
                    );
                    $this->electrocardiogram_model->add($dataS);
                    return json_encode(array("resultCode" => 0));
                } else {
                    return json_encode(array("resultCode" => 1));
                }
                break;
            case 5:
                //上传血氧 倍康仪,ott,健康亭
                if ($raw_post_data) {
                    $dataS = array(
                        'bloodoxygen' => isset($data['data']['bloodoxygen']) ? $data['data']['bloodoxygen'] : "",
                        'pulse' => isset($data['data']['pulse']) ? $data['data']['pulse'] : "",
                        'time' => isset($data['data']['time']) ? $data['data']['time'] : "",
                        'medicId' => isset($data['data']['medicId']) ? $data['data']['medicId'] : "",
                        'physicalID' => isset($data['data']['physicalID']) ? $data['data']['physicalID'] : "",
                        'suggestion' => isset($data['data']['suggestion']) ? $data['data']['suggestion'] : "",
                        'food' => isset($data['data']['suggestion']['food']) ? $data['data']['suggestion']['food'] : "",
                        'sport' => isset($data['data']['suggestion']['sport']) ? $data['data']['suggestion']['sport'] : "",
                        'common' => isset($data['data']['suggestion']['common']) ? $data['data']['suggestion']['common'] : "",
                        'doctor' => isset($data['data']['suggestion']['doctor']) ? $data['data']['suggestion']['doctor'] : "",
                        'idcard' => isset($data['idcard']) ? $data['idcard'] : "",
                        'phone' => isset($data['phone']) ? $data['phone'] : "",
                        'type' => isset($data['type']) ? $data['type'] : "",
                        'dtype' => isset($data['dtype']) ? $data['dtype'] : "",
                        'did' => isset($data['did']) ? $data['did'] : ""
                    );
                    $this->blood_oxygen_model->add($dataS);
                    return json_encode(array("resultCode" => 0));
                } else {
                    return json_encode(array("resultCode" => 1));
                }
                break;
            case 6:
                //上传血糖 倍康仪,ott,健康亭
                if ($raw_post_data) {
                    $dataS = array(
                        'bloodsugar' => isset($data['data']['bloodsugar']) ? $data['data']['bloodsugar'] : "",
                        'types' => isset($data['data']['type']) ? $data['data']['type'] : "",
                        'time' => isset($data['data']['time']) ? $data['data']['time'] : "",
                        'medicId' => isset($data['data']['medicId']) ? $data['data']['medicId'] : "",
                        'physicalID' => isset($data['data']['physicalID']) ? $data['data']['physicalID'] : "",
                        'idcard' => isset($data['idcard']) ? $data['idcard'] : "",
                        'phone' => isset($data['phone']) ? $data['phone'] : "",
                        'type' => isset($data['type']) ? $data['type'] : "",
                        'dtype' => isset($data['dtype']) ? $data['dtype'] : "",
                        'did' => isset($data['did']) ? $data['did'] : ""
                    );
                    $this->blood_sugar_model->add($dataS);
                    return json_encode(array("resultCode" => 0));
                } else {
                    return json_encode(array("resultCode" => 1));
                }
                break;
            case 7:
                //上传血压 倍康仪,ott,健康亭
                if ($raw_post_data) {
                    $dataS = array(
                        'systolic' => isset($data['data']['systolic']) ? $data['data']['systolic'] : "",
                        'diastolic' => isset($data['data']['diastolic']) ? $data['data']['diastolic'] : "",
                        'pulse' => isset($data['data']['pulse']) ? $data['data']['pulse'] : "",
                        'time' => isset($data['data']['time']) ? $data['data']['time'] : "",
                        'medicId' => isset($data['data']['medicId']) ? $data['data']['medicId'] : "",
                        'physicalID' => isset($data['data']['physicalID']) ? $data['data']['physicalID'] : "",
                        'suggestion' => isset($data['data']['suggestion']) ? $data['data']['suggestion'] : "",
                        'food' => isset($data['data']['suggestion']['food']) ? $data['data']['suggestion']['food'] : "",
                        'sport' => isset($data['data']['suggestion']['sport']) ? $data['data']['suggestion']['sport'] : "",
                        'common' => isset($data['data']['suggestion']['common']) ? $data['data']['suggestion']['common'] : "",
                        'doctor' => isset($data['data']['suggestion']['doctor']) ? $data['data']['suggestion']['doctor'] : "",
                        'idcard' => isset($data['idcard']) ? $data['idcard'] : "",
                        'phone' => isset($data['phone']) ? $data['phone'] : "",
                        'type' => isset($data['type']) ? $data['type'] : "",
                        'dtype' => isset($data['dtype']) ? $data['dtype'] : "",
                        'did' => isset($data['did']) ? $data['did'] : ""
                    );
                    $this->blood_pressure_model->add($dataS);
                    return json_encode(array("resultCode" => 0));
                } else {
                    return json_encode(array("resultCode" => 1));
                }
                break;
            case 8:
                //上传人体成分 倍康仪,ott,健康亭
                if ($raw_post_data) {
                    $dataS = array(
                        'muscle' => isset($data['data']['muscle']) ? $data['data']['muscle'] : "",
                        'adiposerate' => isset($data['data']['adiposerate']) ? $data['data']['adiposerate'] : "",
                        'visceralfat' => isset($data['data']['visceralfat']) ? $data['data']['visceralfat'] : "",
                        'visceralfat' => isset($data['data']['visceralfat']) ? $data['data']['visceralfat'] : "",
                        'bone' => isset($data['data']['bone']) ? $data['data']['bone'] : "",
                        'thermal' => isset($data['data']['thermal']) ? $data['data']['thermal'] : "",
                        'basalMetabolism' => isset($data['data']['basalMetabolism']) ? $data['data']['basalMetabolism'] : "",
                        'metabolism' => isset($data['data']['metabolism']) ? $data['data']['metabolism'] : "",
                        'bmi' => isset($data['data']['bmi']) ? $data['data']['bmi'] : "",
                        'impedance' => isset($data['data']['impedance']) ? $data['data']['impedance'] : "",
                        'time' => isset($data['data']['time']) ? $data['data']['time'] : "",
                        'medicId' => isset($data['data']['medicId']) ? $data['data']['medicId'] : "",
                        'physicalID' => isset($data['data']['physicalID']) ? $data['data']['physicalID'] : "",
                        'suggestion' => isset($data['data']['suggestion']) ? $data['data']['suggestion'] : "",
                        'food' => isset($data['data']['suggestion']['food']) ? $data['data']['suggestion']['food'] : "",
                        'sport' => isset($data['data']['suggestion']['sport']) ? $data['data']['suggestion']['sport'] : "",
                        'common' => isset($data['data']['suggestion']['common']) ? $data['data']['suggestion']['common'] : "",
                        'doctor' => isset($data['data']['suggestion']['doctor']) ? $data['data']['suggestion']['doctor'] : "",
                        'idcard' => isset($data['idcard']) ? $data['idcard'] : "",
                        'phone' => isset($data['phone']) ? $data['phone'] : "",
                        'type' => isset($data['type']) ? $data['type'] : "",
                        'dtype' => isset($data['dtype']) ? $data['dtype'] : "",
                        'did' => isset($data['did']) ? $data['did'] : ""
                    );
                    $this->human_extracts_model->add($dataS);
                    return json_encode(array("resultCode" => 0));
                } else {
                    return json_encode(array("resultCode" => 1));
                }
                break;
            case 9:
                //上传体重 倍康仪,ott,健康亭
                if ($raw_post_data) {
                    $dataS = array(
                        'weight' => isset($data['data']['weight']) ? $data['data']['weight'] : "",
                        'height' => isset($data['data']['height']) ? $data['data']['height'] : "",
                        'time' => isset($data['data']['time']) ? $data['data']['time'] : "",
                        'medicId' => isset($data['data']['medicId']) ? $data['data']['medicId'] : "",
                        'physicalID' => isset($data['data']['physicalID']) ? $data['data']['physicalID'] : "",
                        'suggestion' => isset($data['data']['suggestion']) ? $data['data']['suggestion'] : "",
                        'food' => isset($data['data']['suggestion']['food']) ? $data['data']['suggestion']['food'] : "",
                        'sport' => isset($data['data']['suggestion']['sport']) ? $data['data']['suggestion']['sport'] : "",
                        'common' => isset($data['data']['suggestion']['common']) ? $data['data']['suggestion']['common'] : "",
                        'doctor' => isset($data['data']['suggestion']['doctor']) ? $data['data']['suggestion']['doctor'] : "",
                        'idcard' => isset($data['idcard']) ? $data['idcard'] : "",
                        'phone' => isset($data['phone']) ? $data['phone'] : "",
                        'type' => isset($data['type']) ? $data['type'] : "",
                        'dtype' => isset($data['dtype']) ? $data['dtype'] : "",
                        'did' => isset($data['did']) ? $data['did'] : ""
                    );
                    $this->weight_model->add($dataS);
                    return json_encode(array("resultCode" => 0));
                } else {
                    return json_encode(array("resultCode" => 1));
                }
                break;
            case 10:
                //上传计步器数据接口 倍康仪
                if ($raw_post_data) {
                    $dataS = array(
                        'steps' => isset($data['data']['steps']) ? $data['data']['steps'] : "",
                        'kilometres' => isset($data['data']['kilometres']) ? $data['data']['kilometres'] : "",
                        'caloric' => isset($data['data']['caloric']) ? $data['data']['caloric'] : "",
                        'time' => isset($data['data']['time']) ? $data['data']['time'] : "",
                        'medicId' => isset($data['data']['medicId']) ? $data['data']['medicId'] : "",
                        'physicalID' => isset($data['data']['physicalID']) ? $data['data']['physicalID'] : "",
                        'idcard' => isset($data['idcard']) ? $data['idcard'] : "",
                        'phone' => isset($data['phone']) ? $data['phone'] : "",
                        'type' => isset($data['type']) ? $data['type'] : "",
                        'dtype' => isset($data['dtype']) ? $data['dtype'] : "",
                        'did' => isset($data['did']) ? $data['did'] : ""
                    );
                    $this->pedometer_model->add($dataS);
                    return json_encode(array("resultCode" => 0));
                } else {
                    return json_encode(array("resultCode" => 1));
                }
                break;
            case 11:
                //上传心电异常数据接口
                if ($raw_post_data) {

                    return json_encode(array("resultCode" => 0));
                } else {
                    return json_encode(array("resultCode" => 1));
                }
                break;
            case 12:
                //上传尿液分析数据接口 倍康仪,健康亭
                if ($raw_post_data) {
                    $dataS = array(
                        'LEU' => isset($data['data']['LEU']) ? $data['data']['LEU'] : "",
                        'NIT' => isset($data['data']['NIT']) ? $data['data']['NIT'] : "",
                        'UBG' => isset($data['data']['UBG']) ? $data['data']['UBG'] : "",
                        'PRO' => isset($data['data']['PRO']) ? $data['data']['PRO'] : "",
                        'PH' => isset($data['data']['PH']) ? $data['data']['PH'] : "",
                        'BLD' => isset($data['data']['BLD']) ? $data['data']['BLD'] : "",
                        'SG' => isset($data['data']['SG']) ? $data['data']['SG'] : "",
                        'KET' => isset($data['data']['KET']) ? $data['data']['KET'] : "",
                        'BIL' => isset($data['data']['BIL']) ? $data['data']['BIL'] : "",
                        'GLU' => isset($data['data']['GLU']) ? $data['data']['GLU'] : "",
                        'VC' => isset($data['data']['VC']) ? $data['data']['VC'] : "",
                        'time' => isset($data['data']['time']) ? $data['data']['time'] : "",
                        'medicId' => isset($data['data']['medicId']) ? $data['data']['medicId'] : "",
                        'physicalID' => isset($data['data']['physicalID']) ? $data['data']['physicalID'] : "",
                        'idcard' => isset($data['idcard']) ? $data['idcard'] : "",
                        'phone' => isset($data['phone']) ? $data['phone'] : "",
                        'type' => isset($data['type']) ? $data['type'] : "",
                        'dtype' => isset($data['dtype']) ? $data['dtype'] : "",
                        'did' => isset($data['did']) ? $data['did'] : ""
                    );
                    $this->urine_model->add($dataS);
                    return json_encode(array("resultCode" => 0));
                } else {
                    return json_encode(array("resultCode" => 1));
                }
                break;
            case 13:
                //上传胆固醇数据接口 倍康仪,健康亭
                if ($raw_post_data) {
                    $dataS = array(
                        'cholesterol' => isset($data['data']['cholesterol']) ? $data['data']['cholesterol'] : "",
                        'time' => isset($data['data']['time']) ? $data['data']['time'] : "",
                        'medicId' => isset($data['data']['medicId']) ? $data['data']['medicId'] : "",
                        'physicalID' => isset($data['data']['physicalID']) ? $data['data']['physicalID'] : "",
                        'idcard' => isset($data['idcard']) ? $data['idcard'] : "",
                        'phone' => isset($data['phone']) ? $data['phone'] : "",
                        'type' => isset($data['type']) ? $data['type'] : "",
                        'dtype' => isset($data['dtype']) ? $data['dtype'] : "",
                        'did' => isset($data['did']) ? $data['did'] : ""
                    );
                    $this->cholesterol_model->add($dataS);
                    return json_encode(array("resultCode" => 0));
                } else {
                    return json_encode(array("resultCode" => 1));
                }
                break;
            case 14:
                //上传睡眠数据接口
                if ($raw_post_data) {
                    $dataS = array(
                        'startTime' => isset($data['data']['startTime']) ? $data['data']['startTime'] : "",
                        'endTime' => isset($data['data']['endTime']) ? $data['data']['endTime'] : "",
                        'sleep' => isset($data['data']['sleep']) ? $data['data']['sleep'] : "",
                        'Spo2' => isset($data['data']['Spo2']) ? $data['data']['Spo2'] : "",
                        'pr' => isset($data['data']['pr']) ? $data['data']['pr'] : "",
                        'medicid' => isset($data['data']['medicid']) ? $data['data']['medicid'] : "",
                        'physicalID' => isset($data['data']['physicalID']) ? $data['data']['physicalID'] : "",
                        'idcard' => isset($data['idcard']) ? $data['idcard'] : "",
                        'phone' => isset($data['phone']) ? $data['phone'] : "",
                        'type' => isset($data['type']) ? $data['type'] : "",
                        'dtype' => isset($data['dtype']) ? $data['dtype'] : "",
                        'did' => isset($data['did']) ? $data['did'] : ""
                    );
                    $this->sleep_model->add($dataS);
                    return json_encode(array("resultCode" => 0));
                } else {
                    return json_encode(array("resultCode" => 1));
                }
                break;
            case 15;
                //上传血尿酸数据接口 倍康仪,健康亭
                if ($raw_post_data) {
                    $dataS = array(
                        'uricacid' => isset($data['data']['uricacid']) ? $data['data']['uricacid'] : "",
                        'time' => isset($data['data']['time']) ? $data['data']['time'] : "",
                        'medicId' => isset($data['data']['medicId']) ? $data['data']['medicId'] : "",
                        'medicId' => isset($data['data']['medicId']) ? $data['data']['medicId'] : "",
                        'idcard' => isset($data['idcard']) ? $data['idcard'] : "",
                        'phone' => isset($data['phone']) ? $data['phone'] : "",
                        'type' => isset($data['type']) ? $data['type'] : "",
                        'dtype' => isset($data['dtype']) ? $data['dtype'] : "",
                        'did' => isset($data['did']) ? $data['did'] : ""
                    );
                    $this->sleep_model->add($dataS);
                    return json_encode(array("resultCode" => 0));
                } else {
                    return json_encode(array("resultCode" => 1));
                }
                break;
            case 16:
                //批量上传计步器数据接口 倍康仪
                if ($raw_post_data) {
                    foreach ($data['data'] as $arr) {
                        $dataS = array(
                            'steps' => isset($arr['steps']) ? $arr['steps'] : "",
                            'kilometres' => isset($arr['kilometres']) ? $arr['kilometres'] : "",
                            'caloric' => isset($arr['caloric']) ? $arr['caloric'] : "",
                            'time' => isset($arr['time']) ? $arr['time'] : "",
                            'medicId' => isset($arr['medicId']) ? $arr['medicId'] : "",
                            'physicalID' => isset($arr['physicalID']) ? $arr['physicalID'] : "",
                            'organizationId' => isset($arr['organizationId']) ? $arr['organizationId'] : "",
                            'idcard' => isset($arr['idCard']) ? $arr['idCard'] : "",
                            'phone' => isset($data['phone']) ? $data['phone'] : "",
                            'type' => isset($data['type']) ? $data['type'] : "",
                            'dtype' => isset($data['dtype']) ? $data['dtype'] : "",
                            'did' => isset($data['did']) ? $data['did'] : ""
                        );
                        $this->pedometer_model->add($dataS);
                    }
                    return json_encode(array("resultCode" => 0));
                } else {
                    return json_encode(array("resultCode" => 1));
                }
                break;
            case 17:
                //批量上传体温数据接口 倍康仪,老人手表
                if ($raw_post_data) {

                    foreach ($data['data'] as $arr) {
                        $dataS = array(
                            'temperature' => isset($arr['temperature']) ? $arr['temperature'] : "",
                            'time' => isset($arr['time']) ? $arr['time'] : "",
                            'medicId' => isset($arr['medicId']) ? $arr['medicId'] : "",
                            'physicalID' => isset($arr['physicalID']) ? $arr['physicalID'] : "",
                            'organizationId' => isset($arr['organizationId']) ? $arr['organizationId'] : "",
                            'idcard' => isset($arr['idCard']) ? $arr['idCard'] : "",
                            'phone' => isset($data['phone']) ? $data['phone'] : "",
                            'type' => isset($data['type']) ? $data['type'] : "",
                            'dtype' => isset($data['dtype']) ? $data['dtype'] : "",
                            'did' => isset($data['did']) ? $data['did'] : ""
                        );
                        $this->temperature_model->add($dataS);
                    }
                    return json_encode(array("resultCode" => 0));
                } else {
                    return json_encode(array("resultCode" => 1));
                }
                break;
            case 18:
                //批量上传血氧数据接口 倍康仪,老人手表
                if ($raw_post_data) {
                    foreach ($data['data'] as $arr) {
                        $dataS = array(
                            'bloodoxygen' => isset($arr['bloodoxygen']) ? $arr['bloodoxygen'] : "",
                            'pulse' => isset($arr['pulse']) ? $arr['pulse'] : "",
                            'time' => isset($arr['time']) ? $arr['time'] : "",
                            'medicId' => isset($arr['medicId']) ? $arr['medicId'] : "",
                            'physicalID' => isset($arr['physicalID']) ? $arr['physicalID'] : "",
                            'suggestion' => isset($arr['suggestion']) ? $arr['suggestion'] : "",
                            'organizationId' => isset($arr['organizationId']) ? $arr['organizationId'] : "",
                            'idcard' => isset($arr['idCard']) ? $arr['idCard'] : "",
                            'phone' => isset($data['phone']) ? $data['phone'] : "",
                            'type' => isset($data['type']) ? $data['type'] : "",
                            'dtype' => isset($data['dtype']) ? $data['dtype'] : "",
                            'did' => isset($data['did']) ? $data['did'] : ""
                        );
                        $this->blood_oxygen_model->add($dataS);
                    }
                    return array("resultCode", 0);
                } else {
                    return array("resultCode", 1);
                }
                break;
            case 19:
                //批量上传尿液分析数据接口 倍康仪
                if ($raw_post_data) {
                    foreach ($data['data'] as $arr) {
                        $dataS = array(
                            'LEU' => isset($arr['LEU']) ? $arr['LEU'] : "",
                            'NIT' => isset($arr['NIT']) ? $arr['NIT'] : "",
                            'UBG' => isset($arr['UBG']) ? $arr['UBG'] : "",
                            'PRO' => isset($arr['PRO']) ? $arr['PRO'] : "",
                            'PH' => isset($arr['PH']) ? $data['data']['PH'] : "",
                            'BLD' => isset($arr['BLD']) ? $arr['BLD'] : "",
                            'SG' => isset($arr['SG']) ? $arr['SG'] : "",
                            'KET' => isset($arr['KET']) ? $arr['KET'] : "",
                            'BIL' => isset($arr['BIL']) ? $arr['BIL'] : "",
                            'GLU' => isset($arr['GLU']) ? $arr['GLU'] : "",
                            'VC' => isset($arr['VC']) ? $arr['VC'] : "",
                            'time' => isset($arr['time']) ? $arr['time'] : "",
                            'medicId' => isset($arr['medicId']) ? $arr['medicId'] : "",
                            'physicalID' => isset($arr['physicalID']) ? $arr['physicalID'] : "",
                            'organizationId' => isset($arr['organizationId']) ? $arr['organizationId'] : "",
                            'idcard' => isset($arr['idCard']) ? $arr['idCard'] : "",
                            'phone' => isset($data['phone']) ? $data['phone'] : "",
                            'type' => isset($data['type']) ? $data['type'] : "",
                            'dtype' => isset($data['dtype']) ? $data['dtype'] : "",
                            'did' => isset($data['did']) ? $data['did'] : ""
                        );
                        $this->urine_model->add($dataS);
                    }
                    return array("resultCode", 0);
                } else {
                    return array("resultCode", 1);
                }
                break;
            case 20:
                //批量上传血尿酸数据接口 倍康仪
                if ($raw_post_data) {
                    foreach ($data['data'] as $arr) {
                        $dataS = array(
                            'uricacid' => isset($arr['uricacid']) ? $arr['uricacid'] : "",
                            'time' => isset($arr['time']) ? $arr['time'] : "",
                            'medicId' => isset($arr['medicId']) ? $arr['medicId'] : "",
                            'medicId' => isset($arr['medicId']) ? $arr['medicId'] : "",
                            'organizationId' => isset($arr['organizationId']) ? $arr['organizationId'] : "",
                            'idcard' => isset($arr['idCard']) ? $arr['idCard'] : "",
                            'phone' => isset($data['phone']) ? $data['phone'] : "",
                            'type' => isset($data['type']) ? $data['type'] : "",
                            'dtype' => isset($data['dtype']) ? $data['dtype'] : "",
                            'did' => isset($data['did']) ? $data['did'] : ""
                        );
                        $this->sleep_model->add($dataS);
                    }
                    return array("resultCode", 0);
                } else {
                    return array("resultCode", 1);
                }
                break;
            case 21:
                //批量上传血糖数据接口 倍康仪,老人手表
                if ($raw_post_data) {
                    foreach ($data['data'] as $arr) {
                        $dataS = array(
                            'bloodsugar' => isset($arr['bloodsugar']) ? $arr['bloodsugar'] : "",
                            'types' => isset($arr['type']) ? $arr['type'] : "",
                            'time' => isset($arr['time']) ? $arr['time'] : "",
                            'medicId' => isset($arr['medicId']) ? $arr['medicId'] : "",
                            'physicalID' => isset($arr['physicalID']) ? $arr['physicalID'] : "",
                            'organizationId' => isset($arr['organizationId']) ? $arr['organizationId'] : "",
                            'idcard' => isset($arr['idCard']) ? $arr['idCard'] : "",
                            'phone' => isset($data['phone']) ? $data['phone'] : "",
                            'type' => isset($data['type']) ? $data['type'] : "",
                            'dtype' => isset($data['dtype']) ? $data['dtype'] : "",
                            'did' => isset($data['did']) ? $data['did'] : ""
                        );
                        $this->blood_sugar_model->add($dataS);
                    }
                    return array("resultCode", 0);
                } else {
                    return array("resultCode", 1);
                }
                break;
            case 22:
                //批量上传胆固醇数据接口 倍康仪
                if ($raw_post_data) {
                    foreach ($data['data'] as $arr) {
                        $dataS = array(
                            'cholesterol' => isset($arr['cholesterol']) ? $arr['cholesterol'] : "",
                            'time' => isset($arr['time']) ? $arr['time'] : "",
                            'medicId' => isset($arr['medicId']) ? $arr['medicId'] : "",
                            'physicalID' => isset($arr['physicalID']) ? $arr['physicalID'] : "",
                            'organizationId' => isset($arr['organizationId']) ? $arr['organizationId'] : "",
                            'idcard' => isset($arr['idCard']) ? $arr['idCard'] : "",
                            'phone' => isset($data['phone']) ? $data['phone'] : "",
                            'type' => isset($data['type']) ? $data['type'] : "",
                            'dtype' => isset($data['dtype']) ? $data['dtype'] : "",
                            'did' => isset($data['did']) ? $data['did'] : ""
                        );
                        $this->cholesterol_model->add($dataS);
                    }
                    return array("resultCode", 0);
                } else {
                    return array("resultCode", 1);
                }
                break;
            case 23:
                //批量上传血压数据接口 倍康仪,老人手表
                if ($raw_post_data) {
                    foreach ($data['data'] as $arr) {
                        $dataS = array(
                            'systolic' => isset($arr['systolic']) ? $arr['systolic'] : "",
                            'diastolic' => isset($arr['diastolic']) ? $arr['diastolic'] : "",
                            'pulse' => isset($arr['pulse']) ? $arr['pulse'] : "",
                            'time' => isset($arr['time']) ? $arr['time'] : "",
                            'medicId' => isset($arr['medicId']) ? $$arr['medicId'] : "",
                            'physicalID' => isset($arr['physicalID']) ? $arr['physicalID'] : "",
                            'suggestion' => isset($arr['suggestion']) ? $arr['suggestion'] : "",
                            'organizationId' => isset($arr['organizationId']) ? $arr['organizationId'] : "",
                            'idcard' => isset($arr['idCard']) ? $arr['idCard'] : "",
                            'phone' => isset($data['phone']) ? $data['phone'] : "",
                            'type' => isset($data['type']) ? $data['type'] : "",
                            'dtype' => isset($data['dtype']) ? $data['dtype'] : "",
                            'did' => isset($data['did']) ? $data['did'] : ""
                        );
                        $this->blood_pressure_model->add($dataS);
                    }
                    return array("resultCode", 0);
                } else {
                    return array("resultCode", 1);
                }
                break;
            case 24:
                //批量上传人体成分数据接口 倍康仪,老人手表
                if ($raw_post_data) {
                    foreach ($data['data'] as $arr) {
                        $dataS = array(
                            'muscle' => isset($arr['muscle']) ? $arr['muscle'] : "",
                            'adiposerate' => isset($arr['adiposerate']) ? $arr['adiposerate'] : "",
                            'visceralfat' => isset($arr['visceralfat']) ? $arr['visceralfat'] : "",
                            'visceralfat' => isset($arr['visceralfat']) ? $arr['visceralfat'] : "",
                            'bone' => isset($arr['bone']) ? $arr['bone'] : "",
                            'thermal' => isset($arr['thermal']) ? $arr['thermal'] : "",
                            'basalMetabolism' => isset($arr['basalMetabolism']) ? $arr['basalMetabolism'] : "",
                            'metabolism' => isset($arr['metabolism']) ? $arr['metabolism'] : "",
                            'bmi' => isset($arr['bmi']) ? $arr['bmi'] : "",
                            'impedance' => isset($arr['impedance']) ? $arr['impedance'] : "",
                            'time' => isset($arr['time']) ? $arr['time'] : "",
                            'medicId' => isset($arr['medicId']) ? $arr['medicId'] : "",
                            'physicalID' => isset($arr['physicalID']) ? $arr['physicalID'] : "",
                            'suggestion' => isset($arr['suggestion']) ? $arr['suggestion'] : "",
                            'organizationId' => isset($arr['organizationId']) ? $arr['organizationId'] : "",
                            'idcard' => isset($arr['idCard']) ? $arr['idCard'] : "",
                            'phone' => isset($data['phone']) ? $data['phone'] : "",
                            'type' => isset($data['type']) ? $data['type'] : "",
                            'dtype' => isset($data['dtype']) ? $data['dtype'] : "",
                            'did' => isset($data['did']) ? $data['did'] : ""
                        );
                        $this->human_extracts_model->add($dataS);
                    }
                    return array("resultCode", 0);
                } else {
                    return array("resultCode", 1);
                }
                break;
            case 25:
                //批量上传体重数据接口 倍康仪,老人手表
                if ($raw_post_data) {
                    foreach ($data['data'] as $arr) {
                        $dataS = array(
                            'weight' => isset($arr['weight']) ? $arr['weight'] : "",
                            'height' => isset($arr['height']) ? $arr['height'] : "",
                            'time' => isset($arr['time']) ? $arr['time'] : "",
                            'medicId' => isset($arr['medicId']) ? $arr['medicId'] : "",
                            'physicalID' => isset($arr['physicalID']) ? $arr['physicalID'] : "",
                            'suggestion' => isset($arr['suggestion']) ? $arr['suggestion'] : "",
                            'organizationId' => isset($arr['organizationId']) ? $arr['organizationId'] : "",
                            'idcard' => isset($arr['idCard']) ? $arr['idCard'] : "",
                            'phone' => isset($data['phone']) ? $data['phone'] : "",
                            'type' => isset($data['type']) ? $data['type'] : "",
                            'dtype' => isset($data['dtype']) ? $data['dtype'] : "",
                            'did' => isset($data['did']) ? $data['did'] : ""
                        );
                        $this->weight_model->add($dataS);
                    }
                    return array("resultCode", 0);
                } else {
                    return array("resultCode", 1);
                }
                break;
            case 26:
                //用户信息修改 倍康仪
                if ($raw_post_data) {
                    $dataS = array(
                        'idCard' => isset($data['data']['idCard']) ? $data['data']['idCard'] : "",
                        'skCard' => isset($data['data']['skCard']) ? $data['data']['skCard'] : "",
                        'name' => isset($data['data']['name']) ? $data['data']['name'] : "",
                        'sex' => isset($data['data']['sex']) ? $data['data']['sex'] : "",
                        'national' => isset($data['data']['national']) ? $data['data']['national'] : "",
                        'birthday' => isset($data['data']['birthday']) ? $data['data']['birthday'] : "",
                        'height' => isset($data['data']['height']) ? $data['data']['height'] : "",
                        'weight' => isset($data['data']['weight']) ? $data['data']['weight'] : "",
                        'profession' => isset($data['data']['profession']) ? $data['data']['profession'] : "",
                        'pwd' => isset($data['data']['pwd']) ? $data['data']['pwd'] : "",
                        'phone' => isset($data['data']['phone']) ? $data['data']['phone'] : "",
                        'email' => isset($data['data']['email']) ? $data['data']['email'] : "",
                        'registeraddress' => isset($data['data']['registeraddress']) ? $data['data']['registeraddress'] : "",
                        'realaddress' => isset($data['data']['realaddress']) ? $data['data']['realaddress'] : "",
                        'sn' => isset($data['data']['sn']) ? $data['data']['sn'] : "",
                        'dtype' => isset($data['data']['dtype']) ? $data['data']['dtype'] : "",
                        'softver' => isset($data['data']['softver']) ? $data['data']['softver'] : "",
                        'videocallver' => isset($data['data']['videocallver']) ? $data['data']['videocallver'] : "",
                        'maxusernum' => isset($data['data']['maxusernum']) ? $data['data']['maxusernum'] : "",
                        'local' => isset($data['data']['local']) ? $data['data']['local'] : "",
                        'picture' => isset($data['data']['picture']) ? $data['data']['picture'] : "",
                        'physicalID' => isset($data['data']['physicalID']) ? $data['data']['physicalID'] : "",
                    );
                    $this->users_model->where(array("idCard" => $data['idcard']))->save($dataS);

                    return json_encode(array("resultCode" => 0));
                } else {
                    return json_encode(array("resultCode" => 1));
                }
                break;
            case 27:
                //手表定位信息上传 老人手表
                if ($raw_post_data) {
                    $dataS = array(
                        'time' => isset($data['data']['time']) ? $data['data']['time'] : "",
                        'isRecently' => isset($data['data']['isRecently']) ? $data['data']['isRecently'] : "",
                        'lat' => isset($data['data']['lat']) ? $data['data']['lat'] : "",
                        'weidu' => isset($data['data']['weidu']) ? $data['data']['weidu'] : "",
                        'lng' => isset($data['data']['lng']) ? $data['data']['lng'] : "",
                        'jingdu' => isset($data['data']['jingdu']) ? $data['data']['jingdu'] : "",
                        'dataType' => isset($data['data']['dataType']) ? $data['data']['dataType'] : "",
                        'o_lat' => isset($data['data']['o_lat']) ? $data['data']['o_lat'] : "",
                        'o_lng' => isset($data['data']['o_lng']) ? $data['data']['o_lng'] : "",
                        'address' => isset($data['data']['address']) ? $data['data']['address'] : "",
                        'idcard' => isset($data['idcard']) ? $data['idcard'] : "",
                        'phone' => isset($data['phone']) ? $data['phone'] : "",
                        'type' => isset($data['type']) ? $data['type'] : "",
                        'dtype' => isset($data['dtype']) ? $data['dtype'] : "",
                        'did' => isset($data['did']) ? $data['did'] : ""
                    );
                    $this->watch_positioning_model->add($dataS);
                    return json_encode(array("resultCode" => 0));
                } else {
                    return json_encode(array("resultCode" => 1));
                }
                break;
            case 28:
                //心血脑管上传 倍康仪
                if ($raw_post_data) {
                    $dataS = array(
                        'photo' => isset($data['data']['photo']) ? $data['data']['photo'] : "",
                        'vri' => isset($data['data']['vri']) ? $data['data']['vri'] : "",
                        'time' => isset($data['data']['time']) ? $data['data']['time'] : "",
                        'medicId' => isset($data['data']['medicId']) ? $data['data']['medicId'] : "",
                        'physicalID' => isset($data['data']['physicalID']) ? $data['data']['physicalID'] : "",
                        'idcard' => isset($data['idcard']) ? $data['idcard'] : "",
                        'phone' => isset($data['phone']) ? $data['phone'] : "",
                        'type' => isset($data['type']) ? $data['type'] : "",
                        'dtype' => isset($data['dtype']) ? $data['dtype'] : "",
                        'did' => isset($data['did']) ? $data['did'] : ""
                    );
                    $this->cardio_cerebral_vessels_model->add($dataS);
                    return json_encode(array("resultCode" => 0));
                } else {
                    return json_encode(array("resultCode" => 1));
                }
                break;
            case 31:
                //糖化血红蛋白上传 倍康仪，健康亭
                if ($raw_post_data) {
                    $dataS = array(
                        'ngsp' => isset($data['data']['ngsp']) ? $data['data']['ngsp'] : "",
                        'medicId' => isset($data['data']['medicId']) ? $data['data']['vri'] : "",
                        'time' => isset($data['data']['time']) ? $data['data']['time'] : "",
                        'medicId' => isset($data['data']['medicId']) ? $data['data']['medicId'] : "",
                        'physicalID' => isset($data['data']['physicalID']) ? $data['data']['physicalID'] : "",
                        'suggestion' => isset($data['data']['suggestion']) ? $data['data']['suggestion'] : "",
                        'food' => isset($data['data']['suggestion']['food']) ? $data['data']['suggestion']['food'] : "",
                        'sport' => isset($data['data']['suggestion']['sport']) ? $data['data']['suggestion']['sport'] : "",
                        'common' => isset($data['data']['suggestion']['common']) ? $data['data']['suggestion']['common'] : "",
                        'doctor' => isset($data['data']['suggestion']['doctor']) ? $data['data']['suggestion']['doctor'] : "",
                        'idcard' => isset($data['idcard']) ? $data['idcard'] : "",
                        'phone' => isset($data['phone']) ? $data['phone'] : "",
                        'type' => isset($data['type']) ? $data['type'] : "",
                        'dtype' => isset($data['dtype']) ? $data['dtype'] : "",
                        'did' => isset($data['did']) ? $data['did'] : ""
                    );
                    $this->cardio_cerebral_vessels_model->add($dataS);
                    return json_encode(array("resultCode" => 0));
                } else {
                    return json_encode(array("resultCode" => 1));
                }
                break;
            case 35:
                //批量心电上传 倍康仪，健康亭
                if ($raw_post_data) {
                    foreach ($data['data'] as $arr) {
                        $dataS = array(
                            'pushOrder' => isset($arr['pushOrder']) ? $arr['pushOrder'] : "",
                            'startTime' => isset($arr['startTime']) ? $arr['startTime'] : "",
                            'endTime' => isset($arr['endTime']) ? $arr['endTime'] : "",
                            'ecg' => isset($arr['ecg']) ? $arr['ecg'] : "",
                            'ecgPng' => isset($arr['ecgPng']) ? $arr['ecgPng'] : "",
                            'HR' => isset($arr['HR']) ? $arr['HR'] : "",
                            'ST1' => isset($arr['ST1']) ? $arr['ST1'] : "",
                            'HR' => isset($arr['HR']) ? $arr['HR'] : "",
                            'ST2' => isset($arr['ST2']) ? $arr['ST2'] : "",
                            'rr' => isset($arr['rr']) ? $arr['rr'] : "",
                            'dlMethod' => isset($arr['dlMethod']) ? $arr['dlMethod'] : "",
                            'zy' => isset($arr['zy']) ? $arr['zy'] : "",
                            'medicId' => isset($arr['medicId']) ? $arr['medicId'] : "",
                            'physicalID' => isset($arr['physicalID']) ? $arr['physicalID'] : "",
                            'organizationId' => isset($arr['organizationId']) ? $arr['organizationId'] : "",
                            'idcard' => isset($arr['idCard']) ? $arr['idCard'] : "",
                            'phone' => isset($data['phone']) ? $data['phone'] : "",
                            'type' => isset($data['type']) ? $data['type'] : "",
                            'dtype' => isset($data['dtype']) ? $data['dtype'] : "",
                            'did' => isset($data['did']) ? $data['did'] : ""
                        );
                        $this->electrocardiogram_model->add($dataS);
                    }
                    return array("resultCode", 0);
                } else {
                    return array("resultCode", 1);
                }
                break;
            case 36:
                //血红蛋白上传 倍康仪，健康亭
                if ($raw_post_data) {
                    $dataS = array(
                        'hb' => isset($data['data']['hb']) ? $data['data']['hb'] : "",
                        'hct' => isset($data['data']['hct']) ? $data['data']['hct'] : "",
                        'time' => isset($data['data']['time']) ? $data['data']['time'] : "",
                        'medicId' => isset($data['data']['medicId']) ? $data['data']['medicId'] : "",
                        'physicalID' => isset($data['data']['physicalID']) ? $data['data']['physicalID'] : "",
                        'suggestion' => isset($data['data']['suggestion']) ? $data['data']['suggestion'] : "",
                        'food' => isset($data['data']['suggestion']['food']) ? $data['data']['suggestion']['food'] : "",
                        'sport' => isset($data['data']['suggestion']['sport']) ? $data['data']['suggestion']['sport'] : "",
                        'common' => isset($data['data']['suggestion']['common']) ? $data['data']['suggestion']['common'] : "",
                        'doctor' => isset($data['data']['suggestion']['doctor']) ? $data['data']['suggestion']['doctor'] : "",
                        'idcard' => isset($data['idcard']) ? $data['idcard'] : "",
                        'phone' => isset($data['phone']) ? $data['phone'] : "",
                        'type' => isset($data['type']) ? $data['type'] : "",
                        'dtype' => isset($data['dtype']) ? $data['dtype'] : "",
                        'did' => isset($data['did']) ? $data['did'] : ""
                    );
                    $this->cardio_cerebral_vessels_model->add($dataS);
                    return json_encode(array("resultCode" => 0));
                } else {
                    return json_encode(array("resultCode" => 1));
                }
                break;

        }


        //echo json_encode(S('app_access_token_user_u_b6e622706c00dd770c30b75c48dbd7d7ec1370de'));


    }
}