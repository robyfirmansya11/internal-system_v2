<?php

namespace Tests\Feature;

use App\Enums\Role;
use App\Models\Company;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class LocaleTest extends TestCase
{
    use DatabaseTransactions;

    public static function languages(): array
    {
        return [['en', 'Welcome back', 'Companies', 'Name'], ['id', 'Selamat datang kembali', 'Perusahaan', 'Nama'], ['zh_CN', '欢迎回来', '公司', '名称']];
    }

    #[DataProvider('languages')]
    public function test_language_applies_to_auth_portal_and_company_pages(string $locale, string $welcome, string $companies, string $name): void
    {
        $this->post('/language', ['locale' => $locale, 'return_to' => '/portal/login'])
            ->assertRedirect('/portal/login')->assertSessionHas('locale', $locale)->assertCookie('locale', $locale);
        $this->get('/portal/login')->assertOk()->assertSee($welcome)->assertSee('lang="'.str_replace('_', '-', $locale).'"', false);
        $this->get('/portal/register')->assertOk()->assertSee(__('Konfirmasi kata sandi'));
        $this->post('/portal/register', [])->assertSessionHasErrors(['name', 'email', 'password']);
        $this->assertSame(__('validation.required', ['attribute' => $locale === 'en' ? 'name' : __('validation.attributes.name')]), session('errors')->first('name'));

        $user = User::factory()->create(['level' => Role::Superadmin]);
        $company = Company::create(['nama' => 'Original Company '.uniqid(), 'kode' => 'LANG']);
        $this->actingAs($user);
        $this->get('/portal')->assertOk()->assertSee($companies);
        foreach (['portal/workspace', 'admin'] as $path) {
            $this->get('/'.$path.'/companies')->assertOk()->assertSee($companies)->assertSee($name);
        }
        Filament::setCurrentPanel(Filament::getPanel('portal'));
        Livewire::test(\App\Filament\Resources\Companies\Pages\ListCompanies::class)
            ->searchTable($company->nama)->assertCanSeeTableRecords([$company]);
        $this->assertSame($company->nama, $company->fresh()->nama);
    }

    public function test_language_cookie_is_used_without_a_session_preference(): void
    {
        $this->withCookie('locale', 'zh_CN')->get('/portal/login')->assertOk()->assertSee('欢迎回来');
    }

    public function test_invalid_locale_is_rejected_and_redirects_cannot_leave_the_site(): void
    {
        $this->post('/language', ['locale' => '../../invalid'])->assertSessionHasErrors('locale');
        foreach (['https://example.com', '//example.com', '/\\example.com'] as $url) {
            $this->post('/language', ['locale' => 'id', 'return_to' => $url])->assertRedirect('/portal');
        }
        $this->post('/language', ['locale' => 'en', 'return_to' => '/portal/workspace/companies?search=Example'])->assertRedirect('/portal/workspace/companies?search=Example');
    }
}
