<?php

namespace App\Filament\Resources\Assessments\Schemas;

use App\Models\Module;
use App\Models\Topic;
use App\Models\User;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\ToggleButtons;
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
                    ->options(
                        Module::query()
                            ->pluck('name', 'id')
                    ),
                Select::make('topic_id')
                    ->required()
                    ->label('TOPIK')
                    ->searchable()
                    ->options(
                        Topic::query()
                            ->pluck('name', 'id')
                    ),
                Select::make('supervisors')
                    ->options(
                        User::query()
                            ->whereHas('roles', fn($q) => $q->where('name', 'guru'))
                            ->pluck('name', 'id')
                    )
                    ->searchable()
                    ->required(),
                DatePicker::make('start_date')
                    ->label('WAKTU MULAI')
                    ->required(),
                DatePicker::make('end_date')
                    ->label('WAKTU SELESAI')
                    ->required(),
                TextInput::make('time_test')
                    ->label('LAMA TEST')
                    ->numeric()
                    ->default(0)
                    ->required(),
                TextInput::make('correct_point')
                    ->label('NILAI JAWABAN BENAR')
                    ->numeric()
                    ->default(0)
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
                Toggle::make('detail_result')
                    ->label('TAMPILKAN DETAIL JAWABAN SETELAH SELESAI')
                    ->required()
                    ->default(false),
                Toggle::make('need_token')
                    ->label('BUTUH TOKEN UNTUK MENGIKUTI UJIAN')
                    ->required()
                    ->default(true),
                TextInput::make('total_question')
                    ->label('PERTANYAAN YANG DI TAMPILKAN')
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
