<?php

namespace App\Filament\Pages;

use App\Action\PrintAssessmentCard as ActionPrintAssessmentCard;
use App\Action\PrintLoginCard;
use App\Enum\Menu;
use App\Models\Module;
use App\Models\Participant;
use App\Models\ParticipantGroup;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Alignment;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

class LoginAssessmentCard extends Page implements HasForms
{
    use InteractsWithForms;

    protected string $view = 'filament.pages.login-assessment-card';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::Printer;

    protected static string|UnitEnum|null $navigationGroup = Menu::DATA_PESERTA->value;

    protected static ?string $navigationLabel = "Cetak Kartu Login";

    protected static ?string $title = "Cetak Kartu Login";

    protected static ?int $navigationSort = 2;

    public $formData = [
        'module_id' => null,
        'group_id' => null,
        'participant_id' => null
    ];

    public function form(Schema $schema): Schema
    {
        return $schema
            ->statePath('formData')
            ->components([
                Section::make('')
                    ->schema([
                        Select::make('module_id')
                            ->label('Pilih Modul')
                            ->reactive()
                            ->afterStateUpdated(function ($set) {
                                $set('group_id', null);
                                $set('participant_id', null);
                            })
                            ->options(
                                Module::query()
                                    ->pluck('name', 'id')
                            )
                            ->required(),
                        Select::make('group_id')
                            ->label('Pilih Kelas')
                            ->reactive()
                            ->afterStateUpdated(fn($set) => $set('participant_id', null))
                            ->disabled(fn($get) => !$get('module_id'))
                            ->options(
                                fn($get) =>
                                ParticipantGroup::query()
                                    ->when($get('module_id'), function ($q) use ($get) {
                                        $q->whereRaw("
                                                    exists (
                                                        select 1 from assessments a 
                                                        join assessment_participant_groups apg on apg.assessment_id = a.id
                                                        where a.module_id = '" . $get('module_id') . "')");
                                    })
                                    ->pluck('name', 'id')
                            ),
                        Select::make('participant_id')
                            ->label('Pilih Siswa')
                            ->disabled(fn($get) => !$get('group_id'))
                            ->options(function ($get) {
                                $groupId = $get('group_id');
                        
                                if (!$groupId) {
                                    return [];
                                }
                        
                                return Participant::query()
                                    ->where('participant_group_id', $groupId)
                                    ->join('users', 'users.id', '=', 'participants.user_id')
                                    ->orderBy('users.name')
                                    ->pluck('users.name', 'participants.id')
                                    ->toArray();
                            })
                    ])
                    ->footerActions([
                        Action::make('print')
                            ->action(function ($get) {
                                return PrintLoginCard::execute($get('module_id'), $get('group_id'), $get('participant_id'));
                            })
                            ->icon(Heroicon::Printer)
                    ])
                    ->footerActionsAlignment(Alignment::End)
            ]);
    }
}
