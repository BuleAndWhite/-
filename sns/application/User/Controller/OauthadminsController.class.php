<?php
/**
 * 参    数：
 * 作    者：lht
 * 功    能：OAth2.0协议下第三方登录数据报表
 * 修改日期：2013-12-13
 */

namespace User\Controller;

use Common\Controller\AdminbaseController;

class OauthadminsController extends AdminbaseController
{
    protected $num;

    //用户列表(old)
    public function index_old()
    {
        //$where_ands = array("u.status =1");
        $where_ands = array();
        $fields = array(
            'start_time' => array("field" => "u.time", "operator" => ">"),
            'end_time' => array("field" => "u.time", "operator" => "<"),
            'keyword' => array("field" => "u.parent_did", "operator" => "like")
        );

        if (IS_POST) {
            foreach ($fields as $param => $val) {
                if (isset($_POST[$param]) && !empty($_POST[$param])) {
                    $operator = $val['operator'];
                    $field = $val['field'];
                    $_GET[$param] = $_POST[$param];

                    if (!empty($_POST['start_time']) && !empty($_POST['end_time'])) {
                        $get = $param == "start_time" || $param == "end_time" ? $_POST[$param] : $_POST[$param];
                    } else {

                        $get = $_POST[$param];
                    }

                    if ($operator == "like") {
                        $get = "%$get%";
                    }
                    array_push($where_ands, "$field $operator '$get'");
                }
            }
        } else {
            foreach ($fields as $param => $val) {
                if (isset($_GET[$param]) && !empty($_GET[$param])) {
                    $operator = $val['operator'];
                    $field = $val['field'];
                    if (!empty($_GET['start_time']) && !empty($_GET['end_time'])) {
                        $get = $param == "start_time" || $param == "end_time" ? strtotime($_GET[$param]) : $_GET[$param];
                    } else {
                        $get = $_GET[$param];
                    }
                    if ($operator == "like") {
                        $get = "%$get%";
                    }
                    array_push($where_ands, "$field $operator '$get'");
                }
            }
        }


        $id = get_current_admin_id();


        $usersModel = M("users")->where(array("id" => $id))->find();
        $userModel = M("user")->where(array("sn" => $usersModel['did']))->find();

        if ($usersModel['user_type'] == 4) {
            $postReplyS = M('role_user')
                ->alias("r")
                ->join("__ROLE__ u on r.role_id = u.id")
                ->field("u.*")
                ->where(array("r.user_id" => $id))
                ->find();

            array_push($where_ands, " u.parent_did = " . '"' . $usersModel['did'] . '"');
            $where = join("  and ", $where_ands);
            //余额
            $months = M('User')->where(array("parent_did" => $usersModel['did'], "is_vip" => 2))->count();//半年用户数量
            $year = M('User')->where(array("parent_did" => $usersModel['did'], "is_vip" => 3))->count();//一年用户数量
            $once = M('User')->where(array("parent_did" => $usersModel['did'], "is_vip" => 1))->count();//一年用户数量
            $twoYears = M('User')->where(array("parent_did" => $usersModel['did'], "is_vip" => 4))->count();//一年用户数量
            $threeYears = M('User')->where(array("parent_did" => $usersModel['did'], "is_vip" => 5))->count();//一年用户数量
            $machineOwner = M('User')->where(array("parent_did" => $usersModel['did'], "is_vip" => 7))->count();//虚拟机主用户数量
            $owner = M('User')->where(array("parent_did" => $usersModel['did'], "is_vip" => 8))->count();//机主用户数量
            $total = M('User')->where(array("sn" => $usersModel['did'], "is_vip" => 5))->find();//机主总盈利
            $total = $this->total($total['owner_id']);
            $machineOwner = $machineOwner * 10000;//天使
            $owner = $owner * 100000;
            $threeYears = $threeYears * 10000;
            $twoYears = $twoYears * 2000;
            $months = $months * 218;
            $year = $year * 365;
            $once = $once * 29.9;
            $whereLs['status'] = array('in', "1,2");//cid在这个数组中
            $whereL['parent_did'] = $usersModel['did'];//cid在这个数组中

            $postReply = M('User')
                ->where($whereL)
                ->select();

//            $sum =0;
//            foreach ($postReply as  $i=>$item) {
//                $whereLs['unionid'] =$item['unionid'];
//
//                $money = M('withdraw')
//                    ->where($whereLs)
//                    ->sum("money");
//                $sum =$sum +$money;
//            }

            $begintime = date("Y-m-d H:i:s", mktime(0, 0, 0, date('m'), date('d'), date('Y')));
            $endtime = date("Y-m-d H:i:s", mktime(0, 0, 0, date('m'), date('d') + 1, date('Y')) - 1);
            $whereA = array(
                'parent_did' => $usersModel['did'],
                'time' => array(array('gt', $begintime), array('lt', $endtime)),
            );

//            $userList['totalRevenue'] = ($once + $months + $year+$threeYears+$twoYears+$machineOwner+$owner) ;
            $userList['totalRevenue'] = ($once + $months + $year + $threeYears + $twoYears);
//            $userList['total'] = ($once + $months + $year+$threeYears+$twoYears) * ($postReplyS['percentage']/100) -$sum;
//            $userList['total'] = (($once*0.274) + ($months*0.274) + ($year*0.274)+($threeYears*0.25)+($twoYears*0.1)+($machineOwner*0.1)+($owner*0.1)) * ($postReplyS['percentage']/100) ;
            $recording = M("withdraw")->where(array("unionid" => $userModel['unionid'], "status" => 2))->sum("money");//提现成功
            $underReview = M("withdraw")->where(array("unionid" => $userModel['unionid'], "status" => 1))->sum("money");//正在审核

            $userList['total'] = floor((($once * 0.274) + ($months * 0.274) + ($year * 0.274) + ($threeYears * 0.1) + ($twoYears * 0.25) + ($total * 0.01)) - ($recording + $underReview));
            $userList['count'] = M('User')
                ->where($whereL)
                ->count();
            $userList['today'] = M('User')
                ->where($whereA)
                ->count();

        } else if ($usersModel['user_type'] == 5) {
            $postReplyS = M('role_user')
                ->alias("r")
                ->join("__ROLE__ u on r.role_id = u.id")
                ->field("u.*")
                ->where(array("r.user_id" => $id))
                ->find();

            array_push($where_ands, " u.parent_did = " . '"' . $usersModel['did'] . '"');
            $where = join("  and ", $where_ands);
            //余额
            $months = M('User')->where(array("parent_did" => $usersModel['did'], "is_vip" => 2))->count();//半年用户数量
            $year = M('User')->where(array("parent_did" => $usersModel['did'], "is_vip" => 3))->count();//一年用户数量
            $once = M('User')->where(array("parent_did" => $usersModel['did'], "is_vip" => 1))->count();//一年用户数量
            $twoYears = M('User')->where(array("parent_did" => $usersModel['did'], "is_vip" => 4))->count();//一年用户数量
            $threeYears = M('User')->where(array("parent_did" => $usersModel['did'], "is_vip" => 5))->count();//一年用户数量
            $machineOwner = M('User')->where(array("parent_did" => $usersModel['did'], "is_vip" => 7))->count();//虚拟机主用户数量
            $owner = M('User')->where(array("parent_did" => $usersModel['did'], "is_vip" => 8))->count();//机主用户数量
            $machineOwner = $machineOwner * 10000;//天使
            $owner = $owner * 100000;
            $threeYears = $threeYears * 10000;
            $twoYears = $twoYears * 2000;
            $months = $months * 218;
            $year = $year * 365;
            $once = $once * 29.9;
            $whereLs['status'] = array('in', "1,2");//cid在这个数组中
            $whereL['parent_did'] = $usersModel['did'];//cid在这个数组中

            $postReply = M('User')
                ->where($whereL)
                ->select();

//            $sum =0;
//            foreach ($postReply as  $i=>$item) {
//                $whereLs['unionid'] =$item['unionid'];
//
//                $money = M('withdraw')
//                    ->where($whereLs)
//                    ->sum("money");
//                $sum =$sum +$money;
//            }

            $begintime = date("Y-m-d H:i:s", mktime(0, 0, 0, date('m'), date('d'), date('Y')));
            $endtime = date("Y-m-d H:i:s", mktime(0, 0, 0, date('m'), date('d') + 1, date('Y')) - 1);
            $whereA = array(
                'parent_did' => $usersModel['did'],
                'time' => array(array('gt', $begintime), array('lt', $endtime)),
            );

//            $userList['totalRevenue'] = ($once + $months + $year+$threeYears+$twoYears+$machineOwner+$owner) ;
            $userList['totalRevenue'] = ($once + $months + $year + $threeYears + $twoYears);
//            $userList['total'] = ($once + $months + $year+$threeYears+$twoYears) * ($postReplyS['percentage']/100) -$sum;
//            $userList['total'] = (($once*0.274) + ($months*0.274) + ($year*0.274)+($threeYears*0.25)+($twoYears*0.1)+($machineOwner*0.1)+($owner*0.1)) * ($postReplyS['percentage']/100) ;
            $recording = M("withdraw")->where(array("unionid" => $userModel['unionid'], "status" => 2))->sum("money");//提现成功
            $underReview = M("withdraw")->where(array("unionid" => $userModel['unionid'], "status" => 1))->sum("money");//正在审核

            $userList['total'] = floor((($once * 0.274) + ($months * 0.274) + ($year * 0.274) + ($threeYears * 0.1) + ($twoYears * 0.25)) - ($recording + $underReview));
            $userList['count'] = M('User')
                ->where($whereL)
                ->count();
            $userList['today'] = M('User')
                ->where($whereA)
                ->count();

        } else if ($usersModel['user_type'] == 3) {

            $postReplyS = M('role_user')
                ->alias("r")
                ->join("__ROLE__ u on r.role_id = u.id")
                ->field("u.*")
                ->where(array("r.user_id" => $id))
                ->find();

            array_push($where_ands, " u.owner_id = " . '"' . $usersModel['did'] . '"');
            $where = join("  and ", $where_ands);
            //余额
            $months = M('User')->where(array("owner_id" => $usersModel['did'], "is_vip" => 2))->count();//半年用户数量
            $year = M('User')->where(array("owner_id" => $usersModel['did'], "is_vip" => 3))->count();//一年用户数量
            $once = M('User')->where(array("owner_id" => $usersModel['did'], "is_vip" => 1))->count();//一年用户数量
            $twoYears = M('User')->where(array("owner_id" => $usersModel['did'], "is_vip" => 4))->count();//一年用户数量
            $threeYears = M('User')->where(array("owner_id" => $usersModel['did'], "is_vip" => 5))->count();//一年用户数量
            $partner = M('User')->where(array("owner_id" => $usersModel['did'], "is_vip" => 5))->count();//一年用户数量
            $machineOwner = M('User')->where(array("owner_id" => $usersModel['did'], "is_vip" => 7))->count();//虚拟机主用户数量
            $owner = M('User')->where(array("owner_id" => $usersModel['did'], "is_vip" => 8))->count();//机主用户数量

            $machineOwner = $machineOwner * 10000;//天使
            $owner = $owner * 100000;
            $threeYears = $threeYears * 10000;
            $twoYears = $twoYears * 2000;
            $months = $months * 218;
            $year = $year * 365;
            $once = $once * 29.9;
            $whereLs['status'] = array('in', "1,2");//cid在这个数组中
            $whereL['owner_id'] = $usersModel['did'];//cid在这个数组中

            $postReply = M('User')
                ->where($whereL)
                ->select();

//            $sum =0;
//            foreach ($postReply as  $i=>$item) {
//                $whereLs['unionid'] =$item['unionid'];
//
//                $money = M('withdraw')
//                    ->where($whereLs)
//                    ->sum("money");
//                $sum =$sum +$money;
//            }

            $begintime = date("Y-m-d H:i:s", mktime(0, 0, 0, date('m'), date('d'), date('Y')));
            $endtime = date("Y-m-d H:i:s", mktime(0, 0, 0, date('m'), date('d') + 1, date('Y')) - 1);
            $whereA = array(
                'owner_id' => $usersModel['did'],
                'time' => array(array('gt', $begintime), array('lt', $endtime)),
            );

//        $userList['totalRevenue'] = ($once + $months + $year+$threeYears+$twoYears+$machineOwner+$owner) ;
            $userList['totalRevenue'] = ($once + $months + $year + $threeYears + $twoYears);
            $userList['partnerCount'] = $partner;
//            $userList['total'] = ($once + $months + $year+$threeYears+$twoYears) * ($postReplyS['percentage']/100) -$sum;

//            $userList['total'] = (($once*0.274) + ($months*0.274) + ($year*0.274)+($threeYears*0.3)+($twoYears*0.5)+($machineOwner*0.1)+($owner*0.1)) * ($postReplyS['percentage']/100) ;
            $userE = M("user")->where(array("parent_did" => $usersModel['did']))->select();
            $talCount = 0;
            if ($userE) {
                $arrayC = 0;
                foreach ($userE as $key => $value) {
                    $monthss = M('User')->where(array("parent_did" => $value['parent_did'], "is_vip" => 2))->count();//半年用户数量
                    $years = M('User')->where(array("parent_did" => $value['parent_did'], "is_vip" => 3))->count();//一年用户数量
                    $onces = M('User')->where(array("parent_did" => $value['parent_did'], "is_vip" => 1))->count();//一年用户数量
                    $twoYearss = M('User')->where(array("parent_did" => $value['parent_did'], "is_vip" => 4))->count();//一年用户数量
                    $threeYearss = M('User')->where(array("parent_did" => $value['parent_did'], "is_vip" => 5))->count();//一年用户数量
                    $machineOwners = M('User')->where(array("parent_did" => $value['parent_did'], "is_vip" => 7))->count();//虚拟机主用户数量
                    $owners = M('User')->where(array("parent_did" => $value['parent_did'], "is_vip" => 8))->count();//机主用户数量
                    $machineOwners = $machineOwners * 30000;
                    $owners = $owners * 100000;
                    $threeYearss = $threeYearss * 10000;
                    $twoYearss = $twoYearss * 2000;
                    $monthss = $monthss * 218;
                    $years = $years * 365;
                    $onces = $onces * 29.9;
                    $arrayC = (($onces * 0.274) + ($monthss * 0.274) + ($years * 0.274) + ($threeYearss * 0.1) + ($twoYearss * 0.25));
                }
                $talCount = (($once * 0.274) + ($months * 0.274) + ($year * 0.274) + ($threeYears * 0.5) + ($twoYears * 0.3) + $arrayC);
                $recording = M("withdraw")->where(array("unionid" => $userModel['unionid'], "status" => 2))->sum("money");//提现成功
                $underReview = M("withdraw")->where(array("unionid" => $userModel['unionid'], "status" => 1))->sum("money");//正在审核

                $userList['total'] = floor($talCount - ($talCount * ($partner * 0.01)) - ($recording + $underReview));

            } else {
                $recording = M("withdraw")->where(array("unionid" => $userModel['unionid'], "status" => 2))->sum("money");//提现成功
                $underReview = M("withdraw")->where(array("unionid" => $userModel['unionid'], "status" => 1))->sum("money");//正在审核

                $talCount = (($once * 0.274) + ($months * 0.274) + ($year * 0.274) + ($threeYears * 0.5) + ($twoYears * 0.3));
                $userList['total'] = floor($talCount - ($talCount * ($partner * 0.01)) - ($recording + $underReview));
            }
//            $userList['total'] = ($once + $months + $year+$threeYears+$twoYears+$machineOwner+$owner) * ($postReplyS['percentage']/100) ;
            $userList['count'] = M('User')
                ->where($whereL)
                ->count();
            $userList['today'] = M('User')
                ->where($whereA)
                ->count();

        } else if ($usersModel['user_type'] == 2) {

            $postReplyS = M('role_user')
                ->alias("r")
                ->join("__ROLE__ u on r.role_id = u.id")
                ->field("u.*")
                ->where(array("r.user_id" => $id))
                ->find();

            array_push($where_ands, " u.operation = " . '"' . $usersModel['did'] . '"');
            $where = join("  and ", $where_ands);
            //余额
            $months = M('User')->where(array("operation" => $usersModel['did'], "is_vip" => 2))->count();//半年用户数量
            $year = M('User')->where(array("operation" => $usersModel['did'], "is_vip" => 3))->count();//一年用户数量
            $once = M('User')->where(array("operation" => $usersModel['did'], "is_vip" => 1))->count();//一年用户数量
            $twoYears = M('User')->where(array("operation" => $usersModel['did'], "is_vip" => 4))->count();//一年用户数量
            $threeYears = M('User')->where(array("operation" => $usersModel['did'], "is_vip" => 5))->count();//一年用户数量
            $machineOwner = M('User')->where(array("operation" => $usersModel['did'], "is_vip" => 7))->count();//虚拟机主用户数量
            $owner = M('User')->where(array("operation" => $usersModel['did'], "is_vip" => 8))->count();//机主用户数量
            $machineOwner = $machineOwner * 10000;//天使
            $owner = $owner * 100000;
            $threeYears = $threeYears * 10000;
            $twoYears = $twoYears * 2000;
            $months = $months * 218;
            $year = $year * 365;
            $once = $once * 29.9;
            $whereLs['status'] = array('in', "1,2");//cid在这个数组中
            $whereL['operation'] = $usersModel['did'];//cid在这个数组中

            $postReply = M('User')
                ->where($whereL)
                ->select();

//            $sum =0;
//            foreach ($postReply as  $i=>$item) {
//                $whereLs['unionid'] =$item['unionid'];
//
//                $money = M('withdraw')
//                    ->where($whereLs)
//                    ->sum("money");
//                $sum =$sum +$money;
//            }

            $begintime = date("Y-m-d H:i:s", mktime(0, 0, 0, date('m'), date('d'), date('Y')));
            $endtime = date("Y-m-d H:i:s", mktime(0, 0, 0, date('m'), date('d') + 1, date('Y')) - 1);
            $whereA = array(
                'operation' => $usersModel['did'],
                'time' => array(array('gt', $begintime), array('lt', $endtime)),
            );

            $userList['totalRevenue'] = ($once + $months + $year + $threeYears + $twoYears + $machineOwner + $owner);
//            $userList['total'] = ($once + $months + $year+$threeYears+$twoYears) * ($postReplyS['percentage']/100) -$sum;
//            $userList['total'] = (($once*0.178) + ($months*0.178) + ($year*0.178)+($threeYears*0.1)+($twoYears*0.15)+($machineOwner*0.4)+($owner*0.4));
            $recording = M("withdraw")->where(array("unionid" => $userModel['unionid'], "status" => 2))->sum("money");//提现成功
            $underReview = M("withdraw")->where(array("unionid" => $userModel['unionid'], "status" => 1))->sum("money");//正在审核

            $userList['total'] = floor((($once * 0.178) + ($months * 0.178) + ($year * 0.178) + ($threeYears * 0.1) + ($twoYears * 0.15) + ($owner * 0.4)) - ($recording + $underReview));

//            $userList['total'] = ($once + $months + $year+$threeYears+$twoYears+$machineOwner+$owner) * ($postReplyS['percentage']/100) ;
            $userList['count'] = M('User')
                ->where($whereL)
                ->count();
            $userList['today'] = M('User')
                ->where($whereA)
                ->count();

        } else {
            array_push($where_ands, " u.operation !='' ");
            $where = join("  and ", $where_ands);

            //余额
            $months = M('User')->where("is_vip=2 and operation !='' ")->count();//半年用户数量
            $year = M('User')->where("is_vip=3 and operation !='' ")->count();//一年用户数量
            $once = M('User')->where("is_vip=1 and operation !='' ")->count();//一年用户数量
            $twoYears = M('User')->where("is_vip=4 and operation !='' ")->count();//一年用户数量
            $threeYears = M('User')->where("is_vip=5 and operation !='' ")->count();//一年用户数量
            $machineOwner = M('User')->where("is_vip=7 and operation !='' ")->count();//虚拟机主用户数量

            $owner = M('User')->where("is_vip=8 and operation !='' ")->count();//机主用户数量
            $machineOwner = $machineOwner * 30000;
            $owner = $owner * 100000;
            $threeYears = $threeYears * 10000;
            $twoYears = $twoYears * 2000;
            $months = $months * 218;
            $year = $year * 365;
            $once = $once * 29.9;
            $whereLs['status'] = array('in', "1,2");//cid在这个数组中
//            $whereL['sn'] = array('EQ',$usersModel['did']);//cid在这个数组中
            $postReply = M('User')
                ->select();
//            $sum =0;
//            foreach ($postReply as  $i=>$item) {
//                $whereLs['unionid'] =$item['unionid'];
//
//                $money = M('withdraw')
//                    ->where($whereLs)
//                    ->sum("money");
//                $sum =$sum +$money;
//            }

            $begintime = date("Y-m-d H:i:s", mktime(0, 0, 0, date('m'), date('d'), date('Y')));
            $endtime = date("Y-m-d H:i:s", mktime(0, 0, 0, date('m'), date('d') + 1, date('Y')) - 1);
            $whereA = array(
                'time' => array(array('gt', $begintime), array('lt', $endtime)),
                'operation' => array(array('neq', "")),
            );

            $userList['totalRevenue'] = ($once + $months + $year + $threeYears + $twoYears + $machineOwner + $owner);
//            $userList['total'] = ($once + $months + $year+$threeYears+$twoYears) * 0.25 -$sum;
            $userList['total'] = floor(($once + $months + $year + $threeYears + $twoYears + $machineOwner + $owner) * 0.25);
            $userList['count'] = M('User')
                ->where("operation !=''")
                ->count();
            $userList['today'] = M('User')
                ->where($whereA)
                ->count();
        }


        $oauth_user_model = M('User');

        $count = $oauth_user_model->alias("u")->order("u.time DESC")->where($where)->count();
        $page = $this->page($count, 20);
        $lists = $oauth_user_model
            ->alias("u")
            ->order("u.time DESC")
            ->where($where)
            ->limit($page->firstRow . ',' . $page->listRows)
            ->select();

        foreach ($lists as $key => $list) {
            $lists[$key]['count'] = $oauth_user_model->where(array("parent_id" => $list['unionid']))->count();
            $integral = M("sign")->where(array("unionid" => $list['unionid']))->field("integral")->find();
            $lists[$key]['integral'] = $integral['integral'];

        }

        $user_id = get_current_admin_id();
        $userOne = M("users")->where(array("id" => $user_id))->find();
        $this->assign("page", $page->show('Admin'));
        $this->assign('lists', $lists);
        $this->assign('userList', $userList);
        $this->assign('user', $usersModel);
        $this->assign('parent_id', $id);
        $this->assign("userOneType", $userOne['user_type']);
        $this->assign("userOneDid", $userOne['did']);
        $this->display();
    }

