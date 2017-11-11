<?php
namespace Admin\Model;
use Think\Model;
class AdminModel extends Model{
    protected $insertFields = 'aname,passwd';
    protected $updateFields = 'aid,aname,passwd';
    protected $_validate = array(
        array('aname','require','管理员名称不能为空',1,'regex',3),
        array('aname','1,30','用户名不能超过30个字符',1,'length',3),
        array('passwd','require','密码不能为空',1,'regex',1),
        array('aname', '', '管理员名称已经存在！', 1, 'unique', 3),
    );
    public $_login = array(
        array('aname','require','用户名必填！',1,'regex'), //默认情况下用正则进行验证
        array('passwd','require','密码不能为空',1,'regex'), // 自定义函数验证密码格式
    );

    //添加前的钩子函数
    protected function _before_insert(&$data,$options)
    {
        $data['passwd'] = md5($data['passwd'].C('salt'));
        $data['addtime'] = time();
    }

    // 修改前的钩子函数
    protected function _before_update(&$data, $options)
    {
        if($data['passwd']){
            $data['passwd'] = md5($data['passwd'].C('salt'));
        }else{
            unset($data['passwd']);
        }
        $data['savetime'] = time();
    }

    // 删除前的钩子函数
    protected function _before_delete($options)
    {
        if($options['where']['aid'] == 1){
            $this->error = '超级管理员不能删除';
            return FALSE;
        }
    }

    //列表,搜索
    public function search($pageSize=10)
    {
        $where = array();
        $count = $this->where($where)->count(); //查询满足条件的总条数
        $p = getpage($count);
        $data['page'] = $p->show();
        $data['data'] = $this
            ->field('aid,aname,addtime,savetime')
            ->where($where)
            ->order('aid desc')
            ->limit($p->firstRow.','.$p->listRows)
            ->select();
        return $data;
    }

    //判断登录
    public function loginof($data){
        $where['aname'] = $data['aname'];
        $where['passwd'] = md5($data['passwd'].C('salt'));
        $re = $this->where($where)->find();
        if($re){
            $_SESSION['uname'] = $data['username'];
            $_SESSION['uid'] = $re;
            return true;
        }else{
            $this->error = '用户或者密码错误!';
            return false;
        }
    }
}
