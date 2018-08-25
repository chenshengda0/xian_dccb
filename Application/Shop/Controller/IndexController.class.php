<?php
namespace Shop\Controller;

/**
 * Class IndexController
 * @package Shop\Controller
 */
class IndexController extends HomeController{
    protected $goods_info = 'id,goods_name,goods_ico,goods_introduct,tox_money_need,goods_num,changetime,status,createtime,category_id,is_new,sell_num';
    /**
     * 商城初始化
     */
    public function _initialize(){	
    	parent::_initialize();    	 
    }
    
    //读取公司简介
	public function about(){	
		$map['id'] = I('id');
		$map2['name'] = 'about';
		$map2['status'] = 1;
		$id = M('category')->where($map2)->getField('id');
		$article=M('document')->field('id')->where(array('status'=>1,'category_id'=>$id))->find();	
		if(empty($map['id'])&&!empty($article)){
			$map['id']=$article['id'];
		}
		$map['status'] = 1;
		$article = M('DocumentArticle');
		$title=M('document')->field('title')->where($map)->getField('title');
		$content = $article->where($map)->getField('content');		
		$this->assign('content',$content);//新闻内容		
		$this->assign('about_title',$title);//文章标题
		$this->meta_title='公司简介';
		$this->display();
	}
    /**
     * 商品页初始化
     */
    public function _goods_initialize(){
        $shop_address = D('shop_address')->where('uid=' . is_login())->find();
        $this->assign('shop_address', $shop_address);
    }
    /**
     * 分页
     */
	public function _page_initialize(){
		$config=api('Config/lists');
		C($config);
		$countnum=is_numeric(C('HOME_PAGE_COUNT'))?C('HOME_PAGE_COUNT'):15;
		$countnum=$countnum>0?$countnum:15;
		return $countnum;
	}
    /**
     * 商城首页
     */
    public function index(){
    	
    	//获取轮播图 	
    	$slideList=D('slide')->where(array('status'=>1))->limit(5)->select();
    	$this->assign('slideList',$slideList);
    	
    	//获取最新的前4个商品
    	$goods_list_new=D('Index')->getIndexNew(4);
    	$this->assign('newList', $goods_list_new);  
    	
    	//获取最热卖的前4个商品
    	$goods_list_hot=D('Index')->getIndexHot(4);
    	$this->assign('hotList', $goods_list_hot);
    	
    	
    	//获取推荐的前4个商品
    	$goods_list_rec=D('Index')->getIndexRec(4);
    	$this->assign('recList', $goods_list_rec);
    	
    	//获取商品菜单
    	$tree = D('shopCategory')->getTree();    

     	$categoryList=array();     	
    	foreach ($tree as $category){
    		//查询出该大类的最新前八条数据
    		if ($category['_']) {    			
    			foreach ($category['_'] as $child){
    				$categoryList[$child['id']]=M('shop')->where(array('status'=>1,'category_id'=>$child['id']))->limit(8)->select();    				
    			}    			
    		}
    	}  
    	 
    	$this->assign('categoryList',$categoryList);

    	
    	$this->meta_title = "首页";   
    	$this->display();
    }
    
    
    
    /**
     * 根据类别id获取该类下的商品
     */
    public function getDataByClassId(){
    	$categoryid=I('id');
    	//根据id查询此种类下的前8条数据
    	$map['category_id']=$categoryid;
    	$map['status']=1;
    	$categoryList=M('shop')->where($map)->order('changetime desc')->limit(8)->select(); 
    	$count=count($categoryList);
    	for($i=0;$i<$count;$i++){
    		if(!empty($categoryList[$i]['goods_ico'])){
    			$categoryList[$i]['goods_ico']=get_cover($categoryList[$i]['goods_ico'],'path');   			
    		}
    	}
    
    	$this->ajaxReturn($categoryList);
    }
    
