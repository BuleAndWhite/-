<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Author: XiaoYuan <767684610@qq.com>
// +----------------------------------------------------------------------
/**
 * 获取用户授权
 * 创建日期：2020-06-29
 */

namespace Api\Controller;

use http\Env\Response;
use Think\Controller;

class UserController extends AppController
{
    protected $users_model;
    protected $spec_label_model;//标签表
    protected $resume_social_model;//社交主页
    protected $resume_certificate_model;//资质证书
    protected $resume_education_model;//教育经历
    protected $resume_expect_model;//求职期望
    protected $resume_experience_model;//工作经历
    protected $resume_project_model;//项目经验
    protected $spec_select_position_model;//职位选择
    protected $job_greet_model;//默认信息
    protected $access_model;//访问表
    protected $recruitment_model;//招聘表
    protected $user_company_model;//招聘表
    protected $user_student_authen_model;//学生认证
    protected $sof_model;//动态
    protected $appid;
    protected $appsecret;
    public $OK;
    public $IllegalAesKey;
    public $IllegalIv;
    public $IllegalBuffer;
    public $DecodeBase64Error;

    function __construct()
    {
        parent::_initialize();
        $this->users_model = M('User');
        $this->spec_label_model = M('SpecLabel');
        $this->resume_social_model = M('ResumeSocial');
        $this->resume_certificate_model = M('ResumeCertificate');
        $this->resume_education_model = M('ResumeEducation');
        $this->resume_expect_model = M('ResumeExpect');
        $this->resume_experience_model = M('ResumeExperience');
        $this->resume_project_model = M('ResumeProject');
        $this->spec_select_position_model = M('SpecSelectPosition');
        $this->job_greet_model = M('JobGreet');
        $this->access_model = M('Access');
        $this->recruitment_model = M('Recruitment');
        $this->user_company_model = M('UserCompany');
        $this->user_student_authen_model = M('UserStudentAuthen');
        $this->sof_model = M('Sof');
        $this->appid = C("MAPP_ID");
        $this->appsecret = C("MAPP_SECRET");
        $this->OK = "0";
        $this->IllegalAesKey = "-41001";
        $this->IllegalIv = "-41002";
        $this->IllegalBuffer = "-41003";
        $this->DecodeBase64Error = "-41004";
    }


    /**
     * 2018年6月5日14:10:06
     * 刘北林
     * 首页授权
     */
    public function authorizeLogin()
    {
        $resources = new ResourcesController();
        $encryptedData = $_REQUEST['encryptedData'] ? urldecode($_REQUEST['encryptedData']) : "";
        $iv = $_REQUEST['iv'] ? urldecode($_REQUEST['iv']) : "";
        $code = urldecode($_REQUEST['code']);

        if (!$encryptedData || !$iv || !$code) {
            $this->apiError('非法错误');
        }
        $tokenList = $this->obtainToken($this->appid, $this->appsecret, $code);
        if (isset($tokenList['errcode']) && !empty($tokenList['errcode'])){
            $this->apiError($tokenList['errmsg']);
        }else {
            $errCode = $this->decryptData($encryptedData, $iv, $user_token, $tokenList['session_key']);
        }

        $user_token = json_decode($user_token, true);
        $userLists = $this->users_model->where(array("unionid" => $user_token['unionId']))->find();
        //用户存在则绑定用户 不存在添加用户
        if ($userLists) {
            if ($user_token['unionId']) {
                $data['unionid'] = $resources->remove_xss($user_token['unionId']);
                S("unionid", $resources->remove_xss($user_token['unionId']), 60 * 60 * 24 * 3000);
                S("xopenid", $resources->remove_xss($user_token['openId']), 60 * 60 * 24 * 3000);
                $data['xopenid'] = $resources->remove_xss($user_token['openId']);
                $data['name'] = $resources->remove_xss($resources->filter_Emoji($user_token['nickName']));
                $data['avatar'] = $resources->remove_xss($user_token['avatarUrl']);
                $data['ip'] = $resources->remove_xss($_SERVER["REMOTE_ADDR"]);
                $data['update_time'] = date("Y-m-d H:i:s", time());
                $this->users_model->where(array("id" => $userLists['id']))->save($data);
            }
        } else {
            if ($user_token['unionId']) {
                $data['unionid'] = $resources->remove_xss($user_token['unionId']);
                S("unionid", $resources->remove_xss($user_token['unionId']), 60 * 60 * 24 * 3000);
                S("xopenid", $resources->remove_xss($user_token['openId']), 60 * 60 * 24 * 3000);
                $data['xopenid'] = $resources->remove_xss($user_token['openId']);
                $data['name'] = $resources->remove_xss($resources->filter_Emoji($user_token['nickName']));
                $data['avatar'] = $resources->remove_xss($user_token['avatarUrl']);
                $data['ip'] = $resources->remove_xss($_SERVER["REMOTE_ADDR"]);
                $data['session_key'] = $resources->remove_xss($tokenList['session_key']);
                $data['time'] = date("Y-m-d H:i:s", time());
                $this->users_model->add($data);
            }
        }
        if ($errCode == 0) {
            $this->apiReturn(array("data" => array("unionid" => $user_token['unionId'])));
        } else {
            $this->apiError($errCode);
        }
    }

    /**
     * Notes: 返回用户信息
     * User: Sen
     * DateTime: 2020/7/13 19:00
     * Return:
     */
    public function userInfo()
    {
        $unionid = I("unionid");


        if (!$unionid) {
            $this->apiError('非法错误');
        }
        $User = D('User');
        $uid = $User->getIdByUnionid($unionid);
        $userList['data'] = $User
            ->alias("u")
            ->join("left join __USER_ENTERPISE__ as e on u.id = e.uid")
            ->where(array("u.unionid" => $unionid))
            ->field("u.id,u.name,u.avatar,e.id as eid,e.name as ename,u.is_chat")
            ->find();

        $where_one = array(
            "uid" => $uid,
            "isdel" => 0,
            "status" => 1,
        );
        //公司绑定状态
        $post_status = $this->recruitment_model->where($where_one)->find();
        empty($post_status) ? $userList['data']["post_status"] = 0 : $userList['data']["post_status"] = 1;
        //发布状态
        $release_status = $recruitmentList = $this->recruitment_model->where(array("uid" => $uid))->select();
        empty($release_status) ? $userList['data']["release_status"] = 0 : $userList['data']["release_status"] = 1;
        //我发布的动态数量
        $userList['data']["sof_num"] = $this->sof_model->where(array("uid" => $uid, "isdel" => 0))->count();
        $this->apiReturn($userList);
    }

    /**
     * name 刘北林
     * data 2020年6月29日14:12:17
     * @param $appid
     * @param $appsecret
     * @return mixed
     * 获取 access_token
     */
    public function obtainToken($appid, $secret, $code)
    {
        $c = new \Curl();
        $url = "https://api.weixin.qq.com/sns/jscode2session?appid=" . $appid . "&secret=" . $secret . "&js_code=" . $code . "&grant_type=authorization_code";
        $user = $c->get($url);
        $user_token = json_decode($user, true);
        return $user_token;
    }

    /**
     * 检验数据的真实性，并且获取解密后的明文.
     * @param $encryptedData string 加密的用户数据
     * @param $iv string 与用户数据一同返回的初始向量
     * @param $data string 解密后的原文
     *刘北林
     * 2020年6月29日14:57:55
     * @return int 成功0，失败返回对应的错误码
     */
    public function decryptData($encryptedData, $iv, &$user_token, $session_key)
    {
        if (strlen($session_key) != 24) {
            return $this->IllegalAesKey;
        }
        $aesKey = base64_decode($session_key);

        if (strlen($iv) != 24) {
            return $this->IllegalIv;
        }
        $aesIV = base64_decode($iv);

        $aesCipher = base64_decode($encryptedData);

        $result = openssl_decrypt($aesCipher, "AES-128-CBC", $aesKey, 1, $aesIV);

        $dataObj = json_decode($result);
        if ($dataObj == NULL) {
            return $this->IllegalBuffer;
        }
        if ($dataObj->watermark->appid != $this->appid) {
            return $this->IllegalBuffer;
        }
        $user_token = $result;
        return $this->OK;
    }

