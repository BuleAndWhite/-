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
    <form class="well form-search" method="post" action="<?php echo U('Oauthadmins/index');?>">
        奖金类别：
        <select id="order_select" name="status">
            <option value="0"
            <?php echo $post_data['status'] == 0 ? 'selected' : ''; ?>
            >无</option>
            <option value="1"
            <?php echo $post_data['status'] == 1 ? 'selected' : ''; ?>
            >A类奖金</option>
            <option value="2"
            <?php echo $post_data['status'] == 2 ? 'selected' : ''; ?>
            >B类奖金</option>
            <option value="3"
            <?php echo $post_data['status'] == 3 ? 'selected' : ''; ?>
            >C类奖金</option>
        </select>
        &nbsp; &nbsp;
        时间：
        <input type="text" name="start_time" class="js-date" value="<?php echo ((isset($formget["start_time"]) && ($formget["start_time"] !== ""))?($formget["start_time"]):''); ?>"
               style="width: 80px;" autocomplete="off">-
        <input type="text" class="js-date" name="end_time" value="<?php echo ((isset($formget["end_time"]) && ($formget["end_time"] !== ""))?($formget["end_time"]):''); ?>" style="width: 80px;"
               autocomplete="off"> &nbsp; &nbsp;
        关键字：
        <input type="text" name="keyword" style="width: 200px;" value="<?php echo ((isset($formget["keyword"]) && ($formget["keyword"] !== ""))?($formget["keyword"]):''); ?>"
               placeholder="请输入关键字...">
        <input type="submit" class="btn btn-primary" value="搜索"/>&nbsp; &nbsp;

    </form>
    <div class="well form-search" method="post" action="<?php echo U('Oauthadmins/index');?>">
        <span style="margin-left:50px; ">总收益:</span>
        <span style=" color:#1abc9c"><?php echo ($userList['Grossprofit']); ?></span>&nbsp;
        <span style="margin-left:50px; ">艾灸仪本月收益:</span>
        <span style=" color:#1abc9c"><?php echo ($userList['Moxibustion']); ?></span>&nbsp;
        <span style="margin-left:50px; ">当月A奖金池:</span>
        <span style=" color:#1abc9c"><?php echo ($userList['A']); ?></span>&nbsp;
        <span style="margin-left:50px; ">当月B奖金池:</span>
        <span style=" color:#1abc9c"><?php echo ($userList['B']); ?></span>&nbsp;
        <span style="margin-left:50px; ">当月C奖金池:</span>
        <span style=" color:#1abc9c"><?php echo ($userList['C']); ?></span>&nbsp;
        <span style="margin-left:50px; ">总用户数量:</span>
        <span style="color:#1abc9c"><?php echo ($userList['count']); ?></span>&nbsp; &nbsp;
        <span style="margin-left:50px; ">今日新增用户:</span>
        <span style="color:#1abc9c"><?php echo ($userList['today']); ?></span>

    </div>

    <form method="post" class="js-ajax-form">
        <table class="table table-hover table-bordered">
            <thead>
            <tr>
                <th align="center">ID</th>
                <th>来源</th>
                <th>设备编号</th>
                <th>用户名</th>
                <th>手机号</th>
                <th>头像</th>
                <th>VIP</th>
                <!--                                <th>幸运用户</th>-->
                <th>艾灸奖金等级</th>
                <th>子用户</th>
                <th>艾灸团队(直+间)</th>
                <th>艾灸团队(直)</th>
                <th>首次登录时间</th>
                <th>最后登录时间</th>
                <th>最后登录IP</th>
                <th>积分</th>
            </tr>
            </thead>
            <tbody>
            <?php $user_statuses=array("0"=>"禁言","1"=>"正常"); $user_aga=array("0"=>"已禁言","1"=>"正常"); ?>
            <?php $status=array("0"=>"不是会员","1"=>"单次","2"=>"茉莉会员","3"=>"一年","4"=>"两年(创客)","5"=>"三年(合伙人)","7"=>"三年(天使机主)","8"=>"三年(机主)"); ?>
            <?php if(is_array($lists)): foreach($lists as $key=>$vo): ?><tr>
                    <td align="center"><?php echo ($vo["id"]); ?></td>
                    <td>微信</td>
                    <td><?php echo ($vo["parent_did"]); ?></td>
                    <td><?php echo ($vo["name"]); ?></td>
                    <td><?php echo ($vo["phone"]); ?></td>
                    <td><img width="25" height="25" src="<?php echo ($vo["avatar"]); ?>"/></td>
                    <td>
                        <a href="javascript:open_iframe_dialog('<?php echo U('comment/commentadmin/settingVip',array('idCard'=>$vo['idcard'],'unionid'=>$vo['unionid']));?>','设置VIP')"><?php echo ($status[$vo['is_vip']]); ?>&nbsp;(设置VIP)</a>
                    </td>
                    <td>
                        <a href="javascript:open_iframe_dialog('<?php echo U('comment/commentadmin/setingProxy',array('idCard'=>$vo['idcard'],'unionid'=>$vo['unionid']));?>','设置VIP')">
                           <span style = "color:red">
                            <?php if($vo["agent_level"] == 1): ?>A类奖金<?php endif; ?>
                            <?php if($vo["agent_level"] == 2): ?>B类奖金<?php endif; ?>
                            <?php if($vo["agent_level"] == 3): ?>C类奖金<?php endif; ?>
                               </span>
                            (设置奖金级别)
                        </a>
                    </td>
                    <!-- 设置幸运用户
                        <td>
                           <?php if($vo["lucky"] == 0 ): ?><a href="<?php echo U('Oauthadmins/setlucky',array('id'=>$vo['id']));?>" onclick='return setLucky();'>设置幸运用户</a>
                           <?php else: ?>
                           <a style = "color:red" href="<?php echo U('Oauthadmins/cancellucky',array('id'=>$vo['id']));?>" onclick='return cancelLucky();'>取消幸运用户</a><?php endif; ?>
                       </td>
                   -->
                    <td>
                        <a href="javascript:open_iframe_dialog('<?php echo U('comment/commentadmin/userList',array('unionid'=>$vo['unionid']));?>','用户列表')"><?php echo ($vo['count']); ?></a>
                    </td>
                    <td>
                        <a href="javascript:open_iframe_dialog('<?php echo U('comment/commentadmin/userLists',array('unionid'=>$vo['unionid']));?>','推荐(直+间)')"><?php echo ($vo['countMoxibustion']); ?></a>
                    </td>
                    <td>
                        <a href="javascript:open_iframe_dialog('<?php echo U('comment/commentadmin/userListz',array('unionid'=>$vo['unionid']));?>','推荐(直)')"><?php echo ($vo['countMoxibustions']); ?></a>
                    </td>
                    <td><?php echo ($vo['time']); ?></td>
                    <td><?php echo ($vo['update_time']); ?></td>
                    <td><?php echo ($vo["ip"]); ?></td>
                    <td><?php echo ($vo["integral"]); ?></td>
                    <!--					<td ><?php echo ($user_statuses[$vo['user_status']]); ?></td>-->

                    <!--					<td align="center">-->

                    <!--						<?php if($user["user_type"] == 2 ||$user["user_type"] == 3 ||$user["user_type"] == 4 ||$user["user_type"] == 5): ?>-->


                    <!--							<?php if($vo["is_vip"] == 4 ||$vo["is_vip"] == 5 ): ?>-->
                    <!--								<?php if($vo["is_backstage"] == -1): ?>-->
                    <!--									&lt;!&ndash;<a href="<?php echo U('admin/user/add',array('role_id'=>$parent_id,'id'=>$vo['id']));?>">添加后台账号</a>&ndash;&gt;-->
                    <!--									&lt;!&ndash;—&ndash;&gt;-->
                    <!--									<?php else: ?>-->
                    <!--									&lt;!&ndash;<a href="<?php echo U('admin/user/index');?>">查看后台账号</a>&ndash;&gt;-->
                    <!--									&lt;!&ndash;—&ndash;&gt;-->
                    <!--<?php endif; ?>-->
                    <!--								<?php else: ?>-->
                    <!--<?php endif; ?>-->
                    <!--							<?php else: ?>-->


                    <!--<?php endif; ?>-->
                    <!--&lt;!&ndash;						<a href="<?php echo U('oauthadmins/delete',array('iweixin d'=>$vo['id']));?>" class="js-ajax-delete"><?php echo L('DELETE');?></a>&ndash;&gt;-->
                    <!--&lt;!&ndash;						—&ndash;&gt;-->
                    <!--&lt;!&ndash;						<a href="<?php echo U('oauthadmins/ban',array('id'=>$vo['id']));?>" class="js-ajax-dialog-btn" data-msg="您确定要拉黑此用户吗?">拉黑</a>|&ndash;&gt;-->
                    <!--&lt;!&ndash;						<a href="<?php echo U('oauthadmins/cancelban',array('id'=>$vo['id']));?>" class="js-ajax-dialog-btn" data-msg="您确定要启用此用户吗?">启用</a>&ndash;&gt;-->
                    <!--						&lt;!&ndash;—&ndash;&gt;-->
                    <!--						&lt;!&ndash;<a href="<?php echo U('oauthadmin/isGag',array('unGag'=>$vo['id']));?>" class="js-ajax-dialog-btn" data-msg="您确定要禁言此用户吗?">禁言</a>|&ndash;&gt;-->
                    <!--						&lt;!&ndash;<a href="<?php echo U('oauthadmin/isGag',array('gag'=>$vo['id']));?>" class="js-ajax-dialog-btn" data-msg="您确定要启用此用户吗?">启用</a>&ndash;&gt;-->

                    <!--					</td>-->
                </tr><?php endforeach; endif; ?>
            </tbody>
        </table>
        <div class="pagination"><?php echo ($page); ?></div>
    </form>
</
>
<script src="/public/js/common.js"></script>
<script>
    function setLucky() {
        if (confirm("确定设置该用户为幸运用户吗？")) {
            return true;
        } else {
            return false;
        }
    }

    function cancelLucky() {
        if (confirm("确定取消该用户的幸运用户资格吗？")) {
            return true;
        } else {
            return false;
        }
    }
</script>
</body>
</html>