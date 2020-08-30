<?php

namespace Admin\Controller;

use Common\Controller\AdminbaseController;

class UserController extends AdminbaseController
{
    protected $users_model, $role_model, $user_model;

    function _initialize()
    {
        parent::_initialize();
        $this->users_model = D("Common/Users");
        $this->user_model = D("Common/User");
        $this->role_model = D("Common/Role");
    }

    function index()
    {

//        $c =new \Curl();
//        $user_token = $this->obtainTokens("wxe9a94c6c3382f860", "19be1d0edf5f39dcd564fe995ef84bad");
//
//        $post = '{"action_name": "QR_LIMIT_STR_SCENE", "action_info": {"scene": {"scene_str": "T20160027"}}}';
//        $userList = "https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token=" . $user_token;
//        $user = $c->post($userList, $post);
//        $ajaxJson =json_decode($user, true);
        $user_id = get_current_admin_id();
        $userOne = $this->users_model->where(array("id" => $user_id))->find();
        $count = $this->users_model->alias("u")
            ->join("LEFT JOIN __USER__ u1 on u.wx_id = u1.id")->where(array("u.parent" => $user_id))->count();
        $page = $this->page($count, 20);
        $users = $this->users_model
            ->alias("u")
            ->join("LEFT JOIN __USER__ u1 on u.wx_id = u1.id")
            ->field("u.*,u1.name,u1.is_vip")
            ->where(array("u.parent" => $user_id))
            ->order("u.create_time DESC")
            ->limit($page->firstRow . ',' . $page->listRows)
            ->select();

        $roles_src = $this->role_model->select();
        $roles = array();
        foreach ($roles_src as $r) {
            $roleid = $r['id'];
            $roles["$roleid"] = $r;
        }
        $this->assign("page", $page->show('Admin'));
        $this->assign("roles", $roles);

        $this->assign("users", $users);
        $this->assign("userOneDid", $userOne['did']);
        $this->assign("userOneType", $userOne['user_type']);
        $this->display();
    }

    public function code()
    {

        $c = new \Curl();
        $user_token = $this->obtainTokens("wxe9a94c6c3382f860", "19be1d0edf5f39dcd564fe995ef84bad");

        $post = '{"action_name": "QR_LIMIT_SCENE", "action_info": {"scene": {"scene_id": 123}}}';
        $userList = "https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token=" . $user_token;
        $c->post($userList, $post);
    }

    /**
     * name 刘北林
     * data 2018年5月18日15:12:17
     * @param $appid
     * @param $appsecret
     * @return mixed
     * 获取 access_token 推送token
     */
    public function obtainTokens($appid, $appsecret)
    {
        $c = new \Curl();
        $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=" . $appid . "&secret=" . $appsecret;
        $user = $c->get($url);
        $user_token = json_decode($user, true);
        S("access_token", $user_token['access_token'], 60 * 60 * 2);
        return $user_token['access_token'];
//        $c = new \Curl();
//
//        $url = "https://api.weixin.qq.com/sns/oauth2/refresh_token?appid=" . $appid . "&grant_type=refresh_token&refresh_token=" . S("refresh_token");
//        $user = $c->get($url);
//        $user_token = json_decode($user, true);
//        return $user_token;
    }

    function add()
    {
        $user_id = get_current_admin_id();
        $user_id = $this->users_model->where(array("id" => $user_id))->find();
        $role_id = $_GET['role_id'];
        $id = $_GET['id'];
        if ($role_id) {
            $this->assign("role_id", $role_id);
        } else {
            $this->assign("role_id", 0);
        }
        $this->assign("id", $id);
        $roles = $this->role_model->where("status=1")->order("id desc")->select();
        $roles_src = array();
        foreach ($roles as $r) {
            if ($user_id['user_type'] == 1) {
                $roleid = $r['id'];
                $roles_src["$roleid"] = $r;
            } else if ($user_id['user_type'] == 2) {
                if ($r['id'] == 9) {
                    $roleid = $r['id'];
                    $roles_src["$roleid"] = $r;
                }
            } else if ($user_id['user_type'] == 3 || $user_id['user_type'] == 4 || $user_id['user_type'] == 5) {
                if ($r['id'] == 11 || $r['id'] == 12) {
                    $roleid = $r['id'];
                    $roles_src["$roleid"] = $r;
                }
            }

        }

        $userFon = $this->users_model->where(array("id" => $user_id))->find();
        $this->assign("user_type", $userFon['user_type']);
        $this->assign("roles", $roles_src);
        $this->display();
    }

