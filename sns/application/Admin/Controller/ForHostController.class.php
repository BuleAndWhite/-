<?php
/**
 * @date：2018年1月12日15:08:23
 * @content：站内信
 * @User：刘柏林
 */

namespace Admin\Controller;

use Common\Controller\AdminbaseController;

class ForHostController extends AdminbaseController
{
    protected $posts_model;
    protected $user_model;
    protected $circle_type_model;
    protected $master_model;//弃用
    protected $forum_model;//专题定义
    protected $forum_post_model;//专题定义
    protected $master_apply_model;
    protected $post_digg_model;
    protected $post_reply_model;
    protected $reply_model;
    protected $message_content_model;
    protected $message_model;
    protected $for_host_model;

    function _initialize()
    {
        parent::_initialize();
        $this->user_model = D("Common/Users");;
        $this->post_digg_model = D("Common/PostDigg");
        $this->forum_model = D("Common/Forum");
        $this->forum_post_model = D("Common/ForumPost");
        $this->message_content_model = D("Common/MessageContent");
        $this->message_model = D("Common/Message");
        $this->for_host_model = D("Common/ForHost");
    }

    /**
     * @date：2018年1月12日15:08:23
     * @content：全部帖子
     * @User：刘柏林
     */
    public function index()
    {

        $this->_lists();
        $this->display();
    }

    /**
     * Notes:查看设备绑定状态
     * User: Sen
     * Date: 2019-6-27 0027
     * Time: 14:36
     * Return:
     */
    public function check_device()
    {
        $this->device_list();
        $this->display();
    }

    /**
     * Notes: 绑定机器信息
     * User: Sen
     * Date: 2019-7-2 0002
     * Time: 18:02
     * Return:
     */
    public function owner_information()
    {
        $this->ownerInformation();
        $this->display();
    }

    /**
     * @date 2018年1月12日15:08:23
     * @content 帖子回收站
     * @User：刘柏林
     */
    public function trash()
    {
        $this->trashList();
        $this->display();
    }

    /**
     * @date：2018年1月12日20:06:08
     * @param ：首页推荐帖子
     * @User：刘柏林
     */
    public function home_post()
    {
        $this->homePostList();
        $this->display();
    }

    /**
     * @date：2018年1月13日18:04:07
     * @param ：圈子分类
     * @User：刘柏林
     */
    public function circle_type()
    {
        $this->circleTypeList();
        $this->display();
    }

    /**
     * @date：2018年1月13日18:04:07
     * @param ：专题列表
     * @User：刘柏林
     */
    public function circle_list()
    {
        $this->circleList();
        $this->display();
    }

    /**
     * @date：2018年1月13日18:04:07
     * @param ：圈主列表
     * @User：刘柏林
     */
    public function circle_del()
    {
        $this->circleDelList();
        $this->display();
    }

    /**
     * @date：2018年1月13日18:04:07
     * @param ：圈主推荐
     * @User：刘柏林
     */
    public function circle_recommend()
    {
        $this->circleDelRecommend();
        $this->display();
    }

    /**
     * @date：2018年1月14日22:05:33
     * @param ：添加圈主
     * @User：刘柏林
     */
    public function add()
    {
        $this->display();
    }

    /**
     * @date：2018年1月14日22:05:33
     * @param ：贴吧评论
     * @User：刘柏林
     */
    public function post_reply()
    {
        $this->postReplyList();
        $this->display();
    }

    /**
     * @date：2018年1月18日23:20:34
     * @param ：添加帖子
     * @User：刘柏林
     */
    public function circle_add()
    {

        $this->assign("forumId", $_GET['id']);
        $this->display();
    }

    /**
     * @date：2018年1月21日21:08:02
     * @param 朋友圈类型
     * @User：刘柏林
     */
    public function select()
    {
        $id = $_GET["id"];
        $SofRes = $this->for_host_model->where(array("id" => $id))->find();
        $this->ajaxReturn(array("info" => $SofRes['conten']));
    }

