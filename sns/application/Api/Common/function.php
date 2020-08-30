<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2014 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: XiaoYuan <767684610@qq.com>
// +----------------------------------------------------------------------
/**
 * 开发者token验证
 * @param string $token
 * @return bool
 */
function check_app_token($app_token)
{
    $signature = $_GET["signature"];
    $timestamp = $_GET["timestamp"];
    $nonce = $_GET["nonce"];

    $tmpArr = array($app_token, $timestamp, $nonce);
    // use SORT_STRING rule
    sort($tmpArr, SORT_STRING);
    $tmpStr = implode($tmpArr);
    $tmpStr = sha1($tmpStr);

    if ($tmpStr == $signature) {
        return true;
    } else {
        return false;
    }
}

/**
 * 获取生成token,是否拥有权限访问
 * @param string $uuid
 * @param string $app_id
 * @param string $app_secret
 * @return $access_token
 */
function get_app_access_token($uuid, $app_id, $app_secret)
{
    $access_token = S(C('APP_AUTH_CODE') . C('APP_ACCESS_TOKEN_A_') . $uuid);   //这里appid和appsecret我写固定了，实际是通过客户端获取  所以这里我们可以做很多 比如判断appid和appsecret有效性等
    //这里token存在则直接返回，不存在则生成并设置，最后返回token
    if ($access_token) {
        return $access_token;//S($ori_str,null);
    }

    //这里是token产生的机制  您也可以自己定义
    //$nonce = create_noncestr(32);
    $tmpArr = array($uuid, $app_id, $app_secret);
    sort($tmpArr, SORT_STRING);
    $tmpStr = implode($tmpArr);
    $access_token = sha1(C('APP_AUTH_CODE') . $tmpStr);
    //这里做了缓存 'a'=>b 和'b'=>a格式的缓存
    //S($this->app_id.'_'.$this->app_secret,$tmpStr,7200);
    S(C('APP_AUTH_CODE') . C('APP_ACCESS_TOKEN_A_') . $uuid, $access_token, 86400 * 180);
    S(C('APP_AUTH_CODE') . C('APP_ACCESS_TOKEN_B_') . $access_token, $uuid, 86400 * 180);
    return $access_token;
}

/**
 * 获取设备号
 * @param string $access_token
 * @return uuid
 */
function get_device_uuid($access_token)
{
    return S(C('APP_AUTH_CODE') . C('APP_ACCESS_TOKEN_B_') . $access_token);
}

/**
 * 获取生成用户登录token
 * @param integer $userid
 * @param string $app_access_token
 * @return $access_token
 */
function get_user_access_token($userid, $app_access_token)
{
    $access_token = S(C('APP_AUTH_CODE') . C('APP_ACCESS_TOKEN_USER_A_') . $userid);
    //这里token存在则直接返回，不存在则生成并设置，最后返回token
    if ($access_token) {
        $tempToken = S(C('APP_AUTH_CODE') . C('APP_ACCESS_TOKEN_USER_U_') . $access_token);
        //$tempToken = $tempToken.$app_access_token;
        //$tempToken1 = json_decode($tempToken,TRUE);
        //echo json_encode(array('status'=>1,'acc'=>$tempToken['access_token']));exit;
        if ($tempToken && $tempToken['access_token'] != $app_access_token) {

            //删除缓存，这里如果多设备登录，则删除之前设备的token。
            S(C('APP_AUTH_CODE') . C('APP_ACCESS_TOKEN_USER_A_') . $userid, null);
            S(C('APP_AUTH_CODE') . C('APP_ACCESS_TOKEN_USER_U_') . $access_token, null);
            return get_user_access_token($userid, $app_access_token);
        }
        //缓存+7天
        S(C('APP_AUTH_CODE') . C('APP_ACCESS_TOKEN_USER_A_') . $userid, $access_token, 60 * 60 * 24 * 7);
        S(C('APP_AUTH_CODE') . C('APP_ACCESS_TOKEN_USER_U_') . $access_token, json_encode(array(
            'user_id' => $tempToken['user_id'], 'access_token' => $tempToken['access_token']
        )), 60 * 60 * 24 * 7);
        return $access_token;//S($ori_str,null);
    }
    //这里是token产生的机制  您也可以自己定义
    $nonce = create_noncestr(32);
    $timestamp = time();
    $tmpArr = array($userid, $timestamp, $nonce);
    sort($tmpArr, SORT_STRING);
    $tmpStr = implode($tmpArr);
    $access_token = sha1(C('APP_AUTH_CODE') . $tmpStr);
    //S($this->app_id.'_'.$this->app_secret,$tmpStr,7200);
    //设置用户缓存，7天有效
    //S($access_token, 'app_access_token_'.$uuid, 60*60*24*7);
    //$access_token = json_encode(array('access_token'=>$temp_token,'user_id'=>$userid));
    //这里做了缓存 'a'=>b 和'b'=>a格式的缓存
    //缓存token
    S(C('APP_AUTH_CODE') . C('APP_ACCESS_TOKEN_USER_A_') . $userid, $access_token, 60 * 60 * 24 * 7);
    //缓存用户id
    S(C('APP_AUTH_CODE') . C('APP_ACCESS_TOKEN_USER_U_') . $access_token, json_encode(array(
        'user_id' => $userid, 'access_token' => $app_access_token
    )), 60 * 60 * 24 * 7);
    return $access_token;
}

