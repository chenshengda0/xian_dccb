<?php

// OneThink常量定义
use Admin\Model\AuthRuleModel;
const ONETHINK_VERSION = '1.0.131218';
const ONETHINK_ADDON_PATH = './Addons/';

/**
 * 系统公共库文件
 * 主要定义系统公共函数库
 */

function getmonth($date,$format=FALSE)
{
	$firstday = date('Y-m-01', $date);
	$lastday = date('Y-m-t', $date);
	if($format){
		$firstday = strtotime($firstday);
		$lastday = strtotime($lastday.'23:59:59');
	}
	return array($firstday, $lastday);
}

/**
 * 查询用户总业绩
 */
function get_totalbill($uid){
	
	$info=M('member')->where(array('uid'=>$uid))->find();
	$left_bill=$info['left_bill_all'];
	$right_bill=$info['right_bill_all'];
	$totalbill=$left_bill+$right_bill;
	//$minbill=min($values);
	return $totalbill;
}
/**
 * 查询用户小区业绩
 */
function exportExcel($expTitle,$expCellName,$expTableData){
	$xlsTitle = iconv('utf-8', 'gb2312', $expTitle);//文件名称
	$fileName = $expTitle.date('YmdHis');//or $xlsTitle 文件名称可根据自己情况设定
	$cellNum = count($expCellName);
	$dataNum = count($expTableData);
	vendor("PHPExcel.PHPExcel");

	$objPHPExcel = new \PHPExcel();

	/*设置默认样式*/
	$objPHPExcel->getDefaultStyle()->getFont()->setSize(13);//设置表格默认样式
	$objPHPExcel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);//设置表格默认样式
	$objPHPExcel->getActiveSheet()->getDefaultStyle()->getAlignment()->setWrapText(true);//换行
	$objPHPExcel->getActiveSheet()->getDefaultRowDimension()->setRowHeight(20);//设置行高
	$objPHPExcel->getActiveSheet()->getDefaultColumnDimension()->setWidth(15);//设置单元格宽

	$cellName = range('A','Z');

	$objPHPExcel->getActiveSheet(0)->mergeCells('A1:'.$cellName[$cellNum-1].'1');//合并单元格
	$objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setSize(16)->setBold(true);
	$objPHPExcel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue('A1', $expTitle);

	$objPHPExcel->getActiveSheet()->getStyle($cellName[0].'2:'.$cellName[$cellNum-1].'2')->getFont()->setBold(true);
	for($i=0;$i<$cellNum;$i++){
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue($cellName[$i].'2', $expCellName[$i][1]);
	}
	// Miscellaneous glyphs, UTF-8
	for($i=0;$i<$dataNum;$i++){
		$objPHPExcel->getActiveSheet()->getRowDimension($i+3)->setRowHeight(30);
		for($j=0;$j<$cellNum;$j++){
			$objPHPExcel->getActiveSheet(0)->setCellValueExplicit($cellName[$j].($i+3), $expTableData[$i][$expCellName[$j][0]]);
		}
	}

	header('pragma:public');
	header('Content-type:applicationnd.ms-excel;charset=utf-8;name="'.$xlsTitle.'.xls"');
	header("Content-Disposition:attachment;filename=$fileName.xls");//attachment新窗口打印inline本窗口打印
	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
	$objWriter->save('php://output');
	exit;
}
function get_minbill($uid){

	$info=M('member')->where(array('uid'=>$uid))->find();
	$left_bill=$info['left_bill_all'];
	$right_bill=$info['right_bill_all'];
	$minbill=intval(min($left_bill,$right_bill));
	return $minbill;
}
/**
 * 检测用户是否登录
 * @return integer 0-未登录，大于0-当前登录用户ID
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
function is_login()
{
    $user = session('user_auth');
    if (empty($user)) {
        return 0;
    } else {
        return session('user_auth_sign') == data_auth_sign($user) ? $user['uid'] : 0;
    }
}


/**
 * 检测权限
 */
function CheckPermission($uids)
{
    if (is_administrator()) {
        return true;
    }
    if (in_array(is_login(), $uids)) {
        return true;
    }
    return false;
}

function check_auth($rule, $type = AuthRuleModel::RULE_URL )
{
    if (is_administrator()) {
        return true;//管理员允许访问任何页面
    }
    static $Auth = null;
    if (!$Auth) {
        $Auth = new \Think\Auth();
    }
    if (!$Auth->check($rule, is_login(), $type)) {
        return false;
    }
    return true;

}

/**
 * 字符串转换为数组，主要用于把分隔符调整到第二个参数
 * @param  string $str 要分割的字符串
 * @param  string $glue 分割符
 * @return array
 */
function str2arr($str,$glue=',')
{
	return array_values(array_filter(explode($glue, $str)));
}

/**
 * 数组转换为字符串，主要用于把分隔符调整到第二个参数
 * @param  array  $arr 要连接的数组
 * @param  string $glue 分割符
 * @return string
 */
function arr2str($arr, $glue = ',')
{
    return implode($glue, $arr);
}
/**
 *  二维数组验证一个值是否存在
 * @param unknown $value
 * @param unknown $array
 * @return boolean
 */
function deep_in_array($value, $array) {
	foreach($array as $item) {
		if(!is_array($item)) {
			if ($item == $value) {
				return true;
			} else {
				continue;
			}
		}
		 
		if(in_array($value, $item)) {
			return true;
		} else if(deep_in_array($value, $item)) {
			return true;
		}
	}
	return false;
}

/**
 * 判断当前月份有几天
 */
/*function get_month_day(){
	$year = date('Y');
  $month=date("m");
	if (in_array($month, array(1, 3, 5, 7, 8, 01, 03, 05, 07, 08, 10, 12))) {
		$text = '31';
	}elseif ($month == 2){
		if ($year % 400 == 0 || ($year % 4 == 0 && $year % 100 !== 0)) {        //判断是否是闰年
			$text = '29';
		} else {
			$text = '28';
		}
	} else {
		$text = '30';
	}
	
	return $text;
}*/
/**
 * 字符串截取，支持中文和其他编码
 * @static
 * @access public
 * @param string $str 需要转换的字符串
 * @param string $start 开始位置
 * @param string $length 截取长度
 * @param string $charset 编码格式
 * @param string $suffix 截断显示字符
 * @return string
 */
function msubstr($str, $start = 0, $length, $charset = "utf-8", $suffix = true)
{
    if (function_exists("mb_substr"))
        $slice = mb_substr($str, $start, $length, $charset);
    elseif (function_exists('iconv_substr')) {
        $slice = iconv_substr($str, $start, $length, $charset);
        if (false === $slice) {
            $slice = '';
        }
    } else {
        $re['utf-8'] = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|[\xe0-\xef][\x80-\xbf]{2}|[\xf0-\xff][\x80-\xbf]{3}/";
        $re['gb2312'] = "/[\x01-\x7f]|[\xb0-\xf7][\xa0-\xfe]/";
        $re['gbk'] = "/[\x01-\x7f]|[\x81-\xfe][\x40-\xfe]/";
        $re['big5'] = "/[\x01-\x7f]|[\x81-\xfe]([\x40-\x7e]|\xa1-\xfe])/";
        preg_match_all($re[$charset], $str, $match);
        $slice = join("", array_slice($match[0], $start, $length));
    }
    return $suffix ? $slice . '...' : $slice;
}

