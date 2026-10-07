<?php

namespace app\massage\controller;

use app\AdminRest;
use app\massage\model\Config;
use app\massage\model\Order;
use app\massage\model\Service;
use app\massage\model\StoreService;
use app\shop\model\Article;
use app\shop\model\Banner;
use app\shop\model\Cap;
use app\shop\model\GoodsCate;
use app\shop\model\GoodsSh;
use app\shop\model\GoodsShList;
use longbingcore\wxcore\aliyun;
use longbingcore\wxcore\aliyunVirtual;
use think\App;
use app\shop\model\Goods as Model;
use app\massage\model\ServiceType;
use think\Db;


class AdminService extends AdminRest
{


    protected $model;

    protected $goods_sh;

    protected $goods_sh_list;

    public function __construct(App $app)
    {

        parent::__construct($app);

        $this->model = new Service();


    }


    /**
     * @author chenniang
     * @DataTime: 2021-03-15 14:43
     * @功能说明:商品列表
     */
    public function serviceList()
    {


        $input = $this->_param;

        $dis[] = ['uniacid', '=', $this->_uniacid];

        $type = !empty($input['type']) ? $input['type'] : 1;

        if (!empty($input['status'])) {

            $dis[] = ['status', '=', $input['status']];

        } else {

            $dis[] = ['status', '>', -1];

        }

        if (!empty($input['name'])) {

            $dis[] = ['title', 'like', '%' . $input['name'] . '%'];

        }

        if ($type == 1) {

            $dis[] = ['check_status', '=', 2];

        } else {

            $dis[] = ['type', '=', $type];

        }

        if (!empty($input['check_status'])) {

            $dis[] = ['check_status', '=', $input['check_status']];
        }

        if ($this->_user['is_admin'] == 0) {

            $dis[] = ['admin_id', '=', $this->_user['id']];

        }

        if (!empty($input['start_time']) && !empty($input['end_time'])) {

            $dis[] = ['create_time', 'between', "{$input['start_time']},{$input['end_time']}"];

        }

        $is_add = !empty($input['is_add']) ? $input['is_add'] : 0;

        $dis[] = ['is_add', '=', $is_add];

        $data = $this->model->dataList($dis, $input['limit']);

        if (!empty($data['data'])) {

            $admin_model = new \app\massage\model\Admin();

            foreach ($data['data'] as &$v) {

                if (!empty($v['admin_id'])) {

                    $v['admin_name'] = $admin_model->where(['id' => $v['admin_id']])->value('agent_name');
                }

            }
        }

        $list = [

            0 => 'all',

            1 => 'ing',

            2 => 'pass',

            3 => 'nopass',
        ];

        foreach ($list as $k => $value) {

            $dis = [

                'uniacid' => $this->_uniacid,

                'type' => 2

            ];

            if ($this->_user['is_admin'] == 0) {

                $dis['admin_id'] = $this->_user['id'];
            }

            if (!empty($k)) {

                $dis['check_status'] = $k;
            }

            $data[$value] = $this->model->where($dis)->where('status', '>', -1)->count();
        }

        return $this->success($data);

    }


    /**
     * @author chenniang
     * @DataTime: 2021-03-15 14:58
     * @功能说明:审核详情
     */
    public function serviceInfo()
    {

        $input = $this->_param;

        $dis = [

            'id' => $input['id']
        ];

        $data = $this->model->dataInfo($dis);

        $store_model = new StoreService();

        $data['store'] = $store_model->getServiceStore($input['id']);

        return $this->success($data);

    }


    /**
     * @author chenniang
     * @DataTime: 2021-07-03 00:27
     * @功能说明:添加
     */
    public function serviceAdd()
    {

        $input = $this->_input;

        $input['uniacid'] = $this->_uniacid;

        if (!empty($this->_user['is_admin'] == 0)) {

            $input['type'] = 2;

            $input['check_status'] = 1;

            $input['admin_id'] = $this->_user['id'];

        }

        $res = $this->model->dataAdd($input);

        return $this->success($res, 200, $res);

    }