    //用户列表(new)
    public function index()
    {
        $where_ands = array();
        $fields = array(
            'start_time' => array("field" => "time", "operator" => ">"),
            'end_time' => array("field" => "time", "operator" => "<"),
            'keyword' => array("field" => "name", "operator" => "like"),
            'status' => array("field" => "agent_level", "operator" => "=")
        );

        if (IS_POST) {
            foreach ($fields as $param => $val) {
                if (isset($_POST[$param]) && !empty($_POST[$param])) {
                    $operator = $val['operator'];
                    $field = $val['field'];
                    $_GET[$param] = $_POST[$param];

                    if (!empty($_POST['start_time']) && !empty($_POST['end_time'])) {
                        $get = $param == "start_time" || $param == "end_time" ? $_POST[$param] : $_POST[$param];
                    } else {

                        $get = $_POST[$param];
                    }

                    if ($operator == "like") {
                        $get = "%$get%";
                    }
                    array_push($where_ands, "$field $operator '$get'");
                }
            }
        } else {
            foreach ($fields as $param => $val) {
                if (isset($_GET[$param]) && !empty($_GET[$param])) {
                    $operator = $val['operator'];
                    $field = $val['field'];
                    if (!empty($_GET['start_time']) && !empty($_GET['end_time'])) {
                        $get = $param == "start_time" || $param == "end_time" ? $_GET[$param] : $_GET[$param];
                    } else {
                        $get = $_GET[$param];
                    }
                    if ($operator == "like") {
                        $get = "%$get%";
                    }
                    array_push($where_ands, "$field $operator '$get'");
                }
            }
        }
        $where = join(" and ", $where_ands);;

        $begintime = date("Y-m-d H:i:s", mktime(0, 0, 0, date('m'), date('d'), date('Y')));
        $endtime = date("Y-m-d H:i:s", mktime(0, 0, 0, date('m'), date('d') + 1, date('Y')) - 1);
        $whereA = array(
            'time' => array(array('gt', $begintime), array('lt', $endtime)),
        );
        $userList['today'] = $lists = M('user')
            ->where($whereA)
            ->count();
        $userList['count'] = $lists = M('user')
            ->where($where)
            ->order("time DESC")
            ->count();
        $page = $this->page($userList['count'], 20);
        $lists = M('user')
            ->where($where)
            ->order("time DESC")
            ->limit($page->firstRow . ',' . $page->listRows)
            ->select();
        $data = array();
        foreach ($lists as $key => $list) {
            $integral = M("sign")->where(array("unionid" => $list['unionid']))->field("integral")->find();
            $lists[$key]['integral'] = $integral['integral'];
            $lists[$key]['count'] = M('user')->where(array("parent_id" => $list['unionid']))->count();
            $res = $this->teamRecursion($i = 0, $list['unionid'], $data);
            $ress = $this->teamRecursions($list['unionid']);
            $res = $this->uniquArr($res);
            $ress = $this->uniquArr($ress);
            $lists[$key]['countMoxibustion'] = sizeof($res);
            $lists[$key]['countMoxibustions'] = sizeof($ress);
        }

        //艾灸仪奖金池
        $beginThismonth = date("Y-m-01 00:00:00", time());
        $endThismonth = date('Y-m-t 23:59:59', time());
        $wheres = array(
            "success" => 1,
            "order_type" => 1,
            "type" => 9,
            "update_time" => array("between", array($beginThismonth, $endThismonth))
        );
        $bonusMonthe = M("order")
            ->where($wheres)
            ->sum("total_fee");
        $userList["Moxibustion"] = floor($bonusMonthe) / 100;
        $userList["A"] = floor($bonusMonthe * 0.05) / 100;
        $userList["B"] = floor($bonusMonthe * 0.05) / 100;
        $userList["C"] = floor($bonusMonthe * 0.03) / 100;


        $totalShare = $this->shareAmountAll();
        $totalRevenue = $this->ownerAmountAll();
        $res = M('order')
            ->where(array('success' => 1))
            ->sum('total_fee');
        //毛利润
        $userList['Grossprofit'] = floor($res) / 100;
        //纯利润
        $userList['Netprofit'] = $res - ($totalShare + $totalRevenue);


        $this->assign("page", $page->show('Admin'));
        $this->assign('userList', $userList);
        $this->assign('lists', $lists);

        $this->display();


    }

