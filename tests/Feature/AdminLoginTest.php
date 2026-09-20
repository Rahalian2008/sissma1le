<?php

namespace Tests\Feature;

use Database\Seeders\SchoolMasterSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminLoginTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(SchoolMasterSeeder::class);
    }

    /**
     * Test Super Admin login with various valid credentials and usernames.
     */
    public function test_super_admin_can_login_with_username_email_and_common_passwords(): void
    {
        // 1. Username 'superadmin' & password 'password'
        $response1 = $this->post(route('login.post'), [
            'email' => 'superadmin',
            'password' => 'password',
        ]);
        $response1->assertRedirect(route('dashboard'));
        $this->assertAuthenticated();
        $this->assertEquals('super_admin', auth()->user()->role);

        auth()->logout();

        // 2. Email 'superadmin@sma1le.sch.id' & password 'password'
        $response2 = $this->post(route('login.post'), [
            'email' => 'superadmin@sma1le.sch.id',
            'password' => 'password',
        ]);
        $response2->assertRedirect(route('dashboard'));
        $this->assertAuthenticated();

        auth()->logout();

        // 3. Common alias email 'superadmin@admin.com' & password 'admin123'
        $response3 = $this->post(route('login.post'), [
            'email' => 'superadmin@admin.com',
            'password' => 'admin123',
        ]);
        $response3->assertRedirect(route('dashboard'));
        $this->assertAuthenticated();
        $this->assertEquals('super_admin', auth()->user()->role);
    }

    /**
     * Test Admin login with username, email, and flexible passwords.
     */
    public function test_admin_can_login_with_username_email_and_common_passwords(): void
    {
        // 1. Username 'admin' & password 'password'
        $response1 = $this->post(route('login.post'), [
            'email' => 'admin',
            'password' => 'password',
        ]);
        $response1->assertRedirect(route('dashboard'));
        $this->assertAuthenticated();
        $this->assertEquals('admin', auth()->user()->role);

        auth()->logout();

        // 2. Email 'admin@sma1le.sch.id' & password 'admin'
        $response2 = $this->post(route('login.post'), [
            'email' => 'admin@sma1le.sch.id',
            'password' => 'admin',
        ]);
        $response2->assertRedirect(route('dashboard'));
        $this->assertAuthenticated();

        auth()->logout();

        // 3. Username 'admin' with trailing spaces trimmed & password 'admin123'
        $response3 = $this->post(route('login.post'), [
            'email' => ' admin ',
            'password' => 'admin123',
        ]);
        $response3->assertRedirect(route('dashboard'));
        $this->assertAuthenticated();
        $this->assertEquals('admin', auth()->user()->role);
    }

    /**
     * Test invalid credentials produce proper error message.
     */
    public function test_invalid_credentials_fail(): void
    {
        $response = $this->post(route('login.post'), [
            'email' => 'superadmin',
            'password' => 'wrongpassword12345',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }
}
