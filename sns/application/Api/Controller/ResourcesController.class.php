<?php
/**
 * Created by PhpStorm
 * Name:
 * User: 刘北林
 * Date: 2020-06-23
 * Time: 16:47
 */

namespace Api\Controller;


use Org\Util\ArrayList;

class ResourcesController extends AppController
{

    protected $user_model;//用户

    function __construct()
    {
        header("Access-Control-Allow-Origin: *");
        header("Content-Type: text/html; charset=UTF-8");
        parent::_initialize();
        $this->user_model = M('User');
    }

    /***
     * Notes:职位筛选检索
     * param $arr 筛选的所有值
     * User: Belise
     * DateTime: 2020-06-30 12:50
     * Return :array()职位列表
     */
    public function recruitmentFilter($arr, $str)
    {
        $sql = "SELECT
	e.id AS cid,
	d.enterprise_id,
	d.bid AS id,
	e.NAME,
	d.position,
	d.time,
	d.avatar ,
	d.is_anxious ,
	d.address_x,
	d.address_y
FROM
	(
	SELECT
		b.id AS bid,
		b.enterprise_id,
		b.position,
		b.time,
		b.is_anxious,
		b.content,
		b.address_x,
		b.address_y,
		b.ishot,
		c.NAME AS cname,
		c.avatar 
	FROM
		(
		SELECT
			* 
		FROM
			( SELECT *, group_concat( spec_info_id ORDER BY spec_info_id ) AS space_list FROM az_spec_label WHERE spec_info_id IN ".$arr." GROUP BY object_id ) AS a 
		WHERE
			a.space_list LIKE  ". $str ."
		) AS a
		LEFT JOIN az_recruitment AS b ON a.object_id = b.id
		INNER JOIN az_user AS c ON b.uid = c.id 
	) AS d
	LEFT JOIN az_user_enterpise AS e ON d.enterprise_id = e.id;";
        return $sql;
    }

    /**
     * Notes: 根据unionid获取此用户聊天列表 最后一条 sql
     * User: Belief
     * param $type 默认1 是所有跟boss聊天的消息 2是boss消息只针对自己发布的职位消息
     * DateTime: 2020/6/28 17:05
     * Return: array(聊天列表)
     */
    public function chatList($id, $tabelname,$type=1)
    {
        $boss_id =" ";
        if($type ==2){
            $boss_id =" and r.uid =".$id." ";
        }
        $sql = "SELECT
        c.content,
        u.id,
        u.`name` AS username1,
        u.avatar AS avatar1,
        t.`name` AS username2,
        t.avatar AS avatar2,
        r.`id` AS rid,
        r.content AS r_content,
        r.num,
        c.uid,
        c.parent_id,
        c.unread 
    FROM
        (
    SELECT
        *,
        count( CASE WHEN is_read <= 0 THEN 0 END ) unread 
    FROM
        ( SELECT * FROM az_job_application ORDER BY create_time DESC LIMIT 1000 ) AS a 
    WHERE
        a.uid = " . $id . "
        OR a.parent_id = " . $id . " 
    GROUP BY
        CONCAT(
    IF
        ( a.uid > a.parent_id, a.uid, a.parent_id ),
    IF
        ( a.uid < a.parent_id, a.uid, a.parent_id ) 
        ) 
        ) c
        INNER JOIN az_user AS u ON u.id = c.parent_id
        INNER JOIN az_user AS t ON t.id = c.uid
        INNER JOIN az_recruitment AS r ON r.id = c.recruitment_id
        where tablename='" . $tabelname . "'".$boss_id;

        return $sql;
    }

    /**
     * Notes: 根据unionid获取用户信息
     * User: Belief
     * DateTime: 2020/6/27 17:05
     * Return: array(用户List)
     */
    public function userInfo($unionid)
    {
        $uid = $this->getId($unionid);
        $userList = $this->user_model->where(array("id" => $uid))->find();
        return $userList;
    }

    /**
     * Notes: 根据unionid获取uid
     * User: Sen
     * DateTime: 2020/6/23 17:05
     * Return: uid
     */
    public function getId($unionid)
    {
        $userOne = $this->user_model->where(array("unionid" => $unionid))->find();
        return $userOne['id'];
    }

    /**
     * Notes: 防止脚本
     * User: Sen
     * DateTime: 2020/5/9 15:13
     * Return :
     */
    public function remove_xss($val)
    {
        $val = $this->escapeString($val);
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

    /**
     * Notes: 分页 $count条数  $page一页多少条  $pageNum 页码(针对mysql)
     * User: Sen
     * DateTime: 2020/6/23 19:05
     * Return:
     */
    public function page($count, $page, $pageNum)
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
            'num' => $page,
            'start_num' => $start_num,
            'current_page' => $current_page,
            'total_page' => $total_page,
        );
        return $data;

    }

    /**
     * Notes: 二维数组去重
     * User: Sen
     * DateTime: 2020/6/24 15:34
     * Return:
     */
    public function array_deduplication($arr, $key)
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
    public function createNoncestr($length = 8)
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
    public function filter_Emoji($str)
    {
        $str = preg_replace_callback(    //执行一个正则表达式搜索并且使用一个回调进行替换
            '/./u',
            function (array $match) {
                return strlen($match[0]) >= 4 ? '' : $match[0];
            },
            $str);

        return $str;
    }

    /**
     * Notes: 二维数组分页
     * User: Sen
     * DateTime: 2020/7/1 15:50
     * Return:
     *  $arr 二维数组 $p  页数 $count 每页多少条
     */
    public function arr_page($arr, $p, $count)
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
     * @param $data  二维数组
     * @param $id  id字段
     * @param $parent_id 父级id字段
     * @return array
     */
    public function generateTree($data, $id, $parent_id)
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
     * Notes: 根据某一个字段分组二维
     * User: Sen
     * DateTime: 2020/7/8 14:16
     * Return:  {dataArr:需要分组的数据；keyStr:分组依据}
     */
    public function dataGroup($dataArr, $keyStr)
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
    public function arraySort($array,$keys,$sort='asc') {
        $newArr = $valArr = array();
        foreach ($array as $key=>$value) {
            $valArr[$key] = $value[$keys];
        }
        ($sort == 'asc') ?  asort($valArr) : arsort($valArr);
        reset($valArr);
        foreach($valArr as $key=>$value) {
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
    public function DiffDate($date1, $date2) {
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
}