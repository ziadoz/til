<?php

use Closure;
use Laravel\Dusk\Concerns\ProvidesBrowser;
use Laravel\Dusk\Browser;
use Throwable;

trait ProvidesEvents
{
    use ProvidesBrowser {
        ProvidesBrowser::browse as duskBrowse;
    }

    /**
     * Create a new browser instance and fire events.
     *
     * @param Closure $callback
     * @return void
     */
    public function browse(Closure $callback)
    {
        $this->duskBrowse(function (Browser ...$browsers) use ($callback) {
            try {
                event(new BrowsingStarted($browsers, $this->getCaller()));
                $callback($browsers);
            } catch (Throwable $exception) {
                event(new BrowsingFailed($browsers, $this->getCaller(), $exception));
                throw $exception;
            } finally {
                event(new BrowsingFinished($browsers, $this->getCaller()));
            }
        });
    }
}
