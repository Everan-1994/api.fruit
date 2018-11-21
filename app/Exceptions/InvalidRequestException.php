<?php

namespace App\Exceptions;

use App\Http\Controllers\Controller;
use Exception;

class InvalidRequestException extends Exception
{
    public function __construct(string $message = "", int $code = 400)
    {
        parent::__construct($message, $code);
    }

    public function render()
    {
        $con =  new Controller();
        return $con->fail($this->message, $this->code);
    }
}
