<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, minimum-scale=1, user-scalable=no">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="white-translucent" />
    <link rel="stylesheet" href="css/global.css?id=333">
    <link rel="stylesheet" href="css/new_style.css?id=333">
    <script type="text/javascript" src="js/autoSize.js?id=333"></script>
    <script type="text/javascript" src="js/jquery/jquery-2.14.min.js"></script>
    <script type="text/javascript" src="js/vue/vue.js?id=333"></script>
    <title>我的收益</title>
    <style>
        html,body{height: 100%;}
    </style>
</head>
<body>
<div id="my_teams" class="my_teams">
    <div class="main">
        <div class="title">我的收益 &nbsp;:&nbsp; <span>{{earnings}}</span></div>
        <ul>
            <li v-for="(item,index) in teams">
                <div class="user">
                    <img :src="item.avatar" alt="" class="avatar">
                    <span class="name">{{item.name}}</span>
                </div>
                <div class="lv" v-show="item.is_vip==1" style="color: #dad8d9">单次体检</div>
                <div class="lv" v-show="item.is_vip==2" style="color: #25c2eb">半年会员</div>
                <div class="lv" v-show="item.is_vip==3" style="color: #ffcc32">一年会员</div>
                <div class="lv" v-show="item.is_vip==4" style="color: #ed8c2e">创客</div>
                <div class="lv" v-show="item.is_vip==5" style="color: #668bfe">合伙人</div>
                <div class="lv" v-show="item.is_vip==7" style="color: #b30fbe">虚拟机主</div>
                <div class="lv" v-show="item.is_vip==8" style="color: #b924fc">机主</div>
            </li>
        </ul>
        <div class="btn">
            <a href="https://beilinliu.kuaifengpay.com/view/rule.html" class="prize"></a>

            <?php if($_GET['q_r_code'] == -1){?>

                <a href="javascript:void (0)" class="recommend_code go_recommend"></a>
            <?php }else{?>
                <a href=https://beilinliu.kuaifengpay.com/share_ewm.php?q_r_code=<?php echo $_GET['q_r_code'];?>" class="go_recommend"></a>
            <?php }?>

        </div>
    </div>
    <div class="no_recommend_code">
        <div class="box">
            <div class="text"></div>
            <div class="enter">好</div>
        </div>
    </div>
</div>
<script>
    new Vue({
        el:'#my_teams',
        data:{
            earnings:"<?php echo $_GET['total'];?>",
            user_type:"<?php echo $_GET['user_type'];?>",
            did:"<?php echo $_GET['did'];?>",
            teams:[]
        },
        methods:{
            init:function () {
                var url = 'https://bolon.kuaifengpay.com/Api/Index/teams',
                    params={user_type:this.user_type ,did:this.did},
                    _this = this;
                $.getJSON(url,params,function(res){
                    var status = res.status;
                    if(status == 1){
                        res.data.length > 0 && (_this.teams = res.data);
                    }
                })
            }
        },
        mounted:function () {
            this.init();
        }
    })
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