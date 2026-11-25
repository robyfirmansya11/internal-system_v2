<?php

namespace App\Filament\Resources\Assets\Schemas;

use App\Models\Asset;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Validation\Rule;

class AssetForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make(__('Asset Information'))->schema([
                TextInput::make('asset_code')->label('Asset Code')->required()->maxLength(50)->unique(ignoreRecord: true)
                    ->helperText(__('Use a unique inventory code, including assets in Trash.')),
                TextInput::make('name')->label('Asset Name')->required()->maxLength(255),
                TextInput::make('category')->required()->maxLength(100)->placeholder(__('Laptop, Vehicle, Furniture')),
                TextInput::make('serial_number')->label('Serial Number')->maxLength(255),
                Select::make('company_id')->label('Company')->relationship('company', 'nama', modifyQueryUsing: fn (Builder $query) => $query->whereNull('companies.deleted_at'))->searchable()->preload()->required()
                    ->rules([Rule::exists('companies', 'id')->whereNull('deleted_at')]),
                TextInput::make('location')->maxLength(255),
                Select::make('assigned_to')->label('Assigned To')->relationship('assignee', 'name')->searchable()->preload()->rules(['nullable', 'exists:users,id']),
                Select::make('status')->options(Asset::STATUSES)->default('Available')->required()->rules([Rule::in(array_keys(Asset::STATUSES))]),
                Select::make('condition')->options(Asset::CONDITIONS)->default('Good')->required()->rules([Rule::in(array_keys(Asset::CONDITIONS))]),
            ])->columns(2)->columnSpanFull(),
            Section::make(__('Acquisition and Notes'))->schema([
                DatePicker::make('purchase_date')->label('Purchase Date')->maxDate(today()),
                TextInput::make('purchase_cost')->label('Purchase Cost')->numeric()->prefix('Rp')->minValue(0)->maxValue(9999999999999.99)->rules(['nullable', 'decimal:0,2']),
                Textarea::make('notes')->maxLength(5000)->rows(3)->columnSpanFull(),
            ])->columns(2)->columnSpanFull(),
        ]);
    }
}
