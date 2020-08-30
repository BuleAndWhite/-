<?php

namespace Comment\Controller;

use Common\Controller\AdminbaseController;

class CommentadminController extends AdminbaseController
{

    protected $comments_model;
    protected $reply_model;
    protected $terms_model;
    protected $all_unionid;
    protected $name;

    function _initialize()
    {
        parent::_initialize();
        $this->comments_model = D("Common/Comments");
        $this->comments_model = D("Common/Comments");
        $this->reply_model = D("Common/Reply");
        $this->terms_model = D("Portal/Terms");
    }

    function index()
    {
        $post_id =$_GET['post_id']? $_GET['post_id']:"";
        $tablename =$_GET['tablename']? $_GET['tablename']:"";
        $where_ands = array("r.isdel = 0 and r.object_id = $post_id and r.tablename = '".$tablename."' ");
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
//        $idList = $this->terms_model->where(array("parent" => 2))->select();
//
//        $map = "";
//        foreach ($idList as $mtermId) {
//            $map = $map ? $map . "," . $mtermId["term_id"] : $mtermId["term_id"];
//        }
//        $map = $map ? "2," . $map : 2;
//        array_push($where_ands, "r.tablename = $tablename ");
        $where = join("  and ", $where_ands);


        $count = M('reply')
            ->alias("r")
            ->join("__USER__ u on r.uid = u.id")
            ->join("__SOF__ f on r.object_id = f.id")
            ->join("__USER__ u1 on f.uid = u1.id")
//            ->join("__POSTS__ f on r.object_id = f.id")
//            ->join("__USER__ u1 on f.post_author = u1.id")
//            ->join("__TERM_RELATIONSHIPS__ t on f.id = t.object_id")
            ->where($where)
            ->count();
        $page = $this->page($count, 20);
        $comments = M('reply')
            ->alias("r")
            ->join("__USER__ u on r.uid = u.id")
            ->join("__SOF__ f on r.object_id = f.id")
            ->join("__USER__ u1 on f.uid = u1.id")
//            ->join("__POSTS__ f on r.object_id = f.id")
//            ->join("__USER__ u1 on f.post_author = u1.id")
//            ->join("__TERM_RELATIONSHIPS__ t on f.id = t.object_id")
            ->field("r.*,u.name username1,u1.name username2")
            ->where($where)
            ->order('r.create_time desc')
            ->limit($page->firstRow . ',' . $page->listRows)
            ->select();
        foreach ($comments as $k => $item) {
            $comments[$k]['create_time'] = date("Y-m-d H:i:s", $item['create_time']);
        }


//        $count = M('reply')
//            ->alias("r")
//            ->join("__USERS__ u on r.uid = u.id")
//            ->where($where)
//            ->count();
//        $page = $this->page($count, 20);
//        $comments = M('reply')
//            ->alias("r")
//            ->join("__USERS__ u on r.uid = u.id")
//            ->field("r.*,u.user_nicename full_name")
//            ->where($where)
//            ->order('r.create_time desc')
//            ->limit($page->firstRow . ',' . $page->listRows)
//            ->select();
//        foreach ($comments as $k => $item) {
//            $comments[$k]['create_time'] = date("Y-m-d H:i:s", $item['create_time']);
//        }
        $this->assign("comments", $comments);
        $this->assign("formget", $_GET);
        $this->assign("Page", $page->show('Admin'));
        $this->display(":index");
    }

    public function settingVip()
    {
        $unionid = I("get.unionid") ? I("get.unionid") : $_POST['unionid'];
        $idCard = I("get.idCard") ? I("get.idCard") : $_POST['idCard'];
        $this->assign("unionid", $unionid);
        $this->assign("idCard", $idCard);
        $this->display(":setting_vip");
    }

    public function setingProxy()
    {
        $unionid = I("get.unionid") ? I("get.unionid") : $_POST['unionid'];
        $this->assign("unionid", $unionid);
        $this->display(":setting_proxy");
    }

    public function settingUp()
    {
        $id = I("get.id") ? I("get.id") : $_POST['id'];
        $this->assign("id", $id);
        $this->display(":setting_up");
    }