    /**
     * Notes: 添加个人信息简历信息 根据type 执行某个功能
     * User: Belief
     * DateTime: 2020-7-4
     * Return : 成功失败
     */
    public function resumePublicAdd()
    {
        $dataL = $this->dataL;
        if (!$dataL['type']) {
            $this->apiError('非法错误！请填写必填参数');
        }
        switch ($dataL['type']) {
            case 1:
                $resources = new ResourcesController();
                $id = $resources->remove_xss($resources->getId($dataL['unionid']));
                $data['name_n'] = I('name_n');
                $data['sex'] = I('sex');
                if ($dataL['birthday']) {
                    $data['birthday'] = I('birthday');
                }
                $data['we_chat'] = I('we_chat');
                $data['phone'] = I('phone');
                $data['email'] = I('email');
                $data['participate_time'] = I('participate_time');
                $data['advantage'] = I('advantage');
                $job_status_id_before = I('job_status_id_before');//修改前
                $job_status_id_rear = I('job_status_id_rear');//修改后
                $data['update_time'] = date("Y-m-d H:i:s", time());

                $saveL = $this->users_model->where(array("id" => $id))->save($data);
                if ($saveL) {
                    if ($job_status_id_before) {
                        if ($job_status_id_rear && $job_status_id_rear != $job_status_id_before) {
                            $this->spec_label_model->where(array("object_id" => $id, "spec_info_id" => $job_status_id_before, "tablename" => "user"))->save(array("spec_info_id" => $job_status_id_rear));
                            $this->apiSuccess("操作成功！");
                        } else {
                            $this->apiSuccess("操作成功！");
                        }
                    } else {
                        $spec_label_add["object_id"] = $id;

                        $spec_label_add["spec_info_id"] = $job_status_id_rear;
                        $spec_label_add["tablename"] = "user";
                        $this->spec_label_model->add($spec_label_add);
                        $this->apiSuccess("操作成功！");
                    }
                } else {
                    $this->apiError('操作失败！');
                }
                break;//个人信息
            case 2://个人优势
                $resources = new ResourcesController();
                $id = $resources->remove_xss($resources->getId($dataL['unionid']));
                $data['advantage'] = I('advantage');
                $data['update_time'] = date("Y-m-d H:i:s", time());
                !$this->users_model->where(array("id" => $id))->save($data) ? $this->apiError('操作失败！') : $this->apiSuccess("操作成功！");
                break; //个人优势
            case 3://求职期望
                $resources = new ResourcesController();
                $id = $resources->remove_xss($resources->getId($dataL['unionid']));
                $position_id = I('position_id');
                $city_id = I('city_id');
                $salary_id = I('salary_id');
                $first_industry_id = I("first_industry_id"); //行业 一级id
                $second_industry_id = I("second_industry_id"); //行业 二级id

                if (!$id || !$position_id || !$city_id || !$salary_id || !$first_industry_id || !$second_industry_id) {
                    $this->apiError('非法错误！请填写必填参数');
                }

                $expectList = $this->resume_expect_model->where(array("uid" => $id, "isdel" => 0))->select();

                foreach ($expectList as $key => $value) {
                    $levelOne = $this->spec_select_position_model->where(array("object_id" => $value['id'], "position_id" => $position_id, "tablename" => "resume_expect"))->find();
                    if ($levelOne) {
                        $this->apiError('求职意向已经存在了！');
                    }
                }

                $resumeExpect['city_id'] = $city_id;
                $resumeExpect['uid'] = $id;
                $resumeExpect['time'] = date("Y-m-d H:i:s", time());

                $expectRes = $this->resume_expect_model->add($resumeExpect);
                if ($expectRes) {
                    $spec_label_add["object_id"] = $expectRes;
                    $spec_label_add["spec_info_id"] = $salary_id;
                    $spec_label_add["tablename"] = "resume_expect";
                    $this->spec_label_model->add($spec_label_add);

                    $spec_label_add["spec_info_id"] = $first_industry_id;
                    $spec_label_add["level"] = $second_industry_id;
                    $this->spec_label_model->add($spec_label_add);

                    $spec_select_position["object_id"] = $expectRes;
                    $spec_select_position["position_id"] = $position_id;
                    $spec_select_position["tablename"] = "resume_expect";
                    $this->spec_select_position_model->add($spec_select_position);
                    $this->apiSuccess("操作成功！");
                } else {
                    $this->apiError('操作失败！');
                }

                break;//求职期望
            case 4://工作经历
                $resources = new ResourcesController();
                $id = $resources->remove_xss($resources->getId($dataL['unionid']));//用户id
                $company_name = I('company_name');//公司名称
                $start_time = I('start_time');//开始时间
                $end_time = I('end_time');//结束时间
                $department = I('department');//部门
                $content = I('content');//工作内容
                $performance = I('performance');//工作业绩
                $position_name = I('position_name');//职位名称
                $first_industry_id = I('first_industry_id');//行业一级id
                $second_industry_id = I('second_industry_id');//行业二级id
                $position_id = I('position_id');//职位三级id
                if (!$id || !$company_name || !$start_time || !$end_time || !$department || !$content || !$performance || !$position_name || !$first_industry_id || !$second_industry_id || !$position_id) {
                    $this->apiError('非法错误！请填写必填参数');
                }
                $add_content = array(
                    "company_name" => $company_name,
                    "start_time" => $start_time,
                    "end_time" => $end_time,
                    "department" => $department,
                    "content" => $content,
                    "performance" => $performance,
                    "position_name" => $position_name,
                    "uid" => $id,
                    "time" => date("Y-m-d H:i:s", time()),
                );

                $res = $this->resume_experience_model->add($add_content);
                if ($res) {
                    //添加行业标签
                    $industry_add = array(
                        "object_id" => $res,
                        "spec_info_id" => $first_industry_id,
                        "tablename" => "resume_experience",
                        "level" => $second_industry_id,
                    );
                    $industry_res = $this->spec_label_model->add($industry_add);
                    //添加职位标签
                    $position_add = array(
                        "object_id" => $res,
                        "position_id" => $position_id,
                        "tablename" => "resume_experience",
                    );
                    $position_res = $this->spec_select_position_model->add($position_add);

                    if (empty($industry_res) || empty($position_res)) {
                        $this->apiError("工作经历添加失败！");
                    } else {
                        $this->apiSuccess("工作经历添加成功！");
                    }
                } else {
                    $this->apiError("工作经历添加失败！");
                }
                break; //工作经历
            case 5://项目经历
                $resources = new ResourcesController();
                $id = $resources->remove_xss($resources->getId($dataL['unionid']));//用户id
                $name = I('name');//项目名称
                $character = I('character');//担任角色
                $start_time = I('start_time');//项目开始时间
                $end_time = I('end_time');//项目结束时间
                $description = I('description');//项目描述
                $performance = I('performance');//项目业绩
                $url = I('url');//项目链接

                if (!$id || !$name || !$character || !$start_time || !$end_time || !$description || !$performance || !$url) {
                    $this->apiError('非法错误！请填写必填参数');
                }
                $add_content = array(
                    "name" => $name,
                    "character" => $character,
                    "start_time" => $start_time,
                    "end_time" => $end_time,
                    "description" => $description,
                    "performance" => $performance,
                    "url" => $url,
                    "uid" => $id,
                    "time" => date("Y-m-d H:i:s", time()),
                );

                $res = $this->resume_project_model->add($add_content);
                if ($res) {
                    $this->apiSuccess("项目经历添加成功！");
                } else {
                    $this->apiError("项目经历添加失败！");
                }
                break; //项目经历
            case 6://教育经历
                $resources = new ResourcesController();
                $id = $resources->remove_xss($resources->getId($dataL['unionid']));//用户id
                $school = I('school');//学校
                $education_id = I('education_id');//学历id
                $profession = I('profession');//专业
                $start_time = I('start_time');//开始时间针对年
                $end_time = I('end_time');//结束时间针对年
                $experience = I('experience');//在校经历

                if (!$id || !$school || !$education_id || !$profession || !$start_time || !$end_time || !$experience) {
                    $this->apiError('非法错误！请填写必填参数');
                }


                $education_add = array(
                    "school" => $school,
                    "profession" => $profession,
                    "start_time" => $start_time,
                    "end_time" => $end_time,
                    "experience" => $experience,
                    "uid" => $id,
                    "time" => date("Y-m-d H:i:s", time()),
                );
                $res = $this->resume_education_model->add($education_add);

                if ($res) {
                    //添加行业标签
                    $industry_add = array(
                        "object_id" => $res,
                        "spec_info_id" => $education_id,
                        "tablename" => "resume_education",
                    );
                    $education_res = $this->spec_label_model->add($industry_add);
                    if ($education_res) {
                        $this->apiSuccess("教育经历添加成功");
                    } else {
                        $this->apiError("教育经历添加失败");
                    }
                } else {
                    $this->apiError("教育经历添加失败");
                }

                break; //教育经历
            case 7://资质证书
                $resources = new ResourcesController();
                $id = $resources->remove_xss($resources->getId($dataL['unionid']));//用户id
                $certificate = I('certificate');//证书
                if (!$id || !$certificate) {
                    $this->apiError('非法错误！请填写必填参数');
                }

                $certificate_add = array(
                    'uid' => $id,
                    'certificate' => $certificate,
                    'time' => date("Y-m-d H:i:s", time()),
                );

                $res = $this->resume_certificate_model->add($certificate_add);

                if ($res) {
                    $this->apiSuccess("资质证书添加成功！");
                } else {
                    $this->apiError("资质证书添加失败！");
                }
                break;//资质证书
            case 8://社交主页
                $resources = new ResourcesController();
                $id = $resources->remove_xss($resources->getId($dataL['unionid']));//用户id
                $url = I('url');//主页链接
                if (!$id || !$url) {
                    $this->apiError('非法错误！请填写必填参数');
                }

                $social_add = array(
                    'uid' => $id,
                    'url' => $url,
                    'time' => date("Y-m-d H:i:s", time())
                );
                $res = $this->resume_social_model->add($social_add);
                if ($res) {
                    $this->apiSuccess("社交主页添加成功！");
                } else {
                    $this->apiError("社交主页添加失败！");
                }
                break; //社交主页

        }


    }

