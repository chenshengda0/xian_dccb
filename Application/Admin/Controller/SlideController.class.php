<?php
namespace Admin\Controller;

/**
 * 后台订单控制器
  * @author 烟消云散 <1010422715@qq.com>
 */
class SlideController extends AdminController {

    /**
     * 订单管理
     * author 烟消云散 <1010422715@qq.com>
     */
    public function index(){
        /* 查询条件初始化 */
	
       $map  = array('status' => 1);
        if(IS_GET){
        	$title=trim(I('get.title'));
		 	$map['title'] = array('like',"%{$title}%");
       		$list   =   M("Slide")->where($map)->field(true)->order('id desc')->select();}
     	else{ 
		 $list = $this->lists('Slide', $map,'id desc');
	 	}

        $this->assign('list', $list);
        // 记录当前列表页的cookie
        Cookie('__forward__',$_SERVER['REQUEST_URI']);
        
        $this->meta_title = '首页幻灯片管理';
        $this->display();
    }

  /* 编辑幻灯片 */
    public function edit($id = null, $pid = 0){
        $slide = D('slide');
        if(IS_POST){ //提交表单
            if(false !== $slide->update()){				
                $this->success('编辑成功！', U('index'));
            } else {
                $error = $slide->getError();
                $this->error(empty($error) ? '未知错误！' : $error);
            }
        } else {     
            
            $info = $id ? $slide->info($id) : '';
            $this->assign('info',$info);          
            $this->meta_title = '编辑幻灯片';
            $this->display();
        }
    }


    /**
     * 新增幻灯片
     */
    public function add(){
        $slide = D('slide');
        if(IS_POST){ //提交表单			
            if(false !== $slide->update()){
		
                $this->success('新增成功！', U('index'));
            } else {
                $error = $slide->getError();
                $this->error(empty($error) ? '未知错误！' : $error);
            }
        } else {           
			$this->assign('list',$list);
            $this->assign('info', null);         
            $this->meta_title = '新增幻灯片';           
            $this->display('edit');
        }
    }
/**
 * 删除幻灯片
 */
 public function del(){   
 	if(IS_POST){
             $ids = I('post.id');
            $order = M("slide");			
            if(is_array($ids)){
                  foreach($ids as $id){		
                       $order->where("id='$id'")->delete();						
                	}
            }
           $this->success("删除成功！");
        }else{
            $id = I('get.id');
            $db = M("slide");
            $status = $db->where("id='$id'")->delete();
            if ($status){
                $this->success("删除成功！");
            }else{
                $this->error("删除失败！");
            }
        }      
    }

}