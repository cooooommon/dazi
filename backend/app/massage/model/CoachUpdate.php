<?php
namespace app\massage\model;

use app\BaseModel;
use think\facade\Db;

class CoachUpdate extends BaseModel
{
    //定义表名
    protected $name = 'massage_service_coach_update';







    /**
     * @author chenniang
     * @DataTime: 2021-03-15 14:37
     * @功能说明:后台列表
     */
    public function adminDataList($dis,$mapor,$page=10){

        $data = $this->alias('a')
                ->join('shequshop_school_user_list b','a.user_id = b.id')
                ->where($dis)
                ->where(function ($query) use ($mapor){
                    $query->whereOr($mapor);
                })
                ->field('a.*,b.nickName,b.avatarUrl')
                ->group('a.id')
                ->order('a.id desc')
                ->paginate($page)
                ->toArray();

        return $data;

    }



    /**
     * @author chenniang
     * @DataTime: 2020-09-29 11:04
     * @功能说明:添加
     */
    public function dataAdd($data){

        $data['create_time'] = time();

        if(isset($data['service'])){

            $service = $data['service'];

            unset($data['service']);
        }

        $res = $this->insert($data);

        $id = $this->getLastInsID();

        if(!empty($service)){

            $this->updateSome($data['coach_id'],$data['uniacid'],$service);
        }

        return $res;

    }



    /**
     * @param $id
     * @param $uniacid
     * @param $spe
     * @param $update int 0是修改临时表 1是覆盖关联技能表
     * @功能说明:
     * @author chenniang
     * @DataTime: 2021-03-23 13:35
     */
    public function updateSome($id,$uniacid,$service,$update=0){

        $service = !isset($service[0]['ser_id']) ? Service::whereIn('id', $service)->field('id as ser_id,price')->select()->toArray() : $service;

        if ($update == 0) {
            $s_model = new ServiceCoachBak();

            $s_model->where(['coach_id' => $id])->delete();

            $now_data = ServiceCoach::where('coach_id', $id)->field('ser_id,price,balance')->select()->toArray();

            if (!empty($service)) {

                foreach ($service as $value) {

                    foreach ($now_data as $item) {
                        if ($value['ser_id'] == $item['ser_id']) {
                            $value['price'] = $item['price'];
                            $value['balance'] = $item['balance'];
                        }
                    }

                    $insert['ser_id'] = $value['ser_id'] ?? $value;
                    $insert['uniacid'] = $uniacid;
                    $insert['coach_id'] = $id;
                    $insert['price'] = $value['price'] ?? 0;
                    $insert['balance'] = $value['balance'] ?? 0;
                    $s_model->dataAdd($insert);
                }
            }
        } else {
            $s_model = new ServiceCoach();
            $s_model->where(['coach_id' => $id])->delete();
            foreach ($service as $datum) {
                $arr['ser_id'] = $datum['ser_id'];
                $arr['uniacid'] = $uniacid;
                $arr['coach_id'] = $id;
                $arr['price'] = $datum['price'];
                $arr['balance'] = $datum['balance'] ?? 0;
                $s_model->dataAdd($arr);
            }
        }
        return true;
    }

    /**
     * @author chenniang
     * @DataTime: 2020-09-29 11:05
     * @功能说明:编辑
     */
    public function dataUpdate($dis,$data){

        $res = $this->where($dis)->update($data);

        return $res;

    }


    /**
     * @author chenniang
     * @DataTime: 2020-09-29 11:06
     * @功能说明:列表
     */
    public function dataList($dis,$page=10,$mapor){

        $data = $this->where($dis)->where(function ($query) use ($mapor){
            $query->whereOr($mapor);
        })->order('distance asc,id desc')->paginate($page)->toArray();

        return $data;

    }




    /**
     * @author chenniang
     * @DataTime: 2020-09-29 11:43
     * @功能说明:
     */
    public function dataInfo($dis,$file='*'){

        $data = $this->where($dis)->field($file)->find();

        return !empty($data)?$data->toArray():[];

    }


    /**
     * @author chenniang
     * @DataTime: 2023-04-13 17:45
     * @功能说明:覆盖服务
     */
    public function updateService($coach_id,$uniacid){

        $serv_model = new ServiceCoachBak;

        $data = $serv_model->where(['coach_id' => $coach_id])->field('uniacid,ser_id,coach_id,price,balance')->select()->toArray();
        $this->updateSome($coach_id,$uniacid,$data,1);
        return true;

    }








}