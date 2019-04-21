<?php
/**
 * 验证活动和商品的状态
 * 这个接口对应着 /astatus/{aid}_{gid}.js 静态文件的动态实现
 * 例如： /astatus/1_1.js
 * 不能秒杀的时候，静态文件才会存在
 * 活动开始前，静态文件存在
 * 互动进行中，会统一把静态文件删除，则nginx的rewrite失效，进入到这个动态文件
 *
 * nginx 的站点配置信息(文件不存在的时候走rewrite到动态文件)
 * 如果请求的文件$request_filename不存在 就走下面rewrite的规则  执行astatus.php文件而不是astatus.js文件
 * if (!-e $request_filename) {
 *   rewrite ^([^\.]*)/astatus/([0-9]+)_([0-9]+).js$ $1/astatus.php?aid=$2&gid=$3 last;
 * }
 *
 * 例如： /astatus/1_2.js
 * 文件如果存在，则nginx直接返回静态文件的内容
 * 如果不存在，则把参数赋值给动态接口 /astatus.php?aid=1&gid=2
 *
 * User: wangyi
 */


/**
 * 初始化，获取变量，登录信息
 */
include './init.php';

$aid = getReqInt('aid');
$gid = getReqInt('gid');


if (!$login_userinfo['uid'] || !$aid || !$gid) {
    $result = array('error_no' => '201', 'error_msg' => '请求参数异常');
    echo json_encode($result);
    exit();
}


/**
 * 商品状态
 */
//性能考虑 获取redis缓存

//获取redis服务对象
$redis_obj = \common\Datasource::getRedis('instance1');
//mget 读取两个数据
$data = $redis_obj->mget(array(
    'st_a_' . $aid,//获取活动信息
    'st_g_' . $gid,//获取goods信息
));

die('test');


if ($data && $data[0] && $data[1]) {
    // 活动状态和商品状态都是正常状态，才可以返回一个正确的验证码
    $info = array(
        'now' => time(),
        'ip' => getClientIp(),
        'uid' => $login_userinfo['uid'],
    );
    // 加密签名
    $str = signQuestion($info);
    echo json_encode(array('user_sign' => $str));
} else {
    $result = array('error_no' => '202', 'error_msg' => '活动商品已下架或者已售完');
    echo json_encode($result);
}