    public function total($did)
    {


        //余额
        $months = M('User')->where(array("owner_id" => $did, "is_vip" => 2))->count();//半年用户数量
        $year = M('User')->where(array("owner_id" => $did, "is_vip" => 3))->count();//一年用户数量
        $once = M('User')->where(array("owner_id" => $did, "is_vip" => 1))->count();//一年用户数量
        $twoYears = M('User')->where(array("owner_id" => $did, "is_vip" => 4))->count();//一年用户数量
        $threeYears = M('User')->where(array("owner_id" => $did, "is_vip" => 5))->count();//一年用户数量
        $partner = M('User')->where(array("owner_id" => $did, "is_vip" => 5))->count();//一年用户数量
        $machineOwner = M('User')->where(array("owner_id" => $did, "is_vip" => 7))->count();//虚拟机主用户数量
        $owner = M('User')->where(array("owner_id" => $did, "is_vip" => 8))->count();//机主用户数量

        $machineOwner = $machineOwner * 10000;//天使
        $owner = $owner * 100000;
        $threeYears = $threeYears * 10000;
        $twoYears = $twoYears * 2000;
        $months = $months * 218;
        $year = $year * 365;
        $once = $once * 29.9;

        $userE = M("user")->where(array("parent_did" => $did))->select();
        $talCount = 0;
        if ($userE) {
            $arrayC = 0;
            foreach ($userE as $key => $value) {
                $monthss = M('User')->where(array("parent_did" => $value['parent_did'], "is_vip" => 2))->count();//半年用户数量
                $years = M('User')->where(array("parent_did" => $value['parent_did'], "is_vip" => 3))->count();//一年用户数量
                $onces = M('User')->where(array("parent_did" => $value['parent_did'], "is_vip" => 1))->count();//一年用户数量
                $twoYearss = M('User')->where(array("parent_did" => $value['parent_did'], "is_vip" => 4))->count();//一年用户数量
                $threeYearss = M('User')->where(array("parent_did" => $value['parent_did'], "is_vip" => 5))->count();//一年用户数量
                $machineOwners = M('User')->where(array("parent_did" => $value['parent_did'], "is_vip" => 7))->count();//虚拟机主用户数量
                $owners = M('User')->where(array("parent_did" => $value['parent_did'], "is_vip" => 8))->count();//机主用户数量
                $machineOwners = $machineOwners * 30000;
                $owners = $owners * 100000;
                $threeYearss = $threeYearss * 10000;
                $twoYearss = $twoYearss * 2000;
                $monthss = $monthss * 218;
                $years = $years * 365;
                $onces = $onces * 29.9;
                $arrayC = (($onces * 0.274) + ($monthss * 0.274) + ($years * 0.274) + ($threeYearss * 0.1) + ($twoYearss * 0.25));
            }
            $talCount = (($once * 0.274) + ($months * 0.274) + ($year * 0.274) + ($threeYears * 0.5) + ($twoYears * 0.3) + $arrayC);
            return $talCount;

        } else {
            $talCount = (($once * 0.274) + ($months * 0.274) + ($year * 0.274) + ($threeYears * 0.5) + ($twoYears * 0.3));
            return $talCount;
        }

    }

