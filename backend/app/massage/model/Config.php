<?php

namespace app\massage\model;

use AlibabaCloud\Client\AlibabaCloud;
use app\BaseModel;
use Exception;
use think\facade\Db;

class Config extends BaseModel
{
    //定义表名
    protected $name = 'shequshop_school_config';


    /**
     * @author chenniang
     * @DataTime: 2020-09-29 11:04
     * @功能说明:添加
     */
    public function dataAdd($data)
    {
        return $this->insert($data);
    }


    /**
     * @author chenniang
     * @DataTime: 2020-09-29 11:05
     * @功能说明:编辑
     */
    public function dataUpdate($dis, $data)
    {
        return $this->where($dis)->update($data);
    }


    /**
     * @author chenniang
     * @DataTime: 2020-09-29 11:06
     * @功能说明:列表
     */
    public function dataList($dis, $page)
    {
        return $this->where($dis)->order('id desc')->paginate($page)->toArray();
    }


    /**
     * @author chenniang
     * @DataTime: 2020-09-29 11:43
     * @功能说明:
     */
    public function dataInfo($dis)
    {
        if (empty($dis['uniacid'])) {
            $dis['uniacid'] = 666;
        }
        $data = $this->where($dis)->find();
        if (empty($data)) {
            $this->dataAdd($dis);
            $data = $this->where($dis)->find();
        }
        return !empty($data) ? $data->toArray() : [];

    }
}