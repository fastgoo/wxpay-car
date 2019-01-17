<?php
/**
 * Created by PhpStorm.
 * User: Mr.Zhou
 * Date: 2019/1/16
 * Time: 9:21 AM
 */
include_once "./vendor/autoload.php";

$config = [
    'mch_id' => '',
    'appid' => '',
    'appsecret' => '',
    'key' => '',
    'sub_mch_id' => '',
    'sub_appid' => '',
    'sub_appsecret' => '',
    'sign_type' => 'HMAC-SHA256',
    'trade_scene' => 'PARKING',
    'version' => '2.0',
    'jump_scene' => 'APP',
];
$client = \CarPay\CarClient::init($config);
$pnumber = "";


//入场通知
try {
    $ret = $client->user()->incomeNotify([
        'start_time' => '20190117133000',//入场时间
        'plate_number' => $pnumber,//车牌号
        'notify_url' => "http://www.baidu.com",//回调地址，用于接收当前处于停车场内所有车辆车牌状态变更的通知
        'car_type' => '小型车',//车辆类型：小型车 大型车
        'parking_name' => '测试停车场',//停车场名称，32个长度
        'free_time' => '1800',//免费时长，秒
    ]);
    var_dump($ret);
    exit;
} catch (\CarPay\Core\CarPayException $exception) {
    var_dump($exception->getMessage());
}


//申请扣款
try {
    $ret = $client->order()->pay([
        'body' => '停车测试自动扣费',//扣费描述
        'detail' => "详情1\n详情2\n详情3",//扣费描述详情
        'attach' => json_encode(['a' => '123']),//自定义参数
        'out_trade_no' => '773729701',//订单号
        'total_fee' => '10',//金额，分
        'spbill_create_ip' => '127.0.0.1',//本地IP
        'notify_url' => 'http://www.baidu.com',//回调地址
        'start_time' => '20190117133000',//入场时间
        'end_time' => '20190117135000',//出场时间
        'charging_time' => '1200',//停车时长，秒
        'plate_number' => $pnumber,//车牌号
        'car_type' => '小型车',//车辆类型：小型车 大型车
        'parking_name' => '测试停车场',//停车场名称，32个长度
    ]);
    var_dump($ret);
    exit;
} catch (\CarPay\Core\CarPayException $exception) {
    var_dump($exception->getMessage());
}


//查询订单
try {

    $ret = $client->order()->payQuery(['out_trade_no' => '123456']);
    var_dump($ret);
    exit;
} catch (\CarPay\Core\CarPayException $exception) {
    var_dump($exception->getMessage());
}


//获取车牌号的状态，未签约则获取参数去唤起签约
try {
    switch ($client->user()->getState($pnumber)) {
        case "NORMAL"://正常用户，已开通车主服务，且已授权访问
            //do something...
            break;
        case "PAUSED"://已暂停车主服务
            //do something...
            break;
        case "OVERDUE"://用户已开通车主服务，但欠费状态。提示用户还款，请跳转到车主服务
            //do something...
            break;
        case "UNAUTHORIZED"://用户未授权使用当前业务，或未开通车主服务。请跳转到授权接口
            //根据code获取到openid，然后获取授权所需的参数，传给 小程序 | h5 | APP 唤起微信签约
            //$code = "";
            //$openid = $client->user()->getOpenidByCode($code);
            $openid = "oe3dLuKuO3U0udKYKh0QJkUFd7Us";
            $authInfo = $client->user()->getAuthSign($openid, $pnumber);
            print_r($authInfo);
            break;
        default://异常状态类型
    }

} catch (\CarPay\Core\CarPayException $carPayException) {
    var_dump($carPayException->getMessage());
}