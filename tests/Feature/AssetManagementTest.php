<?php

namespace Tests\Feature;

use App\Enums\Role;
use App\Filament\Resources\Assets\AssetResource;
use App\Filament\Resources\Assets\Pages\CreateAsset;
use App\Filament\Resources\Assets\Pages\EditAsset;
use App\Filament\Resources\Assets\Pages\ListAssets;
use App\Models\Asset;
use App\Models\Company;
use App\Models\Department;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Livewire\Livewire;
use Tests\TestCase;

class AssetManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Filament::setCurrentPanel(Filament::getPanel('admin'));
    }

    private function assetData(): array
    {
        return ['asset_code' => 'IT-001', 'name' => 'Office Laptop', 'category' => 'Laptop',
            'company_id' => Company::create(['nama' => 'Asset Test', 'kode' => 'AST'])->id,
            'status' => 'Available', 'condition' => 'Good', 'purchase_cost' => '12500000.50'];
    }

    public function test_allowed_roles_and_departments_can_open_the_menu(): void
    {
        $users = [User::factory()->create(['level' => Role::Superadmin, 'jabatan' => 'IT']),
            User::factory()->create(['level' => Role::Admin, 'jabatan' => 'HRD'])];
        foreach (config('assets.management_departments') as $name) {
            $user = User::factory()->create(['level' => Role::User, 'jabatan' => 'Staff']);
            $user->departments()->attach(Department::create(['nama_department' => $name]));
            $users[] = $user;
        }
        foreach ($users as $user) {
            $this->actingAs($user);
            $this->assertTrue(AssetResource::canViewAny());
            $this->assertNotEmpty(AssetResource::getNavigationItems());
            $this->get('/admin/assets')->assertOk();
            $this->get('/admin/assets/create')->assertOk();
        }
    }

    public function test_other_users_cannot_open_pages_or_mutate_assets(): void
    {
        $asset = Asset::create($this->assetData());
        foreach ([Role::User, Role::Superuser, Role::Admin] as $role) {
            $user = User::factory()->create(['level' => $role, 'jabatan' => 'Staff']);
            $user->departments()->attach(Department::firstOrCreate(['nama_department' => 'Finance, Accounting & Tax Department']));
            $this->actingAs($user);
            $this->assertFalse(AssetResource::canViewAny());
            foreach (['view', 'update', 'delete', 'restore', 'forceDelete'] as $ability) {
                $this->assertFalse(Gate::allows($ability, $asset));
            }
            $this->assertFalse(Gate::allows('create', Asset::class));
            $this->get('/admin/assets')->assertForbidden();
            $this->get('/admin/assets/create')->assertForbidden();
            $this->get('/admin/assets/'.$asset->id.'/edit')->assertForbidden();
        }
        auth()->logout();
        $this->get('/admin/assets')->assertRedirect('/admin/login');
    }

    public function test_department_match_is_exact_and_revocation_applies_immediately(): void
    {
        $user = User::factory()->create(['level' => Role::User]);
        $department = Department::create(['nama_department' => 'IT/GA']);
        $user->departments()->attach($department);
        $user->load('departments');
        $this->assertTrue($user->canManageAssets());
        $user->departments()->detach();
        $this->assertFalse($user->canManageAssets());
        $user->departments()->attach(Department::create(['nama_department' => 'IT/GA External']));
        $this->assertFalse($user->canManageAssets());
        $user->departments()->attach($department);
        $department->delete();
        $this->assertFalse($user->canManageAssets());
    }

    public function test_authorized_user_can_create_edit_archive_and_restore_an_asset(): void
    {
        $user = User::factory()->create(['level' => Role::Admin, 'jabatan' => 'HRD']);
        $this->actingAs($user);
        $data = $this->assetData();
        Livewire::test(CreateAsset::class)->fillForm($data)->call('create')->assertHasNoFormErrors();
        $asset = Asset::where('asset_code', 'IT-001')->firstOrFail();
        $this->assertSame('12500000.50', $asset->purchase_cost);
        Livewire::test(EditAsset::class, ['record' => $asset->id])
            ->fillForm(['location' => 'Head Office', 'assigned_to' => $user->id, 'status' => 'In Use'])
            ->call('save')->assertHasNoFormErrors();
        $this->assertSame($user->id, $asset->fresh()->assigned_to);
        Livewire::test(ListAssets::class)->callTableAction('delete', $asset);
        $this->assertSoftDeleted('assets', ['id' => $asset->id]);
        Livewire::test(ListAssets::class)->filterTable('trashed', false)->callTableAction('restore', $asset);
        $this->assertNotSoftDeleted('assets', ['id' => $asset->id]);
        $this->assertFalse(Gate::allows('forceDelete', $asset));
    }

    public function test_invalid_and_duplicate_inventory_data_is_rejected(): void
    {
        $data = $this->assetData();
        Asset::create($data)->delete();
        $this->actingAs(User::factory()->create(['level' => Role::Admin, 'jabatan' => 'HRD']));
        Livewire::test(CreateAsset::class)->fillForm($data)->call('create')->assertHasFormErrors(['asset_code' => 'unique']);
        Livewire::test(CreateAsset::class)->fillForm(array_merge($data, ['asset_code' => 'IT-002', 'purchase_cost' => -1]))
            ->call('create')->assertHasFormErrors(['purchase_cost']);
        Livewire::test(CreateAsset::class)->fillForm(array_merge($data, ['asset_code' => 'IT-003', 'status' => 'Unknown']))
            ->call('create')->assertHasFormErrors(['status']);
        Company::findOrFail($data['company_id'])->delete();
        Livewire::test(CreateAsset::class)->fillForm(array_merge($data, ['asset_code' => 'IT-004']))
            ->call('create')->assertHasFormErrors(['company_id']);
    }
}
