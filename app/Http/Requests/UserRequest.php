<?php

namespace App\Http\Requests;

use Illuminate\Http\Request;

class UserRequest extends Requests
{
    public function rules(Request $request)
    {
        switch ($request->method()){
            case 'POST':
                return [
                    'phone'    => 'required',
                    'password' => 'required|string|between:6,18',
                ];
        }
    }

    public function messages()
    {
        return [
            'phone.required'    => '手机号不能为空',
            'password.required' => '密码不能为空',
            'password.between'  => '密码长度在6~18位之间',
        ];
    }
}