    /**
     * 商品页
     * @param int $page
     * @param int $category_id
     */
    public function goods()
    {   	
    	$id=I('id');
    	$key=I('order');
    	$sort=I('sort');
    	
    	if(isset($key)){		  
			if($key=="1"){ $listsort="view"." ".$sort;}  
			if($key=="2"){ $listsort="id"." ".$sort;} 
			if($key=="3"){ $listsort="ms_price"." ".$sort;} 
			if($key=="4"){ $listsort="sell_num"." ".$sort;}  	
		 } 
		if(empty($key)){
			$key="1";$see="asc";
			$order="view";$sort="asc";
		    $listsort=$order." ".$sort;			
		}
		
    	if($sort=="asc"){$see="desc";}
      	if($sort=="desc"){$see="asc";}
      	
       	$this->assign('see',$see);
      	$this->assign('order',$key);
    	$goods_category = D('shopCategory')->find($id);
    	if(empty($goods_category)){
    		$this->error('你要查找的类别不存在');
    	}    	
    	//根据商品类别获取该类别下的所有子类别
    	$childCategory_list = D('shopCategory')->getCategoryById($id);    	
    	$this->assign('childList',$childCategory_list);
    	
    	//根据类别id获取获取该类别下的商品
    	if ($id != 0) {    	
    		$goods_categorys = D('shop_category')->field('id,title')->where("id=%d OR pid=%d", array($id, $id))->limit(999)->select();
    		$ids = array();
    		foreach ($goods_categorys as $v) {
    			$ids[] = $v['id'];
    		}
    		$map['category_id'] = array('in', implode(',', $ids));
    	}
    	$map['status'] = 1;
    	$count = M('shop')->where($map)->count();  	
    	$Page= new \Think\Page($count,15);
    	$Page->setConfig('prev','上一页');
    	$Page->setConfig('next','下一页');
    	$Page->setConfig('first','第一页');
    	$Page->setConfig('last','尾页');
    	$Page->setConfig('theme','%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% %HEADER%');
    	$show= $Page->show();
    	$goods_list=M('shop')->where($map)->order($listsort)->limit($Page->firstRow.','.$Page->listRows)->select();
    	//dump($goods_list);
    	
    	$this->assign('goodList',$goods_list);// 赋值数据集
    	$this->assign('page',$show);// 赋值分页输出    	
    	
    	//获取热销的产品
    	$goods_list=D('Index')->getIndexHot(10);
    	$this->assign('hotList', $goods_list);
    	
    	$this->assign('category_id', $id);   
    	$this->assign('value',$sort);
    	$this->meta_title=$goods_category['title'];
        $this->display();
    }

    /**
     * 商品详情页
     * @param int $id
     */
    public function goodsDetail($id = 0)
    {    	
        $this->_goods_initialize();
        $goods = D('shop')->getGoodsinfo($id);
        
        ///var_dump($goods);
        
//         $bonusRule=M('bonus_rule')->where(array('status'=>1))->find();
//         $shop_rate=$bonusRule['shop_rate'];
//         $all=$all*$shop_rate;
        if (empty($goods)) {
            $this->error('对不起，你查找的商品不存在！');
        }        
        //根据商品类别获取该类别下的所有子类别      
        $childCategory_list = D('shopCategory')->getCategoryById($goods['category_id'],'_');
        $this->assign('childList',$childCategory_list);
        
		//更新浏览次数          
        M('shop')->where(array('id' => $id))->setInc('view');
        
        //获取同一类浏览量前五的商品
        $view_list=M('shop')->where(array('status'=>1,'category_id'=>$goods['category_id'],'id'=>array('neq',$goods['id'])))->limit(5)->order("view desc")->select();        
        $this->assign('viewlist', $view_list);
        
        //获取热销的产品
        $goods_list=D('Index')->getIndexHot(10);
        $this->assign('hotList', $goods_list);
       
        $this->assign('goodinfo', $goods);
        $this->meta_title='商品详情页';
        $this->display();
    }

}