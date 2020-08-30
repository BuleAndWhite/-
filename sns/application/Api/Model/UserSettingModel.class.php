<?php

namespace api\Model;
use Think\Model;

/**
 *
 * @2020-08-13 裴超群
 * 用户相关设置 == 模型类
 *
 * */

class UserSettingModel extends Model
{
    /**
     * 获取设置信息
     *
     * @$nSetId      设置内容id
     * @$nParentId   父级内容id
     * $isOrder      是否按规则排序
     *
     * @return array
     * */
    public function getSetInfo($nSetId = 0,$nParentId = 0,$isOrder = false){

        //获取变量
        $nSetId    = intval($nSetId);
        $nParentId = intval($nParentId);

        //整理条件
        $arrWhere = [];
        if($nSetId > 0){
            $arrWhere['id'] = $nSetId;
        }

        $arrWhere['parent_id'] = 0;
        if($nParentId > 0){
            $arrWhere['parent_id'] = $nParentId;
        }
        $arrWhere['state'] = 1;

        //是否开启排序
        $sOrder = "create_time asc";
        if($isOrder){
            $sOrder = "sort desc";
        }
        //获取信息
        $sFields = "id,parent_id,name,name_type,sort,jumpway,page_css";
        $arrInfo = M('Setting')->field($sFields)->where($arrWhere)->order($sOrder)->select();

        if(count($arrInfo) <= 0){
            return [0,'未找到配置信息'];
        }

        //获取新消息提醒下拉数据
        $arrMessSelect = [];
        if($nParentId > 0 && $nParentId == 2){
            $arrMessInfo    = M('Setting')->field($sFields)->where(['state'=>1,'parent_id'=>15])->select();
            $arrMessSelect  = array_column($arrMessInfo, null,'name_type');
            $arrMessIdsInfo = array_column($arrMessInfo, null,'id');
        }

        //整理信息
        $arrData = [];
        foreach($arrInfo as $k => $v){
            $v['selected'] = 2;
            $v['list'] = [];
            $v['user'] = [];
            if($v['name_type'] == 'newmessage'){
                $v['list'] = $arrMessIdsInfo;
                if(!empty($arrMessSelect['allowall'])){
                    $v['selected_name'] = $arrMessSelect['allowall']['name'];
                    $v['selected_id']   = $arrMessSelect['allowall']['id'];
                }
            }
            $arrData[] = $v;
        }

        return [1,$arrData];
    }

    /**
     * 获取用户设置信息
     *
     * $nUserId 用户id
     * $nSetId  设置id
     *
     * */
    public function userSet($nUserId,$nSetId){
        if(intval($nUserId) <= 0){
            return [0,'用户id不能为空'];
        }

        //设置条件
        $arrWhere            = [];
        $arrWhere['user_id'] = ['eq',intval($nUserId)];
        $arrWhere['state']   = ['neq',9];
        if(intval($nSetId) > 0){
            $arrWhere['set_id'] = ['eq',intval($nSetId)];
        }

        //获取用户设置过得信息
        $arrSet = M('UserSetting')->field('id,user_id,set_id,parent_id,set_user_id,state')->where($arrWhere)->select();

        return [1,$arrSet];
    }

    /**
     * 添加设置
     *
     * $nUserId     用户id
     * $nSetId      设置内容id
     * $nParentId   设置内容父id
     * $nState      状态
     * $nSetUserId  被设置用户id
     * */
    public function addUserSet($nUserId,$nParentId,$nSetId,$nState,$nSetUserId = 0){

        if(intval($nUserId) <= 0){
            return [0,'用户id不能为空'];
        }

        if(intval($nParentId) <= 0){
            return [0,'父级id不能为空'];
        }

        if(intval($nSetId) <= 0){
            return [0,'设置内容id不能为空'];
        }

        if(intval($nState) <= 0){
            return [0,'状态不能为空'];
        }

        if((intval($nSetId) == 6 || intval($nSetId) == 7) && intval($nSetUserId) <= 0){
            return [0,'被设置用户id不能为空'];
        }

        //判断是否有添加记录
        $arrWhere              = [];
        $arrWhere['user_id']   = ['eq',intval($nUserId)];
        if(intval($nParentId) != 15){ //新消息时 有多个设置id 只需通过获取父id即可
            $arrWhere['set_id'] = ['eq',intval($nSetId)];
        }
        $arrWhere['parent_id'] = ['eq',intval($nParentId)];
        $arrWhere['state']     = ['neq',9];

        //获取用户设置过得信息
        //开启事务
        M('UserSetting')->startTrans();
        $arrSet = M('UserSetting')->where($arrWhere)->select();
        if(count($arrSet) > 0){
            //判断添加列表是否已存在
            $arrSetUserId = array_column($arrSet, 'set_user_id');
            if(intval($nSetId) == 6 || intval($nSetId) == 7){
                if(in_array($nSetUserId,$arrSetUserId)){//存在设置直接返回
                    M('UserSetting')->commit();
                    return [1,'设置成功'];
                }else{
                    //不存在添加
                    $arrData = [];
                    $arrData['user_id']     = intval($nUserId);
                    $arrData['parent_id']   = intval($nParentId);
                    $arrData['set_id']      = intval($nSetId);
                    $arrData['set_user_id'] = intval($nSetUserId);
                    $arrData['state']       = $nState;
                    $arrData['create_time'] = get_curt_time();
                    $bUser = M('UserSetting')->data($arrData)->add();
                    if(!$bUser){
                        M('UserSetting')->rollback();
                        return [0,'设置失败'];
                    }
                    M('UserSetting')->commit();
                    return [1,'设置成功'];
                }
            }

            //判断按钮设置是否已存在
            foreach($arrSet as $k => $v){
                if($v['state'] == $nState && $v['set_id'] == intval($nSetId)){
                    M('UserSetting')->commit();
                    return [1,'设置成功'];
                }
            }
            $arrUpdate                = [];
            if(intval($nParentId) == 15){
                $arrUpdate['set_id']  = intval($nSetId);
            }
            $arrUpdate['state']       = $nState;
            $arrUpdate['update_time'] = get_curt_time();
            $bUserSetting = M('UserSetting')->where($arrWhere)->save($arrUpdate);
            if(!$bUserSetting){
                M('UserSetting')->rollback();
                return [0,'设置失败'];
            }

            M('UserSetting')->commit();
            return [1,'设置成功'];
        }

        //执行添加
        $arrData = [];
        $arrData['user_id']     = intval($nUserId);
        $arrData['parent_id']   = intval($nParentId);
        $arrData['set_id']      = intval($nSetId);
        $arrData['set_user_id'] = intval($nSetUserId);
        $arrData['state']       = $nState;
        $arrData['create_time'] = get_curt_time();
        $bUser = M('UserSetting')->data($arrData)->add();
        if(!$bUser){
            M('UserSetting')->rollback();
            return [0,'设置失败'];
        }
        M('UserSetting')->commit();
        return [1,'设置成功'];
    }

