<?php


namespace app\massage\model;


use app\BaseModel;

class EntryAgreement extends BaseModel
{
    protected $name = 'massage_coach_entry_agreement';

    public static function getInfo($uniacid)
    {
        $info = self::where(['uniacid' => $uniacid])->find();
        if (empty($info)) {
            self::insert(['uniacid' => $uniacid]);
        }
        $data = self::where(['uniacid' => $uniacid])->find();
        $data = [
            'registration_agreement' => $data['registration_agreement'] ?? '',
            'billing_rules' => $data['billing_rules'] ?? '',
            'legal_notice' => $data['legal_notice'] ?? ''
        ];
        return $data;
    }
}