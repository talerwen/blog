<?php 
namespace Admin\Controller;
use Think\Controller;
use Think\Model;
class ArticleCategoryController extends PublicController{
    // 列表页面
    public function index(){
        parent::isLogin();
        $model = D('ArticleCategory');
        $this->assign('data',$model->getTree());
        $this->display();
    }

    // 添加
    public function add(){
        parent::isLogin();
        if(IS_POST){
            $model = D('ArticleCategory');
            if($model->create(I ('post.'),1)){
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
            $model = D('ArticleCategory');
            if($model->create(I('post.'),2)){
                if($model->save() !== FALSE){
                    $this->success('修改成功!',U('index'));
                    exit;
                }
            }
            $this->error($model->getError());
        }
        $model = D('ArticleCategory');
        $data = $model->find($id);
        $this->assign($data);
        $this->assign('data',$model->getTree());
        $this->display();
    }

    // 删除
    public function delete(){
        parent::isLogin();
        $model = D('ArticleCategory');
        if($model->delete(I('get.id',0)) !== FALSE){
            $this->success('删除成功!',U('index'));
            exit;
        }
        $this->error($model->getError());
    }
}
