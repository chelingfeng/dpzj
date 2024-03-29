<?php

namespace app\api\controller\store;

use app\admin\model\store\StoreDescription;
use app\admin\model\system\SystemAttachment;
use app\models\store\StoreOrder;
use app\models\store\StoreVisit;
use app\models\system\SystemStore;
use app\models\store\StoreProduct;
use app\models\store\StoreProductAttr;
use app\models\store\StoreProductRelation;
use app\models\store\StoreProductReply;
use app\models\user\User;
use app\Request;
use crmeb\services\GroupDataService;
use crmeb\services\QrcodeService;
use crmeb\services\SystemConfigService;
use crmeb\services\UtilService;
use crmeb\services\upload\Upload;
use think\facade\Db;

/**
 * 商品类
 * Class StoreProductController
 * @package app\api\controller\store
 */
class StoreProductController
{
    /**
     * 商品列表
     * @param Request $request
     * @return mixed
     */
    public function lst(Request $request)
    {
        $data = UtilService::getMore([
            ['sid', 0],
            ['cid', 0],
            ['keyword', ''],
            ['priceOrder', ''],
            ['salesOrder', ''],
            ['news', 0],
            ['page', 0],
            ['limit', 0],
            ['type', 0]
        ], $request);
        
        $user = Db::table('eb_user')->where('uid', $request->uid())->find();
        if (isSupplyChain()) {
            $data['ids'] = [-1];
            if (!empty($user['shop_id'])) {
                $shop = Db::table('eb_shop')->where('id', $user['shop_id'])->find();
                $brandProducts = Db::table('eb_brand_product')->where('admin_id', $shop['admin_id'])->select()->toArray();
                $hides = Db::table('eb_shop_product_hide')->where('shop_id', $shop['id'])->select()->toArray();
                $hIds = [];
                foreach ($hides as $hide) {
                    $hIds[] = $hide['product_id'];
                }
                foreach ($brandProducts as $brandProduct) {
                    if (!in_array($brandProduct['product_id'], $hIds)) {
                        $data['ids'][] = $brandProduct['product_id'];
                    }
                }
            }
            $data['ids'] = array_values($data['ids']);
        }
        
        $data = StoreProduct::getProductList($data, $request->uid());
        foreach ($data as &$v) {
            $minShopPrice = Db::table('eb_shop_price')->where(['product_id' => $v['id'], 'shop_id' => $user['shop_id']])->order('price ASC')->find();
            if (!empty($minShopPrice)) {
                $v['minPriceAttr'] = Db::table('eb_store_product_attr_value')->where(['product_id' => $v['id'], 'suk' => $minShopPrice['suk']])->order('price ASC')->find();
                $v['minPriceAttr']['price'] = $minShopPrice['price'];
                $v['price'] = $minShopPrice['price'];
            } else {
                $v['minPriceAttr'] = Db::table('eb_store_product_attr_value')->where('product_id', $v['id'])->order('price ASC')->find();
            }
        }

        return app('json')->successful($data);
    }