    /**
     * @date 2018年1月12日15:08:11
     * @content 站内信管理内容
     * @User：刘柏林
     */
    private function _lists()
    {

        $where_ands = array("o.isdel != 1 ");
        $fields = array(
            'start_time' => array("field" => "o.time", "operator" => ">"),
            'end_time' => array("field" => "o.time", "operator" => "<"),
            'keyword' => array("field" => "u.name", "operator" => "like")
        );

        if (IS_POST) {
            foreach ($fields as $param => $val) {
                if (isset($_POST[$param]) && !empty($_POST[$param])) {
                    $operator = $val['operator'];
                    $field = $val['field'];
                    $_GET[$param] = $_POST[$param];

                    if (!empty($_POST['start_time']) && !empty($_POST['end_time'])) {
                        $get = $param == "start_time" || $param == "end_time" ? $_POST[$param] : $_POST[$param];
                    } else {

                        $get = $_POST[$param];
                    }

                    if ($operator == "like") {
                        $get = "%$get%";
                    }
                    array_push($where_ands, "$field $operator '$get'");
                }
            }
        } else {
            foreach ($fields as $param => $val) {
                if (isset($_GET[$param]) && !empty($_GET[$param])) {
                    $operator = $val['operator'];
                    $field = $val['field'];
                    if (!empty($_GET['start_time']) && !empty($_GET['end_time'])) {
                        $get = $param == "start_time" || $param == "end_time" ? $_GET[$param] : $_GET[$param];
                    } else {
                        $get = $_GET[$param];
                    }
                    if ($operator == "like") {
                        $get = "%$get%";
                    }
                    array_push($where_ands, "$field $operator '$get'");
                }
            }
        }
        $user_id = get_current_admin_id();
        $userOne = M("users")->where(array("id" => $user_id))->find();
//        array_push($where_ands, " u1.did = ".'"'.$userOne['did'].'"');
        $where = join(" and ", $where_ands);


        $count = M('user')
            ->alias("u")
            ->join("right join  __FOR_HOST__ o on u.unionid = o.unionid")
            ->where($where)
            ->count();
        $page = $this->page($count, 15);
        $forumPostList = M('user')
            ->alias("u")
            ->join("right join   __FOR_HOST__ o on u.unionid = o.unionid")
            ->field("u.name username,u.status stu,u.id user_id,u.parent_did,u.is_vip,u.sn,o.*")
            ->where($where)
            ->order('o.time desc')
            ->limit($page->firstRow . ',' . $page->listRows)
            ->select();


        $this->assign("page", $page->show('Admin'));
        unset($_GET[C('VAR_URL_PARAMS')]);
        $this->assign("formget", $_GET);
        $id = get_current_admin_id();
        $this->assign('parent_id', $id);
        $this->assign("messageContentList", $forumPostList);
    }


    /**
     * @date：2018年1月12日18:35:09
     * @param ：排序
     * @User：刘柏林
     */
    public function listorders()
    {
        $status = parent::_listorders($this->forum_model);
        if ($status) {
            $this->success("排序更新成功！");
        } else {
            $this->error("排序更新失败！");
        }
    }

    /**
     * @date：2018年1月12日18:35:09
     * @param ：帖子
     * @User：刘柏林
     */
    public function listOrdersForumPost()
    {
        $status = parent::_listorders($this->forum_post_model);
        if ($status) {
            $this->success("排序更新成功！");
        } else {
            $this->error("排序更新失败！");
        }
    }

    /**
     * @date：2018年1月12日18:35:09
     * @param ：排序
     * @User：刘柏林
     */
    public function sortList()
    {
        $status = parent::_listorders($this->circle_type_model);
        if ($status) {
            $this->success("排序更新成功！");
        } else {
            $this->error("排序更新失败！");
        }
    }

    /**
     * @date：2018年1月12日18:36:07
     * @param 删除
     * @User：刘柏林
     */
    public function delete()
    {
        if (isset($_GET['id'])) {
            $tid = intval(I("get.id"));
            $data['isdel'] = 1;//设为伪删除
            if ($this->for_host_model->where("id=$tid")->save($data)) {
                $this->success("删除成功！");
            } else {
                $this->error("删除失败！");
            }
        }
        if (isset($_POST['ids'])) {
            $tids = join(",", $_POST['ids']);
            $data['isdel'] = 1;//设为伪删除
            if ($this->for_host_model->where("id in ($tids)")->save($data)) {
                $this->success("删除成功！");
            } else {
                $this->error("删除失败！");
            }
        }
    }


    /**
     * @date：2018年1月14日18:05:06
     * @param ：添加圈子
     * @User：刘柏林
     */
    public function circleAdd()
    {
        $circleType = M("circle_type")->select();
        $this->assign("circleType", $circleType);
    }

