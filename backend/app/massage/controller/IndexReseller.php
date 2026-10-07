<?php
namespace app\massage\controller;
use app\AdminRest;
use app\ApiRest;
use app\massage\model\City;
use app\massage\model\Coach;
use app\massage\model\CoachLevel;
use app\massage\model\Commission;
use app\massage\model\Config;
use app\massage\model\DistributionList;
use app\massage\model\Order;
use app\massage\model\User;
use app\massage\model\Wallet;
use longbingcore\wxcore\YsCloudApi;
use think\App;



class IndexReseller extends ApiRest
{


    protected $model;

    protected $user_model;

    protected $cash_model;

    protected $wallet_model;

    protected $coach_model;


    public function __construct(App $app) {

        parent::__construct($app);

        $this->model        = new DistributionList();

        $this->user_model   = new User();

        $this->cash_model   = new Commission();

        $this->wallet_model = new Wallet();

        $this->coach_model  = new Coach();

    }


    /**
     * @author chenniang
     * @DataTime: 2023-03-23 13:49
     * @功能说明:合伙人中心
     */
    public function partnerIndex(){

        $order_model = new Order();
        //超时自动取消订单
        $order_model->coachBalanceArr($this->_uniacid);

        $data = $this->user_model->dataInfo(['id'=>$this->_user['id']],'nickName,avatarUrl,new_cash,cash');

        $data['order_cash'] = $this->model->partnerOrderPrice($this->_user['id']);
        //已提现金额
        $data['wallet_cash'] = $this->wallet_model->where(['user_id'=>$this->_user['id'],'type'=>4])->where('status','<>',3)->sum('apply_price');
        //未入账
        $data['not_recorded']= $this->cash_model->where(['top_id'=>$this->_user['id'],'status'=>1])->where('type','in',[1,9])->sum('cash');
        //累计订单量
        $data['total_order_count'] = $this->cash_model->where(['top_id'=>$this->_user['id'],'status'=>2])->where('type','in',[1,9])->group('order_id')->count();
        //今日订单数量
        $data['today_order_count'] = $this->cash_model->where(['top_id'=>$this->_user['id'],'status'=>2])->where('type','in',[1,9])->whereTime('create_time','today')->group('order_id')->count();
        //累计邀请向导
        $data['total_coach_count'] = $this->coach_model->where(['partner_id'=>$this->_user['id'],'agent_type'=>2,'status'=>2])->count();
        //今日邀请向导
        $data['today_coach_count'] = $this->coach_model->where(['partner_id'=>$this->_user['id'],'agent_type'=>2,'status'=>2])->whereTime('partner_time','today')->count();
        //累计邀请用户
        $data['total_user_count'] = $this->user_model->where(['pid'=>$this->_user['id']])->count();
        //今日邀请用户
        $data['today_user_count'] = $this->user_model->where(['pid'=>$this->_user['id']])->whereTime('create_time','today')->count();

        $data['order_cash'] = round($data['order_cash'],2);

        $data['wallet_cash']= round($data['wallet_cash'],2);

        $data['not_recorded']= round($data['not_recorded'],2);

        return $this->success($data);

    }


    /**
     * @author chenniang
     * @DataTime: 2023-03-23 14:30
     * @功能说明:合伙人邀请的向导
     */
    public function partnerCoachList(){

        $dis = [

            'status'     => 2,

            'agent_type' => 2,

            'partner_id' => $this->_user['id']
        ];

        $data = $this->coach_model->where($dis)->field('id,coach_name,work_img,city_id')->order('partner_time desc,id desc')->paginate(10)->toArray();

        if(!empty($data['data'])){

            $config_model = new Config();

            $level_model  = new CoachLevel();

            $city_model   = new City();

            $config = $config_model->dataInfo(['uniacid'=>$this->_uniacid]);

            $level_cycle = $config['level_cycle'];

            $is_current  = $config['is_current'];

            foreach ($data['data'] as &$v){

                $v['order_count'] = $level_model->getMinCount($v['id'],$level_cycle,0,$is_current);

                $v['city'] = $city_model->where(['id'=>$v['city_id']])->value('city');
            }

        }
        return $this->success($data);
    }















}
