<?php
namespace Admin\Controller;

/**
 * 后台配置控制器
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
class ConfigController extends AdminController {

    /**
     * 配置管理
     * @author 麦当苗儿 <zuojiazi@vip.qq.com>
     */
    public function index(){
        /* 查询条件初始化 */
        $map = array();
        $map  = array('status' => 1);
        if(isset($_GET['group'])){
            $map['group']   =   I('group',0);
        }
        if(isset($_GET['name'])){
            $map['name']    =   array('like', '%'.(string)I('name').'%');
        }

        $list = $this->lists('Config', $map,'sort,id');
        // 记录当前列表页的cookie
        Cookie('__forward__',$_SERVER['REQUEST_URI']);

        $this->assign('group',C('CONFIG_GROUP_LIST'));
        $this->assign('group_id',I('get.group',0));
        $this->assign('list', $list);
        $this->meta_title = '配置管理';
        $this->display();
    }

    /**
     * 新增配置
     * @author 麦当苗儿 <zuojiazi@vip.qq.com>
     */
    public function add(){
        if(IS_POST){
            $Config = D('Config');
            $data = $Config->create();
            if($data){
                if($Config->add()){
                    S('DB_CONFIG_DATA',null);
                    $this->success('新增成功', U('index'));
                } else {
                    $this->error('新增失败');
                }
            } else {
                $this->error($Config->getError());
            }
        } else {
            $this->meta_title = '新增配置';
            $this->assign('info',null);
            $this->display('edit');
            
        }
    }

    /**
     * 编辑配置
     * @author 麦当苗儿 <zuojiazi@vip.qq.com>
     */
    public function edit($id = 0){
        if(IS_POST){
            $Config = D('Config');
            $data = $Config->create();
            if($data){
                if($Config->save()){
                    S('DB_CONFIG_DATA',null);
                    //记录行为
                    action_log('update_config','config',$data['id'],UID);
                    $this->success('更新成功', Cookie('__forward__'));
                } else {
                    $this->error('更新失败');
                }
            } else {
                $this->error($Config->getError());
            }
        } else {
            $info = array();
            /* 获取数据 */
            $info = M('Config')->field(true)->find($id);

            if(false === $info){
                $this->error('获取配置信息错误');
            }
            $this->assign('info', $info);
            $this->meta_title = '编辑配置';
            $this->display();
        }
    }

    /**
     * 批量保存配置
     * @author 麦当苗儿 <zuojiazi@vip.qq.com>
     */
    public function save($config){
        if($config && is_array($config)){
            $Config = M('Config');
            foreach ($config as $name => $value) {
                $map = array('name' => $name);
                $Config->where($map)->setField('value', $value);
            }
        }
        S('DB_CONFIG_DATA',null);
        $this->success('保存成功！');
    }

    /**
     * 删除配置
     * @author 麦当苗儿 <zuojiazi@vip.qq.com>
     */
    public function del(){
        $id = array_unique((array)I('id',0));

        if ( empty($id) ) {
            $this->error('请选择要操作的数据!');
        }

        $map = array('id' => array('in', $id) );
        if(M('Config')->where($map)->delete()){
            S('DB_CONFIG_DATA',null);
            //记录行为
            action_log('update_config','config',$id,UID);
            $this->success('删除成功');
        } else {
            $this->error('删除失败！');
        }
    }

    // 获取某个标签的配置参数
    public function group() {
        $id     =   I('get.id',1);
        $type   =   C('CONFIG_GROUP_LIST');
        if(!APP_DEBUG){
        	unset($type['4']);
        }
        unset($type['3'],$type['5'],$type['7'],$type['8']);
        $list   =   M("Config")->where(array('status'=>1,'group'=>$id))->field('id,name,title,extra,value,remark,type')->order('sort')->select();
        if($list) {
            $this->assign('list',$list);
        }
        $this->assign('id',$id);
        $this->assign('type',$type);
        $this->meta_title = $type[$id].'设置';
        $this->display();
    }

    /**
     * 配置排序
     * @author huajie <banhuajie@163.com>
     */
    public function sort(){
        if(IS_GET){
            $ids = I('get.ids');

            //获取排序的数据
            $map = array('status'=>array('gt',-1));
            if(!empty($ids)){
                $map['id'] = array('in',$ids);
            }elseif(I('group')){
                $map['group']	=	I('group');
            }
            $list = M('Config')->where($map)->field('id,title')->order('sort asc,id asc')->select();

            $this->assign('list', $list);
            $this->meta_title = '配置排序';
            $this->display();
        }elseif (IS_POST){
            $ids = I('post.ids');
            $ids = explode(',', $ids);
            foreach ($ids as $key=>$value){
                $res = M('Config')->where(array('id'=>$value))->setField('sort', $key+1);
            }
            if($res !== false){
                $this->success('排序成功！',Cookie('__forward__'));
            }else{
                $this->eorror('排序失败！');
            }
        }else{
            $this->error('非法请求！');
        }
    }
    
    /**
     * 颜色配置
     */
    public function rankColor($type=0){
    	$rankcolor = M('RankColor');
    	if(IS_POST){
    		$color = I('color');
    		$ftcolor = I('ftcolor');
    		$rank = I('rank');
    		$len = count($color);
    		for($i=0;$i<$len;++$i){
    			if(!empty($color[$i])||(!empty($ftcolor[$i]))){
    				if(!empty($color[$i])){
    					$data['bgcolor'] = $color[$i];
    				}
    				if(!empty($ftcolor[$i])){
    					$data['fontcolor'] = $ftcolor[$i];
    				}
    				$data['create_time'] = time();
    				$res = $rankcolor->where(array('rank'=>$rank[$i],'types'=>$type))->getField('id');
    				if($res){
    					$rankcolor->where(array('id'=>$res))->save($data);
    				}else{
    					$data['rank'] = $rank[$i];
    					$data['types'] = $type;
    					$rankcolor->add($data);
    				}
    			}
    		}
    		$this->success('配置成功');
    	}else {
    		if($type==0){
              	$aa = '1:100,2:100,3:100,4:100,5:100';
    			$reg_type_arr = user_level_bonus_format($aa);
    			foreach ($reg_type_arr as &$v){
    				$v['4'] = get_userrank($v['1']);
    				$v['5'] = $rankcolor->where(array('rank'=>$v['1'],'types'=>$type))->getField('bgcolor');
    				$v['6'] = $rankcolor->where(array('rank'=>$v['1'],'types'=>$type))->getField('fontcolor');
    			}
    		}else{
    			$r_type = get_bonus_rule('update_regtype');
    			$arr[0] = array('1'=>'0');
    			$reg_type_arr = array_merge($arr,user_level_bonus_format($r_type));
    			foreach ($reg_type_arr as &$v){
    				$v['4'] = get_regtype($v['1']);
    				$v['5'] = $rankcolor->where(array('rank'=>$v['1'],'types'=>$type))->getField('bgcolor');
    				$v['6'] = $rankcolor->where(array('rank'=>$v['1'],'types'=>$type))->getField('fontcolor');
    			}
    		}
    		$this->assign('reg_type',$reg_type_arr);
    		$this->assign('type',$type);
    		$this->meta_title='颜色配置';
    		$this->display();
    	}
    }
    
    /**
     * 会员职务颜色配置
     */
    public function regtypeColor(){
    	$rankcolor = M('RankColor');
    	if(IS_POST){
    		$color = I('color');
    		$ftcolor = I('ftcolor');
    		$rank = I('rank');
    		$len = count($color);
    		for($i=0;$i<$len;++$i){
    			if(!empty($color[$i])||(!empty($ftcolor[$i]))){
    				if(!empty($color[$i])){
    					$data['bgcolor'] = $color[$i];
    				}
    				if(!empty($ftcolor[$i])){
    					$data['fontcolor'] = $ftcolor[$i];
    				}
    				$data['create_time'] = time();
    				$res = $rankcolor->where(array('rank'=>$rank[$i],'types'=>1))->getField('id');
    				if($res){
    					$rankcolor->where(array('id'=>$res))->save($data);
    				}else{
    					$data['rank'] = $rank[$i];
    					$rankcolor->add($data);
    				}
    			}
    		}
    		$this->success('配置成功');
    	}else {
    		$r_type = get_bonus_rule('update_regtype');
    		$arr[0] = array('1'=>'0');
    		$reg_type_arr = array_merge($arr,user_level_bonus_format($r_type));
    		foreach ($reg_type_arr as &$v){
    			$v['4'] = get_regtype($v['1']);
    			$v['5'] = $rankcolor->where(array('rank'=>$v['1'],'types'=>1))->getField('bgcolor');
    			$v['6'] = $rankcolor->where(array('rank'=>$v['1'],'types'=>1))->getField('fontcolor');
    		}
    
    
    		$this->assign('reg_type',$reg_type_arr);
    		$this->meta_title='职务颜色配置';
    		$this->display();
    	}
    }
}