    /**
     * @date：2018年1月14日18:05:06
     * @param ：添加公告
     * @User：刘柏林
     */
    public function messageAdd()
    {
        if (IS_POST) {
            $data = I('post');

            $data["create_time"] = strtotime($data['create_time']);

            $data['from_id'] = get_current_admin_id();
            $data['args'] = "all";
            $rs = $this->message_content_model->add($data);
            if ($rs !== false) {
                $this->success("保存成功！");
                exit;
            } else {
                $this->error("保存失败！");
                exit;
            }
        }

        $this->display();
    }


    /**
     * @date：2018年1月14日16:12:29
     * @param 站内信审核
     * @User：刘柏林
     */
    public function circleCheck()
    {
        if (isset($_GET['ids']) && $_GET["check"]) {
            $data["status"] = 2;
            $tid = $_GET['ids'];
            $user_id = $_GET['user_id'];
            $users_id = $_GET['users_id'];
            $parent_did = $_GET['parent_did'];
            $uis_vip = $_GET['is_vip'];
            $createNoncestrs = "KF_" . $this->createNoncestrs();
            $userOne = M("user")->where(array("id" => $users_id))->find();
            $datas['is_vip'] = $uis_vip;
            $times = "";
            switch ($uis_vip) {
                case 1:
                    $times = date("Y-m-d H:i:s", strtotime("+1 day"));
                    break;
                case 2:
                    $times = date("Y-m-d H:i:s", strtotime("+6 month"));
                    break;
                case 3:
                    $times = date("Y-m-d H:i:s", strtotime("+1 year"));
                    break;
                case 4:
                    $times = date("Y-m-d H:i:s", strtotime("+3 year"));
                    break;
                case 5:
                    $times = date("Y-m-d H:i:s", strtotime("+5 year"));
                    break;
                case 7:
                    $times = date("Y-m-d H:i:s", strtotime("+3 year"));
                    break;
                case 8:
                    $times = date("Y-m-d H:i:s", strtotime("+3 year"));
                    break;
            }

            if ($userOne['is_vip'] == 2 || $userOne['is_vip'] == 3 || $userOne['is_vip'] == 4 || $userOne['is_vip'] == 5 || $userOne['is_vip'] == 7 || $userOne['is_vip'] == 8) {
                $datas['time_maturity'] = date("Y-m-d H:i:s", (strtotime($userOne['time_maturity']) - strtotime(date("Y-m-d H:i:s", time()))) + strtotime($times));
            } else {
                $datas['time_maturity'] = $times;
                $datas['time_start'] = date("Y-m-d H:i:s", time());
            }
            $datas['is_pays'] = 1;
            $datas['status'] = 1;
            $datas['sn'] = $createNoncestrs;
            $datas['is_backstage'] = 1;


            M("user")->where(array("id" => $user_id))->save($datas);


            $userOne = M("user")->where(array("id" => $user_id))->find();

            $usersOne = M("users")->where(array("did" => $parent_did))->find();

            $date = array();
            if ($uis_vip == 7) {
                $role_id = 10;
                $date['did'] = $createNoncestrs;
                $date['user_type'] = 3;
                $date['user_status'] = 1;

            } else if ($uis_vip == 8) {
                $role_id = 10;
                $date['did'] = $createNoncestrs;
                $date['user_type'] = 3;
                $date['user_status'] = 1;

            }

            $date['parent'] = $usersOne['id'];
            $date['wx_id'] = $user_id;
            $date['did'] = $createNoncestrs;
            $date['user_login'] = $userOne['phone'];
            $date['user_pass'] = sp_password("kf123456");

            $results = M("users")->add($date);

            $dates['q_r_code'] = $this->owner($createNoncestrs);
            $dates['password_code'] = $this->passwords($createNoncestrs);
            $result = M("users")->where(array("id" => $results))->save($dates);

            if ($result !== false) {
                $role_user_model = M("RoleUser");
                $role_user_model->add(array("role_id" => $role_id, "user_id" => $results));
                $this->success("审核成功！");
            }

        }
        if (isset($_GET['ids']) && $_GET["uncheck"]) {
            $data["status"] = 3;
            $tid = $_GET['ids'];

            if ($this->for_host_model->where(array("id" => $tid))->save($data) !== false) {
                $this->success("已驳回！");
            } else {
                $this->error("失败！");
            }
        }
    }

