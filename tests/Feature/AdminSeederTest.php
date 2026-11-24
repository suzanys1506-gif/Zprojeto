<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_only_seeds_an_admin_when_credentials_are_configured(): void
    {
        config([
            'library.admin.email' => null,
            'library.admin.password' => null,
        ]);

        $this->seed();

        $this->assertDatabaseMissing('users', ['email' => 'admin@entrelinhas.com']);

        config([
            'library.admin.email' => 'admin@example.test',
            'library.admin.password' => 'a-unique-strong-password',
        ]);

        $this->seed();

        $admin = User::where('email', 'admin@example.test')->firstOrFail();
        $this->assertSame('admin', $admin->role);
        $this->assertTrue(Hash::check('a-unique-strong-password', $admin->password));
    }

    public function test_it_rejects_partially_configured_admin_credentials(): void
    {
        config([
            'library.admin.email' => 'admin@example.test',
            'library.admin.password' => null,
        ]);

        $this->expectException(\InvalidArgumentException::class);

        $this->seed();
    }
}
