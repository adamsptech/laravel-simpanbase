<?php

namespace App\Filament\Resources\OeeMonthlies;

use App\Filament\Resources\OeeMonthlies\Pages\CreateOeeMonthly;
use App\Filament\Resources\OeeMonthlies\Pages\EditOeeMonthly;
use App\Filament\Resources\OeeMonthlies\Pages\ListOeeMonthlies;
use App\Filament\Resources\OeeMonthlies\Schemas\OeeMonthlyForm;
use App\Filament\Resources\OeeMonthlies\Tables\OeeMonthliesTable;
use App\Models\OeeMonthly;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class OeeMonthlyResource extends Resource
{
    protected static ?string $model = OeeMonthly::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedPresentationChartLine;

    protected static \UnitEnum|string|null $navigationGroup = 'Management';

    protected static ?int $navigationSort = 22;

    protected static ?string $navigationLabel = 'OEE Data';

    protected static bool $shouldRegisterNavigation = false; // Hidden - access via OEE Report

    protected static ?string $modelLabel = 'OEE Record';

    protected static ?string $pluralModelLabel = 'OEE Records';

    public static function form(Schema $schema): Schema
    {
        return OeeMonthlyForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return OeeMonthliesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListOeeMonthlies::route('/'),
            'create' => CreateOeeMonthly::route('/create'),
            'edit' => EditOeeMonthly::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::getEloquentQuery()->with(['equipment:id,name']);
    }

    public static function canAccess(): bool
    {
        $role = auth()->user()?->role?->name ?? 'User';
        return in_array($role, ['Admin', 'Manager', 'Planner']);
    }
}
