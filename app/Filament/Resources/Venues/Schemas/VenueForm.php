<?php

namespace App\Filament\Resources\Venues\Schemas;

use App\enums\Region;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class VenueForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components(self::getComponents());
    }

    public static function getComponents(): array
    {
        return [
            TextInput::make('name')
                ->required(),
            TextInput::make('city')
                ->required(),
            TextInput::make('country')
                ->required(),
            TextInput::make('post_code')
                ->required(),
            Select::make('region')
                ->enum(Region::class)
                ->options(Region::class)
        ];
    }
}