    /**
     * 推广码
     * 刘北林
     * 2018年9月5日12:09:20
     */
    public function owner($did)
    {
        vendor("phpqrcode.phpqrcode");

        $hello = explode(',', $did);

        $newarray = "";
        if (count($hello)) {
            foreach ($hello as $key => $v) {

                $uL = M("users")->where(array("did" => $hello[$key]))->find();
                if ($uL['user_type'] == 2) {
                    $owner_id = $hello[$key];
                    $operation = $hello[$key];
                } elseif ($uL['user_type'] == 3) {
                    $userOne = M("user")->where(array("sn" => $hello[$key]))->find();
                    $owner_id = $hello[$key];
                    $operation = $userOne['operation'];
                } else {
                    $userOne = M("user")->where(array("sn" => $hello[$key]))->find();
                    $owner_id = $userOne['parent_did'];
                    $operation = $userOne['operation'];
                }
                $level = 1;
                $size = 6;
                $errorCorrectionLevel = intval($level);//容错级别
                $matrixPointSize = intval($size);//生成图片大小
                $qrurl = "https://bolon.kuaifengpay.com/Api/Index/authorized?scene_str=" . $hello[$key] . "&user_type=" . $uL['user_type'] . "&owner_id=" . $owner_id . "&operation=" . $operation;
                //保存位置
                $path = "public/kuaifeng/";
                // 生成的文件名
                $fileName = $path . date('Y-m-d', time()) . "-" . $this->createNoncestrs() . '.png';

                //生成二维码图片
                $object = new \QRcode();
                $object->png($qrurl, $fileName, $errorCorrectionLevel, $matrixPointSize, 2);
                ob_clean();
                $QR = $fileName;//已经生成的原始二维码图
                $logo = "public/images/code.png";//准备好的logo图片
                if (file_exists($logo)) {
                    $QR = imagecreatefromstring(file_get_contents($QR));
                    $logo = imagecreatefromstring(file_get_contents($logo));
                    $QR_width = imagesx($QR);//二维码图片宽度
                    $QR_height = imagesy($QR);//二维码图片高度
                    $logo_width = imagesx($logo);//logo图片宽度
                    $logo_height = imagesy($logo);//logo图片高度
                    $logo_qr_width = $QR_width / 5;
                    $scale = $logo_width / $logo_qr_width;
                    $logo_qr_height = $logo_height / $scale;
                    $from_width = ($QR_width - $logo_qr_width) / 2;
                    //重新组合图片并调整大小
                    imagecopyresampled($QR, $logo, $from_width, $from_width, 0, 0, $logo_qr_width,
                        $logo_qr_height, $logo_width, $logo_height);
                    //输出图片
                    header("Content-type: image/png");
                    // dump($qrcode_path);
                    $qrcode_path = $path . date('Y-m-d', time()) . "-" . $this->createNoncestrs() . '.png';
                    M("users")->where(array("did" => $hello[$key]))->save(array("q_r_code" => "https://bolon.kuaifengpay.com/" . $qrcode_path));
                    imagepng($QR, $qrcode_path);
                    $newarray = "https://bolon.kuaifengpay.com/" . $qrcode_path;
//                    $newarray[$key]['q_r_code'] = "https://bolon.kuaifengpay.com/" . $qrcode_path;
//                    $newarray[$key]['did'] = $hello[$key];
                }
            }
        }
        return $newarray;
    }

