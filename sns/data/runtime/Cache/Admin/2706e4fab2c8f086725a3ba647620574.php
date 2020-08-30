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
    <style>

    .wrap select {
        width: 415px
    }

    .appmsg_content p.title {
        overflow: hidden;
        height: 25px;
        line-height: 25px;
    }

    .hidden {
        display: none !important;
        visibility: hidden !important;
    }

    .submsg-title {
        padding: 5px 14px;
    }

    .submsg-title img {
        width: 45px;
        height: 45px;
        float: right;
    }

    .weixin_modal {
        display: block;
        left: 0px;
        width: 100%;
        position: absolute;
        top: 0px;
        bottom: 0px;
        z-index: 10000;
    }

    .weixin_modal, .modal-backdrop {
        display: none
    }

    .modal-content {
        position: relative;
        background-color: #ffffff;
        border: 1px solid #999999;
        border: 1px solid rgba(0, 0, 0, 0.2);
        border-radius: 0px;
        -webkit-box-shadow: 0 3px 9px rgba(0, 0, 0, 0.5);
        box-shadow: 0 3px 9px rgba(0, 0, 0, 0.5);
        background-clip: padding-box;
        outline: 0;
    }

    .modal-backdrop {
        position: fixed;
        top: 0;
        right: 0;
        bottom: 0;
        left: 0;
        background-color: #ccc;
    }

    .modal-backdrop.in {
        opacity: 0.3;
    }

    .modal-header {
        padding: 15px;
        border-bottom: 1px solid #e5e5e5;
        min-height: 16.42857143px;
        background: #fff;
    }

    .modal-header h3 {
        color: #323336;
        font-weight: 400;
        margin-top: 5px;
        margin-bottom: 5px;
        font-size: 18px;
    }

    .modal-dialog {
        width: 900px;
        margin: 30px auto;
    }

    button.close {
        padding: 0;
        cursor: pointer;
        background: transparent;
        border: 0;
        -webkit-appearance: none;
    }

    .close {
        float: right;
        font-size: 21px;
        font-weight: bold;
        line-height: 1;
        color: #000000;
        text-shadow: none;
        opacity: 0.2;
        width: 18px;
        filter: alpha(opacity=20);
    }

    .btn {
        display: inline-block;
        margin-bottom: 0;
        font-weight: normal;
        text-align: center;
        vertical-align: middle;
        touch-action: manipulation;
        cursor: pointer;
        background-image: none;
        border: 1px solid transparent;
        white-space: nowrap;
        font-size: 14px;
        line-height: 1.42857143;
        border-radius: 0px;
        -webkit-user-select: none;
        -moz-user-select: none;
        -ms-user-select: none;
        user-select: none;
    }

    .col-md-4 {
        width: 29%;
        float: left;
        padding-left: 15px;
        padding-right: 15px;
    }

    .row {
        margin-left: -15px;
        margin-right: -15px;
    }

    .appmsg {
        border: 1px solid #e7e7eb;
        margin-bottom: 10px;
    }

    .appmsg_content {
        padding: 0 14px;
        position: relative;
    }

    .appmsg_content .cover {
        width: 100%;
        height: 120px;
        overflow: hidden;
    }

    .appmsg_desc {
        max-height: 80px;
        overflow: hidden;
        padding: 5px 0 10px;
        word-wrap: break-word;
        /*     word-break: break-all; */
    }

    .appmsg_opr a {
        display: block;
        padding: 8px 0;
    }

    .appmsg_opr {
        background-color: #f4f4f4;
        border-top: 1px solid #e7e7eb;
        text-align: center;
    }

    .glyphicon {
        position: relative;
        top: 1px;
        display: inline-block;
        font-family: 'Glyphicons Halflings';
        font-style: normal;
        font-weight: normal;
        line-height: 1;
        -webkit-font-smoothing: antialiased;
        -moz-osx-font-smoothing: grayscale;
    }
