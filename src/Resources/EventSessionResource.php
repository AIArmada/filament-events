<?php

declare(strict_types=1);

namespace AIArmada\FilamentEvents\Resources;

use AIArmada\Events\Models\EventSession;
use BackedEnum;
use Filament\Actions\ViewAction;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

final class EventSessionResource extends Resource
{
    protected static ?string $model = EventSession::class;

    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-list-bullet';

    protected static ?int $navigationSort = 3;

    public static function getNavigationGroup(): ?string
    {
        return config('filament-events.navigation.group');
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('event.title')
                    ->label('Event'),
                Tables\Columns\TextColumn::make('occurrence.title')
                    ->label('Occurrence'),
                Tables\Columns\TextColumn::make('title'),
                Tables\Columns\TextColumn::make('starts_at')
                    ->dateTime(),
                Tables\Columns\TextColumn::make('ends_at')
                    ->dateTime(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'draft' => 'gray',
                        'scheduled' => 'info',
                        'published' => 'success',
                        'delayed', 'postponed' => 'warning',
                        'cancelled' => 'danger',
                        'completed' => 'success',
                        'archived' => 'gray',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('sort_order')
                    ->numeric()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'draft' => 'Draft',
                        'scheduled' => 'Scheduled',
                        'published' => 'Published',
                        'delayed' => 'Delayed',
                        'postponed' => 'Postponed',
                        'cancelled' => 'Cancelled',
                        'completed' => 'Completed',
                        'archived' => 'Archived',
                    ]),
            ])
            ->actions([
                ViewAction::make(),
            ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Session Details')
                    ->schema([
                        TextEntry::make('event.title'),
                        TextEntry::make('occurrence.title'),
                        TextEntry::make('title'),
                        TextEntry::make('slug'),
                        TextEntry::make('summary'),
                        TextEntry::make('description'),
                        TextEntry::make('starts_at')->dateTime(),
                        TextEntry::make('ends_at')->dateTime(),
                        TextEntry::make('timezone'),
                        TextEntry::make('status')->badge(),
                        TextEntry::make('visibility')->badge(),
                        TextEntry::make('delivery_mode')->badge(),
                        TextEntry::make('capacity')->numeric(),
                        TextEntry::make('sort_order')->numeric(),
                    ])->columns(2),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => EventSessionResource\Pages\ListEventSessions::route('/'),
            'view' => EventSessionResource\Pages\ViewEventSession::route('/{record}'),
        ];
    }
}
