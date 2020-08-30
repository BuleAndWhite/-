<?php
/**
 * @date：2018年1月12日15:08:23
 * @content：帖子
 * @User：刘柏林
 */

namespace Admin\Controller;

use Common\Controller\AdminbaseController;

class InformationController extends AdminbaseController
{
    protected $user_model;
    protected $circle_type_model;
    protected $master_model;//弃用
    protected $forum_model;//专题定义
    protected $forum_post_model;//专题定义
    protected $master_apply_model;
    protected $post_digg_model;
    protected $reply_model;

    protected $post_reply_model;

    function _initialize()
    {
        parent::_initialize();
        $this->user_model = D("Common/Users");
        $this->circle_type_model = D("Common/ForumType");
        $this->post_digg_model = D("Common/PostDigg");
        $this->forum_model = D("Common/Forum");
        $this->forum_post_model = D("Common/ForumPost");
        $this->reply_model = D("Common/Reply");
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
        $this->circleAdd();
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
     * @date：2018年1月18日23:20:34
     * @param：添加帖子
     * @User：刘柏林
     */
    public function plate_add()
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
            'keyword' => array("field" => "f.content", "operator" => "like")
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

        $count = M('forum_post')
            ->alias("f")
            ->join("__USERS__ u on f.uid = u.id")
            ->join("__FORUM__ f1 on f.forum_id = f1.id")
            ->where($where)
            ->count();
        $page = $this->page($count, 20);
        $forumPostList = M('forum_post')
            ->alias("f")
            ->join("__USERS__ u on f.uid = u.id")
            ->join("__FORUM__ f1 on f.forum_id = f1.id")
            ->field("f.*,u.user_nicename,f1.name as fname")
            ->where($where)
            ->order('f.listorder,f.create_time desc')
            ->limit($page->firstRow . ',' . $page->listRows)
            ->select();
        foreach ($forumPostList as $k => $item) {
            $forumPostList[$k]['create_time'] = date("Y-m-d H:i:s", $item['create_time']);
        }
        $this->assign("page", $page->show('Admin'));
        unset($_GET[C('VAR_URL_PARAMS')]);
        $this->assign("formget", $_GET);
        $this->assign("forumPostList", $forumPostList);
    }

    /**
     * @date 2018年1月12日15:08:11
     * @content 帖子回收站
     * @User：刘柏林
     */
    private function trashList()
    {
        $count = M('forum_post')
            ->alias("f")
            ->join("__USERS__ u on f.uid = u.id")
            ->join("__FORUM__ f1 on f.forum_id = f1.id")
            ->where(array("f.isdel" => 1))
            ->count();
        $page = $this->page($count, 20);
        $order_list = M('forum_post')
            ->alias("f")
            ->join("__USERS__ u on f.uid = u.id")
            ->join("__FORUM__ f1 on f.forum_id = f1.id")
            ->field("f.*,u.user_nicename,f1.name")
            ->where(array("f.isdel" => 1))
            ->order('f.create_time desc')
            ->limit($page->firstRow . ',' . $page->listRows)
            ->select();
        foreach ($order_list as $k => $item) {
            $order_list[$k]['create_time'] = date("Y-m-d H:i:s", $item['create_time']);
        }
        $this->assign("page", $page->show('Admin'));
        $this->assign("forumPostList", $order_list);
    }

    /**
     * @date 2018年1月12日15:08:11
     * @content 首页推荐帖子
     * @User：刘柏林
     */
    public function homePostList()
    {
        $where_ands = array("f.isdel != 1");
        array_push($where_ands, "f.ishot = 1");
        $where = join(" and ", $where_ands);
        $count = M('forum_post')
            ->alias("f")
            ->join("__USERS__ u on f.uid = u.id")
            ->join("__FORUM__ f1 on f.forum_id = f1.id")
            ->where($where)
            ->count();
        $page = $this->page($count, 20);
        $order_list = M('forum_post')
            ->alias("f")
            ->join("__USERS__ u on f.uid = u.id")
            ->join("__FORUM__ f1 on f.forum_id = f1.id")
            ->field("f.*,u.user_nicename,f1.name")
            ->where($where)
            ->order('f.create_time desc')
            ->limit($page->firstRow . ',' . $page->listRows)
            ->select();
        foreach ($order_list as $k => $item) {
            $order_list[$k]['create_time'] = date("Y-m-d H:i:s", $item['create_time']);
        }
        $this->assign("page", $page->show('Admin'));
        $this->assign("forumPostIshot", $order_list);
    }

