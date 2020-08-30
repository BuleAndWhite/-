<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Author: XiaoYuan <767684610@qq.com>
// +----------------------------------------------------------------------
/**
 * 访问入口
 * 创建日期：2017-04-24
 */

namespace Api\Controller;

use Think\Controller;
use Api\Controller\ResourcesController;


/**
 * 此类为招聘专用类
 * Class RecruitmentController
 * @package Api\Controller
 */
class RecruitmentController extends AppController
{
    protected $user_model;//用户
    protected $spec_model;//标签表
    protected $spec_info_model;//标签分类
    protected $spec_label_model;//标签关系表
    protected $spec_industry_model;//标签关系表
    protected $user_enterpise_model;//公司表
    protected $recruitment_model;//招聘表
    protected $job_greet_model;//默认信息
    protected $spec_select_position_model;//职位选择表
    protected $job_application_model;//求职岗位留言
    protected $sof_attachment_model;//附件表
    protected $job_send_model;//简历投递
    protected $access_model;//访问表
    protected $subscription_model;//简历投递
    protected $resume_education_model;//教育
    protected $resume_expect_model;//求职期望
    protected $user_group_model;//人才分组
    protected $user_join_group_model;//人才分组关系表


    function __construct()
    {
        header("Access-Control-Allow-Origin: *");
        header("Content-Type: text/html; charset=UTF-8");
        parent::_initialize();
        $this->user_model = M('User');
        $this->spec_model = M('Spec');
        $this->spec_info_model = M('SpecInfo');
        $this->spec_label_model = M('SpecLabel');
        $this->spec_industry_model = M("SpecIndustry");
        $this->user_enterpise_model = M("UserEnterpise");
        $this->recruitment_model = M('Recruitment');
        $this->job_greet_model = M('JobGreet');
        $this->spec_select_position_model = M("SpecSelectPosition");
        $this->job_application_model = M('JobApplication');
        $this->sof_attachment_model = M('SofAttachment');
        $this->job_send_model = M('JobSend');
        $this->access_model = M('Access');
        $this->subscription_model = M('Subscription');
        $this->resume_education_model = M('ResumeEducation');
        $this->resume_expect_model = M('ResumeExpect');
        $this->user_group_model = M('UserGroup');
        $this->user_join_group_model = M('UserJoinGroup');
    }

    /***
     * Notes:测试
     * Created by
     * User: Belief
     * DateTime: 2020-07-03 0:05
     * Return : array() 招聘职位的信息加标签
     */
    public function index()
    {
        $reOne = $this->recruitment_model->where(array("id" => 1))->find();
        $lableList = $this->spec_label_model
            ->alias("s")
            ->join("LEFT JOIN __SPEC_INFO__ as s1 on s1.id = s.spec_info_id")
            ->join("LEFT JOIN __SPEC_INDUSTRY__ as s3 on s3.id = s.level")
            ->join("__SPEC__ as s2 on s2.id = s1.spec_id")
            ->where(array("s.object_id" => $reOne['id']))
            ->field("s1.spec_info_name,s3.spec_info_name industry_name,s2.rename")
            ->select();
        foreach ($lableList as $item => $var) {
            $reOne[$var['rename']] = $var['spec_info_name'];
        }
        $this->ajaxReturn($reOne);

    }

    /**
     * Notes: 分类下标签详情（招聘） 已完成
     * User: Sen
     * DateTime: 2020/6/29 14:32
     * Return:
     */
    public function classificationDetails()
    {
        $dataL = $this->dataL;
        $resources = new ResourcesController();
        $unionid = $resources->remove_xss($dataL['unionid']);
        $id = $resources->remove_xss($dataL['id']); //分类id
        if (empty($unionid)) {
            $this->apiError("非法请求！");
        }

        $data = $this->spec_info_model
            ->where(array("spec_id" => $id))
            ->select();
        if ($id == 4) {
            foreach ($data as $k => $v) {
                $data[$k]["List"] = $this->spec_industry_model
                    ->where(array("spec_info_id" => $v["id"]))
                    ->field("id as tid, spec_info_id as tspen_info_id, spec_info_name as tspec_info_name")
                    ->select();
            }
        }

        $this->apiReturn(array("data" => $data));


    }

