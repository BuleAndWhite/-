<?php

/**
 * 会员
 */

namespace User\Controller;

use Common\Controller\AdminbaseController;

class IndexadminController extends AdminbaseController
{
    function index()
    {
//        $users_model = M("Users");
//        $count = $users_model->where(array("user_type" => 2))->count();
//        $page = $this->page($count, 20);
//        $lists = $users_model
//            ->where(array("user_type" => 2))
//            ->order("create_time DESC")
//            ->limit($page->firstRow . ',' . $page->listRows)
//            ->select();
//        $this->assign('lists', $lists);
//        $this->assign("page", $page->show('Admin'));
        $fields = array(
            'start_time' => array("field" => "p.time", "operator" => ">"),
            'end_time' => array("field" => "p.time", "operator" => "<"),
            'keyword' => array("field" => "p.password", "operator" => "like"),
            'status' => array("field" => "p.status", "operator" => "=")
        );
        $where_ands = array();
        if (IS_POST) {
            foreach ($fields as $param => $val) {
                if (isset($_POST[$param]) && !empty($_POST[$param])) {
                    $operator = $val['operator'];
                    $field = $val['field'];
                    $_GET[$param] = $_POST[$param];

                    if (!empty($_POST['start_time']) && !empty($_POST['end_time'])) {
                        $get = $param == "start_time" || $param == "end_time" ? strtotime($_POST[$param]) : $_POST[$param];
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

//        array_push($where_ands, " p.did IS NULL ");
        $where = join("  and ", $where_ands);

//
//        $where = join(" and ", $where_ands);

        $count = M('password')
            ->alias("p")
           // ->join("__USERS__ u on p.uid = u.id")
           // ->join("__USERS__ u on p.uid = u.id")
            ->where($where)
            ->count();
        $page = $this->page($count, 20);
        $password_model = M('password')
            ->alias("p")
            //->join("__USERS__ u on p.uid = u.id")
           // ->join("__USERS__ u on p.uid = u.id")
            //->field("p.*,u.user_nicename")
            //->field("p.*")
            ->where($where)
            ->order('p.id desc')
            ->limit($page->firstRow . ',' . $page->listRows)
            ->select();
        $this->assign("page", $page->show('Admin'));

        $this->assign('forumPassword', $password_model);
        $this->display(":indexs");
    }

    /**
     * 导出订单
     **/
    public function export()
    {

        $xlsName = '口令码报表_' . date('Ymd');
        $xlsCell = array(
            array('password', '口令码')
        );
        $start_time = $_POST['start_time'] ? $_POST['start_time'] : "";
        $end_time = $_POST['end_time'] ? $_POST['end_time'] : "";
        if (!$start_time || !$end_time) {
            $this->error('非法错误！');
        }

        $where_ands = array();
        $fields = array(
            'start_time' => array("field" => "id", "operator" => ">="),
            'end_time' => array("field" => "id", "operator" => "<="),
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
     * 刘北林
     * 2018年6月29日18:08:17
     * 生成口令码
     */
    public function password()
    {
//        foreach ($_POST['photos_url'] as $key => $url) {
//            $photourl = sp_asset_relative_url($url);
//            $_POST['smeta']['photo'][] = array("url" => $photourl, "alt" => $_POST['photos_alt'][$key]);
//            $article['smeta'] = json_encode($_POST['smeta']);
//        }
        if (IS_POST) {

            $count = $_POST['count'] ? $_POST['count'] : 0;
            $uid = $_POST['uid'] ? $_POST['uid'] : 1;
            if ($uid && $count) {
                for ($i = 0; $i < $count; $i++) {
                    M("password")->add(array("password" => $this->createNoncestr(),"did"=>"SK-E100", "uid" => $uid, "time" => date("Y-m-d H:i:s", time())));
                }
                $this->index();

//            $rst = M("password")->where(array("password" => $id))->find();
//            if ($rst) {
//                M("password")->where(array("id" => $rst['id']))->save(array("password"=>$this->createNoncestr()));
//                $this->success("生成成功！", U("indexadmin/index"));
//            } else {
//                $this->error('生成失败！');
//            }
            } else {
                $this->index();
            }
        }
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

    function ban()
    {
        $id = intval($_GET['id']);
        if ($id) {
            $rst = M("Users")->where(array("id" => $id, "user_type" => 2))->setField('user_status', '0');
            if ($rst) {
                $this->success("会员拉黑成功！", U("indexadmin/index"));
            } else {
                $this->error('会员拉黑失败！');
            }
        } else {
            $this->error('数据传入失败！');
        }
    }

    function cancelban()
    {
        $id = intval($_GET['id']);
        if ($id) {
            $rst = M("Users")->where(array("id" => $id, "user_type" => 2))->setField('user_status', '1');
            if ($rst) {
                $this->success("会员启用成功！", U("indexadmin/index"));
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
            $rst = M("Users")->where(array("id" => $gag, "user_type" => 2))->setField('gag', '1');
            if ($rst) {
                $this->success("会员启用成功！", U("indexadmin/index"));
            } else {
                $this->error('会员启用失败！');
            }
        }

        if ($unGag) {
            $rst = M("Users")->where(array("id" => $unGag, "user_type" => 2))->setField('gag', '0');
            if ($rst) {
                $this->success("会员禁言成功！", U("indexadmin/index"));
            } else {
                $this->error('会员禁言失败！');
            }
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
//        for($i=0;$i<$cellNum;$i++){
//            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($cellName[$i].'1', $expCellName[$i][1]);
//        }
        // Miscellaneous glyphs, UTF-8
        for ($i = 0; $i < $dataNum; $i++) {
            for ($j = 0; $j < $cellNum; $j++) {
                $objPHPExcel->getActiveSheet(0)->setCellValue($cellName[$j] . ($i + 1), "\t" . $expTableData[$i][$expCellName[$j][0]]);
            }
        }
        header('pragma:public');
        header('Content-type:application/vnd.ms-excel;charset=utf-8;name="' . $xlsTitle . '.xls"');
        header("Content-Disposition:attachment;filename=$fileName.xls");//attachment新窗口打印inline本窗口打印
        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');//Excel5为xls格式，excel2007为xlsx格式
        $objWriter->save('php://output');
        exit;
    }
}
