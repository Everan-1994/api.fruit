<?php

namespace App\Http\Requests;


class WeappAuthorizationRequest extends Requests
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'code'      => 'required|string',
            'nickName'  => 'required|string',
            'gender'    => 'required|integer|between:0,1',
            'avatarUrl' => 'required|string',
            'phone'     => 'sometimes|unique:users,phone',
        ];
    }
}
