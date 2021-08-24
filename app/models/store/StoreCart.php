<?php
/**
 *
 * @author: xaboy<365615158@qq.com>
 * @day: 2017/12/18
 */

namespace app\models\store;

use app\admin\model\store\StoreProductAttrValue;
use app\admin\model\system\SystemGroupData;
use crmeb\basic\BaseModel;
use crmeb\services\UtilService;
use crmeb\traits\ModelTrait;
use think\facade\Db;

/**
 * TODO 购物车Model
 * Class StoreCart
 * @package app\models\store
 */
class StoreCart extends BaseModel
{
    /**
     * 数据表主键
     * @var string
     */
    protected $pk = 'id';

    /**
     * 模型名称
     * @var string
     */
    protected $name = 'store_cart';

    use ModelTrait;

    protected $insert = ['add_time'];

    protected function setAddTimeAttr()
    {
        return time();
    }

    public static function setCart($uid,$product_id,$cart_num = 1,$product_attr_unique = '',$type='product',$is_new = 0,$combination_id=0,$seckill_id = 0,$bargain_id = 0, $user_stock_id = 0, $takeout_id)
    {
        if($cart_num < 1) $cart_num = 1;
        if($seckill_id){
            $StoreSeckillinfo = StoreSeckill::getValidProduct($seckill_id);
            if(!$StoreSeckillinfo)
                return self::setErrorInfo('该产品已下架或删除');
            $userbuycount = StoreOrder::where('uid', $uid)->where('paid', 1)->where('seckill_id', $seckill_id)->count();
            if($StoreSeckillinfo['num'] <= $userbuycount || $StoreSeckillinfo['num'] < $cart_num)
                return self::setErrorInfo('每人限购'.$StoreSeckillinfo['num'].'件');
            $res = StoreProductAttrValue::where('product_id',$seckill_id)->where('unique',$product_attr_unique)->where('type',1)->field('suk,quota')->find();
            if($cart_num > $res['quota'])
                return self::setErrorInfo('该产品库存不足'.$cart_num);
            $product_stock = StoreProductAttrValue::where('product_id',$StoreSeckillinfo['product_id'])->where('suk',$res['suk'])->where('type',0)->value('stock');
            if($product_stock < $cart_num)
                return self::setErrorInfo('该产品库存不足'.$cart_num);
        }elseif($bargain_id){
            if(!StoreBargain::validBargain($bargain_id))
                return self::setErrorInfo('该产品已下架或删除');
            $StoreBargainInfo = StoreBargain::getBargain($bargain_id);
            $res = StoreProductAttrValue::where('product_id',$bargain_id)->where('type',2)->field('suk,quota')->find();
            if($cart_num > $res['quota'])
                return self::setErrorInfo('该产品库存不足'.$cart_num);
            $product_stock = StoreProductAttrValue::where('product_id',$StoreBargainInfo['product_id'])->where('suk',$res['suk'])->where('type',0)->value('stock');
            if($product_stock < $cart_num)
                return self::setErrorInfo('该产品库存不足'.$cart_num);
        }elseif($combination_id){//拼团
            $StoreCombinationInfo = StoreCombination::getCombinationOne($combination_id);
            if(!$StoreCombinationInfo)
                return self::setErrorInfo('该产品已下架或删除');
            $userbuycount = StoreOrder::where('uid', $uid)->where('paid', 1)->where('combination_id', $combination_id)->count();
            if($StoreCombinationInfo['num'] <= $userbuycount || $StoreCombinationInfo['num'] < $cart_num)
                return self::setErrorInfo('每人限购'.$StoreCombinationInfo['num'].'件');
            $res = StoreProductAttrValue::where('product_id',$combination_id)->where('unique',$product_attr_unique)->where('type',3)->field('suk,quota')->find();
            if($cart_num > $res['quota'])
                return self::setErrorInfo('该产品库存不足'.$cart_num);
            $product_stock = StoreProductAttrValue::where('product_id',$StoreCombinationInfo['product_id'])->where('suk',$res['suk'])->where('type',0)->value('stock');
            if($product_stock < $cart_num)
                return self::setErrorInfo('该产品库存不足'.$cart_num);
        }elseif($user_stock_id){//自由交易 11223344
            $userStock = Db::table('eb_user_stock')->where('id', $user_stock_id)->find();
            if($userStock['stock'] < $cart_num)
                return self::setErrorInfo('该产品库存不足'.$cart_num);
        } elseif($takeout_id){//自提
            $userStock = Db::table('eb_user_stock')->where('id', $takeout_id)->find();
            if($userStock['stock'] < $cart_num)
                return self::setErrorInfo('该产品库存不足'.$cart_num);
        } else{
            if(!StoreProduct::isValidProduct($product_id))
                return self::setErrorInfo('该产品已下架或删除');
            if(!StoreProductAttr::issetProductUnique($product_id,$product_attr_unique))
                return self::setErrorInfo('请选择有效的产品属性');
            if(StoreProduct::getProductStock($product_id,$product_attr_unique) < $cart_num)
                return self::setErrorInfo('该产品库存不足'.$cart_num);
        }
        if($cart = self::where('type', $type)->where('uid', $uid)->where('product_id', $product_id)->where('product_attr_unique', $product_attr_unique)->where('is_new', $is_new)->where('is_pay', 0)->where('is_del', 0)->where('user_stock_id', $user_stock_id)->where('takeout_id', $takeout_id)->where('combination_id', $combination_id)->where('bargain_id', $bargain_id)->where('seckill_id', $seckill_id)->find()){
            if($is_new)
                $cart->cart_num = $cart_num;
            else
                $cart->cart_num = bcadd($cart_num, $cart->cart_num, 0);
            $cart->add_time = time();
            $cart->save();
            return $cart;
        }else{
            $add_time = time();
            return self::create(compact('uid','product_id','cart_num','product_attr_unique','is_new','type','combination_id','add_time','bargain_id','seckill_id', 'user_stock_id', 'takeout_id'));
        }
    }

