<?php

namespace App\Filament\Resources\EvaluateAnswers;

use App\Filament\Resources\EvaluateAnswers\Pages\CreateEvaluateAnswer;
use App\Filament\Resources\EvaluateAnswers\Pages\EditEvaluateAnswer;
use App\Filament\Resources\EvaluateAnswers\Pages\ListEvaluateAnswers;
use App\Filament\Resources\EvaluateAnswers\Schemas\EvaluateAnswerForm;
use App\Filament\Resources\EvaluateAnswers\Tables\EvaluateAnswersTable;
use App\Models\Answer;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class EvaluateAnswerResource extends Resource
{
    protected static ?string $model = Answer::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return EvaluateAnswerForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return EvaluateAnswersTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListEvaluateAnswers::route('/'),
            'create' => CreateEvaluateAnswer::route('/create'),
            'edit' => EditEvaluateAnswer::route('/{record}/edit'),
        ];
    }
}
