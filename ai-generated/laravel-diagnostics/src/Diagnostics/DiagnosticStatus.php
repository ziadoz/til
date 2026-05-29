<?php

declare(strict_types=1);

namespace Ziadoz\LaravelDiagnostics\Diagnostics;

enum DiagnosticStatus: string
{
    case Ok = 'ok';
    case Failed = 'failed';
    case Skipped = 'skipped';

    public function label(): string
    {
        return match ($this) {
            self::Ok => 'OK',
            self::Failed => 'FAILED',
            self::Skipped => 'SKIPPED',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Ok => 'green',
            self::Failed => 'red',
            self::Skipped => 'yellow',
        };
    }

    public function symbol(): string
    {
        return match ($this) {
            self::Ok => '✓',
            self::Failed => '✗',
            self::Skipped => '~',
        };
    }
}
