<?php
/**
 * Created by PhpStorm.
 * User: 仙瑜005
 * Date: 2020/6/28
 * Time: 15:59
 */

namespace Api\Controller;

use Think\Controller;
use Api\Controller\ResourcesController;

/**
 * 此类为求职专用类
 * Class JobSearchController
 * @package Api\Controller
 */
class JobSearchController extends AppController

{
    protected $user_model;//用户
    protected $recruitment_model;//招聘表
    protected $job_application_model;//求职岗位留言
    protected $sof_attachment_model;//求职岗位留言
    protected $spec_industry_model;//求职岗位留言
    protected $job_send_model;//简历投递
    protected $job_greet_model;//简历投递
    protected $user_enterpise_model;//公司表
    protected $spec_model;//标签表
    protected $spec_info_model;//标签分类
    protected $spec_label_model;//标签关系表


    function __construct()
    {
        header("Access-Control-Allow-Origin: *");
        header("Content-Type: text/html; charset=UTF-8");
        parent::_initialize();
        $this->user_model = M('User');
        $this->recruitment_model = M('Recruitment');
        $this->job_application_model = M('JobApplication');
        $this->sof_attachment_model = M('SofAttachment');
        $this->spec_industry_model = M('SpecIndustry');
        $this->user_enterpise_model = M("UserEnterpise");
        $this->job_send_model = M('JobSend');
        $this->job_greet_model = M('JobGreet');
        $this->spec_model = M('Spec');
        $this->spec_info_model = M('SpecInfo');
        $this->spec_label_model = M('SpecLabel');
    }

    /**
     * Notes: 分类筛选列表接口（求职）
     * User: Sen
     * DateTime: 2020/6/29 14:30
     * Return:
     */
    public function categoryFilterList()
    {
//        $dataL = $this->dataL;
//        $resources = new ResourcesController();
//        $unionid = $resources->remove_xss($dataL['unionid']);
//        if (empty($unionid)) {
//            $this->apiError("非法请求！");
//        }

        //验证用户信息
        $nUnionid =trim(I('unionid'));
        $objUser  = D('User');
        list($nErr , $nUserId) = $objUser->getUserIdByUnionId($nUnionid);
        if($nErr != 1){
            return $this->apiError($nUserId);
        }

        $filter_id = array(1, 2, 3, 4, 5, 6);
        $where["id"] = array("in", $filter_id);


        $data = $this->spec_model
            ->where($where)
            ->field("id,spec_name,rename")
            ->select();

        $arrSpecIds = array_column($data, 'id');

        $arrWhere   = [];
        $arrWhere['spec_id'] = ['in',$arrSpecIds];
        $arrList    = $this->spec_info_model
            ->where($arrWhere)
            ->field("id,spec_id,spec_info_name")
            ->select();

        //整理数据
        foreach($data as $k => $v){
            foreach($arrList as $m => $n){
                if($n['spec_id'] == $v['id']){
                    unset($n['spec_id']);
                    $v['list'][] = $n;
                }
            }
            $arrData[] = $v;
        }


//        foreach ($data as $k => $v) {
//            $data[$k]["List"] = $this->spec_info_model
//                ->where(array("spec_id" => $v["id"]))
//                ->field("id,spec_info_name")
//                ->select();
//        }

        $this->apiReturn(array("data" => $arrData));


    }

    /**
     * Notes: 分类下标签详情（求职）
     * User: Sen
     * DateTime: 2020/6/29 14:32
     * Return:
     */
    public function classificationDetails()
    {
//        $dataL = $this->dataL;
//        $resources = new ResourcesController();
//        $unionid = $resources->remove_xss($dataL['unionid']);
//        $id = $resources->remove_xss($dataL['id']); //分类id
//        if (empty($unionid)) {
//            $this->apiError("非法请求！");
//        }
        //验证用户信息
        $nUnionid = trim(I('unionid'));
        $id       = intval(I('id'));
        $objUser  = D('User');
        list($nErr , $nUserId) = $objUser->getUserIdByUnionId($nUnionid);
        if($nErr != 1){
            return $this->apiError($nUserId);
        }

        $data = $this->spec_info_model
            ->where(array("spec_id" => $id))
            ->select();

        $this->apiReturn(array("data" => $data));


    }