/**
 * 系统加密方法
 * @param string $data 要加密的字符串
 * @param string $key 加密密钥
 * @param int    $expire 过期时间 单位 秒
 * @return string
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
function think_encrypt($data, $key = '', $expire = 0)
{
    $key = md5(empty($key) ? C('DATA_AUTH_KEY') : $key);
    $data = base64_encode($data);
    $x = 0;
    $len = strlen($data);
    $l = strlen($key);
    $char = '';

    for ($i = 0; $i < $len; $i++) {
        if ($x == $l) $x = 0;
        $char .= substr($key, $x, 1);
        $x++;
    }

    $str = sprintf('%010d', $expire ? $expire + time() : 0);

    for ($i = 0; $i < $len; $i++) {
        $str .= chr(ord(substr($data, $i, 1)) + (ord(substr($char, $i, 1))) % 256);
    }
    return str_replace(array('+', '/', '='), array('-', '_', ''), base64_encode($str));
}

/**
 * 系统解密方法
 * @param  string $data 要解密的字符串 （必须是think_encrypt方法加密的字符串）
 * @param  string $key 加密密钥
 * @return string
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
function think_decrypt($data, $key = '')
{
    $key = md5(empty($key) ? C('DATA_AUTH_KEY') : $key);
    $data = str_replace(array('-', '_'), array('+', '/'), $data);
    $mod4 = strlen($data) % 4;
    if ($mod4) {
        $data .= substr('====', $mod4);
    }
    $data = base64_decode($data);
    $expire = substr($data, 0, 10);
    $data = substr($data, 10);

    if ($expire > 0 && $expire < time()) {
        return '';
    }
    $x = 0;
    $len = strlen($data);
    $l = strlen($key);
    $char = $str = '';

    for ($i = 0; $i < $len; $i++) {
        if ($x == $l) $x = 0;
        $char .= substr($key, $x, 1);
        $x++;
    }

    for ($i = 0; $i < $len; $i++) {
        if (ord(substr($data, $i, 1)) < ord(substr($char, $i, 1))) {
            $str .= chr((ord(substr($data, $i, 1)) + 256) - ord(substr($char, $i, 1)));
        } else {
            $str .= chr(ord(substr($data, $i, 1)) - ord(substr($char, $i, 1)));
        }
    }
    return base64_decode($str);
}

/**
 * 数据签名认证
 * @param  array $data 被认证的数据
 * @return string       签名
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
function data_auth_sign($data)
{
    //数据类型检测
    if (!is_array($data)) {
        $data = (array)$data;
    }
    ksort($data); //排序
    $code = http_build_query($data); //url编码并生成query字符串
    $sign = sha1($code); //生成签名
    return $sign;
}

/**
 * 对查询结果集进行排序
 * @access public
 * @param array  $list 查询结果
 * @param string $field 排序的字段名
 * @param array  $sortby 排序类型
 * asc正向排序 desc逆向排序 nat自然排序
 * @return array
 */
function list_sort_by($list, $field, $sortby = 'asc')
{
    if (is_array($list)) {
        $refer = $resultSet = array();
        foreach ($list as $i => $data)
            $refer[$i] = &$data[$field];
        switch ($sortby) {
            case 'asc': // 正向排序
                asort($refer);
                break;
            case 'desc': // 逆向排序
                arsort($refer);
                break;
            case 'nat': // 自然排序
                natcasesort($refer);
                break;
        }
        foreach ($refer as $key => $val)
            $resultSet[] = &$list[$key];
        return $resultSet;
    }
    return false;
}

/**
 * 把返回的数据集转换成Tree
 * @param array  $list 要转换的数据集
 * @param string $pid parent标记字段
 * @param string $level level标记字段
 * @return array
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
function list_to_tree($list, $pk = 'id', $pid = 'pid', $child = '_child', $root = 0)
{


    // 创建Tree
    $tree = array();
    if (is_array($list)) {
        // 创建基于主键的数组引用
        $refer = array();
        foreach ($list as $key => $data) {
            $refer[$data[$pk]] =& $list[$key];
        }
        foreach ($list as $key => $data) {
            // 判断是否存在parent
            $parentId = $data[$pid];
            if ($root == $parentId) {
                $tree[] =& $list[$key];
            } else {
                if (isset($refer[$parentId])) {
                    $parent =& $refer[$parentId];
                    $parent[$child][] =& $list[$key];
                }
            }
        }
    }

    return $tree;
}

/**
 * 将list_to_tree的树还原成列表
 * @param  array  $tree 原来的树
 * @param  string $child 孩子节点的键
 * @param  string $order 排序显示的键，一般是主键 升序排列
 * @param  array  $list 过渡用的中间数组，
 * @return array        返回排过序的列表数组
 * @author yangweijie <yangweijiester@gmail.com>
 */
function tree_to_list($tree, $child = '_child', $order = 'id', &$list = array())
{
    if (is_array($tree)) {
        $refer = array();
        foreach ($tree as $key => $value) {
            $reffer = $value;
            if (isset($reffer[$child])) {
                unset($reffer[$child]);
                tree_to_list($value[$child], $child, $order, $list);
            }
            $list[] = $reffer;
        }
        $list = list_sort_by($list, $order, $sortby = 'asc');
    }
    return $list;
}

/**
 * 格式化字节大小
 * @param  number $size 字节数
 * @param  string $delimiter 数字和单位分隔符
 * @return string            格式化后的带单位的大小
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
function format_bytes($size, $delimiter = '')
{
    $units = array('B', 'KB', 'MB', 'GB', 'TB', 'PB');
    for ($i = 0; $size >= 1024 && $i < 5; $i++) $size /= 1024;
    return round($size, 2) . $delimiter . $units[$i];
}

/**
 * 设置跳转页面URL
 * 使用函数再次封装，方便以后选择不同的存储方式（目前使用cookie存储）
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
function set_redirect_url($url)
{
    cookie('redirect_url', $url);
}

/**
 * 获取跳转页面URL
 * @return string 跳转页URL
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
function get_redirect_url()
{
    $url = cookie('redirect_url');
    return empty($url) ? __APP__ : $url;
}

/**
 * 处理插件钩子
 * @param string $hook 钩子名称
 * @param mixed  $params 传入参数
 * @return void
 */
function hook($hook, $params = array())
{
    \Think\Hook::listen($hook, $params);
}

/**
 * 获取插件类的类名
 * @param strng $name 插件名
 */
function get_addon_class($name)
{
    $class = "Addons\\{$name}\\{$name}Addon";
    return $class;
}

/**
 * 获取插件类的配置文件数组
 * @param string $name 插件名
 */
function get_addon_config($name)
{
    $class = get_addon_class($name);
    if (class_exists($class)) {
        $addon = new $class();
        return $addon->getConfig();
    } else {
        return array();
    }
}

