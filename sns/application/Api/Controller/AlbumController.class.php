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
class AlbumController extends AppController
{

    function __construct()
    {
        header("Access-Control-Allow-Origin: *");
        header("Content-Type: text/html; charset=UTF-8");
        parent::_initialize();

    }

    /**
     * Notes:添加相册
     * User: 孙成睿
     * DateTime: 2020/8/2110:59
     */
    public function albumAdd()
    {
        $unionid = I("unionid");
        $name = I("name","默认相册");

        if (empty($unionid)) {
            $this->apiError("非法请求！");
        }

        $User = D('User');
        $uid = $User->getIdByUnionid($unionid);
        $Album = M("Album");



        $where['uid'] = $uid;
        $where['is_private'] = 0;
        //查找非隐私相册的数量
        /*$count = $Album->where($where)->count();
        $max_album_number = C('MAX_ALBUM_NUMBER');
        if ($count < $max_album_number) {*/
        $add = array(
            "uid" => $uid,
            "name" => $name,
            "create_time" => date("Y-m-d H:i:s"),

        );

        $album = $Album->add($add);

        if ($album) {
            $this->apiSuccess("相册添加成功!");
        } else {
            $this->apiError("相册添加失败，请重试!");
        }
    }

    public function setCover() {
        $unionid = I('unionid');
        $album_id = I("album_id");
        $photo_id = I("photo_id");

        if (empty($unionid) || empty($album_id) || empty($photo_id)){
            $this->apiError("非法请求！");
        }

        $User = D('User');
        $uid = $User->getIdByUnionid($unionid);
        $album_uid = M('Album')->getFieldById($album_id,'uid');
        if ($album_uid != $uid){
            $this->apiError("非法请求！");
        }
        $Photo = M('Photo');
        $cover_photo_id = $Photo->where(['album_id' => $album_id, 'is_cover' => 1])->getField('id');
        if ($cover_photo_id == $photo_id){
            $this->apiError("您选择相同的照片作为封面，请重新选择！");
        }
        $Photo->startTrans();
        if (!empty($cover_photo_id)){
            $cancel_result = $Photo->where("id = ".$cover_photo_id)->setField('is_cover',0);
        }

        $set_result = $Photo->where("id = ".$photo_id)->setField('is_cover',1);
        if (!$cancel_result){
            $Photo->rollback();
        }

        $Photo->commit();
        $this->apiSuccess("封面设置成功!");
    }
    /**
     * 添加相片
     */
    public function photoAdd(){

        $unionid = I("unionid");
        $album_id = I("album_id");
        $media = I("media");

        if (empty($unionid) || empty($media)) {
            $this->apiError("非法请求！");
        }

        $User = D('User');
        $Album = M("Album");
        $Photo = M('Photo');
        $uid = $User->getIdByUnionid($unionid);

        if (empty($album_id)){
            $where['uid'] = $uid;
            $where['is_private'] = 0;
            //添加关系前 先查询关系
            $album_id = $Album->where($where)->getField('id');
            if (empty($album_id))
            {
                $add = array(
                    "uid" => $uid,
                    "name" => '默认相册',
                    "create_time" => date("Y-m-d H:i:s"),
                );
                $album_id = $Album->add($add);
            }

        }
        /*$count = $Photo->where(['uid'=>$uid,'album_id'=>$album_id])->count();

        if ($count  == 20){
            $this->apiError("您上传的照片已超过允许添加的相片数量！");
        }
        $total_allow_count = 20 - $count;*/
        if (is_array($media)){

            $dataList = [];
            foreach ($media as $v){

                $dataList[] = [
                    'album_id' => $album_id,
                    'uid' => $uid,
                    'url' => $v,
                    'create_time' => date("Y-m-d H:i:s")
                ];
                /*$total_allow_count--;
                if ($total_allow_count == 0){
                    break;
                }*/
            }
            $res = $Photo->addAll($dataList);
            /*if (count($dataList) != count($media)){
                $this->apiError("您已成功上传count($dataList)张照片，照片已超过允许添加的相片数量！");
            }*/
        }else if (is_string($media)){

            $add = [
                'album_id' => $album_id,
                'url' => $media,
                'create_time' => date("Y-m-d H:i:s")
            ];
            $res = $Photo->add($add);

        }
        if ($res) {
            $this->apiSuccess("照片添加成功!");
        } else {
            $this->apiError("照片添加失败，请重试!");
        }
    }

    /**
     * Notes:添加相片评论
     * User: 孙成睿
     * DateTime: 2020/8/2019:29
     * Return:
     */
    public function commentAdd() {
        $unionid = I("unionid");
        $photo_id = I("photo_id");
        $content = I("content");

        if (empty($unionid) || empty($photo_id) || empty($content)) {
            $this->apiError("非法请求！");
        }
        $User = D('User');
        $PhotoComment = D("PhotoComment");
        $uid = $User->getIdByUnionid($unionid);

        $data = [
            'photo_id' => $photo_id,
            'uid' => $uid,
            'content' => $content,
            'create_time' => date("Y-m-d H:i:s")
        ];

        $res = $PhotoComment->add($data);

        if ($res) {
            $this->apiSuccess("评论添加成功!");
        } else {
            $this->apiError("评论添加失败!");
        }
    }

