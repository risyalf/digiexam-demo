<?php

namespace App\Filament\Pages;

use App\Models\Module;
use App\Traits\HasRefreshFunction;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Colors\Color;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\Column;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;

class MenuModul extends Page implements HasTable, HasForms
{
    use HasRefreshFunction, InteractsWithTable, InteractsWithForms;

    protected string $view = 'filament.pages.menu-modul';

    public array $dataFilter = [
        'name' => null
    ];

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('FILTER')
                    ->components([
                        TextInput::make('dataFilter.name')
                            ->label('NAMA MODUL')
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
                    Module::query()
                        ->when($this->dataFilter['name'], fn($q) => $q->whereRaw("name like '%{$this->dataFilter['name']}%'"))
                )
                ->columns([
                    TextColumn::make('no')
                        ->label('NO.')
                        ->rowIndex(),
                    TextColumn::make('name')
                        ->label('NAMA')
                        ->alignCenter(),
                    TextColumn::make('created_at')
                        ->label('DIBUAT PADA')
                        ->alignCenter(),
                ])
                ->headerActions([
                    CreateAction::make('BUAT MODUL')
                        ->icon(Heroicon::DocumentPlus)
                        ->color(Color::Green)
                        ->schema([
                            TextInput::make('name')
                                ->label('NAMA MODUL')
                                ->required()
                        ]),
                ])
                ->recordActions(
                    ActionGroup::make([
                        EditAction::make('EDIT MODUL')
                            ->icon(Heroicon::OutlinedPencil)
                            ->color(Color::Yellow)
                            ->schema([
                                TextInput::make('name')
                                    ->label('NAMA MODUL')
                                    ->required()
                            ]),
                        DeleteAction::make('HAPUS MODUL')
                            ->icon(Heroicon::OutlinedTrash)
                            ->color(Color::Red)
                            ->requiresConfirmation()
                    ])
                );
    }
}