    /**
     * Notes: 职位筛选（完成）
     * User: Sen
     * DateTime: 2020/6/28 18:55
     * Return:
     */
    public function positionFilter()
    {
//        $resources = new ResourcesController();
//        $raw_post_data = file_get_contents('php://input', 'r');
//        $dataL = "";
//        if ($raw_post_data) {
//            $dataL = json_decode($raw_post_data, true);
//        }
//        $unionid = $resources->remove_xss($dataL["unionid"]);
//        $education_id = $resources->remove_xss($dataL["education_id"]);//教育id
//        $experience_id = $resources->remove_xss($dataL["experience_id"]);//工作经验id
//        $salary_id = $resources->remove_xss($dataL["salary_id"]);//薪资待遇id
//        $industry = $resources->remove_xss($dataL["industry_id"]);//行业id
//        $scale = $resources->remove_xss($dataL["scale_id"]);//公司规模id
//        $financing = $resources->remove_xss($dataL["financing_id"]);//融资阶段id

        $education_id   = intval(I('education_id'));
        $experience_id  = intval(I('experience_id'));
        $salary_id      = intval(I('salary_id'));
        $industry       = intval(I('industry_id'));
        $scale          = intval(I('scale_id'));
        $financing      = intval(I('financing_id'));

        $arr = array_values(array_filter(array($education_id, $experience_id, $salary_id, $industry, $scale, $scale, $financing)));
//        $page = $resources->remove_xss($dataL["page"]);//每页多少条
//        $page_num = $resources->remove_xss($dataL["page_num"]);//页码
//        if (empty($unionid)) {
//            $this->apiError("非法请求！");
//        }
//        if (empty($page)) {
//            $page = 8;
//        }
//        if (empty($page_num)) {
//            $page_num = 1;
//        }


        $nPage  = intval(I('page'));
        $nLimit = intval(I('page_num'));

        if($nPage <= 0){
            $page = 8;
        }
        if($nLimit <= 0){
            $page_num = 1;
        }

        //验证用户信息
        $nUnionid = trim(I('unionid'));
        $objUser  = D('User');
        list($nErr , $nUserId) = $objUser->getUserIdByUnionId($nUnionid);
        if($nErr != 1){
            return $this->apiError($nUserId);
        }

        if (empty($arr)) {
            //无筛选条件 返回最新
            $recruitmentList = $this->recruitment_model
                ->alias("a")
                ->join("left join __USER__ as b on a.uid = b.id ")
                ->join("right join __USER_ENTERPISE__  as c on c.id = a.enterprise_id")
                ->where(array("a.isdel" => 0, "a.status" => 1))
                ->field("a.id,c.name,a.position,a.time,b.avatar,a.is_anxious,a.address_x,a.address_y,c.id as cid ")
                ->order("a.time desc")
                ->select();
            foreach ($recruitmentList as $k => $v) {
                $lableList = $this->spec_label_model
                    ->alias("s")
                    ->join("LEFT JOIN __SPEC_INFO__ as s1 on s1.id = s.spec_info_id")
                    ->join("__SPEC__ as s2 on s2.id = s1.spec_id")
                    ->where(array("s.object_id" => $v['id'], "s.tablename" => "recruitment"))
                    ->field("s1.spec_info_name,s2.rename")
                    ->select();
                foreach ($lableList as $item => $var) {
                    if ($var['rename'] == "education") {
                        $recruitmentList[$k]["education"] = $var['spec_info_name'];
                    }
                    if ($var['rename'] == "salary") {
                        $recruitmentList[$k]["salary"] = $var['spec_info_name'];
                    }
                }
            }


            foreach ($recruitmentList as $k => $v) {
                $lableList = $this->spec_label_model
                    ->alias("s")
                    ->join("LEFT JOIN __SPEC_INFO__ as s1 on s1.id = s.spec_info_id")
                    ->join("__SPEC__ as s2 on s2.id = s1.spec_id")
                    ->where(array("s.object_id" => $v['cid'], "s.tablename" => "user_enterpise"))
                    ->field("s1.spec_info_name,s2.rename")
                    ->select();

                foreach ($lableList as $item => $var) {
                    if ($var['rename'] == "welfare") {
                        $recruitmentList[$k]["welfare"] = $var['spec_info_name'];
                    }
                }
                unset($recruitmentList[$k]["cid"]);
                unset($recruitmentList[$k]["enterprise_id"]);
                $recruitmentList[$k]["address"] = $recruitmentList[$k]["address_x"] . $recruitmentList[$k]["address_y"];
            }

            $page_data = arr_page($recruitmentList, $page_num, $page);
            $data["data"] = array(
                "current_page" => $page_data["current_page"],
                "total_page" => $page_data['total_page'],
                "recruitmentList" => $page_data["list"]
            );
            $this->apiReturn($data);


        } else {
            //有筛选条件
            asort($arr);
            $str = implode(',', $arr);
            $strs = "\"" . $str . "\"";
            $arrstr = "(" . $str . ")";

            $sql = D('Spec')->recruitmentFilter($arrstr, $strs);
            $res = M()->query($sql);
            foreach ($res as $k => $v) {
                $lableList = $this->spec_label_model
                    ->alias("s")
                    ->join("LEFT JOIN __SPEC_INFO__ as s1 on s1.id = s.spec_info_id")
                    ->join("__SPEC__ as s2 on s2.id = s1.spec_id")
                    ->where(array("s.object_id" => $v['id'], "s.tablename" => "recruitment"))
                    ->field("s1.spec_info_name,s2.rename")
                    ->select();
                foreach ($lableList as $item => $var) {
                    if ($var['rename'] == "education") {
                        $res[$k]["education"] = $var['spec_info_name'];
                    }
                    if ($var['rename'] == "salary") {
                        $res[$k]["salary"] = $var['spec_info_name'];
                    }
                }
            }

            foreach ($res as $k => $v) {
                $lableList = $this->spec_label_model
                    ->alias("s")
                    ->join("LEFT JOIN __SPEC_INFO__ as s1 on s1.id = s.spec_info_id")
                    ->join("__SPEC__ as s2 on s2.id = s1.spec_id")
                    ->where(array("s.object_id" => $v['cid'], "s.tablename" => "user_enterpise"))
                    ->field("s1.spec_info_name,s2.rename")
                    ->select();

                foreach ($lableList as $item => $var) {
                    if ($var['rename'] == "welfare") {
                        $res[$k]["welfare"] = $var['spec_info_name'];
                    }
                }
                $res[$k]["address"] = $res[$k]["address_x"] . $res[$k]["address_y"];
                unset($res[$k]["cid"]);
                unset($res[$k]["enterprise_id"]);
                unset($res[$k]["address_x"]);
                unset($res[$k]["address_y"]);
            }


            $page_data = arr_page($res, $page_num, $page);
            $data["data"] = array(
                "current_page" => $page_data["current_page"],
                "total_page" => $page_data['total_page'],
                "recruitmentList" => $page_data["list"]
            );
            $this->apiReturn($data);
        }
    }

