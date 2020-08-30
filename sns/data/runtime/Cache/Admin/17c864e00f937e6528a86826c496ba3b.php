<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<title><?php echo L('ADMIN_CENTER');?></title>
	<!-- Tell the browser to be responsive to screen width -->
	<meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
	<!-- Bootstrap 3.3.6 -->
	<link rel="stylesheet" href="/public/files/bootstrap/css/bootstrap.min.css">
	<!-- Font Awesome -->
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.5.0/css/font-awesome.min.css">
	<!-- Ionicons -->
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/ionicons/2.0.1/css/ionicons.min.css">
	<!-- Theme style -->
	<link rel="stylesheet" href="/public/files/dist/css/AdminLTE.min.css">
	<!-- iCheck -->
	<link rel="stylesheet" href="/public/files/plugins/iCheck/square/blue.css">

	<!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
	<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
	<!--[if lt IE 9]>
	<script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
	<script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
	<![endif]-->
	<style>
		.login-page, .register-page {
			width: 100%;
			height: 100%;
			padding: 0;
			margin: 0;
			background: url(/public/images/bg.gif) fixed repeat;
		}
		.btn-primary{
			border: 1px solid #2c3e50;
		}
		.btn-primary:hover,btn-primary:active:focus, .btn-primary:active:hover{
			border: 1px solid #2c3e50;
		}
		.btn-primary:active{
			border: 1px solid #2c3e50;
		}
		.tips_error{
			display: block;
			padding-top: 10px;
			color: red;
			height: 25px;
		}
		.tips_success{
			display: block;
			padding-top: 10px;
			height: 25px;
		}
	</style>
</head>
<body class="hold-transition login-page">
<div class="login-box" style="padding-top: 132px;">
	<div class="login-logo" style="color: #fff;">
		<b></b>校园通
	</div>
	<!-- /.login-logo -->
	<div class="login-box-body">
		<p class="login-box-msg"><br></p>

		<form method="post" name="login" action="<?php echo U('public/dologin');?>" autoComplete="off" class="js-ajax-form">
			<div class="form-group has-feedback">
				<input type="text" class="form-control" name="username" id="js-admin-name" placeholder="用户名或邮箱">
				<span class="glyphicon glyphicon-envelope form-control-feedback"></span>
			</div>
			<div class="form-group has-feedback">
				<input type="password" class="form-control" name="password" placeholder="密码">
				<span class="glyphicon glyphicon-lock form-control-feedback"></span>
			</div>
			<div class="row">
				<!--<div class="col-xs-8">
                  <div class="checkbox icheck">
                    <label>
                      <input type="checkbox"> Remember Me
                    </label>
                  </div>
                </div>-->
				<!-- /.col -->
				<div class="col-xs-4" style="float: none; width: 100%;">
					<button type="submit" name="submit" class="btn js-ajax-submit btn-primary btn-block btn-flat" data-loadingmsg="<?php echo L('LOADING');?>" style="background: #2c3e50;width: 60%;">登 录</button>
					<span class="tips_error"></span>
				</div>
				<!-- /.col -->
			</div>
		</form>

		<!--<div class="social-auth-links text-center">
          <p>- OR -</p>
          <a href="#" class="btn btn-block btn-social btn-facebook btn-flat"><i class="fa fa-facebook"></i> Sign in using
            Facebook</a>
          <a href="#" class="btn btn-block btn-social btn-google btn-flat"><i class="fa fa-google-plus"></i> Sign in using
            Google+</a>
        </div>-->
		<!-- /.social-auth-links -->

		<!-- <a href="#">I forgot my password</a><br>
         <a href="register.html" class="text-center">Register a new membership</a>
     -->
	</div>
	<!-- /.login-box-body -->
</div>
<!-- /.login-box -->

<!-- jQuery 2.2.0 -->
<script src="/public/files/plugins/jQuery/jQuery-2.2.0.min.js"></script>
<!-- Bootstrap 3.3.6 -->
<script src="/public/files/bootstrap/js/bootstrap.min.js"></script>
<!-- iCheck -->
<script src="/public/files/plugins/iCheck/icheck.min.js"></script>

<script>
    var GV = {
        DIMAUB: "",
        JS_ROOT: "/public/js/",//js版本号
        TOKEN : ''	//token ajax全局
    };
</script>
<script src="/public/js/wind.js"></script>
<script src="/public/js/jquery.js"></script>
<script type="text/javascript" src="/public/js/common.js"></script>
<script>
    ;(function(){
        document.getElementById('js-admin-name').focus();
    })();
</script>
</body>
</html>