    /**
     * Notes: 修改个人信息简历信息 根据type 执行某个功能
     * User: Belief
     * DateTime: 2020-7-4
     * Return : 成功失败
     */
    public function resumePublicSave()
    {
        $dataL = $this->dataL;
        if (!$dataL['type']) {
            $this->apiError('非法错误！请填写必填参数');
        }
        switch ($dataL['type']) {
            case 1:
                $resources = new ResourcesController();
                $id = $resources->remove_xss($resources->getId($dataL['unionid']));
                $data['name_n'] = I('name_n');
                $data['sex'] = I('sex');
                if ($dataL['birthday']) {
                    $data['birthday'] = I('birthday');
                }
                $data['we_chat'] = I('we_chat');
                $data['participate_time'] = I('participate_time');
                $data['phone'] = I('phone');
                $data['email'] = I('email');
                $data['advantage'] = I('advantage');
                $job_status_id_before = I('job_status_id_before');//修改前
                $job_status_id_rear = I('job_status_id_rear');//修改后
                $data['update_time'] = date("Y-m-d H:i:s", time());

                $saveL = $this->users_model->where(array("id" => $id))->save($data);
                if ($saveL) {
                    if ($job_status_id_before) {
                        if ($job_status_id_rear && $job_status_id_rear != $job_status_id_before) {
                            $this->spec_label_model->where(array("object_id" => $id, "spec_info_id" => $job_status_id_before, "tablename" => "user"))->save(array("spec_info_id" => $job_status_id_rear));
                            $this->apiSuccess("操作成功！");
                        } else {
                            $this->apiSuccess("操作成功！");
                        }
                    } else {
                        $spec_label_add["object_id"] = $id;

                        $spec_label_add["spec_info_id"] = $job_status_id_rear;
                        $spec_label_add["tablename"] = "user";
                        $this->spec_label_model->add($spec_label_add);
                        $this->apiSuccess("操作成功！");
                    }
                } else {
                    $this->apiError('操作失败！');
                }
                break;//个人信息
            case 2://个人优势
                $resources = new ResourcesController();
                $id = $resources->remove_xss($resources->getId($dataL['unionid']));
                $data['advantage'] = I('advantage');
                $data['update_time'] = date("Y-m-d H:i:s", time());
                !$this->users_model->where(array("id" => $id))->save($data) ? $this->apiError('操作失败！') : $this->apiSuccess("操作成功！");
                break; //个人优势
            case 3://求职期望
                $resources = new ResourcesController();
                $id = $resources->remove_xss($resources->getId($dataL['unionid']));//微信id
                $resume_expect_id = I('resume_expect_id');//求职期望id
                $position_id = I('position_id');//职位id
                $city_id = I('city_id');//地区
                $salary_id = I('salary_id');
                $first_industry_id = I("first_industry_id"); //行业 一级id
                $second_industry_id = I("second_industry_id"); //行业 二级id


                if (!$id || !$position_id || !$city_id || !$first_industry_id || !$second_industry_id) {
                    $this->apiError('非法错误！请填写必填参数');
                }

                $resumeExpect['city_id'] = $city_id;
                $resumeExpect['update_time'] = date("Y-m-d H:i:s", time());
                $expectRes = $this->resume_expect_model->where(array("id" => $resume_expect_id))->save($resumeExpect);
                if ($expectRes) {
                    //直接更新label表，薪资，行业
                    $save_one = array(
                        "spec_info_id" => $salary_id,
                        "level" => array("eq", ""),
                    );

                    $where_one = array(
                        "object_id" => $resume_expect_id,
                        "tablename" => "resume_expect",
                    );

                    $res_one = $this->spec_label_model->where($where_one)->save($save_one);

                    $save_two = array(
                        "spec_info_id" => $first_industry_id,
                        "level" => $second_industry_id,
                    );

                    $where_two = array(
                        "object_id" => $resume_expect_id,
                        "tablename" => "resume_expect",
                        "level" => array("neq", ""),
                    );
                    $res_two = $this->spec_label_model->where($where_two)->save($save_two);
                    //职位
                    $save = array(
                        "position_id" => $position_id,
                    );
                    $where = array(
                        "object_id" => $resume_expect_id,
                        "tablename" => "resume_expect",
                    );
                    $res = $this->spec_select_position_model->where($where)->save($save);

                    if ($res_one !== false && $res_two !== false && $res !== false) {
                        $this->apiSuccess("操作成功！");
                    } else {
                        $this->apiError('操作失败！');
                    }
                } else {
                    $this->apiError('操作失败！');
                }

                break;//求职期望
            case 4://工作经历
                $resources = new ResourcesController();
                $id = $resources->remove_xss($resources->getId($dataL['unionid']));//用户id
                $resume_experience_id = I('resume_experience_id');//工作经历id
                $company_name = I('company_name');//公司名称
                $start_time = I('start_time');//开始时间
                $end_time = I('end_time');//结束时间
                $department = I('department');//部门
                $content = I('content');//工作内容
                $performance = I('performance');//工作业绩
                $position_name = I('position_name');//职位名称
                $first_industry_id = I('first_industry_id');//行业一级id
                $second_industry_id = I('second_industry_id');//行业二级id
                $position_id = I('position_id');//职位三级id
                if (!$id || !$resume_experience_id || !$company_name || !$start_time || !$end_time || !$department || !$content || !$performance || !$position_name || !$first_industry_id || !$second_industry_id || !$position_id) {
                    $this->apiError('非法错误！请填写必填参数');
                }

                $save_content = array(
                    "company_name" => $company_name,
                    "start_time" => $start_time,
                    "end_time" => $end_time,
                    "department" => $department,
                    "content" => $content,
                    "performance" => $performance,
                    "position_name" => $position_name,
                    "update_time" => date("Y-m-d H:i:s", time()),
                );

                $res = $this->resume_experience_model->where(array("id" => $resume_experience_id))->save($save_content);
                if ($res !== false) {
                    //修改行业标签
                    $industry_save = array(
                        "spec_info_id" => $first_industry_id,
                        "level" => $second_industry_id,
                    );
                    $industry_res = $this->spec_label_model->where(array("object_id" => $resume_experience_id, "tablename" => "resume_experience"))->save($industry_save);
                    //添加职位标签
                    $position_save = array(
                        "position_id" => $position_id,
                    );
                    $position_res = $this->spec_select_position_model->where(array("object_id" => $resume_experience_id, "tablename" => "resume_experience"))->save($position_save);

                    if ($industry_res !== false || $position_res !== false) {
                        $this->apiSuccess("工作经历修改成功！");
                    } else {
                        $this->apiError("工作经历修改失败！");
                    }
                } else {
                    $this->apiError("工作经历修改失败！");
                }

                break; //工作经历
            case 5://项目经历
                $resources = new ResourcesController();
                $id = $resources->remove_xss($resources->getId($dataL['unionid']));//用户id
                $resume_project_id = I('resume_project_id');//项目经历id
                $name = I('name');//项目名称
                $character = I('character');//担任角色
                $start_time = I('start_time');//项目开始时间
                $end_time = I('end_time');//项目结束时间
                $description = I('description');//项目描述
                $performance = I('performance');//项目业绩
                $url = I('url');//项目链接

                if (!$id || !$resume_project_id || !$name || !$character || !$start_time || !$end_time || !$description || !$performance || !$url) {
                    $this->apiError('非法错误！请填写必填参数');
                }
                $save_content = array(
                    "name" => $name,
                    "character" => $character,
                    "start_time" => $start_time,
                    "end_time" => $end_time,
                    "description" => $description,
                    "performance" => $performance,
                    "url" => $url,
                    "update_time" => date("Y-m-d H:i:s", time()),
                );

                $res = $this->resume_project_model->where(array("id" => $resume_project_id))->save($save_content);
                if ($res !== false) {
                    $this->apiSuccess("项目经历修改成功！");
                } else {
                    $this->apiError("项目经历修改失败！");
                }

                break; //项目经历
            case 6://教育经历
                $resources = new ResourcesController();
                $id = $resources->remove_xss($resources->getId($dataL['unionid']));//用户id
                $resume_education_id = I('resume_education_id');//教育经历id
                $school = I('school');//学校
                $education_id = I('education_id');//学历id
                $profession = I('profession');//专业
                $start_time = I('start_time');//开始时间针对年
                $end_time = I('end_time');//结束时间针对年
                $experience = I('experience');//在校经历

                if (!$id || !$resume_education_id || !$school || !$education_id || !$profession || !$start_time || !$end_time || !$experience) {
                    $this->apiError('非法请求！请填写必填参数');
                }


                $save_content = array(
                    "school" => $school,
                    "profession" => $profession,
                    "start_time" => $start_time,
                    "end_time" => $end_time,
                    "experience" => $experience,
                    "update_time" => date("Y-m-d H:i:s", time()),
                );
                $res = $this->resume_education_model->where(array("id" => $resume_education_id))->save($save_content);

                if ($res !== false) {
                    //修改行业标签
                    $industry_save = array(
                        "spec_info_id" => $education_id,
                    );
                    $education_res = $this->spec_label_model->where(array("object_id" => $resume_education_id, "tablename" => "resume_education"))->save($industry_save);
                    if ($education_res !== false) {
                        $this->apiSuccess("教育经历修改成功");
                    } else {
                        $this->apiError("教育经历修改失败");
                    }
                } else {
                    $this->apiError("教育经历修改失败");
                }
                break; //教育经历
            case 7://资质证书
                $resources = new ResourcesController();
                $id = $resources->remove_xss($resources->getId($dataL['unionid']));//用户id
                $resume_certificate_id = I('resume_certificate_id');//资质证书id
                $certificate = I('certificate');//证书
                if (!$id || !$certificate || !$resume_certificate_id) {
                    $this->apiError('非法错误！请填写必填参数');
                }

                $save_content = array(
                    'certificate' => $certificate,
                    'update_time' => date("Y-m-d H:i:s", time())
                );

                $res = $this->resume_certificate_model->where(array("id" => $resume_certificate_id))->save($save_content);

                if ($res !== false) {
                    $this->apiSuccess("资质证书修改成功！");
                } else {
                    $this->apiError("资质证书修改失败！");
                }
                break;//资质证书
            case 8://社交主页
                $resources = new ResourcesController();
                $id = $resources->remove_xss($resources->getId($dataL['unionid']));//用户id
                $resume_social_id = I('resume_social_id');//社交主页id
                $url = I('url');//主页链接
                if (!$id || !$url || !$resume_social_id) {
                    $this->apiError('非法错误！请填写必填参数');
                }

                $social_save = array(
                    'url' => $url,
                    'update_time' => date("Y-m-d H:i:s", time())
                );
                $res = $this->resume_social_model->where(array("id" => $resume_social_id))->save($social_save);
                if ($res !== false) {
                    $this->apiSuccess("社交主页修改成功！");
                } else {
                    $this->apiError("社交主页添修改失败！");
                }
                break; //社交主页

        }


    }

