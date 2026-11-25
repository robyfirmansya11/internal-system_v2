<?php

namespace Tests\Feature;

use App\Enums\Role;
use App\Models\User;
use Tests\TestCase;

class PortalTest extends TestCase
{
    public function test_guest_uses_existing_login_and_remembers_portal(): void
    {
        $this->get('/portal')->assertRedirect(route('portal.login'));
        $this->assertEquals(url('/portal'), session('url.intended'));
    }

    public function test_account_without_panel_access_is_denied(): void
    {
        $user = new User(['name' => 'No access', 'level' => null]);
        $this->actingAs($user)->get('/portal')->assertForbidden();
    }

    public function test_dashboard_view_supports_empty_and_populated_states(): void
    {
        $user = new User(['name' => 'Portal Employee', 'level' => Role::User]);
        $data = ['user' => $user, 'modules' => collect(), 'navigationGroups' => collect(), 'navigationLinks' => collect(), 'recent' => collect(), 'stats' => ['Total pengajuan' => 0]];
        $this->view('portal.dashboard', $data)->assertSee(__('Belum ada pengajuan'))->assertSee('Portal Employee');
        $data['recent'] = collect([['label' => 'Leave Request', 'title' => '<script>alert(1)</script>', 'status' => 'Approved', 'updated_at' => now(), 'url' => '/admin']]);
        $this->view('portal.dashboard', $data)->assertSee('Approved')->assertSee('&lt;script&gt;', false)->assertDontSee('<script>alert(1)</script>', false);
    }
}
