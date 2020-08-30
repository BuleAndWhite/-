<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, initial-scale=1, maximum-scale=1, minimum-scale=1, user-scalable=no">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="white-translucent"/>
    <title>会员充值</title>
    <link rel="stylesheet" href="css/global.css?22122">
    <link rel="stylesheet" href="css/style.css?22122">
    <script type="text/javascript" src="js/autoSize.js"></script>
    <script type="text/javascript" src="js/jquery/jquery-2.14.min.js"></script>
    <script type="text/javascript" src="js/pay.js"></script>
</head>
<body>
<div class="pay-vip">
    <div class='pay-items-t p m'>VIP会员类型</div>
    <div class='pay-items p'>

        <div class="pay-item item_index1 active" data-money="699.9" data-type="4" data-index='4'>
            <div class='yh'>买两年送一年</div>
            <div class='item-name'>白金会员(两年)</div>
            <div class='item-money'>￥699.9</div>
            <div class='item-count'>折合0.64元/天</div>
        </div>
        <div class="pay-item item_index2"data-money="365" data-type="3" data-index='3'>
            <div class='item-name'>黄金会员(一年)</div>
            <div class='item-money'>￥365</div>
            <div class='item-count'>折合1元/天</div>
        </div>
        <div class="pay-item item_index2"data-money="29.9" data-type="1"  data-index='1'>
            <div class='item-name'>体检一次</div>
            <div class='item-money'>￥29.9</div>
            <div class='item-count'>折合29.9元/次</div>
        </div>
    </div>
    <div class='pay-ment-t p m'>支付方式</div>
    <div class='payment'>
        <image class='pay-ment-icon' src='https://bolon.kuaifengpay.com/public/images/wx-icon.jpg' />
        <span>微信支付</span>
    </div>
    <div class='pay-tq-t p m'>
        VIP会员特权
    </div>
    <div class='pay-tq'>
        <image src='https://bolon.kuaifengpay.com/public/images/tq-icon.jpg' class='tq-img' />
    </div>
    <div class='pay'>
        <div class='pay-money'>总计：<span class='pay-money-text'>999.9</span></div>
        <div class='pay-text'>您已经选择:砖石会员,60个月</div>
        <div class='pay-btn' bindtap='pay_order'onclick="callpay()" >确认支付</div>
    </div>
</div>

</div>

<script type="text/javascript">

    function callpay() {
        var type = $(' .active').attr('data-type');
        var total_fee = $(' .active').attr('data-money');
        var openid = "<?php echo $_GET['openid'];?>";
        var access_token = "<?php echo $_GET['access_token'];?>";

        $.ajax({
            url: 'https://bolon.kuaifengpay.com/Api/IndexGene/actionWxhandle?total_fee=' + total_fee + '&type=' + type + '&openid=' + openid,
            type: 'GET',
            success: function (data) {
                //调用微信JS api 支付
                function jsApiCall() {
                    WeixinJSBridge.invoke(
                        'getBrandWCPayRequest',
                        data.data,
                        function (res) {
                            if (res.err_msg == "get_brand_wcpay_request:ok") {
                                //支付成功
                                window.location.href = 'https://bolon.kuaifengpay.com/Api/IndexGene/actionAucess?openid=' + openid+"&access_token="+access_token+"&total_fee="+total_fee


                            } else {
                                // WeixinJSBridge.log(res.err_msg);
                                // alert(res.err_code+res.err_desc+res.err_msg);
                                //支付失败
                            }
                        }
                    );
                }

                if (typeof WeixinJSBridge == "undefined") {
                    if (document.addEventListener) {
                        document.addEventListener('WeixinJSBridgeReady', jsApiCall, false);
                    } else if (document.attachEvent) {
                        document.attachEvent('WeixinJSBridgeReady', jsApiCall);
                        document.attachEvent('onWeixinJSBridgeReady', jsApiCall);
                    }
                } else {
                    jsApiCall();
                }
            }
        });


    }
</script>

<script type="text/javascript">
    var paymentList = [

        { 'money': '699.9', name: ' 铂金会员', 'time': ',36个月' },
        { 'money': '365', name: ' 黄金会员', 'time': ',12个月' },
        { 'money': '29.9', name:'体检一次','time':''}
    ]
    var money = 699.9,time=',36个月',name='铂金会员';
    var pay_money = $('.pay-money-text');
    var pay_time = $('.pay-text');
    $('.pay-item').click(function(){

        var index = $(this).index();
        money = paymentList[index].money;
        name = paymentList[index].name;
        time = paymentList[index].time;
        $(this).addClass('active').siblings().removeClass('active');
        insertData();
    })

    function insertData(){
        pay_money.html(money);
        pay_time.html('您已经选择:'+name+time)
    }
    insertData();

    // 调用支付 你自己写   money  是钱   time 是购买的月 name 是购买的vip名称；
    //直接拿3个变量自己用


</script>
</body>
</html>