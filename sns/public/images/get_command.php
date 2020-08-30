
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, minimum-scale=1, user-scalable=no">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="white-translucent" />
    <title>口令码领取</title>
    <link rel="stylesheet" href="css/global.css">
    <link rel="stylesheet" href="css/new_style.css">
    <script type="text/javascript" src="js/autoSize.js"></script>
    <script type="text/javascript" src="js/jquery/jquery-2.14.min.js"></script>
    <style>
        html,body{height: 100%;}
    </style>
</head>
<body>
    <div class="command">
        <div class="number"><?php echo $_GET['password'];?></div>
        <p class="explain">注：凭口令码可在智慧健康亭免费体检</p>
        <div class="ewm">
            <img src="images/new_images/ewm.png" alt="">
        </div>
        <p>长按识别二维码加关注</p>
    </div>
</body>
</html>
