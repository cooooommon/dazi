<?php


namespace app\massage\controller;


use app\admin\controller\Update;
use app\AdminRest;
use app\massage\model\Diy;
use app\massage\model\StoreApply;
use app\massage\model\StoreApplyUpdate;
use app\massage\model\StoreType;
use app\massage\model\User;
use app\massage\model\Wallet;
use app\store\model\PackageOrder;
use app\store\model\StorePackage;
use think\App;

class AdminStore extends AdminRest
{
    public function __construct(App $app)
    {
        parent::__construct($app);
    }

    /**
     * 列表
     * @return mixed
     * @throws \think\db\exception\DbException
     */
    public function getList()
    {
        (new Diy())->chancelBanner($this->_uniacid);

        $name = $this->request->param('name', '');
        $start_time = $this->request->param('start_time', '');
        $end_time = $this->request->param('end_time', '');
        $status = $this->request->param('status', '');
        $limit = $this->request->param('limit', '10');
        $is_update = $this->request->param('is_update', '');
        $where = [
            ['uniacid', '=', $this->_uniacid],
            ['status', '>', -1],
        ];
        if (!empty($name)) {
            $where[] = ['name', 'like', '%' . $name . '%'];
        }
        if (!empty($start_time) && !empty($end_time)) {
            $where[] = ['create_time', '>=', $start_time];
            $where[] = ['create_time', '<', $end_time];
        }
        if (!empty($status)) {
            $where[] = ['status', '=', $status];
        }
        if (!empty($is_update)) {
            $where[] = ['is_update', '=', 1];
        }
        $data = StoreApply::getList($where, $limit);
        $data['all_count'] = StoreApply::getCount([
            ['uniacid', '=', $this->_uniacid],
            ['status', '>', -1],
        ]);
        $data['apply_count'] = StoreApply::getCount([
            ['uniacid', '=', $this->_uniacid],
            ['status', '=', 1],
        ]);
        $data['pass_count'] = StoreApply::getCount([
            ['uniacid', '=', $this->_uniacid],
            ['status', '=', 2],
        ]);
        $data['update_count'] = StoreApply::getCount([
            ['uniacid', '=', $this->_uniacid],
            ['is_update', '=', 1],
            ['status', '>', -1],
        ]);
        $data['refuse_count'] = StoreApply::getCount([
            ['uniacid', '=', $this->_uniacid],
            ['status', '=', 4],
        ]);
        return $this->success($data);
    }

    /**
     * @Desc: diy列表
     * @return mixed
     * @throws \think\db\exception\DbException
     * @Auther: shurong
     * @Time: 2023/11/22 16:20
     */
    public function getListDiy()
    {
        $name = $this->request->param('title', '');

        $limit = $this->request->param('limit', '10');

        $where = [
            ['uniacid', '=', $this->_uniacid],
            ['status', '=', 2],
        ];

        if (!empty($name)) {
            $where[] = ['name', 'like', '%' . $name . '%'];
        }

        $data = StoreApply::getList($where, $limit);

        if (!empty($data['data'])) {
            foreach ($data['data'] as &$item) {
                $item['title'] = $item['name'];
            }
        }

        return $this->success($data);
    }

    /**
     * @Desc: 获取门店列表
     * @return mixed
     * @Auther: shurong(贝润网络)
     * @Time: 2024/9/26 11:02
     */
    public function getStoreList()
    {
        $where = [
            ['uniacid', '=', $this->_uniacid],
            ['status', '=', 2]
        ];

        $data = StoreApply::getListNoPage($where);

        return $this->success($data);
    }

    /**
     * 审核
     * @return mixed
     */
    public function check()
    {
        $data = $this->request->only(['id', 'status', 'check_msg']);
        $data['check_time'] = time();
        $res = StoreApply::update($data, ['id' => $data['id']]);
        if ($res === false) {
            return $this->error('审核失败');
        }
        return $this->success('');
    }

    /**
     * 详情
     * @return mixed
     */
    public function getInfo()
    {
        $id = $this->request->param('id', '');
        $data = StoreApply::getInfo(['id' => $id]);
        $data['user_name'] = User::where('id', $data['user_id'])->value('nickName');
        $data['type_name'] = StoreType::where('id', 'in', explode(',', $data['type_id']))->column('name');
        return $this->success($data);
    }

