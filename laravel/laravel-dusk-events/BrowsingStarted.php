<?php

use Laravel\Dusk\Browser;

class BrowsingStarted
{
    /**
     * @param array $browsers array<\Laravel\Dusk\Browse>
     */
    public function __construct(public array $browsers, public string $caller)
    {
    }
}
