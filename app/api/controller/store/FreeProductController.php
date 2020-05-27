<?php
namespace app\api\controller\store;


use app\models\store\StoreCategory;
use app\Request;
use think\facade\Db;
use app\models\store\StoreProductAttr;

class FreeProductController
{
    public function downUserStock(Request $request, $id)
    {
        Db::table('eb_user_stock')->where('id', $id)->update(['is_show' => 0]);
        return app('json')->success('ok', []);
    }

    public function upUserStock(Request $request, $id)
    {
        Db::table('eb_user_stock')->where('id', $id)->update(['is_show' => 1, 'price' => $request->post('price')]);
        return app('json')->success('ok', []);
    }

    public function userStock(Request $request)
    {
        $uid = $request->uid();
        $data = Db::table('eb_user_stock')->where('user_id', $uid)->where('stock', '>', 0)->order('id ASC')->select()->toArray();
        foreach ($data as &$d) {
            $d['product'] = Db::table('eb_store_product')->where('id', $d['product_id'])->find();
            $d['user'] = Db::table('eb_user')->where('uid', $d['user_id'])->find();
            $d['attrValue'] = Db::table('eb_store_product_attr_value')->where('unique', $d['product_attr_unique'])->find();
        }
        return app('json')->success('ok', $data);
    }
    
    public function list(Request $request)
    {
        $data = Db::table('eb_user_stock')->where('is_show', 1)->where('stock', '>', 0)->order('id ASC')->select()->toArray();
        foreach ($data as &$d) {
            $d['product'] = Db::table('eb_store_product')->where('id', $d['product_id'])->find();
            $d['user'] = Db::table('eb_user')->where('uid', $d['user_id'])->find();
            $d['attrValue'] = Db::table('eb_store_product_attr_value')->where('unique', $d['product_attr_unique'])->find();
        }
        return app('json')->success('ok', $data);
    }

    public function get(Request $request, $id)
    {
        $userStock = Db::table('eb_user_stock')->where('id', $id)->find();
        $uid = $request->uid();
        if ($userStock['user_id'] == $uid) {
            return app('json')->fail('无法购买自己的商品');
        }
        list($productAttr, $productValue) = StoreProductAttr::getProductAttrDetail($userStock['product_id'], $uid, 0);
        $productValue = Db::table('eb_store_product_attr_value')->where('unique', $userStock['product_attr_unique'])->find();
        $productValue['price'] = $userStock['price'];
        $productValue['stock'] =$userStock['stock'];
        $data = [
            'storeInfo' => Db::table('eb_store_product')->where('id', $userStock['product_id'])->find(),
            'productAttr' => $productAttr,
            'productValue' => [
                $productValue['suk'] => $productValue
            ],
        ];
        return app('json')->success('ok', $data);
    }
}