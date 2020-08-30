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
        <li class="active"><a href="javascript:;">动态回收站</a></li>
        <li><a href="<?php echo U('Information/post_reply');?>" target="_self">评论管理</a></li>
        <li><a href="<?php echo U('Information/index');?>">帖子管理</a></li>
        <li><a href="<?php echo U('Information/home_post');?>">推荐首页帖子</a></li>
        <!--<li><a href="<?php echo U('Information/cirle_add');?>">添加帖子</a></li>-->
        <!--<li><a href="<?php echo U('AdminPost/add',array('term'=>empty($term['term_id'])?'':$term['term_id']));?>" target="_self">添加帖子</a></li>-->
    </ul>

    <form class="js-ajax-form" action="" method="post">
        <div class="table-actions">
            <button class="btn btn-primary btn-small js-ajax-submit" type="submit"
                    data-action="<?php echo U('Information/trashInfo',array('trashId'=>1));?>" data-subcheck="true"
                    data-msg="你确定还原吗？">还原
            </button>
            <button class="btn btn-primary btn-small js-ajax-submit" type="submit"
                    data-action="<?php echo U('Information/trashInfo',array( 'uncheckId'=>1));?>" data-subcheck="true"
                    data-msg="你确定删除吗？">清空
            </button>
        </div>
        <table class="table table-hover table-bordered table-list">
            <thead>
            <tr>
                <th width="15"><label><input type="checkbox" class="js-check-all" data-direction="x"
                                             data-checklist="js-check-x"></label></th>
                <th width="50"><?php echo L('SORT');?></th>
                <th width="50">Id</th>
                <th width="50">标题</th>
                <th width="50">专题</th>
                <th width="80">发布人</th>
                <th width="50">点赞数量</th>
                <th width="50">访问数量</th>
                <th width="50">评论数量</th>
                <th width="70">创建时间</th>
                <th width="70">操作</th>
            </tr>
            </thead>
            <?php $top_status=array("1"=>"已置顶","0"=>"未置顶"); $recommend_status=array("1"=>"推荐热门","0"=>"取消热门"); ?>
            <?php if(is_array($forumPostList)): foreach($forumPostList as $key=>$vo): ?><tr>
                    <td><input type="checkbox" class="js-check" data-yid="js-check-y" data-xid="js-check-x" name="ids[]"
                               value="<?php echo ($vo["id"]); ?>" title="ID:<?php echo ($vo["id"]); ?>"></td>
                    <td><input name="listorders[<?php echo ($vo["id"]); ?>]" class="input input-order" type="text" size="5"
                               value="<?php echo ($vo["listorder"]); ?>" title="ID:<?php echo ($vo["id"]); ?>"></td>
                    <td><?php echo ($vo["id"]); ?></td>
                    <td> <?php echo ($vo["title"]); ?></td>
                    <td><?php echo ($vo["name"]); ?></td>
                    <td><?php echo ($vo["user_nicename"]); ?></td>
                    <td><?php echo ($vo["praise_num"]); ?></td>
                    <td><?php echo ($vo["click_num"]); ?></td>
                    <td><a href="javascript:open_iframe_dialog('<?php echo U('comment/commentadmin/index',array('post_id'=>$vo['id'],'tablename'=>'forum_post'));?>','评论列表')"><?php echo ($vo["reply_num"]); ?></a></td>
                    <td><?php echo ($vo["create_time"]); ?></td>
                    <td><?php echo ($top_status[$vo['istop']]); ?><br><?php echo ($recommend_status[$vo['ishot']]); ?>
                    </td>
                </tr><?php endforeach; endif; ?>
            <tfoot>
            <tr>
                <th width="15"><label><input type="checkbox" class="js-check-all" data-direction="x"
                                             data-checklist="js-check-x"></label></th>
                <th width="50"><?php echo L('SORT');?></th>
                <th width="50">Id</th>
                <th width="50">标题</th>
                <th width="50">专题</th>
                <th width="80">发布人</th>
                <th width="50">点赞数量</th>
                <th width="50">访问数量</th>
                <th width="50">评论数量</th>
                <th width="70">创建时间</th>
            </tr>
            </tfoot>
        </table>
        <div class="table-actions">
            <button class="btn btn-primary btn-small js-ajax-submit" type="submit"
                    data-action="<?php echo U('Information/trashInfo',array('trashId'=>1));?>" data-subcheck="true"
                    data-msg="你确定还原吗？">还原
            </button>
            <button class="btn btn-primary btn-small js-ajax-submit" type="submit"
                    data-action="<?php echo U('Information/trashInfo',array('uncheckId'=>1));?>" data-subcheck="true"
                    data-msg="你确定删除吗？">清空
            </button>
        </div>
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