<?php

namespace App\Filament\Resources\Assessments\Pages;

use App\Filament\Resources\Assessments\AssessmentResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Enums\Width;
use Override;

class ListAssessments extends ListRecords
{
    protected static string $resource = AssessmentResource::class;

    #[Override]
    public function getMaxContentWidth(): Width|string|null
    {
        return Width::MaxContent;
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
