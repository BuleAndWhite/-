<?php
/**
 * Created by PhpStorm
 * Name: 校友树洞-吐槽
 * User: 刘北林
 * Date: 2020-06-23
 * Time: 16:01
 */

namespace Api\Controller;


class MakeComplaintsController extends AppController
{
    protected $make_complaints_model;//校园树洞吐槽表
    protected $reply_model;//评论表

    function __construct()
    {
        header("Access-Control-Allow-Origin: *");
        header("Content-Type: text/html; charset=UTF-8");
        parent::_initialize();
        $this->make_complaints_model = M('MakeComplaints');
        $this->reply_model = M('Reply');

    }

    /***
     * Notes:校园树洞 吐槽列表 需要修改  加评论数 点赞数
     * User: 刘北林
     * DateTime: 2020-06-23 16:05
     * Return :吐槽列表  评论列表
     */
    public function makeList()
    {

//        $dataL = $this->dataL;
//        $page = $dataL['page'] ? $dataL['page'] : 1;//每页条数
//        $page_num = $dataL['page_num'] ? $dataL['page_num'] : 1;//页码

        $nPage  = intval(I('page'));
        $nLimit = intval(I('page_num'));

        if($nPage <= 0){
            $page = 8;
        }
        if($nLimit <= 0){
            $page_num = 1;
        }

//        $resources = new ResourcesController();

        $count = $this->make_complaints_model
            ->alias("m")
            ->join("__USER__ u ON m.uid = u.id")
            ->count();
        $pagination = page($count, $page, $page_num);
        $makeList = $this->make_complaints_model
            ->alias("m")
            ->join("__USER__ u ON m.uid = u.id")
            ->limit($pagination['start_num'], $pagination["num"])
            ->order("m.create_time desc")
            ->field("m.id,m.uid,m.content,m.praise_num,m.click_num,m.reply_num,m.create_time,m.update_time,u.name,u.avatar")
            ->select();

//		foreach ($makeList as $key => $val) {
//			$makeList[$key]['nested'] = $this->reply_model
//				->alias("r")
//				->join("__USER__ u ON r.uid = u.id")
//				->field("r.content,u.name,u.avatar")
//				->where(array("r.object_id" => $val['id'], "r.tablename" => "make_complaints"))
//				->select();
//		}

        $makeList = $this->getSubTree($makeList, $pk = 'parent_id', $pid = 'id', $child = 0); //自己封装的无限极方法 放在了function.php


        $returnList["data"] = array(
            "current_page" => $pagination["current_page"],
            "total_page" => $pagination["total_page"] ? $pagination["total_page"] : 1,
            "recruitmentList" => $makeList
        );

        $makeList ? $this->apiReturn($returnList) : $this->apiError('空数据');
    }


    /***
     * Notes:校园树洞 吐槽单个
     * User: 刘北林
     * DateTime: 2020-06-23 16:05
     * Return :吐槽单个 评论列表
     */
    public function makeOne()
    {
//        $dataL = $this->dataL;
//        $make_id = $dataL['make_id'] ? $dataL['make_id'] : 2;//帖子id

        $make_id  = intval(I('make_id'));

        if($make_id <= 0){
           return $this->apiError('缺少参数');
        }

        $res = $this->make_complaints_model
            ->alias("m")
            ->join("__USER__ u ON m.uid = u.id")
            ->where(array("m.id" => $make_id))
            ->order("m.create_time desc")
            ->field("m.id,m.uid,m.content,m.praise_num,m.click_num,m.reply_num,m.create_time,m.update_time,u.name,u.avatar")
            ->find();

        $res['reply'] = M("reply")
            ->alias("r")
            ->join("__USER__ u ON r.uid = u.id")
            ->where(array("r.object_id" => $make_id, "r.tablename" => "make_complaints"))
            ->order("r.create_time desc")
            ->field("r.*,u.name username,u.avatar")
            ->select();

        $res['reply'] = $this->getSubTree($res['reply'], $pk = 'parent_id', $pid = 'id', $child = 0); //自己封装的无限极方法

        $this->apiReturn(array("data" => $res));
    }

    /**
     * @param $data array  数据
     * @param $parent  string 父级元素的名称 如 parent_id
     * @param $son     string 子级元素的名称 如 comm_id
     * @param $pid     int    父级元素的id 实际上传递元素的主键
     * @return array
     */
    public function getSubTree($data, $parent, $son, $pid = 0)
    {
        $tmp = array();
        foreach ($data as $key => $value) {
            if ($value[$parent] == $pid) {
                $value['child'] = $this->getSubTree($data, $parent, $son, $value[$son]);
                $tmp[] = $value;
            }
        }
        return $tmp;
    }

    /***
     * Notes:校园树洞 吐槽单个
     * User: 刘北林
     * DateTime: 2020-06-23 16:05
     * Return :吐槽单个 评论列表
     */
    public function makeOne1()
    {
//        $dataL = $this->dataL;
//        $make_id = $dataL['make_id'];
//        if (!$make_id) {
//            $this->apiError('非法错误，缺少参数！');
//        }
        $make_id  = intval(I('make_id'));

        if($make_id <= 0){
            return $this->apiError('缺少参数');
        }

        $makeList = $this->make_complaints_model
            ->alias("m")
            ->join("__USER__ u ON m.uid = u.id")
            ->where(array("m.id" => $make_id))
            ->order("m.ishot,m.create_time desc")
            ->field("m.id,m.uid,m.content,m.praise_num,m.click_num,m.reply_num,m.create_time,m.update_time,u.name,u.avatar")
            ->find();

        $makeList['nested'] = $this->reply_model
            ->alias("r")
            ->join("__USER__ u ON r.uid = u.id")
            ->field("r.content,u.name,u.avatar")
            ->where(array("r.object_id" => $makeList['id'], "r.tablename" => "make_complaints"))
            ->select();

        $returnList['data'] = $makeList;
        $makeList ? $this->apiReturn($returnList) : $this->apiError('空数据');
    }

    /***
     * Notes:添加吐槽
     * User: 刘北林
     * DateTime: 2020-06-23 16:51
     * Return :
     */
    public function addMake()
    {
//        $dataL = $this->dataL;
//        $resources = new ResourcesController();
//        $data['uid'] = $resources->remove_xss($resources->getId($dataL['unionid']));
//        $data['content'] = $resources->remove_xss($dataL['content']);
//        $data['type'] = $resources->remove_xss($dataL['type']);

        //验证用户信息
        $nUnionid = trim(I('unionid'));
        $sContent = trim(I('content'));
        $nType    = intval(I('type'));

        $objUser  = D('User');
        list($nErr , $nUserId) = $objUser->getUserIdByUnionId($nUnionid);
        if($nErr != 1){
            return $this->apiError($nUserId);
        }

        if(strlen($sContent) <= 0){
            return $this->apiError('内容不能为空');
        }

        if($nType <= 0){
            $nType = 0;
        }

        $data            = [];
        $data['uid']     = $nUserId;
        $data['content'] = $sContent;
        $data['type']    = $nType;
        $data['create_time'] = time();

        if (!$data['uid'] || !$data['content'] || !$data['type']) {
            $this->apiError('非法错误，缺少参数！');
        }
        $res = $this->make_complaints_model->add($data);
        if ($res) {
            $this->apiSuccess("添加成功！");
        } else {
            $this->apiError('添加失败，服务器异常！');
        }
    }


}