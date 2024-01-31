<?php

namespace Tests;

use App\Controllers\MyController;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Tests\TestCase;

class MyTest extends TestCase
{
    public function test_thing(): void
    {
        // Make response slower deliberately for testing.
        $this->app->bind(MyController::class, function () {
            return new class extends MyController
            {
                public function index(Request $request): Response
                {
                    sleep(5);
                    return parent::index($request);
                }
            };
        });

        // Test the controller...
    }
}