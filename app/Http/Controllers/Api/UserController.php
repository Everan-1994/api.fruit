<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class UserController extends Controller
{
    /**
     * @param Request $request
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(Request $request)
    {
        $users = User::query()
            ->where('status', $request->input('status', 1))
            ->when($request->filled('remake'), function ($query) use ($request) {
                return $query->where('remake', 'like', '%' . $request->input('remake') . '%');
            })
            ->when($request->filled('phone'), function ($query) use ($request) {
                return $query->where('phone', preg_replace('# #','',$request->input('phone')));
            })
            ->where('affiliation_phone', $request->input('affiliation_phone', 3))
            ->whereIdentify($request->input('identify', 3))
            ->orderBy($request->input('order', 'created_at'), $request->input('sort', 'desc'))
            ->paginate($request->input('pageSize', 10), ['*'], 'page', $request->input('page', 1));

        return UserResource::collection($users);
    }
}
