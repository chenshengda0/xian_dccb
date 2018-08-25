<?php

namespace Home\Model;

use Think\Model;
use User\Api\UserApi;


/**
 * 文档基础模型
 */
class MemberModel extends Model
{
    /* 用户模型自动完成 */
    protected $_auto = array(
        array('login', 0, self::MODEL_INSERT),
        array('reg_ip', 'get_client_ip', self::MODEL_INSERT, 'function', 1),
        array('reg_time', 'time', self::MODEL_INSERT,'function'),
        array('update_time', 'time',self::MODEL_UPDATE,'function'),
    );

    protected $_validate = array(
    	array('usernumber', 'checkDenyMember', -2, self::EXISTS_VALIDATE, 'callback'), //用户名禁止注册
//    	array('IDcard', '15,18', -21, self::EXISTS_VALIDATE, 'length'), //身份证号长度不合法
    	array('IDcard',  'isCreditNo', -22, self::EXISTS_VALIDATE, 'callback'), //身份证号格式不正确
     	//array('IDcard', '', -23, self::EXISTS_VALIDATE, 'unique'), //身份证号被占用 
    	//array('IDcard',  'checkCreditNo', -23, self::EXISTS_VALIDATE, 'callback'), //身份证号禁止注册
    );

    /**
     * 检测用户名是不是被禁止注册
     * @param  string $usernumber 会员编号
     * @return boolean          ture - 未禁用，false - 禁止注册
     */
    protected function checkDenyMember($usernumber)
    {
    	$denyname = C('USER_NAME_BAOLIU');
    	$denyname = str2arr($denyname);
    	if(in_array($usernumber, $denyname)){
    		return false;
    	}
    }
    
    protected function checkCreditNo($vStr)
    {
    	$map['IDcard'] = $vStr;
    	$count = $this->where($map)->count();
    	$max_id = C('MAX_IDCARD')?C('MAX_IDCARD'):4;
    	if($count>= $max_id){
    		return false;
    	}
    }

    
    /**
     * 验证身份证号
     * @param $vStr
     * @return bool
     */
    protected function isCreditNo($vStr){
    	if(APP_DEBUG){
    		return true;
    	}
    	$vCity = array(
    			'11','12','13','14','15','21','22',
    			'23','31','32','33','34','35','36',
    			'37','41','42','43','44','45','46',
    			'50','51','52','53','54','61','62',
    			'63','64','65','71','81','82','91'
    	);
    
    	if (!preg_match('/^([\d]{17}[xX\d]|[\d]{15})$/', $vStr)) return false;
    
    	if (!in_array(substr($vStr, 0, 2), $vCity)) return false;
    
    	$vStr = preg_replace('/[xX]$/i', 'a', $vStr);
    	$vLength = strlen($vStr);
    
    	if ($vLength == 18){
    		$vBirthday = substr($vStr, 6, 4) . '-' . substr($vStr, 10, 2) . '-' . substr($vStr, 12, 2);
    	} else {
    		$vBirthday = '19' . substr($vStr, 6, 2) . '-' . substr($vStr, 8, 2) . '-' . substr($vStr, 10, 2);
    	}
    
    	if (date('Y-m-d', strtotime($vBirthday)) != $vBirthday) return false;
    	if ($vLength == 18){
    		$vSum = 0;
    		for ($i = 17 ; $i >= 0 ; $i--){
    			$vSubStr = substr($vStr, 17 - $i, 1);
    			$vSum += (pow(2, $i) % 11) * (($vSubStr == 'a') ? 10 : intval($vSubStr , 11));
    		}
    		if($vSum % 11 != 1) return false;
    	}
    }
    
    protected function checkNickname($nickname){
        //如果用户名中有空格，不允许注册
        if (strpos($nickname, ' ') !== false) {
            return false;
        }
        preg_match('/^(?!_|\s\')[A-Za-z0-9_\x80-\xff\s\']+$/', $nickname, $result);

        if (!$result) {
            return false;
        }
        return true;
    }

    public function registerMember($data){
   		
        /* 在当前应用中注册用户 */
    	$user = $this->create($data);
    	
        if($user){
            
            $uid=$this->add($user);
            if (!$uid) {
                $this->error = '前台用户信息注册失败，请重试！';
                return false;
            }
        }else{
            
            return $this->getError(); //错误详情见自动验证注释
        }
        return $uid;
    }
    
    
    public function updateMember($uid, $userdata){
    	 
    	
    	$map['uid'] = $uid ;
    	$result = $this->where($map)->save($userdata);    	
     
     
    		if (!$result) {
    			$this->error = '前台用户信息注册失败，请重试！';
    			return false;
    		}else{
	    		return true;
	    	}
    }

    /**
     * 登录指定用户
     * @param  integer $uid 用户ID
     * @return boolean      ture-登录成功，false-登录失败
     */
 public function login($uid, $remember = false)
    {
        /* 检测是否在当前应用注册 */
        $user = $this->field(true)->find($uid);
        if (!$user) { //未注册
            /* 在当前应用中注册用户 */
            $Api = new UserApi();
            $info = $Api->info($uid);
            $user = $this->create(array('nickname' => $info[1], 'status' => 1));
            $user['uid'] = $uid;
            if (!$this->add($user)) {
                $this->error = '前台用户信息注册失败，请重试！';
                return false;
            }
        } elseif (1 != $user['status']) {
            $this->error = '用户未激活或已禁用！'; //应用级别禁用
            return false;
        }
        /* 登录用户 */
        $this->autoLogin($user, $remember);
        //记录行为
        action_log('user_login', 'member', $uid, $uid);
        return true;
    }

