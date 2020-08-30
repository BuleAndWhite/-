<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Author: XiaoYuan <767684610@qq.com>
// +----------------------------------------------------------------------
/**
 * 公共访问
 * 创建日期：2017-04-24
 */
namespace Api\Controller;
use Think\Controller;
class PublicController extends AppController{
	function __construct(){
		parent::_initialize();
	}

	public function test(){
		dump(json_decode('{"user_id":"31","access_token":"3e90ba788d1cdf475ae90c6bd7859fb8fcd26415"}',true));
		echo json_encode(S('app_access_token_user_u_b6e622706c00dd770c30b75c48dbd7d7ec1370de'));
	}
	/**
	 * 访问授权
	 * @return json
	 */
	public function token(){
		if(empty(I('app_id')) || empty(I('app_secret')) || $this->app_id != I('app_id') || $this->app_secret != I('app_secret')){
			$this->apiError('Invalid access');
		}
		$access_token = get_app_access_token(I('uuid'), I('app_id'), I('app_secret'));
		$this->apiReturn(array('access_token'=>$access_token));
    }
    /**
     * 获取活动列表 
     * @return json
     */
    public function get_activity_list(){
    	parent::checkAppToken();
    	$ac_list = M('posts')->where("post_status=1 and post_type=2")->field('id,post_title,post_keywords,post_excerpt,post_content,smeta,post_modified as create_time')->order('post_modified desc')->select();
    	//$ac_list['smeta'] = get_file_path($ac_list['smeta']);
    	foreach ($ac_list as $k => $v){
    		$ac_list[$k]['smeta'] = get_file_path($v['smeta']);
    	}
    	$this->apiReturn(array('result'=>$ac_list));
    }
    /**
     * 获取活动详情 
     * @param integer id
     * @return json
     */
    public function get_activity_info(){
    	parent::checkAppToken();
    	$posts_model = M('posts');
    	$id = I('id');
    	$info = $posts_model->where("post_status=1 and id='$id'")->field('id,post_title,post_keywords,post_excerpt,post_content,smeta,post_modified as create_time')->find();
    	if(!$info){
    		$this->apiError('此活动不存在');
    	}
    	$posts_model->where(array('id'=>$id))->setInc('post_hits', 1);
    	$this->apiReturn(array('info'=>$info));
    }
    /**
     * 获取杂志分类列表
     * @return json
     */
    public function get_terms_list(){
		parent::checkAppToken();
		$params['status'] = 1;
		//获取推荐参数
		if(isset($_GET['type_name'])){
			$params[I('type_name')] = 1;
		}
    	$terms_list = M('terms')->where("status=1")->field('term_id as id,name,pic')->order('listorder asc')->select();
    	foreach ($terms_list as $k => $v){
    		$terms_list[$k]['pic'] = get_file_path($v['pic']);
    	}
    	$this->apiReturn(array('result'=>$terms_list));
    }
    /**
     * 获取杂志列表
     * @return json
     */
    public function get_posts_list(){
    	parent::checkAppToken();
    	$posts_model = M('posts');
    	$user_access_token = I('get.user_access_token');
    	if(isset($_GET['is_rec'])){
    		$params = array('b.status' => 1,'a.recommended' => 1);
    	}
    	else{
    		$params = array('b.status' => 1,'c.term_id' => I('term_id'));
    	}
    	$limit = isset($_GET['limit']) ? intval($_GET['limit']) : 10;
    	//商品总数
    	$count = $posts_model
	    	->alias('a')
	    	->join('__TERM_RELATIONSHIPS__ b on a.id=b.object_id')
	    	->join('__TERMS__ c on b.term_id=c.term_id')
	    	->where($params)
	    	->count();
    	//实例化分页类
    	$Page = new \Think\Page($count,$limit);
    	$posts_list = $posts_model
	    	->alias('a')
	    	->join('__TERM_RELATIONSHIPS__ b on a.id=b.object_id')
	    	->join('__TERMS__ c on b.term_id=c.term_id')
	    	->where($params)
	    	->field('a.id,a.post_title,a.post_keywords,a.post_excerpt,a.post_content,a.smeta,a.post_modified as create_time,a.post_hits,a.post_like,c.name')
	    	->order('b.listorder asc,a.post_modified desc')
	    	->limit($Page->firstRow.','.$Page->listRows)
    		->select();
    	foreach ($posts_list as $k => $v){
    		$posts_list[$k]['thumb'] = get_file_path($v['smeta']);
    		if($user_access_token){
    			$user_id = get_user_id($user_access_token);
    			$status = M('posts_likes')->where(array('object_id'=>$v['id'],'uid'=>$user_id))->getField('status');
    			$posts_list[$k]['posts_likes'] = $status ? 1 : 0;
    		}
    		else{
    			$posts_list[$k]['posts_likes'] = 0;
    		}
    		unset($posts_list[$k]['smeta']);
    	}
    	$this->apiReturn(array('count'=>$count, 'limit' => $limit, 'result'=>$posts_list));
    }