    public static function removeUserCart($uid,$ids)
    {
        return self::where('uid',$uid)->where('id','IN',implode(',',$ids))->update(['is_del'=>1]);
    }

    public static function getUserCartNum($uid, $type, $numType)
    {
        if($numType){
            return self::where('uid',$uid)->where('type',$type)->where('is_pay',0)->where('is_del',0)->where('is_new',0)->count();
        }else{
            return self::where('uid',$uid)->where('type',$type)->where('is_pay',0)->where('is_del',0)->where('is_new',0)->sum('cart_num');
        }
    }

    /**
     * TODO 修改购物车库存
     * @param $cartId
     * @param $cartNum
     * @param $uid
     * @return StoreCart|bool
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function changeUserCartNum($cartId,$cartNum,$uid)
    {
        $count = self::where('uid',$uid)->where('id',$cartId)->count();
        if(!$count) return self::setErrorInfo('参数错误');
        $cartInfo = self::where('uid',$uid)->where('id',$cartId)->field('product_id,combination_id,seckill_id,bargain_id,product_attr_unique,cart_num')->find()->toArray();
        $stock = 0;
        if($cartInfo['bargain_id']){
            //TODO 获取砍价产品的库存
            $stock = 0;
        }else if($cartInfo['seckill_id']){
            //TODO 获取秒杀产品的库存
            $stock = 0;
        }else if($cartInfo['combination_id']){
            //TODO 获取拼团产品的库存
            $stock = 0;
        }else if($cartInfo['product_id']){
            //TODO 获取普通产品的库存
            $stock = StoreProduct::getProductStock($cartInfo['product_id'],$cartInfo['product_attr_unique']);
        }
        if(!$stock) return self::setErrorInfo('暂无库存');
        if(!$cartNum) return self::setErrorInfo('库存错误');
        if($stock < $cartNum) return self::setErrorInfo('库存不足'.$cartNum);
        if($cartInfo['cart_num'] == $cartNum) return true;
        return self::where('uid',$uid)->where('id',$cartId)->update(['cart_num'=>$cartNum]);
    }

    public static function getUserProductCartList($uid,$cartIds='',$status=0)
    {
        $productInfoField = 'id,image,price,ot_price,vip_price,postage,give_integral,sales,stock,store_name,unit_name,is_show,is_del,is_postage,cost,is_sub,temp_id';
        $seckillInfoField = 'id,image,price,ot_price,postage,give_integral,sales,stock,title as store_name,unit_name,is_show,is_del,is_postage,cost,temp_id,weight,volume,start_time,stop_time,time_id';
        $bargainInfoField = 'id,image,min_price as price,price as ot_price,postage,give_integral,sales,stock,title as store_name,unit_name,status as is_show,is_del,is_postage,cost,temp_id,weight,volume';
        $combinationInfoField = 'id,image,price,postage,sales,stock,title as store_name,is_show,is_del,is_postage,cost,temp_id,weight,volume';
        $model = new self();
        $valid = $invalid = [];
        $model = $model->where('uid',$uid)->where('type','product')->where('is_pay',0)
            ->where('is_del',0);
        if(!$status) $model = $model->where('is_new',0);
        if($cartIds) $model = $model->where('id','IN',$cartIds);
        $model = $model->order('add_time DESC');
        $list = $model->select()->toArray();
        if(!count($list)) return compact('valid','invalid');
        $now = time();
        foreach ($list as $k=>$cart){
            if($cart['seckill_id']){
                $product = StoreSeckill::field($seckillInfoField)
                    ->find($cart['seckill_id'])->toArray();
            }elseif($cart['bargain_id']){
                $product = StoreBargain::field($bargainInfoField)
                    ->find($cart['bargain_id'])->toArray();
            }elseif($cart['combination_id']){
                $product = StoreCombination::field($combinationInfoField)
                    ->find($cart['combination_id'])->toArray();
            }elseif($cart['user_stock_id']){
                $product = StoreProduct::field($productInfoField)
                    ->find($cart['product_id'])->toArray();
            }elseif($cart['takeout_id']){
                $product = StoreProduct::field($productInfoField)
                    ->find($cart['product_id'])->toArray();
            }else{
                $product = StoreProduct::field($productInfoField)
                    ->find($cart['product_id'])->toArray();
            }
            $product['image'] = set_file_url($product['image']);
            $cart['productInfo'] = $product;

            //商品不存在
            if(!$product){
                $model->where('id',$cart['id'])->update(['is_del'=>1]);
            //商品删除或无库存
            }else if(!$product['is_show'] || $product['is_del'] || !$product['stock']) {
                $invalid[] = $cart;

            //秒杀产品未开启或者已结束
            } else if ($cart['seckill_id'] && ($product['start_time'] > $now || $product['stop_time'] < $now)) {
                $invalid[] = $product;
                
                //商品属性不对应
            } else if(!StoreProductAttr::issetProductUnique($cart['product_id'],$cart['product_attr_unique']) && !$cart['combination_id'] && !$cart['takeout_id'] && !$cart['seckill_id']&& !$cart['user_stock_id']&& !$cart['bargain_id']){
                $invalid[] = $cart;
            //正常商品
            }else{

                if ($cart['seckill_id']) {
                    $config = SystemGroupData::get($product['time_id']);
                    if ($config) {
                        $arr = json_decode($config->value, true);
                        $now_hour = date('H', time());
                        $start_hour = $arr['time']['value'];
                        $continued = $arr['continued']['value'];
                        $end_hour = $start_hour + $continued;
                        if ($start_hour > $now_hour) {
                            //'活动未开启';
                            $invalid[] = $cart;
                            continue;
                        } elseif ($end_hour < $now_hour) {
                            //'活动已结束';
                            $invalid[] = $cart;
                            continue;
                        }
                    }

                }

                if($cart['product_attr_unique']){
                    $attrInfo = StoreProductAttr::uniqueByAttrInfo($cart['product_attr_unique']);
                    if ($cart['user_stock_id']) {
                        $userStock = Db::table('eb_user_stock')->where('id', $cart['user_stock_id'])->find();
                        $attrInfo['price'] = $userStock['price'];
                    }
                    if ($cart['takeout_id']) {
                        $userStock = Db::table('eb_user_stock')->where('id', $cart['takeout_id'])->find();
                        $attrInfo['price'] = 0;
                    }
                    //商品没有对应的属性
                    if(!$attrInfo || !$attrInfo['stock'])
                        $invalid[] = $cart;
                    else{
                        $cart['productInfo']['attrInfo'] = $attrInfo;
                        if($cart['combination_id'] || $cart['seckill_id'] || $cart['bargain_id']) {
                            $cart['truePrice'] = $attrInfo['price'];
                            $cart['vip_truePrice'] = 0;
                        }else {
                            $cart['truePrice'] = (float)StoreProduct::setLevelPrice($attrInfo['price'],$uid,true);
                            $cart['vip_truePrice'] = (float)StoreProduct::setLevelPrice($attrInfo['price'], $uid);
                        }
                        $cart['trueStock'] = $attrInfo['stock'];
                        $cart['costPrice'] = $attrInfo['cost'];
                        $cart['productInfo']['image'] = empty($attrInfo['image']) ? $cart['productInfo']['image'] : $attrInfo['image'];
                        if ($cart['user_stock_id']) {
                            $userStock = Db::table('eb_user_stock')->where('id', $cart['user_stock_id'])->find();
                            $attrInfo['price'] = $userStock['price'];
                            $cart['truePrice'] = $userStock['price'];
                            $cart['trueStock'] = $userStock['stock'];
                            $cart['vip_truePrice'] = 0;
                        }
                        if ($cart['takeout_id']) {
                            $userStock = Db::table('eb_user_stock')->where('id', $cart['takeout_id'])->find();
                            $attrInfo['price'] = 0;
                            $cart['truePrice'] = 0;
                            $cart['trueStock'] = $userStock['stock'];
                            $cart['vip_truePrice'] = 0;
                        }
                        $valid[] = $cart;
                    }
                }else{
                    if($cart['combination_id'] || $cart['seckill_id'] || $cart['bargain_id']) {
                        $cart['truePrice'] = $cart['productInfo']['price'];
                        $cart['vip_truePrice'] = 0;
                        if($cart['bargain_id']){
                            $cart['productInfo']['attrInfo'] = StoreProductAttrValue::where('product_id',$cart['bargain_id'])->where('type',2)->find();
                        }
                        $cart['productInfo']['attrInfo']['weight'] = $product['weight'];
                        $cart['productInfo']['attrInfo']['volume'] = $product['volume'];
                    } else {
                        $cart['truePrice'] = (float)StoreProduct::setLevelPrice($cart['productInfo']['price'],$uid,true);
                        $cart['vip_truePrice'] = (float)StoreProduct::setLevelPrice($cart['productInfo']['price'], $uid);
                    }
                    $cart['trueStock'] = $cart['productInfo']['stock'];
                    $cart['costPrice'] = $cart['productInfo']['cost'];
                    $valid[] = $cart;
                }
            }
          
        }
        
        foreach ($valid as $k=>$cart){
            if($cart['trueStock'] < $cart['cart_num']){
                $cart['cart_num'] = $cart['trueStock'];
                $model->where('id',$cart['id'])->update(['cart_num'=>$cart['cart_num']]);
                $valid[$k] = $cart;
            }

            unset($valid[$k]['uid'],$valid[$k]['is_del'],$valid[$k]['is_new'],$valid[$k]['is_pay'],$valid[$k]['add_time']);
            if(isset($valid[$k]['productInfo'])){
                unset($valid[$k]['productInfo']['is_del'],$valid[$k]['productInfo']['is_del'],$valid[$k]['productInfo']['is_show']);
            }
        }
        foreach ($invalid as $k=>$cart){
            unset($valid[$k]['uid'],$valid[$k]['is_del'],$valid[$k]['is_new'],$valid[$k]['is_pay'],$valid[$k]['add_time']);
            if(isset($invalid[$k]['productInfo'])){
                unset($invalid[$k]['productInfo']['is_del'],$invalid[$k]['productInfo']['is_del'],$invalid[$k]['productInfo']['is_show']);
            }
        }

        foreach ($valid as &$d) {
            $suk = $d['productInfo']['attrInfo']['suk'];
            $user = Db::table('eb_user')->where('uid', $uid)->find();
            $minShopPrice = Db::table('eb_shop_price')->where(['suk' => $suk, 'product_id' => $d['product_id'], 'shop_id' => $user['shop_id']])->find();
            if (!empty($minShopPrice)) {
                $d['truePrice'] = $minShopPrice['price'];
                $d['productInfo']['price'] = $minShopPrice['price'];
                $d['productInfo']['attrInfo']['price'] = $minShopPrice['price'];
            }
                
        }

        return compact('valid','invalid');
    }

    /**
     * 拼团
     * @param $uid
     * @param string $cartIds
     * @return array
     */
    public static function getUserCombinationProductCartList($uid,$cartIds='')
    {
        $productInfoField = 'id,image,slider_image,price,cost,ot_price,vip_price,postage,mer_id,give_integral,cate_id,sales,stock,store_name,unit_name,is_show,is_del,is_postage';
        $model = new self();
        $valid = $invalid = [];
        $model = $model->where('uid',$uid)->where('type','product')->where('is_pay',0)
            ->where('is_del',0);
        if($cartIds) $model->where('id','IN',$cartIds);
        $list = $model->select()->toArray();
        if(!count($list)) return compact('valid','invalid');
        foreach ($list as $k=>$cart){
            $product = StoreProduct::field($productInfoField)
                ->find($cart['product_id'])->toArray();
            $cart['productInfo'] = $product;
            //商品不存在
            if(!$product){
                $model->where('id',$cart['id'])->update(['is_del'=>1]);
            //商品删除或无库存
            }else if(!$product['is_show'] || $product['is_del'] || !$product['stock']){
                $invalid[] = $cart;
            //商品属性不对应
//            }else if(!StoreProductAttr::issetProductUnique($cart['product_id'],$cart['product_attr_unique'])){
//                $invalid[] = $cart;
            //正常商品
            }else{
                $cart['truePrice'] = (float)StoreCombination::where('id',$cart['combination_id'])->value('price');
                $cart['costPrice'] = (float)StoreCombination::where('id',$cart['combination_id'])->value('cost');
                $cart['trueStock'] = StoreCombination::where('id',$cart['combination_id'])->value('stock');
                $valid[] = $cart;
            }
        }

        foreach ($valid as $k=>$cart){
            if($cart['trueStock'] < $cart['cart_num']){
                $cart['cart_num'] = $cart['trueStock'];
                $model->where('id',$cart['id'])->update(['cart_num'=>$cart['cart_num']]);
                $valid[$k] = $cart;
            }
        }

        return compact('valid','invalid');
    }

    /**
     * 产品编号
     * @param array $ids
     * @return array
     */
    public static function getCartIdsProduct(array $ids)
    {
        return self::whereIn('id',$ids)->column('product_id','id');
    }

    /**
    *  获取购物车内最新一张产品图
     */
    public static function getProductImage(array $cart_id){
        return self::whereIn('a.id',$cart_id)->alias('a')->order('a.id desc')
            ->join('store_product p','p.id = a.product_id')->value('p.image');
    }

}