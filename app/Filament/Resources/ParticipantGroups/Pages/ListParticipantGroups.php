<?php

namespace App\Filament\Resources\ParticipantGroups\Pages;

use App\Filament\Resources\ParticipantGroups\ParticipantGroupResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListParticipantGroups extends ListRecords
{
    protected static string $resource = ParticipantGroupResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label("Tambah Grup Peserta")
                ->modalHeading("Tambah Grup Peserta"),
        ];
    }
}
