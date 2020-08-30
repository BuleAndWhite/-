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
<style>
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
        width: 1200px;
        position:fixed;
        left:150px;
        top:150px;
    }

    button.close {
        padding: 0;
        cursor: pointer;
        background: transparent;
        border: 0;
        -webkit-appearance: none;
        font-size: 20px;
        opacity: 1;
        color: red;
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
        padding-left: 15px;
        padding-right: 15px;
    }

    .row {
        margin-left: -15px;
        margin-right: -15px;
    }

    .appmsg {
        border-bottom: 1px solid #e7e7eb;
        margin-top: 35px;
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
<script src="/public/datetimepicker/static/bootstrap-datetimepicker.min.js"></script>
<script src="/public/datetimepicker/static/bootstrap-datetimepicker.zh-CN.js"></script>
<link href="/public/datetimepicker/static/datetimepicker_blue.css" rel="stylesheet"/>
<link href="/public/datetimepicker/static/datetimepicker.css"/>
<link href="/public/datetimepicker/static/dropdown.css"/>
</head>
<body>
<div class="wrap js-check-wrap">
    <ul class="nav nav-tabs">
        <li class="active"><a href="<?php echo U('Order/index');?>">H5信息列表</a></li>
    </ul>
    <form class="well form-search" method="post" action="<?php echo U('ManagementH5/application_exports');?>">

        <div class="table-actions">
            导出提现列表：
            时间：
            <input type="text" name="start_time" class="js-date" value="<?php echo ((isset($formget["start_time"]) && ($formget["start_time"] !== ""))?($formget["start_time"]):''); ?>"
                   style="width: 80px;" autocomplete="off">-
            <input type="text" class="js-date" name="end_time" value="<?php echo ($formget["end_time"]); ?>" style="width: 80px;"
                   autocomplete="off"> &nbsp; &nbsp;

            <input type="submit" class="btn btn-primary" value="导出"/>
        </div>
    </form>
    <form class="well form-search" id="mainform" action="<?php echo U('ManagementH5/applicationH5');?>" method="post">

        时间：
        <input type="text" class="start_time time" name="start_time" value="<?php echo $post_data['start_time']; ?>"
               style="width: 140px;" autocomplete="off" placeholder="开始时间"/>-
        <input type="text" class="end_time time" name="end_time" value="<?php echo $post_data['end_time']; ?>"
               style="width: 140px;" autocomplete="off" placeholder="结束时间"/> &nbsp; &nbsp;
       电话号码：
        <input type="text" name="phone" style="width: 200px;" value="<?php echo ($post_data["order_id"]); ?>" placeholder="电话号码">
        <input type="submit" class="btn btn-primary" id="search_submit" value="搜索">
<!--        <span style="margin-left:10px; ">总订单数:</span>-->
<!--        <span style=" color:#1abc9c"><?php echo ($bonus["A"]); ?></span>&nbsp;-->
<!--        <span style="margin-left:10px; ">总金额:</span>-->
<!--        <span style=" color:#1abc9c"><?php echo ($bonus["B"]); ?></span>&nbsp;-->

        <script type="text/javascript">
            $('.time').datetimepicker({
                format: 'yyyy-mm-dd hh:ii',
                language: "zh-CN",
                minView: 0,
                autoclose: true,
            });
        </script>
    </form>

        <table class="table table-hover table-bordered table-list">
            <thead>
            <tr>
                <th width="50">ID</th>
                <th width="120">用户姓名</th>
                <th width="120">用户电话</th>
                <th width="120">用户地址</th>
                <th width="120">填入时间</th>
            </tr>
            </thead>
            <?php if(is_array($res)): foreach($res as $key=>$res): ?><tr>
                    <td>
                        <?php echo ($res["id"]); ?>
                    </td>

                    <td>
                       <?php echo ($res["name"]); ?>
                    </td>
                    <td>
                        <?php echo ($res["phone"]); ?>
                    </td>
                    <td>
                        <?php echo ($res["address"]); ?>
                    </td>
                    <td>
                        <?php echo ($res["time"]); ?>
                    </td>

                </tr><?php endforeach; endif; ?>
            <tfoot>
            <tr>
                <!-- 						<th width="16"><label><input type="checkbox" class="js-check-all" data-direction="x" data-checklist="js-check-x"></label></th> -->
                <th width="50">ID</th>
                <th width="120">用户姓名</th>
                <th width="120">用户电话</th>
                <th width="120">用户地址</th>
                <th width="120">填入时间</th>
            </tr>
            </tfoot>
        </table>
        <!-- <div class="table-actions">
            <button class="btn btn-primary btn-small js-ajax-submit" type="submit" data-action="<?php echo U('Order/export');?>" data-subcheck="true" data-msg="<?php echo L('DELETE_CONFIRM_MESSAGE');?>">发货</button>
            <button class="btn btn-primary btn-small js-ajax-submit" type="submit">退款</button>
        </div> -->
        <div class="pagination"><?php echo ($page); ?></div>
    </form>
</div>
<div class="weixin_modal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close btn" data-dismiss="modal" aria-hidden="true">
                    ×
                </button>
                <h3 id="myModalLabel">
                    订单编号：
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
    $('button.close').click(function () {
        $('.modal-backdrop,.weixin_modal').hide();
    });
    var html = '<img src="/admin/themes/simplebootx//Public/assets/images/wheel_throbber.gif"> 正在加载...';
    $('a.look_detail').click(function () {
        var orderId = $(this).attr('data-oid');
        $('#myModalLabel').html('快递信息填入');
        $('.modal-body').html(html);
        $.get($(this).attr('href'), function (data) {

            $('.modal-body').html(data);
        });
        $('.modal-backdrop,.weixin_modal').show();
        return false;
    });
    $('a.look_detail1').click(function () {
        var orderId = $(this).attr('data-oid');
        $('#myModalLabel').html('订单信息');
        $('.modal-body').html(html);
        $.get($(this).attr('href'), function (data) {

            $('.modal-body').html(data);
        });
        $('.modal-backdrop,.weixin_modal').show();
        return false;
    });

    $('#export_data').click(function () {
        $('#export_true').val('1');
        //alert($('#mainform').serialize());
        var param = $('#mainform').serialize();
        location.href = "<?php echo U('Order/export');?>&" + param;
        $('#export_true').val('');
        return false;
    });
    setCookie('refersh_time', 0);

    function refersh_window() {
        var refersh_time = getCookie('refersh_time');
        if (refersh_time == 1) {
            window.location.reload();
        }
    }

    setInterval(function () {
        refersh_window()
    }, 3000);
    $(function () {
        $("#selected-cid").change(function () {
            $("#cid-form").submit();
        });
    });
</script>
</body>
</html>