    //删除用户
    function delete()
    {
        $id = intval($_GET['id']);
        if (empty($id)) {
            $this->error('非法数据！');
        }
        $rst = M("User")->where(array("id" => $id))->delete();
        if ($rst !== false) {
            $this->success("删除成功！", U("oauthadmin/index"));
        } else {
            $this->error('删除失败！');
        }
    }

    /**
     * @date：2018年1月22日21:43:46
     * @param ：拉黑第三方用户
     * @User：刘柏林
     */
    function ban()
    {
        $id = intval($_GET['id']);
        if ($id) {
            $rst = M("user")->where(array("id" => $id))->setField('user_status', '0');
            if ($rst) {
                $this->success("会员拉黑成功！", U("oauthadmin/index"));
            } else {
                $this->error('会员拉黑失败！');
            }
        } else {
            $this->error('数据传入失败！');
        }
    }

    /**
     * @date：2018年1月22日21:44:08
     * @param 启用第三方用户：
     * @User：刘柏林
     */
    function cancelban()
    {
        $id = intval($_GET['id']);
        if ($id) {
            $rst = M("user")->where(array("id" => $id))->setField('user_status', '1');
            if ($rst) {
                $this->success("会员启用成功！", U("oauthadmin/index"));
            } else {
                $this->error('会员启用失败！');
            }
        } else {
            $this->error('数据传入失败！');
        }
    }