    /**
     * 口令码
     * 刘北林
     * 2018年9月5日12:09:20
     */
    public function passwords($did)
    {
        vendor("phpqrcode.phpqrcode");

        $hello = explode(',', $did);

        $newarray = "";
        if (count($hello)) {
            foreach ($hello as $key => $v) {

                $uL = M("users")->where(array("did" => $hello[$key]))->find();
                if ($uL['user_type'] == 2 || $uL['user_type'] == 3) {
                    $owner_id = $hello[$key];
                    $operation = $hello[$key];
                } else {
                    $userOne = M("user")->where(array("sn" => $hello[$key]))->find();
                    $owner_id = $userOne['parent_did'];
                    $operation = $userOne['operation'];
                }
                $level = 1;
                $size = 6;
                $errorCorrectionLevel = intval($level);//容错级别
                $matrixPointSize = intval($size);//生成图片大小
                $qrurl = "https://bolon.kuaifengpay.com/Api/Index/authorizeds?scene_str=" . $hello[$key] . "&user_type=" . $uL['user_type'] . "&owner_id=" . $owner_id . "&operation=" . $operation;

                //保存位置
                $path = "public/kuaifeng/";
                // 生成的文件名
                $fileName = $path . date('Y-m-d', time()) . "-" . $this->createNoncestrs() . '.png';

                //生成二维码图片
                $object = new \QRcode();
                $object->png($qrurl, $fileName, $errorCorrectionLevel, $matrixPointSize, 2);
                ob_clean();
                $QR = $fileName;//已经生成的原始二维码图
                $logo = "public/images/code.png";//准备好的logo图片
                if (file_exists($logo)) {
                    $QR = imagecreatefromstring(file_get_contents($QR));
                    $logo = imagecreatefromstring(file_get_contents($logo));
                    $QR_width = imagesx($QR);//二维码图片宽度
                    $QR_height = imagesy($QR);//二维码图片高度
                    $logo_width = imagesx($logo);//logo图片宽度
                    $logo_height = imagesy($logo);//logo图片高度
                    $logo_qr_width = $QR_width / 5;
                    $scale = $logo_width / $logo_qr_width;
                    $logo_qr_height = $logo_height / $scale;
                    $from_width = ($QR_width - $logo_qr_width) / 2;
                    //重新组合图片并调整大小
                    imagecopyresampled($QR, $logo, $from_width, $from_width, 0, 0, $logo_qr_width,
                        $logo_qr_height, $logo_width, $logo_height);
                    //输出图片
                    header("Content-type: image/png");
                    // dump($qrcode_path);
                    $qrcode_path = $path . date('Y-m-d', time()) . "-" . $this->createNoncestrs() . '.png';
                    M("users")->where(array("did" => $hello[$key]))->save(array("password_code" => "https://bolon.kuaifengpay.com/" . $qrcode_path));
                    imagepng($QR, $qrcode_path);
                    $newarray = "https://bolon.kuaifengpay.com/" . $qrcode_path;
//                    $newarray[$key]['password_code'] = "https://bolon.kuaifengpay.com/" . $qrcode_path;
//                    $newarray[$key]['did'] = $hello[$key];
//                    $newarray[$key]['count'] = $userOne = M("password")->where(array("did" => $hello[$key], "is_receive" => 1))->count();
                }
            }
        }

        return $newarray;
    }

    /**
     *  作用：产生随机字符串，不长于32位
     */
    public
    function createNoncestrs($length = 8)
    {
        $chars = "0123456789abcdefghijklmnopqrstuvwxyz";
        $str = "";
        for ($i = 0; $i < $length; $i++) {
            $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
        }

        return $str;
    }

    /**
     * Notes:机器绑定(信息)
     * User: Sen
     * Date: 2019-6-27 0027
     * Time: 17:49
     * Return:
     */
    public function device_list()
    {
        $where_ands = array();
        $fields = array(
            'start_time' => array("field" => "b.time", "operator" => ">"),
            'end_time' => array("field" => "b.time", "operator" => "<"),
            'keyword' => array("field" => "b.device_code", "operator" => "=")
        );

        if (IS_POST) {
            foreach ($fields as $param => $val) {
                if (isset($_POST[$param]) && !empty($_POST[$param])) {
                    $operator = $val['operator'];
                    $field = $val['field'];
                    $_GET[$param] = $_POST[$param];

                    if (!empty($_POST['start_time']) && !empty($_POST['end_time'])) {
                        $get = $param == "start_time" || $param == "end_time" ? $_POST[$param] : $_POST[$param];
                    } else {

                        $get = $_POST[$param];
                    }

                    if ($operator == "like") {
                        $get = "%$get%";
                    }
                    array_push($where_ands, "$field $operator '$get'");
                }
            }
        } else {
            foreach ($fields as $param => $val) {
                if (isset($_GET[$param]) && !empty($_GET[$param])) {
                    $operator = $val['operator'];
                    $field = $val['field'];
                    if (!empty($_GET['start_time']) && !empty($_GET['end_time'])) {
                        $get = $param == "start_time" || $param == "end_time" ? $_GET[$param] : $_GET[$param];
                    } else {
                        $get = $_GET[$param];
                    }
                    if ($operator == "like") {
                        $get = "%$get%";
                    }
                    array_push($where_ands, "$field $operator '$get'");
                }
            }
        }
        $user_id = get_current_admin_id();
        $where = join(" and ", $where_ands);
        $lists = M('user')
            ->alias('a')
            ->join('__DEVICE_BINDING__ b on a.unionid = b.unionid')
            ->where($where)
            ->count();
        $page = $this->page($lists, 15);
        //获取所有设备信息(包括机主)
        $res = M('user')
            ->alias('a')
            ->join('__DEVICE_BINDING__ b on a.unionid = b.unionid')
            ->where($where)
            ->order("time DESC")
            ->limit($page->firstRow . ',' . $page->listRows)
            ->field('a.unionid,a.name wxname,b.id,b.device_code,b.device_name,b.time,b.phone,b.name,b.type,b.bingding_status,b.device_name,b.is_delete,b.owner_status,b.expiration_time,b.address_z,b.address_s')
            ->select();
        foreach ($res as $k => $val) {
            $res[$k]['conditionFinish'] = $this->condition($val['id'], $val['unionid']);
            $res[$k]['conditionRemaining'] = 365 - $res[$k]['conditionFinish'];
        }
        $this->assign("messageContentList", $res);
        $this->assign("page", $page->show('Admin'));
    }