    /**
     * Notes: 添加公司 已完成
     * User: Sen
     * DateTime: 2020/7/1 17:34
     * Return:
     */
    public function companyAdd()
    {
        $dataL = $this->dataL;
        $resources = new ResourcesController();

        $unionid = $resources->remove_xss($dataL['unionid']);
        $name = $resources->remove_xss($dataL['name']);//公司名
        $u_name = $resources->remove_xss($dataL['u_name']);//添加公司的人员名字
        $identity = $resources->remove_xss($dataL['identity']);//公司名
        $address_s = $resources->remove_xss($dataL['address_s']);//手动地址
        $address_z = $resources->remove_xss($dataL['address_z']);//定位地址
        $latitude_x = $resources->remove_xss($dataL['latitude_x']);//纬度X轴
        $latitude_y = $resources->remove_xss($dataL['latitude_y']);//纬度Y轴
        $introduction = $resources->remove_xss($dataL['introduction']);//公司介绍
        $file_path = $dataL["file_path"]; //公司图片（数组）
        $scale_id = $resources->remove_xss($dataL["scale_id"]); //公司规模id
        $financing_id = $resources->remove_xss($dataL["financing_id"]); //公司福利id
        $welfare_id = $resources->remove_xss($dataL["welfare_id"]); //融资阶段id
        $attendance_id = $resources->remove_xss($dataL["attendance_id"]); //考勤时间id

        $first_industry_id = $resources->remove_xss($dataL["first_industry_id"]); //行业 二级id
        $second_industry_id = $resources->remove_xss($dataL["second_industry_id"]); //行业 三级id

        $list = array($scale_id, $financing_id, $welfare_id, $attendance_id);
        $User = D('User');
        $uid = $User->getIdByUnionid($unionid);
        //公司唯一
        $user_enterpise = $this->user_enterpise_model->where(array("uid" => $uid))->find();

        if ($user_enterpise) {
            $this->apiError("您已绑定公司，请勿重复绑定！");
        }
        if (empty($unionid)
            || empty($name)
            || empty($u_name)
            || empty($identity)
            || empty($address_s)
            || empty($address_z)
            || empty($latitude_x)
            || empty($latitude_y)
            || empty($introduction)
            || empty($first_industry_id)
            || empty($second_industry_id)
            || empty($scale_id)
            || empty($financing_id)
            || empty($welfare_id)
            || empty($attendance_id)
        ) {
            $this->apiError("非法请求！");
        }

        $add = array(
            "uid" => $uid,
            "name" => $name,
            "u_name" => $u_name,
            "identity" => $identity,
            "address_s" => $address_s,
            "address_z" => $address_z,
            "latitude_x" => $latitude_x,
            "latitude_y" => $latitude_y,
            "introduction" => $introduction,
            "time" => date("Y-m-d H:i:s", time())
        );
        //添加公司信息
        $id = $this->user_enterpise_model->add($add);
        if (empty($id)) {
            $this->apiError("未知错误！");
        }

        //添加公司标签
        $spec_label_add = array();
        foreach ($list as $k => $v) {
            $spec_label_add[$k]["object_id"] = $id;
            $spec_label_add[$k]["spec_info_id"] = $v;
            $spec_label_add[$k]["tablename"] = "user_enterpise";
        }
        $res_sp = $this->spec_label_model->addAll($spec_label_add);

        //公司添加行业
        $add_ind = array(
            "object_id" => $id,
            "spec_info_id" => $first_industry_id,
            "level" => $second_industry_id,
            "tablename" => "user_enterpise"
        );
        $res_ind = $this->spec_label_model->add($add_ind);
        if ($file_path) {
            //添加公司附件（图片）直接获取文件路径
            $sof_attachment_add = array();
            foreach ($file_path as $k => $val) {
                $sof_attachment_add[$k]["object_id"] = $id;
                $sof_attachment_add[$k]["tablename"] = "user_enterpise";
                $sof_attachment_add[$k]["uid"] = $uid;
                $sof_attachment_add[$k]["create_time"] = date("Y-m-d H:i:s", time());
                $sof_attachment_add[$k]["url"] = $val["path"];
                $sof_attachment_add[$k]["size"] = $val["size"];
                $sof_attachment_add[$k]["type"] = $val["type"];
            }


            $res_sof = $this->sof_attachment_model->addAll($sof_attachment_add);
            if (empty($res_sp) || empty($res_ind) || empty($res_sof)) {
                $this->apiError("非法错误！");
            }
        } else {
            if (empty($res_sp || $res_ind)) {
                $this->apiError("非法错误！");
            }
        }

        $this->apiSuccess("公司添加成功");

    }

    /**
     * Notes: 公司详情 已完成
     * User: Sen
     * DateTime: 2020/7/2 18:47
     * Return:
     */
    public function companyDetails()
    {
        $dataL = $this->dataL;
        $resources = new ResourcesController();
        $unionid = $resources->remove_xss($dataL['unionid']);
        $id = $resources->remove_xss($dataL['id']);
        if (empty($unionid)) {
            $this->apiError("非法请求");
        }

        $data = $this->user_enterpise_model
            ->where(array("id" => $id))
            ->field("id,uid,name,u_name,identity,address_s,address_z,latitude_x,latitude_y,introduction")
            ->find();

        if (empty($data)) {
            $this->apiError("未知错误！");
        }
        //公司标签
        $lableList = $this->spec_label_model
            ->alias("s")
            ->join("__SPEC_INFO__ as s1 on s1.id = s.spec_info_id")
            ->join("__SPEC__ as s2 on s2.id = s1.spec_id")
            ->where(array("s.object_id" => $id))
            ->field("s1.spec_info_name,s2.rename")
            ->select();
        foreach ($lableList as $item => $var) {
            $data[$var['rename']] = $var['spec_info_name'];
        }
        //公司图片
        $data["imgList"] = $this->sof_attachment_model
            ->where(array("object_id" => $id, "tablename" => "user_enterpise"))
            ->field("url")
            ->select();
        foreach ($data["imgList"] as $k => $v) {
            $data["imgList"][$k]["url"] = C('DOMAIN_NAME') . $v["url"];
        }

        $this->apiReturn(array("data" => $data));


    }

