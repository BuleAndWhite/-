<?php
/**
 *
 * @2020-08-13 裴超群
 * 用户相关设置
 *
 * */
namespace Api\Controller;


class UserSettingController extends AppController
{
    /**
     * 获取设置信息
     * @$nSetId      设置内容id
     * @$nParentId   父级内容id
     *
     * */
    public function getSet(){

        //获取参数
        $nSetId    = intval(I('set_id'));
        $nParentId = intval(I('parent_id'));

        //当前登录用户信息
        $nUnionid  = trim(I('unionid'));
        list($nErr , $nUserId) = D('User')->getUserIdByUnionId($nUnionid);
        if($nErr != 1){
            return $this->apiError($nUserId);
        }

        //获取设置信息
        list($nErr , $arrSet) = D('UserSetting')->getSetInfo($nSetId , $nParentId,true);
        if($nErr != 1){
            return $this->apiError($arrSet);
        }

        //获取设置信息
        list($nErr , $arrUserSet) = D('UserSetting')->userSet($nUserId,$nSetId);
        if($nErr != 1){
            return $this->apiError($arrUserSet);
        }

        //判断当前用户是否有设置信息  当设置了不让他看我或者不看他配置时
        if(count($arrUserSet) > 0 && ($nSetId == 6 || $nSetId == 7)){
            $arrUserIds = array_column($arrUserSet, 'set_user_id','set_user_id');
            //获取用户昵称头像
            list($nErr , $arrSetUserInfo) = D('User')->getUserNameAvatar($arrUserIds);
            if($nErr != 1){
                return $this->apiError($arrSetUserInfo);
            }
            $arrData = [];
            foreach($arrSet as $k => $v){
                $v['user'] = $arrSetUserInfo;
                $arrData[] = $v;
            }
            $arrInfo = $arrData;
        }else{
            //处理 按钮设置  消息设置
            $arrData = [];
            foreach($arrSet as $k => $v){
                foreach($arrUserSet as $m => $n){
                    if($v['id'] == $n['set_id'] && $n['state'] == 1){
                        $v['selected'] = 1;
                    }

                    if($v['id'] == 15 && $v['id'] == $n['parent_id'] && $n['state'] == 1 && $v['selected_id'] != $n['set_id']){
                        $v['selected_id']   = $n['set_id'];
                        $v['selected_name'] = !empty($v['list'][$n['set_id']]['name'])?$v['list'][$n['set_id']]['name']:'';
                    }
                }
                $arrData[] = $v;
            }
            $arrInfo = $arrData;
        }

        return $this->apiReturn(array("data" => $arrInfo));
    }

    /**
     * 搜索设置朋友动态权限
     *
     * $sKeyWords   搜索关键字
     * $nSetId      设置内容id
     * $nUnionid    用户unionid
     *
     * */
    public function dynamics(){

        $sKeyWords = trim(I('key_word'));
        $nSetId    = intval(I('set_id'));

        //当前登录用户信息
        $nUnionid  = trim(I('unionid'));
        list($nErr , $nUserId) = D('User')->getUserIdByUnionId($nUnionid);
        if($nErr != 1){
            return $this->apiError($nUserId);
        }

        $arrUserInfo = [];
        if($nSetId == 6 || $nSetId == 7){
            //获取设置信息
            list($nErr , $arrUserSet) = D('UserSetting')->userSet($nUserId,$nSetId);
            if($nErr != 1){
                return $this->apiError($arrUserSet);
            }

            //获取用户id
            $arrUserIds = array_column($arrUserSet, 'set_user_id','set_user_id');
            list($nErr , $arrSetUserInfo) = D('User')->getUserNameAvatar($arrUserIds,$sKeyWords,true);
            if($nErr != 1){
                return $this->apiError($arrSetUserInfo);
            }
            $arrUserInfo = $arrSetUserInfo;
        }

        return $this->apiReturn(array("data" => $arrUserInfo));
    }

    /**
     * 添加设置
     *
     * $nUnionid    用户unionid
     * $nSetId      设置内容id
     * $nParentId   设置内容父id
     * $nSetUserId  被设置用户id
     * $nState      需要设置的状态
     * */
    public function addDynamics(){

        $nSetId     = intval(I('set_id'));
        $nParentId  = intval(I('parent_id'));
        $nSetUserId = intval(I('set_user_id'));
        $nState     = intval(I('state'));

        //当前登录用户信息
        $nUnionid   = trim(I('unionid'));
        list($nErr , $nUserId) = D('User')->getUserIdByUnionId($nUnionid);
        if($nErr != 1){
            return $this->apiError($nUserId);
        }

        //判断用户是否存在
        if($nSetUserId > 0){
            list($nErr , $arrUser) = D('User')->getUserById($nSetUserId);
            if($nErr != 1){
                return $this->apiError($arrUser);
            }
        }


        //设置信息
        list($nErr , $arrSetInfo) = D('UserSetting')->addUserSet($nUserId,$nParentId,$nSetId,$nState,$nSetUserId);
        if($nErr != 1){
            return $this->apiError($arrSetInfo);
        }
        return $this->apiSuccess($arrSetInfo);
    }

    /**
     * 删除列表用户(软删除)
     *
     * $nUnionid    用户unionid
     * $nSetId      设置内容id
     * $nParentId   设置内容父id
     * $nSetUserId  被设置用户id
     * */
    public function delDynamics(){
        $nSetId     = intval(I('set_id'));
        $nParentId  = intval(I('parent_id'));
        $nSetUserId = intval(I('set_user_id'));

        //当前登录用户信息
        $nUnionid   = trim(I('unionid'));
        list($nErr , $nUserId) = D('User')->getUserIdByUnionId($nUnionid);
        if($nErr != 1){
            return $this->apiError($nUserId);
        }

        //判断用户是否存在
        if($nSetUserId > 0){
            list($nErr , $arrUser) = D('User')->getUserById($nSetUserId);
            if($nErr != 1){
                return $this->apiError($arrUser);
            }
        }

        //执行删除
        list($nErr , $arrSetInfo) = D('UserSetting')->delUserSet($nUserId,$nParentId,$nSetId,$nSetUserId);
        if($nErr != 1){
            return $this->apiError($arrSetInfo);
        }
        return $this->apiSuccess($arrSetInfo);
    }

    /**
     * 添加用户反馈记录
     *
     * $nUnionid    用户unionid
     * $sContent    内容
     * $sImg        上传图片
     * $sPhone      手机号
     *
     * */
    public function addFeedback(){

        $sContent   = trim(I('content'));
        $sImg       = trim(I('img'));
        $sPhone     = trim(I('phone'));

        //当前登录用户信息
        $nUnionid   = trim(I('unionid'));
        list($nErr , $nUserId) = D('User')->getUserIdByUnionId($nUnionid);
        if($nErr != 1){
            return $this->apiError($nUserId);
        }

        //执行添加
        list($nErr , $arrSetInfo) = D('UserSetting')->addUserFeedback($nUserId,$sContent,$sImg,$sPhone);
        if($nErr != 1){
            return $this->apiError($arrSetInfo);
        }
        return $this->apiSuccess($arrSetInfo);
    }

}