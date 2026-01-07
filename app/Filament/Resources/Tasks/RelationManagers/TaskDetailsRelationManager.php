<?php

namespace App\Filament\Resources\Tasks\RelationManagers;

use Filament\Actions\AssociateAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\DissociateAction;
use Filament\Actions\DissociateBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class TaskDetailsRelationManager extends RelationManager
{
    protected static string $relationship = 'taskDetails';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('checklist_name')
                    ->default(null),
                TextInput::make('actual')
                    ->default(null),
                TextInput::make('recommended')
                    ->default(null),
                TextInput::make('action')
                    ->default(null),
                Select::make('engineer_id')
                    ->relationship('engineer', 'name')
                    ->default(null),
            ]);
    }

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('checklist_name')
                    ->placeholder('-'),
                TextEntry::make('actual')
                    ->placeholder('-'),
                TextEntry::make('recommended')
                    ->placeholder('-'),
                TextEntry::make('action')
                    ->placeholder('-'),
                TextEntry::make('engineer.name')
                    ->label('Engineer')
                    ->placeholder('-'),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('checklist_name')
            ->columns([
                TextColumn::make('checklist_name')
                    ->searchable(),
                TextColumn::make('actual')
                    ->searchable(),
                TextColumn::make('recommended')
                    ->searchable(),
                TextColumn::make('action')
                    ->searchable(),
                TextColumn::make('engineer.name')
                    ->searchable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make(),
                AssociateAction::make(),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                DissociateAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DissociateBulkAction::make(),
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
