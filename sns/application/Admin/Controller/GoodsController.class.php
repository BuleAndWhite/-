<?php
// +----------------------------------------------------------------------
// | AnzaiSoft [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2015-2017 All rights reserved.
// +----------------------------------------------------------------------
// | Author: ZhangQingYuan <767684610@qq.com>
// +----------------------------------------------------------------------
namespace Admin\Controller;
use Common\Controller\AdminbaseController;
use Flc\Alidayu\Client;
use Flc\Alidayu\App;
use Flc\Alidayu\Requests\AlibabaAliqinFcSmsNumSend;
class GoodsController extends AdminbaseController {
	protected $goods_model;
	protected $terms_model;
	protected $taxonomys=array("article"=>"文章","picture"=>"图片");
	function _initialize() {
		parent::_initialize();
		$this->goods_model = D('goods');
		$this->terms_model = D("goods_cat");
		$this->assign("taxonomys",$this->taxonomys);
	}
	
	/**
	 * 商品管理
	 */
    public function index(){
    	$params['a.is_delete'] = '0';
    	//$count = $this->goods_model->where($params)->count();
    	$count = M('goods a')
    		->join('__GOODS_CAT__ b on a.cid=b.term_id')
    		->where($params)
    		->count();
    	$page = $this->page($count, 20);
    	$list = M('goods a')
    		->join('__GOODS_CAT__ b on a.cid=b.term_id')
    		->where($params)
	    	->order('a.listorder asc, a.create_time desc')
	    	->field('a.id,a.name,a.price,a.smeta,a.status,a.create_time,a.listorder,b.name as c_name')
	    	->limit($page->firstRow . ',' . $page->listRows)
	    	->select();
//     	dump($list);
    	$this->assign("page", $page->show('Admin'));
    	$this->assign("list", $list);
		$this->display();
    }
    //stdClass Object 转 数组
    private function objectArray($array){
	    if(is_object($array)){
	        $array = (array)$array;
	    }
	    if(is_array($array)){
	        foreach($array as $key=>$value){
	            $array[$key] = objectArray($value);
	        }
	    }
	    return $array;
	}
    public function add_post(){
    	if(IS_POST){
    		$data = I('post');
    		if(!empty($_POST['photos_alt']) && !empty($_POST['photos_url'])){
				foreach ($_POST['photos_url'] as $key=>$url){
					$photourl=sp_asset_relative_url($url);
					$_POST['smeta']['photo'][]=array("url"=>$photourl,"alt"=>$_POST['photos_alt'][$key]);
				}
			}
			$_POST['smeta']['thumb'] = sp_asset_relative_url($_POST['smeta']['thumb']);
			$data['smeta'] = json_encode($_POST['smeta']);
			$data['content'] = htmlspecialchars_decode($data['content']);
    		if(!empty($data['id'])){
                $data["update_time"] = date("Y:m:d H:i:s");
    			$rs = $this->goods_model->save($data);
    		}else{
                $data["create_time"] = date("Y:m:d H:i:s");
    			$rs = $this->goods_model->add($data);
    		}
    		if ($rs!==false) {
    			$this->success("保存成功！");exit;
    		} else {
    			$this->error("保存失败！");exit;
    		}
    	}
    	$this->list = $this->goods_model->select();
    	$this->display();
    }
    public function add(){
    	$terms = $this->terms_model->order(array("listorder"=>"asc"))->select();
    	$term_id = intval(I("get.term"));
    	$this->_getTermTree();
    	$term=$this->terms_model->where("term_id=$term_id")->find();
    	$this->assign("author","1");
    	$this->assign("term",$term);
    	$this->assign("terms",$terms);
    	$this->display();
    }
    private function _getTermTree($term=array()){
    	$result = $this->terms_model->order(array("listorder"=>"asc"))->select();
    
    	$tree = new \Tree();
    	$tree->icon = array('&nbsp;&nbsp;&nbsp;│ ', '&nbsp;&nbsp;&nbsp;├─ ', '&nbsp;&nbsp;&nbsp;└─ ');
    	$tree->nbsp = '&nbsp;&nbsp;&nbsp;';
    	foreach ($result as $r) {
    		$r['str_manage'] = '<a href="' . U("AdminTerm/add", array("parent" => $r['term_id'])) . '">添加子类</a> | <a href="' . U("AdminTerm/edit", array("id" => $r['term_id'])) . '">修改</a> | <a class="js-ajax-delete" href="' . U("AdminTerm/delete", array("id" => $r['term_id'])) . '">删除</a> ';
    		$r['visit'] = "<a href='#'>访问</a>";
    		$r['taxonomys'] = $this->taxonomys[$r['taxonomy']];
    		$r['id']=$r['term_id'];
    		$r['parentid']=$r['parent'];
    		$r['selected']=$r['term_id'] == $term?"selected":"";
//     		$r['checked'] =in_array($r['term_id'], $term)?"checked":"";
    		$array[] = $r;
    	}
    
    	$tree->init($array);
    	$str="<option value='\$id' \$selected>\$spacer\$name</option>";
    	$taxonomys = $tree->get_tree(0, $str);
    	$this->assign("taxonomys", $taxonomys);
    }
    //商品分类
    public function category(){
    	$result = $this->terms_model->order(array("listorder"=>"asc"))->select();
    	$tree = new \Tree();
    	$tree->icon = array('&nbsp;&nbsp;&nbsp;│ ', '&nbsp;&nbsp;&nbsp;├─ ', '&nbsp;&nbsp;&nbsp;└─ ');
    	$tree->nbsp = '&nbsp;&nbsp;&nbsp;';
    	foreach ($result as $r) {
    		$r['str_manage'] = '<a href="' . U("Goods/add_cat", array("parent" => $r['term_id'])) . '">添加子类</a> | <a href="' . U("Goods/edit_cat", array("id" => $r['term_id'])) . '">'.L('EDIT').'</a> | <a class="js-ajax-delete" href="' . U("Goods/delete_cat", array("id" => $r['term_id'])) . '">'.L('DELETE').'</a> ';
    		$url=U('portal/list/index',array('id'=>$r['term_id']));
    		$r['url'] = $url;
    		$r['taxonomys'] = $this->taxonomys[$r['taxonomy']];
    		$r['id']=$r['term_id'];
    		$r['parentid']=$r['parent'];
    		$array[] = $r;
    	}
    	
    	$tree->init($array);
    	$str = "<tr>
					<td><input name='listorders[\$id]' type='text' size='3' value='\$listorder' class='input input-order'></td>
					<td>\$id</td>
					<td>\$spacer \$name</td>
					<td>\$str_manage</td>
				</tr>";
    	$taxonomys = $tree->get_tree(0, $str);
    	$this->assign("taxonomys", $taxonomys);
    	$this->display();
    }
    function add_cat(){
    	$parentid = intval(I("get.parent"));
    	$tree = new \Tree();
    	$tree->icon = array('&nbsp;&nbsp;&nbsp;│ ', '&nbsp;&nbsp;&nbsp;├─ ', '&nbsp;&nbsp;&nbsp;└─ ');
    	$tree->nbsp = '&nbsp;&nbsp;&nbsp;';
    	$terms = $this->terms_model->order(array("path"=>"asc"))->select();
    	 
    	$new_terms=array();
    	foreach ($terms as $r) {
    		$r['id']=$r['term_id'];
    		$r['parentid']=$r['parent'];
    		$r['selected']= (!empty($parentid) && $r['term_id']==$parentid)? "selected":"";
    		$new_terms[] = $r;
    	}
    	$tree->init($new_terms);
    	$tree_tpl="<option value='\$id' \$selected>\$spacer\$name</option>";
    	$tree=$tree->get_tree(0,$tree_tpl);
    	 
    	$this->assign("terms_tree",$tree);
    	$this->assign("parent",$parentid);
    	$this->display();
    }
    
