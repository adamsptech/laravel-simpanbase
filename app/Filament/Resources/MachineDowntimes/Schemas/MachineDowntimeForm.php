<?php

namespace App\Filament\Resources\MachineDowntimes\Schemas;

use App\Models\Equipment;
use App\Models\MachineDowntime;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Hidden;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;
use Filament\Forms\Get;
use Filament\Forms\Set;

class MachineDowntimeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Equipment & Problem')
                    ->columns(2)
                    ->schema([
                        Select::make('equipment_id')
                            ->label('Equipment')
                            ->options(Equipment::pluck('name', 'id'))
                            ->searchable()
                            ->required(),
                        TextInput::make('problem')
                            ->label('Problem Description')
                            ->required()
                            ->maxLength(255),
                        Textarea::make('root_cause')
                            ->label('Root Cause Analysis')
                            ->rows(3)
                            ->columnSpanFull(),
                    ]),
                
                Section::make('Downtime Period')
                    ->columns(2)
                    ->schema([
                        DateTimePicker::make('start_datetime')
                            ->label('Start Date/Time')
                            ->required()
                            ->native(false)
                            ->displayFormat('d/m/Y H:i')
                            ->live()
                            ->afterStateUpdated(function (Get $get, Set $set) {
                                self::calculateDowntime($get, $set);
                            }),
                        DateTimePicker::make('end_datetime')
                            ->label('End Date/Time')
                            ->required()
                            ->native(false)
                            ->displayFormat('d/m/Y H:i')
                            ->live()
                            ->afterStateUpdated(function (Get $get, Set $set) {
                                self::calculateDowntime($get, $set);
                            }),
                        TextInput::make('downtime_minutes')
                            ->label('Downtime (minutes)')
                            ->numeric()
                            ->disabled()
                            ->dehydrated()
                            ->default(0),
                        TextInput::make('downtime_formatted')
                            ->label('Downtime (HH:MM)')
                            ->disabled()
                            ->dehydrated(false),
                    ]),
                
                Hidden::make('year'),
                Hidden::make('month'),
                Hidden::make('reported_by')
                    ->default(fn () => auth()->id()),
            ]);
    }

    protected static function calculateDowntime(Get $get, Set $set): void
    {
        $start = $get('start_datetime');
        $end = $get('end_datetime');
        
        if ($start && $end) {
            $minutes = MachineDowntime::calculateDowntimeMinutes($start, $end);
            $set('downtime_minutes', $minutes);
            $set('downtime_formatted', MachineDowntime::formatDowntime($minutes));
            
            // Auto-set year and month from start date
            $startDate = new \DateTime($start);
            $set('year', (int) $startDate->format('Y'));
            $set('month', (int) $startDate->format('n'));
        }
    }
}
