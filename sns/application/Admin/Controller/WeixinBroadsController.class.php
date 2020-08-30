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
class WeixinBroadsController extends AdminbaseController {
	protected $send_model;
	protected $msg_type = array('news'=>'图文', 'voice'=>'语音', 'image'=>'图片', 'video'=>'视频');
	function _initialize() {
		parent::_initialize();
		parent::check_token();
		$this->assign('msg_type', $this->msg_type);
		$this->send_model =D("Common/Send");
		$this->assign('group_list', get_groups());
	}
	/**
	 *  显示
	 */
	public function index() {
		$map['token'] = session('TOKEN');
		$send_list=$this->send_model->where($map)->order('sendtime asc,id asc')->select();
		//dump($send_list);
		$this->assign("send_list",$send_list);
		$this->display();
	}
	
	/**
	 *  添加
	 */
	function add(){
		//dump(get_groups());
		//dump(get_user());
		//dump(json_decode($str,true));
		//echo strtotime('2016-02-18 15:58:02');//date('Y-m-d H:i:s',time()-60*60*24*4);
		$this->display();
	}
	/**
	 *  添加
	 */
	function add_post(){
		$info_data = $_POST['info'];
		$send_time = $_POST['sendtime'];
		$mktime = mktime($send_time['hour'],$send_time['min'],0,$send_time['month'],$send_time['day'],$send_time['year']);
		$info_data['sendtime'] = $mktime;
		if(empty($info_data['media_id'])){
			$this->error('没有选择群发素材');
		}
		if(empty($info_data['id'])){
			$info_data['token'] = session('TOKEN');
			$status = $this->send_model->add($info_data);
		}else{
			$status = $this->send_model->save($info_data);
		}
		if($status){
			$this->success("保存成功！");
		}else{
			$this->success("保存失败！");
		}
	}
	/**
	 * 编辑
	 */
	function edit(){
		$id= intval(I("get.id"));
		$send_info=$this->send_model->where("id=$id")->find();
		$this->assign('send_info',$send_info);
		$this->display('add');
	}
	
	/**
	 * 编辑
	 */
	function edit_post(){
		if (IS_POST) {
			if(empty($_POST['active'])){
				$_POST['active']=0;
			}else{
				$this->send_model->where("active=1")->save(array("active"=>0));
			}
			if ($this->send_model->create()) {
				if ($this->send_model->save() !== false) {
					$this->success("保存成功！", U("navcat/index"));
				} else {
					$this->error("保存失败！");
				}
			} else {
				$this->error($this->send_model->getError());
			}
		}
	}
	
	
	function delete(){
		if(isset($_POST['ids'])){
			$ids = implode(",", $_POST['ids']);
			if ($this->send_model->where("id in ($ids)")->delete()!==false) {
				$this->success("删除成功！");
			} else {
				$this->error("删除失败！");
			}
		}else{
			$id = intval(I("get.id"));
			if ($this->send_model->delete($id)!==false) {
				$this->success("删除成功！");
			} else {
				$this->error("删除失败！");
			}
		}
	}
}