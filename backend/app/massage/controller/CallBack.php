<?php
namespace app\massage\controller;
use app\AdminRest;
use app\ApiRest;
use app\massage\model\FddRealnameCallback;
use app\virtual\model\PlayRecord;
use longbingcore\wxcore\PayNotify;
use think\App;
use think\facade\Db;
use WxPayApi;


class CallBack  extends ApiRest
{

    protected $app;

    public function __construct ( App $app )
    {
        $this->app = $app;
    }


    /**
     * @author chenniang
     * @DataTime: 2022-12-08 15:33
     * @功能说明:发大大实名认证回调
     */
    public function fddCallBack(){

        $inputs = $_GET;

        $model = new FddRealnameCallback();

        $insert = [

            'uniacid' => 666,
            'personName' => $inputs['personName'],
            'transactionNo' => $inputs['transactionNo'],
            'authenticationType' => $inputs['authenticationType'],
            'status' => $inputs['status'],
            'sign' => $inputs['sign'],
        ];

        $model->dataAdd($insert);

        $res = ['code'=>0,'msg'=>'成功'];

        echo json_encode($res);exit;

    }



}
