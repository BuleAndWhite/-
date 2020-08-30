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
class WeixinReplyController extends AdminbaseController {
	protected $reply_model;
	protected $weixin_reply_text;
	function _initialize() {
		parent::_initialize();
		parent::check_token();
		$this->reply_model =D("Common/Reply");
		$this->weixin_reply_text = M('weixin_reply_text');
		$this->assign('keyword_type',array('完全匹配', '左边匹配', '右边匹配', '模糊匹配'));
		$this->assign('group_list', get_groups());
	}
	//图文
	function index() {
		$condition['token'] = session('TOKEN');
		$this->list = $this->weixin_reply_text->where($condition)->select();
		$this->display();
	}
	//添加文本回复
	function add_text(){
		$condition['token'] = session('TOKEN');
		if(IS_POST){
			$data = I('info');
			if(empty($data['id'])){
				$data['token'] = $condition['token'];
				$result = $this->weixin_reply_text->add($data);
			}else{
				$result = $this->weixin_reply_text->save($data);
			}
			$this->success('保存成功！', U('WeixinReply/index'));
		}
		if(isset($_GET['id'])){
			$condition['id'] = I('id');
			$this->info = $this->weixin_reply_text->where($condition)->find();
		}
		$this->display();
	}
	//多图文
	function mult(){
		//dump($this->reply_model);exit;
		$this->display();
	}
	//文本
	function news(){
		$this->display();
	}
	function add_news(){
		$this->display();
	}
	function add_mult(){
		$this->display();
	}
	function delete(){
		if(isset($_POST['ids'])){
			$ids = implode(",", $_POST['ids']);
			if ($this->weixin_reply_text->where("id in ($ids)")->delete()!==false) {
				$this->success("删除成功！");
			} else {
				$this->error("删除失败！");
			}
		}else{
			$id = intval(I("get.id"));
			if ($this->weixin_reply_text->delete($id)!==false) {
				$this->success("删除成功！");
			} else {
				$this->error("删除失败！");
			}
		}
	}
}