    /**
     * Notes: 简历删除
     * User: Sen
     * DateTime: 2020/7/6 16:27
     * Return:
     */
    public function resumePublicDel()
    {
        $dataL = $this->dataL;
        if (!$dataL['type']) {
            $this->apiError('非法错误！请填写必填参数');
        }
        $resources = new ResourcesController();
        $id = $resources->remove_xss($resources->getId($dataL['unionid']));//用户id
        if (empty($id)) {
            $this->apiError("非法请求！");
        }
        switch ($dataL['type']) {
            case 3://求职期望
                $resume_expect_id = I('resume_expect_id');//求职期望id

                $save_arr = array(
                    "isdel" => 1,
                    "update_time" => date("Y-m-d H:i:s", time()),
                );

                $res = $this->resume_expect_model->where(array("id" => $resume_expect_id))->save($save_arr);

                if ($res !== false) {
                    $this->apiSuccess("操作成功！");
                } else {
                    $this->apiError("操作失败！");
                }
                break;//求职期望
            case 4://工作经历

                $resume_experience_id = I('resume_experience_id');//工作经历id

                $save_arr = array(
                    "isdel" => 1,
                    "update_time" => date("Y-m-d H:i:s", time()),
                );

                $res = $this->resume_experience_model->where(array("id" => $resume_experience_id))->save($save_arr);

                if ($res !== false) {
                    $this->apiSuccess("操作成功！");
                } else {
                    $this->apiError("操作失败！");
                }


                break; //工作经历
            case 5://项目经历

                $resume_project_id = I('resume_project_id');//项目经历id

                $save_arr = array(
                    "isdel" => 1,
                    "update_time" => date("Y-m-d H:i:s", time()),
                );

                $res = $this->resume_project_model->where(array("id" => $resume_project_id))->save($save_arr);

                if ($res !== false) {
                    $this->apiSuccess("操作成功！");
                } else {
                    $this->apiError("操作失败！");
                }

                break; //项目经历
            case 6://教育经历

                $resume_education_id = I('resume_education_id');//教育经历id

                $save_arr = array(
                    "isdel" => 1,
                    "update_time" => date("Y-m-d H:i:s", time()),
                );

                $res = $this->resume_education_model->where(array("id" => $resume_education_id))->save($save_arr);

                if ($res !== false) {
                    $this->apiSuccess("操作成功！");
                } else {
                    $this->apiError("操作失败！");
                }

                break; //教育经历
            case 7://资质证书

                $resume_certificate_id = I('resume_certificate_id');//资质证书id

                $save_arr = array(
                    "isdel" => 1,
                    "update_time" => date("Y-m-d H:i:s", time()),
                );

                $res = $this->resume_certificate_model->where(array("id" => $resume_certificate_id))->save($save_arr);

                if ($res !== false) {
                    $this->apiSuccess("操作成功！");
                } else {
                    $this->apiError("操作失败！");
                }

                break;//资质证书
            case 8://社交主页
                $resume_social_id = I('resume_social_id');//社交主页id

                $save_arr = array(
                    "isdel" => 1,
                    "update_time" => date("Y-m-d H:i:s", time()),
                );

                $res = $this->resume_social_model->where(array("id" => $resume_social_id))->save($save_arr);

                if ($res !== false) {
                    $this->apiSuccess("操作成功！");
                } else {
                    $this->apiError("操作失败！");
                }

                break; //社交主页
        }
    }

