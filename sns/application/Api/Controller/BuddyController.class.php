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
class BuddyController extends AppController
{

    function __construct()
    {
        header("Access-Control-Allow-Origin: *");
        header("Content-Type: text/html; charset=UTF-8");
        parent::_initialize();
    }

    /**
     * Notes: 好友搜索
     * User: Sen
     * DateTime: 2020/6/29 10:58
     * Return:
     */
    public function buddySearch()
    {
        $name = I('name');//要搜索的名字

        if (empty($name)) {
            $this->apiError("关键字不能为空！");
        }
        $data = M('User')->where(array("name" => $name))->field("id,name,avatar")->select();

        $this->apiReturn(array("data" => $data));

    }

    /**
     * Notes: 好友搜索
     * User: Sen
     * DateTime: 2020/6/29 10:58
     * Return:
     */
    public function mybuddySearch()
    {
        $name = I('name');//要搜索的名字
        $unionid = I('unionid');
        if (empty($unionid) || empty($name)) {
            $this->apiError("关键字不能为空！");
        }
        $User = D('User');
        $uid = $User->getIdByUnionid($unionid);
        $buddy_ids = M('Buddy')->where(['uid' => $uid])->getField('object_id',true);
        $where = [
            'id' => ['in',$buddy_ids],
            'name' => $name
        ];
        $data = $User->where($where)->field("id,name,avatar")->select();

        $this->apiReturn(array("data" => $data));

    }

    /**
     * Notes: 个人信息
     * User: Sen
     * DateTime: 2020/6/30 10:53
     * Return:
     */
    public function personalInformation()
    {
        $unionid = I('unionid');
        $user_id = I('user_id');//需要添加的好友id

        if (empty($unionid) || empty($user_id)) {
            $this->apiError("非法请求！");
        }
        $User = D('User');
        $uid = $User->getIdByUnionid($unionid);
        $data = $User->where(array("id" => $user_id))->field("name,avatar")->find();

        $where1 = array("uid" => $uid, "object_id" => $user_id);
        $where2 = array("uid" => $user_id, "object_id" => $uid);

        $where['_complex'] = array(
            $where1,
            $where2,
            '_logic' => 'or'
        );
        //判断关系
        $status = M('Buddy')
            ->where($where)
            ->field("status")
            ->find();
        if (empty($status)) {
            $status["status"] = -1;
        }
        $data += $status;
        $this->apiReturn(array("data" => $data));

    }

    /**
     * Notes: 添加好友发送验证(黑名单不做取消，被加入黑名单 只有主人才能解封)
     * User: Sen
     * DateTime: 2020/6/30 9:53
     * Return:
     */
    public function buddyAdd()
    {
        $unionid = I('unionid');
        $user_id = I('user_id');//需要添加的好友id
        $remarks = I('remarks');//备注信息

        if (empty($unionid) || empty($user_id)) {
            $this->apiError("非法请求！");
        }

        $User = D('User');
        $user_info = $User->getUserInfoByUnionId($unionid, ' id,name');
        if ($user_info['id'] == $user_id) {
            $this->apiError("无效操作！");
        }
        if (empty($remarks)) {
            $remarks = "你好,我是" . $user_info['name'] . ",希望成为你的好友！";
        }


        $where = array("uid" => $user_info['id'], "object_id" => $user_id);

        //添加关系前 先查询关系
        $buddy = M('Buddy')->where($where)->find();
        $where = [
            'uid' => $user_id,
            'object_id' => $user_info['id'],
            'status' => 2
        ];
        $b = M('Buddy')->where($where)->find();
        if ($b) {
            $this->apiError("你已被对方拉入黑名单，无法添加对方为好友！");
        }
        if (empty($buddy)) {
            $add = array(
                "uid" => $user_info['id'],
                "object_id" => $user_id,
                "remarks" => $remarks,
                "create_time" => date("Y-m-d H:i:s", time())
            );

            $buddy = M('Buddy')->add($add);

            if ($buddy) {
                $this->apiSuccess("好友添加成功，等待验证!");
            } else {
                $this->apiError("好友添加失败，请重试!");
            }
        } else if ($buddy["status"] == 0) {
            //身份判断
            $buddyInfo = M('Buddy')->where(array("uid" => $user_info['id'], "object_id" => $user_id))->find();
            if ($buddyInfo) {
                $this->apiError("等待好友验证，请勿重复添加!");
            } else {
                $this->apiError("对方已添加你为好友，请前往验证列表验证!");
            }
        } else if ($buddy["status"] == 1) {
            $this->apiError("该用户已是你的好友，请勿重复添加!");
        } else if ($buddy["status"] == 2) {
            //被拉黑，需要主人取消拉黑

            if ($b) {
                $this->apiError("你已被对方拉入黑名单，无法添加对方为好友！");
            } else {

                $save = array(
                    "uid" => $user_info['id'],
                    "object_id" => $user_id,
                    "remarks" => $remarks,
                    "create_time" => date("Y-m-d H:i:s"),
                    "status" => 0,
                    "is_read" => 0
                );

                $res = M('Buddy')->where(array("id" => $buddy["id"]))->save($save);

                if ($res !== false) {
                    $this->apiSuccess("好友添加成功，等待验证!");
                }
            }
        }
    }