    /**
     * Notes: 招聘信息发布 已完成
     * User: Sen
     * DateTime: 2020/6/23 16:15
     * Return: 0：失败 1：成功
     */
    public function recruitmentRelease()
    {
        $dataL = $this->dataL;
        $resources = new ResourcesController();
        $unionid = $resources->remove_xss($dataL['unionid']);
        $User = D('User');
        $uid = $User->getIdByUnionid($unionid);
        //判断是否绑定公司
        $enterprise_id = $resources->remove_xss($dataL['enterprise_id']);//公司id
        if (empty($enterprise_id)) {
            $this->apiError("未绑定公司，无法发布职位招聘！");
        }

        $content = $resources->remove_xss($dataL['content']);//职位描述
        $address_x = $resources->remove_xss($dataL['address_x']);//城市
        $address_y = $resources->remove_xss($dataL['address_y']);//地区
        $mailbox = $resources->remove_xss($dataL['mailbox']);//简历接收邮箱
        $is_anxious = $resources->remove_xss($dataL['is_anxious']);//是否急招


        $experience_id = $resources->remove_xss($dataL['experience_id']);//工作经验id
        $education_id = $resources->remove_xss($dataL['education_id']);//学历id
        $salary_id = $resources->remove_xss($dataL['salary_id']);//薪资待遇id

        $position = $resources->remove_xss($dataL['position']);//职位名
        $position_id = $resources->remove_xss($dataL['position_id']);//职位三级id


        $lable = array($experience_id, $education_id, $salary_id);
//        $address = $address_x . "," . $address_y;
        $time = date("Y-m-d H:i:s", time());//发布时间

//        if (empty($unionid)
//            || empty($enterprise_id)
//            || empty($position)
//            || empty($content)
//            || empty($address_x)
//            || empty($address_y)
//            || empty($mailbox)
//            || empty($experience_id)
//            || empty($education_id)
//            || empty($salary_id)
//            || empty($position_id)
//        ) {
//            $this->apiError("非法请求！");
//        }


        $is_anxious == 0 ? $is_anxious = 0 : $is_anxious = 1;
        $add = array(
            "enterprise_id" => $enterprise_id,
            "position" => $position,
            "content" => $content,
            "uid" => $uid,
            "address_x" => $address_x,
            "address_y" => $address_y,
            "is_anxious" => $is_anxious,
            "mailbox" => $mailbox,
            "time" => $time,
        );
        //添加职位
        $id = $this->recruitment_model->add($add);

        if (empty($id)) {
            $this->apiError("未知错误！");
        }

        //获取公司的行业id填充到简历里面
        $industry = $this->spec_label_model
            ->where(array("object_id" => $enterprise_id, "tablename" => "user_enterpise", "level" => array("neq", "")))
            ->find();


        //行业数据
        $spec_label_industry = array(
            "object_id" => $id,
            "spec_info_id" => $industry["spec_info_id"],
            "level" => $industry["level"],
            "tablename" => "recruitment"
        );
        //添加职位数据
        $pos = array(
            "object_id" => $id,
            "position_id" => $position_id,
            "tablename" => "recruitment"
        );
        //标签数据
        foreach ($lable as $k => $v) {
            $spec_label_add[$k]["object_id"] = $id;
            $spec_label_add[$k]["spec_info_id"] = $v;
            $spec_label_add[$k]["tablename"] = "recruitment";
        }
        //添加职位标签（经验，工作经验，薪资待遇）
        $res_sp = $this->spec_label_model->addAll($spec_label_add);
        //给招聘添加行业
        $res_in = $this->spec_label_model->add($spec_label_industry);
        //给招聘添加职位
        $res_pos = $this->spec_select_position_model->add($pos);
        if ($res_sp && $res_in && $res_pos) {
            $this->apiSuccess("简历发布成功");
        } else {
            $this->apiError("简历发布失败");
        }
    }

//    /**
//     * Notes: 根据发布的职位推荐人才(工作经验没有返回)
//     * User: Sen
//     * DateTime: 2020/7/11 13:59
//     * Return:
//     */
//    public function recommendTalent()
//    {
//        $dataL = $this->dataL;
//        $resources = new ResourcesController();
//        $unionid = $resources->remove_xss($dataL['unionid']);
//        $page = $resources->remove_xss($dataL['page']);//每页条数
//        $page_num = $resources->remove_xss($dataL['page_num']);//页码
//
//        if (empty($unionid)) {
//            $this->apiError("非法请求！");
//        }
//        if (empty($page_num)) {
//            $page_num = 1;
//        }
//        if (empty($page)) {
//            $page = 8;
//        }
//        $id = $resources->getId($unionid);
//        //获取发布的职位
//        $where = array(
//            "a.uid" => $id,
//            "a.status" => 1,
//            "a.isdel" => 0,
//            "e.isdel" => 0,
//            "g.isdel" => 0,
//            "b.tablename" => "recruitment",
//            "d.tablename" => "resume_expect",
//            "h.tablename" => "resume_education",
//        );
//        $res = $this->recruitment_model
//            ->alias("a")
//            ->join("right join __SPEC_SELECT_POSITION__ as b on b.object_id = a.id")
//            ->join("right join __SPEC_POSITION__ as c on c.id = b.position_id")
//            ->join("right join __SPEC_SELECT_POSITION__ as d on d.position_id = b.position_id")
//            ->join("right join __RESUME_EXPECT__ as e on e.id = d.object_id")
//            ->join("right join __USER__ as f on f.id = e.uid")
//            ->join("right join __RESUME_EDUCATION__ as g on g.uid = f.id")
//            ->join("right join __SPEC_LABEL__ as h on h.object_id = g.id")
//            ->join("right join __SPEC_INFO__ as i on i.id = h.spec_info_id")
//            ->field("f.id,f.avatar,c.name,i.spec_info_name")
//            ->where($where)
//            ->order("i.id desc")
//            ->select();
//
//        $data = $resources->dataGroup($res, "id");
//        foreach ($data as $k => $v) {
//            $data[$k] = $data[$k][0];
//        }
//        $data = array_values($data);
//        $data = $resources->arr_page($data,$page_num,$page);
//        $this->apiReturn(array("data" => $data));
//    }

    /**
     * Notes: 我的招聘列表 已完成
     * 。
     * User: Sen
     * DateTime: 2020/6/24 9:41
     * Return:
     */
    public function myRecruitment()
    {
        $dataL = $this->dataL;
        $resources = new ResourcesController();
        $unionid = $resources->remove_xss($dataL['unionid']);
        $page = $resources->remove_xss($dataL['page']);//每页条数
        $page_num = $resources->remove_xss($dataL['page_num']);//页码

        if (empty($unionid)) {
            $this->apiError("非法请求！");
        }
        if (empty($page_num)) {
            $page_num = 1;
        }
        if (empty($page)) {
            $page = 8;
        }

        $User = D('User');
        $uid = $User->getIdByUnionid($unionid);

        $num = $this->recruitment_model
            ->where(array("uid" => $uid))
            ->count();

        $pagination = $resources->page($num, $page, $page_num);
        $recruitmentList = $this->recruitment_model
            ->where(array("uid" => $uid))
            ->limit($pagination['start_num'], $pagination["num"])
            ->select();

        $total_page = $pagination["total_page"] == 0 ? $pagination["total_page"] = 1 : $pagination["total_page"];

        $data["data"] = array(
            "current_page" => $pagination["current_page"],
            "total_page" => $total_page,
            "recruitmentList" => $recruitmentList
        );
        $this->apiReturn($data);
    }

