<?php
namespace Addons\SiteStat;
use Common\Controller\Addon;

/**
 * 系统环境信息插件
 * @author thinkphp
 */

class SiteStatAddon extends Addon{

    public $info = array(
        'name'=>'SiteStat',
        'title'=>'站点统计信息',
        'description'=>'统计站点的基础信息',
        'status'=>1,
        'author'=>'thinkphp',
        'version'=>'0.2'
    );

    public function install(){
        return true;
    }

    public function uninstall(){
        return true;
    }

    //实现的AdminIndex钩子方法
    public function AdminIndex($param){
        $config = $this->getConfig();
        $this->assign('addons_config', $config);
		$map['status'] = 1;
        if($config['display']){
            $info['newuser']		=	M('Member')->where($map)->count();
            $info['userall']		=	M('Ucenter_member')->count();
            $map['touser'] = 0;
            $info['message']	=	M('Liuyan')->where($map)->count();
            $info['wd']	=	M('Withdrawal')->where($map)->count();
            $info['order']		=	M('ShopOrder')->where($map)->count();
            $this->assign('info',$info);
            $this->display('info');
        }
    }
}