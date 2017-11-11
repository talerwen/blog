<?php
// 本类由系统自动生成，仅供测试用途
namespace Home\Controller;
use Think\Controller;
class IndexController extends Controller {
    public function index(){
		$ArticleModel = new \Admin\Model\ArticleModel();
		$this->assign('article_list',$ArticleModel->search(3)['data']);
		$ArticleCategoryModel = new \Admin\Model\ArticleCategoryModel();
		$this->assign('one_level_cat',$ArticleCategoryModel->getOneLevelCategory());
		C('TOKEN_ON',false);
		$hot = M('Article')->field('atid,title,addtime')->limit(3)->order('click_num desc')->select();
		$this->assign('article_hot',$hot);

    	$this->display();
	}

	// 标题列表页面
	public function lst(){
		$ArticleModel = new \Home\Model\ArticleModel();
		$data = $ArticleModel->search(['is_public'=>true]);
		$this->assign('search',$data['search']);
		$this->assign('article_list',$data['data']);
		$this->assign('page',$data['page']);
		C('TOKEN_ON',false);
		$ArticleCategoryModel = new \Admin\Model\ArticleCategoryModel();
		$this->assign('one_level_cat',$ArticleCategoryModel->getOneLevelCategory());

		$hot = M('Article')->field('atid,title,addtime')->limit(3)->order('click_num desc')->select();
		$this->assign('article_hot',$hot);

		$this->display();
	}

	// 单篇文章页面
	public function art(){
		if(IS_GET){
			$id = I('get.id')+0;
			$data = M('Article')->where(['atid'=>$id])->find();
			$db = M();
			$prev_id = $db->query("select atid from bg_article where atid < {$id} order by atid desc limit 1 ")[0]['atid'];
			$next_id = $db->query("select atid from bg_article where atid > {$id} order by atid asc limit 1 ")[0]['atid'];
			$prev_id = $prev_id ? $prev_id : 0;
			$next_id = $next_id ? $next_id : 0;
			$data = M('Article')->where(['atid'=>$id])->find();
			$this->assign($data);
			$this->assign('prev_id',$prev_id);
			$this->assign('next_id',$next_id);

			C('TOKEN_ON',false);

			$ArticleModel = new \Admin\Model\ArticleModel();
			$this->assign('article_list',$ArticleModel->search(3)['data']);
			$hot = M('Article')->field('atid,title,addtime')->limit(3)->order('click_num desc')->select();
			M('Article')->where(['atid'=>$id])->setInc('click_num'); // 用户的积分加3
			$this->assign('article_hot',$hot);

			$ArticleCategoryModel = new \Admin\Model\ArticleCategoryModel();
			$this->assign('one_level_cat',$ArticleCategoryModel->getOneLevelCategory());
			$this->display();
		}else{
			$this->error('对不起没有找到这个页面');
		}
	}

	// 添加点赞
	public function addPraise(){
		$id = I('get.id');
		$re['data'] = D('Article')->addPraise($id);
		$this->ajaxReturn($re);
	}

}
