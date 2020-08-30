<?php
// +----------------------------------------------------------------------
// | ThinkCMF [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2014 http://www.thinkcmf.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: Tuolaji <479923197@qq.com>
// +----------------------------------------------------------------------
namespace Portal\Controller;

use Common\Controller\AdminbaseController;

class AdminPostController extends AdminbaseController
{
    protected $posts_model;
    protected $term_relationships_model;
    protected $terms_model;
    protected $sign_up_model;
    protected $message_content_model;
    protected $goods_model;
    protected $reply_model;

    function _initialize()
    {
        parent::_initialize();
        $this->posts_model = D("Portal/Posts");
        $this->terms_model = D("Portal/Terms");
        $this->term_relationships_model = D("Portal/TermRelationships");
        $this->sign_up_model = D("Portal/SignUp");
        $this->message_content_model = D("Common/MessageContent");
        $this->goods_model = D('goods');
        $this->reply_model = D("Common/Reply");
    }

    function index()
    {
        $this->_lists();
        $this->_getTree();
        $this->display();
    }

    /**
     * @date：2018年3月19日13:59:28
     * @param：文章管理
     * @User：刘柏林
     */
    function article()
    {
        $this->articleList();
        $this->_getTree();
        $this->display();
    }


    /**
     * @date：2018年3月7日15:44:44
     * @param：活动管理
     * @User：刘柏林
     */
    function activity()
    {
        $this->activityList();
        $this->_getTree();
        $this->display();
    }

    function add()
    {
        $terms = $this->terms_model->order(array("listorder" => "asc"))->select();
        $term_id = intval(I("get.term"));
        $idList = $this->terms_model->where(array("parent" => 1))->select();

        $map = "";
        foreach ($idList as $mtermId) {
            $map = $map ? $map . "," . $mtermId["term_id"] : $mtermId["term_id"];
        }

        $map = $map ? "1," . $map : 1;
        $this->_getTermTree(array(), $map);


        $term = $this->terms_model->where("term_id=$term_id")->find();
        $this->assign("author", "1");
        $this->assign("term", $term);
        $this->assign("terms", $terms);
        $this->display();
    }

    /**
     * @date：2018年3月19日14:08:00
     * @param：添加文章
     * @User：刘柏林
     */
    function article_add()
    {
        $terms = $this->terms_model->order(array("listorder" => "asc"))->select();
        $term_id = intval(I("get.term"));
        $idList = $this->terms_model->where(array("parent" => 5))->select();

        $map = "";
        foreach ($idList as $mtermId) {
            $map = $map ? $map . "," . $mtermId["term_id"] : $mtermId["term_id"];
        }
        $map = $map ? "5," . $map : 5;
        $this->_getTree();


        $term = $this->terms_model->where("term_id=$term_id")->find();
        $this->assign("author", "1");
        $this->assign("term", $term);
        $this->assign("terms", $terms);
        $this->display();
    }

    /**
     * @date：2018年3月7日16:49:26
     * @param：添加活动回顾
     * @User：刘柏林
     */
    function review()
    {
        $terms = $this->terms_model->order(array("listorder" => "asc"))->select();
        $term_id = $_GET['term'];
        $this->_getTermTree(array(), $term_id);
        $term = $this->terms_model->where("term_id=$term_id")->find();
        $this->assign("author", "1");
        $this->assign("term_id", $term_id);
        $this->assign("term", term);
        $this->assign("terms", $terms);
        $this->assign("id", $_GET['id']);
        $this->display();
    }

    /**
     * @date：2018年3月7日16:48:59
     * @param：添加活动
     * @User：刘柏林
     */
    function add_activity()
    {
        $terms = $this->terms_model->order(array("listorder" => "asc"))->select();
        $term_id = intval(I("get.term"));
        $idList = $this->terms_model->where(array("parent" => 2))->select();

        $map = "";
        foreach ($idList as $mtermId) {
            $map = $map ? $map . "," . $mtermId["term_id"] : $mtermId["term_id"];
        }
        $map = $map ? "2," . $map : 2;
        $this->_getTermTree(array(), $map);

        $term = $this->terms_model->where("term_id=$term_id")->find();
        $this->assign("author", "1");
        $this->assign("term", $term);
        $this->assign("terms", $terms);
        $this->display();
    }

    function add_post()
    {
        if (IS_POST) {
//            if (empty($_POST['term'])) {
//                $this->error("请至少选择一个分类栏目！");
//            }
            if (!empty($_POST['photos_alt']) && !empty($_POST['photos_url'])) {
                foreach ($_POST['photos_url'] as $key => $url) {
                    $photourl = sp_asset_relative_url($url);
                    $_POST['smeta']['photo'][] = array("url" => $photourl, "alt" => $_POST['photos_alt'][$key]);
                }
            }
            $_POST['smeta']['thumb'] = sp_asset_relative_url($_POST['smeta']['thumb']);

            $_POST['post']['post_date'] = date("Y-m-d H:i:s", time());
            $_POST['post']['post_author'] = get_current_admin_id();
            $article = I("post.post");
            $article['smeta'] = json_encode($_POST['smeta']);
            $article['post_content'] = htmlspecialchars_decode($article['post_content']);
            $result = $this->posts_model->add($article);
            if ($result) {
                //
//                foreach ($_POST['term'] as $mterm_id) {
//                    $this->term_relationships_model->add(array("term_id" => intval($mterm_id), "object_id" => $result));
//                }
                $this->term_relationships_model->add(array("term_id" => $_POST['term'], "object_id" => $result));
                $this->success("添加成功！");
            } else {
                $this->error("添加失败！");
            }

        }
    }

    /**
     * @date：2018年3月19日14:20:51
     * @param：添加文章
     * @User：刘柏林
     */
    function addArticle()
    {
        if (IS_POST) {
//            if (empty($_POST['term'])) {
//                $this->error("请至少选择一个分类栏目！");
//            }
            if (!empty($_POST['photos_alt']) && !empty($_POST['photos_url'])) {
                foreach ($_POST['photos_url'] as $key => $url) {
                    $photourl = sp_asset_relative_url($url);
                    $_POST['smeta']['photo'][] = array("url" => $photourl, "alt" => $_POST['photos_alt'][$key]);
                }
            }
            $_POST['smeta']['thumb'] = sp_asset_relative_url($_POST['smeta']['thumb']);

            $_POST['post']['post_date'] = date("Y-m-d H:i:s", time());
            $_POST['post']['post_author'] = get_current_admin_id();
            $article = I("post.post");
            $article['smeta'] = json_encode($_POST['smeta']);
            $article['post_content'] = htmlspecialchars_decode($article['post_content']);
            $result = $this->posts_model->add($article);
            if ($result) {
                //
                foreach ($_POST['term'] as $mterm_id) {
                    $this->term_relationships_model->add(array("term_id" => intval($mterm_id), "object_id" => $result));
                }
                $this->term_relationships_model->add(array("term_id" => $_POST['term'], "object_id" => $result));
                $this->success("添加成功！");
            } else {
                $this->error("添加失败！");
            }

        }
    }