    /**
     * @date：2018年1月22日21:15:29
     * @param 是否禁言
     * @User：刘柏林
     */
    public function isGag()
    {
        $gag = intval($_GET['gag']);
        $unGag = intval($_GET['unGag']);
        if ($gag) {
            $rst = M("oauth_user")->where(array("id" => $gag))->setField('gag', '1');
            if ($rst) {
                $this->success("会员启用成功！", U("oauthadmin/index"));
            } else {
                $this->error('会员启用失败！');
            }
        }

        if ($unGag) {
            $rst = M("oauth_user")->where(array("id" => $unGag))->setField('gag', '0');
            if ($rst) {
                $this->success("会员禁言成功！", U("oauthadmin/index"));
            } else {
                $this->error('会员禁言失败！');
            }
        }
    }

    public function add()
    {
        $this->display();
    }

    public function addList()
    {
        if (IS_POST) {
            $data = I('post');

            $times = "";
            switch ($data['is_vip']) {
                case 1:
                    $times = date("Y-m-d H:i:s", strtotime("+1 day"));
                    break;
                case 2:
                    $times = date("Y-m-d H:i:s", strtotime("+6 month"));
                    break;
                case 3:
                    $times = date("Y-m-d H:i:s", strtotime("+1 year"));
                    break;
                case 4:
                    $times = date("Y-m-d H:i:s", strtotime("+2 year"));
                    break;
                case 5:
                    $times = date("Y-m-d H:i:s", strtotime("+3 year"));
                    break;
                case 7:
                    $times = date("Y-m-d H:i:s", strtotime("+3 year"));
                    break;
                case 8:
                    $times = date("Y-m-d H:i:s", strtotime("+3 year"));
                    break;
            }

            $data['name'] = $data['name'];
            $data['is_vip'] = $data['is_vip'];
            $data['idCard'] = $data['idCard'];
            $data['time'] = $data['time'];
            $data['time_maturity'] = $times;
            $data['time_start'] = date("Y-m-d H:i:s", time());
            $rs = M("user")->add($data);
            $orderModel['time'] = date("Y-m-d H:i:s", time());
            $orderModel['status'] = 1;
            $orderModel['type'] = $data['is_vip'];
            $orderModel['uid'] = $rs;
            $orderModel['success'] = 1;
            $orderModel['prepay_id'] = "wx" . $this->createNoncestr();
            $orderModel['total_fee'] = $data['total_fee'];
            $orderList = M("order")->add($orderModel);

            if ($orderList !== false) {
                $this->success("保存成功！");
                exit;
            } else {
                $this->error("保存失败！");
                exit;
            }
        }


    }

