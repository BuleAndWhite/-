<?php

//$jsApiParameters = json_encode($_POST);
//
//$userL = json_decode($jsApiParameters, true);
//$total_fee = $_GET['total_fee'] * 0.01;

//③、在支持成功回调通知中处理成功之后的事宜，见 notify.php
/**
 * 注意：
 * 1、当你的回调地址不可访问的时候，回调通知会失败，可以通过查询订单来确认支付是否成功
 * 2、jsapi支付时需要填入用户openid，WxPay.JsApiPay.php中有获取openid流程 （文档可以参考微信公众平台“网页授权接口”，
 * 参考http://mp.weixin.qq.com/wiki/17/c0f37d5704f0b64713d5d2c37b468d75.html）
 */
?>

<html>
<head>
    <meta http-equiv="content-type" content="text/html;charset=utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <title>微信支付</title>
</head>
<body>
<br/>
<div class="x_addtable">
    <input type="radio" name="radio" value="1">单选1
    <input type="radio" name="radio" value="2">单选2
    <input type="radio" name="radio" value="3" checked>单选3
    <font color="#9ACD32"><b>该笔订单支付金额为<span style="color:#f00;font-size:50px"> <?php echo $total_fee; ?>
                元</span></b></font><br/><br/>
    <div align="center">
        <button style="width:210px; height:50px; border-radius: 15px;background-color:#FE6714; border:0px #FE6714 solid; cursor: pointer;  color:white;  font-size:16px;"
                type="button" onclick="callpay()">立即支付
        </button>
    </div>
</div>
</body>
<script src="jquery.js"></script>
<script type="text/javascript">

    function callpay() {
        var total_fee = $("input[name='radio']:checked").val();
        if (total_fee == 1) {
            var type = 1;
        } else if (total_fee == 2) {
            var type = 2;
        } else {
            var type = 3;
        }
        $.ajax({
            url: 'https://bolon.kuaifengpay.com/Api/IndexGene/actionWxhandle?total_fee=' + total_fee+'&type='+type,
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
                                window.location.href = "https://beilinliu.kuaifengpay.com/pay_finish.php?total_fee=" + total_fee;
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

</html>
