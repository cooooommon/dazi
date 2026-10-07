<?php
namespace app\massage\model;

use app\BaseModel;
use app\broker\info\PermissionBroker;
use app\broker\model\Broker;
use app\broker\model\CommissionShare;
use think\facade\Db;

class CoachChangeLog extends BaseModel
{
    //定义表名
    protected $name = 'massage_order_coach_change_logs';




    /**
     * @author chenniang
     * @DataTime: 2020-09-29 11:04
     * @功能说明:添加
     */
    public function dataAdd($data){

        $data['create_time'] = time();

        $res = $this->insert($data);

        return $res;

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
    public function dataList($dis,$page){

        $data = $this->where($dis)->order('status desc,id desc')->paginate($page)->toArray();

        return $data;

    }


    /**
     * @author chenniang
     * @DataTime: 2020-09-29 11:43
     * @功能说明:
     */
    public function dataInfo($dis){

        $data = $this->where($dis)->find();

        return !empty($data)?$data->toArray():[];

    }


    /**
     * @author chenniang
     * @DataTime: 2023-02-24 11:37
     * @功能说明:更换向导时候计算车费
     */
    public function giveCarPrice($order,$arr = [5,6,7]){
        //当前次是否给了车费
        $give_car = 0;
        //如果向导出发需要给向导车费
        if(in_array($order['pay_type'],$arr)&&$order['true_car_price']>0){

            $coach_model = new Coach();

            $comm_model = new Commission();

            $car_cash = $order['true_car_price'];
            //加车费记录
            $insert = [

                'uniacid' => $order['uniacid'],

                'user_id' => $order['user_id'],

                'top_id'  => $order['coach_id'],

                'order_id'=> $order['id'],

                'order_code' => $order['order_code'],

                'admin_id'=> $order['admin_id'],

                'type'    => 8,

                'cash'    => $car_cash,

                'status'  => 2,

                'cash_status' => 0

            ];
            $comm_model->dataAdd($insert);

            if($order['coach_id']>0){

                $res = $coach_model->where(['id'=>$order['coach_id']])->update(['car_price'=>Db::raw("car_price+$car_cash")]);

                if($res==0){

                    return ['code'=>500,'msg'=>'转派失败，请从试'];
                }
            }else{

                if(!empty($order['admin_id'])){
                    //再把钱给代理商
                    $admin_model = new Admin();

                    $admin = $admin_model->dataInfo(['id'=>$order['admin_id']]);

                    $insert = [

                        'uniacid' => $order['uniacid'],

                        'user_id' => $order['user_id'],

                        'top_id'  => $order['admin_id'],

                        'order_id'=> $order['id'],

                        'order_code' => $order['order_code'],

                        'type'    => 2,

                        'cash'    => $car_cash,

                        'admin_id'=> $order['admin_id'],

                        'balance' => 0,

                        'status'  => 2,

                        'city_type'=> $admin['city_type'],

                        'car_cash' => $car_cash,

                    ];

                    $comm_model->dataAdd($insert);

                    $res = $admin_model->where(['id'=>$order['admin_id'],'cash'=>$admin['cash']])->update(['cash'=>Db::raw("cash+$car_cash")]);

                    if($res==0){

                        return ['code'=>500,'msg'=>'转派失败，请从试'];
                    }
                }

            }

            $give_car = 1;

        }

        return $give_car;
    }


    /**
     * @param $uniacid
     * @功能说明:
     * @author chenniang
     * @DataTime: 2023-03-08 11:36
     */
    public function initData($uniacid){

        $this->dataUpdate(['uniacid'=>$uniacid],['is_new'=>0]);

        $data = $this->where(['uniacid'=>$uniacid])->group('order_id')->order('id desc')->select()->toArray();

        if(!empty($data)){

            foreach ($data as $v){

                $find = $this->where(['order_id'=>$v['order_id']])->order('id desc')->find();

                $this->dataUpdate(['id'=>$find->id],['is_new'=>1]);
            }
        }


    }

    /**
     * @param $order
     * @param $coach_id
     * @param $coach_name
     * @功能说明:添加转发记录
     * @author chenniang
     * @DataTime: 2023-02-24 11:42
     */
    public function addLog($order,$coach_id,$coach_name,$init_coach_id,$text,$phone,$have_car_price){

        $dis = [

            'status' => 1,

            'order_id' => $order['id']
        ];

        $this->dataUpdate($dis,['is_new'=>0]);
        //原来的订单没有向导
        if($order['coach_id']==0){

            $old_coach_name = $this->where($dis)->order('id desc')->value('now_coach_name');

            $old_coach_mobile = $this->where($dis)->order('id desc')->value('now_coach_mobile');
        }

        $insert = [

            'uniacid'       => $order['uniacid'],

            'old_coach_id'  => $order['coach_id'],

            'pay_type'      => $order['pay_type'],

            'is_new'        => 1,

            'text'          => $text,

            'now_coach_id'  => $coach_id,

            'old_coach_name'=> !empty($old_coach_name)?$old_coach_name:'',

            'old_coach_mobile'=> !empty($old_coach_mobile)?$old_coach_mobile:'',

            'now_coach_name'=> $coach_name,

            'now_coach_mobile'=> $phone,

            'car_price'     => $order['true_car_price'],

            'order_id'      => $order['id'],

            'start_time'    => $order['start_time'],

            'end_time'      => $order['end_time'],

            'init_coach_id' => $init_coach_id,

            'have_car_price'=> $have_car_price,
        ];
        //添加日志
        $res = $this->dataAdd($insert);

        return $res;

    }


    /**
     * @param $time_long
     * @param $order_id
     * @功能说明:更改加钟订单的时间
     * @author chenniang
     * @DataTime: 2023-02-27 11:54
     */
    public function changeAddOrderTime($order,$time_long,$coach_id){

        $order_model = new Order();

        $add_order = $order_model->where(['add_pid'=>$order['id'],'pay_type'=>1])->select()->toArray();

        if(!empty($add_order)){

            foreach ($add_order as $value){

                $order_model->cancelOrder($value);
            }
        }

        $where[] = ['pay_type','not in',[-1,1,7]];

        $where[] = ['add_pid','=',$order['id']];

        $where[] = ['is_add','=',1];

        $order_list = $order_model->where($where)->select()->toArray();

        if(!empty($order_list)){

            foreach ($order_list as $value){

                $update['coach_id']   = $coach_id;

                $update['start_time'] = $value['start_time'] + $time_long;

                $update['end_time']   = $value['end_time'] + $time_long;

                $update['pay_type']   = 3;

                $update['admin_id']   = 0;

                $update['partner_id'] = 0;

                $update['receiving_time'] = time();

                $order_model->dataUpdate(['id'=>$value['id']],$update);
                //重新结算佣金
                $this->getCashData($value,$coach_id,$order['admin_id']);

            }

        }

        return true;

    }



    /**
     * @author chenniang
     * @DataTime: 2023-02-24 11:50
     * @功能说明:修改订单信息
     */
    public function updateOrderInfo($order,$coach_id,$have_car_price,$start_time,$control_id,$admin_id){

        $car_price = $have_car_price==0?$order['true_car_price']:0;

        $update = [

            'coach_id' => $coach_id,

            'true_car_price' => $car_price,

            'pay_type' => 3,

            'receiving_time' => time(),

            'start_time' => $start_time,

            'admin_id'   => 0,

            'partner_id' => 0,

            'end_time' => $start_time + $order['true_time_long'] * 60,

            //转单之后需要清空之前的流程
            'serout_time' => 0,
            'serout_lng' => '',
            'serout_lat' => '',
            'serout_address' => '',
            'arrive_time' => 0,
            'arrive_img' => '',
            'arr_lng' => '',
            'arr_lat' => '',
            'arr_address' => '',
            'start_service_time' => 0
        ];

        $order_model = new Order();
        //修改订单信息
        $res = $order_model->dataUpdate(['id'=>$order['id']],$update);

        $time_long = $update['start_time'] - $order['start_time'];

        $order['admin_id'] = $admin_id;
        //修改加单的信息
        $this->changeAddOrderTime($order,$time_long,$coach_id);

        $log_model = new OrderLog();

        $log_model->addLog($order['id'],$order['uniacid'],3,$order['pay_type'],1,$control_id);

        return $res;
    }



    /**
     * @param $order
     * @param $coach_id
     * @功能说明:
     * @author chenniang
     * @DataTime: 2023-02-27 14:40
     */
    public function getCashData($order,$coach_id,$admin_id){

        $order_model = new Order();

        $comm_model  = new Commission();
        //清空以前所有的佣金
        $comm_model->where(['order_id'=>$order['id']])->where('status','<>',2)->delete();

        CommissionShare::where('order_id',$order['id'])->delete();

        $coach = Coach::where('id',$coach_id)->find();

        $order['coach_id'] = $coach_id;

        $order['broker_id'] = 0;

        $auth = false;

        if ($coach_id){

            $p = new PermissionBroker($coach['uniacid']);

            $auth = $p->pAuth();
        }

        if (!empty($coach['broker_id']) && $auth) {

            $broker = Broker::where([['id', '=', $coach['broker_id']], ['status', '=', 2]])->find();

            if (!empty($broker)) {

                $order['broker_id'] = $coach['broker_id'];
            }
        }

        $order_model->update(['broker_id' => $order['broker_id']], ['id' => $order['id']]);

        //结算新的佣金信息
        $res = $order_model->getCashData($order,1,$admin_id);

        if(!empty($res['code'])){

            return $res;
        }
        //修改订单信息
        $order_model->dataUpdate(['id'=>$order['id']],$res['order_data']);

        //修改通知
        if(isset($res['order_data']['admin_id'])){

            $this->updateNotice($order['id'],$res['order_data']['admin_id']);

        }
        //将分销记录打开
        $comm_model->dataUpdate(['order_id'=>$order['id'],'status'=>-1],['status'=>1]);

        return $res;

    }


    /**
     * @param $order_id
     * @param $admin_id
     * @功能说明:处理代理商订单通知
     * @author chenniang
     * @DataTime: 2023-03-14 10:24
     */
    public function updateNotice($order_id,$admin_id){

        $notice_model = new NoticeList();

        $refund_model = new RefundOrder();

        $notice_model->dataUpdate(['order_id'=>$order_id,'type'=>1],['admin_id'=>$admin_id]);

        $refund_id = $refund_model->where(['order_id'=>$order_id])->column('id');

        $notice_model->where(['type'=>2])->where('order_id','in',$refund_id)->update(['admin_id'=>$admin_id]);

        return true;
    }


    /**
     * @param $time_long
     * @param $order_id
     * @功能说明:校验加钟订单
     * @author chenniang
     * @DataTime: 2023-02-27 11:13
     */
    public function checkAddOrderTime($order,$start_time,$coach_id){

        $order_model = new Order();

        $where[] = ['pay_type','not in',[-1]];

        $where[] = ['add_pid','=',$order['id']];

        $where[] = ['is_add','=',1];

        $order_list = $order_model->where($where)->field('id,coach_id,start_time,end_time,order_end_time,pay_type,add_pid')->select()->toArray();

        if(!empty($order_list)){

            foreach ($order_list as $value){

                $value['coach_id'] = $coach_id;

                $start_time = $start_time-$order['start_time']+$value['start_time'] ;
                //校验时间
                $check = $order_model->checkTime($value,$start_time,$value['add_pid']);

                if(!empty($check['code'])){

                    return $check;

                }

            }

        }

        return true;

    }

    /**
     * @param $order
     * @功能说明:校验时间
     * @author chenniang
     * @DataTime: 2023-02-28 17:22
     */
    public function checkTime($order,$coach_id){

        $config_model = new Config();

        $order_model  = new Order();

        $config = $config_model->dataInfo(['uniacid' => $order['uniacid']]);

        $start_time = $order['start_time']>time()+$config['time_interval']?$order['start_time']:time()+$config['time_interval'];

        if($coach_id!=0){

            $order['coach_id'] = $coach_id;

            $coach_model = new Coach();

            $coach = $coach_model->dataInfo(['id'=>$coach_id]);
            //获取该向导最近的服务时间
            $start_time = $this->getCoachNearTime($order,$coach);

            if(empty($start_time)){

                return ['code'=>500,'msg'=>'该向导不符合该订单时间要求，请刷新重试'];
            }
            //校验主订单
            $check = $order_model->checkTime($order,$start_time);

            if(!empty($check['code'])){

                return $check;
            }
            //校验加钟订单
            $check = $this->checkAddOrderTime($order,$start_time,$coach_id);

            if(!empty($check['code'])){

                return $check;
            }

        }

        return $start_time;

    }


    /**
     * @author chenniang
     * @DataTime: 2023-02-22 11:33
     * @功能说明:订单更换向导
     */
    public function orderChangeCoach($order,$coach_id,$control_id,$admin_id,$coach_name='',$text='',$phone=''){

        if($order['pay_type']<1){

            return ['code'=>500,'msg'=>'订单未付款，无法转派'];
        }

        if($order['pay_type']==7){

            return ['code'=>500,'msg'=>'订单已结束，无法转派'];
        }

        $init_coach_id = $this->where(['order_id'=>$order['id']])->value('init_coach_id');

        $init_coach_id = !empty($init_coach_id)?$init_coach_id:$order['coach_id'];

        Db::startTrans();
        //给向导结算车费
        $have_car_price = $this->giveCarPrice($order);

        if(!empty($have_car_price['code'])){

            Db::rollback();

            return $have_car_price;
        };

        $order['true_car_price'] = $have_car_price==1?0:$order['true_car_price'];
        //添加转发日志
        $res = $this->addLog($order,$coach_id,$coach_name,$init_coach_id,$text,$phone,$have_car_price);

        if($res==0){

            Db::rollback();

            return ['code'=>500,'msg'=>'转派失败，请重试'];
        }
        //校验时间
        $start_time = $this->checkTime($order,$coach_id);

        if(!empty($start_time['code'])){

            return $start_time;
        }
        //修改订单信息
        $res = $this->updateOrderInfo($order,$coach_id,$have_car_price,$start_time,$control_id,$admin_id);

        if($res==0){

            Db::rollback();

            return ['code'=>500,'msg'=>'转派失败，请重试1'];
        }
        //重新结算佣金
        $res= $this->getCashData($order,$coach_id,$admin_id);

        if(!empty($res['code'])&&$res['code']==300){

            Db::rollback();

            return ['code'=>500,'msg'=>'请添加向导等级'];

        }

        if($res==0){

            Db::rollback();

            return ['code'=>500,'msg'=>'转派失败，请重试2'];
        }

        Db::commit();

        $order_model = new Order();

        $order = $order_model->dataInfo(['id'=>$order['id']]);

        if(!empty($phone)){

            $config_model = new ShortCodeConfig();

            $res = $config_model->sendSms($phone, $order['uniacid'], $order['order_code'], 1);

        }else{

            $coach_model = new Coach();
            //发送通知
            $coach_model->paySendMsg($order);
        }

        if(!empty($admin_id)){

            $order['admin_id'] = $admin_id;
        }
        //语音电话通知
        $call_model = new \app\reminder\model\Config();

        $call_model->sendCalled($order,$phone);
        //打印
        $print_model = new Printer();

        $print_model->printer($order['id'],0);

        return $res;
    }


    /**
     * @author chenniang
     * @DataTime: 2023-02-28 17:35
     * @功能说明:订单转派记录
     */
    public function orderChangeLog($order_id){

        $dis = [

            'order_id' => $order_id
        ];

        $data = $this->where($dis)->select()->toArray();

        $coach_model = new Coach();

        if(!empty($data)){

            foreach ($data as &$v){

                if($v['old_coach_id']!=0){

                    $v['old_coach_name'] = $coach_model->where(['id'=>$v['old_coach_id']])->value('coach_name');

                    $v['old_coach_img']  = $coach_model->where(['id'=>$v['old_coach_id']])->value('work_img');

                    $v['old_coach_mobile']  = $coach_model->where(['id'=>$v['old_coach_id']])->value('mobile');
                }else{

                    $v['old_coach_img'] = defaultCoachAvatar();
                }

                if($v['now_coach_id']!=0){

                    $v['now_coach_name'] = $coach_model->where(['id'=>$v['now_coach_id']])->value('coach_name');

                    $v['now_coach_img']  = $coach_model->where(['id'=>$v['now_coach_id']])->value('work_img');

                    $v['now_coach_mobile']  = $coach_model->where(['id'=>$v['now_coach_id']])->value('mobile');

                }else{

                    $v['now_coach_img'] = defaultCoachAvatar();
                }

            }
        }

        return $data;

    }





    /**
     * @param $order
     * @param $start_time
     * @param int $order_id 加钟订单
     * @param int $p_order_id 升级订单
     * @功能说明:转派向导时候 获取满足条件的向导 并获取最近的服务时间
     * @author chenniang
     * @DataTime: 2023-03-02 11:56
     */
    public function getNearTimeCoach($order,$list){

        $service_model = new Service();

        $coach_model   = new Coach();

        $order_model   = new Order();

        $total_long = 0;

        $config_model = new Config();

        foreach ($order['order_goods'] as $v){

            $time_long = $service_model->where(['id'=>$v['service_id']])->value('time_long');

            $total_long += $time_long*$v['num'];

        }

        $total_long = $total_long*60;

        $arr = [];

        $config = $config_model->dataInfo(['uniacid' => $order['uniacid']]);

        $start_time = $order['start_time']>time()?$order['start_time']:time();
        //目前只获取往后24小时
        $order_end_time = $start_time+86400;
        //检查该时间段是否被预约
        $where[] = ['pay_type','not in',[-1,7]];

        $where[] = ['end_time','>',time()];

        $where[] = ['id','<>',$order['id']];

        $order_list = $order_model->where($where)->field('id,start_time,end_time,order_end_time,pay_type,coach_id')->select()->toArray();

        $time_interval = $config['time_interval']>0?$config['time_interval']*60-1:0;

        foreach ($list as $coach){

            if(empty($coach['start_time'])){

                continue;
            }
            //校验向导休息时间
            $rest_arr = $coach_model->getCoachRestTime($coach,$start_time,$order_end_time,$config);

            $i = 0;

            $work_start_time = $start_time;

            while ($work_start_time<$order_end_time){

                $work_start_time = $start_time + $i*60;

                $end_time = $total_long+$work_start_time;

                $i++;

                if($work_start_time-$time_interval<=time()){

                    continue;
                }
                //校验向导服务时间
                $res = $order_model->checkCoachTime($coach,$work_start_time,$end_time);

                if(!empty($res['code'])){

                    continue;
                }
                //校验向导订单时间
                $res = $order_model->checkOrderTime($order_list,$config,$work_start_time,$end_time,$coach['id']);

                if(!empty($res['code'])){

                    continue;
                }
                //校验加钟订单
                $check = $this->checkAddOrderTime($order,$work_start_time,$coach['id']);

                if(!empty($check['code'])){

                    continue;
                }
                //校验向导休息时间
                $res = $order_model->checkCoachRestTime($rest_arr,$work_start_time,$end_time);

                if(!empty($res['code'])){

                    continue;
                }

                $arr[] = $coach['id'];

                $coach_model->dataUpdate(['id'=>$coach['id']],['near_time'=>$work_start_time]);

                break;
            }
        }

        return $arr;

    }



    /**
     * @param $order
     * @param $start_time
     * @param int $order_id 加钟订单
     * @param int $p_order_id 升级订单
     * @功能说明:转派向导时候 获取满足条件的向导 并获取最近的服务时间
     * @author chenniang
     * @DataTime: 2023-03-02 11:56
     */
    public function getCoachNearTime($order,$coach){

        $service_model = new Service();

        $coach_model   = new Coach();

        $order_model   = new Order();

        $total_long = 0;

        $config_model = new Config();

        foreach ($order['order_goods'] as $v){

            $time_long = $service_model->where(['id'=>$v['service_id']])->value('time_long');

            $total_long += $time_long*$v['num'];

        }

        $total_long = $total_long*60;

        $config = $config_model->dataInfo(['uniacid' => $order['uniacid']]);

        $start_time = $order['start_time']>time()+$config['time_interval']*60?$order['start_time']:time()+$config['time_interval']*60;
        //目前只获取往后24小时
        $order_end_time = $start_time+86400;
        //检查该时间段是否被预约
        $where[] = ['pay_type','not in',[-1,7]];

        $where[] = ['end_time','>',time()];

        $order_list = $order_model->where($where)->field('id,start_time,end_time,order_end_time,pay_type,coach_id')->select()->toArray();
        //校验向导休息时间
        $rest_arr = $coach_model->getCoachRestTime($coach,$start_time,$order_end_time,$config);

        $i = 0;

        $order_start_time = $start_time;

        while ($order_start_time<$order_end_time){

            $order_start_time = $start_time + $i * 60;

            $end_time = $total_long+$order_start_time;

            $i++;
            //校验向导时间
            $res = $order_model->checkCoachTime($coach,$order_start_time,$end_time);

            if(!empty($res['code'])){

                continue;
            }
            //校验向导时间
            $res = $order_model->checkOrderTime($order_list,$config,$order_start_time,$end_time,$coach['id']);

            if(!empty($res['code'])){

                continue;
            }
            //校验加钟订单
            $check = $this->checkAddOrderTime($order,$order_start_time,$coach['id']);

            if(!empty($check['code'])){

                continue;
            }
            //校验向导休息时间
            $res = $order_model->checkCoachRestTime($rest_arr,$order_start_time,$end_time);

            if(!empty($res['code'])){

                continue;
            }

           return $order_start_time;

        }

        return false;

    }











}