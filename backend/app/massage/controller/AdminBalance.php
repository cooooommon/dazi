<?php
namespace app\massage\controller;
use app\AdminRest;
use app\massage\model\BalanceCard;
use app\massage\model\BalanceOrder;
use app\massage\model\BalanceWater;
use app\massage\model\Coach;
use app\massage\model\Commission;
use app\massage\model\Coupon;
use app\massage\model\Integral;
use app\massage\model\MassageConfig;
use app\massage\model\User;
use app\shop\model\Article;
use app\shop\model\Banner;
use app\shop\model\Cap;
use app\shop\model\Date;
use app\shop\model\MsgConfig;
use app\shop\model\OrderAddress;
use app\shop\model\OrderGoods;
use app\shop\model\RefundOrder;
use app\shop\model\RefundOrderGoods;
use app\shop\model\Wallet;
use think\App;
use app\shop\model\Order as Model;
use think\facade\Db;


class AdminBalance extends AdminRest
{


    protected $model;

    protected $order_model;

    protected $refund_order_model;

    public function __construct(App $app) {

        parent::__construct($app);

        $this->model = new BalanceCard();

        $this->order_model = new BalanceOrder();

      //  $this->refund_order_model = new RefundOrder();

    }


    /**
     * @author chenniang
     * @DataTime: 2021-07-04 19:09
     * @功能说明:储值充值卡列表
     */
    public function cardList(){

        $input = $this->_param;

        $dis[] = ['uniacid','=',$this->_uniacid];

        $dis[] = ['status','>',-1];

        if(!empty($input['name'])){

            $dis[] = ['title','like','%'.$input['name'].'%'];

        }

        if (!empty($input['status'])){

            $dis[] = ['status','=',$input['status']];
        }

        $data = $this->model->dataList($dis,$input['limit']);

        return $this->success($data);

    }



    /**
     * @author chenniang
     * @DataTime: 2021-07-04 18:56
     * @功能说明:添加充值卡
     */
    public function cardAdd(){

        $input = $this->_input;

        $input['uniacid'] = $this->_uniacid;

        $res = $this->model->dataAdd($input);

        return $this->success($res);

    }


    /**
     * @author chenniang
     * @DataTime: 2021-07-04 18:57
     * @功能说明:编辑充值卡
     */
    public function cardUpdate(){

        $input = $this->_input;

        $dis = [

            'id' => $input['id']
        ];

        $res = $this->model->dataUpdate($dis,$input);

        return $this->success($res);

    }


    /**
     * @author chenniang
     * @DataTime: 2021-07-04 18:59
     * @功能说明:充值卡详情
     */
    public function cardInfo(){

        $input = $this->_input;

        $dis = [

            'id' => $input['id']
        ];

        $res = $this->model->dataInfo($dis);

        return $this->success($res);


    }




    /**
     * @author chenniang
     * @DataTime: 2021-07-04 19:09
     * @功能说明:储值订单列表
     */
    public function orderList(){

        $input = $this->_param;

        $dis[] = ['uniacid','=',$this->_uniacid];

        $dis[] = ['status','>',1];

        if(!empty($input['name'])){

            $dis[] = ['title','like','%'.$input['name'].'%'];

        }

        if(!empty($input['order_code'])){

            $dis[] = ['order_code','like','%'.$input['order_code'].'%'];

        }

        if(!empty($input['start_time'])){

            $dis[] = ['pay_time','between',"{$input['start_time']},{$input['end_time']}"];

        }

        $data = $this->order_model->dataList($dis,$input['limit']);

        if ($data['data']){

            foreach ($data['data'] as &$datum) {

                $datum['coach_name'] = Coach::where(['id' => $datum['coach_id']])->value('coach_name');

                $comm_info = Commission::where(['order_id' => $datum['id'], 'type' => 7, 'status' => 2])->find();

                if (!empty($comm_info)) {
                    //佣金
                    $datum['comm_cash'] = $comm_info['cash'];
                    //比列
                    $datum['comm_balance'] = (float)$comm_info['balance'];
                }
            }
        }


        return $this->success($data);

    }






    /**
     * @author chenniang
     * @DataTime: 2021-07-04 18:59
     * @功能说明:充值订单详情
     */
    public function orderInfo(){

        $input = $this->_input;

        $dis = [

            'id' => $input['id']
        ];

        $res = $this->order_model->dataInfo($dis);

        return $this->success($res);


    }