    /**
     * Notes: 绑定机器(通过审核)
     * User: Sen
     * Date: 2019-6-27 0027
     * Time: 17:50
     * Return:
     */
    public function approved()
    {
        $id = $_GET['id'];
        $unionid = $_GET['unionid'];
        if (empty($id) || empty($unionid)) {
            $this->error('非法错误');
        }

        $userinfo = M("user")->where(array('unionid' => $unionid))->find();
        //拿去设备id
        if ($userinfo['multi_equipment']) {
            $machine = implode(',', array($userinfo['multi_equipment'], $id));
        } else {
            $machine = $id;
        }
        //不是会员
        if (empty($userinfo['time_start'])) {
            $data = array(
                'multi_equipment' => $machine,
                'is_vip' => 8,
                'time_start' => date('Y-m-d h:i:s'),
                'time_maturity' => date('Y-m-d H:i:s', strtotime("+3 year")),
            );
            //已成为会员
        } else {
            //会员在有效期
            if ($userinfo['time_maturity'] > date('Y-m-d h:i:s')) {
                $date = date("Y-m-d H:i:s", strtotime("+3 years", strtotime($userinfo['time_maturity'])));
                $data = array(
                    'multi_equipment' => $machine,
                    'is_vip' => 8,
                    'time_maturity' => $date,
                );
                //会员过期
            } else {
                $data = array(
                    'multi_equipment' => $machine,
                    'is_vip' => 8,
                    'time_maturity' => date('Y-m-d H:i:s', strtotime("+3 year")),
                );
            }
        }

        $res = M("device_binding")
            ->where(array('id' => $id))
            ->save(array(
                'bingding_status' => '2',
                'owner_status' => '1',
                'binding_time' => date("Y-m-d H:i:s", time()),
                'untied_time' => date('Y-m-d H:i:s', strtotime("+3 year")),
                'expiration_time' => date('Y-m-d H:i:s', strtotime("+3 year")),
            ));

        if ($res) {
            $userinfo = M("user")->where(array('unionid' => $unionid))->save($data);
            if ($userinfo) {
                $this->success('审核成功');
            } else {
                $this->error("服务器内部错误");
            }
        } else {
            $this->error("审核失败");
        }

    }

    /**
     * Notes: 绑定机器(驳回审核)
     * User: Sen
     * Date: 2019-6-28 0028
     * Time: 9:50
     * Return:
     */
    public function turnDown()
    {
        $id = $_GET['id'];
        $res = M("device_binding")
            ->where(array('id' => $id))
            ->save(array('bingding_status' => '3',));

        if ($res) {
            $this->success("驳回成功");
        } else {
            $this->error("驳回失败");
        }
    }

    /**
     * Notes: 机器信息(请求被驳回)
     * User: Sen
     * Date: 2019-6-27 0027
     * Time: 17:53
     * Return:
     */
    public function reset()
    {
        $id = $_GET['id'];
        $res = M("device_binding")
            ->where(array('id' => $id))
            ->save(array('is_delete' => '1'));
        if ($res) {
            $this->success("取消驳回成功");
        } else {
            $this->error("取消驳回失败");
        }
    }

    /**
     * Notes:  机器信息(解绑)
     * User: Sen
     * Date: 2019-7-1 0001
     * Time: 11:53
     * Return:
     */
    public function untied()
    {
        $id = $_GET['id'];
        $res = M("device_binding")
            ->where(array('id' => $id))
            ->save(array('owner_status' => '2', 'untied_time' => date("Y-m-d H:i:s", time())));
        if ($res) {
            $this->success("解绑成功");
        } else {
            $this->error("解绑失败");
        }
    }

