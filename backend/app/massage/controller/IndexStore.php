<?php


namespace app\massage\controller;


use app\ApiRest;
use app\massage\model\StoreApply;
use app\massage\model\StoreApplyUpdate;
use app\massage\model\StoreType;
use app\store\model\PackageOrder;
use app\store\model\PackageOrderComment;
use think\App;
use think\facade\Db;

class IndexStore extends ApiRest
{
    public function __construct(App $app)
    {
        parent::__construct($app);
    }

    /**
     * 添加
     * @return mixed
     */
    public function apply()
    {
        $data = $this->request->only(['name', 'cover', 'banner', 'mobile', 'license', 'lng', 'lat', 'address', 'info', 'tag', 'status', 'intro', 'type_id', 'trade_week', 'start_time', 'end_time', 'contact_type', 'qywx_kid']);
        $uid = $this->getUserId();
        $info = StoreApply::getInfo([['user_id', '=', $uid], ['status', '>', -1]]);
        if (!empty($info) && in_array($info['status'], [1, 2, 3])) {
            return $this->error('已申请');
        }
        $data['uniacid'] = $this->_uniacid;
        $data['user_id'] = $uid;
        $data['status'] = 1;
        if (!empty($info) && $info['status'] == 4) {
            $res = StoreApply::update($data, ['id' => $info['id']]);
        } else {
            $res = StoreApply::add($data);
        }
        if ($res) {
            return $this->success($res);
        }
        return $this->error('申请失败');
    }

    /**
     * 获取门店
     * @return mixed
     */
    public function getStore()
    {
        $type = request()->param('time_type', 1);

        $data = StoreApply::getInfo([['user_id', '=', $this->getUserId()], ['status', '>', -1]]);
        $data = empty($data) ? '' : $data;

        if ($type == 1) {

            $timestamp = time();
            $start = strtotime(date('Y-m-d', strtotime("this week Monday", $timestamp)));
            $end = strtotime(date('Y-m-d', strtotime("this week Sunday", $timestamp))) + 24 * 3600 - 1;
        } else if ($type == 2) {

            $start = mktime(0, 0, 0, date('m'), 1, date('Y'));
            $end = mktime(23, 59, 59, date('m'), date('t'), date('Y'));
        }

        $data = PackageOrder::getShopData($data, $start, $end);

        if (!empty($data)){

            $data['qywx_company_id'] = getConfigSetting($this->_uniacid, 'qywx_company_id');
        }

        return $this->success($data);
    }

    /**
     * 编辑
     * @return mixed
     */
    public function edit()
    {
        $data = $this->request->only(['store_id', 'name', 'cover', 'banner', 'mobile', 'license', 'lng', 'lat', 'address', 'info', 'tag', 'status', 'intro', 'type_id', 'trade_week', 'start_time', 'end_time', 'contact_type', 'qywx_kid']);
        $store = StoreApply::getInfo([['user_id', '=', $this->getUserId()], ['status', '>', -1]]);
        if (empty($store) || $store['status'] == -1) {
            return $this->error('门店不存在或已删除');
        }
        $data['uniacid'] = $this->_uniacid;
        $data['user_id'] = $this->getUserId();
        $res = StoreApplyUpdate::edit($data);
        if ($res) {
            return $this->success($res);
        }
        return $this->error('申请失败');
    }

    /**
     * 列表
     * @return mixed
     * @throws \think\db\exception\DbException
     */
    public function getList()
    {
        $input = $this->_param;
        $where = [
            ['uniacid', '=', $this->_uniacid],
            ['status', '=', 2]
        ];
        if (!empty($input['name'])) {

            $where[] = ['name', 'like', '%' . $input['name'] . '%'];
        }

        if (!empty($input['type_id'])) {

            $where[] = ['', 'exp', Db::raw("find_in_set({$input['type_id']},type_id)")];
        }
        $alh = 'ACOS(SIN((' . $input['lat'] . ' * 3.1415) / 180 ) *SIN((lat * 3.1415) / 180 ) +COS((' . $input['lat'] . ' * 3.1415) / 180 ) * COS((lat * 3.1415) / 180 ) *COS((' . $input['lng'] . ' * 3.1415) / 180 - (lng * 3.1415) / 180 ) ) * 6378.137*1000 as distance';
        $data = StoreApply::getIndexList($where, $alh, $input['limit']);
        return $this->success($data);
    }

    /**
     * 详情
     * @return mixed
     */
    public function getInfo()
    {
        $id = $this->request->param('id', '');

        $data = StoreApply::getInfo(['id' => $id]);

        if (empty($data)) {
            return $this->error('门店不存在');
        }

        $data['type_name'] = StoreType::where('id', 'in', explode(',', $data['type_id']))->column('name');

        $status = getTradeStatus($data);

        $data = array_merge($data->toArray(), $status);

        $data['qywx_company_id'] = getConfigSetting($this->_uniacid, 'qywx_company_id');

        return $this->success($data);
    }

    /**
     * @Desc: 店铺分类
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @Auther: shurong
     * @Time: 2023/11/20 17:33
     */
    public function storeTypeList()
    {
        $where = [
            ['uniacid', '=', $this->_uniacid],
            ['status', '=', 1]
        ];

        $data = StoreType::getListNoPage($where);

        return $this->success($data);
    }

    /**
     * @Desc: 门店评价列表
     * @return mixed
     * @Auther: shurong
     * @Time: 2023/12/5 10:59
     */
    public function commentList()
    {
        $limit = request()->param('limit', 5);
        $type = request()->param('type', 0);
        $store_id = request()->param('store_id', 0);

        $where = [
            ['a.uniacid', '=', $this->_uniacid],
            ['a.status', '=', 1],
            ['a.store_id', '=', $store_id]
        ];

        if ($type == 1) {

            $where[] = ['a.star', '>', 3];
        } elseif ($type == 2) {

            $where[] = ['a.star', '<=', 3];
        }

        $data = PackageOrderComment::getList($where, $limit);

        $where = [
            ['uniacid', '=', $this->_uniacid],
            ['status', '=', 1],
            ['store_id', '=', $store_id],
            ['star', '>', 3]
        ];
        $data['star_good'] = PackageOrderComment::getCount($where);

        $where = [
            ['uniacid', '=', $this->_uniacid],
            ['status', '=', 1],
            ['store_id', '=', $store_id],
            ['star', '<=', 3]
        ];
        $data['star_bad'] = PackageOrderComment::getCount($where);

        return $this->success($data);
    }
}