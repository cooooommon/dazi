<?php

namespace app\massage\model;

use app\BaseModel;

class UserLabelData extends BaseModel
{
    //定义表名
    protected $name = 'massage_service_user_label_data';

    /**
     * @author chenniang
     * @DataTime: 2020-09-29 11:04
     * @功能说明:添加
     */
    public function dataAdd($data)
    {
        $data['create_time'] = time();
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
        $data = $this->where($dis)->find();
        return !empty($data) ? $data->toArray() : [];
    }


    /**
     * @author chenniang
     * @DataTime: 2022-10-24 15:16
     * @功能说明:获取用户标签
     */
    public function getUserLabel($user_id)
    {
        //先取出该用户每个标签下最新一条记录的 id
        //原写法依赖 GROUP BY 在组内"任选一行"：MySQL 5.6 不报错，但选到的是任意行
        //（实测为组内最旧那行），order('a.id desc') 只对结果排序、影响不了组内选行，
        //因此改为显式取 MAX(id)，结果确定且不依赖 ONLY_FULL_GROUP_BY
        $max_rows = $this->where(['user_id' => $user_id, 'status' => 1])
            ->field('label_id, MAX(id) AS max_id')
            ->group('label_id')
            ->select()
            ->toArray();

        $max_ids = array_column($max_rows, 'max_id');

        if (empty($max_ids)) {

            return [];
        }

        $dis = [
            'a.user_id' => $user_id,
            'b.status' => 1,
            'a.status' => 1
        ];
        return $this->alias('a')
            ->join('massage_service_user_label_list b', 'a.label_id = b.id')
            ->where($dis)
            ->whereIn('a.id', $max_ids)
            ->field('a.*,b.title')
            ->order('a.id desc')
            ->select()
            ->toArray();
    }
}