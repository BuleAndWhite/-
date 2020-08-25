<?php
/**
 * Created by PhpStorm.
 * User: 仙瑜005
 * Date: 2020/7/15
 * Time: 11:21
 */

namespace Api\Controller;


class AlumniCircleController extends AppController
{
    protected $users_model;
    protected $reply_model;//评论表

    function __construct()
    {
        header("Access-Control-Allow-Origin: *");
        header("Content-Type: text/html; charset=UTF-8");
        parent::_initialize();
    }

    /**
     * Notes: 发布动态
     * User: Sen
     * DateTime: 2020/7/15 11:23
     * Return:
     */
    public function postNews()
    {
        $unionid = I('unionid');//微信id
        $content = I('content');//内容
        $type = I('type');//类型1：动态2：约会3：契约4：帮帮
        $media = I("media");
        $suid=I('suid');

        if (empty($unionid) || (empty($content) && empty($media))) {
            $this->apiError("非法请求！");
        }
        $User = D('User');
        $uid = $User->getIdByUnionid($unionid);
        $suid =implode(',',$suid);
        $sof_add = array(
            "uid" => $uid, "content" => json_encode($content,true), "type" => $type,"suid"=>$suid, "create_time" => date("Y-m-d H:i:s", time()),
        );
        //将数据插入朋友圈表
        $sof_id =M('Sof')->add($sof_add);

        if (empty($sof_id)) {
            $this->apiError("操作失败！");
        }
        //资源,媒资是否为空
        if (!empty($media)) {
            //路径存储。关联起来
            foreach ($media as $k => $v) {
                $sof_attachment = array(
                    "uid" => $uid,
                    "object_id" => $sof_id,
                    "tablename" => "sof",
                    "create_time" => date("Y-m-d H:i:s", time()),
                    "url" => $v["path"],
                    "size" => $v["size"],
                    "type" => 2,//文件
                    "title" => $v["filename"],
                );
                M('SofAttachment')->add($sof_attachment);
            }
            $this->apiSuccess("操作成功！");
        }
        $this->apiSuccess("操作成功！");
    }

    /**
     * Notes: 我的发布(1：动态2：约会3：契约4：帮帮)
     * User: Sen
     * DateTime: 2020/7/15 19:26
     * Return:
     */
    public function myDynamicList()
    {
        $unionid = I('unionid');//微信id
        $type = I('type');//类型1：动态2：约会3：契约4：帮帮
        $page = I('page', 1);//页码
        $page_num = I('page_num', 8);//每页条数


        if (empty($unionid) || empty($type)) {
            $this->apiError("非法请求！");
        }

        $User = D('User');
        $uid = $User->getIdByUnionid($unionid);

        $where = array(
            "s.type" => $type, "s.isdel" => 0, "s.uid" => $uid,
        );
        $count =M('Sof')->alias("s")->where($where)->count();
        $dynamic_list =M('Sof')->alias("s")->join("right join __USER__ as u on u.id = s.uid")->where($where)->field("s.id,s.suid,u.avatar,s.uid,s.create_time,u.name,s.content,s.praise_num,s.click_num,s.reply_num,s.transfer_num,s.type")->page($page, $page_num)->order("s.create_time desc")->select();
        //获取用户@的好友的数据

         $where = [
            'id' => ['in',$dynamic_list['suid']]
         ];
        $suid_name= $User->where($where)->getField('name,id',true);
        unset($where);
        foreach ($dynamic_list as $k => $v) {
            //点赞
            $undigg = M('Like')->where(array("object_id" => $v["id"], "tablename" => "sof", "uid" => $uid))->field("undigg")->find();
            if ($undigg) {
                $dynamic_list[$k]["undigg"] = $undigg["undigg"];
            } else {
                $dynamic_list[$k]["undigg"] = 0;
            }

            //内容过滤
            if(!empty($v['content'])){
                $dynamic_list[$k]["content"] = json_decode($v['content']);
            }

            //图片，视频
            $dynamic_list[$k]["List"] = M('SofAttachment')->where(array(
                "object_id" => $v["id"], "tablename" => "sof"
            ))->field("url")->select();
            //将@的好友的信息放进$dynamic_list数组里面
            foreach ($suid_name as $key => $value) {
            	$dynamic_list[$k]["suid_name"]=$value;
            }
        }
        $total_page = $count % $page_num == 0 ? ($count / $page_num) : ceil($count / $page_num);
        $returnList["data"] = array(
            "total" => $count,
            "current_page" => $page,
            "total_page" => $total_page,
            "dynamic_list" => $dynamic_list
        );
        $dynamic_list ? $this->apiReturn($returnList) : $this->apiError('空数据');
    }

