<?php

namespace App\Filament\Pages;

use App\Models\Assessment;
use App\Models\AssessmentToken;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\ToggleButtons;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Colors\Color;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Support\Facades\DB;

class GenerateToken extends Page implements HasForms, HasTable
{
    use InteractsWithForms, InteractsWithTable;

    protected string $view = 'filament.pages.generate-token';

    public array $createFormData = [
        'all_module' => false,
        'assessment_id' => null,
        'expired_time' => 15,
    ];

    public array $filterFormData = [
        'date_from' => null,
        'date_to' => null,
        'show_expired' => false,
        'assessment_id' => null
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
                ->components([
                    Section::make('Form')
                        ->hiddenLabel()
                        ->components([
                            // Toggle::make('createFormData.all_module')
                            //     ->label('Untuk Semua Module'),
                            Checkbox::make('createFormData.all_module')
                                ->label('Untuk Semua Test')
                                ->helperText('Jika dicentang, token ini bisa digunakan untuk semua test.'),
                            Select::make('createFormData.assessment_id')
                                ->label('Pilih Test')
                                ->reactive()
                                ->searchable()
                                ->options(
                                    Assessment::all()
                                        ->pluck('name', 'id')
                                )
                                ->visible(fn() => !$this->createFormData['all_module']),
                            Select::make('expired_time')
                                ->label('Masa Aktif (Dalam Menit)')
                                ->default(15)
                                ->options([
                                    15,
                                    30,
                                    45,
                                    60
                                ]),
                        ])
                        ->headerActions([
                            Action::make('generate_token')
                                ->label('Generate Token')
                                ->color(Color::Green)
                                ->action('createToken')
                        ])
                ]);
    }

    public function filterForm(Schema $schema): Schema
    {
        return $schema
                ->components([
                    Section::make('filter')
                        ->label('Filter')
                        ->components([
                            Grid::make(2)
                                ->components([
                                    DatePicker::make('filterFormData.date_from')
                                        ->format('d/m/Y')
                                        ->defaultFocusedDate(now())
                                        ->reactive(),
                                    DatePicker::make('filterFormData.date_to')
                                        ->format('d/m/Y')
                                        ->defaultFocusedDate(now())
                                        ->reactive()
                                        ->minDate(fn (callable $get) => $get('date_from') ? Carbon::createFromFormat('Y-m-d', $get('date_from')) : now()->startOfDay()),
                                ]),
                            ToggleButtons::make('filterFormData.show_expired')
                                ->label('Tampilkan Expired'),
                            Select::make('filterFormData.assessment_id')
                                ->label('Assessment')
                                ->searchable()
                                ->options(
                                    Assessment::query()
                                        ->pluck('name', 'id')
                                )
                        ])
                ]);
    }

    public function table(Table $table): Table
    {
        return $table
                ->query(
                    AssessmentToken::query()
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
                        ->rowIndex()
                        ->copyable(),
                    TextColumn::make('value')
                        ->label('Token')
                        ->copyable()
                        ->alignCenter(),
                    TextColumn::make('created_at')
                        ->time()
                        ->label('Di Buat Pada')
                        ->copyable()
                        ->alignCenter(),
                    TextColumn::make('expired_until')
                        ->time()
                        ->label('Masa Aktif Sampai')
                        ->copyable()
                        ->alignCenter(),
                    TextColumn::make('assessment_id')
                        ->label('Assessment')
                        ->copyable()
                        ->alignCenter(),
                    IconColumn::make('all_module')
                        ->boolean()
                        ->label('Untuk Semua Modul')
                        ->alignCenter(),
                    TextColumn::make('status')
                        ->copyable()
                        ->alignCenter()
                        ->formatStateUsing(function($record) {
                            $status = "AKTIF";

                            return $status;
                        }),
                ]);
    }

    public function createToken()
    {
        dd($this->createFormData);
        try {
            DB::beginTransaction();

            AssessmentToken::create([
                'expired_time' => $this->createFormData['expired_time'],
                'expired_until' => Carbon::now()->addMinutes($this->createFormData['expired_time']),
                'assessment_id' => $this->createFormData['all_module'] ? null : $this->createFormData['assessment_id'],
                'all_module' => $this->createFormData['all_module'],
            ]);

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
}
