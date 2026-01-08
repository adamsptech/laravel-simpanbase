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
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;

class MachineDowntimeForm
{
    public static function configure(Schema $schema): Schema
    {
        $userRole = auth()->user()?->role?->name ?? 'User';
        $isCustomer = $userRole === 'Customer';
        $isEngineer = in_array($userRole, ['Engineer', 'Technician', 'User']);
        $isAdmin = in_array($userRole, ['Admin', 'Manager', 'Supervisor', 'Planner']);

        return $schema
            ->components([
                Section::make('Equipment & Problem')
                    ->description($isCustomer ? 'Report a downtime issue' : 'Downtime details')
                    ->columns(2)
                    ->schema([
                        Select::make('equipment_id')
                            ->label('Equipment')
                            ->options(Equipment::pluck('name', 'id'))
                            ->searchable()
                            ->required()
                            ->disabled(fn ($record) => $record && !$record->canCustomerEdit() && $isCustomer),
                        Select::make('status')
                            ->label('Status')
                            ->options([
                                MachineDowntime::STATUS_OPEN => 'Open',
                                MachineDowntime::STATUS_IN_PROGRESS => 'In Progress',
                                MachineDowntime::STATUS_CLOSED => 'Closed',
                            ])
                            ->default(MachineDowntime::STATUS_OPEN)
                            ->disabled($isCustomer)
                            ->visible(!$isCustomer || fn ($record) => $record !== null),
                        TextInput::make('problem')
                            ->label('Problem Description')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull()
                            ->disabled(fn ($record) => $record && !$record->canCustomerEdit() && $isCustomer),
                    ]),
                
                Section::make('Downtime Period')
                    ->columns(2)
                    ->schema([
                        DateTimePicker::make('start_datetime')
                            ->label('Start Date/Time')
                            ->required()
                            ->native(false)
                            ->displayFormat('d/m/Y H:i')
                            ->default(now())
                            ->live()
                            ->afterStateUpdated(function (Get $get, Set $set) {
                                self::calculateDowntime($get, $set);
                            })
                            ->disabled(fn ($record) => $record && !$record->canCustomerEdit() && $isCustomer),
                        DateTimePicker::make('end_datetime')
                            ->label('End Date/Time')
                            ->native(false)
                            ->displayFormat('d/m/Y H:i')
                            ->live()
                            ->afterStateUpdated(function (Get $get, Set $set) {
                                self::calculateDowntime($get, $set);
                            })
                            // Customer cannot set end time, only engineer
                            ->disabled($isCustomer)
                            ->visible(!$isCustomer || fn ($record) => $record !== null),
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

                Section::make('Engineer Analysis')
                    ->description('To be filled by engineer after pickup')
                    ->columns(1)
                    ->schema([
                        Textarea::make('root_cause')
                            ->label('Root Cause Analysis')
                            ->rows(3)
                            ->disabled($isCustomer && fn ($record) => !$record || $record->status === MachineDowntime::STATUS_OPEN),
                        Textarea::make('action_taken')
                            ->label('Action Taken')
                            ->rows(3)
                            ->disabled($isCustomer),
                    ])
                    // Show for non-customers or when record exists
                    ->visible(!$isCustomer || fn ($record) => $record !== null),
                
                Hidden::make('year')
                    ->default(fn () => now()->year),
                Hidden::make('month')
                    ->default(fn () => now()->month),
                Hidden::make('reported_by')
                    ->default(fn () => auth()->id()),
                Hidden::make('submitted_by')
                    ->default(fn () => $isCustomer ? auth()->id() : null),
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
        } elseif ($start) {
            // If only start is set, still set year/month
            $startDate = new \DateTime($start);
            $set('year', (int) $startDate->format('Y'));
            $set('month', (int) $startDate->format('n'));
        }
    }
}
