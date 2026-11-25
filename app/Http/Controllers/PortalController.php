<?php

namespace App\Http\Controllers;

use App\Services\ApprovalDashboardService;
use Filament\Facades\Filament;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

class PortalController extends Controller
{
    public function __invoke(Request $request, \App\Services\PortalNavigationService $navigation)
    {
        if (! $request->user()) {
            return redirect()->guest(route('portal.login'));
        }

        $panel = Filament::getPanel('portal');
        abort_unless($request->user()->canAccessPanel($panel), 403);
        Filament::setCurrentPanel($panel);
        $navigationGroups = $navigation->groups($panel);

        $modules = collect(ApprovalDashboardService::modules())
            ->map(fn ($module) => [...$module, 'route' => str_replace('filament.admin.', 'filament.portal.', $module['route'])])
            ->filter(function ($module) use ($panel) {
            $resource = collect($panel->getResources())->first(fn ($resource) => $resource::getModel() === $module['model']);

            return $resource && $resource::canViewAny() && Route::has($module['route']);
        })->map(fn ($module) => [...$module, 'url' => route($module['route'])]);

        $totals = collect();
        $recent = collect();
        foreach ($modules as $module) {
            // Personal dashboard: never aggregate another employee's requests.
            $query = $module['model']::query()->where('user_id', $request->user()->id);
            foreach ((clone $query)->selectRaw('status, count(*) as total')->groupBy('status')->pluck('total', 'status') as $status => $count) {
                $totals[$status] = ($totals[$status] ?? 0) + $count;
            }
            foreach ($query->latest('updated_at')->limit(8)->get() as $record) {
                $recent->push([
                    'label' => $module['label'], 'title' => ($module['title'])($record),
                    'status' => $record->status, 'updated_at' => $record->updated_at, 'url' => $module['url'],
                ]);
            }
        }

        return view('portal.dashboard', [
            'user' => $request->user(), 'modules' => $modules,
            'navigationGroups' => $navigationGroups,
            'navigationLinks' => $navigation->links($navigationGroups),
            'recent' => $recent->sortByDesc('updated_at')->take(8),
            'stats' => [
                'Total pengajuan' => $totals->sum(),
                'Menunggu persetujuan' => $totals['Submitted'] ?? 0,
                'Disetujui' => $totals['Approved'] ?? 0,
                'Ditolak' => $totals['Rejected'] ?? 0,
            ],
        ]);
    }
}
