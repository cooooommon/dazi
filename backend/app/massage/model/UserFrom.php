<?php
/**
 * Created by PhpStorm
 * User: shurong
 * Date: 2024/2/20
 * Time: 14:39
 * docs:
 */

namespace app\massage\model;

use app\BaseModel;

class UserFrom extends BaseModel
{
    protected $name = 'massage_user_from_list';

    //from_type 1 公众号搜索 2 用户分享链接 3 分销员-邀请用户 4 渠道商 5 渠道商员工 6 经纪人 7 代理商 8 技师-充值邀请码 9 渠道商 10 分销员-分销员邀请用户购买会员卡 11 抖音 12 视频号 13 小红书 14 其他

    /**
     * @Desc: 插入
     * @param $uniacid
     * @param $user_id
     * @param $from_type
     * @param $from_id
     * @return int|string
     * @Auther: shurong
     * @Time: 2024/2/20 14:45
     */
    public static function add($uniacid, $user_id, $from_type, $from_id)
    {
        //判断是否为分销员
        if (in_array($from_type, [2, 3, 10])) {

//            $config = longbingGetAppConfig($uniacid,true);
//
//            if ($config['fx_check']==1){
//
//
//            }


            $distribu_model = new DistributionList();

            $dis = [

                'user_id' => $from_id,

                'status' => 2
            ];

            $distribu_user = $distribu_model->dataInfo($dis);

            if ($distribu_user) {
                $from_type = $from_type == 2 ? 3 : $from_type;

                $from_id = $distribu_user['id'];
            } else {

                $from_type = 2;
            }
        }

        $insert = [
            'uniacid' => $uniacid,
            'user_id' => $user_id,
            'from_type' => $from_type,
            'from_id' => $from_id,
            'create_time' => time()
        ];

        return self::insert($insert);
    }

    /**
     * @Desc: 获取用户来源
     * @param $user_ids
     * @return array
     * @Auther: shurong
     * @Time: 2024/2/20 15:27
     */
    public static function getListByUserIds($user_ids, $uniacid)
    {
        $data = self::alias('a')
            ->field('a.user_id,a.from_type,a.from_name,b.nickName,c.user_name,d.user_name as channel_name,e.name as staff_name,f.name as broker_name,g.agent_name as admin_name,h.coach_name,i.channel_id')
            ->whereIn('a.user_id', $user_ids)
            ->leftJoin('massage_service_user_list b', 'a.from_id=b.id and a.from_type=2')
            ->leftJoin('massage_distribution_list c', 'a.from_id=c.id and a.from_type in (3,10)')
            ->leftJoin('massage_channel_list d', 'a.from_id=d.id and a.from_type=4')
            ->leftJoin('massage_channel_staff_list e', 'a.from_id=e.id and a.from_type=5')
            ->leftJoin('massage_broker_list f', 'a.from_id=f.id and a.from_type=6')
            ->leftJoin('shequshop_school_admin g', 'a.from_id=g.id and a.from_type=7')
            ->leftJoin('massage_service_coach_list h', 'a.from_id=h.id and a.from_type=8')
            ->leftJoin('massage_channel_staff_list i', 'a.from_id=i.id and a.from_type=9')
            ->select()
            ->toArray();

        $arr = [];

        $attendant_name = getConfigSetting($uniacid, 'attendant_name');

        if (!empty($data)) {
            foreach ($data as $item) {
                switch ($item['from_type']) {
                    case 1:
                        $from_name = '公众号搜索';
                        break;
                    case 2:
                        $from_name = $item['nickName'] . '-分享链接';
                        break;
                    case 3:
                        $from_name = $item['user_name'] . '-分销码';
                        break;
                    case 4:
                        $from_name = $item['channel_name'] . '-渠道商邀请用户二维码';
                        break;
                    case 5:
                        $from_name = $item['staff_name'] . '-渠道员工码';
                        break;
                    case 6:
                        $from_name = $item['broker_name'] . '-经纪人邀请' . $attendant_name . '码';
                        break;
                    case 7:
                        $from_name = $item['admin_name'] . '-代理商邀请' . $attendant_name . '码';
                        break;
                    case 8:
                        $from_name = $item['coach_name'] . '-' . $attendant_name . '充值邀请码';
                        break;
                    case 9:
                        $from_name = ChannelList::where('id', $item['channel_id'])->value('user_name') . '-渠道商邀请员工二维码';
                        break;
                    case 10:
                        $from_name = $item['user_name'] . '-分销员邀请用户购买会员卡';
                        break;
                    case 11:
                        $from_name = '抖音-' . (empty($item['from_name']) ? '无' : $item['from_name']);
                        break;
                    case 12:
                        $from_name = '视频号-' . (empty($item['from_name']) ? '无' : $item['from_name']);
                        break;
                    case 13:
                        $from_name = '小红书-' . (empty($item['from_name']) ? '无' : $item['from_name']);
                        break;
                    case 14:
                        $from_name = '其他-' . (empty($item['from_name']) ? '无' : $item['from_name']);
                        break;
                    default:
                        $from_name = '';
                }
                $arr[] = [
                    'user_id' => $item['user_id'],
                    'from_name' => $from_name
                ];
            }
        }

        return $arr;
    }

    /**
     * @Desc: 获取单字段
     * @param $user_id
     * @param $field
     * @Auther: shurong(贝润网络)
     * @Time: 2024/11/18 17:03
     */
    public static function getFromValue($user_id, $field = 'from_type')
    {
        return self::where('user_id', $user_id)->value($field);
    }

    /**
     * @Desc: 编辑用户来源
     * @param $data
     * @return UserFrom|int|string
     * @throws \think\db\exception\DbException
     * @Auther: shurong(贝润网络)
     * @Time: 2024/11/18 18:03
     */
    public static function updateFrom($data)
    {
        $count = self::where(['user_id' => $data['user_id']])->count();

        if ($count > 0) {

            return self::where(['user_id' => $data['user_id']])->update($data);
        } else {
            $data['create_time'] = time();
            return self::insert($data);
        }
    }
}