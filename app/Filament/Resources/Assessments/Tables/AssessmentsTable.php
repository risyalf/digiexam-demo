<?php

namespace App\Filament\Resources\Assessments\Tables;

use App\Action\SyncParticipantAssessment;
use App\Models\Assessment;
use App\Models\Module;
use App\Models\Topic;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Notifications\Notification;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\Alignment;
use Filament\Support\Enums\Width;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Enums\RecordActionsPosition;
use Filament\Tables\Filters\SelectFilter;
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
                    ->rowIndex(isFromZero: false),
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
                    ->wrapHeader()
                    ->copyable(),
                TextColumn::make('end_date')
                    ->label('WAKTU SELESAI')
                    ->wrapHeader()
                    ->copyable(),
                TextColumn::make('time_test')
                    ->label('LAMA TEST (MENIT)')
                    ->wrapHeader()
                    ->copyable(),
                TextColumn::make('correct_point')
                    ->label('NILAI JAWABAN BENAR')
                    ->wrapHeader()
                    ->copyable(),
                TextColumn::make('wrong_point')
                    ->label('NILAI JAWABAN SALAH')
                    ->wrapHeader()
                    ->copyable(),
                TextColumn::make('empty_point')
                    ->label('NILAI JAWABAN KOSONG')
                    ->wrapHeader()
                    ->copyable(),
                ToggleColumn::make('show_result')
                    ->label('TAMPILKAN JAWABAN SETELAH SELESAI')
                    ->wrapHeader()
                    ->disabled(),
                ToggleColumn::make('answer_not_null')
                    ->label('JAWABAN TERISI SEMUA')
                    ->wrapHeader()
                    ->disabled(),
                ToggleColumn::make('need_token')
                    ->label('BUTUH TOKEN UNTUK MENGIKUTI UJIAN')
                    ->disabled()
                    ->wrapHeader(),
                TextColumn::make('total_question')
                    ->label('PERTANYAAN YANG DI TAMPILKAN')
                    ->wrapHeader()
                    ->copyable(),
                ToggleColumn::make('randomize_question')
                    ->label('ACAK SOAL')
                    ->wrapHeader()
                    ->disabled(),
                ToggleColumn::make('need_token')
                    ->label('ACAK JAWABAN')
                    ->wrapHeader()
                    ->disabled(),
            ])
            ->filters([
                SelectFilter::make('name')
                    ->options(
                        Assessment::query()
                            ->pluck('name', 'id')
                    )
                    ->searchable()
                    ->label('Nama Ujian'),
                SelectFilter::make('module_id')
                    ->options(
                        Module::query()
                            ->pluck('name', 'id')
                    )
                    ->searchable()
                    ->label('Modul'),
                SelectFilter::make('topic_id')
                    ->options(fn($get) =>
                        Topic::query()
                        ->with('module')
                        ->get()
                        ->mapWithKeys(fn ($topic) => [
                            $topic->id => "{$topic->module->name} - {$topic->name}"
                        ])
                    )
                    ->searchable()
                    ->label('Topic'),
            ], FiltersLayout::AboveContent)
            ->deferFilters()
            ->filtersFormWidth(Width::Full)
            ->recordActions(
                ActionGroup::make([
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
            )
            ->recordActionsPosition(RecordActionsPosition::BeforeColumns)
            ->recordActionsColumnLabel('AKSI')
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