    public function settingDate()
    {
        $id = I("get.id") ? I("get.id") : $_POST['id'];
        $this->assign("id", $id);
        $this->display(":setting_date");
    }
    public function setting_proxy()
    {
        $unionid = isset($_POST['unionid']) ? $_POST['unionid'] : "";
        $status = $_POST['status'];
        $res = M("user")
            ->where(array("unionid" => $unionid))
            ->save(array("agent_level" => $status));
        if($res){
            $this->success("设置成功！");
            echo "<script>parent.location.reload();</script>";
        }else{
            $this->error("服务器内部错误！");
        }


    }
    public function setting_vip()
    {
        if (IS_POST) {
            $status = $_POST['status'];
            $unionid = isset($_POST['unionid']) ? $_POST['unionid'] : "";
            $idCard = isset($_POST['idCard']) ? $_POST['idCard'] : "";
            if (!$unionid && !$idCard) {
                $this->error("非法错误！");
            }
            if (!$unionid) {
                $userOne = "";
            } else {
                $userOne = M("user")->where(array("unionid" => $unionid))->find();
            }
            if (!$idCard) {
                $userOneUn = "";
            } else {
                $userOneUn = M("user")->where(array("idCard" => $idCard))->find();
            }


            if ($userOne) {
                switch ($status) {
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
                }


                if ($userOne['is_vip'] == 2 || $userOne['is_vip'] == 3 || $userOne['is_vip'] == 4 || $userOne['is_vip'] == 5) {
                    $datas['time_maturity'] = date("Y-m-d H:i:s", (strtotime($userOne['time_maturity']) - strtotime(date("Y-m-d H:i:s", time()))) + strtotime($times));
                } else {
                    $datas['time_maturity'] = $times;
                    $datas['time_start'] = date("Y-m-d H:i:s", time());
                }
                $datas['is_vip'] = $status;
                M("user")->where(array("unionid" => $unionid))->save($datas);

                echo "<script>parent.location.reload();</script>";
            } else if ($userOneUn) {
                switch ($status) {
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
                }


                if ($userOne['is_vip'] == 2 || $userOne['is_vip'] == 3 || $userOne['is_vip'] == 4 || $userOne['is_vip'] == 5) {
                    $datas['time_maturity'] = date("Y-m-d H:i:s", (strtotime($userOne['time_maturity']) - strtotime(date("Y-m-d H:i:s", time()))) + strtotime($times));
                } else {
                    $datas['time_maturity'] = $times;
                    $datas['time_start'] = date("Y-m-d H:i:s", time());
                }
                $datas['is_vip'] = $status;
                M("user")->where(array("idCard" => $idCard))->save($datas);
                echo "<script>parent.location.reload();</script>";

            } else {
                $this->error("非法错误！");
            }
        }
    }

    public function setting_up()
    {
        $id = I("get.id") ? I("get.id") : $_POST['id'];
        $proportion = $_POST['proportion'];
        $res = M("device_binding")
            ->where(array('id' => $id))
            ->save(array('proportion' => $proportion));
        if ($res) {
            echo "<script>parent.location.reload();</script>";
        } else {
            $this->error("非法错误！");
        }

    }

    public function setting_date()
    {

        $id = I("get.id") ? I("get.id") : $_POST['id'];
        $expiration_time = $_POST['expiration_time'];
        if ($expiration_time < date("Y-m-d H:i:s")) {
            $this->error("非法错误！");
        }
        $res = M("device_binding")
            ->where(array('id' => $id))
            ->save(array('expiration_time' => $expiration_time, "untied_time" => $expiration_time));
        if ($res) {
            echo "<script>parent.location.reload();</script>";
        } else {
            $this->error("非法错误！");
        }

    }