    /**
     * Notes: 我的发布(1：动态2：约会3：契约4：帮帮)
     * User: Sen
     * DateTime: 2020/7/15 19:26
     * Return:
     */
    public function doSearch()
    {
        $content = I('content');//微信id
        $type = I('type');//类型1：动态2：约会3：契约4：帮帮
        $page = I('page', 1);//页码
        $page_num = I('page_num', 8);//每页条数


        if (empty($content) || empty($type)) {
            $this->apiError("非法请求！");
        }

        $sContent = "";
        if(!empty($content)){
            $sContent = str_replace('"','',json_encode($content));
            $sContent = str_replace("\\",'_',$sContent);
        }

        $where = array(
            "s.type" => $type, "s.isdel" => 0, "s.content" => ['like','%'.$sContent.'%'],
        );
        $count =M('Sof')->alias("s")->where($where)->count();
        $dynamic_list =M('Sof')->alias("s")->join("right join __USER__ as u on u.id = s.uid")->where($where)->field("s.id,u.avatar,s.uid,s.create_time,u.name,s.content,s.praise_num,s.click_num,s.reply_num,s.transfer_num,s.type")->page($page, $page_num)->order("s.create_time desc")->select();
        foreach ($dynamic_list as $k => $v) {
            //点赞
            $undigg = M('Like')->where(array("object_id" => $v["id"], "tablename" => "sof", "uid" => $uid))->field("undigg")->find();
            if ($undigg) {
                $dynamic_list[$k]["undigg"] = $undigg["undigg"];
            } else {
                $dynamic_list[$k]["undigg"] = 0;
            }

            //内容过滤
            if(!empty($v['content'])){
                $sContent = json_decode($v['content']);
                $dynamic_list[$k]["content"] = $sContent;
            }

            //图片，视频
            $dynamic_list[$k]["List"] = M('SofAttachment')->where(array("object_id" => $v["id"], "tablename" => "sof"))->field("url")->select();
        }
        $total_page = $count % $page_num == 0 ? ($count / $page_num) : ceil($count / $page_num);
        $returnList["data"] = array(
            "total" => $count,
            "current_page" => $page,
            "total_page" => $total_page,
            "dynamic_list" => $dynamic_list
        );
        $dynamic_list ? $this->apiReturn($returnList) : $this->apiError('空数据');
    }

    /**
     * Notes: 推荐动态
     * User: Sen
     * DateTime: 2020/7/16 11:38
     * Return:
     */
    public function dynamicRecommend()
    {
        //获取用户输入参数
        $unionid = I('unionid');//微信id
        $type = I('type');//类型1：动态2：约会3：契约4：帮帮
        $ishot = I('ishot',1);//是否推荐
        $page = I('page', 1);//页码
        $page_num = I('page_num', 8);//每页条数
        //判断必填参数
        if (empty($unionid) || empty($type)) {
            $this->apiError("非法请求！");
        }
        //根据微信id获取用户id
        $User = D('User');
        $uid = $User->getIdByUnionid($unionid);
        //获取分页信息和动态列表
        $where = array(
            "s.type" => $type,
            "s.isdel" => 0,
        );
        $ishot == 1 && $where['ishot'] = $ishot;
        $count =M('Sof')->alias("s")->where($where)->count();
        if ($count == 0){
            $this->apiError('空数据');
        }
        $dynamic_list =M('Sof')->alias("s")->join("right join __USER__ as u on u.id = s.uid")->where($where)->field("s.id,u.avatar,s.uid,s.create_time,s.transfer_id,s.transfer_sid,s.suid,u.name,s.content,s.praise_num,s.click_num,s.reply_num,s.transfer_num,s.type")->page($page, $page_num)->order("s.create_time desc")->select();
        $dynamic_user_list = array_unique(array_column($dynamic_list, 'uid'));
        
        //获取用户@的好友的数据
         $where =
            'id' => ['in',$dynamic_list['suid']]
         ];
        $suid_name= $User->where($where)->getField('name,id',true);
        unset($where);
        //获取用户关注状态
        $where['uid'] = $uid;
        $where['object_id'] = ['in',$dynamic_user_list];
        $dynamic_user_follow_status_list = M('Follow')->where($where)->field('object_id,is_attention')->select();
        $map_user_follow = [];
        if (!empty($dynamic_user_follow_status_list)){
            //生成map user attention_status映射关系
            array_map(function ($v) use (&$map_user_follow) {
                $map_user_follow[$v['object_id']] = $v['is_attention'];
            }, $dynamic_user_follow_status_list);
        }