    function add_cat_post(){
    	if (IS_POST) {
    		if ($this->terms_model->create()) {
    			if ($this->terms_model->add()!==false) {
    				F('all_terms',null);
    				$this->success("添加成功！",U("Goods/category"));
    			} else {
    				$this->error("添加失败！");
    			}
    		} else {
    			$this->error($this->terms_model->getError());
    		}
    	}
    }
    
    function edit_cat(){
    	$id = intval(I("get.id"));
    	$data=$this->terms_model->where(array("term_id" => $id))->find();
    	$tree = new \Tree();
    	$tree->icon = array('&nbsp;&nbsp;&nbsp;│ ', '&nbsp;&nbsp;&nbsp;├─ ', '&nbsp;&nbsp;&nbsp;└─ ');
    	$tree->nbsp = '&nbsp;&nbsp;&nbsp;';
    	$terms = $this->terms_model->where(array("term_id" => array("NEQ",$id), "path"=>array("notlike","%-$id-%")))->order(array("path"=>"asc"))->select();
    
    	$new_terms=array();
    	foreach ($terms as $r) {
    		$r['id']=$r['term_id'];
    		$r['parentid']=$r['parent'];
    		$r['selected']=$data['parent']==$r['term_id']?"selected":"";
    		$new_terms[] = $r;
    	}
    
    	$tree->init($new_terms);
    	$tree_tpl="<option value='\$id' \$selected>\$spacer\$name</option>";
    	$tree=$tree->get_tree(0,$tree_tpl);
    
    	$this->assign("terms_tree",$tree);
    	$this->assign("data",$data);
    	$this->display();
    }
    
