<?php

declare(strict_types=1);

namespace AIArmada\FilamentEvents\Resources;

use AIArmada\Events\Models\Event;
use BackedEnum;
use Filament\Actions\ViewAction;
use Filament\Infolists\Components\CodeEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

final class EventResource extends Resource
{
    protected static ?string $model = Event::class;

    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-calendar';

    protected static ?int $navigationSort = 1;

    public static function getNavigationGroup(): ?string
    {
        return config('filament-events.navigation.group');
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->searchable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'draft' => 'gray',
                        'pending_review' => 'warning',
                        'scheduled' => 'info',
                        'published' => 'success',
                        'delayed', 'postponed' => 'warning',
                        'cancelled', 'voided', 'expired' => 'danger',
                        'completed' => 'success',
                        'archived' => 'gray',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('visibility')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'public' => 'success',
                        'unlisted' => 'warning',
                        'private', 'internal' => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('delivery_mode')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'physical' => 'info',
                        'online' => 'success',
                        'hybrid' => 'warning',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('occurrences_count')
                    ->label('Occurrences')
                    ->counts('occurrences'),
                Tables\Columns\TextColumn::make('published_at')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'draft' => 'Draft',
                        'pending_review' => 'Pending Review',
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
                Tables\Filters\SelectFilter::make('delivery_mode')
                    ->options([
                        'physical' => 'Physical',
                        'online' => 'Online',
                        'hybrid' => 'Hybrid',
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
                Section::make('Identity')
                    ->schema([
                        TextEntry::make('title'),
                        TextEntry::make('slug'),
                        TextEntry::make('summary'),
                        TextEntry::make('description'),
                        TextEntry::make('type'),
                    ])->columns(2),
                Section::make('Lifecycle')
                    ->schema([
                        TextEntry::make('status')->badge(),
                        TextEntry::make('visibility')->badge(),
                        TextEntry::make('delivery_mode')->badge(),
                        TextEntry::make('published_at')->dateTime(),
                        TextEntry::make('cancelled_at')->dateTime(),
                        TextEntry::make('postponed_at')->dateTime(),
                        TextEntry::make('archived_at')->dateTime(),
                    ])->columns(2),
                Section::make('Ownership')
                    ->schema([
                        TextEntry::make('owner_type'),
                        TextEntry::make('owner_id'),
                    ])->columns(2),
                Section::make('Metadata')
                    ->schema([
                        CodeEntry::make('metadata')
                            ->visible(fn (?array $state): bool => ! empty($state)),
                    ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => EventResource\Pages\ListEvents::route('/'),
            'view' => EventResource\Pages\ViewEvent::route('/{record}'),
        ];
    }
}
