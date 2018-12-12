<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\InvalidRequestException;
use App\Http\Controllers\Controller;
use App\Http\Requests\UserRequest;
use App\Http\Requests\WeappAuthorizationRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
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
            'data' => new UserResource($userData),
            'meta' => $this->respondWithToken($token),
        ];

        return $this->success($auth, '登录成功');
    }

    /**
     * @param $code
     * @return mixed
     * @throws InvalidRequestException
     */
    public function getData($code)
    {
        // 根据 code 获取微信 openid 和 session_key
        $miniProgram = \EasyWeChat::miniProgram();
        $data = $miniProgram->auth->session($code);

        // 如果结果错误，说明 code 已过期或不正确，返回 401 错误
        if (isset($data['errcode'])) {
            throw new InvalidRequestException($data['errmsg'], Response::HTTP_UNAUTHORIZED);
        }

        return $data;
    }

    /**
     * @param WeappAuthorizationRequest $request
     * @return \Illuminate\Http\JsonResponse
     * @throws InvalidRequestException
     * @throws \Illuminate\Validation\ValidationException
     */
    public function weappStore(WeappAuthorizationRequest $request)
    {
        $data = $this->getData($request->input('code'));

        // 找到 openid 对应的用户 找不到则创建
        $user = User::query()->where('openid', $data['openid'])->first();

        if (!$user) {
            $this->validate($request, [
                'phone' => 'required|unique:users,phone',
            ], [
                'phone.required' => '手机号不能为空',
                'phone.unique'   => '手机号已备注册',
            ]);

            $user = User::query()->create([
                'name'     => $request->input('nickName'),
                'sex'      => $request->input('gender'),
                'avatar'   => $request->input('avatarUrl'),
                'phone'    => $request->input('phone'),
                'status'   => User::ACTIVE,
                'password' => bcrypt($data['openid']),
                'openid'   => $data['openid'],
            ]);
        } else {
            // 保存用户最新信息
            $attributes = [
                'name'   => $request->input('nickName'),
                'sex'    => $request->input('gender'),
                'avatar' => $request->input('avatarUrl'),
            ];
        }

        $attributes['session_key'] = $data['session_key'];

        if ($user['status'] === User::FREEZE) {
            throw new InvalidRequestException('账号已被冻结，请联系管理员。', Response::HTTP_BAD_REQUEST);
        }

        // 更新用户数据
        $user->update($attributes);

        return $this->success(new UserResource($user), 'success', Response::HTTP_CREATED);
    }

    /**
     * @param Request $request
     * @return array
     * @throws InvalidRequestException
     * @throws \Illuminate\Validation\ValidationException
     */
    public function weappLogin(Request $request)
    {
        $params = $this->validate($request, [
            'code'  => 'required|string',
            'phone' => 'regex:/^1[3456789][0-9]{9}$/',
        ], [
            'code.required' => 'code 不能为空',
            'phone.regex'   => '手机格式不正确'
        ]);

        $data = $this->getData($params['code']);

        return $this->respondWithToken(Auth::guard('api')->attempt([
            'phone'    => $params['phone'],
            'password' => $data['openid'],
        ]));
    }

    protected function respondWithToken($token)
    {
        return [
            'access_token' => 'Bearer ' . $token,
            'expires_in'   => Auth::guard('api')->factory()->getTTL() * 60,
        ];
    }

    public function logout()
    {
        Auth::guard('api')->logout();

        return $this->success([], '退出成功');
    }
}