    /**
     * @date：2018年1月13日18:13:27
     * @param：圈子分类
     * @User：刘柏林
     */
    public function circleTypeList()
    {
        $count = M('forum_type')
            ->count();
        $page = $this->page($count, 20);
        $order_list = M('forum_type')
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
            ->join("__USERS__ u on f.uid = u.id")
            ->join("__FORUM_TYPE__ c on f.type_id = c.id")
            ->where($where)
            ->count();
        $page = $this->page($count, 20);
        $masterList = M('forum')
            ->alias("f")
            ->join("__USERS__ u on f.uid = u.id")
            ->join("__FORUM_TYPE__ c on f.type_id = c.id")
            ->field("f.*,u.user_nicename,c.name fname")
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

            if ($this->forum_post_model->where("id in ($tid)")->save($data)) {
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

            if ($this->forum_post_model->where("id in ($ids)")->save($data) !== false) {
                $this->success("置顶成功！");
            } else {
                $this->error("置顶失败！");
            }
        }
        if (isset($_POST['ids']) && $_GET["untop"]) {

            $data["istop"] = 0;
            $tids = join(",", $_POST['ids']);

            if ($this->forum_post_model->where("id in ($tids)")->save($data)) {
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

            $tids = join(",", $_POST['ids']);

            if ($this->forum_post_model->where("id in ($tids)")->save($data) !== false) {
                $this->success("推荐成功！");
            } else {
                $this->error("推荐失败！");
            }
        }
        if (isset($_POST['ids']) && $_GET["unrecommend"]) {

            $data["ishot"] = 0;
            $tids = join(",", $_POST['ids']);

            if ($this->forum_post_model->where("id in ($tids)")->save($data)) {
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
            if ($this->circle_type_model->where("id in ($tids)")->save($data) !== false) {
                $this->success("禁言成功！");
            } else {
                $this->error("禁言失败！");
            }
        }
        if (isset($_POST['ids']) && $_GET["NaBledId"]) {
            $data["nabled"] = 1;
            $tids = join(",", $_POST['ids']);

            if ($this->circle_type_model->where("id in ($tids)")->save($data)) {
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
            if ($this->forum_post_model->where("id in ($tid)")->save($data) !== false) {
                $this->success("还原成功！");
            } else {
                $this->error("还原失败！");
            }
        }
        if (isset($_POST['ids']) && $_GET["uncheckId"]) {

            $data["isdel"] = 1;
            $tid = join(",", $_POST['ids']);

            if ($this->forum_post_model->where("id in ($tid)")->save($data)) {
                $this->success("删除成功！");
            } else {
                $this->error("删除失败！");
            }
        }
    }

    /**
     * @date：2018年1月12日18:36:07
     * @param删除
     * @User：刘柏林
     */
    public function delete()
    {
        if (isset($_GET['tid'])) {
            $tid = intval(I("get.tid"));
            $data['isdel'] = 1;//设为伪删除
            if ($this->forum_post_model->where("id=$tid")->save($data)) {
                $this->success("删除成功！");
            } else {
                $this->error("删除失败！");
            }
        }
        if (isset($_POST['ids'])) {
            $tids = join(",", $_POST['ids']);
            $data['isdel'] = 1;//设为伪删除
            if ($this->forum_post_model->where("id in ($tids)")->save($data)) {
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
     * @date：2018年1月17日21:44:03
     * @param：评论删除
     * @User：刘柏林
     */
    public function replyDel()
    {

        if (isset($_GET['ids'])) {
            $tid = $_GET['ids'];
            $data['isdel'] = 1;//设为伪删除
            if ($this->reply_model->where(array("id" => $tid))->save($data)) {
                $this->success("删除成功！");
            } else {
                $this->error("删除失败！");
            }
        }
    }

    /**
     * @date：2018年1月14日17:23:48
     * @param：专题恢复删除
     * @User：刘柏林
     */
    public function restoreMasterIsDel()
    {

        if (isset($_GET['ids'])) {
            $tid = $_GET['ids'];
            $data['isdel'] = 0;//恢复
            if ($this->forum_model->where(array("id" => $tid))->save($data)) {
                $this->success("恢复成功！");
            } else {
                $this->error("恢复失败！");
            }
        }
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
     * @date：2018年1月14日17:23:48
     * @param：移除圈主推荐
     * @User：刘柏林
     */
    public function RemoveCircleRecommend()
    {
        if (isset($_GET['ids'])) {
            $tids = $_GET['ids'];
            $data['recommend'] = 0;
            if ($this->master_model->where(array("master_id" => $tids))->save($data)) {
                $this->success("移除推荐成功！");
            } else {
                $this->error("移除推荐失败！");
            }
        }
    }

    /**
     * @date：2018年1月14日18:05:06
     * @param：专题回收站
     * @User：刘柏林
     */
    public function circleDelList()
    {
        $where_ands = array("f.isdel != 0");
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
            ->join("__USERS__ u on f.uid = u.id")
            ->join("__FORUM_TYPE__ c on f.type_id = c.id")
            ->where($where)
            ->count();
        $page = $this->page($count, 20);
        $masterList = M('forum')
            ->alias("f")
            ->join("__USERS__ u on f.uid = u.id")
            ->join("__FORUM_TYPE__ c on f.type_id = c.id")
            ->field("f.*,u.user_nicename,c.name fname")
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
     * @date：2018年1月14日18:05:06
     * @param：圈主推荐
     * @User：刘柏林
     */
    public function circleDelRecommend()
    {
        $where_ands = array("m.recommend = 1");
        $fields = array(
            'start_time' => array("field" => "m.ctime", "operator" => ">"),
            'end_time' => array("field" => "m.ctime", "operator" => "<"),
            'keyword' => array("field" => "m.master_name", "operator" => "like")
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

        $count = M('master')
            ->alias("m")
            ->join("__USERS__ u on m.uid = u.id")
            ->join("__FORUM_TYPE__ c on m.cid = c.id")
            ->where($where)
            ->count();
        $page = $this->page($count, 20);
        $masterList = M('master')
            ->alias("m")
            ->join("__USERS__ u on m.uid = u.id")
            ->join("__FORUM_TYPE__ c on m.cid = c.id")
            ->field("m.*,u.user_nicename,c.name")
            ->where($where)
            ->order('m.ctime desc')
            ->limit($page->firstRow . ',' . $page->listRows)
            ->select();
        foreach ($masterList as $k => $item) {
            $masterList[$k]['ctime'] = date("Y-m-d H:i:s", $item['ctime']);
        }
        $this->assign("page", $page->show('Admin'));
        unset($_GET[C('VAR_URL_PARAMS')]);
        $this->assign("formget", $_GET);
        $this->assign("masterList", $masterList);
    }

    /**
     * @date：2018年1月14日18:05:06
     * @param：添加圈子
     * @User：刘柏林
     */
    public function circleAdd()
    {
        $circleType = M("forum_type")->select();
        $this->assign("circleType", $circleType);
    }

    /**
     * @date：2018年1月14日18:05:06
     * @param：添加专题
     * @User：刘柏林
     */
    public function circleAddPost()
    {
        if (IS_POST) {
            $data = I('post');
            //成员数 弃用
//            $data['follower_count'] = count($_POST['adminId']);
//            $data['admin_uid'] = implode(',', $_POST['adminId']);

            $data["create_time"] = strtotime($data['create_time']);
            $_POST['smeta'] = sp_asset_relative_url($_POST['smeta']['thumb']);
            $data['thumb'] = $_POST['smeta'];
            $data['uid'] = get_current_admin_id();
            $rs = $this->forum_model->add($data);
            if ($rs !== false) {
                $this->success("保存成功！");
                exit;
            } else {
                $this->error("保存失败！");
                exit;
            }
        }
        $this->list = $this->forum_model->select();
        $this->display();
    }

    /**
     * @date：2018年1月14日18:05:06
     * @param：添加板块
     * @User：刘柏林
     */
    public function plateAddPost()
    {
        if (IS_POST) {
            $data = I('post');

            $rs = $this->circle_type_model->add($data);
            if ($rs !== false) {
                $this->success("保存成功！");
                exit;
            } else {
                $this->error("保存失败！");
                exit;
            }
        }
        $this->list = $this->forum_model->select();
        $this->display();
    }

    /**
     * @date：2018年1月17日18:10:40
     * @param：搜索用户设置圈主
     * @User：刘柏林
     */
    public function masterSearch()
    {
        $Search = $_POST['Search'];
        $condtion['user_nicename'] = array("like", "%" . $Search . "%");

        $userList = $this->user_model->where($condtion)->select();
        if ($userList && $Search) {
            $this->ajaxReturn(array("code" => 1, "clicks" => $Search+1));
        } else {
            $this->ajaxReturn(array("code" => -1));
        }
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
     * @date：2018年1月18日14:22:03
     * @param：评论列表
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
        array_push($where_ands, "r.tablename = 'forum_post'");
        $where = join("  and ", $where_ands);

        $count = M('reply')
            ->alias("r")
            ->join("__USER__ u on r.uid = u.id")
            ->join("__FORUM_POST__ f on r.object_id = f.id")
            ->join("__USER__ u1 on f.uid = u1.id")
            ->where($where)
            ->count();
        $page = $this->page($count, 20);
        $postReply = M('reply')
            ->alias("r")
            ->join("__USER__ u on r.uid = u.id")
            ->join("__FORUM_POST__ f on r.object_id = f.id")
            ->join("__USER__ u1 on f.uid = u1.id")
            ->field("r.*,u.name username1,u1.name username2,f.title")
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
        $this->assign("postReply", $postReply);
    }

    /**
     * @date：2018年1月18日14:22:03
     * @param：评论回收站
     * @User：刘柏林
     */
    public function post_reply_del()
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
        array_push($where_ands, "r.tablename = 'forum_post'");
        $where = join("  and ", $where_ands);

        $count = M('reply')
            ->alias("r")
            ->join("__USERS__ u on r.uid = u.id")
            ->join("__FORUM_POST__ f on r.object_id = f.id")
            ->join("__USERS__ u1 on r.object_id = u1.id")
            ->where($where)
            ->count();
        $page = $this->page($count, 20);
        $postReply = M('reply')
            ->alias("r")
            ->join("__USERS__ u on r.uid = u.id")
            ->join("__FORUM_POST__ f on r.object_id = f.id")
            ->join("__USERS__ u1 on r.object_id = u1.id")
            ->field("r.*,u.user_nicename username1,u1.user_nicename username2,f.title")
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
        $this->assign("postReply", $postReply);
        $this->display();
    }

    /**
     * @date：2018年1月18日14:22:03
     * @param 热门推荐评论
     * @User：刘柏林
     */
    public function post_reply_home()
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
        array_push($where_ands, "r.tablename = 'forum_post'");
        $where = join("  and ", $where_ands);

        $count = M('reply')
            ->alias("r")
            ->join("__USERS__ u on r.uid = u.id")
            ->join("__FORUM_POST__ f on r.object_id = f.id")
            ->join("__USERS__ u1 on r.object_id = u1.id")
            ->where($where)
            ->count();
        $page = $this->page($count, 20);
        $postReply = M('reply')
            ->alias("r")
            ->join("__USERS__ u on r.uid = u.id")
            ->join("__FORUM_POST__ f on r.object_id = f.id")
            ->join("__USERS__ u1 on r.object_id = u1.id")
            ->field("r.*,u.user_nicename username1,u1.user_nicename username2,f.title")
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
        $this->assign("postReply", $postReply);
        $this->display();
    }

    /**
     * @date：2018年1月18日23:38:40
     * @param：添加帖子
     * @User：刘柏林
     */
    public function circleAddOne()
    {
        if (IS_POST) {

            $data = I('post');
            $data["create_time"] = strtotime($data['create_time']);
            $_POST['smeta'] = sp_asset_relative_url($_POST['smeta']['thumb']);
            $data['thumb'] = $_POST['smeta'];
            $data['uid'] = get_current_admin_id();
            $data['content'] = htmlspecialchars_decode($data['content']);
            $result = $this->forum_post_model->add($data);

            if ($result) {
                $this->success("添加成功！");
            } else {
                $this->error("添加失败！");
            }

        }
    }

    /**
     * @date：2018年1月19日16:12:22
     * @param：修改帖子
     * @User：刘柏林
     */
    public function edit_post()
    {
        $id = I('id');
        $post = $this->forum_post_model->where("id=$id")->find();
        $post['create_time'] = date("Y-m-d H:i:s", $post['create_time']);
        $this->assign("post", $post);
        $this->display();
    }

    /**
     * @date：2018年1月19日16:12:22
     * @param：修改帖子
     * @User：刘柏林
     */
    public function editPostSub()
    {
        if (IS_POST) {
            $data = I('post');
            $_POST['thumb'] = sp_asset_relative_url($_POST['smeta']['thumb']);
            $data['imgs'] = $_POST['smeta'];

            $data["create_time"] = strtotime($data['create_time']);
            $data['content'] = htmlspecialchars_decode($data['content']);
            $result = $this->forum_post_model->save($data);
            if ($result !== false) {
                $this->success("保存成功！");
            } else {
                $this->error("保存失败！");
            }

        }

    }

}