<?php

namespace Tests;


use Database\Seeders\DeliveryTypeSeeder;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Mail;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication, RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();

        Mail::fake();

        app()->make(RolePermissionSeeder::class)->run();
        app()->make(DeliveryTypeSeeder::class)->run();
    }
}
