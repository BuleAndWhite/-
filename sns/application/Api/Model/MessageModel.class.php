<?php

namespace Api\Model;

use Think\Model;

/**
 *
 * Notes:消息模型
 * User: 裴超群
 * Date: 2020/8/18
 * Time: 9:41
 *
 */
class MessageModel extends Model
{
    /**
     * Notes:       添加发送消息
     * Date:        2020/8/18
     * Time:        9:42
     * $nUserId     发送用户id
     * $sFrom       发送来源
     * $nType       类型      默认 1 默认系统消息(后续扩展)
     * $sContent    发送内容
     */
    public function addMessage($nUserId , $sFrom , $sContent , $nToUserId , $nType = 1)
    {
        //过滤参数
        $nUserId = intval($nUserId);
        if($nUserId <= 0){
            return [0,'用户id不能为空'];
        }

        $sFrom   = trim($sFrom);
        if(strlen($sFrom) <= 0){
            return [0,'来源不能为空'];
        }

        $sContent = trim($sContent);
        if(strlen($sContent) <= 0){
            return [0,'来源不能为空'];
        }

        $nToUserId = intval($nToUserId);
        if($nToUserId <= 0){
            return [0,'被发送用户id'];
        }

        $nType = intval($nType);
        if($nType <= 1){
            $nType = 1;
        }

        $arrData = [];
        $arrData['uid']     = $nUserId;
        $arrData['content'] = $sContent;
        $arrData['from']    = $sFrom;
        $arrData['type']    = $nType;
        $arrData['to_uid']  = $nToUserId;
        $arrData['state']   = 1;
        $arrData['create_time'] = get_curt_time();

        //开启事务
        M('Message')->startTrans();
        $bMessage = M('Message')->add($arrData);

        if(!$bMessage){
            M('Message')->rollback();
            return [0,'添加失败'];
        }

        M('Message')->commit();
        return [1,'添加成功'];
    }

    /**
     *
     * Notes:       获取消息列表
     * Date:        2020/8/18
     * Time:        10:29
     * $nUserId     用户id
     * $nType       类型 默认 1 默认系统消息(后续扩展)
     * $nState      状态 1 未读 2 已读 9 删除
     * $nPage       页码    默认0
     * $nLimit      每页条数 默认10
     *
     */
    public function getList($nUserId , $nType = 1 , $nState = 0 , $nPage = 0 , $nLimit = 10){
        //设置参数
        $nUserId = intval($nUserId);
        if($nUserId <= 0){
            return [0,'用户id不能为空'];
        }

        $nType   = intval($nType);
        if($nType <= 1){
            $nType = 1;
        }

        $nPage   = intval($nPage);
        if($nPage <= 0){
            $nPage = 0;
        }

        $nLimit  = intval($nLimit);
        if($nLimit <= 0){
            $nLimit = 10;
        }
        $nState = intval($nState);
        $nStart = $nPage * $nLimit;

        $arrWhere           = [];
        $arrWhere['uid']    = $nUserId;
        $arrWhere['type']   = $nType;
        $arrWhere['status'] = $nState;
        if($nState <= 0){
            $arrWhere['status'] = ['neq',9];
        }

        //获取分页数据
        $arrInfo = M('Message')
                    ->where($arrWhere)
                    ->limit($nStart,$nLimit)
                    ->order(['id'=>'desc'])
                    ->select();
        if(count($arrInfo) <= 0){
            return [1,[]];
        }
        return [1,$arrInfo];
    }

    /**
     * Notes:       修改状态
     * Date:        2020/8/18
     * Time:        10:52
     * $nMessageId  消息id
     * $nState      需要修改的状态 1 未读 2 已读 9删除
     */
    public function saveState($nUserId , $nMessageId , $nState){
        $nUserId = intval($nUserId);
        if($nUserId <= 0){
            return [0,'用户id不能为空'];
        }

        $nMessageId = intval($nMessageId);
        if($nMessageId <= 0){
            return [0,'消息id不能为空'];
        }

        $nState = intval($nState);
        if($nState <= 0){
            return [0,'状态不能为空'];
        }

        $arrWhere           = [];
        $arrWhere['id']     = $nMessageId;
        $arrWhere['uid']    = $nUserId;

        //获取分页数据
        M('Message')->startTrans();
        $arrMessage = M('Message')->where($arrWhere)->find();
        if(count($arrMessage) <= 0){
            M('Message')->rollback();
            return [0,'未找到消息信息'];
        }

        //判断是否已修改
        if(!empty($arrMessage['state']) && $arrMessage['state'] == $nState){
            M('Message')->commit();
            return [1,'修改成功'];
        }

        //修改数据
        $arrUpdata   = [];
        $arrUpdata['status']      = $nState;
        $arrUpdata['update_time'] = get_curt_time();
        $bMessage = M('Message')->where($arrWhere)->save($arrUpdata);
        IF(!$bMessage){
            M('Message')->rollback();
            return [0,'修改失败'];
        }
        M('Message')->commit();
        return [1,'修改成功'];
    }



}