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

<style type="text/css">
    .pic-list li {
        margin-bottom: 5px;
    }
</style>
<script type="text/javascript" src="/public/js/information/information.js"></script>

<script type="text/javascript" src="/public/js/information/professionalLbl.data.min.js"></script>

<link href="/public/industry/big-window.css" rel="stylesheet"/>
</head>
<body>
<div class="wrap js-check-wrap">
    <ul class="nav nav-tabs">
        <li class="active"><a href="javascript:;" target="_self">添加用户</a></li>
        <!--<li><a href="<?php echo U('Oauthadmin/index');?>">用户管理</a></li>-->
    </ul>
    <form action="<?php echo U('Oauthadmin/addList');?>" method="post" class="form-horizontal js-ajax-forms"
          enctype="multipart/form-data">
        <div class="row-fluid">
            <div class="span9">
                <table class="table table-bordered">
                    <tr>
                        <th width="80">vip</th>
                        <td>
                            <select name="post[is_vip]">
                                    <option value="5">三年</option>
                                    <option value="4">两年</option>
                                    <option value="3">一年</option>
                                    <option value="2">半年</option>
                                    <option value="1">一次</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th>姓名</th>
                        <td>
                            <input type="text" style="width:400px;" name="post[name]" id="name" required
                                   value=""
                                   placeholder="请输入姓名"/>
                            <span class="form-required">*</span>
                        </td>
                    </tr>
                    <tr>
                        <th>手机号</th>
                        <td>
                            <input type="text" style="width:400px;" name="post[phone]" id="phone" required
                                   value=""
                                   placeholder="请输入手机号"/>
                            <span class="form-required">*</span>
                        </td>
                    </tr>
                    <tr>
                    <th>金额</th>
                    <td>
                        <input type="text" style="width:400px;" name="post[total_fee]" id="total_fee" required
                               value=""
                               placeholder="请输入金额"/>
                        <span class="form-required">*</span>
                    </td>
                </tr>
                    <tr>
                        <th>身份证</th>
                        <td>
                            <input type="text" style="width:400px;" name="post[idCard]" id="idCard" required
                                   value=""
                                   placeholder="请输入身份证"/>
                            <span class="form-required">*</span>
                        </td>
                    </tr>
                    <tr>
                        <th>设备编号</th>
                        <td>
                            <input type="text" style="width:400px;" name="post[sn]" id="sn" required
                                   value=""
                                   placeholder="请输入设备编号"/>
                            <span class="form-required">*</span>
                        </td>
                    </tr>
                    <!--<tr>-->
                        <!--<th>圈主</th>-->
                        <!--<td>-->
                            <!--&lt;!&ndash;<input type="text" style="width:400px;" name="post[uid]" id="uids" required value=""&ndash;&gt;-->
                            <!--&lt;!&ndash;placeholder="圈主id"/>&ndash;&gt;-->
                            <!--<div class="uids"></div>-->
                            <!--<input type="text" style="width:400px;" id="master_uid"-->
                                   <!--master_url="<?php echo U('Information/masterSearch');?>" onkeyup="masterUP()" value=""-->
                                   <!--placeholder="搜索管理员"/>-->
                            <!--<div class="user_list"></div>-->
                        <!--</td>-->
                    <!--</tr>-->

                    <!--<tr>-->
                        <!--<th>介绍</th>-->
                        <!--<td>-->
                            <!--<textarea name="post[descr]" id="descr"-->
                                      <!--style="width: 98%; height: 50px;" placeholder="请填写简介"></textarea>-->
                        <!--</td>-->
                    <!--</tr>-->
                    <!--<tr>-->
                        <!--<th><b>状态</b></th>-->
                    <!--</tr>-->
                    <!--<tr>-->
                        <!--<td>-->
                            <!--<label class="radio"><input type="radio" name="post[state]" value="1"-->
                                                        <!--checked>显示</label>-->
                            <!--<label class="radio"><input type="radio" name="post[state]" value="0">隐藏</label>-->
                        <!--</td>-->
                    <!--</tr>-->
                    <!--<tr>-->
                        <!--<th><b>可否评论</b></th>-->
                    <!--</tr>-->
                    <!--<tr>-->
                        <!--<td>-->
                            <!--<label class="radio"><input type="radio" checked name="post[permissions]" value="1">可评论</label>-->
                            <!--<label class="radio"><input type="radio" name="post[permissions]" value="0"-->
                                                        <!--&gt;禁止评论</label>-->
                        <!--</td>-->
                    <!--</tr>-->
                    <!--<tr>-->
                        <!--<th>公告</th>-->
                        <!--<td>-->
                            <!--<textarea name="post[notify]" id="notify"-->
                                      <!--style="width: 98%; height: 50px;" placeholder="请填写公告"></textarea>-->
                        <!--</td>-->
                    <!--</tr>-->
                    <!--<tr>-->
                        <!--<th>标签</th>-->
                        <!--<td>-->

                            <!--<div class=" fll select_parents">-->
                                <!--<input type="hidden" class="selHidden" name="skill_id" value="">-->
                                <!--<textarea class="itSkillResult mt0 w100p" cols="80" rows="30" placeholder="点击选择"-->
                                          <!--readonly></textarea>-->
                                <!--<input type="hidden">-->
                                <!--<div class="itSkillDiv"></div>-->
                            <!--</div>-->
                        <!--</td>-->
                    <!--</tr>-->
                    <!--<tr>-->
                        <!--<th>标签</th>-->
                        <!--<td>-->
                            <!--<label><input name="post[permissions]" checked type="radio" value="1"/>所有人 </label>-->
                            <!--<label><input name="post[permissions]" type="radio" value="2"/>管理员 </label>-->
                            <!--<label><input name="post[permissions]" type="radio" value="3"/>版主 </label>-->
                        <!--</td>-->
                    <!--</tr>-->

                </table>
            </div>

        </div>
        <div class="form-actions">
            <button class="btn btn-primary js-ajax-submit" type="submit">提交</button>
            <a class="btn" href="<?php echo U('Oauthadmin/index');?>">返回</a>
        </div>
    </form>
