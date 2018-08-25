<?php
namespace Common\Model;
use Think\Model;

/**
 * 奖金规则模型
 */

class LiuyanModel extends Model {
	  /* 自动验证规则 */
    protected $_validate = array(
        array('title', 'require', '请输入留言标题', self::EXISTS_VALIDATE, 'regex', self::MODEL_INSERT),
        array('content', 'require', '请输入留言内容', self::EXISTS_VALIDATE, 'regex', self::MODEL_INSERT),
        array('reply', 'require', '请输入回复内容', self::EXISTS_VALIDATE, 'regex', self::MODEL_UPDATE),
    );

    /* 自动完成规则 */
    protected $_auto = array(
        array('status', 0, self::MODEL_INSERT, 'string'),
        array('status', 1, self::MODEL_UPDATE, 'string'),
    	array('create_time', 'time', self::MODEL_INSERT, 'function'),
        array('reply_time', 'time', self::MODEL_UPDATE, 'function'),
    );
}