/**
 * 插件显示内容里生成访问插件的url
 * @param string $url url
 * @param array  $param 参数
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
function addons_url($url, $param = array())
{
    $url = parse_url($url);
    $case = C('URL_CASE_INSENSITIVE');
    $addons = $case ? parse_name($url['scheme']) : $url['scheme'];
    $controller = $case ? parse_name($url['host']) : $url['host'];
    $action = trim($case ? strtolower($url['path']) : $url['path'], '/');

    /* 解析URL带的参数 */
    if (isset($url['query'])) {
        parse_str($url['query'], $query);
        $param = array_merge($query, $param);
    }

    /* 基础参数 */
    $params = array(
        '_addons' => $addons,
        '_controller' => $controller,
        '_action' => $action,
    );
    $params = array_merge($params, $param); //添加额外参数
    if (strtolower(MODULE_NAME) == 'admin') {
        return U('Admin/Addons/execute', $params);
    } else {
        return U('Home/Addons/execute', $params);

    }

}

/**
 * 时间戳格式化
 * @param int $time
 * @return string 完整的时间显示
 * @author huajie <banhuajie@163.com>
 */
function time_format($time = NULL, $format = 'Y-m-d H:i')
{
    $time = $time === NULL ? NOW_TIME : intval($time);
    return date($format, $time);
}

/**
 * 根据用户ID获取用户名
 * @param  integer $uid 用户ID
 * @return string       用户名
 */
function get_username($uid = 0)
{
    static $list;
    if (!($uid && is_numeric($uid))) { //获取当前登录用户名
        return $_SESSION['onethink_home']['user_auth']['username'];
    }
	
    /* 获取缓存数据 */
    if (empty($list)) {
        $list = S('sys_active_user_list');
    }

    /* 查找用户信息 */
    $key = "u{$uid}";
    if (isset($list[$key])) { //已缓存，直接使用
        $name = $list[$key];
    } else { //调用接口获取用户信息
        $User = new User\Api\UserApi();
        $info = $User->info($uid);
    
        if ($info && isset($info[1])) {
            $name = $list[$key] = $info[1];
            /* 缓存用户 */
            $count = count($list);
            $max = C('USER_MAX_CACHE');
            while ($count-- > $max) {
                array_shift($list);
            }
            S('sys_active_user_list', $list);
        } else {
            $name = '';
        }
    }
  
    return $name;
  
}

/**
 * 根据用户ID获取用户昵称
 * @param  integer $uid 用户ID
 * @return string       用户昵称
 */
function get_nickname($uid = 0)
{
    static $list;
    if (!($uid && is_numeric($uid))) { //获取当前登录用户名
        return session('user_auth.username');
    }

    /* 获取缓存数据 */
    if (empty($list)) {
        $list = S('sys_user_nickname_list');
    }

    /* 查找用户信息 */
    $key = "u{$uid}";
    if (isset($list[$key])) { //已缓存，直接使用
        $name = $list[$key];
    } else { //调用接口获取用户信息
        $info = M('Member')->field('nickname')->find($uid);
        if ($info !== false && $info['nickname']) {
            $nickname = $info['nickname'];
            $name = $list[$key] = $nickname;
            /* 缓存用户 */
            $count = count($list);
            $max = C('USER_MAX_CACHE');
            while ($count-- > $max) {
                array_shift($list);
            }
            S('sys_user_nickname_list', $list);
        } else {
            $name = '';
        }
    }
    return $name;
}

/**
 * 获取分类信息并缓存分类
 * @param  integer $id 分类ID
 * @param  string  $field 要获取的字段名
 * @return string         分类信息
 */
function get_category($id, $field = null)
{
    static $list;

    /* 非法分类ID */
    if (empty($id) || !is_numeric($id)) {
        return '';
    }

    /* 读取缓存数据 */
    if (empty($list)) {
        $list = S('sys_category_list');
    }

    /* 获取分类名称 */
    if (!isset($list[$id])) {
        $cate = M('Category')->find($id);
        if (!$cate || 1 != $cate['status']) { //不存在分类，或分类被禁用
            return '';
        }
        $list[$id] = $cate;
        S('sys_category_list', $list); //更新缓存
    }
    return is_null($field) ? $list[$id] : $list[$id][$field];
}

/* 根据ID获取分类标识 */
function get_category_name($id)
{
    return get_category($id, 'name');
}

/* 根据ID获取分类名称 */
function get_category_title($id)
{
    return get_category($id, 'title');
}

/**
 * 获取文档模型信息
 * @param  integer $id 模型ID
 * @param  string  $field 模型字段
 * @return array
 */
function get_document_model($id = null, $field = null)
{
    static $list;

    /* 非法分类ID */
    if (!(is_numeric($id) || is_null($id))) {
        return '';
    }

    /* 读取缓存数据 */
    if (empty($list)) {
        $list = S('DOCUMENT_MODEL_LIST');
    }

    /* 获取模型名称 */
    if (empty($list)) {
        $map = array('status' => 1, 'extend' => 1);
        $model = M('Model')->where($map)->field(true)->select();
        foreach ($model as $value) {
            $list[$value['id']] = $value;
        }
        S('DOCUMENT_MODEL_LIST', $list); //更新缓存
    }

    /* 根据条件返回数据 */
    if (is_null($id)) {
        return $list;
    } elseif (is_null($field)) {
        return $list[$id];
    } else {
        return $list[$id][$field];
    }
}

/**
 * 解析UBB数据
 * @param string $data UBB字符串
 * @return string 解析为HTML的数据
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
function ubb($data)
{
    //TODO: 待完善，目前返回原始数据
    return $data;
}

/**
 * 记录行为日志，并执行该行为的规则
 * @param string $action 行为标识
 * @param string $model 触发行为的模型名
 * @param int    $record_id 触发行为的记录id
 * @param int    $user_id 执行行为的用户id
 * @return boolean
 * @author huajie <banhuajie@163.com>
 */
function action_log($action = null, $model = null, $record_id = null, $user_id = null)
{

    //参数检查
    if (empty($action) || empty($model) || empty($record_id)) {
        return '参数不能为空';
    }
    if (empty($user_id)) {
        $user_id = is_login();
    }

    //查询行为,判断是否执行
    $action_info = M('Action')->getByName($action);
    if ($action_info['status'] != 1) {
        return '该行为被禁用或删除';
    }

    //插入行为日志
    $data['action_id'] = $action_info['id'];
    $data['user_id'] = $user_id;
    $data['action_ip'] = ip2long(get_client_ip());
    $data['model'] = $model;
    $data['record_id'] = $record_id;
    $data['create_time'] = NOW_TIME;

    //解析日志规则,生成日志备注
    if (!empty($action_info['log'])) {
        if (preg_match_all('/\[(\S+?)\]/', $action_info['log'], $match)) {
            $log['user'] = $user_id;
            $log['record'] = $record_id;
            $log['model'] = $model;
            $log['time'] = NOW_TIME;
            $log['data'] = array('user' => $user_id, 'model' => $model, 'record' => $record_id, 'time' => NOW_TIME);
            foreach ($match[1] as $value) {
                $param = explode('|', $value);
                if (isset($param[1])) {
                    $replace[] = call_user_func($param[1], $log[$param[0]]);
                } else {
                    $replace[] = $log[$param[0]];
                }
            }
            $data['remark'] = str_replace($match[0], $replace, $action_info['log']);
        } else {
            $data['remark'] = $action_info['log'];
        }
    } else {
        //未定义日志规则，记录操作url
        $data['remark'] = '操作url：' . $_SERVER['REQUEST_URI'];
    }

    M('ActionLog')->add($data);

    if (!empty($action_info['rule'])) {
        //解析行为
        $rules = parse_action($action, $user_id);

        //执行行为
        $res = execute_action($rules, $action_info['id'], $user_id);
    }
}

