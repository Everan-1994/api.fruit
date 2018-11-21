<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\InvalidRequestException;
use App\Http\Controllers\Controller;
use App\Http\Requests\UserRequest;
use App\Http\Requests\WeappAuthorizationRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

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

        // 用户是否冻结
        if (!$userData['status']) {
            $this->logout();
            throw new InvalidRequestException('账户被冻结');
        }

        // 用户时候拥有权限
        if ($userData['identify'] === 3) {
            $this->logout();
            throw new InvalidRequestException('没有权限');
        }

        $auth = [
            'data' => $userData,
            'meta' => $this->respondWithToken($token),
        ];

        return $this->success($auth, '登录成功');
    }

    /**
     * @param WeappAuthorizationRequest $request
     * @return \Illuminate\Http\JsonResponse
     * @throws InvalidRequestException
     */
    public function weappStore(WeappAuthorizationRequest $request)
    {
        // 根据 code 获取微信 openid 和 session_key
        $miniProgram = \EasyWeChat::miniProgram();
        $data = $miniProgram->auth->session($request->input('code'));

        // 如果结果错误，说明 code 已过期或不正确，返回 401 错误
        if (isset($data['errcode'])) {
            throw new InvalidRequestException($data['errmsg'], Response::HTTP_UNAUTHORIZED);
        }

        // 找到 openid 对应的用户 找不到则创建
        $user = User::query()->whereOpenid($data['openid'])->first();

        if (!$user) {
            $user = User::query()->create([
                'name'     => $request->input('nickName'),
                'sex'      => $request->input('gender'),
                'avatar'   => $request->input('avatarUrl'),
                'phone'    => $request->input('phone'),
                'status'   => User::ACTIVE,
                'password' => bcrypt($data['openid']),
                'openid'   => $data['openid'],
            ]);
        }

        $attributes['weixin_session_key'] = $data['session_key'];

        if ($user['status'] === User::FREEZE) {
            throw new InvalidRequestException('账号已被冻结，请联系管理员。', Response::HTTP_BAD_REQUEST);
        }

        // 更新用户数据
        $user->update($attributes);

        $auth = [
            'data' => $user,
            'meta' => $this->respondWithToken(Auth::guard('api')->attempt([
                'phone'    => $user['phone'],
                'password' => $user['openid'],
            ])),
        ];

        return $this->success($auth);
    }

    protected function respondWithToken($token)
    {
        return [
            'accessToken' => $token,
            'tokenType'   => 'Bearer',
            'expiresIn'   => Auth::guard('api')->factory()->getTTL() * 60,
        ];
    }

    public function logout()
    {
        Auth::guard('api')->logout();

        return $this->success([], '退出成功');
    }
}
