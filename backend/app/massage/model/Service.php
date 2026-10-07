<?php
namespace app\massage\model;

use app\BaseModel;
use app\member\model\MemberConfig;
use think\facade\Db;

class Service extends BaseModel
{
    //定义表名
    protected $name = 'massage_service_service_list';


    protected $append = [

        'create_time_text',

      //  'coach'

    ];


//    /**
//     * @author chenniang
//     * @DataTime: 2021-07-06 23:52
//     * @功能说明:绑定的向导
//     */
//    public function getCoachAttr($value,$data){
//
//        if(!empty($data['id'])){
//
//            $coach_s_model = new ServiceCoach();
//
//            $coach_model = new Coach();
//
//            $list  = $coach_s_model->where(['ser_id'=>$data['id']])->column('coach_id');
//
//            $coach = $coach_model->where('id','in',$list)->where(['status'=>2])->field('id,coach_name,work_img')->select()->toArray();
//
//            return $coach;
//        }
//
//
//    }




    /**
     * @param $value
     * @param $data
     * @功能说明:
     * @author chenniang
     * @DataTime: 2021-03-23 11:12
     */
    public function getImgsAttr($value,$data){

        if(!empty($value)){

            return explode(',',$value);

        }

    }


    /**
     * @author chenniang
     * @DataTime: 2021-03-23 11:12
     * @功能说明:
     */
    public function getCreateTimeTextAttr($value,$data){

        if(!empty($data['create_time'])){

            return date('Y-m-d H:i:s',$data['create_time']);
        }

    }








    /**
     * @author chenniang
     * @DataTime: 2020-09-29 11:04
     * @功能说明:添加
     */
    public function dataAdd($data){

        $data['create_time'] = time();

        if(isset($data['sale'])){

            $data['total_sale'] = $data['sale'];
        }

        if(isset($data['coach'])){

            $coach = $data['coach'];

            unset($data['coach']);
        }

        $data['imgs'] = !empty($data['imgs'])?implode(',',$data['imgs']):'';

        $res = $this->insert($data);

        $id  = $this->getLastInsID();

        if(!empty($coach)){

            $this->updateSome($id,$data['uniacid'],$coach);
        }

        return $id;

    }


    /**
     * @author chenniang
     * @DataTime: 2020-09-29 11:05
     * @功能说明:编辑
     */
    public function dataUpdate($dis,$data){

        if(isset($data['coach'])){

            $coach = $data['coach'];

            unset($data['coach']);
        }

        if(isset($data['imgs'])){

            $data['imgs'] = !empty($data['imgs'])?implode(',',$data['imgs']):'';

        }

        if(isset($data['sale'])&&isset($data['true_sale'])){

            $data['total_sale'] = $data['sale']+$data['true_sale'];
        }

        $res = $this->where($dis)->update($data);

        if(isset($coach)){

            $this->updateSome($dis['id'],$data['uniacid'],$coach);
        }


        return $res;

    }


    /**
     * @param $id
     * @param $uniacid
     * @param $spe
     * @功能说明:
     * @author chenniang
     * @DataTime: 2021-03-23 13:35
     */
    public function updateSome($id,$uniacid,$coach){

        $s_model = new ServiceCoach();

        $s_model->where(['ser_id'=>$id])->delete();

        if(!empty($coach)){

            foreach ($coach as $value){

                $insert['coach_id'] = $value['coach_id'];

                $insert['uniacid']  = $uniacid;

                $insert['ser_id']   = $id;

                $insert['price']   = $value['price'];

                $insert['balance'] = $value['balance']??0;

                $s_model->dataAdd($insert);

            }

        }

        return true;

    }




    /**
     * @author chenniang
     * @DataTime: 2020-09-29 11:06
     * @功能说明:列表
     */
    public function dataList($dis,$page){

        $data = $this->where($dis)->order('top desc,id desc')->paginate($page)->each(function ($item){
            $item['price'] = round($item['price'],2);
        })->toArray();

        return $data;

    }


    /**
     * @author chenniang
     * @DataTime: 2020-09-29 11:06
     * @功能说明:列表
     */
    public function indexDataList($dis,$page,$sort,$where){

        $data = $this->where($dis)->order("$sort,id desc")->paginate($page)->toArray();
        if ($data['data']) {
            $ids = array_column($data['data'], 'id');
            $arr = Db::name('massage_service_service_coach')
                ->alias('a')
                ->leftJoin('massage_service_coach_list b','a.coach_id=b.id')
                ->field('TRIM(TRAILING "." FROM TRIM(TRAILING "0" FROM min(price))) as price,ser_id')
                ->whereIn('ser_id', $ids)
                ->where($where)
                ->group('ser_id')
                ->select();
            foreach ($data['data'] as &$datum) {
                $datum['price'] = 0;
                foreach ($arr as $item) {
                    if ($datum['id'] == $item['ser_id']) {
                        $datum['price'] = $item['price'];
                    }
                }
            }
        }
        return $data;

    }





