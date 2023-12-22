<?php

use Laravel\Dusk\Browser;
use Exception;

class BrowsingFailed
{
    /**
     * @param array $browsers array<\Laravel\Dusk\Browse>
     */
    public function __construct(public array $browsers, public string $caller, public Exception $exception)
    {
    }
}
