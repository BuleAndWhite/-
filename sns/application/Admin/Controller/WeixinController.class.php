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
class WeixinController extends AdminbaseController {
	protected $weixin_model;
	protected $weixin_options;
	function _initialize() {
		parent::_initialize();
		parent::check_token();
		$this->weixin_model = D("Common/WeixinMenu");
		$this->weixin_options = D("Common/WeixinOptions");
	}
	/**
	 * 开发者设置
	 */
    public function index(){
    	//echo $this->weixin->test();
    	//dump(json_decode(M('options')->where("option_name='weixin_options'")->getField('option_value'),true));
    	if(IS_POST){
    		dump($_POST);
    	}
    	$this->weixin_list = $this->weixin_options->order('ID asc')->select();
    	$this->display();
    }
    public function oauth(){
    	if(IS_POST){
    		$ids = I('ids');
    		M('wechat_config')->where('id=1')->save(array('config' => implode(',', $ids)));
    		//echo implode(',', $ids);
    		$this->success('操作成功！');
    	}
    	$wechat_config = M('wechat_config')->where('id=1')->getField('config');
    	$this->assign('wechat_config', $wechat_config);
    	$this->user_list = M('oauth_user')->select();
    	$this->display();
    }
    public function add(){
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
    	$this->display();
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
     * 欢迎语
     * */
    public function welcome(){
    	$map['app_wxid'] = session('TOKEN');
    	if(IS_POST){
    		$data['welcome_config'] = json_encode(I('info'));
    		$this->weixin_options->where($map)->save($data);
    		$this->success("保存成功！");
    	}
    	$this->info = json_decode($this->weixin_options->where($map)->getField('welcome_config'),true);
    	$this->display();
    }
    /**
     * 同步微信菜单
     */
    public function create_menu(){
    	$map['parent_id'] = '0';
    	$map['token'] = session('TOKEN');
    	$menu_list = $this->weixin_model->where($map)->order('listorder,id')->select();
    	$menu_data = array();
    	$tarr1 = array('view','view_limited','click');
    	$tarr2 = array('url','media_id','key');
    	foreach ($menu_list as $k => $v){
    		$rs = $this->weixin_model->where(array('parent_id'=>$v['id']))->order('listorder,id')->select();
    		$temparr1 = array();
    		if($rs){
    			$temparr1['name'] = urlencode($v['menu_name']);
    			foreach ($rs as $key => $value){
    				$temparr = array();
    				$temparr['type'] = $tarr1[$value['type']];
    				$temparr['name'] = urlencode($value['menu_name']);
    				$temparr[$tarr2[$value['type']]] = urlencode($value['comment']);
    				$temparr1['sub_button'][] = $temparr;
    			}
    		}else{
    			$temparr1['type'] = $tarr1[$v['type']];
    			$temparr1['name'] = urlencode($v['menu_name']);
    			$temparr1['url'] = urlencode($v['comment']);
    		}
    		$menu_data[] = $temparr1;
    	}
    	$result = create_menu($menu_data);
    	$this->result = $result;
		$this->display();
    }
    /**
     *加载微信素材
     */
    public function select($type = 'news', $offset = 0, $count = 20){
    	$material_data = array('type'=>$type, 'offset'=>$offset, 'count'=>$count);
    	$list = getbatch_material($material_data);
    	$this->assign('list',$list['item']);
    	$map['app_wxid'] = session('TOKEN');
    	$this->assign('weixin_options', $this->weixin_options->where($map)->find());
    	$type = $type == 'video' ? 'voice' : $type;
    	$content = $this->fetch('select_'.$type);
    	echo $content;
    }
    
    /**
     * 添加菜单
     */
    public function add_menu(){
//     	echo $this->weixin->access_token.'<br/>';
// 		$data = array('media_id'=>'2iNTDvjGboLNriGFn7Wxp3pG1H01wbvPKiMvvqf7s5o');
// 		echo json_encode($data);
//     	print_r($this->weixin->get_media('2iNTDvjGboLNriGFn7Wxp3mBx0ve_2dcsB1O8RSNFTU'));
    	$this->t = $_REQUEST['t'];
    	$this->menu_list = $this->weixin_model->where('parent_id=0')->order('listorder,id')->select();
    	$this->display('add_menu');
    }
    /**
     * 修改
     */
    public function  edit(){
    	$this->id = I('id');
    	$this->menu_info = $this->weixin_model->where(array('id'=>$this->id))->find();
    	$token = session('TOKEN');
    	$map['token'] = $token;
    	$map['parent_id'] = '0';
    	$this->menu_list = $this->weixin_model->where($map)->order('parent_id,listorder')->select();
    	$this->t = $this->menu_info['type'] ? 'c' : 'b';
    	$this->display('add_menu');
    }
    /**
     * 删除
     */
    public function delete($type = 0){
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
    	$token = session('TOKEN');
    	$map['token'] = $token;
    	$map['parent_id'] = $id;
    	if($this->weixin_model->where($map)->find()){
    		$this->error('请先删除子菜单！');
    	}
    	if($this->weixin_model->where(array('id'=>$id))->delete()){
    		$this->success('删除成功！');
    	}else{
    		$this->error('删除失败！');
    	}
    }
    /**
     * 菜单添加
     */
    public function add_post(){
    	$info = $_POST['info'];
    	if(empty($info['menu_name'])){
    		$this->error('菜单名称不能为空！');
    	}
    	if(empty($info['id'])){
    		$token = session('TOKEN');
    		$map['token'] = $token;
    		if(empty($info['parent_id'])){
    			$map['parent_id'] = '0';
    			$count = $this->weixin_model->where($map)->order('listorder,id')->count();
    			if($count >= 3){
    				$this->error('一级菜单最多设置三个！');
    			}
    		}else{
    			$map['parent_id'] = $info['parent_id'];
    			$count = $this->weixin_model->where($map)->order('listorder asc,id asc')->count();
    			if($count >= 5){
    				$this->error('二级菜单最多设置五个！');
    			}
    		}
    		$info['token'] = $token;
    		$rs = $this->weixin_model->add($info);
    	}else{
    		$rs = $this->weixin_model->save($info);
    	}
    	if($rs){
    		$this->success('保存成功！',U('Weixin/menu'));
    	}else{
    		$this->error('保存失败！');
    	}
    }
    
    /**
     *菜单列表 
     */
    public function menu(){
    	$map['token'] = session('TOKEN');
    	$result = $this->weixin_model->where($map)->order('listorder,id')->select();
		$tree = new \Tree();
		$tree->icon = array('&nbsp;&nbsp;&nbsp;│ ', '&nbsp;&nbsp;&nbsp;├─ ', '&nbsp;&nbsp;&nbsp;└─ ');
		$tree->nbsp = '&nbsp;&nbsp;&nbsp;';
		$type = array('打开网址','回复消息');
		foreach ($result as $r) {
			$r['str_manage'] = '<a href='.U('Weixin/edit',array('id'=>$r['id'])).'>'.L('EDIT').'</a> | <a href="'.U('Weixin/delete',array('id'=>$r['id'])).'" class="js-ajax-delete">'.L('DELETE').'</a>';
			$url = U('Weixin/edit',array('id'=>$r['id']));
			$r['url'] = $url;
			$r['id']=$r['id'];
			
			$r['parentid']=$r['parent_id'];
			if(!$r['type']){
				$r['comment'] = "<div style='width: 480px;overflow: hidden;padding: 10px;'><a style='width' href='".$r['comment']."' target='_blank'>".$r['comment']."</a></div>";
			}
			if($this->weixin_model->where(array('parent_id'=>$r['id']))->find() && !$r['parent_id']){
				$r['comment'] = '有二级菜单无需设置';
			}
			$r['type'] = $type[$r['type']];
			$array[] = $r;
		}
		//<empty name="vo.comment">有二级菜单无需设置<else/>{$vo.comment}</empty>
		$tree->init($array);
		$str = "<tr>
					<td><input name='listorders[\$id]' class='input input-order mr5' type='text' size='3' value='\$listorder'></td>
					<td><a>\$id</a></td>
					<td>\$spacer <a href='\$url'><span>\$menu_name</span></a></td>
					 <td>\$type</td>
					<td>\$comment</td>
					<td>
						\$str_manage
					</td>
				</tr>";
		/*
		$str = "<tr>
					<td><input name='listorders[\$id]' type='text' size='3' value='\$listorder' class='input input-order'></td>
					<td>\$id</td>
					<td>\$spacer <a href='\$url' target='_blank'>\$name</a></td>
	    			<td>\$taxonomys</td>
					<td>\$str_manage</td>
				</tr>";
				*/
		$taxonomys = $tree->get_tree(0, $str);
		$this->assign("taxonomys", $taxonomys);
		$this->display();
    }
    //排序
    public function listorders() {
    	$status = parent::_listorders($this->weixin_model);
    	if ($status) {
    		$this->success("排序更新成功！");
    	} else {
    		$this->error("排序更新失败！");
    	}
    }
}