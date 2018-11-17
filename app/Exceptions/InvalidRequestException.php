<?php

namespace App\Exceptions;

use Exception;
use Throwable;
use Illuminate\HTTP\Request;

class InvalidRequestException extends Exception
{
    public function __construct(string $message = "", int $code = 400, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    public function render(Request $request)
    {
        if ($request->expectsJson()) {
            return response()->json(['msg' => $this->message], $this->code);
        }
        return view('pages.error', ['msg' => $this->message]);
    }
}
