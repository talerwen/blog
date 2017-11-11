<?php 
namespace Admin\Controller;
use Think\Controller;
use Think\Model;
class ArticleController extends PublicController{
    // 列表页面
    public function index(){
        parent::isLogin();
        C('TOKEN_ON',false);
        $model = D('Article');
        $data = $model->search(10,false);
        $this->assign($data);
        $this->display();
    }

    // 添加
    public function add(){
        parent::isLogin();
        if(IS_POST){
            $model = D('Article');
            if($model->create(I('post.'),1)){
                if($id = $model->add()){
                    $this->success('添加成功！',U('index'));
                }
                exit;
            }
            $this->error($model->getError());
        }
        $model = D('ArticleCategory');
        $this->assign('data',$model->getTree());
        $this->display();
    }

    // 编辑
    public function edit(){
        parent::isLogin();
        $id = I('get.id');
        if(IS_POST){
            $model = D('Article');
            if($model->create(I('post.'),2)){
                if($model->save() !== FALSE){
                    $this->success('修改成功!',U('index'));
                    exit;
                }
            }
            $this->error($model->getError());
        }
        $model = M('Article');
        $data = $model->find($id);
        $this->assign($data);

        $model = D('ArticleCategory');
        $this->assign('data',$model->getTree());

        $this->display();
    }
    
    public function go_public(){
        parent::isLogin();
        $id = I('post.id');
        $model = D('Article');
        $re = $model->go_public($id);
        if($re){
            $result_data = ['status'=>1];
        }else{
            $result_data = ['status'=>0];
        }
        $this->ajaxReturn($result_data);
    }

    // 删除
    public function delete(){
        parent::isLogin();
        $model = D('Article');
        if($model->delete(I('get.id',0)) !== FALSE){
            $this->success('删除成功!',U('index'));
            exit;
        }
        $this->error($model->getError());
    }
}