    function add_post()
    {
        $createNoncestrs = "KF_" . $this->createNoncestrs();
        if (IS_POST) {
            if (!empty($_POST['role_id']) && is_array($_POST['role_id'])) {
                $role_ids = $_POST['role_id'];
                unset($_POST['role_id']);
                if ($this->users_model->create()) {
                    $date = $_POST;
                    foreach ($role_ids as $role_id) {
                        if ($role_id == 1 || $role_id == 2) {
                            $date['user_type'] = 1;
                            $date['user_status'] = 1;
                            break;
                        } else if ($role_id == 9) {
                            $date['did'] = $createNoncestrs;
                            $date['user_type'] = 2;
                            $date['user_status'] = 1;
                        } else if ($role_id == 10) {
                            $date['did'] = $createNoncestrs;
                            $date['user_type'] = 3;
                            $date['user_status'] = 1;
                        } else if ($role_id == 11) {
                            $date['did'] = $createNoncestrs;
                            $date['user_type'] = 4;
                            $date['user_status'] = 1;
                            $this->password(200, $createNoncestrs, $date['user_id']);
                        } else if ($role_id == 12) {
                            $date['did'] = $createNoncestrs;
                            $date['user_type'] = 5;
                            $date['user_status'] = 1;
                            $this->password(1000, $createNoncestrs, $date['user_id']);
                        }
                    }
                    $date['parent'] = get_current_admin_id();

                    $result = $this->users_model->add($date);
                    if ($result !== false) {
                        $date = $_POST;
                        if ($date['user_id']) {
                            $this->user_model->where(array("id" => $date['user_id']))->save(array("is_backstage" => 1, "sn" => $createNoncestrs));
                        }
                        $role_user_model = M("RoleUser");
                        foreach ($role_ids as $role_id) {
                            $role_user_model->add(array("role_id" => $role_id, "user_id" => $result));
                        }

                        $this->success("添加成功！", U("user/index"));
                    } else {
                        $this->error("添加失败！");
                    }
                } else {
                    $this->error($this->users_model->getError());
                }
            } else {
                $this->error("请为此用户指定角色！");
            }

        }
    }


    function edit()
    {
        $id = intval(I("get.id"));
        $roles = $this->role_model->where("status=1")->order("id desc")->select();
        $user_id = get_current_admin_id();
        $user_id = $this->users_model->where(array("id" => $user_id))->find();
        $roles_src = array();
        foreach ($roles as $r) {
            if ($user_id['user_type'] == 1) {
                $roleid = $r['id'];
                $roles_src["$roleid"] = $r;
            } else if ($user_id['user_type'] == 2) {
                if ($r['id'] == 9 || $r['id'] == 10) {
                    $roleid = $r['id'];
                    $roles_src["$roleid"] = $r;
                }
            } else if ($user_id['user_type'] == 3 || $user_id['user_type'] == 4 || $user_id['user_type'] == 5) {
                if ($r['id'] == 11 || $r['id'] == 12) {
                    $roleid = $r['id'];
                    $roles_src["$roleid"] = $r;
                }
            }
            if ($r['id'] == 11) {

            } else if ($r['id'] == 12) {

            }

        }

        $this->assign("roles", $roles_src);
        $role_user_model = M("RoleUser");
        $role_ids = $role_user_model->where(array("user_id" => $id))->getField("role_id", true);
        $this->assign("role_ids", $role_ids);

        $user = $this->users_model->where(array("id" => $id))->find();
        $this->assign($user);
        $this->display();
    }

