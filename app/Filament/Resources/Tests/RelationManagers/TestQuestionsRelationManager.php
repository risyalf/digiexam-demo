<?php

namespace App\Filament\Resources\Tests\RelationManagers;

use App\Filament\Resources\TestQuestions\TestQuestionResource;
use Filament\Actions\CreateAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;

class TestQuestionsRelationManager extends RelationManager
{
    protected static string $relationship = 'testQuestions';

    protected static ?string $relatedResource = TestQuestionResource::class;

    public function table(Table $table): Table
    {
        return $table
            ->headerActions([
                CreateAction::make(),
            ]);
    }
}
