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
class BigShopController extends AdminController
{

    protected $shopModel;
    protected $shop_configModel;
    protected $shop_categoryModel;

    function _initialize()
    {
        $this->shopModel = D('Shop/Shop');
        $this->shop_configModel = D('Shop/ShopConfig');
        $this->shop_categoryModel = D('Shop/ShopCategory');
        parent::_initialize();
    }

    /**商品分类
     * @author 郑钟良<zzl@ourstu.com>
     */
    public function shopCategory()
    {
        //显示页面
        $builder = new AdminTreeListBuilder();
        $attr['class'] = 'btn ajax-post';
        $attr['target-form'] = 'ids';

        $map['type'] = 1;
        $map['status']  = 1;
        $tree = $this->shop_categoryModel->getTree(0, 'id,title,sort,pid,status',$map);
        

        $builder->title('商城分类管理')
            ->buttonNew(U('Shop/add'))
            ->data($tree)
            ->display();
    }

    /**分类添加
     * @param int $id
     * @param int $pid
     * @author 郑钟良<zzl@ourstu.com>
     */
    public function add($id = 0, $pid = 0)
    {
        if (IS_POST) {
            if ($id != 0) {
                $category = $this->shop_categoryModel->create();
                if ($this->shop_categoryModel->save($category)) {

                    $this->success('编辑成功。', U('Shop/shopCategory'));
                } else {
                    $this->error('编辑失败。');
                }
            } else {
                $category = $this->shop_categoryModel->create();
                if ($this->shop_categoryModel->add($category)) {

                    $this->success('新增成功。', U('Shop/shopCategory'));
                } else {
                    $this->error('新增失败。');
                }
            }


        } else {
            $builder = new AdminConfigBuilder();
            $categorys = $this->shop_categoryModel->select();
            $opt = array();
            foreach ($categorys as $category) {
                $opt[$category['id']] = $category['title'];
            }
            if ($id != 0) {
                $category = $this->shop_categoryModel->find($id);
            } else {
                $category = array('pid' => $pid, 'status' => 1);
                $father_category_pid=$this->shop_categoryModel->where(array('id'=>$pid))->getField('pid');
                if($father_category_pid!=0){
                    $this->error('分类不能超过三级！');
                }
            }


            $builder->title('新增分类')->keyId()->keyText('title', '标题')->keySelect('pid', '父分类', '选择父级分类', array('0' => '顶级分类') + $opt)
                ->keyStatus()->keyCreateTime()->keyUpdateTime()
                ->data($category)
                ->buttonSubmit(U('Shop/add'))->buttonBack()->display();
        }

    }

    /**分类回收站
     * @param int $page
     * @param int $r
     * @author 郑钟良<zzl@ourstu.com>
     */
    public function categoryTrash($page = 1, $r = 20,$model='')
    {
        $builder = new AdminListBuilder();
        $builder->clearTrash($model);
        //读取微博列表
        $map = array('status' => -1);
        $list = $this->shop_categoryModel->where($map)->page($page, $r)->select();
        $totalCount = $this->shop_categoryModel->where($map)->count();

        //显示页面

        $builder->title('商城分类回收站')
            ->setStatusUrl(U('setStatus'))->buttonRestore()->buttonClear('ShopCategory')
            ->keyId()->keyText('title', '标题')->keyStatus()->keyCreateTime()
            ->data($list)
            ->pagination($totalCount, $r)
            ->display();
    }


    public function operate($type = 'move', $from = 0)
    {
        $builder = new AdminConfigBuilder();
        $from = $this->shop_categoryModel->find($from);

        $opt = array();
        $categorys = $this->shop_categoryModel->select();
        foreach ($categorys as $category) {
            $opt[$category['id']] = $category['title'];
        }
        if ($type === 'move') {

            $builder->title('移动分类')->keyId()->keySelect('pid', '父分类', '选择父分类', $opt)->buttonSubmit(U('Shop/add'))->buttonBack()->data($from)->display();
        } else {

            $builder->title('合并分类')->keyId()->keySelect('toid', '合并至的分类', '选择合并至的分类', $opt)->buttonSubmit(U('Shop/doMerge'))->buttonBack()->data($from)->display();
        }

    }

