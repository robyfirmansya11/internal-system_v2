<?php

namespace Tests\Feature;

use App\Enums\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class CreateAdministratorTest extends TestCase
{
    use DatabaseTransactions;

    public function test_command_creates_an_administrator_with_a_hashed_password(): void
    {
        $email = 'administrator-'.uniqid().'@example.test';
        $this->artisan('app:create-administrator')
            ->expectsQuestion('Administrator name', 'Test Administrator')
            ->expectsQuestion('Administrator email', $email)
            ->expectsQuestion('Password (at least 12 characters, letters, numbers and symbols)', 'Test-Only-Strong-123!')
            ->expectsQuestion('Confirm password', 'Test-Only-Strong-123!')
            ->assertSuccessful();
        $user = User::where('email', $email)->firstOrFail();
        $this->assertSame(Role::Superadmin, $user->level);
        $this->assertTrue(Hash::check('Test-Only-Strong-123!', $user->password));
    }
}
