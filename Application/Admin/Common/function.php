<?php

/**
 * 后台公共文件
 * 主要定义后台公共函数库
 */

/**
 * 根据类别获取类别名称，shop_category
 */
function getShopCategoryTitle($categoryid) {
	if (empty ( $categoryid ) || ! is_numeric ( $categoryid )) {
		return false;
	}
	return M ( 'shop_category' )->where ( array (
			'status' => 1,
			'id' => $categoryid
	) )->getField ( 'title' );
}

/* 解析列表定义规则*/

function get_list_field($data, $grid,$model){

	// 获取当前字段数据
    foreach($grid['field'] as $field){
        $array  =   explode('|',$field);
        $temp  =	$data[$array[0]];
        // 函数支持
        if(isset($array[1])){
            $temp = call_user_func($array[1], $temp);
        }
        $data2[$array[0]]    =   $temp;
    }
    if(!empty($grid['format'])){
        $value  =   preg_replace_callback('/\[([a-z_]+)\]/', function($match) use($data2){return $data2[$match[1]];}, $grid['format']);
    }else{
        $value  =   implode(' ',$data2);
    }

	// 链接支持
	if(!empty($grid['href'])){
		$links  =   explode(',',$grid['href']);
        foreach($links as $link){
            $array  =   explode('|',$link);
            $href   =   $array[0];
            if(preg_match('/^\[([a-z_]+)\]$/',$href,$matches)){
                $val[]  =   $data2[$matches[1]];
            }else{
                $show   =   isset($array[1])?$array[1]:$value;
                // 替换系统特殊字符串
                $href	=	str_replace(
                    array('[DELETE]','[EDIT]','[MODEL]'),
                    array('del?ids=[id]&model=[MODEL]','edit?id=[id]&model=[MODEL]',$model['id']),
                    $href);

                // 替换数据变量
                $href	=	preg_replace_callback('/\[([a-z_]+)\]/', function($match) use($data){return $data[$match[1]];}, $href);

                $val[]	=	'<a href="'.U($href).'">'.$show.'</a>';
            }
        }
        $value  =   implode(' ',$val);
	}
    return $value;
}

// 获取模型名称
function get_model_by_id($id){
    return $model = M('Model')->getFieldById($id,'title');
}

// 获取属性类型信息
function get_attribute_type($type=''){
    // TODO 可以加入系统配置
    static $_type = array(
        'num'       =>  array('数字','int(10) UNSIGNED NOT NULL'),
        'string'    =>  array('字符串','varchar(255) NOT NULL'),
        'textarea'  =>  array('文本框','text NOT NULL'),
        'datetime'  =>  array('时间','int(10) NOT NULL'),
        'bool'      =>  array('布尔','tinyint(2) NOT NULL'),
        'select'    =>  array('枚举','char(50) NOT NULL'),
    	'radio'		=>	array('单选','char(10) NOT NULL'),
    	'checkbox'	=>	array('多选','varchar(100) NOT NULL'),
    	'editor'    =>  array('编辑器','text NOT NULL'),
    	'picture'   =>  array('上传图片','int(10) UNSIGNED NOT NULL'),
    	'file'    	=>  array('上传附件','int(10) UNSIGNED NOT NULL'),
    );
    return $type?$_type[$type][0]:$_type;
}

/**
 * 获取对应状态的文字信息
 * @param int $status
 * @return string 状态文字 ，false 未获取到
 * @author huajie <banhuajie@163.com>
 */
function get_status_title($status = null){
    if(!isset($status)){
        return false;
    }
    switch ($status){
        case -1 : return    '已删除';   break;
        case 0  : return    '禁用';     break;
        case 1  : return    '正常';     break;
        case 2  : return    '待审核';   break;
        default : return    false;      break;
    }
}

// 获取数据的状态操作
function show_status_op($status) {
    switch ($status){
        case 0  : return    '启用';     break;
        case 1  : return    '禁用';     break;
        case 2  : return    '审核';		break;
        default : return    false;      break;
    }
}

