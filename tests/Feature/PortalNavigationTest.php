<?php

namespace Tests\Feature;

use App\Enums\Role;
use App\Models\User;
use App\Services\PortalNavigationService;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class PortalNavigationTest extends TestCase
{
    use DatabaseTransactions;

    public function test_staff_sees_all_permitted_menu_groups_but_not_administration(): void
    {
        $user = User::factory()->create(['level' => Role::User]);
        $this->actingAs($user);
        $panel = Filament::getPanel('admin');
        Filament::setCurrentPanel($panel);
        $service = app(PortalNavigationService::class);
        $groups = $service->groups($panel);
        $links = $service->links($groups);
        $labels = $links->pluck('label')->all();

        $this->assertContains('My Profile', $labels);
        $this->assertContains('Meal Claims', $labels);
        $this->assertNotContains('Departments', $labels);
        $this->assertNotContains('Activity Logs', $labels);
        $this->assertNotContains('Asset Management', $labels);
        $dashboard = $groups->flatMap(fn ($group) => $group['items'])->firstWhere('active', true);
        $this->assertSame(route('portal.dashboard'), $dashboard['url']);

        $this->get('/portal')->assertOk()->assertSee(__('Semua menu'))->assertSee('My Profile')->assertDontSee('Activity Logs');
    }

    public function test_superadmin_navigation_includes_reports_master_data_and_assets(): void
    {
        $user = User::factory()->create(['level' => Role::Superadmin]);
        $this->actingAs($user);
        $panel = Filament::getPanel('admin');
        Filament::setCurrentPanel($panel);
        $service = app(PortalNavigationService::class);
        $groups = $service->groups($panel);
        $labels = $service->links($groups)->pluck('label')->all();

        foreach (['Departments', 'Activity Logs', 'Asset Management', 'My Profile', 'Meal Claims'] as $label) {
            $this->assertContains($label, $labels);
        }
        foreach (['HRIS', 'Master Data', 'Report', 'Account', 'IT & General Affairs'] as $group) {
            $this->assertContains(__($group), $groups->pluck('label')->all());
        }
        $this->get('/portal')->assertOk()->assertSee('Asset Management')->assertSee('Activity Logs');
    }
}
