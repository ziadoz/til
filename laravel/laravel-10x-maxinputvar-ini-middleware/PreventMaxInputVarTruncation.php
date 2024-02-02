<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class PreventMaxInputVarTruncation
{
    protected ?int $inputVars;

    public function handle(Request $request, Closure $next): Response
    {
        if (
            $this->isWriting($request) &&
            $this->inputVars($request) <= $this->maxInputVars()
        ) {
            return $next($request);
        }

        throw new HttpException(413, sprintf(
            'Request containing %d input vars exceeds maximum %d',
            $this->inputVars($request),
            $this->maxInputVars(),
        ));
    }

    protected function isWriting(Request $request): bool
    {
        return in_array($request->method(), ['POST', 'PUT', 'PATCH', 'DELETE']);
    }

    protected function inputVars(Request $request): int
    {
        return $this->inputVars ??= $request->collect()->flatten()->count();
    }

    protected function maxInputVars(): int
    {
        return ini_get('max_input_vars') ?: 1_000;
    }
}
