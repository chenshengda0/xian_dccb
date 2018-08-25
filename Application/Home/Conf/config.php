<?php

/**
 * 前台配置文件
 * 所有除开系统级别的前台配置
 */
define('SERVER_KEY', 'FT?Jo.*Mz<"9/dD{mG-C$k1VB+Pr~seLup42O[U&'); //加密KEY
return array(

    // 预先加载的标签库
    'TAGLIB_PRE_LOAD'     =>    'OT\\TagLib\\Article,OT\\TagLib\\Think',
        
    /* 主题设置 */
    'DEFAULT_THEME' =>  'wap',  // 默认模板主题名称
    'MOBLIE_THEME' =>  'wap',  // 默认手机模板主题名称
    'TMPL_LOAD_DEFAULTTHEME'=>'true',  // 如果找到不到则定位到默认主题
    
// 	'TMPL_ACTION_ERROR'     =>  'Public:error', // 默认错误跳转对应的模板文件
//     'TMPL_ACTION_SUCCESS'   =>  'Public:success', // 默认成功跳转对应的模板文件

    /* 数据缓存设置 */
    'DATA_CACHE_PREFIX' => 'onethink_', // 缓存前缀
    'DATA_CACHE_TYPE'   => 'File', // 数据缓存类型

    /* 文件上传相关配置 */
    'DOWNLOAD_UPLOAD' => array(
        'mimes'    => '', //允许上传的文件MiMe类型
        'maxSize'  => 5*1024*1024, //上传的文件大小限制 (0-不做限制)
        'exts'     => 'jpg,gif,png,jpeg,zip,rar,tar,gz,7z,doc,docx,txt,xml', //允许上传的文件后缀
        'autoSub'  => true, //自动子目录保存文件
        'subName'  => array('date', 'Y-m-d'), //子目录创建方式，[0]-函数名，[1]-参数，多个参数使用数组
        'rootPath' => './Uploads/Download/', //保存根路径
        'savePath' => '', //保存路径
        'saveName' => array('uniqid', ''), //上传文件命名规则，[0]-函数名，[1]-参数，多个参数使用数组
        'saveExt'  => '', //文件保存后缀，空则使用原后缀
        'replace'  => false, //存在同名是否覆盖
        'hash'     => true, //是否生成hash编码
        'callback' => false, //检测文件是否存在回调函数，如果存在返回文件信息数组
    ), //下载模型上传配置（文件上传类配置）

    /* 编辑器图片上传相关配置 */
    'EDITOR_UPLOAD' => array(
        'mimes'    => '', //允许上传的文件MiMe类型
        'maxSize'  => 2*1024*1024, //上传的文件大小限制 (0-不做限制)
        'exts'     => 'jpg,gif,png,jpeg', //允许上传的文件后缀
        'autoSub'  => true, //自动子目录保存文件
        'subName'  => array('date', 'Y-m-d'), //子目录创建方式，[0]-函数名，[1]-参数，多个参数使用数组
        'rootPath' => './Uploads/Editor/', //保存根路径
        'savePath' => '', //保存路径
        'saveName' => array('uniqid', ''), //上传文件命名规则，[0]-函数名，[1]-参数，多个参数使用数组
        'saveExt'  => '', //文件保存后缀，空则使用原后缀
        'replace'  => false, //存在同名是否覆盖
        'hash'     => true, //是否生成hash编码
        'callback' => false, //检测文件是否存在回调函数，如果存在返回文件信息数组
    ),

     /* 图片上传相关配置 */
    'PICTURE_UPLOAD' => array(
        'mimes'    => '', //允许上传的文件MiMe类型
        'maxSize'  => 2*1024*1024, //上传的文件大小限制 (0-不做限制)
        'exts'     => 'jpg,gif,png,jpeg', //允许上传的文件后缀
        'autoSub'  => true, //自动子目录保存文件
        'subName'  => array('date', 'Y-m-d'), //子目录创建方式，[0]-函数名，[1]-参数，多个参数使用数组
        'rootPath' => './Uploads/Picture/', //保存根路径
        'savePath' => '', //保存路径
        'saveName' => array('uniqid', ''), //上传文件命名规则，[0]-函数名，[1]-参数，多个参数使用数组
        'saveExt'  => '', //文件保存后缀，空则使用原后缀
        'replace'  => false, //存在同名是否覆盖
        'hash'     => true, //是否生成hash编码
        'callback' => false, //检测文件是否存在回调函数，如果存在返回文件信息数组
    ), //图片上传相关配置（文件上传类配置）

    /* 模板相关配置 */
    'TMPL_PARSE_STRING' => array(
        '__STATIC__' => __ROOT__ . '/Public/static',
        '__ADDONS__' => __ROOT__ . '/Public/' . MODULE_NAME . '/Addons',
        '__IMG__'    => __ROOT__ . '/Public/' . MODULE_NAME . '/a/imgs',
        '__CSS__'    => __ROOT__ . '/Public/' . MODULE_NAME . '/a/css',
        '__JS__'     => __ROOT__ . '/Public/' . MODULE_NAME . '/a/js',
        '__PLU__'     => __ROOT__ . '/Public/' . MODULE_NAME . '/plugins',
        '__HOME__'     => __ROOT__ . '/Public/' . MODULE_NAME ,
        '__MAT__'     => __ROOT__ . '/Public/' . MODULE_NAME.'/mat' ,
        '__LOGIN__'     => __ROOT__ . '/Public/' . MODULE_NAME.'/login' ,
        '__MOBILE__'     => __ROOT__ . '/Public/' . MODULE_NAME.'/mobile' ,
        '__H-W__'     => __ROOT__ . '/Public/Home/H_wap' ,
        
    ),

    /* SESSION 和 COOKIE 配置 */
    'SESSION_PREFIX' => 'zx_home', //session前缀
    'COOKIE_PREFIX'  => 'zx_home_', // Cookie前缀 避免冲突

    /**
     * 附件相关配置
     * 附件是规划在插件中的，所以附件的配置暂时写到这里
     * 后期会移动到数据库进行管理
     */
    'ATTACHMENT_DEFAULT' => array(
        'is_upload'     => true,
        'allow_type'    => '0,1,2', //允许的附件类型 (0-目录，1-外链，2-文件)
        'driver'        => 'Local', //上传驱动
        'driver_config' => null, //驱动配置
    ), //附件默认配置

    'ATTACHMENT_UPLOAD' => array(
        'mimes'    => '', //允许上传的文件MiMe类型
        'maxSize'  => 5*1024*1024, //上传的文件大小限制 (0-不做限制)
        'exts'     => 'jpg,gif,png,jpeg,zip,rar,tar,gz,7z,doc,docx,txt,xml', //允许上传的文件后缀
        'autoSub'  => true, //自动子目录保存文件
        'subName'  => array('date', 'Y-m-d'), //子目录创建方式，[0]-函数名，[1]-参数，多个参数使用数组
        'rootPath' => './Uploads/Attachment/', //保存根路径
        'savePath' => '', //保存路径
        'saveName' => array('uniqid', ''), //上传文件命名规则，[0]-函数名，[1]-参数，多个参数使用数组
        'saveExt'  => '', //文件保存后缀，空则使用原后缀
        'replace'  => false, //存在同名是否覆盖
        'hash'     => true, //是否生成hash编码
        'callback' => false, //检测文件是否存在回调函数，如果存在返回文件信息数组
    ), //附件上传配置（文件上传类配置）
    
    //支付宝配置参数
    'alipay_config'=>array(
    		'partner' =>'2088901332681362',   //这里是你在成功申请支付宝接口后获取到的PID；
    		'key'=>'6cmxx9tfy319fgfehf3w0hh3yc3wgr9p',//这里是你在成功申请支付宝接口后获取到的Key
    		'sign_type'=>strtoupper('MD5'),
    		'input_charset'=> strtolower('utf-8'),
    		'cacert'=> getcwd().'\\cacert.pem',
    		'transport'=> 'http',
    ),
    //以上配置项，是从接口包中alipay.config.php 文件中复制过来，进行配置；
    
    'alipay'   =>array(
    		//这里是卖家的支付宝账号，也就是你申请接口时注册的支付宝账号
    		'seller_email'=>'pay@xxx.com',
    
    		//这里是异步通知页面url，提交到项目的Pay控制器的notifyurl方法；
    		'notify_url'=>'http://www.xxx.com/Pay/notifyurl',
    
    		//这里是页面跳转通知url，提交到项目的Pay控制器的returnurl方法；
    		'return_url'=>'http://www.xxx.com/Pay/returnurl',
    
    		//支付成功跳转到的页面，我这里跳转到项目的User控制器，myorder方法，并传参payed（已支付列表）
    		'successpage'=>'User/myorder?ordtype=payed',
    
    		//支付失败跳转到的页面，我这里跳转到项目的User控制器，myorder方法，并传参unpay（未支付列表）
    		'errorpage'=>'User/myorder?ordtype=unpay',
    ),

);
