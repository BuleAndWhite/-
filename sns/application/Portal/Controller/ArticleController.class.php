<?php
// +----------------------------------------------------------------------
// | ThinkCMF [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2014 http://www.thinkcmf.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: Dean <zxxjjforever@163.com>
// +----------------------------------------------------------------------
/**
 * 文章内页
 */
namespace Portal\Controller;
use Common\Controller\HomebaseController;
class ArticleController extends HomebaseController {
    //文章内页
    public function index() {
    	$id=intval($_GET['id']);
//    	$article=sp_sql_post($id,'');
//
//    	if(empty($article)){
//    	    header('HTTP/1.1 404 Not Found');
//    	    header('Status:404 Not Found');
//    	    if(sp_template_file_exists(MODULE_NAME."/404")){
//    	        $this->display(":404");
//    	    }
//    	    return ;
//    	}

        $term=M("term_relationships")->where(array("tid"=>$id))->find();

        M("posts")->where(array("id"=>$term['object_id']))->setInc('readss',1);

        $post =M("posts")->where(array("id"=>$term['object_id']))->field("readss,post_title,post_excerpt,post_content,post_modified")->find();

        $this->assign("postList",$post);
//    	$tplname=$term["one_tpl"];
//    	$tplname=sp_get_apphome_tpl($tplname, "index");
    	$this->display(":index");
    }

    public function do_like(){
    	$this->check_login();

    	$id=intval($_GET['id']);//posts表中id

    	$posts_model=M("Posts");

    	$can_like=sp_check_user_action("posts$id",1);

    	if($can_like){
    		$posts_model->save(array("id"=>$id,"post_like"=>array("exp","post_like+1")));
    		$this->success("赞好啦！");
    	}else{
    		$this->error("您已赞过啦！");
    	}

    }
}
