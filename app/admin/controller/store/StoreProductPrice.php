<?php

namespace app\admin\controller\store;

use app\Request;
use think\facade\Db;
use app\admin\controller\AuthController;

class StoreProductPrice extends AuthController
{
    public function index(Request $request)
    {
        $where1 = 'is_del = 0';
        if (!empty($_POST['keyword'])) {
            $where1 .= " AND store_name LIKE '%".$_POST['keyword']."%'";
        }
        $goods = Db::table('eb_store_product')->where($where1)->column('id, store_name');
        $goodsIds = [];
        foreach ($goods as $g) {
            $goodsIds[] = $g['id'];
        }
        $suks = Db::table('eb_store_product_attr_value')->where('product_id', 'in', $goodsIds)->order('product_id ASC')->select()->toArray();
        foreach ($suks as &$suk) {
            $suk['product'] = $goods[$suk['product_id']];
        }
        
        $brandProducts = Db::table('eb_brand_product')->where([])->select()->toArray();
        $bpGroupByAdminId = [];
        foreach ($brandProducts as $brandProduct) {
            if (!isset($bpGroupByAdminId[$brandProduct['admin_id']])) {
                $bpGroupByAdminId[$brandProduct['admin_id']] = [];
                
            }
            $bpGroupByAdminId[$brandProduct['admin_id']][] = $brandProduct['product_id'];
        }
        
        $where = 'del = 0';
        if (!empty($_POST['admin_id'])) {
            $where .= ' AND admin_id = '.$_POST['admin_id'];
        }
        if (!empty($_POST['shop_id'])) {
            $where .= ' AND id = '.$_POST['shop_id'];
        }

        $shops = Db::table('eb_shop')->where($where)->order('admin_id ASC')->select()->toArray();
        foreach ($shops as &$shop) {
            // eb_brand_product - eb_shop_product_hide
            $brandProductIds = $bpGroupByAdminId[$shop['admin_id']] ?? [];
            $hides = Db::table('eb_shop_product_hide')->where('shop_id = '.$shop['id'])->column('product_id');
            foreach ($hides as $hide) {
                foreach ($brandProductIds as $key => $brandProduct) {
                    if ($hide == $brandProduct) {
                        unset($brandProductIds[$key]);
                    }
                }
            }
            
          
            $brandProductIds = array_values($brandProductIds);
            $prices = Db::table('eb_shop_price')->where('shop_id = '.$shop['id'])->select()->toArray();
            $newBp = [];
            foreach ($brandProductIds as $brandProductId) {
                $newBp[$brandProductId] = [];
                foreach ($prices as $price) {
                    if (isset($newBp[$price['product_id']])) {
                        $newBp[$price['product_id']][$price['suk']] = $price['price'];
                    }
                }
            }
            $shop['products'] = $newBp;
            $shop['admin'] = Db::table('eb_system_admin')->where('id = '.$shop['admin_id'])->find();
        }

        $where = 'id > 0';
        if (!empty($_POST['admin_id'])) {
            $where .= ' AND admin_id = '.$_POST['admin_id'];
        }
        $sshops = Db::table('eb_shop')->where($where)->select()->toArray();
        
        $this->assign('shops', $shops);
        $this->assign('suks', $suks);
        $this->assign('admins', Db::table('eb_system_admin')->where("roles = '7'")->select()->toArray());
        $this->assign('sshops', $sshops);
        return $this->fetch();
    }

    public function save()
    {
        $where = "product_id = ".$_POST['product_id']." AND suk = '".$_POST['suk']."'";
        $suk = Db::table('eb_store_product_attr_value')->where($where)->find();

        $where = "product_id = ".$_POST['product_id']." AND suk = '".$_POST['suk']."' AND shop_id = ".$_POST['shop_id']." AND admin_id = ".$_POST['admin_id'];
        Db::table('eb_shop_price')->where($where)->delete();

        if ($suk['price'] != $_POST['price']) {
            Db::table('eb_shop_price')->insert($_POST);
        }
    }
}