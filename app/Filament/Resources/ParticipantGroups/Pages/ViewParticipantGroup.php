<?php

namespace App\Filament\Resources\ParticipantGroups\Pages;

use App\Filament\Resources\ParticipantGroups\ParticipantGroupResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewParticipantGroup extends ViewRecord
{
    protected static string $resource = ParticipantGroupResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