    /**
     * @author chenniang
     * @DataTime: 2020-09-29 11:43
     * @功能说明:
     */
    public function dataInfo($dis){

        $data = $this->where($dis)->find();

        if (!empty($data)) {

            $data->toArray();
            $data['price'] = round($data['price'], 2);
            $data['coach'] = $this->getServiceCoach($data['id'], $data['type'], $data['admin_id']);

            return $data;
        } else {

            return [];
        }

    }


    /**
     * @author chenniang
     * @DataTime: 2020-09-29 11:43
     * @功能说明:
     */
    public function serviceInfo($dis){

        $data = $this->where($dis)->find();

        return !empty($data)?$data->toArray():[];


    }

    /**
     * @author chenniang
     * @DataTime: 2021-07-07 10:21
     * @功能说明:服务向导列表
     */
       public function serviceCoachList($dis){

        $data = $this->alias('a')
            ->join('massage_service_service_coach b','a.id = b.ser_id','left')
            ->where($dis)
            ->field(['a.id,a.title,a.cover,a.init_price,b.price,a.is_add,a.type,a.time_long,a.total_sale,a.sub_title,a.admin_id,a.price as service_price,b.id as data_id,b.balance'])
            ->group('a.id')
            ->order('a.top desc,a.id desc')
            ->select()
            ->toArray();

        return $data;

    }

    public function serviceBakCoachList($dis){

        $data = $this->alias('a')
            ->join('massage_service_service_coach_bak b','a.id = b.ser_id','left')
            ->where($dis)
            ->field(['a.id,a.title,a.cover,a.init_price,a.is_add,a.type,a.time_long,a.total_sale,a.sub_title,a.admin_id'])
            ->group('a.id')
            ->order('a.top desc,a.id desc')
            ->select()
            ->toArray();

        return $data;

    }


    /**
     * 升级项目
     * @param $dis
     * @param $goods_price
     * @return mixed
     */
    public function serviceCoachListV2($dis,$goods_price)
    {
        $data = $this->alias('a')
            ->join('massage_service_service_coach b','a.id = b.ser_id','left')
            ->where($dis)
            ->field(['a.id,a.title,a.cover,a.init_price,if(b.price>0,b.price,a.price) as price,a.is_add,a.type,a.time_long,a.total_sale,a.sub_title,a.admin_id'])
            ->having("price>$goods_price")
            ->group('a.id')
            ->order('a.top desc,a.id desc')
            ->select()
            ->toArray();

        return $data;
    }

    /**
     * 向导服务列表带分页、、
     * @param $dis
     * @param $limit
     * @return mixed
     */
    public function serviceCoachListPage($dis,$limit){

        $data = $this->alias('a')
            ->join('massage_service_service_coach b','a.id = b.ser_id','left')
            ->where($dis)
            ->field(['a.id,a.title,a.cover,a.init_price,b.price,a.is_add,a.type,a.time_long,a.total_sale,a.sub_title,a.admin_id'])
            ->group('a.id')
            ->order('a.top desc,a.id desc')
            ->paginate($limit)
            ->toArray();

        return $data;

    }



    /**
     * @author chenniang
     * @DataTime: 2022-09-15 16:20
     * @功能说明:获取销量最高的向导
     */
    public function getSaleTopOne($uniacid){

        $key = 'getSaleTopOnezzz';

        $data = getCache($key,$uniacid);

        if(!empty($data)){

            return $data;
        }

        $order_model = new Order();

        $coach_model = new Coach();

        $coach_id = $coach_model->where(['status'=>2,'uniacid'=>$uniacid])->column('id');

        $dis[] = ['pay_type','>',1];

        $dis[] = ['uniacid','=',$uniacid];

        $dis[] = ['coach_id','in',$coach_id];

        $data = $order_model->where($dis)->field('SUM(true_service_price) as all_price,coach_id')->whereTime('create_time','week')->group('coach_id')->order('all_price desc,id desc')->find();

        if(!empty($data)){

            setCache($key,$data->coach_id,10,$uniacid);

            return $data->coach_id;

        }else{

            return 0;
        }

    }


    /**
     * @author chenniang
     * @DataTime: 2022-09-15 16:20
     * @功能说明:获取最近其他的向导
     */
    public function getSaleTopSeven($uniacid){

        $coach_model = new Coach();

        $dis[] = ['status','=',2];

        $dis[] = ['uniacid','=',$uniacid];

        $data = $coach_model->where($dis)->whereTime('sh_time','-7 days')->column('id');

        return $data;

    }