/**
 * 获取文档的类型文字
 * @param string $type
 * @return string 状态文字 ，false 未获取到
 * @author huajie <banhuajie@163.com>
 */
function get_document_type($type = null){
    if(!isset($type)){
        return false;
    }
    switch ($type){
        case 1  : return    '目录'; break;
        case 2  : return    '主题'; break;
        case 3  : return    '段落'; break;
        default : return    false;  break;
    }
}

/**
 * 获取配置的类型
 * @param string $type 配置类型
 * @return string
 */
function get_config_type($type=0){
    $list = C('CONFIG_TYPE_LIST');
    return $list[$type];
}

/**
 * 获取配置的分组
 * @param string $group 配置分组
 * @return string
 */
function get_config_group($group=0){
    $list = C('CONFIG_GROUP_LIST');
    return $group?$list[$group]:'';
}




/**
 * select返回的数组进行整数映射转换
 *
 * @param array $map  映射关系二维数组  array(
 *                                          '字段名1'=>array(映射关系数组),
 *                                          '字段名2'=>array(映射关系数组),
 *                                           ......
 *                                       )
 * @author 朱亚杰 <zhuyajie@topthink.net>
 * @return array
 *
 *  array(
 *      array('id'=>1,'title'=>'标题','status'=>'1','status_text'=>'正常')
 *      ....
 *  )
 *
 */
function int_to_string(&$data,$map=array('status'=>array(1=>'正常',-1=>'删除',0=>'禁用',2=>'未审核',3=>'草稿'))) {
    if($data === false || $data === null ){
        return $data;
    }
    $data = (array)$data;
    foreach ($data as $key => $row){
        foreach ($map as $col=>$pair){
            if(isset($row[$col]) && isset($pair[$row[$col]])){
                $data[$key][$col.'_text'] = $pair[$row[$col]];
            }
        }
    }
    return $data;
}

/**
 * 动态扩展左侧菜单,base.html里用到
 * @author 朱亚杰 <zhuyajie@topthink.net>
 */
function extra_menu($extra_menu,&$base_menu){
    foreach ($extra_menu as $key=>$group){
        if( isset($base_menu['child'][$key]) ){
            $base_menu['child'][$key] = array_merge( $base_menu['child'][$key], $group);
        }else{
            $base_menu['child'][$key] = $group;
        }
    }
}

/**
 * 获取参数的所有父级分类
 * @param int $cid 分类id
 * @return array 参数分类和父类的信息集合
 * @author huajie <banhuajie@163.com>
 */
function get_parent_category($cid){
    if(empty($cid)){
        return false;
    }
    $cates  =   M('Category')->where(array('status'=>1))->field('id,title,pid')->order('sort')->select();
    $child  =   get_category($cid);	//获取参数分类的信息
    $pid    =   $child['pid'];
    $temp   =   array();
    $res[]  =   $child;
    while(true){
        foreach ($cates as $key=>$cate){
            if($cate['id'] == $pid){
                $pid = $cate['pid'];
                array_unshift($res, $cate);	//将父分类插入到数组第一个元素前
            }
        }
        if($pid == 0){
            break;
        }
    }
    return $res;
}

/**
 * 获取当前分类的文档类型
 * @param int $id
 * @return array 文档类型数组
 * @author huajie <banhuajie@163.com>
 */
function get_type_bycate($id = null){
    if(empty($id)){
        return false;
    }
    $type_list  =   C('DOCUMENT_MODEL_TYPE');
    $model_type =   M('Category')->getFieldById($id, 'type');
    $model_type =   explode(',', $model_type);
    foreach ($type_list as $key=>$value){
        if(!in_array($key, $model_type)){
            unset($type_list[$key]);
        }
    }
    return $type_list;
}

/**
 * 获取当前文档的分类
 * @param int $id
 * @return array 文档类型数组
 * @author huajie <banhuajie@163.com>
 */