/**
 * 解析行为规则
 * 规则定义  table:$table|field:$field|condition:$condition|rule:$rule[|cycle:$cycle|max:$max][;......]
 * 规则字段解释：table->要操作的数据表，不需要加表前缀；
 *              field->要操作的字段；
 *              condition->操作的条件，目前支持字符串，默认变量{$self}为执行行为的用户
 *              rule->对字段进行的具体操作，目前支持四则混合运算，如：1+score*2/2-3
 *              cycle->执行周期，单位（小时），表示$cycle小时内最多执行$max次
 *              max->单个周期内的最大执行次数（$cycle和$max必须同时定义，否则无效）
 * 单个行为后可加 ； 连接其他规则
 * @param string $action 行为id或者name
 * @param int    $self 替换规则里的变量为执行用户的id
 * @return boolean|array: false解析出错 ， 成功返回规则数组
 * @author huajie <banhuajie@163.com>
 */
function parse_action($action = null, $self)
{
    if (empty($action)) {
        return false;
    }

    //参数支持id或者name
    if (is_numeric($action)) {
        $map = array('id' => $action);
    } else {
        $map = array('name' => $action);
    }

    //查询行为信息
    $info = M('Action')->where($map)->find();
    if (!$info || $info['status'] != 1) {
        return false;
    }

    //解析规则:table:$table|field:$field|condition:$condition|rule:$rule[|cycle:$cycle|max:$max][;......]
    $rules = $info['rule'];
    $rules = str_replace('{$self}', $self, $rules);
    $rules = explode(';', $rules);
    $return = array();
    foreach ($rules as $key => &$rule) {
        $rule = explode('|', $rule);
        foreach ($rule as $k => $fields) {
            $field = empty($fields) ? array() : explode(':', $fields);
            if (!empty($field)) {
                $return[$key][$field[0]] = $field[1];
            }
        }
        //cycle(检查周期)和max(周期内最大执行次数)必须同时存在，否则去掉这两个条件
        if (!array_key_exists('cycle', $return[$key]) || !array_key_exists('max', $return[$key])) {
            unset($return[$key]['cycle'], $return[$key]['max']);
        }
    }

    return $return;
}

/**
 * 执行行为
 * @param array $rules 解析后的规则数组
 * @param int   $action_id 行为id
 * @param array $user_id 执行的用户id
 * @return boolean false 失败 ， true 成功
 * @author huajie <banhuajie@163.com>
 */
function execute_action($rules = false, $action_id = null, $user_id = null)
{
    if (!$rules || empty($action_id) || empty($user_id)) {
        return false;
    }

    $return = true;
    foreach ($rules as $rule) {

        //检查执行周期
        $map = array('action_id' => $action_id, 'user_id' => $user_id);
        $map['create_time'] = array('gt', NOW_TIME - intval($rule['cycle']) * 3600);
        $exec_count = M('ActionLog')->where($map)->count();
        if ($exec_count > $rule['max']) {
            continue;
        }

        //执行数据库操作
        $Model = M(ucfirst($rule['table']));
        /**
         * 判断是否加入了货币规则
         * @author 郑钟良<zzl@ourstu.com>
         */
        if ($rule['tox_money_rule'] != '' && $rule['tox_money_rule'] != null) {
            $change = array($rule['field'] => array('exp', $rule['rule']), $rule['tox_money_field'] => array('exp', $rule['tox_money_rule']));
            $res = $Model->where($rule['condition'])->setField($change);
        } else {
            $field = $rule['field'];
            $res = $Model->where($rule['condition'])->setField($field, array('exp', $rule['rule']));
        }
        if (!$res) {
            $return = false;
        }
    }
    return $return;
}

//基于数组创建目录和文件
function create_dir_or_files($files)
{
    foreach ($files as $key => $value) {
        if (substr($value, -1) == '/') {
            mkdir($value);
        } else {
            @file_put_contents($value, '');
        }
    }
}

if (!function_exists('array_column')) {
    function array_column(array $input, $columnKey, $indexKey = null)
    {
        $result = array();
        if (null === $indexKey) {
            if (null === $columnKey) {
                $result = array_values($input);
            } else {
                foreach ($input as $row) {
                    $result[] = $row[$columnKey];
                }
            }
        } else {
            if (null === $columnKey) {
                foreach ($input as $row) {
                    $result[$row[$indexKey]] = $row;
                }
            } else {
                foreach ($input as $row) {
                    $result[$row[$indexKey]] = $row[$columnKey];
                }
            }
        }
        return $result;
    }
}

/**
 * 获取表名（不含表前缀）
 * @param string $model_id
 * @return string 表名
 * @author huajie <banhuajie@163.com>
 */
function get_table_name($model_id = null)
{
    if (empty($model_id)) {
        return false;
    }
    $Model = M('Model');
    $name = '';
    $info = $Model->getById($model_id);
    if ($info['extend'] != 0) {
        $name = $Model->getFieldById($info['extend'], 'name') . '_';
    }
    $name .= $info['name'];
    return $name;
}

/**
 * 获取属性信息并缓存
 * @param  integer $id 属性ID
 * @param  string  $field 要获取的字段名
 * @return string         属性信息
 */
function get_model_attribute($model_id, $group = true)
{
    static $list;

    /* 非法ID */
    if (empty($model_id) || !is_numeric($model_id)) {
        return '';
    }

    /* 读取缓存数据 */
    if (empty($list)) {
        $list = S('attribute_list');
    }

    /* 获取属性 */
    if (!isset($list[$model_id])) {
        $map = array('model_id' => $model_id);
        $extend = M('Model')->getFieldById($model_id, 'extend');

        if ($extend) {
            $map = array('model_id' => array("in", array($model_id, $extend)));
        }
        $info = M('Attribute')->where($map)->select();
        $list[$model_id] = $info;
        //S('attribute_list', $list); //更新缓存
    }

    $attr = array();
    foreach ($list[$model_id] as $value) {
        $attr[$value['id']] = $value;
    }

    if ($group) {
        $sort = M('Model')->getFieldById($model_id, 'field_sort');

        if (empty($sort)) { //未排序
            $group = array(1 => array_merge($attr));
        } else {
            $group = json_decode($sort, true);

            $keys = array_keys($group);
            foreach ($group as &$value) {
                foreach ($value as $key => $val) {
                    $value[$key] = $attr[$val];
                    unset($attr[$val]);
                }
            }

            if (!empty($attr)) {
                $group[$keys[0]] = array_merge($group[$keys[0]], $attr);
            }
        }
        $attr = $group;
    }
    return $attr;
}

/**
 * 调用系统的API接口方法（静态方法）
 * api('User/getName','id=5'); 调用公共模块的User接口的getName方法
 * api('Admin/User/getName','id=5');  调用Admin模块的User接口
 * @param  string       $name 格式 [模块名]/接口名/方法名
 * @param  array|string $vars 参数
 */