    /**
     * Notes: 单个简历模块详情
     * User: Sen
     * DateTime: 2020/7/9 18:38
     * Return:
     */
    public function resumeIndividualDetails()
    {
        $dataL = $this->dataL;
        if (!$dataL['type']) {
            $this->apiError('非法错误！请填写必填参数');
        }
        $resources = new ResourcesController();
        $id = $resources->remove_xss($resources->getId($dataL['unionid']));//用户id
        if (empty($id)) {
            $this->apiError("非法请求！");
        }
        switch ($dataL['type']) {
            case 1://个人信息

                $userinfo = $this->users_model
                    ->where(array("id" => $id))//求职期望id
                    ->field("name,sex,birthday,we_chat,phone,email")
                    ->find();
                $userinfo["sex"] == 1 ? $userinfo["sex"] = "男" : $userinfo["sex"] == 2 ? $userinfo["sex"] = "女" : $userinfo["sex"] = "未知";

                $this->apiReturn(array("data" => $userinfo));
                break;//个人信息
            case 2://个人优势
                $data["data"] = $this->users_model
                    ->where(array("id" => $id))//求职期望id
                    ->field("advantage")
                    ->find();
                $this->apiReturn($data);
                break;//个人优势
            case 3://求职期望
                $resume_expect_id = I('resume_expect_id');//求职期望id
                $where = array(
                    "a.id" => $resume_expect_id,
                    "a.isdel" => 0,
                    "c.tablename" => "resume_expect",
                    "s.tablename" => "resume_expect",
                );
                $res = $this->resume_expect_model
                    ->alias("a")
//                    ->join("right join __CITY__ as b on b.id = a.city_id")
                    ->join("right join __SPEC_SELECT_POSITION__ as c on c.object_id = a.id")
                    ->join("right join __SPEC_POSITION__ as d on d.id = c.position_id")
                    ->join("right join __SPEC_LABEL__ as s on s.object_id = a.id")
                    ->join("LEFT JOIN __SPEC_INFO__ as s1 on s1.id = s.spec_info_id")
                    ->join("LEFT JOIN __SPEC_INDUSTRY__ as s3 on s3.id = s.level")
                    ->join("__SPEC__ as s2 on s2.id = s1.spec_id")
                    ->where($where)
                    ->order("s.level desc")
                    ->field("a.id,d.name as position_name,a.city_id,s1.spec_info_name,s3.spec_info_name industry_name,s2.rename,c.position_id,s.spec_info_id as first_industry_id,s.level as second_industry_id")
                    ->select();
//                print_r($res);die;
                foreach ($res as $item => $var) {
                    if (!empty($var['industry_name'])) {
                        $res[$item][$var['rename']] = $var['spec_info_name'] . "-" . $var["industry_name"];
                    } else {
                        $res[$item][$var['rename']] = $var['spec_info_name'];
                        $res[$item]["salary_id"] = $var['first_industry_id'];
                    }
                    unset($res[$item]["spec_info_name"]);
                    unset($res[$item]["industry_name"]);
                    unset($res[$item]["rename"]);
                }
                $data["data"] = $res[0] + $res[1];
                $this->apiReturn($data);
                break;//求职期望
            case 4://工作经历
                $resume_experience_id = I('resume_experience_id');//工作经历id

                $where = array(
                    "a.isdel" => 0,
                    "a.id" => $resume_experience_id,
                    "b.tablename" => "resume_experience",
                    "s.tablename" => "resume_experience",
                );

                $res = $this->resume_experience_model
                    ->alias("a")
                    ->join("right join __SPEC_SELECT_POSITION__ as b on b.object_id = a.id")
                    ->join("right join __SPEC_LABEL__ as s on s.object_id = a.id")
                    ->join("LEFT JOIN __SPEC_INFO__ as s1 on s1.id = s.spec_info_id")
                    ->join("LEFT JOIN __SPEC_INDUSTRY__ as s3 on s3.id = s.level")
                    ->join("__SPEC__ as s2 on s2.id = s1.spec_id")
                    ->where($where)
                    ->field("a.id,a.company_name,a.start_time,a.id,a.end_time,a.department,a.content,a.performance,a.position_name,s1.spec_info_name,s3.spec_info_name industry_name,s2.rename,s.spec_info_id first_industry_id,s.level second_industry_id,b.position_id")
                    ->find();

                $res['rename'] = $res['spec_info_name'];
                $res["industry_name"] = $res['spec_info_name'] . "-" . $res['industry_name'];
                unset($res["spec_info_name"]);
                unset($res["rename"]);
                unset($res["industry"]);

                $this->apiReturn(array("data" => $res));

                break; //工作经历
            case 5://项目经历
                $resume_project_id = I('resume_project_id');//项目经历id
                $where = array(
                    "id" => $resume_project_id,
                    "isdel" => 0,
                );

                $data["data"] = $this->resume_project_model
                    ->where($where)
                    ->field("id,name,character,start_time,end_time,description,performance,url,id")
                    ->find();

                $this->apiReturn($data);
                break; //项目经历
            case 6://教育经历
                $resume_education_id = I('resume_education_id');//教育经历id
                $where = array(
                    "a.id" => $resume_education_id,
                    "a.isdel" => 0,
                    "b.tablename" => "resume_education"
                );
                $data["data"] = $this->resume_education_model
                    ->alias("a")
                    ->join("right join __SPEC_LABEL__ as b on b.object_id = a.id")
                    ->join("right join __SPEC_INFO__ as c on c.id = b.spec_info_id")
                    ->join("right join __SPEC__ as d on d.id = c.spec_id")
                    ->where($where)
                    ->field("a.id,a.school,a.profession,a.start_time,a.end_time,a.experience,c.spec_info_name,d.rename,b.spec_info_id as education_id")
                    ->find();

                $data["data"]["rename"] = $data["data"]["spec_info_name"];
                unset($data["data"]["spec_info_name"]);
                unset($data["data"]["rename"]);

                $this->apiReturn($data);
                break; //教育经历
            case 7://资质证书
                $resume_certificate_id = I('resume_certificate_id');//资质证书id

                $where = array(
                    "id" => array("in", $resume_certificate_id),
                    "isdel" => 0
                );
                $data["data"] = $this->resume_certificate_model
                    ->where($where)
                    ->field("id,certificate")
                    ->find();
                $this->apiReturn($data);
                break;//资质证书
            case 8://社交主页
                $resume_social_id = I('resume_social_id');//项目经历id

                $where = array(
                    "id" => $resume_social_id,
                    "isdel" => 0
                );
                $data["data"] = $this->resume_social_model
                    ->where($where)
                    ->field("id,url")
                    ->find();
                $this->apiReturn($data);
                break; //社交主页
        }
    }