    /**
     *  作用：产生随机字符串，不长于32位
     */

    public
    function createNoncestr($length = 38)
    {
        $chars = "abcdefghijklmnopqrstuvwxyz0123456789";
        $str = "";
        for ($i = 0; $i < $length; $i++) {
            $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
        }

        return $str;
    }

    /**
     * Notes: 所有机主的总收入
     * User: Sen
     * Date: 2019-7-11 0011
     * Time: 15:20
     * Return:
     */
    protected
    function ownerAmountAll()
    {
        $user = M("user")
            ->where(array('is_vip' => array("egt", 8)))
            ->field('unionid')
            ->select();
        $total = 0;
        foreach ($user as $user_key => $val) {
            $user = M("user")
                ->where(array('unionid' => $val['unionid']))
                ->find();

            $multi_equipment = explode(",", $user['multi_equipment']);

            $where = array(
                'c.id' => array('in', $multi_equipment),
                'c.owner_status' => array('in', array(1, 2)),
                'c.unionid' => $val['unionid'],
                'a.success' => 1
            );


            $where['_string'] = 'a.time between c.binding_time and c.untied_time';


            $order['data'] = M('order')
                ->alias('a')
                ->join("__USER__ b ON a.uid = b.id")
                ->join("__DEVICE_BINDING__ c ON b.machine = c.device_code")
                ->where($where)
                ->order('a.time DESC')
                ->select();
            $tl = 0;
            foreach ($order['data'] as $order_key => $val) {
                $tl += (floor($val['total_fee'] * 100) / 100) * $val['proportion'] * 0.01;
            }
            $tl = (floor($tl * 100) / 100);

            $total += $tl;
        }

        return $total;
    }

