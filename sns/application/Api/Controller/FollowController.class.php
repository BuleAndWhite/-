<?php
/**
 * Created by PhpStorm.
 * User: 仙瑜005
 * Date: 2020/6/29
 * Time: 10:52
 */

namespace Api\Controller;

/**
 * Notes: 好友类
 * User: Sen
 * DateTime: 2020/6/29 10:54
 */
class FollowController extends AppController
{

    function __construct()
    {
        header("Access-Control-Allow-Origin: *");
        header("Content-Type: text/html; charset=UTF-8");
        parent::_initialize();

    }


    /**
     * Notes: 关注用户
     * User: Sen
     * DateTime: 2020/6/30 9:53
     * Return:
     */
    public function followAdd()
    {
        $User = D('User');
        $Follow = M("Follow");

        $unionid = I("unionid");
        $object_id = I("object_id");
        $type = I("type");

        if (empty($unionid) || empty($object_id) || empty($type)) {
            $this->apiError("非法请求！");
        }

        $uid = $User->getIdByUnionid($unionid);


        if ($uid == $object_id) {
            $this->apiError("无效操作！");
        }

        $where['uid'] = $uid;
        $where['object_id'] = $object_id;
        $where['tablename'] = $type;

        //添加关系前 先查询关系
        $followInfo = $Follow->where($where)->field('id, is_attention')->find();

        if (empty($followInfo)) {
            $add = array(
                "uid" => $uid,
                "object_id" => $object_id,
                "create_time" => date("Y-m-d H:i:s", time()),
                "tablename" => $type,
                'is_attention' => 1
            );

            $follow = $Follow->add($add);

            if ($follow) {
                $this->apiSuccess("关注成功!");
            } else {
                $this->apiError("关注失败，请重试!");
            }
        } else{
            if ($followInfo['is_attention'] == 1) {
                $this->apiError("您已关注该用户!");
            }else if ($followInfo['is_attention'] == 0){

                $data = array(
                    "is_attention" => 1
                );

                $res = $Follow->where(array("id" => $followInfo["id"]))->save($data);
                if ($res !== false) {
                    $this->apiSuccess("关注成功!");
                }
            }
        }
    }

    /**
     * 获取用户的关注状态
     */
    public function getStatus(){
        $User = D('User');
        $Follow = M("Follow");

        $unionid = I("unionid");
        $object_id = I("object_id");
        $type = I("type");

        if (empty($unionid) || empty($object_id) || empty($type)) {
            $this->apiError("非法请求！");
        }

        $uid = $User->getIdByUnionid($unionid);

        $where['uid'] = $uid;
        $where['object_id'] = $object_id;
        $where['tablename'] = $type;
        $follow = $Follow->where($where)->field('is_attention')->find();

        $this->apiReturn(array("data" => $follow));
    }

    /**
     * Notes: 我的关注
     * User: Sen
     * DateTime: 2020/6/30 10:39
     * Return:
     */
    public function myFollow()
    {
        $unionid = I('unionid');
        $type = I("type", "user");

        if (empty($unionid) ) {
            $this->apiError("非法请求！");
        }
        $User = D('User');
        $Follow = M("Follow");
        $uid = $User->getIdByUnionid($unionid);

        $where['uid'] = $uid;
        $where['tablename'] = $type;
        $where['is_attention'] = 1;
        $follow_list = $Follow
            ->alias('f')
            ->join("left join __USER__ as u on f.object_id = u.id")
            ->where($where)
            ->field('f.id,f.object_id as follow_uid,u.name,u.avatar')
            ->select();

        $this->apiReturn(array("data" => $follow_list));
    }

    /**
     * Notes: 我的关注
     * User: Sen
     * DateTime: 2020/6/30 10:39
     * Return:
     */
    public function myFans()
    {
        $User = D('User');
        $Follow = M("Follow");
        $unionid = I('unionid');
        $type = I("type", "user");

        if (empty($unionid) ) {
            $this->apiError("非法请求！");
        }

        $uid = $User->getIdByUnionid($unionid);

        $where['object_id'] = $uid;
        $where['tablename'] = $type;
        $where['is_attention'] = 1;
        //获取用户的粉丝id列表
        $fans_ids = $Follow->where($where)->getField('uid',true);

        $map_user_follow = [];
        if(count($fans_ids) > 0){
            $where_status['object_id'] = ['in',$fans_ids];
            $where_status['uid'] = $uid;
            $where_status['tablename'] = $type;
            $status_list = $Follow->where($where_status)->field('object_id,is_attention')->select();
            array_map(function ($v) use (&$map_user_follow) {
                $map_user_follow[$v['object_id']] = $v['is_attention'];
            }, $status_list);
        }

        $follow_list = $Follow
            ->alias('f')
            ->join("left join __USER__ as u on f.uid = u.id")
            ->where($where)
            ->field('f.id,f.uid as fans_uid,u.name,u.avatar')
            ->select();
        foreach ($follow_list as $k => $v){
            $follow_list[$k]['attention_status'] = $map_user_follow[$v['fans_uid']]?$map_user_follow[$v['fans_uid']] : 0;
        }
        $this->apiReturn(array("data" => $follow_list));
    }

    /**
     * 取消关注
     */
    public function cancel(){
        $User = D('User');
        $Follow = M("Follow");

        $unionid = I("unionid");
        $object_id = I("object_id");
        $type = I("type");

        if (empty($unionid) || empty($object_id) || empty($type)) {
            $this->apiError("非法请求！");
        }

        $uid = $User->getIdByUnionid($unionid);

        $where['uid'] = $uid;
        $where['object_id'] = $object_id;
        $where['tablename'] = $type;
        $follow = $Follow->where($where)->field('id,is_attention')->find();
        if (empty($follow)){
            $this->apiError("您还没有关注该用户！");
        }
        if ($follow['is_attention'] == 0){
            $this->apiError("您已经取消关注了！");
        }else{
            $save['is_attention'] = 0;
            $res = $Follow->where(array("id" => $follow["id"]))->save($save);

            if ($res == 1) {
                $this->apiSuccess("取消关注成功");
            }else{
                $this->apiError("网络错误！");
            }
        }
    }
}