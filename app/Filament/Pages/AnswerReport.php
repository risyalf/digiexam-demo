<?php

namespace App\Filament\Pages;

use App\Action\ReportAnswerDetail;
use App\Action\ReportAnswerSummary;
use App\Enum\Menu;
use App\Models\Module;
use App\Models\ParticipantGroup;
use App\Models\Topic;
use BackedEnum;
use Exception;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Colors\Color;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

class AnswerReport extends Page implements HasForms
{
    use InteractsWithForms;

    protected string $view = 'filament.pages.answer-report';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::DocumentCheck;

    protected static string|UnitEnum|null $navigationGroup = Menu::DATA_TES->value;

    protected static ?string $navigationLabel = "Evaluasi Jawaban";

    protected static ?string $title = "Evaluasi Jawaban";

    protected static ?int $navigationSort = 3;

    public array $data = [
        'module_id' => null,
        'topic_id' => null,
        'group_id' => null,
        'type' => null,
        'created_at' => null
    ];

    public function form(Schema $schema): Schema
    {
        return $schema
            ->statePath('data')
            ->schema([
                Section::make('Download Report')
                    ->schema([
                        Select::make('module_id')
                            ->label('Modul')
                            ->reactive()
                            ->afterStateUpdated(fn($set) => $set('topic_id', null))
                            ->options(
                                Module::query()
                                    ->pluck('name', 'id')
                            ),
                        Select::make('topic_id')
                            ->label('Topik')
                            ->disabled(fn($get) => !$get('module_id'))
                            ->options(
                                function ($get) {
                                    return Topic::query()
                                        ->where('module_id', $get('module_id'))
                                        ->pluck('name', 'id');
                                }
                            ),
                        Select::make('group_id')
                            ->label('Kelas')
                            ->options(
                                ParticipantGroup::query()
                                    ->pluck('name', 'id')
                            ),
                        Select::make('type')
                            ->label('Tipe')
                            ->reactive()
                            ->options([
                                'DETAIL' => 'DETAIL',
                                'SUMMARY' => 'SUMMARY'
                            ]),
                        DatePicker::make('created_at')
                            ->label('Tanggal Submit'),
                    ])
                    ->headerActions([
                        Action::make('download')
                            ->color('primary')
                            ->action(function ($get) {
                                try {
                                    if (!$get('type')) {
                                        throw new Exception("TOLONG PILIH TIPE REPORT YANG INGIN DI DOWNLOAD");
                                    }
                                    if (!$get('topic_id')) {
                                        throw new Exception("TOLOG PILIH TOPIK TERLEBIH DAHULU");
                                    }

                                    if ($get('type') == 'DETAIL') {
                                        return ReportAnswerDetail::execute($this->data);
                                    } else {
                                        return ReportAnswerSummary::execute($this->data);
                                    }
                                } catch (\Throwable $th) {
                                    Notification::make()->danger()->title('ERROR')->body($th->getMessage())->send();
                                }
                            })
                    ])
            ]);
    }
}
