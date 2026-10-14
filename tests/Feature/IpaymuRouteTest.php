<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class IpaymuRouteTest extends TestCase
{
    public function test_ipaymu_webhook_route_registered(): void
    {
        $this->assertTrue(Route::has('webhook.ipaymu'));
        $this->assertFalse(Route::has('webhook.cashi'));

        $route = collect(Route::getRoutes())->firstWhere('action.as', 'webhook.ipaymu');
        $this->assertContains('POST', $route->methods);
        $this->assertSame('api/webhook/ipaymu', $route->uri);
    }
}
