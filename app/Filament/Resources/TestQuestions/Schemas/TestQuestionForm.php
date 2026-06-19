<?php

namespace App\Filament\Resources\TestQuestions\Schemas;

use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class TestQuestionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                TextInput::make('id')
                    ->hidden(),
                Select::make('type')
                    ->options([
                        'Pilihan Ganda' => 'Pilihan Ganda',
                        'Esai' => 'Esai',
                    ])
                    ->required(),
                RichEditor::make('name')
                    ->label('Nama Soal')
                    ->required(),
            ]);
    }
}
