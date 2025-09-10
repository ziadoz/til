<?php
// A rubbish AI-generated logger output class:

namespace App\Console\Output;

use Illuminate\Support\Facades\Log;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Formatter\OutputFormatter;
use Symfony\Component\Console\Formatter\OutputFormatterInterface;
use Symfony\Component\Console\Output\Output;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * LoggingOutput is a Symfony Console Output implementation that forwards all console
 * writes to Laravel's logger instead of STDOUT/STDERR.
 */
class LoggingOutput extends Output implements OutputInterface
{
    private LoggerInterface $logger;

    private bool $isDecorated = false;

    public function __construct(
        int $verbosity = self::VERBOSITY_NORMAL,
        bool $decorated = false,
        ?OutputFormatterInterface $formatter = null,
        ?LoggerInterface $logger = null,
    ) {
        parent::__construct($verbosity, $decorated, $formatter ?? new OutputFormatter());

        if ($logger !== null) {
            $this->logger = $logger;
        } elseif (class_exists(Log::class) && method_exists(Log::class, 'getLogger')) {
            $this->logger = Log::getLogger();
        } else {
            $this->logger = app(LoggerInterface::class);
        }
        $this->isDecorated = $decorated;
    }

    /**
     * Writes a message to the logs at the mapped log level.
     */
    protected function doWrite(string $message, bool $newline): void
    {
        $payload = $this->isDecorated ? $message : $this->stripAnsi($message);
        $lines = preg_split("/\r?\n/", $payload) ?: [$payload];

        foreach ($lines as $line) {
            if ($line === '' && !$newline) {
                continue;
            }

            $this->log($line);
        }
    }

    private function log(string $line): void
    {
        $level = $this->mapVerbosityToLevel();

        match ($level) {
            'debug' => $this->logger->debug($line),
            'info' => $this->logger->info($line),
            'notice' => $this->logger->notice($line),
            'warning' => $this->logger->warning($line),
            'error' => $this->logger->error($line),
            'critical' => $this->logger->critical($line),
            'alert' => $this->logger->alert($line),
            'emergency' => $this->logger->emergency($line),
            default => $this->logger->info($line),
        };
    }

    private function mapVerbosityToLevel(): string
    {
        return match ($this->getVerbosity()) {
            self::VERBOSITY_QUIET => 'notice',
            self::VERBOSITY_NORMAL => 'info',
            self::VERBOSITY_VERBOSE => 'notice',
            self::VERBOSITY_VERY_VERBOSE => 'debug',
            self::VERBOSITY_DEBUG => 'debug',
            default => 'info',
        };
    }

    private function stripAnsi(string $text): string
    {
        return preg_replace('/\e\[[;?0-9]*[a-zA-Z]/', '', $text) ?? $text;
    }
}