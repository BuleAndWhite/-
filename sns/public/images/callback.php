<?php
$xml = file_get_contents('php://input', 'r');
//        $xml = file_get_contents("php://input");
$log = json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
$id = $log['out_trade_no'];  //获取单号
print_r("<script>alert('2');</script>");

//这里修改状态
exit('SUCCESS');  //打死不能去掉
?>
