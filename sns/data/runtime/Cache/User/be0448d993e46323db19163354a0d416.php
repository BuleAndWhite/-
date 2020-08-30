<?php if (!defined('THINK_PATH')) exit();?><!doctype html>
<html>
<head>
	<meta charset="utf-8">
	<!-- Set render engine for 360 browser -->
	<meta name="renderer" content="webkit">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <!-- HTML5 shim for IE8 support of HTML5 elements -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <![endif]-->

	<link href="/public/simpleboot/themes/<?php echo C('SP_ADMIN_STYLE');?>/theme.min.css" rel="stylesheet">
    <link href="/public/simpleboot/css/simplebootadmin.css" rel="stylesheet">
    <link href="/public/js/artDialog/skins/default.css" rel="stylesheet" />
    <link href="/public/simpleboot/font-awesome/4.4.0/css/font-awesome.min.css"  rel="stylesheet" type="text/css">
    <style>
		.length_3{width: 180px;}
		form .input-order{margin-bottom: 0px;padding:3px;width:40px;}
		.table-actions{margin-top: 5px; margin-bottom: 5px;padding:0px;}
		.table-list{margin-bottom: 0px;}
	</style>
	<!--[if IE 7]>
	<link rel="stylesheet" href="/public/simpleboot/font-awesome/4.4.0/css/font-awesome-ie7.min.css">
	<![endif]-->
<script type="text/javascript">
//全局变量
var GV = {
    DIMAUB: "/",
    JS_ROOT: "public/js/",
    TOKEN: ""
};
</script>
<!-- Le javascript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="/public/js/jquery.js"></script>
    <script src="/public/js/wind.js"></script>
    <script src="/public/simpleboot/bootstrap/js/bootstrap.min.js"></script>
<?php if(APP_DEBUG): ?><style>
		#think_page_trace_open{
			z-index:9999;
		}
	</style><?php endif; ?>
