<?php
namespace Admin\Controller;

use Admin\Builder\AdminConfigBuilder;
use Admin\Builder\AdminListBuilder;
use Admin\Builder\AdminTreeListBuilder;

/**
 * Class ShopController
 * @package Admin\controller
 * @郑钟良
 */
class ShopController extends AdminController
{

    protected static $goodsModel;
    protected static $shopConfigModel;
    protected static $shopCategoryModel;

    function _initialize()
    {
        self::$goodsModel = D('Shop/Shop');
        self::$shopConfigModel = D('Shop/ShopConfig');
        self::$shopCategoryModel = D('Shop/ShopCategory');
        parent::_initialize();
    }

    /**
     * 商品分类
     */
    public function shopCategory(){
        $builder = new AdminTreeListBuilder();
        $map['status'] = array('egt',0);
        $tree = self::$shopCategoryModel->getTree(0, 'id,title,sort,pid,status',$map);
     
        $this->meta_title = '商城分类管理';
        $builder->title('商城分类管理')
            ->buttonNew(U('Shop/addCategory'))
            ->setLevel(4)->setModel('Category')
            ->data($tree)->display('shopcategory');
    }

    /**
     * 添加/编辑 商品分类
     * @param number $id 分类id
     * @param number $pid 分类父id
     */
    public function addCategory($id = 0, $pid = 0){
        if (IS_POST) {
            if ($id != 0) {
                $category = self::$shopCategoryModel->create();
                if (self::$shopCategoryModel->save($category)) {
                    $this->success('编辑成功。', U('Shop/shopCategory'));
                } else {
                    $this->error('编辑失败。');
                }
            } else {
                $category = self::$shopCategoryModel->create();
                if (self::$shopCategoryModel->add($category)) {
                    $this->success('新增成功。', U('Shop/shopCategory'));
                } else {
                    $this->error('新增失败。');
                }
            }
        } else {
         	$builder = new AdminConfigBuilder();
            $categorys = self::$shopCategoryModel->noHasGoods();
            $opt = array();
            foreach ($categorys as $category) {
                $opt[$category['id']] = $category['title'];
            }
            if ($id != 0) {
                $category = self::$shopCategoryModel->find($id);
                $title='编辑分类';
            } else {
                $category = array('pid' => $pid, 'status' => 1);
                $title='新增分类';
            }
            
            $this->meta_title = $title;
            $builder->title($title)->keyId()->keyText('title', '标题')
            	->keySelect('pid', '父分类', '选择父级分类', array('0' => '顶级分类') + $opt)
                ->keyStatus()->keyCreateTime()->keyUpdateTime()
                ->data($category)
                ->buttonSubmit(U('addCategory'))->buttonBack()->display('shopcategory');
        }

    }
    
    /**
     * 删除分类
     * @param  $id
     */
    public function delCategory($ids){
    	$ids = is_array($ids) ? $ids : explode(',', $ids);
    	$ids = get_children_node(self::$shopCategoryModel,$ids);
    	self::$shopCategoryModel->where(array('id'=>array('in',$ids)))->delete();
    	$this->success('删除分类成功',U('shopCategory'));
    }

    /**
     * 设置商品分类状态：，禁用=0，启用=1
     * @param $ids
     * @param $status
     */
    public function setCategoryStatus($ids, $status)
    {
        $ids = is_array($ids) ? $ids : explode(',', $ids);
    	$ids = get_children_node(self::$shopCategoryModel,$ids);
    	self::$shopCategoryModel->where(array('id'=>array('in',$ids)))->setField('status',$status);
    	if($status){
    		$this->success('启用成功',U('shopCategory'));
    	}else{
    		$this->success('禁用成功',U('shopCategory'));
    	}
    }
    
    public function delGoods($ids){
        
    	$ids = is_array($ids) ? $ids : explode(',', $ids);
    	
    	self::$goodsModel->delGoods($ids);
    	$this->success('删除成功', U('goodsList'));
    }

    /**
     * 设置商品状态：禁用=0，启用=1
     * @param $ids
     * @param $status
     * @author 郑钟良<zzl@ourstu.com>
     */
    public function setGoodsStatus($ids, $status)
    {
        $builder = new AdminListBuilder();
        $builder->doSetStatus('shop', $ids, $status);
    }

