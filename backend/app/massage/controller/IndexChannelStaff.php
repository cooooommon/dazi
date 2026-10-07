<?php

namespace app\massage\controller;

use app\ApiRest;
use app\massage\model\ChannelStaff;

/**
 * Created by PhpStorm
 * User: shurong
 * Date: 2023/10/30
 * Time: 13:37
 * docs:
 */
class IndexChannelStaff extends ApiRest
{
    protected $staff;

    public function __construct(\think\App $app)
    {
        parent::__construct($app);

        $this->staff = ChannelStaff::getFirst(['status' => 1, 'user_id' => $this->getUserId()]);

        if (empty($this->staff)) {

            $this->errorMsg('你还不是员工');
        }
    }

    /**
     * @Desc: 信息
     * @return mixed
     * @Auther: shurong
     * @Time: 2023/10/30 13:42
     */
    public function index()
    {
        $data = ChannelStaff::staffInfo(['a.status' => 1, 'a.user_id' => $this->getUserId()]);

        return $this->success($data);
    }

    /**
     * @Desc: 我的渠道流水
     * @return mixed
     * @Auther: shurong
     * @Time: 2023/10/30 14:34
     */
    public function commList()
    {
        $input = $this->_param;

        $where = [
            ['a.pay_type', '>', 1],
            ['a.uniacid', '=', $this->_uniacid],
            ['a.channel_staff_id', '=', $this->staff['id']]
        ];

        if (!empty($input['start_time'])) {

            $where[] = ['a.create_time', '>', $input['start_time']];
        }

        if (!empty($input['end_time'])) {

            $where[] = ['a.create_time', '<', $input['end_time']];
        }

        $data = ChannelStaff::staffComList($where, $input['limit'] ?? 10);

        return $this->success($data);
    }

}