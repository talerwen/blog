<?php
namespace Admin\Controller;
use Think\Controller;
class IndexController extends PublicController{
	public function index(){
		parent::isLogin();
		$this->display();
	}	
}