function api($name, $vars = array())
{
    $array = explode('/', $name);
    $method = array_pop($array);
    $classname = array_pop($array);
    $module = $array ? array_pop($array) : 'Common';
    $callback = $module . '\\Api\\' . $classname . 'Api::' . $method;
    if (is_string($vars)) {
        parse_str($vars, $vars);
    }
    return call_user_func_array($callback, $vars);
}

/**
 * 根据条件字段获取指定表的数据
 * @param mixed  $value 条件，可用常量或者数组
 * @param string $condition 条件字段
 * @param string $field 需要返回的字段，不传则返回整个数据
 * @param string $table 需要查询的表
 * @author huajie <banhuajie@163.com>
 */
function get_table_field($value = null, $condition = 'id', $field = null, $table = null)
{
    if (empty($value) || empty($table)) {
        return false;
    }

    //拼接参数
    $map[$condition] = $value;
    $info = M(ucfirst($table))->where($map);
    if (empty($field)) {
        $info = $info->field(true)->find();
    } else {
        $info = $info->getField($field);
    }
    return $info;
}

/**
 * 获取链接信息
 * @param int    $link_id
 * @param string $field
 * @return 完整的链接信息或者某一字段
 * @author huajie <banhuajie@163.com>
 */
function get_link($link_id = null, $field = 'url')
{
    $link = '';
    if (empty($link_id)) {
        return $link;
    }
    $link = M('Url')->getById($link_id);
    if (empty($field)) {
        return $link;
    } else {
        return $link[$field];
    }
}

/**
 * 获取文档封面图片
 * @param int    $cover_id
 * @param string $field
 * @return 完整的数据  或者  指定的$field字段值
 * @author huajie <banhuajie@163.com>
 */
function get_cover($cover_id, $field = null)
{
    if (empty($cover_id)) {
        return false;
    }
    $picture = M('Picture')->where(array('status' => 1))->getById($cover_id);

    if (is_bool(strpos($picture['path'], 'http://'))) {
        $picture['path'] = fixAttachUrl($picture['path']);
    }

    return empty($field) ? $picture : $picture[$field];
}

function del_picture($id){
	$picture = M('Picture')->where(array('id' => $id))->find();
	$path = '.'.$picture['path'];
	$res = unlink($path);
	if($res){
		M('Picture')->where(array('id' => $id))->delete();
	}
	return $res;
}

function deleteDir($dir){
	if (rmdir($dir)==false && is_dir($dir)) {
	    if ($dp = opendir($dir)) {
	     while (($file=readdir($dp)) != false) {
	      if (is_dir($file) && $file!='.' && $file!='..') {
	       deleteDir($file);
	      } else {
	       unlink($file);
	      }
	     }
	     closedir($dp);
	    } else {
	     exit('Not permission');
	    }
	}
}
/**
 * 检查$pos(推荐位的值)是否包含指定推荐位$contain
 * @param number $pos 推荐位的值
 * @param number $contain 指定推荐位
 * @return boolean true 包含 ， false 不包含
 * @author huajie <banhuajie@163.com>
 */
function check_document_position($pos = 0, $contain = 0)
{
    if (empty($pos) || empty($contain)) {
        return false;
    }

    //将两个参数进行按位与运算，不为0则表示$contain属于$pos
    $res = $pos & $contain;
    if ($res !== 0) {
        return true;
    } else {
        return false;
    }
}

/**
 * 获取数据的所有子孙数据的id值
 */
function get_stemma(&$model,$pids=array(), $field = 'id')
{
    $collection = array();

    //非空判断
    if (empty($pids)) {
        $collection = $model->getField($field,true);
    }

    if (is_array($pids)) {
        $pids = trim(implode(',', $pids), ',');
    }
    $child_ids = $model->field($field)->where(array('pid' => array('IN', (string)$pids)))->getField($field,true);

    while (!empty($child_ids)) {
        $collection = array_merge($collection, $child_ids);
        $child_ids = $model->field($field)->where(array('pid' => array('IN', $child_ids)))->getField($field,true);
    }
    return array_unique($collection);
}
/**
 * 获取数据的所有pids及其已下的接点
 */
function get_children_node($model,$pids=array(), $field = 'id'){
	$cids = get_stemma($model,$pids, $field);
	if(!empty($pids)){
		$cids = array_unique(array_merge($pids,$cids));
	}
	return $cids;
}

/**
 * 获取所有子节点
 * @param unknown $list
 * @param number $parentid
 * @param number $deep
 * @return multitype:number
 */
function get_all_children(&$list,$pid=0,&$res= array()){
	//static $result = array();//保存按顺序查找到的分类
	//按照parent_id查
	foreach($list as $row) {
		//判断
		if ($row['pid'] == $pid) {
			//就是当前分类的子分类
			$res[] = $row['id'];
			//递归查找
			get_all_children($list, $row['id'],$res);
		}
	}
	return $res;
}

/**
 * 获取导航URL
 * @param  string $url 导航URL
 * @return string      解析或的url
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
function get_nav_url($url)
{
    switch ($url) {
        case 'http://' === substr($url, 0, 7):
        case '#' === substr($url, 0, 1):
            break;
        default:
            $url = U($url);
            break;
    }
    return $url;
}

/**
 * @param $url 检测当前url是否被选中
 * @return bool|string
 * @auth 陈一枭
 */
function get_nav_active($url)
{
    switch ($url) {
        case 'http://' === substr($url, 0, 7):
            if (strtolower($url) === strtolower($_SERVER['HTTP_REFERER'])) {
                return 1;
            }
        case '#' === substr($url, 0, 1):
            return 0;
            break;
        default:
            $url_array = explode('/', $url);
            if ($url_array[0] == '') {
                $MODULE_NAME = $url_array[1];
            } else {
                $MODULE_NAME = $url_array[0]; //发现模块就是当前模块即选中。

            }
            if (strtolower($MODULE_NAME) === strtolower(MODULE_NAME)) {
                return 1;
            };
            break;

    }
    return 0;
}

/**
 * 获取列表总行数
 * @param  string  $category 分类ID
 * @param  integer $status 数据状态
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
function get_list_count($category, $status = 1)
{
    static $count;
    if (!isset($count[$category])) {
        $count[$category] = D('Document')->listCount($category, $status);
    }
    return $count[$category];
}

/**
 * t函数用于过滤标签，输出没有html的干净的文本
 * @param string text 文本内容
 * @return string 处理后内容
 */
function op_t($text)
{
    $text = nl2br($text);
    $text = real_strip_tags($text);
    $text = addslashes($text);
    $text = trim($text);
    return $text;
}

/**
 * h函数用于过滤不安全的html标签，输出安全的html
 * @param string $text 待过滤的字符串
 * @param string $type 保留的标签格式
 * @return string 处理后内容
 */
