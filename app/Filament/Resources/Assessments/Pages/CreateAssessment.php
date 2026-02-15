<?php

namespace App\Filament\Resources\Assessments\Pages;

use App\Action\CreateAssessmentGroup;
use App\Action\SyncParticipantAssessment;
use App\Filament\Resources\Assessments\AssessmentResource;
use Filament\Resources\Pages\CreateRecord;

class CreateAssessment extends CreateRecord
{
    protected static string $resource = AssessmentResource::class;

    protected function afterCreate() : void
    {
        SyncParticipantAssessment::execute($this->record->id);
    }
}