    /**
     * Notes:删除相片评论
     * User: 孙成睿
     * DateTime: 2020/8/2019:29
     * Return:
     */
    public function commentDel() {
        $unionid = I("unionid");
        $comment_id = I("comment_id");

        if (empty($unionid) || empty($comment_id)) {
            $this->apiError("非法请求！");
        }
        $User = D('User');
        $PhotoComment = D("PhotoComment");
        $Photo = D('Photo');
        $uid = $User->getIdByUnionid($unionid);
        $comment_info = $PhotoComment->getById($comment_id);
        $photo_uid = $Photo->getFieldById($comment_info['photo_id'], 'uid');
        if ($uid != $comment_info['uid'] && $uid != $photo_uid){
            $this->apiError("非法请求！");
        }

        $res = $PhotoComment->delete($comment_id);

        if ($res) {
            $this->apiSuccess("评论删除成功!");
        } else {
            $this->apiError("评论删除失败!");
        }
    }

    /**
     * Notes:相片评论列表
     * User: 孙成睿
     * DateTime: 2020/8/219:48
     * Return: @return
     */
    public function getCommentList(){

        $photo_id = I('photo_id');
        $page = I('page', 1);//页码
        $page_num = I('page_num', 3);//每页条数

        if (empty($photo_id) ){
            $this->apiError("非法请求！");
        }

        $PhotoComment = D("PhotoComment");
        $where = [
            'photo_id' => $photo_id
        ];
        $count = $PhotoComment->where($where)->count();
        $comment_list = $PhotoComment
            ->alias('pc')
            ->join("right join __USER__ as u on u.id = pc.uid")
            ->where($where)
            ->field("pc.id,u.avatar,pc.uid,pc.create_time,u.name,pc.content")
            ->page($page, $page_num)
            ->order("pc.create_time desc")
            ->select();
        $total_page = $count % $page_num == 0 ? ($count / $page_num) : ceil($count / $page_num);
        $returnList["data"] = array(
            "total" => $count,
            "current_page" => $page,
            "total_page" => $total_page,
            "comment_list" => $comment_list
        );
        $comment_list ? $this->apiReturn($returnList) : $this->apiError('空数据');
    }

    /**
     * Notes:相册列表
     * User: 孙成睿
     * DateTime: 2020/8/2110:51
     * Return: @return
     */
    public function getAlbumList(){
        $unionid = I("unionid");
        $page = I('page', 1);//页码
        $page_num = I('page_num', 7);//每页条数

        if (empty($unionid) ) {
            $this->apiError("非法请求！");
        }
        $User = D('User');
        $Album = M("Album");
        $Photo = M('Photo');
        $uid = $User->getIdByUnionid($unionid);

        $where['uid'] = $uid;
        $where['is_private'] = 0;
        //查找非隐私相册的数量
        $count = $Album->where($where)->count();

        $album_list = $Album->where($where)->field('id,name,create_time')->page($page,$page_num)->order("create_time desc")->select();
        $album_ids = array_column($album_list, 'id');
        $where = [
            'album_id' =>['in',$album_ids],
            'is_cover' => 1
        ];
        $cover_list = $Photo->where($where)->field('album_id,url')->select();
        $map_album_cover = [];
        array_map(function ($v) use (&$map_album_cover){
            $map_album_cover[$v['album_id']] = $v['url'];
        },$cover_list);
        foreach ($album_list as $k => $v){
            $album_list[$k]['cover_url'] = isset($map_album_cover[$v['id']])?$map_album_cover[$v['id']]:"";
        };
        $total_page = $count % $page_num == 0 ? ($count / $page_num) : ceil($count / $page_num);
        $returnList["data"] = array(
            "total" => $count,
            "current_page" => $page,
            "total_page" => $total_page,
            "album_list" => $album_list
        );
        $album_list ? $this->apiReturn($returnList) : $this->apiError('您还没有创建相册');
    }

    /**
     * Notes:获取照片列表/查看相册
     * User: 孙成睿
     * DateTime: 2020/8/2110:52
     * Return: @return
     */
    public function getPhotoList() {
        $unionid = I("unionid");
        $album_id = I("album_id/d");
        $page = I('page', 1);//页码
        $page_num = I('page_num', 7);//每页条数

        if (empty($unionid) ) {
            $this->apiError("非法请求！");
        }

        $User = D('User');
        $Album = M('Album');
        $Photo = M('Photo');

        $uid = $User->getIdByUnionid($unionid);
        if (empty($album_id)){
            $where = [
                'uid' => $uid,
                'is_private' => 0
            ];
            $album_id = $Album->where($where)->getField('id');

        }

        if ($album_id){
            $where_photo = [
                'album_id' => $album_id
            ];
            //查找非隐私相册的数量
            $count = $Photo->where($where_photo)->count();
            $photo_list = $Photo->where($where_photo)->field('id,url,create_time,is_cover')->page($page,$page_num)->order("id desc")->select();
            $total_page = $count % $page_num == 0 ? ($count / $page_num) : ceil($count / $page_num);
            $returnList["data"] = array(
                "total" => $count,
                "current_page" => $page,
                "total_page" => $total_page,
                "photo_list" => $photo_list
            );
            $photo_list ? $this->apiReturn($returnList) : $this->apiError('您还没有创建相册');
        }else {
            $this->apiError('您还没有创建相册');
        }
    }

    /**
     * Notes:删除照片
     * User: 孙成睿
     * DateTime: 2020/8/2110:52
     * Return: @return
     */
    public function photoDel(){
        $unionid = I("unionid");
        $photo_id = I("photo_id/d");

        if (empty($unionid) || empty($photo_id)) {
            $this->apiError("非法请求！");
        }

        $User = D('User');
        $uid = $User->getIdByUnionid($unionid);

        $Photo = M('Photo');

        $photo_info = $Photo->getById($photo_id);
        if ($photo_info['uid'] != $uid){
            $this->apiError("非法操作！");
        }
        $result = $Photo->delete($photo_id);

        if ($result) {
            $this->apiSuccess("照片删除成功!");
        } else {
            $this->apiError("照片删除失败，请重试!");
        }
    }
}