    /**
     * 重新审核详情
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function reInfo()
    {
        $id = $this->request->param('id', '');
        $data = StoreApplyUpdate::getInfo(['uniacid' => $this->_uniacid, 'store_id' => $id, 'status' => 1]);
        if (empty($data)) {

            StoreApply::update(['is_update' => 0], ['id' => $id]);
            return $this->error('无需审核');
        }
        $data['user_name'] = User::where('id', $data['user_id'])->value('nickName');
        $data['type_name'] = StoreType::where('id', 'in', explode(',', $data['type_id']))->column('name');
        return $this->success($data);
    }

    /**
     * 重新审核
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function reCheck()
    {
        $data = $this->request->only(['id', 'status', 'check_msg']);
        $data['check_time'] = time();
        $res = StoreApplyUpdate::update($data, ['id' => $data['id']]);
        $update = [
            'is_update' => 0
        ];
        $info = StoreApplyUpdate::getInfo(['id' => $data['id']]);
        if (empty($info)) {
            return $this->error('数据不存在');
        }
        $info = $info->toArray();
        $id = $info['store_id'];
        if ($data['status'] == 2) {
            $arr = [
                'name', 'cover', 'banner', 'mobile', 'license', 'lng', 'lat', 'address', 'info', 'tag', 'intro', 'check_time', 'check_time', 'type_id', 'trade_week', 'start_time', 'end_time', 'contact_type', 'qywx_kid'
            ];
            foreach ($info as $key => $item) {
                if (!in_array($key, $arr)) {
                    unset($info[$key]);
                }
            }
            $update = array_merge($update, $info);

            if (isset($update['trade_week'])) {

                StorePackage::where([['store_id', '=', $id], ['status', '>', -1]])->update(['use_trade_week' => $update['trade_week'], 'use_start_time' => $update['start_time'], 'use_end_time' => $update['end_time']]);
            }

        }
        StoreApply::update($update, ['id' => $id]);
        if ($res === false) {
            return $this->error('审核失败');
        }
        return $this->success('');
    }

    /**
     * 修改状态
     * @return mixed
     */
    public function changeStatus()
    {
        $data = $this->request->only(['id', 'status']);

        if ($data['status'] == -1) {

            $store = StoreApply::find($data['id']);

            if ($store['cash'] > 0) {

                return $this->error('此门店还有佣金未提现，不可删除');
            }

            $where = [
                ['status', '=', 2],

                ['store_id', '=', $data['id']]
            ];

            $order = PackageOrder::getFirst($where);

            if (!empty($order)) {

                return $this->error('此门店还有未完成的订单，不可删除。订单id：' . $order['id']);
            }

            $wallet_dis = [

                ['coach_id', '=', $data['id']],

                ['status', '=', 1],

                ['type', '=', 6]
            ];

            $wallet = (new Wallet())->dataInfo($wallet_dis);

            if (!empty($wallet)) {

                $this->errorMsg('此门店还有提现申请中，无法删除。提现id：' . $wallet['id']);

            }
        }

        $res = StoreApply::update($data, ['id' => $data['id']]);
        if ($res === false) {
            return $this->error('审核失败');
        }
        return $this->success('');
    }

    /**
     * @Desc: 插入
     * @return mixed
     * @Auther: shurong
     * @Time: 2023/11/20 11:31
     */
    public function typeAdd()
    {
        $data = request()->only(['name', 'img', 'top']);

        $data['name'] = trim($data['name']);

        $data['uniacid'] = $this->_uniacid;

        if (StoreType::where(['status' => 1, 'name' => $data['name']])->count() > 0) {

            return $this->error('此分类已存在');
        }

        $res = StoreType::add($data);

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
        $data = request()->only(['id', 'name', 'img', 'top', 'status']);

        if (isset($data['status']) && $data['status'] == -1) {

            StoreApply::cancel($data['id']);
        }

        if (isset($data['name']) && StoreType::where([['status', '=', 1], ['name', '=', $data['name']], ['id', '<>', $data['id']]])->count() > 0) {

            return $this->error('此分类已存在');
        }

        $res = StoreType::update($data, ['id' => $data['id']]);

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

        $where = [
            ['uniacid', '=', $this->_uniacid],
            ['status', '=', 1]
        ];

        if (!empty($name)) {

            $where[] = ['name', 'like', '%' . $name . '%'];
        }
        if (!empty($title)) {

            $where[] = ['name', 'like', '%' . $title . '%'];
        }

        $data = StoreType::getList($where, $limit);

        if (!empty($data['data'])) {

            foreach ($data['data'] as &$datum) {

                $datum['title'] = $datum['name'];
            }
        }

        return $this->success($data);
    }

