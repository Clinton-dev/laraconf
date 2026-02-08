<?php

namespace App\Filament\Resources\Talks\Tables;

use App\enums\TalkLength;
use App\enums\TalkStatus;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class TalksTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->persistFiltersInSession()
            ->filtersTriggerAction(function ($action) {
                return $action->button()->label('Filter');
            })
            ->columns([
                TextColumn::make('title')
                    ->searchable(),
                ImageColumn::make('speaker.avatar')
                    ->label('Avatar')
                    ->circular()
                    ->defaultImageUrl(function ($record) {
                        return 'https://ui-avatars.com/api/?background=0D8ABC&color=fff&name='.urlencode($record->speaker->name);
                    }),
                TextColumn::make('speaker.name')
                    ->searchable(),
                ToggleColumn::make('new_talk'),
                TextColumn::make('status')
                    ->color(function (string $state): string {
                        return TalkStatus::from($state)->getColor();
                    })
                    ->badge(),
                IconColumn::make('length')
                    ->icon(function (?string $state): string {
                        $length = TalkLength::tryFrom($state ?? '');

                        return match ($length) {
                            TalkLength::NORMAL => 'heroicon-o-megaphone',
                            TalkLength::KEYNOTE => 'heroicon-o-key',
                            TalkLength::LIGHTNING => 'heroicon-o-bolt',
                            default => 'heroicon-o-question-mark-circle',
                        };
                    }),
            ])
            ->filters([
                TernaryFilter::make('new_talk'),
                SelectFilter::make('speaker')
                    ->relationship('speaker', 'name')
                    ->searchable()
                    ->preload()
                    ->multiple(),
                Filter::make('has_avatar')
                    ->label('Show speakers with avatar')
                    ->toggle()
                    ->query(function (Builder $query): Builder {
                        return $query->whereHas('speaker', function (Builder $query): void {
                            $query->whereNotNull('avatar');
                        });
                    }),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