    /**
     * @date：2018年3月7日16:51:21
     * @param：活动回顾添加
     * @User：刘柏林
     */
    function addReview()
    {
        if (IS_POST) {

            if (!empty($_POST['photos_alt']) && !empty($_POST['photos_url'])) {
                foreach ($_POST['photos_url'] as $key => $url) {
                    $photourl = sp_asset_relative_url($url);
                    $_POST['smeta']['photo'][] = array("url" => $photourl, "alt" => $_POST['photos_alt'][$key]);
                }
            }
            $_POST['smeta']['thumb'] = sp_asset_relative_url($_POST['smeta']['thumb']);
            $_POST['post']['review_id'] = -1;
            $_POST['post']['post_date'] = date("Y-m-d H:i:s", time());
            $_POST['post']['post_author'] = get_current_admin_id();
            $article = I("post.post");
            $article['smeta'] = json_encode($_POST['smeta']);
            $article['post_content'] = htmlspecialchars_decode($article['post_content']);
            $result = $this->posts_model->add($article);
            if ($result) {
                //
//                foreach ($_POST['term'] as $mterm_id) {
//                    $this->term_relationships_model->add(array("term_id" => intval($mterm_id), "object_id" => $result));
//                }
                $id = $_POST['id'];
                $postOne['id'] = $id;
                $postL['review_id'] = $result;
                $this->posts_model->where($postOne)->save($postL);
                $this->term_relationships_model->add(array("term_id" => $_POST['term'], "object_id" => $result));
                $this->success("添加成功！");
            } else {
                $this->error("添加失败！");
            }

        }
    }

    /**
     * @date：2018年3月7日16:51:21
     * @param：活动添加
     * @User：刘柏林
     */
    function addActivity()
    {
        if (IS_POST) {
//            if (empty($_POST['term'])) {
//                $this->error("请至少选择一个分类栏目！");
//            }
            if (!empty($_POST['photos_alt']) && !empty($_POST['photos_url'])) {
                foreach ($_POST['photos_url'] as $key => $url) {
                    $photourl = sp_asset_relative_url($url);
                    $_POST['smeta']['photo'][] = array("url" => $photourl, "alt" => $_POST['photos_alt'][$key]);
                }
            }
            $_POST['smeta']['thumb'] = sp_asset_relative_url($_POST['smeta']['thumb']);

            $_POST['post']['post_date'] = date("Y-m-d H:i:s", time());
            $_POST['post']['post_author'] = get_current_admin_id();
            $article = I("post.post");
            $article['smeta'] = json_encode($_POST['smeta']);
            $article['post_content'] = htmlspecialchars_decode($article['post_content']);
            $result = $this->posts_model->add($article);
            if ($result) {
                //
//                foreach ($_POST['term'] as $mterm_id) {
//                    $this->term_relationships_model->add(array("term_id" => intval($mterm_id), "object_id" => $result));
//                }
                $this->term_relationships_model->add(array("term_id" => $_POST['term'], "object_id" => $result));
                $this->success("添加成功！");
            } else {
                $this->error("添加失败！");
            }

        }
    }

    public
    function edit()
    {
        $id = intval(I("get.id"));
        $term_relationship = M('TermRelationships')->where(array("object_id" => $id, "status" => 1))->getField("term_id", true);
        $termId = intval(I("get.term"));
//        $this->_getTermTree($term_relationship);
        $terms = $this->terms_model->select();
        $post = $this->posts_model->where("id=$id")->find();
        $this->assign("post", $post);
        $this->assign("smeta", json_decode($post['smeta'], true));
        $this->assign("terms", $terms);
        $this->assign("termId", $termId);
        $this->assign("term", $term_relationship);
        $this->display();
    }

    /**
     * @date：2018年3月7日16:10:01
     * @param：修改活动
     * @User：刘柏林
     */
    public
    function edit_activity()
    {
        $id = intval(I("get.id"));
        $termId = intval(I("get.term"));
        $term_relationship = M('TermRelationships')->where(array("object_id" => $id, "status" => 1))->getField("term_id", true);
//        $this->_getTermTree($term_relationship,$termId);
        $terms = $this->terms_model->select();
        $post = $this->posts_model->where("id=$id")->find();
        $this->assign("post", $post);
        $this->assign("smeta", json_decode($post['smeta'], true));
        $this->assign("terms", $terms);
        $this->assign("termId", $termId);
        $this->assign("term", $term_relationship);
        $this->display();
    }

    /**
     * @date：2018年3月7日16:10:01
     * @param：修改文章
     * @User：刘柏林
     */
    public
    function article_edit()
    {
        $id = intval(I("get.id"));
        $termId = intval(I("get.term"));
        $term_relationship = M('TermRelationships')->where(array("object_id" => $id, "status" => 1))->getField("term_id", true);
        $this->_getTree();
        $terms = $this->terms_model->select();
        $post = $this->posts_model->where("id=$id")->find();
        $this->assign("post", $post);
        $this->assign("smeta", json_decode($post['smeta'], true));
        $this->assign("terms", $terms);
        $this->assign("termId", $termId);
        $this->assign("term", $term_relationship);
        $this->display();
    }

    /**
     * @date：2018年3月7日15:49:52
     * @param：比赛修改
     * @User：刘柏林
     */
    public
    function edit_post()
    {
        if (IS_POST) {
//            if (empty($_POST['term'])) {
//                $this->error("请至少选择一个分类栏目！");
//            }
            $post_id = intval($_POST['post']['id']);

//            $this->term_relationships_model->where(array("object_id" => $post_id, "term_id" => array("not in", implode(",", $_POST['term']))))->delete();
//            $this->term_relationships_model->where(array("object_id" => $post_id, "term_id" => array("not in", $_POST['term'])))->delete();
            $mterm_id = $_POST['term'];
            $find_term_relationship = $this->term_relationships_model->where(array("object_id" => $post_id, "term_id" => $mterm_id))->count();
            if (empty($find_term_relationship)) {
                $this->term_relationships_model->addindex(array("term_id" => intval($mterm_id), "object_id" => $post_id));
            } else {
                $this->term_relationships_model->where(array("object_id" => $post_id, "term_id" => $mterm_id))->save(array("status" => 1));
            }


            if (!empty($_POST['photos_alt']) && !empty($_POST['photos_url'])) {
                foreach ($_POST['photos_url'] as $key => $url) {
                    $photourl = sp_asset_relative_url($url);
                    $_POST['smeta']['photo'][] = array("url" => $photourl, "alt" => $_POST['photos_alt'][$key]);
                }
            }
            $_POST['smeta']['thumb'] = sp_asset_relative_url($_POST['smeta']['thumb']);
            unset($_POST['post']['post_author']);
            $article = I("post.post");
            $article['smeta'] = json_encode($_POST['smeta']);
            $article['post_content'] = htmlspecialchars_decode($article['post_content']);
            $result = $this->posts_model->save($article);
            if ($result !== false) {
                $this->success("保存成功！");
            } else {
                $this->error("保存失败！");
            }
        }
    }