    /**
     * 获取杂志详情
     * @param integer id
     * @return json
     */
    public function get_posts_info(){
    	parent::checkAppToken();
    	$id = I('object_id');
    	$posts_model = M('posts');
    	$params = array(
    			'b.status' => 1,
    			'a.id' => I('object_id')
    	);
    	$info = $posts_model
	    	->alias('a')
	    	->join('__TERM_RELATIONSHIPS__ b on a.id=b.object_id')
	    	->join('__TERMS__ c on b.term_id=c.term_id')
	    	->where($params)
	    	->field('a.id,a.post_title,a.post_keywords,a.post_excerpt,a.post_content,a.smeta,a.post_modified as create_time,c.name')
	    	->order('b.listorder asc,a.post_modified desc')
    		->find();
    	if(!$info){
    		$this->apiError('杂志不存在');
    	}
    	$posts_model->where(array('id'=>$id))->setInc('post_hits', 1);
    	$this->apiReturn(array('info'=>$info));
    }
    /**
     * 获取商品列表
     * @param integer limit
     * @param integer page
     * @return json
     */
    public function get_goods_list(){
    	parent::checkAppToken();
    	$goods_model = M('goods');
    	$goods_cat = I('goods_cat') ? I('goods_cat') : 1;
    	$params = array(
    			'a.status' => 1,
    			'a.cid' => $goods_cat
    	);
    	$limit = isset($_GET['limit']) ? intval($_GET['limit']) : 10;
    	//商品总数
    	$count = $goods_model
	    	->alias('a')
	    	->join('__GOODS_CAT__ b on a.cid=b.term_id')
	    	->where($params)
    		->count();
    	//实例化分页类
    	$Page = new \Think\Page($count,$limit);
    	//获取列表
    	$goods_list = $goods_model
	    	->alias('a')
	    	->join('__GOODS_CAT__ b on a.cid=b.term_id')
	    	->where($params)
	    	->field('a.id,a.name,a.price,a.cid,a.smeta,a.keywords,a.description,a.stock,a.clicks,b.name as cat_name')
	    	->order('a.listorder asc')
	    	->limit($Page->firstRow.','.$Page->listRows)
    		->select();
    	//获取图片路径
    	foreach ($goods_list as $k => $v){
    		$goods_list[$k]['thumb'] = get_file_path($v['smeta']);
    		unset($goods_list[$k]['smeta']);
   		}
    	//返回json
    	$this->apiReturn(array('count'=>$count, 'limit' => $limit, 'result'=>$goods_list));
    }
    /**
     * 获取商品详情 
     * @param integer goods_id
     * @return json
     */
    public function get_goods_info(){
    	parent::checkAppToken();
    	$goods_model = M('goods');
    	$goods_id = I('goods_id');
    	$params = array(
    			'a.status' => 1,
    			'a.id' => $goods_id
    	);
    	$goods_info = $goods_model
	    	->alias('a')
	    	->join('__GOODS_CAT__ b on a.cid=b.term_id')
	    	->where($params)
	    	->field('a.id,a.name,a.price,a.cid,a.content,a.stock,a.clicks,b.name as cat_name')
	    	->find();
    	if(!$goods_info){
    		$this->apiError('不存在此数据');
    	}
    	$this->apiReturn(array('info'=>$goods_info));
    }
    /**
     * 轮播图列表
     * @param string $slider_name
     * @return json
     */
    public function get_slider_list(){
    	parent::checkAppToken();
    	$slider_list = sp_getslide(I('slider_name'));
    	foreach ($slider_list as $k => $v){
    		$slider_list[$k]['slide_pic'] = get_file_path($v['slide_pic']);
    	}
    	$this->apiReturn(array('result'=>$slider_list));
    }
    /**
     * 找回密码验证码
     * @param string mobile
     * @param string verify_code
     * @return json
     */
    public function send_code_findpwd(){
    	parent::checkAppToken();
    	$mobile = I('mobile');
    	$code = I('verify_code');
    	$info = M('users')->where(array('mobile'=>$mobile))->find();
    	if(!$info){
    		$this->apiError('该手机号不存在');
    	}
    	$result = send_sms($mobile, json_encode(array('name'=>$code)));
    	if($result){
    		$this->apiSuccess('发送成功');
    	}
    	else{
    		$this->apiError('发送失败');
    	}
    }
    /**
     * 发送注册验证码
     * @param string mobile
     * @param string verify_code
     * @return json
     */
    public function send_code(){
    	!IS_POST && $this->apiError('请求失败');
    	$mobile = I('mobile');
    	$code = I('verify_code');
    	$result =$this->send_sms($mobile, json_encode(array('name'=>$code)));
    	$this->ajaxReturn($result);
    	if($result){
    		$this->apiSuccess('发送成功');
    	}
    	else{
    		$this->apiError('发送失败');
    	}
    }
    /**
     * 检测新版本
     */
    public function check_version(){
    	$version = I('version');
    	//$this->apiReturn(array('version'=>'2.2.0'), '已有新版本');
    	$this->apiError('暂无新版本，无需更新');
    }
    //短信发送测试
    public function send_sms($mobile,$code){
        vendor('alidayu.TopSdk');
        $c = new \TopClient();
        $c->appkey = 'LTAIPNeB3r3YARDW';
        $c->secretKey = '1tWwEhC5nAjAlnkoo6zFWFBuIDYy6d';
        $req = new \AlibabaAliqinFcSmsNumSendRequest;
        $req->setExtend("");
        $req->setSmsType("normal");
        $req->setSmsFreeSignName("万元户");//做什么用的，类别
        $req->setSmsParam($code);// 验证码
        $req->setRecNum($mobile);//电话号码
        $req->setSmsTemplateCode("SMS_187591406"); //模板
        $resp = $c->execute($req);
        return $resp;
    }
    /**
     * 获取@的好友的具体信息
     */
    public function getNewAtList($atlist)
    {
        if ($atlist != "") {
            $atListArray = json_decode($atlist);
            foreach ($atListArray as $k => $v) {
                $atListArray[$k] = (array)json_decode($v);
            }
            foreach ($atListArray as $k => $v) {
                $uidArray[$k] = $v['uid'];
            }
            $uidArray = implode(',', $uidArray);
            $where['id'] = array('in', $uidArray);
            $NewName = M('User')->where($where)->select();
            foreach ($atListArray as $k=>$v){
                foreach ($NewName as $key => $value){
                    if($value['id']==$v['uid']){
                        $atListArray[$k]['newname']=  $value['name'];
                    }
                }
            }
            return $atListArray;
        }
    }
}