    /**
     * Notes: 职位公司搜索(分页未完成)
     * User: Sen
     * DateTime: 2020/6/28 18:57
     * Return:
     */
    public function doSearch()
    {
//        $dataL = $this->dataL;
//        $resources = new ResourcesController();
//
//        $unionid = $resources->remove_xss($dataL['unionid']);
//        $content = $resources->remove_xss($dataL['content']);//搜索的内容
//        $city_name = $resources->remove_xss($dataL['city_name']);//城市
//        $page = $resources->remove_xss($dataL['page']);//每页条数
//        $page_num = $resources->remove_xss($dataL['page_num']);//页码

//        if (empty($unionid) || empty($content) || empty($city_name)) {
//            $this->apiError("必填参数缺少!");
//        }
//        if (empty($page)) {
//            $page = 8;
//        }
//        if (empty($page)) {
//            $page_num = 1;
//        }

        $nPage     = intval(I('page'));
        $nLimit    = intval(I('page_num'));
        $content   = trim(I('content'));
        $city_name = trim(I('city_name'));

        if($nPage <= 0){
            $page = 8;
        }
        if($nLimit <= 0){
            $page_num = 1;
        }

        //验证用户信息
        $nUnionid = trim(I('unionid'));
        $objUser  = D('User');
        list($nErr , $nUserId) = $objUser->getUserIdByUnionId($nUnionid);
        if($nErr != 1) {
            return $this->apiError($nUserId);
        }

        $where1 = array(
            "address_s" => $city_name,
            "name" => array("like", '%' . $content . '%'),
        );
        //先搜索公司，然后获取公司id
        $company = $this->user_enterpise_model
            ->where($where1)
            ->field("id")
            ->select();

        if ($company) {
//            将公司id方法到一维数组
            $arr = array();
            foreach ($company as $k => $v) {
                $arr[$k] = $v["id"];
            }
            //搜索公司下的职位
            $recruitmentList = $this->recruitment_model
                ->alias("a")
                ->join("left join __USER__ as b on a.uid = b.id ")
                ->join("right join __USER_ENTERPISE__  as c on c.id = a.enterprise_id")
                ->where(array("a.isdel" => 0, "a.is_anxious" => 1, "a.status" => 1, "a.enterprise_id" => array("in", $arr)))
                ->field("a.id,c.name,a.position,a.time ,b.avatar,a.is_anxious,a.address_x,a.address_y,c.id as cid ")
                ->select();
            foreach ($recruitmentList as $k => $v) {
                $positionList = $this->spec_label_model
                    ->alias("s")
                    ->join("LEFT JOIN __SPEC_INFO__ as s1 on s1.id = s.spec_info_id")
                    ->join("__SPEC__ as s2 on s2.id = s1.spec_id")
                    ->where(array("s.object_id" => $v['id'], "s.tablename" => "recruitment"))
                    ->field("s1.spec_info_name,s2.rename")
                    ->select();
                foreach ($positionList as $item => $var) {
                    if ($var['rename'] == "education") {
                        $recruitmentList[$k]["education"] = $var['spec_info_name'];
                    }
                    if ($var['rename'] == "salary") {
                        $recruitmentList[$k]["salary"] = $var['spec_info_name'];
                    }
                }
            }

            foreach ($recruitmentList as $k => $v) {
                $companyList = $this->spec_label_model
                    ->alias("s")
                    ->join("LEFT JOIN __SPEC_INFO__ as s1 on s1.id = s.spec_info_id")
                    ->join("__SPEC__ as s2 on s2.id = s1.spec_id")
                    ->where(array("s.object_id" => $v['cid'], "s.tablename" => "user_enterpise"))
                    ->field("s1.spec_info_name,s2.rename")
                    ->select();

                foreach ($companyList as $item => $var) {
                    if ($var['rename'] == "welfare") {
                        $recruitmentList[$k]["welfare"] = $var['spec_info_name'];
                    }
                }
                $recruitmentList[$k]["address"] = $recruitmentList[$k]["address_x"] . $recruitmentList[$k]["address_y"];
                unset($recruitmentList[$k]["cid"]);
                unset($recruitmentList[$k]["address_x"]);
                unset($recruitmentList[$k]["address_y"]);
            }

            //搜索招聘，合并数据
            $where2 = array(
                "address_x" => $city_name,
                "position" => array("like", '%' . $content . '%'),
            );

            $recruitment = $this->recruitment_model
                ->where($where2)
                ->field("enterprise_id")
                ->select();
            if (empty($recruitment)) {

                $data = arr_page($recruitmentList, $page_num, $page);

                $this->apiReturn(array("data" => $data));
            } else {
                $recruitment_arr = array();
                foreach ($recruitment as $k => $v) {
                    $recruitment_arr[$k] = $v["enterprise_id"];
                }
                $recruitmentLists = $this->recruitment_model
                    ->alias("a")
                    ->join("left join __USER__ as b on a.uid = b.id ")
                    ->join("right join __USER_ENTERPISE__  as c on c.id = a.enterprise_id")
                    ->where(array("a.isdel" => 0, "a.is_anxious" => 1, "a.status" => 1, "a.enterprise_id" => array("in", $recruitment_arr)))
                    ->field("a.id,c.name,a.position,a.time,b.avatar,a.is_anxious,a.address_x,a.address_y,c.id as cid ")
                    ->select();
                foreach ($recruitmentLists as $k => $v) {
                    $positionLists = $this->spec_label_model
                        ->alias("s")
                        ->join("LEFT JOIN __SPEC_INFO__ as s1 on s1.id = s.spec_info_id")
                        ->join("__SPEC__ as s2 on s2.id = s1.spec_id")
                        ->where(array("s.object_id" => $v['id'], "s.tablename" => "recruitment"))
                        ->field("s1.spec_info_name,s2.rename")
                        ->select();
                    foreach ($positionLists as $item => $var) {
                        if ($var['rename'] == "education") {
                            $recruitmentLists[$k]["education"] = $var['spec_info_name'];
                        }
                        if ($var['rename'] == "salary") {
                            $recruitmentLists[$k]["salary"] = $var['spec_info_name'];
                        }
                    }
                }

                foreach ($recruitmentLists as $k => $v) {
                    $companyLists = $this->spec_label_model
                        ->alias("s")
                        ->join("LEFT JOIN __SPEC_INFO__ as s1 on s1.id = s.spec_info_id")
                        ->join("__SPEC__ as s2 on s2.id = s1.spec_id")
                        ->where(array("s.object_id" => $v['cid'], "s.tablename" => "user_enterpise"))
                        ->field("s1.spec_info_name,s2.rename")
                        ->select();

                    foreach ($companyLists as $item => $var) {
                        if ($var['rename'] == "welfare") {
                            $recruitmentLists[$k]["welfare"] = $var['spec_info_name'];
                        }
                    }
                    $recruitmentLists[$k]["address"] = $recruitmentLists[$k]["address_x"] . $recruitmentLists[$k]["address_y"];
                    unset($recruitmentLists[$k]["cid"]);
                    unset($recruitmentLists[$k]["address_x"]);
                    unset($recruitmentLists[$k]["address_y"]);
                }

                $res = array_merge($recruitmentList, $recruitmentLists);
                $data = array_deduplication($res, "id");

                $data = arr_page($data, $page_num, $page);
                $this->apiReturn(array("data" => $data));
            }
            //二维数组去重
        } else {
            //搜索职位
            $where3 = array(
                "address_x" => $city_name,
                "position" => array("like", '%' . $content . '%'),
            );

            $recruitment = $this->recruitment_model
                ->where($where3)
                ->field("enterprise_id")
                ->select();
            if (empty($recruitment)) {
                $this->apiReturn(array("data" => array()));
            } else {
                $recruitment_arr = array();
                foreach ($recruitment as $k => $v) {
                    $recruitment_arr[$k] = $v["enterprise_id"];
                }
                $recruitmentLists = $this->recruitment_model
                    ->alias("a")
                    ->join("left join __USER__ as b on a.uid = b.id ")
                    ->join("right join __USER_ENTERPISE__  as c on c.id = a.enterprise_id")
                    ->where(array("a.isdel" => 0, "a.is_anxious" => 1, "a.status" => 1, "a.enterprise_id" => array("in", $recruitment_arr)))
                    ->field("a.id,c.name,a.position,a.time ,b.avatar,a.is_anxious,a.address_x,a.address_y,c.id as cid ")
                    ->select();
                foreach ($recruitmentLists as $k => $v) {
                    $positionLists = $this->spec_label_model
                        ->alias("s")
                        ->join("LEFT JOIN __SPEC_INFO__ as s1 on s1.id = s.spec_info_id")
                        ->join("__SPEC__ as s2 on s2.id = s1.spec_id")
                        ->where(array("s.object_id" => $v['id'], "s.tablename" => "recruitment"))
                        ->field("s1.spec_info_name,s2.rename")
                        ->select();
                    foreach ($positionLists as $item => $var) {
                        if ($var['rename'] == "education") {
                            $recruitmentLists[$k]["education"] = $var['spec_info_name'];
                        }
                        if ($var['rename'] == "salary") {
                            $recruitmentLists[$k]["salary"] = $var['spec_info_name'];
                        }
                    }
                }

                foreach ($recruitmentLists as $k => $v) {
                    $companyLists = $this->spec_label_model
                        ->alias("s")
                        ->join("LEFT JOIN __SPEC_INFO__ as s1 on s1.id = s.spec_info_id")
                        ->join("__SPEC__ as s2 on s2.id = s1.spec_id")
                        ->where(array("s.object_id" => $v['cid'], "s.tablename" => "user_enterpise"))
                        ->field("s1.spec_info_name,s2.rename")
                        ->select();

                    foreach ($companyLists as $item => $var) {
                        if ($var['rename'] == "welfare") {
                            $recruitmentLists[$k]["welfare"] = $var['spec_info_name'];
                        }
                    }
                    $recruitmentLists[$k]["address"] = $recruitmentLists[$k]["address_x"] . $recruitmentLists[$k]["address_y"];
                    unset($recruitmentLists[$k]["cid"]);
                    unset($recruitmentLists[$k]["address_x"]);
                    unset($recruitmentLists[$k]["address_y"]);
                }

                $data = arr_page($recruitmentLists, $page_num, $page);
                $this->apiReturn(array("data" => $data));
            }
        }


    }

