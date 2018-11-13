<?php

namespace App\Exceptions;

use Exception;

class InternalException extends Exception
{

    public function __construct(string $message, int $code = 500)
    {
        parent::__construct($message, $code);
    }

    public function render()
    {
        return response()->json(['msg' => $this->message], $this->code);
    }
}
