<?php
namespace app\api\controller\store;


use app\models\store\StoreCategory;
use app\Request;
use think\facade\Db;

class CategoryController
{
    public function category(Request $request)
    {
        $cateogry = StoreCategory::with('children')->where('is_show',1)->order('sort desc,id desc')->where('pid',0)->select();
        $cateogry = $cateogry->hidden(['add_time','is_show','sort','children.sort','children.add_time','children.pid','children.is_show'])->toArray();
        return app('json')->success($this->hide($cateogry, $request));
    }

    protected function hide($cateogry, $request)
    {
        if (isSupplyChain()) {
            $user = $request->user();
            $user = $user->toArray();

            $ids = [-1];
            $shop = Db::table('eb_shop')->where('id', $user['shop_id'])->find();
            $brandProducts = Db::table('eb_brand_product')->where('admin_id', $shop['admin_id'])->select()->toArray();
            $hides = Db::table('eb_shop_product_hide')->where('shop_id', $shop['id'])->select()->toArray();
            $hIds = [];
            foreach ($hides as $hide) {
                $hIds[] = $hide['product_id'];
            }
            foreach ($brandProducts as $brandProduct) {
                if (!in_array($brandProduct['product_id'], $hIds)) {
                    $ids[] = $brandProduct['product_id'];
                }
            }
            $ids = array_values($ids);

            $cids = [];
            $products = Db::table('eb_store_product')->where('id', 'in', $ids)->field('cate_id')->select()->toArray();
            foreach ($products as $product) {
                $cids = array_merge($cids, explode(',', $product['cate_id']));
            }
            
            foreach ($cateogry as $k => $c) {
                foreach ($c['children'] as $k2 => $cc) {
                    if (!in_array($cc['id'], $cids)) {
                        unset($cateogry[$k]['children'][$k2]);
                    }
                }
                $cateogry[$k]['children'] = array_values($cateogry[$k]['children']);
            }
                

        }
       
        return $cateogry;
    }
}