<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c)
// +----------------------------------------------------------------------
// | Author: 刘北林
// +----------------------------------------------------------------------
namespace Api\Controller;

use Think\Controller;

//use Api\Controller\FileUploadController;
class SystemController extends AppController
{
    function __construct()
    {
        header("Content-Type:text/html;charset=utf-8");
        parent::_initialize();
//        //限制ip访问
//        $list = array('211.152.53.68', '127.0.0.1', '139.196.38.222');
//        if(!in_array(get_client_ip(), $list)){
//            //\Think\Log::write(get_client_ip());exit;
//        }
    }

    /***
     * Notes:发邮件
     * User: Belief
     * DateTime: 2020-06-28 19:22
     * Return :返回true false
     */
    public function send_email()
    {
        $result = sp_send_email('354564559@qq.com', '发简历', '8885222', './public/xianyu/resume/20200628/2020-06-281593312339.docx', '简历.docx');

        if ($result) {
            $this->apiSuccess($result);
        } else {
            $this->apiError($result);
        }

    }

    /**
     * Notes: 多文件上传（共用）
     * User: Sen
     * DateTime: 2020/7/2 15:50
     * Return:
     */
    public function filesUpload()
    {
        $FileUploadController = new FileUploadController();

        $files = $_FILES["files"];
        $type = $_REQUEST["type"];
//        $num = $_REQUEST["num"];

        if (empty($files) || empty($type)) {
            $this->apiError("非法请求！");
        }
        $path = "";
        $max = "";
        if ($type == 1) {//简历
            $path = "./public/xianyu/resume/";
            $max = 2000000;
        } else if ($type == 2) {//公司图片
            $path = "./public/xianyu/company/";
            $max = 2000000;
        } else if ($type == 3) {//用户
            $path = "./public/xianyu/user/";
            $max = 2000000;
        } else if ($type == 4) {//朋友圈
            $path = "./public/xianyu/sof/";
            $max = 2000000;
        } else if ($type == 5) {//公用
            $path = "./public/xianyu/public/";
            $max = 2000000;
        }

        $data["data"] = $FileUploadController->uploadFileOne($files, $path, $max);

        $this->apiReturn($data);
    }

    /**
     * Notes: 职位搜索（根据名字）
     * User: Sen
     * DateTime: 2020/7/4 0:17
     * Return :
     */
    public function positionSearch()
    {
        $resources = new ResourcesController();
        $dataL = $this->dataL;

        $unionid = $resources->remove_xss($dataL["unionid"]);
        $name = strtoupper($resources->remove_xss($dataL["name"]));

        if (empty($unionid)) {
            $this->apiError("非法请求！");
        }
        if (empty($name)) {
            $this->apiError("搜索内容不能为空！");
        }
        $third = M("spec_position")
            ->where(array("name" => array("like", '%' . $name . '%'), "parentcode" => 2))
            ->select();
        if (empty($third)) {
            $this->apiError("该职位未录入！");
        }
        $second = array();
        foreach ($third as $key => $val) {
            $second[] = M("spec_position")
                ->where(array("id" => $val["parent_id"], "parentcode" => 1))
                ->find();
        }

        $first = array();
        foreach ($second as $k => $v) {
            $first[] = M("spec_position")
                ->where(array("id" => $v["parent_id"], "parentcode" => 0))
                ->find();
        }

        $data = array();
        foreach ($third as $ke => $ve) {
            $data[] = array(
                "fid" => $first[$ke]["id"],
                "sid" => $second[$ke]["id"],
                "tid" => $third[$ke]["id"],
                "relationship" => $first[$ke]["name"] . "-" . $second[$ke]["name"],
                "name" => $third[$ke]["name"],
            );
        }

        $this->apiReturn(array("data" => $data));

    }

    /**
     * Notes: 职位分类
     * User: Sen
     * DateTime: 2020/7/4 19:39
     * Return :
     */
    public function positionList()
    {
        $resources = new ResourcesController();
        $dataL = $this->dataL;
        $unionid = strtoupper($resources->remove_xss($dataL["unionid"]));

        if (empty($unionid)) {
            $this->apiError("非法请求！");
        }

        $res = M("spec_position")->select();
        $data = $resources->generateTree($res, "id", "parent_id");
        $this->apiReturn(array("data" => $data));
    }

    /**
     * Notes: 城市列表
     * User: Sen
     * DateTime: 2020/7/8 12:00
     * Return:
     */
        public function  cityList()
    {
        $resources = new ResourcesController();
        $dataL = $this->dataL;
        $unionid = strtoupper($resources->remove_xss($dataL["unionid"]));

        if (empty($unionid)) {
            $this->apiError("非法请求！");
        }

        $res  = M('city')->select();
        $data["data"] = $resources->dataGroup($res,"alphabet");
//        $data["data"] = $resources->arraySort($data["data"],"alphabet");

        $this->apiReturn($data);
    }
}