/**
 * 获取用户id
 * @param string $user_access_token
 * @return integer user_id
 */
function get_user_id($user_access_token)
{
    $data = json_decode(S(C('APP_AUTH_CODE') . C('APP_ACCESS_TOKEN_USER_U_') . $user_access_token), true);
    if ($data) {
        //存在则累加有效期
        get_user_access_token($data['user_id'], $data['access_token']);
        return $data['user_id'];
    }
    return false;
}

/**
 * 作用：产生随机字符串，不长于32位
 * @param integer $length
 * @return string $str
 */
function create_noncestr($length = 32)
{
    $chars = "abcdefghijklmnopqrstuvwxyz0123456789";
    $str = "";
    for ($i = 0; $i < $length; $i++) {
        $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
    }
    return $str;
}

/**
 * 获取文件地址
 * @param string $filename
 * @return string url
 */
function get_file_path($filename)
{
    $data = json_decode($filename, true);
    if (is_null($data)) {
        return $filename?C('APP_REQUEST_URL') . $filename:'';
    }
    return C('APP_REQUEST_URL') . '/data/upload/' . $data['thumb'];
}


/**
 * 防sql注入字符串转义
 * @param $content 要转义内容
 * @return array|string
 */
function escapeString($content)
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

/**
 * Notes: 分页 $count条数  $page一页多少条  $pageNum 页码(针对mysql)
 * User: Sen
 * DateTime: 2020/6/23 19:05
 * Return:
 */
function page($count, $page, $pageNum)
{
    //分页
    $num = $count / $page;
    if (empty($pageNum) || $pageNum == 1) {
        $current_page = 1;
        $start_num = 0;
        if (is_float($num)) {
            $total_page = ceil($num);
        } else {
            $total_page = $num;
        }
    } else {
        $current_page = $pageNum;
        $start_num = ($pageNum - 1) * $page;
        if (is_float($num)) {
            $total_page = ceil($num);
        } else {
            $total_page = $num;
        }
    }
    $data = array(
        'num' => $page, 'start_num' => $start_num, 'current_page' => $current_page, 'total_page' => $total_page,
    );
    return $data;

}

/**
 * Notes: 二维数组去重
 * User: Sen
 * DateTime: 2020/6/24 15:34
 * Return:
 */
function array_deduplication($arr, $key)
{
    $tmp_arr = array();
    foreach ($arr as $k => $v) {
        if (in_array($v[$key], $tmp_arr))   //搜索$v[$key]是否在$tmp_arr数组中存在，若存在返回true
        {
            unset($arr[$k]); //销毁一个变量  如果$tmp_arr中已存在相同的值就删除该值
        } else {
            $tmp_arr[$k] = $v[$key];  //将不同的值放在该数组中保存
        }
    }
    return $arr;
}

/**
 * Notes: 产生随机字符串，不长于32位
 * User: Sen
 * DateTime: 2020/7/9 11:25
 * Return:
 */
function createNoncestr($length = 8)
{
    $chars = "abcdefghijklmnopqrstuvwxyz0123456789";
    $str = "";
    for ($i = 0; $i < $length; $i++) {
        $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
    }
    return $str;
}

/**
 * Notes: 过滤掉emoji表情
 * User: Sen
 * DateTime: 2020/7/9 11:25
 * Return:
 */
function filter_Emoji($str)
{
    $str = preg_replace_callback(    //执行一个正则表达式搜索并且使用一个回调进行替换
        '/./u', function (array $match) {
        return strlen($match[0]) >= 4?'':$match[0];
    }, $str);

    return $str;
}

/**
 * Notes: 二维数组分页
 * User: Sen
 * DateTime: 2020/7/1 15:50
 * Return:
 *  $arr 二维数组 $p  页数 $count 每页多少条
 */
