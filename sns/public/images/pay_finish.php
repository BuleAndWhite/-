<?php
$total_fee = $_GET['total_fee'];
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
        <div class="btn" onClick="javaScript:history.go(-1)">
            <img src="images/new_images/finish_sub.png" alt="">
        </div>
    </div>
</div>
</body>
</html>

<!---->
<!--<!doctype html>-->
<!--<html lang="en">-->
<!--<head>-->
<!--    <meta charset="UTF-8">-->
<!--    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, minimum-scale=1, user-scalable=no">-->
<!--    <meta name="apple-mobile-web-app-capable" content="yes">-->
<!--    <meta name="apple-mobile-web-app-status-bar-style" content="white-translucent" />-->
<!--    <title>支付成功</title>-->
<!--    <link rel="stylesheet" href="css/global.css?222">-->
<!--    <link rel="stylesheet" href="css/style.css?222">-->
<!--    <script type="text/javascript" src="js/autoSize.js"></script>-->
<!--    <script type="text/javascript" src="js/jquery/jquery-2.14.min.js"></script>-->
<!--</head>-->
<!--<body>-->
<!--<div class="pay-finish">-->
<!--    <div class="title">-->
<!--        支付成功-->
<!--    </div>-->
<!--    <div class="finish-img">-->
<!--        <img src="images/pay-success.png" alt="">-->
<!--    </div>-->
<!--    <div class="finish-status">-->
<!--        <p>您已经成功支付金额</p>-->
<!--        <p class="pay-money">¥--><?php //echo $total_fee; ?><!--元</p>-->
<!--    </div>-->
<!--    <div class="back" onClick="javaScript:history.go(-1)">返回</div>-->
<!--</div>-->
<!--</body>-->
<!--</html>-->