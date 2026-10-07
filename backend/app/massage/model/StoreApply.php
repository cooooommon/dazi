<?php


namespace app\massage\model;


use app\BaseModel;
use app\store\model\StorePackage;
use think\facade\Db;

class StoreApply extends BaseModel
{
    protected $name = 'massage_store_apply';

    /**
     * 电话号码加密
     * @param $value
     * @param $data
     * @return string|string[]
     */
    public function getMobileAttr($value, $data)
    {

        if (!empty($value) && isset($data['uniacid'])) {

            if (numberEncryption($data['uniacid']) == 1) {

                return substr_replace($value, "****", 2, 4);
            }

        }

        return $value;

    }

    /**
     * 添加
     * @param $data
     * @return int|string
     */
    public static function add($data)
    {
        $data['create_time'] = $data['update_time'] = time();
        return self::insert($data);
    }

    public static function getInfo($where)
    {
        return self::where($where)->find();
    }

    /**
     * 列表
     * @param $where
     * @param $limit
     * @return array
     * @throws \think\db\exception\DbException
     */
    public static function getList($where, $limit = 10)
    {
        return self::where($where)
            ->field(['id', 'name', 'uniacid', 'cover', 'banner', 'mobile', 'license', 'lng', 'lat', 'address', 'info', 'tag', 'status', 'intro', 'status', 'is_update', 'create_time', 'check_time', 'check_msg', 'is_top', 'trade_week', 'start_time', 'end_time', 'store_balance', 'share_balance', 'type_id'])
            ->order('is_top desc,top desc,create_time desc')
            ->paginate($limit)
            ->each(function ($item) {
                $item['create_time'] = date('Y-m-d H:i:s', $item['create_time']);
                $item['check_time'] = date('Y-m-d H:i:s', $item['check_time']);
                $item['package_num'] = StorePackage::where([['store_id', '=', $item['id']], ['status', '>', -1]])->count();
                $item['type_name'] = StoreType::where('id', 'in', explode(',', $item['type_id']))->column('name');
            })
            ->toArray();
    }

    /**
     * 获取数量
     * @param $where
     * @return int
     * @throws \think\db\exception\DbException
     */
    public static function getCount($where)
    {
        return self::where($where)->count();
    }

    /**
     * 审核
     * @param $data
     * @return StoreApply
     */
    public static function check($data)
    {
        $data['check_time'] = time();
        return self::where('id', $data['id'])->update($data);
    }

    /**
     * 门店列表
     * @param $where
     * @param $alh
     * @param int $limit
     * @return array
     * @throws \think\db\exception\DbException
     */
    public static function getIndexList($where, $alh, $limit = 10)
    {
        return self::where($where)
            ->field(['id', 'name', 'cover', 'banner', 'mobile', 'license', 'lng', 'lat', 'address', 'info', 'tag', 'status', 'intro', 'status', 'is_update', 'create_time', 'check_time', 'check_msg', 'type_id', 'is_top', $alh])
            ->order('is_top desc,top desc,distance asc')
            ->paginate($limit)
            ->each(function ($item) {
                $item['create_time'] = date('Y-m-d H:i:s', $item['create_time']);
                $item['check_time'] = date('Y-m-d H:i:s', $item['check_time']);
                $item['type_name'] = StoreType::where('id', 'in', explode(',', $item['type_id']))->column('name');
            })
            ->toArray();
    }

    /**
     * @Desc: 删除分类  则清空门店分类
     * @param $type_id
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @Auther: shurong
     * @Time: 2023/11/29 11:53
     */
    public static function cancel($type_id)
    {
        $where[] = ['', 'exp', Db::raw("find_in_set({$type_id},type_id)")];

        $data = self::where($where)->select()->toArray();
        if (!empty($data)) {
            foreach ($data as $datum) {
                $arr = explode(',', $datum['type_id']);

                foreach ($arr as $key => $item) {
                    if ($item == $type_id) {
                        unset($arr[$key]);
                    }
                }
                self::where('id', $datum['id'])->update(['type_id' => implode(',', $arr)]);
            }
        }
    }

    public static function getListNoPage($where)
    {
        return self::where($where)->order('top desc,id desc')->select()->toArray();
    }
}