    /**
     * @date：2018年3月7日15:49:52
     * @param：活动修改
     * @User：刘柏林
     */
    public
    function editActivity()
    {
        if (IS_POST) {
//            if (empty($_POST['term'])) {
//                $this->error("请至少选择一个分类栏目！");
//            }
            $post_id = intval($_POST['post']['id']);
//            $this->term_relationships_model->where(array("object_id" => $post_id, "term_id" => array("not in", implode(",", $_POST['term']))))->delete();
//            $this->term_relationships_model->where(array("object_id" => $post_id, "term_id" => array("not in", $_POST['term'])))->delete();
            $mterm_id = $_POST['term'];
            $find_term_relationship = $this->term_relationships_model->where(array("object_id" => $post_id, "term_id" => $mterm_id))->count();
            if (empty($find_term_relationship)) {
                $this->term_relationships_model->addindex(array("term_id" => intval($mterm_id), "object_id" => $post_id));
            } else {
                $this->term_relationships_model->where(array("object_id" => $post_id, "term_id" => $mterm_id))->save(array("status" => 1));
            }


            if (!empty($_POST['photos_alt']) && !empty($_POST['photos_url'])) {
                foreach ($_POST['photos_url'] as $key => $url) {
                    $photourl = sp_asset_relative_url($url);
                    $_POST['smeta']['photo'][] = array("url" => $photourl, "alt" => $_POST['photos_alt'][$key]);
                }
            }
            $_POST['smeta']['thumb'] = sp_asset_relative_url($_POST['smeta']['thumb']);
            unset($_POST['post']['post_author']);
            $article = I("post.post");
            $article['smeta'] = json_encode($_POST['smeta']);
            $article['post_content'] = htmlspecialchars_decode($article['post_content']);
            $result = $this->posts_model->save($article);
            if ($result !== false) {
                $this->success("保存成功！");
            } else {
                $this->error("保存失败！");
            }
        }
    }

    /**
     * @date：2018年3月7日15:49:52
     * @param：活动文章
     * @User：刘柏林
     */
    public
    function editArticle()
    {
        if (IS_POST) {
//            if (empty($_POST['term'])) {
//                $this->error("请至少选择一个分类栏目！");
//            }
            $post_id = intval($_POST['post']['id']);
//            $this->term_relationships_model->where(array("object_id" => $post_id, "term_id" => array("not in", implode(",", $_POST['term']))))->delete();
//            $this->term_relationships_model->where(array("object_id" => $post_id, "term_id" => array("not in", $_POST['term'])))->delete();
            $mterm_id = $_POST['term'];
            $find_term_relationship = $this->term_relationships_model->where(array("object_id" => $post_id))->count();
            if (empty($find_term_relationship)) {
//                $this->term_relationships_model->addindex(array("term_id" => intval($mterm_id), "object_id" => $post_id));
                $this->term_relationships_model->add(array("term_id" => intval($mterm_id), "object_id" => $post_id));
            } else {
                $this->term_relationships_model->where(array("object_id" => $post_id))->save(array("status" => 1,"term_id" => intval($mterm_id)));
            }


            if (!empty($_POST['photos_alt']) && !empty($_POST['photos_url'])) {
                foreach ($_POST['photos_url'] as $key => $url) {
                    $photourl = sp_asset_relative_url($url);
                    $_POST['smeta']['photo'][] = array("url" => $photourl, "alt" => $_POST['photos_alt'][$key]);
                }
            }
            $_POST['smeta']['thumb'] = sp_asset_relative_url($_POST['smeta']['thumb']);
            unset($_POST['post']['post_author']);
            $article = I("post.post");
            $article['smeta'] = json_encode($_POST['smeta']);
            $article['post_content'] = htmlspecialchars_decode($article['post_content']);
            $result = $this->posts_model->save($article);
            if ($result !== false) {
                $this->success("保存成功！");
            } else {
                $this->error("保存失败！");
            }
        }
    }

//排序
    public
    function listorders()
    {
        $status = parent::_listorders($this->term_relationships_model);
        if ($status) {
            $this->success("排序更新成功！");
        } else {
            $this->error("排序更新失败！");
        }
    }

