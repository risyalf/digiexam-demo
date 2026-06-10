<?php

namespace App\Filament\Pages;

use App\Action\GenerateRandomString;
use App\Enum\Menu;
use App\Enum\ParticipantStatus;
use App\Models\Assessment;
use App\Models\Module;
use App\Models\Participant;
use App\Models\ParticipantAssessment;
use App\Models\ParticipantGroup;
use App\Models\Topic;
use App\Models\User;
use BackedEnum;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Carbon\Carbon;
use Filament\Actions\Action;
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
use Filament\Support\Enums\Width;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\On;
use Override;
use UnitEnum;

class MonitorAssessment extends Page implements HasTable, HasForms
{
    use InteractsWithTable, InteractsWithForms, HasPageShield;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::MagnifyingGlassPlus;

    protected static string|UnitEnum|null $navigationGroup = Menu::DATA_TES->value;

    protected static ?string $navigationLabel = "Monitor Assessment";

    protected static ?int $navigationSort = 1;

    protected string $view = 'filament.pages.monitor-assessment';

    protected Width|string|null $maxContentWidth = Width::Full;

    public array $filterFormData = [
        'status' => null,
        'name' => null,
        'module_id' => null,
        'topic_id' => null,
        'group_id' => null
    ];

    protected function getForms(): array
    {
        return [
            'filterForm',
        ];
    }

