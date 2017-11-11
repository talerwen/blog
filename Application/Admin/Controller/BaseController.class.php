<?php
namespace Admin\Controller;
use Think\Controller;
use Think\Model;
class BaseController extends Controller{
    // 列表页面
    public function login(){
        $this->display();
    }
}
