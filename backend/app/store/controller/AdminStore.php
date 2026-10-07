<?php
namespace app\store\controller;
use app\AdminRest;
use app\massage\model\ActionLog;
use app\massage\model\Admin;
use app\massage\model\AdminRole;
use app\node\model\RoleAdmin;
use app\node\model\RoleList;
use app\node\model\RoleNode;
use app\store\model\StoreList;
use think\App;



class AdminStore extends AdminRest
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

        $dis[] = ['status','>',-1];

        if(!empty($input['title'])){

            $dis[] = ['title','like','%'.$input['title'].'%'];

        }

        if($this->_user['is_admin']!=1){

            $dis[] = ['admin_id','=',$this->_user['id']];
        }

        $store_model = new StoreList();

        $data = $store_model->dataList($dis,$input['limit']);

        return $this->success($data);

    }


    /**
     * @author chenniang
     * @DataTime: 2023-03-28 15:48
     * @功能说明:门店下拉框
     */
    public function storeSelect(){

        $input = $this->_param;

        $dis[] = ['uniacid','=',$this->_uniacid];

        $dis[] = ['status','=',1];

        if(!empty($input['title'])){

            $dis[] = ['title','like','%'.$input['title'].'%'];

        }

        if($this->_user['is_admin']!=1){

            $dis[] = ['admin_id','=',$this->_user['id']];
        }

        if(!empty($input['admin_id'])){

            $dis[] = ['admin_id','=',$input['admin_id']];
        }

        $store_model = new StoreList();

        $data = $store_model->where($dis)->select()->toArray();

        return $this->success($data);

    }


    /**
     * @author chenniang
     * @DataTime: 2023-03-23 16:25
     * @功能说明:添加门店信息
     */
    public function storeAdd(){

        $input = $this->_input;

        $input['uniacid'] = $this->_uniacid;

        $input['total_num'] = $input['order_num'];

        $store_model = new StoreList();

        $res = $store_model->dataAdd($input);

        return $this->success($res);
    }


    /**
     * @author chenniang
     * @DataTime: 2023-03-23 16:34
     * @功能说明:门店信息
     */
    public function storeInfo(){

        $input = $this->_param;

        $dis = [

            'id' => $input['id']
        ];

        $store_model = new StoreList();

        $res = $store_model->dataInfo($dis);

        return $this->success($res);
    }


    /**
     * @author chenniang
     * @DataTime: 2023-03-23 16:35
     * @功能说明:编辑门店
     */
    public function storeUpdate(){

        $input = $this->_input;

        $dis = [

            'id' => $input['id']
        ];

        $store_model = new StoreList();

        $data = $store_model->dataInfo($dis);

        if(isset($input['order_num'])){

            $input['total_num'] = $input['order_num']+$data['true_order_num'];

        }

        $res = $store_model->dataUpdate($dis,$input);

        return $this->success($res);
    }


















}
