<?php
// +----------------------------------------------------------------------
// | ThinkCMF [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2014 http://www.thinkcmf.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: Dean <zxxjjforever@163.com>
// +----------------------------------------------------------------------
namespace Portal\Controller;
use Common\Controller\HomebaseController; 

class QrcodeController extends HomebaseController {

                public function index()   {  
                  
                    vendor("phpqrcode.phpqrcode");

                    $code=$_REQUEST['code'];
                    $data = $code;
               
                    $level = 'L';
                  
                    $size = 5;
         
            \QRcode::png($data, false, $level, $size);
                
                }
    }
