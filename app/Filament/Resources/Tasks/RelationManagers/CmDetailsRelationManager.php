<?php

namespace App\Filament\Resources\Tasks\RelationManagers;

use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CmDetailsRelationManager extends RelationManager
{
    protected static string $relationship = 'cmDetails';

    protected static ?string $title = 'CM Details (Corrective Maintenance)';

    protected static ?string $recordTitleAttribute = 'action';

    public function form(Schema $form): Schema
    {
        return $form->components([
            Section::make('Problem Analysis')
                ->columns(1)
                ->schema([
                    TextInput::make('action')
                        ->label('Corrective Action Taken')
                        ->required()
                        ->maxLength(255),
                    Textarea::make('root_cause')
                        ->label('Root Cause Analysis')
                        ->rows(3),
                    Textarea::make('solution')
                        ->label('Solution/Recommendation')
                        ->rows(3),
                ]),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('action')
            ->columns([
                TextColumn::make('action')
                    ->label('Action Taken')
                    ->limit(50)
                    ->searchable(),
                TextColumn::make('root_cause')
                    ->label('Root Cause')
                    ->limit(50),
                TextColumn::make('solution')
                    ->label('Solution')
                    ->limit(50),
                TextColumn::make('created_at')
                    ->dateTime('d/m/Y')
                    ->label('Date'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make(),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                DeleteBulkAction::make(),
            ]);
    }
}