    /**
     * Notes: 所有分销的总输入
     * User: Sen
     * Date: 2019-7-11 0011
     * Time: 15:21
     * Return:
     */
    protected
    function shareAmountAll()
    {
        $user = M("user")
            ->field('id,parent_id')
            ->order("id DESC")
            ->select();

        $arr = array();

        foreach ($user as $user_key => $user_val) {
            if (!empty($user_val['parent_id'])) {
                array_push($arr, $user_val['id']);
            }
        }
        $where = array(
            "a.success" => 1,
            "a.uid" => array('in', $arr)
        );
        $moneyList = M('order')
            ->alias("a")
            ->join("RIGHT JOIN __USER__ b ON a.uid = b.id")
            ->where($where)
            ->field('a.total_fee')
            ->select();

        $total = 0;
        foreach ($moneyList as $moneyList_key => $val) {
            $total += floor($val['total_fee'] * 0.273) * 0.01;
        }
        return $total;
    }

    /**
     * Notes: 设置幸运用户
     * User: Sen
     * Date: 2019-7-19 0019
     * Time: 17:29
     * Return:
     */
    public function setlucky()
    {
        $id = intval($_GET['id']);

        if ($id) {
            $res = M("user")
                ->where(array('id' => $id))
                ->field("lucky")
                ->find();
            if ($res['lucky'] > 0) {
                $this->error("该用户已经是幸运用户！");
            } else {
                $res = M("user")
                    ->where(array('id' => $id))
                    ->save(array("lucky" => 1));
                if ($res) {
                    $this->success("设置幸运用户成功！");
                } else {
                    $this->error("设置幸运用户失败！");
                }
            }
        } else {
            $this->error('非法错误');
        }
    }

