<?php

namespace App\Filament\Pages;

use App\Action\ExportTestFormDocx;
use App\Models\Module;
use App\Models\Test;
use App\Models\Topic;
use App\Traits\HasRefreshFunction;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Colors\Color;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\PhpWord;
use Symfony\Component\HttpFoundation\StreamedResponse;

class MenuSoal extends Page implements HasTable, HasForms
{
    use HasRefreshFunction, InteractsWithTable, InteractsWithForms;

    protected string $view = 'filament.pages.menu-soal';

    public array $dataFilter = [
        'topic' => null,
        'name' => null,
        'created' => null
    ];

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('FILTER')
                    ->components([
                        Select::make('dataFilter.topic')
                            ->label('TOPIK')
                            ->options(
                                Topic::query()
                                    ->pluck('name', 'id')
                            ),
                        TextInput::make('dataFilter.name')
                            ->label('NAMA SOAL'),
                        DatePicker::make('dataFilter.created')
                            ->label('DIBUAT PADA')
                    ])
                    ->footerActions([
                        Action::make('search')
                            ->label('SEARCH')
                            ->action(fn() => $this->dispatch('do-refresh'))
                    ])
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Test::query()
                    ->with('topic')
                    ->when($this->dataFilter['name'], fn($q) => $q->whereRaw("name like '%{$this->dataFilter['name']}%'"))
                    ->when($this->dataFilter['topic'], function ($q) {
                        $q->whereHas('topic', fn($q) => $q->where('id', $this->dataFilter['topic']));
                    })
                    ->when($this->dataFilter['created'], fn($q) => $q->whereDate('created_at', $this->dataFilter['created']))
            )
            ->columns([
                TextColumn::make('no')
                    ->label('NO.')
                    ->rowIndex(),
                TextColumn::make('topic.name')
                    ->label('NAMA MODUL')
                    ->alignCenter(),
                TextColumn::make('name')
                    ->label('NAMA')
                    ->alignCenter(),
                TextColumn::make('created_at')
                    ->label('DIBUAT PADA')
                    ->alignCenter(),
            ])
            ->headerActions([
                Action::make('export')
                    ->label('EXPORT FORMAT')
                    ->icon(Heroicon::ArrowUp)
                    ->color(Color::Green)
                    ->action(fn () => ExportTestFormDocx::execute('IMPORT-FORMAT-SOAL.docx'))
            ])
            ->recordActions(
                ActionGroup::make([
                    EditAction::make('EDIT SOAL')
                        ->icon(Heroicon::OutlinedPencil)
                        ->color(Color::Yellow)
                        ->schema([
                            Select::make('module_id')
                                ->options(
                                    Module::query()
                                        ->pluck('name', 'id')
                                )
                                ->required(),
                            TextInput::make('name')
                                ->label('NAMA SOAL')
                                ->required(),
                        ]),
                    DeleteAction::make('HAPUS SOAL')
                        ->icon(Heroicon::OutlinedTrash)
                        ->color(Color::Red)
                        ->requiresConfirmation()
                ])
            );
    }
}
