<?php

namespace App\Filament\Resources\Assessments\Schemas;

use App\Models\Module;
use App\Models\Test;
use App\Models\Topic;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;

class AssessmentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                TextInput::make('name')
                    ->label('NAMA')
                    ->required(),
                TextInput::make('description')
                    ->label('DESKRIPSI'),
                Select::make('module_id')
                    ->required()
                    ->label('MODUL')
                    ->searchable()
                    ->reactive()
                    ->afterStateUpdated(function(Set $set) {
                        $set('topic_id', null);
                        $set('test_id', null);
                    })
                    ->options(
                        Module::query()
                            ->pluck('name', 'id')
                    ),
                Select::make('topic_id')
                    ->required()
                    ->label('TOPIK')
                    ->searchable()
                    ->reactive()
                    ->afterStateUpdated(fn(Set $set) => $set('test_id', null))
                    ->options(
                        fn($get) =>
                        Topic::query()
                            ->where('module_id', $get('module_id'))
                            ->pluck('name', 'id')
                    )
                    ->disabled(fn($get) => !$get('module_id')),
                Select::make('test_id')
                    ->required()
                    ->label('SOAL')
                    ->searchable()
                    ->reactive()
                    ->options(
                        fn($get) =>
                        Test::query()
                            ->where('topic_id', $get('topic_id'))
                            ->pluck('name', 'id')
                    )
                    ->disabled(fn($get) => !$get('topic_id')),
                Select::make('participant_groups')
                    ->label('KELAS PESERTA')
                    ->multiple()
                    ->relationship('participant_groups', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),
                DateTimePicker::make('start_date')
                    ->label('WAKTU MULAI')
                    ->required(),
                DateTimePicker::make('end_date')
                    ->label('WAKTU SELESAI')
                    ->required(),
                TextInput::make('time_test')
                    ->label('WAKTU MENGERJAKAN')
                    ->numeric()
                    ->default(90)
                    ->required(),
                TextInput::make('correct_point')
                    ->label('NILAI JAWABAN BENAR')
                    ->numeric()
                    ->default(1)
                    ->required(),
                TextInput::make('wrong_point')
                    ->label('NILAI JAWABAN SALAH')
                    ->numeric()
                    ->default(0)
                    ->required(),
                TextInput::make('empty_point')
                    ->label('NILAI JAWABAN KOSONG')
                    ->numeric()
                    ->default(0)
                    ->required(),
                Toggle::make('show_result')
                    ->label('TAMPILKAN JAWABAN SETELAH SELESAI')
                    ->required()
                    ->default(false),
                Toggle::make('answer_not_null')
                    ->label('JAWABAN TERISI SEMUA')
                    ->required()
                    ->default(true),
                Toggle::make('need_token')
                    ->label('BUTUH TOKEN UNTUK MENGIKUTI UJIAN')
                    ->required()
                    ->default(true),
                TextInput::make('total_question')
                    ->label('JUMLAH SOAL')
                    ->numeric()
                    ->default(0)
                    ->required(),
                Toggle::make('randomize_question')
                    ->label('ACAK SOAL')
                    ->required()
                    ->default(true),
                Toggle::make('need_token')
                    ->label('ACAK JAWABAN')
                    ->required()
                    ->default(true),
            ]);
    }
}
