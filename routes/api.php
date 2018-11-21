<?php

$api = app('Illuminate\Routing\Router');

// 后台API
$api->group([
    'namespace' => 'Api',
    'middleware' => 'cors'
], function ($api) {

    $api->group([
        'middleware' => 'throttle: 20, 1', // 调用接口限制 1分钟20次
    ], function ($api) {
        // 后台登录
        $api->post('login', 'AuthorizationsController@login');
        // 小程序登录
        $api->post('weapp/login', 'AuthorizationsController@weappStore');
    });

    $api->group([
        'middleware' => [
            'throttle: 60, 1', // 调用接口限制 1分钟60次
            'refresh.token' // 刷新 token
        ]
    ], function ($api) {
        // 退出登录
        $api->delete('logout', 'AuthorizationsController@logout');
    });
});
