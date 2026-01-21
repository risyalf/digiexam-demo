<?php

namespace App\Filament\Pages;

use App\Action\GenerateRandomString;
use App\Enum\AssessmentParticipantStatus;
use App\Models\Assessment;
use App\Models\AssessmentParticipant;
use App\Models\User;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\SelectAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\Alignment;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Actions\HeaderActionsPosition;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\On;

class MonitorAssessment extends Page implements HasTable, HasForms
{
    use InteractsWithTable, InteractsWithForms;

    protected string $view = 'filament.pages.monitor-assessment';

    public array $selectFormData = [
        'assessment_id' => null,
        'status' => null,
        'date_start' => null,
        'time' => null,
        'name' => null,
    ];

    public array $filterFormData = [
        'status' => null,
        'name' => null
    ];

    protected function getForms(): array
    {
        return [
            'selectForm',
            'filterForm',
        ];
    }

    public function selectForm(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Pilih Test')
                    ->collapsible()
                    ->footerActionsAlignment(Alignment::Right)
                    ->footerActions([
                        Action::make('select')
                            ->icon(Heroicon::Check)
                            ->label('Pilih Tes')
                            ->color(Color::Green)
                            ->action(fn() => $this->dispatch('do-refresh')),
                    ])
                    ->components([
                        Select::make('selectFormData.assessment_id')
                            ->label('Nama Test')
                            ->options(
                                Assessment::query()
                                    ->where('start_date', '<=', Carbon::now()->toDateTimeString())
                                    ->where('end_date', '>=', Carbon::now()->toDateTimeString())
                                    ->pluck('name', 'id')
                            )
                            ->reactive()
                            ->afterStateUpdated(function ($state, Set $set, Get $get) {
                                $assesment = Assessment::find($state);

                                $set('selectFormData.name', $assesment ? $assesment->name : null);
                                $set('selectFormData.status', $assesment ? 'Aktif' : null);
                                $set('selectFormData.date_start', $assesment ? $assesment->start_date : null);
                                $set('selectFormData.time', $assesment ? $assesment->time_test : null);

                                $this->dispatch('do-update');
                            })
                            ->searchable(),
                        Grid::make(2)
                            ->components([
                                TextInput::make('selectFormData.name')
                                    ->label('Nama')
                                    ->readOnly()
                                    ->copyable(),
                                TextInput::make('selectFormData.status')
                                    ->readOnly()
                                    ->copyable(),
                                TextInput::make('selectFormData.date_start')
                                    ->label('Waktu Mulai')
                                    ->readOnly()
                                    ->copyable(),
                                TextInput::make('selectFormData.time')
                                    ->label('Waktu Tes')
                                    ->readOnly()
                                    ->copyable(),
                            ])
                    ])

            ]);
    }

    public function filterForm(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Filter Siswa')
                    ->collapsed()
                    ->collapsible()
                    // ->visible(fn() => $this->selectFormData['assessment_id'])
                    ->components([
                        Select::make('filterFormData.status')
                            ->options(
                                AssessmentParticipantStatus::options()
                            ),
                        Select::make('filterFormData.name')
                            ->label('Siswa')
                            ->searchable()
                            ->options(function ($q) {
                                $siswas = User::query()
                                    ->role('siswa')
                                    ->pluck('name', 'id');

                                return [
                                    null => 'Semua Siswa',
                                    ...$siswas
                                ];
                            })
                    ])
                    ->footerActionsAlignment(Alignment::Right)
                    ->footerActions([
                        Action::make('search')
                            ->icon(Heroicon::OutlinedMagnifyingGlass)
                            ->label('Cari')
                            ->color(Color::Green)
                            ->action(fn() => $this->dispatch('do-refresh')),
                    ])
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                AssessmentParticipant::query()
                    ->with('user', 'assessment')
                    ->when($this->selectFormData['assessment_id'], function ($q) {
                        $q->where('assessment_id', $this->selectFormData['assessment_id']);
                    })
                    ->when($this->filterFormData['status'], function ($q) {
                        $q->where('status', $this->filterFormData['status']);
                    })
                    ->when($this->filterFormData['name'], function ($q) {
                        $q->where('user_id', $this->filterFormData['name']);
                    })
            )
            ->heading('Peserta')
            ->headerActions([
                Action::make('open')
                    ->label('Generate Unlock Token')
                    ->accessSelectedRecords()
                    ->successNotificationTitle('Sukses Membuka Ujian Siswa!')
                    ->requiresConfirmation()
                    ->color(Color::Green)
                    ->modal()
                    ->action(
                        function (Collection $records) {
                            $userIds = $records->pluck('user_id')->toArray();

                            User::query()
                                ->where('is_locked', true)
                                ->whereIn('id', $userIds)
                                ->update([
                                    'unlock_token' => GenerateRandomString::execute(),
                                ]);

                            Notification::make()
                                ->success()
                                ->title('SUKSES GENERATE UNLOCK TOKEN')
                                ->send();
                        }
                    )
                    ->deselectRecordsAfterCompletion(),
                Action::make('lock')
                    ->label('Kunci Partisipan')
                    ->accessSelectedRecords()
                    ->successNotificationTitle('Sukses Mengunci Ujian Siswa!')
                    ->requiresConfirmation()
                    ->color(Color::Yellow)
                    ->action(
                        function (Collection $records) {
                            $userIds = $records->pluck('user_id')->toArray();

                            User::query()
                                ->where('is_locked', false)
                                ->whereIn('id', $userIds)
                                ->update([
                                    'is_locked' => true,
                                ]);

                            AssessmentParticipant::query()
                                ->whereIn('id', $userIds)
                                ->update([
                                    'status' => AssessmentParticipantStatus::LOCKED
                                ]);


                            Notification::make()
                                ->success()
                                ->title('SUKSES LOCK SISWA')
                                ->send();
                        }
                    )
                    ->deselectRecordsAfterCompletion(),
                // Action::make('stop')
                //     ->label('Hentikan Partisipan')
                //     ->accessSelectedRecords()
                //     ->successNotificationTitle('Sukses Menghentikan Ujian Siswa!')
                //     ->requiresConfirmation()
                //     ->color(Color::Orange)
                //     ->action(
                //         function(Collection $records) { 
                //             AssessmentParticipant::query()
                //                 ->whereIn('id', $records->pluck('id')->toArray())
                //                 ->update([
                //                     'status' => AssessmentParticipantStatus::PAUSED
                //                 ]);
                //         }
                //     )
                //     ->deselectRecordsAfterCompletion(),
                Action::make('delete')
                    ->label('Hapus Data Partisipan')
                    ->accessSelectedRecords()
                    ->successNotificationTitle('Sukses Menghapus Partisipan!')
                    ->requiresConfirmation()
                    ->color(Color::Red)
                    ->action(
                        function (Collection $records) {
                            AssessmentParticipant::whereIn('id', $records->pluck('id')->toArray())->delete();
                        }
                    )
                    ->deselectRecordsAfterCompletion(),
                Action::make('refresh')
                    ->icon(Heroicon::ArrowPath)
                    ->label('Refresh')
                    ->color(Color::Green)
                    ->action(fn() => $this->dispatch('do-refresh')),
            ])
            ->columns([
                TextColumn::make('No.')
                    ->rowIndex()
                    ->alignCenter(),
                TextColumn::make('user.name')
                    ->copyable()
                    ->label('Nama')
                    ->alignLeft(),
                TextColumn::make('assessment.name')
                    ->copyable()
                    ->label('Ujian Yang Di Ikuti')
                    ->alignLeft(),
                TextColumn::make('start_time')
                    ->copyable()
                    ->label('Waktu Mulai')
                    ->alignCenter(),
                TextColumn::make('end_time')
                    ->copyable()
                    ->label('Waktu Selesai')
                    ->alignCenter(),
                TextColumn::make('point')
                    ->copyable()
                    ->label('Poin')
                    ->alignCenter(),
                TextColumn::make('status')
                    ->copyable()
                    ->label('Status')
                    ->alignCenter(),
                TextColumn::make('user.unlock_token')
                    ->copyable()
                    ->label('Unlock Token')
                    ->alignCenter(),
            ])
            ->selectable();
    }

    #[On('do-refresh')]
    public function doRefresh() {}
}