    /**
     * Notes: 首页推荐职位 完成
     * User: Sen
     * DateTime: 2020/6/28 17:40
     * Return:
     */
    public function recruitmentHot()
    {
//        $dataL = $this->dataL;
//        $resources = new ResourcesController();
//
//        $unionid = $resources->remove_xss($dataL['unionid']);
//        $page = $resources->remove_xss($dataL['page']);//每页条数
//        $page_num = $resources->remove_xss($dataL['page_num']);//页码
//
//        if (empty($unionid)) {
//            $this->apiError("非法请求！");
//        }

        $nPage  = intval(I('page'));
        $nLimit = intval(I('page_num'));

        if($nPage <= 0){
            $page = 8;
        }
        if($nLimit <= 0){
            $page_num = 1;
        }

        //验证用户信息
        $nUnionid = trim(I('unionid'));
        $objUser  = D('User');
        list($nErr , $nUserId) = $objUser->getUserIdByUnionId($nUnionid);
        if($nErr != 1){
            return $this->apiError($nUserId);
        }

//        if (empty($page)) {
//            $page = 8;
//        }
//        if (empty($page_num)) {
//            $page_num = 1;
//        }

        $recruitmentList = $this->recruitment_model
            ->alias("a")
            ->join("left join __USER__ as b on a.uid = b.id ")
            ->join("right join __USER_ENTERPISE__  as c on c.id = a.enterprise_id")
            ->where(array("a.isdel" => 0, "a.is_anxious" => 1, "a.status" => 1))
            ->field("a.id,a.uid,c.name,a.position,a.time ,b.avatar,a.is_anxious,a.address_x,a.address_y,c.id as cid ")
            ->select();
        foreach ($recruitmentList as $k => $v) {
            $lableList = $this->spec_label_model
                ->alias("s")
                ->join("LEFT JOIN __SPEC_INFO__ as s1 on s1.id = s.spec_info_id")
                ->join("__SPEC__ as s2 on s2.id = s1.spec_id")
                ->where(array("s.object_id" => $v['id'], "s.tablename" => "recruitment"))
                ->field("s1.spec_info_name,s2.rename")
                ->select();
            foreach ($lableList as $item => $var) {
                if ($var['rename'] == "education") {
                    $recruitmentList[$k]["education"] = $var['spec_info_name'];
                }
                if ($var['rename'] == "salary") {
                    $recruitmentList[$k]["salary"] = $var['spec_info_name'];
                }
            }
        }


        foreach ($recruitmentList as $k => $v) {
            $lableList = $this->spec_label_model
                ->alias("s")
                ->join("LEFT JOIN __SPEC_INFO__ as s1 on s1.id = s.spec_info_id")
                ->join("__SPEC__ as s2 on s2.id = s1.spec_id")
                ->where(array("s.object_id" => $v['cid'], "s.tablename" => "user_enterpise"))
                ->field("s1.spec_info_name,s2.rename")
                ->select();

            foreach ($lableList as $item => $var) {
                if ($var['rename'] == "welfare") {
                    $recruitmentList[$k]["welfare"] = $var['spec_info_name'];
                }
            }
            $recruitmentList[$k]["address"] = $recruitmentList[$k]["address_x"] . $recruitmentList[$k]["address_y"];
            unset($recruitmentList[$k]["cid"]);
            unset($recruitmentList[$k]["address_x"]);
            unset($recruitmentList[$k]["address_y"]);
        }

        $page_data = arr_page($recruitmentList, $page_num, $page);
        $data["data"] = array(
            "current_page" => $page_data["current_page"],
            "total_page" => $page_data['total_page'],
            "recruitmentList" => $page_data["list"]
        );
        $this->apiReturn($data);


    }

