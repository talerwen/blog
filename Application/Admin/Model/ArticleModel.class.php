<?php
namespace Admin\Model;
use Think\Model;
class ArticleModel extends Model{
    protected $insertFields = 'title,content,acid';
    protected $updateFields = 'atid,title,content,acid';
    protected $_validate = array(
        array("title","require","文章标题不能为空",1,"regex",3),
        array("title","","文章标题已经存在",1,"unique",3),
        array("title","1,50","文章标题不能超过50个字符",1,"length",3),
        array("content","require","文章内容不能为空",1,"regex",3),
    );
    //添加前的钩子函数
    protected function _before_insert(&$data,$options)
    {
        $data['content'] = clean_xss($_POST['content']);
        $data['addtime'] = time();
    }

    // 修改前的钩子函数
    protected function _before_update(&$data, $options)
    {
        $data['content'] = clean_xss($_POST['content']);
        $data['savetime'] = time();
    }

    // 删除前的钩子函数
    protected function _before_delete($options)
    {
    }

    //列表,搜索
    public function search($pageSize=10)
    {
        $where = array();
        if(I('get.search')){
            $where['a.title'] = array('like','%'.I('get.search').'%');
            $data['search'] = I('get.search');
        }
        $count = $this->alias('a')->where($where)->join('left join __ARTICLE_CATEGORY__ b on a.acid=b.acid')->count(); //查询满足条件的总条数
        $p = getpage($count,$pageSize);
        $data['page'] = $p->show();
        $data['data'] = $this->alias('a')
            ->field('a.*,b.acname')
            ->where($where)
            ->join('left join __ARTICLE_CATEGORY__ b on a.acid=b.acid')
            ->order('a.atid desc')
            ->limit($p->firstRow.','.$p->listRows)
            ->select();
        return $data;
    }
}