</style>
    <ul class="nav nav-tabs">
        <li class="active"><a href="javascript:;">朋友圈回收站</a></li>
        <!--<li><a href="<?php echo U('Information/post_reply');?>">评论管理</a></li>-->
        <li><a href="<?php echo U('CircleOfFriends/index');?>">朋友圈管理</a></li>
        <li><a href="<?php echo U('CircleOfFriends/home_circle');?>">推荐首页朋友圈</a></li>
        <li><a href="<?php echo U('CircleOfFriends/circle_add');?>">添加朋友圈</a></li>
        <!--<li><a href="<?php echo U('AdminPost/add',array('term'=>empty($term['term_id'])?'':$term['term_id']));?>" target="_self">添加帖子</a></li>-->
    </ul>

    <form class="js-ajax-form" action="" method="post">
        <div class="table-actions">
            <button class="btn btn-primary btn-small js-ajax-submit" type="submit"
                    data-action="<?php echo U('CircleOfFriends/trashInfo',array('trashId'=>1));?>" data-subcheck="true"
                    data-msg="你确定还原吗？">还原
            </button>

        </div>
        <table class="table table-hover table-bordered table-list">
            <thead>
            <tr>
                <th width="15"><label><input type="checkbox" class="js-check-all" data-direction="x"
                                             data-checklist="js-check-x"></label></th>
                <th width="50">Id</th>
                <th width="50">内容</th>
                <th width="80">发布人</th>
                <th width="50">点赞数量</th>
                <th width="50">访问数量</th>
                <th width="50">评论数量</th>
                <th width="70">创建时间</th>
                <th width="50"><?php echo L('STATUS');?></th>
            </tr>
            </thead>
            <?php $top_status=array("1"=>"已置顶","0"=>"未置顶"); $recommend_status=array("1"=>"推荐热门","0"=>"未热门"); ?>
            <?php if(is_array($sofList)): foreach($sofList as $k=>$vo): ?><tr>
                    <td><input type="checkbox" class="js-check" data-yid="js-check-y" data-xid="js-check-x" name="ids[]"
                               value="<?php echo ($vo["id"]); ?>" title="ID:<?php echo ($vo["id"]); ?>"></td>
                    <td><?php echo ($vo["id"]); ?></td>
                    <td>
                        <a class="contentSof" data-content-sof="<?php echo ($vo["id"]); ?>" data-toggle="modal">查看内容</a>

                    <td><?php echo ($vo["user_nicename"]); ?></td>
                    <td><?php echo ($vo["praise_num"]); ?></td>
                    <td><?php echo ($vo["click_num"]); ?></td>
                    <td><a href="javascript:open_iframe_dialog('<?php echo U('comment/commentadmin/index',array('post_id'=>$vo['id'],'tablename'=>'sof'));?>','评论列表')"><?php echo ($vo["reply_num"]); ?></a></td>
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
                <th width="50">圈子</th>
                <th width="50">评论数</th>
                <th width="50">点赞数</th>
                <th width="80">发布人</th>
                <th width="70">创建时间</th>
                <th width="50"><?php echo L('STATUS');?></th>
            </tr>
            </tfoot>
        </table>
        <div class="table-actions">
            <button class="btn btn-primary btn-small js-ajax-submit" type="submit"
                    data-action="<?php echo U('CircleOfFriends/trashInfo',array('trashId'=>1));?>" data-subcheck="true"
                    data-msg="你确定还原吗？">还原
            </button>

        </div>
        <div class="pagination"><?php echo ($page); ?></div>
    </form>
</div>
<div class="weixin_modal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close btn" data-dismiss="modal" aria-hidden="true">
                    X
                </button>
                <h3 id="myModalLabel" style="text-align: center">
                    内容
                </h3>
            </div>
            <div class="modal-body" style="max-height: 550px; overflow-y: auto;">


            </div>
            <div style="text-align:center;margin:10px auto;">
            </div>
        </div>
    </div>
</div>
<div class="modal-backdrop  in"></div>
<script src="/public/js/common.js"></script>
<script>
    $(function () {
        $('button.close').click(function () {
            $('.modal-backdrop,.weixin_modal').hide();
        });
        var html = '<img src="/admin/themes/simplebootx//Public/assets/images/wheel_throbber.gif"> 正在加载...';
        $('.contentSof').click(function () {
            $('.modal-body').html(html);
            var id = $(this).attr("data-content-sof");

            $.get("<?php echo U('CircleOfFriends/select');?>", {id: id}, function (data) {
                $('.modal-body').html(data.info);
            });
            $('.modal-backdrop,.weixin_modal').show();
        });
        $('.choose-media').live('click', function () {
            $('#comment').val($(this).attr('data-mid'));
            $('.modal-backdrop,.weixin_modal').hide();
        });
    })
</script>
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