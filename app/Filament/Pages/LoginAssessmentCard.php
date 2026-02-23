<?php

namespace App\Filament\Pages;

use App\Action\PrintAssessmentCard as ActionPrintAssessmentCard;
use App\Action\PrintLoginCard;
use App\Enum\Menu;
use App\Models\Module;
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
        'group_id' => null
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
                                ->options(
                                    Module::query()
                                        ->pluck('name', 'id')
                                )
                                ->required(),
                            Select::make('group_id')
                                ->label('Pilih Kelas')
                                ->options(
                                    ParticipantGroup::query()
                                        ->pluck('name', 'id')
                                ),
                        ])
                        ->footerActions([
                            Action::make('print')
                            ->action(function ($get) {
                                    return PrintLoginCard::execute($get('module_id'), $get('group_id'));
                                })
                                ->icon(Heroicon::Printer)
                        ])
                        ->footerActionsAlignment(Alignment::End)
                ]);
    }
}
