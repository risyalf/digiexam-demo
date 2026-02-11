<?php

namespace App\Filament\Resources\EvaluateAnswers\Pages;

use App\Filament\Resources\EvaluateAnswers\EvaluateAnswerResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListEvaluateAnswers extends ListRecords
{
    protected static string $resource = EvaluateAnswerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