function op_h($text, $type = 'html')
{
    // 无标签格式
    $text_tags = '';
    //只保留链接
    $link_tags = '<a>';
    //只保留图片
    $image_tags = '<img>';
    //只存在字体样式
    $font_tags = '<i><b><u><s><em><strong><font><big><small><sup><sub><bdo><h1><h2><h3><h4><h5><h6>';
    //标题摘要基本格式
    $base_tags = $font_tags . '<p><br><hr><a><img><map><area><pre><code><q><blockquote><acronym><cite><ins><del><center><strike>';
    //兼容Form格式
    $form_tags = $base_tags . '<form><input><textarea><button><select><optgroup><option><label><fieldset><legend>';
    //内容等允许HTML的格式
    $html_tags = $base_tags . '<ul><ol><li><dl><dd><dt><table><caption><td><th><tr><thead><tbody><tfoot><col><colgroup><div><span><object><embed><param>';
    //专题等全HTML格式
    $all_tags = $form_tags . $html_tags . '<!DOCTYPE><meta><html><head><title><body><base><basefont><script><noscript><applet><object><param><style><frame><frameset><noframes><iframe>';
    //过滤标签
    $text = real_strip_tags($text, ${$type . '_tags'});
    // 过滤攻击代码
    if ($type != 'all') {
        // 过滤危险的属性，如：过滤on事件lang js
        while (preg_match('/(<[^><]+)(ondblclick|onclick|onload|onerror|unload|onmouseover|onmouseup|onmouseout|onmousedown|onkeydown|onkeypress|onkeyup|onblur|onchange|onfocus|action|background|codebase|dynsrc|lowsrc)([^><]*)/i', $text, $mat)) {
            $text = str_ireplace($mat[0], $mat[1] . $mat[3], $text);
        }
        while (preg_match('/(<[^><]+)(window\.|javascript:|js:|about:|file:|document\.|vbs:|cookie)([^><]*)/i', $text, $mat)) {
            $text = str_ireplace($mat[0], $mat[1] . $mat[3], $text);
        }
    }
    return $text;
}

function real_strip_tags($str, $allowable_tags = "")
{
    $str = html_entity_decode($str, ENT_QUOTES, 'UTF-8');
    return strip_tags($str, $allowable_tags);
}


function is_ie()
{
    $userAgent = $_SERVER['HTTP_USER_AGENT'];
    $pos = strpos($userAgent, ' MSIE ');
    if ($pos === false) {
        return false;
    } else {
        return true;
    }
}

function array_subtract($a, $b)
{
    return array_diff($a, array_intersect($a, $b));
}

/*require_once(APP_PATH . '/Common/Common/extend.php');*/
function tox_addons_url($url, $param)
{
    // 拆分URL
    $url = explode('/', $url);
    $addon = $url[0];
    $controller = $url[1];
    $action = $url[2];

    // 调用u函数
    $param['_addons'] = $addon;
    $param['_controller'] = $controller;
    $param['_action'] = $action;
    return U("Home/Addons/execute", $param);
}


/**
 * 取一个二维数组中的每个数组的固定的键知道的值来形成一个新的一维数组
 * @param $pArray 一个二维数组
 * @param $pKey 数组的键的名称
 * @return 返回新的一维数组
 */
function getSubByKey($pArray, $pKey = "", $pCondition = "")
{
    $result = array();
    if (is_array($pArray)) {
        foreach ($pArray as $temp_array) {
            if (is_object($temp_array)) {
                $temp_array = (array)$temp_array;
            }
            if (("" != $pCondition && $temp_array[$pCondition[0]] == $pCondition[1]) || "" == $pCondition) {
                $result[] = ("" == $pKey) ? $temp_array : isset($temp_array[$pKey]) ? $temp_array[$pKey] : "";
            }
        }
        return $result;
    } else {
        return false;
    }
}

/**
 * create_rand  随机生成一个字符串
 * @param int $length 字符串的长度
 * @return string
 * @author:xjw129xjt xjt@ourstu.com
 */
function create_rand($length = 8)
{
    $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    $str = '';
    for ($i = 0; $i < $length; $i++) {
        $str .= $chars[mt_rand(0, strlen($chars) - 1)];
    }
    return $str;

}

function create_order($uid=0,$type=1){
	$yCode = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J');
	$uCode =  array('K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T');
	$len = count($yCode);
	$yu = $uid%$len;
	$ye = (intval(date('Y')) - 2014)%$len;
    $orderSn = $yCode[$ye]. strtoupper(dechex(date('m'))) . date('d') . substr(time(), -5) . substr(microtime(), 2, 5) . sprintf('%04d%02d', rand(1000, 9999),rand(0,99)).$uCode[$yu].$type;
	return $orderSn;
}

/**
 * tox_get_headers 获取链接header
 * @param $url
 * @return array
 * @author:xjw129xjt xjt@ourstu.com
 */
function tox_get_headers($url)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_NOBODY, 1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HEADER, 1);
    $f = curl_exec($ch);
    curl_close($ch);
    $h = explode("\n", $f);
    $r = array();
    foreach ($h as $t) {
        $rr = explode(":", $t, 2);
        if (count($rr) == 2) {
            $r[$rr[0]] = trim($rr[1]);
        }
    }
    return $r;
}

/**
 * get_some_day  获取n天前0点的时间戳
 * @param int $some  n天
 * @param null $day   当前时间
 * @return int|null
 * @author:xjw129xjt(肖骏涛) xjt@ourstu.com
 */
function get_some_day($some=30,$day=null){
    $time = $day?$day:time();
    $some_day = $time-60*60*24*$some;
    $btime = date('Y-m-d'.' 00:00:00',$some_day);
    $some_day = strtotime($btime);
    return $some_day;
}


/**
 * 现金变更记录
 * @param unknown $recordtype 记录类型（消费、收入）
 * @param unknown $changetype 变更类型
 * @param unknown $userinfo 来源
 * @param unknown $targetinfo 目标用户
 * @param unknown $money 变更金额
 * @param unknown $hasmoney 变更后该账户余额
 * @return unknown|number
 */
function moneyChange($recordtype,$changetype, $targetinfo,$userinfo,$money ,$hasmoney,$taxmoney,$moneytype){
    $MoneyChange =  M('MoneyChange');
    $data['userid']=$userinfo['uid'];
    $data['usernumber']=$userinfo['usernumber'];
    $data['targetuserid']= $targetinfo['uid'];
    $data['targetusernumber']= $targetinfo['usernumber'];
    $data['recordtype']=$recordtype;
    $data['moneytype']=$moneytype;
    $data['money']=$money;
    $data['hasmoney']=$hasmoney;
    $data['changetype']=$changetype;
    $data['taxmoney']=$taxmoney;
    $data['createtime']=time();
    $data['status']=1;
    $result = $MoneyChange->add($data);

    return $result ;
}

/**
 * 现金变更记录
 * @param array $type（类型：记录类型，变更类型，币种）
 * @param array $userinfo 来源
 * @param array $targetinfo 目标用户
 * @param array $money
 * @return unknown|number
 */
function money_change($type,$targetinfo,$userinfo,$money,$op=null){
    
	$MoneyChange =  M('MoneyChange');
	$data['userid']=$userinfo['uid'];
	$data['usernumber']=$userinfo['usernumber'];
	$data['targetuserid']= $targetinfo['uid'];
	$data['targetusernumber']= $targetinfo['usernumber'];

	$data['recordtype']=$type['recordtype'];
	$data['moneytype']=$type['moneytype'];
	$data['changetype']=$type['changetype'];

	$data['money']=$money['money'];
//	$data['electron_money']=$money['electron_money'];
	$data['register_money']=$money['register_money'];
	$data['hasmoney']=$money['hasmoney'];
	$data['hasbill']=$money['hasbill'];
	$data['lovefund']=isset($money['lovefund'])?$money['lovefund']:0;
	$data['tax_money']=isset($money['taxmoney'])?$money['taxmoney']:0;
	$data['cpmoney']=isset($money['cpmoney'])?$money['cpmoney']:0;
	$data['othermoney']=isset($money['othermoney'])?$money['othermoney']:0;
	if(!empty($op)){
		$data['op'] = $op;
	}

	$data['createtime']=time();
	$data['status']=1;
	
	$result = $MoneyChange->add($data);
	return $result ;
}

