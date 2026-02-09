<?php

namespace App\Filament\Resources\Talks\Tables;

use App\enums\TalkLength;
use App\enums\TalkStatus;
use App\Models\Talk;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

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
                        return 'https://ui-avatars.com/api/?background=0D8ABC&color=fff&name=' . urlencode($record->speaker->name);
                    }),
                TextColumn::make('speaker.name')
                    ->searchable(),
                ToggleColumn::make('new_talk'),
                TextColumn::make('status')
                    ->color(function (TalkStatus|string $state): string {
                        $status = $state instanceof TalkStatus
                            ? $state
                            : TalkStatus::from($state);

                        return $status->getColor();
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
                EditAction::make()
                    ->slideOver(),
                ActionGroup::make([
                    Action::make('approve')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->visible(function ($record) {
                            return $record->status === TalkStatus::SUBMITTED;
                        })
                        ->action(function (Talk $record) {
                            $record->approve();
                        })
                        ->after(function () {
                            Notification::make()->success()->title('This Talk was approved')
                                ->body('The speaker has been notified and the talk has been added to the conference schedule')
                                ->send();
                        }),
                    Action::make('reject')
                        ->visible(function ($record) {
                            return $record->status === TalkStatus::SUBMITTED;
                        })
                        ->icon('heroicon-o-no-symbol')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->action(function (Talk $record) {
                            $record->reject();
                        })
                        ->after(function () {
                            Notification::make()->danger()->title('This Talk was rejected')
                                ->body('The speaker has been notified')
                                ->send();
                        }),
                ])
                    ->visible(fn($record) => $record->status === TalkStatus::SUBMITTED),

            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    BulkAction::make('approve')
                        ->action(function (Collection $records) {
                            $records->each->approve();
                        })
                    ,
                    DeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ])
            ->headerActions([
                Action::make('export')
                    ->tooltip('This will export all records visible in the table. Adjust filters to export subste of records')
                    ->action(function ($livewire) {
                        ray($livewire);
                        // Export logic here
                        ray('Exporting talks');
                    }),
            ]);
    }
}
