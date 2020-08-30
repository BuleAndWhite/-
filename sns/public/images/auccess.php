<?php
$total_fee = $_GET['total_fee'] * 0.01;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, minimum-scale=1, user-scalable=no">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="white-translucent" />
    <link rel="stylesheet" href="css/global.css">
    <link rel="stylesheet" href="css/new_style.css">
    <script type="text/javascript" src="js/autoSize.js"></script>
    <script type="text/javascript" src="js/jquery/jquery-2.14.min.js"></script>
    <title>支付成功</title>
    <style>
        html,body{height: 100%;}
    </style>
</head>
<body>
<div class="pay_finished">
    <div class="success_icon"></div>
    <p class="t">您已成功支付金额</p>
    <p class="money">￥<?php echo $total_fee; ?>元</p>
    <div class="text">
        <p class="t2">长按加关注</p>
        <p class="t3">带你飞</p>
        <div class="ewm">
            <img src="images/new_images/ewm.png" alt="">
        </div>
        <div class="btn">
            <img src="images/new_images/finish_sub.png" alt="">
        </div>
    </div>
</div>
</body>
</html>

<!--<html>-->
<!--<head>-->
<!--    <meta http-equiv="content-type" content="text/html;charset=utf-8"/>-->
<!--    <meta name="viewport" content="width=device-width, initial-scale=1"/>-->
<!--    <title>微信支付成功</title>-->
<!--</head>-->
<!--<body>-->
<!--<br/>-->
<!--<div class="x_addtable">-->
<!--<div align="center">-->
<!--    <div style="font-size:18px; "align="center"  >支付成功</div>-->
<!--    <font color="#9ACD32"><b>支付金额<span style="color:#f00;font-size:50px"> --><!--元</span></b></font><br/><br/>-->
<!--  -->
<!--</div>-->
<!--</div>-->
<!--</body>-->
<!--<script src="jquery.js"></script>-->
<!--</html>-->
