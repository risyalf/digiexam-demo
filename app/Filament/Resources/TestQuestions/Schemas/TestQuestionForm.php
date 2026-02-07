<?php

namespace App\Filament\Resources\TestQuestions\Schemas;

use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class TestQuestionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                RichEditor::make('name')
                    ->label('Nama Soal')
                    ->required(),
            ]);
    }
}
