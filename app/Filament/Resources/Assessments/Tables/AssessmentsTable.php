<?php

namespace App\Filament\Resources\Assessments\Tables;

use App\Action\SyncParticipantAssessment;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Notifications\Notification;
use Filament\Support\Colors\Color;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class AssessmentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('no')
                    ->label('No.')
                    ->rowIndex(isFromZero:false),
                TextColumn::make('name')
                    ->label('NAMA')
                    ->copyable(),
                TextColumn::make('description')
                    ->label('DESKRIPSI')
                    ->copyable(),
                TextColumn::make('module.name')
                    ->label('MODUL')
                    ->copyable(),
                TextColumn::make('topic.name')
                    ->label('TOPIK')
                    ->copyable(),
                TextColumn::make('start_date')
                    ->label('WAKTU MULAI')
                    ->copyable(),
                TextColumn::make('end_date')
                    ->label('WAKTU SELESAI')
                    ->copyable(),
                TextColumn::make('time_test')
                    ->label('LAMA TEST (MENIT)')
                    ->copyable(),
                TextColumn::make('correct_point')
                    ->label('NILAI JAWABAN BENAR')
                    ->copyable(),
                TextColumn::make('wrong_point')
                    ->label('NILAI JAWABAN SALAH')
                    ->copyable(),
                TextColumn::make('empty_point')
                    ->label('NILAI JAWABAN KOSONG')
                    ->copyable(),
                ToggleColumn::make('show_result')
                    ->label('TAMPILKAN JAWABAN SETELAH SELESAI')
                    ->disabled(),
                ToggleColumn::make('answer_not_null')
                    ->label('JAWABAN TERISI SEMUA')
                    ->disabled(),
                ToggleColumn::make('need_token')
                    ->label('BUTUH TOKEN UNTUK MENGIKUTI UJIAN')
                    ->disabled(),
                TextColumn::make('total_question')
                    ->label('PERTANYAAN YANG DI TAMPILKAN')
                    ->copyable(),
                ToggleColumn::make('randomize_question')
                    ->label('ACAK SOAL')
                    ->disabled(),
                ToggleColumn::make('need_token')
                    ->label('ACAK JAWABAN')
                    ->disabled(),
            ])
            ->filters([
                TrashedFilter::make(),
            ])
            ->recordActions([
                EditAction::make()
                    ->color(Color::Cyan)
                    ->button(),
                Action::make('resync')
                    ->label('Sync Siswa')
                    ->button()
                    ->color(Color::Emerald)
                    ->icon(Heroicon::CloudArrowDown)
                    ->action(fn($record) => SyncParticipantAssessment::execute($record->id))
                    ->successNotification(Notification::make()->success()->title('SUKSES SYNCHRONIZE SISWA'))
                    ->failureNotification(fn() => Notification::make()->danger()->title('ERROR')->body('ADA ERROR KETIKA SYNC SISWA'))
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make()
                ]),
            ])
            ->paginated();
    }
}
