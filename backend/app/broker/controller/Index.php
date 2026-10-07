<?php
/**
 * Created by PhpStorm
 * User: shurong
 * Date: 2023/12/6
 * Time: 18:37
 * docs:
 */

namespace app\broker\controller;

use app\ApiRest;
use app\broker\model\Broker;
use think\App;

class Index extends ApiRest
{
    public function __construct(App $app)
    {
        parent::__construct($app);
    }

    /**
     * @Desc:申请经纪人
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @Auther: shurong
     * @Time: 2023/12/6 18:40
     */
    public function applyBroker()
    {
        $data = request()->only(['user_id', 'name', 'mobile', 'text']);

        $data['user_id'] = $this->getUserId();

        $where = [
            ['uniacid', '=', $this->_uniacid],
            ['status', '>', -1],
            ['user_id', '=', $data['user_id']]
        ];

        $info = Broker::getFirst($where);

        if (!empty($info) && in_array($info['status'], [1, 2, 3])) {

            $this->errorMsg('您已申请，不可重复申请');
        }

        $insert = [
            'uniacid' => $this->_uniacid,
            'user_id' => $data['user_id'],
            'name' => $data['name'],
            'mobile' => $data['mobile'],
            'text' => $data['text'],
            'status' => 1
        ];

        if (!empty($info) && $info['status'] == 4) {

            $res = Broker::update($insert, ['id' => $info['id']]);
        } else {

            $res = Broker::add($insert);
        }

        return $this->success($res);
    }

    /**
     * @Desc: 信息回显
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @Auther: shurong
     * @Time: 2023/12/12 18:55
     */
    public function info()
    {

        $info = Broker::getFirst([['user_id', '=', $this->getUserId()], ['status', '>', -1]]);

        return $this->success($info);
    }
}