    /**
     * @author chenniang
     * @DataTime: 2022-09-15 16:20
     * @功能说明:获取销量最高的向导
     */
    public function getSaleTopFive($uniacid,$coach_id){

      //  $coach_model = new Coach();

      //  $coach_id = $coach_model->where(['status'=>2,'uniacid'=>$uniacid])->where('id','<>',$coach_id)->column('id');

        $order_model = new Order();

        $dis[] = ['pay_type','>',1];

        $dis[] = ['uniacid','=',$uniacid];

        $dis[] = ['coach_id','<>',$coach_id];

        $data = $order_model->where($dis)->field('COUNT(id) as counts,coach_id')->whereTime('create_time','week')->group('coach_id')->order('counts desc,id desc')->limit(5)->select()->toArray();

        return array_column($data,'coach_id');

    }

    /**
     * @author chenniang
     * @DataTime: 2021-03-18 10:07
     * @功能说明:增加|减少库存 增加|减少销量
     */
    public function setOrDelStock($goods_id,$num,$type=2){

        if(empty($goods_id)){

            return true;
        }

        $goods_info = $this->dataInfo(['id'=>$goods_id]);
        //退货
        if($type==1){

            $update = [

                'true_sale' => $goods_info['true_sale']-$num,

                'total_sale'=> $goods_info['total_sale']-$num,

                'lock'      => $goods_info['lock']+1,

            ];
            //如果是售后增加退款数量
//            if($refund==1){
//
//                $update['refund_num'] = $goods_info['refund_num']+$num;
//            }
            //减销量 加退款数量
            $res = $this->where(['id'=>$goods_id,'lock'=>$goods_info['lock']])->update($update);

            if($res!=1){

                return ['code'=>500,'msg'=>'提交失败'];
            }

        }else{

            $update = [

                'true_sale' => $goods_info['true_sale']+$num,

                'total_sale'=> $goods_info['total_sale']+$num,

                'lock'      => $goods_info['lock']+1,

            ];
            //增加销量
            $res = $this->where(['id'=>$goods_id,'lock'=>$goods_info['lock']])->update($update);

            if($res!=1){

                return ['code'=>500,'msg'=>'提交失败'];
            }


        }

        return true;
    }


    /**
     * @author chenniang
     * @DataTime: 2021-07-06 23:52
     * @功能说明:绑定的向导
     */
    public function getServiceCoach($ser_id,$type=1,$admin_id=0){

        if(!empty($ser_id)){

            $coach_s_model = new ServiceCoach();

//            $coach_model = new Coach();
//
//            $list  = $coach_s_model->where(['ser_id'=>$ser_id])->column('coach_id');
//            //门店服务直查询 代理商关联的向导
//            if($type==2){
//
//                $store_model = new StoreList();
//
//                $coach_id = $store_model->getAdminStoreCoach($admin_id);
//
//                $list = array_intersect($coach_id,$list);
//            }
//
//            $coach = $coach_model->where('id','in',$list)->where(['status'=>2])->field('id,coach_name,work_img')->select()->toArray();

            $coach = $coach_s_model->alias('a')
                ->where(['a.ser_id' => $ser_id, 'b.status' => 2])
                ->field('b.id,b.coach_name,b.work_img,a.price,a.balance')
                ->join('massage_service_coach_list b', 'a.coach_id=b.id', 'left')
                ->select()
                ->toArray();

            return $coach;
        }

    }


    /**
     * @param $store_id
     * @功能说明:获取门店关联的服务
     * @author chenniang
     * @DataTime: 2023-03-28 22:23
     */
    public function getStoreService($store_id){

        $dis = [

            'a.status' => 1,

            'a.is_add' => 0,

            'a.check_status' => 2,

            'c.status' => 2,

            'c.store_id'=> $store_id
        ];

        $data = $this->alias('a')
                ->join('massage_service_service_coach b','a.id = b.ser_id')
                ->join('massage_service_coach_list c','b.coach_id = c.id')
                ->where($dis)
                ->field('a.id,a.title,a.cover,a.init_price,a.price,a.sub_title,a.time_long,a.total_sale')
                ->group('a.id')
                ->order('a.top desc')
                ->limit(5)
                ->select()
                ->toArray();

        return $data;
    }



    /**
     * @param $store_id
     * @功能说明:获取门店关联的服务列表
     * @author chenniang
     * @DataTime: 2023-03-29 17:46
     */
    public function getStoreServicePage($store_id){

        $dis = [

            'a.status' => 1,

            'c.status' => 2,

            'c.store_id'=> $store_id,

            'a.is_add' => 0,

            'a.check_status' => 2
        ];

        $data = $this->alias('a')
            ->join('massage_service_service_coach b','a.id = b.ser_id')
            ->join('massage_service_coach_list c','b.coach_id = c.id')
            ->where($dis)
            ->field('a.id,a.title,a.cover,a.init_price,a.price,a.sub_title,a.time_long,a.total_sale')
            ->group('a.id')
            ->order('a.top desc')
            ->paginate(10)
            ->toArray();

        return $data;
    }

}