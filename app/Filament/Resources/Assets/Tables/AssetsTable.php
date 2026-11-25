<?php

namespace App\Filament\Resources\Assets\Tables;

use App\Models\Asset;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\RestoreAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class AssetsTable
{
    public static function configure(Table $table): Table
    {
        return $table->defaultSort('created_at', 'desc')->columns([
            TextColumn::make('asset_code')->label('Asset Code')->searchable()->sortable(),
            TextColumn::make('name')->label('Asset Name')->searchable()->sortable(),
            TextColumn::make('category')->searchable()->sortable(),
            TextColumn::make('serial_number')->label('Serial Number')->searchable()->toggleable(isToggledHiddenByDefault: true),
            TextColumn::make('company.nama')->label('Company')->searchable()->sortable(),
            TextColumn::make('location')->searchable(),
            TextColumn::make('assignee.name')->label('Assigned To')->placeholder(__('Unassigned'))->searchable(),
            TextColumn::make('status')->badge()->color(fn (string $state): string => match ($state) {
                'Available' => 'success', 'In Use' => 'info', 'Maintenance' => 'warning', 'Lost' => 'danger', default => 'gray',
            }),
            TextColumn::make('condition')->badge(),
            TextColumn::make('purchase_date')->label('Purchase Date')->date()->toggleable(isToggledHiddenByDefault: true),
            TextColumn::make('purchase_cost')->label('Purchase Cost')->money('IDR')->sortable()->toggleable(isToggledHiddenByDefault: true),
        ])->filters([
            SelectFilter::make('status')->options(Asset::STATUSES),
            SelectFilter::make('condition')->options(Asset::CONDITIONS),
            SelectFilter::make('company')->relationship('company', 'nama')->label('Company'),
            TrashedFilter::make(),
        ])->recordActions([EditAction::make(), DeleteAction::make(), RestoreAction::make()]);
    }
}
