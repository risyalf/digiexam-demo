<?php

namespace App\Filament\Pages;

use App\Action\CreateToken;
use App\Enum\Menu;
use App\Models\Assessment;
use App\Models\AssessmentToken;
use BackedEnum;
use Carbon\Carbon;
use Exception;
use Filament\Actions\Action;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\Alignment;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\On;
use UnitEnum;

class GenerateToken extends Page implements HasForms, HasTable
{
    use InteractsWithForms, InteractsWithTable;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::Key;
    
    protected static string|UnitEnum|null $navigationGroup = Menu::DATA_TES->value;

    protected string $view = 'filament.pages.generate-token';

    public array $createFormData = [
        'all_module' => false,
        'assessment_id' => null,
        'expired_time' => null,
    ];

    public array $filterFormData = [
        'date_from' => null,
        'date_to' => null,
        'show_expired' => false,
        'assessment_id' => null,
    ];

    protected function getForms(): array
    {
        return [
            'createForm',
            'filterForm'
        ];
    }

    public function createForm(Schema $schema): Schema
    {
        return $schema
                ->statePath('createFormData')
                ->components([
                    Section::make('Form')
                        ->hiddenLabel()
                        ->components([
                            Checkbox::make('all_module')
                                ->label('Untuk Semua Test')
                                ->reactive()
                                ->helperText('Jika dicentang, token ini bisa digunakan untuk semua test.'),
                            Select::make('assessment_id')
                                ->label('Pilih Test')
                                ->reactive()
                                ->searchable()
                                ->options(
                                    Assessment::all()
                                        ->pluck('name', 'id')
                                )
                                ->visible(fn($get) => !$get('all_module')),
                            Select::make('expired_time')
                                ->label('Masa Aktif (Dalam Menit)')
                                ->options([
                                    15 => 15,
                                    30 => 30,
                                    45 => 45,
                                    60 => 60,
                                ]),
                        ])
                        ->headerActions([
                            Action::make('generate_token')
                                ->label('Generate Token')
                                ->action('createToken')
                        ])
                ]);
    }

    public function filterForm(Schema $schema): Schema
    {
        return $schema
                ->statePath('filterFormData')
                ->components([
                    Section::make('filter')
                        ->label('Filter')
                        ->collapsible()
                        ->collapsed()
                        ->components([
                            Grid::make(2)
                                ->components([
                                    DatePicker::make('date_from')
                                        ->format('d/m/Y')
                                        ->defaultFocusedDate(now())
                                        ->reactive(),
                                    DatePicker::make('date_to')
                                        ->format('d/m/Y')
                                        ->defaultFocusedDate(now())
                                        ->reactive()
                                        ->minDate(fn (callable $get) => $get('date_from') ? Carbon::createFromFormat('Y-m-d', $get('date_from')) : now()->startOfDay()),
                                ]),
                            Checkbox::make('show_expired')
                                ->label('Tampilkan Expired'),
                            Select::make('assessment_id')
                                ->label('Assessment')
                                ->searchable()
                                ->options(
                                    Assessment::query()
                                        ->pluck('name', 'id')
                                )
                        ])
                        ->footerActionsAlignment(Alignment::Right)
                        ->footerActions([
                            Action::make('filter')
                                ->icon(Heroicon::MagnifyingGlass)
                                ->color(Color::Emerald)
                                ->label('Filter')
                                ->action(fn() => $this->dispatch('do-refresh'))
                        ]),
                ]);
    }

    public function table(Table $table): Table
    {
        return $table
                ->query(
                    AssessmentToken::query()
                        ->with('assessment')
                        ->select([
                            '*',
                            DB::raw("null as status"),
                        ])
                        ->when($this->filterFormData['date_from'] && $this->filterFormData['date_to'], function($q) {
                            $q->whereRaw("(start_date::date >= '{$this->filterFormData['date_from']}' and start_date::date <= '{$this->filterFormData['date_to']}')");
                        })
                        ->when($this->filterFormData['date_from'] && $this->filterFormData['date_to'], function($q) {
                            $q->whereRaw("(start_date::date >= '{$this->filterFormData['date_from']}' and start_date::date <= '{$this->filterFormData['date_to']}')");
                        })
                        ->when(!$this->filterFormData['show_expired'], function($q) {
                            $q->where('expired_until', '>', now());
                        })
                        ->when($this->filterFormData['assessment_id'], function($q) {
                            $q->where('id', $this->filterFormData['assessment_id']);
                        })
                )
                ->columns([
                    TextColumn::make('No.')
                        ->rowIndex(isFromZero:false)
                        ->copyable(),
                    TextColumn::make('value')
                        ->label('Token')
                        ->copyable()
                        ->alignCenter(),
                    TextColumn::make('created_at')
                        ->label('Di Buat Pada')
                        ->copyable()
                        ->alignCenter(),
                    TextColumn::make('expired_time')
                        ->label('Masa Aktif')
                        ->copyable()
                        ->formatStateUsing(fn($state) => $state." Menit")
                        ->alignCenter(),
                    TextColumn::make('expired_until')
                        ->label('Masa Aktif Sampai')
                        ->copyable()
                        ->alignCenter(),
                    TextColumn::make('assessment.name')
                        ->label('Assessment')
                        ->copyable()
                        ->alignCenter(),
                    IconColumn::make('all_module')
                        ->boolean()
                        ->label('Untuk Semua Test')
                        ->alignCenter(),
                    TextColumn::make('status')
                        ->label('Status')
                        ->alignCenter()
                        ->formatStateUsing(function ($record) {
                            return now()->gt($record->expired_until) ? 'EXPIRED' : 'AKTIF';
                        })
                        ->badge()
                        ->color(fn ($state): string => match ($state) {
                            'AKTIF' => 'success',
                            'EXPIRED' => 'danger',
                            default => 'gray',
                        }),
                ])
                ->paginated();
    }

    public function createToken()
    {
        try {
            if (!$this->createFormData['all_module'] && !$this->createFormData['assessment_id']) {
                throw new Exception("Silahkan Pilih Test Untuk Generate Token");
            }
            if (!$this->createFormData['expired_time']) {
                throw new Exception("Silahkan Pilih Masa Aktif Token");
            }

            DB::beginTransaction();

            CreateToken::execute($this->createFormData);

            Notification::make()
                ->success()
                ->title('Sukses Membuat Token!')
                ->send();

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            Notification::make()
                ->danger()
                ->title('ERROR')
                ->body($th->getMessage())
                ->send();
        }
    }

    #[On('do-refresh')]
    public function doRefresh() {}
}