    function edit_post()
    {
        if (IS_POST) {
            if (!empty($_POST['role_id']) && is_array($_POST['role_id'])) {
                if (empty($_POST['user_pass'])) {
                    unset($_POST['user_pass']);
                }
                $role_ids = $_POST['role_id'];
                $id = $_POST['id'];
                unset($_POST['role_id']);
                if ($this->users_model->create()) {
                    foreach ($role_ids as $role_id) {
                        if ($role_id == 1 || $role_id == 2) {
                            $date['user_type'] = 1;
                            $date['user_status'] = 1;
                            break;
                        } else {
                            $date['user_type'] = 2;
                            $date['user_status'] = 1;
                        }
                    }
                    $date = $_POST;
                    $result = $this->users_model->where(array("id" => $id))->save($date);
                    if ($result !== false) {
                        $uid = intval($_POST['id']);
                        $role_user_model = M("RoleUser");
                        $role_user_model->where(array("user_id" => $uid))->delete();
                        foreach ($role_ids as $role_id) {
                            $role_user_model->add(array("role_id" => $role_id, "user_id" => $uid));
                        }
                        $this->success("保存成功！");
                    } else {
                        $this->error("保存失败！");
                    }
                } else {
                    $this->error($this->users_model->getError());
                }
            } else {
                $this->error("请为此用户指定角色！");
            }

        }
    }

    /**
     *  作用：产生随机字符串，不长于32位
     */
    public
    function createNoncestrs($length = 8)
    {
        $chars = "0123456789abcdefghijklmnopqrstuvwxyz";
        $str = "";
        for ($i = 0; $i < $length; $i++) {
            $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
        }

        return $str;
    }

    /**
     *  删除
     */
    function delete()
    {
        $id = intval(I("get.id"));
        if ($id == 1) {
            $this->error("最高管理员不能删除！");
        }

        if ($this->users_model->where("id=$id")->delete() !== false) {
            M("RoleUser")->where(array("user_id" => $id))->delete();
            $this->success("删除成功！");
        } else {
            $this->error("删除失败！");
        }
    }


    function userinfo()
    {
        $id = get_current_admin_id();
        $user = $this->users_model->where(array("id" => $id))->find();
        $this->assign($user);
        $this->display();
    }

    function userinfo_post()
    {
        if (IS_POST) {
            $_POST['id'] = get_current_admin_id();
            $create_result = $this->users_model
                ->field("user_login,user_email,last_login_ip,last_login_time,create_time,user_activation_key,user_status,role_id,score,user_type", true)//排除相关字段
                ->create();
            if ($create_result) {
                if ($this->users_model->save() !== false) {
                    $this->success("保存成功！");
                } else {
                    $this->error("保存失败！");
                }
            } else {
                $this->error($this->users_model->getError());
            }
        }
    }

    function ban()
    {
        $id = intval($_GET['id']);
        if ($id) {
            $rst = $this->users_model->where(array("id" => $id, "user_type" => 1))->setField('user_status', '0');
            if ($rst) {
                $this->success("管理员停用成功！", U("user/index"));
            } else {
                $this->error('管理员停用失败！');
            }
        } else {
            $this->error('数据传入失败！');
        }
    }

    function cancelban()
    {
        $id = intval($_GET['id']);
        if ($id) {
            $rst = $this->users_model->where(array("id" => $id, "user_type" => 1))->setField('user_status', '1');
            if ($rst) {
                $this->success("管理员启用成功！", U("user/index"));
            } else {
                $this->error('管理员启用失败！');
            }
        } else {
            $this->error('数据传入失败！');
        }
    }