    /**
     * Notes: 招聘信息大厅(最新) 完成
     * User: Sen
     * DateTime: 2020/6/23 19:57
     * Return:
     */
    public function recruitment()
    {
//        $dataL = $this->dataL;
//        $resources = new ResourcesController();
//
//        $unionid = $resources->remove_xss($dataL['unionid']);
//        $page = $resources->remove_xss($dataL['page']);//每页条数
//        $page_num = $resources->remove_xss($dataL['page_num']);//页码
//
//        if (empty($unionid)) {
//            $this->apiError("非法请求！");
//        }
//        if (empty($page)) {
//            $page = 8;
//        }
//        if (empty($page_num)) {
//            $page_num = 1;
//        }

        $nPage  = intval(I('page'));
        $nLimit = intval(I('page_num'));

        if($nPage <= 0){
            $page = 8;
        }
        if($nLimit <= 0){
            $page_num = 1;
        }

        //验证用户信息
        $nUnionid = trim(I('unionid'));
        $objUser  = D('User');
        list($nErr , $nUserId) = $objUser->getUserIdByUnionId($nUnionid);
        if($nErr != 1){
            return $this->apiError($nUserId);
        }

        $recruitmentList = $this->recruitment_model
            ->alias("a")
            ->join("left join __USER__ as b on a.uid = b.id ")
            ->join("right join __USER_ENTERPISE__  as c on c.id = a.enterprise_id")
            ->where(array("a.isdel" => 0, "a.status" => 1))
            ->field("a.id,a.uid,c.name,a.position,a.time ,b.avatar,a.is_anxious,a.address_x,a.address_y,c.id as cid ")
            ->order("a.time desc")
            ->select();
        foreach ($recruitmentList as $k => $v) {
            $lableList = $this->spec_label_model
                ->alias("s")
                ->join("LEFT JOIN __SPEC_INFO__ as s1 on s1.id = s.spec_info_id")
                ->join("__SPEC__ as s2 on s2.id = s1.spec_id")
                ->where(array("s.object_id" => $v['id'], "s.tablename" => "recruitment"))
                ->field("s1.spec_info_name,s2.rename")
                ->select();
            foreach ($lableList as $item => $var) {
                if ($var['rename'] == "education") {
                    $recruitmentList[$k]["education"] = $var['spec_info_name'];
                }
                if ($var['rename'] == "salary") {
                    $recruitmentList[$k]["salary"] = $var['spec_info_name'];
                }
            }
        }


        foreach ($recruitmentList as $k => $v) {
            $lableList = $this->spec_label_model
                ->alias("s")
                ->join("LEFT JOIN __SPEC_INFO__ as s1 on s1.id = s.spec_info_id")
                ->join("__SPEC__ as s2 on s2.id = s1.spec_id")
                ->where(array("s.object_id" => $v['cid'], "s.tablename" => "user_enterpise"))
                ->field("s1.spec_info_name,s2.rename")
                ->select();

            foreach ($lableList as $item => $var) {
                if ($var['rename'] == "welfare") {
                    $recruitmentList[$k]["welfare"] = $var['spec_info_name'];
                }
            }
            unset($recruitmentList[$k]["cid"]);
            $recruitmentList[$k]["address"] = $recruitmentList[$k]["address_x"] . $recruitmentList[$k]["address_y"];
        }

        $page_data = arr_page($recruitmentList, $page_num, $page);
        $data["data"] = array(
            "current_page" => $page_data["current_page"],
            "total_page" => $page_data['total_page'],
            "recruitmentList" => $page_data["list"]
        );
        $this->apiReturn($data);

    }