    /**
     * Notes: 机主信息
     * User: Sen
     * Date: 2019-7-3 0003
     * Time: 18:58
     * Return:
     */
    public function ownerInformation()
    {


        $where_ands = array();
        $fields = array(
            'start_time' => array("field" => "a.time", "operator" => ">"),
            'end_time' => array("field" => "a.time", "operator" => "<"),
            'keyword' => array("field" => "a.device_code", "operator" => "="),
            'status' => array("field" => "a.owner_status", "operator" => "=")
        );

        if (IS_POST) {
            foreach ($fields as $param => $val) {
                if (isset($_POST[$param]) && !empty($_POST[$param])) {
                    $operator = $val['operator'];
                    $field = $val['field'];
                    $_GET[$param] = $_POST[$param];

                    if (!empty($_POST['start_time']) && !empty($_POST['end_time'])) {
                        $get = $param == "start_time" || $param == "end_time" ? $_POST[$param] : $_POST[$param];
                    } else {

                        $get = $_POST[$param];
                    }

                    if ($operator == "like") {
                        $get = "%$get%";
                    }
                    array_push($where_ands, "$field $operator '$get'");
                }
            }
        } else {
            foreach ($fields as $param => $val) {
                if (isset($_GET[$param]) && !empty($_GET[$param])) {
                    $operator = $val['operator'];
                    $field = $val['field'];
                    if (!empty($_GET['start_time']) && !empty($_GET['end_time'])) {
                        $get = $param == "start_time" || $param == "end_time" ? $_GET[$param] : $_GET[$param];
                    } else {
                        $get = $_GET[$param];
                    }
                    if ($operator == "like") {
                        $get = "%$get%";
                    }
                    array_push($where_ands, "$field $operator '$get'");
                }
            }
        }

        $where = join(" and ", $where_ands);
        if (empty($where)) {
            $where = array(
                'a.owner_status' => array('in', array(1, 2))
            );
        }
        $count = M("device_binding")
            ->alias('a')
            ->join('__USER__ b ON a.unionid = b.unionid')
            ->where($where)
            ->count();
        $page = $this->page($count['count'], 15);
        $res = M("device_binding")
            ->alias('a')
            ->join('__USER__ b ON a.unionid = b.unionid')
            ->where($where)
            ->limit($page->firstRow . ',' . $page->listRows)
            ->field('a.type,a.unionid,a.id,b.name,a.device_code,b.avatar,a.time,a.phone,a.owner_status,a.proportion,a.address_z,a.address_s')
            ->order("time desc")
            ->select();

        $money = array();
        foreach ($res as $k => $val) {
            $res[$k]['amountownertotal'] = $this->amountMachineTotal($val['id'], $val['unionid']);
            $res[$k]['amountownertoday'] = $this->amountMachineSingle($val['id'], $val['unionid']);
            $res[$k]['memberownertotal'] = $this->memberMachineTotal($val['id'], $val['unionid']);
            $res[$k]['memberownertoday'] = $this->memberMachineSingle($val['id'], $val['unionid']);
            $money['amounttotal'] += $this->amountMachineTotal($val['id'], $val['unionid']);
            $money['amounttoday'] += $this->amountMachineSingle($val['id'], $val['unionid']);
            $money['membertotal'] += $this->memberMachineTotal($val['id'], $val['unionid']);
            $money['membertoday'] += $this->memberMachineSingle($val['id'], $val['unionid']);
        }
        $this->assign("page", $page->show('Admin'));
        $this->assign("money", $money);
        $this->assign("messageContentList", $res);


    }

    /**
     * Notes: 单个机器总收入      -------------取值
     * User: Sen
     * Date: 2019-7-2 0002
     * Time: 20:01
     * Return:
     */
    public
    function amountMachineTotal($id, $unionid)
    {

        $where = array(
            'a.id' => $id,
            'a.owner_status' => array('in', array(1, 2)),
            'a.unionid' => $unionid,
            'c.success' => 1,
            'c.order_type' => array('in', array(-1, 2)),
            'c.platform' => 1
        );
        $where['_string'] = 'c.time between a.binding_time and a.untied_time';
        $res = M('device_binding')
            ->alias('a')
            ->join("__USER__ b ON a.device_code = b.machine")
            ->join("__ORDER__ c ON b.id = c.uid")
            ->where($where)
            ->field("a.proportion,c.total_fee")
            ->select();
        $total = 0;
        foreach ($res as $res_key => $val) {
            $total += (floor($val['total_fee'] * 100) / 100) * $val['proportion'] * 0.01;
        }
        $total = (floor($total * 100) / 100);
        return $total;


    }

