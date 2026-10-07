<?php
namespace app\store\controller;
use app\AdminRest;
use app\ApiRest;
use app\card\model\Config;
use app\massage\model\ActionLog;
use app\massage\model\Admin;
use app\massage\model\AdminRole;
use app\massage\model\Coach;
use app\massage\model\Comment;
use app\massage\model\Service;
use app\node\model\RoleAdmin;
use app\node\model\RoleList;
use app\node\model\RoleNode;
use app\store\model\StoreList;
use think\App;



class IndexStore extends ApiRest
{

    public function __construct(App $app) {

        parent::__construct($app);

    }


    /**
     * @author chenniang
     * @DataTime: 2023-03-23 16:25
     * @功能说明:门店列表
     */
    public function storeList(){

        $input = $this->_param;

        $dis[] = ['uniacid','=',$this->_uniacid];

        $dis[] = ['status','=',1];

        if(!empty($input['title'])){

            $dis[] = ['title','like','%'.$input['title'].'%'];

        }

        $lat = !empty($input['lat'])?$input['lat']:0;

        $lng = !empty($input['lng'])?$input['lng']:0;

        $alh = 'ACOS(SIN(('.$lat.' * 3.1415) / 180 ) *SIN((lat * 3.1415) / 180 ) +COS(('.$lat.' * 3.1415) / 180 ) * COS((lat * 3.1415) / 180 ) *COS(('.$lng.' * 3.1415) / 180 - (lng * 3.1415) / 180 ) ) * 6378.137*1000 as distance';

        $store_model = new StoreList();

        $data = $store_model->indexDataList($dis,$alh);

        if(!empty($data['data'])){

            foreach ($data['data'] as &$v){

                $v['work_status'] = $store_model->workStatus($v);

                $v['distance'] = distance_text($v['distance']);

            }

        }

        return $this->success($data);

    }


    /**
     * @author chenniang
     * @DataTime: 2023-03-29 17:43
     * @功能说明:门店详情
     */
    public function storeInfo(){

        $input = $this->_param;

        $store_model = new StoreList();

        $service_model = new Service();

        $coach_model   = new Coach();

        $dis = [

            'id' => $input['id']
        ];

        $data['info'] = $store_model->dataInfo($dis);
        //获取门店关联的服务
        $data['service_list'] = $service_model->getStoreService($input['id']);
        //屏蔽向导
        $shield_coach = $coach_model->getShieldCoach($this->getUserId());
        //获取门店关联的向导
        $data['coach_list'] = $coach_model->where(['store_id'=>$input['id'],'status'=>2,'is_work'=>1])->where('id','not in',$shield_coach)->field('id,coach_name,work_img,city_id')->order('id desc')->limit(10)->select()->toArray();

        return $this->success($data);

    }


    /**
     * @author chenniang
     * @DataTime: 2023-03-29 17:54
     * @功能说明:门店管理的服务列表
     */
    public function storeServiceList(){

        $input = $this->_param;

        $service_model = new Service();
        //获取门店关联的服务
        $data = $service_model->getStoreServicePage($input['id']);

        return $this->success($data);
    }


    /**
     * @author chenniang
     * @DataTime: 2023-03-31 11:39
     * @功能说明:门店详情评价列表
     */
    public function commentList(){

        $input = $this->_param;

        $comment_model = new Comment();

        $coach_model   = new Coach();
        //屏蔽向导
        $shield_coach = $coach_model->getShieldCoach($this->getUserId());

        $coach_list = $coach_model->where(['store_id'=>$input['id'],'status'=>2,'is_work'=>1])->where('id','not in',$shield_coach)->column('id');

        $dis[] = ['a.status','=',1];

        $dis[] = ['a.coach_id','in',$coach_list];

        $data = $comment_model->dataList($dis);

        $config_model  = new \app\massage\model\Config();

        $anonymous_evaluate = $config_model->where(['uniacid'=>$this->_uniacid])->value('anonymous_evaluate');

        if(!empty($data['data'])){

            foreach ($data['data'] as &$v){

                $v['create_time'] = date('Y-m-d H:i:s',$v['create_time']);
                //开启匿名评价
                if($anonymous_evaluate==1){

                    $v['nickName'] = '匿名用户';

                    $v['avatarUrl']= 'https://lbqny.migugu.com/admin/farm/default-user.png';
                }
            }
        }

        return $this->success($data);

    }















}
