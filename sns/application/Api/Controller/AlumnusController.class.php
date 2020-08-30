<?php
/**
 * Created by PhpStorm
 * Name:校友圈
 * User: Blief
 * Date: 2020-06-27
 * Time: 15:07
 */

namespace Api\Controller;


class AlumnusController extends AppController
{

    protected $reply_model;//评论表

    function __construct()
    {
        header("Access-Control-Allow-Origin: *");
        header("Content-Type: text/html; charset=UTF-8");
        parent::_initialize();
    }


    /***
     * Notes:创建学校
     * User: Belief
     * DateTime: 2020-06-27 15:13
     * Return :String 成功 失败
     */
    public function addSchool()
    {
        $data['uid'] = D("User")->getIdByUnionid(I("unionid"));
        $data['school_name'] = I('school_name');
        $data['introduction'] = I('introduction');
        $data['type'] = I('type');
        $data['logo'] = I('logo');
        $data['img'] = I('img');
        $data['time'] = time();

        if (!$data['uid'] || !$data['school_name'] || !$data['introduction']) {
            $this->apiError('非法错误，缺少参数！');
        }

        $res = D("School")->add($data);
        if ($res) {
            $this->apiSuccess("添加成功！");
        } else {
            $this->apiError('添加失败，服务器异常！');
        }
    }

    /***
     * Notes:学校列表
     * User: Belief
     * DateTime: 2020-06-27 15:13
     * Return :array（学校列表）
     */
    public function schoolList()
    {
        $dataL = $this->dataL;
        $page = $dataL['page'] ? $dataL['page'] : 1;//每页条数
        $page_num = $dataL['page_num'] ? $dataL['page_num'] : 1;//页码

        $resources = new ResourcesController();
        $count = M('School')
            ->alias("s")
            ->join("__USER__ u ON s.uid = u.id")
            ->count();
        $pagination = $resources->page($count, $page, $page_num);
        $makeList = M('School')
            ->alias("s")
            ->join("__USER__ u ON s.uid = u.id")
            ->limit($pagination['start_num'], $pagination["num"])
            ->order("s.ishot,s.time desc")
            ->field("s.*,u.name,u.avatar")
            ->select();


        $returnList["data"] = array(
            "current_page" => $pagination["current_page"],
            "total_page" => $pagination["total_page"] ? $pagination["total_page"] : 1,
            "recruitmentList" => $makeList
        );

        $makeList ? $this->apiReturn($returnList) : $this->apiError('空数据');
    }

    /***
     * Notes:创建班级  生成教室的座位
     * User: Belief
     * DateTime: 2020-06-27 15:13
     * Return :String 成功 失败
     */
    public function addGrade()
    {
        $file_path = I("file_path"); //公司图片（数组
        $unionid = I('unionid');
        if (empty($unionid)){
            $this->apiError('非法请求！');
        }
        $data['uid'] = D('User')->getIdByUnionId($unionid);
        $data['school_id'] = I('school_id');
        $data['name'] = I('name');
        $data['num'] = I('num');
        $data['grade'] = I('grade');
        $data['major'] = I('major');
        $data['introduction'] = I('introduction');
        $data['time'] = time();

        if (!$data['uid'] || !$data['name'] || !$data['num']) {
            $this->apiError('非法错误，缺少参数！');
        }
        //将班级注册聊天频道
        $gradeChatGroupId = M('userTalkGroup')->add([
            'user_list'=>$data['uid'],
            'status'=>5,
            'ext'=>'班级频道',
            'update_time'=>time(),
            'create_time'=>time()
        ]);
        $data['group_id'] = $gradeChatGroupId;
        $res = M('Grade')->add($data);
        if ($res) {
            if ($file_path) {
                $sof_attachment_add["object_id"] = $res;
                $sof_attachment_add["tablename"] = "grade";
                $sof_attachment_add["uid"] = $data['uid'];
                $sof_attachment_add["create_time"] = date("Y-m-d H:i:s", time());
                $sof_attachment_add["url"] = $file_path["path"];
                $sof_attachment_add["size"] = $file_path["size"];
                $sof_attachment_add["type"] = $file_path["type"];
                M('SofAttachment')->add($sof_attachment_add);
            }
            for ($i = 1; $i <= $data['num']; $i++) {
                $sect['grade_id'] = $res;
                $sect['numbering'] = createNoncestr(8);
                $sect['serial'] = $i;
                $sect['time'] = time();
                $sect['seat_is_seated'] = $i == 1 ? 1 : 0;
                if ($i == 1) {
                    $sect['uid'] =  $data['uid'];
                }
                $grade_seat_id = M('GradeSeat')->add($sect);
                if ($i == 1) {
                    $grade_join['grade_id'] = $res;
                    $grade_join['seat_id'] = $grade_seat_id;
                    $grade_join['uid'] = $data['uid'];
                    $grade_join['time'] = time();
                    M('GradeJoin')->add($grade_join);
                }
            }
            $this->apiSuccess("添加成功！");
        } else {
            $this->apiError('添加失败，服务器异常！');
        }
    }

