<?php

namespace App\Filament\Resources\EvaluateAnswers\Pages;

use App\Filament\Resources\EvaluateAnswers\EvaluateAnswerResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditEvaluateAnswer extends EditRecord
{
    protected static string $resource = EvaluateAnswerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
