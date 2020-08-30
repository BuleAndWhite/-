<?php
/**
 * @date：2018年1月12日15:08:23
 * @content：站内信
 * @User：刘柏林
 */

namespace Admin\Controller;

use Common\Controller\AdminbaseController;

class ManagementH5Controller extends AdminbaseController
{

    function _initialize()
    {

    }

    /**
     * Notes: 无需操作
     * User: Sen
     * Date: 2019-9-16 0016
     * Time: 9:39
     * Return:
     */
    public function index()
    {
        //H5展示
    }

    /**
     * Notes: H5推广数据展示
     * User: Sen
     * Date: 2019-9-16 0016
     * Time: 9:39
     * Return:
     */
    public function h5_list()
    {
        $where_ands = array();
        $fields = array(
            'start_time' => array("field" => "a.time", "operator" => ">"),
            'end_time' => array("field" => "a.time", "operator" => "<"),
            'phone' => array("field" => "a.phone", "operator" => "=")
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
        $count = M("h5_extension")
            ->alias("a")
            ->join(" __USER__  as b on a.parent_id = b.unionid")
            ->where($where)
            ->field('b.name as pname,a.*')
            ->count();
        $page = $this->page($count, 20);
        $res = M("h5_extension")
            ->alias("a")
            ->join(" __USER__  as b on a.parent_id = b.unionid")
            ->limit($page->firstRow . ',' . $page->listRows)
            ->where($where)
            ->order("a.time desc")
            ->field('b.name as pname,a.*')
            ->select();

        $this->post_data = I('post.');
        $this->assign("page", $page->show('Admin'));
        $this->assign("res", $res);
//        $this->assign("num", $num);
        $this->display();
    }

    /**
     * Notes: H5信息导出
     * User: Sen
     * Date: 2019-7-19 0019
     * Time: 13:36
     * Return:
     */
    public function mall_exports()
    {
        $xlsName = 'H5信息导出_' . date('Ymd');
        $xlsCell = array(
            array('id', 'ID'),
            array('name', '用户姓名'),
            array('pname', '父级微信昵称'),
            array('phone', '用户电话'),
            array('address', '用户地址'),
            array('time', '填入时间'),
        );
        $start_time = $_POST['start_time'] ? $_POST['start_time'] : "";
        $end_time = $_POST['end_time'] ? $_POST['end_time'] : "";
        if (!$start_time || !$end_time) {
            $this->error('时间不允许为空！');
        }

        $fields = array(
            'start_time' => array("field" => "o.update_time", "operator" => ">"),
            'end_time' => array("field" => "o.update_time", "operator" => "<"),
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

        $forumPostList = M("h5_extension")
            ->alias("a")
            ->join(" __USER__  as b on a.parent_id = b.unionid")
            ->where($where)
            ->order("time desc")
            ->field('b.name as pname,a.*')
            ->select();
        $this->exportExcel($xlsName, $xlsCell, $forumPostList);
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
                $objPHPExcel->getActiveSheet(0)->setCellValue($cellName[$j] . ($i + 2), $expTableData[$i][$expCellName[$j][0]]);
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
	 * Notes: 会议报名H5
	 * User: Sen
	 * Date: 2019-10-14 0019
	 * Time: 13:36
	 * Return:
	 */
	public function applicationH5()
	{
		$where_ands = array();
		$fields = array(
			'start_time' => array("field" => "time", "operator" => ">"),
			'end_time' => array("field" => "time", "operator" => "<"),
			'phone' => array("field" => "phone", "operator" => "=")
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
		$count = M("application")
			->where($where)
			->count();
		$page = $this->page($count, 20);
		$res = M("application")
			->limit($page->firstRow . ',' . $page->listRows)
			->where($where)
			->order("time desc")
			->select();

		$this->post_data = I('post.');
		$this->assign("page", $page->show('Admin'));
		$this->assign("res", $res);
//        $this->assign("num", $num);
		$this->display();
	}
	/**
	 * Notes: H5签到信息导出
	 * User: Sen
	 * Date: 2019-7-19 0019
	 * Time: 13:36
	 * Return:
	 */
	public function application_exports()
	{
		$xlsName = 'H5签到信息导出_' . date('Ymd');
		$xlsCell = array(
			array('id', 'ID'),
			array('name', '用户姓名'),
			array('phone', '用户电话'),
			array('address', '用户地址'),
			array('time', '填入时间'),
		);
		$start_time = $_POST['start_time'] ? $_POST['start_time'] : "";
		$end_time = $_POST['end_time'] ? $_POST['end_time'] : "";
		if (!$start_time || !$end_time) {
			$this->error('时间不允许为空！');
		}

		$fields = array(
			'start_time' => array("field" => "o.update_time", "operator" => ">"),
			'end_time' => array("field" => "o.update_time", "operator" => "<"),
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

		$forumPostList = M("application")
			->where($where)
			->order("time desc")
			->select();
		$this->exportExcel($xlsName, $xlsCell, $forumPostList);
	}

}