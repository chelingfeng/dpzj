<?php

namespace app\admin\controller\shop;

use app\admin\controller\AuthController;
use think\facade\Db;

class ShopProduct extends AuthController
{
    /**
     * @return string
     */
    public function index()
    {
        $bpIds = [];
        $bp = Db::table('eb_brand_product')->where('admin_id', $this->adminInfo->id)->select();
        foreach ($bp as $b) {
            $bpIds[] = $b['product_id'];
        }

        $db = Db::table('eb_store_product')->where('id', 'in', $bpIds);
        if (!empty($_POST['keyword'])) {
            $db->where('store_name', 'like', '%'.$_POST['keyword'].'%');
        }
        $goods = $db->column('id, store_name');
        

        $where = 'admin_id = '.$this->adminInfo->id;
        if (!empty($_POST['shop_id'])) {
            $where .= ' AND id = '.$_POST['shop_id'];
        }
        $shops = Db::table('eb_shop')->where($where)->order('admin_id ASC')->select()->toArray();

        foreach ($shops as &$shop) {
            $shop['hideProduct'] =  Db::table('eb_shop_product_hide')->where('shop_id', $shop['id'])->column('product_id');  
        }

        $where = 'admin_id = '.$this->adminInfo->id;
        $sshops = Db::table('eb_shop')->where($where)->select()->toArray();
        $this->assign('shops', $shops);
        $this->assign('goods', $goods);
        $this->assign('sshops', $sshops);
        return $this->fetch();
    }

    public function save()
    {
        $result = Db::table('eb_shop_product_hide')->where('product_id', $_POST['product_id'])->where('admin_id', $_POST['admin_id'])->where('shop_id', $_POST['shop_id'])->find();
        if ($result) {
            Db::table('eb_shop_product_hide')->where('product_id', $_POST['product_id'])->where('admin_id', $_POST['admin_id'])->where('shop_id', $_POST['shop_id'])->delete();
        } else {
            Db::table('eb_shop_product_hide')->insert($_POST);
        }
    }
}