function get_cate($cate_id = null){
    if(empty($cate_id)){
        return false;
    }
    $cate   =   M('Category')->where('id='.$cate_id)->getField('title');
    return $cate;
}

 // 分析枚举类型配置值 格式 a:名称1,b:名称2
function parse_config_attr($string) {
    $array = preg_split('/[,;\r\n]+/', trim($string, ",;\r\n"));
    if(strpos($string,':')){
        $value  =   array();
        foreach ($array as $val) {
            list($k, $v) = explode(':', $val);
            $value[$k]   = $v;
        }
    }else{
        $value  =   $array;
    }
    return $value;
}

// 获取子文档数目
function get_subdocument_count($id=0){
    return  M('Document')->where('pid='.$id)->count();
}



 // 分析枚举类型字段值 格式 a:名称1,b:名称2
 // 暂时和 parse_config_attr功能相同
 // 但请不要互相使用，后期会调整
function parse_field_attr($string) {
    if(0 === strpos($string,':')){
        // 采用函数定义
        return   eval(substr($string,1).';');
    }
    $array = preg_split('/[,;\r\n]+/', trim($string, ",;\r\n"));
    if(strpos($string,':')){
        $value  =   array();
        foreach ($array as $val) {
            list($k, $v) = explode(':', $val);
            $value[$k]   = $v;
        }
    }else{
        $value  =   $array;
    }
    return $value;
}

/**
 * 获取行为数据
 * @param string $id 行为id
 * @param string $field 需要获取的字段
 * @author huajie <banhuajie@163.com>
 */
function get_action($id = null, $field = null){
	if(empty($id) && !is_numeric($id)){
		return false;
	}
	$list = S('action_list');
	if(empty($list[$id])){
		$map = array('status'=>array('gt', -1), 'id'=>$id);
		$list[$id] = M('Action')->where($map)->field(true)->find();
	}
	return empty($field) ? $list[$id] : $list[$id][$field];
}

/**
 * 根据条件字段获取数据
 * @param mixed $value 条件，可用常量或者数组
 * @param string $condition 条件字段
 * @param string $field 需要返回的字段，不传则返回整个数据
 * @author huajie <banhuajie@163.com>
 */
function get_document_field($value = null, $condition = 'id', $field = null){
	if(empty($value)){
		return false;
	}

	//拼接参数
	$map[$condition] = $value;
	$info = M('Model')->where($map);
	if(empty($field)){
		$info = $info->field(true)->find();
	}else{
		$info = $info->getField($field);
	}
	return $info;
}

/**
 * 获取行为类型
 * @param intger $type 类型
 * @param bool $all 是否返回全部类型
 * @author huajie <banhuajie@163.com>
 */
function get_action_type($type, $all = false){
	$list = array(
		1=>'系统',
		2=>'用户',
	);
	if($all){
		return $list;
	}
	return $list[$type];
}

function admin_md5($str){
     if(C('ADMIN_AUTH_KEY')){
         $key = C('ADMIN_AUTH_KEY');
     }else{
         $key = "bobyao";
     }
    return '' === $str ? '' : md5(sha1($str) . $key);
}

function is_admin_login(){
    $user = session('admin_auth');
    if (empty($user)) {
        return 0;
    } else {
        return session('admin_auth_sign') == data_auth_sign($user) ? $user['mid'] : 0;
    }
}

/**
 * 检测当前用户是否为超级管理员
 * @return boolean true-管理员，false-非管理员
 */
function is_administrator($uid = null){
    $uid = is_null($uid) ? is_admin_login() : $uid;
    $admin_uids = explode(',', C('USER_ADMINISTRATOR'));//调整验证机制，支持多管理员，用,分隔
    //dump($admin_uids);exit;
    return $uid && (in_array(intval($uid), $admin_uids));//调整验证机制，支持多管理员，用,分隔
}

