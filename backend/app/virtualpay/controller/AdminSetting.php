<?php
declare (strict_types = 1);

namespace app\virtualpay\controller;

use app\AdminRest;
use think\App;
use think\facade\Db;

/**
 * 虚拟支付管理端配置（OfferID / AppKey / 兑换比例）
 * 若未接入管理后台 UI，可直接按 backend/virtualpay.sql 中的示例写表
 */
class AdminSetting extends AdminRest
{

    protected $app;

    public function __construct ( App $app )
    {
        parent::__construct($app);

        $this->app = $app;
    }


    /**
     * 配置详情
     */
    public function configInfo ()
    {
        $config = Db::name('virtualpay_config')->where(['uniacid' => $this->_uniacid])->find();

        if (!empty($config['app_key'])) {

            $config['app_key'] = substr($config['app_key'], 0, 4) . '****';
        }

        return $this->success(['config' => $config ?: null]);
    }


    /**
     * 配置保存（app_key 留空表示不修改）
     */
    public function configUpdate ()
    {
        $input = $this->request->only(['offer_id', 'app_key', 'env', 'enabled', 'coin_rate']);

        $data = [
            'offer_id'    => trim((string)($input['offer_id'] ?? '')),
            'env'         => intval($input['env'] ?? 0),
            'enabled'     => intval($input['enabled'] ?? 1),
            'coin_rate'   => intval($input['coin_rate'] ?? 1),
            'update_time' => time(),
        ];

        if (!empty($input['app_key'])) {

            $data['app_key'] = trim((string)$input['app_key']);
        }

        if (!empty($data['offer_id']) && empty($data['app_key'])) {

            $exists = Db::name('virtualpay_config')->where(['uniacid' => $this->_uniacid])->value('app_key');

            if (empty($exists)) {

                $this->errorMsg('请填写现网 AppKey');
            }
        }

        $exists = Db::name('virtualpay_config')->where(['uniacid' => $this->_uniacid])->find();

        if (!empty($exists)) {

            Db::name('virtualpay_config')->where(['uniacid' => $this->_uniacid])->update($data);

        } else {

            $data['uniacid']     = $this->_uniacid;
            $data['create_time'] = time();

            if (empty($data['app_key'])) {

                $this->errorMsg('请填写现网 AppKey');
            }

            Db::name('virtualpay_config')->insert($data);
        }

        return $this->success(true);
    }

}
