<?php

namespace App\Filament\Resources\Assessments\Pages;

use App\Action\CreateAssessmentParticipant;
use App\Filament\Resources\Assessments\AssessmentResource;
use App\Models\Participant;
use App\Models\ParticipantAssessment;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateAssessment extends CreateRecord
{
    protected static string $resource = AssessmentResource::class;

    protected function afterCreate() : void
    {
        CreateAssessmentParticipant::execute($this->data['participant_groups'], $this->record->id);
    }
}
