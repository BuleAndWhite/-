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
        <li class="active"><a href="javascript:;">专题管理</a></li>
        <!--<li><a href="<?php echo U('Information/circle_recommend');?>" target="_self">专题推荐</a></li>-->
        <li><a href="<?php echo U('Information/circle_del');?>" target="_self">专题回收站</a></li>
        <li><a href="<?php echo U('Information/add');?>" target="_self">添加专题</a></li>
    </ul>
    <form class="well form-search" method="post" action="<?php echo U('Information/index');?>">

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
        <div class="table-actions">
            <button class="btn btn-primary btn-small js-ajax-submit" type="submit"
                    data-action="<?php echo U('Information/listorders');?>"><?php echo L('SORT');?>
            </button>
        </div>
        <table class="table table-hover table-bordered table-list">
            <thead>
            <tr>
                <th width="50">Id</th>
                <th width="50"><?php echo L('SORT');?></th>
                <th width="50">名称</th>
                <th width="50">介绍</th>
                <th width="80">分类</th>
                <th width="70">创建者</th>
                <th width="50">关注数量</th>
                <th width="50">访问数量</th>
                <th width="50">状态</th>
                <th width="50">创建时间</th>
                <th width="70"><?php echo L('ACTIONS');?></th>
            </tr>
            </thead>
            <?php $status=array("1"=>"显示","0"=>"隐藏"); ?>
            <?php if(is_array($forumList)): foreach($forumList as $key=>$vo): ?><tr>
                    <td><?php echo ($vo["id"]); ?></td>
                    <td><input name="listorders[<?php echo ($vo["id"]); ?>]" class="input input-order" type="text" size="5"
                               value="<?php echo ($vo["listorder"]); ?>" title="ID:<?php echo ($vo["id"]); ?>"></td>
                    <td><span><?php echo ($vo["name"]); ?></span></td>
                    <td><span><?php echo ($vo["descr"]); ?></span></td>
                    <td><?php echo ($vo["fname"]); ?></td>
                    <td><?php echo ($vo["user_nicename"]); ?></td>
                    <td>
                        <a href="javascript:open_iframe_dialog('<?php echo U('comment/commentadmin/follow',array('post_id'=>$vo['id'],'tablename'=>'forum'));?>','关注列表')"><?php echo ($vo["follow_num"]); ?></a>
                    </td>
                    <td><?php echo ($vo["click_num"]); ?></td>
                    <td><?php echo ($status[$vo['state']]); ?></td>
                    <td><?php echo ($vo["create_time"]); ?></td>
                    <td>
                        <?php if($vo["state"] == 0): ?><a href="<?php echo U('Information/circleCheck',array('check'=>1,'ids'=>$vo['id']));?>"
                               class="js-ajax-dialog-btn">显示</a>
                            <?php else: ?>
                            <a href="<?php echo U('Information/circleCheck',array('uncheck'=>1,'ids'=>$vo['id']));?>"
                               class="js-ajax-dialog-btn">隐藏</a><?php endif; ?>
                        <a href="<?php echo U('Information/circleDel',array('isdel'=>$vo['id']));?>" class="js-ajax-delete">删除</a>

                        <a href="<?php echo U('Information/circle_add', array('id'=>$vo['id']));?>">添加帖子</a>
                    </td>
                </tr><?php endforeach; endif; ?>

        </table>

        <div class="pagination"><?php echo ($page); ?></div>
    </form>
</div>
<script src="/public/js/common.js"></script>
<script>
    function refersh_window() {
        var refersh_time = getCookie('refersh_time');
        if (refersh_time == 1) {
            window.location = "<?php echo U('AdminPost/index',$formget);?>";
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