<?php

namespace App\Filament\Resources\ParticipantGroups\Pages;

use App\Filament\Resources\ParticipantGroups\ParticipantGroupResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;
use Filament\Support\Enums\Width;

class ManageParticipantGroups extends ManageRecords
{
    protected static string $resource = ParticipantGroupResource::class;

    protected Width|string|null $maxContentWidth = Width::Full;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()->label("Tambah Kelas Peserta")];
    }
}
