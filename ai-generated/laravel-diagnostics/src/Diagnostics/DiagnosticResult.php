<?php

declare(strict_types=1);

namespace Ziadoz\LaravelDiagnostics\Diagnostics;

final class DiagnosticResult
{
    public function __construct(
        public readonly DiagnosticStatus $status,
        public readonly string $message,
        public readonly string $name = '',
        public readonly string $label = '',
        public readonly ?string $rawOutput = null,
        public readonly ?float $durationMs = null,
        public readonly array $context = [],
    ) {}

    public static function ok(
        string $message,
        ?string $rawOutput = null,
        ?float $durationMs = null,
        array $context = [],
    ): self {
        return new self(DiagnosticStatus::Ok, $message, rawOutput: $rawOutput, durationMs: $durationMs, context: $context);
    }

    public static function failed(
        string $message,
        ?string $rawOutput = null,
        ?float $durationMs = null,
        array $context = [],
    ): self {
        return new self(DiagnosticStatus::Failed, $message, rawOutput: $rawOutput, durationMs: $durationMs, context: $context);
    }

    public static function skipped(
        string $message,
        ?string $rawOutput = null,
        ?float $durationMs = null,
        array $context = [],
    ): self {
        return new self(DiagnosticStatus::Skipped, $message, rawOutput: $rawOutput, durationMs: $durationMs, context: $context);
    }

    public function forDiagnostic(string $name, string $label): self
    {
        return new self(
            status: $this->status,
            message: $this->message,
            name: $name,
            label: $label,
            rawOutput: $this->rawOutput,
            durationMs: $this->durationMs,
            context: $this->context,
        );
    }
}
