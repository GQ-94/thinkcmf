<?php

namespace app\demo\controller;

use cmf\controller\BaseController;
use GuzzleHttp\Client;

/**
 * Class CurlController
 * @package app\demo\controller
 */
class CurlController extends BaseController
{
    /**
     * 抓取王者荣耀英雄图片
     */
    public function index()
    {
        $client   = new Client();
        $response = $client->request('GET', 'http://pvp.qq.com/web201605/js/herolist.json');
        $json_arr = json_decode($response->getBody()->getContents(), true);

        if (!file_exists(ROOT_PATH . 'public/upload/heroskin')) {
            mkdir(ROOT_PATH . 'public/upload/heroskin');
        }

        foreach ($json_arr as $value) {
            $id                 = $value['ename'];
            $hero_name          = $value['cname'];
            $value['skin_name'] = isset($value['skin_name']) ? $value['skin_name'] : $value['title'];
            $list_img           = explode('|', $value['skin_name']);
            for ($i = 1; $i <= count($list_img); $i++) {
                $file_path = 'upload/heroskin/' . $hero_name . '-' . $list_img[$i - 1] . '.jpg';
                $client->get("http://game.gtimg.cn/images/yxzj/img201606/skin/hero-info/$id/$id-bigskin-$i.jpg", ['save_to' => $file_path]);
            }
        }
    }
}
