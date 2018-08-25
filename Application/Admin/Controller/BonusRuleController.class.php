<?php
namespace Admin\Controller;

/**
 * 奖金规则
 * @author adolf
 *
 */
class BonusRuleController extends AdminController {
	/**
	 * 奖金规则设置
	 */
	public function bonusRule($id = 1){
	if(IS_POST){
			$BonusRole = D('BonusRule');
			$data = $BonusRole->create();
			if($data){
				if($BonusRole->save()){
					action_log('update_BonusRule', 'BonusRule', $data['id'], UID);
					S('BONUS_RULE',NULL);
					$rule =   M('BonusRule')->where(array('id'=>$id))->find();
					S('BONUS_RULE',$rule);
					
					$bonus_rule=M('bonus_rule')->where('id=1')->find();
					$arr1=$this->change($bonus_rule['level_money']);//报单金额
					$arr2=$this->change($bonus_rule['back_money']);//返点金额
					$arr3=$this->change($bonus_rule['leader_money']);//领导奖
                    $arr4=$this->change($bonus_rule['divid_money']);//分红奖
					foreach($arr1 as $k=>$v){
						$arr[$k-1]['rank']=$k;
						$arr[$k-1]['bill_money']=$v;
						$arr[$k-1]['back_money']=$arr2[$k];
						$arr[$k-1]['leader_rate']=$arr3[$k];
                        $arr[$k-1]['divid_money']=$arr4[$k];
					}
					$row=M('level_rate')->select();
					if($row){
						M()->execute('truncate table zx_level_rate');
					}
					M('level_rate')->addAll($arr);
					
					$this->success('更新成功');
				} else {
					$this->error('更新失败');
				}
			} else {
				$this->error($BonusRole->getError());
			}
		} else {
			/* 获取数据 */
			$info = M('BonusRule')->find($id);

			if(empty($info)){
				$this->error('获取后台菜单信息错误');
			}
			$this->assign('info', $info);
			$this->meta_title = '奖金管理';
			$this->display();
		}
	}
	public function change($str){
		$arr1=explode(',', $str);
		foreach($arr1 as $v){
			$arr2=explode(':', $v);
			$arr[$arr2[0]]=$arr2[1];
		}
		return $arr;
	}

	
}