<?php
// +----------------------------------------------------------------------
// | AnzaiSoft [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2015-2016 http://www.anzaisoft.net All rights reserved.
// +----------------------------------------------------------------------
// | Author: ZhangQingYuan <767684610@qq.com>
// +----------------------------------------------------------------------
namespace Admin\Controller;
use Common\Controller\AdminbaseController;
class WeixinPublicController extends AdminbaseController {
	protected $weixin_model;
	protected $weixin_options;
	function _initialize() {
		parent::_initialize();
		$this->weixin_model = D("Common/WeixinMenu");
		$this->weixin_options = D("Common/WeixinOptions");
	}
	/**
	 * 开发者设置
	 */
    function index(){
    	if(IS_POST){
    		$map['id'] = I('id');
	    	$info = M('weixin_options')->where($map)->find();
	    
	    	unset ($map);
	    	$map['uid'] = session('ADMIN_ID');
	    	$res = M ('weixin_options_link')->where($map)->setField('is_use', 0);
	    
	    	$map['mp_id'] = $info['id'];
	    	$res = M ('weixin_options_link')->where ($map)->setField ('is_use', 1);
	    	session ( 'TOKEN', $info['app_wxid']);
    	}
    	$this->weixin_list = $this->weixin_options->order('ID ASC')->select();
    	$this->assign('token',session('TOKEN'));
    	$this->display('Weixin:index');
    }
    function add(){
    	if(IS_POST){
    		$data = I('options');
    		if(!empty($data['id'])){
    			$rst2 = $this->weixin_options->save($data);
    		}else{
    			$rst2 = $this->weixin_options->add($data);
    		}
    		if ($rst2!==false) {
    			$this->success("保存成功！");
    		} else {
    			$this->error("保存失败！");
    		}
    	}
    	if(isset($_GET['id'])){
    		$this->options = $this->weixin_options->where(array('id'=>I('id')))->find();
    	}
    	$this->display('Weixin:add');
    }
    function index_post(){
    	$data= $_POST['options'];
    	if(!empty($data['id'])){
    		$rst2 = $this->weixin_options->save($data);
    	}else{
    		$rst2 = $this->weixin_options->add($data);
    	}
    	if ($rst2!==false) {
    		$this->success("保存成功！",U('Weixin/index'));
    	} else {
    		$this->error("保存失败！");
    	}
    }
    /**
     * 删除
     */
    function delete($type = 0){
    	$id = I('id');
    	if($type){
    		$ids = I('ids');
    		if(empty($ids)){
    			$condition['id'] = $id;
    		}else{
    			$condition['id'] = array('in',$ids);
    		}
    		if($this->weixin_options->where($condition)->delete()){
    			$this->success('删除成功！');
    		}else{
    			$this->error('删除失败！');
    		}
    	}
    }
}