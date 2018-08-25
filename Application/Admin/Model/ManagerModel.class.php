<?php

namespace Admin\Model;
use Think\Model;

/**
 * 管理员模型
 */

class ManagerModel extends Model {

        /* 用户模型自动验证 */
    protected $_validate = array(
        /* 验证用户名 */
        array('mname', '4,20', -1, self::EXISTS_VALIDATE, 'length'), //用户名长度不合法
        array('mname', '', -2, self::EXISTS_VALIDATE, 'unique'), //用户名被占用
        array('mname', '_checkMember', -3, self::EXISTS_VALIDATE, 'callback'), //管理员名不能再会员表中存在

        /* 验证密码 */
        array('password', '6,16', '-10', self::EXISTS_VALIDATE, 'length'), //密码长度不合法
        array('repassword','password','-11',0,'confirm'), // 验证确认密码是否和密码一致
        array('password','_checkPwd','-12',0,'callback'), // 自定义函数验证密码格式

        /* 验证邮箱 */
        array('email', 'email', -20, self::EXISTS_VALIDATE), //邮箱格式不正确
        
    );

    /* 用户模型自动完成 */
    protected $_auto = array(
        array('password', 'admin_md5', 1, 'function'),
        array('add_time', NOW_TIME, self::MODEL_INSERT),
        array('add_ip', 'get_client_ip', self::MODEL_INSERT, 'function', 1),
        array('update_time', NOW_TIME,2),
        array('update_ip', 'get_client_ip',2,'function', 1),
    );
    
    public function _checkMember($mname){
    	$res = M('Member')->where(array('usernumber'=>$mname))->getField('uid');
    	if($res){
    		return false;
    	}
    	return true;
    }

    /*密码格式检测*/
    public function _checkPwd($pwd){
        //如果用户名中有空格，不允许注册
        preg_match("/^[0-9]*$/", $pwd, $result);
        if ($result) {
            return false;
        }
        return true;
    }
    /**
     * 登录指定用户
     * @param  integer $uid 用户ID
     * @return boolean      ture-登录成功，false-登录失败
     */
    public function login($username, $password){
        /* 检测是否在当前应用注册 */
        $user = $this->where(array('mname'=>$username))->find();
        
        if(!$user || 1 != $user['status']) {
           
            return -100;
        }

        $password_md5 = admin_md5($password);
        if($password_md5!=$user['password']){
           return -101;
        }
        //记录行为
        action_log('admin_login', 'Manager', $user['mid'], $user['mid']);

        /* 登录用户 */
        $this->autoLogin($user);
        return true;
    }

    /**
     * 注销当前用户
     * @return void
     */
    public function logout(){
        session('admin_auth', null);
        session('admin_auth_sign', null);
    }

    /**
     * 自动登录用户
     * @param  integer $user 用户信息数组
     */
    private function autoLogin($user){
        /* 更新登录信息 */
        $data = array(
            'mid'             => $user['mid'],
            'login_count'           => array('exp', '`login_count`+1'),
            'last_login_time' => NOW_TIME,
            'last_login_ip'   => get_client_ip(1),
        );
        
       
        $this->save($data);
        /* 记录登录SESSION和COOKIES */
        $auth = array(
            'mid'             => $user['mid'],
            'mname'        => $user['mname'],
            'last_login_time' => $user['last_login_time'],
        );

        session('admin_auth', $auth);
        session('admin_auth_sign', data_auth_sign($auth));

    }

   /**
    * 管理员注册
    * @param unknown $data
    * @return Ambigous <\Think\mixed, boolean, unknown, string>|string
    */
   public function register($data){
      $data = $this->create($data);
       if($data){
          return  $this->add($data);
       }else{
          return  $this->getError();
       }
   }
   /**
    * 更新字段
    * @param unknown $field
    * @param unknown $mid
    * @return Ambigous <boolean, unknown>|string
    */
   public function updateField($field){
       
       $field = $this->create($field);
       if($field){
           if(isset($field['password'])){
               $field['password'] = admin_md5($field['password']);
           }
           return $this->setField($field);
       }else{
           return $this->getError();
       }       
   }
   
   public function checkPwd($pwd,$mid){
       $dbpwd = $this->where(array('mid'=>$mid))->getField('password');
       if(admin_md5($pwd)=== $dbpwd){
           return true;
       }else{
           return false;
       }
   }

}