    /**
     * Notes: 取消幸运用户
     * User: Sen
     * Date: 2019-7-19 0019
     * Time: 17:29
     * Return:
     */
    public function cancellucky()
    {
        $id = intval($_GET['id']);

        if ($id) {
            $res = M("user")
                ->where(array('id' => $id))
                ->field("lucky")
                ->find();
            if ($res['lucky'] = 0) {
                $this->error("该用户不是幸运用户,无法取消！");
            } else {
                $res = M("user")
                    ->where(array('id' => $id))
                    ->save(array("lucky" => 0));
                if ($res) {
                    $this->success("取消幸运用户成功！");
                } else {
                    $this->error("取消幸运用户失败！");
                }
            }
        } else {
            $this->error('非法错误');
        }
    }

    /**
     * Notes: 艾灸团队(递归)
     * User: Sen
     * Date: 2019-7-29 0029
     * Time: 19:03
     * Return:
     * @param $i
     * @param $unionid
     * @param $data
     * @return mixed
     */
    public function teamRecursion($i, $unionid, $data)
    {
        $arr = array();
        if (empty($unionid)) {
            return $data;
        }
        if ($i == 9) {
            return $data;
        }
        $where['b.parent_id'] = array('in', $unionid);
        $where['a.success'] = 1;
        $where['a.platform'] = 1;
        $where['a.order_type'] = 1;
        $where['a.type'] = 9;

        if ($i < 10) {
            $orderList = M("order")
                ->alias('a')
                ->join("right join __USER__ b ON a.uid = b.id")
                ->where($where)
                ->order("a.time DESC")
                ->field("b.id,a.type,a.time,b.name,b.avatar,b.unionid")
                ->select();
            //计算金额
            foreach ($orderList as $k => $val) {
                array_push($arr, $val['unionid']);
            }
            $arr = array_unique($arr);
            if (in_array($this->all_unionid, $arr)) {
                $arr = array_flip($arr);
                unset($arr[$this->all_unionid]);
                $arr = array_flip($arr);
            }

            $this->num += sizeof($arr);

            $orderList = array_merge($orderList, $data);

            $res = $this->teamRecursion($i + 1, $arr, $orderList);
        }
        return $res;
    }

    /**
     * Notes: 艾灸团队(无递归)
     * User: Sen
     * Date: 2019-7-29 0029
     * Time: 19:03
     * Return:
     * @param $i
     * @param $unionid
     * @param $data
     * @return mixed
     */
    public function teamRecursions($unionid)
    {
        $arr = array();
        $where['b.parent_id'] = $unionid;
        $where['a.success'] = 1;
        $where['a.platform'] = 1;
        $where['a.order_type'] = 1;
        $where['a.type'] = 9;
        $orderList = M("order")
            ->alias('a')
            ->join("right join __USER__ b ON a.uid = b.id")
            ->where($where)
            ->order("a.time DESC")
            ->field("b.id,a.type,a.time,b.name,b.avatar,b.unionid")
            ->select();

        foreach ($orderList as $k => $val) {
            array_push($arr, $val['unionid']);
        }
        $arr = array_unique($arr);
        return $orderList;
    }

    /**
     * Notes: 分页
     * User: Jason
     * Date: 2019-7-28
     * Time: 12:40
     * Return:
     * @param $count 条数
     * @param $page  分页条数
     * @param $pageNum  页码
     */
    protected
    function pages($count, $page, $pageNum)
    {
        //分页
        $num = $count / $page;
        if (empty($pageNum) || $pageNum == 1) {
            $current_page = 1;
            $start_num = 0;
            if (is_float($num)) {
                $total_page = ceil($num);
            } else {
                $total_page = $num;
            }
        } else {
            $current_page = $pageNum;
            $start_num = ($pageNum - 1) * $page;
            if (is_float($num)) {
                $total_page = ceil($num);
            } else {
                $total_page = $num;
            }
        }
        $data = array(
            'start_num' => $start_num,
            'current_page' => $current_page,
            'total_page' => $total_page,
        );
        return $data;

    }

    /**
     * Notes: 二维数组去重
     * User: Sen
     * Date: 2019-7-29 0029
     * Time: 19:24
     * Return:
     * @param $arr
     * @param $key
     * @return mixed
     */
    private function uniquArr($array)
    {
        $result = array();
        foreach ($array as $k => $val) {
            $code = false;
            foreach ($result as $_val) {
                if ($_val['unionid'] == $val['unionid']) {
                    $code = true;
                    break;
                }
            }
            if (!$code) {
                $result[] = $val;
            }
        }
        return $result;
    }

}