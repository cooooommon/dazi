<?php

namespace app\card\controller;

use app\BaseController;

class GetImage extends BaseController
{

    /**
     * 将线上图片转为本地图片用于前端cavans画图
     */
    public function getImage ()
    {
        $param = $this->request->param();
        $path = isset($param['path']) ? trim((string) $param['path']) : '';
        if ($path === '') {
            return $this->error('请传入参数');
        }
        //只允许 http/https，禁止内网/保留地址，避免 SSRF
        $parts = parse_url($path);
        if (empty($parts['scheme']) || empty($parts['host']) ||
            !in_array(strtolower($parts['scheme']), ['http', 'https'], true)) {
            return $this->error('非法图片地址');
        }
        $ip = gethostbyname($parts['host']);
        if (!filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
            return $this->error('非法图片地址');
        }
        //把https 替换为  http
        $path = str_replace("https://", "http://", $path);
        //禁止远程重定向，避免通过 302 绕过内网地址校验
        stream_context_set_default(['http' => ['follow_location' => 0, 'max_redirects' => 0]]);
        //判断类型
        $type_img = @getimagesize($path);
        if (empty($type_img['mime'])) {
            return $this->error('图片不存在');
        }
        ob_start();
        $resourch = null;
        if (strpos($type_img['mime'], 'jpeg') !== false) {
            $resourch = imagecreatefromjpeg($path);
        } elseif (strpos($type_img['mime'], 'png') !== false) {
            $resourch = imagecreatefrompng($path);
        }
        if (empty($resourch)) {
            ob_end_clean();
            return $this->error('不支持的图片格式');
        }
        if (strpos($type_img['mime'], 'jpeg') !== false) {
            imagejpeg($resourch);
        } else {
            imagepng($resourch);
        }
        $content = ob_get_clean();
        imagedestroy($resourch);
        return response($content, 200, ['Content-Length' => strlen($content)])->contentType('image/png');
    }
}