  	/**
  	 * 显示商品列表
  	 * @param number $page
  	 * @param number $r
  	 */
    public function goodsList($page = 1, $r = 20)
    {
        
        $map['status'] = array('egt', 0);
        $goodsList = self::$goodsModel->where($map)->order('createtime desc')->page($page, $r)->select();
        $totalCount = self::$goodsModel->where($map)->count();
        $builder = new AdminListBuilder();
        $builder->title('商品列表');
        $builder->meta_title = '商品列表';
        foreach ($goodsList as &$val) {
            $category = self::$shopCategoryModel->where('id=' . $val['category_id'])->getField('title');
            $val['category'] = $category;
            unset($category);
            $val['is_hot'] = ($val['is_hot'] == 0) ? '否' : '是';
            $val['is_recommend'] = ($val['is_recommend'] == 0) ? '否' : '是';
            $val['is_new'] = ($val['is_new'] == 0) ? '否' : '是';
        }
        unset($val);
        $builder->buttonNew(U('Shop/goodsEdit'))->buttonDelete(U('setGoodsStatus'))->setStatusUrl(U('setGoodsStatus'));
        $builder->keyId()->keyText('goods_name', '商品名称')->keyText('ms_price', '商品价格')
     
          ->keyText('chanliang', '每小时产量')->keyText('suanli', '算力')->keyText('tox_money_need', '限购')
        	
        	->keyStatus('status', '出售状态')->keyUpdateTime('changetime')->keyDoActionEdit('Shop/goodsEdit?id=###')
        	->keyDoAction('Shop/delGoods?ids=###', '删除');
        $builder->data($goodsList);
        $builder->pagination($totalCount, $r);
        
        $builder->display('goodslist');
    }

	/**
	 * 根据$mark='is_hot'|'is_recommend'|'is_new'来设置状态是否启用------wpf------2015/05/28
	 * @param number $id
	 * @param unknown $mark
	 */
    public function setNew($id = 0,$mark)
    {
        if ($id == 0) {
            $this->error('请选择商品');
        }
        $is_new = intval(!self::$goodsModel->where(array('id' => $id))->getField($mark));
        $rs = self::$goodsModel->where(array('id' => $id))->setField(array("$mark" => $is_new, 'changetime' => time()));
        if ($rs) {
            $this->success('设置成功！');
        } else {
            $this->error('设置失败！');
        }
    }



	/**
	 * 添加或编辑商品
	 * @param number $id
	 * @param string $goods_name
	 * @param string $goods_ico
	 * @param string $goods_introduct
	 * @param string $goods_detail
	 * @param string $tox_money_need
	 * @param string $ms_price
	 * @param string $goods_num
	 * @param string $status
	 * @param number $category_id
	 * @param number $comment
	 * @param number $view
	 * @param number $sell_num
	 * @param number $is_hot
	 * @param number $is_recommend
	 * @param number $is_new
	 */
    public function goodsEdit($id = 0)
    {
        $isEdit = $id ? 1 : 0;
        if (IS_POST) {
        	$Shop = D('Shop');
        	$data = $_POST;
           
            if ($isEdit) {
            	$data['id'] = $id;

                $res = self::$goodsModel->create($data);
                if($res){
                	self::$goodsModel->save();
                	$this->success('编辑成功', U('Shop/goodsList'));
                }else{
                	$this->error(self::$goodsModel->getError());
                }
            } else {
             	$res = self::$goodsModel->create($data);
                if($res){
                	self::$goodsModel->add();
                	$this->success( '添加成功', U('Shop/goodsList'));
                }else{
                	$this->error(self::$goodsModel->getError());
                }
            }
            
        } else {
            $builder = new AdminConfigBuilder();
            $builder->title($isEdit ? '编辑商品' : '添加商品');
            $builder->meta_title = $isEdit ? '编辑商品' : '添加商品';
            
            //获取分类列表
            $goods_category_list = self::$shopCategoryModel->getLeaf();           
            $options = array_combine(array_column($goods_category_list, 'id'), array_column($goods_category_list, 'title'));
                     
            $builder->keyId()->keyText('goods_name', '商品名称')
            ->keySingleImage('goods_ico', '商品图标')
            ->keyEditor('goods_detail', '商品详情')
            ->keyInteger('ms_price', '商品价格')
             
              ->keyInteger('chanliang', '产量/小时')
              ->keyInteger('shouming', '寿命(小时)')
              ->keyInteger('suanli', '算力')
              ->keyInteger('tox_money_need', '限购')
            ->keyStatus('status', '出售状态');
            
            if ($isEdit) {
                $goods = self::$goodsModel->where('id=' . $id)->find();
                $builder->data($goods);
                $builder->buttonSubmit(U('Shop/goodsEdit'));
                $builder->buttonBack();
                $builder->display('goodslist');
            } else {
                $goods['status'] = 1;
                $builder->buttonSubmit(U('Shop/goodsEdit'));
                $builder->buttonBack();
                $builder->data($goods);
                $builder->display('goodslist');
            }
        }

    	
    	
    	
    	
    }

}
