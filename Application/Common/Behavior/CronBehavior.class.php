<?php
namespace Common\Behavior;
use Think\Behavior;
defined('THINK_PATH') or exit();

class CronBehavior extends Behavior{
    //行为执行入口
    public function run(&$param){
		
		$time=time();
		$cron = M("Cron")->where("available>0 AND nextrun<=$time")->order("nextrun")->find();
		
		if($cron) {

			$cron['filename'] = str_replace(array('..', '/', '\\'), '', $cron['filename']);
			$cronfile = 'Application/Common/lib/Cron/'.$cron['filename'];

			$cron['minute'] = explode(",", $cron['minute']);
			$this->setnextime($cron);
			//print_r($cronfile);
			@set_time_limit(5000);
			@ignore_user_abort(TRUE);
			
			

			if(!@include $cronfile) {
				return false;
			}
		}
    }
	
    /**
     * 设置下次运行时间
     * @param unknown $cron
     * @return boolean
     */
	private  function setnextime($cron) {

		if(empty($cron)) return FALSE;

		list($yearnow, $monthnow, $daynow, $weekdaynow, $hournow, $minutenow) = explode('-', gmdate('Y-m-d-w-H-i', time() + 8 * 3600));

			if($cron['weekday'] == -1) {
				if($cron['day'] == -1) {
					$firstday = $daynow;
					$secondday = $daynow + 1;
				} else {
					$firstday = $cron['day'];
					$secondday = $cron['day'] + gmdate('t', time() + 8 * 3600);
				}
			} else {
				$firstday = $daynow + ($cron['weekday'] - $weekdaynow);
				$secondday = $firstday + 6;
			}
		

		if($firstday < $daynow) {
			$firstday = $secondday;
		}

		if($firstday == $daynow) {
			$todaytime = self::todaynextrun($cron);
			if($todaytime['hour'] == -1 && $todaytime['minute'] == -1) {
				$cron['day'] = $secondday;
				$nexttime = self::todaynextrun($cron, 0, -1);
				$cron['hour'] = $nexttime['hour'];
				$cron['minute'] = $nexttime['minute'];
			} else {
				$cron['day'] = $firstday;
				$cron['hour'] = $todaytime['hour'];
				$cron['minute'] = $todaytime['minute'];
			}
		} else {
			$cron['day'] = $firstday;
			$nexttime = self::todaynextrun($cron, 0, -1);
			$cron['hour'] = $nexttime['hour'];
			$cron['minute'] = $nexttime['minute'];
		}

		if($cron['month']>0){
			$yearnow = $yearnow+1;
			$monthnow = $cron['month'];
		}
		$nextrun = @gmmktime($cron['hour'], $cron['minute'] > 0 ? $cron['minute'] : 0, 0, $monthnow, $cron['day'], $yearnow) - 8 * 3600;
		$data = array('lastrun' => time(), 'nextrun' => $nextrun);
		if(!($nextrun > time())) {
			$data['available'] = '0';
		}
		
		//print_r($data);
		//$this->db->where('cronid',$cron['cronid'])->update($this->cron_table, $data);
		M("Cron")->where("cronid=$cron[cronid]")->save($data);

		return true;
	}

	private  function todaynextrun($cron, $hour = -2, $minute = -2) {

		$hour = $hour == -2 ? gmdate('H', time() + 8 * 3600) : $hour;
		$minute = $minute == -2 ? gmdate('i', time() + 8 * 3600) : $minute;

		$nexttime = array();
		if($cron['hour'] == -1 && !$cron['minute']) {
			$nexttime['hour'] = $hour;
			$nexttime['minute'] = $minute + 1;
		} elseif($cron['hour'] == -1 && $cron['minute'] != '') {
			$nexttime['hour'] = $hour;
			if(($nextminute = self::nextminute($cron['minute'], $minute)) === false) {
				++$nexttime['hour'];
				$nextminute = $cron['minute'][0];
			}
			$nexttime['minute'] = $nextminute;
		} elseif($cron['hour'] != -1 && $cron['minute'] == '') {
			if($cron['hour'] < $hour) {
				$nexttime['hour'] = $nexttime['minute'] = -1;
			} elseif($cron['hour'] == $hour) {
				$nexttime['hour'] = $cron['hour'];
				$nexttime['minute'] = $minute + 1;
			} else {
				$nexttime['hour'] = $cron['hour'];
				$nexttime['minute'] = 0;
			}
		} elseif($cron['hour'] != -1 && $cron['minute'] != '') {
			$nextminute = self::nextminute($cron['minute'], $minute);
			if($cron['hour'] < $hour || ($cron['hour'] == $hour && $nextminute === false)) {
				$nexttime['hour'] = -1;
				$nexttime['minute'] = -1;
			} else {
				$nexttime['hour'] = $cron['hour'];
				$nexttime['minute'] = $nextminute;
			}
		}

		return $nexttime;
	}
	
	/**
	 * 下次运行的分钟
	 * @param unknown $nextminutes
	 * @param unknown $minutenow
	 * @return unknown|boolean
	 */
	private  function nextminute($nextminutes, $minutenow) {
		foreach($nextminutes as $nextminute) {
			if($nextminute > $minutenow) {
				return $nextminute;
			}
		}
		return false;
	}

}