    /**
     * Notes: 好友验证列表(收到的)
     * User: Sen
     * DateTime: 2020/6/30 10:39
     * Return:
     */
    public function buddyVerificationListReceive()
    {
        $unionid = I('unionid');
        if (empty($unionid)) {
            $this->apiError("非法请求！");
        }
        $User = D('User');
        $uid = $User->getIdByUnionid($unionid);
        $buddy = M('Buddy')
            ->alias('bu')
            ->join("left join __USER__ as u on bu.uid = u.id")
            ->where(array('bu.object_id' => $uid, "bu.status" => "0"))
            ->field('bu.id,u.name,u.avatar,bu.remarks,bu.is_read')
            ->select();

        $this->apiReturn(array("data" => $buddy));

    }

    /**
     * Notes: 好友验证列表(发送的)
     * User: Sen
     * DateTime: 2020/6/30 10:39
     * Return:
     */
    public function buddyVerificationListSend()
    {
        $unionid = I('unionid');
        if (empty($unionid)) {
            $this->apiError("非法请求！");
        }
        $User = D('User');
        $uid = $User->getIdByUnionid($unionid);

        $where['bu.uid'] = ['eq',$uid];
        $where['status'] = ['neq',2];
        $buddy = M('Buddy')
            ->alias('bu')
            ->join("left join __USER__ as u on bu.object_id = u.id")
            ->where($where)
            ->field('bu.object_id,u.name,u.avatar,bu.remarks,bu.is_read,bu.status')
            ->select();
        $status_title = [
            0 => '等待验证',
            1=> '已成为好友',
            2 =>'已拒绝'
        ];
        foreach ($buddy as $key => $value){
            $buddy[$key]['status_title'] = $status_title[$value['status']];
        }
        $this->apiReturn(array("data" => $buddy));
    }

    /**
     * Notes: 好友验证通过
     * User: Sen
     * DateTime: 2020/6/30 14:51
     * Return:
     */
    public function verificationPass()
    {
        $unionid = I('unionid');
        $id = I('id');//需要验证的信息id

        if (empty($unionid) || empty($id)) {
            $this->apiError("非法请求！");
        }

        $Buddy = M('Buddy');
        $User = D('User');
        $user_id = $User->getIdByUnionid($unionid);

        $where['id'] = $id;
        $buddy = $Buddy->where($where)->find();
        if ($buddy['object_id'] != $user_id){
            $this->apiError("非法请求！");
        }
        if ($buddy["status"] == 0) {

            $res = $Buddy->where(array("id" => $id))->save(array("status" => 1, "is_read" => 1));

            if ($res !== false) {
                $this->apiSuccess("好友验证成功，现在可以开始聊天了！");
            } else {
                $this->apiError("服务器内部错误！");
            }
        } else if ($buddy["status"] == 1) {
            $this->apiError("验证已通过，请勿重复提交！");
        } else {
            $this->apiError("非法操作！");
        }

    }

    /**
     * Notes: 好友验证拒绝
     * User: Sen
     * DateTime: 2020/6/30 14:51
     * Return:
     */
    public function verificationReject()
    {
        $unionid = I('unionid');
        $id = I('id');//需要验证的信息id

        if (empty($unionid) || empty($id)) {
            $this->apiError("非法请求！");
        }

        $Buddy = M('Buddy');
        $User = D('User');
        $user_id = $User->getIdByUnionid($unionid);

        $where['id'] = $id;
        $buddy = $Buddy->where($where)->find();
        if ($buddy['object_id'] != $user_id){
            $this->apiError("非法请求！");
        }
        if ($buddy["status"] == 0) {
            $res = $Buddy->where(array("id" => $id))->delete();
            if ($res !== false) {
                $this->apiSuccess("验证拒绝成功！");
            } else {
                $this->apiError("服务器内部错误！");
            }
        }  else {
            $this->apiError("非法操作！");
        }

    }