    /**
     * Notes: 求职者可以聊
     * User: Sen
     * DateTime: 2020/7/9 14:52
     * Return:
     */
    public function canChat()
    {
//        $dataL = $this->dataL;
//        $resources = new ResourcesController();
//        $unionid = $resources->remove_xss($dataL['unionid']);
//        $rid = $resources->remove_xss($dataL['rid']);//职位id
//        $pid = $resources->remove_xss($dataL['pid']);//被回复人ID
//
//        if (empty($unionid) || empty($rid) || empty($pid)) {
//            $this->apiError("非法请求！");
//        }
//        $id = $resources->getId($unionid);

        //验证用户信息
        $rid         = intval(I('rid'));
        $pid         = intval(I('pid'));
        $nUnionid    = trim(I('unionid'));
        $room_number = getRandNumber();

        $objUser  = D('User');
        list($nErr , $nUserId) = $objUser->getUserIdByUnionId($nUnionid);
        if($nErr != 1){
            return $this->apiError($nUserId);
        }
        $id = $nUserId;


        //查询招呼

        $where = array(
            "recruitment_id" => $rid,
            "uid" => $id,
            "parent_id" => $pid,
        );
        //查询对方状态是不是可以聊

        $r = $this->job_application_model->where($where)->find();

        if ($r) {
            $this->apiError("招呼用语不能重复发送！");
        }

        //查询求职默认回复
        $content = $this->user_model
            ->alias("a")
            ->join("right join __JOB_GREET__ as b on b.id = a.is_job_select")
            ->where(array("a.id" => $id))
            ->field("content")
            ->find();

        $add = array(
            "recruitment_id" => $rid,
            "uid" => $id,
            "parent_id" => $pid,
            "tablename" => "recruitment",
            "create_time" => date("Y-m-d H:i:s", time()),
            "room_number" => $room_number,
            "type" => 4,
            "content" => $content["content"],
            "is_read" => 0,
        );

        $res = $this->job_application_model->add($add);

        if ($res) {
            $this->apiSuccess("操作成功！");
        } else {
            $this->apiError("操作失败！");
        }

    }

    /**
     * Notes: 求职聊天窗口  (求职)  完成
     * User: Sen
     * DateTime: 2020/6/24 11:45
     * Return:
     */
    public function ChatWindow()
    {
//        $dataL = $this->dataL;
//        $resources = new ResourcesController();
//        $unionid = $resources->remove_xss($dataL['unionid']);
//        $rid = $resources->remove_xss($dataL['rid']);//职位id
//        $pid = $resources->remove_xss($dataL['pid']);//被回复人ID
//
//
//        if (empty($unionid) || empty($rid) || empty($pid)) {
//            $this->apiError("非法请求！");
//        }
//        $uid = $resources->getId($unionid);
        //验证用户信息
        $rid         = intval(I('rid'));
        $pid         = intval(I('pid'));
        $nUnionid    = trim(I('unionid'));

        $objUser  = D('User');
        list($nErr , $nUserId) = $objUser->getUserIdByUnionId($nUnionid);
        if($nErr != 1){
            return $this->apiError($nUserId);
        }
        $uid = $nUserId;

        $data = $this->recruitment_model
            ->alias("a")
            ->join("left join __USER__ as b on a.uid = b.id")
            ->join("right join __USER_ENTERPISE__ as c on c.id = a.enterprise_id")
            ->where(array("a.id" => $rid))
            ->field("a.id,a.uid,a.position,a.position,a.address_x,a.address_y,c.name as cname, c.id as cid ,c.u_name,c.identity ")
            ->find();
        $data["address"] = $data["address_x"] . $data["address_y"];
        unset($data["address_x"]);
        unset($data["address_y"]);
        $lableList = $this->spec_label_model
            ->alias("s")
            ->join("LEFT JOIN __SPEC_INFO__ as s1 on s1.id = s.spec_info_id")
            ->join("LEFT JOIN __SPEC_INDUSTRY__ as s3 on s3.id = s.level")
            ->join("__SPEC__ as s2 on s2.id = s1.spec_id")
            ->where(array("s.object_id" => $rid))
            ->field("s1.spec_info_name,s3.spec_info_name industry_name,s2.rename")
            ->select();
        foreach ($lableList as $item => $var) {
            if ($var['rename'] == "education") {
                $data[$var['rename']] = $var['spec_info_name'];
            }
            if ($var['rename'] == "salary") {
                $data[$var['rename']] = $var['spec_info_name'];
            }
        }

        if (empty($data)) {
            $this->apiError("服务器内部错误！");
        }

        $where1 = array("a.recruitment_id" => $rid, "a.uid" => $pid, "a.parent_id" => $uid, "a.isdel" => 0, "a.tablename" => "recruitment");
        $where2 = array("a.recruitment_id" => $rid, "a.uid" => $uid, "a.parent_id" => $pid, "a.isdel" => 0, "a.tablename" => "recruitment");

        $where['_complex'] = array(
            $where1,
            $where2,
            '_logic' => 'or'
        );
        $chat_content = $this->job_application_model
            ->alias("a")
            ->join("__USER__ as b on a.uid = b.id")
            ->where($where)
            ->field('b.avatar,a.content,a.create_time,a.is_read,a.uid,a.parent_id,a.type,a.room_number')
            ->select();

        $data["sid"] = $uid;
        $data["oid"] = $data["uid"];
        //没有聊天信息时
        if (empty($chat_content)) {
            $data["starting_information"] = date("Y-m-d H:i:s", time()) . "由您发起的沟通";
            $this->apiReturn(array("data" => $data));
        }
        if ($chat_content[0]["uid"] == $uid) {
            $data["starting_information"] = $chat_content[0]["create_time"] . "由您发起的沟通";
        } else {
            $data["starting_information"] = $chat_content[0]["create_time"] . "由对方发起的沟通";
        }
        $data["room_number"] = $chat_content[0]["room_number"];

        $data["chat_content"] = $chat_content;


        $wheres = array("recruitment_id" => $rid, "uid" => $pid, "parent_id" => $uid);
        $res = $this->job_application_model
            ->where($wheres)
            ->save(array("is_read" => 1));

        $this->apiReturn(array("data" => $data));

    }

