<?php
/**
 * @date：2018年1月12日15:08:23
 * @content：站内信
 * @User：刘柏林
 */

namespace Admin\Controller;

use Common\Controller\AdminbaseController;

class WithdrawController extends AdminbaseController
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
    protected $withdraw_model;

    function _initialize()
    {
        parent::_initialize();
        $this->user_model = D("Common/Users");;
        $this->post_digg_model = D("Common/PostDigg");
        $this->forum_model = D("Common/Forum");
        $this->forum_post_model = D("Common/ForumPost");
        $this->message_content_model = D("Common/MessageContent");
        $this->message_model = D("Common/Message");
        $this->withdraw_model = D("Common/Withdraw");
    }

    /**
     * @date：2018年1月12日15:08:23
     * @content：全部帖子
     * @User：刘柏林
     */
    public function index()
    {
        $this->display();
    }

    /**
     * @date：2018年1月12日15:08:23
     * @content：全部帖子
     * @User：刘柏林
     */
    public function withdrawal()
    {
        $this->_lists();
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
     * @param：首页推荐帖子
     * @User：刘柏林
     */
    public function home_post()
    {
        $this->homePostList();
        $this->display();
    }

    /**
     * @date：2018年1月13日18:04:07
     * @param：圈子分类
     * @User：刘柏林
     */
    public function circle_type()
    {
        $this->circleTypeList();
        $this->display();
    }

    /**
     * @date：2018年1月13日18:04:07
     * @param：专题列表
     * @User：刘柏林
     */
    public function circle_list()
    {
        $this->circleList();
        $this->display();
    }

    /**
     * @date：2018年1月13日18:04:07
     * @param：圈主列表
     * @User：刘柏林
     */
    public function circle_del()
    {
        $this->circleDelList();
        $this->display();
    }

    /**
     * @date：2018年1月13日18:04:07
     * @param：圈主推荐
     * @User：刘柏林
     */
    public function circle_recommend()
    {
        $this->circleDelRecommend();
        $this->display();
    }

    /**
     * @date：2018年1月14日22:05:33
     * @param：添加圈主
     * @User：刘柏林
     */
    public function add()
    {
        $this->display();
    }

    /**
     * @date：2018年1月14日22:05:33
     * @param：贴吧评论
     * @User：刘柏林
     */
    public function post_reply()
    {
        $this->postReplyList();
        $this->display();
    }

    /**
     * @date：2018年1月18日23:20:34
     * @param：添加帖子
     * @User：刘柏林
     */
    public function circle_add()
    {

        $this->assign("forumId", $_GET['id']);
        $this->display();
    }


    /**
     * @date 2018年1月12日15:08:11
     * @content 站内信管理内容
     * @User：刘柏林
     */
    private function _lists()
    {

        $where_ands = array("w.isdel != 1");

        $fields = array(
            'start_time' => array("field" => "w.time", "operator" => ">"),
            'end_time' => array("field" => "w.time", "operator" => "<"),
            'keyword' => array("field" => "u.name", "operator" => "like"),
            'varied' => array("field" => "w.varied", "operator" => "=")
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

        $count = M('user')
            ->alias("u")
            ->join("LEFT JOIN __WITHDRAW__ w on u.unionid = w.unionid")
            ->join("__PAYMENT_ADD__ p on w.payment_id = p.id ")
            ->where($where)
            ->count();
        $page = $this->page($count, 20);

        $forumPostList = M('user')
            ->alias("u")
            ->join("LEFT JOIN __WITHDRAW__ w on u.unionid = w.unionid")
            ->join("__PAYMENT_ADD__ p on w.payment_id = p.id ")
            ->field("w.*,u.name,p.card,p.phone,p.account,p.branch,p.name as pname")
            ->where($where)
            ->order('w.time desc')
            ->limit($page->firstRow . ',' . $page->listRows)
            ->select();
        foreach ($forumPostList as $k => $val){
            $forumPostList[$k]['moneyT'] = floor($val["money"] * 0.95 * 100) / 100;
        }
        $this->assign("page", $page->show('Admin'));
        unset($_GET[C('VAR_URL_PARAMS')]);

        $this->assign("formget", $_GET);
        $this->assign("messageContentList", $forumPostList);

    }

    /**
     * @date：2018年1月12日18:35:09
     * @param：排序
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
     * @param：帖子
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
     * @param：排序
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
     * @param删除
     * @User：刘柏林
     */
    public function delete()
    {
        if (isset($_GET['id'])) {
            $tid = intval(I("get.id"));
            $data['isdel'] = 1;//设为伪删除
            if ($this->withdraw_model->where("id=$tid")->save($data)) {

                $this->success("删除成功！");
            } else {
                $this->error("删除失败！");
            }
        }
        if (isset($_POST['ids'])) {
            $tids = join(",", $_POST['ids']);
            $data['isdel'] = 1;//设为伪删除
            if ($this->withdraw_model->where("id in ($tids)")->save($data)) {
                $this->success("删除成功！");
            } else {
                $this->error("删除失败！");
            }
        }
    }

    /**
     * @date：2018年1月14日18:05:06
     * @param：添加圈子
     * @User：刘柏林
     */
    public function circleAdd()
    {
        $circleType = M("circle_type")->select();
        $this->assign("circleType", $circleType);
    }

    /**
     * @date：2018年1月14日18:05:06
     * @param：添加公告
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
     * Notes: 回执单文件上传
     * User: Sen
     * Date: 2019-7-5 0005
     * Time: 20:24
     * Return:
     */
    function upload_files()
    {
        $id = $_REQUEST['id'];
        $savepath = date('Ymd') . '/';
        $config = array(
            'FILE_UPLOAD_TYPE' => sp_is_sae() ? "Sae" : 'Local',//TODO 其它存储类型暂不考虑
            'rootPath' => './' . C("UPLOADPATH"),
            'savePath' => $savepath,
            'maxSize' => 10485761111,//50M
            'saveName' => array('uniqid', ''),
            'exts' => array('png', 'jpg'),
            'autoSub' => false,
        );
        ini_set('max_execution_time', '0');
        $upload = new \Think\Upload($config);//
        $info = $upload->upload();
        //开始上传
        if ($info) {

            //上传成功
            $file = $info['uploadkey']['savepath'] . $info['uploadkey']['savename'];

            M("withdraw")->where(array("id" => $id))->save(array("receipt_img" => C("TMPL_PARSE_STRING.__UPLOAD__") . $file));
            echo json_encode(array('status' => '1', 'id' => $id, 'url' => C("TMPL_PARSE_STRING.__UPLOAD__") . $file, 'file_alt' => $info['uploadkey']['name'], 'file_size' => $info['uploadkey']['size']));
            exit;

        } else {
            //上传失败，返回错误
            echo json_encode(array('status' => '0', 'info' => $upload->getError()));
            exit;
        }
    }

    /**
     * Notes: 查看回执单
     * User: Sen
     * Date: 2019-7-6 0006
     * Time: 16:53
     * Return:
     */
    public function select()
    {
        $id = $id = $_REQUEST['id'];
        $img_url = M("withdraw")
            ->where(array("id" => $id))
            ->field('receipt_img')
            ->find();
        $this->ajaxReturn(array("info" => $img_url['receipt_img']));
    }

    /**
     * Notes: 提现审核（通过）
     * User: Sen
     * Date: 2019-7-6 0006
     * Time: 19:19
     * Return:
     * @param $id
     */
    public function approved()
    {
        $id = $_GET['id'];
        $res = $this->withdraw_model
            ->where(array('id' => $id))
            ->save(array('status' => '2'));
        if ($res) {
            $this->success("取消驳回成功");
        } else {
            $this->error("取消驳回失败");
        }
    }

    /**
     * Notes: 提现审核（驳回）
     * User: Sen
     * Date: 2019-7-6 0006
     * Time: 19:20
     * Return:
     */
    public function reset()
    {
        $id = $_GET['id'];
        $res = $this->withdraw_model
            ->where(array('id' => $id))
            ->save(array('status' => '3'));
        if ($res) {
            $this->success("取消驳回成功");
        } else {
            $this->error("取消驳回失败");
        }
    }


    /**
     * 导出订单
     * Data：2019年7月18日11:21:23
     * Name:刘北林
     */
    public function export()
    {

        $xlsName = '订单报表_' . date('Ymd');
        $xlsCell = array(
            array('username', '姓名'),
            array('phone', '手机号'),
            array('money', '金额'),
            array('typest', '账号类型'),
            array('account', '开户行'),
            array('branch', '支行'),
            array('time', '提现时间'),
        );
        $start_time = $_POST['start_time'] ? $_POST['start_time'] : "";
        $end_time = $_POST['end_time'] ? $_POST['end_time'] : "";
        if (!$start_time || !$end_time) {
            $this->error('非法错误！');
        }

        $where_ands = array();


        $fields = array(
            'start_time' => array("field" => "w.time", "operator" => ">"),
            'end_time' => array("field" => "w.time", "operator" => "<")
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
            ->join("LEFT JOIN __WITHDRAW__ w on u.unionid = w.unionid")
            ->join("__PAYMENT_ADD__ p on w.payment_id = p.id ")
            ->field("w.*,u.name,p.card,p.phone,p.account,p.branch,p.type typest,p.name username")
            ->where($where)
            ->order('w.time desc')
            ->select();
        foreach ($forumPostList as $key => $arr) {
            if ($arr['typest'] == 1) {
                $forumPostList[$key]['typest'] = "支付宝";
            } else {
                $forumPostList[$key]['typest'] = "银行卡";
            }
        }
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

}