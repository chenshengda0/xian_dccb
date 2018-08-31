<?php
namespace Admin\Controller;

/**
 * 商城配置类
 * @package Admin\controller
 * @author bobyao
 */
class ShopConfigController extends AdminController
{

    protected $Config;

    function _initialize()
    {
        $this->Config = D('Config');
        parent::_initialize();
    }
    
    
    /**
     * 批量保存配置
     */
    public function save($config){
    	if($config && is_array($config)){
    		foreach ($config as $name => $value) {
    			$map = array('name' => $name);
    			$this->Config->where($map)->setField('value', $value);
    		}
    	}
    	S('DB_CONFIG_DATA',null);
    	$this->success('保存成功！');
    }
    // 获取某个标签的配置参数
    public function group() {
    	$list   =   $this->Config->where(array('status'=>1,'group'=>8))->field('id,name,title,extra,value,remark,type')->order('sort')->select();
    	if($list) {
    		$this->assign('list',$list);
    	}
    	$this->meta_title = '商城设置';
    	$this->display();
    }
}
