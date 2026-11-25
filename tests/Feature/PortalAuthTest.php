<?php

namespace Tests\Feature;

use App\Enums\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class PortalAuthTest extends TestCase
{
    use DatabaseTransactions;

    public function test_registration_can_be_disabled_for_internal_deployments(): void
    {
        config(['portal.registration_enabled' => false]);
        $this->get('/portal/register')->assertNotFound();
        $this->post('/portal/register', [])->assertNotFound();
        $this->get('/portal/login')->assertOk()->assertDontSee('href="'.route('portal.register').'"', false);
    }

    public function test_login_and_register_pages_render(): void
    {
        $this->get('/portal/login')->assertOk()->assertSee(__('Selamat datang kembali'));
        $this->get('/portal/register')->assertOk()->assertSee(__('Konfirmasi kata sandi'));
    }

    public function test_existing_account_can_login_and_logout(): void
    {
        $user = User::factory()->create(['level' => Role::User]);
        $this->withSession(['url.intended' => url('/portal')])->post('/portal/login', [
            'email' => $user->email, 'password' => 'password', 'remember' => '1',
        ])->assertRedirect('/portal');
        $this->assertAuthenticatedAs($user);
        $this->post('/portal/logout')->assertRedirect('/portal/login');
        $this->assertGuest();
        $this->get('/portal/logout')->assertStatus(405);
    }

    public function test_invalid_password_is_rejected(): void
    {
        $user = User::factory()->create(['level' => Role::User]);
        $this->post('/portal/login', ['email' => $user->email, 'password' => 'incorrect'])->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_registration_hashes_password_and_ignores_privileged_fields(): void
    {
        $email = 'portal-'.uniqid().'@example.com';
        $this->post('/portal/register', [
            'name' => 'New Employee', 'email' => $email, 'password' => 'StrongPass123',
            'password_confirmation' => 'StrongPass123', 'level' => 'Superadmin', 'jabatan' => 'HRD',
        ])->assertRedirect('/portal');
        $user = User::where('email', $email)->firstOrFail();
        $this->assertSame(Role::User, $user->level);
        $this->assertNull($user->jabatan);
        $this->assertTrue(Hash::check('StrongPass123', $user->password));
        $this->assertAuthenticatedAs($user);
    }

    public function test_registration_validates_duplicate_email_and_password_confirmation(): void
    {
        $user = User::factory()->create(['level' => Role::User]);
        $this->post('/portal/register', [
            'name' => 'New Employee', 'email' => $user->email,
            'password' => 'short', 'password_confirmation' => 'different',
        ])->assertSessionHasErrors(['email', 'password']);
        $this->assertGuest();
    }

    public function test_login_attempts_are_rate_limited(): void
    {
        for ($attempt = 0; $attempt < 5; $attempt++) {
            $this->post('/portal/login', [])->assertSessionHasErrors('email');
        }
        $this->post('/portal/login', [])->assertStatus(429);
    }

    public function test_authenticated_users_are_redirected_from_auth_pages(): void
    {
        $user = User::factory()->create(['level' => Role::User]);
        $this->actingAs($user)->get('/portal/login')->assertRedirect('/portal');
        $this->get('/portal/register')->assertRedirect('/portal');
    }
}