    /**
     * Notes: 加入黑名单
     * User: Sen
     * DateTime: 2020/6/30 14:30
     * Return:
     */
    public function blacklistAdd()
    {
        $unionid = I('unionid');
        $user_id = I('user_id');

        if (empty($unionid) || empty($user_id)) {
            $this->apiError("非法请求！");
        }

        $User = D('User');
        $uid = $User->getIdByUnionid($unionid);
        //获取关系
        $where1 = array("uid" => $uid, "object_id" => $user_id);
        $where2 = array("uid" => $user_id, "object_id" => $uid);

        $where['_complex'] = array(
            $where1,
            $where2,
            '_logic' => 'or'
        );
        //添加关系前 先查询关系
        $buddy = M('Buddy')
            ->where($where)
            ->find();

        if ($buddy["status"] == 2) {
            $w = array("uid" => $uid, "object_id" => $user_id);
            $b = M('Buddy')->where($w)->find();
            if ($b) {
                $this->apiError("对方已在你的黑名单列表，请勿错误操作！");
            } else {
                $this->apiError("你已被对方加入黑名单，对方已从你的好友列表消失！");
            }
        }

        $save = array(
            "uid" => $uid,
            "object_id" => $user_id,
            "status" => 2,
        );

        $res = M('Buddy')->where(array("id" => $buddy["id"]))->save($save);

        if ($res !== false) {
            $where = [
                'uid' => $user_id,
                'object_id' => $uid
            ];
            $buddy = M('Buddy')->where($where)->find();
            if (!empty($buddy))
            {
                M('Buddy')->where(array("id" => $buddy["id"]))->delete();
            }
            $this->apiSuccess("该好友已被你加入黑名单！");
        } else {
            $this->apiError("服务器内部错误!");
        }

    }

    /**
     * Notes: 黑名单列表
     * User: Sen
     * DateTime: 2020/6/30 14:07
     * Return:
     */
    public function myBlacklist()
    {
        $unionid = I('unionid');
        if (empty($unionid)) {
            $this->apiError("非法请求！");
        }
        $User = D('User');
        $uid = $User->getIdByUnionid($unionid);
        $data = M('Buddy')
            ->alias("bu")
            ->join("left join __USER__ as u on bu.object_id = u.id")
            ->where(array("bu.uid" => $uid, "status" => "2"))
            ->field("bu.object_id ,u.name,u.avatar")
            ->select();

        $this->apiReturn(array("data" => $data));
    }

    /**
     * Notes: 好友消息列表
     * User: Sen
     * DateTime: 2020/6/30 15:29
     * Return:
     */
    public function buddyList()
    {
        $unionid = I('unionid');
        if (empty($unionid)) {
            $this->apiError("非法请求！");
        }

        $User = D('User');
        $uid = $User->getIdByUnionid($unionid);

        $where = array(
            'bu.status' => 1,
            'bu.uid' => $uid
        );
        $userList = M('Buddy')
            ->alias('bu')
            ->join("left join __USER__ as u on bu.object_id = u.id ")
            ->where($where)
            ->field('bu.id as bid, bu.object_id as uid, u.name, bu.remarks,  u.avatar')
            ->select();

        $this->apiReturn(array("data" => $userList));

    }

    /**
     * Notes: 当前用户的保存会话列表
     * User: miuwen
     * DateTime: 2020/8/27 15:20
     * Return:
     */
    public function userHoldTalkList(){
        //TODO 必须校验userId的合法性
        $userId = I('user_id');
        if(!$userId){
            $this->apiReturn('','用户id为必填',100);
        }
        //TODO 这里职业少了一个在status 不是1 的情况下 需要显示的职业名
        $list = M('userTalkList')
            ->alias("r")
            ->join("__USER__ u on r.last_user = u.id")
            ->field("u.name name,r.last_msg last_msg,r.group_id group_id,r.is_read is_read,u.avatar avatar,r.status status,r.uid uid,r.last_status last_status,from_unixtime(r.update_time, '%m-%d %H:%i:%s') update_time")
            ->where(array('uid'=>$userId))->order("update_time desc")->select();
        $rt = [];
        if(count($list)){
            foreach ($list as $key=>$value){
                $rt[$value['status']][] = $value;
            }
        }
        $this->apiReturn(['data'=>$rt],'sucdess',1);
    }

