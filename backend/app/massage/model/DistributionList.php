<?php

namespace app\massage\model;

use app\BaseModel;
use app\massage\model\User;
use think\facade\Db;

class DistributionList extends BaseModel
{


    protected $name = 'massage_distribution_list';


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
     * @author chenniang
     * @DataTime: 2020-09-29 11:04
     * @功能说明:添加
     */
    public function dataAdd($data)
    {

        $data['create_time'] = time();

        $res = $this->insert($data);

        return $res;

    }


    /**
     * @author chenniang
     * @DataTime: 2020-09-29 11:05
     * @功能说明:编辑
     */
    public function dataUpdate($dis, $data)
    {

        $res = $this->where($dis)->update($data);

        return $res;

    }


    /**
     * @author chenniang
     * @DataTime: 2020-09-29 11:06
     * @功能说明:列表
     */
    public function dataList($dis, $page)
    {

        $data = $this->where($dis)->order('id desc')->paginate($page)->toArray();

        return $data;

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
     * @param $user_id
     * @param int $type
     * @功能说明:团队人数
     * @author chenniang
     * @DataTime: 2022-07-28 17:58
     */
    public function teamCount($user_id, $type = 1)
    {

        $user_model = new User();

        $dis[] = ['is_fx', '=', 1];

        if ($type == 1) {

            $dis[] = ['pid', '=', $user_id];

        } else {

            $top_id = $user_model->where(['pid' => $user_id, 'is_fx' => 1])->column('id');

            $dis[] = ['pid', 'in', $top_id];

        }

        $data = $user_model->where($dis)->count();

        return $data;

    }

    /**
     * @author chenniang
     * @DataTime: 2021-12-30 11:26
     * @功能说明:后台列表
     */
    public function adminDataList($dis, $page = 10, $where = [])
    {

        $data = $this->alias('a')
            ->join('massage_service_user_list b', 'a.user_id = b.id', 'left')
            ->where($dis)
            ->where(function ($query) use ($where) {
                $query->whereOr($where);
            })
            ->field('a.*,b.nickName,b.avatarUrl')
            ->group('a.id')
            ->order('a.id desc')
            ->paginate($page)
            ->toArray();

        return $data;

    }


    /**
     * @param $dis
     * @param int $page
     * @功能说明:用户收益列表
     * @author chenniang
     * @DataTime: 2022-07-29 14:50
     */
    public function userProfitList($dis, $page = 10, $where = [])
    {

        $user_model = new User();

        $data = $user_model->alias('a')
            ->join('massage_distribution_list b', 'a.id = b.user_id', 'left')
            ->where($dis)
            ->where(function ($query) use ($where) {
                $query->whereOr($where);
            })
            ->field('b.*,a.nickName,a.avatarUrl,a.fx_cash')
            ->group('a.id')
            ->order('a.id desc')
            ->paginate($page)
            ->toArray();

        return $data;
    }


    /**
     * @param $dis
     * @param int $page
     * @功能说明:用户收益列表
     * @author chenniang
     * @DataTime: 2022-07-29 14:50
     */
    public function userProfitSelect($dis, $where = [])
    {

        $user_model = new User();

        $data = $user_model->alias('a')
            ->join('massage_distribution_list b', 'a.id = b.user_id', 'left')
            ->where($dis)
            ->where(function ($query) use ($where) {
                $query->whereOr($where);
            })
            ->field('b.*,a.nickName,a.avatarUrl,a.fx_cash')
            ->group('a.id')
            ->order('a.id desc')
            ->select()
            ->toArray();

        return $data;
    }


    /**
     * @param $dis
     * @param $where
     * @param int $page
     * @功能说明:
     * @author chenniang
     * @DataTime: 2023-03-23 10:28
     */
    public function userDataList($dis, $where, $page = 10)
    {

        $user_model = new User();

        $data = $user_model
            ->alias('a')
            ->join('massage_distribution_list b', 'a.id = b.user_id', 'left')
            ->where($dis)
            ->where(function ($query) use ($where) {
                $query->whereOr($where);
            })
            ->field('b.*, ifnull(b.sh_time,0) as sh_time,a.nickName,a.avatarUrl,a.new_cash,a.cash as new_cash,a.id')
            ->group('a.id')
            ->order('sh_time desc,a.id desc')
            ->paginate($page)
            ->toArray();

        if (!empty($data['data'])) {

            $coach_model = new Coach();

            foreach ($data['data'] as &$v) {
                //邀请向导数量
                $v['coach_count'] = $coach_model->where(['status' => 2, 'partner_id' => $v['id'], 'agent_type' => 2])->count();
                //邀请用户数量
                $v['user_count'] = $user_model->where(['pid' => $v['id']])->count();
                //订单量
                $v['order_count'] = $this->partnerOrderCount($v['id']);
            }

        }

        return $data;
    }


    /**
     * @param $partner_id
     * @功能说明:合伙人相关订单数量
     * @author chenniang
     * @DataTime: 2023-03-23 11:53
     */
    public function partnerOrderCount($partner_id)
    {

        $commis_model = new Commission();

        $count = $commis_model->where(['top_id' => $partner_id, 'status' => 2])->where('type', 'in', [1, 9])->group('order_id')->count();

        return $count;
    }


    /**
     * @param $partner_id
     * @功能说明:合伙人相关订单数量
     * @author chenniang
     * @DataTime: 2023-03-23 11:53
     */
    public function partnerOrderPrice($partner_id)
    {

        $commis_model = new Commission();

        $order_model = new Order();

        $order_id = $commis_model->where(['top_id' => $partner_id, 'status' => 2])->where('type', 'in', [1, 9])->column('order_id');

        $count = $order_model->where('id', 'in', $order_id)->sum('true_service_price');

        return $count;
    }


}