</div>
<script type="text/javascript" src="/public/js/common.js"></script>
<script type="text/javascript" src="/public/js/content_addtop.js"></script>

<script type="text/javascript" src="/public/js/ueditor/ueditor.config.js"></script>
<script type="text/javascript" src="/public/js/ueditor/ueditor.all.min.js"></script>
<script type="text/javascript">

    $(function () {
        $(".js-ajax-close-btn").on('click', function (e) {
            e.preventDefault();
            Wind.use("artDialog", function () {
                art.dialog({
                    id: "question",
                    icon: "question",
                    fixed: true,
                    lock: true,
                    background: "#CCCCCC",
                    opacity: 0,
                    content: "您确定需要关闭当前页面嘛？",
                    ok: function () {
                        setCookie("refersh_time", 1);
                        window.close();
                        return true;
                    }
                });
            });
        });
        /////---------------------
        Wind.use('validate', 'ajaxForm', 'artDialog', function () {
            //javascript


            var form = $('form.js-ajax-forms');
            //ie处理placeholder提交问题
            if ($.browser.msie) {
                form.find('[placeholder]').each(function () {
                    var input = $(this);
                    if (input.val() == input.attr('placeholder')) {
                        input.val('');
                    }
                });
            }

            var formloading = false;
            //表单验证开始
            form.validate({
                //是否在获取焦点时验证
                onfocusout: false,
                //是否在敲击键盘时验证
                onkeyup: false,
                //当鼠标掉级时验证
                onclick: false,
                //验证错误
                showErrors: function (errorMap, errorArr) {
                    //errorMap {'name':'错误信息'}
                    //errorArr [{'message':'错误信息',element:({})}]
                    try {
                        $(errorArr[0].element).focus();
                        art.dialog({
                            id: 'error',
                            icon: 'error',
                            lock: true,
                            fixed: true,
                            background: "#CCCCCC",
                            opacity: 0,
                            content: errorArr[0].message,
                            cancelVal: '确定',
                            cancel: function () {
                                $(errorArr[0].element).focus();
                            }
                        });
                    } catch (err) {
                    }
                },
                //验证规则
                rules: {
                    'post[master_name]': {
                        required: 1
                    }
                },
                //验证未通过提示消息
                messages: {
                    'post[master_name]': {
                        required: '请输入标题'
                    }
                },
                //给未通过验证的元素加效果,闪烁等
                highlight: false,
                //是否在获取焦点时验证
                onfocusout: false,
                //验证通过，提交表单
                submitHandler: function (forms) {
                    if (formloading)
                        return;
                    $(forms).ajaxSubmit({
                        url: form.attr('action'), //按钮上是否自定义提交地址(多按钮情况)
                        dataType: 'json',
                        beforeSubmit: function (arr, $form, options) {
                            formloading = true;
                        },
                        success: function (data, statusText, xhr, $form) {
                            formloading = false;
                            if (data.status) {
                                setCookie("refersh_time", 1);
                                //添加成功
                                Wind.use("artDialog", function () {
                                    art.dialog({
                                        id: "succeed",
                                        icon: "succeed",
                                        fixed: true,
                                        lock: true,
                                        background: "#CCCCCC",
                                        opacity: 0,
                                        content: data.info,
                                        button: [{
                                            name: '继续添加？',
                                            callback: function () {
                                                reloadPage(window);
                                                return true;
                                            },
                                            focus: true
                                        }, {
                                            name: '返回列表页',
                                            callback: function () {
                                                location = "<?php echo U('Oauthadmin/index');?>";
                                                return true;
                                            }
                                        }]
                                    });
                                });
                            } else {
                                isalert(data.info);
                            }
                        }
                    });
                }
            });
        });
        ////-------------------------
    });
</script>
</body>
</html>