    /**
     * Notes: 招聘聊天列表  (完成)
     * User: Sen
     * DateTime: 2020/6/24 16:37
     * Return:
     */
    public function recruitmentChatList()
    {
//        $dataL = $this->dataL;
//        $resources = new ResourcesController();
//        $unionid = $resources->remove_xss($dataL['unionid']);
//        if (empty($unionid)) {
//            $this->apiError("非法请求！");
//        }
//        $id = $resources->getId($unionid);//回复id

        $nUnionid = trim(I('unionid'));
        $objUser  = D('User');
        list($nErr , $nUserId) = $objUser->getUserIdByUnionId($nUnionid);
        if($nErr != 1){
            return $this->apiError($nUserId);
        }
        $id = $nUserId;

        $where1 = array("a.uid" => $id, "a.tablename" => "recruitment");
        $where2 = array("a.parent_id" => $id, "a.tablename" => "recruitment");

        $where['_complex'] = array(
            $where1,
            $where2,
            '_logic' => 'or'
        );
        $user_id = $this->job_application_model
            ->alias("a")
            ->join("right join __RECRUITMENT__ as b on b.id = a.recruitment_id")
            ->join("right join __USER_ENTERPISE__ as c on c.id = b.enterprise_id")
            ->where($where)
            ->field("a.create_time,a.uid,a.parent_id,a.recruitment_id,a.content,a.room_number,b.id,b.position,b.address_x,c.name")
            ->select();
        if (empty($user_id)) {
            $this->apiReturn(array("data" => ""));
        }
        $new_chat_list = arraySort($user_id, "create_time", "desc");//根据时间排序，最大的一条在第一
        $new_chat_list = dataGroup($new_chat_list, "room_number");//根据parent_id分组
        $arr = array();
        foreach ($new_chat_list as $k => $v) {
            if ($new_chat_list[$k][0]["uid"] !== $id) {
                $new_chat_list[$k][0]["parent_id"] = $new_chat_list[$k][0]["uid"];
                $new_chat_list[$k][0]["uid"] = $id;
            }
            $arr[$k] = $new_chat_list[$k][0];
        }

        $arr = array_values($arr);
        foreach ($arr as $ke => $val) {
            $avatar = $this->user_model->where(array("id" => $val["parent_id"]))->field("avatar")->find();
            $arr[$ke]["avatar"] = $avatar["avatar"];
        }


        $this->apiReturn(array("data" => $arr));
    }

    /**
     * Notes:创建个人简历 （数据表字段等待完善）
     * User: Sen
     * DateTime: 2020/6/27 20:16
     * Return :
     */
    public function resumeGeneration()
    {
//        $dataL = $this->dataL;
//        $resources = new ResourcesController();
//        $resumUpload = new FileUploadController();
//        $unionid = $resources->remove_xss($_REQUEST['unionid']);
//
//        if (empty($unionid)) {
//            $this->apiError("非法请求！");
//        }
//
//        $uid = $resources->getId($unionid);

        $unionid = I("post.unionid");
        if (empty($unionid)){
            $this->apiError("非法请求！");
        }

        $resume = $_FILES["resume"];

        //判断文件是否存在
        if(count($resume) <= 0){
            return $this->apiError('请上传文件');
        }
        $uid = D('User')->getidByUnionid($unionid);
        //简历存储路径
        $path = "./public/xianyu/resume/";
        $resumeUpload = new FileUploadController();
        $res = $resumeUpload->uploadFileOne($resume, $path);
        if ($res === false) {
            $this->apiError("文件上传失败！");
        }
        $add = array(
            "uid" => $uid,
            "tablename" => "resume",
            "create_time" => date("Y-m-d H:i:s"),
            "url" => $res["path"],
            "size" => $resume["size"],
            "type" => 2,
            "title" => $res["filename"],
        );
        $r = $this->sof_attachment_model->add($add);

        if ($r) {
            $this->apiSuccess("简历上传成功！");
        } else {
            $this->apiSuccess("简历上传失败！");
        }

    }

    /**
     * Notes: 个人简历列表
     * User: Sen
     * DateTime: 2020/6/27 22:56
     * Return :
     */
    public function myResume()
    {
//        $dataL = $this->dataL;
//        $resources = new ResourcesController();
//        $unionid = $resources->remove_xss($dataL['unionid']);
//
//        if (empty($unionid)) {
//            $this->apiError("非法请求！");
//        }
//
//        $uid = $resources->getId($unionid);

        $nUnionid = trim(I('unionid'));
        $objUser  = D('User');
        list($nErr , $nUserId) = $objUser->getUserIdByUnionId($nUnionid);
        if($nErr != 1){
            return $this->apiError($nUserId);
        }

        $uid    = $nUserId;
        $resume = $this->sof_attachment_model
            ->where(array("uid" => $uid, "tablename" => "resume"))
            ->field('id, uid, create_time, url,title,type')
            ->select();

        if (empty($resume)) {
            $this->apiError("当前简历没有创建！");
        }
//        foreach ($resume as $k => $v) {
//            $resume[$k]["url"] = C('DOMAIN_NAME') . $v["url"];
//        }

        $this->apiReturn(array("data" => $resume));
    }

