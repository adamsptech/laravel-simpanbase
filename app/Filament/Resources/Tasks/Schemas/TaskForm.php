<?php

namespace App\Filament\Resources\Tasks\Schemas;

use App\Models\Equipment;
use App\Models\Sublocation;
use App\Models\Task;
use App\Models\User;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;

class TaskForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                Section::make('Work Order Information')
                    ->columns(2)
                    ->schema([
                        Select::make('maint_category_id')
                            ->label('Maintenance Type')
                            ->relationship('maintCategory', 'name')
                            ->required()
                            ->native(false),

                        Select::make('status')
                            ->options([
                                Task::STATUS_OPEN => 'Open',
                                Task::STATUS_SUBMITTED_SUPERVISOR => 'Pending Supervisor',
                                Task::STATUS_SUBMITTED_MANAGER => 'Pending Manager',
                                Task::STATUS_SUBMITTED_CUSTOMER => 'Pending Customer',
                                Task::STATUS_CLOSED => 'Closed',
                            ])
                            ->default(Task::STATUS_OPEN)
                            ->required()
                            ->native(false),

                        Select::make('priority')
                            ->options([
                                Task::PRIORITY_LOW => 'Low',
                                Task::PRIORITY_MEDIUM => 'Medium',
                                Task::PRIORITY_HIGH => 'High',
                            ])
                            ->default(Task::PRIORITY_MEDIUM)
                            ->required()
                            ->native(false),

                        DatePicker::make('due_date')
                            ->label('Due Date')
                            ->native(false),
                    ]),

                Section::make('Location & Equipment')
                    ->columns(2)
                    ->schema([
                        Select::make('location_id')
                            ->relationship('location', 'name')
                            ->searchable()
                            ->preload()
                            ->live()
                            ->afterStateUpdated(fn ($set) => $set('sublocation_id', null))
                            ->native(false),

                        Select::make('sublocation_id')
                            ->label('Sub-Location')
                            ->options(function ($get) {
                                $locationId = $get('location_id');
                                if (!$locationId) {
                                    return [];
                                }
                                return Sublocation::where('location_id', $locationId)
                                    ->pluck('name', 'id');
                            })
                            ->searchable()
                            ->live()
                            ->afterStateUpdated(fn ($set) => $set('equipment_id', null))
                            ->native(false),

                        Select::make('equipment_id')
                            ->label('Equipment')
                            ->options(function ($get) {
                                $sublocationId = $get('sublocation_id');
                                if (!$sublocationId) {
                                    return [];
                                }
                                return Equipment::where('sublocation_id', $sublocationId)
                                    ->pluck('name', 'id');
                            })
                            ->searchable()
                            ->native(false),
                    ]),

                Section::make('PM Schedule')
                    ->columns(2)
                    ->description('For Preventive Maintenance tasks')
                    ->schema([
                        Select::make('period_id')
                            ->label('Maintenance Period')
                            ->relationship('period', 'name')
                            ->helperText('Determines recurrence pattern (Daily, Weekly, Monthly, etc.)')
                            ->live()
                            ->native(false),

                        Select::make('type_check_id')
                            ->label('Checklist Type')
                            ->relationship('typeCheck', 'name')
                            ->native(false),

                        Toggle::make('make_recurring')
                            ->label('Make Recurring')
                            ->helperText('Create a series of scheduled work orders')
                            ->live()
                            ->dehydrated(false)
                            ->visible(fn ($get) => $get('period_id') !== null)
                            ->columnSpanFull(),

                        Select::make('recurrence_duration')
                            ->label('Recurrence Duration')
                            ->options([
                                1 => '1 Year',
                                2 => '2 Years',
                                3 => '3 Years',
                                4 => '4 Years',
                                5 => '5 Years',
                            ])
                            ->default(1)
                            ->helperText('How long to generate recurring tasks')
                            ->dehydrated(false)
                            ->visible(fn ($get) => $get('make_recurring') === true)
                            ->native(false),
                    ]),

                Section::make('Assignment')
                    ->columns(2)
                    ->schema([
                        Select::make('assigned_to')
                            ->label('Assigned Engineer')
                            ->options(function () {
                                return User::whereHas('role', function (Builder $query) {
                                    $query->where('name', 'Engineer');
                                })->pluck('name', 'id');
                            })
                            ->searchable()
                            ->native(false),

                        Select::make('supervisor_id')
                            ->label('Supervisor')
                            ->options(function () {
                                return User::whereHas('role', function (Builder $query) {
                                    $query->whereIn('name', ['Supervisor', 'Manager']);
                                })->pluck('name', 'id');
                            })
                            ->searchable()
                            ->native(false),

                        TextInput::make('shift')
                            ->label('Shift'),
                    ]),

                Section::make('Timing')
                    ->columns(3)
                    ->collapsed()
                    ->schema([
                        DateTimePicker::make('started_at')
                            ->label('Started At')
                            ->native(false),

                        DateTimePicker::make('ended_at')
                            ->label('Ended At')
                            ->native(false),

                        TextInput::make('duration')
                            ->label('Duration')
                            ->placeholder('e.g. 2 hours'),
                    ]),

                Section::make('Notes')
                    ->schema([
                        Textarea::make('notes')
                            ->label('Work Order Notes')
                            ->rows(4)
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
