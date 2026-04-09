<?php

namespace Tests;

use Laravel\Dusk\Browser;
trait FakesJavaScriptFetch
{
    // Usage: $browser->script($this->fakeFetchResponse(['foo' => 'bar']));
    public function fakeFetchResponse(array $data): string
    {
        $json = json_encode($data);

        return <<<SCRIPT
            window.fetch = new Proxy(window.fetch, {
                apply(target, that, args) {
                    return Promise.resolve(
                        new Response(
                            '$json',
                            { status: 200, headers: { 'Content-Type': 'application/json' }},
                        )
                    );
                },
            });
            SCRIPT;
    }
}
