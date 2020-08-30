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
        position: fixed;
        left: 50%;
        top: 30%;
        z-index: 10000;
        /*设定这个div的margin-top的负值为自身的高度的一半,margin-left的值也是自身的宽度的一半的负值.*/
        /*宽为400,那么margin-top为-200px*/
        /*高为200那么margin-left为-100px;*/
        margin: -200px 0 0 -350px;

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
</head>
<body>
<div class="wrap js-check-wrap">
    <ul class="nav nav-tabs">
        <li class="active"><a href="javascript:;">提现管理</a></li>

    </ul>
    <form class="well form-search" method="post" action="<?php echo U('Withdraw/export');?>">
        <div class="table-actions">

            时间：
            <input type="text" name="start_time" class="js-date" value="<?php echo ((isset($formget["start_time"]) && ($formget["start_time"] !== ""))?($formget["start_time"]):''); ?>"
                   style="width: 80px;" autocomplete="off">-
            <input type="text" class="js-date" name="end_time" value="<?php echo ($formget["end_time"]); ?>" style="width: 80px;"
                   autocomplete="off"> &nbsp; &nbsp;

            <input type="submit" class="btn btn-primary" value="导出提现列表"/>
        </div>
    </form>

    <form class="well form-search" method="post" action="<?php echo U('Withdraw/withdrawal');?>">

        时间：
        <input type="text" name="start_time" class="js-date" value="<?php echo ((isset($formget["start_time"]) && ($formget["start_time"] !== ""))?($formget["start_time"]):''); ?>"
               style="width: 80px;" autocomplete="off">-
        <input type="text" class="js-date" name="end_time" value="<?php echo ($formget["end_time"]); ?>" style="width: 80px;"
               autocomplete="off"> &nbsp; &nbsp;
        关键字：
        <input type="text" name="keyword" style="width: 200px;" value="<?php echo ($formget["keyword"]); ?>" placeholder="请输入关键字...">
        <select name="varied">
            <option value="0" selected="selected">全部</option>
            <option value="1">小程序</option>
            <option value="2">公众号</option>
        </select>
        <input type="submit" class="btn btn-primary" value="搜索"/>
    </form>
    <form class="js-ajax-form" action="" method="post">

        <table class="table table-hover table-bordered table-list">
            <thead>
            <tr>
                <th width="15"><label><input type="checkbox" class="js-check-all" data-direction="x"
                                             data-checklist="js-check-x"></label></th>
                <th width="50">Id</th>
                <th width="50">提现类型</th>
                <th width="50">申请人</th>
                <th width="50">手机号</th>
                <th width="80">提现金额</th>
                <th width="80">提现金额(抽取手续费后)</th>
                <th width="80">付款方式</th>
                <th width="80">账号</th>
                <th width="80">开户行</th>
                <th width="80">支行</th>
                <th width="70">创建时间</th>
                <th width="50">状态</th>
                <th width="50">回执单</th>
                <th width="70"><?php echo L('ACTIONS');?></th>
            </tr>
            </thead>
            <?php $status=array("1"=>"审核中","2"=>"成功","3"=>"驳回"); $type=array("1"=>"支付宝","2"=>"银行卡"); $varied=array("1"=>"小程序","2"=>"公众号"); ?>
            <?php if(is_array($messageContentList)): foreach($messageContentList as $key=>$vo): ?><tr>
                    <td><input type="checkbox" class="js-check" data-yid="js-check-y" data-xid="js-check-x" name="ids[]"
                               value="<?php echo ($vo["id"]); ?>" title="ID:<?php echo ($vo["id"]); ?>"></td>
                    <td><?php echo ($vo["id"]); ?></td>
                    <td><?php echo ($varied[$vo['varied']]); ?></td>
                    <td><?php echo ($vo["pname"]); ?></td>
                    <td><?php echo ($vo["phone"]); ?></td>
                    <td> <?php echo ($vo["money"]); ?></td>
                    <td style = "color:red"> <?php echo ($vo["moneyT"]); ?></td>
                    <td> <?php echo ($vo["card"]); ?></td>
                    <td> <?php echo ($type[$vo['type']]); ?></td>
                    <td> <?php echo ($vo["account"]); ?></td>
                    <td><?php echo ($vo["branch"]); ?></td>
                    <td><?php echo ($vo["time"]); ?></td>
                    <td><?php echo ($status[$vo['status']]); ?>
                    </td>
                    <td>
                        <?php if($vo["status"] == 1): ?>待审核中<br>没有回执单
                            <?php elseif($vo["status"] == 2): ?>
                            <div class="controls">
                                <input type="hidden" name="url" class="save_paths" value="">
                                <input type="hidden" name="size" class="size" value="">
                                <div onclick="toEdit(<?php echo ($vo['id']); ?>)"><img width="30px" height="30px"
                                                                        src="/public/images/xiazai.png"/></div>
                                <div id="progress" style="display: inline-block">
                                    <div class="bar" style="width: 0%;"></div>
                                </div>
                                <input style="display: none" id="fileupload<?php echo ($vo['id']); ?>" type="file" name="uploadkey"
                                       data-url="<?php echo U('Withdraw/upload_files',array('id'=>$vo['id']));?>"
                                       multiple>
                            </div>
                            <?php if(empty($vo['receipt_img']) == false): ?><a class="contentSof" data-content-sof="<?php echo ($vo["id"]); ?>" data-toggle="modal">查看回执单</a><?php endif; ?>

                            <?php elseif($vo["status"] == 3): ?>
                            审核驳回<br>没有回执单<?php endif; ?>

                    </td>
                    <td>
                        <a href="javascript:open_iframe_dialog('<?php echo U('comment/commentadmin/match',array('unionid'=>$vo['unionid']));?>','账号列表')">个人账号匹配</a>
                        <?php if($vo["status"] == 1): ?><a class="js-ajax-dialog-btn"
                               href="<?php echo U('Withdraw/approved',array('id'=>$vo['id']));?>">
                                通过
                            </a>
                            <a class="js-ajax-dialog-btn"
                               href="<?php echo U('Withdraw/reset',array('id'=>$vo['id']));?>">
                                驳回
                            </a>
                            <?php elseif($vo["status"] == 3): ?>
                            <a href="<?php echo U('Withdraw/delete',array('id'=>$vo['id']));?>"
                               class="js-ajax-delete"><?php echo L('DELETE');?></a><?php endif; ?>
                    </td>
                </tr>
                <script src="/public/js/jquery.ui.widget.js"></script>
                <script src="/public/js/jquery.iframe-transport.js"></script>
                <script src="/public/js/jquery.fileupload.js"></script>
                <script>
                    function toEdit(id) {

                        $('#fileupload' + id).click();
                        $('#fileupload' + id).fileupload({
                            dataType: 'json',
                            acceptFileTypes: /(\.|\/)(jpe?g|png)$/i,
                            maxFileSize: 10485761111,
                            progressall: function (e, data) {
                                var progress = parseInt(data.loaded / data.total * 100, 10);
                            },
                            done: function (e, data) {
                                // console.log(data);
                                if (data.result.status == 1) {
                                    // console.log(data)
                                    window.location.reload()
                                } else {
                                    // alert(data.result.info);
                                }
                                setTimeout(function () {
                                    $('#progress').hide();
                                }, 2000)
                            }
                        });
                    }

                </script><?php endforeach; endif; ?>
            <tfoot>
            <tr>
                <th width="15"><label><input type="checkbox" class="js-check-all" data-direction="x"
                                             data-checklist="js-check-x"></label></th>
                <th width="50">Id</th>
                <th width="50">提现类型</th>
                <th width="50">申请人</th>
                <th width="50">手机号</th>
                <th width="80">提现金额</th>
                <th width="80">提现金额(抽取手续费后)</th>
                <th width="80">付款方式</th>
                <th width="80">账号</th>
                <th width="80">开户行</th>
                <th width="80">支行</th>
                <th width="70">创建时间</th>
                <th width="50">状态</th>
                <th width="50">回执单</th>
                <th width="70"><?php echo L('ACTIONS');?></th>
            </tr>
            </tfoot>
        </table>

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
                    回执单
                </h3>
            </div>
            <div class="modal-body" style="max-height: 550px; overflow-y: auto;">
                <img class="imgUrl">
            </div>
            <div style="text-align:center;margin:10px auto;">
            </div>
        </div>
    </div>
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

    $(function () {
        $('button.close').click(function () {
            $('.modal-backdrop,.weixin_modal').hide();
        });
        // var html = '<img src="/admin/themes/simplebootx//Public/assets/images/wheel_throbber.gif"> 正在加载...';
        $('.contentSof').click(function () {
            // $('.modal-body').html(html);
            $(".imgUrl").attr('src', " /admin/themes/simplebootx//Public/assets/images/wheel_throbber.gif");
            var id = $(this).attr("data-content-sof");

            $.get("<?php echo U('Withdraw/select');?>", {id: id}, function (data) {
                $(".imgUrl").attr('src', data.info);


            });
            $('.modal-backdrop,.weixin_modal').show();
        });
        $('.choose-media').live('click', function () {
            $('#comment').val($(this).attr('data-mid'));
            $('.modal-backdrop,.weixin_modal').hide();
        });
    })
</script>
</body>
</html>