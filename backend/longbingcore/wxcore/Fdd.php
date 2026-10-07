<?php
declare(strict_types=1);

namespace longbingcore\wxcore;

use app\card\model\User;
use FddAccount;
use FddCertification;
use FddContractManageMent;
use FddSignContract;
use FddTemplate;
use think\facade\Db;

class Fdd{

    static protected $uniacid;

    public function __construct($uniacid)
    {
       self::$uniacid = $uniacid;

        //引用excel库
        require_once  EXTEND_PATH.'fdd/Fdd.Api.php';

    }


    /**
     * @author chenniang
     * @DataTime: 2023-04-06 16:40
     * @功能说明:注册账号
     */
    public function registerAccount($user_id,$type=1){

        $user_model = new \app\massage\model\User();

        $open_id = $user_model->where(['id'=>$user_id])->value('openid');

        if(empty($open_id)){

            return ['code'=>500,'用户信息错误'];
        }

        $open_id = $open_id.$type;

        $open_id = str_replace('-','',$open_id);

        $input = new FddAccount();

        $input->SetOpenID($open_id);

        $input->SetAccountType($type);

        $res = \FddApi::registerAccount($input,1);

        return $res;

    }


    /**
     * @author chenniang
     * @DataTime: 2023-04-06 18:27
     * @功能说明:获取个人实名认证地址
     */
    public function getPersonVerifyUrl($customer_id){

        $input = new FddCertification();

        $input->SetCustomerID($customer_id);

        $input->SetVerifiedWay(9);

        $input->SetPageModify(1);

        $NotifyUrl = 'https://'.$_SERVER['SERVER_NAME'].'/massage/CallBack/fddCallBack';

        $input->SetNotifyUrl($NotifyUrl);

       // $input->SetReturnUrl('');

        $res = \FddApi::getPersonVerifyUrl($input);

        return $res;

    }


    /**
     * @param $customer_id
     * @功能说明:获取企业实名认证地址
     * @author chenniang
     * @DataTime: 2023-04-07 14:21
     */
    public function getCompanyVerifyUrl($customer_id){

        $input = new FddCertification();

        $input->SetCustomerId($customer_id);

        //$input->SetVerifiedWay(9);

        $input->SetPageModify(1);


     //   $NotifyUrl = 'https://'.$_SERVER['SERVER_NAME'].'/massage/CallBack/fddCallBack';

      //  $input->SetNotifyUrl($NotifyUrl);

        // $input->SetReturnUrl('');

        $res = \FddApi::getCompanyVerifyUrl($input);

        return $res;

    }


    /**
     * @author chenniang
     * @DataTime: 2023-04-07 13:48
     * @功能说明:查询个人认证信息
     */
    public function FindPersonCertInfo($transactionNo){

        $input = new FddCertification();

        $input->SetVerifiedVSerialNo($transactionNo);

        $res = \FddApi::FindPersonCertInfo($input);

        return $res;

    }



    /**
     * @author chenniang
     * @DataTime: 2023-04-07 13:48
     * @功能说明:查询企业认证信息
     */
    public function FindCompanyCertInfo($transactionNo){

        $input = new FddCertification();

        $input->SetVerifiedVSerialNo($transactionNo);

        $res = \FddApi::FindCompanyCertInfo($input);

        return $res;


    }


    /**
     * @author chenniang
     * @DataTime: 2023-04-07 14:04
     * @功能说明:通过uuid下载文件
     */
    public function getFile($uuid,$type=1){

        $input = new FddCertification();

        $input->SetUUID($uuid);

        $input->SetDoc_type($type);

        $res = \FddApi::getFile($input);

        return $res;

    }


    /**
     * @author chenniang
     * @DataTime: 2023-04-07 14:13
     * @功能说明:绑定实名认证
     */
    public function ApplyCert($customer_id,$verified_serialno){

        $input = new FddCertification();

        $input->SetCustomerId($customer_id);

        $input->SetVerifiedVSerialNo($verified_serialno);

        $res = \FddApi::ApplyCert($input);

        return $res;

    }


    /**
     * @author chenniang
     * @DataTime: 2023-04-07 15:55
     * @功能说明:模版填充
     */
    public function GenerateContract($tmpl_id,$title,$map){

        $input = new FddTemplate();

        $input->SetDoc_title($title);

        $input->SetTemplate_id($tmpl_id);

        $input->SetContract_id(orderCode());

        $input->SetParameter_map($map);

        $res = \FddApi::GenerateContract($input);

        return $res;

    }


    /**
     * @author chenniang
     * @DataTime: 2023-04-07 16:24
     * @功能说明:上传合同
     */
    public function Uploaddocs($contract_id,$doc_title,$doc_url){

        $input = new FddTemplate();

        $input->SetContract_id($contract_id);

        $input->SetDoc_title($doc_title);

        $input->SetDoc_url($doc_url);

        $input->SetDoc_type('.doc');

        $res = \FddApi::Uploaddocs($input);

        return $res;
    }


    /**
     * @author chenniang
     * @DataTime: 2023-04-07 17:08
     * @功能说明:自动签署
     */
    public function ExtsignAuto($transaction_id,$contract_id,$customer_id,$doc_title){

        $input = new FddSignContract();

        $input->SetTransaction_id($transaction_id);

        $input->SetContract_id($contract_id);

        $input->SetCustomer_id($customer_id);

        $input->SetDoc_title($doc_title);

        $res = \FddApi::ExtsignAuto($input);

        return $res;

    }



    /**
     * @author chenniang
     * @DataTime: 2023-04-07 17:08
     * @功能说明:手动签署
     */
    public function Extsign($transaction_id,$contract_id,$customer_id,$doc_title){

        $input = new FddSignContract();

        $input->SetTransaction_id($transaction_id);

        $input->SetContract_id($contract_id);

        $input->SetCustomer_id($customer_id);

        $input->SetDoc_title($doc_title);

        $res = \FddApi::Extsign($input);

        return $res;

    }


    /**
     * @param $contract_id
     * @功能说明:合同归档
     * @author chenniang
     * @DataTime: 2023-04-07 17:26
     */
    public function ContractFiling($contract_id){

        $input = new FddContractManageMent();

        $input->SetContract_id($contract_id);

        $res = \FddApi::ContractFiling($input);

        return $res;

    }


    /**
     * @param $contract_id
     * @功能说明:下载合同
     * @author chenniang
     * @DataTime: 2023-04-07 17:29
     */
    public function DownLoadContract($contract_id){

        $input = new FddContractManageMent();

        $input->SetContract_id($contract_id);

        $res = \FddApi::DownLoadContract($input);

        return $res;

    }



    /**
     * @param $contract_id
     * @功能说明:合同查看
     * @author chenniang
     * @DataTime: 2023-04-07 17:29
     */
    public function ViewContract($contract_id){

        $input = new FddContractManageMent();

        $input->SetContract_id($contract_id);

        $res = \FddApi::ViewContract($input);

        return $res;
    }




}