<?php
$q_r_code = $_GET['q_r_code'];
$q_r_code =str_replace(' ',"+",$q_r_code)
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, minimum-scale=1, user-scalable=no">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="white-translucent" />
    <link rel="stylesheet" href="css/global.css?id=22222333">
    <link rel="stylesheet" href="css/new_style.css?id=22332233">
    <script type="text/javascript" src="js/autoSize.js?id=22323233"></script>
    <script type="text/javascript" src="js/jquery/jquery-2.14.min.js?id=22322333"></script>
    <title>口令码</title>
    <style>
        html,body{height: 100%;}
    </style>
</head>
<body>
    <div class="recommend">
        <div class="t">我的推广码</div>
        <div class="ewm">
            <img src="<?php echo $q_r_code?>" alt="">
        </div>
        <p class="explain">长按二维码以推广</p>
    </div>
</body>
</html>