    /**
     * 推广码
     * 刘北林
     * 2018年9月5日12:09:20
     */
    public function owner()
    {
        vendor("phpqrcode.phpqrcode");
        $did = I("get.did");
        $user_type = I("get.user_type");
        $hello = explode(',', $did);

        $newarray = M("users")->where(array("did" => $hello[0]))->select();

        if (!$newarray[0]['q_r_code']) {

            if (count($hello)) {
                foreach ($hello as $key => $v) {

                    $uL = M("users")->where(array("did" => $hello[$key]))->find();
                    if ($uL['user_type'] == 2) {
                        $owner_id = $hello[$key];
                        $operation = $hello[$key];
                    } elseif ($uL['user_type'] == 3) {
                        $userOne = M("user")->where(array("sn" => $hello[$key]))->find();
                        $owner_id = $hello[$key];
                        $operation = $userOne['operation'];
                    } else {
                        $userOne = M("user")->where(array("sn" => $hello[$key]))->find();
                        $owner_id = $userOne['parent_did'];
                        $operation = $userOne['operation'];
                    }
                    $level = 1;
                    $size = 6;
                    $errorCorrectionLevel = intval($level);//容错级别
                    $matrixPointSize = intval($size);//生成图片大小
                    $qrurl = "https://bolon.kuaifengpay.com/Api/Index/authorized?scene_str=" . $hello[$key] . "&user_type=" . $uL['user_type'] . "&owner_id=" . $owner_id . "&operation=" . $operation;
                    //保存位置
                    $path = "public/kuaifeng/";
                    // 生成的文件名
                    $fileName = $path . date('Y-m-d', time()) . "-" . $this->createNoncestrs() . '.png';

                    //生成二维码图片
                    $object = new \QRcode();
                    $object->png($qrurl, $fileName, $errorCorrectionLevel, $matrixPointSize, 2);
                    ob_clean();
                    $QR = $fileName;//已经生成的原始二维码图
                    $logo = "public/images/code.png";//准备好的logo图片
                    if (file_exists($logo)) {
                        $QR = imagecreatefromstring(file_get_contents($QR));
                        $logo = imagecreatefromstring(file_get_contents($logo));
                        $QR_width = imagesx($QR);//二维码图片宽度
                        $QR_height = imagesy($QR);//二维码图片高度
                        $logo_width = imagesx($logo);//logo图片宽度
                        $logo_height = imagesy($logo);//logo图片高度
                        $logo_qr_width = $QR_width / 5;
                        $scale = $logo_width / $logo_qr_width;
                        $logo_qr_height = $logo_height / $scale;
                        $from_width = ($QR_width - $logo_qr_width) / 2;
                        //重新组合图片并调整大小
                        imagecopyresampled($QR, $logo, $from_width, $from_width, 0, 0, $logo_qr_width,
                            $logo_qr_height, $logo_width, $logo_height);
                        //输出图片
                        header("Content-type: image/png");
                        // dump($qrcode_path);
                        $qrcode_path = $path . date('Y-m-d', time()) . "-" . $this->createNoncestrs() . '.png';
                        M("users")->where(array("did" => $hello[$key]))->save(array("q_r_code" => "https://bolon.kuaifengpay.com/" .$qrcode_path));
                        imagepng($QR, $qrcode_path);
                        $newarray[$key]['q_r_code'] = "https://bolon.kuaifengpay.com/" .$qrcode_path;
                        $newarray[$key]['did'] = $hello[$key];
                    } else {

                    }


//
//                    //$qrurl=$ajaxJson["url"];
//                    $level = 'L';// 纠错级别：L、M、Q、H
//                    $size = 4;// 点的大小：1到10,用于手机端4就可以了
//                    $QRcode = new \QRcode();
//                    ob_start();
//                    $QRcode->png($qrurl, false, $level, $size, 2);
//                    $imageString = base64_encode(ob_get_contents());
//                    ob_end_clean();
//                    $imageString = "data:image/jpg;base64," . $imageString;
//                    M("users")->where(array("did" => $hello[$key]))->save(array("q_r_code" => $imageString));
//                    $newarray[$key]['q_r_code'] = $imageString;
//                    $newarray[$key]['did'] = $hello[$key];

                }
            }
        }

        $this->assign("didList", $newarray);

        $this->display();
    }