    /**
     * Notes: 线上简历信息
     * User: Sen
     * DateTime: 2020/7/7 18:45
     * Return:
     */
    public function myPresumeInformation()
    {
        $dataL = $this->dataL;
        $resources = new ResourcesController();
        $id = $resources->remove_xss($resources->getId($dataL['unionid']));//用户id
        if (empty($id)) {
            $this->apiError("非法请求！");
        }
        $data = $this->users_model
            ->where(array("id" => $id))
            ->field("avatar,name,identity_status,sex,participate_time,we_chat,birthday,advantage")
            ->find();

        //求职期望id
        $resume_expect_id = $this->resume_expect_model->where(array("uid" => $id, "isdel" => 0))->field("id")->select();
        $resume_expect_id = array_column($resume_expect_id, 'id');

        if (!array_values(array_filter($resume_expect_id))) {
            $data["resume_expect"] = "";
        } else {
            $expect_where = array(
                "a.id" => array("in", $resume_expect_id),
                "c.tablename" => "resume_expect",
                "s.tablename" => "resume_expect"
            );
            $expect_res = $this->resume_expect_model
                ->alias("a")
//            ->join("right join __CITY__ as b on b.id = a.city_id")
                ->join("right join __SPEC_SELECT_POSITION__ as c on c.object_id = a.id")
                ->join("right join __SPEC_POSITION__ as d on d.id = c.position_id")
                ->join("right join __SPEC_LABEL__ as s on s.object_id = a.id")
                ->join("LEFT JOIN __SPEC_INFO__ as s1 on s1.id = s.spec_info_id")
                ->join("LEFT JOIN __SPEC_INDUSTRY__ as s3 on s3.id = s.level")
                ->join("__SPEC__ as s2 on s2.id = s1.spec_id")
                ->where($expect_where)
                ->field("a.id,d.name as position_name,a.city_id,s1.spec_info_name,s3.spec_info_name industry_name,s2.rename")
                ->select();
            foreach ($expect_res as $item => $var) {
                if (!empty($var['industry_name'])) {
                    $expect_res[$item][$var['rename']] = $var['spec_info_name'] . "-" . $var["industry_name"];
                } else {
                    $expect_res[$item][$var['rename']] = $var['spec_info_name'];
                }
                unset($expect_res[$item]["spec_info_name"]);
                unset($expect_res[$item]["industry_name"]);
                unset($expect_res[$item]["rename"]);
            }
            $expect_ress = $resources->dataGroup($expect_res, "id");
            foreach ($expect_ress as $a => $b) {
                $data["resume_expect"][$a] = $b[0] + $b[1];
            }
            $data["resume_expect"] = array_values($data["resume_expect"]);
        }
        //工作经历id
        $resume_experience_id = $this->resume_experience_model->where(array("uid" => $id, "isdel" => 0))->field("id")->select();
        $resume_experience_id = array_column($resume_experience_id, 'id');
        if (!array_values(array_filter($resume_experience_id))) {
            $data["experience"] = "";
        } else {

            $experience_where = array(
                "a.id" => array("in", $resume_experience_id),
                "s.tablename" => "resume_experience",
            );


            $data["experience"] = $this->resume_experience_model
                ->alias("a")
                ->join("right join __SPEC_LABEL__ as s on s.object_id = a.id")
                ->join("LEFT JOIN __SPEC_INFO__ as s1 on s1.id = s.spec_info_id")
                ->join("LEFT JOIN __SPEC_INDUSTRY__ as s3 on s3.id = s.level")
                ->join("__SPEC__ as s2 on s2.id = s1.spec_id")
                ->where($experience_where)
                ->field("a.id,a.company_name,a.start_time,a.id,a.end_time,a.department,a.content,a.performance,a.position_name,s1.spec_info_name,s3.spec_info_name industry_name,s2.rename")
                ->select();

            foreach ($data["experience"] as $item => $var) {
                $data["experience"][$item][$var['rename']] = $var['spec_info_name'];
                $data["experience"][$item]["industry_name"] = $var['spec_info_name'] . "-" . $var['industry_name'];

                unset($data["experience"][$item]["spec_info_name"]);
                unset($data["experience"][$item]["rename"]);
                unset($data["experience"][$item]["industry"]);
            }
        }
        //项目经历id
        $resume_project_id = $this->resume_project_model->where(array("uid" => $id, "isdel" => 0))->field("id")->select();
        $resume_project_id = array_column($resume_project_id, 'id');
        if (!array_values(array_filter($resume_project_id))) {
            $data["project"] = "";
        } else {
            $project_where = array(
                "id" => array("in", $resume_project_id),
            );

            $data["project"] = $this->resume_project_model
                ->where($project_where)
                ->field("id,name,character,start_time,end_time,description,performance,url,id")
                ->select();

        }
        //教育经历id
        $resume_education_id = $this->resume_education_model->where(array("uid" => $id, "isdel" => 0))->field("id")->select();
        $resume_education_id = array_column($resume_education_id, 'id');
        if (!array_values(array_filter($resume_education_id))) {
            $data["education"] = "";
        } else {
            $education_where = array(
                "a.id" => array("in", $resume_education_id),
                "b.tablename" => "resume_education"
            );
            $data["education"] = $this->resume_education_model
                ->alias("a")
                ->join("right join __SPEC_LABEL__ as b on b.object_id = a.id")
                ->join("right join __SPEC_INFO__ as c on c.id = b.spec_info_id")
                ->join("right join __SPEC__ as d on d.id = c.spec_id")
                ->where($education_where)
                ->field("a.id,a.school,a.profession,a.start_time,a.end_time,a.experience,c.spec_info_name,d.rename")
                ->select();
            foreach ($data["education"] as $k => $v) {
                $data["education"][$k][$v["rename"]] = $v["spec_info_name"];
                unset($data["education"][$k]["spec_info_name"]);
                unset($data["education"][$k]["rename"]);
            }
        }
        //资格证书id
        $resume_certificate_id = $this->resume_certificate_model->where(array("uid" => $id, "isdel" => 0))->field("id")->select();
        $resume_certificate_id = array_column($resume_certificate_id, 'id');
        if (!array_values(array_filter($resume_certificate_id))) {
            $data["certificate"] = "";
        } else {
            $where = array(
                "id" => array("in", $resume_certificate_id),
            );
            $data["certificate"] = $this->resume_certificate_model
                ->where($where)
                ->field("id,certificate")
                ->select();
        }
        //社交主页id
        $resume_social_id = $this->resume_social_model->where(array("uid" => $id, "isdel" => 0))->field("id")->select();
        $resume_social_id = array_column($resume_social_id, 'id');
        if (!array_values(array_filter($resume_social_id))) {
            $data["social"] = "";
        } else {
            $where = array(
                "id" => array("in", $resume_social_id),
            );
            $data["social"] = $this->resume_social_model
                ->where($where)
                ->field("id,url")
                ->select();
        }
        $this->apiReturn(array("data" => $data));

    }

