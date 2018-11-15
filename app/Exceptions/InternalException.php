<?php

namespace App\Exceptions;

use App\Http\Controllers\Controller;
use Exception;

class InternalException extends Exception
{

    public function __construct(string $message, int $code = 500)
    {
        parent::__construct($message, $code);
    }

    public function render()
    {
        $con =  new Controller();
        return $con->fail($this->message, $this->code);
    }
}
