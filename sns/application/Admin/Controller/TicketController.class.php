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
class TicketController extends AdminbaseController {
	protected $ticket_model;
	function _initialize() {
		parent::_initialize();
		$this->ticket_model = M('ticket');
	}
	/**
	 * 门票管理
	 */
    public function index(){
    	$result = $this->ticket_model->select();
		$tree = new \Tree();
		$tree->icon = array('&nbsp;&nbsp;&nbsp;│ ', '&nbsp;&nbsp;&nbsp;├─ ', '&nbsp;&nbsp;&nbsp;└─ ');
		$tree->nbsp = '&nbsp;&nbsp;&nbsp;';
		$type = array('当日票','预售票');
		foreach ($result as $r) {
			$r['str_manage'] = '<a href='.U('Ticket/edit',array('id'=>$r['id'])).'>'.L('EDIT').'</a> | <a href="'.U('Ticket/delete',array('id'=>$r['id'])).'" class="js-ajax-delete">'.L('DELETE').'</a>';
			//$r['ticket_type'] = $type[$r['ticket_type']];
			$r['parentid']=$r['parent_id'];
			$array[] = $r;
		}
		//<empty name="vo.comment">有二级菜单无需设置<else/>{$vo.comment}</empty>
		$tree->init($array);
		$str = "<tr>
					<td><a>\$id</a></td>
					<td>\$spacer \$name</td>
					<td>\$price</td>
					<td>\$comment</td>
					<td>
						\$str_manage
					</td>
				</tr>";
		$taxonomys = $tree->get_tree(0, $str);
		$this->assign("taxonomys", $taxonomys);
		$this->display();
    }
    public function add(){
    	if(IS_POST){
    		$data = I('info');
    		$data['start_date'] = strtotime($data['start_date']);
    		$data['end_date'] = strtotime($data['end_date']);
    		if(!empty($data['id'])){
    			$rs = $this->ticket_model->save($data);
    		}else{
    			$rs = $this->ticket_model->add($data);
    		}
    		if ($rs!==false) {
    			$this->success("保存成功！", U('Admin/Ticket/index'));
    		} else {
    			$this->error("保存失败！");
    		}
    	}
    	$this->list = $this->ticket_model->where(array('parent_id'=>'0'))->select();
    	$this->display();
    }
    /**
     * 修改
     */
    public function  edit(){
    	$this->id = I('id');
    	$this->list = $this->ticket_model->where(array('parent_id'=>'0'))->select();
    	$this->ticket_info = $this->ticket_model->where(array('id'=>$this->id))->find();
    	$this->display('add');
    }
    public function index_post(){
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
    public function delete($type = 0){
    	$condition['id'] = I('id');
    	if($this->ticket_model->where($condition)->delete()){
    		$this->success('删除成功！');
    	}else{
    		$this->error('删除失败！');
    	}
    }
}