    /**
     * @author chenniang
     * @DataTime: 2021-07-07 17:00
     * @功能说明:充值余额
     */
    public function payBalanceOrder(){

        $input = $this->_input;

        $is_add = isset($input['is_add'])?$input['is_add']:1;

        if(!empty($input['card_id'])){

            $dis = [

                'id'     => $input['card_id'],

                'status' => 1
            ];

            $card = $this->model->dataInfo($dis);

            if(empty($card)){

                $this->errorMsg('充值卡已被下架');
            }

        }else{

            $card = [

                'price'      => $input['price'],

                'true_price' => $input['price'],

                'id'         => 0,

                'title'      => $is_add==1?'自定义金额充值':'自定义扣款'
            ];
        }

        $pay_model = isset($input['pay_model'])?$input['pay_model']:1;

        foreach ($input['user_id'] as $value){

            if($is_add==-1){

                $user_model = new User();

                $balance = $user_model->where(['id'=>$value])->value('balance');

                if($balance<$input['price']){

                    $this->errorMsg('用户扣款余额不足,用户ID:'.$value);
                }
            }

            $insert = [

                'uniacid'    => $this->_uniacid,

                'user_id'    => $value,

                'order_code' => orderCode(),

                'pay_price'  => $card['price'],

                'integral'   => $card['price'],

                'sale_price' => $card['price'],

                'true_price' => $card['true_price'],

                'card_id'    => $card['id'],

                'title'      => $card['title'],

                'coach_id'   => !empty($input['coach_id'])?$input['coach_id']:0,

                'pay_model' => $pay_model,

                'admin_user'=> $this->_user['id'],

                'type'      => $is_add==1?2:3

            ];

            $res = $this->order_model->dataAdd($insert);

            if($res==0){

                $this->errorMsg('充值失败');

            }

            $order_id = $this->order_model->getLastInsID();

            $config_model = new MassageConfig();

            $config = $config_model->dataInfo(['uniacid'=>$this->_uniacid]);
            //储值返回佣金
            if(!empty($input['coach_id'])&&$config['balance_cash']==1&&$card['price']>0){

                $cash_insert = [

                    'uniacid' => $this->_uniacid,

                    'user_id' => $value,

                    'top_id'  => $input['coach_id'],

                    'order_id'=> $order_id,

                    'order_code' => $insert['order_code'],

                    'type'    => 7,

                    'cash'    => round($config['balance_balance']*$card['price']/100,2),

                    'balance' => $config['balance_balance'],

                    'status'  => -1,

                ];

                $comm_model = new Commission();

                $comm_model->dataAdd($cash_insert);
                //
                $integral_insert = [

                    'uniacid' => $this->_uniacid,

                    'coach_id' => $input['coach_id'],

                    'order_id'=> $order_id,

                    'integral'   => round($config['balance_balance']*$card['price']/100,2),

                    'balance' => $config['balance_balance'],

                    'status'  => -1,

                    'type'    => 1,

                    'user_id' => $value,

                    'user_cash'=>  $card['price']

                ];

                $integral_model = new Integral();

                $integral_model->dataAdd($integral_insert);
            }
            //储值返回积分
            if(!empty($input['coach_id'])&&$config['balance_integral']==1&&$card['price']>0){

                $integral_insert = [

                    'uniacid' => $this->_uniacid,

                    'coach_id' => $input['coach_id'],

                    'order_id'=> $order_id,

                    'integral'   => round($card['price'],2),

                    'balance' => 100,

                    'status'  => -1,

                    'user_id' => $input['user_id'],

                    'user_cash'=> $card['price']

                ];

                $integral_model = new Integral();

                $integral_model->dataAdd($integral_insert);
            }

            $res = $this->order_model->orderResult($insert['order_code'],$insert['order_code']);

        }

        return $this->success($res);

    }

    /**
     * @author chenniang
     * @DataTime: 2021-07-07 17:00
     * @功能说明: 储值流水
     */
    public function payWater(){

        $input = $this->_param;

        $dis[] = ['user_id','=',$input['user_id']];

        if(!empty($input['start_time'])){

            $dis[] = ['create_time','between',"{$input['start_time']},{$input['end_time']}"];

        }

        $water_model = new BalanceWater();

        $data = $water_model->dataList($dis,$input['limit']);

        if(!empty($data['data'])){

            $admin_model = new \app\massage\model\Admin();

            $user_model  = new User();

            foreach ($data['data'] as &$v){

                $order = $this->order_model->dataInfo(['id'=>$v['order_id']]);

                if(!empty($order['admin_user'])){

                    $v['control_name'] = $admin_model->where(['id'=>$order['admin_user']])->value('username');
                }else{

                    $v['control_name'] = $user_model->where(['id'=>$v['user_id']])->value('nickName');
                }

                $v['price'] = round($v['price'],2);
            }

        }

        return $this->success($data);

    }



}
