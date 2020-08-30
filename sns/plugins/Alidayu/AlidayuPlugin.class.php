<?php
// +----------------------------------------------------------------------
// | ThinkCMF [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2014 http://www.thinkcmf.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: Dean <zxxjjforever@163.com>
// +----------------------------------------------------------------------
namespace plugins\Alidayu;//Demo插件英文名，改成你的插件英文就行了
use Common\Lib\Plugin;

/**
 * Demo
 */
class AlidayuPlugin extends Plugin{//Demo插件英文名，改成你的插件英文就行了

        public $info = array(
            'name'=>'Alidayu',//Demo插件英文名，改成你的插件英文就行了
            'title'=>'阿里大鱼',
            'description'=>'阿里大鱼，用于短信发送',
            'status'=>1,
            'author'=>'Qshaw',
            'version'=>'1.0'
        );
        
        public $has_admin=1;//插件是否有后台管理界面

        public function install(){//安装方法必须实现
            return true;//安装成功返回true，失败false
        }

        public function uninstall(){//卸载方法必须实现
            return true;//卸载成功返回true，失败false
        }
        
        public function smssend($param){
        	$config=$this->getConfig();
        	include "sdk/TopSdk.php";
			date_default_timezone_set('Asia/Shanghai'); 
			$SmsParam = json_encode($param['SmsParam']);
			$c = new \TopClient;
			$c->method = $config['method'];
			$c->appkey = $config['appkey'];
			$c->secretKey = $config['secret'];
			$c->format = "json";
			$req = new \AlibabaAliqinFcSmsNumSendRequest;
			$req->setExtend($param['Extend']);
			$req->setSmsType("normal");
			$req->setSmsFreeSignName($param['SmsFreeSignName']);
			$req->setSmsParam($SmsParam);
			$req->setRecNum($param['RecNum']);
			$req->setSmsTemplateCode($param['SmsTemplateCode']);
			$resp = $c->execute($req, $sessionKey);
        }

    }