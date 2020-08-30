<?php
/**
 * @date：2018年1月12日15:08:23
 * @content 朋友圈
 * @User：刘柏林
 */

namespace Admin\Controller;

use Common\Controller\AdminbaseController;

class CircleOfFriendsController extends AdminbaseController
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
    protected $sof_model;
    protected $label_model;
    protected $sof_attachment_model;

    function _initialize()
    {
        parent::_initialize();
        $this->user_model = D("Common/Users");
        $this->post_digg_model = D("Common/PostDigg");
        $this->forum_model = D("Common/Forum");
        $this->forum_post_model = D("Common/ForumPost");
        $this->reply_model = D("Common/Reply");
        $this->sof_model = D("Common/Sof");
        $this->label_model = D("Common/Label");
        $this->sof_attachment_model = D("Common/SofAttachment");
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
     * @date 2018年1月12日15:08:23
     * @content 朋友圈回收站
     * @User：刘柏林
     */
    public function trash_circle()
    {
        $this->trashList();
        $this->display();
    }

    /**
     * @date：2018年1月12日20:06:08
     * @param：首页推荐朋友圈
     * @User：刘柏林
     */
    public function home_circle()
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
     * @date：2018年1月14日22:05:33
     * @param：添加圈主
     * @User：刘柏林
     */
    public function add()
    {
        $this->circleAdd();
        $this->display();
    }

    /**
     * @date：2018年1月14日22:05:33
     * @param：贴吧评论
     * @User：刘柏林
     */
    public function circle_reply()
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
     * @date：2018年1月14日04:07:12
     * @param：标签
     * @User：刘柏林
     */
    public function biaoQian()
    {
        $res = M('skill')
            ->select();
        $list = array();
        $res2 = array();
        for ($i = 0; $i < count($res); $i++) {
            $res2[$res[$i]['id']] = $res[$i];
        }
        $k = 0;
        $pid = 0;
        foreach ($res2 as $key => $val) {
            if ($val['pid'] != $pid) {
                $k++;
            }
            $idlist[$key] = $val['id'];
            $pidlist[$val['pid']][$key] = $key;
            if (in_array($val['pid'], $idlist)) {

                if ($res2[$val['pid']]['type'] == 0) {
                    $list[0]['p'] = '';
                    $list[0]['c'][$k]['n'] = $res2[$val['pid']]['name'];
                    $list[0]['c'][$k]['a'][] = array('s' => $val['name'], 'id' => $key);
                }
            }
            $pid = $val['pid'];
        }
        print_r($list);
        return;
        $testJSON = $list;
        $ItJson = "{positionlist:" . json_encode($testJSON, JSON_UNESCAPED_UNICODE) . "}";
        $generateJsContent = "var professionaldata = '" . $ItJson . "'";
        $generteFileName = 'professionalLbl.data.min.js';
        $counter_file = 'public/js/information/' . $generteFileName;//文件名及路径,在当前目录下新建aa.txt文件

        $fopen = fopen($counter_file, 'wb ');//新建文件命令
        fputs($fopen, $generateJsContent);//向文件中写入内容;
        fclose($fopen);
        print_r($ItJson);
        return;
    }

    /**
     * @date 2018年1月12日15:08:11
     * @content 帖子管理内容
     * @User：刘柏林
     */
    private function _lists()
    {
        $where_ands = array("f.isdel != 1");
        $fields = array(
            'start_time' => array("field" => "f.create_time", "operator" => ">"),
            'end_time' => array("field" => "f.create_time", "operator" => "<"),
            'keyword' => array("field" => "f.content", "operator" => "like"),
            'user_nicename' => array("field" => "u.user_nicename", "operator" => "like")
        );

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
        $where = join(" and ", $where_ands);

        $count = M('sof')
            ->alias("f")
            ->join("__USER__ u on f.uid = u.id")
            ->where($where)
            ->count();
        $page = $this->page($count, 20);
        $forumPostList = M('sof')
            ->alias("f")
            ->join("__USER__ u on f.uid = u.id")
            ->field("f.*,u.name as user_nicename")
            ->where($where)
            ->order('f.create_time desc')
            ->limit($page->firstRow . ',' . $page->listRows)
            ->select();
        foreach ($forumPostList as $k => $item) {
            $forumPostList[$k]['create_time'] = date("Y-m-d H:i:s", $item['create_time']);
        }
        $this->assign("page", $page->show('Admin'));
        unset($_GET[C('VAR_URL_PARAMS')]);
        $this->assign("formget", $_GET);
        $this->assign("sofList", $forumPostList);
    }

    /**
     * @date：2018年1月31日16:46:40
     * @param：根据id查询评论
     * @User：刘柏林
     */
    public function replyOne(){

    }
    /**
     * @date 2018年1月12日15:08:11
     * @content 朋友圈回收站
     * @User：刘柏林
     */
    private function trashList()
    {
        $count = M('sof')
            ->alias("s")
            ->join("__USER__ u on s.uid = u.id")
            ->where(array("s.isdel" => 1))
            ->count();
        $page = $this->page($count, 20);
        $order_list = M('sof')
            ->alias("s")
            ->join("__USER__ u on s.uid = u.id")
            ->field("s.*,u.name as user_nicename")
            ->where(array("s.isdel" => 1))
            ->order('s.create_time desc')
            ->limit($page->firstRow . ',' . $page->listRows)
            ->select();
        foreach ($order_list as $k => $item) {
            $order_list[$k]['create_time'] = date("Y-m-d H:i:s", $item['create_time']);
        }
        $this->assign("page", $page->show('Admin'));
        $this->assign("sofList", $order_list);
    }

    /**
     * @date 2018年1月12日15:08:11
     * @content 首页推荐朋友圈
     * @User：刘柏林
     */
    public function homePostList()
    {
        $where_ands = array("s.isdel != 1");
        array_push($where_ands, "s.ishot = 1");
        $where = join(" and ", $where_ands);
        $count = M('sof')
            ->alias("s")
            ->join("__USER__ u on s.uid = u.id")
            ->where($where)
            ->count();
        $page = $this->page($count, 20);
        $order_list = M('sof')
            ->alias("s")
            ->join("__USER__ u on s.uid = u.id")
            ->field("s.*,u.name as user_nicename")
            ->where($where)
            ->order('s.create_time desc')
            ->limit($page->firstRow . ',' . $page->listRows)
            ->select();
        foreach ($order_list as $k => $item) {
            $order_list[$k]['create_time'] = date("Y-m-d H:i:s", $item['create_time']);
        }
        $this->assign("page", $page->show('Admin'));
        $this->assign("sofIshot", $order_list);
    }

    /**
     * @date：2018年1月13日18:13:27
     * @param：圈子分类
     * @User：刘柏林
     */
    public function circleTypeList()
    {
        $count = M('circle_type')
            ->count();
        $page = $this->page($count, 20);
        $order_list = M('circle_type')
            ->order('listorder asc')
            ->limit($page->firstRow . ',' . $page->listRows)
            ->select();

        $this->assign("page", $page->show('Admin'));
        $this->assign("circleTypeList", $order_list);
    }

    /**
     * @date：2018年1月13日18:13:27
     * @param：专题列表
     * @User：刘柏林
     */
    public function circleList()
    {
        $where_ands = array("f.isdel != 1");
        $fields = array(
            'start_time' => array("field" => "f.create_time", "operator" => ">"),
            'end_time' => array("field" => "f.create_time", "operator" => "<"),
            'keyword' => array("field" => "f.name", "operator" => "like")
        );

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
        $where = join(" and ", $where_ands);

        $count = M('forum')
            ->alias("f")
            ->join("__USER__ u on f.uid = u.id")
            ->join("__CIRCLE_TYPE__ c on f.type_id = c.circle_id")
            ->where($where)
            ->count();
        $page = $this->page($count, 20);
        $masterList = M('forum')
            ->alias("f")
            ->join("__USER__ u on f.uid = u.id")
            ->join("__CIRCLE_TYPE__ c on f.type_id = c.circle_id")
            ->field("f.*,u.name as user_nicename,c.circle_name")
            ->where($where)
            ->order('f.listorder,f.create_time desc')
            ->limit($page->firstRow . ',' . $page->listRows)
            ->select();
        foreach ($masterList as $k => $item) {
            $masterList[$k]['create_time'] = date("Y-m-d H:i:s", $item['create_time']);
        }
        $this->assign("page", $page->show('Admin'));
        unset($_GET[C('VAR_URL_PARAMS')]);
        $this->assign("formget", $_GET);
        $this->assign("forumList", $masterList);
    }

    /**
     * @date：2018年1月12日18:35:37
     * @param：取消推荐热点帖子
     * @User：刘柏林
     */
    public function unRecommend()
    {

        if (isset($_POST['ids']) && $_GET["unrecommend"]) {

            $data["ishot"] = 0;
            $tid = join(",", $_POST['ids']);

            if ($this->sof_model->where("id in ($tid)")->save($data)) {
                $this->success("取消推荐成功！");
            } else {
                $this->error("取消推荐失败！");
            }
        }
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
     * @date：2018年1月12日18:35:19
     * @param：审核
     * @User：刘柏林
     */
    public function check()
    {
        if (isset($_POST['ids']) && $_GET["check"]) {
            $data["status"] = 1;

            $tids = join(",", $_POST['ids']);
            if ($this->posts_model->where("id in ($tids)")->save($data) !== false) {
                $this->success("审核成功！");
            } else {
                $this->error("审核失败！");
            }
        }
        if (isset($_POST['ids']) && $_GET["uncheck"]) {

            $data["status"] = 0;
            $tids = join(",", $_POST['ids']);

            if ($this->posts_model->where("id in ($tids)")->save($data)) {
                $this->success("取消审核成功！");
            } else {
                $this->error("取消审核失败！");
            }
        }
    }

    /**
     * @date：2018年1月14日16:12:29
     * @param：圈主审核
     * @User：刘柏林
     */
    public function circleCheck()
    {
        if (isset($_GET['ids']) && $_GET["check"]) {
            $data["state"] = 1;
            $tid = $_GET['ids'];
            if ($this->forum_model->where(array("id" => $tid))->save($data) !== false) {
                $this->success("显示成功！");
            } else {
                $this->error("显示失败！");
            }
        }
        if (isset($_GET['ids']) && $_GET["uncheck"]) {

            $data["state"] = 0;
            $tid = $_GET['ids'];

            if ($this->forum_model->where(array("id" => $tid))->save($data)) {
                $this->success("已隐藏！");
            } else {
                $this->error("隐藏失败！");
            }
        }
    }


    /**
     * @date：2018年1月12日18:35:28
     * @param：帖子顶置
     * @User：刘柏林
     */
    public function top()
    {
        if (isset($_POST['ids']) && $_GET["top"]) {
            $data["istop"] = 1;

            $ids = join(",", $_POST['ids']);

            if ($this->sof_model->where("id in ($ids)")->save($data) !== false) {
                $this->success("置顶成功！");
            } else {
                $this->error("置顶失败！");
            }
        }
        if (isset($_POST['ids']) && $_GET["untop"]) {

            $data["istop"] = 0;
            $tid = join(",", $_POST['ids']);

            if ($this->sof_model->where("id in ($tid)")->save($data)) {
                $this->success("取消置顶成功！");
            } else {
                $this->error("取消置顶失败！");
            }
        }
    }

    /**
     * @date：2018年1月12日18:35:37
     * @param：推荐
     * @User：刘柏林
     */
    public function recommend()
    {
        if (isset($_POST['ids']) && $_GET["recommend"]) {
            $data["ishot"] = 1;

            $tid = join(",", $_POST['ids']);

            if ($this->sof_model->where("id in ($tid)")->save($data) !== false) {
                $this->success("推荐成功！");
            } else {
                $this->error("推荐失败！");
            }
        }
        if (isset($_POST['ids']) && $_GET["unrecommend"]) {

            $data["ishot"] = 0;
            $tid = join(",", $_POST['ids']);

            if ($this->sof_model->where("id in ($tid)")->save($data)) {
                $this->success("取消推荐成功！");
            } else {
                $this->error("取消推荐失败！");
            }
        }
    }

    /**
     * @date：2018年1月12日18:35:37
     * @param：推荐
     * @User：刘柏林
     */
    public function recommendReply()
    {
        if (isset($_POST['ids']) && $_GET["recommend"]) {
            $data["ishot"] = 1;

            $tids = join(",", $_POST['ids']);

            if ($this->reply_model->where("id in ($tids)")->save($data) !== false) {
                $this->success("推荐成功！");
            } else {
                $this->error("推荐失败！");
            }
        }
        if (isset($_POST['ids']) && $_GET["unrecommend"]) {

            $data["ishot"] = 0;
            $tids = join(",", $_POST['ids']);

            if ($this->reply_model->where("id in ($tids)")->save($data)) {
                $this->success("取消推荐成功！");
            } else {
                $this->error("取消推荐失败！");
            }
        }
    }

    /**
     * @date：2018年1月12日18:35:37
     * @param：圈主推荐
     * @User：刘柏林
     */
    public function masterRecommend()
    {
        if (isset($_GET['ids']) && $_GET["check"]) {
            $data["recommend"] = 1;
            $tids = $_GET['ids'];
            if ($this->master_model->where(array("master_id" => $tids))->save($data) !== false) {
                $this->success("推荐成功！");
            } else {
                $this->error("推荐失败！");
            }
        }
        if (isset($_GET['ids']) && $_GET["uncheck"]) {

            $data["recommend"] = 0;
            $tids = $_GET['ids'];

            if ($this->master_model->where(array("master_id" => $tids))->save($data)) {
                $this->success("取消推荐成功！");
            } else {
                $this->error("取消推荐失败！");
            }
        }

    }

    /**
     * @Date 2018年1月12日15:22:22
     * @content 是否禁言
     * @User：刘柏林
     */
    public function gossip()
    {
        if (isset($_POST['ids']) && $_GET["gossipId"]) {
            $data["whether_to_block"] = 1;

            $tids = join(",", $_POST['ids']);
            if ($this->posts_model->where("id in ($tids)")->save($data) !== false) {
                $this->success("禁言成功！");
            } else {
                $this->error("禁言失败！");
            }
        }
        if (isset($_POST['ids']) && $_GET["unGossipId"]) {

            $data["whether_to_block"] = 0;
            $tids = join(",", $_POST['ids']);

            if ($this->posts_model->where("id in ($tids)")->save($data)) {
                $this->success("取消禁言成功！");
            } else {
                $this->error("取消禁言失败！");
            }
        }
    }

    /**
     * @Date 2018年1月12日15:22:22
     * @content 圈子类型是否禁用
     * @User：刘柏林
     */
    public function circleNaBled()
    {
        if (isset($_POST['ids']) && $_GET["unNaBledId"]) {
            $data["nabled"] = 0;
            $tids = join(",", $_POST['ids']);
            if ($this->circle_type_model->where("circle_id in ($tids)")->save($data) !== false) {
                $this->success("禁言成功！");
            } else {
                $this->error("禁言失败！");
            }
        }
        if (isset($_POST['ids']) && $_GET["NaBledId"]) {
            $data["nabled"] = 1;
            $tids = join(",", $_POST['ids']);

            if ($this->circle_type_model->where("circle_id in ($tids)")->save($data)) {
                $this->success("启用成功！");
            } else {
                $this->error("启用失败！");
            }
        }
    }

    /**
     * @Date 2018年1月12日15:22:22
     * @content 还原清空帖子
     * @User：刘柏林
     */
    public function trashInfo()
    {
        if (isset($_POST['ids']) && $_GET["trashId"]) {
            $data["isdel"] = 0;

            $tid = join(",", $_POST['ids']);
            if ($this->sof_model->where("id in ($tid)")->save($data) !== false) {
                $this->success("还原成功！");
            } else {
                $this->error("还原失败！");
            }
        }
        if (isset($_POST['ids']) && $_GET["uncheckId"]) {

            $data["isdel"] = 1;
            $tid = join(",", $_POST['ids']);

            if ($this->sof_model->where("id in ($tid)")->save($data)) {
                $this->success("删除成功！");
            } else {
                $this->error("删除失败！");
            }
        }
    }

    /**
     * @date：2018年1月12日18:36:07
     * @param删除 朋友圈
     * @User：刘柏林
     */
    public function delete()
    {
        if (isset($_GET['tid'])) {
            $tid = intval(I("get.tid"));
            $data['isdel'] = 1;//设为伪删除
            if ($this->sof_model->where("id=$tid")->save($data)) {
                $this->success("删除成功！");
            } else {
                $this->error("删除失败！");
            }
        }
        if (isset($_POST['ids'])) {
            $tid = join(",", $_POST['ids']);
            $data['isdel'] = 1;//设为伪删除
            if ($this->sof_model->where("id in ($tid)")->save($data)) {
                $this->success("删除成功！");
            } else {
                $this->error("删除失败！");
            }
        }
    }

    /**
     * @date：2018年1月12日18:36:07
     * @param  删除评论
     * @User：刘柏林
     */
    public function deleteReply()
    {
        if (isset($_GET['tid'])) {
            $tid = intval(I("get.tid"));
            $data['isdel'] = 1;//设为伪删除
            if ($this->reply_model->where("id=$tid")->save($data)) {
                $this->success("删除成功！");
            } else {
                $this->error("删除失败！");
            }
        }
        if (isset($_POST['ids'])) {
            $tid = join(",", $_POST['ids']);
            $data['isdel'] = 1;//设为伪删除
            if ($this->reply_model->where("id in ($tid)")->save($data)) {
                $this->success("删除成功！");
            } else {
                $this->error("删除失败！");
            }
        }
    }

    /**
     * @date：2018年1月12日18:36:07
     * @param  删除专题
     * @User：刘柏林
     */
    public function circleDel()
    {
        if (isset($_GET['isdel'])) {
            $tid = intval(I("get.isdel"));
            $data['isdel'] = 1;//设为伪删除
            if ($this->forum_model->where(array("id" => $tid))->save($data)) {
                $this->success("删除成功！");
            } else {
                $this->error("删除失败！");
            }
        }

    }

    /**
     * @date：2018年1月14日17:23:48
     * @param：圈主删除
     * @User：刘柏林
     */
    public function masterIsDel()
    {
        if (isset($_GET['ids'])) {
            $tids = $_GET['ids'];
            $data['is_del'] = -1;//设为伪删除
            if ($this->master_model->where(array("master_id" => $tids))->save($data)) {
                $this->success("解散成功！");
            } else {
                $this->error("解散失败！");
            }
        }
    }


    /**
     * @date：2018年1月14日17:23:48
     * @param：移除圈主推荐
     * @User：刘柏林
     */
    public function RemoveCircleRecommend()
    {
        if (isset($_GET['ids'])) {
            $tid = $_GET['ids'];
            $data['ishot'] = 0;
            if ($this->sof_model->where(array("id" => $tid))->save($data)) {
                $this->success("移除推荐成功！");
            } else {
                $this->error("移除推荐失败！");
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
     * @date：2018年1月17日18:10:40
     * @param：点赞帖子
     * @User：刘柏林
     */
    public function giveTheThumbs()
    {
        //$data['uid'] =$_POST['uid'];//点赞人
        //$data['post_id'] =$_POST['post_id'];//被点赞的帖子id
        //$data['c_time'] =$_POST['c_time'];//点赞时间
        $data['uid'] = 1;//点赞人
        $data['post_id'] = 1;//被点赞的帖子id
        $data['undigg'] = 0;//是否取消点赞

        $data['c_time'] = time();//点赞时间
        $data['fabulous'] = 1;
        if ($data['undigg'] == 1) {
            $data['fabulous'] = 1;
            M('post')->where(array("id" => $data['post_id']))->setInc('digg_count', 1);
            $rs = $this->post_digg_model->add($data);
            if ($rs !== false) {
                $this->success("保存成功！");
                exit;
            } else {
                $this->error("保存失败！");
                exit;
            }
        } else {
            $data['fabulous'] = 0;
            M('post')->where(array("id" => $data['post_id']))->setDec('digg_count', 1);
            $diggList = $this->post_digg_model->where(array("uid" => $data['uid'], "post_id" => $data['post_id']))->find();
            $rs = $this->post_digg_model->where(array("id" => $diggList['id']))->save($data);
            if ($rs !== false) {
                $this->success("保存成功！");
                exit;
            } else {
                $this->error("保存失败！");
                exit;
            }
        }
    }


    /**
     * @date：2018年1月19日16:12:22
     * @param：修改朋友圈
     * @User：刘柏林
     */
    public function edit_circle()
    {
        $id = I('id');
        $sofList = $this->sof_model->where("id=$id")->find();
        $sofList['create_time'] = date("Y-m-d H:i:s", $sofList['create_time']);
        $this->assign("post", $sofList);
        $this->display();
    }

    /**
     * @date：2018年1月19日16:12:22
     * @param：修改朋友圈
     * @User：刘柏林
     */
    public function editPostSub()
    {
        if (IS_POST) {
            $data = I('post');

            $data["update_time"] = strtotime($data['create_time']);
            $data['content'] = htmlspecialchars_decode($data['content']);
            $result = $this->sof_model->save($data);
            if ($result !== false) {
                $this->success("保存成功！");
            } else {
                $this->error("保存失败！");
            }

        }

    }

    /**
     * @date：2018年1月21日21:08:02
     * @param 朋友圈类型
     * @User：刘柏林
     */
    public function select()
    {
        $id = $_GET["id"];
        $SofRes = $this->sof_model->where(array("id" => $id))->find();
        $this->ajaxReturn(array("info" => $SofRes['content']));
    }

    /**
     * @date：2018年1月21日21:08:02
     * @param 朋友圈类型
     * @User：刘柏林
     */
    public function labelStr()
    {
        $id['label_id'] = $_GET["id"];
        $sof_id = $_GET["sof_id"];
        $SofRes = $this->sof_model->where(array("id" => $sof_id))->save($id);
        echo 1;
    }

    /**
     * @date：2018年1月18日14:22:03
     * @param：朋友圈评论列表
     * @User：刘柏林
     */
    public function postReplyList()
    {
        $where_ands = array("r.isdel = 0");
        $fields = array(
            'start_time' => array("field" => "r.create_time", "operator" => ">"),
            'end_time' => array("field" => "r.create_time", "operator" => "<"),
            'keyword' => array("field" => "r.content", "operator" => "like")
        );

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
        array_push($where_ands, "r.tablename = 'sof'");
        $where = join("  and ", $where_ands);

        $count = M('reply')
            ->alias("r")
            ->join("__USER__ u on r.uid = u.id")
            ->join("__SOF__ s on r.object_id = s.id")
            ->join("__USER__ u1 on s.uid = u1.id")
            ->where($where)
            ->count();
        $page = $this->page($count, 20);
        $postReply = M('reply')
            ->alias("r")
            ->join("__USER__ u on r.uid = u.id")
            ->join("__SOF__ s on r.object_id = s.id")
            ->join("__USER__ u1 on s.uid= u1.id")
            ->field("r.*,u.name username1,u1.name username2")
            ->where($where)
            ->order('r.create_time desc')
            ->limit($page->firstRow . ',' . $page->listRows)
            ->select();
        foreach ($postReply as $k => $item) {
            $postReply[$k]['create_time'] = date("Y-m-d H:i:s", $item['create_time']);
        }
        $this->assign("page", $page->show('Admin'));
        unset($_GET[C('VAR_URL_PARAMS')]);
        $this->assign("formget", $_GET);
        $this->assign("sofReply", $postReply);
    }

    /**
     * @date：2018年1月18日14:22:03
     * @param：评论回收站
     * @User：刘柏林
     */
    public function circle_reply_del()
    {
        $where_ands = array("r.isdel = 1");
        $fields = array(
            'start_time' => array("field" => "r.create_time", "operator" => ">"),
            'end_time' => array("field" => "r.create_time", "operator" => "<"),
            'keyword' => array("field" => "r.content", "operator" => "like")
        );

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
        array_push($where_ands, "r.tablename = 'sof'");
        $where = join("  and ", $where_ands);

        $count = M('reply')
            ->alias("r")
            ->join("__USER__ u on r.uid = u.id")
            ->join("__SOF__ s on r.object_id = s.id")
            ->join("__USER__ u1 on s.uid= u1.id")
            ->where($where)
            ->count();
        $page = $this->page($count, 20);
        $postReply = M('reply')
            ->alias("r")
            ->join("__USER__ u on r.uid = u.id")
            ->join("__SOF__ s on r.object_id = s.id")
            ->join("__USER__ u1 on s.uid= u1.id")
            ->field("r.*,u.name username1,u1.name username2")
            ->where($where)
            ->order('r.create_time desc')
            ->limit($page->firstRow . ',' . $page->listRows)
            ->select();
        foreach ($postReply as $k => $item) {
            $postReply[$k]['create_time'] = date("Y-m-d H:i:s", $item['create_time']);
        }
        $this->assign("page", $page->show('Admin'));
        unset($_GET[C('VAR_URL_PARAMS')]);
        $this->assign("formget", $_GET);
        $this->assign("sofReply", $postReply);
        $this->display();
    }

    /**
     * @date：2018年1月18日14:22:03
     * @param 朋友圈热门推荐评论
     * @User：刘柏林
     */
    public function circle_reply_home()
    {
        $where_ands = array("r.ishot = 1");
        $fields = array(
            'start_time' => array("field" => "r.create_time", "operator" => ">"),
            'end_time' => array("field" => "r.create_time", "operator" => "<"),
            'keyword' => array("field" => "r.content", "operator" => "like")
        );

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
        array_push($where_ands, "r.tablename = 'sof'");
        $where = join("  and ", $where_ands);

        $count = M('reply')
            ->alias("r")
            ->join("__USER__ u on r.uid = u.id")
            ->join("__SOF__ s on r.object_id = s.id")
            ->join("__USER__ u1 on s.uid= u1.id")
            ->where($where)
            ->count();
        $page = $this->page($count, 20);
        $postReply = M('reply')
            ->alias("r")
            ->join("__USER__ u on r.uid = u.id")
            ->join("__SOF__ s on r.object_id = s.id")
            ->join("__USER__ u1 on s.uid= u1.id")
            ->field("r.*,u.name username1,u1.name username2")
            ->where($where)
            ->order('r.create_time desc')
            ->limit($page->firstRow . ',' . $page->listRows)
            ->select();
        foreach ($postReply as $k => $item) {
            $postReply[$k]['create_time'] = date("Y-m-d H:i:s", $item['create_time']);
        }
        $this->assign("page", $page->show('Admin'));
        unset($_GET[C('VAR_URL_PARAMS')]);
        $this->assign("formget", $_GET);
        $this->assign("sofReply", $postReply);
        $this->display();
    }

    /**
     * @date：2018年1月14日17:23:48
     * @param：评论恢复删除
     * @User：刘柏林
     */
    public function recoveryReply()
    {

        if (isset($_POST['ids'])) {

            $tid = join(",", $_POST['ids']);
            $data['isdel'] = 0;//恢复
            if ($this->reply_model->where("id in ($tid)")->save($data)) {
                $this->success("恢复成功！");
            } else {
                $this->error("恢复失败！");
            }
        }
    }

    /**
     * @date：2018年1月24日16:26:31
     * @param：上传文件
     * @User：刘柏林
     */
    function upload_file()
    {
        $savepath = date('Ymd') . '/';
        $config = array(
            'FILE_UPLOAD_TYPE' => sp_is_sae() ? "Sae" : 'Local',//TODO 其它存储类型暂不考虑
            'rootPath' => './' . C("UPLOADPATH"),
            'savePath' => $savepath,
            'maxSize' => 10485761111,//50M
            'saveName' => array('uniqid', ''),
            'exts' => array('gif','mp4', 'jpg', 'sql'),
            'autoSub' => false,
        );

        ini_set('max_execution_time', '0');
        $upload = new \Think\Upload($config);//
        $info = $upload->upload();
        //开始上传
        if ($info) {
            //上传成功
            $file = $info['uploadkey']['savepath'] . $info['uploadkey']['savename'];
            echo json_encode(array('status' => '1', 'type' => I('param.type'), 'url' => C("TMPL_PARSE_STRING.__UPLOAD__") . $file, 'file_alt' => $info['uploadkey']['name'], 'file_size' => $info['uploadkey']['size']));
            exit;

        } else {
            //上传失败，返回错误
            echo json_encode(array('status' => '0', 'info' => $upload->getError()));
            exit;
        }
    }

    /**
     * @date：2018年1月18日23:38:40
     * @param：添加朋友圈
     * @User：刘柏林
     */
    public function circleAddOne()
    {
        if (IS_POST) {

            $data = I('post');
            $data["create_time"] = strtotime($data['create_time']);

//            $data['uid'] = get_current_admin_id();
            $data['uid'] = 1;
            $data['content'] = htmlspecialchars_decode($data['content']);
            $result = $this->sof_model->add($data);
            if ($_POST["types"] == 1) {
                $dataAttachment["url"] = sp_asset_relative_url($_POST['url']);
                $dataAttachment["object_id"] = $result;
                $dataAttachment["create_time"] = $data["create_time"];
                $dataAttachment["tablename"] = "sof";
                $dataAttachment["state"] = $data['state'];
                $dataAttachment["type"] = $_POST["types"];
                $dataAttachment["uid"] = 1;
                $dataAttachment["size"] = $_POST['size'];
                $result = $this->sof_attachment_model->add($dataAttachment);
            } else {
                $dataList = $_POST['photos_url'];
                $sizes = $_POST['sizes'];
                $sizesList = [];
                foreach ($sizes as $k1 => $item1) {
                    $sizesList[$k1] = $item1;
                }
                foreach ($dataList as $k => $item) {
                    $dataAttachment["state"] = $data['state'];
                    $dataAttachment["url"] = sp_asset_relative_url($item);
                    $dataAttachment["object_id"] = $result;
                    $dataAttachment["type"] = $_POST["types"];
                    $dataAttachment["create_time"] = $data["create_time"];
                    $dataAttachment["tablename"] = "sof";
                    $dataAttachment["size"] =$sizesList[$k];
                    $dataAttachment["uid"] = 1;
                    $result = $this->sof_attachment_model->add($dataAttachment);
                }

            }

//
//            $data = I('post');
//            print_r($_POST);return;
//            $data["create_time"] = strtotime($data['create_time']);
//            $data["url"] = sp_asset_relative_url($data['url']);
//            $_POST["photos_url"] =  $_POST["photos_url"];
//
////            $_POST['smeta'] = sp_asset_relative_url($_POST['smeta']['thumb']);
////            $data['thumb'] = $_POST['smeta'];
//            $data['uid'] = get_current_admin_id();
//            $data['content'] = htmlspecialchars_decode($data['content']);
//            $result = $this->forum_post_model->add($data);

            if ($result) {
                $this->success("添加成功！");
            } else {
                $this->error("添加失败！");
            }

        }
    }
}