    /**
     * @Desc: 添加门店
     * @return mixed
     * @Auther: shurong
     * @Time: 2023/11/20 15:23
     */
    public function addStore()
    {
        $data = request()->only(['user_id', 'name', 'cover', 'banner', 'mobile', 'license', 'lng', 'lat', 'address', 'info', 'tag', 'intro', 'type_id', 'trade_week', 'start_time', 'end_time', 'contact_type', 'qywx_kid']);

        $store = StoreApply::getInfo([['user_id', '=', $data['user_id']], ['status', 'in', [0, 1, 2, 3]]]);

        if (!empty($store)) {

            return $this->error('此用户已申请店铺，不可添加');
        }

        $data['banner'] = !empty($data['banner']) ? implode(',', $data['banner']) : '';
        $data['tag'] = !empty($data['tag']) ? implode(',', $data['tag']) : '';
        $data['trade_week'] = !empty($data['trade_week']) ? implode(',', $data['trade_week']) : '';


        $arr = [
            'uniacid' => $this->_uniacid,
            'is_admin' => 1,
            'status' => 2,
            'check_time' => time(),
            'create_time' => time(),
            'update_time' => time()
        ];

        $data = array_merge($data, $arr);

        $store = StoreApply::getInfo([['user_id', '=', $data['user_id']], ['status', '=', 4]]);

        if (empty($store)) {

            $res = StoreApply::insert($data);
        } else {

            $data['status'] = 2;
            $res = StoreApply::update($data, ['id' => $store['id']]);
        }


        return $this->success($res);
    }

    /**
     * @Desc: 门店分类无分页
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @Auther: shurong
     * @Time: 2023/11/20 15:30
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

        $data = StoreType::getListNoPage($where);

        return $this->success($data);
    }

    /**
     * @Desc: 置顶
     * @return mixed
     * @Auther: shurong
     * @Time: 2023/11/20 16:21
     */
    public function storeTop()
    {
        $id = request()->param('id', '');

        $status = request()->param('status', '1');

        $where = [
            'uniacid' => $this->_uniacid
        ];
        if ($status == 1) {

            $top = StoreApply::where($where)->max('top');

            $res = StoreApply::update(['top' => $top + 1, 'is_top' => 1], ['id' => $id]);
        } else {

            $res = StoreApply::update(['top' => 0, 'is_top' => 0], ['id' => $id]);
        }

        return $this->success($res);
    }

    /**
     * @Desc: 门店用户列表
     * @return mixed
     * @Auther: shurong
     * @Time: 2023/11/20 17:27
     */
    public function storeUserList()
    {

        $input = $this->_param;

        $dis[] = ['uniacid', '=', $this->_uniacid];

        $dis[] = ['status', 'in', [0, 1, 2, 3]];

        $user_id = StoreApply::where($dis)->column('user_id');

        $where1 = [];

        if (!empty($input['nickName'])) {

            $where1[] = ['nickName', 'like', '%' . $input['nickName'] . '%'];

            $where1[] = ['phone', 'like', '%' . $input['nickName'] . '%'];
        }

        $user_model = new User();

        $where[] = ['uniacid', '=', $this->_uniacid];

        $where[] = ['id', 'not in', $user_id];

        $list = $user_model->dataList($where, $input['limit'], $where1);

        return $this->success($list);

    }

    /**
     * @Desc: 编辑
     * @return mixed
     * @Auther: shurong
     * @Time: 2023/11/24 14:39
     */
    public function editStore()
    {
        $data = request()->only(['id', 'user_id', 'name', 'cover', 'banner', 'mobile', 'license', 'lng', 'lat', 'address', 'info', 'tag', 'intro', 'type_id', 'trade_week', 'start_time', 'end_time', 'store_balance', 'share_balance', 'contact_type', 'qywx_kid']);

        if (isset($data['banner'])) {

            $data['banner'] = !empty($data['banner']) ? implode(',', $data['banner']) : '';
        }

        if (isset($data['tag'])) {

            $data['tag'] = !empty($data['tag']) ? implode(',', $data['tag']) : '';
        }
        if (isset($data['trade_week'])) {

            $data['trade_week'] = !empty($data['trade_week']) ? implode(',', $data['trade_week']) : '';
        }

        $res = StoreApply::update($data, ['id' => $data['id']]);

        return $this->success($res);
    }

    /**
     * @Desc: 批量修改门店比例
     * @return mixed
     * @Auther: shurong
     * @Time: 2024/3/29 11:13
     */
    public function editStoreBalance()
    {
        $data = request()->only(['ids', 'store_balance']);

        $ids = $data['ids'];

        unset($data['ids']);

        $res = StoreApply::whereIn('id', $ids)->update($data);

        return $this->success($res);
    }

}