<?php

namespace App\Filament\Resources\TypeChecks\Schemas;

use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class TypeCheckForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Checklist Type Details')
                    ->description('Define the maintenance checklist type')
                    ->schema([
                        Select::make('equipment_id')
                            ->relationship('equipment', 'name')
                            ->searchable()
                            ->preload()
                            ->label('Equipment'),
                        Select::make('period_id')
                            ->relationship('period', 'name')
                            ->searchable()
                            ->preload()
                            ->label('Period'),
                        TextInput::make('name')
                            ->required()
                            ->label('Checklist Name'),
                    ])
                    ->columns(2),
                    
                Section::make('Checklist Items')
                    ->description('Add the items to check for this maintenance type')
                    ->schema([
                        Repeater::make('checklists')
                            ->relationship()
                            ->label('')
                            ->schema([
                                TextInput::make('name')
                                    ->required()
                                    ->label('Item Name')
                                    ->placeholder('e.g., Check oil level'),
                                TextInput::make('recommended')
                                    ->label('Recommended Value')
                                    ->placeholder('e.g., 3-5 liters'),
                            ])
                            ->columns(2)
                            ->addActionLabel('Add Checklist Item')
                            ->reorderable()
                            ->collapsible()
                            ->defaultItems(0)
                            ->itemLabel(fn (array $state): ?string => $state['name'] ?? 'New Item'),
                    ]),
            ]);
    }
}
