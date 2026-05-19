<?php

namespace App\Filament\Resources\EvaluateAnswers\Pages;

use App\Filament\Resources\EvaluateAnswers\EvaluateAnswerResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Enums\Width;

class ListEvaluateAnswers extends ListRecords
{
    protected static string $resource = EvaluateAnswerResource::class;

    protected Width|string|null $maxContentWidth = Width::Full;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