    /**
     * Notes: 私聊频道创建
     * User: miuwen
     * DateTime: 2020/8/27 15:20
     * Return:
     */
    public function createUserChatGroup(){
        $userId = I('userId');
        $friendId = I('friendId');
        $status = I('status');
        if(!$userId){
            $this->apiReturn('','用户id为必填',100);
        }
        if(!$friendId){
            $this->apiReturn('','对方用户id为必填',101);
        }
        if(!$status){
            $this->apiReturn('','状态为必填',102);
        }
        $friend =  M('User')->where(['id'=>$friendId])->find();
        if(!$friend){
            $this->apiReturn('','对方不存在',103);
        }
        //查看当前角色是否持有该聊天
        $telkHoldGroup = M('UserTalkList')->where(array("uid"=>$userId,'status'=>$status))->select();
        $groupIdList = array();
        $hasGroup = false;
        $group = '';

        if($telkHoldGroup&&count($telkHoldGroup)>0){
            foreach ($telkHoldGroup as $key=>$value){
                $groupIdList[] = $value['group_id'];
            }
            //去对应的组查找用户是否存在(目前查找私聊)
            $telkGroupListInfo = M('UserTalkGroup')->where('id',$groupIdList)->select();
            if(count($telkGroupListInfo)){
                foreach ($telkGroupListInfo as $groupInfo){
                    $userIdList = explode('|',$groupInfo['user_list']);
                    if(in_array($userId,$userIdList)&&in_array($friendId,$userIdList)){
                        $hasGroup = true;
                        $group = $groupInfo;
                    }
                }
            }
        }
        if(!$hasGroup){
            //不存在创建
            $userList = implode(array($userId,$friendId),'|');
            $groupId = M("UserTalkGroup")->add(array(
                'user_list'=>$userList,
                'status'=>$status,
                'update_time'=>time(),
                'create_time'=>time()
            ));
            //插入对应用户的持有聊天列表
            $insertData = array(
                array('uid'=>$userId,'group_id'=>$groupId,'status'=>$status,'update_time'=>time(),'create_time'=>time()),
                array('uid'=>$friendId,'group_id'=>$groupId,'status'=>$status,'update_time'=>time(),'create_time'=>time()),
            );

            $ret = M('UserTalkList')->addAll($insertData);
            if($ret){
                $this->apiReturn(array('group_id'=>$groupId),'添加聊天频道成功',1);
            }else{
                $this->apiReturn('','添加聊天频道失败',2);
            }
        }else{
            $this->apiReturn(array('group_id'=>$group['id']),'已存在，不需要添加频道',1);
        }
    }



    /**
     * Notes: 聊天历史数据
     * User: Sen
     * DateTime: 2020/7/21 15:20
     * Return:
     */
    public function buddyChatWindow()
    {
        $user_id = I('user_id');//好友id
        $groupId = I('groupId');//群组id
        if ( empty($user_id) || empty($groupId)) {
            $this->apiError("非法请求！");
        }
        $groupTalkList = M('userTalk')
            ->alias("r")
            ->join("__USER__ u on r.uid = u.id")
            ->field("u.name name,r.content content,u.avatar avatar,r.uid uid,from_unixtime(r.create_time, '%Y-%m-%d %H:%i:%s') create_time")
            ->where(array('r.group'=>$groupId))->order("r.create_time desc")->select();
        if(!$groupTalkList){
            $this->apiReturn(['data'=>[]]);
        }else{
            $this->apiReturn(array('data'=>$groupTalkList));
        }
    }