    /**
     * 产品分享二维码 推广员
     * @param Request $request
     * @param $id
     * @return mixed
     */
    public function code(Request $request, $id)
    {
        if (!$id || !($storeInfo = StoreProduct::getValidProduct($id, 'id'))) return app('json')->fail('商品不存在或已下架');
        $userType = $request->get('user_type', 'wechat');
        $user = $request->user();
        try {
            switch ($userType) {
                case 'wechat':
                    //公众号
                    $name = $id . '_product_detail_' . $user['uid'] . '_is_promoter_' . $user['is_promoter'] . '_wap.jpg';
                    $url = QrcodeService::getWechatQrcodePath($name, '/detail/' . $id . '?spread=' . $user['uid']);
                    if ($url === false)
                        return app('json')->fail('二维码生成失败');
                    else
                        return app('json')->successful(['code' => image_to_base64($url)]);
                    break;
                case 'routine':
                    //小程序
                    $name = $id . '_' . $user['uid'] . '_' . $user['is_promoter'] . '_product.jpg';
                    $imageInfo = SystemAttachment::getInfo($name, 'name');
                    $siteUrl = sys_config('site_url');
                    if (!$imageInfo) {
                        $data = 'id=' . $id;
                        if ($user['is_promoter'] || sys_config('store_brokerage_statu') == 2) $data .= '&pid=' . $user['uid'];
                        $res = \app\models\routine\RoutineCode::getPageCode('pages/goods_details/index', $data, 280);
                        if (!$res) return app('json')->fail('二维码生成失败');
                        $uploadType = (int)sys_config('upload_type', 1);
                        $upload = new Upload($uploadType, [
                            'accessKey' => sys_config('accessKey'),
                            'secretKey' => sys_config('secretKey'),
                            'uploadUrl' => sys_config('uploadUrl'),
                            'storageName' => sys_config('storage_name'),
                            'storageRegion' => sys_config('storage_region'),
                        ]);
                        $res = $upload->to('routine/product')->validate()->stream($res, $name);
                        if ($res === false) {
                            return app('json')->fail($upload->getError());
                        }
                        $imageInfo = $upload->getUploadInfo();
                        $imageInfo['image_type'] = $uploadType;
                        if ($imageInfo['image_type'] == 1) $remoteImage = UtilService::remoteImage($siteUrl . $imageInfo['dir']);
                        else $remoteImage = UtilService::remoteImage($imageInfo['dir']);
                        if (!$remoteImage['status']) return app('json')->fail('小程序二维码未能生成');
                        SystemAttachment::attachmentAdd($imageInfo['name'], $imageInfo['size'], $imageInfo['type'], $imageInfo['dir'], $imageInfo['thumb_path'], 1, $imageInfo['image_type'], $imageInfo['time'], 2);
                        $url = $imageInfo['dir'];
                    } else $url = $imageInfo['att_dir'];
                    if ($imageInfo['image_type'] == 1) $url = $siteUrl . $url;
                    return app('json')->successful(['code' => $url]);
            }
        } catch (\Exception $e) {
            return app('json')->fail($e->getMessage(), [
                'code' => $e->getCode(),
                'line' => $e->getLine(),
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * 产品详情
     * @param Request $request
     * @param $id
     * @param int $type
     * @return mixed
     */
    public function detail(Request $request, $id, $type = 0)
    {
        if (!$id || !($storeInfo = StoreProduct::getValidProduct($id))) return app('json')->fail('商品不存在或已下架');
        $siteUrl = sys_config('site_url');
        $storeInfo['image'] = set_file_url($storeInfo['image'], $siteUrl);
        $storeInfo['image_base'] = set_file_url($storeInfo['image'], $siteUrl);
        $storeInfo['code_base'] = QrcodeService::getWechatQrcodePath($id . '_product_detail_wap.jpg', '/detail/' . $id);
        $uid = $request->uid();
        $data['uid'] = $uid;
        $storeInfo['description'] = htmlspecialchars_decode(StoreDescription::getDescription($id));
        //替换windows服务器下正反斜杠问题导致图片无法显示
        $storeInfo['description'] = preg_replace_callback('#<img.*?src="([^"]*)"[^>]*>#i', function ($imagsSrc) {
            return isset($imagsSrc[1]) && isset($imagsSrc[0]) ? str_replace($imagsSrc[1], str_replace('\\', '/', $imagsSrc[1]), $imagsSrc[0]) : '';
        }, $storeInfo['description']);
        $storeInfo['userCollect'] = StoreProductRelation::isProductRelation($id, $uid, 'collect');
        $storeInfo['userLike'] = StoreProductRelation::isProductRelation($id, $uid, 'like');
        list($productAttr, $productValue) = StoreProductAttr::getProductAttrDetail($id, $uid, $type);
        StoreVisit::setView($uid, $id, $storeInfo['cate_id'], 'viwe');
        $data['storeInfo'] = StoreProduct::setLevelPrice($storeInfo, $uid, true);
        $data['similarity'] = StoreProduct::cateIdBySimilarityProduct($storeInfo['cate_id'], 'id,store_name,image,price,sales,ficti', 4);
        $data['productAttr'] = $productAttr;
        $data['productValue'] = $productValue;

        $user = Db::table('eb_user')->where('uid', $request->uid())->find();
        foreach ($data['productValue'] as &$p) {
            $minShopPrice = Db::table('eb_shop_price')->where(['suk' => $p['suk'], 'product_id' => $id, 'shop_id' => $user['shop_id']])->find();
            if (!empty($minShopPrice)) {
                $p['price'] = $minShopPrice['price'];
            }
        }

        $data['storeInfo']['vip_price'] = 0; //不显示vip价格
       
        $minShopPrice = Db::table('eb_shop_price')->where(['product_id' => $id, 'shop_id' => $user['shop_id']])->order('price ASC')->find();
        if (!empty($minShopPrice)) {
            $data['storeInfo']['price'] = $minShopPrice['price'];
        }

        $data['priceName'] = 0;
        if (!$data['storeInfo']['spec_type'] && !empty($productAttr)) {
            StoreProduct::where('id', $id)->update(['spec_type' => 1]);
        }
        if ($uid) {
            $user = $request->user();
            if (!$user->is_promoter) {
                $price = StoreOrder::where(['paid' => 1, 'refund_status' => 0, 'uid' => $uid])->sum('pay_price');
                $status = is_brokerage_statu($price);
                if ($status) {
                    User::where('uid', $uid)->update(['is_promoter' => 1]);
                    $user->is_promoter = 1;
                }
            }
            if ($user->is_promoter) {
                $data['priceName'] = StoreProduct::getPacketPrice($storeInfo, $productValue);
            }
            if (!strlen(trim($data['priceName'])))
                $data['priceName'] = 0;
        }
        $data['reply'] = StoreProductReply::getRecProductReply($storeInfo['id']);
        $data['replyCount'] = StoreProductReply::productValidWhere()->where('product_id', $storeInfo['id'])->count();
        if ($data['replyCount']) {
            $goodReply = StoreProductReply::productValidWhere()->where('product_id', $storeInfo['id'])->where('product_score', 5)->count();
            $data['replyChance'] = $goodReply;
            if ($goodReply) {
                $data['replyChance'] = bcdiv($goodReply, $data['replyCount'], 2);
                $data['replyChance'] = bcmul($data['replyChance'], 100, 2);
            }
        } else $data['replyChance'] = 0;
        $data['mer_id'] = $storeInfo['mer_id'];
        $data['system_store'] = ($res = SystemStore::getStoreDispose()) ? $res : [];
        $data['good_list'] = StoreProduct::getGoodList(18, 'image,store_name,price,id,ot_price');
        $data['mapKey'] = sys_config('tengxun_map_key');
        $data['store_self_mention'] = (int)sys_config('store_self_mention') ?? 0;//门店自提是否开启
        $data['activity'] = StoreProduct::activity($data['storeInfo']['id'], false);
        return app('json')->successful($data);
    }

    /**
     * 为你推荐
     *
     * @param Request $request
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function product_hot(Request $request)
    {
        list($page, $limit) = UtilService::getMore([
            ['page', 0],
            ['limit', 0]
        ], $request, true);
        if (!$limit) return app('json')->successful([]);
        $productHot = StoreProduct::getHotProductLoading('id,image,store_name,cate_id,price,unit_name,ot_price', (int)$page, (int)$limit);
        if (!empty($productHot)) {
            foreach ($productHot as $k => $v) {
                $productHot[$k]['activity'] = StoreProduct::activity($v['id']);
            }
        }
        return app('json')->successful($productHot);
    }

    /**
     * 获取首页推荐不同类型产品的轮播图和产品
     * @param Request $request
     * @param $type
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function groom_list(Request $request, $type)
    {
        $info['banner'] = [];
        $info['list'] = [];
        if ($type == 1) {//TODO 精品推荐
            $info['banner'] = sys_data('routine_home_bast_banner') ?: [];//TODO 首页精品推荐图片
            $info['list'] = StoreProduct::getBestProduct('id,image,store_name,cate_id,price,ot_price,IFNULL(sales,0) + IFNULL(ficti,0) as sales,unit_name,sort');//TODO 精品推荐个数
        } else if ($type == 2) {//TODO  热门榜单
            $info['banner'] = sys_data('routine_home_hot_banner') ?: [];//TODO 热门榜单 猜你喜欢推荐图片
            $info['list'] = StoreProduct::getHotProduct('id,image,store_name,cate_id,price,ot_price,unit_name,sort,IFNULL(sales,0) + IFNULL(ficti,0) as sales', 0, $request->uid());//TODO 热门榜单 猜你喜欢
        } else if ($type == 3) {//TODO 首发新品
            $info['banner'] = sys_data('routine_home_new_banner') ?: [];//TODO 首发新品推荐图片
            $info['list'] = StoreProduct::getNewProduct('id,image,store_name,cate_id,price,ot_price,unit_name,sort,IFNULL(sales,0) + IFNULL(ficti,0) as sales', 0, $request->uid());//TODO 首发新品
        } else if ($type == 4) {//TODO 促销单品
            $info['banner'] = sys_data('routine_home_benefit_banner') ?: [];//TODO 促销单品推荐图片
            $info['list'] = StoreProduct::getBenefitProduct('id,image,store_name,cate_id,price,ot_price,stock,unit_name,sort');//TODO 促销单品
        }
        return app('json')->successful($info);
    }

    /**
     * 产品评价数量和好评度
     * @param $id
     * @return mixed
     */
    public function reply_config($id)
    {
        if (!$id || !is_numeric($id)) return app('json')->fail('参数错误!');
        return app('json')->successful(StoreProductReply::productReplyCount($id));
    }

    /**
     * 获取产品评论
     * @param Request $request
     * @param $id
     * @param $type
     * @return mixed
     */
    public function reply_list(Request $request, $id)
    {
        list($page, $limit, $type) = UtilService::getMore([
            ['page', 0], ['limit', 0], ['type', 0]
        ], $request, true);
        if (!$id || !is_numeric($id)) return app('json')->fail('参数错误!');
        $list = StoreProductReply::getProductReplyList($id, (int)$type, $page, $limit);
        return app('json')->successful($list);
    }

}