function arr_page($arr, $p, $count)
{
    $list = array();
    if (empty($p)) {
        $p = 1;
    }
    if (empty($count)) {
        $count = 8;
    }
    $num = count($arr);
    $list["total_page"] = ceil($num / $count);
    $list["current_page"] = $p;


    $start = ($p - 1) * $count;
    for ($i = $start; $i < $start + $count; $i++) {
        if (!empty($arr[$i])) {
            $list["list"][] = $arr[$i];
        }
    }
    return $list;
}

/**
 * Notes: 二维数组分组
 * User: Sen
 * DateTime: 2020/7/4 22:51
 * @param $data :二维数组
 * @param $id  :id字段
 * @param $parent_id :父级id字段
 * @return array
 */

function generateTree($data, $id, $parent_id)
{
    $items = array();
    foreach ($data as $v) {
        $items[$v[$id]] = $v;
    }
    $tree = array();
    foreach ($items as $k => $item) {
        if (isset($items[$item[$parent_id]])) {
            $items[$item[$parent_id]]['List'][] = &$items[$k];
        } else {
            $tree[] = &$items[$k];
        }
    }
    return $tree;
}

/**
 * Notes: 8位随机数
 * User: Sen
 * DateTime: 2020/7/5 18:47
 * @param int $start 要生成的数字开始范围
 * @param int $end 结束范围
 * @param int $length 需要生成的随机数个数
 * @return mixed  生成的随机数
 */
function getRandNumber($start = 1, $end = 9, $length = 8)
{

    //初始化变量为0

    $connt = 0;

    //建一个新数组

    $temp = array();

    while ($connt < $length) {

        //在一定范围内随机生成一个数放入数组中

        $temp[] = mt_rand($start, $end);

        //$data = array_unique($temp);

        //去除数组中的重复值用了“翻翻法”，就是用array_flip()把数组的key和value交换两次。这种做法比用 array_unique() 快得多。

        $data = array_flip(array_flip($temp));

        //将数组的数量存入变量count中

        $connt = count($data);

    }

    //为数组赋予新的键名

    shuffle($data);

    //数组转字符串

    $str = implode(",", $data);

    //替换掉逗号

    $number = str_replace(',', '', $str);

    return $number;

}

/**
 * 获取当前格式化时间
 * */
if (!function_exists('get_curt_time')) {
    function get_curt_time(){
        return date("Y-m-d H:i:s",time());
    }
}


/**
 * 验证手机号
 * */
if (!function_exists('isMobile')) {
    function isMobile($sPhone)
    {
        if (preg_match("/^1[34578]{1}\d{9}$/", $sPhone)) {
            return true;
        }
        return false;
    }
}

/**
 * Notes: 根据某一个字段分组二维
 * User: Sen
 * DateTime: 2020/7/8 14:16
 * Return:  {dataArr:需要分组的数据；keyStr:分组依据}
 */
function dataGroup($dataArr, $keyStr)
{
    $newArr = [];
    foreach ($dataArr as $k => $val) {    //数据根据日期分组
        $newArr[$val[$keyStr]][] = $val;
    }
    return $newArr;
}

/**
 * Notes: 排序
 * User: Sen
 * DateTime: 2020/7/8 14:23
 * Return:
 */
function arraySort($array, $keys, $sort = 'asc')
{
    $newArr = $valArr = array();
    foreach ($array as $key => $value) {
        $valArr[$key] = $value[$keys];
    }
    ($sort == 'asc')?asort($valArr):arsort($valArr);
    reset($valArr);
    foreach ($valArr as $key => $value) {
        $newArr[$key] = $array[$key];
    }
    return $newArr;
}

/**
 * Notes: 获取两个日期的差值
 * User: Sen
 * DateTime: 2020/7/15 9:56
 * Return:
 */
function DiffDate($date1, $date2)
{
    if (strtotime($date1) > strtotime($date2)) {
        $ymd = $date2;
        $date2 = $date1;
        $date1 = $ymd;
    }
    list($y1, $m1, $d1) = explode('-', $date1);
    list($y2, $m2, $d2) = explode('-', $date2);
    $y = $m = $d = $_m = 0;
    $math = ($y2 - $y1) * 12 + $m2 - $m1;
    $y = round($math / 12);
    $m = intval($math % 12);
    $d = (mktime(0, 0, 0, $m2, $d2, $y2) - mktime(0, 0, 0, $m2, $d1, $y2)) / 86400;
    if ($d < 0) {
        $m -= 1;
        $d += date('j', mktime(0, 0, 0, $m2, 0, $y2));
    }
    $m < 0 && $y -= 1;
    return array($y, $m, $d);
}