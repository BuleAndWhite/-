<?php
/**
 * 参    数：
 * 作    者：lht
 * 功    能：OAth2.0协议下第三方登录数据报表
 * 修改日期：2013-12-13
 */

namespace User\Controller;

use Common\Controller\AdminbaseController;

class OauthadminController extends AdminbaseController
{

    //用户列表
    function index()
    {
        $where_ands = array();
        $fields = array(
            'start_time' => array("field" => "u.time", "operator" => ">"),
            'end_time' => array("field" => "u.time", "operator" => "<"),
            'keyword' => array("field" => "u.name", "operator" => "like")
        );

        if (IS_POST) {
            foreach ($fields as $param => $val) {
                if (isset($_POST[$param]) && !empty($_POST[$param])) {
                    $operator = $val['operator'];
                    $field = $val['field'];
                    $_GET[$param] = $_POST[$param];

                    if (!empty($_POST['start_time']) && !empty($_POST['end_time'])) {
                        $get = $param == "start_time" || $param == "end_time" ?$_POST[$param] : $_POST[$param];
                    } else {

                        $get = $_POST[$param];
                    }

                    if ($operator == "like") {
                        $get = "%$get%";
                    }
                    array_push($where_ands, "$field $operator '$get'");
                }
            }
        }
        else {
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


        $id =get_current_admin_id();




        $usersModel =M("users")->where(array("id"=>$id))->find();

        if($usersModel['user_type'] ==2){
            $postReplyS = M('role_user')
                ->alias("r")
                ->join("__ROLE__ u on r.role_id = u.id")
                ->field("u.*")
                ->where(array("r.user_id"=>$id))
                ->find();

            array_push($where_ands, " u.machine = ".'"'.$usersModel['did'].'"');
            $where = join("  and ", $where_ands);
            //余额
            $months =M('User')->where(array("machine" => $usersModel['did'], "is_vip" => 2))->count();//半年用户数量
            $year = M('User')->where(array("machine" => $usersModel['did'], "is_vip" => 3))->count();//一年用户数量
            $once = M('User')->where(array("machine" => $usersModel['did'], "is_vip" => 1))->count();//一年用户数量
            $twoYears = M('User')->where(array( "machine" => $usersModel['did'],"is_vip" => 4))->count();//一年用户数量
            $threeYears = M('User')->where(array("machine" => $usersModel['did'], "is_vip" => 5))->count();//一年用户数量
            $threeYears = $threeYears * 999.9;
            $twoYears = $twoYears * 699.9;
            $months = $months * 218;
            $year = $year * 365;
            $once = $once * 29.8;
            $whereLs['status'] = array('in',"1,2");//cid在这个数组中
            $whereL['machine'] = array('EQ',$usersModel['did']);//cid在这个数组中

            $postReply = M('User')
                ->where($whereL)
                ->select();

            $sum =0;
            foreach ($postReply as  $i=>$item) {
                $whereLs['unionid'] =$item['unionid'];

                $money = M('withdraw')
                    ->where($whereLs)
                    ->sum("money");
                $sum =$sum +$money;
            }
         
            $begintime = date("Y-m-d H:i:s", mktime(0, 0, 0, date('m'), date('d'), date('Y')));
            $endtime = date("Y-m-d H:i:s", mktime(0, 0, 0, date('m'), date('d') + 1, date('Y')) - 1);
            $whereA = array(
                'machine' => $usersModel['did'],
                'time' => array(array('gt', $begintime), array('lt', $endtime)),
            );

            $userList['totalRevenue'] = ($once + $months + $year+$threeYears+$twoYears) ;
            $userList['total'] = ($once + $months + $year+$threeYears+$twoYears) * ($postReplyS['percentage']/100) -$sum;
            $userList['count'] =M('User')
                ->where($whereL)
                ->count();
            $userList['today'] = M('User')
                ->where($whereA)
                ->count();

        }else {
            $where = join(" and ", $where_ands);
            //余额
            $months =M('User')->where(array( "machine" => $usersModel['did'],"is_vip" => 2))->count();//半年用户数量
            $year = M('User')->where(array( "machine" => $usersModel['did'],"is_vip" => 3))->count();//一年用户数量
            $once = M('User')->where(array( "machine" => $usersModel['did'],"is_vip" => 1))->count();//一年用户数量
            $twoYears = M('User')->where(array( "machine" => $usersModel['did'],"is_vip" => 4))->count();//一年用户数量
            $threeYears = M('User')->where(array( "machine" => $usersModel['did'],"is_vip" => 5))->count();//一年用户数量
            $threeYears = $threeYears * 999.9;
            $twoYears = $twoYears * 699.9;
            $months = $months * 218;
            $year = $year * 365;
            $once = $once * 29.8;
            $whereLs['status'] = array('in',"1,2");//cid在这个数组中
//            $whereL['sn'] = array('EQ',$usersModel['did']);//cid在这个数组中
            $postReply = M('User')
                ->select();
            $sum =0;
            foreach ($postReply as  $i=>$item) {
                $whereLs['unionid'] =$item['unionid'];

                $money = M('withdraw')
                    ->where($whereLs)
                    ->sum("money");
                $sum =$sum +$money;
            }

            $begintime = date("Y-m-d H:i:s", mktime(0, 0, 0, date('m'), date('d'), date('Y')));
            $endtime = date("Y-m-d H:i:s", mktime(0, 0, 0, date('m'), date('d') + 1, date('Y')) - 1);
            $whereA = array(
                'time' => array(array('gt', $begintime), array('lt', $endtime)),
            );
            $userList['totalRevenue'] = ($once + $months + $year+$threeYears+$twoYears) ;
            $userList['total'] = ($once + $months + $year+$threeYears+$twoYears) * 0.25 -$sum;
            $userList['count'] =M('User')
                ->count();
            $userList['today'] = M('User')
                ->where($whereA)
                ->count();
        }


        $oauth_user_model = M('User');
        $count = $oauth_user_model->alias("u")->order("u.time DESC")->where($where)->count();
        $page = $this->page($count, 20);
        $lists = $oauth_user_model
            ->alias("u")
            ->order("u.time DESC")
            ->where($where)
            ->limit($page->firstRow . ',' . $page->listRows)
            ->select();
        foreach ($lists as $key => $list) {
            $lists[$key]['count'] = $oauth_user_model->where(array("parent_id" => $list['unionid']))->count();
            $integral =M("sign")->where(array("unionid" => $list['unionid']))->field("integral")->find();
            $lists[$key]['integral'] =$integral['integral'];
        }
        $this->assign("page", $page->show('Admin'));
        $this->assign('lists', $lists);
        $this->assign('userList', $userList);
        $this->assign('user', $usersModel);
        $this->display();
    }

    //删除用户
    function delete()
    {
        $id = intval($_GET['id']);
        if (empty($id)) {
            $this->error('非法数据！');
        }
        $rst = M("User")->where(array("id" => $id))->delete();
        if ($rst !== false) {
            $this->success("删除成功！", U("oauthadmin/index"));
        } else {
            $this->error('删除失败！');
        }
    }

    /**
     * @date：2018年1月22日21:43:46
     * @param：拉黑第三方用户
     * @User：刘柏林
     */
    function ban()
    {
        $id = intval($_GET['id']);
        if ($id) {
            $rst = M("user")->where(array("id" => $id))->setField('user_status', '0');
            if ($rst) {
                $this->success("会员拉黑成功！", U("oauthadmin/index"));
            } else {
                $this->error('会员拉黑失败！');
            }
        } else {
            $this->error('数据传入失败！');
        }
    }

    /**
     * @date：2018年1月22日21:44:08
     * @param启用第三方用户：
     * @User：刘柏林
     */
    function cancelban()
    {
        $id = intval($_GET['id']);
        if ($id) {
            $rst = M("user")->where(array("id" => $id))->setField('user_status', '1');
            if ($rst) {
                $this->success("会员启用成功！", U("oauthadmin/index"));
            } else {
                $this->error('会员启用失败！');
            }
        } else {
            $this->error('数据传入失败！');
        }
    }

    /**
     * @date：2018年1月22日21:15:29
     * @param 是否禁言
     * @User：刘柏林
     */
    public function isGag()
    {
        $gag = intval($_GET['gag']);
        $unGag = intval($_GET['unGag']);
        if ($gag) {
            $rst = M("oauth_user")->where(array("id" => $gag))->setField('gag', '1');
            if ($rst) {
                $this->success("会员启用成功！", U("oauthadmin/index"));
            } else {
                $this->error('会员启用失败！');
            }
        }

        if ($unGag) {
            $rst = M("oauth_user")->where(array("id" => $unGag))->setField('gag', '0');
            if ($rst) {
                $this->success("会员禁言成功！", U("oauthadmin/index"));
            } else {
                $this->error('会员禁言失败！');
            }
        }
    }
    public function  add(){
        $this->display();
    }
    public function addList(){
        if (IS_POST) {
            $data = I('post');

            $times = "";
            switch ($data['is_vip']) {
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
                    $times = date("Y-m-d H:i:s", strtotime("+2 year"));
                    break;
                case 5:
                    $times = date("Y-m-d H:i:s", strtotime("+3 year"));
                    break;
            }

            $data['name'] =$data['name'];
            $data['is_vip'] =$data['is_vip'];
            $data['idCard'] =$data['idCard'];
            $data['time'] =$data['time'];
            $data['time_maturity'] = $times;
            $data['time_start'] = date("Y-m-d H:i:s", time());
            $rs = M("user")->add($data);
            $orderModel['time'] = date("Y-m-d H:i:s", time());
            $orderModel['status'] = 1;
            $orderModel['type'] = $data['is_vip'];
            $orderModel['uid'] = $rs;
            $orderModel['success'] = 1;
            $orderModel['prepay_id'] = "wx".$this->createNoncestr();
            $orderModel['total_fee'] = $data['total_fee'];
            $orderList =M("order")->add($orderModel);

            if ($orderList !== false) {
                $this->success("保存成功！");
                exit;
            } else {
                $this->error("保存失败！");
                exit;
            }
        }


    }

    public function quotation()
    {
        if (IS_POST) {

            $xlsName = '用户统计_' . date('Ymd');
            $xlsCell = array(
                array('name', '用户名称'),
                array('idcard', '身份证'),
                array('phone', '手机号'),
                array('name_n', '姓名')
            );
            $where_ands = array();
            $where  =array();
            $id =get_current_admin_id();
            $usersModel =M("users")->where(array("id"=>$id))->find();

//            if($usersModel['user_type'] ==4){
//
//                array_push($where_ands, " parent_did = ".'"'.$usersModel['did'].'"');
//                $where = join("  and ", $where_ands);
//
//
//            }
//            else if($usersModel['user_type'] ==5){
//
//                array_push($where_ands, " parent_did = ".'"'.$usersModel['did'].'"');
//                $where = join("  and ", $where_ands);
//
//            }
//            else if($usersModel['user_type'] ==3){
//
//
//                array_push($where_ands, " owner_id = ".'"'.$usersModel['did'].'"');
//                $where = join("  and ", $where_ands);
//
//
//            } else if($usersModel['user_type'] ==2){
//
//
//
//                array_push($where_ands, " operation = ".'"'.$usersModel['did'].'"');
//                $where = join("  and ", $where_ands);
//
//
//            }
//            else {
//                array_push($where_ands, " operation !='' ");
//                $where = join("  and ", $where_ands);
//
//            }
            array_push($where_ands, " machine = ".'"'.$usersModel['did'].'"');
            $where = join("  and ", $where_ands);

            $oauth_user_model = M('User');

            $lists = $oauth_user_model->order("time DESC")->where($where)->select();



            $order_list = array_values($lists);
            $this->exportExcel($xlsName, $xlsCell, $order_list);
        }
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
        //        $objPHPExcel->getActiveSheet(0)->mergeCells('A1:'.$cellName[$cellNum-1].'1');//合并单元格
        //         $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A1', $expTitle.'  Export time:'.date('Y-m-d H:i:s'));
        for ($i = 0; $i < $cellNum; $i++) {
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($cellName[$i] . '1', $expCellName[$i][1]);
        }
        // Miscellaneous glyphs, UTF-8
        for ($i = 0; $i < $dataNum; $i++) {
            for ($j = 0; $j < $cellNum; $j++) {
                $objPHPExcel->getActiveSheet(0)->setCellValue($cellName[$j] . ($i + 2), "\t" . $expTableData[$i][$expCellName[$j][0]]);
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
     *  作用：产生随机字符串，不长于32位
     */

    public
    function createNoncestr($length = 38)
    {
        $chars = "abcdefghijklmnopqrstuvwxyz0123456789";
        $str = "";
        for ($i = 0; $i < $length; $i++) {
            $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
        }

        return $str;
    }
}