    /**
     * 口令码
     * 刘北林
     * 2018年9月5日12:09:20
     */
    public function passwords()
    {
        vendor("phpqrcode.phpqrcode");
        $did = I("get.did");
        $user_type = I("get.user_type");
        $hello = explode(',', $did);
        $newarray = M("users")->where(array("did" => $hello[0]))->select();

        if (!$newarray[0]['password_code']) {

            foreach ($hello as $key => $v) {

                $uL = M("users")->where(array("did" => $hello[$key]))->find();
                if ($uL['user_type'] == 2 || $uL['user_type'] == 3) {
                    $owner_id = $hello[$key];
                    $operation = $hello[$key];
                } else {
                    $userOne = M("user")->where(array("sn" => $hello[$key]))->find();
                    $owner_id = $userOne['parent_did'];
                    $operation = $userOne['operation'];
                }
                $level = 1;
                $size = 6;
                $errorCorrectionLevel = intval($level);//容错级别
                $matrixPointSize = intval($size);//生成图片大小
                $qrurl = "https://bolon.kuaifengpay.com/Api/Index/authorizeds?scene_str=" . $hello[$key] . "&user_type=" . $uL['user_type'] . "&owner_id=" . $owner_id . "&operation=" . $operation;
//                $qrurl = "https://bolon.kuaifengpay.com/Api/IndexGene/authorizeds?scene_str=" . $hello[$key] . "&user_type=" . $uL['user_type'] . "&owner_id=" . $owner_id . "&operation=" . $operation;


                //保存位置
                $path = "public/kuaifeng/";
                // 生成的文件名
                $fileName = $path . date('Y-m-d', time()) . "-" . $this->createNoncestrs() . '.png';

                //生成二维码图片
                $object = new \QRcode();
                $object->png($qrurl, $fileName, $errorCorrectionLevel, $matrixPointSize, 2);
                ob_clean();
                $QR = $fileName;//已经生成的原始二维码图
                $logo = "public/images/code.png";//准备好的logo图片
//                $logo = "public/images/em12.jpg";//准备好的logo图片
                if (file_exists($logo)) {
                    $QR = imagecreatefromstring(file_get_contents($QR));
                    $logo = imagecreatefromstring(file_get_contents($logo));
                    $QR_width = imagesx($QR);//二维码图片宽度
                    $QR_height = imagesy($QR);//二维码图片高度
                    $logo_width = imagesx($logo);//logo图片宽度
                    $logo_height = imagesy($logo);//logo图片高度
                    $logo_qr_width = $QR_width / 5;
                    $scale = $logo_width / $logo_qr_width;
                    $logo_qr_height = $logo_height / $scale;
                    $from_width = ($QR_width - $logo_qr_width) / 2;
                    //重新组合图片并调整大小
                    imagecopyresampled($QR, $logo, $from_width, $from_width, 0, 0, $logo_qr_width,
                        $logo_qr_height, $logo_width, $logo_height);
                    //输出图片
                    header("Content-type: image/png");
                    // dump($qrcode_path);
                    $qrcode_path = $path . date('Y-m-d', time()) . "-" . $this->createNoncestrs() . '.png';
                    M("users")->where(array("did" => $hello[$key]))->save(array("password_code" => "https://bolon.kuaifengpay.com/" . $qrcode_path));
                    imagepng($QR, $qrcode_path);
                    $newarray[$key]['password_code'] = "https://bolon.kuaifengpay.com/" . $qrcode_path;
                    $newarray[$key]['did'] = $hello[$key];

//
//
//                //$qrurl=$ajaxJson["url"];
//                $level = 'L';// 纠错级别：L、M、Q、H
//                $size = 4;// 点的大小：1到10,用于手机端4就可以了
//                $QRcode = new \QRcode();
//                ob_start();
//                $QRcode->png($qrurl, false, $level, $size, 2);
//                $imageString = base64_encode(ob_get_contents());
//                ob_end_clean();
//                $imageString = "data:image/jpg;base64," . $imageString;
//                M("users")->where(array("did" => $hello[$key]))->save(array("password_code"=>$imageString));
//                $newarray[$key]['password_code'] = $imageString;
//                $newarray[$key]['did'] = $hello[$key];
                    $newarray[$key]['count'] = $userOne = M("password")->where(array("did" => $hello[$key], "is_receive" => 1))->count();
                }
            }
        } else {
            foreach ($newarray as $key => $v) {
                $newarray[$key]['count'] = $userOne = M("password")->where(array("did" => $hello[0], "is_receive" => 1))->count();

            }
        }


        $this->assign("didList", $newarray);

        $this->display();
    }

    /**
     * 刘北林
     * 2018年6月29日18:08:17
     * 生成口令码
     */
    public function password($count, $createNoncestrs, $user_id)
    {

        if ($user_id && $count) {
            for ($i = 0; $i < $count; $i++) {
                M("password")->add(array("did" => $createNoncestrs, "password" => $this->createNoncestr(), "uid" => $user_id, "time" => date("Y-m-d H:i:s", time())));
            }
        }
        return true;

    }

    /**
     *  作用：产生随机字符串，不长于32位
     */
    public
    function createNoncestr($length = 8)
    {
        $chars = "0123456789";
        $str = "";
        for ($i = 0; $i < $length; $i++) {
            $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
        }

        return $str;
    }
}