function finance($changetype,$hasmoney,$taxmoney){
	$count_date=strtotime('today');
	$map['createtime'] = $count_date;
	$count=M("Finance")->where($map)->find();
	$expend_arr = array(1,2,4,7,6);
	$income_arr = array(5,3,20,25,8);
	if(!empty($count)){
		if(in_array($changetype, $expend_arr)){
			//$data['income']=$count['income']+$taxmoney;
			$data['income']=$count['income'];
			$data['expend']=$count['expend']+$hasmoney;
		}elseif(in_array($changetype, $income_arr)){
			$data['income']=$count['income']+$hasmoney;
		}else{
			//$data['income']=$count['income']+$taxmoney;
			$data['income']=$count['income'];
		}
		M("Finance")->where($map)->save($data);
	}else{
		$data['createtime']=$count_date;
		if(in_array($changetype, $expend_arr)){
			$data['expend']=$hasmoney;
			$data['income']=$taxmoney;
		}elseif(in_array($changetype, $income_arr)){
			$data['income']=$hasmoney;
		}else{
			$data['income']=$taxmoney;
		}
		M("Finance")->add($data);
	}
}
/*
 * 短信
 * $user 用户名
 * $yzm  验证码
 * $form 平台名称
 * $mobil 收短信手机号
 * $type 短信类型
 */
function msg($user,$yzm,$form,$mobile,$type,$appid){
    if ($type==1){  //登录
        
        $moba="T170317002396";
    }else{
        $data=array("minute"=>"1","code"=>$yzm,"comName"=>"$form");
        $moba="T170317002396";
    }
    $host = "http://ali-sms.showapi.com";
    $path = "/sendSms";
    $method = "GET";
    $appcode = $appid;
    $headers = array();
    array_push($headers, "Authorization:APPCODE " . $appcode);

    $data=json_encode($data);
    $querys = "mobile=$mobile&tNum=$moba&content=$data";

    $bodys = "";
    $url = $host . $path . "?" . $querys;
    
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($curl, CURLOPT_FAILONERROR, false);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_HEADER, true);
    if (1 == strpos("$".$host, "https://"))
    {
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
    }
			
    $status=substr(curl_exec($curl),9,3);
    $typa=substr(curl_exec($curl),13,2);
    $map["mobile"]=$mobile;
    $map["msg"]=$yzm;
    $map["contents"]=$typa;
    $map["status"]=$status;
    $map["create_time"]=time();
    $res=D("msg")->add($map);
    return true;
}
/*****************************/
/**
 * 系统公共库文件
 * 主要定义系统公共函数库
 */
function sendMsg($mobile)
{
    // $mobile = safe_replace($mobile);

    
    if (empty($mobile)) {
        $mes['status'] = 0;
        $mes['message'] = '手机号码不能为空';
    }

    if (time() > session('set_time') + 60 || session('set_time') == '') {
        session('set_time', time());
        $user_mobile = $mobile;
        $code = getCode();
        $sms_code = sha1(md5(trim($code) . trim($mobile)));
        session('sms_code', $sms_code);
        //发送短信
//        $content="您本次的验证码为".$code."，请在5分钟内完成验证，验证码打死也不要告诉别人哦！";//要发送的短信内容
        // require_once COMMON_PATH . 'Util/SmsMeilian.class.php';
        // Vendor('SmsMeilian');
        require_once APP_PATH . 'Common/Common/SmsMeilian.php';
        $username='tyj';  //用户名dctx
        $password_md5='5d93ceb70e2bf5daa84ec3d0cd2c731a';  //32位MD5密码加密，不区分大小写0b11ac988314c2399752d3b4d875b217
        $apikey='a9a28f8f9ad1f4510de0a2c350468fc0';  //apikey秘钥（请登录 http://m.5c.com.cn 短信平台-->账号管理-->我的信息 中复制apikey）e525954fc72f54324d3c4a7bd2fc20c6
        $contentUrlEncode = urlencode('【CIEX】你的验证码是'.$code.'，如非本人操作，请忽略本短信');//执行URLencode编码  ，$content = urldecode($content);解码
        $smsMeilian = new SmsMeilian();
        $result = $smsMeilian->sendSMS($username, $password_md5, $apikey, $mobile, $contentUrlEncode,'UTF-8'); 
        if (strpos($result,"success")>-1) {
            $mes['status'] = 1;
            $mes['message'] = '短信发送成功';
            return $mes;
        } else {
            $mes['status'] = 0;
            $mes['message'] = '短信发送失败';
            return $mes;
        }
    } else {
        $msgtime = session('set_time') + 60 - time();
        $data = $msgtime . '秒之后再试';
        $mes['status'] = 0;
        $mes['message'] = $data;
        return $mes;
    }
}
function getCode() {
    return  rand(100000,999999);
}
function is_sndpsd(){
    $ifpsd=session('ifpsd');
    return $ifpsd;
}
/**
 * 判断访问设备
 * @return boolean
 */
function isMobile(){
    $useragent=isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
    $useragent_commentsblock=preg_match('|\(.*?\)|',$useragent,$matches)>0?$matches[0]:'';
    
    $mobile_os_list=array('Google Wireless Transcoder','Windows CE','WindowsCE','Symbian','Android','armv6l','armv5','Mobile','CentOS','mowser','AvantGo','Opera Mobi','J2ME/MIDP','Smartphone','Go.Web','Palm','iPAQ');
    $mobile_token_list=array('Profile/MIDP','Configuration/CLDC-','160×160','176×220','240×240','240×320','320×240','UP.Browser','UP.Link','SymbianOS','PalmOS','PocketPC','SonyEricsson','Nokia','BlackBerry','Vodafone','BenQ','Novarra-Vision','Iris','NetFront','HTC_','Xda_','SAMSUNG-SGH','Wapaka','DoCoMo','iPhone','iPod');

    $found_mobile=CheckSubstr($mobile_os_list,$useragent_commentsblock) ||
    CheckSubstr($mobile_token_list,$useragent);

    if ($found_mobile){
        return true;
    }else{
        return false;
    }
}

function CheckSubstr($substrs,$text){
	foreach($substrs as $substr)
		if(false!==strpos($text,$substr)){
		return true;
	}
	return false;
}
/**
 * 查找接点位
 * @param unknown $uid
 * @return unknown
 */
function getPosition($uid=1){
        $maxpdeep = M('Member')->max('pdeep');
        $map['pdeep'] = $maxpdeep;
        $puid = M('Member')->where($map)->getField('uid');
        if($puid){
            return $puid;
        }else{
            return ;
        }
}

