<?php
namespace Admin\Controller;

/**
 * 后台用户控制器
 */
class ManagerController extends AdminController
{

    /**
     * 管理员列表
     */
    public function index(){
        $mid = is_admin_login();
        if(is_administrator($mid)){
            $map['status'] = array('egt', 0);
            $list = $this->lists('Manager', $map);
        }else {
            $list = M('Manager')->where(array('mid'=>$mid))->select();
        }
        int_to_string($list);
        $this->assign('_list', $list);
        $this->meta_title = '用户信息';
        $this->display();
    }



    /**
     * 修改管理员名初始化
     */
    public function updateMname(){
        $mid = is_admin_login();
        if(IS_POST){
            //获取参数
            $nickname = I('post.nickname');
            $password = I('post.password');
            empty($nickname) && $this->error('请输入管理员名');
            empty($password) && $this->error('请输入密码');
            
            //密码验证
            $res = D('Manager')->checkPwd($password,$mid);
            if(!$res){
                $this->error('密码不正确');
            }
            $data['mid'] = $mid;
            $data['mname'] = $nickname;
            $res = D('Manager')->updateField($data);
            
            
            if ($res>0) {
                $user = session('admin_auth');
                $user['mname'] = $data['mname'];
                session('admin_auth', $user);
                session('admin_auth_sign', data_auth_sign($user));
                $this->success('修改管理名成功',U('Index/index'));
            } else {
                $this->error($this->showRegError($res));
            }
        }else{
            $mname = M('Manager')->where(array('mid'=>$mid))->getField('mname');
            $this->assign('nickname', $mname);
            $this->meta_title = '修改管理员名';
            $this->display();
        }
    }

    /**
     * 修改密码初始化
     */
    public function updatePassword(){
        if(IS_POST){
            //获取参数
            $password = I('post.old');
            empty($password) && $this->error('请输入原密码');
            $data['password'] = I('post.password');
            empty($data['password']) && $this->error('请输入新密码');
            $repassword = I('post.repassword');
            empty($repassword) && $this->error('请输入确认密码');
            
            if ($data['password'] !== $repassword) {
                $this->error('您输入的新密码与确认密码不一致');
            }
            
            //密码验证
            $res = D('Manager')->checkPwd($password,UID);
            if(!$res){
                $this->error('密码不正确');
            }
            $data['mid'] = UID;
            $res = D('Manager')->updateField($data);
            if ($res>0) {
            	session('admin_auth', null);
            	session('admin_auth_sign', null);
                $this->success('修改密码成功!',U('Index/index'));
            } else {
                $this->error($this->showRegError($res));
            }
        }else{
            $this->meta_title = '修改密码';
            $this->display();
        }
        
    }

    /**
     * 添加管理员
     */
    public function add(){
        if (IS_POST) {
            /*接受post数据*/
            $data['mname'] = $username = trim(I('post.username'));
            $data['password'] = $password = trim(I('post.password'));
            $data['repassword'] = $repassword = trim(I('post.repassword'));
            $data['email'] = $email = trim(I('post.email'));
            /* 检测密码 */
            if ($password != $repassword) {
                $this->error('密码和重复密码不一致！');
            }

            $mid = D('Manager')->register($data);
            if ($mid>0) { //注册成功
                $this->success('用户添加成功！', U('index'));

            } else { //注册失败，显示错误信息
                $this->error($this->showRegError($mid));
            }
        } else {
            $this->meta_title = '添加管理员';
            $this->display();
        }
    }

    public function changeGroup(){
    
        if ($_POST['do'] == 1) {
            //清空group
            $aAll = I('post.all', 0, 'intval');
            $aUids = I('post.uid', array(), 'intval');
            $aGids = I('post.gid', array(), 'intval');
    
            if ($aAll) {//设置全部用户
                $prefix = C('DB_PREFIX');
                D('')->execute("TRUNCATE TABLE {$prefix}auth_group_access");
                $aUids = M('Manager')->getField('mid', true);
    
            } else {
                M('AuthGroupAccess')->where(array('uid' => array('in', implode(',', $aUids))))->delete();;
            }
            foreach ($aUids as $uid) {
                foreach ($aGids as $gid) {
                    M('AuthGroupAccess')->add(array('uid' => $uid, 'group_id' => $gid));
                }
            }
    
    
            $this->success('成功。');
        } else {
            $aId = I('post.id', array(), 'intval');
    
            foreach ($aId as $uid) {
                $user[] = query_user(array('space_link', 'uid'), $uid);
            }
    
    
            $groups = M('AuthGroup')->select();
            $this->assign('groups', $groups);
            $this->assign('users', $user);
            $this->display();
        }
    
    }
    
    /**
     * 管理员状态修改
     */
    public function changeStatus($method = null){
        $id = array_unique((array)I('id', 0));
        if (in_array(C('USER_ADMINISTRATOR'), $id)) {
            $this->error("不允许对超级管理员执行该操作!");
        }
        $id = is_array($id) ? implode(',', $id) : $id;
        if (empty($id)) {
            $this->error('请选择要操作的数据!');
        }
        $map['mid'] = array('in', $id);
        switch (strtolower($method)) {
            case 'forbiduser':
               // M('Manager')->where($map)->setField('status',0);
                $this->forbid('Manager',$map);
               //$this->success('该管理员禁用成功');
                break;
            case 'resumeuser':
               // M('Manager')->where($map)->setField('status',1);
                $this->resume( 'Manager',$map);
                //$this->success('该管理员启用成功');
                break;
            case 'deleteuser':
                 //M('Manager')->where($map)->setField('status',-1);
                $this->delete('Manager', $map);
                break;
            default:
                $this->error('参数非法');
        }
    }
    
    /**
     * 获取用户注册错误信息
     * @param  integer $code 错误编码
     * @return string        错误信息
     */
    private function showRegError($code = 0){
        switch ($code) {
            case -1:
                $error = '用户名长度不合安全规则！';
                break;
            case -2:
                 $error = '用户名被占用！';
                break;
           case -3:
                $error = '用户名在会员中已存在！';
                break;
            case -10:
                $error = '密码长度不合安全规则！';
                break;
            case -11:
                $error = '两次输入的密码不一致！';
                break;
            case -12:
                $error = '密码不能为纯数字！';
                break;
           case -20:
                $error = '邮箱格式不正确！';
                break;
            default:
                $error = '未知错误';
        }
        return $error;
    }

}
