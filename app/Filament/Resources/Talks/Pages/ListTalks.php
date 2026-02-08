<?php

namespace App\Filament\Resources\Talks\Pages;

use App\enums\TalkStatus;
use App\Filament\Resources\Talks\TalkResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;

class ListTalks extends ListRecords
{
    protected static string $resource = TalkResource::class;

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('All Talks'),
            'approved' => Tab::make('Approved')
                ->modifyQueryUsing(fn ($query) => $query->where('status', TalkStatus::APPROVED)),
            'rejected' => Tab::make('Rejected')
                ->modifyQueryUsing(fn ($query) => $query->where('status', TalkStatus::REJECTED)),
            'submitted' => Tab::make('Submitted')
                ->modifyQueryUsing(fn ($query) => $query->where('status', TalkStatus::SUBMITTED)),
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