        //获取动态转发关系
        $dynamic_transfer_sids = array_filter(array_unique(array_column($dynamic_list, 'transfer_sid')));
        if (!empty($dynamic_transfer_sids))
        {
            //获取原创列表
            $orginal_list = M('Sof')->alias("s")->join("right join __USER__ as u on u.id = s.uid")->where(['s.id'=>['in',$dynamic_transfer_sids]])->field("s.id,u.avatar,s.uid,s.create_time,u.name,s.content,s.praise_num,s.click_num,s.reply_num,s.type")->select();
            //生成map映射
            $map_dynamic_transfer = [];
            array_map(function ($v) use (&$map_dynamic_transfer) {

                $map_dynamic_transfer[$v['id']] = $v;
                $map_dynamic_transfer[$v['id']]['content'] = json_decode($v['content'],true);
                unset($map_dynamic_transfer[$v['id']]['id']);
            }, $orginal_list);

            //获取原创动态附件列表
            $orginal_attachement_list = M('SofAttachment')->where(array("object_id" => ['in', $dynamic_transfer_sids], "tablename" => "sof"))->field("object_id,url")->select();
            if (!empty($orginal_attachement_list)) {
                $map_dynamic_attachment = [];
                array_map(function ($v) use (&$map_dynamic_attachment) {

                    $map_dynamic_attachment[$v['object_id']][] = $v['url'];
                }, $orginal_attachement_list);
            }
        }

