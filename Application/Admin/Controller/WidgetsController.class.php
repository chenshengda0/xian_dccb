<?php
namespace Admin\Controller;

/**
 * 小部件控制
 */
class WidgetsController extends AdminController {
	

	public function listView($table_name,$list,$opt = array()){
		$return = array();
		if(!empty($list)){
			$lang = C('LANG');
			$lang_label = $lang[$table_name];
			$title = array();
			foreach ($list[0] as $key=>$v){
				$title[] = $lang_label[$key];
			}
			if(!empty($opt)){
				$title[] = '操作';
			}
			 
			foreach ($list[0] as &$v){
				$v['opt'] = $opt;
			}
			$return['title'] = $title;
			$return['data'] = $list;
		}
		return $return;
	}

}
