<?php
return array (
  'app' => 'Admin',
  'model' => 'Order',
  'action' => 'default',
  'data' => '',
  'type' => '1',
  'status' => '1',
  'name' => '订单管理',
  'icon' => 'bars',
  'remark' => '',
  'listorder' => '52',
  'children' => 
  array (
    array (
      'app' => 'Admin',
      'model' => 'Order',
      'action' => 'index',
      'data' => '',
      'type' => '1',
      'status' => '1',
      'name' => '所有订单',
      'icon' => '',
      'remark' => '',
      'listorder' => '0',
    ),
    array (
      'app' => 'Admin',
      'model' => 'Ticket',
      'action' => 'index',
      'data' => '',
      'type' => '0',
      'status' => '0',
      'name' => '门票管理',
      'icon' => '',
      'remark' => '',
      'listorder' => '0',
    ),
    array (
      'app' => 'Admin',
      'model' => 'Order',
      'action' => 'invoice',
      'data' => '',
      'type' => '1',
      'status' => '0',
      'name' => '开票管理',
      'icon' => '',
      'remark' => '',
      'listorder' => '0',
    ),
    array (
      'app' => 'Admin',
      'model' => 'Order',
      'action' => 'delivery_list',
      'data' => '',
      'type' => '1',
      'status' => '1',
      'name' => '发货单',
      'icon' => '',
      'remark' => '',
      'listorder' => '0',
    ),
    array (
      'app' => 'Admin',
      'model' => 'Order',
      'action' => 'return_list',
      'data' => '',
      'type' => '1',
      'status' => '1',
      'name' => '退货单',
      'icon' => '',
      'remark' => '',
      'listorder' => '0',
    ),
    array (
      'app' => 'Admin',
      'model' => 'Order',
      'action' => 'order_log',
      'data' => '',
      'type' => '1',
      'status' => '1',
      'name' => '订单日志',
      'icon' => '',
      'remark' => '',
      'listorder' => '0',
    ),
    array (
      'app' => 'Admin',
      'model' => 'Order',
      'action' => 'recharge',
      'data' => '',
      'type' => '1',
      'status' => '1',
      'name' => '会员充值订单',
      'icon' => '',
      'remark' => '',
      'listorder' => '0',
    ),
  ),
);