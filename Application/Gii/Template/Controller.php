namespace <?php echo $moduleName; ?>\Controller;
use Think\Controller;
use Think\Model;
class <?php echo $mvcName; ?>Controller extends Controller{
    // 列表页面
    public function index(){
        $model = D('<?php echo $mvcName; ?>');
        $data = $model->search();
        $this->assign($data);
        $this->display();
    }

    // 添加
    public function add(){
        if(IS_POST){
            $model = D('<?php echo $mvcName; ?>');
            if($model->create(I('post.'),1)){
                if($id = $model->add()){
                    $this->success('添加成功！',U('index'));
                }
                exit;
            }
            $this->error($model->getError());
        }
        $this->display();
    }

    // 编辑
    public function edit(){
        $id = I('get.id');
        if(IS_POST){
            $model = D('<?php echo $mvcName; ?>');
            if($model->create(I('post.'),2)){
                if($model->save() !== FALSE){
                    $this->success('修改成功!',U('index'));
                    exit;
                }
            }
            $this->error($model->getError());
        }
        $model = M('<?php echo $mvcName; ?>');
        $data = $model->find($id);
        $this->assign($data);
        $this->display();
    }

    // 删除
    public function delete(){
        $model = D('<?php echo $mvcName; ?>');
        if($model->delete(I('get.id',0)) !== FALSE){
            $this->success('删除成功!',U('index'));
            exit;
        }
        $this->error($model->getError());
    }
}
