
<?php
$userSelect = json_decode($_GET['user_select'], true);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, minimum-scale=1, user-scalable=no">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="white-translucent" />
    <link rel="stylesheet" href="css/global.css?id=1131">
    <link rel="stylesheet" href="css/new_style.css?id=1131">
    <script type="text/javascript" src="js/autoSize.js?id=1311"></script>
    <script type="text/javascript" src="js/jquery/jquery-2.14.min.js?id=1131"></script>
    <title>口令码</title>
    <style>
        html,body{height: 100%;}
    </style>
</head>
<body>
<?php if( ($_GET["user_type"] ==4 && $_GET["countCode"] != -1) || ($_GET["user_type"] ==5 && $_GET["countCode"] != -1) ){?>
    <div class="my_code">
        <div class="t">我的口令码</div>
        <div class="ewm">
            <img src="<?php echo $_GET['password_code']?>" alt="">
        </div>
        <p class="explain">长按二维码以推广</p>
        <div class="surplus_num">
            推广口令码剩余：<span><?php echo $_GET['countCode']?>张</span>
        </div>

<?php }?>
    <div class="my_recommend_code">
        <ul>
            <?php foreach ($userSelect as $value) {?>
                <li class="<?php if( $value["status"] ==-1){echo  ' yes';}else{echo ' no';}?>">
                    <div class="code">
                        口令码：<span><?php echo $value['password']?></span>
                    </div>
                    <?php if( $value["status"] ==-1){?>
                        <div class="type ">已使用</div>
                    <?php }else{?>
                        <div class="type ">未使用</div>
                    <?php }?>

                </li>
            <?php }?>
        </ul>
    </div>
    </div>
</body>
</html>