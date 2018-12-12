<?php

$api = app('Illuminate\Routing\Router');

// API
$api->group([
    'namespace' => 'Api',
    'middleware' => 'cors'
], function ($api) {

    $api->group([
        'middleware' => 'throttle: 20, 1', // 调用接口限制 1分钟20次
    ], function ($api) {
        // 后台登录
        $api->post('login', 'AuthorizationsController@login');
        // 小程序登录信息更新
        $api->post('weapp/update_info', 'AuthorizationsController@weappStore');
        // 小程序登录
        $api->post('weapp/authorizations', 'AuthorizationsController@weappLogin');

    });

    $api->group([
        'middleware' => [
            'throttle: 60, 1', // 调用接口限制 1分钟60次
            'refresh.token' // 刷新 token

        ]
    ], function ($api) {
        // 退出登录
        $api->delete('logout', 'AuthorizationsController@logout');

        $api->group([
            'prefix' => 'weapp' // 前缀
        ],function ($api) {
            $api->get('test', 'AuthorizationsController@testApi');
        });

        $api->group([
            'prefix' => 'fruit' // 前缀
        ],function ($api) {
            // 用户列表
            $api->get('user', 'UserController@index');
        });

    });
});
