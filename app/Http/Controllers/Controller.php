<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function success(array $data = [], string $message = 'success', $code = 200)
    {
        return response()->json(array_filter([
            'data'    => $data,
            'message' => $message,
        ]), $code);
    }

    public function fail(string $message = 'success', $code = 400)
    {
        return response()->json([
            'message' => $message,
        ], $code);
    }
}
