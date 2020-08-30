<?php
// +----------------------------------------------------------------------
// | ThinkCMF [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2014 http://www.thinkcmf.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: Dean <zxxjjforever@163.com>
// +----------------------------------------------------------------------
namespace Portal\Controller;
use Common\Controller\HomebaseController; 
use Admin\Controller\PublicController;
/**
 * 首页
 */
class TestController extends HomebaseController {
	function _initialize(){
		parent::_initialize();
		header("Content-type: text/html; charset=utf-8");
	}
	public function demo16(){
		dump(json_decode('"\u53f6\u5c0f\u4fca"', true));
	}
}