        foreach ($dynamic_list as $k => $v) {
            $dynamic_list[$k]['attention_status'] = $map_user_follow[$v['uid']] ? $map_user_follow[$v['uid']] : 0;
            //点赞
            $undigg = M('Like')->where(array("object_id" => $v["id"], "tablename" => "sof", "uid" => $uid))->field("undigg")->find();
            if ($undigg) {
                $dynamic_list[$k]["undigg"] = $undigg["undigg"];
            } else {
                $dynamic_list[$k]["undigg"] = 0;
            }

            //内容过滤
            if(!empty($v['content'])){
                $dynamic_list[$k]["content"] = json_decode($v['content']);
            }

            //图片，视频
            $dynamic_list[$k]["List"] = M('SofAttachment')->where(array("object_id" => $v["id"], "tablename" => "sof"))->field("url")->select();

            //转发
            if ($v['transfer_sid']){
                $dynamic_list[$k]["transfer"] = $map_dynamic_transfer[$v['transfer_sid']];
                if (!empty($map_dynamic_attachment[$v['transfer_sid']][0])){
                    $dynamic_list[$k]["transfer"]['url'] = $map_dynamic_attachment[$v['transfer_sid']][0];
                }
            }
            //用户的@的好友信息
            foreach ($suid_name as $key => $value) {
            	$dynamic_list[$k]["suid_name"]=$value;
            }
        }
        $total_page = $count % $page_num == 0 ? ($count / $page_num) : ceil($count / $page_num);
        $returnList["data"] = array(
            "total" => $count,
            "current_page" => $page,
            "total_page" => $total_page,
            "dynamic_list" => $dynamic_list
        );
        $dynamic_list ? $this->apiReturn($returnList) : $this->apiError('空数据');
    }

    /**
     * Notes: 好友动态
     * User: Sen
     * DateTime: 2020/7/16 14:05
     * Return:
     */
    public function dynamicBuddy()
    {

        $unionid = I('unionid');//微信id
        $type = I('type');//类型1：动态2：约会3：契约4：帮帮
        $page = I('page',1);//页码
        $page_num = I('page_num', 8);//每页条数

        if (empty($unionid || empty($type))) {
            $this->apiError("非法请求！");
        }

        $User = D('User');
        $uid = $User->getIdByUnionid($unionid);
        //获取好友id
        $where_buddy = array(
            "uid" => $uid, "status" => 1,
        );

        $buddy_list = M('Buddy')->where($where_buddy)->getField("object_id", true);
        if (empty($buddy_list)) {
            $this->apiReturn(array("data" => ""));
        }

        $where = array(
            "s.type" => $type, "s.isdel" => 0, "s.uid" => array("in", $buddy_list)
        );
        $count =M('Sof')->alias("s")->where($where)->count();
        if ($count == 0){
            $this->apiError('空数据');
        }
        $dynamic_list =M('Sof')->alias("s")->join("right join __USER__ as u on u.id = s.uid")->where($where)->field("s.id,u.avatar,s.uid,s.suid,s.create_time,u.name,s.content,s.praise_num,s.click_num,s.reply_num,s.transfer_num,s.type,s.transfer_sid")->page($page, $page_num)->order("s.create_time desc")->select();

        
        //获取用户@的好友的数据
         $where =
            'id' => ['in',$dynamic_list['suid']]
         ];
        $suid_name= $User->where($where)->getField('name,id',true);
        unset($where);

        $dynamic_transfer_sids = array_filter(array_unique(array_column($dynamic_list, 'transfer_sid')));
        if (!empty($dynamic_transfer_sids))
        {
            //获取原创列表
            $orginal_list = M('Sof')->alias("s")->join("right join __USER__ as u on u.id = s.uid")->where(['s.id'=>['in',$dynamic_transfer_sids]])->field("s.id,u.avatar,s.uid,s.create_time,u.name,s.content,s.praise_num,s.click_num,s.reply_num,s.type")->select();
            //生成map映射
            $map_dynamic_transfer = [];
            array_map(function ($v) use (&$map_dynamic_transfer) {

                $map_dynamic_transfer[$v['id']] = $v;
                unset($map_dynamic_transfer[$v['id']]['id']);
            }, $orginal_list);

            //获取原创动态附件列表
            $orginal_attachement_list = M('SofAttachment')->where(array("object_id" => ['in', $dynamic_transfer_sids], "tablename" => "sof"))->field("object_id,url")->select();
            if (!empty($orginal_attachement_list)) {
                $map_dynamic_attachment = [];
                array_map(function ($v) use (&$map_dynamic_attachment) {

                    $map_dynamic_attachment[$v['object_id']][] = $v['url'];
                }, $orginal_attachement_list);
            }
        }

        foreach ($dynamic_list as $k => $v) {
            //点赞
            $undigg = M('Like')->where(array(
                "object_id" => $v["id"], "tablename" => "sof", "uid" => $uid
            ))->field("undigg")->find();
            if ($undigg) {
                $dynamic_list[$k]["undigg"] = $undigg["undigg"];
            } else {
                $dynamic_list[$k]["undigg"] = 0;
            }

            //内容过滤
            if(!empty($v['content'])){
                $sContent = json_decode($v['content']);
                $dynamic_list[$k]["content"] = $sContent;
            }

            //图片，视频
            $dynamic_list[$k]["List"] = M('SofAttachment')->where(array(
                "object_id" => $v["id"], "tablename" => "sof"
            ))->field("url")->select();

            //转发
            if ($v['transfer_sid']){
                $dynamic_list[$k]["transfer"] = $map_dynamic_transfer[$v['transfer_sid']];
                if (!empty($map_dynamic_attachment[$v['transfer_sid']][0])){
                    $dynamic_list[$k]["transfer"]['url'] = $map_dynamic_attachment[$v['transfer_sid']][0];
                }
            }
            //用户的@的好友的信息数据
            foreach ($suid_name as $key => $value) {
            	$dynamic_list[$k]["suid_name"]=$value;
            }
        }

        $total_page = $count % $page_num == 0 ? ($count / $page_num) : ceil($count / $page_num);
        $returnList["data"] = array(
            "total" => $count,
            "current_page" => $page,
            "total_page" => $total_page,
            "dynamic_list" => $dynamic_list
        );
        $this->apiReturn($returnList);
    }

    /**
     * Notes: 关注发布
     * User: Sen
     * DateTime: 2020/7/16 14:05
     * Return:
     */
    public function dynamicFollow()
    {

        $unionid = I('unionid');//微信id
        $type = I('type');//类型1：动态2：约会3：契约4：帮帮
        $page = I('page',1);//页码
        $page_num = I('page_num', 8);//每页条数

        if (empty($unionid || empty($type))) {
            $this->apiError("非法请求！");
        }

        $User = D('User');
        $uid = $User->getIdByUnionid($unionid);
        //获取好友id
        $where_follow = array(
            "uid" => $uid, "is_attention" => 1,
        );

        $follow_list = M('Follow')->where($where_follow)->getField("object_id", true);
        if (empty($follow_list)) {
            $this->apiReturn(array("data" => ""));
        }

        $where = array(
            "s.type" => $type, "s.isdel" => 0, "s.uid" => array("in", $follow_list)
        );
        $count =M('Sof')->alias("s")->where($where)->count();
        if ($count == 0){
            $this->apiError('空数据');
        }
        $dynamic_list =M('Sof')->alias("s")->join("right join __USER__ as u on u.id = s.uid")->where($where)->field("s.id,u.avatar,s.uid,s.suid,s.create_time,u.name,s.content,s.praise_num,s.click_num,s.reply_num,s.transfer_num,s.type,s.transfer_sid")->page($page, $page_num)->order("s.create_time desc")->select();

        $dynamic_transfer_sids = array_filter(array_unique(array_column($dynamic_list, 'transfer_sid')));
         
        //获取用户@的好友的数据
         $where =
            'id' => ['in',$dynamic_list['suid']]
         ];
        $suid_name= $User->where($where)->getField('name,id',true);
        unset($where);
        if (!empty($dynamic_transfer_sids))
        {
            //获取原创列表
            $orginal_list = M('Sof')->alias("s")->join("right join __USER__ as u on u.id = s.uid")->where(['s.id'=>['in',$dynamic_transfer_sids]])->field("s.id,u.avatar,s.uid,s.create_time,u.name,s.content,s.praise_num,s.click_num,s.reply_num,s.type")->select();
            //生成map映射
            $map_dynamic_transfer = [];
            array_map(function ($v) use (&$map_dynamic_transfer) {

                $map_dynamic_transfer[$v['id']] = $v;
                unset($map_dynamic_transfer[$v['id']]['id']);
            }, $orginal_list);

            //获取原创动态附件列表
            $orginal_attachement_list = M('SofAttachment')->where(array("object_id" => ['in', $dynamic_transfer_sids], "tablename" => "sof"))->field("object_id,url")->select();
            if (!empty($orginal_attachement_list)) {
                $map_dynamic_attachment = [];
                array_map(function ($v) use (&$map_dynamic_attachment) {

                    $map_dynamic_attachment[$v['object_id']][] = $v['url'];
                }, $orginal_attachement_list);
            }
        }

        foreach ($dynamic_list as $k => $v) {
            //点赞
            $undigg = M('Like')->where(array(
                "object_id" => $v["id"], "tablename" => "sof", "uid" => $uid
            ))->field("undigg")->find();
            if ($undigg) {
                $dynamic_list[$k]["undigg"] = $undigg["undigg"];
            } else {
                $dynamic_list[$k]["undigg"] = 0;
            }

            //内容过滤
            if(!empty($v['content'])){
                $dynamic_list[$k]["content"] = json_decode($v['content']);
            }

            //图片，视频
            $dynamic_list[$k]["List"] = M('SofAttachment')->where(array(
                "object_id" => $v["id"], "tablename" => "sof"
            ))->field("url")->select();

            //转发
            if ($v['transfer_sid']){
                $dynamic_list[$k]["transfer"] = $map_dynamic_transfer[$v['transfer_sid']];
                if (!empty($map_dynamic_attachment[$v['transfer_sid']][0])){
                    $dynamic_list[$k]["transfer"]['url'] = $map_dynamic_attachment[$v['transfer_sid']][0];
                }
            }
            //用户@的好友的信息
            foreach ($suid_name as $key => $value){
            	$dynamic_list[$k]["suid_name"]=$value;
            }
        }

        $total_page = $count % $page_num == 0 ? ($count / $page_num) : ceil($count / $page_num);
        $returnList["data"] = array(
            "total" => $count,
            "current_page" => $page,
            "total_page" => $total_page,
            "dynamic_list" => $dynamic_list
        );
        $this->apiReturn($returnList);
    }

    /**
     * Notes: 动态点赞
     * User: Sen
     * DateTime: 2020/7/16 14:30
     * Return:
     */
    public function giveLike()
    {
        $unionid = I('unionid');//微信id
        $sid = I('sid');//动态id
        $status = I('status');//是否取消点赞1否 0是
        $type = I("type", "sof");

        if (empty($unionid || empty($status))) {
            $this->apiError("非法请求！");
        }
        $User = D('User');
        $uid = $User->getIdByUnionid($unionid);

        $wheres = array(
            "object_id" => $sid, "tablename" => $type, "uid" => $uid,
        );
        $likeOne = M('Like')->where($wheres)->find();
        if ($likeOne) {
            if ($likeOne['undigg'] == $status) {
                $this->apiError("请勿重复操作");
            }
        }
        if ($status == 0) {//取消点赞

            $sof_res =M('Sof')->where(array("id" => $sid))->setDec("praise_num");
            //将关系插入点赞关系表
            $save = array(
                "undigg" => $status,
            );

            $where = array(
                "object_id" => $sid, "tablename" => "sof", "uid" => $uid,
            );

            $like_res = M('Like')->where($where)->save($save);

            if ($sof_res !== false && $like_res !== false) {

                $this->apiSuccess("操作成功！");
            } else {

                $this->apiError("操作失败！");
            }
        } else {
            $sof_res =M('Sof')->where(array("id" => $sid))->setInc("praise_num");

            $where_relationship = array(
                "object_id" => $sid, "tablename" => "sof", "uid" => $uid,
            );

            $like_relationship = M('Like')->where($where_relationship)->find();

            if (empty($like_relationship)) {//关系不存在 添加

                $like_add = array(
                    "object_id" => $sid,
                    "tablename" => "sof",
                    "uid" => $uid,
                    "create_time" => date("Y-m-d H:i:s", time()),
                    "is_read" => 0,
                    "undigg" => $status,
                );

                $like_res = M('Like')->add($like_add);

                if ($sof_res !== false && $like_res) {

                    $this->apiSuccess("操作成功！");
                } else {

                    $this->apiError("操作失败！");
                }
            } else {//关系已存在 修改
                $like_save = array(

                    "undigg" => $status,
                );
                $like_where = array(
                    "object_id" => $sid,
                    "tablename" => "sof",
                    "uid" => $uid,
                );

                $like_res = M('Like')->where($like_where)->save($like_save);

                if ($sof_res !== false && $like_res !== false) {

                    $this->apiSuccess("操作成功！");
                } else {
                    $this->apiError("操作失败！");
                }
            }
        }
    }

    /**
     * Notes: 动态详情
     * User: Sen
     * DateTime: 2020/7/16 15:51
     * Return:
     */
    public function dynamicDetails()
    {
        $unionid = I('unionid');//微信id
        $sid = I('sid');//动态id
        $type = I("type", "sof");//发布类型，默认为sof：动态

        if (empty($unionid) || empty($sid)) {
            $this->apiError("非法请求！");
        }
        $User = D('User');
        $uid = $User->getIdByUnionid($unionid);
        $where = array(
            "s.isdel" => 0, "s.id" => $sid,
        );

        $dynamic_detail =M('Sof')->alias("s")->join("right join __USER__ as u on u.id = s.uid")->where($where)->field("s.id as sid,s.uid,s.create_time,s.suid,u.name,u.avatar,s.content,s.click_num,s.reply_num,s.type,s.transfer_num,s.transfer_id,s.transfer_sid")->order("s.create_time desc")->find();
        //点赞
        $undigg = M('Like')->where(array("object_id" => $dynamic_detail["sid"], "tablename" => $type, "uid" => $uid))->field("undigg")->find();
        if ($undigg) {
            $dynamic_detail["undigg"] = $undigg["undigg"];
        } else {
            $dynamic_detail["undigg"] = 0;
        }
        //内容过滤
        if(!empty($dynamic_detail['content'])){
            $dynamic_detail["content"] =  json_decode($dynamic_detail['content']);
        }

        //图片，视频
        $dynamic_detail["List"] = M('SofAttachment')->where(array(
            "object_id" => $dynamic_detail["sid"], "tablename" => $type
        ))->field("url")->select();

        //获取用户@的好友的数据
        $where = [
            'id' => ['in',$dynamic_detail['suid']]
        ];
        unset($where);
        $dynamic_detail["suid_name"] = $User->where($where)->getField('name,id',true);

        $reply_where = array(
            "r.object_id" => $sid, "r.tablename" => $type,
        );
        //评论
        $Reply = M('Reply')->alias("r")->join("right join __USER__ as u on u.id = r.uid")->where($reply_where)->field("u.id as uid,u.avatar,u.name,r.id as rid,r.content,r.create_time,r.parent_id")->select();
        $dynamic_detail["Reply"] = generateTree($Reply, "rid", "parent_id");
        if (!empty($dynamic_detail['transfer_sid'])) {
            $where_transfer['s.id'] = $dynamic_detail['transfer_sid'];
            $where_transfer['s.isdel'] = 0;
            $dynamic_detail['Transfer'] = M('Sof')->alias("s")->join("right join __USER__ as u on u.id = s.uid")->where($where_transfer)->field("u.id as uid,u.avatar,u.name,s.id as transfer_sid,s.content,s.create_time")->find();
            $dynamic_detail['Transfer']['url'] =  M('SofAttachment')->where(array("object_id" => $dynamic_detail['transfer_sid'], "tablename" => $type))->getfield("url");
        }
        M('Sof')->where(array("id" => $sid))->setInc("click_num");
        $this->apiReturn(array("data" => $dynamic_detail));
    }

    /**
     * Notes: 动态评论
     * User: Sen
     * DateTime: 2020/7/16 16:30
     * Return:
     */
    public function commentAdd()
    {

        $unionid = I('unionid');//微信id
        $sid = I('sid');//动态id
        $content = I('content');//评论内容
        $parent_id = I('parent_id',0);//评论id  如果是评论帖子 着为0


        if (empty($unionid) || empty($sid) || empty($content)) {
            $this->apiError("非法请求！");
        }
        $User = D('User');
        $uid = $User->getIdByUnionid($unionid);

       M('Sof')->where(array("id" => $sid))->setInc("click_num");

        $reply = array(
            'object_id' => $sid, 'tablename' => "sof", 'uid' => $uid, 'create_time' => date("Y-m-d H:i:s", time()),
            'content' => $content, 'parent_id' => $parent_id,
        );

        $reply_res = M('Reply')->add($reply);

        if ($reply_res) {
            if ($parent_id == 0) {//添加
               M('Sof')->where(array("id" => $sid))->setInc("reply_num");
                $this->apiSuccess("操作成功！");
            } else {
                $this->apiSuccess("操作成功！");
            }
        } else {
            $this->apiError("操作失败！");
        }
    }

    /**
     * 收到的赞
     */
    public function likeReceived(){
        $unionid = I('unionid');
        $type = I('type');
        $page = I('page', 1);
        $page_num = I('page_num', 8);

        if (empty($unionid) || empty($type)) {
            $this->apiError("非法请求！");
        }
        $User = D('User');
        $uid = $User->getIdByUnionid($unionid);

        $where = [
            's.uid' => $uid,
            's.praise_num' => ['gt', 0],
            's.isdel' => 0,
            's.type' => $type
        ];

        $dynamic_ids = M('Sof')->alias("s")->where($where)->getField('id',true);

        $dynamic_list = M('Sof')->alias("s")->join(" left join __USER__ u on u.id = s.uid")->where($where)->field("s.id,s.content as s_content,s.uid as s_uid,u.name as s_username,u.avatar as s_avatar,s.praise_num")->select();

        $map_sid_dynamic = [];
        array_map(function ($v) use (&$map_sid_dynamic) {
            $v["List"] = M('SofAttachment')->where(array("object_id" => $v["id"], "tablename" => "sof"))->field("url")->select();
            $map_sid_dynamic[$v['id']] = $v;
            unset($map_sid_dynamic[$v['id']]['id']);
        }, $dynamic_list);

        $where_like = [
            'l.object_id' => ['in',$dynamic_ids],
            'undigg' => 0
        ];
        $count =M('Like')->alias("l")->where($where_like)->count();
        $like_list = M('Like')->alias("l")->where($where_like)->join(' left join __USER__ u on u.id = l.uid')->field('l.object_id as sid, l.create_time, l.is_read,l.uid as like_uid,u.avatar as like_user_avatar,u.name as like_user_name')->page($page, $page_num)->select();
        foreach ($like_list as $k => $v){
            $like_list[$k] += $map_sid_dynamic[$v['sid']];
        }
        $total_page = $count % $page_num == 0 ? ($count / $page_num) : ceil($count / $page_num);
        $returnList["data"] = array(
            "total" => $count,
            "current_page" => $page,
            "total_page" => $total_page,
            "like_list" => $like_list
        );
        $this->apiReturn(array("data" => $returnList));
    }

    /**
     * 收到的评论
     */
    public function replyReceived(){
        $unionid = I('unionid');
        $type = I('type');
        $page = I('page', 1);
        $page_num = I('page_num', 8);

        if (empty($unionid) || empty($type)) {
            $this->apiError("非法请求！");
        }
        $User = D('User');
        $uid = $User->getIdByUnionid($unionid);

        $Sof = M('Sof');
        $where = [
            's.uid' => $uid,
            's.reply_num' => ['gt', 0],
            's.isdel' => 0,
            's.type' => $type
        ];
        $dynamic_ids = $Sof->alias("s")->where($where)->getField('id',true);
        $dynamic_list = M('Sof')->alias("s")->join(" left join __USER__ u on u.id = s.uid")->where($where)->field("s.id,s.content as s_content,s.uid as s_uid,u.name as s_username,u.avatar as s_avatar,s.reply_num")->select();

        $map_sid_dynamic = [];
        array_map(function ($v) use (&$map_sid_dynamic) {
            $v["List"] = M('SofAttachment')->where(array("object_id" => $v["id"], "tablename" => "sof"))->field("url")->select();
            $map_sid_dynamic[$v['id']] = $v;
            unset($map_sid_dynamic[$v['id']]['id']);
        }, $dynamic_list);
        $tablename = $type == 1? 'sof':"";
        $where_reply = [
            'r.object_id' => ['in',$dynamic_ids],
            'r.parent_id' => 0,
            'r.tablename' => $tablename
        ];
        $count =M('Reply')->alias("r")->where($where_reply)->count();
        $reply_list = M('Reply')->alias("r")->where($where_reply)->join(' left join __USER__ u on u.id = r.uid')->field('r.object_id as sid, r.create_time as reply_create_time, r.is_read,r.uid as reply_uid,r.content as reply_content,u.avatar as reply_user_avatar,u.name as reply_user_name')->page($page, $page_num)->select();
        foreach ($reply_list as $k => $v){
            $reply_list[$k] += $map_sid_dynamic[$v['sid']];
        }
        $total_page = $count % $page_num == 0 ? ($count / $page_num) : ceil($count / $page_num);
        $returnList["data"] = array(
            "total" => $count,
            "current_page" => $page,
            "total_page" => $total_page,
            "reply_list" => $reply_list
        );
        $this->apiReturn(array("data" => $returnList));
    }

    /**
     *  转发动态
     */
    public function transfer(){
        $unionid = I('unionid');//微信id
        $sid = I('sid');//动态id
        $suid=I('suid');

        if (empty($unionid) || empty($sid)) {
            $this->apiError("非法请求！");
        }
        $sof_content = M('Sof')->getFieldById($sid,'content');
        $content = I('content','转发：'.$sof_content);//转发内容

        $User = D('User');
        $uid = $User->getIdByUnionid($unionid);
        M('Sof')->where(array("id" => $sid))->setInc("click_num");

        $transfer = array(
            'object_id' => $sid,
            'tablename' => "sof",
            'uid' => $uid,
            'create_time' => date("Y-m-d H:i:s", time()),
            'content' => json_encode($content,true),
        );

        $transfer_res = M('Transfer')->add($transfer);
        $suid =implode(',',$suid);  //将@的好友的id转换为字符串

        if ($transfer_res) {
            $type = M('Sof')->getFieldById($sid,'type');
            $sof_add = array(
                "uid" => $uid,
                "content" => json_encode($content,true),
                "type" => $type,
                "create_time" => date("Y-m-d H:i:s", time()),
                "transfer_id" => $transfer_res,
                "transfer_sid" => $sid,
                "suid" => $suid
            );

            //将转发信息插入朋友圈表
            M('Sof')->add($sof_add);
            M('Sof')->where(array("id" => $sid))->setInc("transfer_num");
            $this->apiSuccess("操作成功！");
        } else {
            $this->apiError("操作失败！");
        }
    }
}