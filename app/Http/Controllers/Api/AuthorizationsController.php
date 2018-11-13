<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\InvalidRequestException;
use App\Http\Controllers\Controller;
use App\Http\Requests\UserRequest;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Auth;

class AuthorizationsController extends Controller
{
    /**
     * @param UserRequest $request
     * @return \Illuminate\Http\JsonResponse
     * @throws InvalidRequestException
     */
    public function login(UserRequest $request)
    {
        $credentials = [
            'phone'    => preg_replace('/\s+/', '', $request->input('phone')),
            'password' => preg_replace('/\s+/', '', $request->input('password')),
        ];

        $token = Auth::guard('api')->attempt($credentials);

        if (!$token) {
            throw new InvalidRequestException('手机号或密码错误');
        }

        // 记录登入日志
        // event(new LoginEvent(\Auth::guard('api')->user(), new Agent(), $request->getClientIp()));

        // 使用 Auth 登录用户
        $userData = (new UserResource(Auth::guard('api')->user()));

        // 用户是否启用
        if (!$userData['status']) {
            throw new InvalidRequestException('账户被冻结');
        }

        $auth = [
            'data' => $userData,
            'meta' => [
                'accessToken' => $token,
                'tokenType'   => 'Bearer',
                'expiresIn'   => Auth::guard('api')->factory()->getTTL() * 60,
            ],
        ];

        return response()->json($auth);
    }

    public function logout()
    {
        Auth::guard('api')->logout();

        return response()->json(['msg' => '退出成功']);
    }
}
