<?php
// +----------------------------------------------------------------------
// | ThinkCMF [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2014 http://www.thinkcmf.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: Dean <zxxjjforever@163.com>
// +----------------------------------------------------------------------
return array (
	'method' => array (// 在后台插件配置表单中的键名 ,会是config[text]
		'title' => 'method:', // 表单的label标题
		'type' => 'text',// 表单的类型：text,password,textarea,checkbox,radio,select等
		'value' => 'alibaba.aliqin.fc.sms.num.send',// 表单的默认值
		'tip' => '阿里大鱼的类型，默认为短信发送类型' //表单的帮助提示
	),
	'appkey' => array (// 在后台插件配置表单中的键名 ,会是config[text]
		'title' => 'appkey:', // 表单的label标题
		'type' => 'text',// 表单的类型：text,password,textarea,checkbox,radio,select等
		'value' => '',// 表单的默认值
		'tip' => '阿里大鱼的appkey' //表单的帮助提示
	),
	'secret' => array (// 在后台插件配置表单中的键名 ,会是config[text]
		'title' => 'secret:', // 表单的label标题
		'type' => 'text',// 表单的类型：text,password,textarea,checkbox,radio,select等
		'value' => '',// 表单的默认值
		'tip' => '阿里大鱼的secret' //表单的帮助提示
	),
);
					