    public function filterForm(Schema $schema): Schema
    {
        return $schema
            ->statePath('filterFormData')
            ->components([
                Section::make('Filter Siswa')
                    ->collapsed()
                    ->collapsible()
                    // ->visible(fn() => $this->selectFormData['assessment_id'])
                    ->components([
                        Select::make('status')
                            ->options(
                                ParticipantStatus::options()
                            ),
                        Select::make('name')
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
                            }),
                        Select::make('module_id')
                            ->label('Modul')
                            ->searchable()
                            ->options(
                                Module::query()
                                    ->pluck('name', 'id')
                            ),
                        Select::make('topic_id')
                            ->label('Topik')
                            ->searchable()
                            ->options(
                                fn($get) =>
                                Topic::query()
                                    ->when($get('module_id'), fn($q, $v) => $q->where('module_id', $v))
                                    ->pluck('name', 'id')
                            ),
                        Select::make('group_id')
                            ->label('Kelas')
                            ->searchable()
                            ->options(ParticipantGroup::query()
                                ->pluck('name', 'id')),
                    ])
                    ->footerActionsAlignment(Alignment::Right)
                    ->footerActions([
                        Action::make('search')
                            ->icon(Heroicon::OutlinedMagnifyingGlass)
                            ->label('Cari')
                            ->color(Color::Emerald)
                            ->action(fn() => $this->dispatch('do-refresh')),
                    ])
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                ParticipantAssessment::query()
                    ->with(['participant', 'assessment'])
                    ->when($this->filterFormData['status'], function ($q) {
                        $q->where('status', $this->filterFormData['status']);
                    })
                    ->when($this->filterFormData['name'], function ($q) {
                        $q->whereHas('participant', fn($q) => $q->where('user_id', $this->filterFormData['name']));
                    })
                    ->when($this->filterFormData['topic_id'], function ($q) {
                        $q->whereHas('assessment', fn($q) => $q->where('topic_id', $this->filterFormData['topic_id']));
                    })
                    ->when($this->filterFormData['group_id'], function ($q) {
                        $q->whereHas('participant', fn($q) => $q->where('participant_group_id', $this->filterFormData['group_id']));
                    })
            )
            ->paginated()
            ->heading('Peserta')
            ->persistFiltersInSession(false)
            ->persistColumnsInSession(false)
            ->headerActions([
                Action::make('open')
                    ->label('Generate Unlock Token')
                    ->accessSelectedRecords()
                    ->successNotificationTitle('Sukses Membuka Ujian Siswa!')
                    ->requiresConfirmation()
                    ->color(Color::Emerald)
                    ->icon(Heroicon::LockOpen)
                    ->modal()
                    ->action(
                        function (Collection $records) {
                            $ids = $records->pluck('id')->toArray();

                            ParticipantAssessment::query()
                                ->where('status', ParticipantStatus::LOCKED)
                                ->whereIn('id', $ids)
                                ->get()
                                ->map(fn($data) => $data->update([
                                    'unlock_token' => GenerateRandomString::execute(),
                                ]));
                        }
                    )
                    ->successNotification(Notification::make()->success()->title('SUKSES GENERATE UNLOCK TOKEN'))
                    ->deselectRecordsAfterCompletion(),
                Action::make('open-same')
                    ->label('Generate Unlock Token YANG SAMA')
                    ->accessSelectedRecords()
                    ->successNotificationTitle('Sukses Membuka Ujian Siswa!')
                    ->requiresConfirmation()
                    ->color(Color::Emerald)
                    ->icon(Heroicon::LockOpen)
                    ->modal()
                    ->action(
                        function (Collection $records) {
                            $ids = $records->pluck('id')->toArray();

                            ParticipantAssessment::query()
                                ->where('status', ParticipantStatus::LOCKED)
                                ->whereIn('id', $ids)
                                ->update([
                                    'unlock_token' => GenerateRandomString::execute(),
                                ]);
                        }
                    )
                    ->successNotification(Notification::make()->success()->title('SUKSES GENERATE UNLOCK TOKEN'))
                    ->deselectRecordsAfterCompletion(),
                Action::make('lock')
                    ->label('Kunci Siswa')
                    ->accessSelectedRecords()
                    ->successNotificationTitle('Sukses Mengunci Ujian Siswa!')
                    ->requiresConfirmation()
                    ->color(Color::Indigo)
                    ->icon(Heroicon::LockClosed)
                    ->action(
                        function (Collection $records) {
                            $ids = $records->pluck('id')->toArray();

                            ParticipantAssessment::query()
                                ->whereIn('id', $ids)
                                ->update([
                                    'unlock_token' => null,
                                    'status' => ParticipantStatus::LOCKED
                                ]);
                        }
                    )
                    ->successNotification(Notification::make()->success()->title('SUKSES LOCK SISWA'))
                    ->deselectRecordsAfterCompletion(),
                Action::make('delete')
                    ->label('Hapus Siswa')
                    ->icon(Heroicon::Trash)
                    ->accessSelectedRecords()
                    ->successNotificationTitle('Sukses Menghapus Siswa!')
                    ->requiresConfirmation()
                    ->color(Color::Red)
                    ->action(
                        function (Collection $records) {
                            ParticipantAssessment::whereIn('id', $records->pluck('id')->toArray())->delete();
                        }
                    )
                    ->successNotification(Notification::make()->success()->title('SUKSES HAPUS SISWA'))
                    ->deselectRecordsAfterCompletion(),
                Action::make('refresh')
                    ->icon(Heroicon::ArrowPath)
                    ->label('Refresh')
                    ->color(Color::Slate)
                    ->action(fn() => $this->dispatch('do-refresh')),
            ])
            ->columns([
                TextColumn::make('No.')
                    ->rowIndex(isFromZero: false)
                    ->alignCenter(),
                TextColumn::make('participant.user.name')
                    ->copyable()
                    ->label('Nama')
                    ->alignLeft(),
                TextColumn::make('participant.participantGroup.name')
                    ->copyable()
                    ->label('Kelas')
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
                    ->formatStateUsing(fn($state) => round($state, 2))
                    ->alignCenter()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('status')
                    ->copyable()
                    ->label('Status')
                    ->alignCenter(),
                TextColumn::make('unlock_token')
                    ->copyable()
                    ->label('Unlock Token')
                    ->alignCenter(),
            ])
            ->selectable();
    }

    #[On('do-refresh')]
    public function doRefresh() {}
}
