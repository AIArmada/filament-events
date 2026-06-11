<?php

declare(strict_types=1);

namespace AIArmada\FilamentEvents\Resources;

use AIArmada\Events\Models\EventOccurrence;
use BackedEnum;
use Filament\Actions\ViewAction;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

final class EventOccurrenceResource extends Resource
{
    protected static ?string $model = EventOccurrence::class;

    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-clock';

    protected static ?int $navigationSort = 2;

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
                Tables\Columns\TextColumn::make('title'),
                Tables\Columns\TextColumn::make('starts_at')
                    ->dateTime()
                    ->sortable(),
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
                Tables\Columns\TextColumn::make('visibility')
                    ->badge(),
                Tables\Columns\TextColumn::make('capacity')
                    ->numeric(),
                Tables\Columns\TextColumn::make('published_at')
                    ->dateTime(),
                Tables\Columns\TextColumn::make('cancelled_at')
                    ->dateTime(),
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
                Tables\Filters\SelectFilter::make('visibility')
                    ->options([
                        'public' => 'Public',
                        'unlisted' => 'Unlisted',
                        'private' => 'Private',
                    ]),
                Tables\Filters\SelectFilter::make('event_id')
                    ->label('Event')
                    ->relationship('event', 'title'),
            ])
            ->actions([
                ViewAction::make(),
            ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Event')
                    ->schema([
                        TextEntry::make('event.title'),
                    ]),
                Section::make('Date / Time')
                    ->schema([
                        TextEntry::make('starts_at')->dateTime(),
                        TextEntry::make('ends_at')->dateTime(),
                        TextEntry::make('timezone'),
                    ])->columns(2),
                Section::make('Status')
                    ->schema([
                        TextEntry::make('status')->badge(),
                        TextEntry::make('visibility')->badge(),
                        TextEntry::make('delivery_mode')->badge(),
                        TextEntry::make('capacity')->numeric(),
                    ])->columns(2),
                Section::make('Lifecycle Timestamps')
                    ->schema([
                        TextEntry::make('published_at')->dateTime(),
                        TextEntry::make('delayed_at')->dateTime(),
                        TextEntry::make('postponed_at')->dateTime(),
                        TextEntry::make('cancelled_at')->dateTime(),
                        TextEntry::make('completed_at')->dateTime(),
                        TextEntry::make('archived_at')->dateTime(),
                    ])->columns(2),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => EventOccurrenceResource\Pages\ListEventOccurrences::route('/'),
            'view' => EventOccurrenceResource\Pages\ViewEventOccurrence::route('/{record}'),
        ];
    }
}