    /**
     * 注销当前用户
     * @return void
     */
    public function logout()
    {
    	session('ifpsd', null);
        session('user_auth', null);
        session('user_auth_sign', null);
        cookie('OX_LOGGED_USER', NULL);
    }

    /**
     * 自动登录用户
     * @param  integer $user 用户信息数组
     */
    private function autoLogin($user, $remember = false)
    {
        /* 更新登录信息 */
        $data = array(
            'uid' => $user['uid'],
            'login' => array('exp', '`login`+1'),
            'last_login_time' => NOW_TIME,
            'last_login_ip' => get_client_ip(1),
        );
        $this->save($data);

        /* 记录登录SESSION和COOKIES */
        $auth = array(
            'uid' => $user['uid'],
            'username' => get_username($user['uid']),
            'last_login_time' => $user['last_login_time'],
        );

        session('user_auth', $auth);
        session('user_auth_sign', data_auth_sign($auth));

        if ($remember) {
            $token = build_auth_key();
            $user1 = D('user_token')->where('uid=' . $user['uid'])->find();
            $data['token'] = $token;
            $data['time'] = time();;
            if ($user1 == null) {
                $data['uid'] = $user['uid'];
                D('user_token')->add($data);
            } else {
                D('user_token')->where('uid=' . $user['uid'])->save($data);
            }
        }

        if (!$this->getCookieUid() && $remember) {
            $expire = 3600 * 24 * 7;
            cookie('OX_LOGGED_USER', $this->jiami($this->change() . ".{$user['uid']}.{$token}"), $expire);

        }
    }
    public function need_login()
    {

        if ($uid = $this->getCookieUid()) {
            $this->login($uid);
            return true;
        }
    }

    public function getCookieUid()
    {
        static $cookie_uid = null;
        if (isset($cookie_uid) && $cookie_uid !== null) {
            return $cookie_uid;
        }
        $cookie = cookie('OX_LOGGED_USER');
        $cookie = explode(".", $this->jiemi($cookie));
        $map['uid'] = $cookie[1];
        $user = D('user_token')->where($map)->find();
        $cookie_uid = ($cookie[0] != $this->change()) || ($cookie[2] != $user['token']) ? false : $cookie[1];
        $cookie_uid =  $user['time']-time() >= 3600*24*7 ? false:$cookie_uid;
        return $cookie_uid;
    }


    /**
     * 加密函数
     * @param string $txt 需加密的字符串
     * @param string $key 加密密钥，默认读取SECURE_CODE配置
     * @return string 加密后的字符串
     */
    private function jiami($txt, $key = null)
    {
        empty($key) && $key = $this->change();

        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789-=_";
        $nh = rand(0, 64);
        $ch = $chars[$nh];
        $mdKey = md5($key . $ch);
        $mdKey = substr($mdKey, $nh % 8, $nh % 8 + 7);
        $txt = base64_encode($txt);
        $tmp = '';
        $i = 0;
        $j = 0;
        $k = 0;
        for ($i = 0; $i < strlen($txt); $i++) {
            $k = $k == strlen($mdKey) ? 0 : $k;
            $j = ($nh + strpos($chars, $txt [$i]) + ord($mdKey[$k++])) % 64;
            $tmp .= $chars[$j];
        }
        return $ch . $tmp;
    }

    /**
     * 解密函数
     * @param string $txt 待解密的字符串
     * @param string $key 解密密钥，默认读取SECURE_CODE配置
     * @return string 解密后的字符串
     */
    private function jiemi($txt, $key = null)
    {
        empty($key) && $key = $this->change();

        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789-=_";
        $ch = $txt[0];
        $nh = strpos($chars, $ch);
        $mdKey = md5($key . $ch);
        $mdKey = substr($mdKey, $nh % 8, $nh % 8 + 7);
        $txt = substr($txt, 1);
        $tmp = '';
        $i = 0;
        $j = 0;
        $k = 0;
        for ($i = 0; $i < strlen($txt); $i++) {
            $k = $k == strlen($mdKey) ? 0 : $k;
            $j = strpos($chars, $txt[$i]) - $nh - ord($mdKey[$k++]);
            while ($j < 0) {
                $j += 64;
            }
            $tmp .= $chars[$j];
        }

        return base64_decode($tmp);
    }

    private function change()
    {
        preg_match_all('/\w/', C('DATA_AUTH_KEY'), $sss);
        $str1 = '';
        foreach ($sss[0] as $v) {
            $str1 .= $v;
        }
        return $str1;
    }

    /**
     * 同步登陆时添加用户信息
     * @param $uid
     * @param $info
     * @return mixed
     * autor:xjw129xjt
     */
    public function addSyncData($uid,$info){

        $data1['nickname'] = mb_substr($info['nick'],0,11, 'utf-8');
        //去除特殊字符。
        $data1['nickname'] = preg_replace('/[^A-Za-z0-9_\x80-\xff\s\']/','', $data1['nickname']);
        empty($data1['nickname']) && $data1['nickname']=$this->rand_nickname();
        $data1['nickname'] .='_'.$this->rand_nickname();
        $data1['sex'] = $info['sex'];
        $data =  $this->create($data1);
        $data['uid'] = $uid;
        $res = $this->add($data);
        return $res;
    }

    public function rand_nickname()
    {
        $nickname= $this->create_rand(4);
        if ($this->where(array('nickname' => $nickname))->select()) {
            $this->rand_nickname();
        } else {
            return $nickname;
        }
    }

    function create_rand($length = 8)
    {
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $password = '';
        for ($i = 0; $i < $length; $i++) {
            $password .= $chars[mt_rand(0, strlen($chars) - 1)];
        }
        return $password;
    }


}
