<?php

namespace Tests\Feature;

use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    public function test_admin_login(): void
    {
        $response = $this->post('/login', [
            'email' => 'admin@lentera.test',
            'password' => 'password',
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertAuthenticated();

        // Follow redirect to /dashboard
        $follow = $this->get('/dashboard');
        $follow->assertRedirect(route('admin.dashboard'));
    }

    public function test_juri_login(): void
    {
        $response = $this->post('/login', [
            'email' => 'juri1@lentera.test',
            'password' => 'password',
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertAuthenticated();

        // Follow redirect to /dashboard
        $follow = $this->get('/dashboard');
        $follow->assertRedirect(route('juri.dashboard'));
    }
}
