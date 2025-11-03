<?php

namespace Tests;

use Spatie\Permission\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use  RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // S'assurer que le rôle 'patient' existe dans la base de test
        Role::firstOrCreate(['name' => 'patient', 'guard_name' => 'web']);
    }
}