    /**
     * Notes: 好友聊天信息发送
     * User: Sen
     * DateTime: 2020/7/21 16:29
     * Return:
     */
    public function chatSend()
    {
        $groupId = I('groupId');//聊天频道id
        $content = I('content');//聊天内容
        $user_id = I('user_id');//用户id
        $status = I('type');//类型
        //TODO 必须通过当前token校验合法性
        if (empty($groupId) || empty($content)|| empty($user_id)|| empty($status)) {
            $this->apiError("非法请求！");
        }
        $group = M('userTalkGroup')->where(array("id"=>$groupId))->find();
        if(!$group){
            $this->apiError("不存在该频道");
        }
        $userList = explode('|',$group['user_list']);
        $sendUser = [];
        if(!in_array($user_id,$userList)){
            $this->apiError("没有当前频道权限！");
        }else{
            foreach ($userList as $user){
                if($user!=$user_id){
                    $sendUser[]=$user;
                }
            }
        }
        //添加一条新信息
        $talkInfo = array(
            'group'=>$groupId,
            'content'=>$content,
            'status'=>$status,
            'uid'=>$user_id,
            'create_time'=>time(),
            'update_time'=>time()
        );
        $res = M('userTalk')->add($talkInfo);
        $where = 'uid in ('.join(',',$userList).')';
        //设置未读
        M('userTalkList')->where($where)->save(['is_read'=>0,'last_msg'=>$content,'last_user'=>$user_id,'last_status'=>$status,'update_time'=>time()]);
        M('userTalkList')->where('uid',$user_id)->save(['isRead'=>1]);
        $sendFrom = M('user')->where(array('id'=>$user_id))
            ->field("name,avatar,id uid")
            ->find();

        //通知ws服务器
        $this->sendToWsServer('chatMessage',$sendUser,array(
            'content'=>$content,
            'sendFrom'=>$sendFrom,
            'time'=>time()
        ),'sendMessage');
        if($res){
            $this->apiSuccess("信息发送成功！");
        }else{
            $this->apiError("信息发送失败！");
        }
    }
    //获取班级群组历史聊天信息
    public function getClassChatHistory(){
        $userId = I('userId');//用户id
        $groupId = I('groupId');//当前聊天组id
        //判断用户是否在这个群组内
        $groupInfo = M('userTalkGroup')->where(array('id'=>$groupId))->find();
        if(!$groupInfo){
            return $this->apiError("群组不存在！");
        }
        $groupUsers = explode('|',$groupInfo['user_list']);
        //在组里边 查询数据
        if(in_array($userId,$groupUsers)){
            $talkList = M('userTalk')
                ->alias("r")
                ->join("__USER__ u on r.uid = u.id")
                ->field("u.name name,r.content content,u.avatar avatar,r.uid uid,from_unixtime(r.create_time, '%Y-%m-%d %H:%i:%s') create_time")
                ->where(array('r.group'=>$groupId))->order("r.create_time desc")->select();
            if(count($talkList)){
                return   $this->apiReturn($talkList,'success');
            }else{
                $this->apiReturn([],'success');
            }
        }else{
            return $this->apiError("没有发该群组的权限！");
        }
    }
    //班级发送留言
    public function sendClassMessage(){
        $userId = I('userId');//用户id
        $groupId = I('groupId');//当前聊天组id
        $content = I('content');//留言
        if (empty($groupId) || empty($content)|| empty($userId)) {
            $this->apiError("非法请求！");
        }
        $groupInfo = M('userTalkGroup')->where(array('id'=>$groupId))->find();
        if(!$groupInfo){
            return $this->apiError("群组不存在！");
        }
        $groupUsers = explode('|',$groupInfo['user_list']);
        if(in_array($userId,$groupUsers)){
            //添加一条新信息
            $talkInfo = array(
                'group'=>$groupId,
                'content'=>$content,
                'status'=>5,
                'uid'=>$userId,
                'create_time'=>time(),
                'update_time'=>time()
            );
            $res = M('userTalk')->add($talkInfo);
            if($res){
                //通知ws服务器
                $this->sendToWsServer('classMessage',$groupUsers,array(
                    'content'=>$content,
                    'sendFrom'=>$groupUsers,
                    'time'=>time()
                ),'sendMessage');

            }
        }else{
            return $this->apiError("没有发该群组的权限！");
        }
    }
    //测试消息连通性用
    public function testChatSend(){
        $userId = $_GET['userId'];//聊天频道id
        $content = $_GET['content'];//聊天内容
        $sendFromId = $_GET['sendFrom'];//聊天对象
        $sendFrom = M('user')->where(array('id'=>$sendFromId))
            ->field("name,avatar,id uid")
            ->find();
        $sendData = array(
            'content'=>$content,
            'sendFrom'=>$sendFrom,
            'time'=>time()
        );
        echo json_encode($sendData);
        $ret = $this->sendToWsServer('chatMessage',[$userId],$sendData,'sendMessage');
        $this->apiReturn($ret);
    }
}