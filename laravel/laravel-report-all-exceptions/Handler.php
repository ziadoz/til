<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;

class Handler extends ExceptionHandler
{
    public function register(): void
    {
        // Stop ignoring all internally ignored exceptions.
        foreach ($this->internalDontReport as $e) {
            $this->stopIgnoring($e);
        }
    }
}