    /**商品分类合并
     * @param $id
     * @param $toid
     * @author 郑钟良<zzl@ourstu.com>
     */
    public function doMerge($id, $toid)
    {
        $effect_count = $this->shopModel->where(array('category_id' => $id))->setField('category_id', $toid);
        $this->shop_categoryModel->where(array('id' => $id))->setField('status', -1);
        $this->success('合并分类成功。共影响了' . $effect_count . '个内容。', $this->shop_categoryModel);
        //TODO 实现合并功能 shop_category
    }

    /**
     * 设置商品分类状态：删除=-1，禁用=0，启用=1
     * @param $ids
     * @param $status
     * @author 郑钟良<zzl@ourstu.com>
     */
    public function setStatus($ids, $status)
    {
        $builder = new AdminListBuilder();
        $builder->doSetStatus('shopCategory', $ids, $status);
    }
    
    public function delDoods($ids){
    	$ids = is_array($ids) ? $ids : explode(',', $ids);
    	M('Shop')->where(array('id' => array('in', $ids)))->delete();
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
  	 * 显示商品列表------wpf------2015/05/28
  	 * @param number $page
  	 * @param number $r
  	 */
    public function goodsList($page = 1, $r = 20)
    {
        $map['status'] = array('egt', 0);
        $goodsList = $this->shopModel->where($map)->order('createtime desc')->page($page, $r)->select();
        $totalCount = $this->shopModel->where($map)->count();
        $builder = new AdminListBuilder();
        $builder->title('商品列表');
        $builder->meta_title = '商品列表';
        foreach ($goodsList as &$val) {
            $category = $this->shop_categoryModel->where('id=' . $val['category_id'])->getField('title');
            $val['category'] = $category;
            unset($category);
            $val['is_hot'] = ($val['is_hot'] == 0) ? '否' : '是';
            $val['is_recommend'] = ($val['is_recommend'] == 0) ? '否' : '是';
            $val['is_new'] = ($val['is_new'] == 0) ? '否' : '是';
        }
        unset($val);
        $builder->buttonNew(U('Shop/goodsEdit'))->buttonDelete(U('setGoodsStatus'))->setStatusUrl(U('setGoodsStatus'));
        $builder->keyId()->keyText('goods_name', '商品名称')->keyText('category', '商品分类')->keyText('tox_money_need', '商品价格')->keyText('goods_num', '商品余量')->keyText('sell_num', '已售出量')
        	->keyText('comment', '评论数')->keyText('view', '访问量')
        	->keyLinkStatus('is_hot', '是否热销', "Shop/setNew?id=###&mark=is_hot")
        	->keyLinkStatus('is_recommend', '是否推荐', "Shop/setNew?id=###&mark=is_recommend")->keyLinkStatus('is_new', '是否新品', "Shop/setNew?id=###&mark=is_new")
        	->keyStatus('status', '出售状态')->keyUpdateTime('changetime')->keyDoActionEdit('Shop/goodsEdit?id=###')
        	->keyDoAction('Shop/delDoods?ids=###&status=-1', '删除');
        $builder->data($goodsList);
        $builder->pagination($totalCount, $r);
        $builder->display();
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
        $is_new = intval(!$this->shopModel->where(array('id' => $id))->getField($mark));
        $rs = $this->shopModel->where(array('id' => $id))->setField(array("$mark" => $is_new, 'changetime' => time()));
        if ($rs) {
            $this->success('设置成功！');
        } else {
            $this->error('设置失败！');
        }
    }

    /**商品回收站
     * @param int $page
     * @param int $r
     * @author 郑钟良<zzl@ourstu.com>
     */
    public function goodsTrash($page = 1, $r = 10,$model='')
    {
        $builder = new AdminListBuilder();
        $builder->clearTrash($model);
        //读取微博列表
        $map = array('status' => -1);
        $goodsList = $this->shopModel->where($map)->order('changetime desc')->page($page, $r)->select();
        $totalCount = $this->shopModel->where($map)->count();

        //显示页面

        $builder->title('商品回收站')
            ->setStatusUrl(U('setGoodsStatus'))->buttonRestore()->buttonClear('Shop/Shop')
            ->keyId()->keyLink('goods_name', '标题', 'Shop/goodsEdit?id=###')->keyCreateTime()->keyStatus()
            ->data($goodsList)
            ->pagination($totalCount, $r)
            ->display();
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
    public function goodsEdit($id = 0, $goods_name = '', $goods_ico = '', $goods_introduct = '', $goods_detail = '', $tox_money_need = '',$ms_price='', $goods_num = '', $status = '', $category_id = 0, $comment=0,$view=0,$sell_num = 0, $is_hot = 0,$is_recommend = 0,$is_new = 0)
    {
        $isEdit = $id ? 1 : 0;
        if (IS_POST) {
        	$Shop = D('Shop');
        	$data = $_POST;
           
            if ($isEdit) {
            	$data['id'] = $id;
                $res = $this->shopModel->create($data);
                if($res){
                	$this->shopModel->save();
                	$this->success('编辑成功', U('Shop/goodsList'));
                }else{
                	$this->error($this->shopModel->getError());
                }
            } else {
             	$res = $this->shopModel->create($data);
                if($res){
                	$this->shopModel->add();
                	$this->success( '添加成功', U('Shop/goodsList'));
                }else{
                	$this->error($this->shopModel->getError());
                }
            }
            
        } else {
            $builder = new AdminConfigBuilder();
            $builder->title($isEdit ? '编辑商品' : '添加商品');
            $builder->meta_title = $isEdit ? '编辑商品' : '添加商品';

            //获取分类列表
            $category_map['status'] = array('egt', 0);      
            $category_map['pid'] = array('neq', 0);
            $goods_category_list = $this->shop_categoryModel->where($category_map)->order('pid desc')->select();           
            $options = array_combine(array_column($goods_category_list, 'id'), array_column($goods_category_list, 'title'));
                     
            $builder->keyId()->keyText('goods_name', '商品名称')
            ->keySingleImage('goods_ico', '商品图标')->keyImages('pics', '商品图集')->keySelect('category_id', '商品分类', '', $options)
            ->keyText('goods_introduct', '商品广告语')->keyEditor('goods_detail', '商品详情')
            ->keyInteger('tox_money_need', '市场价')->keyInteger('ms_price', '秒杀价')->keyInteger('goods_num', '商品余量')
            ->keyBool('is_hot', '是否为热销')->keyBool('is_recommend', '是否为推荐')->keyBool('is_new', '是否为新品')->keyStatus('status', '出售状态');
            
            if ($isEdit) {
                $goods = $this->shopModel->where('id=' . $id)->find();
                $builder->data($goods);
                $builder->buttonSubmit(U('Shop/goodsEdit'));
                $builder->buttonBack();
                $builder->display();
            } else {
                $goods['status'] = 1;
                $builder->buttonSubmit(U('Shop/goodsEdit'));
                $builder->buttonBack();
                $builder->data($goods);
                $builder->display();
            }
        }
    }
    


    /**商城日志
     * @param int $page
     * @param int $r
     * @author 郑钟良<zzl@ourstu.com>
     */
    public function shopLog($page = 1, $r = 20)
    {
        //读取列表
        $model = M('shop_log');
        $list = $model->page($page, $r)->order('create_time desc')->select();
        $totalCount = $model->count();
        //显示页面
        $builder = new AdminListBuilder();

        $builder->title('商城信息记录');
        $builder->meta_title = '商城信息记录';

        $builder->keyId()->keyText('message', '信息')->keyUid()->keyCreateTime()
            ->data($list)
            ->pagination($totalCount, $r)
            ->display();
    }

}
