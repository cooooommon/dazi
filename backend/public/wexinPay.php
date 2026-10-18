<?php
/**
 * 微信支付 v2 异步通知中继（独立版）
 *
 * 下单时 notify_url 形如：
 *   https://<host>/wexinPay.php?params={"i":"1","t":"","v":"","is_app":0,"n":"longbing_playwith"}
 * 微信服务器会向该地址 POST XML 通知。本文件把原始 XML 原样转发给
 * /index.php/shop/IndexWxPay/returnPay，并把后端的处理结果返回给微信。
 *
 * 安全约束：
 *  - 转发目标固定为当前站点，不接受外部 URL，避免 SSRF；
 *  - params 只读取 i / is_app，且必须是数字；
 *  - 不落盘、不执行任何外部内容。
 */

ini_set('display_errors', '0');
error_reporting(0);

/**
 * 返回微信 FAIL，让微信按策略重试
 */
function wexinPayReplyFail($msg)
{
    header('Content-Type: text/xml; charset=utf-8');
    echo '<xml><return_code><![CDATA[FAIL]]></return_code><return_msg><![CDATA['
        . str_replace(']]>', ']]]]><![CDATA[>', $msg)
        . ']]></return_msg></xml>';
    exit;
}

// 支付控制器发起支付前会请求 ?health=1 做健康检查，必须返回 ok
if (isset($_GET['health']) && $_GET['health'] === '1') {
    header('Content-Type: text/plain; charset=utf-8');
    echo 'ok';
    exit;
}

if (!isset($_SERVER['REQUEST_METHOD']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit;
}

$rawBody = file_get_contents('php://input');
if ($rawBody === false || $rawBody === '' || strlen($rawBody) > 1048576) {
    wexinPayReplyFail('invalid notify body');
}

$params = isset($_GET['params']) && is_string($_GET['params'])
    ? json_decode($_GET['params'], true)
    : null;
if (!is_array($params) || !isset($params['i'], $params['is_app'])) {
    wexinPayReplyFail('invalid params');
}

$uniacid = (string) $params['i'];
$isApp   = (string) $params['is_app'];
if (!preg_match('/^\d+$/', $uniacid) || !preg_match('/^\d+$/', $isApp)) {
    wexinPayReplyFail('invalid params');
}

$host = $_SERVER['HTTP_HOST'] ?? '';
if (!preg_match('/^[A-Za-z0-9.\-]+(:\d+)?$/', $host)) {
    wexinPayReplyFail('invalid host');
}

$url = 'https://' . $host . '/index.php/shop/IndexWxPay/returnPay?'
    . http_build_query(['i' => $uniacid, 'is_app' => $isApp]);

$ch = curl_init($url);
curl_setopt_array($ch, [
    CURLOPT_POST           => true,
    CURLOPT_POSTFIELDS     => $rawBody,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_CONNECTTIMEOUT => 2,
    CURLOPT_TIMEOUT        => 5,
    CURLOPT_HTTPHEADER     => ['Content-Type: text/xml; charset=utf-8'],
    CURLOPT_SSL_VERIFYPEER => true,
    CURLOPT_SSL_VERIFYHOST => 2,
    CURLOPT_FOLLOWLOCATION => false,
]);
$response = curl_exec($ch);
$status   = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error    = curl_error($ch);
curl_close($ch);

if ($response === false || $status < 200 || $status >= 300 || trim($response) === '') {
    error_log('wexinPay relay failed: ' . $error . ' status=' . $status);
    wexinPayReplyFail('notify forward failed');
}

// 后端正常应返回微信要求的 XML；不是则按失败处理，让微信重试
if (strpos($response, '<return_code>') === false) {
    error_log('wexinPay relay got unexpected response: ' . substr(strip_tags($response), 0, 500));
    wexinPayReplyFail('invalid backend response');
}

header('Content-Type: text/xml; charset=utf-8');
echo $response;
exit;