</head>
<body>
	<div class="wrap">
		<ul class="nav nav-tabs">
			<li class="active"><a><?php echo L('USER_OAUTHADMIN_INDEX');?></a></li>
			<!--<li ><a href="<?php echo U('Oauthadmin/add');?>">添加用户</a></li>-->
		</ul>
		<form class="well form-search" method="post" action="<?php echo U('Oauthadmin/index');?>">
			时间：
			<input type="text" name="start_time" class="js-date" value="<?php echo ((isset($formget["start_time"]) && ($formget["start_time"] !== ""))?($formget["start_time"]):''); ?>" style="width: 80px;" autocomplete="off">-
			<input type="text" class="js-date" name="end_time" value="<?php echo ((isset($formget["end_time"]) && ($formget["end_time"] !== ""))?($formget["end_time"]):''); ?>" style="width: 80px;" autocomplete="off"> &nbsp; &nbsp;
			关键字：
			<input type="text" name="keyword" style="width: 200px;" value="<?php echo ((isset($formget["keyword"]) && ($formget["keyword"] !== ""))?($formget["keyword"]):''); ?>" placeholder="请输入关键字...">
			<input  type="submit" class="btn btn-primary" value="搜索" />&nbsp; &nbsp;
			<span style="margin-left:100px; ">累积收益:</span>
			<span style=" color:#1abc9c"><?php echo ($userList['totalRevenue']); ?></span>&nbsp; &nbsp;
			<span style="margin-left:10px; ">总用户数量:</span>
			<span style="color:#1abc9c"><?php echo ($userList['count']); ?></span>&nbsp; &nbsp;
			<?php if($user["user_type"] == 2||$user["user_type"] == 3||$user["user_type"] == 4||$user["user_type"] == 5): ?><span style="margin-left:10px; ">可取款:</span>
			<span style="color:#1abc9c"><?php echo ($userList['total']); ?></span>&nbsp; &nbsp;<?php endif; ?>
			<span style="margin-left:10px; ">今日新增用户:</span>
			<span style="color:#1abc9c"><?php echo ($userList['today']); ?></span>
            <?php if($userOneType == 3 ||$userOneType == 4||$userOneType == 5): ?>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp;<a href='<?php echo U("admin/user/owner",array("did"=>$userOneDid,"user_type"=>$userOneType));?>'>我的推广码</a><?php endif; ?>
		</form>
		<form class="js-ajax-form" method="post" action="<?php echo U('Oauthadmin/quotation');?>">
			<div class="table-actions" style="float: right; position: absolute;top: 90px;left: 1540px;">
				<input type="hidden" name="project_id" value="<?php echo ($project_id); ?>">
				<input type="submit" class="btn btn-primary" value="导出所有用户信息"/>
			</div>
		</form>
		<form method="post" class="js-ajax-form">
			<table class="table table-hover table-bordered">
				<thead>
					<tr>
						<th align="center">ID</th>
						<th><?php echo L('USER_FROM');?></th>
						<th>设备编号</th>
						<th><?php echo L('USERNAME');?></th>
						<th>手机号</th>
						<th><?php echo L('AVATAR');?></th>
						<th>VIP</th>
						<th>子用户</th>
						<!--<th><?php echo L('BINGDING_ACCOUNT');?></th>-->
						<th><?php echo L('FIRST_LOGIN_TIME');?></th>
						<th><?php echo L('LAST_LOGIN_TIME');?></th>
						<th><?php echo L('LAST_LOGIN_IP');?></th>
						<th>积分</th>

						<th>状态</th>
						<th align="center"><?php echo L('ACTIONS');?></th>
					</tr>
				</thead>
				<tbody>
				<?php $user_statuses=array("0"=>"禁言","1"=>"正常"); $user_aga=array("0"=>"已禁言","1"=>"正常"); ?>
				<?php $status=array("0"=>"不是会员","1"=>"单次","2"=>"半年","3"=>"一年","4"=>"两年(创客)","5"=>"三年(合伙人)","7"=>"三年(天使机主)","8"=>"三年(机主)"); ?>
					<?php if(is_array($lists)): foreach($lists as $key=>$vo): ?><tr>
						<td align="center"><?php echo ($vo["id"]); ?></td>
						<td>微信</td>
						<td><?php echo ($vo["machine"]); ?></td>
						<td><?php echo ($vo["name"]); ?></td>
						<td><?php echo ($vo["phone"]); ?></td>
						<td><img width="25" height="25" src="<?php echo ($vo["avatar"]); ?>" /></td>
						<?php if($user["user_type"] == 1): ?><td><a href="javascript:open_iframe_dialog('<?php echo U('comment/commentadmin/settingVip',array('idCard'=>$vo['idcard'],'unionid'=>$vo['unionid']));?>','设置VIP')"><?php echo ($status[$vo['is_vip']]); ?>&nbsp;(设置VIP)</a></td>
							<?php else: ?>
							<td><?php echo ($status[$vo['is_vip']]); ?></td><?php endif; ?>
						<td><a href="javascript:open_iframe_dialog('<?php echo U('comment/commentadmin/userList',array('unionid'=>$vo['unionid']));?>','用户列表')"><?php echo ($vo['count']); ?></a></td>

						<!--<td><?php echo ((isset($vo['uid']) && ($vo['uid'] !== ""))?($vo['uid']):'无'); ?></td>-->						<td><?php echo ($vo['time']); ?></td>
						<td><?php echo ($vo['update_time']); ?></td>

						<td><?php echo ($vo["ip"]); ?></td>
						<td><?php echo ($vo["integral"]); ?></td>
						<td ><?php echo ($user_statuses[$vo['user_status']]); ?></td>

						<td align="center">

							<?php if($user["user_type"] == 2 ||$user["user_type"] == 3 ||$user["user_type"] == 4 ||$user["user_type"] == 5): if($vo["is_vip"] == 4 ||$vo["is_vip"] == 5 ): if($vo["is_backstage"] == -1): ?><a href="<?php echo U('admin/user/add',array('role_id'=>$parent_id,'id'=>$vo['id']));?>">添加后台账号</a>
									—
									<?php else: ?>
									<a href="<?php echo U('admin/user/index');?>">查看后台账号</a>
									—<?php endif; ?>
								<?php else: endif; ?>
								<?php else: endif; ?>
							<a href="<?php echo U('oauthadmin/delete',array('iweixin d'=>$vo['id']));?>" class="js-ajax-delete"><?php echo L('DELETE');?></a>
							—
							<a href="<?php echo U('oauthadmin/ban',array('id'=>$vo['id']));?>" class="js-ajax-dialog-btn" data-msg="您确定要拉黑此用户吗?">拉黑</a>|
							<a href="<?php echo U('oauthadmin/cancelban',array('id'=>$vo['id']));?>" class="js-ajax-dialog-btn" data-msg="您确定要启用此用户吗?">启用</a>
							<!--—-->
							<!--<a href="<?php echo U('oauthadmin/isGag',array('unGag'=>$vo['id']));?>" class="js-ajax-dialog-btn" data-msg="您确定要禁言此用户吗?">禁言</a>|-->
							<!--<a href="<?php echo U('oauthadmin/isGag',array('gag'=>$vo['id']));?>" class="js-ajax-dialog-btn" data-msg="您确定要启用此用户吗?">启用</a>-->

						</td>
					</tr><?php endforeach; endif; ?>
				</tbody>
			</table>
			<div class="pagination"><?php echo ($page); ?></div>
		</form>
	</div>
	<script src="/public/js/common.js"></script>
</body>
</html>