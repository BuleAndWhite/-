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
<div class="wrap js-check-wrap">
    <ul class="nav nav-tabs">
        <li class="active"><a href="javascript:;">消息管理</a></li>
        <li><a href="<?php echo U('Message/add');?>">添加公告</a></li>
    </ul>
    <form class="well form-search" method="post" action="<?php echo U('Message/index');?>">

        时间：
        <input type="text" name="start_time" class="js-date" value="<?php echo ((isset($formget["start_time"]) && ($formget["start_time"] !== ""))?($formget["start_time"]):''); ?>"
               style="width: 80px;" autocomplete="off">-
        <input type="text" class="js-date" name="end_time" value="<?php echo ($formget["end_time"]); ?>" style="width: 80px;"
               autocomplete="off"> &nbsp; &nbsp;
        关键字：
        <input type="text" name="keyword" style="width: 200px;" value="<?php echo ($formget["keyword"]); ?>" placeholder="请输入关键字...">
        <input type="submit" class="btn btn-primary" value="搜索"/>
    </form>
    <form class="js-ajax-form" action="" method="post">

        <table class="table table-hover table-bordered table-list">
            <thead>
            <tr>
                <th width="15"><label><input type="checkbox" class="js-check-all" data-direction="x"
                                             data-checklist="js-check-x"></label></th>
                <th width="50">Id</th>
                <th width="50">标题</th>
                <th width="80">发布人</th>
                <th width="50">内容</th>
                <th width="50">接收人</th>
                <th width="70">创建时间</th>
                <th width="50"><?php echo L('STATUS');?></th>
                <th width="70"><?php echo L('ACTIONS');?></th>
            </tr>
            </thead>
            <?php $top_status=array("1"=>"已审核","0"=>"未审核"); ?>
            <?php if(is_array($messageContentList)): foreach($messageContentList as $key=>$vo): ?><tr>
                    <td><input type="checkbox" class="js-check" data-yid="js-check-y" data-xid="js-check-x" name="ids[]"
                               value="<?php echo ($vo["id"]); ?>" title="ID:<?php echo ($vo["id"]); ?>"></td>
                    <td><?php echo ($vo["id"]); ?></td>
                    <td> <?php echo ($vo["title"]); ?></td>
                    <td><?php echo ($vo["user_nicename1"]); ?></td>
                    <td><?php echo ($vo["content"]); ?></td>
                    <td><?php echo ($vo["user_nicename2"]); ?></td>
                    <td><?php echo ($vo["create_time"]); ?></td>
                    <td><?php echo ($top_status[$vo['status']]); ?>
                    </td>
                    <td>
                        <a href="<?php echo U('Message/edit', array('id'=>$vo['id']));?>"><?php echo L('EDIT');?></a>
                        <a href="<?php echo U('Message/delete',array('id'=>$vo['id']));?>"
                           class="js-ajax-delete"><?php echo L('DELETE');?></a></td>
                </tr><?php endforeach; endif; ?>
            <tfoot>
            <tr>
                <th width="15"><label><input type="checkbox" class="js-check-all" data-direction="x"
                                             data-checklist="js-check-x"></label></th>
                <th width="50">Id</th>
                <th width="50">标题</th>
                <th width="80">发布人</th>
                <th width="50">内容</th>
                <th width="50">接收人</th>
                <th width="70">创建时间</th>
                <th width="50"><?php echo L('STATUS');?></th>
                <th width="70"><?php echo L('ACTIONS');?></th>
            </tr>
            </tfoot>
        </table>

        <div class="pagination"><?php echo ($page); ?></div>
    </form>
</div>
<script src="/public/js/common.js"></script>
<script>
    function refersh_window() {
        var refersh_time = getCookie('refersh_time');
        if (refersh_time == 1) {
            window.location = "<?php echo U('Message/index',$formget);?>";
        }
    }

    setInterval(function () {
        refersh_window();
    }, 2000);
    $(function () {
        setCookie("refersh_time", 0);
        Wind.use('ajaxForm', 'artDialog', 'iframeTools', function () {
            //批量移动
            $('.js-articles-move').click(function (e) {
                var str = 0;
                var id = tag = '';
                $("input[name='ids[]']").each(function () {
                    if ($(this).attr('checked')) {
                        str = 1;
                        id += tag + $(this).val();
                        tag = ',';
                    }
                });
                if (str == 0) {
                    art.dialog.through({
                        id: 'error',
                        icon: 'error',
                        content: '您没有勾选信息，无法进行操作！',
                        cancelVal: '关闭',
                        cancel: true
                    });
                    return false;
                }
                var $this = $(this);
                art.dialog.open("/index.php?g=portal&m=AdminPost&a=move&ids=" + id, {
                    title: "批量移动",
                    width: "80%"
                });
            });
        });
    });
</script>
</body>
</html>