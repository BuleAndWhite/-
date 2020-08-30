<?php


namespace Api\Controller;


class FileUploadController extends AppController
{
    //设定属性：保存允许上传的MIME类型
    private static $types = array(
        'image/png',
        'image/jpg',
        'image/jpeg',
        'application/msword',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'text/html',
        "text/rtf",
        "application/pdf",
        "video/mp4"
    );

    //修改允许上传类型
    public static function setTypes($types = array(), $type = array())
    {
        //判定是否为空
        if (!empty($types)) self::$types = $types;
    }

    public static $error;    //记录单文件上传过程中出现的错误信息
    public static $errors;   //记录多文件上传过程中出现的错误信息
    public static $files;    //记录多文件上传成功后文件名对应信息

    /**
     * @desc 文档单文件上传
     * @param string $file ,上传文件信息数组
     * @param string $path ,上传路径
     * @param int $max = 2M,最大上传大小
     * @return bool|string,成功返回文件名，失败返回false
     */
    public static function uploadFileOne($file, $path, $max = 2000000, $num = 0)
    {
        //判定文件有效性
        if (!isset($file['error']) || count($file) != 5) {
            self::$error = '错误的上传文件！';
            return false;
        }
        //路径判定
        if (!is_dir($path)) {
            self::$error = '存储路径不存在！';
            return false;
        }
        //判定文件是否正确上传
        switch ($file['error']) {
            case 1:
            case 2:
                self::$error = '文件超过服务器允许大小！';
                return false;
            case 3:
                self::$error = '文件只有部分被上传！';
                return false;
            case 4:
                self::$error = '没有选中要上传的文件！';
                return false;
            case 6:
            case 7:
                self::$error = '服务器错误！';
                return false;
        }
        //判定文件类型
        if (!in_array($file['type'], self::$types)) {
            self::$error = '当前上传的文件类型不允许！';
            return false;
        }
        //判定业务大小
        if ($file['size'] > $max) {
            self::$error = '当前上传的文件超过允许的大小！当前允许的大小是：' . (string)($max / 1000000) . 'M';
            return false;
        }
        //获取随机名字
        $filename = self::getRandomName($file['name'], $num);
        //移动上传的临时文件到指定目录
        $path = $path . date('Ymd', time()) . "/";
        if (!file_exists($path)) {
            //检查是否有该文件夹，如果没有就创建，并给予最高权限
            mkdir($path, 0777, true);
            chmod($path, 0777);
        }
        if (move_uploaded_file($file['tmp_name'], $path . '/' . $filename)) {
            //成功
            $data["size"] = $file['size'];
            $data["type"] = $file["type"];
            $data["filename"] = $filename;
            $data["path"] = substr($path, 1) . $filename;
            $data["url_path"] = "https://sns.kuaifengpay.com" . substr($path, 1) . $filename;
            return $data;

        } else {
            //失败
            self::$error = '文件移动失败！';
            return false;
        }
    }

    /**
     * @desc 文件多文件上传
     * @param array $files ,上传文件信息二维数组
     * @param string $path ,上传路径
     * @param int $max = 2M,最大上传大小
     * @return bool 是否全部上传成功
     */
    public static function uploadFileAll($files, $path, $max = 2000000)
    {
        $data = array();
        for ($i = 0, $len = count($files['name']); $i < $len; $i++) {
            $file = array(
                'name' => $files['name'][$i],
                'type' => $files['type'][$i],
                'tmp_name' => $files['tmp_name'][$i],
                'error' => $files['error'][$i],
                'size' => $files['size'][$i]
            );
            $res = self::uploadFileOne($file, $path, $max, $i);
            if (!$res) {
                //错误处理
                $error = self::$error;
                self::$errors[] = "文件：{$file['name']}上传失败:{$error}!<br>";
            } else {
                $data[$i] = $res;
            }
        }
        if (!empty(self::$errors)) {
            //错误处理
            return false;
        } else {
            return $data;
        }
    }

    /**
     * @desc 获取随机文件名
     * @param string $filename ,文件原名
     * @param string $prefix ,前缀
     * @return string,返回新文件名
     */
    public static function getRandomName($filename, $num = 0)
    {
        //取出源文件后缀
        $ext = strrchr($filename, '.');
        $time = time();
        $re = new ResourcesController();
        $rand = $re->createNoncestr();
        $new_name = date('Y-m-d') . $time . $rand;
        return $new_name . $ext;
    }
}