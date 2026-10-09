<?php

namespace app\massage\model;

use app\BaseModel;
use think\facade\Db;

class ConfigSetting extends BaseModel
{
    //定义表名
    protected $name = 'massage_config_setting';


    /**
     * @param $value
     * @param $data
     * @功能说明:
     * @author chenniang
     * @DataTime: 2023-02-23 14:14
     */
    public function getValueAttr($value, $data)
    {
        if (isset($data) && $data['field_type'] == 1) {
            $value = (float) $value;
        }
        return $value;
    }


    /**
     * @author chenniang
     * @DataTime: 2020-09-29 11:06
     * @功能说明:列表
     */
    public function dataList($dis, $page)
    {
        return $this->where($dis)->order('status desc,id desc')->paginate($page)->toArray();
    }


    /**
     * @author chenniang
     * @DataTime: 2023-02-22 16:59
     * @功能说明:初始化配置记录
     */
    public function initData($uniacid)
    {
        $dataPath = APP_PATH.'massage/info/ConfigSetting.php';
        $data = include $dataPath;
        $key = 'config_setting_init_data';
        incCache($key, 1, $uniacid, 99);
        $value = getCache($key, $uniacid);
        if ($value == 1) {
            //一次查询取回已存在的配置项，替代逐条 find()（原 N+1 写法在 99 秒
            //防重入计数器过期后的首个请求要跑全量循环，实测耗时 2 秒+）
            $keys = array_column($data, 'key');
            $exists = $this->where([['uniacid', '=', $uniacid], ['key', 'in', $keys]])->column('key');
            $insert = [];
            foreach ($data as $v) {
                if (!in_array($v['key'], $exists)) {
                    $insert[] = [
                        'uniacid'       => $uniacid,
                        'key'           => $v['key'],
                        'text'          => $v['text'],
                        'value'         => $v['default_value'],
                        'default_value' => $v['default_value'],
                        'field_type'    => $v['field_type'],
                    ];
                }
            }
            if (!empty($insert)) {
                Db::name('massage_config_setting')->insertAll($insert);
            }
        }
        decCache($key, 1, $uniacid);
        return true;
    }


    /**
     * @author chenniang
     * @DataTime: 2020-09-29 11:43
     * @功能说明:
     */
    public function dataInfo($uniacid, $key_arr = [])
    {
        if (empty($uniacid)) {
            return false;
        }
        $this->initData($uniacid);
        $dis[] = ['uniacid', '=', $uniacid];
        if (!empty($key_arr)) {
            $dis[] = ['key', 'in', $key_arr];
        }
        $data = $this->where($dis)->select()->toArray();
        $arr = [];
        foreach ($data as $v) {
            $arr[$v['key']] = $v['value'];
        }
        return $arr;
    }


    /**
     * @param $arr
     * @功能说明:
     * @author chenniang
     * @DataTime: 2023-02-23 13:50
     */
    public function dataUpdate($arr, $uniacid)
    {
        if (!empty($arr)) {
            foreach ($arr as $k => $value) {
                $dis = [
                    'uniacid' => $uniacid,
                    'key' => $k
                ];
                $this->where($dis)->update(['value' => $value]);
            }
        }
        return true;
    }


}