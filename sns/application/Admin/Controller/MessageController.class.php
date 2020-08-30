<?php
/**
 * @date：2018年1月12日15:08:23
 * @content：站内信
 * @User：刘柏林
 */

namespace Admin\Controller;

use Common\Controller\AdminbaseController;

class MessageController extends AdminbaseController
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

    function _initialize()
    {
        parent::_initialize();
        $this->user_model = D("Common/Users");;
        $this->post_digg_model = D("Common/PostDigg");
        $this->forum_model = D("Common/Forum");
        $this->forum_post_model = D("Common/ForumPost");
        $this->message_content_model = D("Common/MessageContent");
        $this->message_model = D("Common/Message");
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
        $where_ands = array("m.isdel != 1");
        $fields = array(
            'start_time' => array("field" => "m.create_time", "operator" => ">"),
            'end_time' => array("field" => "m.create_time", "operator" => "<"),
            'keyword' => array("field" => "m.title", "operator" => "like")
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

        $count = M('message_content')
            ->alias("m")
            ->join("__USERS__ u on m.from_id = u.id")
            ->join("__USERS__ u1 on m.args = u1.id")
            ->where($where)
            ->count();
        $page = $this->page($count, 20);
        $forumPostList = M('message_content')
            ->alias("m")
            ->join("__USERS__ u on m.from_id = u.id")
            ->join("LEFT JOIN __USERS__ u1 on m.args = u1.id")
            ->field("m.*,u.user_nicename user_nicename1,u1.user_nicename user_nicename2")
            ->where($where)
            ->order('m.create_time desc')
            ->limit($page->firstRow . ',' . $page->listRows)
            ->select();
        foreach ($forumPostList as $k => $item) {
            $forumPostList[$k]['create_time'] = date("Y-m-d H:i:s", $item['create_time']);
            $forumPostList[$k]['user_nicename2'] = $item['user_nicename2']?$item['user_nicename2']:"all";
        }
        $this->assign("page", $page->show('Admin'));
        unset($_GET[C('VAR_URL_PARAMS')]);
        $this->assign("formget", $_GET);
        $this->assign("messageContentList", $forumPostList);
    }


    /**
     * @date：2018年1月19日16:12:22
     * @param：修改朋友圈
     * @User：刘柏林
     */
    public function edit()
    {
        $id = I('id');
        $sofList = $this->message_content_model->where("id=$id")->find();
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

            $result = $this->message_content_model->save($data);
            if ($result !== false) {
                $this->success("保存成功！");
            } else {
                $this->error("保存失败！");
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
     * @date：2018年1月12日18:36:07
     * @param删除
     * @User：刘柏林
     */
    public function delete()
    {
        if (isset($_GET['id'])) {
            $tid = intval(I("get.id"));
            $data['isdel'] = 1;//设为伪删除
            if ($this->message_content_model->where("id=$tid")->save($data) ){
                $this->message_model->where(array("content_id"=>$tid))->save($data);
                $this->success("删除成功！");
            } else {
                $this->error("删除失败！");
            }
        }
        if (isset($_POST['ids'])) {
            $tids = join(",", $_POST['ids']);
            $data['isdel'] = 1;//设为伪删除
            if ($this->message_content_model->where("id in ($tids)")->save($data)) {
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
     * @date：2018年1月14日16:12:29
     * @param 站内信审核
     * @User：刘柏林
     */
    public function circleCheck()
    {
        if (isset($_GET['ids']) && $_GET["check"]) {
            $data["status"] = 1;
            $tid = $_GET['ids'];
            if ($this->message_content_model->where(array("id" => $tid))->save($data) !== false) {
                $this->success("显示成功！");
            } else {
                $this->error("显示失败！");
            }
        }
        if (isset($_GET['ids']) && $_GET["uncheck"]) {
            $data["status"] = 0;
            $tid = $_GET['ids'];

            if ($this->message_content_model->where(array("id" => $tid))->save($data)) {
                $this->success("已隐藏！");
            } else {
                $this->error("隐藏失败！");
            }
        }
    }

}