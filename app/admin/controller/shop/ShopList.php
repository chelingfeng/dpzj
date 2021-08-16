<?php

namespace app\admin\controller\shop;

use app\admin\controller\AuthController;
use think\facade\Db;

class ShopList extends AuthController
{
    /**
     * @return string
     */
    public function index()
    {
        $where = 'admin_id = '.$this->adminInfo->id.' AND del = 0';
        $_GET['page'] = empty($_GET['page']) ? 1 : $_GET['page'];
        $list = Db::table('eb_shop')->where($where)->page(1, 15)->select()->toArray();

        foreach ($list as &$v) {
            $v['admin'] = Db::table('eb_system_admin')->where('id = '.$v['admin_id'])->find();
        }
        $this->assign('list', $list);
        return $this->fetch();
    }

    public function add()
    {
        if (Db::table('eb_shop')->where(['username' => $_POST['username']])->find()) {
            return json(['code' => 1, 'msg' => '门店名称已存在']);
        }
        Db::table('eb_shop')->insert([
            'username' => $_POST['username'],
            'password' => md5('a123456'),
            'address' => $_POST['address'],
            'phone' => $_POST['phone'],
            'admin_id' => $this->adminInfo->id,
            'add_time' => date('Y-m-d H:i:s'),
            'update_time' => date('Y-m-d H:i:s'),
        ]);
        return json(['code' => 0]);
    }

    public function del()
    {
        Db::table('eb_shop')->where('id = '.$_POST['id'])->update(['del' => 1]);
        return json(['code' => 0]);
    }

    public function find()
    {
        $data = Db::table('eb_shop')->where('id = '.$_POST['id'])->find();
        return json(['code' => 0, 'data' => $data]);
    }

    public function save()
    {
        $data = [
            'username' => $_POST['username'],
            'address' => $_POST['address'],
            'phone' => $_POST['phone'],
            'update_time' => date('Y-m-d H:i:s'),
        ];
        if ($_POST['password']) {
            $data['password'] = md5($_POST['password']);
        }
        Db::table('eb_shop')->where('id = '.$_POST['id'])->update($data);
        return json(['code' => 0]);
    }
}