    /**
     * Notes: 招聘信息详情 已完成
     * User: Sen
     * DateTime: 2020/6/24 11:02
     * Return:
     */
    public function recruitmentDetails()
    {
        $dataL = $this->dataL;
        $resources = new ResourcesController();
        $unionid = $resources->remove_xss($dataL['unionid']);
        $id = $resources->remove_xss($dataL['id']);//职位id
        $User = D('User');
        $uid = $User->getIdByUnionid($unionid);
        if (empty($unionid) || empty($id)) {
            $this->apiError("非法请求！");
        }

        $a = $this->access_model
            ->where(array("uid" => $uid, "object_id" => $id, "tablename" => "recruitment", "isdel" => 0))
            ->find();
        if (empty($a)) {
            $add = array(
                "uid" => $uid,
                "object_id" => $id,
                "tablename" => "recruitment",
                "isdel" => 0,
                "time" => date("Y-m-d H:i:s", time())
            );
            $this->access_model->add($add);
        }

        $recruitmentList = $this->recruitment_model
            ->alias("a")
            ->join("left join __USER__ as b on a.uid = b.id ")
            ->join("right join __USER_ENTERPISE__ as c on c.id = a.enterprise_id")
            ->where(array("a.isdel" => 0, "a.id" => $id))
            ->order("a.ishot desc")
            ->field("a.id,a.uid,a.position,a.content,a.address_x,a.address_y,a.is_anxious,a.time,b.avatar,c.name as cname,c.identity,c.latitude_x,c.latitude_y,c.u_name,c.id as cid")
            ->find();
        //公司福利

        $inList = $this->spec_label_model
            ->alias("s")
            ->join("LEFT JOIN __SPEC_INFO__ as s1 on s1.id = s.spec_info_id")
            ->join("LEFT JOIN __SPEC_INDUSTRY__ as s3 on s3.id = s.level")
            ->join("__SPEC__ as s2 on s2.id = s1.spec_id")
            ->where(array("s.object_id" => $recruitmentList["cid"], "tablename" => "user_enterpise"))
            ->field("s1.spec_info_name,s3.spec_info_name industry_name,s2.rename")
            ->select();
        $recruitmentLists = array();
        foreach ($inList as $item => $var) {
            $recruitmentLists[$var['rename']] = $var['spec_info_name'];
        }

        $lableList = $this->spec_label_model
            ->alias("s")
            ->join("LEFT JOIN __SPEC_INFO__ as s1 on s1.id = s.spec_info_id")
            ->join("LEFT JOIN __SPEC_INDUSTRY__ as s3 on s3.id = s.level")
            ->join("__SPEC__ as s2 on s2.id = s1.spec_id")
            ->where(array("s.object_id" => $id))
            ->field("s1.spec_info_name,s3.spec_info_name industry_name,s2.rename")
            ->select();
        foreach ($lableList as $item => $var) {
            $recruitmentList[$var['rename']] = $var['spec_info_name'];

        }
        $recruitmentList["welfare"] = $recruitmentLists["welfare"];

        $this->apiReturn(array("data" => $recruitmentList));

    }

