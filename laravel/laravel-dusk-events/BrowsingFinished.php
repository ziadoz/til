<?php

use Laravel\Dusk\Browser;

class BrowsingFinished
{
    /**
     * @param array $browsers array<\Laravel\Dusk\Browse>
     */
    public function __construct(public array $browsers, public string $caller)
    {
    }
}