    /**
     * Notes: 简历投递
     * User: Sen
     * DateTime: 2020/6/27 22:44
     * Return :
     */
    public function resumeDelivery()
    {
//        $dataL = $this->dataL;
//        $resources = new ResourcesController();
//        $unionid = $resources->remove_xss($dataL['unionid']);
//        $rid = $resources->remove_xss($dataL['rid']);//岗位id
//        $pid = $resources->remove_xss($dataL['pid']);//岗位发起人ID
//        $mid = $resources->remove_xss($dataL['mid']);//简历id
//
//
//        if (empty($unionid) || empty($rid) || empty($pid) || empty($mid)) {
//            $this->apiError("非法请求！");
//        }
//
//        $uid = $resources->getId($unionid);

        $rid         = intval(I('rid'));
        $pid         = intval(I('pid'));
        $mid         = intval(I('mid'));
        $nUnionid    = trim(I('unionid'));

        $objUser  = D('User');
        list($nErr , $nUserId) = $objUser->getUserIdByUnionId($nUnionid);
        if($nErr != 1){
            return $this->apiError($nUserId);
        }
        $uid      = $nUserId;

        //投递之前查询是否投递，是否已读（已读可以重复发送）
        $job_send = $this->job_send_model
            ->where(array("recruitment_id" => $rid, "parent_id" => $pid, "uid" => $uid))
            ->order("create_time desc")
            ->find();
        if ($job_send) {//已投递
            if ($job_send["is_read"] == 0) {//未读
                $this->apiError("该企业未接收简历，不能重复投递");
            } else {//已读
                $resume = $this->sof_attachment_model
                    ->where(array("id" => $mid))
                    ->field("id,url")
                    ->find();

                $add = array(
                    "recruitment_id" => $rid,
                    "resume_url" => $resume["url"],
                    "uid" => $uid,
                    "create_time" => date("Y-m-d H:i:s", time()),
                    "parent_id" => $pid,
                    "is_read" => 0,
                    "tablename" => "recruitment",
                );

                $res = $this->job_send_model->add($add);

                if ($res) {
                    $this->apiSuccess("简历投递成功!");
                } else {
                    $this->apiError("简历投递失败！");
                }
            }
        } else {//未投递
            $resume = $this->sof_attachment_model
                ->where(array("id" => $mid))
                ->field("id,url")
                ->find();

            $add = array(
                "recruitment_id" => $rid,//岗位id
                "resume_url" => $resume["url"],
                "uid" => $uid,
                "create_time" => date("Y-m-d H:i:s", time()),
                "parent_id" => $pid,
                "is_read" => 0,
                "tablename" => "recruitment",
            );

            $res = $this->job_send_model->add($add);

            if ($res) {
                $this->apiSuccess("简历投递成功!");
            } else {
                $this->apiError("简历投递失败！");
            }

        }
    }

    /**
     * Notes: 我的投递
     * User: Sen
     * DateTime: 2020/6/28 18:31
     * Return:
     */
    public function myDelivery()
    {
//        $dataL = $this->dataL;
//        $resources = new ResourcesController();
//        $unionid = $resources->remove_xss($dataL['unionid']);
//        $page = $resources->remove_xss($dataL['page']);//每页条数
//        $page_num = $resources->remove_xss($dataL['page_num']);//页码
//        if (empty($unionid)) {
//            $this->apiError("非法请求！");
//        }
//
//        if (empty($page_num)) {
//            $page_num = 1;
//        }
//        if (empty($page)) {
//            $page = 8;
//        }
//
//        $uid = $resources->getId($unionid);

        $nPage  = intval(I('page'));
        $nLimit = intval(I('page_num'));

        if($nPage <= 0){
            $page = 8;
        }
        if($nLimit <= 0){
            $page_num = 1;
        }

        //验证用户信息
        $nUnionid = trim(I('unionid'));
        $objUser  = D('User');
        list($nErr , $nUserId) = $objUser->getUserIdByUnionId($nUnionid);
        if($nErr != 1){
            return $this->apiError($nUserId);
        }
        $uid = $nUserId;

        $num = $this->job_send_model
            ->alias("a")
            ->where(array("a.uid" => $uid, "a.tablename" => "recruitment"))
            ->join("left join __RECRUITMENT__ as b on a.recruitment_id = b.id")
            ->join("left join __USER__ as c on b.uid = c.id ")
            ->join("right join __USER_ENTERPISE__ as d on d.id = b.enterprise_id")
            ->count();

        $pagination = page($num, $page, $page_num);

        $myDeliveryList = $this->job_send_model
            ->alias("a")
            ->where(array("a.uid" => $uid, "a.tablename" => "recruitment"))
            ->join("left join __RECRUITMENT__ as b on a.recruitment_id = b.id")
            ->join("left join __USER__ as c on b.uid = c.id ")
            ->join("right join __USER_ENTERPISE__ as d on d.id = b.enterprise_id")
            ->limit($pagination['start_num'], $pagination["num"])
            ->field("b.id,a.create_time,d.name as dname ,b.position,b.address_x,b.ishot,c.name,c.avatar")
            ->select();
        $total_page = $pagination["total_page"] == 0 ? $pagination["total_page"] = 1 : $pagination["total_page"];
        $data["data"] = array(
            "current_page" => $pagination["current_page"],
            "total_page" => $total_page,
            "myDeliveryList" => $myDeliveryList
        );
        $this->apiReturn($data);

    }


}