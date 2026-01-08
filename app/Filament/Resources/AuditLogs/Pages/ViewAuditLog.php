<?php

namespace App\Filament\Resources\AuditLogs\Pages;

use App\Filament\Resources\AuditLogs\AuditLogResource;
use App\Models\AuditLog;
use Filament\Actions\Action;
use Filament\Infolists\Components\KeyValueEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;

class ViewAuditLog extends ViewRecord
{
    protected static string $resource = AuditLogResource::class;

    public function getTitle(): string
    {
        $record = $this->getRecord();
        return "View Audit - {$record->id} @ {$record->created_at->format('Y-m-d H:i:s')}";
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('view_record')
                ->label('View Record')
                ->icon('heroicon-o-eye')
                ->color('primary')
                ->url(function () {
                    $record = $this->getRecord();
                    if ($record->model_type && $record->model_id) {
                        // Try to generate URL to the related record
                        $modelClass = $record->model_type;
                        if ($modelClass === 'App\\Models\\Task') {
                            return \App\Filament\Resources\Tasks\TaskResource::getUrl('view', ['record' => $record->model_id]);
                        }
                        if ($modelClass === 'App\\Models\\Equipment') {
                            return \App\Filament\Resources\Equipment\EquipmentResource::getUrl('view', ['record' => $record->model_id]);
                        }
                    }
                    return null;
                })
                ->visible(fn () => $this->getRecord()->model_id !== null),
        ];
    }

    public function infolist(Schema $schema): Schema
    {
        return $schema->components([
            Tabs::make('AuditTabs')
                ->tabs([
                    Tab::make('Metadata')
                        ->icon('heroicon-o-information-circle')
                        ->schema([
                            TextEntry::make('action')
                                ->label('Event')
                                ->badge()
                                ->color(fn (string $state): string => match ($state) {
                                    'created' => 'success',
                                    'updated' => 'warning',
                                    'deleted' => 'danger',
                                    default => 'gray',
                                }),
                            TextEntry::make('created_at')
                                ->label('Recorded at')
                                ->dateTime('d/m/Y H:i:s'),
                            TextEntry::make('model_name')
                                ->label('Model'),
                            TextEntry::make('model_label')
                                ->label('Record'),
                            TextEntry::make('ip_address')
                                ->label('IP address'),
                            TextEntry::make('model_id')
                                ->label('Record ID'),
                            TextEntry::make('user_agent')
                                ->label('User agent')
                                ->columnSpanFull(),
                        ])
                        ->columns(2),
                    Tab::make('User data')
                        ->icon('heroicon-o-user')
                        ->schema([
                            TextEntry::make('user_name')
                                ->label('User'),
                            TextEntry::make('user_id')
                                ->label('User ID'),
                            TextEntry::make('ip_address')
                                ->label('IP Address'),
                            TextEntry::make('user_agent')
                                ->label('Browser')
                                ->columnSpanFull(),
                        ])
                        ->columns(2),
                    Tab::make('Old (Before)')
                        ->icon('heroicon-o-minus-circle')
                        ->schema([
                            KeyValueEntry::make('old_values')
                                ->label('')
                                ->columnSpanFull(),
                        ])
                        ->visible(fn (AuditLog $record) => !empty($record->old_values)),
                    Tab::make('New (After)')
                        ->icon('heroicon-o-plus-circle')
                        ->schema([
                            KeyValueEntry::make('new_values')
                                ->label('')
                                ->columnSpanFull(),
                        ])
                        ->visible(fn (AuditLog $record) => !empty($record->new_values)),
                ])
                ->columnSpanFull(),
        ]);
    }
}