    /**
     * 执行用户删除权限(不让他看或者不看他)
     *
     * $nUserId     用户id
     * $nSetId      设置内容id
     * $nParentId   设置内容父id
     * $nSetUserId  被设置用户id
     * */
    public function delUserSet($nUserId,$nParentId,$nSetId,$nSetUserId = 0){
        if(intval($nUserId) <= 0){
            return [0,'用户id不能为空'];
        }

        if(intval($nParentId) <= 0){
            return [0,'父级id不能为空'];
        }

        if(intval($nSetId) <= 0){
            return [0,'设置内容id不能为空'];
        }

        $arrParameter = [6,7]; //不让他看我/不看他id
        if(!in_array($nSetId, $arrParameter)){
            return [0,'设置参数id错误'];
        }

        if((intval($nSetId) == 6 || intval($nSetId) == 7) && intval($nSetUserId) <= 0){
            return [0,'被设置用户id不能为空'];
        }

        //判断是否有添加记录
        $arrWhere                = [];
        $arrWhere['user_id']     = ['eq',intval($nUserId)];
        $arrWhere['set_id']      = ['eq',intval($nSetId)];
        $arrWhere['parent_id']   = ['eq',intval($nParentId)];
        $arrWhere['set_user_id'] = ['eq',intval($nSetUserId)];

        //获取用户设置过得信息
        //开启事务
        M('UserSetting')->startTrans();
        $arrSet = M('UserSetting')->where($arrWhere)->find();
        if(count($arrSet) <= 0){
            M('UserSetting')->rollback();
            return [0,'未找到设置信息'];
        }

        if($arrSet['state'] == 9){
            M('UserSetting')->commit();
            return [1,'删除成功'];
        }

        //修改状态
        $arrUpdate                = [];
        $arrUpdate['state']       = 9;
        $arrUpdate['update_time'] = get_curt_time();
        $bUserSetting = M('UserSetting')->where($arrWhere)->save($arrUpdate);
        if(!$bUserSetting){
            M('UserSetting')->rollback();
            return [0,'删除失败'];
        }

        M('UserSetting')->commit();
        return [1,'删除成功'];
    }

    /**
     * 添加反馈信息
     *
     * $sContent    内容
     * $sImg        上传图片
     * $sPhone      手机号
     *
     * */
    public function addUserFeedback($nUserId,$sContent,$sImg,$sPhone){
        if(intval($nUserId) <= 0){
            return [0,'用户信息不正确'];
        }

        if(strlen($sContent) <= 0){
            return [0,'反馈内容不能为空'];
        }

//        if(strlen($sImg) <= 0){
//            return [0,'图片地址不能为空'];
//        }

//        if(strlen($sPhone) <= 0){
//            return [0,'手机号不能为空'];
//        }

        if(strlen($sPhone) > 0 && !isMobile($sPhone)){
            return [0,'请输入正确的手机号'];
        }

        $arrData            = [];
        $arrData['user_id'] = $nUserId;
        $arrData['content'] = $sContent;
        $arrData['img']     = $sImg;
        $arrData['phone']   = $sPhone;
        $arrData['state']   = 1;
        $arrData['create_time'] = get_curt_time();

        //开启事务
        M('UserFeedback')->startTrans();
        $bUserFeedback = M('UserFeedback')->add($arrData);

        if(!$bUserFeedback){
            M('UserFeedback')->rollback();
            return [0,'添加失败'];
        }

        M('UserFeedback')->commit();
        return [1,'添加成功'];
    }
}