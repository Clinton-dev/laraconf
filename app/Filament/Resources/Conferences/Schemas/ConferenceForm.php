<?php

namespace App\Filament\Resources\Conferences\Schemas;

use App\enums\Region;
use App\Filament\Resources\Venues\Schemas\VenueForm;
use App\Models\Conference;
use App\Models\Speaker;
use Filament\Actions\Action;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;

class ConferenceForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components(self::getForm());
    }

    public static function getForm(): array
    {
        return [
            Section::make('Conference Details')
//                ->aside()
                ->collapsible()
                ->description('General information about the conference')
                ->icon('heroicon-o-information-circle')
                ->columnSpanFull()
                ->columns(2)
//                ->columns(['md' => 2, 'lg'=> 3])
                ->schema([
                    TextInput::make('name')
                        ->columnSpanFull()
                        ->label('Conference name')
                        ->default('My Conference')
                        ->required()
                        ->maxLength(60),
                    MarkdownEditor::make('description')
                        ->columnSpan(2)
                        ->required(),
                    DateTimePicker::make('start_date')
                        ->native(false)
                        ->required(),
                    DateTimePicker::make('end_date')
                        ->native(false)
                        ->required(),
                    Fieldset::make('Status')
                        ->columnSpan(2)
                        ->schema([
                            Select::make('status')
                                ->columnSpanFull()
                                ->options([
                                    'draft' => 'Draft',
                                    'published' => 'Published',
                                    'archived' => 'Archived',
                                ])
                                ->required(),
                            Toggle::make('is_published')
                                ->default(true),
                        ])
                ]),
            Section::make('location')
                ->columnSpanFull()
                ->schema([
                    Select::make('region')
                        ->live()
                        ->enum(Region::class)
                        ->options(Region::class),
//                for cases where one dropdown affects what's going to be shown in another
                    Select::make('venue_id')
                        ->editOptionForm(VenueForm::getComponents())
                        ->createOptionForm(VenueForm::getComponents())
                        ->searchable()
                        ->preload()
                        ->relationship('venue', 'name', modifyQueryUsing: function (Builder $query, Get $get) {
                            return $query->where('region', $get('region'));
                        }),
                ]),
            CheckboxList::make('speakers')
                ->relationship('speakers', 'name')
                ->options(
                    Speaker::all()->pluck('name', 'id')
                )
                ->required(),
            Action::make('star')
                ->label('Fill with factory data')
                ->icon('heroicon-s-star')
                ->visible(function (string $operation){
                    if($operation !== 'create') {
                        return false;
                    }

                    if(!app()->environment('local')) {
                        return false;
                    }

                    return true;
                })
                ->action(function ($livewire) {
                    $data = Conference::factory()->make()->toArray();
                    $livewire->form->fill($data);
                })
        ];
    }
}
