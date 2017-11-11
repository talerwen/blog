<?php
namespace Home\Model;
use Think\Model;
class ArticleModel extends Model{
    //列表,搜索
    public function search($pram,$pageSize=10)
    {
        $where = array();
        if(I('get.search')){
            $where['a.title'] = array('like','%'.I('get.search').'%');
            $data['search'] = I('get.search');
            $map['_logic']='AND';
        }
        if(I('get.cat')){
            $condition['a.acid'] = array('eq',I('get.cat'));
            $condition['b.parentid'] = array('eq',I('get.cat'));
            $condition['_logic'] = "OR";
            $where['_complex']=$condition;
        }
        if($pram['is_public']){
            $where['a.is_public'] = 1;
        }
        $count = $this->alias('a') ->join('left join __ARTICLE_CATEGORY__ b on a.acid=b.acid')->where($where)->count(); //查询满足条件的总条数
        $p = getpage($count);
		$url = '';
        if(I('get.cat')){
            $url = 'lst-cat-'.I('get.cat');
        }else{
            $url = 'lst';
        }
		if(I('get.search')){
			$search_url = '?search='.I('get.search');
		}
		$data['page'] = $p->show($url,$search_url);
        $data['data'] = $this->alias('a')
            ->field('a.*,b.acname')
            ->where($where)
            ->join('left join __ARTICLE_CATEGORY__ b on a.acid=b.acid')
            ->order('a.atid desc')
            ->limit($p->firstRow.','.$p->listRows)
            ->select();
        return $data;
    }
    public function addPraise($id){
        $this->where(['atid'=>$id])->setInc('praise_num');
        $num = $this->where(['atid'=>$id])->getField('praise_num');
        return $num;
    }
}
