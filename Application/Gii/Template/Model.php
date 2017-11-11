namespace <?php echo $moduleName; ?>\Model;
use Think\Model;
class <?php echo $mvcName; ?>Model extends Model{
    <?php $data_iu = $this->getData(); ?>
    protected $insertFields = '<?php echo $data_iu['insert']; ?>';
    protected $updateFields = '<?php echo $data_iu['update']; ?>';
    protected $_validate = array(
        <?php echo $this->getVerifyArray(); ?>
    );
    //添加前的钩子函数
    protected function _before_insert(&$data,$options)
    {
        <?php echo $this->getBeforeInsert(); ?>
    }

    // 修改前的钩子函数
    protected function _before_update(&$data, $options)
    {
        <?php echo $this->getBeforeUpdate(); ?>
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
            ->order('<?php echo $this->pk; ?> desc')
            ->limit($p->firstRow.','.$p->listRows)
            ->select();
        return $data;
    }
}