    /**
     * Notes: 单个机器今日的收入   ---------------取值
     * User: Sen
     * Date: 2019-7-1 0001
     * Time: 20:04
     * Return:金额
     */
    public
    function amountMachineSingle($id, $unionid)
    {
        if (empty($id) || empty($unionid)) {
            $this->apiError('非法错误');
        }

        $beginToday = date("Y-m-d H:i:s", mktime(0, 0, 0, date('m'), date('d'), date('Y')));
        $endToday = date("Y-m-d H:i:s", mktime(0, 0, 0, date('m'), date('d') + 1, date('Y')) - 1);
        //分成比
        $where = array(
            'a.id' => $id,
            'a.owner_status' => array('in', array(1, 2)),
            'a.unionid' => $unionid,
            'c.platform' => 1,
            'c.success' => 1,
            'c.order_type' => array('in', array(-1, 2)),
            'c.time' => array('between', "$beginToday,$endToday")
        );
        $where['_string'] = 'c.time between a.binding_time and a.untied_time';
        $res = M('device_binding')
            ->alias('a')
            ->join("__USER__ b ON a.device_code = b.machine")
            ->join("__ORDER__ c ON b.id = c.uid")
            ->where($where)
            ->field("a.proportion,c.total_fee")
            ->select();

        $total = 0;
        foreach ($res as $res_key => $val) {
            $total += (floor($val['total_fee'] * 100) / 100) * $val['proportion'] * 0.01;
        }
        $total = (floor($total * 100) / 100);
        return $total;

    }

    /**
     * Notes: 单个机器总会员   ---------------取值
     * User: Sen
     * Date: 2019-7-1 0001
     * Time: 20:04
     * Return:金额
     */
    public
    function memberMachineTotal($id, $unionid)
    {
        if (empty($id) || empty($unionid)) {
            $this->apiError('非法错误');
        }

        $where = array(
            'a.id' => $id,
            'a.owner_status' => array('in', array(1, 2)),
            'a.unionid' => $unionid,
        );
        $where['_string'] = 'b.time between a.binding_time and a.untied_time';
        $res = M('device_binding')
            ->alias('a')
            ->join("__USER__ b ON a.device_code = b.machine")
            ->where($where)
            ->count();

        return $res;

    }

    /**
     * Notes: 单个机器今日的会员   ---------------取值
     * User: Sen
     * Date: 2019-7-1 0001
     * Time: 20:04
     * Return:金额
     */
    public
    function memberMachineSingle($id, $unionid)
    {
        if (empty($id) || empty($unionid)) {
            $this->apiError('非法错误');
        }

        $beginToday = date("Y-m-d H:i:s", mktime(0, 0, 0, date('m'), date('d'), date('Y')));
        $endToday = date("Y-m-d H:i:s", mktime(0, 0, 0, date('m'), date('d') + 1, date('Y')) - 1);

        $where = array(
            'a.id' => $id,
            'a.owner_status' => array('in', array(1, 2)),
            'a.unionid' => $unionid,
            'b.time' => array('between', "$beginToday,$endToday")
        );
        $where['_string'] = 'b.time between a.binding_time and a.untied_time';
        $res = M('device_binding')
            ->alias('a')
            ->join("__USER__ b ON a.device_code = b.machine")
            ->where($where)
            ->count();

        return $res;

    }

    /**
     * Notes: 激活虚拟主机
     * User: Sen
     * Date: 2019-9-3 0003
     * Time: 14:55
     * Return:
     */
    public function activation()
    {
        $id = $_GET['id'];
        $res = M("device_binding")
            ->where(array('id' => $id))
            ->save(array('type' => '1'));
        if ($res) {
            $this->success("激活成功");
        } else {
            $this->error("激活失败");
        }
    }

    public
    function condition($id, $unionid)
    {

        $where = array(
            'a.id' => $id,
            'a.owner_status' => array('in', array(1, 2)),
            'a.unionid' => $unionid,
            'c.success' => 1,
            'c.order_type' => -1,
            'c.platform' => 1,
            'c.type' => array('in', array(3, 5))
        );
        $where['_string'] = 'c.time between a.binding_time and a.untied_time';
        $res = M('device_binding')
            ->alias('a')
            ->join("__USER__ b ON a.device_code = b.machine")
            ->join("__ORDER__ c ON b.id = c.uid")
            ->where($where)
            ->field("a.proportion,c.total_fee")
            ->count();

        return $res;


    }
}