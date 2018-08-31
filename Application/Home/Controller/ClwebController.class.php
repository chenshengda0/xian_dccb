<?php


namespace Home\Controller;
use Common\Controller\CommonController;
require_once APP_PATH . 'User/Conf/config.php';
require_once(APP_PATH . 'User/Common/common.php');
/**
 * 用户控制器
 */
class ClwebController extends CommonController{
	
    /* 用户中心首页 */
    public function index(){
    	return $this->display();
  }

}