    /**
     * Notes: 线上简历信息
     * User: Sen
     * DateTime: 2020/7/7 18:45
     * Return:
     */
    public function presumeInformation()
    {
        $id = I('id');//用户id
        if (empty($id)) {
            $this->apiError("非法请求！");
        }
        $data = $this->users_model
            ->where(array("id" => $id))
            ->field("avatar,name,identity_status,sex,participate_time,we_chat,birthday,advantage")
            ->find();

        //求职期望id
        $resume_expect_id = $this->resume_expect_model->where(array("uid" => $id, "isdel" => 0))->field("id")->select();
        $resume_expect_id = array_column($resume_expect_id, 'id');

        if (!array_values(array_filter($resume_expect_id))) {
            $data["resume_expect"] = "";
        } else {
            $expect_where = array(
                "a.id" => array("in", $resume_expect_id),
                "c.tablename" => "resume_expect",
                "s.tablename" => "resume_expect"
            );
            $expect_res = $this->resume_expect_model
                ->alias("a")
                //            ->join("right join __CITY__ as b on b.id = a.city_id")
                ->join("right join __SPEC_SELECT_POSITION__ as c on c.object_id = a.id")
                ->join("right join __SPEC_POSITION__ as d on d.id = c.position_id")
                ->join("right join __SPEC_LABEL__ as s on s.object_id = a.id")
                ->join("LEFT JOIN __SPEC_INFO__ as s1 on s1.id = s.spec_info_id")
                ->join("LEFT JOIN __SPEC_INDUSTRY__ as s3 on s3.id = s.level")
                ->join("__SPEC__ as s2 on s2.id = s1.spec_id")
                ->where($expect_where)
                ->field("a.id,d.name as position_name,a.city_id,s1.spec_info_name,s3.spec_info_name industry_name,s2.rename")
                ->select();
            foreach ($expect_res as $item => $var) {
                if (!empty($var['industry_name'])) {
                    $expect_res[$item][$var['rename']] = $var['spec_info_name'] . "-" . $var["industry_name"];
                } else {
                    $expect_res[$item][$var['rename']] = $var['spec_info_name'];
                }
                unset($expect_res[$item]["spec_info_name"]);
                unset($expect_res[$item]["industry_name"]);
                unset($expect_res[$item]["rename"]);
            }
            $expect_ress = dataGroup($expect_res, "id");
            foreach ($expect_ress as $a => $b) {
                $data["resume_expect"][$a] = $b[0] + $b[1];
            }
            $data["resume_expect"] = array_values($data["resume_expect"]);
        }
        //工作经历id
        $resume_experience_id = $this->resume_experience_model->where(array("uid" => $id, "isdel" => 0))->field("id")->select();
        $resume_experience_id = array_column($resume_experience_id, 'id');
        if (!array_values(array_filter($resume_experience_id))) {
            $data["experience"] = "";
        } else {

            $experience_where = array(
                "a.id" => array("in", $resume_experience_id),
                "s.tablename" => "resume_experience",
            );


            $data["experience"] = $this->resume_experience_model
                ->alias("a")
                ->join("right join __SPEC_LABEL__ as s on s.object_id = a.id")
                ->join("LEFT JOIN __SPEC_INFO__ as s1 on s1.id = s.spec_info_id")
                ->join("LEFT JOIN __SPEC_INDUSTRY__ as s3 on s3.id = s.level")
                ->join("__SPEC__ as s2 on s2.id = s1.spec_id")
                ->where($experience_where)
                ->field("a.id,a.company_name,a.start_time,a.id,a.end_time,a.department,a.content,a.performance,a.position_name,s1.spec_info_name,s3.spec_info_name industry_name,s2.rename")
                ->select();

            foreach ($data["experience"] as $item => $var) {
                $data["experience"][$item][$var['rename']] = $var['spec_info_name'];
                $data["experience"][$item]["industry_name"] = $var['spec_info_name'] . "-" . $var['industry_name'];

                unset($data["experience"][$item]["spec_info_name"]);
                unset($data["experience"][$item]["rename"]);
                unset($data["experience"][$item]["industry"]);
            }
        }
        //项目经历id
        $resume_project_id = $this->resume_project_model->where(array("uid" => $id, "isdel" => 0))->field("id")->select();
        $resume_project_id = array_column($resume_project_id, 'id');
        if (!array_values(array_filter($resume_project_id))) {
            $data["project"] = "";
        } else {
            $project_where = array(
                "id" => array("in", $resume_project_id),
            );

            $data["project"] = $this->resume_project_model
                ->where($project_where)
                ->field("id,name,character,start_time,end_time,description,performance,url,id")
                ->select();

        }
        //教育经历id
        $resume_education_id = $this->resume_education_model->where(array("uid" => $id, "isdel" => 0))->field("id")->select();
        $resume_education_id = array_column($resume_education_id, 'id');
        if (!array_values(array_filter($resume_education_id))) {
            $data["education"] = "";
        } else {
            $education_where = array(
                "a.id" => array("in", $resume_education_id),
                "b.tablename" => "resume_education"
            );
            $data["education"] = $this->resume_education_model
                ->alias("a")
                ->join("right join __SPEC_LABEL__ as b on b.object_id = a.id")
                ->join("right join __SPEC_INFO__ as c on c.id = b.spec_info_id")
                ->join("right join __SPEC__ as d on d.id = c.spec_id")
                ->where($education_where)
                ->field("a.id,a.school,a.profession,a.start_time,a.end_time,a.experience,c.spec_info_name,d.rename")
                ->select();
            foreach ($data["education"] as $k => $v) {
                $data["education"][$k][$v["rename"]] = $v["spec_info_name"];
                unset($data["education"][$k]["spec_info_name"]);
                unset($data["education"][$k]["rename"]);
            }
        }
        //资格证书id
        $resume_certificate_id = $this->resume_certificate_model->where(array("uid" => $id, "isdel" => 0))->field("id")->select();
        $resume_certificate_id = array_column($resume_certificate_id, 'id');
        if (!array_values(array_filter($resume_certificate_id))) {
            $data["certificate"] = "";
        } else {
            $where = array(
                "id" => array("in", $resume_certificate_id),
            );
            $data["certificate"] = $this->resume_certificate_model
                ->where($where)
                ->field("id,certificate")
                ->select();
        }
        //社交主页id
        $resume_social_id = $this->resume_social_model->where(array("uid" => $id, "isdel" => 0))->field("id")->select();
        $resume_social_id = array_column($resume_social_id, 'id');
        if (!array_values(array_filter($resume_social_id))) {
            $data["social"] = "";
        } else {
            $where = array(
                "id" => array("in", $resume_social_id),
            );
            $data["social"] = $this->resume_social_model
                ->where($where)
                ->field("id,url")
                ->select();
        }
        $this->apiReturn(array("data" => $data));

    }

    /**
     * Notes: 招聘默认信息列表
     * User: Sen
     * DateTime: 2020/7/9 18:08
     * Return:
     */
    public function greetInfoList()
    {
        $dataL = $this->dataL;
        $resources = new ResourcesController();
        $unionid = I('unionid');
        $type = I('type'); //1:boss 2:求职者
        if (empty($unionid) || empty($type)) {
            $this->apiError("非法请求！");
        }

        $id = $resources->getId($unionid);

        $where1 = array("uid" => 0, "type" => $type);
        $where2 = array("uid" => $id, "type" => $type);

        $where['_complex'] = array(
            $where1,
            $where2,
            '_logic' => 'or'
        );
        $res = $this->job_greet_model
            ->where($where)
            ->field("id,content")
            ->select();

        $this->apiReturn(array("data" => $res));
    }

    /**
     * Notes: 默认信息添加
     * User: Sen
     * DateTime: 2020/7/9 18:30
     * Return:
     */
    public function greetInfoAdd()
    {
        $dataL = $this->dataL;
        $resources = new ResourcesController();
        $unionid = I('unionid');
        $type = I('type'); //1:boss 2:求职者
        $content = I('content'); //内容
        if (empty($unionid) || empty($type)) {
            $this->apiError("非法请求！");
        }

        $id = $resources->getId($unionid);

        $add = array(
            "content" => $content,
            "uid" => $id,
            "time" => date("Y-m-d H:i:s", time()),
            "tablename" => "recruitment",
        );

        $res = $this->job_greet_model->add($add);

        if ($res) {
            $this->apiSuccess("操作成功！");
        } else {
            $this->apiSuccess("操作失败！");
        }
    }

