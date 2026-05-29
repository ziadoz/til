<?php

declare(strict_types=1);

namespace Ziadoz\LaravelDiagnostics\Diagnostics\Mail;

use Illuminate\Support\Facades\Mail;
use Symfony\Component\Mailer\Transport\Smtp\SmtpTransport;
use Throwable;
use Ziadoz\LaravelDiagnostics\Diagnostics\Diagnostic;
use Ziadoz\LaravelDiagnostics\Diagnostics\DiagnosticResult;

final class MailConnectDiagnostic extends Diagnostic
{
    public function name(): string
    {
        return 'mail.connect';
    }

    public function label(): string
    {
        return 'Mail Connection';
    }

    public function run(array $options = []): DiagnosticResult
    {
        $mailer = $options['mailer'] ?? config('mail.default');

        try {
            [$transport, $durationMs] = $this->benchmark(
                fn () => Mail::mailer($mailer)->getSymfonyTransport()
            );

            $driver = config("mail.mailers.{$mailer}.transport");
            $context = ['mailer' => $mailer, 'driver' => $driver];

            if ($transport instanceof SmtpTransport) {
                $transport->start();
                $context['host'] = config("mail.mailers.{$mailer}.host");
                $context['port'] = config("mail.mailers.{$mailer}.port");
            }

            return DiagnosticResult::ok(
                message: "Mail transport [{$mailer}] is reachable",
                durationMs: $durationMs,
                context: $context,
            );
        } catch (Throwable $e) {
            return DiagnosticResult::failed(
                message: "Mail transport [{$mailer}] failed: {$e->getMessage()}",
                rawOutput: $e->getMessage(),
                context: ['mailer' => $mailer],
            );
        }
    }
}
