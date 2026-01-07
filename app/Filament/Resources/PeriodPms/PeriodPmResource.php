<?php

namespace App\Filament\Resources\PeriodPms;

use App\Filament\Resources\PeriodPms\Pages\CreatePeriodPm;
use App\Filament\Resources\PeriodPms\Pages\EditPeriodPm;
use App\Filament\Resources\PeriodPms\Pages\ListPeriodPms;
use App\Filament\Resources\PeriodPms\Schemas\PeriodPmForm;
use App\Filament\Resources\PeriodPms\Tables\PeriodPmsTable;
use App\Models\PeriodPm;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class PeriodPmResource extends Resource
{
    protected static ?string $model = PeriodPm::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCalendarDays;

    protected static \UnitEnum|string|null $navigationGroup = 'Settings';

    protected static ?int $navigationSort = 2;

    protected static ?string $navigationLabel = 'PM Periods';

    protected static ?string $modelLabel = 'PM Period';

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return PeriodPmForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PeriodPmsTable::configure($table);
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
            'index' => ListPeriodPms::route('/'),
            'create' => CreatePeriodPm::route('/create'),
            'edit' => EditPeriodPm::route('/{record}/edit'),
        ];
    }

    public static function canAccess(): bool
    {
        $role = auth()->user()?->role?->name ?? 'User';
        return in_array($role, ['Admin', 'Manager', 'Planner']);
    }
}

