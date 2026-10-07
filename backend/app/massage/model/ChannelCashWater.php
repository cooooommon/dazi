<?php
/**
 * Created by PhpStorm
 * User: shurong
 * Date: 2023/11/1
 * Time: 11:17
 * docs:
 */

namespace app\massage\model;

use app\BaseModel;

class ChannelCashWater extends BaseModel
{
    protected $name = 'massage_channel_cash_water';

    /**
     * @Desc: 增加记录
     * @param $uniacid
     * @param $channel_id
     * @param $before
     * @param $after
     * @return int|string
     * @Auther: shurong
     * @Time: 2023/11/1 11:21
     */
    public static function record($uniacid, $channel_id, $before, $after)
    {
        $change = $after - $before;
        $insert = [
            'uniacid' => $uniacid,
            'channel_id' => $channel_id,
            'before' => $before,
            'change' => $change,
            'after' => $after,
            'create_time' => time()
        ];
        return self::insert($insert);
    }

    /**
     * @Desc: 手动余额变动记录
     * @param $where
     * @param $limit
     * @return array
     * @throws \think\db\exception\DbException
     * @Auther: shurong
     * @Time: 2023/11/1 11:28
     */
    public static function getCashList($where, $limit)
    {
        return self::where($where)
            ->field('id,before,change,after,create_time')
            ->order('create_time desc')
            ->paginate($limit)
            ->toArray();
    }
}