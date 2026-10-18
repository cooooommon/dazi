<?php

namespace app\publics\controller;

use app\BaseController;
use app\card\service\UserService;
use app\massage\model\Police;
use app\massage\model\SendMsgConfig;
use think\facade\Db;
use think\facade\Log;
use think\facade\Request;

class HttpAsync extends BaseController
{

    public function message()
    {
        //异步消息入口必须携带 .env 中配置的 ASYNC_MESSAGE_KEY，未配置时默认关闭
        $secret = (string) \think\facade\Env::get('ASYNC_MESSAGE_KEY', '');
        $key = (string) ($this->request->header('x-async-key') ?: Request::param('async_key', ''));
        if ($secret === '' || !hash_equals($secret, $key)) {
            http_response_code(401);
            echo 'no auth';
            return;
        }
        $message = Request::param('message', '');
        $data = json_decode((string) $message, true);
        if (!is_array($data) || empty($data['action'])) {
            echo 'invalid message';
            return;
        }
        //try{
        switch ($action = $data['action']) {
            //老代码  作注释处理
            /*//发送消息服务通知
            case 'sendMessageWxServiceNotice':
                longbingSendMessageWxServiceNotice($data['message']);
                break;
            //发送普通服务通知
            case 'SendWxServiceNotice':
                longbingSendWxServiceNotice($data['count_id']);
                break;
            //发送普通服务通知
            case 'longbingSendWxServiceNotice':
                longbingSendWxServiceNotice($data['count_id']);
                break;
            case 'longbingSendWxServiceNoticeBase':
                longbingSendWxServiceNoticeBase($data['data']);
                break;
            case 'updatecollectionRate':
                updatecollectionRate($data['client_id']);
                break;
            case 'updateCustomerRate':
                updateCustomerRate($data['page'] ,$data['page_count']);
                break;
            case 'longbingCreateWxCode':
                longbingCreateWxCode($data['uniacid'], $data['data'], $data['page'], $data['type']);
                break;
            case 'longbingCreateSharePng':
                UserService::createHeaderQr($data['uniacid'], ['staff_id' => $data['user_id']]);
                longbingCreateSharePng($data['gData'], $data['user_id'], $data['uniacid']);
                break;
            case 'longbingSaveFormId':
                longbingSaveFormId($data['data']);
                break;
            case 'longbingCreateHeaderQr':
                UserService::createHeaderQr($data['uniacid'], ['staff_id' => $data['user_id']]);
                break;*/
            case 'police_notice'://求救通知
                $model = new Police();
                $model->sendPoliceNotice($data['uniacid'], $data['coach_id'], $data['address']);
                break;
            case 'orderServiceNotice'://订单服务通知
                $model = new SendMsgConfig();
                $model->orderServiceNotice($data['uniacid']);
                break;
        }
        //}catch(Exception $e)
        //{}
        echo 'message ok ';
    }

}
