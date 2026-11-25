<?php

namespace Tests\Feature;

use App\Enums\Role;
use App\Models\User;
use App\Services\PortalNavigationService;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Route;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class PortalWorkspaceTest extends TestCase
{
    use DatabaseTransactions;

    public static function roles(): array
    {
        return [
            'employee' => [Role::User, 'Staff'],
            'approver' => [Role::Superuser, 'Finance Manager'],
            'hr' => [Role::Admin, 'HRD'],
            'finance' => [Role::Admin, 'Finance'],
            'system administrator' => [Role::Superadmin, 'IT'],
        ];
    }

    #[DataProvider('roles')]
    public function test_every_visible_menu_renders_inside_the_portal(Role $role, string $position): void
    {
        $user = User::factory()->create(['level' => $role, 'jabatan' => $position]);
        $this->actingAs($user);
        $panel = Filament::getPanel('portal');
        Filament::setCurrentPanel($panel);
        $navigation = app(PortalNavigationService::class);
        $links = $navigation->links($navigation->groups($panel));
        $this->assertNotEmpty($links);

        foreach ($links as $item) {
            $this->assertStringContainsString('/portal/workspace/', $item['url'], $item['label']);
            $this->get($item['url'])->assertOk()->assertSee('fi-panel-portal', false)
                ->assertSee('portal-workspace.js', false);
        }

        foreach ($panel->getResources() as $resource) {
            Filament::setCurrentPanel($panel);
            if ($resource::canCreate() && isset($resource::getPages()['create'])) {
                $this->get($resource::getUrl('create', panel: 'portal'))->assertOk()->assertSee('fi-panel-portal', false);
            }
        }
    }

    public function test_all_admin_resource_routes_have_a_portal_equivalent(): void
    {
        foreach (Route::getRoutes() as $route) {
            if (str_starts_with($route->getName() ?? '', 'filament.admin.resources.')) {
                $this->assertTrue(Route::has(str_replace('filament.admin.', 'filament.portal.', $route->getName())), $route->getName());
            }
        }
    }

    public function test_portal_form_validation_create_edit_and_table_search_work(): void
    {
        $this->actingAs(User::factory()->create(['level' => Role::Superadmin]));
        Filament::setCurrentPanel(Filament::getPanel('portal'));
        Filament::bootCurrentPanel();
        $name = 'Portal QA '.uniqid();

        \Livewire\Livewire::test(\App\Filament\Resources\Departments\Pages\CreateDepartment::class)
            ->fillForm(['nama_department' => ''])
            ->call('create')->assertHasFormErrors(['nama_department' => 'required'])
            ->fillForm(['nama_department' => $name])
            ->call('create')->assertHasNoFormErrors()
            ->assertRedirect('/portal/workspace/departments');

        $record = \App\Models\Department::where('nama_department', $name)->firstOrFail();
        $this->get('/portal/workspace/departments/'.$record->id.'/edit')
            ->assertOk()->assertSee('fi-panel-portal', false);
        \Livewire\Livewire::test(\App\Filament\Resources\Departments\Pages\EditDepartment::class, ['record' => $record->id])
            ->fillForm(['nama_department' => $name.' Updated'])
            ->call('save')->assertHasNoFormErrors()
            ->assertRedirect('/portal/workspace/departments');
        $this->assertSame($name.' Updated', $record->fresh()->nama_department);

        \Livewire\Livewire::test(\App\Filament\Resources\Departments\Pages\ListDepartments::class)
            ->searchTable($name)->assertCanSeeTableRecords([$record])
            ->searchTable('no-matching-department-'.uniqid())->assertCanNotSeeTableRecords([$record]);
    }

    public function test_guests_and_unauthorized_staff_cannot_access_workspace(): void
    {
        $this->get('/portal/workspace/users')->assertRedirect('/portal/workspace/login');
        $this->get('/portal/workspace/login')->assertRedirect('/portal/login');
        $this->actingAs(User::factory()->create(['level' => Role::User]));
        $this->get('/portal/workspace/users')->assertForbidden();
        $this->get('/portal/workspace/assets')->assertForbidden();
        $this->get('/portal/workspace/departments/create')->assertForbidden();
    }

    public function test_original_admin_theme_and_portal_dashboard_are_preserved(): void
    {
        $this->actingAs(User::factory()->create(['level' => Role::Superadmin]));
        $this->get('/admin/users')->assertOk()->assertSee('fi-panel-admin', false)->assertDontSee('portal-workspace.js', false);
        $this->get('/portal/workspace')->assertRedirect('/portal');
        $this->get('/portal')->assertOk()->assertSee('/portal/workspace/users', false);
    }
}