    /**
     * Notes: 招聘聊天窗口 完成
     * User: Sen
     * DateTime: 2020/6/24 11:45
     * Return:
     */
    public function ChatWindow()
    {
        $dataL = $this->dataL;
        $resources = new ResourcesController();
        $unionid = $resources->remove_xss($dataL['unionid']);
        $rid = $resources->remove_xss($dataL['rid']);//职位id
        $pid = $resources->remove_xss($dataL['pid']);//被回复人ID


        if (empty($unionid) || empty($rid) || empty($pid)) {
            $this->apiError("非法请求！");
        }
        $User = D('User');
        $uid = $User->getIdByUnionid($unionid);

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
        $data["oid"] = $pid;
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
     * Notes:招聘聊天(发信息) 完成
     * User: Sen
     * DateTime: 2020/6/24 14:11
     * Return:
     */
    public function Chat()
    {
        $dataL = $this->dataL;
        $resources = new ResourcesController();
        $unionid = $resources->remove_xss($dataL['unionid']);
        $rid = $resources->remove_xss($dataL['rid']);//职位id
        $pid = $resources->remove_xss($dataL['pid']);//被回复id
        $room_number = $resources->remove_xss($dataL['room_number']);//聊天室编号
        $content = $resources->remove_xss($dataL['content']);//回复内容
        if (empty($unionid) || empty($rid) || empty($pid)) {
            $this->apiError("非法请求！");
        }
        if (empty($content)) {
            $this->apiError("请勿发送空内容！");
        }
        $User = D('User');
        $uid = $User->getIdByUnionid($unionid);//回复人id
        if (empty($room_number)) {
            $room_number = $resources->getRandNumber();
        }

        $add = array(
            "recruitment_id" => $rid,
            "uid" => $uid,
            "parent_id" => $pid,
            "content" => $content,
            "is_read" => 0,
            "create_time" => date("Y-m-d H:i:s", time()),
            "room_number" => $room_number,
            "tablename" => "recruitment",
        );

        $res = $this->job_application_model->add($add);

        if ($res !== false) {
            $this->apiSuccess("发送成功！");
        } else {
            $this->apiError("发送失败！");
        }
    }

    /**
     * Notes: 招聘聊天列表
     * User: Sen
     * DateTime: 2020/6/24 16:37
     * Return:
     */
    public function recruitmentChatList()
    {
        $dataL = $this->dataL;
        $resources = new ResourcesController();
        $unionid = $resources->remove_xss($dataL['unionid']);
        if (empty($unionid)) {
            $this->apiError("非法请求！");
        }
        $id = $resources->getId($unionid);//回复id

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
        $new_chat_list = $resources->arraySort($user_id, "create_time", "desc");//根据时间排序，最大的一条在第一
        $new_chat_list = $resources->dataGroup($new_chat_list, "room_number");//根据parent_id分组
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
     * Notes: 接收简历
     * User: Sen
     * DateTime: 2020/6/28 11:45
     * Return:
     */
    public function resumeReceive()
    {
        $dataL = $this->dataL;
        $resources = new ResourcesController();
        $unionid = $resources->remove_xss($dataL['unionid']);
        $uid = $resources->remove_xss($dataL['uid']);//求职者ID

        $id = $resources->getId($unionid);//bossID

        $res = $this->job_send_model
            ->alias('a')
            ->join("right join __RECRUITMENT__ as b on b.id = a.recruitment_id")
            ->join("right join __USER__ as c on c.id =a.uid")
            ->where(array("a.uid" => $uid, "a.parent_id" => $id, "a.is_read" => 0))
            ->field("a.resume_url,b.mailbox,c.name")
            ->find();


        if ($res) {

            $mail = $res["mailbox"];
            $title = "简历邮件";
            $content = "求职者简历";
            $file_path = "." . $res["resume_url"];
            $file_name = $res["name"] . "." . substr($res["resume_url"], strripos($res["resume_url"], ".") + 1);

            $r = sp_send_email($mail, $title, $content, $file_path, $file_name);
            if ($r) {
                $res_save = $this->job_send_model->where(array("uid" => $uid, "parent_id" => $id, "is_read" => 0))->save(array("is_read" => 1));
                if ($res_save !== false) {
                    $this->apiSuccess("简历接收成功，请前往接收的邮箱查看!");
                } else {
                    $this->apiError("简历接收失败，请重新接收！");
                }
            } else {
                $this->apiError("邮件接收失败！");
            }
        } else {
            $ress = $this->job_send_model->where(array("uid" => $uid, "parent_id" => $id, "is_read" => 1))->find();
            if ($ress) {
                $this->apiError("该简历已接收，请勿重复接收！");
            } else {
                $this->apiError("服务器内部错误！");
            }
        }


    }

    /**
     * Notes: 接收到的简历列表
     * User: Sen
     * DateTime: 2020/7/10 13:56
     * Return:
     */
    public function resumeReceiveList()
    {
        $dataL = $this->dataL;
        $resources = new ResourcesController();
        $unionid = $resources->remove_xss($dataL['unionid']);
        $User = D('User');
        $uid = $User->getIdByUnionid($unionid);
        //根据uid获取我接受的简历
        $where = array(
            "a.parent_id" => $uid,
            "a.tablename" => "recruitment",
        );
        $res = $this->job_send_model
            ->alias("a")
            ->join("right join __USER__ as b on b.id = a.uid")
            ->join("right join __RECRUITMENT__ as c on c.id = a.recruitment_id")
            ->where($where)
            ->field("a.id,b.id as uid,b.name,b.avatar,a.resume_url,a.create_time,c.position,a.is_read")
            ->select();

        foreach ($res as $k => $v) {
            $res[$k]["resume_url"] = C('DOMAIN_NAME') . $v["resume_url"];
        }

        $this->apiReturn(array("data" => $res));

    }

    /**
     * Notes: 职位访客
     * User: Sen
     * DateTime: 2020/7/9 20:00
     * Return:
     */
    public function jobVisitors()
    {
        $resources = new ResourcesController();
        $unionid = I('unionid');
        $page = I('page');//每页条数
        $page_num = I('page_num');//页码

        if (empty($unionid)) {
            $this->apiError("非法请求！");
        }

        if (empty($page)) {
            $page = 1;
        }
        if (empty($page_num)) {
            $page_num = 8;
        }

        $User = D('User');
        $uid = $User->getIdByUnionid($unionid);

        $where = array(
            "r.status" => 1,
            "r.uid" => $uid
        );
        $count =M('Recruitment')->alias("r")->where($where)->count();
        $res = $this->recruitment_model
            ->alias("r")
            ->join("right join __ACCESS__ as a on a.object_id = r.id")
            ->join("right join __USER__ as u on u.id = a.uid")
            ->where($where)
            ->order("a.time desc")
            ->field("u.id uid,  u.name,u.avatar,r.id as rid,r.position,a.time")
            ->page($page, $page_num)
            ->select();
        $total_page = $count % $page_num == 0 ? ($count / $page_num) : ceil($count / $page_num);
        $returnList["data"] = array(
            "total" => $count,
            "current_page" => $page,
            "total_page" => $total_page,
            "list" => $res
        );
        $this->apiReturn(array("data" => $returnList));

    }

    /**
     * Notes:人才定位行业列表
     * User: Sen
     * DateTime: 2020/7/11 10:52
     * Return:
     */
    public function industryList()
    {
        $dataL = $this->dataL;
        $resources = new ResourcesController();
        $unionid = $resources->remove_xss($dataL['unionid']);
        if (empty($unionid)) {
            $this->apiError("非法请求！");
        }
        $res = $this->spec_info_model
            ->where(array("spec_id" => 4))
            ->field("id,spec_info_name")
            ->select();
        foreach ($res as $k => $v) {
            $res[$k]["List"] = $this->spec_industry_model
                ->where(array("spec_info_id" => $v["id"]))
                ->field("id,spec_info_name")
                ->select();
        }
        $this->apiReturn(array("data" => $res));

    }

    /**
     * Notes: 人才定位
     * User: Sen
     * DateTime: 2020/7/11 10:41
     * Return:
     */
    public function talentPositioning()
    {
        $dataL = $this->dataL;
        $resources = new ResourcesController();
        $unionid = $resources->remove_xss($dataL['unionid']);
        $page = $resources->remove_xss($dataL['page']);//每页条数
        $page_num = $resources->remove_xss($dataL['page_num']);//页码
        $industry_id = $resources->remove_xss($dataL['industry_id']);//行业id

        if (empty($unionid)) {
            $this->apiError("非法请求！");
        }
        if (empty($page_num)) {
            $page_num = 1;
        }
        if (empty($page)) {
            $page = 8;
        }
        $User = D('User');
        $uid = $User->getIdByUnionid($unionid);
        if (empty($industry_id)) {
            $where = array(
                "a.isdel" => 0,
                "a.status" => 1,
                "a.uid" => $uid,
                "b.tablename" => "recruitment",
                "c.tablename" => "resume_expect",
                "d.isdel" => 0,
                "f.isdel" => 0,
                "g.tablename" => "resume_expect",
                "h.isdel" => 0,
                "i.tablename" => "resume_education",
            );
        } else {
            $where = array(
                "a.isdel" => 0,
                "a.status" => 1,
                "a.uid" => $uid,
                "b.tablename" => "recruitment",
                "c.tablename" => "resume_expect",
                "d.isdel" => 0,
                "f.isdel" => 0,
                "g.tablename" => "resume_expect",
                "g.level" => $industry_id,
                "h.isdel" => 0,
                "i.tablename" => "resume_education",
            );
        }
        $position_id = $this->recruitment_model
            ->alias("a")
            ->join("right join __SPEC_SELECT_POSITION__ as b on b.object_id = a.id")
            ->join("right join __SPEC_SELECT_POSITION__ as c on c.position_id = b.position_id")
            ->join("right join __SPEC_POSITION__ as k on k.id = c.position_id")
            ->join("right join __RESUME_EXPECT__ as d on d.id = c.object_id")
            ->join("right join __USER__ as e on e.id = d.uid")
            ->join("right join __RESUME_EXPECT__ as f on f.uid = e.id")
            ->join("right join __SPEC_LABEL__ as g on g.object_id = f.id")
            ->join("right join __RESUME_EDUCATION__ as h on h.uid = e.id")
            ->join("right join __SPEC_LABEL__ as i on i.object_id = h.id")
            ->join("right join __SPEC_INFO__ as j on j.id = i.spec_info_id")
            ->where($where)
            ->order("j.id desc")
            ->field("e.id,e.name,e.avatar,j.spec_info_name as education,k.name as position,e.participate_time")
            ->select();

        $data = $resources->dataGroup($position_id, "id");

        foreach ($data as $k => $v) {
            $data[$k] = $v[0];
        }
        //数组初始化
        $data = array_values($data);
        foreach ($data as $ke => $val) {
            $date_time = strtotime(date("Y-m-d")) - strtotime($val["participate_time"]);
            $year = floor($date_time / (3600 * 24 * 365));
            switch ($year) {
                case 0:
                    $data[$ke]["experience"] = "1年以内";
                    break;
                case 1:
                    $data[$ke]["experience"] = "1-3年";
                    break;
                case 2:
                    $data[$ke]["experience"] = "1-3年";
                    break;
                case 3:
                    $data[$ke]["experience"] = "1-3年";
                    break;
                case 4:
                    $data[$ke]["experience"] = "3-5年";
                    break;
                case 5:
                    $data[$ke]["experience"] = "3-5年";
                    break;
                case 6:
                    $data[$ke]["experience"] = "5-10年";
                    break;
                case 7:
                    $data[$ke]["experience"] = "5-10年";
                    break;
                case 8:
                    $data[$ke]["experience"] = "5-10年";
                    break;
                case 9:
                    $data[$ke]["experience"] = "5-10年";
                    break;
                case 10:
                    $data[$ke]["experience"] = "5-10年";
                    break;
                default:
                    $data[$ke]["experience"] = "10年以上";
                    break;

            }
            unset($data[$ke]["participate_time"]);
        }

        $data = $resources->arr_page($data, $page_num, $page);


        $this->apiReturn(array("data" => $data));
    }

    /***
     * Notes: 人才筛选
     * Created by
     * User: Belief
     * DateTime: 2020-07-11 17:07
     * Return : 返回array 人才信息
     */
    public function talentScreening()
    {
        $dataL = $this->dataL;
        $resources = new ResourcesController();
        $unionid = $resources->remove_xss($dataL['unionid']);
        $education_id = $resources->remove_xss($dataL['education_id']);//学历id
        $experience_id = $resources->remove_xss($dataL['experience_id']);//经验id
        $salary_id = $resources->remove_xss($dataL['salary_id']);//薪资id
        $page = $resources->remove_xss($dataL['page']);//每页多少条
        $page_num = $resources->remove_xss($dataL['page_num']);//页数

        $User = D('User');
        $uid = $User->getIdByUnionid($unionid);
        $start_time = "";
        $end_time = "";
        if (empty($experience_id)) {
            $userList = array();
        } else {
            switch ($experience_id) {
                case 8:
                    $start_time = date("Y", time()) . "-01-01"; //开始时间
                    $end_time = date("Y", time()) . "-12-31"; //开始时间
                    break;
                case 9:
                    $start_time = date("Y", time()) . "-01-01"; //开始时间
                    $end_time = date("Y", time()) . "-12-31"; //开始时间
                    break;
                case 10:
                    $start_time = date("Y", time()) . "-01-01"; //开始时间
                    $end_time = date("Y", time()) . "-12-31"; //开始时间
                    break;
                case 11:
                    $start_time = date("Y", strtotime("+1 year")) . "-01-01"; //开始时间
                    $end_time = date("Y", strtotime("+3 year")) . "-12-31"; //开始时间
                    break;
                case 12:
                    $start_time = date("Y", strtotime("+3 year")) . "-01-01"; //开始时间
                    $end_time = date("Y", strtotime("+5 year")) . "-12-31"; //开始时间
                    break;
                case 13:
                    $start_time = date("Y", strtotime("+5 year")) . "-01-01"; //开始时间
                    $end_time = date("Y", strtotime("+10 year")) . "-12-31"; //开始时间
                    break;
                case 14:
                    $start_time = date("Y", strtotime("+10 year")) . "-01-01"; //开始时间
                    $end_time = date("Y", strtotime("+30 year")) . "-12-31"; //开始时间
                    break;
            }
            $userList = array();
            if ($experience_id) {
                $where = array(
                    'participate_time' => array(array('egt', $start_time), array('elt', $end_time))
                );
                $userList = $this->user_model->where($where)->field("id")->select();
            }
            //工作经历
            $userList = $resources->array_deduplication($userList, "id");//去重
            $userList = array_column($userList, "id");//二维转一维
        }

        if (empty($education_id)) {
            $educationFind = array();
        } else {
            $educationFind = $this->resume_education_model
                ->alias("r")
                ->join("__SPEC_LABEL__ as s on s.object_id = r.id")
                ->join("__SPEC_INFO__ as s1 on s1.id = s.spec_info_id")
                ->join("__USER__ as u on u.id = r.uid")
                ->where(array("s.spec_info_id" => $education_id, "s.tablename" => "resume_education"))
                ->field("u.*,s1.spec_info_name")
                ->group("s.object_id")
                ->order("s1.id desc")
                ->field("u.id")
                ->select();
            //学历
            $educationFind = $resources->array_deduplication($educationFind, "id");//去重
            $educationFind = array_column($educationFind, "id");//二维转一维
        }
        if (empty($salary_id)) {
            $expectFind = array();
        } else {
            $expectFind = $this->resume_expect_model
                ->alias("r")
                ->join("__SPEC_LABEL__ as s on s.object_id = r.id")
                ->join("__SPEC_INFO__ as s1 on s1.id = s.spec_info_id")
                ->join("__USER__ as u on u.id = r.uid")
                ->where(array("s.spec_info_id" => $salary_id, "s.tablename" => "resume_expect"))
                ->field("u.*")
                ->group("s.object_id")
                ->order("s1.id desc")
                ->field("u.id")
                ->select();
            //薪资
            $expectFind = $resources->array_deduplication($expectFind, "id");//去重
            $expectFind = array_column($expectFind, "id");//二维转一维
        }

        //发布的职位，根据职位去查询匹配的人
        $where = array(
            "a.isdel" => 0,
            "a.status" => 1,
            "a.uid" => $uid,
            "b.tablename" => "recruitment",
            "c.tablename" => "resume_expect",
            "d.isdel" => 0,
        );

        $industryFind = $this->recruitment_model
            ->alias("a")
            ->join("right join __SPEC_SELECT_POSITION__ as b on b.object_id = a.id")
            ->join("right join __SPEC_SELECT_POSITION__ as c on c.position_id = b.position_id")
            ->join("right join __RESUME_EXPECT__ as d on d.id = c.object_id")
            ->join("right join __USER__ as e on e.id = d.uid")
            ->where($where)
            ->field("e.id")
            ->select();
        $industryFind = $resources->array_deduplication($industryFind, "id");//去重
        $industryFind = array_column($industryFind, "id");//二维转一维

        //取用户id相同的
        $a = array_intersect_assoc(empty($userList) ? $userList = $educationFind : $userList, empty($educationFind) ? $educationFind = $userList : $educationFind);
        $b = array_intersect_assoc(empty($a) ? $a = $expectFind : $a, empty($expectFind) ? $expectFind = $a = $expectFind : $expectFind);
        $c = array_intersect_assoc(empty($b) ? $b = $industryFind : $b, empty($industryFind) ? $industryFind = $b : $industryFind);
        $uidList = array_values($c);

        //所有筛选条件都为空时
        if (empty($uidList)) {
            $wheres = array(
                "a.isdel" => 0,
                "a.status" => 1,
                "a.uid" => $uid,
                "b.tablename" => "recruitment",
                "c.tablename" => "resume_expect",
                "d.isdel" => 0,
                "f.isdel" => 0,
                "g.tablename" => "resume_expect",
                "h.isdel" => 0,
                "i.tablename" => "resume_education",
            );
        } else {  //筛选条件都为空时
            $wheres = array(
                "a.isdel" => 0,
                "a.status" => 1,
                "a.uid" => $uid,
                "b.tablename" => "recruitment",
                "c.tablename" => "resume_expect",
                "d.isdel" => 0,
                "e.id" => array("in", $uidList),
                "f.isdel" => 0,
                "g.tablename" => "resume_expect",
                "h.isdel" => 0,
                "i.tablename" => "resume_education",
            );
        }

        $position_id = $this->recruitment_model
            ->alias("a")
            ->join("right join __SPEC_SELECT_POSITION__ as b on b.object_id = a.id")
            ->join("right join __SPEC_SELECT_POSITION__ as c on c.position_id = b.position_id")
            ->join("right join __RESUME_EXPECT__ as d on d.id = c.object_id")
            ->join("right join __USER__ as e on e.id = d.uid")
            ->join("right join __RESUME_EXPECT__ as f on f.uid = e.id")
            ->join("right join __SPEC_LABEL__ as g on g.object_id = f.id")
            ->join("right join __RESUME_EDUCATION__ as h on h.uid = e.id")
            ->join("right join __SPEC_LABEL__ as i on i.object_id = h.id")
            ->join("right join __SPEC_INFO__ as j on j.id = i.spec_info_id")
            ->where($wheres)
            ->order("j.id desc")
            ->field("e.id,e.name,e.avatar,j.spec_info_name as education,a.position,e.participate_time")
            ->select();

        $data = $resources->dataGroup($position_id, "id");

        foreach ($data as $k => $v) {
            $data[$k] = $v[0];
        }
        //数组初始化
        $data = array_values($data);
        foreach ($data as $ke => $val) {
            $date_time = strtotime(date("Y-m-d")) - strtotime($val["participate_time"]);
            $year = floor($date_time / (3600 * 24 * 365));
            switch ($year) {
                case 0:
                    $data[$ke]["experience"] = "1年以内";
                    break;
                case 1:
                    $data[$ke]["experience"] = "1-3年";
                    break;
                case 2:
                    $data[$ke]["experience"] = "1-3年";
                    break;
                case 3:
                    $data[$ke]["experience"] = "1-3年";
                    break;
                case 4:
                    $data[$ke]["experience"] = "3-5年";
                    break;
                case 5:
                    $data[$ke]["experience"] = "3-5年";
                    break;
                case 6:
                    $data[$ke]["experience"] = "5-10年";
                    break;
                case 7:
                    $data[$ke]["experience"] = "5-10年";
                    break;
                case 8:
                    $data[$ke]["experience"] = "5-10年";
                    break;
                case 9:
                    $data[$ke]["experience"] = "5-10年";
                    break;
                case 10:
                    $data[$ke]["experience"] = "5-10年";
                    break;
                default:
                    $data[$ke]["experience"] = "10年以上";
                    break;

            }
            unset($data[$ke]["participate_time"]);
        }
        $data = $resources->arr_page($data, $page_num, $page);

        $this->apiReturn(array("data" => $data));


    }

    /**
     * Notes: 添加人才分组
     * User: Sen
     * DateTime: 2020/7/13 15:38
     * Return:
     */
    public function talentGroupAdd()
    {
        $dataL = $this->dataL;
        $resources = new ResourcesController();
        $unionid = $resources->remove_xss($dataL['unionid']);
        $name = $resources->remove_xss($dataL['name']);
        $User = D('User');
        $uid = $User->getIdByUnionid($unionid);
        if (empty($unionid) || empty($name)) {
            $this->apiError("非法请求！");
        }
        $where = array(
            "name" => $name,
            "uid" => $uid,
        );
        $group = $this->user_group_model
            ->where($where)
            ->find();

        if ($group) {
            $this->apiError("该分组已经创建！");
        }

        $add = array(
            "name" => $name,
            "uid" => $uid,
            "room_number" => $resources->getRandNumber(),
            "time" => date("Y-m-d H:i:s", time())
        );

        $res = $this->user_group_model->add($add);

        if ($res) {
            $this->apiSuccess("操作成功！");
        } else {
            $this->apiError("操作失败！");
        }
    }

    /**
     * Notes: 人才分组列表
     * User: Sen
     * DateTime: 2020/7/13 15:26
     * Return:
     */
    public function talentGroupList()
    {
        $dataL = $this->dataL;
        $resources = new ResourcesController();
        $unionid = $resources->remove_xss($dataL['unionid']);
        $User = D('User');
        $uid = $User->getIdByUnionid($unionid);
        if (empty($unionid)) {
            $this->apiError("非法请求！");
        }

        $where = array(
            "uid" => $uid,
            "isdel" => 0,
        );
        $talentGroupList = $this->user_group_model
            ->where($where)
            ->field("id,name,uid,time,room_number")
            ->select();

        $this->apiReturn(array("data" => $talentGroupList));


    }

    /**
     * Notes: 删除人才分组
     * User: Sen
     * DateTime: 2020/7/13 15:44
     * Return:
     */
    public function talentGroupDel()
    {
        $dataL = $this->dataL;
        $resources = new ResourcesController();
        $unionid = $resources->remove_xss($dataL['unionid']);
        $id = $resources->remove_xss($dataL['id']);//人才分组id
        $User = D('User');
        $uid = $User->getIdByUnionid($unionid);
        if (empty($unionid) || empty($id)) {
            $this->apiError("非法请求！");
        }
        $res = $this->user_group_model->where(array("uid" => $uid, "id" => $id))->save(array("isdel" => 1));

        if ($res !== false) {
            $this->apiSuccess("操作成功！");
        } else {
            $this->apiError("操作失败！");
        }
    }

    /**
     * Notes: 将人才添加进入分组
     * User: Sen
     * DateTime: 2020/7/13 15:53
     * Return:
     */
    public function talentAdd()
    {
        $dataL = $this->dataL;
        $resources = new ResourcesController();
        $unionid = $resources->remove_xss($dataL['unionid']);
        $id = $resources->remove_xss($dataL['id']);//人才分组id
        $tid = $resources->remove_xss($dataL['tid']);//人才id
        $room_number = $resources->remove_xss($dataL['room_number']);//群组编号
        $User = D('User');
        $uid = $User->getIdByUnionid($unionid);
        if (empty($unionid) || empty($id) || empty($tid) || empty($room_number)) {
            $this->apiError("非法请求！");
        }

        $where = array(
            "object_id" => $id,
            "uid" => $uid,
            "parent_id" => $tid,
            "room_number" => $room_number,
        );

        $join_group = $this->user_join_group_model
            ->where($where)
            ->find();
        if ($join_group) {
            $this->apiError("该人才已添加到该分组，请勿重复添加！");
        }

        $add = array(
            "object_id" => $id,
            "uid" => $uid,
            "parent_id" => $tid,
            "room_number" => $room_number,
            "tablename" => "user_group",
            "time" => date("Y-m-d H:i:s", time()),
        );
        $res = $this->user_join_group_model->add($add);

        if ($res) {
            $this->apiSuccess("操作成功！");
        } else {
            $this->apiError("操作失败！");
        }


    }

    /**
     * Notes: 分组详情
     * User: Sen
     * DateTime: 2020/7/13 16:11
     * Return:
     */
    public function talentGroupDetail()
    {
        $dataL = $this->dataL;
        $resources = new ResourcesController();
        $unionid = $resources->remove_xss($dataL['unionid']);
        $id = $resources->remove_xss($dataL['id']);//人才分组id
        $page = $resources->remove_xss($dataL['page']);//每页多少条
        $page_num = $resources->remove_xss($dataL['page_num']);//页数

        if (empty($unionid) || empty($id)) {
            $this->apiError("非法请求！");
        }
        $where = array(
            "a.object_id" => $id,
            "a.isdel" => 0,
            "a.tablename" => "user_group",
            "f.isdel" => 0,
            "g.tablename" => "resume_expect",
            "h.isdel" => 0,
            "i.tablename" => "resume_education",
            "k.tablename" => "resume_expect",
        );
        $detail = $this->user_join_group_model
            ->alias("a")
            ->join("right join __USER__ as e on e.id = a.parent_id")
            ->join("right join __RESUME_EXPECT__ as f on f.uid = e.id")
            ->join("right join __SPEC_LABEL__ as g on g.object_id = f.id")
            ->join("right join __RESUME_EDUCATION__ as h on h.uid = e.id")
            ->join("right join __SPEC_LABEL__ as i on i.object_id = h.id")
            ->join("right join __SPEC_INFO__ as j on j.id = i.spec_info_id")
            ->join("right join __SPEC_SELECT_POSITION__ as k on k.object_id = f.id")
            ->join("right join __SPEC_POSITION__ as b on b.id = k.position_id")
            ->where($where)
            ->order("j.id desc")
            ->field("a.id,e.id as tid,e.name,e.avatar,j.spec_info_name as education,e.participate_time,b.name")
            ->select();

        $data = $resources->dataGroup($detail, "id");

        foreach ($data as $k => $v) {
            $data[$k] = $v[0];
        }
        //数组初始化
        $data = array_values($data);
        foreach ($data as $ke => $val) {
            $date_time = strtotime(date("Y-m-d")) - strtotime($val["participate_time"]);
            $year = floor($date_time / (3600 * 24 * 365));
            switch ($year) {
                case 0:
                    $data[$ke]["experience"] = "1年以内";
                    break;
                case 1:
                    $data[$ke]["experience"] = "1-3年";
                    break;
                case 2:
                    $data[$ke]["experience"] = "1-3年";
                    break;
                case 3:
                    $data[$ke]["experience"] = "1-3年";
                    break;
                case 4:
                    $data[$ke]["experience"] = "3-5年";
                    break;
                case 5:
                    $data[$ke]["experience"] = "3-5年";
                    break;
                case 6:
                    $data[$ke]["experience"] = "5-10年";
                    break;
                case 7:
                    $data[$ke]["experience"] = "5-10年";
                    break;
                case 8:
                    $data[$ke]["experience"] = "5-10年";
                    break;
                case 9:
                    $data[$ke]["experience"] = "5-10年";
                    break;
                case 10:
                    $data[$ke]["experience"] = "5-10年";
                    break;
                default:
                    $data[$ke]["experience"] = "10年以上";
                    break;

            }
            unset($data[$ke]["participate_time"]);
        }

        $data = $resources->arr_page($data, $page_num, $page);


        $this->apiReturn(array("data" => $data));
    }

    /**
     * Notes: 将人才从分组中删除
     * User: Sen
     * DateTime: 2020/7/13 18:02
     * Return:
     */
    public function talentDel()
    {
        $dataL = $this->dataL;
        $resources = new ResourcesController();
        $unionid = $resources->remove_xss($dataL['unionid']);
        $id = $resources->remove_xss($dataL['id']);//人才分组id
        $tid = $resources->remove_xss($dataL['tid']);//人才id
        $User = D('User');
        $uid = $User->getIdByUnionid($unionid);

        $where = array(
            "object_id" => $id,
            "uid" => $uid,
            "parent_id" => $tid,
        );
        $save = array(
            "isdel" => 1,
            "update_time" => date("Y-m-d H:i:s",time()),
        );
        $res = $this->user_join_group_model
            ->where($where)
            ->save($save);

            if($res !== false){
                $this->apiSuccess("操作成功！");
            }else{
                $this->apiError("操作失败！");
            }
    }


}