    function edit_cat_post(){
    	if (IS_POST) {
    		if ($this->terms_model->create()) {
    			if ($this->terms_model->save()!==false) {
    				F('all_terms',null);
    				$this->success("修改成功！");
    			} else {
    				$this->error("修改失败！");
    			}
    		} else {
    			$this->error($this->terms_model->getError());
    		}
    	}
    }
    
    //排序
    public function listorders_cat() {
    	$status = parent::_listorders($this->terms_model);
    	if ($status) {
    		$this->success("排序更新成功！");
    	} else {
    		$this->error("排序更新失败！");
    	}
    }
    
    /**
     *  删除
     */
    public function delete_cat() {
    	$id = intval(I("get.id"));
    	$count = $this->terms_model->where(array("parent" => $id))->count();
    
    	if ($count > 0) {
    		$this->error("该菜单下还有子类，无法删除！");
    	}
    
    	if ($this->terms_model->delete($id)!==false) {
    		$this->success("删除成功！");
    	} else {
    		$this->error("删除失败！");
    	}
    }
    /**
     * 修改
     */
    public function  edit(){
    	$id = I('id');
    	$info = $this->goods_model->find($id);
    	$this->assign('info', $info);
    	$this->assign("smeta",json_decode($info['smeta'],true));
    	$this->_getTermTree($info['cid']);
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
    //排序
    public function listorders() {
    	$status = parent::_listorders($this->goods_model);
    	if ($status) {
    		$this->success("排序更新成功！");
    	} else {
    		$this->error("排序更新失败！");
    	}
    }
    /**
     * 删除
     */
    public function delete($type = 0){
    	if(isset($_POST['ids'])){
    		$ids=join(",",$_POST['ids']);
    		$data['is_delete'] = 1;
    		if ($this->goods_model->where("id in ($ids)")->setField('is_delete', 1)) {
    			$this->success("删除成功！");
    		} else {
    			$this->error("删除失败！");
    		}
    	}else{
    		$id = intval(I("get.id"));
    		if ($this->goods_model->where(array('id'=>$id))->setField('is_delete', 1)) {
    			$this->success("删除成功！");
    		} else {
    			$this->error("删除失败！");
    		}
    	}
    }
}