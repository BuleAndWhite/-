<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>成为机主</title>
</head>
<body>

</body>
</html>

<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, minimum-scale=1, user-scalable=no">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="white-translucent" />
    <title>绑定身份证</title>
    <link rel="stylesheet" href="css/global.css">
    <link rel="stylesheet" href="css/new_style.css">
    <script type="text/javascript" src="js/autoSize.js"></script>
    <script type="text/javascript" src="js/jquery/jquery-2.14.min.js"></script>
    <script type="text/javascript" src="js/Validform_v5.3.1.js"></script>
    <script type="text/javascript" src="js/become.js?ii2=22www"></script>
    <style>
        html,body{height: 100%;}
    </style>
</head>
<body>
<div class="become">
    <div class="main">
        <div class="title">
            <div class="select">
                <div class="item1 item active" data-type="1"></div>
                <div class="item2 item" data-type="2"></div>
            </div>
            <div class="text" style="display: block">
                注：缴纳10万元押金即可成为机主，获得机器运营二维码可直接发展合伙人、创客、会员，获得收益分成和游客体检收
            </div>
            <div class="text" style="display: none">
                注：缴纳3万元押金即可成为天使机主，获得虚拟运营二维码可直接发展合伙人，众筹到10万元，即可升级为机主，拥有收益分成
            </div>
        </div>
        <form id="become">
            <div class="form_con">
                <h1>基本信息</h1>
                <div class="mr username">
                    <em>姓名</em>
                    <input type="text" id="username" placeholder="请填写您的真实姓名" name="name" datatype="*" >
                </div>
                <div class="mr idcard">
                    <em>身份证</em>
                    <input type="text" id="idcard" placeholder="请输入身份证号码" name="idcard" datatype="sfz" >
                </div>
                <div class="mr m">
                    <em>联系方式</em>
                    <input type="text" id="mobile" placeholder="请输入电话号码" name="phone"  data-type="mobile">
                </div>
                <div class="mr email">
                    <em>邮箱</em>
                    <input type="text" id="email" placeholder="请输入邮箱" name="mailbox" data-type="e">
                </div>
                <div class="mr email">
                    <em>金额</em>
                    <span>100000</span>
                </div>
                <div class="mr bz">
                    <em>申请备注</em>
                    <input type="text" id="bz" placeholder="请输入备注" name="conten">
                </div>
            </div>
            <div class="submit"attrOperation="<?php echo $_GET['operation'];?>" attrOwnerId="<?php echo $_GET['owner_id'];?>"attrSceneStr="<?php echo $_GET['scene_str'];?>" attrOpenid="<?php echo $_GET['openid'];?>" attrToken="<?php echo $_GET['access_token'];?>" attrOperation="<?php echo $_GET['operation'];?>" attrUnionid="<?php echo $_GET['unionid'];?>"><input type="submit" name="submit" value="确定"></div>


        </form>
    </div>
</div>
</body>
</html>