    private
    function _lists($status = 1)
    {

        $idList = $this->terms_model->where(array("parent" => 1))->select();
        $idList = $idList ? $idList : 1;
        $map = "1";
        foreach ($idList as $mtermId) {
            $map = $map ? $map . "," . $mtermId["term_id"] : $mtermId["term_id"];
        }

//        $term_id = $_REQUEST["term"] ? $_REQUEST["term"] : 1;
        if (!empty($_REQUEST["term"])) {
            $term_id = intval($_REQUEST["term"]);
            $term = $this->terms_model->where("term_id=$term_id")->find();
            $this->assign("term", $term);
            $_GET['term'] = $term_id;
        }

        $where_ands = empty($idList) ? array("a.status=$status") : array("a.term_id in($map) and a.status=$status ");

        $fields = array(
            'start_time' => array("field" => "post_date", "operator" => ">"),
            'end_time' => array("field" => "post_date", "operator" => "<"),
            'keyword' => array("field" => "post_title", "operator" => "like"),
        );
        if (IS_POST) {

            foreach ($fields as $param => $val) {
                if (isset($_POST[$param]) && !empty($_POST[$param])) {
                    $operator = $val['operator'];
                    $field = $val['field'];
                    $get = $_POST[$param];
                    $_GET[$param] = $get;
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


        $count = $this->term_relationships_model
            ->alias("a")
            ->join(C('DB_PREFIX') . "posts b ON a.object_id = b.id")
            ->join("__TERMS__ t ON a.term_id = t.term_id")
            ->where($where)
            ->count();

        $page = $this->page($count, 20);


        $posts = $this->term_relationships_model
            ->alias("a")
            ->join(C('DB_PREFIX') . "posts b ON a.object_id = b.id")
            ->join("__TERMS__ t ON a.term_id = t.term_id")
            ->field("a.*,b.*,t.name termname")
            ->where($where)
            ->limit($page->firstRow . ',' . $page->listRows)
            ->order("a.listorder ASC,b.post_modified DESC")->select();
        $users_obj = M("Users");
        $users_data = $users_obj->field("id,user_login")->where("user_status=1")->select();
        $users = array();
        foreach ($users_data as $u) {
            $users[$u['id']] = $u;
        }
//        $terms = $this->terms_model->order(array("term_id = $term_id"))->getField("term_id,name", true);
        $this->assign("users", $users);
//        $this->assign("terms", $terms);
        $this->assign("Page", $page->show('Admin'));
        $this->assign("current_page", $page->GetCurrentPage());
        unset($_GET[C('VAR_URL_PARAMS')]);
        $this->assign("formget", $_GET);
        $this->assign("posts", $posts);
    }

    public
    function upload_material()
    {
        $post_id = $_REQUEST["post_id"];
        $object_id = $_REQUEST["object_id"];
        $this->assign("post_id", $post_id);
        $this->assign("object_id", $object_id);
        $this->display();
    }

    /**
     * @param int $status
     * @date：2018年3月7日15:47:20
     * @param：活动管理
     * @User：刘柏林
     */
    private
    function activityList($status = 1)
    {
        $idList = $this->terms_model->where(array("parent" => 2))->select();
        $idList = $idList ? $idList : 2;
        $map = "2";
        foreach ($idList as $mtermId) {
            $map = $map ? $map . "," . $mtermId["term_id"] : $mtermId["term_id"];
        }

//        $term_id = $_REQUEST["term"] ? $_REQUEST["term"] : 2;
        if (!empty($_REQUEST["term"])) {
            $term_id = intval($_REQUEST["term"]);
            $term = $this->terms_model->where("term_id=$term_id")->find();
            $this->assign("term", $term);
            $_GET['term'] = $term_id;
        }

        $where_ands = empty($idList) ? array("a.status=$status") : array("a.term_id in($map) and a.status=$status  and b.review_id !=-1 ");

        $fields = array(
            'start_time' => array("field" => "post_date", "operator" => ">"),
            'end_time' => array("field" => "post_date", "operator" => "<"),
            'keyword' => array("field" => "post_title", "operator" => "like"),
        );
        if (IS_POST) {

            foreach ($fields as $param => $val) {
                if (isset($_POST[$param]) && !empty($_POST[$param])) {
                    $operator = $val['operator'];
                    $field = $val['field'];
                    $get = $_POST[$param];
                    $_GET[$param] = $get;
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


        $count = $this->term_relationships_model
            ->alias("a")
            ->join(C('DB_PREFIX') . "posts b ON a.object_id = b.id")
            ->join("__TERMS__ t ON a.term_id = t.term_id")
            ->where($where)
            ->count();

        $page = $this->page($count, 20);

//        $where_ands = empty($idList) ? array("a.status=$status") : array("a.term_id in(2,$map)  and a.status=$status  and b.review_id !=-1 ");

        $posts = $this->term_relationships_model
            ->alias("a")
            ->join(C('DB_PREFIX') . "posts b ON a.object_id = b.id")
            ->join("__TERMS__ t ON a.term_id = t.term_id")
            ->field("a.*,b.*,t.name termname")
            ->where($where)
            ->limit($page->firstRow . ',' . $page->listRows)
            ->order("a.listorder ASC,b.post_modified DESC")->select();
        $users_obj = M("Users");
        $users_data = $users_obj->field("id,user_login")->where("user_status=1")->select();
        $users = array();
        foreach ($users_data as $u) {
            $users[$u['id']] = $u;
        }
//        $terms = $this->terms_model->order(array("term_id = $term_id"))->getField("term_id,name", true);
        $this->assign("users", $users);
//        $this->assign("terms", $terms);
        $this->assign("Page", $page->show('Admin'));
        $this->assign("current_page", $page->GetCurrentPage());
        unset($_GET[C('VAR_URL_PARAMS')]);
        $this->assign("formget", $_GET);
        $this->assign("posts", $posts);
    }

    /**
     * @date：2018年3月5日20:07:21
     * @param：审核列表
     * @User：刘柏林
     */
    public
    function audit()
    {
        $where_ands = array("s.audit_status != 2");
        $fields = array(
            'start_time' => array("field" => "s.create_time", "operator" => ">"),
            'end_time' => array("field" => "s.create_time", "operator" => "<"),
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
        $post_id = $_GET['post_id'];

        array_push($where_ands, " s.object_id =$post_id ");

        $where = join(" and ", $where_ands);

        $count = M('sign_up')
            ->alias("s")
            ->join("__USERS__ u on s.uid = u.id")
            ->join("__POSTS__ p on s.object_id = p.id")
            ->where($where)
            ->count();
        $page = $this->page($count, 20);
        $forumPostList = M('sign_up')
            ->alias("s")
            ->join("__USERS__ u on s.uid = u.id")
            ->join("__POSTS__ p on s.object_id = p.id")
            ->field("s.*,u.user_nicename full_name,p.post_title")
            ->where($where)
            ->order('s.create_time desc')
            ->limit($page->firstRow . ',' . $page->listRows)
            ->select();
//        foreach ($forumPostList as $k => $item) {
//            $forumPostList[$k]['create_time'] = date("Y-m-d H:i:s", $item['create_time']);
//        }
        $this->assign("page", $page->show('Admin'));
        unset($_GET[C('VAR_URL_PARAMS')]);
        $this->assign("formget", $_GET);
        $this->assign("PostList", $forumPostList);
        $this->display();
    }

    /**
     * @date：2018年3月5日20:09:17
     * @param：素材列表
     * @User：刘柏林
     */
    public
    function material()
    {
        $post_id = $_GET['post_id'];
//        $opus = $_GET['opus'];
        $object_id = $_GET['object_id'];
//        $signUp = M('sof_attachment')->where("id in ($opus)")->select();
        $where['uid'] = $post_id;
        $where['object_id'] = $object_id;
//        $where['id'] = array('not in', $opus);

        $count = M('sof_attachment')
            ->where($where)
            ->count();
        $page = $this->page($count, 12);

        $attachment = M('sof_attachment')
            ->where($where)
            ->limit($page->firstRow . ',' . $page->listRows)
            ->select();
        $this->assign("page", $page->show('Admin'));
        $this->assign("attachment", $attachment);
        $this->assign("uid", $post_id);
        $this->assign("object_id", $object_id);
//        $this->assign("signUp", $signUp);
        $this->display();
    }

    /**
     * @date：2018年3月5日20:09:17
     * @param：素材列表
     * @User：刘柏林
     */
    public
    function material_material()
    {
        $id = $_GET['id'];
        if (isset($_GET['material_id'])) {
            $material_id = $_GET['material_id'];
            $uid = $_GET['uid'];
            $signUp = M('sof_attachment')->where("id in ($material_id)")->select();
//            $where['uid'] = $uid;
            $where['object_id'] = $id;
            $where['id'] = array('not in', $material_id);


            $count = M('sof_attachment')
                ->where($where)
                ->count();
            $page = $this->page($count, 12);

            $attachment = M('sof_attachment')
                ->where($where)
                ->limit($page->firstRow . ',' . $page->listRows)
                ->select();
        } else {
            $uid = $_GET['uid'];
            $signUp = M('sof_attachment')->where(array("id" => -1))->select();
//            $where['uid'] = $uid;
            $where['object_id'] = $id;
//            $where['id'] = array('not in', $material_id);
            $count = M('sof_attachment')
                ->where($where)
                ->count();
            $page = $this->page($count, 12);
            $attachment = M('sof_attachment')
                ->where($where)
                ->limit($page->firstRow . ',' . $page->listRows)
                ->select();
        }
        $this->assign("page", $page->show('Admin'));
        $this->assign("attachment", $attachment);
        $this->assign("uid", $uid);
        $this->assign("object_id", $id);
        $this->assign("signUp", $signUp);
        $this->display();
    }

    /**
     * @date：2018年3月5日20:09:17
     * @param：商品列表 选取关联商品
     * @User：刘柏林
     */
    public
    function commodity()
    {

        if (isset($_GET['commodity_id'])) {
            $commodity_id = $_GET['commodity_id'];
            $id = $_GET['id'];
        } else {
            $commodity_id = $_POST['commodity_id'];
            $id = $_GET['id'];
        }
        if ($commodity_id) {
            if (empty($_POST['term']) || $_POST['term'] == 0) {

                $count = $this->goods_model
                    ->where("id not in ($commodity_id)")
                    ->count();
                $page = $this->page($count, 12);

                $info = $this->goods_model
                    ->where("id not in ($commodity_id)")
                    ->limit($page->firstRow . ',' . $page->listRows)
                    ->select();
            } else {
                $where['id'] = array('not in', $commodity_id);
                $where['cid'] = $_POST['term'];
                $count = $this->goods_model
                    ->where($where)
                    ->count();
                $page = $this->page($count, 12);

                $info = $this->goods_model
                    ->where($where)
                    ->limit($page->firstRow . ',' . $page->listRows)
                    ->select();
            }


            $selectedList = $this->goods_model->where("id in ($commodity_id)")->select();
            $goodsCat = M('goods_cat')->select();
            foreach ($info as $k => $item) {
                $info[$k]['smeta'] = json_decode($item['smeta'], true);
            }
            foreach ($selectedList as $ks => $items) {
                $selectedList[$ks]['smeta'] = json_decode($items['smeta'], true);
            }
        } else {
            if (empty($_POST['term']) || $_POST['term'] == 0) {
                $count = $this->goods_model
                    ->count();
                $page = $this->page($count, 12);
                $info = $this->goods_model->limit($page->firstRow . ',' . $page->listRows)->select();
            } else {
//                $where['id'] = array('not in', $commodity_id);
                $where['cid'] = $_POST['term'];
                $count = $this->goods_model
                    ->where($where)
                    ->count();
                $page = $this->page($count, 12);
                $info = $this->goods_model->where($where)
                    ->limit($page->firstRow . ',' . $page->listRows)
                    ->select();
            }


            $selectedList = $this->goods_model->where("id =-1")->select();
            $goodsCat = M('goods_cat')->select();
            foreach ($info as $k => $item) {
                $info[$k]['smeta'] = json_decode($item['smeta'], true);
            }
            foreach ($selectedList as $ks => $items) {
                $selectedList[$ks]['smeta'] = json_decode($items['smeta'], true);
            }
        }
        $this->assign("page", $page->show('Admin'));
        $this->assign("info", $info);
        $this->assign("goodsCat", $goodsCat);
        $this->assign("commodity_id", $commodity_id);
        $this->assign("id", $id);
        $this->assign("selectedList", $selectedList);
        $this->display();
    }

    /**
     * @date：2018年3月6日17:07:57
     * @param：选取素材
     * @User：刘柏林
     */
    function subMaterial()
    {
        if (isset($_POST['ids']) && $_GET["check"]) {
            $data["material_id"] = join(",", $_POST['ids']);

            $object_id = $_POST['object_id'];

//            $tids = join(",", $_POST['ids']);

            if ($this->posts_model->where(array("id" => $object_id))->save($data) !== false) {
                $this->success("素材选取成功！");
            } else {
                $this->error("素材选取失败！");
            }
        }

    }

    /**
     * @date：2018年3月6日17:07:57
     * @param：选取商品
     * @User：刘柏林
     */
    function subCommodity()
    {
        $id = $_POST['id'];
        if (isset($_POST['ids']) && $_GET["check"]) {
            $data["commodity_id"] = join(",", $_POST['ids']);

            if ($this->posts_model->where(array("id" => $id))->save($data) !== false) {
                $this->success("商品添加成功！");
            } else {
                $this->error("商品添加失败！");
            }
        }
    }

    private
    function _getTree()
    {
        $term_id = empty($_REQUEST['term']) ? 0 : intval($_REQUEST['term']);
        $result = $this->terms_model->order(array("listorder" => "asc"))->select();

        $tree = new \Tree();
        $tree->icon = array('&nbsp;&nbsp;&nbsp;│ ', '&nbsp;&nbsp;&nbsp;├─ ', '&nbsp;&nbsp;&nbsp;└─ ');
        $tree->nbsp = '&nbsp;&nbsp;&nbsp;';
        foreach ($result as $r) {
            $r['str_manage'] = '<a href="' . U("AdminTerm/add", array("parent" => $r['term_id'])) . '">添加子类</a> | <a href="' . U("AdminTerm/edit", array("id" => $r['term_id'])) . '">修改</a> | <a class="js-ajax-delete" href="' . U("AdminTerm/delete", array("id" => $r['term_id'])) . '">删除</a> ';
            $r['visit'] = "<a href='#'>访问</a>";
            $r['taxonomys'] = $this->taxonomys[$r['taxonomy']];
            $r['id'] = $r['term_id'];
            $r['parentid'] = $r['parent'];
            $r['selected'] = $term_id == $r['term_id'] ? "selected" : "";
            $array[] = $r;
        }

        $tree->init($array);
        $str = "<option value='\$id' \$selected>\$spacer\$name</option>";
        $taxonomys = $tree->get_tree(0, $str);
        $this->assign("taxonomys", $taxonomys);
    }

    private
    function _getTermTree($term = array(), $term_id = "")
    {
        $result = $this->terms_model->where("term_id in ($term_id)")->order(array("listorder" => "asc"))->select();

        $tree = new \Tree();
        $tree->icon = array('&nbsp;&nbsp;&nbsp;│ ', '&nbsp;&nbsp;&nbsp;├─ ', '&nbsp;&nbsp;&nbsp;└─ ');
        $tree->nbsp = '&nbsp;&nbsp;&nbsp;';
        foreach ($result as $r) {
            $r['str_manage'] = '<a href="' . U("AdminTerm/add", array("parent" => $r['term_id'])) . '">添加子类</a> | <a href="' . U("AdminTerm/edit", array("id" => $r['term_id'])) . '">修改</a> | <a class="js-ajax-delete" href="' . U("AdminTerm/delete", array("id" => $r['term_id'])) . '">删除</a> ';
            $r['visit'] = "<a href='#'>访问</a>";
            $r['taxonomys'] = $this->taxonomys[$r['taxonomy']];
            $r['id'] = $r['term_id'];
            $r['parentid'] = $r['parent'];
            $r['selected'] = in_array($r['term_id'], $term) ? "selected" : "";
            $r['checked'] = in_array($r['term_id'], $term) ? "checked" : "";
            $array[] = $r;
        }

        $tree->init($array);
        $str = "<option value='\$id' \$selected>\$spacer\$name</option>";
        $taxonomys = $tree->get_tree(0, $str);
        $this->assign("taxonomys", $taxonomys);
    }

    function delete()
    {
        if (isset($_GET['tid'])) {
            $tid = intval(I("get.tid"));
            $data['status'] = 0;
            if ($this->term_relationships_model->where("tid=$tid")->save($data)) {
                $this->success("删除成功！");
            } else {
                $this->error("删除失败！");
            }
        }
        if (isset($_POST['ids'])) {
            $tids = join(",", $_POST['ids']);
            $data['status'] = 0;
            if ($this->term_relationships_model->where("tid in ($tids)")->save($data)) {
                $this->success("删除成功！");
            } else {
                $this->error("删除失败！");
            }
        }
    }

    function check()
    {
        if (isset($_POST['ids']) && $_GET["check"]) {
            $data["post_status"] = 1;

            $tids = join(",", $_POST['ids']);
            $objectids = $this->term_relationships_model->field("object_id")->where("tid in ($tids)")->select();
            $ids = array();
            foreach ($objectids as $id) {
                $ids[] = $id["object_id"];
            }
            $ids = join(",", $ids);
            if ($this->posts_model->where("id in ($ids)")->save($data) !== false) {
                $this->success("审核成功！");
            } else {
                $this->error("审核失败！");
            }
        }
        if (isset($_POST['ids']) && $_GET["uncheck"]) {

            $data["post_status"] = 0;
            $tids = join(",", $_POST['ids']);
            $objectids = $this->term_relationships_model->field("object_id")->where("tid in ($tids)")->select();
            $ids = array();
            foreach ($objectids as $id) {
                $ids[] = $id["object_id"];
            }
            $ids = join(",", $ids);
            if ($this->posts_model->where("id in ($ids)")->save($data)) {
                $this->success("取消审核成功！");
            } else {
                $this->error("取消审核失败！");
            }
        }
    }

    function top()
    {
        if (isset($_POST['ids']) && $_GET["top"]) {
            $data["istop"] = 1;

            $tids = join(",", $_POST['ids']);
            $objectids = $this->term_relationships_model->field("object_id")->where("tid in ($tids)")->select();
            $ids = array();
            foreach ($objectids as $id) {
                $ids[] = $id["object_id"];
            }
            $ids = join(",", $ids);
            if ($this->posts_model->where("id in ($ids)")->save($data) !== false) {
                $this->success("置顶成功！");
            } else {
                $this->error("置顶失败！");
            }
        }
        if (isset($_POST['ids']) && $_GET["untop"]) {

            $data["istop"] = 0;
            $tids = join(",", $_POST['ids']);
            $objectids = $this->term_relationships_model->field("object_id")->where("tid in ($tids)")->select();
            $ids = array();
            foreach ($objectids as $id) {
                $ids[] = $id["object_id"];
            }
            $ids = join(",", $ids);
            if ($this->posts_model->where("id in ($ids)")->save($data)) {
                $this->success("取消置顶成功！");
            } else {
                $this->error("取消置顶失败！");
            }
        }
    }

    function recommend()
    {
        if (isset($_POST['ids']) && $_GET["recommend"]) {
            $data["recommended"] = 1;

            $tids = join(",", $_POST['ids']);
            $objectids = $this->term_relationships_model->field("object_id")->where("tid in ($tids)")->select();
            $ids = array();
            foreach ($objectids as $id) {
                $ids[] = $id["object_id"];
            }
            $ids = join(",", $ids);
            if ($this->posts_model->where("id in ($ids)")->save($data) !== false) {
                $this->success("推荐成功！");
            } else {
                $this->error("推荐失败！");
            }
        }
        if (isset($_POST['ids']) && $_GET["unrecommend"]) {

            $data["recommended"] = 0;
            $tids = join(",", $_POST['ids']);
            $objectids = $this->term_relationships_model->field("object_id")->where("tid in ($tids)")->select();
            $ids = array();
            foreach ($objectids as $id) {
                $ids[] = $id["object_id"];
            }
            $ids = join(",", $ids);
            if ($this->posts_model->where("id in ($ids)")->save($data)) {
                $this->success("取消推荐成功！");
            } else {
                $this->error("取消推荐失败！");
            }
        }
    }

    function move()
    {
        if (IS_POST) {
            if (isset($_GET['ids']) && isset($_POST['term_id'])) {
                $tids = $_GET['ids'];
                if ($this->term_relationships_model->where("tid in ($tids)")->save($_POST) !== false) {
                    $this->success("移动成功！");
                } else {
                    $this->error("移动失败！");
                }
            }
        } else {
            $parentid = intval(I("get.parent"));

            $tree = new \Tree();
            $tree->icon = array('&nbsp;&nbsp;&nbsp;│ ', '&nbsp;&nbsp;&nbsp;├─ ', '&nbsp;&nbsp;&nbsp;└─ ');
            $tree->nbsp = '&nbsp;&nbsp;&nbsp;';
            $terms = $this->terms_model->order(array("path" => "asc"))->select();
            $new_terms = array();
            foreach ($terms as $r) {
                $r['id'] = $r['term_id'];
                $r['parentid'] = $r['parent'];
                $new_terms[] = $r;
            }
            $tree->init($new_terms);
            $tree_tpl = "<option value='\$id'>\$spacer\$name</option>";
            $tree = $tree->get_tree(0, $tree_tpl);

            $this->assign("terms_tree", $tree);
            $this->display();
        }
    }

    /**
     * @date：2018年3月16日16:30:10
     * @param：活动回收
     * @User：刘柏林
     */
    function recyclebin()
    {
        $this->activityList(0);
        $this->_getTree();
        $this->display();
    }
    /**
     * @date：2018年3月16日16:30:10
     * @param：文章回收
     * @User：刘柏林
     */
    function recyarticle()
    {
        $this->articleList(0);
        $this->_getTree();
        $this->display();
    }
    /**
     * @date：2018年3月16日16:30:10
     * @param：比赛回收
     * @User：刘柏林
     */
    function recycling()
    {
        $this->_lists(0);
        $this->_getTree();
        $this->display();
    }

    function clean()
    {
        if (isset($_POST['ids'])) {
            $ids = implode(",", $_POST['ids']);
            $tids = implode(",", array_keys($_POST['ids']));
            $data = array("post_status" => "0");
            $status = $this->term_relationships_model->where("tid in ($tids)")->delete();
            if ($status !== false) {
                foreach ($_POST['ids'] as $post_id) {
                    $post_id = intval($post_id);
                    $count = $this->term_relationships_model->where(array("object_id" => $post_id))->count();
                    if (empty($count)) {
                        $status = $this->posts_model->where(array("id" => $post_id))->delete();
                    }
                }

            }

            if ($status !== false) {
                $this->success("删除成功！");
            } else {
                $this->error("删除失败！");
            }
        } else {
            if (isset($_GET['id'])) {
                $id = intval(I("get.id"));
                $tid = intval(I("get.tid"));
                $status = $this->term_relationships_model->where("tid = $tid")->delete();
                if ($status !== false) {
                    $count = $this->term_relationships_model->where(array("object_id" => $id))->count();
                    if (empty($count)) {
                        $status = $this->posts_model->where("id=$id")->delete();
                    }

                }
                if ($status !== false) {
                    $this->success("删除成功！");
                } else {
                    $this->error("删除失败！");
                }
            }
        }
    }

    function restore()
    {
        if (isset($_GET['id'])) {
            $id = intval(I("get.id"));
            $data = array("tid" => $id, "status" => "1");
            if ($this->term_relationships_model->save($data)) {
                $this->success("还原成功！");
            } else {
                $this->error("还原失败！");
            }
        }
    }

    /**
     * @date：2018年1月14日16:12:29
     * @param：圈主审核
     * @User：刘柏林
     */
    public
    function circleCheck()
    {
        if (isset($_GET['ids']) && $_GET["check"]) {
            $data["audit_status"] = 1;
            $tid = $_GET['ids'];
            $post_title = $_GET['post_title'];
            if ($this->sign_up_model->where(array("id" => $tid))->save($data) !== false) {
                $data["create_time"] = time();
                $data['from_id'] = get_current_admin_id();
                $data['title'] = $post_title . "比赛审核";
                $data['content'] = $post_title . "比赛审核成功";
                $data['args'] = $tid;
                $this->message_content_model->add($data);
                $this->success("审核成功！");
            } else {
                $this->error("审核失败！");
            }
        }
        if (isset($_GET['ids']) && $_GET["uncheck"]) {

            $data["audit_status"] = 0;
            $tid = $_GET['ids'];

            if ($this->sign_up_model->where(array("id" => $tid))->save($data)) {
                $data["create_time"] = time();
                $data['from_id'] = get_current_admin_id();
                $data['title'] = $post_title . "比赛审核";
                $data['content'] = $post_title . "比赛已取消审核";
                $data['args'] = $tid;
                $this->message_content_model->add($data);
                $this->success("已取消审核！");
            } else {
                $this->error("取消审核失败！");
            }
        }
        if (isset($_GET['ids']) && $_GET["entlassen"]) {

            $data["audit_status"] = 2;
            $tid = $_GET['ids'];

            if ($this->sign_up_model->where(array("id" => $tid))->save($data)) {
                $data["create_time"] = time();
                $data['from_id'] = get_current_admin_id();
                $data['title'] = $post_title . "比赛审核";
                $data['content'] = $post_title . "比赛已驳回";
                $data['args'] = $tid;
                $this->message_content_model->add($data);
                $this->success("已驳回！");
            } else {
                $this->error("驳回失败！");
            }
        }
    }

    /**
     * @date：2018年1月12日18:35:37
     * @param：审核
     * @User：刘柏林
     */
    public
    function auditors()
    {
        if (isset($_POST['ids']) && $_GET["audit"]) {
            $data["audit_status"] = 1;

            $tid = join(",", $_POST['ids']);

            if ($this->sign_up_model->where("id in ($tid)")->save($data) !== false) {
                $this->success("推荐成功！");
            } else {
                $this->error("推荐失败！");
            }
        }
        if (isset($_POST['ids']) && $_GET["unaudit"]) {

            $data["audit_status"] = 0;
            $tid = join(",", $_POST['ids']);

            if ($this->sign_up_model->where("id in ($tid)")->save($data)) {
                $this->success("取消推荐成功！");
            } else {
                $this->error("取消推荐失败！");
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
            'exts' => array('gif', 'mp4', 'jpg', 'png', 'sql'),
            'autoSub' => false,
        );

        ini_set('max_execution_time', '0');
        $upload = new \Think\Upload($config);//
        $info = $upload->upload();
        //开始上传
        if ($info) {
            //上传成功
            $file = $info['uploadkey']['savepath'] . $info['uploadkey']['savename'];
            $array = explode('.', sp_asset_relative_url(C("TMPL_PARSE_STRING.__UPLOAD__") . $file));

            //添加视频图片
            $dataAttachment["url"] = sp_asset_relative_url(C("TMPL_PARSE_STRING.__UPLOAD__") . $file);
            $dataAttachment["object_id"] = $_GET['object_id'];
            $dataAttachment["create_time"] = time();
            $dataAttachment["tablename"] = "posts";
            $dataAttachment["state"] = 1;
            $dataAttachment["type"] = $array[1] == 'mp4' || $array[1] == 'gif' ? 1 : 0;
            $dataAttachment["uid"] = $_GET['post_id'];
            $dataAttachment["size"] = $info['uploadkey']['size'];
            M("sof_attachment")->add($dataAttachment);


            echo json_encode(array('status' => '1', 'type' => I('param.type'), 'url' => C("TMPL_PARSE_STRING.__UPLOAD__") . $file, 'file_alt' => $info['uploadkey']['name'], 'file_size' => $info['uploadkey']['size']));
            exit;

        } else {
            //上传失败，返回错误
            echo json_encode(array('status' => '0', 'info' => $upload->getError()));
            exit;
        }
    }

    /**
     * @date：2018年3月16日15:52:112
     * @param：比赛评论管理
     * @User：刘柏林
     */
    public
    function competition_review()
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
        $idList = $this->terms_model->where(array("parent" => 1))->select();

        $map = "";
        foreach ($idList as $mtermId) {
            $map = $map ? $map . "," . $mtermId["term_id"] : $mtermId["term_id"];
        }
        $map = $map ? "1," . $map : 1;

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
        $comments = M('reply')
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
        $this->display();
    }
    /**
     * @date：2018年3月16日15:52:112
     * @param：文章管理
     * @User：刘柏林
     */
    public
    function comment_article()
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


        $idList = $this->terms_model->where(array("parent" => 5))->select();

        $map = "";
        foreach ($idList as $mtermId) {
            $map = $map ? $map . "," . $mtermId["term_id"] : $mtermId["term_id"];
        }

        $map = $map ? "5," . $map : 5;

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
        $comments = M('reply')
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
        $this->display();
    }
    /**
     * @date：2018年1月12日18:35:37
     * @param：推荐
     * @User：刘柏林
     */
    public
    function recommendReply()
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
     * @param  删除评论
     * @User：刘柏林
     */
    public
    function deleteReply()
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
     * @param：评论回收站
     * @User：刘柏林
     */
    public
    function reply_del()
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
        $idList = $this->terms_model->where(array("parent" => 1))->select();

        $map = "";
        foreach ($idList as $mtermId) {
            $map = $map ? $map . "," . $mtermId["term_id"] : $mtermId["term_id"];
        }
        $map = $map ? "1," . $map : 1;
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
        $this->display();
    }

    /**
     * @date：2018年1月18日14:22:03
     * @param：评论回收站 文章
     * @User：刘柏林
     */
    public function article_reply_del()
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
        $idList = $this->terms_model->where(array("parent" => 5))->select();

        $map = "";
        foreach ($idList as $mtermId) {
            $map = $map ? $map . "," . $mtermId["term_id"] : $mtermId["term_id"];
        }
        $map = $map ? "5," . $map : 5;
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
        $this->display();
    }
    /**
     * @date：2018年1月18日14:22:03
     * @param 热门推荐评论 文章
     * @User：刘柏林
     */
    public function article_reply_home()
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
        $idList = $this->terms_model->where(array("parent" => 1))->select();

        $map = "";
        foreach ($idList as $mtermId) {
            $map = $map ? $map . "," . $mtermId["term_id"] : $mtermId["term_id"];
        }
        $map = $map ? "1," . $map : 1;
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
        $this->display();
    }
    /**
     * @date：2018年1月18日14:22:03
     * @param 热门推荐评论
     * @User：刘柏林
     */
    public
    function reply_home()
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
        array_push($where_ands, "r.tablename = 'posts'  and t.term_id =1 ");
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
        $this->display();
    }

    /**
     * @date：2018年1月14日17:23:48
     * @param：评论恢复删除
     * @User：刘柏林
     */
    public
    function recoveryReply()
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
     * @Date 2018年1月12日15:22:22
     * @content 还原清空帖子
     * @User：刘柏林
     */
    public
    function trashInfo()
    {
        if (isset($_POST['ids']) && $_GET["trashId"]) {
            $data["status"] = 1;

            $tid = join(",", $_POST['ids']);
            if ($this->term_relationships_model->where("object_id in ($tid)")->save($data) !== false) {
                $this->success("还原成功！");
            } else {
                $this->error("还原失败！");
            }
        }
        if (isset($_POST['ids']) && $_GET["uncheckId"]) {

            $data["status"] = 0;
            $tid = join(",", $_POST['ids']);

            if ($this->term_relationships_model->where("object_id in ($tid)")->save($data)) {
                $this->success("删除成功！");
            } else {
                $this->error("删除失败！");
            }
        }
    }

    function articleList($status = 1)
    {

        $idList = $this->terms_model->where(array("parent" => 5))->select();
        $idList = $idList ? $idList : 5;
        $map = "5";
        foreach ($idList as $mtermId) {
            $map = $map ? $map . "," . $mtermId["term_id"] : $mtermId["term_id"];
        }

//        $term_id = $_REQUEST["term"] ? $_REQUEST["term"] : 1;
        if (!empty($_REQUEST["term"])) {
            $term_id = intval($_REQUEST["term"]);
            $term = $this->terms_model->where("term_id=$term_id")->find();
            $this->assign("term", $term);
            $_GET['term'] = $term_id;
        }

        $where_ands = array(" a.status=$status ");

        $fields = array(
            'start_time' => array("field" => "post_date", "operator" => ">"),
            'end_time' => array("field" => "post_date", "operator" => "<"),
            'keyword' => array("field" => "post_title", "operator" => "like"),
        );
        if (IS_POST) {

            foreach ($fields as $param => $val) {
                if (isset($_POST[$param]) && !empty($_POST[$param])) {
                    $operator = $val['operator'];
                    $field = $val['field'];
                    $get = $_POST[$param];
                    $_GET[$param] = $get;
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

        $count = $this->term_relationships_model
            ->alias("a")
            ->join(C('DB_PREFIX') . "posts b ON a.object_id = b.id")
            ->join("__TERMS__ t on a.term_id = t.term_id")
            ->where($where)
            ->count();

        $page = $this->page($count, 20);


        $posts = $this->term_relationships_model
            ->alias("a")
            ->join(C('DB_PREFIX') . "posts b ON a.object_id = b.id")
            ->join("__TERMS__ t ON a.term_id = t.term_id")
            ->field("a.*,b.*,t.name termname")
            ->where($where)
            ->limit($page->firstRow . ',' . $page->listRows)
            ->order("a.listorder ASC,b.post_modified DESC")->select();
        $users_obj = M("Users");
        $users_data = $users_obj->field("id,user_login")->where("user_status=1")->select();
        $users = array();
        foreach ($users_data as $u) {
            $users[$u['id']] = $u;
        }
//        $terms = $this->terms_model->order(array("term_id = $term_id"))->getField("term_id,name", true);
        $this->assign("users", $users);
//        $this->assign("terms", $terms);
        $this->assign("Page", $page->show('Admin'));
        $this->assign("current_page", $page->GetCurrentPage());
        unset($_GET[C('VAR_URL_PARAMS')]);
        $this->assign("formget", $_GET);
        $this->assign("posts", $posts);
    }

}