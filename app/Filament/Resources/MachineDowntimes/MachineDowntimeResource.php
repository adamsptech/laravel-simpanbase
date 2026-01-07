<?php

namespace App\Filament\Resources\MachineDowntimes;

use App\Filament\Resources\MachineDowntimes\Pages\CreateMachineDowntime;
use App\Filament\Resources\MachineDowntimes\Pages\EditMachineDowntime;
use App\Filament\Resources\MachineDowntimes\Pages\ListMachineDowntimes;
use App\Filament\Resources\MachineDowntimes\Schemas\MachineDowntimeForm;
use App\Filament\Resources\MachineDowntimes\Tables\MachineDowntimesTable;
use App\Models\MachineDowntime;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class MachineDowntimeResource extends Resource
{
    protected static ?string $model = MachineDowntime::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedExclamationTriangle;

    protected static \UnitEnum|string|null $navigationGroup = 'Maintenance';

    protected static ?int $navigationSort = 5;

    protected static ?string $navigationLabel = 'Machine Downtime';

    protected static ?string $modelLabel = 'Downtime Record';

    protected static ?string $pluralModelLabel = 'Downtime Records';

    protected static ?string $recordTitleAttribute = 'problem';

    public static function form(Schema $schema): Schema
    {
        return MachineDowntimeForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return MachineDowntimesTable::configure($table);
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
            'index' => ListMachineDowntimes::route('/'),
            'create' => CreateMachineDowntime::route('/create'),
            'edit' => EditMachineDowntime::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::getEloquentQuery()->with(['equipment:id,name']);
    }

    public static function canAccess(): bool
    {
        $role = auth()->user()?->role?->name ?? 'User';
        return in_array($role, ['Admin', 'Manager', 'Planner', 'Supervisor']);
    }
}