    /**
     * @author chenniang
     * @DataTime: 2021-07-03 00:27
     * @功能说明:添加
     */
    public function serviceUpdate()
    {

        $input = $this->_input;

        $input['uniacid'] = $this->_uniacid;

        $dis = [

            'id' => $input['id']
        ];

        $res = $this->model->dataUpdate($dis, $input);

        return $this->success($res);

    }


    /**
     * @author chenniang
     * @DataTime: 2023-03-26 21:42
     * @功能说明:审核门店服务
     */
    public function checkStoreGoods()
    {

        $input = $this->_input;

        $dis = [

            'id' => $input['id']
        ];

        $data = $this->model->dataInfo($dis);

        if ($data['check_status'] != 1) {

            $this->errorMsg('服务已经审核');
        }

        $update = [

            'check_status' => $input['check_status'],

            'check_text' => $input['check_text'],

            'check_time' => time()
        ];

        $res = $this->model->dataUpdate($dis, $update);

        return $this->success($res);

    }

    /**
     * @Desc: 插入
     * @return mixed
     * @Auther: shurong
     * @Time: 2023/11/20 11:31
     */
    public function typeAdd()
    {
        $data = request()->only(['name', 'top']);

        $data['name'] = trim($data['name']);

        $data['uniacid'] = $this->_uniacid;

        if (ServiceType::where(['status' => 1, 'name' => $data['name']])->count() > 0) {

            return $this->error('此分类已存在');
        }

        $res = ServiceType::add($data);

        if ($res) {
            return $this->success('');
        }
        return $this->error('');
    }

    /**
     * @Desc: 分类编辑
     * @return mixed
     * @Auther: shurong
     * @Time: 2023/11/20 11:48
     */
    public function typeUpdate()
    {
        $data = request()->only(['id', 'name', 'top', 'status']);

        if (isset($data['name']) && ServiceType::where([['status', '=', 1], ['name', '=', $data['name']], ['id', '<>', $data['id']]])->count() > 0) {

            return $this->error('此分类已存在');
        }

        if (isset($data['status']) && $data['status'] == -1) {

            Service::where('service_type', $data['id'])->update(['service_type' => 0]);
        }

        $res = ServiceType::update($data, ['id' => $data['id']]);

        return $this->success($res);
    }

    /**
     * @Desc: 分类列表
     * @return mixed
     * @throws \think\db\exception\DbException
     * @Auther: shurong
     * @Time: 2023/11/20 11:55
     */
    public function typeList()
    {
        $name = request()->param('name', '');

        $title = request()->param('title', '');

        $limit = request()->param('limit', 10);

        $status = request()->param('status', '');

        $where = [
            ['uniacid', '=', $this->_uniacid],
            ['status', '>', -1]
        ];

        if (!empty($name)) {

            $where[] = ['name', 'like', '%' . $name . '%'];
        }
        if (!empty($title)) {

            $where[] = ['name', 'like', '%' . $title . '%'];
        }

        if (!empty($status)) {

            $where[] = ['status', '=', $status];
        }

        $data = ServiceType::getList($where, $limit);

        if (!empty($data['data'])) {

            foreach ($data['data'] as &$datum) {

                $datum['title'] = $datum['name'];
            }
        }

        return $this->success($data);
    }


    /**
     * @Desc:列表不分页
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @Auther: shurong
     * @Time: 2024/6/19 14:00
     */
    public function typeListNoPage()
    {
        $name = request()->param('name', '');

        $where = [
            ['uniacid', '=', $this->_uniacid],
            ['status', '=', 1]
        ];

        if (!empty($name)) {

            $where[] = ['name', 'like', '%' . $name . '%'];
        }

        $data = ServiceType::getListNoPage($where);

        return $this->success($data);
    }
}
