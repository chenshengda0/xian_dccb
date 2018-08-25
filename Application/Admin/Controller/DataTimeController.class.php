<?php
namespace Admin\Controller;

/**
 * 后台首页控制器
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
class DataTimeController extends AdminController {

    /**
     * 显示现有条目
     * 
     */
    public function index(){
    	$uid = is_login();
    	$cron = D('Cron');
    	$list = $cron->where('1=1')->select();
    	int_to_string ( $list );
    	
    	$this->assign ( '_list', $list );
    	$this->meta_title = '计划任务';
    	$this->display ();
    }
    
    /**
     * 添加条目
     *
     */
    public function add(){
    	if (IS_POST) {
    		$cron = D('Cron');
			$data['name']=I('name');
			$data['filename']=I('filename');
			$month=I('month');
			
			$weekday=I('weekday');
			$day=I('day');
			if($month!=-1&&$day==-1){
				$this->error ( '如果选择月份必须选择日期！', U ( 'add' ) );
			}
			if($weekday!=-1&&$day!=-1){
				if($weekday&&$day){
					$this->error ( '星期和日期不能同时选择！', U ( 'add' ) );
				}
			}
			$data['month'] = $month;
			$data['weekday']=$weekday;
			$data['day']=$day;
			$data['hour']=I('hour');
			$data['minute']=I('minute');
			if (!$cron->add($data)) {
				$this->error ( '添加规则失败！' );
			} else {
				$this->success ( '添加规则成功！', U ( 'index' ) );
			}
		} else {
			$this->assign("week_list",array(1=>'周一',2=>'周二',3=>'周三',4=>'周四',5=>'周五',6=>'周六',7=>'周日'));
			$this->meta_title = '新增任务';
			$this->display ();
		}
    }
    
    /**
     * 修改条目
     *
     */
    public function update(){
    	
    	$cronid=I('cronid');
    	$cron = D('Cron');
    	$info = array();
    	$info = $cron->field(true)->find($cronid);/* 获取数据 */
    	$this->assign ( 'info', $info );
    	$weekday=$cron->where("cronid=$cronid")->getField('weekday');
    	$day=$cron->where("cronid=$cronid")->getField('day');
    	$hour=$cron->where("cronid=$cronid")->getField('hour');
    	$month=$cron->where("cronid=$cronid")->getField('month');
    	$this->assign ( 'weekday', $weekday );
    	$this->assign ( 'month', $month );
    	$this->assign ( 'day', $day );
    	$this->assign ( 'hour', $hour );
    	if(IS_POST){
    		$data['month']=I('month');
	    	$data['name']=I('name');
	    	$data['weekday']=I('weekday');
	    	$data['day']=I('day');
	    	$data['hour']=I('hour');
	    	$data['minute']=I('minute');
	    	if($data['month']!=-1&&$data['day']==-1){
	    		$this->error ( '如果选择月份必须选择日期！', U ( 'add' ) );
	    	}
	    	if($data['weekday']!=-1&&$data['day']!=-1){
	    		if($data['weekday']&&$data['day']){
	    			$this->error ( '星期和日期不能同时选择！', U ( 'add' ) );
	    		}
	    	}
			$data = $cron->create();
			if($data){
				if($cron->save()!== false){
					$this->success('更新成功', U('index' ));
				} else {
					$this->error('更新失败');
				}
			} else {
				$this->error($cron->getError());
			}
		} else {
			$this->assign("week_list",array(1=>'周一',2=>'周二',3=>'周三',4=>'周四',5=>'周五',6=>'周六',7=>'周日'));
			$this->meta_title = '修改时间';
			$this->display ('add');
		}
    }

    /**
     * 删除条目
     *
     */
    public function delete(){
    	$cronid=I('cronid');
		$map = array('cronid' => array('in', $cronid) );
		if(M('Cron')->where($map)->delete()){
			$this->success('删除成功');
		} else {
			$this->error('删除失败！');
		}
	}
    
}
