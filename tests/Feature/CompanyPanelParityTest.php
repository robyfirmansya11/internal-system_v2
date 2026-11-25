<?php

namespace Tests\Feature;

use App\Enums\Role;
use App\Filament\Resources\Companies\Pages\ListCompanies;
use App\Models\Company;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Livewire\Livewire;
use Tests\TestCase;

class CompanyPanelParityTest extends TestCase
{
    use DatabaseTransactions;

    public function test_company_records_match_between_panels_with_identical_filters(): void
    {
        $this->actingAs(User::factory()->create(['level' => Role::Superadmin]));
        $prefix = 'Parity '.uniqid();
        $active = Company::create(['nama' => $prefix.' Active', 'kode' => 'PA']);
        $trashed = Company::create(['nama' => $prefix.' Deleted', 'kode' => 'PD']);
        $trashed->delete();
        $results = [];

        foreach (['admin', 'portal'] as $panelId) {
            Filament::setCurrentPanel(Filament::getPanel($panelId));
            app()->forgetInstance(\Filament\Navigation\NavigationManager::class);
            $component = Livewire::test(ListCompanies::class)->searchTable($prefix);
            $component->assertCanSeeTableRecords([$active])->assertCanNotSeeTableRecords([$trashed]);
            $results[$panelId]['active'] = $component->instance()->getFilteredTableQuery()->orderBy('id')->pluck('id')->all();
            $component->filterTable('trashed', false)
                ->assertCanSeeTableRecords([$trashed])->assertCanNotSeeTableRecords([$active]);
            $results[$panelId]['trash'] = $component->instance()->getFilteredTableQuery()->orderBy('id')->pluck('id')->all();
            $component->filterTable('trashed', true)->assertCanSeeTableRecords([$active, $trashed]);
            $results[$panelId]['all'] = $component->instance()->getFilteredTableQuery()->orderBy('id')->pluck('id')->all();
        }

        $this->assertSame($results['admin'], $results['portal']);
        $this->assertSame([$active->id], $results['portal']['active']);
        $this->assertSame([$trashed->id], $results['portal']['trash']);
    }
}
