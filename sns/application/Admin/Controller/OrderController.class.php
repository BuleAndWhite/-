<?php
// +----------------------------------------------------------------------
// | AnzaiSoft [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2015-2016 http://www.anzaisoft.net All rights reserved.
// +----------------------------------------------------------------------
// | Author: ZhangQingYuan <767684610@qq.com>
// +----------------------------------------------------------------------
namespace Admin\Controller;

use Common\Controller\AdminbaseController;

class OrderController extends AdminbaseController
{
    protected $order_model;

    function _initialize()
    {
        parent::_initialize();
        $this->order_model = D("Common/Order");
    }

    /**
     * 全部订单
     */
    public function index()
    {
        //待付款，待发货，已完成
        $this->_lists();
        $this->display();
    }

    /**
     * 全部订单
     */
    public function recharge()
    {
        $where_ands = array(" o.platform=1 and o.order_type in(2,-1) ");


        $fields = array(
            'status' => array("field" => "o.status", "operator" => "="),
            'start_time' => array("field" => "o.time", "operator" => ">"),
            'end_time' => array("field" => "o.time", "operator" => "<"),
            'order_id' => array("field" => "o.prepay_id", "operator" => "=")
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
                        $get = $param == "start_time" || $param == "end_time" ? strtotime($_GET[$param]) : $_GET[$param];
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
        /*
        $count = M()->table('__ORDER__ a,__ORDER_TICKET__ b,__TICKET__ c')
            ->where(array('a.order_id = b.order_id', 'b.ticket_id = c.ticket_id', $params))
            ->count();
        $page = $this->page($count, 20);
        $order_list = M()->table('__ORDER__ a,__ORDER_TICKET__ b,__TICKET__ c')
                ->where(array('a.order_id = b.order_id', 'b.ticket_id = c.ticket_id', $params))
                ->order('a.create_time desc')
                ->field('a.id,a.uid,c.name,a.order_id,a.receiver_name,a.receiver_mobile,a.create_time,a.status')
                ->limit($page->firstRow . ',' . $page->listRows)
                ->select();
        */
        $count = M('user')
            ->alias("u")
            ->join("right join  __ORDER__ o on u.id = o.uid")
            ->where($where)
            ->count();
        $page = $this->page($count, 20);
        $order_list = M('user')
            ->alias("u")
            ->join("right join   __ORDER__ o on u.id = o.uid")
            ->field("u.name as uname,u.status stu,u.id user_id,u.parent_did,u.is_vip,o.*")
            ->where($where)
            ->order('o.time desc')
            ->limit($page->firstRow . ',' . $page->listRows)
            ->select();

        foreach ($order_list as $i => $value) {
            $order_list[$i]['total_fee'] = $value['total_fee'] * 0.01;
        }
//
//
//        $count = M('order')->where($params)->count();
//        $page = $this->page($count, 20);
//        $order_list = M('order')
//            ->where($params)
//            ->order('update_time desc')
//            ->limit($page->firstRow . ',' . $page->listRows)
//            ->select();


        $beginToday = date("Y-m-d H:i:s", mktime(0, 0, 0, date('m'), date('d'), date('Y')));
        $endToday = date("Y-m-d H:i:s", mktime(0, 0, 0, date('m'), date('d') + 1, date('Y')) - 1);
        $wheres = array(
            "success" => 1,
            "order_type" => array("in", "-1,2"),
        );
        $wheret = array(
            "success" => 1,
            "order_type" => array("in", "-1,2"),
            "update_time" => array("between", "$beginToday,$endToday")
        );
        //计算总金额
        $amountTotal = M("order")
            ->where($wheres)
            ->sum("total_fee");
        $orderTotal = M("order")
            ->where($wheres)
            ->count();
        $amountToday = M("order")
            ->where($wheret)
            ->sum("total_fee");
        $orderToday = M("order")
            ->where($wheret)
            ->count();

        $data["A"] = $orderTotal;
        $data["B"] = floor($amountTotal) / 100;
        $data["C"] = $orderToday;
        $data["D"] = floor($amountToday) / 100;

        $this->post_data = I('post.');
        $this->assign("page", $page->show('Admin'));
        $this->assign("order_list", $order_list);
        $this->assign("data", $data);
        $this->display();
    }

    private function _lists()
    {
        $params['1'] = '1';
        if (IS_POST) {
            if (!empty($_POST['export'])) {
                $this->export();
                exit;
            }
            if (!empty($_POST['status'])) {
                $params['status'] = I('post.status');
            }
            if (!empty($_POST['order_id'])) {
                $params['order_id'] = array('like', '%' . I('order_id') . '%');
            }
            if (!empty($_POST['start_time']) && !empty($_POST['end_time'])) {
                $start_time = strtotime(I('start_time'));
                $end_time = strtotime(I('end_time'));
                $params['create_time'] = array('between', array($start_time, $end_time));
            }
            $this->post_data = I('post.');
        }
        /*
        $count = M()->table('__ORDER__ a,__ORDER_TICKET__ b,__TICKET__ c')
            ->where(array('a.order_id = b.order_id', 'b.ticket_id = c.ticket_id', $params))
            ->count();
        $page = $this->page($count, 20);
        $order_list = M()->table('__ORDER__ a,__ORDER_TICKET__ b,__TICKET__ c')
                ->where(array('a.order_id = b.order_id', 'b.ticket_id = c.ticket_id', $params))
                ->order('a.create_time desc')
                ->field('a.id,a.uid,c.name,a.order_id,a.receiver_name,a.receiver_mobile,a.create_time,a.status')
                ->limit($page->firstRow . ',' . $page->listRows)
                ->select();
        */

        $count = M('orders')->where($params)->count();
        $page = $this->page($count, 20);
        $order_list = M('orders')
            ->where($params)
            ->order('create_time desc')
            ->limit($page->firstRow . ',' . $page->listRows)
            ->select();
        $this->assign("page", $page->show('Admin'));
        $this->assign("order_list", $order_list);
    }

    //发货单
    public function delivery_list()
    {
        $this->_lists();
        $this->display();
    }

    //配货单
    public function delivery_info()
    {
        echo $this->fetch();
//     	$this->display();
    }

    //打印订单
    public function order_print()
    {
        $this->display();
    }

    //开票管理
    public function invoice()
    {
        $params['1'] = '1';
        if (IS_POST) {
            if (!empty($_POST['export'])) {
                $this->export();
                exit;
            }
            if (!empty($_POST['status'])) {
                $params['status'] = I('post.status');
            }
            if (!empty($_POST['order_id'])) {
                $params['order_id'] = array('like', '%' . I('order_id') . '%');
            }
            if (!empty($_POST['start_time']) && !empty($_POST['end_time'])) {
                $start_time = strtotime(I('start_time'));
                $end_time = strtotime(I('end_time'));
                $params['create_time'] = array('between', array($start_time, $end_time));
            }
            $this->post_data = I('post.');
        }
        $count = M('order')->where($params)->count();
        $page = $this->page($count, 20);
        $order_list = M('order')
            ->where($params)
            ->order('create_time desc')
            ->limit($page->firstRow . ',' . $page->listRows)
            ->select();
        $this->assign("page", $page->show('Admin'));
        $this->assign("order_list", $order_list);
        $this->display();
    }

    //是否开票
    public function do_invoice()
    {
        M('order')->where(array('id' => I('id')))->setField('isxuyaofapiao', I('is_invoice'));
        $this->success('操作成功！');
    }

    public function add()
    {
        if (IS_POST) {
            $data = I('options');
            if (!empty($data['id'])) {
                $rst2 = $this->weixin_options->save($data);
            } else {
                $rst2 = $this->weixin_options->add($data);
            }
            if ($rst2 !== false) {
                $this->success("保存成功！");
            } else {
                $this->error("保存失败！");
            }
        }
        if (isset($_GET['id'])) {
            $this->options = $this->weixin_options->where(array('id' => I('id')))->find();
        }
        $this->display();
    }

    public function index_post()
    {
        $data = $_POST['options'];
        if (!empty($data['id'])) {
            $rst2 = $this->weixin_options->save($data);
        } else {
            $rst2 = $this->weixin_options->add($data);
        }
        if ($rst2 !== false) {
            $this->success("保存成功！", U('Weixin/index'));
        } else {
            $this->error("保存失败！");
        }
    }

    /**
     *查看订单信息
     */
    public function detail()
    {
        !IS_AJAX && exit;
        $params['order_id'] = I('oderd_id');
        $this->order_list = M('order_ticket')->where($params)->select();
        echo $this->fetch();
    }

    /**
     *查看订单信息
     */
    public function details()
    {
        !IS_AJAX && exit;
        $params['prepay_id'] = I('oderd_id');
        $this->order_list = M('order')->where($params)->select();
        echo $this->fetch();
    }

    /**
     * 导出订单
     **/
    public function export11()
    {
        !IS_GET && exit;
        $xlsName = 'kou报表_' . date('Ymd');
        $xlsCell = array(
            array('id', '序号'),
            array('name', '门票名称'),
            array('order_id', '订单号'),
            array('username', '姓名'),
            array('mobile', '手机号'),
            array('create_time', '交易时间'),
        );
        $params['1'] = '1';
        if ($_REQUEST['status']) {
            $params['a.status'] = I('status');
        }
        if (!empty($_REQUEST['order_id'])) {
            $params['a.order_id'] = array('like', '%' . I('order_id') . '%');
        }
        if (!empty($_REQUEST['start_time']) && !empty($_REQUEST['end_time'])) {
            $start_time = strtotime(I('start_time'));
            $end_time = strtotime(I('end_time'));
            $params['a.create_time'] = array('between', "$start_time,$end_time");
        }
        $order_list = M()->table('__ORDER__ a,__ORDER_TICKET__ b,__TICKET__ c')
            ->where(array('a.order_id = b.order_id', 'b.ticket_id = c.ticket_id', $params))
            ->order('a.create_time asc')
            ->field('a.id,c.name,a.order_id,a.receiver_name,a.receiver_mobile,a.create_time,a.uid')
            ->select();
        foreach ($order_list as $k => $v) {
            $order_list[$k]['create_time'] = date('Y-m-d H:i:s', $v['create_time']);
            $order_list[$k]['username'] = get_user_info($v['uid'], 'user_nicename');
            $order_list[$k]['mobile'] = get_user_info($v['uid'], 'mobile');
            $order_list[$k]['order_id'] = ' ' . $v['order_id'];
        }
        $this->exportExcel($xlsName, $xlsCell, $order_list);
    }

    /**
     * 导出订单
     **/
    public function export()
    {
        !IS_GET && exit;
        $xlsName = '口令码报表_' . date('Ymd');
        $xlsCell = array(
            array('password', '口令码')
        );

        $where_ands = array();
        $fields = array(
            'start_time' => array("field" => "id", "operator" => ">"),
            'end_time' => array("field" => "id", "operator" => "<"),
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

        $order_list = M("password")
            ->where($where)
            ->field('password')
            ->select();

        $this->exportExcel($xlsName, $xlsCell, $order_list);
    }

    /**
     * @param $expTitle 名称
     * @param $expCellName 参数
     * @param $expTableData 内容
     * @throws \PHPExcel_Exception
     * @throws \PHPExcel_Reader_Exception
     */
    private function exportExcel($expTitle, $expCellName, $expTableData)
    {
        $xlsTitle = iconv('utf-8', 'gb2312', $expTitle);//文件名称
        $fileName = date('YmdHis');//or $xlsTitle 文件名称可根据自己情况设定
        $cellNum = count($expCellName);
        $dataNum = count($expTableData);
        vendor("PHPExcel");

        $objPHPExcel = new \PHPExcel();

        $cellName = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', 'AA', 'AB', 'AC', 'AD', 'AE', 'AF', 'AG', 'AH', 'AI', 'AJ', 'AK', 'AL', 'AM', 'AN', 'AO', 'AP', 'AQ', 'AR', 'AS', 'AT', 'AU', 'AV', 'AW', 'AX', 'AY', 'AZ');
        //$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(100);
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(15);
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(15);
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(15);
        $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(15);
        $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(22);


//                $objPHPExcel->getActiveSheet(0)->mergeCells('A1:'.$cellName[$cellNum-1].'1');//合并单元格
//                 $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A1', $expTitle.'  Export time:'.date('Y-m-d H:i:s'));
        for ($i = 0; $i < $cellNum; $i++) {
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($cellName[$i] . '2', $expCellName[$i][1]);
        }
        // Miscellaneous glyphs, UTF-8
        for ($i = 0; $i < $dataNum; $i++) {
            for ($j = 0; $j < $cellNum; $j++) {
                $objPHPExcel->getActiveSheet(0)->setCellValue($cellName[$j] . ($i + 3), $expTableData[$i][$expCellName[$j][0]]);
            }
        }
        header('pragma:public');
        header('Content-type:application/vnd.ms-excel;charset=utf-8;name="' . $xlsTitle . '.xls"');
        header("Content-Disposition:attachment;filename=$fileName.xls");//attachment新窗口打印inline本窗口打印
        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');//Excel5为xls格式，excel2007为xlsx格式
        $objWriter->save('php://output');
        exit;
    }

    /**
     * @date：2018年1月14日16:12:29
     * @param 站内信审核
     * @User：刘柏林
     */
    public function circleCheck()
    {
        if (isset($_GET['ids']) && $_GET["check"]) {


            $user_id = $_GET['user_id'];

            $parent_did = $_GET['parent_did'];
            $createNoncestrs = "KF_" . $this->createNoncestrs();
            if (M("user")->where(array("id" => $user_id))->save(array("status" => 1, "is_backstage" => 1, "sn" => $createNoncestrs)) !== false) {
                $userOne = M("user")->where(array("id" => $user_id))->find();

                $usersOne = M("users")->where(array("did" => $parent_did))->find();

                $date = array();
                if ($userOne['is_vip'] == 4) {
                    $role_id = 11;
                    $date['user_type'] = 4;
                    $date['user_status'] = 1;
                    $this->password(200, $createNoncestrs, $user_id);
                } else if ($userOne['is_vip'] == 5) {
                    $role_id = 12;
                    $date['user_type'] = 5;
                    $date['user_status'] = 1;
                    $this->password(1000, $createNoncestrs, $user_id);
                } else if ($userOne['is_vip'] == 1) {
                    $role_id = 12;
                    $date['user_type'] = 5;
                    $date['user_status'] = 1;
                } else if ($userOne['is_vip'] == 2) {
                    $role_id = 12;
                    $date['user_type'] = 5;
                    $date['user_status'] = 1;
                } else if ($userOne['is_vip'] == 3) {
                    $role_id = 12;
                    $date['user_type'] = 5;
                    $date['user_status'] = 1;

                }

                $date['parent'] = $usersOne['id'];
                $date['wx_id'] = $user_id;
                $date['did'] = $createNoncestrs;
                $date['user_login'] = $userOne['phone'];
                $date['user_pass'] = sp_password("kf123456");

                $result = M("users")->add($date);
                $results = "";
                if ($result) {
                    $dates['q_r_code'] = $this->owner($createNoncestrs);
                    $dates['password_code'] = $this->passwords($createNoncestrs);

                    $results = M("users")->where(array("id" => $result))->save($dates);
                }

                if ($results !== false) {

                    $role_user_model = M("RoleUser");
                    $role_user_model->add(array("role_id" => $role_id, "user_id" => $result));
                }

                $this->success("审核成功！");
            } else {
                $this->error("审核失败！");
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
     * CMF密码加密方法
     * @param string $pw 要加密的字符串
     * @return string
     */
    function sp_password($pw, $authcode = '')
    {
        if (empty($authcode)) {
            $authcode = C("AUTHCODE");
        }
        $result = "###" . md5(md5($authcode . $pw));
        return $result;
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
     * 刘北林
     * 2018年6月29日18:08:17
     * 生成口令码
     */
    public function password($count, $createNoncestrs, $user_id)
    {

        if ($user_id && $count) {
            for ($i = 0; $i < $count; $i++) {
                M("password")->add(array("did" => $createNoncestrs, "password" => $this->createNoncestr(), "uid" => $user_id, "time" => date("Y-m-d H:i:s", time())));
            }
        }
        return true;

    }

    /**
     *  作用：产生随机字符串，不长于32位
     */
    public
    function createNoncestr($length = 8)
    {
        $chars = "0123456789";
        $str = "";
        for ($i = 0; $i < $length; $i++) {
            $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
        }

        return $str;
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
     * Notes: 导出订单报表
     * User: Sen
     * Date: 2019-7-18 0018
     * Time: 13:32
     * Return:
     */
    public function exports()
    {

        $xlsName = '订单报表_' . date('Ymd');
        $xlsCell = array(
            array('prepay_id', '订单编号'),
            array('company', '商户订单号'),
            array('sname', '下单人'),
            array('name', '收货人'),
            array('phone', '收货电话'),
            array('address', '收货地址'),
            array('total_fee', '总金额(元)'),
            array('time', '下单时间'),
            array('update_time', '支付时间'),
            array('type', '会员类型'),
        );
        $start_time = $_POST['start_time'] ? $_POST['start_time'] : "";
        $end_time = $_POST['end_time'] ? $_POST['end_time'] : "";
        if (!$start_time || !$end_time) {
            $this->error('请选择时间范围！');
        }

        $where_ands = array(" o.platform=1 and o.order_type in(-1,2)");


        $fields = array(
            'start_time' => array("field" => "o.time", "operator" => ">"),
            'end_time' => array("field" => "o.time", "operator" => "<"),
            'status' => array("field" => "o.status", "operator" => "=")
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
        $forumPostList = M('user')
            ->alias("u")
            ->join("right join   __ORDER__ o on u.id = o.uid")
            ->field("o.prepay_id,o.company,o.name,o.total_fee,o.time,o.update_time,o.type,o.address_z,o.address_s,o.phone,u.name as sname")
            ->where($where)
            ->order('o.time desc')
            ->select();
//        print_r($forumPostList);die;
        foreach ($forumPostList as $i => $value) {
            $forumPostList[$i]['address'] = $value['address_z'] . $value['address_s'];
            $forumPostList[$i]['total_fee'] = $value['total_fee'] * 0.01;
//            是否vip 0体验会员 1一次会员 2半年会员 3一年会员 4两年会员 5三年会员 7虚拟机主 8天使机主
            if ($value['type'] == 0) {
                $forumPostList[$i]['type'] = "体验会员";
            } else if ($value['type'] == 1) {
                $forumPostList[$i]['type'] = "一次会员";
            } else if ($value['type'] == 2) {
                $forumPostList[$i]['type'] = "茉莉会员";
            } else if ($value['type'] == 3) {
                $forumPostList[$i]['type'] = "一年会员";
            } else if ($value['type'] == 4) {
                $forumPostList[$i]['type'] = "两年会员";
            } else if ($value['type'] == 5) {
                $forumPostList[$i]['type'] = "三年会员";
            } else if ($value['type'] == 7) {
                $forumPostList[$i]['type'] = "虚拟机主";
            } else if ($value['type'] == 8) {
                $forumPostList[$i]['type'] = "天使机主";
            }

        }
        $total_amount = 0;
        $count = count($forumPostList);
        foreach ($forumPostList as $i => $value) {
            $total_amount = $total_amount + $value['total_fee'];
        }
        $HeadData = array(
            "###起始日期：" . $start_time . "###终止日期：" . $end_time,
            "###交易金额合计：" . $count . "笔，共" . $total_amount . "元",
            "###下载时间：" . date("Y:m:d H:i:s")
        );

        $this->exportExcelNew($xlsName, $xlsCell, $forumPostList, $HeadData);
    }

    /**
     * 全部商品订单
     */
    public function order_mall()
    {
        $where_ands = array("o.platform=1 and o.order_type = 1");
        $fields = array(
            'status' => array("field" => "o.status", "operator" => "="),
            'start_time' => array("field" => "o.time", "operator" => ">"),
            'end_time' => array("field" => "o.time", "operator" => "<"),
            'order_id' => array("field" => "o.prepay_id", "operator" => "=")
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
                        $get = $param == "start_time" || $param == "end_time" ? strtotime($_GET[$param]) : $_GET[$param];
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
        $count = M('user')
            ->alias("u")
            ->join("right join  __ORDER__ o on u.id = o.uid")
            ->join("__GOODS__ g on o.good_id = g.id")
            ->where($where)
            ->count();
        $page = $this->page($count, 20);
        $order_list = M('user')
            ->alias("u")
            ->join("right join   __ORDER__ o on u.id = o.uid")
            ->join("__GOODS__ g on o.good_id = g.id")
            ->field("u.name,u.status stu,u.id user_id,u.parent_did,u.is_vip,o.*,g.name as gname,u.name as uname")
            ->where($where)
            ->order('o.time desc')
            ->limit($page->firstRow . ',' . $page->listRows)
            ->select();

        foreach ($order_list as $i => $value) {
            $order_list[$i]['total_fee'] = $value['total_fee'] * 0.01;
        }
        $beginThismonth = date("Y-m-01 00:00:00", time());
        $endThismonth = date('Y-m-t 23:59:59', time());

        $wheres = array(
            "success" => 1,
            "order_type" => 1,
        );
        $beginToday = date("Y-m-d H:i:s", mktime(0, 0, 0, date('m'), date('d'), date('Y')));
        $endToday = date("Y-m-d H:i:s", mktime(0, 0, 0, date('m'), date('d') + 1, date('Y')) - 1);
        $wheret = array(
            "success" => 1,
            "order_type" => 1,
            "update_time" => array("between", "$beginToday,$endToday")
        );
        //计算总金额
        $amountTotal = M("order")
            ->where($wheres)
            ->sum("total_fee");

        $orderTotal = M("order")
            ->where($wheres)
            ->count();

        $amountToday = M("order")
            ->where($wheret)
            ->sum("total_fee");

        $orderToday = M("order")
            ->where($wheret)
            ->count();

        $bonus["A"] = $orderTotal;
        $bonus["B"] = floor($amountTotal) / 100;
        $bonus["C"] = $orderToday;
        $bonus["D"] = floor($amountToday) / 100;


        $this->post_data = I('post.');
        $this->assign("page", $page->show('Admin'));
        $this->assign("order_list", $order_list);
        $this->assign("bonus", $bonus);
        $this->display();
    }

    /**
     * Notes: 添加快递信息(显示)
     * User: Sen
     * Date: 2019-7-18 0018
     * Time: 21:21
     * Return:
     */
    public function delivery_mall()
    {
        $id = I('order_id');
        $order_list = M('user')
            ->alias("u")
            ->join("right join   __ORDER__ o on u.id = o.uid")
            ->join("__GOODS__ g on o.good_id = g.id")
            ->field('o.prepay_id,o.time,o.id')
            ->where(array('o.id' => $id))
            ->find();
        $this->assign("data", $order_list);
        $this->display();
    }

    /**
     * Notes: Vip添加快递信息(显示)
     * User: Sen
     * Date: 2019-7-18 0018
     * Time: 21:21
     * Return:
     */
    public function Vipdelivery_mall()
    {
        $id = I('order_id');
        $order_list = M('user')
            ->alias("u")
            ->join("right join   __ORDER__ o on u.id = o.uid")
            ->join("__GOODS__ g on o.good_id = g.id")
            ->field('o.prepay_id,o.time,o.id')
            ->where(array('o.id' => $id))
            ->find();
        $this->assign("data", $order_list);
        $this->display();
    }

    /**
     * Notes: 添加快递信息(确认)
     * User: Sen
     * Date: 2019-7-18 0018
     * Time: 21:21
     * Return:
     */
    public function expressAdd()
    {
        $way = I('way');
        $id = I('id');
        $num = I('num');
        $content = I('content');
        $where = array(
            "express_cname" => $way,
            "express_kid" => $num,
            "comment" => $content,
            "status" => 2,
            "delivery_time" => date("Y:m:d H;i;s"),
        );
        $res = M("order")
            ->where(array('id' => $id))
            ->save($where);

        if ($res) {
            return $this->redirect(U('Order/order_mall'));
        } else {
            $this->error('非法错误！');
        }
    }

    /**
     * Notes: 添加快递信息(确认)
     * User: Sen
     * Date: 2019-7-18 0018
     * Time: 21:21
     * Return:
     */
    public function VipexpressAdd()
    {
        $way = I('way');
        $id = I('id');
        $num = I('num');
        $content = I('content');
        $where = array(
            "express_cname" => $way,
            "express_kid" => $num,
            "comment" => $content,
            "status" => 2,
            "delivery_time" => date("Y:m:d H;i;s"),
        );
        $res = M("order")
            ->where(array('id' => $id))
            ->save($where);

        if ($res) {
            return $this->redirect(U('Order/recharge'));
        } else {
            $this->error('非法错误！');
        }
    }

    /**
     * Notes: 确认收货
     * User: Sen
     * Date: 2019-7-19 0019
     * Time: 10:56
     * Return:
     */
    public function delivery_confirm()
    {
        $id = I('order_id');
        $data = array(
            "confirm_time" => date("Y:m:d H;i:s"),
            "status" => 3,
        );
        $res = M("order")
            ->where(array("id" => $id))
            ->save($data);
        if ($res) {
            $this->success('交易完成！');
        } else {
            $this->error('非法错误！');
        }
    }

    /**
     * Notes: 商城订单导出
     * User: Sen
     * Date: 2019-7-19 0019
     * Time: 13:36
     * Return:
     */
    public function mall_exports()
    {
        $xlsName = '商城订单报表_' . date('Ymd');
        $xlsCell = array(
            array('prepay_id', '订单编号'),
            array('company', '商户订单号'),
            array('sname', '下单人'),
            array('name', '收货人'),
            array('phone', '收货电话'),
            array('address', '收货地址'),
            array('total_fee', '总金额(元)'),
            array('time', '下单时间'),
            array('update_time', '支付时间'),
        );
        $start_time = $_POST['start_time'] ? $_POST['start_time'] : "";
        $end_time = $_POST['end_time'] ? $_POST['end_time'] : "";
        $status = $_POST['status'] ? $_POST['status'] : "";
        if (!$start_time || !$end_time) {
            $this->error('时间不允许为空！');
        }

        $where_ands = array("o.platform = 1 and order_type =1 ");


        $fields = array(
            'start_time' => array("field" => "o.time", "operator" => ">"),
            'end_time' => array("field" => "o.time", "operator" => "<"),
            'status' => array("field" => "o.status", "operator" => "=")
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
        $forumPostList = M('user')
            ->alias("u")
            ->join("right join   __ORDER__ o on u.id = o.uid")
            ->field("o.prepay_id,o.company,o.name,o.total_fee,o.time,o.update_time,o.type,o.address_z,o.address_s,o.phone,u.name as sname")
            ->where($where)
            ->order('o.time desc')
            ->select();
        foreach ($forumPostList as $i => $value) {
            $forumPostList[$i]['address'] = $value['address_z'] . $value['address_s'];
            $forumPostList[$i]['total_fee'] = $value['total_fee'] * 0.01;
        }

        $total_amount = 0;
        $count = count($forumPostList);
        foreach ($forumPostList as $i => $value) {
            $total_amount = $total_amount + $value['total_fee'];
        }
        $HeadData = array(
            "###起始日期：" . $start_time . "###终止日期：" . $end_time,
            "###交易金额合计：" . $count . "笔，共" . $total_amount . "元",
            "###下载时间：" . date("Y:m:d H:i:s")
        );

        $this->exportExcelNew($xlsName, $xlsCell, $forumPostList, $HeadData);
    }

    /**
     * Notes: 查看订单信息
     * User: Sen
     * Date: 2019-7-21 0021
     * Time: 15:13recharge
     * Return:
     */
    public function order_info()
    {
        $id = I('order_id');
        $order_list = M('order')
            ->alias("o")
            ->join("__GOODS__ g on o.good_id = g.id")
            ->field('o.comment,o.prepay_id,o.time,o.express_cname,o.express_kid,o.name,o.phone,o.address_z,o.address_s,o.num,o.total_fee,g.name as gname,g.smeta')
            ->where(array('o.id' => $id))
            ->find();
        $order_list['smeta'] = "https://bolon.kuaifengpay.com/data/upload/" . json_decode($order_list['smeta'], true)['thumb'];
        $order_list["total_fee"] = $order_list["total_fee"] / 100;
        $this->assign("data", $order_list);
        $this->display();

    }

    private function exportExcelNew($expTitle, $expCellName, $expTableData, $HeadData)
    {

        $xlsTitle = iconv('utf-8', 'gb2312', $expTitle);//文件名称
        $fileName = "今日订单_".date('YmdHis');//or $xlsTitle 文件名称可根据自己情况设定
        $cellNum = count($expCellName);
        $dataNum = count($expTableData);

//        $HeadData = array("中国", "美国");
        $dataNums = count($HeadData);
        vendor("PHPExcel");

        $objPHPExcel = new \PHPExcel();

        $cellName = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', 'AA', 'AB', 'AC', 'AD', 'AE', 'AF', 'AG', 'AH', 'AI', 'AJ', 'AK', 'AL', 'AM', 'AN', 'AO', 'AP', 'AQ', 'AR', 'AS', 'AT', 'AU', 'AV', 'AW', 'AX', 'AY', 'AZ');
        //$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(100);
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(15);
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(15);
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(15);
        $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(15);
        $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(22);
        //        $objPHPExcel->getActiveSheet(0)->mergeCells('A1:'.$cellName[$cellNum-1].'1');//合并单元格
        //         $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A1', $expTitle.'  Export time:'.date('Y-m-d H:i:s'));
        for ($i = 0; $i < $cellNum; $i++) {
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($cellName[$i] . '6', $expCellName[$i][1]);
        }
        // Miscellaneous glyphs, UTF-8
        for ($i = 0; $i < $dataNum; $i++) {
            for ($j = 0; $j < $cellNum; $j++) {
                $objPHPExcel->getActiveSheet(0)->setCellValue($cellName[$j] . ($i + 7), $expTableData[$i][$expCellName[$j][0]]);
            }
        }
        for ($j = 0; $j < $dataNums; $j++) {
            $objPHPExcel->getActiveSheet(0)->setCellValue($cellName[0] . ($j + 1), $HeadData[$j]);
        }

        header('pragma:public');
        header('Content-type:application/vnd.ms-excel;charset=utf-8;name="' . $xlsTitle . '.xls"');
        header("Content-Disposition:attachment;filename=$fileName.xls");//attachment新窗口打印inline本窗口打印
        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');//Excel5为xls格式，excel2007为xlsx格式
        $objWriter->save('php://output');
        exit;
    }
}