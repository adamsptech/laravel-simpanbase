<?php

namespace App\Filament\Resources\PartAdditions\Schemas;

use App\Models\PartStock;
use App\Models\Supplier;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Hidden;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;
use Filament\Forms\Get;
use Filament\Forms\Set;

class PartAdditionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Part & Quantity')
                    ->columns(2)
                    ->schema([
                        Select::make('part_stock_id')
                            ->label('Part')
                            ->options(PartStock::pluck('name', 'id'))
                            ->searchable()
                            ->required()
                            ->live()
                            ->afterStateUpdated(function (Get $get, Set $set, $state) {
                                if ($state) {
                                    $part = PartStock::find($state);
                                    $set('current_stock_before', $part?->quantity ?? 0);
                                }
                            }),
                        TextInput::make('quantity')
                            ->label('Quantity to Add')
                            ->required()
                            ->numeric()
                            ->minValue(1)
                            ->live()
                            ->afterStateUpdated(function (Get $get, Set $set, $state) {
                                $before = $get('current_stock_before') ?? 0;
                                $set('current_stock_after', $before + ($state ?? 0));
                            }),
                        TextInput::make('current_stock_before')
                            ->label('Current Stock')
                            ->disabled()
                            ->dehydrated(false),
                        TextInput::make('current_stock_after')
                            ->label('Stock After Addition')
                            ->disabled()
                            ->dehydrated(),
                    ]),

                Section::make('Purchase Information')
                    ->columns(2)
                    ->schema([
                        TextInput::make('opb_number')
                            ->label('OPB/PO Number')
                            ->maxLength(100),
                        DatePicker::make('add_date')
                            ->label('Addition Date')
                            ->required()
                            ->default(now())
                            ->native(false),
                        Select::make('supplier_id')
                            ->label('Supplier')
                            ->options(Supplier::pluck('name', 'id'))
                            ->searchable(),
                        TextInput::make('price')
                            ->label('Unit Price')
                            ->numeric()
                            ->prefix('Rp'),
                    ]),

                Section::make('Notes')
                    ->schema([
                        Textarea::make('notes')
                            ->rows(3)
                            ->columnSpanFull(),
                    ])
                    ->collapsed(),

                Hidden::make('added_by')
                    ->default(fn () => auth()->id()),
            ]);
    }
}