    /**
     * Notes:个人名片
     * User: Sen
     * DateTime: 2020/7/14 13:54
     * Return:
     */
    public function personalBusinessCard()
    {

        $unionid = I('unionid');
        $name_n = I('name_n');//用户名
        $position = I('position');//职务
        $email = I('email');//邮箱
        $phone = I('phone');//手机
        $education = I('education');//教育
        $introduce = I("introduce");//自我介绍
        $company = I('company');//个人名片公司
        if (empty($unionid) || empty($name_n) || empty($position) || empty($email) || empty($phone) || empty($education) || empty($introduce)) {
            $this->apiError("非法请求！");
        }
        $User = D('User');
        $uid = $User->getIdByUnionid($unionid);

        $user_save = array(
            "name_n" => $name_n,
            "position" => $position,
            "email" => $email,
            "phone" => $phone,
            "education" => $education,
            "introduce" => $introduce,
        );
        $user_res = $this->users_model->where(array("id" => $uid))->save($user_save);
        if ($user_res !== false) {
            if (empty($company)) {
                $this->apiSuccess("操作成功！");
            } else {
                //修改,添加公司
                foreach ($company as $k => $val) {
                    if (empty($val["id"])) {//添加操作
                        //查询之多三个公司
                        $res = $this->user_company_model->where(array("uid" => $uid, "isdel" => 0))->select();
                        if (sizeof($res) == 3) {
                            $this->apiError("至多添加三家公司,超出三家自动移除！");
                        }
                        $add = array(
                            "uid" => $uid,
                            "time" => date("Y-m-d H:i:s", time()),
                            "name" => $val["name"],
                            "address" => $val["address"],
                            "type" => $val["type"],
                            "business" => $val["business"],
                            "introduction" => $val["introduction"],
                        );
                        //添加数据
                        $this->user_company_model->add($add);
                    } else {//修改操作
                        $save = array(
                            "name" => $val["name"],
                            "address" => $val["address"],
                            "type" => $val["type"],
                            "business" => $val["business"],
                            "introduction" => $val["introduction"],
                        );
                        //修改数据
                        $this->user_company_model->where(array("id" => $val["id"]))->save($save);
                    }
                }
                $this->apiSuccess("操作成功！");
            }
        } else {
            $this->apiError("操作失败！");
        }
    }

    /**
     * Notes: 我的名片详情
     * User: Sen
     * DateTime: 2020/7/15 17:40
     * Return:
     */
    public function mypersonalBusinessCardDetail()
    {
        $unionid = I('unionid');
        if (empty($unionid)) {
            $this->apiError("非法请求!");
        }
        $User = D('User');
        $uid = $User->getIdByUnionid($unionid);
        $where = array(
            "u.id" => $uid
        );

        $data = $User
            ->alias("u")
            ->join("left join __USER_COMPANY__ as uc on u.id = uc.uid and uc.isdel = 0")
            ->where($where)
            ->field("u.name_n,u.position,u.email,u.phone,u.education,u.introduce,uc.id,uc.name,uc.address,uc.type,uc.business,uc.introduction")
            ->select();

        $detail["data"] = array();
        if (empty($data)) {
            $this->apiReturn($detail);
        }
        foreach ($data as $k => $v) {
            $detail["data"]["name_n"] = $v["name_n"];
            $detail["data"]["position"] = $v["position"];
            $detail["data"]["email"] = $v["email"];
            $detail["data"]["phone"] = $v["phone"];
            $detail["data"]["education"] = $v["education"];
            $detail["data"]["introduce"] = $v["introduce"];
            $detail["data"]["company"][$k] = $v;

            unset($detail["data"]["company"][$k]["name_n"]);
            unset($detail["data"]["company"][$k]["position"]);
            unset($detail["data"]["company"][$k]["email"]);
            unset($detail["data"]["company"][$k]["phone"]);
            unset($detail["data"]["company"][$k]["education"]);
            unset($detail["data"]["company"][$k]["introduce"]);
        }

        $this->apiReturn($detail);
    }

    /**
     * Notes: 个人名片详情
     * User: Sen
     * DateTime: 2020/7/15 17:40
     * Return:
     */
    public function personalBusinessCardDetail()
    {
        $unionid = I('unionid');
        $uid = I('uid');
        if (empty($uid)) {
            $this->apiError("非法请求!");
        }
        $User = D('User');
        $myuid = $User->getIdByUnionid($unionid);
        $status = D('Buddy')->where(['uid'=>$myuid,'object_id'=>$uid])->getField('status');
        $where = array(
            "u.id" => $uid
        );

        $data = $User
            ->alias("u")
            ->join("left join __USER_COMPANY__ as uc on u.id = uc.uid and uc.isdel = 0")
            ->where($where)
            ->field("u.name_n,u.position,u.email,u.phone,u.education,u.introduce,uc.id,uc.name,uc.address,uc.type,uc.business,uc.introduction")
            ->select();

        $detail["data"] = array();
        if (empty($data)) {
            $this->apiReturn($detail);
        }
        foreach ($data as $k => $v) {
            $detail["data"]["name_n"] = $v["name_n"];
            $detail["data"]["position"] = $v["position"];
            $detail["data"]["email"] = $v["email"];
            $detail["data"]["phone"] = $v["phone"];
            $detail["data"]["education"] = $v["education"];
            $detail["data"]["introduce"] = $v["introduce"];
            $detail["data"]["status"] = !is_null($status)?$status:-1;
            $detail["data"]["company"][$k] = $v;

            unset($detail["data"]["company"][$k]["name_n"]);
            unset($detail["data"]["company"][$k]["position"]);
            unset($detail["data"]["company"][$k]["email"]);
            unset($detail["data"]["company"][$k]["phone"]);
            unset($detail["data"]["company"][$k]["education"]);
            unset($detail["data"]["company"][$k]["introduce"]);
        }

        $this->apiReturn($detail);
    }

    /**
     * Notes: 个人名片公司删除
     * User: Sen
     * DateTime: 2020/7/14 16:38
     * Return:
     */
    public function personalBusinessCardCompanyDel()
    {
        $unionid = I('unionid');
        $id = I('id');//个人名片公司id

        if (empty($unionid) || empty($id)) {
            $this->apiError("非法请求!");
        }
        $User = D('User');
        $uid = $User->getIdByUnionid($unionid);
        $where = array(
            "uid" => $uid,
            "id" => $id,
        );
        $res = $this->user_company_model->where($where)->save(array("isdel" => 1));

        if ($res !== false) {
            $this->apiSuccess("操作成功！");
        } else {
            $this->apiError("操作失败！");
        }
    }

    /**
     * Notes: 添加学生认证（证件未接收）
     * User: Sen
     * DateTime: 2020/7/15 18:06
     * Return:
     */
    public function studentCertificationAdd()
    {
        $unionid = I('unionid');
        $name = I('name');//姓名
        $school = I('school');//学校
        $department = I('department');//院系
        $major = I('major');//专业
        $entrance_time = I('entrance_time');//入学年份
        $class = I('class');//班级
        $number = I('number');//学号
        if (empty($unionid) || empty($name) || empty($school) || empty($department) || empty($major) || empty($entrance_time) || empty($class) || empty($number)) {
            $this->apiError("非法请求！");
        }
        $User = D('User');
        $uid = $User->getIdByUnionid($unionid);

        $School = M('School');
        $school_id = $School->where(['school_name' => $school])->getField('id');
        if (empty($school_id)){
            $data = [
                'school_name' => $school,
                'introduction' => "暂无内容",
                'time' => time(),
                'state' => 0,
                'uid' => $uid
            ];
            $school_id = $School->add($data);
        }
        $add = array(
            "name" => $name,
            "school_id" => $school_id?$school_id:0,
            "school" => $school,
            "department" => $department,
            "major" => $major,
            "entrance_time" => $entrance_time,
            "class" => $class,
            "number" => $number,
            "uid" => $uid,
            "is_auth" => 0,
            "time" => date("Y-m-d H:i:s", time()),
        );

        $user_student_authen_id = $this->user_student_authen_model->add($add);

        if ($user_student_authen_id) {
            $this->apiSuccess("操作成功！");
        } else {
            $this->apiError("操作失败！");
        }
    }

}