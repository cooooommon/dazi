<?php

namespace app\massage\model;

use app\BaseModel;
use think\facade\Db;

class Diy extends BaseModel
{
    //定义表名
    protected $name = 'massage_action_diy';


    /**
     * @author chenniang
     * @DataTime: 2020-09-29 11:04
     * @功能说明:添加
     */
    public function dataAdd($data)
    {

        $res = $this->insert($data);

        return $res;

    }


    /**
     * @author chenniang
     * @DataTime: 2020-09-29 11:05
     * @功能说明:编辑
     */
    public function dataUpdate($dis, $data)
    {

        $res = $this->where($dis)->update($data);

        return $res;

    }


    /**
     * @author chenniang
     * @DataTime: 2020-09-29 11:06
     * @功能说明:列表
     */
    public function dataList($dis, $page)
    {

        $data = $this->where($dis)->order('status desc,id desc')->paginate($page)->toArray();

        return $data;

    }


    /**
     * @author chenniang
     * @DataTime: 2020-09-29 11:43
     * @功能说明:
     */
    public function dataInfo($dis)
    {

        $data = $this->where($dis)->find();

        if (empty($data)) {

            $input = $dis;

            $input['page'] = [

                1 => [
                    [
                        "id" => "service-search-1",
                        "compontents" => "base",
                        "title" => "搜索",
                        "type" => "search",
                        "icon" => "iconsousuo2",
                        "isDelete" => true,
                        "addNumber" => 1,
                        "attr" => [],
                        "data" => [
                            "title" => "搜索",
                            "placeholder" => "搜索向导昵称"
                        ]
                    ],
                    [
                        "id" => "service-banner-1",
                        "compontents" => "base",
                        "title" => "轮播图",
                        "type" => "service-banner",
                        "icon" => "iconrenwu",
                        "isDelete" => true,
                        "addNumber" => 1,
                        "attr" => [],
                        "data" => [
                            "title" => "轮播图",
                            "style" => [
                                "number" => 400,
                                "min" => 200,
                                "max" => 800,
                                "label" => "请输入",
                                "height" => 400,
                                "whiteSpace" => 0,
                                "wingBlank" => 0,
                            ],
                            "isShowFilter" => true,
                            "bannerList" => [
                                [
                                    "img" => [
                                        0 => [
                                            "url" => "https://lbqnyv3.migugu.com/image/666/23/05/coLq1uUiDPgsYSUJrcX6qQfMGt87Cg7K.jpg"
                                        ]
                                    ]
                                ],
                                [
                                    "img" => [
                                        0 => [
                                            "url" => "https://lbqnyv3.migugu.com/image/666/23/08/LJey4EdOPAPXMTCsreDECr62DuJEq7LY.jpg"
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ],
                    [
                        "id" => "service-settle-1",
                        "compontents" => "base",
                        "title" => "入驻/招商",
                        "type" => "settle",
                        "icon" => "iconsousuo2",
                        "isDelete" => true,
                        "addNumber" => 1,
                        "attr" => [],
                        "data" => [
                            "title" => "入驻/招商"
                        ]
                    ],
                    [
                        "id" => "service-list-1",
                        "compontents" => "base",
                        "title" => "新奇玩法",
                        "type" => "newfangled",
                        "icon" => "iconrenwu",
                        "isDelete" => true,
                        "addNumber" => 1,
                        "attr" => [],
                        "data" => [
                            "title" => "新奇玩法"
                        ]
                    ]
                ],

                2 => [],

                8 =>
                    [
                        [
                            "id" => "store-search-1",
                            "compontents" => "base",
                            "title" => "搜索",
                            "type" => "search",
                            "icon" => "iconsousuo2",
                            "isDelete" => true,
                            "addNumber" => 1,
                            "attr" => [],
                            "data" => [
                                "title" => "搜索",
                                "placeholder" => "请输入场地名称"
                            ]
                        ],
                        "",
                        [
                            "title" => "门店分类",
                            "type" => "column",
                            "icon" => "icondaohang",
                            "isDelete" => true,
                            "addNumber" => 1,
                            "attr" => [],
                            "data" => [
                                "addMouduleName" => "columnList",
                                "columnList" => []
                            ],
                            "id" => 1709179158414,
                            "compontents" => "base"
                        ],
                        [
                            "id" => "store-list-1",
                            "compontents" => "base",
                            "title" => "门店列表",
                            "type" => "list",
                            "icon" => "iconliebiao",
                            "isDelete" => true,
                            "addNumber" => 1,
                            "attr" => [],
                            "data" => [
                                "title" => "门店列表"
                            ]
                        ]

                    ],
                5 => [],
            ];

            $input['page'] = json_encode($input['page']);

            $input['tabbar'] = [
                [
                    'id' => 1,
                    'name' => '首页',
                    'default_img' => 'iconshouye2',
                    'selected_img' => 'iconshouye12',
                ],
                [
                    'id' => 2,
                    'name' => '向导',
                    'default_img' => 'iconpeiwanguan1',
                    'selected_img' => 'iconpeiwanguan2',
                ],
                [
                    'id' => 8,
                    'name' => '门店',
                    'default_img' => 'iconmendian11',
                    'selected_img' => 'iconmendian1',
                ],
                [
                    'id' => 5,
                    'name' => '我的',
                    'default_img' => 'iconwode-2',
                    'selected_img' => 'iconwode-1',
                ]

            ];
            $input['tabbar'] = json_encode($input['tabbar']);

            $this->dataAdd($input);

            $data = $this->where($dis)->find();

        }

        return !empty($data) ? $data->toArray() : [];

    }


    /**
     * @author chenniang
     * @DataTime: 2021-03-19 16:08
     * @功能说明:开启默认
     */
    public function updateOne($id)
    {

        $user_id = $this->where(['id' => $id])->value('user_id');

        $res = $this->where(['user_id' => $user_id])->where('id', '<>', $id)->update(['status' => 0]);

        return $res;
    }

    /**
     * @Desc: 门店状态不对的banner 下架
     * @param $uniacid
     * @Auther: shurong
     * @Time: 2023/11/27 19:13
     */
    public function chancelBanner($uniacid)
    {
        $ids = StoreApply::where([['status', '<>', 2], ['uniacid', '=', $uniacid]])->column('id');

        $diy = $this->dataInfo(['uniacid' => $uniacid]);


        $page = json_decode($diy['page'], true);

        foreach ($page as $key => $value) {

            foreach ($value as $ky => $item) {

                if (isset($item['type']) && $item['type'] == 'banner') {

                    $banner = isset($item['data']['bannerList']) ? $item['data']['bannerList'] : [];

                    if (!empty($banner)) {

                        foreach ($banner as &$valu) {

                            if ($valu['linkType'] == 4) {

                                $url = $valu['link'][0]['url'];

                                $parsedUrl = parse_url($url);

                                $route = $parsedUrl['path'];

                                if (isset($parsedUrl['query'])) {
                                    $queryString = $parsedUrl['query'];

                                    parse_str($queryString, $params);

                                    if ($route == '/business/pages/store/detail' && isset($params['id']) && in_array($params['id'], $ids)) {

                                        $valu['linkType'] = 5;

                                    }
                                }
                            }
                        }

                        $item['data']['bannerList'] = $banner;

                        $value[$ky] = $item;

                    }


                }

            }
            $page[$key] = $value;
        }

        $this->where(['uniacid' => $uniacid])->update(['page' => json_encode($page)]);

    }

}