function get_user_level_moeny($level,$bonusRole,$str,$key = 2){
    $level_money =$bonusRole[$str];
    $user_level_money = user_level_bonus_format($level_money) ;
    return  $user_level_money[$level][$key];
}

function user_level_bonus_format($string) {
    $arr = array_filter(explode(',', $string));

    $count = count($arr) ;
    // 弹出最后一个元素
    for($i = 0; $i < $count; $i++) {
        $result  = explode(':', $arr[$i]) ;
        $count1 = count($result) ;
        for($j = 0; $j < $count1; $j++) {
            $myresult[$result[0]][$j+1] = $result[$j];
        }
    }
    return $myresult;
}

/**
 * 父辈接点所在区
 */
function  leader_area($string){

	$arr = array_filter(explode(',', $string));

	$count = count($arr) ;
	// 弹出最后一个元素
	for($i = 0; $i < $count; $i++) {
		$result  = str2arr($arr[$i],':');
		$count1 = count($result) ;
		for($j = 0; $j < $count1; $j++) {
			$myresult[$result[0]][$j+1] = $result[$j];
		}
	}
	return $myresult;

}

function parentids_format($string) {
	$arr = str2arr($string);

	$count = count($arr) ;
	// 弹出最后一个元素
	for($i = 0; $i < $count; $i++) {
		$result  = explode(':', $arr[$i]) ;
		if($result[1]==1){
			$myresult['array_1'][] = $result[0];
		}
		if($result[1]==2){
			$myresult['array_2'][] = $result[0];
		}
	
	}
	return $myresult;
}


/**
 * 奖金规则中数组长度
 * @param unknown $string
 * @param number $key
 * @return unknown
 */
function arr_level($string,$key=0){
	$arr = array_filter(explode(',', $string));
	for($i=0;$i<count($arr);$i++){
		$level[$i] = array_filter(explode(':', $arr[$i]));
		$res[$i] = $level[$i][$key];
	}
	return $res;
}

/**
 * 获取规则
 * @param unknown $field
 * @param number $id
 */
function get_bonus_rule($field='',$id=1){
	$rule =   M('BonusRule')->where(array('id'=>$id))->find();
	
	if(empty($field)){
		return $rule;
	}else{
		return $rule[$field];
	}
	
}


/**
 * 删除数组中的特定值
 */
function del_array_key($arr,$val){
	foreach($arr as $k => $v){
		if($v ==$val){
			unset($arr[$k]);
		}
	}
	
	return $arr;
}
/**
 * 删除数组中特定键值得值
 */
function del_array($arr,$val,$str='eq'){
	foreach($arr as $k => $v){
		switch ($str){
			case 'elt':
				if($k <= $val){
					unset($arr[$k]);
				}
				break;
			case 'lt':
				if($k < $val){
					unset($arr[$k]);
				}
				break;
			case 'egt':
				if($k >= $val){
					unset($arr[$k]);
				}
				break;
			case 'gt':
				if($k > $val){
					unset($arr[$k]);
				}
				break;
			case 'neq':
				if($k > $val){
					unset($arr[$k]);
				}
				break;
			default:
				if($k == $val){
					unset($arr[$k]);
				}
		}
		
	}

	return $arr;
}

/**
 * 银行卡
 * @param unknown $card
 * @return number|Ambigous <>
 */
function bankInfo($card)
{
	$bankList = C('BANKLIST');
	$reg = '/^(\d{16}|\d{19})$/';
	$res = preg_match($reg, $card);
	if(!$res){
		return -2;
	}
	$card_8 = substr($card, 0, 8);
	if (isset($bankList[$card_8])) {
		return $bankList[$card_8];
	}
	$card_6 = substr($card, 0, 6);
	if (isset($bankList[$card_6])) {
		return $bankList[$card_6];
	}
	$card_5 = substr($card, 0, 5);
	if (isset($bankList[$card_5])) {
		return $bankList[$card_5];
	}
	$card_4 = substr($card, 0, 4);
	if (isset($bankList[$card_4])) {
		return $bankList[$card_4];;
	}
	return -1;
}
/**
 * 区域参数
 * @param unknown $area
 * @return unknown
 */
function param($area){
	$area = str2arr($area);
	$param['province']  = $area[0];
	$param['city'] = $area[1];
	$param['district']  = $area[2];

	return $param;
}

/**
 * 获取收货地址信息
 * @param int $id
 * @return string
 */
/* function address($id){
	$map['id'] = $id;
	$row = M('transport')->where($map)->field('province,city,district,address')->find();
	$province=$row['province'];$city=$row['city'];$district=$row['district'];
	$pro=M('district')->select("$province,$city,$district");

	if(!empty($pro)){
		return $pro[0]['name'].'-'.$pro[1]['name'].'-'.$pro[2]['name'].'-'.$row['address'];
	}
// 	$areaid = array_filter(explode(',', $row['area']));
// 	 $len = count($areaid);
// 	for ($i=0;$i<$len;$i++){
// 	$area[] = M('District')->where(array('id'=>$areaid[$i]))->getField('name');
// 	}
// 	$address = $area[0].$area[1].$area[2].$row['address'];
	return '无';
} */


function address($id){
	$map['id'] = $id;
	//$row = M('transport')->where($map)->field('province,city,district,address')->find();
	$row = M('transport')->where($map)->field('area,address')->find();
	/* 	$province=$row['province'];$city=$row['city'];$district=$row['district'];
	 $pro=M('district')->select("$province,$city,$district");

	if(!empty($pro)){
	return $pro[0]['name'].'-'.$pro[1]['name'].'-'.$pro[2]['name'].'-'.$row['address'];
	} */
	$areaid = array_filter(explode(',', $row['area']));
	$len = count($areaid);
	for ($i=0;$i<$len;$i++){
		$area[] = M('District')->where(array('id'=>$areaid[$i]))->getField('name');
	}
	$address = $area[0].$area[1].$area[2].$row['address'];
	return $address;
}

function getqianbao($id){
    $qianbao=D("member")->where(array("uid"=>$id))->find();

    return  substr($qianbao["qianbao"],13,7);

}

function get_ar($id){
    $qianbao=D("document_article")->where(array("id"=>$id))->find();
    $nr=substr($qianbao["content"],0,300)."...";
    return $nr ;
}

function get_unm($id){
	$info=D("member")->where(array("uid"=>$id))->find();
  
  if($info["realname"]==" "){
  	$a="公司";
  }else{
  	$a=$info["realname"];
  }
  
  
  return $a;
}


require_once(APP_PATH . 'Common/Common/query_user.php');
require_once(APP_PATH . 'Common/Common/thumb.php');
require_once(APP_PATH . 'Common/Common/api.php');
require_once(APP_PATH . 'Common/Common/time.php');
require_once(APP_PATH . 'Common/Common/match.php');
require_once(APP_PATH . 'Common/Common/seo.php');
require_once(APP_PATH . 'Common/Common/type.php');
require_once(APP_PATH . 'Common/Common/cache.php');
require_once(APP_PATH . 'Common/Common/vendors.php');
require_once(APP_PATH . 'Common/Common/parse.php');
require_once(APP_PATH . 'Common/Conf/config_ucenter.php');
require_once(APP_PATH . 'Common/Common/numtotext.php');
require_once(APP_PATH . 'Common/Common/shop.php');
