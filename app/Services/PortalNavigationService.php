<?php

namespace App\Services;

use Filament\Navigation\NavigationItem;
use Filament\Panel;
use Illuminate\Support\Collection;

class PortalNavigationService
{
    public function groups(Panel $panel): Collection
    {
        \Filament\Facades\Filament::setCurrentPanel($panel);
        // A previous panel may already have mounted navigation in this request.
        // Rebuild it for the requested panel rather than retaining its URLs.
        app()->forgetInstance(\Filament\Navigation\NavigationManager::class);
        // Use Filament's own navigation builder so policies, visibility, labels,
        // grouping, ordering and custom pages stay in sync without a second registry.
        return collect($panel->getNavigation())->map(fn ($group) => [
            'label' => $group->getLabel() ?: 'Workspace',
            'items' => $this->items($group->getItems()),
        ])->filter(fn ($group) => $group['items']->isNotEmpty())->values();
    }

    private function items(iterable $items): Collection
    {
        return collect($items)->filter(fn (NavigationItem $item) => $item->isVisible())
            ->map(function (NavigationItem $item) {
                $dashboard = in_array($item->getUrl(), [route('filament.admin.pages.dashboard'), route('portal.dashboard')], true);

                return [
                    'label' => $item->getLabel(),
                    'icon' => $item->getIcon() ?? 'heroicon-o-squares-2x2',
                    'url' => $dashboard ? route('portal.dashboard') : $item->getUrl(),
                    'active' => $dashboard,
                    'new_tab' => $item->shouldOpenUrlInNewTab(),
                    'badge' => $item->getBadge(),
                    'children' => $this->items($item->getChildItems()),
                ];
            })->values();
    }

    public function links(Collection $groups): Collection
    {
        $flatten = function (Collection $items, string $group) use (&$flatten): Collection {
            return $items->flatMap(fn ($item) => collect($item['url'] && ! $item['active'] ? [[...$item, 'group' => $group]] : [])
                ->concat($flatten($item['children'], $group)));
        };

        return $groups->flatMap(fn ($group) => $flatten($group['items'], $group['label']))->values();
    }
}
