<?php
namespace Admin\Model;
use Think\Model;
class ArticleCategoryModel extends Model{
    protected $insertFields = 'acname,parentid';
    protected $updateFields = 'acid,acname,parentid';
    protected $_validate = array(
        array("acname","require","文章分类名称不能为空",1,"regex",3),
        array("acname","","文章分类名称已经存在",1,"unique",3),
        array("acname","1,20","文章分类名称不能超过20个字符",1,"length",3),
    );
    //添加前的钩子函数
    protected function _before_insert(&$data,$options)
    {
    }

    // 修改前的钩子函数
    protected function _before_update(&$data, $options)
    {
    }

    // 删除前的钩子函数
    protected function _before_delete($options)
    {
    }

    //列表,搜索
    public function search($pageSize=10)
    {
        $where = array();
        $count = $this->where($where)->count(); //查询满足条件的总条数
        $p = getpage($count);
        $data['page'] = $p->show();
        $data['data'] = $this
            ->field('*')
            ->where($where)
            ->order('acid desc')
            ->limit($p->firstRow.','.$p->listRows)
            ->select();
        return $data;
    }

    // 获取
    public function getTree(){
        $data = $this->select();
        return $this->_getTree($data);
    }
    private function _getTree($data,$parentid=0,$level=0,$clear=FALSE){
        static $_re = array();
        IF(!$clear){
            $_re = array();
        }
        foreach($data as $k => $v){
            if($v['parentid'] == $parentid){
                $v['level'] = $level;
                $_re[] = $v;
                $this->_getTree($data,$v['acid'],$level+1,TRUE);
            }
        }
        return $_re;
    }

    // 删除该分类下所有子分类时使用
    public function getChildren($catid){
        $data = $this->select();
        return $this->_getChildren($data,$catid);
    }

    private function _getChildren($data,$catid,$clear = TRUE){
        static $_re = array();
        if(!$clear){
            $_re = array();
            ;
        }
        foreach($data as $k => $v){
            if($v['parentid'] == $catid){
                $_re[] = $v['acid'];
                $this->_getChildren($data,$v['acid'],TRUE);
            }
        }
        return $_re;
    }

    public function getOneLevelCategory(){
        return $this->where(['parentid'=>0])->select();
    }
}
