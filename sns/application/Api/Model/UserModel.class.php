<?php


namespace Api\Model;

use Think\Model;
class UserModel extends Model
{
    public function getIdByUnionId($unionid)
    {
        return $this->where(['unionid'=>$unionid])->getField('id');
    }
    /**
     * 通过用户UId获取用户@的好友的用户名
     * */
    public function getUnionidByName($ids)
    {
        $where = [
            'id' => ['in',$ids]
        ];
        return $this->where($where)->getField('name',true);
    }

    public function getUserInfoByUnionId($unionid, $field = '')
    {
        return $this->where(['unionid'=>$unionid])->field($field)->find();
    }

    /**
     * 通过用户UnionId获取用户id
     *
     * $nUnionid 用户Unionid
     * */
    public function getUserIdByUnionId($nUnionid){
        $nUnionid = trim($nUnionid);
        if(strlen($nUnionid) <= 0){
            return [0,'用户Unionid不能为空'];
        }

        $arrWhere   = [];
        $arrWhere[] = ['unionid'=>$nUnionid];
        $nUserId = $this->where($arrWhere)->getField('id');
        if(!$nUserId){
            return [0,'未找到用户信息'];
        }
        return [1,$nUserId];
    }

    /**
     * 获取用户昵称头像信息
     *
     * $arrUserIds 可传数组 单个id
     * $bIsNot     数组取反 不存在数组中的用户信息
     *
     * @return array
     * */
    public function getUserNameAvatar($arrUserIds,$sKeyWord = '',$bIsNot = false){

        $sKeyWord = trim($sKeyWord);

        $arrWhere = [];
        $arrWhere['isdel'] = ['eq',0];

        if(strlen($sKeyWord) > 0){
            $arrWhere['name'] = ['like','%'.$sKeyWord.'%'];
        }

        if(is_array($arrUserIds) && count($arrUserIds) > 0){
            $arrWhere['id'] = ['in',$arrUserIds];
            if($bIsNot){
                $arrWhere['id'] = ['not in',$arrUserIds];
            }
        }elseif(is_numeric($arrUserIds) && intval($arrUserIds) > 0){
            $arrWhere['id'] = ['eq',$arrUserIds];
        }

        $arrData = $this->field('id,name,avatar')->where($arrWhere)->select();
        return [1,$arrData];
    }

    /**
     * 通过用户id获取用户信息
     * */
    public function getUserById($nUserId){
        if(intval($nUserId) <= 0){
            return [0,'用户id不能为空'];
        }

        $arrWhere    = [];
        $arrWhere[]  = ['id'=>intval($nUserId)];
        $arrUserInfo = $this->where($arrWhere)->find();
        return [1,$arrUserInfo];
    }
}