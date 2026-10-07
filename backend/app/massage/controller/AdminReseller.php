<?php
namespace app\massage\controller;
use app\AdminRest;
use app\massage\model\Commission;
use app\massage\model\Config;
use app\massage\model\DistributionList;
use app\massage\model\User;
use app\massage\model\Wallet;
use longbingcore\wxcore\YsCloudApi;
use think\App;



class AdminReseller extends AdminRest
{


    protected $model;

    protected $user_model;

    protected $cash_model;

    protected $wallet_model;



    public function __construct(App $app) {

        parent::__construct($app);

        $this->model        = new DistributionList();

        $this->user_model   = new User();

        $this->cash_model   = new Commission();

        $this->wallet_model   = new Wallet();



    }



    /**
     * @author chenniang
     * @DataTime: 2021-03-15 14:43
     * @功能说明:列表
     */
    public function resellerList(){

        $input = $this->_param;

        $dis[] = ['a.uniacid','=',$this->_uniacid];

        if(!empty($input['status'])){

            $dis[] = ['a.status','=',$input['status']];

        }else{

            $dis[] = ['a.status','>',-1];

        }

        if(!empty($input['start_time'])&&!empty($input['end_time'])){

            $start_time = $input['start_time'];

            $end_time   = $input['end_time'];

            $dis[] = ['a.create_time','between',"$start_time,$end_time"];

        }

        $where = [];

        if(!empty($input['name'])){

            $where[] = ['a.user_name','like','%'.$input['name'].'%'];

            $where[] = ['a.mobile','like','%'.$input['name'].'%'];
        }

        $data = $this->model->adminDataList($dis,$input['limit'],$where);

        $list = [

            0=>'all',

            1=>'ing',

            2=>'pass',

            4=>'nopass'
        ];

        foreach ($list as $k=> $value){

            $dis_s = [];

            $dis_s[] =['uniacid','=',$this->_uniacid];

            if(!empty($k)){

                $dis_s[] = ['status','=',$k];

            }else{

                $dis_s[] = ['status','>',-1];

            }

            $data[$value] = $this->model->where($dis_s)->count();

        }

        return $this->success($data);

    }

    /**
     * @Desc: 分销商信息
     * @return mixed
     * @throws \think\db\exception\DbException
     * @Auther: shurong
     * @Time: 2023/10/26 16:41
     */
    public function resellerInfo(){

        $input = $this->_param;

        $dis = [

            'id' => $input['id']
        ];

        $info = $this->model->dataInfo($dis);

        $user_model = new User();

        $info['nickName'] = $user_model->where(['id'=>$info['user_id']])->value('nickName');

        $info['p_nickName'] = $user_model->where(['id'=>$info['pid']])->value('nickName')??'--';


        $userData = $user_model->getSubUserData($info['user_id']);
        $info = array_merge($info,$userData);

        $userData = $user_model->getSubDistributionData($info['user_id']);
        $info = array_merge($info, $userData);

        return $this->success($info);

    }

    /**
     * @Desc: 分销商下级
     * @return mixed
     * @Auther: shurong
     * @Time: 2023/10/26 17:11
     */
    public function getSubList()
    {
        $input = $this->_param;

        $type = \request()->param('type', 1);

        $user_model = new User();

        $info = $this->model->dataInfo(['id' => $input['id']]);

        $dis = [

            'a.pid' => $info['user_id']
        ];

        if ($type == 1) {

            $dis['a.is_fx'] = 1;

            $data = $user_model->getSubDistributionList($dis);
        } else {
            $dis['a.is_fx'] = 0;

            $data = $user_model->getSubUserList($dis);

        }


        if (!empty($data['data'])) {

            foreach ($data['data'] as &$v) {

                if (isset($v['refund_price'])) {

                    $v['pay_price'] = $v['pay_price'] - $v['refund_price'];

                }

                $v['pay_price'] = round($v['pay_price'], 2);

                $v['create_time'] = !empty($v['create_time']) ? handleTime($v['create_time']) : '';
            }
        }

        return $this->success($data);
    }


    /**
     * @author chenniang
     * @DataTime: 2021-07-03 00:15
     * @功能说明:审核(2通过,3取消,4拒绝)
     */
    public function resellerUpdate(){

        $input = $this->_input;

        $diss = [

            'id' => $input['id']
        ];

        $info = $this->model->dataInfo($diss);

        if(!empty($input['status'])&&in_array($input['status'],[2,4,-1])){

            $input['sh_time'] = time();

            if($input['status']==-1){

                $fx_cash = $this->user_model->where(['id'=>$info['user_id']])->sum('new_cash');

                if($fx_cash>0){

                    $this->errorMsg('分销商还有佣金未提现');
                }

                $dis = [

                    'top_id'  => $info['user_id'],

                    'status'  => 1,

                    'type'    => 1
                ];

                $cash = $this->cash_model->dataInfo($dis);

                if(!empty($cash)){

                    $this->errorMsg('分销商还有佣金未到账');

                }

                $dis = [

                    'user_id' => $info['user_id'],

                    'status'  => 1,

                    'type'    => 4
                ];

                $wallet = $this->wallet_model->dataInfo($dis);

                if(!empty($wallet)){

                    $this->errorMsg('分销商还有提现未处理');

                }

            }

        }

        $data = $this->model->dataUpdate($diss,$input);

        if(isset($input['status'])){

            $update = [

                'is_fx' => 0
            ];

            if($input['status']==2){

                $update['is_fx'] = 1;

            }
            $user = $this->user_model->dataInfo(['id' => $info['user_id']]);

            if ($user['pid'] == 0) {

                $update['pid'] = $info['pid'];

            }

            $this->user_model->dataUpdate(['id'=>$info['user_id']],$update);

        }

        return $this->success($data);

    }


    /**
     * @author chenniang
     * @DataTime: 2023-03-23 10:22
     * @功能说明:合伙人数据统计
     */
    public function partnerDataList(){

        $input = $this->_param;

        $config_model = new Config();

        $config = $config_model->dataInfo(['uniacid'=>$this->_uniacid]);

        $dis[] = ['a.uniacid','=',$this->_uniacid];

        if($config['fx_check']==1){

            $dis[] = ['b.status','=',2];

        }

        $where = [];

        if(!empty($input['name'])){

            $where[] = ['a.nickName','like','%'.$input['name'].'%'];

            $where[] = ['b.user_name','like','%'.$input['name'].'%'];

//            $where[] = ['b.mobile','like','%'.$input['name'].'%'];

        }

        if(!empty($input['start_time'])&&!empty($input['end_time'])){

            $dis[] = ['b.sh_time','between',"{$input['start_time']},{$input['end_time']}"];

        }

        $data = $this->model->userDataList($dis,$where,$input['limit']);

        return $this->success($data);
    }









}
