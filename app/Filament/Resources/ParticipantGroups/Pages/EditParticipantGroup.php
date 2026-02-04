<?php

namespace App\Filament\Resources\ParticipantGroups\Pages;

use App\Filament\Resources\ParticipantGroups\ParticipantGroupResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditParticipantGroup extends EditRecord
{
    protected static string $resource = ParticipantGroupResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }
}