    /***
     * Notes:班级列表 我创建得班级
     * User: Belief
     * DateTime: 2020-06-27 15:13
     * Return :array（列表）
     */
    public function gradeList()
    {
        $unionid = I("unionid");
        $page = I('page',1);//页数
        $page_num =  I('page_num',8);//每页条数


        if (empty($unionid)) {
            $this->apiError("非法错误！");
        }
        $uid =  D("User")->getIdByUnionid($unionid);

        $count = M('Grade')->where(array("uid"=>$uid))->count();
        $makeList = M('Grade')
            ->alias("g")
            ->join("__USER__ u ON g.uid = u.id")
            ->where(array("uid"=>$uid))
            ->page($page, $page_num)
            ->order("g.time desc")
            ->field("g.*,u.name username,u.avatar")
            ->select();

        $total_page = $count % $page_num == 0 ? ($count / $page_num) : ceil($count / $page_num);
        $returnList["data"] = array(
            "current_page" => $page,
            "total_page" => $total_page,
            "recruitmentList" => $makeList
        );

        $makeList ? $this->apiReturn($returnList) : $this->apiError('空数据');
    }

    /***
     * Notes:班级列表 我加入得班级
     * User: Belief
     * DateTime: 2020年7月21日09:41:54
     * Return :array（列表）
     */
    public function gradeJoinList()
    {
        $page = I('page',1);//每页条数
        $page_num =  I('page_num',8);

        $unionid = I("unionid");
        if (empty($unionid)) {
            $this->apiError("非法请求！");
        }

        $uid =  D("User")->getIdByUnionid();
        $count = M('GradeJoin')->where(array("uid"=>$uid))->count();
        $makeList = M('GradeJoin')
            ->alias("gj")
            ->join("__GRADE__ g ON g.id = gj.grade_id")
            ->join("__USER__ u ON u.id = g.uid")
            ->where(array("gj.uid"=>$uid))
            ->page($page,$page_num)
            ->order("g.time desc")
            ->field("g.*,u.name username,u.avatar")
            ->select();

        $total_page = $count % $page_num == 0 ? ($count / $page_num) : ceil($count / $page_num);
        $returnList["data"] = array(
            "current_page" => $page,
            "total_page" => $total_page,
            "recruitmentList" => $makeList
        );

        $makeList ? $this->apiReturn($returnList) : $this->apiError('空数据');
    }

    /***
     * Notes:班级详情 座位展示
     * Created by
     * User: Belise
     * DateTime: 2020-07-02 12:36
     * Return :array() 班级信息座位信息 是否被占位
     */
    public function gradeDetails()
    {
        $dataL = $this->dataL;
        $grade_id = I('grade_id');

        if (!$grade_id) {
            $this->apiError('非法请求，缺少参数！');
        }

        $seatList['data'] = M('Grade')
            ->alias("g")
            ->join("__USER__ u ON u.id = g.uid")
            ->where(array("g.id" => $grade_id))
            ->field("g.*,u.name as username,u.avatar")
            ->find();

        $seatList['data']["time"] = date('Y-m-s h:i:s', $seatList['data']["time"]);
        $seatList['data']['seat'] = M('GradeSeat')
            ->alias("g")
            ->join("LEFT JOIN __USER__ u ON u.id = g.uid")
            ->where(array("grade_id" => $seatList['data']['id']))
            ->field("g.id seat_id,g.seat_is_seated,g.uid g_id,u.name g_name,u.avatar g_avatar")
            ->select();

        $seatList['data'] ? $this->apiReturn($seatList) : $this->apiError('空数据');

    }

    /***
     * Notes:加入班级
     * Created by
     * User: Belies
     * DateTime: 2020-07-02 14:12
     * Return :string 成功 失败
     */
    public function gradeJoin()
    {
        $resources = new ResourcesController();
        $dataL = $this->dataL;
        $grade_id = $dataL['grade_id'];
        $unionid = $dataL['unionid'];
        if (!$grade_id || !$unionid) {
            $this->apiError('非法请求，缺少参数！');
        }
        M('GradeJoin')->where(array("grade_id" => $grade_id))->count();
        $id = $resources->getId($unionid);

        $gradeOne = M('Grade')->where(array("uid" => $id, "id" => $grade_id))->find();
        !$gradeOne ?: $this->apiError('您是群主,请不要重复加入!');
        $is_join = M('GradeJoin')->where(array("uid" => $id, "grade_id" => $grade_id))->find();

        $is_join ? $this->apiError('您已加入，请不要重复加入！') :
            $grade_join['grade_id'] = $grade_id;
        $grade_join['uid'] = $id;
        $grade_join['time'] = time();
        M('GradeJoin')->add($grade_join);
        $this->apiSuccess("加入成功！");

    }

    /***
     * Notes:占座位
     * Created by
     * User: Belies
     * DateTime: 2020-07-02 14:12
     * Return :string 成功 失败
     */
    public function seatJoin()
    {
        $resources = new ResourcesController();
        $dataL = $this->dataL;
        $seat_id = $dataL['seat_id'];
        $grade_id = $dataL['grade_id'];
        $unionid = $dataL['unionid'];
        if (!$seat_id || !$unionid || !$grade_id) {
            $this->apiError('非法请求，缺少参数！');
        }

        $id = $resources->getId($unionid);

        $gradeOne = M('Grade')->where(array("uid" => $id, "grade_id" => $grade_id))->find();
        !$gradeOne ?: $this->apiError('您是群主,请不要重复加入!');
        $is_join = M('GradeJoin')->where(array("seat_id" => $seat_id, "grade_id" => $grade_id))->find();

        $is_join ? $this->apiError('此座位被占用！请选其他座位') :
            $grade_join['seat_id'] = $seat_id;
        M('GradeJoin')->where(array("uid" => $id, "grade_id" => $grade_id))->save($grade_join);
        $seat_join['seat_is_seated'] = 1;
        M('GradeSeat')->where(array("id" => $seat_id))->save($seat_join);
        $this->apiSuccess("已入座！");

    }
}