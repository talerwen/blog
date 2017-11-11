<?php
namespace Admin\Controller;
use Think\Controller;
class PublicController extends Controller{
    //列表页面
    public function login(){
        if(IS_POST){
            $model = D('Admin');
            if($model->validate($model->_login)->create()){
                if($model->loginof(I('post.'))){
                    $this->success('登录成功',U('Index/index'));
                    exit;
                }
            }
            $this->error($model->getError());
        }
        $this->display();
    }

    protected function isLogin(){
		if($_SESSION['uid']['aid'] <=0){
            $this->redirect('Public/login');
        }
    }

    public function logout(){
        session_destroy();
        $this->success('退出成功', U('Index/index'));
    }
}
