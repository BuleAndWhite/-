<?php
$userList = json_decode($_GET['userList'], true);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, minimum-scale=1, user-scalable=no">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="white-translucent" />
    <link rel="stylesheet" href="css/global.css?id=33243223234">
    <link rel="stylesheet" href="css/new_style.css?id=33242233234">
    <script type="text/javascript" src="js/autoSize.js?id=33242233234"></script>
    <script type="text/javascript" src="js/jquery/jquery-2.14.min.js?id=33234223234"></script>
    <title>个人中心</title>
    <style>
        html,body{height: 100%;}
    </style>
</head>
<body>
<div class="home">

    <div class="user_info">
        <div class="information">
            <div class="avatar">
                <img src="<?php echo $userList['avatar'];?>" alt="">
            </div>
            <div class="msg">
                <p class="username"><?php echo $userList['name'];?></p>
                <p class="tel">TEL：<span><?php echo $userList['phone'];?></span></p>
            </div>
        </div>
        <div class="my">
            <div class="surplus">
                <p class="t">我的余额</p>
                <p class="num">余额：<span><?php echo $userList['total'];?></span></p>
            </div>
            <div class="point">
                <p class="t">我的积分</p>
                <p class="num">积分：<span><?php echo $userList['integral'];?></span></p>
            </div>
            <div class="prize">
                <p class="t">我的礼包</p>
                <p class="num">积分：<span><?php echo $userList['gifts_integral'];?></span></p>
            </div>
        </div>
    </div>
    <div class="list">
        <ul>
            <li>
                <a href="https://beilinliu.kuaifengpay.com/my_team.php?user_type=<?php echo $userList["user_type"];?>&did=<?php echo $userList["sn"];?>&total=<?php echo $userList['total'];?>&q_r_code=<?php echo $userList['q_r_code'];?>" target="_blank">
                    <div class="t">
                        <div class="icon">
                            <img src="images/new_images/home/home_icon1.png" alt="">
                        </div>
                        <div class="name">我的收益</div>
                    </div>
                    <div class="go"></div>
                </a>
            </li>
            <?php if($userList['password_code'] ==-1 && $userList['user_type_count'] ==-1){ ?>
                <li class="recommend_codes">
                    <a href="javascript:void (0)" target="_blank">
                        <div class="t">
                            <div class="icon">
                                <img src="images/new_images/home/home_icon2.png" alt="">
                            </div>
                            <div class="name">口令码</div>
                        </div>
                        <div class="go"></div>
                    </a>
                </li>

            <?php }else{?>
                <li>
                    <a href='https://beilinliu.kuaifengpay.com/my_code.php?password_code=<?php echo $userList["password_code"];?>&countCode=<?php echo $userList["countCode"];?>&user_type=<?php echo $userList["user_type"];?>&user_select=<?php echo $userList["user_select"];?>' target="_blank">

                    <div class="t">
                          <div class="icon">
                                <img src="images/new_images/home/home_icon2.png" alt="">
                            </div>
                            <div class="name">口令码</div>

                    </div>
                    <div class="go"></div>
                    </a>
                </li>
            <?php }?>

            <?php if($userList['q_r_code'] ==-1){ ?>
                <li class="recommend_code">
                    <a href="javascript:void (0)" target="_blank">
                        <div class="t">
                            <div class="icon">
                                <img src="images/new_images/home/home_icon3.png" alt="">
                            </div>
                            <div class="name">推广码</div>
                        </div>
                        <div class="go"></div>
                    </a>
                </li>

            <?php }else{?>

                <li>
                    <a href="https://beilinliu.kuaifengpay.com/share_ewm.php?q_r_code=<?php echo $userList['q_r_code'];?>" target="_blank">
                        <div class="t">
                            <div class="icon">
                                <img src="images/new_images/home/home_icon3.png" alt="">
                            </div>
                            <div class="name">推广码</div>
                        </div>
                        <div class="go"></div>
                    </a>
                </li>
            <?php }?>
            <li>
                <a href="tel:4008870208" target="_blank">
                    <div class="t">
                        <div class="icon">
                            <img src="images/new_images/home/home_icon5.png" alt="">
                        </div>
                        <div class="name">客服电话</div>
                    </div>
                    <div class="go"></div>
                </a>
            </li>
        </ul>
    </div>
    <div class="no_recommend_code">
        <div class="box">
            <div class="text"></div>
            <div class="enter">好</div>
        </div>
    </div>

</div>
<script>
    /*
    *   toast插件封装
    *
    *   调用方法：
    *   toast.init(options)
    *   参数options：
    *   {
    *       content：'提示文字'，
    *       btn：按钮文字，
    *       callback ：回调
    *   }
    */
    function toast_ui (){
        this.config = {
            content:'',
            btn:'好',
            callback:'',
            element:$('.no_recommend_code'),
            element_title:$('.no_recommend_code .text'),
            element_btn:$('.no_recommend_code .enter')
        };
        this.show = function(){
            this.config.element.addClass('active');
        };
        this.hide = function () {
            $('.no_recommend_code').removeClass('active');
            this.config.element_btn.unbind();
            this.config.callback && this.config.callback();
        };
        this.element = function () {
            var _this = this;
            this.config.element_btn.click(function(){
                _this.hide();
            });
        };
        this.updateData = function () {
            this.config.element_title.text(this.config.content);
            this.config.element_btn.text(this.config.btn);
        };
        this.init = function (obj) {
            $.extend(this.config,obj);
            this.updateData();
            this.show();
            this.element();
        }
    }
    var toast = new toast_ui();



    $('.recommend_codes').click(function(){
        toast.init({
            content:'您还没有口令码',
            btn:'好',
            callback:function(){

            }
        });
    })
    $('.recommend_code').click(function(){
        toast.init({
            content:'您还没有推广码',
            btn:'好',
            callback:function(){

            }
        });
    })

</script>
</body>
</html>