function xs2arr(){
	$xs = '赵 钱 孙 李 周 吴 郑 王 冯 陈 褚 卫
			蒋 沈 韩 杨 朱 秦 尤 许 何 吕 施 张
			孔 曹 严 华 金 魏 陶 姜 戚 谢 邹 喻
			柏 水 窦 章 云 苏 潘 葛 奚 范 彭 郎
			鲁 韦 昌 马 苗 凤 花 方 俞 任 袁 柳
			酆 鲍 史 唐 费 廉 岑 薛 雷 贺 倪 汤
			滕 殷 罗 毕 郝 邬 安 常 乐 于 时 傅
			皮 卞 齐 康 伍 余 元 卜 顾 孟 平 黄
			和 穆 萧 尹 姚 邵 湛 汪 祁 毛 禹 狄
			米 贝 明 臧 计 伏 成 戴 谈 宋 茅 庞
			熊 纪 舒 屈 项 祝 董 梁 杜 阮 蓝 闵
			席 季 麻 强 贾 路 娄 危 江 童 颜 郭
			梅 盛 林 刁 锺 徐 邱 骆 高 夏 蔡 田
			樊 胡 凌 霍 虞 万 支 柯 昝 管 卢 莫
			经 房 裘 缪 干 解 应 宗 丁 宣 贲 邓
			郁 单 杭 洪 包 诸 左 石 崔 吉 钮 龚
			程 嵇 邢 滑 裴 陆 荣 翁 荀 羊 於 惠
			甄 麴 家 封 芮 羿 储 靳 汲 邴 糜 松
			井 段 富 巫 乌 焦 巴 弓 牧 隗 山 谷
			车 侯 宓 蓬 全 郗 班 仰 秋 仲 伊 宫
			宁 仇 栾 暴 甘 钭 历 戎 祖 武 符 刘
			景 詹 束 龙 叶 幸 司 韶 郜 黎 蓟 溥
			印 宿 白 怀 蒲 邰 从 鄂 索 咸 籍 赖
			卓 蔺 屠 蒙 池 乔 阳 郁 胥 能 苍 双
			闻 莘 党 翟 谭 贡 劳 逄 姬 申 扶 堵
			冉 宰 郦 雍 却 璩 桑 桂 濮 牛 寿 通
			边 扈 燕 冀 僪 浦 尚 农 温 别 庄 晏
			柴 瞿 阎 充 慕 连 茹 习 宦 艾 鱼 容
			向 古 易 慎 戈 廖 庾 终 暨 居 衡 步
			都 耿 满 弘 匡 国 文 寇 广 禄 阙 东
			欧 殳 沃 利 蔚 越 夔 隆 师 巩 厍 聂
			晁 勾 敖 融 冷 訾 辛 阚 那 简 饶 空
			曾 毋 沙 岳 养 鞠 须 丰 巢 关 蒯 相
			查 后 荆 红 游 竺 权 逮 盍 益 桓 公
			万俟 司马 上官 欧阳 夏侯 诸葛 闻人 东方 赫连 皇甫 尉迟 公羊
			澹台 公冶 宗政 濮阳 淳于 单于 太叔 申屠 公孙 仲孙 轩辕 令狐
			钟离 宇文 长孙 慕容 司徒 司空 ';
	$xs = preg_replace("/[\s]+/is"," ",$xs);
	$xs_arr = str2arr($xs, ' ');
	return $xs_arr;
}

function xm2arr(){
	$xs = '昊强 烨伟 苑博  鹏涛 炎彬  鹤轩 伟泽 熠彤  鸿煊 博涛 苑杰 黎昕 烨霖  哲瀚 雨泽 鹤轩 建辉
			雅琳 梦洁 凌薇 美莲  雅静  雪丽  依娜  雅芙 雨婷 曼婷 雪慧 淑颖 钰彤 璟雯 天瑜 婧琪 溶月
			素菲 诗涵 宁馨 妙菱 心琪';
	$xs = preg_replace("/[\s]+/is"," ",$xs);
	$xs_arr = str2arr($xs, ' ');
	return $xs_arr;
}

function ee($uid){
  	$name=D("member")->where(array("uid"=>$uid))->find();
    
    return $name["realname"];
  }
