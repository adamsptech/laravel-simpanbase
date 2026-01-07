<?php

namespace App\Filament\Resources\OeeMonthlies\Schemas;

use App\Models\Equipment;
use Filament\Forms\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;
use Filament\Forms\Get;
use Filament\Forms\Set;

class OeeMonthlyForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Equipment & Period')
                    ->columns(3)
                    ->schema([
                        Select::make('equipment_id')
                            ->label('Equipment')
                            ->options(Equipment::pluck('name', 'id'))
                            ->searchable()
                            ->required(),
                        Select::make('year')
                            ->label('Year')
                            ->options(array_combine(
                                range(now()->year - 3, now()->year + 1),
                                range(now()->year - 3, now()->year + 1)
                            ))
                            ->required()
                            ->default(now()->year),
                        Select::make('month')
                            ->label('Month')
                            ->options([
                                1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April',
                                5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August',
                                9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December',
                            ])
                            ->required()
                            ->default(now()->month),
                    ]),

                Section::make('Monthly Calendar (Working Time)')
                    ->description('Total working days/hours/minutes for the month')
                    ->columns(4)
                    ->schema([
                        TextInput::make('working_days')
                            ->label('Days')
                            ->numeric()
                            ->required()
                            ->default(28)
                            ->live()
                            ->afterStateUpdated(fn (Get $get, Set $set) => self::recalculate($get, $set)),
                        TextInput::make('working_hours')
                            ->label('Hours')
                            ->numeric()
                            ->default(672),
                        TextInput::make('working_minutes')
                            ->label('Minutes')
                            ->numeric()
                            ->default(40320),
                        TextInput::make('dummy_calendar_pct')
                            ->label('%')
                            ->disabled()
                            ->dehydrated(false)
                            ->default('100%'),
                    ]),

                Section::make('Plant Operating')
                    ->description('Actual operating time (usually same as calendar)')
                    ->columns(4)
                    ->schema([
                        TextInput::make('plant_operating_days')->label('Days')->numeric()->default(28),
                        TextInput::make('plant_operating_hours')->label('Hours')->numeric()->default(672),
                        TextInput::make('plant_operating_minutes')->label('Minutes')->numeric()->default(40320),
                        TextInput::make('plant_operating_percentage')->label('%')->numeric()->suffix('%')->default(100),
                    ]),

                Section::make('Planned Maintenance')
                    ->description('Scheduled maintenance downtime')
                    ->columns(4)
                    ->schema([
                        TextInput::make('planned_maintenance_days')->label('Days')->numeric()->default(0),
                        TextInput::make('planned_maintenance_hours')->label('Hours')->numeric()->default(0),
                        TextInput::make('planned_maintenance_minutes')->label('Minutes')->numeric()->default(0),
                        TextInput::make('planned_maintenance_percentage')->label('%')->numeric()->suffix('%')->default(0),
                    ]),

                Section::make('Plant Production')
                    ->description('Operating - Planned Maintenance')
                    ->columns(4)
                    ->schema([
                        TextInput::make('plant_production_days')->label('Days')->numeric()->default(28),
                        TextInput::make('plant_production_hours')->label('Hours')->numeric()->default(672),
                        TextInput::make('plant_production_minutes')->label('Minutes')->numeric()->default(40320),
                        TextInput::make('plant_production_percentage')->label('%')->numeric()->suffix('%')->default(100),
                    ]),

                Section::make('Unplanned Maintenance')
                    ->description('Unexpected breakdowns')
                    ->columns(4)
                    ->schema([
                        TextInput::make('unplanned_maintenance_days')->label('Days')->numeric()->default(0),
                        TextInput::make('unplanned_maintenance_hours')->label('Hours')->numeric()->default(0),
                        TextInput::make('unplanned_maintenance_minutes')->label('Minutes')->numeric()->default(0),
                        TextInput::make('unplanned_maintenance_percentage')->label('%')->numeric()->suffix('%')->default(0),
                    ]),

                Section::make('Actual Plant Production')
                    ->description('Plant Production - Unplanned')
                    ->columns(4)
                    ->schema([
                        TextInput::make('actual_production_days')->label('Days')->numeric()->default(28),
                        TextInput::make('actual_production_hours')->label('Hours')->numeric()->default(672),
                        TextInput::make('actual_production_minutes')->label('Minutes')->numeric()->default(40320),
                        TextInput::make('actual_production_percentage')->label('%')->numeric()->suffix('%')->default(100),
                    ]),

                Section::make('Notes')
                    ->collapsed()
                    ->schema([
                        Textarea::make('notes')
                            ->rows(3)
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    protected static function recalculate(Get $get, Set $set): void
    {
        $days = (int) ($get('working_days') ?? 28);
        $hours = $days * 24;
        $minutes = $hours * 60;
        
        $set('working_hours', $hours);
        $set('working_minutes', $minutes);
        $set('plant_operating_days', $days);
        $set('plant_operating_hours', $hours);
        $set('plant_operating_minutes', $minutes);
        $set('plant_production_days', $days);
        $set('plant_production_hours', $hours);
        $set('plant_production_minutes', $minutes);
        $set('actual_production_days', $days);
        $set('actual_production_hours', $hours);
        $set('actual_production_minutes', $minutes);
    }
}
