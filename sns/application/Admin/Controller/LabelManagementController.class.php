<?php
/**
 * @date：2018年1月18日14:29:48
 * @content：标签
 * @User：刘柏林
 */

namespace Admin\Controller;

use Common\Controller\AdminbaseController;

class LabelManagementController extends AdminbaseController
{
    protected $skill_model;
    protected $user_model;

    function _initialize()
    {
        parent::_initialize();
        $this->user_model = D("Common/Users");
        $this->skill_model = D("Common/Label");
    }

    /**
     * @date：2018年1月12日15:08:23
     * @content：标签列表
     * @User：刘柏林
     */
    public function index()
    {
        $this->_lists();
        $this->display();
    }

    /**
     * @date：2018年1月14日22:05:33
     * @param：添加标签
     * @User：刘柏林
     */
    public function add()
    {
        $skill['pid'] = 0;
        $skillOne = $this->skill_model->where($skill)->select();
        $this->assign("skillOne", $skillOne);
        $this->display();
    }

    /**
     * @date：2018年1月30日14:24:53
     * @param：标签分类添加
     * @User：刘柏林
     */
    public function type_add()
    {
        $this->display();
    }


    /**
     * @date：2018年1月14日04:07:12
     * @param：标签
     * @User：刘柏林
     */
    public function biaoQian()
    {
        $res = M('label')
            ->select();
        $list = array();
        $res2 = array();
        for ($i = 0; $i < count($res); $i++) {
            $res2[$res[$i]['id']] = $res[$i];
        }
        $k = 0;
        $pid = 0;
        foreach ($res2 as $key => $val) {
            if ($val['parent_id'] != $pid) {
                $k++;
            }
            $idlist[$key] = $val['id'];
            $pidlist[$val['parent_id']][$key] = $key;
            if (in_array($val['parent_id'], $idlist)) {

                if ($res2[$val['parent_id']]['type'] == 0) {
                    $list[0]['p'] = '';
                    $list[0]['c'][$k]['n'] = $res2[$val['parent_id']]['name'];
                    $list[0]['c'][$k]['a'][] = array('s' => $val['name'], 'id' => $key);
                }
            }
            $pid = $val['parent_id'];
        }
        $testJSON = $list;
        $ItJson = "{positionlist:" . json_encode($testJSON, JSON_UNESCAPED_UNICODE) . "}";
        $generateJsContent = "var professionaldata = '" . $ItJson . "'";

        $generteFileName = 'professionalLbl.data.min.js';
        $counter_file = 'public/js/information/' . $generteFileName;//文件名及路径,在当前目录下新建aa.txt文件

        $fopen = fopen($counter_file, 'wb ');//新建文件命令
        fputs($fopen, $generateJsContent);//向文件中写入内容;
        fclose($fopen);
        return;
    }

    /**
     * @date 2018年1月18日14:28:33
     * @content 标签列表
     * @User：刘柏林
     */
    private function _lists()
    {
        $where_ands = array("s.parent_id != 0");
        $fields = array(
            'keyword' => array("field" => "s.name", "operator" => "like")
        );

        if (IS_POST) {
            foreach ($fields as $param => $val) {
                if (isset($_POST[$param]) && !empty($_POST[$param])) {
                    $operator = $val['operator'];
                    $field = $val['field'];
                    $_GET[$param] = $_POST[$param];

                    $get = $_POST[$param];
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
                    $get = $_GET[$param];
                    if ($operator == "like") {
                        $get = "%$get%";
                    }
                    array_push($where_ands, "$field $operator '$get'");
                }
            }
        }
        $where = join(" and ", $where_ands);

        $count = M('label')
            ->alias("s")
            ->join("__LABEL__ s1 on s.parent_id = s1.id")
            ->where($where)
            ->count();
        $page = $this->page($count, 20);
        $order_list = M('label')
            ->alias("s")
            ->join("__LABEL__ s1 on s.parent_id = s1.id")
            ->field("s.*,s1.name skill_name")
            ->where($where)
            ->order('s.id desc')
            ->limit($page->firstRow . ',' . $page->listRows)
            ->select();

        $this->assign("page", $page->show('Admin'));
        unset($_GET[C('VAR_URL_PARAMS')]);
        $this->assign("formget", $_GET);
        $this->assign("skillList", $order_list);
    }

    /**
     * @date：2018年1月30日17:35:39
     * @param：分类列表
     * @User：刘柏林
     */
    public function type_index()
    {
        $skillList =$this->skill_model->where(array("parent_id" => 0))->select();
        $this->assign("skillList", $skillList);
        $this->display();
    }
    /**
     * @date：2018年1月12日18:36:07
     * @param 删除
     * @User：刘柏林
     */
    public function delete()
    {
        if (isset($_GET['tid'])) {
            $tid = intval(I("get.tid"));

            if ($this->skill_model->where("id=$tid")->delete()) {
                $this->biaoQian();
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
    public function skillAdd()
    {
        if (IS_POST) {
            $data = I('post');
            $data['type'] = 0;
            $rs = $this->skill_model->add($data);
            if ($rs !== false) {
                $this->biaoQian();
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
     * @date：2018年1月14日18:05:06
     * @param：添加圈子
     * @User：刘柏林
     */
    public function skillTypeAdd()
    {
        if (IS_POST) {
            $data = I('post');
            $data['type'] = 0;
            $data['parent_id'] = 0;
            $rs = $this->skill_model->add($data);
            if ($rs !== false) {
                $this->biaoQian();
                $this->success("保存成功！");
                exit;
            } else {
                $this->error("保存失败！");
                exit;
            }
        }

        $this->display();
    }


}