    public function userList()
    {
        $unionid = I("get.unionid") ? I("get.unionid") : $_POST['unionid'];

        if (!$unionid) {
            $this->assign("comments", array());
            $this->assign("formget", $_GET);
            $this->assign("unionid", $unionid);

            $this->display(":user_list");
        } else {
            $where_ands = array();
            $fields = array(
                'start_time' => array("field" => "time", "operator" => ">"),
                'end_time' => array("field" => "time", "operator" => "<"),
                'keyword' => array("field" => "name", "operator" => "like")
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
            array_push($where_ands, " parent_id = " . "'" . $unionid . "'");
            $where = join("  and   ", $where_ands);
            $count = M('user')
                ->where($where)
                ->count();

            $page = $this->page($count, 20);
            $comments = M('user')
                ->where($where)
                ->order('time desc')
                ->limit($page->firstRow . ',' . $page->listRows)
                ->select();


//        $count = M('reply')
//            ->alias("r")
//            ->join("__USERS__ u on r.uid = u.id")
//            ->where($where)
//            ->count();
//        $page = $this->page($count, 20);
//        $comments = M('reply')
//            ->alias("r")
//            ->join("__USERS__ u on r.uid = u.id")
//            ->field("r.*,u.user_nicename full_name")
//            ->where($where)
//            ->order('r.create_time desc')
//            ->limit($page->firstRow . ',' . $page->listRows)
//            ->select();
//        foreach ($comments as $k => $item) {
//            $comments[$k]['create_time'] = date("Y-m-d H:i:s", $item['create_time']);
//        }
            $this->assign("comments", $comments);
            $this->assign("formget", $_GET);
            $this->assign("unionid", $unionid);
            $this->assign("Page", $page->show('Admin'));
            $this->display(":user_list");
        }
    }

    public function userLists()
    {
        $unionid = I("get.unionid") ? I("get.unionid") : $_POST['unionid'];

        if (!$unionid) {
            $this->assign("comments", array());
            $this->assign("formget", $_GET);
            $this->assign("unionid", $unionid);

            $this->display(":user_list");
        } else {
            $where_ands = array();
            $fields = array(
                'start_time' => array("field" => "b.time", "operator" => ">"),
                'end_time' => array("field" => "b.time", "operator" => "<"),
                'keyword' => array("field" => "b.name", "operator" => "like")
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
            $where = join("  and   ", $where_ands);


            $data = array();
            $this->name = $get;
            $res = $this->teamRecursion($i = 0, $unionid, $data);
            $res = $this->uniquArr($res);
            foreach($res as $k => $val){
                $ress = $this->teamRecursion($i = 0, $val["unionid"], $data);
                $ress = $this->uniquArr($ress);
                $res[$k]["count"] = sizeof($ress);
            }

            $page = $this->page(  sizeof($res), 20);

            $res = array_slice($res, $page->firstRow, 20);
            $this->assign("comments", $res);
            $this->assign("formget", $_GET);
            $this->assign("unionid", $unionid);
            $this->assign("Page", $page->show('Admin'));
            $this->display(":user_lists");
        }
    }

    public function userLists_Secondary()
    {
        $unionid = I("get.unionid") ? I("get.unionid") : $_POST['unionid'];

        if (!$unionid) {
            $this->assign("comments", array());
            $this->assign("formget", $_GET);
            $this->assign("unionid", $unionid);

            $this->display(":user_list");
        } else {
            $where_ands = array();
            $fields = array(
                'start_time' => array("field" => "b.time", "operator" => ">"),
                'end_time' => array("field" => "b.time", "operator" => "<"),
                'keyword' => array("field" => "b.name", "operator" => "like")
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
            $where = join("  and   ", $where_ands);


            $data = array();
            $this->name = $get;
            $res = $this->teamRecursion($i = 0, $unionid, $data);
            $res = $this->uniquArr($res);
            foreach($res as $k => $val){
                $ress = $this->teamRecursion($i = 0, $val["unionid"], $data);
                $ress = $this->uniquArr($ress);
                $res[$k]["count"] = sizeof($ress);
            }

            $page = $this->page(  sizeof($res), 20);

            $res = array_slice($res, $page->firstRow, 20);
            $this->assign("comments", $res);
            $this->assign("formget", $_GET);
            $this->assign("unionid", $unionid);
            $this->assign("Page", $page->show('Admin'));
            $this->display(":user_lists_t");
        }
    }

    public function userListz()
    {
        $unionid = I("get.unionid") ? I("get.unionid") : $_POST['unionid'];

        if (!$unionid) {
            $this->assign("comments", array());
            $this->assign("formget", $_GET);
            $this->assign("unionid", $unionid);

            $this->display(":user_list");
        } else {
            $where_ands = array();
            $fields = array(
                'start_time' => array("field" => "b.time", "operator" => ">"),
                'end_time' => array("field" => "b.time", "operator" => "<"),
                'keyword' => array("field" => "b.name", "operator" => "like")
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
            $where = join("  and   ", $where_ands);
            $data = array();
            $this->name = $get;
            $res = $this->teamRecursionz($unionid);
            $res = $this->uniquArr($res);
            foreach($res as $k => $val){
                $ress = $this->teamRecursionz($val["unionid"]);
                $ress = $this->uniquArr($ress);
                $res[$k]["count"] = sizeof($ress);
            }

            $page = $this->page(  sizeof($res), 20);

            $res = array_slice($res, $page->firstRow, 20);

            $this->assign("comments", $res);
            $this->assign("formget", $_GET);
            $this->assign("unionid", $unionid);
            $this->assign("Page", $page->show('Admin'));
            $this->display(":user_listz");
        }
    }

    public function userListz_Secondary()
    {
        $unionid = I("get.unionid") ? I("get.unionid") : $_POST['unionid'];

        if (!$unionid) {
            $this->assign("comments", array());
            $this->assign("formget", $_GET);
            $this->assign("unionid", $unionid);

            $this->display(":user_list");
        } else {
            $where_ands = array();
            $fields = array(
                'start_time' => array("field" => "b.time", "operator" => ">"),
                'end_time' => array("field" => "b.time", "operator" => "<"),
                'keyword' => array("field" => "b.name", "operator" => "like")
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
            $where = join("  and   ", $where_ands);
            $data = array();
            $this->name = $get;
            $res = $this->teamRecursionz($unionid);
            $res = $this->uniquArr($res);
            foreach($res as $k => $val){
                $ress = $this->teamRecursionz($val["unionid"]);
                $ress = $this->uniquArr($ress);
                $res[$k]["count"] = sizeof($ress);
            }

            $page = $this->page(  sizeof($res), 20);

            $res = array_slice($res, $page->firstRow, 20);

            $this->assign("comments", $res);
            $this->assign("formget", $_GET);
            $this->assign("unionid", $unionid);
            $this->assign("Page", $page->show('Admin'));
            $this->display(":user_listz_t");
        }
    }


    public function match()
    {
        $unionid = I("get.unionid") ? I("get.unionid") : $_POST['unionid'];

        $where_ands = array();
        $fields = array(
            'start_time' => array("field" => "time", "operator" => ">"),
            'end_time' => array("field" => "time", "operator" => "<"),
            'keyword' => array("field" => "name", "operator" => "like")
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
        array_push($where_ands, " unionid = " . "'" . $unionid . "'");
        $where = join("  and   ", $where_ands);

        $count = M('payment_add')
            ->where($where)
            ->count();

        $page = $this->page($count, 20);
        $comments = M('payment_add')
            ->where($where)
            ->order('time desc')
            ->limit($page->firstRow . ',' . $page->listRows)
            ->select();


//        $count = M('reply')
//            ->alias("r")
//            ->join("__USERS__ u on r.uid = u.id")
//            ->where($where)
//            ->count();
//        $page = $this->page($count, 20);
//        $comments = M('reply')
//            ->alias("r")
//            ->join("__USERS__ u on r.uid = u.id")
//            ->field("r.*,u.user_nicename full_name")
//            ->where($where)
//            ->order('r.create_time desc')
//            ->limit($page->firstRow . ',' . $page->listRows)
//            ->select();
//        foreach ($comments as $k => $item) {
//            $comments[$k]['create_time'] = date("Y-m-d H:i:s", $item['create_time']);
//        }
        $this->assign("comments", $comments);
        $this->assign("formget", $_GET);
        $this->assign("unionid", $unionid);
        $this->assign("Page", $page->show('Admin'));
        $this->display(":match");
    }

    function follow()
    {

        $where = array();
//        $table = I("get.tablename");
//        if (!empty($table)) {
//            $where['r.tablename'] = $table;
//        }

        $post_id = I("get.post_id");
        if (!empty($post_id)) {
            $where['r.object_id'] = $post_id;
        }

        $count = M('follow')
            ->alias("r")
            ->join("__USER__ u on r.uid = u.id")
            ->where($where)
            ->count();
        $page = $this->page($count, 20);
        $comments = M('follow')
            ->alias("r")
            ->join("__USER__ u on r.uid = u.id")
            ->field("r.*,u.name full_name")
            ->where($where)
            ->order('r.create_time desc')
            ->limit($page->firstRow . ',' . $page->listRows)
            ->select();
        foreach ($comments as $k => $item) {
            $comments[$k]['create_time'] = date("Y-m-d H:i:s", $item['create_time']);
        }
        $this->assign("comments", $comments);
        $this->assign("Page", $page->show('Admin'));
        $this->display(":follow");
    }

    /**
     * @date：2018年1月12日18:36:07
     * @param 删除 活动
     * @User：刘柏林
     */
    public function delete()
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

    function check()
    {
        if (isset($_POST['ids']) && $_GET["check"]) {
            $data["status"] = 1;

            $ids = join(",", $_POST['ids']);

            if ($this->comments_model->where("id in ($ids)")->save($data) !== false) {
                $this->success("审核成功！");
            } else {
                $this->error("审核失败！");
            }
        }
        if (isset($_POST['ids']) && $_GET["uncheck"]) {

            $data["status"] = 0;
            $ids = join(",", $_POST['ids']);
            if ($this->comments_model->where("id in ($ids)")->save($data) !== false) {
                $this->success("取消审核成功！");
            } else {
                $this->error("取消审核失败！");
            }
        }
    }

    /**
     * @date：2018年1月12日18:35:37
     * @param ：推荐
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
     * @date：2018年1月12日18:36:07
     * @param 删除评论
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
     * @date：2018年1月18日14:22:03
     * @param ：评论回收站
     * @User：刘柏林
     */
    public function reply_del()
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
        $idList = $this->terms_model->where(array("parent" => 2))->select();

        $map = "";
        foreach ($idList as $mtermId) {
            $map = $map ? $map . "," . $mtermId["term_id"] : $mtermId["term_id"];
        }
        $map = $map ? "2," . $map : 2;
        array_push($where_ands, "r.tablename = 'posts' and t.term_id in ($map) ");
        $where = join("  and ", $where_ands);

        $count = M('reply')
            ->alias("r")
            ->join("__USERS__ u on r.uid = u.id")
            ->join("__POSTS__ f on r.object_id = f.id")
            ->join("__USERS__ u1 on f.post_author = u1.id")
            ->join("__TERM_RELATIONSHIPS__ t on f.id = t.object_id")
            ->where($where)
            ->count();
        $page = $this->page($count, 20);
        $postReply = M('reply')
            ->alias("r")
            ->join("__USERS__ u on r.uid = u.id")
            ->join("__POSTS__ f on r.object_id = f.id")
            ->join("__USERS__ u1 on f.post_author = u1.id")
            ->join("__TERM_RELATIONSHIPS__ t on f.id = t.object_id")
            ->field("r.*,u.user_nicename username1,u1.user_nicename username2,f.post_title")
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
        $this->display(":reply_del");
    }

    /**
     * @date：2018年1月18日14:22:03
     * @param 热门推荐评论
     * @User：刘柏林
     */
    public function reply_home()
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
        $idList = $this->terms_model->where(array("parent" => 2))->select();

        $map = "";
        foreach ($idList as $mtermId) {
            $map = $map ? $map . "," . $mtermId["term_id"] : $mtermId["term_id"];
        }
        $map = $map ? "2," . $map : 2;
        array_push($where_ands, "r.tablename = 'posts' and t.term_id in ($map) ");
        $where = join("  and ", $where_ands);
        $count = M('reply')
            ->alias("r")
            ->join("__USERS__ u on r.uid = u.id")
            ->join("__POSTS__ f on r.object_id = f.id")
            ->join("__USERS__ u1 on f.post_author = u1.id")
            ->join("__TERM_RELATIONSHIPS__ t on f.id = t.object_id")
            ->where($where)
            ->count();
        $page = $this->page($count, 20);
        $postReply = M('reply')
            ->alias("r")
            ->join("__USERS__ u on r.uid = u.id")
            ->join("__POSTS__ f on r.object_id = f.id")
            ->join("__USERS__ u1 on f.post_author = u1.id")
            ->join("__TERM_RELATIONSHIPS__ t on f.id = t.object_id")
            ->field("r.*,u.user_nicename username1,u1.user_nicename username2,f.post_title")
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
        $this->display(":reply_home");
    }


    /**
     * @date：2018年1月14日17:23:48
     * @param ：评论恢复删除
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
     * Notes: 艾灸团队(递归)
     * User: Sen
     * Date: 2019-7-29 0029
     * Time: 19:03
     * Return:
     * @param $i
     * @param $unionid
     * @param $data
     * @return mixed
     */
    public function teamRecursion($i, $unionid, $data)
    {
        $arr = array();
        if (empty($unionid)) {
            return $data;
        }
        if ($i == 9) {
            return $data;
        }
        if($this->name){
            $where['a.name'] = array("like", $this->name);
        }
        $where['b.parent_id'] = array('in', $unionid);
        $where['a.success'] = 1;
        $where['a.platform'] = 1;
        $where['a.order_type'] = 1;
        $where['a.type'] = 9;

        if ($i < 10) {
            $orderList = M("order")
                ->alias('a')
                ->join("right join __USER__ b ON a.uid = b.id")
                ->where($where)
                ->order("a.time DESC")
                ->field("b.id,a.type,a.time,b.name,b.avatar,b.unionid")
                ->select();
            //计算金额
            foreach ($orderList as $k => $val) {
                array_push($arr, $val['unionid']);
            }
            $arr = array_unique($arr);
            if (in_array($this->all_unionid, $arr)) {
                $arr = array_flip($arr);
                unset($arr[$this->all_unionid]);
                $arr = array_flip($arr);
            }

            $this->num += sizeof($arr);

            $orderList = array_merge($orderList, $data);

            $res = $this->teamRecursion($i + 1, $arr, $orderList);
        }
        return $res;
    }
    /**
     * Notes: 艾灸团队(无递归)
     * User: Sen
     * Date: 2019-7-29 0029
     * Time: 19:03
     * Return:
     * @param $i
     * @param $unionid
     * @param $data
     * @return mixed
     */
    public function teamRecursionz($unionid)
    {
        if(empty($unionid)){
            return 1;
        }
        $arr = array();

        $where['b.parent_id'] = array('in', $unionid);
        $where['a.success'] = 1;
        $where['a.platform'] = 1;
        $where['a.order_type'] = 1;
        $where['a.type'] = 9;


            $orderList = M("order")
                ->alias('a')
                ->join("right join __USER__ b ON a.uid = b.id")
                ->where($where)
                ->order("a.time DESC")
                ->field("b.id,a.type,a.time,b.name,b.avatar,b.unionid")
                ->select();
        return $orderList;
    }
    /**
     * Notes: 分页
     * User: Jason
     * Date: 2019-7-28
     * Time: 12:40
     * Return:
     * @param $count 条数
     * @param $page  分页条数
     * @param $pageNum  页码
     */
//    protected
//    function pages($count, $page, $pageNum)
//    {
//        //分页
//        $num = $count / $page;
//        if (empty($pageNum) || $pageNum == 1) {
//            $current_page = 1;
//            $start_num = 0;
//            if (is_float($num)) {
//                $total_page = ceil($num);
//            } else {
//                $total_page = $num;
//            }
//        } else {
//            $current_page = $pageNum;
//            $start_num = ($pageNum - 1) * $page;
//            if (is_float($num)) {
//                $total_page = ceil($num);
//            } else {
//                $total_page = $num;
//            }
//        }
//        $data = array(
//            'start_num' => $start_num,
//            'current_page' => $current_page,
//            'total_page' => $total_page,
//        );
//        return $data;
//
//    }

    /**
     * Notes: 二维数组去重
     * User: Sen
     * Date: 2019-7-29 0029
     * Time: 19:24
     * Return:
     * @param $arr
     * @param $key
     * @return mixed
     */
    private function uniquArr($array)
    {
        $result = array();
        foreach ($array as $k => $val) {
            $code = false;
            foreach ($result as $_val) {
                if ($_val['unionid'] == $val['unionid']) {
                    $code = true;
                    break;
                }
            }
            if (!$code) {
                $result[] = $val;
            }
        }
        return $result;
    }


    public function userListH5()
    {
        $unionid = I("get.unionid") ? I("get.unionid") : $_POST['unionid'];
        if (!$unionid) {
            $this->assign("comments", array());
            $this->assign("formget", $_GET);
            $this->assign("unionid", $unionid);

            $this->display(":user_list_h5");
        } else {
            $where_ands = array();
            $fields = array(
                'start_time' => array("field" => "a.time", "operator" => ">"),
                'end_time' => array("field" => "a.time", "operator" => "<"),
                'phone' => array("field" => "a.phone", "operator" => "like")
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
            array_push($where_ands, " a.parent_id = " . "'" . $unionid . "'");
            $where = join("  and   ", $where_ands);
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
            $this->assign("unionid", $unionid);
            $this->display(":user_list_h5");

        }
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
        $unionid = I("get.unionid") ? I("get.unionid") : $_POST['unionid'];

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
        $where['a.parent_id'] = $unionid;
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
}